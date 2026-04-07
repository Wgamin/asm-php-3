<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CouponService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function checkout(CouponService $couponService)
    {
        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('products.index')->with('error', 'Giỏ hàng trống!');
        }

        $appliedCoupon = $couponService->getAppliedCouponFromSession($cart);
        $summary = $couponService->summarize($cart, $appliedCoupon);

        return view('checkout', compact('cart', 'summary', 'appliedCoupon'));
    }

    public function store(Request $request, CouponService $couponService)
    {
        $request->validate([
            'full_name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'payment_method' => 'required',
            'note' => 'nullable|string',
        ]);

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('products.index')->with('error', 'Giỏ hàng trống!');
        }

        $summary = $couponService->summarize($cart);

        DB::beginTransaction();

        try {
            $coupon = null;
            $appliedCouponSession = session('applied_coupon');

            if ($appliedCouponSession) {
                $coupon = Coupon::whereKey($appliedCouponSession['id'] ?? null)->lockForUpdate()->first();
                $couponError = $couponService->getCouponError($coupon, $summary['subtotal']);

                if ($couponError !== null) {
                    DB::rollBack();
                    $couponService->clearAppliedCoupon();

                    return back()->with('error', $couponError)->withInput();
                }

                $summary = $couponService->summarize($cart, $coupon);
            }

            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'email' => Auth::user()->email,
                'address' => $request->address,
                'note' => $request->note,
                'subtotal_amount' => $summary['subtotal'],
                'discount_amount' => $summary['discount'],
                'coupon_id' => $coupon?->id,
                'coupon_code' => $coupon?->code,
                'total_amount' => $summary['total'],
                'status' => 'pending', // Luôn set pending lúc đầu, VNPAY trả về thành công mới đổi trạng thái
                'payment_method' => $request->payment_method,
            ]);

            foreach ($cart as $id => $details) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $id,
                    'quantity' => $details['quantity'],
                    'price' => $details['price'],
                ]);
            }

            if ($coupon) {
                $coupon->increment('used_count');
            }

            DB::commit();

            // ==========================================
            // CẬP NHẬT: XỬ LÝ CHUYỂN HƯỚNG THANH TOÁN
            // ==========================================
            
            // Nếu chọn thanh toán VNPAY
            if ($request->payment_method === 'vnpay') {
                // Gán thêm thông tin đơn hàng vào request để PaymentController dùng
                $request->merge([
                    'order_id' => $order->id,
                    'total_amount' => $order->total_amount
                ]);

                // Trỏ sang hàm createPayment của PaymentController
                return app(\App\Http\Controllers\PaymentController::class)->createPayment($request);
            }

            // Nếu chọn COD
            session()->forget('cart');
            $couponService->clearAppliedCoupon();

            return redirect()->route('order.success')->with('success_order', $order->order_number);

        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->with('error', 'Lỗi thật sự là: ' . $e->getMessage() . ' ở dòng ' . $e->getLine())->withInput();
        }
    }

    public function success()
    {
        $orderNumber = session('success_order');

        if (! $orderNumber) {
            return redirect()->route('home');
        }

        return view('order_success', ['order_number' => $orderNumber]);
    }
}
