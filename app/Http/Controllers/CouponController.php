<?php

namespace App\Http\Controllers;

use App\Services\CouponService;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function apply(Request $request, CouponService $couponService)
    {
        $couponCode = $couponService->normalizeCode($request->input('coupon_code'));

        if ($couponCode === '') {
            return back()->with('error', 'Vui lòng nhập mã coupon.');
        }

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return redirect()->route('products.index')->with('error', 'Giỏ hàng trống!');
        }

        $coupon = $couponService->findByCode($couponCode);
        $couponError = $couponService->getCouponError($coupon, $couponService->calculateCartSubtotal($cart));

        if ($couponError !== null) {
            return back()->with('error', $couponError)->withInput();
        }

        $couponService->storeAppliedCoupon($coupon);

        return back()->with('success', 'Áp dụng coupon thành công.');
    }

    public function remove(CouponService $couponService)
    {
        $couponService->clearAppliedCoupon();

        return back()->with('success', 'Đã gỡ coupon khỏi đơn hàng.');
    }
}
