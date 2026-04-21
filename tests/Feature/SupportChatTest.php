<?php

use App\Models\ChatMessage;
use App\Models\Order;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

function createRealtimeOrder(User $customer, string $orderNumber, float $payableAmount): Order
{
    return Order::create([
        'user_id' => $customer->id,
        'order_number' => $orderNumber,
        'full_name' => $customer->name,
        'phone' => '0900000000',
        'email' => $customer->email,
        'address' => '123 Duong Test',
        'subtotal_amount' => $payableAmount,
        'discount_amount' => 0,
        'shipping_fee_amount' => 0,
        'total_amount' => $payableAmount,
        'payable_amount' => $payableAmount,
        'status' => 'pending',
        'payment_method' => 'cod',
    ]);
}

it('allows customers and admin to exchange support chat messages', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $customer = User::factory()->create();

    $this->actingAs($customer)
        ->postJson(route('chat.send'), [
            'message' => 'Shop oi, bao gio giao hang?',
        ])
        ->assertOk()
        ->assertJsonPath('message.message', 'Shop oi, bao gio giao hang?');

    $this->assertDatabaseHas('chat_messages', [
        'sender_id' => $customer->id,
        'recipient_id' => $admin->id,
        'message' => 'Shop oi, bao gio giao hang?',
    ]);

    $this->actingAs($admin)
        ->getJson(route('admin.chat.messages', $customer))
        ->assertOk()
        ->assertJsonPath('messages.0.message', 'Shop oi, bao gio giao hang?');

    expect(ChatMessage::first()->fresh()->read_at)->not->toBeNull();

    $this->actingAs($admin)
        ->postJson(route('admin.chat.send', $customer), [
            'message' => 'Shop da nhan, du kien giao trong hom nay.',
        ])
        ->assertOk()
        ->assertJsonPath('message.message', 'Shop da nhan, du kien giao trong hom nay.');

    $this->actingAs($customer)
        ->getJson(route('chat.messages'))
        ->assertOk()
        ->assertJsonCount(2, 'messages');
});

it('returns realtime support chat inbox entries for admin', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $firstCustomer = User::factory()->create([
        'name' => 'First Customer',
        'email' => 'first@example.com',
    ]);
    $secondCustomer = User::factory()->create([
        'name' => 'Second Customer',
        'email' => 'second@example.com',
    ]);

    $firstMessage = ChatMessage::create([
        'sender_id' => $firstCustomer->id,
        'recipient_id' => $admin->id,
        'message' => 'First customer message',
    ]);
    $firstMessage->timestamps = false;
    $firstMessage->forceFill([
        'created_at' => now()->subMinutes(5),
        'updated_at' => now()->subMinutes(5),
    ])->save();

    ChatMessage::create([
        'sender_id' => $secondCustomer->id,
        'recipient_id' => $admin->id,
        'message' => 'Second customer message',
    ]);

    $this->actingAs($admin)
        ->getJson(route('admin.chat.conversations'))
        ->assertOk()
        ->assertJsonPath('customers.0.id', $secondCustomer->id)
        ->assertJsonPath('customers.0.name', 'Second Customer')
        ->assertJsonPath('customers.0.last_message', 'Second customer message')
        ->assertJsonPath('customers.0.unread_count', 1)
        ->assertJsonPath('customers.1.id', $firstCustomer->id)
        ->assertJsonPath('customers.1.last_message', 'First customer message')
        ->assertJsonPath('customers.1.unread_count', 1);
});

it('returns realtime new order notifications for admin topbar', function () {
    $admin = User::factory()->create([
        'role' => 'admin',
    ]);
    $customer = User::factory()->create();

    $firstOrder = createRealtimeOrder($customer, 'ORD-REALTIME-001', 120000);
    $secondOrder = createRealtimeOrder($customer, 'ORD-REALTIME-002', 180000);

    $this->actingAs($admin)
        ->getJson(route('admin.realtime.orders', [
            'last_seen_id' => $firstOrder->id,
        ]))
        ->assertOk()
        ->assertJsonPath('unread_count', 1)
        ->assertJsonPath('items.0.order_number', $secondOrder->order_number)
        ->assertJsonPath('items.1.order_number', $firstOrder->order_number);
});
