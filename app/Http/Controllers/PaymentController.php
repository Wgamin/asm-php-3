<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

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
            $order->payment?->update([
                'status' => 'paid',
                'transaction_code' => (string) $request->query('vnp_TransactionNo'),
                'paid_at' => now(),
                'metadata' => $inputData,
            ]);

            if ($order->status === 'pending') {
                $order->update(['status' => 'processing']);
            }

            $order->refresh()->load(['payment', 'shipment']);
            $order->recordStatusHistory('payment_gateway', 'VNPay thanh toan thanh cong', $inputData);

            session()->forget('cart');
            session()->forget('applied_coupon');

            return redirect()->route('order.success')->with('success_order', $order->order_number);
        }

        $order->payment?->update([
            'status' => 'failed',
            'transaction_code' => (string) $request->query('vnp_TransactionNo'),
            'metadata' => $inputData,
        ]);

        $order->refresh()->load(['payment', 'shipment']);
        $order->recordStatusHistory('payment_gateway', 'VNPay thanh toan that bai', $inputData);

        return redirect()->route('checkout')->with('error', 'Thanh toan that bai, vui long thu lai.');
    }
}
