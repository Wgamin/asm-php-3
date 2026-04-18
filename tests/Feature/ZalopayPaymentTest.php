<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
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

function createZalopayCheckoutProduct(): Product
{
    $category = Category::create([
        'name' => 'ZaloPay test',
        'slug' => 'zalopay-test',
    ]);

    return Product::create([
        'category_id' => $category->id,
        'name' => 'Mit ruot do',
        'product_type' => 'simple',
        'price' => 45000,
        'stock' => 10,
        'description' => 'Mo ta ngan',
        'content' => 'Noi dung chi tiet',
        'image' => 'products/test.jpg',
    ]);
}

function enableZalopayShippingConfig(): void
{
    config([
        'shipping.default_provider' => 'ghn',
        'shipping.providers.ghn.enabled' => true,
        'shipping.providers.ghn.token' => 'ghn-test-token',
        'shipping.providers.ghn.shop_id' => '123456',
        'shipping.providers.ghtk.enabled' => false,
    ]);
}

function fakeZalopayShippingApis(): void
{
    Cache::flush();

    Http::fake([
        'https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/province*' => Http::response([
            'code' => 200,
            'data' => [
                ['ProvinceID' => 202, 'ProvinceName' => 'TP HCM'],
            ],
        ]),
        'https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/district*' => Http::response([
            'code' => 200,
            'data' => [
                ['DistrictID' => 1451, 'DistrictName' => 'Quan 1'],
            ],
        ]),
        'https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/ward*' => Http::response([
            'code' => 200,
            'data' => [
                ['WardCode' => '67890', 'WardName' => 'Ben Nghe'],
            ],
        ]),
        'https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee' => Http::response([
            'code' => 200,
            'data' => [
                'total' => 28500,
            ],
        ]),
    ]);
}

function enableZalopayTestConfig(): void
{
    config([
        'services.zalopay.base_url' => 'https://sb-openapi.zalopay.vn',
        'services.zalopay.app_id' => '2554',
        'services.zalopay.key1' => 'ZALOPAY_KEY1_TEST',
        'services.zalopay.key2' => 'ZALOPAY_KEY2_TEST',
        'services.zalopay.callback_url' => 'http://localhost/payment/zalopay/callback',
        'services.zalopay.redirect_url' => 'http://localhost/payment/zalopay-return',
        'services.zalopay.bank_code' => '',
        'services.zalopay.preferred_payment_methods' => 'domestic_card,account,international_card',
        'services.zalopay.expire_duration_seconds' => 900,
    ]);
}

function zalopayCallbackMacForTest(string $data): string
{
    return hash_hmac('sha256', $data, (string) config('services.zalopay.key2'));
}

it('redirects checkout to zalopay sandbox order url', function () {
    enableZalopayShippingConfig();
    enableZalopayTestConfig();

    Http::fake([
        'https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/province*' => Http::response([
            'code' => 200,
            'data' => [
                ['ProvinceID' => 202, 'ProvinceName' => 'TP HCM'],
            ],
        ]),
        'https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/district*' => Http::response([
            'code' => 200,
            'data' => [
                ['DistrictID' => 1451, 'DistrictName' => 'Quan 1'],
            ],
        ]),
        'https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/ward*' => Http::response([
            'code' => 200,
            'data' => [
                ['WardCode' => '67890', 'WardName' => 'Ben Nghe'],
            ],
        ]),
        'https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee' => Http::response([
            'code' => 200,
            'data' => [
                'total' => 28500,
            ],
        ]),
        'https://sb-openapi.zalopay.vn/v2/create' => Http::response([
            'return_code' => 1,
            'return_message' => 'Success',
            'sub_return_code' => 1,
            'sub_return_message' => 'Success',
            'order_url' => 'https://sbgateway.zalopay.vn/openinapp?order=abc123',
            'zp_trans_token' => 'ZPTRANS-TOKEN',
            'order_token' => 'ORDER-TOKEN',
        ]),
    ]);

    $user = User::factory()->create();
    Warehouse::query()->create([
        'name' => 'Kho Test',
        'phone' => '0900000099',
        'province' => 'TP HCM',
        'district' => 'Quan 1',
        'ward' => 'Ben Nghe',
        'address_line' => '1 Duong Kho',
        'is_default' => true,
        'is_active' => true,
    ]);
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
    $product = createZalopayCheckoutProduct();

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
            'payment_method' => 'zalopay',
        ]);

    $order = Order::with('payment')->first();

    $response->assertRedirect('https://sbgateway.zalopay.vn/openinapp?order=abc123');
    expect($order)->not->toBeNull();
    expect($order->payment?->method)->toBe('zalopay');
    expect($order->payment?->provider)->toBe('zalopay');
    expect($order->payment?->status)->toBe('pending');
    expect($order->payment?->metadata['order_url'] ?? null)->toBe('https://sbgateway.zalopay.vn/openinapp?order=abc123');
    expect($order->payment?->metadata['app_trans_id'] ?? null)->not->toBeNull();
    expect($order->payment?->metadata['inventory_applied'] ?? null)->toBeFalse();
    expect($product->fresh()->stock)->toBe(10);
});

