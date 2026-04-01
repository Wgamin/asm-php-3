<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Attribute extends Model
{
    use HasFactory;

    protected $fillable = ['name'];

    /**
     * Một thuộc tính có nhiều giá trị con
     */
    public function attributeValues()
    {
        // Lưu ý: Tên hàm phải khớp với tên bạn gọi trong Controller (attributeValues)
        return $this->hasMany(AttributeValue::class, 'attribute_id', 'id');
    }
}