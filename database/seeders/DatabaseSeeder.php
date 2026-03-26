<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Tạo tài khoản Admin (Nếu chưa có)
        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
            ]
        );

        // 2. Tạo Danh mục sản phẩm
        $cat1 = Category::create(['name' => 'Rau Củ Đà Lạt']);
        $cat2 = Category::create(['name' => 'Trái Cây Nhập Khẩu']);
        $cat3 = Category::create(['name' => 'Nấm & Hạt']);

        // 3. Tạo Sản phẩm mẫu
        Product::create([
            'category_id' => $cat1->id,
            'name' => 'Súp lơ xanh hữu cơ',
            'price' => 45000,
            'description' => 'Súp lơ xanh tươi sạch, không thuốc trừ sâu, giàu dinh dưỡng.',
            'content' => 'Nội dung chi tiết về sản phẩm súp lơ xanh ở đây...', // Thêm dòng này
            'image' => 'sup-lo.jpg'
        ]);

        Product::create([
            'category_id' => $cat2->id,
            'name' => 'Táo Envy Mỹ Size L',
            'price' => 150000,
            'description' => 'Táo giòn, ngọt lịm, nhập khẩu chính ngạch.',
            'content' => 'Nội dung chi tiết về sản phẩm súp lơ xanh ở đây...', // Thêm dòng này
            'image' => 'tao.jpg'
        ]);

        Product::create([
            'category_id' => $cat3->id,
            'name' => 'Hạt Dẻ Cười Mỹ',
            'price' => 280000,
            'description' => 'Hạt dẻ rang muối, giòn tan, tốt cho tim mạch.',
            'content' => 'Nội dung chi tiết về sản phẩm súp lơ xanh ở đây...', // Thêm dòng này
            'image' => 'hat-de.jpg'
        ]);

        Product::create([
            'category_id' => $cat1->id,
            'name' => 'Cà rốt tí hon',
            'price' => 30000,
            'description' => 'Cà rốt ngọt, giòn, phù hợp làm salad.',
            'content' => 'Nội dung chi tiết về sản phẩm súp lơ xanh ở đây...', // Thêm dòng này
            'image' => 'ca-rot.jpg'
        ]);

        echo "--- Đã đổ dữ liệu mẫu thành công! --- \n";
    }
}