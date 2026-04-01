<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    // Cho phép lưu các trường này vào DB
    // Cần thêm 'product_type' và 'slug' (nếu bạn có dùng slug)
    protected $fillable = [
        'category_id', 
        'name', 
        // 'slug',
        'product_type', // QUAN TRỌNG: Để phân biệt simple/variable
        'price',
        'sale_price', // Thêm trường sale_price
        'content',
        'description', 
        'image'
    ];

    /**
     * Quan hệ: Một sản phẩm thuộc về một danh mục
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Quan hệ: Một sản phẩm có nhiều biến thể
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id');
    }

    /**
     * Helper: Kiểm tra nhanh xem sản phẩm có phải hàng biến thể không
     */
    public function isVariable()
    {
        return $this->product_type === 'variable';
    }

    /**
     * Helper: Lấy giá hiển thị (Lấy giá gốc hoặc giá thấp nhất của biến thể)
     */
    public function getDisplayPrice()
    {
        if ($this->isVariable() && $this->variants->count() > 0) {
            return $this->variants->min('price');
        }
        return $this->price;
    }
    public function images() {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order', 'asc');
    }
}