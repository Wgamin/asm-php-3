<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\UserAddress;
use App\Models\Warehouse;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

function createShippingCarrierProduct(): Product
{
    $category = Category::create([
        'name' => 'Van chuyen API',
        'slug' => 'van-chuyen-api',
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

function fakeShippingCarrierApis(): void
{
    Cache::flush();

    Http::fake([
        'https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/province*' => Http::response([
            'code' => 200,
            'message' => 'Success',
            'data' => [
                ['ProvinceID' => 201, 'ProvinceName' => 'Hà Nội'],
                ['ProvinceID' => 202, 'ProvinceName' => 'Hồ Chí Minh'],
            ],
        ]),
        'https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/district*' => Http::response([
            'code' => 200,
            'message' => 'Success',
            'data' => [
                ['DistrictID' => 1450, 'DistrictName' => 'Quận Cầu Giấy'],
                ['DistrictID' => 1451, 'DistrictName' => 'Quận 1'],
            ],
        ]),
        'https://dev-online-gateway.ghn.vn/shiip/public-api/master-data/ward*' => Http::response([
            'code' => 200,
            'message' => 'Success',
            'data' => [
                ['WardCode' => '12345', 'WardName' => 'Dịch Vọng'],
                ['WardCode' => '67890', 'WardName' => 'Bến Nghé'],
            ],
        ]),
        'https://dev-online-gateway.ghn.vn/shiip/public-api/v2/shipping-order/fee' => Http::response([
            'code' => 200,
            'message' => 'Success',
            'data' => [
                'total' => 28500,
            ],
        ]),
        'https://services-staging.ghtklab.com/services/shipment/fee*' => Http::response([
            'success' => true,
            'message' => '',
            'fee' => [
                'name' => 'area1',
                'fee' => 30400,
                'delivery' => true,
            ],
        ]),
    ]);
}

function enableShippingCarrierTestConfig(): void
{
    config([
        'shipping.default_provider' => 'ghn',
        'shipping.providers.ghn.enabled' => true,
        'shipping.providers.ghn.token' => 'ghn-test-token',
        'shipping.providers.ghn.shop_id' => '123456',
        'shipping.providers.ghtk.enabled' => true,
        'shipping.providers.ghtk.token' => 'ghtk-test-token',
        'shipping.providers.ghtk.x_client_source' => 'S123456',
    ]);
}

it('returns shipping quotes from ghn test and ghtk staging providers only', function () {
    enableShippingCarrierTestConfig();
    fakeShippingCarrierApis();

    $user = User::factory()->create();
    $product = createShippingCarrierProduct();
    Warehouse::query()->create([
        'name' => 'Kho Test',
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
    $response->assertJsonPath('selected.key', 'ghn');

    $options = collect($response->json('options'));

    expect($options->pluck('key')->all())->toMatchArray(['ghn', 'ghtk']);
    expect($options->firstWhere('key', 'ghn')['fee'])->toBe(28500);
    expect($options->firstWhere('key', 'ghtk')['fee'])->toBe(30400);

    Http::assertSent(function ($request) {
        if (! str_contains($request->url(), '/v2/shipping-order/fee')) {
            return true;
        }

        return data_get($request->data(), 'from_district_id') === 1450
            && data_get($request->data(), 'to_district_id') === 1450
            && data_get($request->data(), 'weight') === 1500;
    });
});

it('stores the selected external shipping provider in the shipment record', function () {
    enableShippingCarrierTestConfig();
    fakeShippingCarrierApis();

    $user = User::factory()->create();
    $product = createShippingCarrierProduct();
    Warehouse::query()->create([
        'name' => 'Kho Test',
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
            'shipping_provider' => 'ghn',
            'payment_method' => 'cod',
        ]);

    $response->assertRedirect(route('order.success'));

    $order = Order::with(['payment', 'shipment'])->first();

    expect($order)->not->toBeNull();
    expect((float) $order->shipping_fee_amount)->toBe(28500.0);
    expect((float) $order->payable_amount)->toBe(108500.0);
    expect($order->shipment)->not->toBeNull();
    expect($order->shipment->method)->toBe('ghn');
    expect($order->shipment->carrier)->toBe('Giao Hàng Nhanh');
    expect((float) $order->shipment->fee_amount)->toBe(28500.0);
    expect($order->payment?->amount)->toBe(108500.0);
});
