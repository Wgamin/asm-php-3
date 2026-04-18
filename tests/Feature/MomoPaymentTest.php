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

function createMomoCheckoutProduct(): Product
{
    $category = Category::create([
        'name' => 'MoMo test',
        'slug' => 'momo-test',
    ]);

    return Product::create([
        'category_id' => $category->id,
        'name' => 'Xoai cat chu',
        'product_type' => 'simple',
        'price' => 40000,
        'stock' => 10,
        'description' => 'Mo ta ngan',
        'content' => 'Noi dung chi tiet',
        'image' => 'products/test.jpg',
    ]);
}

function enableMomoShippingConfig(): void
{
    config([
        'shipping.default_provider' => 'ghn',
        'shipping.providers.ghn.enabled' => true,
        'shipping.providers.ghn.token' => 'ghn-test-token',
        'shipping.providers.ghn.shop_id' => '123456',
        'shipping.providers.ghtk.enabled' => false,
    ]);
}

function fakeMomoShippingApis(): void
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

function enableMomoTestConfig(): void
{
    config([
        'services.momo.base_url' => 'https://test-payment.momo.vn',
        'services.momo.partner_code' => 'MOMO_PARTNER_TEST',
        'services.momo.access_key' => 'MOMO_ACCESS_TEST',
        'services.momo.secret_key' => 'MOMO_SECRET_TEST',
        'services.momo.redirect_url' => 'http://localhost/payment/momo-return',
        'services.momo.ipn_url' => 'http://localhost/payment/momo/ipn',
        'services.momo.request_type' => 'payWithMethod',
        'services.momo.store_name' => 'Nong San Viet',
        'services.momo.store_id' => 'nongsanviet',
    ]);
}

function momoSignatureForTest(array $payload): string
{
    $accessKey = (string) config('services.momo.access_key');
    $secretKey = (string) config('services.momo.secret_key');

    $raw = 'accessKey=' . $accessKey
        . '&amount=' . (string) ($payload['amount'] ?? '')
        . '&extraData=' . (string) ($payload['extraData'] ?? '')
        . '&message=' . (string) ($payload['message'] ?? '')
        . '&orderId=' . (string) ($payload['orderId'] ?? '')
        . '&orderInfo=' . (string) ($payload['orderInfo'] ?? '')
        . '&orderType=' . (string) ($payload['orderType'] ?? '')
        . '&partnerCode=' . (string) ($payload['partnerCode'] ?? '')
        . '&payType=' . (string) ($payload['payType'] ?? '')
        . '&requestId=' . (string) ($payload['requestId'] ?? '')
        . '&responseTime=' . (string) ($payload['responseTime'] ?? '')
        . '&resultCode=' . (string) ($payload['resultCode'] ?? '')
        . '&transId=' . (string) ($payload['transId'] ?? '');

    return hash_hmac('sha256', $raw, $secretKey);
}

it('redirects checkout to momo sandbox pay url', function () {
    enableMomoShippingConfig();
    enableMomoTestConfig();

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
        'https://test-payment.momo.vn/v2/gateway/api/create' => Http::response([
            'partnerCode' => 'MOMO_PARTNER_TEST',
            'requestId' => 'MOMO-1-20260418120000000',
            'orderId' => 'ORD-MOMO-001',
            'amount' => 108500,
            'responseTime' => 1710000000000,
            'message' => 'Successful.',
            'resultCode' => 0,
            'payUrl' => 'https://test-payment.momo.vn/gw_payment/transactionProcessor',
            'qrCodeUrl' => 'momo://test-qr',
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
    $product = createMomoCheckoutProduct();

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
            'payment_method' => 'momo',
        ]);

    $order = Order::with('payment')->first();

    $response->assertRedirect('https://test-payment.momo.vn/gw_payment/transactionProcessor');
    expect($order)->not->toBeNull();
    expect($order->payment?->method)->toBe('momo');
    expect($order->payment?->provider)->toBe('momo');
    expect($order->payment?->status)->toBe('pending');
    expect($order->payment?->metadata['pay_url'] ?? null)->toBe('https://test-payment.momo.vn/gw_payment/transactionProcessor');
    expect($order->payment?->metadata['inventory_applied'] ?? null)->toBeFalse();
    expect($product->fresh()->stock)->toBe(10);
});

