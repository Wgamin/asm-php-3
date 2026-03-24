<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name', 
        'price', 
        'category_id',
        'description', 
        'content', 
        'image'
    ];
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}
