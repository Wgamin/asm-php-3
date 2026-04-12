<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete()->unique();
            $table->string('method', 50)->default('standard');
            $table->string('carrier')->nullable();
            $table->string('tracking_code')->nullable();
            $table->decimal('fee_amount', 15, 2)->default(0);
            $table->string('status', 50)->default('pending');
            $table->timestamp('estimated_delivery_at')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
