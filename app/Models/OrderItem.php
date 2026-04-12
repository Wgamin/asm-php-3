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
        'variant_id',
        'quantity',
        'price',
        'cost_price',
        'variant_sku',
        'variant_values',
    ];

    protected $casts = [
        'price' => 'float',
        'cost_price' => 'float',
        'variant_values' => 'array',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function getCostTotalAttribute(): float
    {
        return (float) ($this->cost_price ?? 0) * (int) $this->quantity;
    }

    public function getProfitAmountAttribute(): float
    {
        return ((float) $this->price - (float) ($this->cost_price ?? 0)) * (int) $this->quantity;
    }
}
