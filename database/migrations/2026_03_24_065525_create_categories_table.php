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
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            
            // Cột lưu ID danh mục cha (nullable vì danh mục gốc sẽ không có cha)
            $table->unsignedBigInteger('parent_id')->nullable(); 
            
            // Thiết lập khóa ngoại trỏ ngược lại bảng categories
            // Khi danh mục cha bị xóa, các danh mục con sẽ bị xóa theo (onDelete cascade)
            $table->foreign('parent_id')
                ->references('id')
                ->on('categories')
                ->onDelete('cascade');

            $table->string('slug')->unique(); // Thường danh mục cần slug để làm SEO URL
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categories');
    }
};
