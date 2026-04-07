<?php

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createCheckoutProduct(): Product
{
    $category = Category::create([
        'name' => 'Rau cu',
        'slug' => 'rau-cu',
    ]);

    return Product::create([
        'category_id' => $category->id,
        'name' => 'Ca rot huu co',
        'price' => 50000,
        'stock' => 20,
        'description' => 'Mo ta ngan',
        'content' => 'Noi dung chi tiet',
        'image' => 'products/test.jpg',
    ]);
}

it('applies a valid coupon at checkout', function () {
    $user = User::factory()->create();
    $product = createCheckoutProduct();

    $coupon = Coupon::create([
        'code' => 'SAVE10',
        'name' => 'Giam 10 phan tram',
        'type' => 'percent',
        'value' => 10,
        'min_order_amount' => 50000,
        'is_active' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->withSession([
            'cart' => [
                $product->id => [
                    'name' => $product->name,
                    'quantity' => 2,
                    'price' => $product->price,
                    'image' => $product->image,
                ],
            ],
        ])
        ->post(route('checkout.coupon.apply'), [
            'coupon_code' => 'save10',
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertSessionHas('applied_coupon.id', $coupon->id);
});

it('stores order with coupon discount', function () {
    $user = User::factory()->create();
    $product = createCheckoutProduct();

    $coupon = Coupon::create([
        'code' => 'SAVE20',
        'name' => 'Giam 20 phan tram',
        'type' => 'percent',
        'value' => 20,
        'min_order_amount' => 50000,
        'is_active' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->withSession([
            'cart' => [
                $product->id => [
                    'name' => $product->name,
                    'quantity' => 2,
                    'price' => $product->price,
                    'image' => $product->image,
                ],
            ],
            'applied_coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
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
    expect((float) $order->subtotal_amount)->toBe(100000.0);
    expect((float) $order->discount_amount)->toBe(20000.0);
    expect((float) $order->total_amount)->toBe(80000.0);
    expect($order->coupon_code)->toBe('SAVE20');

    expect($coupon->fresh()->used_count)->toBe(1);

    $response->assertRedirect(route('order.success'));
    $response->assertSessionMissing('cart');
    $response->assertSessionMissing('applied_coupon');
});

it('removes applied coupon when cart no longer meets minimum order value', function () {
    $user = User::factory()->create();
    $product = createCheckoutProduct();

    $coupon = Coupon::create([
        'code' => 'MIN100',
        'name' => 'Don tu 100k',
        'type' => 'fixed',
        'value' => 10000,
        'min_order_amount' => 100000,
        'is_active' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->withSession([
            'cart' => [
                $product->id => [
                    'name' => $product->name,
                    'quantity' => 2,
                    'price' => $product->price,
                    'image' => $product->image,
                ],
            ],
            'applied_coupon' => [
                'id' => $coupon->id,
                'code' => $coupon->code,
            ],
        ])
        ->get(route('cart.update_quantity', [
            'id' => $product->id,
            'quantity' => 1,
        ]));

    $response->assertSessionMissing('applied_coupon');
});
