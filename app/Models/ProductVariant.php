<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    protected $fillable = [
        'product_id',
        'sku',
        'price',
        'sale_price',
        'stock',
        'variant_values', // Nơi lưu {"Màu": "Đỏ", "Size": "L"}
        'image'
    ];

    // QUAN TRỌNG NHẤT: Tự động convert JSON <-> Array
    protected $casts = [
        'variant_values' => 'array',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}