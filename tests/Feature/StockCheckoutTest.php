<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createStockCategory(): Category
{
    return Category::create([
        'name' => 'Hang test',
        'slug' => 'hang-test',
    ]);
}

it('prevents checkout when quantity exceeds stock', function () {
    $user = User::factory()->create();
    $category = createStockCategory();

    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Rau muong',
        'product_type' => 'simple',
        'price' => 30000,
        'stock' => 1,
        'description' => 'Mo ta',
        'content' => 'Noi dung',
        'image' => 'products/test.jpg',
    ]);

    $response = $this
        ->actingAs($user)
        ->withSession([
            'cart' => [
                'p-'.$product->id => [
                    'product_id' => $product->id,
                    'variant_id' => null,
                    'name' => $product->name,
                    'quantity' => 2,
                    'price' => $product->price,
                    'image' => $product->image,
                ],
            ],
        ])
        ->post(route('order.store'), [
            'full_name' => 'Nguyen Van A',
            'phone' => '0900000000',
            'address' => '123 Duong Test',
            'payment_method' => 'cod',
        ]);

    $response->assertSessionHasErrors('cart');
    expect(Order::count())->toBe(0);
    expect($product->fresh()->stock)->toBe(1);
});

it('stores variant in order_items and deducts variant stock', function () {
    $user = User::factory()->create();
    $category = createStockCategory();

    $product = Product::create([
        'category_id' => $category->id,
        'name' => 'Ao thun',
        'product_type' => 'variable',
        'price' => 0,
        'stock' => 0,
        'description' => 'Mo ta',
        'content' => 'Noi dung',
        'image' => 'products/test.jpg',
    ]);

    $variant = ProductVariant::create([
        'product_id' => $product->id,
        'sku' => 'TSHIRT-RED-M',
        'price' => 120000,
        'sale_price' => 100000,
        'stock' => 3,
        'variant_values' => [
            'Mau' => 'Do',
            'Size' => 'M',
        ],
        'image' => 'variants/test.jpg',
    ]);

    $response = $this
        ->actingAs($user)
        ->withSession([
            'cart' => [
                'v-'.$variant->id => [
                    'product_id' => $product->id,
                    'variant_id' => $variant->id,
                    'name' => $product->name,
                    'quantity' => 2,
                    'price' => 100000,
                    'image' => 'variants/test.jpg',
                    'variant_values' => [
                        'Mau' => 'Do',
                        'Size' => 'M',
                    ],
                    'variant_label' => 'Mau: Do | Size: M',
                    'sku' => $variant->sku,
                ],
            ],
        ])
        ->post(route('order.store'), [
            'full_name' => 'Nguyen Van A',
            'phone' => '0900000000',
            'address' => '123 Duong Test',
            'payment_method' => 'cod',
        ]);

    $order = Order::first();

    expect($order)->not->toBeNull();
    $orderItem = $order->items()->first();
    expect($orderItem)->not->toBeNull();
    expect($orderItem->variant_id)->toBe($variant->id);
    expect($orderItem->variant_sku)->toBe('TSHIRT-RED-M');
    expect($orderItem->variant_values)->toMatchArray([
        'Mau' => 'Do',
        'Size' => 'M',
    ]);

    expect($variant->fresh()->stock)->toBe(1);
    $response->assertRedirect(route('order.success'));
});
