<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    // Các trường được phép lưu vào Database
    protected $fillable = [
        'user_id', 
        'order_number', 
        'full_name', 
        'phone', 
        'email', 
        'address', 
        'note', 
        'total_amount', 
        'status', 
        'payment_method'
    ];
    
    public function getStatusTextAttribute()
    {
        return match($this->status) {
            'pending'    => 'Chờ xác nhận',
            'processing' => 'Đang chuẩn bị',
            'shipping'   => 'Đang giao hàng',
            'completed'  => 'Đã hoàn thành',
            'cancelled'  => 'Đã hủy đơn',
            default      => 'Liên hệ shop',
        };
    }

    /**
     * Tự động lấy màu sắc tương ứng
     * Cách dùng: $order->status_color
     */
    public function getStatusColorAttribute()
    {
        return match($this->status) {
            'pending'    => 'bg-amber-100 text-amber-600',
            'processing' => 'bg-blue-100 text-blue-600',
            'shipping'   => 'bg-indigo-100 text-indigo-600',
            'completed'  => 'bg-emerald-100 text-emerald-600',
            'cancelled'  => 'bg-red-100 text-red-600',
            default      => 'bg-gray-100 text-gray-600',
        };
    }

    // Thiết lập quan hệ: Một đơn hàng có nhiều sản phẩm chi tiết
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Thiết lập quan hệ: Một đơn hàng thuộc về một người dùng
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}