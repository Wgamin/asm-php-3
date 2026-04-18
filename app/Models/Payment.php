<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'method',
        'provider',
        'amount',
        'status',
        'transaction_code',
        'paid_at',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'float',
        'paid_at' => 'datetime',
        'metadata' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getStatusTextAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'Da thanh toan',
            'failed' => 'Thanh toan that bai',
            'cancelled' => 'Da huy',
            default => 'Cho thanh toan',
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'paid' => 'bg-emerald-100 text-emerald-700',
            'failed' => 'bg-red-100 text-red-700',
            'cancelled' => 'bg-slate-200 text-slate-700',
            default => 'bg-amber-100 text-amber-700',
        };
    }

    public function getMethodTextAttribute(): string
    {
        return match ($this->method) {
            'vnpay' => 'VNPay',
            'momo' => 'MoMo',
            'zalopay' => 'ZaloPay',
            default => 'COD',
        };
    }
}
