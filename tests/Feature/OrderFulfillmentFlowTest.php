<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

function createFulfillmentProduct(): Product
{
    $category = Category::create([
        'name' => 'Don hang flow',
        'slug' => 'don-hang-flow',
    ]);

    return Product::create([
        'category_id' => $category->id,
        'name' => 'Dua leo huu co',
        'product_type' => 'simple',
        'price' => 40000,
        'stock' => 20,
        'weight_grams' => 500,
        'description' => 'Mo ta ngan',
        'content' => 'Noi dung chi tiet',
        'image' => 'products/test.jpg',
    ]);
}

function enableFulfillmentShippingConfig(): void
{
    config([
        'shipping.default_provider' => 'ghn',
        'shipping.providers.ghn.enabled' => true,
        'shipping.providers.ghn.token' => 'ghn-test-token',
        'shipping.providers.ghn.shop_id' => '123456',
        'shipping.providers.ghtk.enabled' => false,
    ]);
}

function fakeFulfillmentShippingApis(): void
{
    Cache::flush();

    Http::fake([
        'https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/province*' => Http::response([
            'code' => 200,
            'message' => 'Success',
            'data' => [
                ['ProvinceID' => 201, 'ProvinceName' => 'Ha Noi'],
            ],
        ]),
        'https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/district*' => Http::response([
            'code' => 200,
            'message' => 'Success',
            'data' => [
                ['DistrictID' => 1450, 'DistrictName' => 'Cau Giay'],
            ],
        ]),
        'https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/ward*' => Http::response([
            'code' => 200,
            'message' => 'Success',
            'data' => [
                ['WardCode' => '12345', 'WardName' => 'Dich Vong'],
            ],
        ]),
        'https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee' => Http::response([
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'total' => 28500,
            ],
        ]),
    ]);
}

it('creates payment and shipment records with ghn shipping fee only', function () {
    enableFulfillmentShippingConfig();
    fakeFulfillmentShippingApis();

    $user = User::factory()->create();
    $product = createFulfillmentProduct();

    Warehouse::query()->create([
        'name' => 'Kho Mac Dinh',
        'phone' => '0900000099',
        'province' => 'Ha Noi',
        'district' => 'Cau Giay',
        'ward' => 'Dich Vong',
        'address_line' => '1 Duong Kho',
        'is_default' => true,
        'is_active' => true,
    ]);

    $address = UserAddress::create([
        'user_id' => $user->id,
        'full_name' => 'Le Thi C',
        'phone' => '0900000009',
        'province' => 'Ha Noi',
        'district' => 'Cau Giay',
        'ward' => 'Dich Vong',
        'address_line' => '12 Nguyen Phong Sac',
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
                    'weight_grams' => $product->weight_grams,
                ],
            ],
        ])
        ->post(route('order.store'), [
            'selected_address_id' => $address->id,
            'payment_method' => 'cod',
        ]);

    $order = Order::with(['payment', 'shipment'])->first();

    expect($order)->not->toBeNull();
    expect((float) $order->total_amount)->toBe(80000.0);
    expect((float) $order->shipping_fee_amount)->toBe(28500.0);
    expect((float) $order->payable_amount)->toBe(108500.0);
    expect($order->payment)->not->toBeNull();
    expect($order->payment->method)->toBe('cod');
    expect((float) $order->payment->amount)->toBe(108500.0);
    expect($order->payment->status)->toBe('pending');
    expect($order->shipment)->not->toBeNull();
    expect($order->shipment->method)->toBe('ghn');
    expect((float) $order->shipment->fee_amount)->toBe(28500.0);
    expect($order->shipment->status)->toBe('pending');

    $response->assertRedirect(route('order.success'));
});

it('syncs payment and shipment status when admin completes a cod order', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $customer = User::factory()->create();

    $order = Order::create([
        'user_id' => $customer->id,
        'order_number' => 'ORD-TEST-001',
        'full_name' => 'Nguyen Van A',
        'phone' => '0900000000',
        'email' => 'customer@example.com',
        'address' => '123 Duong Test',
        'subtotal_amount' => 100000,
        'discount_amount' => 0,
        'shipping_fee_amount' => 28500,
        'total_amount' => 100000,
        'payable_amount' => 128500,
        'status' => 'pending',
        'payment_method' => 'cod',
    ]);

    Payment::create([
        'order_id' => $order->id,
        'method' => 'cod',
        'provider' => 'cash_on_delivery',
        'amount' => 128500,
        'status' => 'pending',
    ]);

    Shipment::create([
        'order_id' => $order->id,
        'method' => 'ghn',
        'carrier' => 'Giao Hang Nhanh',
        'fee_amount' => 28500,
        'status' => 'pending',
    ]);

    $response = $this
        ->actingAs($admin)
        ->post(route('admin.orders.updateStatus', $order->id), [
            'status' => 'completed',
        ]);

    $response->assertSessionHasNoErrors();
    $response->assertRedirect();

    expect($order->fresh()->status)->toBe('completed');
    expect($order->payment()->first()->status)->toBe('paid');
    expect($order->payment()->first()->paid_at)->not->toBeNull();
    expect($order->shipment()->first()->status)->toBe('delivered');
    expect($order->shipment()->first()->delivered_at)->not->toBeNull();
});
