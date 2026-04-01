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
        'coupon_id',
        'coupon_code',
        'total_amount',
        'status',
        'payment_method',
    ];

    protected $casts = [
        'subtotal_amount' => 'float',
        'discount_amount' => 'float',
        'total_amount' => 'float',
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
}
