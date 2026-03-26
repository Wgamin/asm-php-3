<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('order_items', function (Blueprint $table) {
        $table->id();
        $table->foreignId('order_id')->constrained()->onDelete('cascade'); // Thuộc về đơn hàng nào
        $table->foreignId('product_id')->constrained(); // Sản phẩm nào
        $table->integer('quantity');
        $table->decimal('price', 15, 2); // Lưu giá tại thời điểm mua để tránh sai lệch khi đổi giá sau này
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
