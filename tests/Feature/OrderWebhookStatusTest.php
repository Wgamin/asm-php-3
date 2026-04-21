<?php

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Category;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('syncs order status from webhook and stores history', function () {
    $user = User::factory()->create();
    $category = Category::create([
        'name' => 'Webhook test',
        'slug' => 'webhook-test',
    ]);
    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Cam sanh',
        'product_type' => 'simple',
        'price' => 50000,
        'stock' => 6,
        'description' => 'Mo ta',
        'content' => 'Noi dung',
        'image' => 'products/test.jpg',
    ]);

    $order = Order::create([
        'user_id' => $user->id,
        'order_number' => 'ORD-WEBHOOK-001',
        'full_name' => 'Nguyen Van A',
        'phone' => '0900000000',
        'email' => $user->email,
        'address' => '123 Duong Test',
        'subtotal_amount' => 100000,
        'discount_amount' => 0,
        'shipping_fee_amount' => 20000,
        'total_amount' => 100000,
        'payable_amount' => 120000,
        'status' => 'pending',
        'payment_method' => 'vnpay',
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'variant_id' => null,
        'quantity' => 2,
        'price' => 50000,
    ]);

    Payment::create([
        'order_id' => $order->id,
        'method' => 'vnpay',
        'provider' => 'vnpay',
        'amount' => 120000,
        'status' => 'pending',
        'metadata' => [
            'inventory_applied' => false,
            'coupon_usage_applied' => false,
        ],
    ]);

    Shipment::create([
        'order_id' => $order->id,
        'method' => 'fast',
        'carrier' => 'Noi bo',
        'fee_amount' => 20000,
        'status' => 'pending',
    ]);

    $response = $this->postJson(route('webhooks.orders.update', $order), [
        'status' => 'shipping',
        'payment_status' => 'paid',
        'shipment_status' => 'shipping',
        'transaction_code' => 'VNP-123456',
        'tracking_code' => 'TRACK-001',
        'provider' => 'vnpay-webhook',
        'message' => 'Dong bo tu ben thu ba',
    ]);

    $response->assertOk();
    $response->assertJson([
        'ok' => true,
        'order_status' => 'shipping',
        'payment_status' => 'paid',
        'shipment_status' => 'shipping',
    ]);

    expect($order->fresh()->status)->toBe('shipping');
    expect($order->payment()->first()->status)->toBe('paid');
    expect($order->payment()->first()->transaction_code)->toBe('VNP-123456');
    expect($order->shipment()->first()->status)->toBe('shipping');
    expect($order->shipment()->first()->tracking_code)->toBe('TRACK-001');
    expect($order->payment()->first()->metadata['inventory_applied'] ?? null)->toBeTrue();
    expect($product->fresh()->stock)->toBe(4);

    $this->assertDatabaseHas('order_status_histories', [
        'order_id' => $order->id,
        'source' => 'webhook',
        'order_status' => 'shipping',
        'payment_status' => 'paid',
        'shipment_status' => 'shipping',
    ]);
});
