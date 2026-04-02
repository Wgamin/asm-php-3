<?php

namespace App\Services;

use App\Models\Coupon;

class CouponService
{
    public function calculateCartSubtotal(array $cart): float
    {
        return (float) collect($cart)->sum(fn (array $item) => $item['price'] * $item['quantity']);
    }

    public function normalizeCode(?string $code): string
    {
        return strtoupper(trim((string) $code));
    }

    public function findByCode(?string $code): ?Coupon
    {
        $normalizedCode = $this->normalizeCode($code);

        if ($normalizedCode === '') {
            return null;
        }

        return Coupon::whereRaw('UPPER(code) = ?', [$normalizedCode])->first();
    }

    public function getCouponError(?Coupon $coupon, float $subtotal): ?string
    {
        if (! $coupon) {
            return 'Mã coupon không tồn tại.';
        }

        if (! $coupon->is_active) {
            return 'Coupon hiện đang tạm ngưng.';
        }

        $now = now();

        if ($coupon->starts_at && $coupon->starts_at->gt($now)) {
            return 'Coupon chưa đến thời gian sử dụng.';
        }

        if ($coupon->expires_at && $coupon->expires_at->lt($now)) {
            return 'Coupon đã hết hạn.';
        }

        if ($coupon->usage_limit !== null && $coupon->used_count >= $coupon->usage_limit) {
            return 'Coupon đã hết lượt sử dụng.';
        }

        if ($subtotal <= 0) {
            return 'Giỏ hàng trống.';
        }

        if ($coupon->min_order_amount !== null && $subtotal < $coupon->min_order_amount) {
            return 'Đơn hàng chưa đạt giá trị tối thiểu ' . number_format($coupon->min_order_amount) . 'đ để áp mã.';
        }

        return null;
    }

    public function calculateDiscount(Coupon $coupon, float $subtotal): float
    {
        $discount = $coupon->type === Coupon::TYPE_PERCENT
            ? ($subtotal * $coupon->value) / 100
            : $coupon->value;

        if ($coupon->max_discount_amount !== null) {
            $discount = min($discount, $coupon->max_discount_amount);
        }

        return round(min($discount, $subtotal), 2);
    }

    public function summarize(array $cart, ?Coupon $coupon = null): array
    {
        $subtotal = $this->calculateCartSubtotal($cart);
        $couponError = $this->getCouponError($coupon, $subtotal);

        if ($couponError !== null) {
            $coupon = null;
        }

        $discount = $coupon ? $this->calculateDiscount($coupon, $subtotal) : 0;

        return [
            'subtotal' => $subtotal,
            'discount' => $discount,
            'total' => max($subtotal - $discount, 0),
            'coupon' => $coupon,
        ];
    }

    public function getAppliedCouponFromSession(array $cart): ?Coupon
    {
        $appliedCoupon = session('applied_coupon');

        if (! $appliedCoupon || empty($cart)) {
            session()->forget('applied_coupon');

            return null;
        }

        $coupon = Coupon::find($appliedCoupon['id'] ?? null);

        if ($this->getCouponError($coupon, $this->calculateCartSubtotal($cart)) !== null) {
            session()->forget('applied_coupon');

            return null;
        }

        return $coupon;
    }

    public function storeAppliedCoupon(Coupon $coupon): void
    {
        session()->put('applied_coupon', [
            'id' => $coupon->id,
            'code' => $coupon->code,
        ]);
    }

    public function clearAppliedCoupon(): void
    {
        session()->forget('applied_coupon');
    }
}
