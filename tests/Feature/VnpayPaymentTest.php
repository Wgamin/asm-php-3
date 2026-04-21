<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function setVnpayTestEnv(): void
{
    putenv('VNPAY_URL=https://sandbox.vnpayment.vn/paymentv2/vpcpay.html');
    putenv('VNPAY_RETURN_URL=http://localhost/payment/vnpay-return');
    putenv('VNPAY_TMN_CODE=VNPAYTEST');
    putenv('VNPAY_HASH_SECRET=VNPAYSECRET');

    $_ENV['VNPAY_URL'] = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
    $_ENV['VNPAY_RETURN_URL'] = 'http://localhost/payment/vnpay-return';
    $_ENV['VNPAY_TMN_CODE'] = 'VNPAYTEST';
    $_ENV['VNPAY_HASH_SECRET'] = 'VNPAYSECRET';
    $_SERVER['VNPAY_URL'] = 'https://sandbox.vnpayment.vn/paymentv2/vpcpay.html';
    $_SERVER['VNPAY_RETURN_URL'] = 'http://localhost/payment/vnpay-return';
    $_SERVER['VNPAY_TMN_CODE'] = 'VNPAYTEST';
    $_SERVER['VNPAY_HASH_SECRET'] = 'VNPAYSECRET';
}

function createVnpayTestProduct(): Product
{
    $category = Category::create([
        'name' => 'VNPay test',
        'slug' => 'vnpay-test',
    ]);

    return Product::create([
        'category_id' => $category->id,
        'name' => 'Buoi da xanh',
        'product_type' => 'simple',
        'price' => 60000,
        'stock' => 5,
        'description' => 'Mo ta',
        'content' => 'Noi dung',
        'image' => 'products/test.jpg',
    ]);
}

function vnpaySecureHashForTest(array $payload): string
{
    ksort($payload);

    return hash_hmac('sha512', urldecode(http_build_query($payload)), 'VNPAYSECRET');
}

it('marks order paid from vnpay return and deducts stock once', function () {
    setVnpayTestEnv();

    $user = User::factory()->create();
    $product = createVnpayTestProduct();

    $order = Order::create([
        'user_id' => $user->id,
        'order_number' => 'ORD-VNPAY-001',
        'full_name' => 'Nguyen Van A',
        'phone' => '0900000000',
        'email' => $user->email,
        'address' => '123 Duong Test',
        'subtotal_amount' => 120000,
        'discount_amount' => 0,
        'shipping_fee_amount' => 20000,
        'total_amount' => 120000,
        'payable_amount' => 140000,
        'status' => 'pending',
        'payment_method' => 'vnpay',
    ]);

    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'variant_id' => null,
        'quantity' => 2,
        'price' => 60000,
    ]);

    Payment::create([
        'order_id' => $order->id,
        'method' => 'vnpay',
        'provider' => 'vnpay',
        'amount' => 140000,
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
        'vnp_Amount' => 14000000,
        'vnp_Command' => 'pay',
        'vnp_CreateDate' => '20260418120000',
        'vnp_CurrCode' => 'VND',
        'vnp_Locale' => 'vn',
        'vnp_OrderInfo' => 'Thanh toan don hang ' . $order->order_number,
        'vnp_OrderType' => 'billpayment',
        'vnp_ResponseCode' => '00',
        'vnp_TmnCode' => 'VNPAYTEST',
        'vnp_TransactionNo' => '99887766',
        'vnp_TxnRef' => (string) $order->id,
        'vnp_Version' => '2.1.0',
    ];
    $payload['vnp_SecureHash'] = vnpaySecureHashForTest($payload);

    $response = $this->actingAs($user)->get(route('payment.vnpayReturn', $payload));

    $response->assertRedirect(route('order.success'));
    $response->assertSessionHas('success_order', $order->order_number);

    expect($order->fresh()->status)->toBe('processing');
    expect($order->payment()->first()->status)->toBe('paid');
    expect($order->payment()->first()->transaction_code)->toBe('99887766');
    expect($order->payment()->first()->metadata['inventory_applied'] ?? null)->toBeTrue();
    expect($product->fresh()->stock)->toBe(3);
});
