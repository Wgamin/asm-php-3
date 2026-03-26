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
    Schema::create('orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Link tới người dùng
        $table->string('order_number')->unique(); // Mã đơn hàng (VD: ORD-12345)
        $table->string('full_name');
        $table->string('phone');
        $table->string('email');
        $table->text('address');
        $table->text('note')->nullable();
        $table->decimal('total_amount', 15, 2); // Tổng tiền đơn hàng
        $table->enum('status', ['pending', 'processing', 'shipping', 'completed', 'cancelled'])->default('pending');
        $table->enum('payment_method', ['cod', 'banking'])->default('cod');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
