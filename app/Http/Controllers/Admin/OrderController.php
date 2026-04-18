<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderFulfillmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'order_number' => ['nullable', 'string', 'max:255'],
            'customer' => ['nullable', 'string', 'max:255'],
            'status' => ['nullable', 'in:pending,processing,shipping,completed,cancelled'],
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date'],
        ]);

        $orders = Order::with(['user', 'payment', 'shipment'])
            ->when(filled($filters['order_number'] ?? null), function ($query) use ($filters) {
                $query->where('order_number', 'like', '%' . trim((string) $filters['order_number']) . '%');
            })
            ->when(filled($filters['customer'] ?? null), function ($query) use ($filters) {
                $keyword = trim((string) $filters['customer']);

                $query->where(function ($customerQuery) use ($keyword) {
                    $customerQuery
                        ->where('full_name', 'like', '%' . $keyword . '%')
                        ->orWhere('phone', 'like', '%' . $keyword . '%')
                        ->orWhere('email', 'like', '%' . $keyword . '%');
                });
            })
            ->when(filled($filters['status'] ?? null), function ($query) use ($filters) {
                $query->where('status', $filters['status']);
            })
            ->when(filled($filters['date_from'] ?? null), function ($query) use ($filters) {
                $query->whereDate('created_at', '>=', $filters['date_from']);
            })
            ->when(filled($filters['date_to'] ?? null), function ($query) use ($filters) {
                $query->whereDate('created_at', '<=', $filters['date_to']);
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10)
            ->withQueryString();

        return view('admin.orders.index', compact('orders', 'filters'));
    }

    public function show($id)
    {
        $order = Order::with(['items.product', 'items.variant', 'user', 'coupon', 'payment', 'shipment', 'statusHistories'])->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, $id, OrderFulfillmentService $orderFulfillmentService)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,processing,shipping,completed,cancelled'],
        ]);

        $order = Order::with(['payment', 'shipment'])->findOrFail($id);

        DB::transaction(function () use ($order, $validated, $orderFulfillmentService) {
            $status = $validated['status'];
            $previousStatus = $order->status;

            $order->update(['status' => $status]);

            if ($order->shipment) {
                match ($status) {
                    'processing' => $order->shipment->update(['status' => 'preparing']),
                    'shipping' => $order->shipment->update([
                        'status' => 'shipping',
                        'shipped_at' => $order->shipment->shipped_at ?? now(),
                    ]),
                    'completed' => $order->shipment->update([
                        'status' => 'delivered',
                        'delivered_at' => $order->shipment->delivered_at ?? now(),
                    ]),
                    'cancelled' => $order->shipment->update(['status' => 'cancelled']),
                    default => $order->shipment->update(['status' => 'pending']),
                };
            }

            if ($order->payment) {
                if ($status === 'completed' && $order->payment_method === 'cod') {
                    $order->payment->update([
                        'status' => 'paid',
                        'paid_at' => $order->payment->paid_at ?? now(),
                    ]);
                } elseif ($status === 'cancelled' && $previousStatus !== 'cancelled' && in_array($previousStatus, ['pending', 'processing'], true)) {
                    $orderFulfillmentService->release($order);

                    if ($order->payment->status !== 'paid') {
                        $order->payment->update(['status' => 'cancelled']);
                    }
                } elseif ($status === 'cancelled' && $order->payment->status !== 'paid') {
                    $order->payment->update(['status' => 'cancelled']);
                }
            }

            $order->refresh()->load(['payment', 'shipment']);
            $order->recordStatusHistory('admin', 'Cap nhat trang thai don hang tu admin');
        });

        return back()->with('success', 'Cap nhat trang thai don hang thanh cong!');
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->items()->delete();
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Xoa don hang thanh cong!');
    }
}
