<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'parent_id',
        'slug'
    ];

    /**
     * Tự động tạo slug khi lưu danh mục (nếu bạn không nhập slug)
     */
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    /* |--------------------------------------------------------------------------
    | Quan hệ (Relationships)
    |--------------------------------------------------------------------------
    */

    /**
     * Lấy danh mục cha của danh mục hiện tại
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Lấy các danh mục con trực tiếp (cấp 1)
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Lấy tất cả danh mục con (Đệ quy - Lấy hết các cấp dưới)
     */
    public function allChildren()
    {
        return $this->children()->with('allChildren');
    }

    /**
     * Quan hệ với sản phẩm
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /* |--------------------------------------------------------------------------
    | Helper Methods
    |--------------------------------------------------------------------------
    */

    /**
     * Kiểm tra xem danh mục này có phải là danh mục gốc không
     */
    public function isRoot()
    {
        return is_null($this->parent_id);
    }
}