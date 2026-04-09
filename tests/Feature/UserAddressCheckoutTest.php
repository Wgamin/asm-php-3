<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createAddressCheckoutProduct(): Product
{
    $category = Category::create([
        'name' => 'Dia chi test',
        'slug' => 'dia-chi-test',
    ]);

    return Product::create([
        'category_id' => $category->id,
        'name' => 'Ca chua huu co',
        'product_type' => 'simple',
        'price' => 40000,
        'stock' => 10,
        'description' => 'Mo ta ngan',
        'content' => 'Noi dung chi tiet',
        'image' => 'products/test.jpg',
    ]);
}

it('creates the first shipping address as default', function () {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->post(route('profile.addresses.store'), [
            'active_tab' => 'addresses',
            'full_name' => 'Nguyen Van A',
            'phone' => '0900000000',
            'province' => 'Ha Noi',
            'district' => 'Nam Tu Liem',
            'ward' => 'Phuong X',
            'address_line' => '123 Duong Test',
        ]);

    $response->assertRedirect(route('profile', ['tab' => 'addresses']));

    $this->assertDatabaseHas('user_addresses', [
        'user_id' => $user->id,
        'full_name' => 'Nguyen Van A',
        'is_default' => true,
    ]);
});

it('switches the default shipping address', function () {
    $user = User::factory()->create();

    $first = UserAddress::create([
        'user_id' => $user->id,
        'full_name' => 'Nguoi nhan 1',
        'phone' => '0900000001',
        'province' => 'Ha Noi',
        'district' => 'Cau Giay',
        'ward' => 'Dich Vong',
        'address_line' => 'So 1',
        'is_default' => true,
    ]);

    $second = UserAddress::create([
        'user_id' => $user->id,
        'full_name' => 'Nguoi nhan 2',
        'phone' => '0900000002',
        'province' => 'Ha Noi',
        'district' => 'Thanh Xuan',
        'ward' => 'Nhan Chinh',
        'address_line' => 'So 2',
        'is_default' => false,
    ]);

    $response = $this
        ->actingAs($user)
        ->patch(route('profile.addresses.default', $second));

    $response->assertRedirect(route('profile', ['tab' => 'addresses']));

    expect($first->fresh()->is_default)->toBeFalse();
    expect($second->fresh()->is_default)->toBeTrue();
});

it('stores order using the selected shipping address snapshot', function () {
    $user = User::factory()->create();
    $address = UserAddress::create([
        'user_id' => $user->id,
        'full_name' => 'Le Thi B',
        'phone' => '0900000003',
        'province' => 'TP HCM',
        'district' => 'Quan 1',
        'ward' => 'Ben Nghe',
        'address_line' => '45 Nguyen Hue',
        'is_default' => true,
    ]);

    $product = createAddressCheckoutProduct();

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
            'payment_method' => 'cod',
        ]);

    $order = Order::first();

    expect($order)->not->toBeNull();
    expect($order->full_name)->toBe('Le Thi B');
    expect($order->phone)->toBe('0900000003');
    expect($order->address)->toContain('45 Nguyen Hue');
    expect($order->address)->toContain('Ben Nghe');

    $response->assertRedirect(route('order.success'));
});
