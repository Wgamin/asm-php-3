<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');             // Tên sản phẩm
            $table->decimal('price', 15, 2);    // Giá sản phẩm
            $table->decimal('sale_price', 15, 2)->nullable(); // Giá giảm (khuyến mãi)
            $table->string('description');      // Mô tả ngắn (hiển thị ở danh sách)
            $table->longText('content');        // Nội dung chi tiết (bài viết giới thiệu)
            $table->string('image');            // Đường dẫn ảnh sản phẩm
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
