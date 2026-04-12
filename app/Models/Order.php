<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'full_name',
        'phone',
        'email',
        'address',
        'note',
        'subtotal_amount',
        'discount_amount',
        'shipping_fee_amount',
        'coupon_id',
        'coupon_code',
        'total_amount',
        'payable_amount',
        'status',
        'payment_method',
    ];

    protected $casts = [
        'subtotal_amount' => 'float',
        'discount_amount' => 'float',
        'shipping_fee_amount' => 'float',
        'total_amount' => 'float',
        'payable_amount' => 'float',
    ];

    public function getStatusTextAttribute()
    {
        return match ($this->status) {
            'pending' => 'Chờ xác nhận',
            'processing' => 'Đang chuẩn bị',
            'shipping' => 'Đang giao hàng',
            'completed' => 'Đã hoàn thành',
            'cancelled' => 'Đã hủy đơn',
            default => 'Liên hệ shop',
        };
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'pending' => 'bg-amber-100 text-amber-600',
            'processing' => 'bg-blue-100 text-blue-600',
            'shipping' => 'bg-indigo-100 text-indigo-600',
            'completed' => 'bg-emerald-100 text-emerald-600',
            'cancelled' => 'bg-red-100 text-red-600',
            default => 'bg-gray-100 text-gray-600',
        };
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function payment()
    {
        return $this->hasOne(Payment::class);
    }

    public function shipment()
    {
        return $this->hasOne(Shipment::class);
    }

    public function statusHistories()
    {
        return $this->hasMany(OrderStatusHistory::class)->latest('id');
    }

    public function getPayableTotalAttribute(): float
    {
        return (float) ($this->payable_amount ?? $this->total_amount);
    }

    public function getEstimatedCostAmountAttribute(): float
    {
        $this->loadMissing(['items.product', 'items.variant']);

        return (float) $this->items->sum(function ($item) {
            $unitCost = $item->cost_price;

            if ($unitCost === null) {
                $unitCost = $item->variant?->cost_price ?? $item->product?->cost_price ?? 0;
            }

            return (float) $unitCost * (int) $item->quantity;
        });
    }

    public function getGrossProfitAmountAttribute(): float
    {
        return (float) $this->payable_total - (float) $this->estimated_cost_amount;
    }

    public function canBeCancelledByCustomer(): bool
    {
        if ($this->status !== 'pending') {
            return false;
        }

        $paymentStatus = $this->relationLoaded('payment')
            ? $this->payment?->status
            : $this->payment()?->value('status');

        return $paymentStatus !== 'paid';
    }

    public function recordStatusHistory(string $source, ?string $message = null, ?array $payload = null): void
    {
        $this->loadMissing(['payment', 'shipment']);

        $this->statusHistories()->create([
            'source' => $source,
            'order_status' => $this->status,
            'payment_status' => $this->payment?->status,
            'shipment_status' => $this->shipment?->status,
            'message' => $message,
            'payload' => $payload,
        ]);
    }
}
