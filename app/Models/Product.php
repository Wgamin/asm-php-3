<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'product_type',
        'price',
        'sale_price',
        'cost_price',
        'stock',
        'weight_grams',
        'content',
        'description',
        'image',
    ];

    protected $casts = [
        'price' => 'float',
        'sale_price' => 'float',
        'cost_price' => 'float',
        'weight_grams' => 'integer',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id');
    }

    public function reviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    public function approvedReviews()
    {
        return $this->hasMany(ProductReview::class)->where('is_approved', true);
    }

    public function isVariable()
    {
        return $this->product_type === 'variable';
    }

    public function getDisplayPrice()
    {
        if ($this->isVariable() && $this->variants->count() > 0) {
            return $this->variants->min('price');
        }

        return $this->price;
    }

    public function getEffectivePriceAttribute(): float
    {
        if ($this->isVariable() && $this->variants->count() > 0) {
            return (float) $this->variants->min(fn ($variant) => $variant->effective_price);
        }

        return (float) (($this->sale_price ?? 0) > 0 ? $this->sale_price : $this->price);
    }

    public function getEffectiveCostPriceAttribute(): float
    {
        if ($this->isVariable() && $this->variants->count() > 0) {
            return (float) $this->variants->min('cost_price');
        }

        return (float) ($this->cost_price ?? 0);
    }

    public function images()
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order', 'asc');
    }
}
