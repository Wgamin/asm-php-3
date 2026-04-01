<?php

namespace App\Imports;

use App\Models\Product;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow; // Bắt buộc phải có

class ProductsImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Bỏ qua nếu dòng trong Excel không có tên sản phẩm
        if (!isset($row['ten_san_pham'])) {
            return null;
        }

        return new Product([
            // Map tên cột trong Excel (viết thường, thay khoảng trắng bằng gạch dưới) với cột trong Database
            'name'         => $row['ten_san_pham'],
            'slug'         => Str::slug($row['ten_san_pham']) . '-' . time(),
            'category_id'  => $row['id_danh_muc'], 
            'product_type' => 'simple', 
            'price'        => $row['gia_ban'] ?? 0,
            'sale_price'   => $row['gia_giam'] ?? null,
            'stock'        => $row['ton_kho'] ?? 0,
            'description'  => $row['mo_ta'] ?? null,
        ]);
    }
}