<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatMessage;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    public function index(Request $request)
    {
        $admin = Auth::user();
        $customers = $this->conversationCustomers($admin->id);
        $selectedCustomer = $customers->firstWhere('id', (int) $request->query('user'))
            ?? $customers->first();

        $messages = collect();

        if ($selectedCustomer) {
            $messages = $this->conversationQuery($admin->id, $selectedCustomer->id)
                ->with('sender')
                ->latest('id')
                ->take(50)
                ->get()
                ->sortBy('id')
                ->values();
        }

        return view('admin.chat.index', compact('customers', 'selectedCustomer', 'messages'));
    }

    public function messages(User $user): JsonResponse
    {
        abort_if($user->role === 'admin', 404);

        $admin = Auth::user();

        ChatMessage::where('sender_id', $user->id)
            ->where('recipient_id', $admin->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $messages = $this->conversationQuery($admin->id, $user->id)
            ->with('sender')
            ->latest('id')
            ->take(60)
            ->get()
            ->sortBy('id')
            ->values()
            ->map(fn (ChatMessage $message) => $this->presentMessage($message, $admin->id));

        return response()->json([
            'messages' => $messages,
            'customer' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
            ],
        ]);
    }

    public function send(Request $request, User $user): JsonResponse
    {
        abort_if($user->role === 'admin', 404);

        $data = $request->validate([
            'message' => ['required', 'string', 'max:1000'],
        ]);

        $message = ChatMessage::create([
            'sender_id' => Auth::id(),
            'recipient_id' => $user->id,
            'message' => trim($data['message']),
        ])->load('sender');

        return response()->json([
            'message' => $this->presentMessage($message, Auth::id()),
        ]);
    }

    protected function conversationCustomers(int $adminId): Collection
    {
        $customerIds = ChatMessage::query()
            ->where('sender_id', $adminId)
            ->pluck('recipient_id')
            ->merge(
                ChatMessage::query()
                    ->where('recipient_id', $adminId)
                    ->pluck('sender_id')
            )
            ->unique()
            ->values();

        return User::whereIn('id', $customerIds)
            ->where(function ($query) {
                $query->whereNull('role')->orWhere('role', '!=', 'admin');
            })
            ->get()
            ->map(function (User $customer) use ($adminId) {
                $lastMessage = $this->conversationQuery($adminId, $customer->id)->latest('id')->first();
                $unreadCount = ChatMessage::where('sender_id', $customer->id)
                    ->where('recipient_id', $adminId)
                    ->whereNull('read_at')
                    ->count();

                $customer->setAttribute('chat_last_message', $lastMessage?->message);
                $customer->setAttribute('chat_last_time', $lastMessage?->created_at?->diffForHumans());
                $customer->setAttribute('chat_unread_count', $unreadCount);

                return $customer;
            })
            ->sortByDesc(fn (User $customer) => optional(
                $this->conversationQuery($adminId, $customer->id)->latest('id')->first()
            )->created_at)
            ->values();
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
