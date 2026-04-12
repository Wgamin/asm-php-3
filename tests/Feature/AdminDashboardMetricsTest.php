<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createDashboardProduct(): Product
{
    $category = Category::create([
        'name' => 'Dashboard',
        'slug' => 'dashboard',
    ]);

    return Product::create([
        'category_id' => $category->id,
        'name' => 'Dua leo huu co',
        'product_type' => 'simple',
        'price' => 100000,
        'sale_price' => null,
        'cost_price' => 60000,
        'stock' => 50,
        'description' => 'Mo ta ngan',
        'content' => 'Noi dung chi tiet',
        'image' => 'products/test.jpg',
    ]);
}

it('shows real dashboard metrics from database', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $firstUser = User::factory()->create([
        'role' => 'user',
        'name' => 'Khach A',
    ]);

    $secondUser = User::factory()->create([
        'role' => 'user',
        'name' => 'Khach B',
    ]);

    $product = createDashboardProduct();

    $currentOrder = Order::create([
        'user_id' => $firstUser->id,
        'order_number' => 'ORD-DASH-CURRENT',
        'full_name' => 'Khach A',
        'phone' => '0900000001',
        'email' => $firstUser->email,
        'address' => 'Ha Noi',
        'subtotal_amount' => 100000,
        'discount_amount' => 0,
        'shipping_fee_amount' => 20000,
        'total_amount' => 100000,
        'payable_amount' => 120000,
        'status' => 'completed',
        'payment_method' => 'cod',
    ]);

    $currentOrder->forceFill([
        'created_at' => now()->startOfMonth()->addDay(),
        'updated_at' => now()->startOfMonth()->addDay(),
    ])->save();

    OrderItem::create([
        'order_id' => $currentOrder->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price' => 100000,
        'cost_price' => 60000,
    ]);

    $previousOrder = Order::create([
        'user_id' => $secondUser->id,
        'order_number' => 'ORD-DASH-PREV',
        'full_name' => 'Khach B',
        'phone' => '0900000002',
        'email' => $secondUser->email,
        'address' => 'TP HCM',
        'subtotal_amount' => 80000,
        'discount_amount' => 0,
        'shipping_fee_amount' => 10000,
        'total_amount' => 80000,
        'payable_amount' => 90000,
        'status' => 'completed',
        'payment_method' => 'cod',
    ]);

    $previousOrder->forceFill([
        'created_at' => now()->subMonthNoOverflow()->startOfMonth()->addDays(2),
        'updated_at' => now()->subMonthNoOverflow()->startOfMonth()->addDays(2),
    ])->save();

    OrderItem::create([
        'order_id' => $previousOrder->id,
        'product_id' => $product->id,
        'quantity' => 1,
        'price' => 80000,
        'cost_price' => 50000,
    ]);

    $response = $this
        ->actingAs($admin)
        ->get(route('admin.dashboard'));

    $response->assertOk();
    $response->assertSee('Tổng quan hệ thống');
    $response->assertSee('Dua leo huu co');
    $response->assertSee('ORD-DASH-CURRENT');
    $response->assertSee('120,000');
    $response->assertSee('60,000');
});
