<?php

use App\Models\Order;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createAdminFilterOrder(User $customer, array $overrides = []): Order
{
    return Order::create(array_merge([
        'user_id' => $customer->id,
        'order_number' => 'ORD-' . strtoupper(uniqid()),
        'full_name' => $customer->name,
        'phone' => '0900000000',
        'email' => $customer->email,
        'address' => '123 Duong Test',
        'subtotal_amount' => 100000,
        'discount_amount' => 0,
        'shipping_fee_amount' => 0,
        'total_amount' => 100000,
        'payable_amount' => 100000,
        'status' => 'pending',
        'payment_method' => 'cod',
    ], $overrides));
}

it('filters admin orders by order number', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $firstCustomer = User::factory()->create(['name' => 'Nguyen Van A']);
    $secondCustomer = User::factory()->create(['name' => 'Tran Thi B']);

    $targetOrder = createAdminFilterOrder($firstCustomer, [
        'order_number' => 'ORD-FILTER-001',
    ]);

    $otherOrder = createAdminFilterOrder($secondCustomer, [
        'order_number' => 'ORD-FILTER-XYZ',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.orders.index', [
        'order_number' => '001',
    ]));

    $response->assertOk();
    $response->assertSee($targetOrder->order_number);
    $response->assertDontSee($otherOrder->order_number);
});

it('filters admin orders by customer keyword and status', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $matchingCustomer = User::factory()->create([
        'name' => 'Le Thi C',
        'email' => 'lethi@example.com',
    ]);
    $otherCustomer = User::factory()->create([
        'name' => 'Pham Van D',
        'email' => 'phamvan@example.com',
    ]);

    $matchingOrder = createAdminFilterOrder($matchingCustomer, [
        'order_number' => 'ORD-CUSTOMER-001',
        'full_name' => 'Le Thi C',
        'status' => 'shipping',
    ]);

    $otherStatusOrder = createAdminFilterOrder($matchingCustomer, [
        'order_number' => 'ORD-CUSTOMER-002',
        'full_name' => 'Le Thi C',
        'status' => 'pending',
    ]);

    $otherCustomerOrder = createAdminFilterOrder($otherCustomer, [
        'order_number' => 'ORD-CUSTOMER-003',
        'full_name' => 'Pham Van D',
        'status' => 'shipping',
    ]);

    $response = $this->actingAs($admin)->get(route('admin.orders.index', [
        'customer' => 'lethi',
        'status' => 'shipping',
    ]));

    $response->assertOk();
    $response->assertSee($matchingOrder->order_number);
    $response->assertDontSee($otherStatusOrder->order_number);
    $response->assertDontSee($otherCustomerOrder->order_number);
});

it('filters admin orders by date range', function () {
    $admin = User::factory()->create(['role' => 'admin']);
    $customer = User::factory()->create(['name' => 'Do Thi E']);

    $insideRangeOrder = createAdminFilterOrder($customer, [
        'order_number' => 'ORD-DATE-001',
    ]);
    $insideRangeOrder->forceFill([
        'created_at' => Carbon::parse('2026-04-09 10:00:00'),
        'updated_at' => Carbon::parse('2026-04-09 10:00:00'),
    ])->save();

    $outsideRangeOrder = createAdminFilterOrder($customer, [
        'order_number' => 'ORD-DATE-002',
    ]);
    $outsideRangeOrder->forceFill([
        'created_at' => Carbon::parse('2026-04-03 10:00:00'),
        'updated_at' => Carbon::parse('2026-04-03 10:00:00'),
    ])->save();

    $response = $this->actingAs($admin)->get(route('admin.orders.index', [
        'date_from' => '2026-04-08',
        'date_to' => '2026-04-10',
    ]));

    $response->assertOk();
    $response->assertSee($insideRangeOrder->order_number);
    $response->assertDontSee($outsideRangeOrder->order_number);
});