it('marks order paid from momo ipn', function () {
    enableMomoTestConfig();

    $user = User::factory()->create();
    $product = createMomoCheckoutProduct();
    $order = Order::create([
        'user_id' => $user->id,
        'order_number' => 'ORD-MOMO-IPN-001',
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
        'payment_method' => 'momo',
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
        'method' => 'momo',
        'provider' => 'momo',
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

    $payload = [
        'partnerCode' => 'MOMO_PARTNER_TEST',
        'orderId' => $order->order_number,
        'requestId' => 'MOMO-IPN-REQ-001',
        'amount' => 120000,
        'orderInfo' => 'Thanh toan don hang ' . $order->order_number,
        'orderType' => 'momo_wallet',
        'transId' => '4088878653',
        'resultCode' => 0,
        'message' => 'Successful.',
        'payType' => 'qr',
        'responseTime' => '1721720663942',
        'extraData' => base64_encode(json_encode([
            'order_number' => $order->order_number,
            'internal_order_id' => $order->id,
            'payment_method' => 'momo',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)),
    ];
    $payload['signature'] = momoSignatureForTest($payload);

    $response = $this->postJson(route('payment.momoIpn'), $payload);

    $response->assertNoContent();

    expect($order->fresh()->status)->toBe('processing');
    expect($order->payment()->first()->status)->toBe('paid');
    expect($order->payment()->first()->transaction_code)->toBe('4088878653');
    expect($order->payment()->first()->metadata['inventory_applied'] ?? null)->toBeTrue();
    expect($product->fresh()->stock)->toBe(8);

    $this->assertDatabaseHas('order_status_histories', [
        'order_id' => $order->id,
        'source' => 'payment_gateway',
        'order_status' => 'processing',
        'payment_status' => 'paid',
        'shipment_status' => 'pending',
    ]);
});

it('redirects customer to success page after valid momo return', function () {
    enableMomoTestConfig();

    $user = User::factory()->create();
    $order = Order::create([
        'user_id' => $user->id,
        'order_number' => 'ORD-MOMO-RETURN-001',
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
        'payment_method' => 'momo',
    ]);

    Payment::create([
        'order_id' => $order->id,
        'method' => 'momo',
        'provider' => 'momo',
        'amount' => 100000,
        'status' => 'pending',
    ]);

    Shipment::create([
        'order_id' => $order->id,
        'method' => 'fast',
        'carrier' => 'Noi bo',
        'fee_amount' => 10000,
        'status' => 'pending',
    ]);

    $payload = [
        'partnerCode' => 'MOMO_PARTNER_TEST',
        'orderId' => $order->order_number,
        'requestId' => 'MOMO-RETURN-REQ-001',
        'amount' => 100000,
        'orderInfo' => 'Thanh toan don hang ' . $order->order_number,
        'orderType' => 'momo_wallet',
        'transId' => '5000000001',
        'resultCode' => 0,
        'message' => 'Successful.',
        'payType' => 'webApp',
        'responseTime' => '1721720777000',
        'extraData' => base64_encode(json_encode([
            'order_number' => $order->order_number,
            'internal_order_id' => $order->id,
            'payment_method' => 'momo',
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES)),
    ];
    $payload['signature'] = momoSignatureForTest($payload);

    $response = $this->get(route('payment.momoReturn', $payload));

    $response->assertRedirect(route('order.success'));
    $response->assertSessionHas('success_order', $order->order_number);

    expect($order->fresh()->status)->toBe('processing');
    expect($order->payment()->first()->status)->toBe('paid');
    expect($order->payment()->first()->transaction_code)->toBe('5000000001');
});