it('marks order paid from zalopay callback', function () {
    enableZalopayTestConfig();

    $user = User::factory()->create();
    $product = createZalopayCheckoutProduct();
    $order = Order::create([
        'user_id' => $user->id,
        'order_number' => 'ORD-ZLP-CALLBACK-001',
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
        'payment_method' => 'zalopay',
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
        'method' => 'zalopay',
        'provider' => 'zalopay',
        'amount' => 120000,
        'status' => 'pending',
        'metadata' => [
            'gateway' => 'zalopay',
            'app_trans_id' => '250418_1',
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

    $data = json_encode([
        'app_id' => 2554,
        'app_trans_id' => '250418_1',
        'app_time' => 1710000000000,
        'app_user' => 'user_' . $user->id,
        'amount' => 120000,
        'embed_data' => json_encode([
            'internal_order_id' => $order->id,
            'order_number' => $order->order_number,
            'payment_method' => 'zalopay',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
        'item' => '[]',
        'zp_trans_id' => 987654321,
        'server_time' => 1710000001111,
        'channel' => 36,
        'merchant_user_id' => (string) $user->id,
        'user_fee_amount' => 0,
        'discount_amount' => 0,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

    $response = $this->postJson(route('payment.zalopayCallback'), [
        'data' => $data,
        'mac' => zalopayCallbackMacForTest($data),
        'type' => 1,
    ]);

    $response->assertOk();
    $response->assertJson([
        'return_code' => 1,
        'return_message' => 'success',
    ]);

    expect($order->fresh()->status)->toBe('processing');
    expect($order->payment()->first()->status)->toBe('paid');
    expect($order->payment()->first()->transaction_code)->toBe('987654321');
    expect($order->payment()->first()->metadata['inventory_applied'] ?? null)->toBeTrue();
    expect($product->fresh()->stock)->toBe(8);
});

it('redirects customer to success page after valid zalopay return query', function () {
    enableZalopayTestConfig();

    Http::fake([
        'https://sb-openapi.zalopay.vn/v2/query' => Http::response([
            'return_code' => 1,
            'return_message' => 'Success',
            'sub_return_code' => 1,
            'sub_return_message' => 'Success',
            'is_processing' => false,
            'amount' => 100000,
            'zp_trans_id' => 123456789,
            'server_time' => 1710000002222,
        ]),
    ]);

    $user = User::factory()->create();
    $product = createZalopayCheckoutProduct();
    $order = Order::create([
        'user_id' => $user->id,
        'order_number' => 'ORD-ZLP-RETURN-001',
        'full_name' => 'Nguyen Van A',
        'phone' => '0900000000',
        'email' => $user->email,
        'address' => '123 Duong Test',
        'subtotal_amount' => 90000,
        'discount_amount' => 0,
        'shipping_fee_amount' => 10000,
        'total_amount' => 90000,
        'payable_amount' => 100000,
        'status' => 'pending',
        'payment_method' => 'zalopay',
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'variant_id' => null,
        'quantity' => 2,
        'price' => 45000,
    ]);

    Payment::create([
        'order_id' => $order->id,
        'method' => 'zalopay',
        'provider' => 'zalopay',
        'amount' => 100000,
        'status' => 'pending',
        'metadata' => [
            'gateway' => 'zalopay',
            'app_trans_id' => '250418_2',
            'inventory_applied' => false,
            'coupon_usage_applied' => false,
        ],
    ]);

    Shipment::create([
        'order_id' => $order->id,
        'method' => 'fast',
        'carrier' => 'Noi bo',
        'fee_amount' => 10000,
        'status' => 'pending',
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('payment.zalopayReturn', [
            'order' => $order->id,
            'app_trans_id' => '250418_2',
        ]));

    $response->assertRedirect(route('order.success'));
    $response->assertSessionHas('success_order', $order->order_number);

    expect($order->fresh()->status)->toBe('processing');
    expect($order->payment()->first()->status)->toBe('paid');
    expect($order->payment()->first()->transaction_code)->toBe('123456789');
    expect($product->fresh()->stock)->toBe(8);
});
