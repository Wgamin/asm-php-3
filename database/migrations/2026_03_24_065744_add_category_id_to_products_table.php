<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    // Lệnh: php artisan make:migration add_category_id_to_products_table
public function up(): void
{
    Schema::table('products', function (Blueprint $table) {
        $table->unsignedBigInteger('category_id')->after('id'); // Thêm cột sau ID
        
        // Tạo liên kết khóa ngoại (Tùy chọn nhưng nên có)
        $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
};
