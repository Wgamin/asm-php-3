<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderFulfillmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Symfony\Component\HttpFoundation\Response;

class PaymentController extends Controller
{
    public function createPayment(Request $request)
    {
        $order = Order::with(['payment', 'shipment'])->findOrFail((int) $request->input('order_id'));

        $vnpUrl = (string) env('VNPAY_URL');
        $vnpReturnUrl = (string) env('VNPAY_RETURN_URL');
        $vnpTmnCode = (string) env('VNPAY_TMN_CODE');
        $vnpHashSecret = (string) env('VNPAY_HASH_SECRET');

        if ($vnpUrl === '' || $vnpReturnUrl === '' || $vnpTmnCode === '' || $vnpHashSecret === '') {
            return redirect()->route('checkout')->with('error', 'VNPay chua duoc cau hinh day du.');
        }

        $amount = (float) ($order->payable_amount ?? $order->total_amount);
        $inputData = [
            'vnp_Version' => '2.1.0',
            'vnp_TmnCode' => $vnpTmnCode,
            'vnp_Amount' => (int) round($amount * 100),
            'vnp_Command' => 'pay',
            'vnp_CreateDate' => date('YmdHis'),
            'vnp_CurrCode' => 'VND',
            'vnp_IpAddr' => $request->ip(),
            'vnp_Locale' => 'vn',
            'vnp_OrderInfo' => 'Thanh toan don hang ' . $order->order_number,
            'vnp_OrderType' => 'billpayment',
            'vnp_ReturnUrl' => $vnpReturnUrl,
            'vnp_TxnRef' => $order->id,
        ];

        ksort($inputData);
        $hashData = urldecode(http_build_query($inputData));
        $query = http_build_query($inputData);

        if ($vnpHashSecret !== '') {
            $query .= '&vnp_SecureHash=' . hash_hmac('sha512', $hashData, $vnpHashSecret);
        }

        return redirect($vnpUrl . '?' . $query);
    }

    public function vnpayReturn(Request $request)
    {
        $vnpHashSecret = (string) env('VNPAY_HASH_SECRET');
        $inputData = collect($request->query())
            ->filter(fn ($value, $key) => str_starts_with((string) $key, 'vnp_'))
            ->toArray();

        $receivedHash = (string) ($inputData['vnp_SecureHash'] ?? '');
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);

        $hashData = urldecode(http_build_query($inputData));
        $secureHash = hash_hmac('sha512', $hashData, $vnpHashSecret);
        $order = Order::with(['payment', 'shipment'])->find((int) $request->query('vnp_TxnRef'));

        if ($secureHash !== $receivedHash || ! $order) {
            return redirect()->route('checkout')->with('error', 'Chu ky thanh toan khong hop le.');
        }

        if ((string) $request->query('vnp_ResponseCode') === '00') {
            DB::transaction(function () use ($order, $request, $inputData) {
                $order->loadMissing(['items.product', 'items.variant', 'payment']);

                if ($order->payment) {
                    $order->payment->update([
                        'status' => 'paid',
                        'transaction_code' => (string) $request->query('vnp_TransactionNo'),
                        'paid_at' => now(),
                        'metadata' => array_merge($order->payment->metadata ?? [], $inputData),
                    ]);
                }

                app(OrderFulfillmentService::class)->apply($order);

                if ($order->status === 'pending') {
                    $order->update(['status' => 'processing']);
                }

                $order->refresh()->load(['payment', 'shipment']);
                $order->recordStatusHistory('payment_gateway', 'VNPay thanh toan thanh cong', $inputData);
            });

            session()->forget('cart');
            session()->forget('applied_coupon');

            return redirect()->route('order.success')->with('success_order', $order->order_number);
        }

        DB::transaction(function () use ($order, $request, $inputData) {
            $order->loadMissing(['items.product', 'items.variant', 'payment']);

            if ($order->payment) {
                $order->payment->update([
                    'status' => 'failed',
                    'transaction_code' => (string) $request->query('vnp_TransactionNo'),
                    'metadata' => array_merge($order->payment->metadata ?? [], $inputData),
                ]);
            }

            app(OrderFulfillmentService::class)->release($order);

            $order->refresh()->load(['payment', 'shipment']);
            $order->recordStatusHistory('payment_gateway', 'VNPay thanh toan that bai', $inputData);
        });

