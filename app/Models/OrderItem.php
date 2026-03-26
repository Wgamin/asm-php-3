<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id', 
        'product_id', 
        'quantity', 
        'price'
    ];

    // Thiết lập quan hệ: Một chi tiết đơn hàng thuộc về một đơn hàng tổng
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Thiết lập quan hệ: Một chi tiết đơn hàng trỏ đến một sản phẩm
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}