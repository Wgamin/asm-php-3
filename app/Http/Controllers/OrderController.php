<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Payment;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Shipment;
use App\Models\UserAddress;
use App\Services\CouponService;
use App\Services\ShippingService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function checkout(CouponService $couponService, ShippingService $shippingService)
    {
        $cart = $this->refreshCartFromDatabase(session()->get('cart', []));
        session()->put('cart', $cart);

        if (empty($cart)) {
            return redirect()->route('products.index')->with('error', 'Gio hang trong!');
        }

        $appliedCoupon = $couponService->getAppliedCouponFromSession($cart);
        $summary = $couponService->summarize($cart, $appliedCoupon);
        $addresses = Auth::user()->addresses()->get();
        $defaultAddress = $addresses->firstWhere('is_default', true) ?? $addresses->first();
        $selectedAddressId = (int) old('selected_address_id', $defaultAddress?->id);
        $selectedAddress = $addresses->firstWhere('id', $selectedAddressId) ?? $defaultAddress;
        $selectedShippingProvider = (string) old('shipping_provider', '');
        $delivery = $selectedAddress instanceof UserAddress
            ? $this->addressToDelivery($selectedAddress)
            : $this->emptyDelivery();
        $shippingOptions = $shippingService->quoteOptions(
            (float) $summary['total'],
            $delivery,
            $cart
        );
        $shippingQuote = $shippingService->resolveSelectedQuote($shippingOptions, $selectedShippingProvider);
        $selectedShippingProvider = (string) ($shippingQuote['key'] ?? '');
        $payableTotal = (float) $summary['total'] + (float) ($shippingQuote['fee'] ?? 0);

        return view('checkout', compact(
            'cart',
            'summary',
            'appliedCoupon',
            'addresses',
            'selectedAddressId',
            'shippingOptions',
            'selectedShippingProvider',
            'shippingQuote',
            'payableTotal'
        ));
    }

    public function shippingOptions(Request $request, CouponService $couponService, ShippingService $shippingService)
    {
        $request->validate([
            'selected_address_id' => ['nullable', 'integer'],
            'shipping_provider' => ['nullable', 'string', 'max:50'],
        ]);

        $cart = $this->refreshCartFromDatabase(session()->get('cart', []));
        session()->put('cart', $cart);

        if (empty($cart)) {
            return response()->json([
                'message' => 'Gio hang trong.',
            ], 422);
        }

        $appliedCoupon = $couponService->getAppliedCouponFromSession($cart);
        $summary = $couponService->summarize($cart, $appliedCoupon);
        $delivery = $this->resolveDeliveryInformation($request);
        $quotes = $shippingService->quoteOptions((float) $summary['total'], $delivery, $cart);
        if ($quotes === []) {
            return response()->json([
                'message' => 'Hiện chưa lấy được phí ship từ GHN hoặc GHTK cho địa chỉ này.',
            ], 422);
        }

        $selectedQuote = $shippingService->resolveSelectedQuote($quotes, (string) $request->input('shipping_provider'));

        return response()->json([
            'options' => array_values(array_map(
                fn (array $quote): array => $this->serializeShippingQuote($quote),
                $quotes
            )),
            'selected' => $this->serializeShippingQuote($selectedQuote),
            'payable_total' => (float) $summary['total'] + (float) ($selectedQuote['fee'] ?? 0),
            'subtotal_total' => (float) $summary['total'],
        ]);
    }

    public function store(Request $request, CouponService $couponService, ShippingService $shippingService)
    {
        $usesSavedAddress = $request->filled('selected_address_id');

        $request->validate([
            'selected_address_id' => ['nullable', 'integer'],
            'full_name' => [Rule::requiredIf(! $usesSavedAddress), 'nullable', 'string', 'max:255'],
            'phone' => [Rule::requiredIf(! $usesSavedAddress), 'nullable', 'string', 'max:30'],
            'address' => [Rule::requiredIf(! $usesSavedAddress), 'nullable', 'string', 'max:500'],
            'shipping_provider' => ['nullable', 'string', 'max:50'],
            'payment_method' => ['required', 'string', Rule::in(['cod', 'vnpay'])],
            'note' => ['nullable', 'string'],
        ]);

        $delivery = $this->resolveDeliveryInformation($request);
        $cart = $this->normalizeCart(session()->get('cart', []));

        if (empty($cart)) {
            return redirect()->route('products.index')->with('error', 'Gio hang trong!');
        }

        DB::beginTransaction();

        try {
            [$preparedLines, $pricingCart] = $this->prepareLinesForCheckout($cart);

            $summary = $couponService->summarize($pricingCart);
            $coupon = null;
            $appliedCouponSession = session('applied_coupon');

            if ($appliedCouponSession) {
                $coupon = Coupon::whereKey($appliedCouponSession['id'] ?? null)->lockForUpdate()->first();
                $couponError = $couponService->getCouponError($coupon, $summary['subtotal']);

                if ($couponError !== null) {
                    DB::rollBack();
                    $couponService->clearAppliedCoupon();

                    return back()->with('error', $couponError)->withInput();
                }

                $summary = $couponService->summarize($pricingCart, $coupon);
            }

            $shippingOptions = $shippingService->quoteOptions(
                (float) $summary['total'],
                $delivery,
                $pricingCart
            );

            if ($shippingOptions === []) {
                throw ValidationException::withMessages([
                    'shipping_provider' => 'Hiện chưa lấy được phí ship từ GHN hoặc GHTK cho địa chỉ này.',
                ]);
            }

            $requestedShippingProvider = $request->filled('shipping_provider')
                ? (string) $request->input('shipping_provider')
                : null;

            if ($requestedShippingProvider && ! isset($shippingOptions[$requestedShippingProvider])) {
                throw ValidationException::withMessages([
                    'shipping_provider' => 'Don vi van chuyen khong hop le.',
                ]);
            }

            $shippingQuote = $shippingService->resolveSelectedQuote($shippingOptions, $requestedShippingProvider);
            $shippingFee = (float) $shippingQuote['fee'];
            $payableAmount = (float) $summary['total'] + $shippingFee;

            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'full_name' => $delivery['full_name'],
                'phone' => $delivery['phone'],
                'email' => (string) Auth::user()?->email,
                'address' => $delivery['address'],
                'note' => $request->note,
                'subtotal_amount' => $summary['subtotal'],
                'discount_amount' => $summary['discount'],
                'shipping_fee_amount' => $shippingFee,
                'coupon_id' => $coupon?->id,
                'coupon_code' => $coupon?->code,
                'total_amount' => $summary['total'],
                'payable_amount' => $payableAmount,
                'status' => 'pending', // Luôn set pending lúc đầu, VNPAY trả về thành công mới đổi trạng thái
                'payment_method' => $request->payment_method,
            ]);

            $payment = Payment::create([
                'order_id' => $order->id,
                'method' => $request->payment_method,
                'provider' => $request->payment_method === 'vnpay' ? 'vnpay' : 'cash_on_delivery',
                'amount' => $payableAmount,
                'status' => 'pending',
                'metadata' => [
                    'label' => $request->payment_method === 'vnpay' ? 'Thanh toan online' : 'Thu tien khi giao hang',
                ],
            ]);

            $shipment = Shipment::create([
                'order_id' => $order->id,
                'method' => (string) $shippingQuote['method'],
                'carrier' => (string) $shippingQuote['carrier'],
                'fee_amount' => $shippingFee,
                'status' => 'pending',
                'estimated_delivery_at' => $shippingQuote['estimated_delivery_at'],
                'notes' => trim((string) $shippingQuote['description'] . (! empty($shippingQuote['is_live']) ? ' [API test]' : '')),
            ]);

            foreach ($preparedLines as $line) {
                /** @var Product $product */
                $product = $line['product'];
                /** @var ProductVariant|null $variant */
                $variant = $line['variant'];
                $quantity = $line['quantity'];
                $price = $line['price'];
                $costPrice = (float) ($variant?->cost_price ?? $product->cost_price ?? 0);

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'variant_id' => $variant?->id,
                    'quantity' => $quantity,
                    'price' => $price,
                    'cost_price' => $costPrice,
                    'variant_sku' => $variant?->sku,
                    'variant_values' => $line['variant_values'] ?: null,
                ]);

                if ($variant) {
                    $variant->decrement('stock', $quantity);
                } else {
                    $product->decrement('stock', $quantity);
                }
            }

            if ($coupon) {
                $coupon->increment('used_count');
            }

            $order->setRelation('payment', $payment);
            $order->setRelation('shipment', $shipment);
            $order->recordStatusHistory('system', 'Tao don hang moi', [
                'payment_method' => $request->payment_method,
                'coupon_code' => $coupon?->code,
                'shipping_method' => $shippingQuote['method'],
                'shipping_provider' => $shippingQuote['key'] ?? null,
            ]);

            DB::commit();

            // ==========================================
            // CẬP NHẬT: XỬ LÝ CHUYỂN HƯỚNG THANH TOÁN
            // ==========================================
            
            // Nếu chọn thanh toán VNPAY
            if ($request->payment_method === 'vnpay') {
                // Gán thêm thông tin đơn hàng vào request để PaymentController dùng
                $request->merge([
                    'order_id' => $order->id,
                    'total_amount' => $order->payable_total,
                ]);

                // Trỏ sang hàm createPayment của PaymentController
                return app(\App\Http\Controllers\PaymentController::class)->createPayment($request);
            }

            // Nếu chọn COD
            session()->forget('cart');
            $couponService->clearAppliedCoupon();

            return redirect()->route('order.success')->with('success_order', $order->order_number);
        } catch (ValidationException $e) {
            DB::rollBack();

            return back()->withErrors($e->errors())->withInput();
        } catch (\Throwable $e) {
            DB::rollBack();
            report($e);

            return back()->with('error', 'Co loi xay ra, vui long thu lai!')->withInput();
        }
    }

    public function success()
    {
        $orderNumber = session('success_order');

        if (! $orderNumber) {
            return redirect()->route('home');
        }

        $order = Auth::check()
            ? Order::where('user_id', Auth::id())->where('order_number', $orderNumber)->first()
            : null;

        return view('order_success', [
            'order_number' => $orderNumber,
            'order' => $order,
        ]);
    }

    /**
     * @param  array<string, mixed>  $rawCart
     * @return array<string, array<string, mixed>>
     */
    protected function refreshCartFromDatabase(array $rawCart): array
    {
        $cart = $this->normalizeCart($rawCart);
        $refreshed = [];

        foreach ($cart as $lineKey => $line) {
            $productId = (int) ($line['product_id'] ?? 0);
            $variantId = isset($line['variant_id']) ? (int) $line['variant_id'] : null;
            $qty = max((int) ($line['quantity'] ?? 1), 1);

            if ($productId <= 0) {
                continue;
            }

            if ($variantId) {
                $variant = ProductVariant::with('product')->find($variantId);
                if (! $variant || ! $variant->product || (int) $variant->product_id !== $productId) {
                    continue;
                }

                $stock = max((int) $variant->stock, 0);
                if ($stock <= 0) {
                    continue;
                }

                $qty = min($qty, $stock);
                $refreshed[$lineKey] = $this->buildCartLine($variant->product, $variant, $qty);
                continue;
            }

            $product = Product::find($productId);
            if (! $product || $product->isVariable()) {
                continue;
            }

            $stock = max((int) ($product->stock ?? 0), 0);
            if ($stock <= 0) {
                continue;
            }

            $qty = min($qty, $stock);
            $refreshed[$lineKey] = $this->buildCartLine($product, null, $qty);
        }

        return $refreshed;
    }

    /**
     * @param  array<string, array<string, mixed>>  $cart
     * @return array{0: array<int, array<string, mixed>>, 1: array<string, array<string, float|int>>}
     */
    protected function prepareLinesForCheckout(array $cart): array
    {
        $lines = [];
        $pricingCart = [];

        foreach ($cart as $lineKey => $line) {
            $productId = (int) ($line['product_id'] ?? 0);
            $variantId = isset($line['variant_id']) ? (int) $line['variant_id'] : null;
            $quantity = max((int) ($line['quantity'] ?? 1), 1);

            if ($productId <= 0) {
                throw ValidationException::withMessages([
                    'cart' => 'Co du lieu san pham trong gio khong hop le.',
                ]);
            }

            if ($variantId) {
                $variant = ProductVariant::with('product')->lockForUpdate()->find($variantId);
                if (! $variant || ! $variant->product || (int) $variant->product_id !== $productId) {
                    throw ValidationException::withMessages([
                        'cart' => 'Bien the san pham khong con ton tai.',
                    ]);
                }

                if ((int) $variant->stock < $quantity) {
                    throw ValidationException::withMessages([
                        'cart' => 'Ton kho khong du cho ' . $variant->product->name . '.',
                    ]);
                }

                $price = $this->unitPrice($variant->product, $variant);
                $variantValues = $this->normalizeVariantValues($variant->variant_values);

                $lines[] = [
                    'product' => $variant->product,
                    'variant' => $variant,
                    'quantity' => $quantity,
                    'price' => $price,
                    'variant_values' => $variantValues,
                ];

                $pricingCart[$lineKey] = [
                    'price' => $price,
                    'quantity' => $quantity,
                ];

                continue;
            }

            $product = Product::lockForUpdate()->find($productId);
            if (! $product || $product->isVariable()) {
                throw ValidationException::withMessages([
                    'cart' => 'San pham trong gio khong hop le, vui long chon lai.',
                ]);
            }

            if ((int) ($product->stock ?? 0) < $quantity) {
                throw ValidationException::withMessages([
                    'cart' => 'Ton kho khong du cho ' . $product->name . '.',
                ]);
            }

            $price = $this->unitPrice($product, null);

            $lines[] = [
                'product' => $product,
                'variant' => null,
                'quantity' => $quantity,
                'price' => $price,
                'variant_values' => null,
            ];

            $pricingCart[$lineKey] = [
                'price' => $price,
                'quantity' => $quantity,
            ];
        }

        return [$lines, $pricingCart];
    }

    /**
     * @param  array<string, mixed>  $rawCart
     * @return array<string, array<string, mixed>>
     */
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
            $lineKey = $variantId ? 'v-' . $variantId : 'p-' . $productId;

            $line = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'quantity' => max((int) ($item['quantity'] ?? 1), 1),
                'price' => (float) ($item['price'] ?? 0),
                'weight_grams' => max((int) ($item['weight_grams'] ?? 0), 0),
                'image' => $item['image'] ?? null,
                'name' => (string) ($item['name'] ?? ''),
                'sku' => $item['sku'] ?? null,
                'variant_values' => $this->normalizeVariantValues($item['variant_values'] ?? null),
                'variant_label' => $item['variant_label'] ?? null,
            ];

            if (isset($normalized[$lineKey])) {
                $line['quantity'] += (int) ($normalized[$lineKey]['quantity'] ?? 0);
            }

            $normalized[$lineKey] = $line;
        }

        return $normalized;
    }

    /**
     * @return array<string, string>|null
     */
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
        foreach ($variantValues as $key => $value) {
            if (! is_string($key)) {
                continue;
            }

            $normalized[$key] = (string) $value;
        }

        return $normalized ?: null;
    }

    protected function buildCartLine(Product $product, ?ProductVariant $variant, int $quantity): array
    {
        $variantValues = $this->normalizeVariantValues($variant?->variant_values);

        return [
            'product_id' => (int) $product->id,
            'variant_id' => $variant?->id,
            'name' => $product->name,
            'quantity' => max($quantity, 1),
            'price' => $this->unitPrice($product, $variant),
            'weight_grams' => max((int) ($product->weight_grams ?? 0), 0),
            'image' => $variant?->image ?: $product->image,
            'sku' => $variant?->sku,
            'variant_values' => $variantValues,
            'variant_label' => $this->variantLabel($variantValues),
        ];
    }

    protected function unitPrice(Product $product, ?ProductVariant $variant): float
    {
        if ($variant) {
            return (float) (($variant->sale_price && $variant->sale_price > 0) ? $variant->sale_price : $variant->price);
        }

        return (float) (($product->sale_price && $product->sale_price > 0) ? $product->sale_price : $product->price);
    }

    protected function variantLabel(?array $variantValues): ?string
    {
        if (! $variantValues) {
            return null;
        }

        $parts = [];
        foreach ($variantValues as $name => $value) {
            $parts[] = $name . ': ' . $value;
        }

        return $parts ? implode(' | ', $parts) : null;
    }

    /**
     * @param  array<string, array<string, mixed>>  $cart
     */
    protected function cartQuantity(array $cart): int
    {
        return array_sum(array_map(
            fn (array $line): int => max((int) ($line['quantity'] ?? 0), 0),
            $cart
        ));
    }

    /**
     * @return array{full_name:string, phone:string, address:string, shipping_region:?string, province:?string, district:?string, ward:?string, address_line:?string}
     */
    protected function resolveDeliveryInformation(Request $request): array
    {
        if ($request->filled('selected_address_id')) {
            $address = Auth::user()?->addresses()->find((int) $request->input('selected_address_id'));

            if (! $address instanceof UserAddress) {
                throw ValidationException::withMessages([
                    'selected_address_id' => 'Dia chi giao hang khong hop le.',
                ]);
            }

            return $this->addressToDelivery($address);
        }

        return [
            'full_name' => (string) $request->input('full_name'),
            'phone' => (string) $request->input('phone'),
            'address' => (string) $request->input('address'),
            'shipping_region' => (string) $request->input('address'),
            'province' => null,
            'district' => null,
            'ward' => null,
            'address_line' => (string) $request->input('address'),
        ];
    }

    /**
     * @return array{full_name:string, phone:string, address:string, shipping_region:?string, province:?string, district:?string, ward:?string, address_line:?string}
     */
    protected function addressToDelivery(UserAddress $address): array
    {
        return [
            'full_name' => $address->full_name,
            'phone' => $address->phone,
            'address' => $address->full_address,
            'shipping_region' => $address->province,
            'province' => $address->province,
            'district' => $address->district,
            'ward' => $address->ward,
            'address_line' => $address->address_line,
        ];
    }

    /**
     * @return array{full_name:string, phone:string, address:string, shipping_region:?string, province:?string, district:?string, ward:?string, address_line:?string}
     */
    protected function emptyDelivery(): array
    {
        return [
            'full_name' => '',
            'phone' => '',
            'address' => '',
            'shipping_region' => null,
            'province' => null,
            'district' => null,
            'ward' => null,
            'address_line' => null,
        ];
    }

    /**
     * @param  array<string, mixed>  $quote
     * @return array<string, mixed>
     */
    protected function serializeShippingQuote(array $quote): array
    {
        return [
            'key' => (string) ($quote['key'] ?? ''),
            'provider' => (string) ($quote['provider'] ?? ''),
            'method' => (string) ($quote['method'] ?? ''),
            'label' => (string) ($quote['label'] ?? ''),
            'carrier' => (string) ($quote['carrier'] ?? ''),
            'fee' => (float) ($quote['fee'] ?? 0),
            'estimated_days' => (int) ($quote['estimated_days'] ?? 0),
            'description' => (string) ($quote['description'] ?? ''),
            'is_live' => (bool) ($quote['is_live'] ?? false),
        ];
    }
}
