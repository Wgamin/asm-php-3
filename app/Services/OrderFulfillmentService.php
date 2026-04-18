<?php

namespace App\Services;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;

class OrderFulfillmentService
{
    public function apply(Order $order): bool
    {
        $order->loadMissing(['items.product', 'items.variant', 'payment']);

        $payment = $order->payment;
        if (! $payment) {
            return false;
        }

        $inventoryApplied = $this->inventoryApplied($payment);
        $couponApplied = $this->couponApplied($order, $payment);

        if ($inventoryApplied && $couponApplied) {
            return false;
        }

        if (! $inventoryApplied) {
            foreach ($order->items as $item) {
                if ($item->variant_id) {
                    $variant = ProductVariant::query()->lockForUpdate()->find($item->variant_id);

                    if ($variant) {
                        $variant->decrement('stock', (int) $item->quantity);
                    }

                    continue;
                }

                $product = Product::query()->lockForUpdate()->find($item->product_id);
                if ($product) {
                    $product->decrement('stock', (int) $item->quantity);
                }
            }
        }

        if (! $couponApplied && $order->coupon_id) {
            $coupon = Coupon::query()->lockForUpdate()->find($order->coupon_id);
            if ($coupon) {
                $coupon->increment('used_count');
            }
        }

        $this->syncFlags($order, true, true);

        return true;
    }

    public function release(Order $order): bool
    {
        $order->loadMissing(['items.product', 'items.variant', 'payment']);

        $payment = $order->payment;
        if (! $payment) {
            return false;
        }

        $inventoryApplied = $this->inventoryApplied($payment);
        $couponApplied = $this->couponApplied($order, $payment);

        if (! $inventoryApplied && ! $couponApplied) {
            return false;
        }

        if ($inventoryApplied) {
            foreach ($order->items as $item) {
                if ($item->variant_id) {
                    $variant = ProductVariant::query()->lockForUpdate()->find($item->variant_id);

                    if ($variant) {
                        $variant->increment('stock', (int) $item->quantity);
                    }

                    continue;
                }

                $product = Product::query()->lockForUpdate()->find($item->product_id);
                if ($product) {
                    $product->increment('stock', (int) $item->quantity);
                }
            }
        }

        if ($couponApplied && $order->coupon_id) {
            Coupon::query()
                ->lockForUpdate()
                ->whereKey($order->coupon_id)
                ->where('used_count', '>', 0)
                ->decrement('used_count');
        }

        $this->syncFlags($order, false, false);

        return true;
    }

    protected function inventoryApplied(Payment $payment): bool
    {
        $metadata = $payment->metadata ?? [];

        if (array_key_exists('inventory_applied', $metadata)) {
            return (bool) $metadata['inventory_applied'];
        }

        return true;
    }

    protected function couponApplied(Order $order, Payment $payment): bool
    {
        if (! $order->coupon_id) {
            return true;
        }

        $metadata = $payment->metadata ?? [];

        if (array_key_exists('coupon_usage_applied', $metadata)) {
            return (bool) $metadata['coupon_usage_applied'];
        }

        return true;
    }

    protected function syncFlags(Order $order, bool $inventoryApplied, bool $couponApplied): void
    {
        $payment = $order->payment;
        if (! $payment) {
            return;
        }

        $metadata = array_merge($payment->metadata ?? [], [
            'inventory_applied' => $inventoryApplied,
            'coupon_usage_applied' => $couponApplied,
        ]);

        if ($inventoryApplied) {
            $metadata['inventory_applied_at'] = now()->toDateTimeString();
            unset($metadata['inventory_released_at']);
        } else {
            $metadata['inventory_released_at'] = now()->toDateTimeString();
        }

        if ($couponApplied && $order->coupon_id) {
            $metadata['coupon_usage_applied_at'] = now()->toDateTimeString();
            unset($metadata['coupon_usage_released_at']);
        } elseif ($order->coupon_id) {
            $metadata['coupon_usage_released_at'] = now()->toDateTimeString();
        }

        $payment->update(['metadata' => $metadata]);
        $order->setRelation('payment', $payment->fresh());
    }
}
