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
        'variant_values', 
        'image'
    ];

    // Cấu hình ép kiểu dữ liệu JSON về Mảng PHP
    protected $casts = [
        'variant_values' => 'array',
        'price' => 'float',
        'sale_price' => 'float',
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Helper: Hiển thị tên biến thể dựa trên mảng JSON
     * Ví dụ: "Màu: Đỏ, Size: L"
     */
    public function getLabelAttribute()
    {
        if (!$this->variant_values) return 'Default';
        
        return collect($this->variant_values)
            ->map(fn($val, $key) => "$key: $val")
            ->implode(', ');
    }
}