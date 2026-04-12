<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index()
    {
        $supportAdmin = $this->supportAdmin();
        $messages = collect();

        if ($supportAdmin) {
            $messages = $this->conversationQuery(Auth::id(), $supportAdmin->id)
                ->with(['sender', 'recipient'])
                ->latest('id')
                ->take(40)
                ->get()
                ->sortBy('id')
                ->values();
        }

        return view('chat.index', compact('supportAdmin', 'messages'));
    }

    public function messages(): JsonResponse
    {
        $user = Auth::user();
        $supportAdmin = $this->supportAdmin();

        if (! $supportAdmin) {
            return response()->json([
                'available' => false,
                'messages' => [],
            ]);
        }

        ChatMessage::where('sender_id', $supportAdmin->id)
            ->where('recipient_id', $user->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = $this->conversationQuery($user->id, $supportAdmin->id)
            ->with('sender')
            ->latest('id')
            ->take(50)
            ->get()
            ->sortBy('id')
            ->values()
            ->map(fn (ChatMessage $message) => $this->presentMessage($message, $user->id));

        return response()->json([
            'available' => true,
            'support_admin' => [
                'id' => $supportAdmin->id,
                'name' => $supportAdmin->name,
            ],
            'messages' => $messages,
        ]);
    }

    public function send(Request $request): JsonResponse
    {
        $supportAdmin = $this->supportAdmin();

        if (! $supportAdmin) {
            return response()->json([
                'message' => 'Hien tai chua co tai khoan admin de tiep nhan hoi thoai.',
            ], 422);
        }

        $data = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $message = ChatMessage::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $supportAdmin->id,
            'message' => trim($data['message']),
        ])->load('sender');

        return response()->json([
            'message' => $this->presentMessage($message, Auth::id()),
        ]);
    }

    protected function supportAdmin(): ?User
    {
        return User::where('role', 'admin')->orderBy('id')->first();
    }

    protected function conversationQuery(int $firstUserId, int $secondUserId)
    {
        return ChatMessage::query()->between($firstUserId, $secondUserId);
    }

    protected function presentMessage(ChatMessage $message, int $currentUserId): array
    {
        return [
            'id' => $message->id,
            'message' => $message->message,
            'mine' => $message->sender_id === $currentUserId,
            'sender_name' => $message->sender?->name,
            'time' => $message->created_at?->format('H:i'),
            'created_at' => $message->created_at?->toIso8601String(),
            'read_at' => $message->read_at?->toIso8601String(),
        ];
    }
}
