<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    protected $fillable = ['name'];

    // Lấy các giá trị cụ thể của thuộc tính này (Ví dụ: Đỏ, Xanh)
    public function values()
    {
        return $this->hasMany(AttributeValue::class);
    }
}