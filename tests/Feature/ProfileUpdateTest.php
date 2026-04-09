<?php

use App\Models\User;
use App\Models\UserAddress;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

uses(RefreshDatabase::class);

it('updates the profile phone number and avatar', function () {
    Storage::fake('public');

    $user = User::factory()->create([
        'phone' => null,
    ]);

    $response = $this
        ->actingAs($user)
        ->from(route('profile'))
        ->post(route('profile.update'), [
            'active_tab' => 'info',
            'name' => 'Nguyen Van A',
            'email' => $user->email,
            'phone' => '0901234567',
            'avatar' => UploadedFile::fake()->image('avatar.jpg'),
        ]);

    $response
        ->assertRedirect(route('profile'))
        ->assertSessionHasNoErrors();

    $user = $user->fresh();

    expect($user->name)->toBe('Nguyen Van A');
    expect($user->phone)->toBe('0901234567');
    expect($user->avatar)->not->toBeNull();

    Storage::disk('public')->assertExists($user->avatar);
});

it('shows the default shipping address on the profile page', function () {
    $user = User::factory()->create([
        'phone' => '0900000009',
    ]);

    $defaultAddress = UserAddress::create([
        'user_id' => $user->id,
        'full_name' => 'Nguoi nhan chinh',
        'phone' => '0900000001',
        'province' => 'Ha Noi',
        'district' => 'Cau Giay',
        'ward' => 'Dich Vong',
        'address_line' => '12 Pho Trung Kinh',
        'is_default' => true,
    ]);

    UserAddress::create([
        'user_id' => $user->id,
        'full_name' => 'Nguoi nhan phu',
        'phone' => '0900000002',
        'province' => 'Ha Noi',
        'district' => 'Ba Dinh',
        'ward' => 'Kim Ma',
        'address_line' => '34 Pho Kim Ma',
        'is_default' => false,
    ]);

    $response = $this
        ->actingAs($user)
        ->get(route('profile'));

    $response->assertOk();
    $response->assertSee('Địa chỉ mặc định');
    $response->assertSee($defaultAddress->full_name);
    $response->assertSee($defaultAddress->phone);
    $response->assertSee($defaultAddress->full_address);
});
