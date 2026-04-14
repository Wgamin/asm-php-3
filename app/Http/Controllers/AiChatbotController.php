<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class AiChatbotController extends Controller
{
    protected const SESSION_KEY = 'ai_chatbot_history';

    public function messages(Request $request): JsonResponse
    {
        return response()->json([
            'available' => $this->isConfigured(),
            'messages' => $this->history($request),
        ]);
    }

    public function send(Request $request): JsonResponse
    {
        $data = $request->validate([
            'message' => ['required', 'string', 'max:2000'],
        ]);

        if (! $this->isConfigured()) {
            return response()->json([
                'message' => 'Chatbot AI chưa được cấu hình API Gemini.',
            ], 503);
        }

        $history = $this->history($request);
        $history[] = [
            'id' => (string) str()->uuid(),
            'role' => 'user',
            'message' => trim($data['message']),
            'created_at' => now()->toIso8601String(),
        ];

        $response = Http::timeout(25)
            ->withHeaders([
                'x-goog-api-key' => (string) config('services.gemini.api_key'),
                'Content-Type' => 'application/json',
            ])
            ->post($this->endpoint(), [
                'systemInstruction' => [
                    'parts' => [[
                        'text' => (string) config('services.gemini.system_instruction'),
                    ]],
                ],
                'contents' => collect($history)->map(function (array $message) {
                    return [
                        'role' => $message['role'] === 'model' ? 'model' : 'user',
                        'parts' => [[
                            'text' => (string) $message['message'],
                        ]],
                    ];
                })->values()->all(),
                'generationConfig' => [
                    'maxOutputTokens' => 512,
                ],
            ]);

        if ($response->failed()) {
            $errorMessage = (string) data_get($response->json(), 'error.message', '');

            Log::warning('Gemini API request failed', [
                'status' => $response->status(),
                'response' => $response->json(),
            ]);

            return response()->json([
                'message' => $errorMessage !== ''
                    ? 'Gemini từ chối yêu cầu: '.$errorMessage
                    : 'Không thể kết nối Gemini lúc này.',
            ], 502);
        }

        $reply = $this->extractReply($response->json());
        if ($reply === null) {
            return response()->json([
                'message' => 'Gemini không trả về nội dung hợp lệ.',
            ], 502);
        }

        $history[] = [
            'id' => (string) str()->uuid(),
            'role' => 'model',
            'message' => $reply,
            'created_at' => now()->toIso8601String(),
        ];

        $request->session()->put(
            self::SESSION_KEY,
            collect($history)->take(-12)->values()->all()
        );

        return response()->json([
            'message' => [
                'id' => $history[array_key_last($history)]['id'],
                'role' => 'model',
                'message' => $reply,
                'created_at' => $history[array_key_last($history)]['created_at'],
                'time' => now()->format('H:i'),
            ],
        ]);
    }

    public function clear(Request $request): JsonResponse
    {
        $request->session()->forget(self::SESSION_KEY);

        return response()->json([
            'cleared' => true,
        ]);
    }

    protected function history(Request $request): array
    {
        $history = $request->session()->get(self::SESSION_KEY, []);

        return collect($history)
            ->filter(fn ($message) => is_array($message))
            ->map(function (array $message) {
                $createdAt = trim((string) ($message['created_at'] ?? ''));

                return [
                    'id' => (string) ($message['id'] ?? str()->uuid()),
                    'role' => $message['role'] === 'model' ? 'model' : 'user',
                    'message' => trim((string) ($message['message'] ?? '')),
                    'created_at' => $createdAt !== '' ? $createdAt : now()->toIso8601String(),
                    'time' => $createdAt !== '' ? optional(\Illuminate\Support\Carbon::parse($createdAt))->format('H:i') : now()->format('H:i'),
                ];
            })
            ->filter(fn (array $message) => $message['message'] !== '')
            ->take(-12)
            ->values()
            ->all();
    }

    protected function extractReply(array $payload): ?string
    {
        $parts = data_get($payload, 'candidates.0.content.parts', []);
        if (! is_array($parts)) {
            return null;
        }

        $text = collect($parts)
            ->map(fn ($part) => trim((string) ($part['text'] ?? '')))
            ->filter()
            ->implode("\n");

        return $text !== '' ? $text : null;
    }

    protected function isConfigured(): bool
    {
        return filled(config('services.gemini.api_key'));
    }

    protected function endpoint(): string
    {
        $baseUrl = rtrim((string) config('services.gemini.base_url'), '/');
        $model = (string) config('services.gemini.model', 'gemini-2.5-flash');

        return $baseUrl.'/models/'.$model.':generateContent';
    }
}
