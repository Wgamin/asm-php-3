<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderFulfillmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
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
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashdata .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
            $query .= urlencode($key) . "=" . urlencode($value) . '&';
        }

        $vnpUrl = $vnpUrl . "?" . $query;
        if ($vnpHashSecret !== '') {
            $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnpHashSecret);
            $vnpUrl .= 'vnp_SecureHash=' . $vnpSecureHash;
        }

        return redirect($vnpUrl);
    }

    public function vnpayReturn(Request $request)
    {
        $vnpHashSecret = (string) env('VNPAY_HASH_SECRET');
        $inputData = [];
        foreach ($request->query() as $key => $value) {
            if (str_starts_with($key, 'vnp_')) {
                $inputData[$key] = $value;
            }
        }

        $receivedHash = $inputData['vnp_SecureHash'] ?? '';
        unset($inputData['vnp_SecureHash']);
        ksort($inputData);

        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData .= '&' . urlencode($key) . "=" . urlencode($value);
            } else {
                $hashData .= urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }

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

    public function createZalopayPayment(Request $request)
    {
        $order = Order::with(['payment', 'shipment', 'items.product', 'items.variant'])->findOrFail((int) $request->input('order_id'));
        $config = $this->zalopayConfig();

        if (! $this->zalopayConfigured($config)) {
            return $this->zalopayCreateFailure($order, 'ZaloPay test chua duoc cau hinh day du.');
        }

        $appTransId = $this->zalopayAppTransId($order);
        $redirectUrl = $this->zalopayRedirectUrl($order, $appTransId, $config);
        $callbackUrl = (string) ($config['callback_url'] ?: route('payment.zalopayCallback'));
        $embedData = [
            'redirecturl' => $redirectUrl,
            'internal_order_id' => $order->id,
            'order_number' => $order->order_number,
            'payment_method' => 'zalopay',
        ];

        $preferredMethods = $this->zalopayPreferredPaymentMethods($config);
        if ($preferredMethods !== []) {
            $embedData['preferred_payment_method'] = $preferredMethods;
        }

        $items = $order->items->map(function ($item) {
            $itemName = $item->product?->name ?? 'San pham';
            if (! empty($item->variant_values)) {
                $variantLabel = collect($item->variant_values)
                    ->map(fn ($value, $key) => $key . ': ' . $value)
                    ->implode(' | ');

                if ($variantLabel !== '') {
                    $itemName .= ' (' . $variantLabel . ')';
                }
            }

            return [
                'itemid' => (string) ($item->variant_id ?: $item->product_id ?: $item->id),
                'itemname' => $itemName,
                'itemprice' => (int) round((float) $item->price),
                'itemquantity' => (int) $item->quantity,
            ];
        })->values()->all();

        $appTime = (int) round(microtime(true) * 1000);
        $params = [
            'app_id' => (int) $config['app_id'],
            'app_trans_id' => $appTransId,
            'app_user' => $this->zalopayAppUser($order),
            'app_time' => $appTime,
            'amount' => (int) round((float) $order->payable_total),
            'description' => Str::limit('Thanh toan don hang ' . $order->order_number, 255, ''),
            'item' => json_encode($items, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'embed_data' => json_encode($embedData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
            'callback_url' => $callbackUrl,
            'redirect_url' => $redirectUrl,
            'expire_duration_seconds' => max((int) $config['expire_duration_seconds'], 300),
        ];

        if ($config['bank_code'] !== '') {
            $params['bank_code'] = $config['bank_code'];
        }

        $params['mac'] = hash_hmac('sha256', $this->zalopayCreateSignatureData($params), $config['key1']);

        $this->rememberZalopayCreateAttempt($order, [
            'app_trans_id' => $appTransId,
            'redirect_url' => $redirectUrl,
            'callback_url' => $callbackUrl,
        ]);

        try {
            $response = Http::asForm()
                ->acceptJson()
                ->timeout(30)
                ->post($this->zalopayCreateUrl($config), $params);
        } catch (\Throwable $e) {
            report($e);

            return $this->zalopayCreateFailure(
                $order,
                'Khong the ket noi sang ZaloPay test, vui long thu lai.',
                ['app_trans_id' => $appTransId]
            );
        }

        $data = is_array($response->json()) ? $response->json() : [];
        if (! $response->successful() || (int) ($data['return_code'] ?? -1) !== 1 || blank($data['order_url'] ?? null)) {
            return $this->zalopayCreateFailure(
                $order,
                (string) ($data['return_message'] ?? $data['sub_return_message'] ?? 'ZaloPay test tu choi tao giao dich.'),
                [
                    'app_trans_id' => $appTransId,
                    'create_response' => $data,
                    'http_status' => $response->status(),
                ]
            );
        }

        $payment = $order->payment;
        if ($payment) {
            $payment->update([
                'metadata' => array_merge($payment->metadata ?? [], [
                    'gateway' => 'zalopay',
                    'app_trans_id' => $appTransId,
                    'order_url' => $data['order_url'] ?? null,
                    'order_token' => $data['order_token'] ?? null,
                    'zp_trans_token' => $data['zp_trans_token'] ?? null,
                    'qr_code' => $data['qr_code'] ?? null,
                    'create_response' => $data,
                ]),
            ]);
        }

        return redirect()->away((string) $data['order_url']);
    }

    public function zalopayReturn(Request $request)
    {
        $order = Order::with(['payment', 'shipment', 'items.product', 'items.variant'])->find((int) $request->query('order'));
        $appTransId = trim((string) $request->query('app_trans_id', ''));

        if (! $order || ! $this->matchesZalopayAppTransId($order, $appTransId)) {
            return redirect()->route($this->paymentFailureRoute())->with('error', 'Khong xac dinh duoc giao dich ZaloPay.');
        }

        $config = $this->zalopayConfig();
        if (! $this->zalopayConfigured($config)) {
            return redirect()->route($this->paymentFailureRoute())->with('error', 'ZaloPay test chua duoc cau hinh day du.');
        }

        if ($order->payment?->status === 'paid') {
            session()->forget('cart');
            session()->forget('applied_coupon');

            return redirect()->route('order.success')->with('success_order', $order->order_number);
        }

        $payload = $this->zalopayQuery($order, $appTransId, $config);
        if ($payload === null) {
            return redirect()->route($this->paymentFailureRoute())->with('error', 'Khong the xac minh giao dich ZaloPay, vui long thu lai sau.');
        }

        $returnCode = (int) ($payload['return_code'] ?? -1);

        if ($returnCode === 1) {
            $this->processZalopaySuccess($order, $payload, 'redirect_query');
            session()->forget('cart');
            session()->forget('applied_coupon');

            return redirect()->route('order.success')->with('success_order', $order->order_number);
        }

        if ($returnCode === 3 || (bool) ($payload['is_processing'] ?? false)) {
            $this->rememberZalopayStatus($order, $payload, 'redirect_query');

            return redirect()->route($this->paymentFailureRoute())->with('error', 'Giao dich ZaloPay dang duoc xu ly, vui long doi them.');
        }

        $this->processZalopayFailure($order, $payload, 'redirect_query');

        return redirect()->route($this->paymentFailureRoute())->with('error', 'Thanh toan ZaloPay that bai, vui long thu lai.');
    }

    public function zalopayCallback(Request $request)
    {
        $payload = $request->all();
        $dataString = (string) ($payload['data'] ?? '');
        $receivedMac = (string) ($payload['mac'] ?? '');
        $config = $this->zalopayConfig();

        if (! $this->zalopayConfigured($config) || $dataString === '' || $receivedMac === '') {
            return response()->json([
                'return_code' => -1,
                'return_message' => 'invalid request',
            ]);
        }

        $expectedMac = hash_hmac('sha256', $dataString, $config['key2']);
        if (! hash_equals($expectedMac, $receivedMac)) {
            return response()->json([
                'return_code' => -1,
                'return_message' => 'invalid mac',
            ]);
        }

        $data = json_decode($dataString, true);
        if (! is_array($data)) {
            return response()->json([
                'return_code' => 0,
                'return_message' => 'invalid data',
            ]);
        }

        $order = $this->resolveZalopayOrderFromCallback($data);
        if (! $order) {
            return response()->json([
                'return_code' => 0,
                'return_message' => 'order not found',
            ]);
        }

        try {
            DB::transaction(function () use ($order, $data) {
                $this->processZalopaySuccess($order, $data, 'callback');
            });
        } catch (\Throwable $e) {
            report($e);

            return response()->json([
                'return_code' => 0,
                'return_message' => 'temporary error',
            ]);
        }

        return response()->json([
            'return_code' => 1,
            'return_message' => 'success',
        ]);
    }

    /**
     * @return array<string, string|int>
     */
    protected function zalopayConfig(): array
    {
        return [
            'base_url' => trim((string) config('services.zalopay.base_url', '')),
            'app_id' => trim((string) config('services.zalopay.app_id', '')),
            'key1' => trim((string) config('services.zalopay.key1', '')),
            'key2' => trim((string) config('services.zalopay.key2', '')),
            'callback_url' => trim((string) config('services.zalopay.callback_url', '')),
            'redirect_url' => trim((string) config('services.zalopay.redirect_url', '')),
            'bank_code' => trim((string) config('services.zalopay.bank_code', '')),
            'preferred_payment_methods' => trim((string) config('services.zalopay.preferred_payment_methods', '')),
            'expire_duration_seconds' => (int) config('services.zalopay.expire_duration_seconds', 900),
        ];
    }

    /**
     * @param  array<string, string|int>  $config
     */
    protected function zalopayConfigured(array $config): bool
    {
        return $config['base_url'] !== ''
            && $config['app_id'] !== ''
            && $config['key1'] !== ''
            && $config['key2'] !== '';
    }

    /**
     * @param  array<string, string|int>  $config
     */
    protected function zalopayCreateUrl(array $config): string
    {
        return rtrim((string) $config['base_url'], '/') . '/v2/create';
    }

    /**
     * @param  array<string, string|int>  $config
     */
    protected function zalopayQueryUrl(array $config): string
    {
        return rtrim((string) $config['base_url'], '/') . '/v2/query';
    }

    /**
     * @param  array<string, mixed>  $params
     */
    protected function zalopayCreateSignatureData(array $params): string
    {
        return (string) $params['app_id']
            . '|' . (string) $params['app_trans_id']
            . '|' . (string) $params['app_user']
            . '|' . (string) $params['amount']
            . '|' . (string) $params['app_time']
            . '|' . (string) $params['embed_data']
            . '|' . (string) $params['item'];
    }

    protected function zalopayQuerySignatureData(string $appId, string $appTransId, string $key1): string
    {
        return $appId . '|' . $appTransId . '|' . $key1;
    }

    protected function zalopayAppTransId(Order $order): string
    {
        $existing = trim((string) ($order->payment?->metadata['app_trans_id'] ?? ''));
        if ($existing !== '') {
            return $existing;
        }

        return now('Asia/Ho_Chi_Minh')->format('ymd') . '_' . $order->id;
    }

    /**
     * @param  array<string, string|int>  $config
     */
    protected function zalopayRedirectUrl(Order $order, string $appTransId, array $config): string
    {
        $baseUrl = (string) ($config['redirect_url'] ?: route('payment.zalopayReturn'));
        $separator = str_contains($baseUrl, '?') ? '&' : '?';

        return $baseUrl . $separator . http_build_query([
            'order' => $order->id,
            'app_trans_id' => $appTransId,
        ]);
    }

    /**
     * @param  array<string, string|int>  $config
     * @return array<int, string>
     */
    protected function zalopayPreferredPaymentMethods(array $config): array
    {
        return collect(explode(',', (string) $config['preferred_payment_methods']))
            ->map(fn ($method) => trim((string) $method))
            ->filter()
            ->values()
            ->all();
    }

    protected function zalopayAppUser(Order $order): string
    {
        return 'user_' . ($order->user_id ?: $order->id);
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    protected function rememberZalopayCreateAttempt(Order $order, array $metadata): void
    {
        $payment = $order->payment;

        if (! $payment) {
            return;
        }

        $payment->update([
            'metadata' => array_merge($payment->metadata ?? [], [
                'gateway' => 'zalopay',
            ], $metadata),
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     * @param  array<string, string|int>  $config
     * @return array<string, mixed>|null
     */
    protected function zalopayQuery(Order $order, string $appTransId, array $config): ?array
    {
        $params = [
            'app_id' => (string) $config['app_id'],
            'app_trans_id' => $appTransId,
        ];
        $params['mac'] = hash_hmac(
            'sha256',
            $this->zalopayQuerySignatureData($params['app_id'], $params['app_trans_id'], (string) $config['key1']),
            (string) $config['key1']
        );

        try {
            $response = Http::asForm()
                ->acceptJson()
                ->timeout(30)
                ->post($this->zalopayQueryUrl($config), $params);
        } catch (\Throwable $e) {
            report($e);

            return null;
        }

        $data = is_array($response->json()) ? $response->json() : [];

        if ($data === []) {
            return null;
        }

        $payment = $order->payment;
        if ($payment) {
            $payment->update([
                'metadata' => array_merge($payment->metadata ?? [], [
                    'gateway' => 'zalopay',
                    'last_query_response' => $data,
                ]),
            ]);
        }

        return $data;
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function processZalopaySuccess(Order $order, array $payload, string $channel): void
    {
        $order->loadMissing(['payment', 'shipment', 'items.product', 'items.variant']);
        $payment = $order->payment;

        if (! $payment) {
            return;
        }

        $transactionCode = trim((string) ($payload['zp_trans_id'] ?? $payload['zp_transid'] ?? ''));
        $previousStatus = (string) $payment->status;
        $previousTransaction = (string) ($payment->transaction_code ?? '');
        $metadata = array_merge($payment->metadata ?? [], [
            'gateway' => 'zalopay',
            'app_trans_id' => (string) ($payload['app_trans_id'] ?? ($payment->metadata['app_trans_id'] ?? '')),
            'last_channel' => $channel,
            'last_return_code' => (int) ($payload['return_code'] ?? 1),
            'last_sub_return_code' => (string) ($payload['sub_return_code'] ?? ''),
            'last_zp_trans_id' => $transactionCode,
            'last_server_time' => (string) ($payload['server_time'] ?? ''),
            'last_payload' => $payload,
        ]);

        $paymentData = [
            'status' => 'paid',
            'paid_at' => $payment->paid_at ?: now(),
            'metadata' => $metadata,
        ];

        if ($transactionCode !== '') {
            $paymentData['transaction_code'] = $transactionCode;
        }

        $payment->update($paymentData);
        app(OrderFulfillmentService::class)->apply($order);

        if ($order->status === 'pending') {
            $order->update(['status' => 'processing']);
        }

        $order->refresh()->load(['payment', 'shipment']);
        if ($previousStatus !== 'paid' || $previousTransaction !== $transactionCode) {
            $order->recordStatusHistory(
                'payment_gateway',
                $channel === 'callback' ? 'ZaloPay thanh toan thanh cong qua callback' : 'ZaloPay thanh toan thanh cong',
                $payload
            );
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function processZalopayFailure(Order $order, array $payload, string $channel): void
    {
        $order->loadMissing(['payment', 'shipment', 'items.product', 'items.variant']);
        $payment = $order->payment;

        if (! $payment) {
            return;
        }

        $transactionCode = trim((string) ($payload['zp_trans_id'] ?? $payload['zp_transid'] ?? ''));
        $previousStatus = (string) $payment->status;
        $previousTransaction = (string) ($payment->transaction_code ?? '');
        $metadata = array_merge($payment->metadata ?? [], [
            'gateway' => 'zalopay',
            'app_trans_id' => (string) ($payload['app_trans_id'] ?? ($payment->metadata['app_trans_id'] ?? '')),
            'last_channel' => $channel,
            'last_return_code' => (int) ($payload['return_code'] ?? 2),
            'last_sub_return_code' => (string) ($payload['sub_return_code'] ?? ''),
            'last_zp_trans_id' => $transactionCode,
            'last_server_time' => (string) ($payload['server_time'] ?? ''),
            'last_payload' => $payload,
        ]);

        if ($previousStatus === 'paid') {
            $payment->update(['metadata' => $metadata]);

            return;
        }

        $paymentData = [
            'status' => 'failed',
            'metadata' => $metadata,
        ];

        if ($transactionCode !== '') {
            $paymentData['transaction_code'] = $transactionCode;
        }

        $payment->update($paymentData);
        app(OrderFulfillmentService::class)->release($order);

        $order->refresh()->load(['payment', 'shipment']);
        if ($previousStatus !== 'failed' || $previousTransaction !== $transactionCode) {
            $order->recordStatusHistory('payment_gateway', 'ZaloPay thanh toan that bai', $payload);
        }
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function rememberZalopayStatus(Order $order, array $payload, string $channel): void
    {
        $order->loadMissing(['payment']);
        $payment = $order->payment;

        if (! $payment) {
            return;
        }

        $payment->update([
            'metadata' => array_merge($payment->metadata ?? [], [
                'gateway' => 'zalopay',
                'last_channel' => $channel,
                'last_return_code' => (int) ($payload['return_code'] ?? 3),
                'last_sub_return_code' => (string) ($payload['sub_return_code'] ?? ''),
                'last_payload' => $payload,
            ]),
        ]);
    }

    /**
     * @param  array<string, mixed>  $payload
     */
    protected function resolveZalopayOrderFromCallback(array $payload): ?Order
    {
        $embedData = [];
        $embedDataString = (string) ($payload['embed_data'] ?? '');

        if ($embedDataString !== '') {
            $decoded = json_decode($embedDataString, true);
            if (is_array($decoded)) {
                $embedData = $decoded;
            }
        }

        $orderId = (int) ($embedData['internal_order_id'] ?? 0);
        if ($orderId > 0) {
            return Order::with(['payment', 'shipment', 'items.product', 'items.variant'])->find($orderId);
        }

        $orderNumber = trim((string) ($embedData['order_number'] ?? ''));
        if ($orderNumber !== '') {
            return Order::with(['payment', 'shipment', 'items.product', 'items.variant'])
                ->where('order_number', $orderNumber)
                ->first();
        }

        return null;
    }

    protected function matchesZalopayAppTransId(Order $order, string $appTransId): bool
    {
        if ($appTransId === '') {
            return false;
        }

        $stored = trim((string) ($order->payment?->metadata['app_trans_id'] ?? ''));

        return $stored === '' || hash_equals($stored, $appTransId);
    }

    /**
     * @param  array<string, mixed>  $metadata
     */
    protected function zalopayCreateFailure(Order $order, string $message, array $metadata = [])
    {
        $order->loadMissing(['payment', 'shipment']);
        $payment = $order->payment;

        if ($payment) {
            $payment->update([
                'status' => 'failed',
                'metadata' => array_merge($payment->metadata ?? [], [
                    'gateway' => 'zalopay',
                    'create_error' => $message,
                ], $metadata),
            ]);
        }

        $order->refresh()->load(['payment', 'shipment']);
        $order->recordStatusHistory('payment_gateway', 'ZaloPay khoi tao giao dich that bai', array_merge([
            'message' => $message,
        ], $metadata));

        return redirect()->route('checkout')->with('error', $message);
    }

    protected function paymentFailureRoute(): string
    {
        return auth()->check() ? 'checkout' : 'home';
    }
}
