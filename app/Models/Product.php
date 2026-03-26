<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Product extends Model
{
    use HasFactory;

    // Cho phép lưu các trường này vào DB
    protected $fillable = [
        'category_id', 
        'name', 
        'price', 
        'description', 
        'image'
    ];

    // Quan hệ ngược lại: Một sản phẩm thuộc về một danh mục
    public function category()
    {
        return $this->belongsTo(Category::class);
    }
}