<?php

use App\Models\Category;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Shipment;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createUserOrderHistoryProduct(): Product
{
    $category = Category::create([
        'name' => 'Lich su don hang',
        'slug' => 'lich-su-don-hang',
    ]);

    return Product::create([
        'category_id' => $category->id,
        'name' => 'Rau muong huu co',
        'product_type' => 'simple',
        'price' => 30000,
        'stock' => 10,
        'description' => 'Mo ta ngan',
        'content' => 'Noi dung chi tiet',
        'image' => 'products/test.jpg',
    ]);
}

function createUserOrderHistoryOrder(User $user, Product $product, array $overrides = []): Order
{
    $order = Order::create(array_merge([
        'user_id' => $user->id,
        'order_number' => 'ORD-HISTORY-001',
        'full_name' => 'Nguyen Van A',
        'phone' => '0900000000',
        'email' => $user->email,
        'address' => '12 Nguyen Trai, Thanh Xuan, Ha Noi',
        'subtotal_amount' => 60000,
        'discount_amount' => 0,
        'shipping_fee_amount' => 20000,
        'total_amount' => 60000,
        'payable_amount' => 80000,
        'status' => 'pending',
        'payment_method' => 'cod',
    ], $overrides));

    OrderItem::create([
        'order_id' => $order->id,
        'product_id' => $product->id,
        'variant_id' => null,
        'quantity' => 2,
        'price' => 30000,
    ]);

    return $order;
}

it('shows a dedicated order detail page for the owner', function () {
    $user = User::factory()->create();
    $product = createUserOrderHistoryProduct();
    $order = createUserOrderHistoryOrder($user, $product);

    Payment::create([
        'order_id' => $order->id,
        'method' => 'cod',
        'provider' => 'cash_on_delivery',
        'amount' => 80000,
        'status' => 'pending',
    ]);

    Shipment::create([
        'order_id' => $order->id,
        'method' => 'fast',
        'carrier' => 'Nong San Viet Express',
        'fee_amount' => 20000,
        'status' => 'pending',
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('profile.orders.show', $order));

    $response->assertOk();
    $response->assertSee('ORD-HISTORY-001');
    $response->assertSee('Rau muong huu co');
    $response->assertSee('Tóm tắt đơn hàng');
});

it('allows customers to cancel a pending unpaid order and restores stock', function () {
    $user = User::factory()->create();
    $product = createUserOrderHistoryProduct();
    $product->update(['stock' => 3]);

    $coupon = Coupon::create([
        'code' => 'GIAM20K',
        'name' => 'Giam 20K',
        'type' => 'fixed',
        'value' => 20000,
        'used_count' => 1,
        'is_active' => true,
    ]);

    $order = createUserOrderHistoryOrder($user, $product, [
        'coupon_id' => $coupon->id,
        'coupon_code' => $coupon->code,
        'discount_amount' => 20000,
        'total_amount' => 40000,
        'payable_amount' => 60000,
    ]);

    Payment::create([
        'order_id' => $order->id,
        'method' => 'cod',
        'provider' => 'cash_on_delivery',
        'amount' => 60000,
        'status' => 'pending',
    ]);

    Shipment::create([
        'order_id' => $order->id,
        'method' => 'fast',
        'carrier' => 'Nong San Viet Express',
        'fee_amount' => 20000,
        'status' => 'pending',
    ]);

    $response = $this
        ->actingAs($user)
        ->patch(route('profile.orders.cancel', $order));

    $response->assertRedirect(route('profile.orders.show', $order));

    expect($order->fresh()->status)->toBe('cancelled');
    expect($order->payment()->first()->status)->toBe('cancelled');
    expect($order->shipment()->first()->status)->toBe('cancelled');
    expect($product->fresh()->stock)->toBe(5);
    expect($coupon->fresh()->used_count)->toBe(0);
});

it('does not allow customers to cancel an order that is already paid or being processed', function () {
    $user = User::factory()->create();
    $product = createUserOrderHistoryProduct();
    $product->update(['stock' => 3]);

    $order = createUserOrderHistoryOrder($user, $product, [
        'status' => 'processing',
    ]);

    Payment::create([
        'order_id' => $order->id,
        'method' => 'vnpay',
        'provider' => 'vnpay',
        'amount' => 80000,
        'status' => 'paid',
    ]);

    Shipment::create([
        'order_id' => $order->id,
        'method' => 'fast',
        'carrier' => 'Nong San Viet Express',
        'fee_amount' => 20000,
        'status' => 'preparing',
    ]);

    $response = $this
        ->actingAs($user)
        ->from(route('profile.orders.show', $order))
        ->patch(route('profile.orders.cancel', $order));

    $response->assertRedirect(route('profile.orders.show', $order));
    $response->assertSessionHas('error');

    expect($order->fresh()->status)->toBe('processing');
    expect($order->payment()->first()->status)->toBe('paid');
    expect($order->shipment()->first()->status)->toBe('preparing');
    expect($product->fresh()->stock)->toBe(3);
});

it('allows customers to review a completed product from order history', function () {
    $user = User::factory()->create();
    $product = createUserOrderHistoryProduct();
    $order = createUserOrderHistoryOrder($user, $product, [
        'status' => 'completed',
    ]);

    $response = $this
        ->actingAs($user)
        ->from(route('profile', ['tab' => 'orders']))
        ->post(route('profile.orders.review'), [
            'active_tab' => 'orders',
            'order_id' => $order->id,
            'product_id' => $product->id,
            'rating' => 4,
            'content' => 'San pham tuoi, dong goi ky va dung voi mo ta.',
        ]);

    $response->assertRedirect(route('profile', ['tab' => 'orders']));

    $this->assertDatabaseHas('product_reviews', [
        'order_id' => $order->id,
        'product_id' => $product->id,
        'user_id' => $user->id,
        'rating' => 4,
    ]);

    $detail = $this->get(route('product.detail', $product->id));
    $detail->assertOk();
    $detail->assertSee('San pham tuoi, dong goi ky va dung voi mo ta.');
});

it('allows customers to buy again from a previous order', function () {
    $user = User::factory()->create();
    $product = createUserOrderHistoryProduct();
    $product->update(['stock' => 10]);
    $order = createUserOrderHistoryOrder($user, $product, [
        'status' => 'completed',
    ]);

    $response = $this
        ->actingAs($user)
        ->post(route('profile.orders.buyAgain', $order));

    $response->assertRedirect(route('cart.index'));

    $cart = session('cart', []);
    expect($cart)->toHaveKey('p-' . $product->id);
    expect($cart['p-' . $product->id]['quantity'])->toBe(2);
});
