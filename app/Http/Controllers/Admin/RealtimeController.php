<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RealtimeController extends Controller
{
    public function orders(Request $request): JsonResponse
    {
        $lastSeenId = max((int) $request->query('last_seen_id', 0), 0);
        $orders = Order::latest('id')->take(8)->get();

        return response()->json([
            'items' => $orders->map(fn (Order $order) => [
                'id' => $order->id,
                'order_number' => $order->order_number,
                'customer_name' => $order->full_name,
                'total' => number_format($order->payable_total) . 'd',
                'status' => $order->status_text,
                'time' => $order->created_at?->diffForHumans(),
                'url' => route('admin.orders.show', $order->id),
            ])->values(),
            'unread_count' => Order::where('id', '>', $lastSeenId)->count(),
            'max_id' => (int) ($orders->max('id') ?? 0),
        ]);
    }
}
