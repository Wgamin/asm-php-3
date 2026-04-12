<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createShippingCarrierProduct(): Product
{
    $category = Category::create([
        'name' => 'Van chuyen noi bo',
        'slug' => 'van-chuyen-noi-bo',
    ]);

    return Product::create([
        'category_id' => $category->id,
        'name' => 'Bap cai huu co',
        'product_type' => 'simple',
        'price' => 40000,
        'stock' => 20,
        'weight_grams' => 750,
        'description' => 'Mo ta ngan',
        'content' => 'Noi dung chi tiet',
        'image' => 'products/test.jpg',
    ]);
}

it('returns internal shipping quote based on the customer region only', function () {
    $user = User::factory()->create();
    $product = createShippingCarrierProduct();

    $address = UserAddress::create([
        'user_id' => $user->id,
        'full_name' => 'Le Thi D',
        'phone' => '0900000011',
        'province' => 'Ha Noi',
        'district' => 'Cau Giay',
        'ward' => 'Dich Vong',
        'address_line' => '88 Duong Lang',
        'is_default' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->withSession([
            'cart' => [
                'p-' . $product->id => [
                    'product_id' => $product->id,
                    'variant_id' => null,
                    'name' => $product->name,
                    'quantity' => 2,
                    'price' => $product->price,
                    'image' => $product->image,
                ],
            ],
        ])
        ->getJson(route('checkout.shipping.options', [
            'selected_address_id' => $address->id,
        ]));

    $response->assertOk();
    $response->assertJsonPath('selected.key', 'fast');
    $response->assertJsonPath('selected.fee', 20000);
    $response->assertJsonPath('payable_total', 100000);

    $options = collect($response->json('options'));
    expect($options)->toHaveCount(1);
    expect($options->first()['key'])->toBe('fast');
});

it('stores the selected internal shipping rule in the shipment record', function () {
    $user = User::factory()->create();
    $product = createShippingCarrierProduct();

    $address = UserAddress::create([
        'user_id' => $user->id,
        'full_name' => 'Tran Thi E',
        'phone' => '0900000012',
        'province' => 'Ha Noi',
        'district' => 'Cau Giay',
        'ward' => 'Dich Vong',
        'address_line' => '99 Nguyen Khang',
        'is_default' => true,
    ]);

    $response = $this
        ->actingAs($user)
        ->withSession([
            'cart' => [
                'p-' . $product->id => [
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
            'selected_address_id' => $address->id,
            'shipping_provider' => 'fast',
            'payment_method' => 'cod',
        ]);

    $response->assertRedirect(route('order.success'));

    $order = Order::with(['payment', 'shipment'])->first();

    expect($order)->not->toBeNull();
    expect((float) $order->shipping_fee_amount)->toBe(20000.0);
    expect((float) $order->payable_amount)->toBe(100000.0);
    expect($order->shipment)->not->toBeNull();
    expect($order->shipment->method)->toBe('fast');
    expect($order->shipment->carrier)->toBe('Nông Sản Việt Express');
    expect((float) $order->shipment->fee_amount)->toBe(20000.0);
    expect($order->payment?->amount)->toBe(100000.0);
});
