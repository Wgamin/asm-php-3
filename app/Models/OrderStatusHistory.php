<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderStatusHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'source',
        'order_status',
        'payment_status',
        'shipment_status',
        'message',
        'payload',
    ];

    protected $casts = [
        'payload' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function getSourceTextAttribute(): string
    {
        return match ($this->source) {
            'admin' => 'Quản trị viên',
            'customer' => 'Khách hàng',
            'payment_gateway' => 'Cổng thanh toán',
            'webhook' => 'Bên thứ ba / webhook',
            default => 'Hệ thống',
        };
    }
}
