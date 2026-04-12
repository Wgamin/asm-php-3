<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ProductReview;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProductReviewController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $data = $request->validate([
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $completedOrderItem = OrderItem::query()
            ->where('product_id', $product->id)
            ->whereHas('order', function ($query) {
                $query
                    ->where('user_id', Auth::id())
                    ->where('status', 'completed');
            })
            ->latest('id')
            ->first();

        if (! $completedOrderItem) {
            return back()->with('error', 'Ban chi co the danh gia san pham da mua va da hoan thanh.');
        }

        $this->persistReview($product, $completedOrderItem, $data);

        return back()
            ->with('success', 'Cam on ban da danh gia san pham.')
            ->with('profile_tab', $request->input('active_tab', 'info'));
    }

    public function storeFromOrder(Request $request)
    {
        $data = $request->validate([
            'order_id' => ['required', 'integer'],
            'product_id' => ['required', 'integer'],
            'rating' => ['required', 'integer', 'between:1,5'],
            'title' => ['nullable', 'string', 'max:255'],
            'content' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $order = Order::query()
            ->with(['items.product'])
            ->whereKey($data['order_id'])
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($order->status !== 'completed') {
            return redirect()
                ->route('profile', ['tab' => 'orders'])
                ->with('error', 'Chi co the danh gia san pham trong don da hoan thanh.')
                ->with('profile_tab', 'orders');
        }

        $completedOrderItem = $order->items
            ->where('product_id', (int) $data['product_id'])
            ->sortByDesc('id')
            ->first();

        if (! $completedOrderItem || ! $completedOrderItem->product) {
            return redirect()
                ->route('profile', ['tab' => 'orders'])
                ->with('error', 'San pham khong hop le de danh gia.')
                ->with('profile_tab', 'orders');
        }

        $this->persistReview($completedOrderItem->product, $completedOrderItem, $data);

        return redirect()
            ->route('profile', ['tab' => 'orders'])
            ->with('success', 'Cam on ban da danh gia san pham.')
            ->with('profile_tab', 'orders');
    }

    protected function persistReview(Product $product, OrderItem $completedOrderItem, array $data): void
    {
        ProductReview::updateOrCreate(
            [
                'product_id' => $product->id,
                'user_id' => Auth::id(),
            ],
            [
                'order_id' => $completedOrderItem->order_id,
                'rating' => $data['rating'],
                'title' => $data['title'] ?? null,
                'content' => $data['content'],
                'is_approved' => true,
            ]
        );
    }
}
