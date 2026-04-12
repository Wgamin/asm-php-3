<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('warehouses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone', 30);
            $table->string('province');
            $table->string('district');
            $table->string('ward');
            $table->string('address_line', 500);
            $table->boolean('is_default')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        DB::table('warehouses')->insert([
            'name' => 'Kho mặc định',
            'phone' => '0900000000',
            'province' => 'Hà Nội',
            'district' => 'Quận Cầu Giấy',
            'ward' => 'Phường Dịch Vọng',
            'address_line' => 'Số 1 Đường Test',
            'is_default' => true,
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('warehouses');
    }
};
