<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\UserAddress;
use App\Services\CouponService;
use App\Services\OrderFulfillmentService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $user->load(['wishlists.category', 'wishlists']);

        $orders = $user->orders()
            ->with([
                'payment',
                'shipment',
                'items.variant',
                'items.product.reviews' => fn ($query) => $query->where('user_id', $user->id),
            ])
            ->latest()
            ->get();
        $addresses = $user->addresses()->get();
        $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();
        $compareIds = array_values(array_map('intval', session()->get('compare', [])));
        $compareProducts = Product::with(['category', 'variants'])
            ->whereIn('id', $compareIds)
            ->get()
            ->sortBy(function ($product) use ($compareIds) {
                return array_search($product->id, $compareIds);
            })
            ->values();

        $editingAddress = null;
        $editAddressId = (int) request('edit_address', 0);
        if ($editAddressId > 0) {
            $editingAddress = $addresses->firstWhere('id', $editAddressId);
        }

        $orderReviewPayload = $orders->mapWithKeys(function (Order $order) {
            $items = $order->items
                ->filter(fn ($item) => $item->product)
                ->unique('product_id')
                ->values()
                ->map(function ($item) {
                    $product = $item->product;
                    $review = $product->reviews->first();
                    $image = $item->variant?->image ?: $product->image;

                    return [
                        'product_id' => (int) $product->id,
                        'name' => $product->name,
                        'image_url' => $image ? asset('storage/' . $image) : asset('images/default-product.png'),
                        'review' => $review ? [
                            'rating' => (int) $review->rating,
                            'title' => (string) ($review->title ?? ''),
                            'content' => (string) $review->content,
                        ] : null,
                    ];
                });

            return [
                $order->id => [
                    'id' => (int) $order->id,
                    'order_number' => (string) ($order->order_number ?: ('#' . $order->id)),
                    'can_review' => $order->status === 'completed' && $items->isNotEmpty(),
                    'items' => $items->values()->all(),
                ],
            ];
        })->all();

        return view('profile.index', compact(
            'user',
            'orders',
            'addresses',
            'defaultAddress',
            'editingAddress',
            'compareProducts',
            'orderReviewPayload'
        ));
    }

    public function showOrder(Order $order)
    {
        $order = $this->ownedOrder($order);
        $order->load(['items.product', 'items.variant', 'payment', 'shipment', 'coupon', 'statusHistories']);

        return view('profile.order-show', compact('order'));
    }

    public function cancelOrder(Order $order, OrderFulfillmentService $orderFulfillmentService)
    {
        $order = $this->ownedOrder($order);
        $order->load(['items.product', 'items.variant', 'payment', 'shipment']);

        if (! $order->canBeCancelledByCustomer()) {
            return back()->with('error', 'Don hang nay khong the huy o thoi diem hien tai.');
        }

        DB::transaction(function () use ($order, $orderFulfillmentService) {
            $orderFulfillmentService->release($order);
            $order->update(['status' => 'cancelled']);

            if ($order->shipment) {
                $order->shipment->update(['status' => 'cancelled']);
            }

            if ($order->payment && $order->payment->status !== 'paid') {
                $order->payment->update(['status' => 'cancelled']);
            }

            $order->refresh()->load(['payment', 'shipment']);
            $order->recordStatusHistory('customer', 'Khach hang huy don hang');
        });

        return redirect()
            ->route('profile.orders.show', $order)
            ->with('success', 'Da huy don hang thanh cong.');
    }

    public function buyAgain(Order $order, CouponService $couponService)
    {
        $order = $this->ownedOrder($order);
        $order->load(['items.product', 'items.variant']);

        $cart = $this->normalizeCart(session()->get('cart', []));
        $addedProducts = 0;
        $skippedProducts = 0;

        foreach ($order->items as $item) {
            $product = $item->product;
            $variant = $item->variant;

            if (! $product) {
                $skippedProducts++;
                continue;
            }

            if ($item->variant_id && ! $variant) {
                $skippedProducts++;
                continue;
            }

            if (! $item->variant_id && $product->isVariable()) {
                $skippedProducts++;
                continue;
            }

            $stock = $variant ? max((int) $variant->stock, 0) : max((int) ($product->stock ?? 0), 0);
            if ($stock <= 0) {
                $skippedProducts++;
                continue;
            }

            $lineKey = $this->cartLineKey((int) $product->id, $variant?->id ? (int) $variant->id : null);
            $currentQty = (int) ($cart[$lineKey]['quantity'] ?? 0);
            $allowedQty = max($stock - $currentQty, 0);

            if ($allowedQty <= 0) {
                $skippedProducts++;
                continue;
            }

            $quantityToAdd = min((int) $item->quantity, $allowedQty);
            $cart[$lineKey] = $this->buildCartLine(
                $product,
                $variant,
                $currentQty + $quantityToAdd
            );

            $addedProducts += $quantityToAdd > 0 ? 1 : 0;

            if ($quantityToAdd < (int) $item->quantity) {
                $skippedProducts++;
            }
        }

        if ($addedProducts === 0) {
            return redirect()
                ->route('profile', ['tab' => 'orders'])
                ->with('error', 'Khong co san pham nao con hang de mua lai.')
                ->with('profile_tab', 'orders');
        }

        session()->put('cart', $cart);
        $couponService->clearAppliedCoupon();

        $message = 'Da them san pham tu don cu vao gio hang.';
        if ($skippedProducts > 0) {
            $message .= ' Mot so san pham da duoc bo qua vi khong con hang hoac khong hop le.';
        }

        return redirect()
            ->route('cart.index')
            ->with('success', $message);
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $data = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'required|string|max:30',
            'avatar' => 'nullable|image|max:2048',
            'remove_avatar' => 'nullable|boolean',
            'password' => 'nullable|min:6|confirmed',
        ]);

        $user->name = $data['name'];
        $user->email = $data['email'];
        $user->phone = $data['phone'];

        if ($request->boolean('remove_avatar') && $user->avatar) {
            Storage::disk('public')->delete($user->avatar);
            $user->avatar = null;
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->avatar = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->filled('password')) {
            $user->password = Hash::make($data['password']);
        }

        $user->save();

        return back()
            ->with('success', 'Cập nhật hồ sơ thành công!')
            ->with('profile_tab', 'info');
    }

    public function storeAddress(Request $request)
    {
        $user = Auth::user();
        $data = $this->validateAddress($request);
        $shouldBeDefault = $request->boolean('is_default') || ! $user->addresses()->exists();

        DB::transaction(function () use ($user, $data, $shouldBeDefault) {
            if ($shouldBeDefault) {
                $user->addresses()->update(['is_default' => false]);
            }

            $user->addresses()->create([
                ...$data,
                'is_default' => $shouldBeDefault,
            ]);
        });

        return redirect()
            ->route('profile', ['tab' => 'addresses'])
            ->with('success', 'Đã thêm địa chỉ giao hàng.')
            ->with('profile_tab', 'addresses');
    }

    public function updateAddress(Request $request, UserAddress $address)
    {
        $user = Auth::user();
        $address = $this->ownedAddress($address);
        $data = $this->validateAddress($request);
        $shouldBeDefault = $request->boolean('is_default');

        DB::transaction(function () use ($user, $address, $data, &$shouldBeDefault) {
            $hasOtherAddresses = $user->addresses()->where('id', '!=', $address->id)->exists();

            if (! $hasOtherAddresses) {
                $shouldBeDefault = true;
            }

            if (! $shouldBeDefault && $address->is_default) {
                $shouldBeDefault = ! $user->addresses()
                    ->where('id', '!=', $address->id)
                    ->where('is_default', true)
                    ->exists();
            }

            if ($shouldBeDefault) {
                $user->addresses()->where('id', '!=', $address->id)->update(['is_default' => false]);
            }

            $address->update([
                ...$data,
                'is_default' => $shouldBeDefault,
            ]);
        });

        return redirect()
            ->route('profile', ['tab' => 'addresses'])
            ->with('success', 'Đã cập nhật địa chỉ giao hàng.')
            ->with('profile_tab', 'addresses');
    }

    public function destroyAddress(UserAddress $address)
    {
        $user = Auth::user();
        $address = $this->ownedAddress($address);
        $wasDefault = $address->is_default;

        DB::transaction(function () use ($user, $address, $wasDefault) {
            $address->delete();

            if ($wasDefault || ! $user->addresses()->where('is_default', true)->exists()) {
                $fallback = $user->addresses()->latest('id')->first();
                if ($fallback) {
                    $fallback->update(['is_default' => true]);
                }
            }
        });

        return redirect()
            ->route('profile', ['tab' => 'addresses'])
            ->with('success', 'Đã xóa địa chỉ giao hàng.')
            ->with('profile_tab', 'addresses');
    }

    public function setDefaultAddress(UserAddress $address)
    {
        $user = Auth::user();
        $address = $this->ownedAddress($address);

        DB::transaction(function () use ($user, $address) {
            $user->addresses()->update(['is_default' => false]);
            $address->update(['is_default' => true]);
        });

        return redirect()
            ->route('profile', ['tab' => 'addresses'])
            ->with('success', 'Đã đặt địa chỉ mặc định.')
            ->with('profile_tab', 'addresses');
    }

    protected function validateAddress(Request $request): array
    {
        return $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:30'],
            'province' => ['required', 'string', 'max:255'],
            'district' => ['required', 'string', 'max:255'],
            'ward' => ['required', 'string', 'max:255'],
            'address_line' => ['required', 'string', 'max:500'],
        ]);
    }

    protected function ownedAddress(UserAddress $address): UserAddress
    {
        if ($address->user_id !== (int) Auth::id()) {
            abort(404);
        }

        return $address;
    }

    protected function ownedOrder(Order $order): Order
    {
        if ($order->user_id !== (int) Auth::id()) {
            abort(404);
        }

        return $order;
    }

    protected function normalizeCart(array $rawCart): array
    {
        $normalized = [];

        foreach ($rawCart as $key => $item) {
            if (! is_array($item)) {
                continue;
            }

            $productId = (int) ($item['product_id'] ?? (is_numeric($key) ? $key : 0));
            if ($productId <= 0) {
                continue;
            }

            $variantId = isset($item['variant_id']) && $item['variant_id'] !== ''
                ? (int) $item['variant_id']
                : null;

            $resolvedKey = $this->cartLineKey($productId, $variantId);
            $line = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'name' => (string) ($item['name'] ?? ''),
                'quantity' => max((int) ($item['quantity'] ?? 1), 1),
                'price' => (float) ($item['price'] ?? 0),
                'weight_grams' => max((int) ($item['weight_grams'] ?? 0), 0),
                'image' => $item['image'] ?? null,
                'sku' => $item['sku'] ?? null,
                'variant_label' => $item['variant_label'] ?? null,
                'variant_values' => $this->normalizeVariantValues($item['variant_values'] ?? null),
            ];

            if (isset($normalized[$resolvedKey])) {
                $line['quantity'] += (int) $normalized[$resolvedKey]['quantity'];
            }

            $normalized[$resolvedKey] = $line;
        }

        return $normalized;
    }

    protected function buildCartLine(Product $product, ?ProductVariant $variant, int $quantity): array
    {
        $variantValues = $variant?->variant_values;
        if (is_string($variantValues)) {
            $variantValues = json_decode($variantValues, true) ?: [];
        }

        $variantValues = is_array($variantValues) ? $variantValues : [];

        return [
            'product_id' => (int) $product->id,
            'variant_id' => $variant?->id,
            'name' => $product->name,
            'quantity' => max($quantity, 1),
            'price' => $variant
                ? (float) (($variant->sale_price && $variant->sale_price > 0) ? $variant->sale_price : $variant->price)
                : (float) (($product->sale_price && $product->sale_price > 0) ? $product->sale_price : $product->price),
            'weight_grams' => max((int) ($product->weight_grams ?? 0), 0),
            'image' => $variant?->image ?: $product->image,
            'sku' => $variant?->sku,
            'variant_values' => $variantValues ?: null,
            'variant_label' => $this->variantLabel($variantValues),
            'stock' => $variant ? max((int) $variant->stock, 0) : max((int) ($product->stock ?? 0), 0),
        ];
    }

    protected function cartLineKey(int $productId, ?int $variantId): string
    {
        return $variantId ? 'v-' . $variantId : 'p-' . $productId;
    }

    protected function normalizeVariantValues(mixed $variantValues): ?array
    {
        if ($variantValues === null || $variantValues === '') {
            return null;
        }

        if (is_string($variantValues)) {
            $decoded = json_decode($variantValues, true);
            if (is_array($decoded)) {
                $variantValues = $decoded;
            }
        }

        if (! is_array($variantValues)) {
            return null;
        }

        $normalized = [];
        foreach ($variantValues as $name => $value) {
            $name = trim((string) $name);
            $value = trim((string) $value);

            if ($name === '' || $value === '') {
                continue;
            }

            $normalized[$name] = $value;
        }

        return $normalized ?: null;
    }

    protected function variantLabel(?array $variantValues): ?string
    {
        if (empty($variantValues)) {
            return null;
        }

        return collect($variantValues)
            ->map(fn ($value, $name) => $name . ': ' . $value)
            ->implode(' | ');
    }
}
