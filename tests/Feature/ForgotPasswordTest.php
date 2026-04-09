<?php

use App\Models\User;
use App\Notifications\UserPasswordOtpNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;

uses(RefreshDatabase::class);

it('sends an otp email for password reset', function () {
    Notification::fake();

    $user = User::factory()->create([
        'email' => 'user@example.com',
    ]);

    $response = $this->post(route('password.email'), [
        'email' => $user->email,
    ]);

    $response
        ->assertRedirect(route('password.reset', ['email' => $user->email]))
        ->assertSessionHas('success');

    Notification::assertSentTo($user, UserPasswordOtpNotification::class);

    expect(DB::table('password_reset_tokens')->where('email', $user->email)->exists())->toBeTrue();
});

it('resets the password with a valid otp', function () {
    $user = User::factory()->create([
        'email' => 'user@example.com',
        'password' => Hash::make('old-password'),
    ]);

    DB::table('password_reset_tokens')->insert([
        'email' => $user->email,
        'token' => Hash::make('123456'),
        'created_at' => now(),
    ]);

    $response = $this->post(route('password.update'), [
        'email' => $user->email,
        'otp' => '123456',
        'password' => 'new-password',
        'password_confirmation' => 'new-password',
    ]);

    $response
        ->assertRedirect(route('login'))
        ->assertSessionHas('success');

    expect(Hash::check('new-password', $user->fresh()->password))->toBeTrue();
    expect(DB::table('password_reset_tokens')->where('email', $user->email)->exists())->toBeFalse();
});
