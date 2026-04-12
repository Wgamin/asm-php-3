<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;

uses(RefreshDatabase::class);

it('stores ai chatbot messages in session and clears them', function () {
    config()->set('services.gemini.api_key', 'test-gemini-key');
    config()->set('services.gemini.model', 'gemini-2.5-flash');
    config()->set('services.gemini.base_url', 'https://generativelanguage.googleapis.com/v1beta');

    Http::fake([
        'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent' => Http::response([
            'candidates' => [[
                'content' => [
                    'parts' => [[
                        'text' => 'Cam nay vi ngot thanh, hop an truc tiep.',
                    ]],
                ],
            ]],
        ]),
    ]);

    $this->getJson(route('ai-chat.messages'))
        ->assertOk()
        ->assertJsonPath('available', true)
        ->assertJsonCount(0, 'messages');

    $this->postJson(route('ai-chat.send'), [
        'message' => 'Cam nay an co ngot khong?',
    ])
        ->assertOk()
        ->assertJsonPath('message.role', 'model')
        ->assertJsonPath('message.message', 'Cam nay vi ngot thanh, hop an truc tiep.');

    Http::assertSent(function ($request) {
        $payload = $request->data();

        return $request->url() === 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent'
            && $request->hasHeader('x-goog-api-key', 'test-gemini-key')
            && data_get($payload, 'contents.0.parts.0.text') === 'Cam nay an co ngot khong?';
    });

    $this->getJson(route('ai-chat.messages'))
        ->assertOk()
        ->assertJsonCount(2, 'messages')
        ->assertJsonPath('messages.0.role', 'user')
        ->assertJsonPath('messages.0.message', 'Cam nay an co ngot khong?')
        ->assertJsonPath('messages.1.role', 'model')
        ->assertJsonPath('messages.1.message', 'Cam nay vi ngot thanh, hop an truc tiep.');

    $this->deleteJson(route('ai-chat.clear'))
        ->assertOk()
        ->assertJsonPath('cleared', true);

    $this->getJson(route('ai-chat.messages'))
        ->assertOk()
        ->assertJsonCount(0, 'messages');
});

it('returns unavailable when gemini api key is missing', function () {
    config()->set('services.gemini.api_key', null);

    $this->getJson(route('ai-chat.messages'))
        ->assertOk()
        ->assertJsonPath('available', false);

    $this->postJson(route('ai-chat.send'), [
        'message' => 'Xin chao',
    ])
        ->assertStatus(503);
});
