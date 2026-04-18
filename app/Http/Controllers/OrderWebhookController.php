<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderFulfillmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class OrderWebhookController extends Controller
{
    public function update(Request $request, Order $order, OrderFulfillmentService $orderFulfillmentService)
    {
        $configuredSecret = trim((string) env('ORDER_WEBHOOK_SECRET', ''));
        $receivedSecret = trim((string) $request->header('X-Webhook-Secret', ''));

        if ($configuredSecret !== '' && ! hash_equals($configuredSecret, $receivedSecret)) {
            abort(403, 'Webhook secret khong hop le.');
        }

        $validated = $request->validate([
            'status' => ['nullable', Rule::in(['pending', 'processing', 'shipping', 'completed', 'cancelled'])],
            'payment_status' => ['nullable', Rule::in(['pending', 'paid', 'failed', 'cancelled'])],
            'shipment_status' => ['nullable', Rule::in(['pending', 'preparing', 'shipping', 'delivered', 'cancelled'])],
            'transaction_code' => ['nullable', 'string', 'max:255'],
            'tracking_code' => ['nullable', 'string', 'max:255'],
            'message' => ['nullable', 'string', 'max:255'],
            'provider' => ['nullable', 'string', 'max:255'],
        ]);

        if (collect($validated)->filter(fn ($value) => $value !== null && $value !== '')->isEmpty()) {
            return response()->json([
                'ok' => false,
                'message' => 'Khong co du lieu trang thai de dong bo.',
            ], 422);
        }

        $order->load(['payment', 'shipment']);

        DB::transaction(function () use ($order, $validated, $request, $orderFulfillmentService) {
            $previousOrderStatus = $order->status;

            if (! empty($validated['status'])) {
                $order->update(['status' => $validated['status']]);
            }

            if ($order->payment && (
                ! empty($validated['payment_status'])
                || ! empty($validated['transaction_code'])
                || ! empty($validated['provider'])
            )) {
                $paymentData = [];

                if (! empty($validated['payment_status'])) {
                    $paymentData['status'] = $validated['payment_status'];
                    if ($validated['payment_status'] === 'paid') {
                        $paymentData['paid_at'] = $order->payment->paid_at ?? now();
                    }
                }

                if (! empty($validated['transaction_code'])) {
                    $paymentData['transaction_code'] = $validated['transaction_code'];
                }

                if (! empty($validated['provider'])) {
                    $paymentData['provider'] = $validated['provider'];
                }

                if ($paymentData !== []) {
                    $order->payment->update($paymentData);
                }
            }

            if (($validated['payment_status'] ?? null) === 'paid') {
                $order->loadMissing(['items.product', 'items.variant', 'payment']);
                $orderFulfillmentService->apply($order);

                if ($order->status === 'pending') {
                    $order->update(['status' => 'processing']);
                }
            } elseif (
                in_array($validated['payment_status'] ?? null, ['failed', 'cancelled'], true)
                || (($validated['status'] ?? null) === 'cancelled' && in_array($previousOrderStatus, ['pending', 'processing'], true))
            ) {
                $order->loadMissing(['items.product', 'items.variant', 'payment']);
                $orderFulfillmentService->release($order);
            }

            if ($order->shipment && (
                ! empty($validated['shipment_status'])
                || ! empty($validated['tracking_code'])
            )) {
                $shipmentData = [];

                if (! empty($validated['shipment_status'])) {
                    $shipmentData['status'] = $validated['shipment_status'];

                    if ($validated['shipment_status'] === 'shipping') {
                        $shipmentData['shipped_at'] = $order->shipment->shipped_at ?? now();
                    }

                    if ($validated['shipment_status'] === 'delivered') {
                        $shipmentData['delivered_at'] = $order->shipment->delivered_at ?? now();
                    }
                }

                if (! empty($validated['tracking_code'])) {
                    $shipmentData['tracking_code'] = $validated['tracking_code'];
                }

                if ($shipmentData !== []) {
                    $order->shipment->update($shipmentData);
                }
            }

            $order->refresh()->load(['payment', 'shipment']);
            $order->recordStatusHistory(
                'webhook',
                $validated['message'] ?? 'Dong bo trang thai tu webhook',
                $request->all()
            );
        });

        return response()->json([
            'ok' => true,
            'order_id' => $order->id,
            'order_status' => $order->fresh()->status,
            'payment_status' => $order->payment()->value('status'),
            'shipment_status' => $order->shipment()->value('status'),
        ]);
    }
}
