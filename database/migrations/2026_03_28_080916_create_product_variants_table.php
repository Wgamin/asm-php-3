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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->string('sku')->unique()->nullable(); // Mã quản lý kho riêng
            $table->decimal('price', 15, 2)->default(0); // Giá riêng cho biến thể
            $table->string('image')->nullable(); // Ảnh riêng cho biến thể
            $table->decimal('sale_price', 15, 2)->nullable(); // Giá khuyến mãi riêng
            $table->integer('stock')->default(0); // Số lượng trong kho

            // Lưu tổ hợp thuộc tính dưới dạng JSON (Ví dụ: {"Màu": "Đỏ", "Size": "L"})
            $table->json('variant_values')->nullable(); 
            
            $table->string('image')->nullable(); // Ảnh riêng cho biến thể
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};
