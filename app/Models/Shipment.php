<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Shipment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'method',
        'carrier',
        'tracking_code',
        'fee_amount',
        'status',
        'estimated_delivery_at',
        'shipped_at',
        'delivered_at',
        'notes',
    ];

    protected $casts = [
        'fee_amount' => 'float',
        'estimated_delivery_at' => 'datetime',
        'shipped_at' => 'datetime',
        'delivered_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'preparing' => 'Dang dong goi',
            'shipping' => 'Dang giao hang',
            'delivered' => 'Da giao',
            'cancelled' => 'Da huy',
            default => 'Cho xu ly',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'preparing' => 'bg-blue-100 text-blue-700',
            'shipping' => 'bg-indigo-100 text-indigo-700',
            'delivered' => 'bg-emerald-100 text-emerald-700',
            'cancelled' => 'bg-red-100 text-red-700',
            default => 'bg-amber-100 text-amber-700',
        };
    }

    public function getMethodTextAttribute(): string
    {
        return match ($this->method) {
            'fast' => 'Giao nhanh nội thành',
            'standard' => 'Giao hàng tiêu chuẩn',
            'free_shipping' => 'Miễn phí vận chuyển',
            'ghn' => 'GHN',
            'ghtk' => 'GHTK',
            default => 'Chua xac dinh',
        };
    }
}