        return redirect()->route('checkout')->with('error', 'Thanh toan that bai, vui long thu lai.');
    }

    public function createMomoPayment(Request $request)
    {
        $order = Order::with(['payment', 'shipment'])->findOrFail((int) $request->input('order_id'));
        $config = $this->momoConfig();

        if (! $this->momoConfigured($config)) {
            return $this->momoCreateFailure($order, 'MoMo test chua duoc cau hinh day du.');
        }

        $requestId = 'MOMO-' . $order->id . '-' . now()->format('YmdHisv');
        $amount = (int) round((float) $order->payable_total);
        $redirectUrl = (string) ($config['redirect_url'] ?: route('payment.momoReturn'));
        $ipnUrl = (string) ($config['ipn_url'] ?: route('payment.momoIpn'));
        $extraData = base64_encode((string) json_encode([
            'order_number' => $order->order_number,
            'internal_order_id' => $order->id,
            'payment_method' => 'momo',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        $payload = [
            'partnerCode' => $config['partner_code'],
            'storeName' => $config['store_name'],
            'storeId' => $config['store_id'],
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $order->order_number,
            'orderInfo' => 'Thanh toan don hang ' . $order->order_number,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'requestType' => $config['request_type'],
            'autoCapture' => true,
            'extraData' => $extraData,
            'deliveryInfo' => [
                'deliveryAddress' => $order->address,
                'deliveryFee' => (string) (int) round((float) ($order->shipping_fee_amount ?? 0)),
                'quantity' => (string) $order->items()->sum('quantity'),
            ],
            'userInfo' => [
                'name' => $order->full_name,
                'phoneNumber' => $order->phone,
                'email' => $order->email,
            ],
        ];
        $payload['signature'] = hash_hmac(
            'sha256',
            $this->momoCreateSignatureData($payload, $config['access_key']),
            $config['secret_key']
        );

        $this->rememberMomoCreateAttempt($order, [
            'request_id' => $requestId,
            'redirect_url' => $redirectUrl,
            'ipn_url' => $ipnUrl,
        ]);

        try {
            $response = Http::acceptJson()
                ->asJson()
                ->timeout(30)
                ->post($this->momoCreateUrl($config), $payload);
        } catch (\Throwable $e) {
            report($e);

            return $this->momoCreateFailure(
                $order,
                'Khong the ket noi sang MoMo test, vui long thu lai.',
                ['request_id' => $requestId]
            );
        }

        $data = is_array($response->json()) ? $response->json() : [];
        if (! $response->successful() || (int) ($data['resultCode'] ?? -1) !== 0 || blank($data['payUrl'] ?? null)) {
            return $this->momoCreateFailure(
                $order,
                (string) ($data['message'] ?? 'MoMo test tu choi tao giao dich.'),
                [
                    'request_id' => $requestId,
                    'create_response' => $data,
                    'http_status' => $response->status(),
                ]
            );
        }

        $payment = $order->payment;
        if ($payment) {
            $payment->update([
                'metadata' => array_merge($payment->metadata ?? [], [
                    'gateway' => 'momo',
                    'request_id' => $requestId,
                    'pay_url' => $data['payUrl'] ?? null,
                    'qr_code_url' => $data['qrCodeUrl'] ?? null,
                    'deeplink' => $data['deeplink'] ?? null,
                    'create_response' => $data,
                ]),
            ]);
        }

        return redirect()->away((string) $data['payUrl']);
    }

    public function momoReturn(Request $request)
    {
        $payload = $request->query();
        $order = Order::with(['payment', 'shipment'])->where('order_number', (string) $request->query('orderId'))->first();

        if (! $order || ! $this->isValidMomoResult($order, $payload)) {
            return redirect()->route($this->paymentFailureRoute())->with('error', 'Chu ky thanh toan MoMo khong hop le.');
        }

        $resultCode = (int) ($payload['resultCode'] ?? -1);
        $this->processMomoResult($order, $payload, 'redirect');

        if ($resultCode === 0) {
            session()->forget('cart');
            session()->forget('applied_coupon');

            return redirect()->route('order.success')->with('success_order', $order->order_number);
        }

        if ($resultCode === 9000) {
            return redirect()->route($this->paymentFailureRoute())->with('error', 'Giao dich MoMo da duoc uy quyen, vui long doi xac nhan.');
        }

        return redirect()->route($this->paymentFailureRoute())->with('error', 'Thanh toan MoMo that bai, vui long thu lai.');
    }

    public function momoIpn(Request $request)
    {
        $payload = $request->all();
        $order = Order::with(['payment', 'shipment'])->where('order_number', (string) ($payload['orderId'] ?? ''))->first();

        if (! $order || ! $this->isValidMomoResult($order, $payload)) {
            return response()->noContent(Response::HTTP_NO_CONTENT);
        }

        $this->processMomoResult($order, $payload, 'ipn');

        return response()->noContent(Response::HTTP_NO_CONTENT);
    }

    /**
     * @param  array<string, mixed>  $config
     */
    protected function momoConfigured(array $config): bool
    {
        return $config['base_url'] !== ''
            && $config['partner_code'] !== ''
            && $config['access_key'] !== ''
            && $config['secret_key'] !== '';
    }

    /**
     * @return array<string, string>
     */
    protected function momoConfig(): array
    {
        return [
            'base_url' => trim((string) config('services.momo.base_url', '')),
            'partner_code' => trim((string) config('services.momo.partner_code', '')),
            'access_key' => trim((string) config('services.momo.access_key', '')),
            'secret_key' => trim((string) config('services.momo.secret_key', '')),
            'redirect_url' => trim((string) config('services.momo.redirect_url', '')),
            'ipn_url' => trim((string) config('services.momo.ipn_url', '')),
            'request_type' => trim((string) config('services.momo.request_type', 'payWithMethod')),
            'store_name' => trim((string) config('services.momo.store_name', config('app.name', 'Laravel'))),
            'store_id' => trim((string) config('services.momo.store_id', 'nongsanviet')),
        ];
    }

    /**
     * @param  array<string, string>  $config
     */
    protected function momoCreateUrl(array $config): string
    {
        return rtrim($config['base_url'], '/') . '/v2/gateway/api/create';
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function momoCreateSignatureData(array $payload, string $accessKey): string
    {
        return 'accessKey=' . $accessKey
            . '&amount=' . (string) ($payload['amount'] ?? '')
            . '&extraData=' . (string) ($payload['extraData'] ?? '')
            . '&ipnUrl=' . (string) ($payload['ipnUrl'] ?? '')
            . '&orderId=' . (string) ($payload['orderId'] ?? '')
            . '&orderInfo=' . (string) ($payload['orderInfo'] ?? '')
            . '&partnerCode=' . (string) ($payload['partnerCode'] ?? '')
            . '&redirectUrl=' . (string) ($payload['redirectUrl'] ?? '')
            . '&requestId=' . (string) ($payload['requestId'] ?? '')
            . '&requestType=' . (string) ($payload['requestType'] ?? '');
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, string>  $config
     */
    protected function momoResultSignatureData(array $payload, array $config): string
    {
        return 'accessKey=' . $config['access_key']
            . '&amount=' . (string) ($payload['amount'] ?? '')
            . '&extraData=' . (string) ($payload['extraData'] ?? '')
            . '&message=' . (string) ($payload['message'] ?? '')
            . '&orderId=' . (string) ($payload['orderId'] ?? '')
            . '&orderInfo=' . (string) ($payload['orderInfo'] ?? '')
            . '&orderType=' . (string) ($payload['orderType'] ?? '')
            . '&partnerCode=' . (string) ($payload['partnerCode'] ?? '')
            . '&payType=' . (string) ($payload['payType'] ?? '')
            . '&requestId=' . (string) ($payload['requestId'] ?? '')
            . '&responseTime=' . (string) ($payload['responseTime'] ?? '')
            . '&resultCode=' . (string) ($payload['resultCode'] ?? '')
            . '&transId=' . (string) ($payload['transId'] ?? '');
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function isValidMomoResult(Order $order, array $payload): bool
    {
        $config = $this->momoConfig();
        $signature = (string) ($payload['signature'] ?? '');

        if (! $this->momoConfigured($config) || $signature === '') {
            return false;
        }

        if ((string) ($payload['partnerCode'] ?? '') !== $config['partner_code']) {
            return false;
        }

        if ((string) ($payload['orderId'] ?? '') !== $order->order_number) {
            return false;
        }

        if ((int) ($payload['amount'] ?? 0) !== (int) round((float) $order->payable_total)) {
            return false;
        }

        $expected = hash_hmac(
            'sha256',
            $this->momoResultSignatureData($payload, $config),
            $config['secret_key']
        );

        return hash_equals($expected, $signature);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function processMomoResult(Order $order, array $payload, string $channel): void
    {
        $order->loadMissing(['payment', 'shipment']);
        $payment = $order->payment;

        if (! $payment) {
            return;
        }

        $resultCode = (int) ($payload['resultCode'] ?? -1);
        $transactionCode = isset($payload['transId']) ? (string) $payload['transId'] : null;
        $previousStatus = (string) $payment->status;
        $previousTransaction = (string) ($payment->transaction_code ?? '');
        $metadata = array_merge($payment->metadata ?? [], [
            'gateway' => 'momo',
            'last_channel' => $channel,
            'last_result_code' => $resultCode,
            'last_message' => (string) ($payload['message'] ?? ''),
            'last_request_id' => (string) ($payload['requestId'] ?? ''),
            'last_response_time' => (string) ($payload['responseTime'] ?? ''),
            'last_pay_type' => (string) ($payload['payType'] ?? ''),
            'extra_data' => (string) ($payload['extraData'] ?? ''),
        ]);

        $paymentData = ['metadata' => $metadata];
        if ($transactionCode !== null && $transactionCode !== '') {
            $paymentData['transaction_code'] = $transactionCode;
        }

        if ($resultCode === 0) {
            $paymentData['status'] = 'paid';
            $paymentData['paid_at'] = $payment->paid_at ?: now();
            $payment->update($paymentData);

            app(OrderFulfillmentService::class)->apply($order);

            if ($order->status === 'pending') {
                $order->update(['status' => 'processing']);
            }

            $order->refresh()->load(['payment', 'shipment']);
            if ($previousStatus !== 'paid' || $previousTransaction !== (string) $transactionCode) {
                $order->recordStatusHistory(
                    'payment_gateway',
                    $channel === 'ipn' ? 'MoMo thanh toan thanh cong qua IPN' : 'MoMo thanh toan thanh cong',
                    $payload
                );
            }

            return;
        }

        if ($resultCode === 9000) {
            $paymentData['status'] = 'pending';
            $payment->update($paymentData);

            $order->refresh()->load(['payment', 'shipment']);
            if ($previousTransaction !== (string) $transactionCode) {
                $order->recordStatusHistory('payment_gateway', 'MoMo giao dich da duoc uy quyen', $payload);
            }

            return;
        }

        if ($previousStatus === 'paid') {
            $payment->update(['metadata' => $metadata]);

            return;
        }

        $paymentData['status'] = 'failed';
        $payment->update($paymentData);
        app(OrderFulfillmentService::class)->release($order);

        $order->refresh()->load(['payment', 'shipment']);
        if ($previousStatus !== 'failed' || $previousTransaction !== (string) $transactionCode) {
            $order->recordStatusHistory('payment_gateway', 'MoMo thanh toan that bai', $payload);
        }
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    protected function rememberMomoCreateAttempt(Order $order, array $metadata): void
    {
        $payment = $order->payment;

        if (! $payment) {
            return;
        }

        $payment->update([
            'metadata' => array_merge($payment->metadata ?? [], [
                'gateway' => 'momo',
            ], $metadata),
        ]);
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    protected function momoCreateFailure(Order $order, string $message, array $metadata = [])
    {
        $order->loadMissing(['payment', 'shipment']);
        $payment = $order->payment;

        if ($payment) {
            $payment->update([
                'status' => 'failed',
                'metadata' => array_merge($payment->metadata ?? [], [
                    'gateway' => 'momo',
                    'create_error' => $message,
                ], $metadata),
            ]);
        }

        $order->refresh()->load(['payment', 'shipment']);
        $order->recordStatusHistory('payment_gateway', 'MoMo khoi tao giao dich that bai', array_merge([
            'message' => $message,
        ], $metadata));

        return redirect()->route('checkout')->with('error', $message);
    }

    protected function paymentFailureRoute(): string
    {
        return auth()->check() ? 'checkout' : 'home';
    }
}
