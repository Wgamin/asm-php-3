<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Services\CouponService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request, $id, CouponService $couponService)
    {
        $product = Product::with('variants')->findOrFail($id);
        $cart = $this->getNormalizedCart();
        $quantity = max((int) $request->input('quantity', 1), 1);

        $variant = null;
        if ($product->isVariable()) {
            $variantId = (int) $request->input('variant_id');
            if ($variantId <= 0) {
                return redirect()
                    ->route('product.detail', $product->id)
                    ->with('error', 'Vui long chon bien the truoc khi them vao gio.');
            }

            $variant = $product->variants->firstWhere('id', $variantId);
            if (! $variant) {
                return redirect()
                    ->route('product.detail', $product->id)
                    ->with('error', 'Bien the khong hop le.');
            }
        }

        $stock = $this->availableStock($product, $variant);
        if ($stock <= 0) {
            return redirect()->back()->with('error', 'San pham da het hang.');
        }

        $lineKey = $this->lineKey($product->id, $variant?->id);
        $currentQty = (int) ($cart[$lineKey]['quantity'] ?? 0);
        if ($currentQty >= $stock) {
            return redirect()->back()->with('error', 'So luong trong gio da dat toi da ton kho.');
        }

        $newQty = min($currentQty + $quantity, $stock);
        $cart[$lineKey] = $this->buildCartLine($product, $variant, $newQty);

        session()->put('cart', $cart);
        $couponService->getAppliedCouponFromSession($cart);

        if ($newQty < ($currentQty + $quantity)) {
            return redirect()->back()->with('success', 'Da cap nhat gio hang den muc ton kho toi da.');
        }

        return redirect()->back()->with('success', 'Da them vao gio hang!');
    }

    public function updateQuantity($id, $quantity, CouponService $couponService)
    {
        $cart = $this->getNormalizedCart();
        $lineKey = $this->resolveLineKey((string) $id, $cart);

        if (! $lineKey || ! isset($cart[$lineKey])) {
            return redirect()->back()->with('error', 'Khong tim thay san pham trong gio!');
        }

        $line = $this->hydrateLineFromDatabase($cart[$lineKey]);
        if (! $line) {
            unset($cart[$lineKey]);
            session()->put('cart', $cart);

            if (empty($cart)) {
                $couponService->clearAppliedCoupon();
            } else {
                $couponService->getAppliedCouponFromSession($cart);
            }

            return redirect()->back()->with('error', 'San pham khong con hop le va da duoc xoa khoi gio.');
        }

        if ($line['stock'] <= 0) {
            unset($cart[$lineKey]);
            session()->put('cart', $cart);

            if (empty($cart)) {
                $couponService->clearAppliedCoupon();
            } else {
                $couponService->getAppliedCouponFromSession($cart);
            }

            return redirect()->back()->with('error', 'San pham da het hang va da duoc xoa khoi gio.');
        }

        $newQty = max((int) $quantity, 1);
        $message = 'Da cap nhat so luong!';

        if ($newQty > $line['stock']) {
            $newQty = $line['stock'];
            $message = 'So luong vuot ton kho, gio hang da duoc dieu chinh.';
        }

        $cart[$lineKey] = array_merge($line, ['quantity' => $newQty]);
        session()->put('cart', $cart);
        $couponService->getAppliedCouponFromSession($cart);

        return redirect()->back()->with('success', $message);
    }

    public function removeItem($id, CouponService $couponService)
    {
        $cart = $this->getNormalizedCart();
        $lineKey = $this->resolveLineKey((string) $id, $cart);

        if ($lineKey && isset($cart[$lineKey])) {
            unset($cart[$lineKey]);
            session()->put('cart', $cart);

            if (empty($cart)) {
                $couponService->clearAppliedCoupon();
            } else {
                $couponService->getAppliedCouponFromSession($cart);
            }

            return redirect()->back()->with('success', 'Da xoa san pham khoi gio!');
        }

        return redirect()->back()->with('error', 'San pham khong ton tai!');
    }

    public function clear(CouponService $couponService)
    {
        session()->forget('cart');
        $couponService->clearAppliedCoupon();

        return redirect()->back()->with('success', 'Gio hang da duoc don sach!');
    }

    protected function getNormalizedCart(): array
    {
        $rawCart = session()->get('cart', []);
        $normalized = [];
        $changed = false;

        foreach ($rawCart as $key => $item) {
            if (! is_array($item)) {
                $changed = true;
                continue;
            }

            $productId = (int) ($item['product_id'] ?? (is_numeric($key) ? $key : 0));
            if ($productId <= 0) {
                $changed = true;
                continue;
            }

            $variantId = isset($item['variant_id']) && $item['variant_id'] !== ''
                ? (int) $item['variant_id']
                : null;
            $resolvedKey = $this->lineKey($productId, $variantId);

            $line = [
                'product_id' => $productId,
                'variant_id' => $variantId,
                'name' => (string) ($item['name'] ?? ''),
                'quantity' => max((int) ($item['quantity'] ?? 1), 1),
                'price' => (float) ($item['price'] ?? 0),
                'image' => $item['image'] ?? null,
                'sku' => $item['sku'] ?? null,
                'variant_label' => $item['variant_label'] ?? null,
                'variant_values' => $this->normalizeVariantValues($item['variant_values'] ?? null),
            ];

            if (isset($normalized[$resolvedKey])) {
                $line['quantity'] += (int) ($normalized[$resolvedKey]['quantity'] ?? 0);
            }

            $normalized[$resolvedKey] = $line;

            if ((string) $key !== $resolvedKey || ! isset($item['product_id'])) {
                $changed = true;
            }
        }

        if ($changed) {
            session()->put('cart', $normalized);
        }

        return $normalized;
    }

    /**
     * @return array<string, mixed>|null
     */
    protected function hydrateLineFromDatabase(array $line): ?array
    {
        $productId = (int) ($line['product_id'] ?? 0);
        $variantId = isset($line['variant_id']) ? (int) $line['variant_id'] : null;

        if ($productId <= 0) {
            return null;
        }

        if ($variantId) {
            $variant = ProductVariant::with('product')->find($variantId);
            if (! $variant || ! $variant->product || (int) $variant->product_id !== $productId) {
                return null;
            }

            return $this->buildCartLine($variant->product, $variant, (int) ($line['quantity'] ?? 1));
        }

        $product = Product::find($productId);
        if (! $product || $product->isVariable()) {
            return null;
        }

        return $this->buildCartLine($product, null, (int) ($line['quantity'] ?? 1));
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
            'price' => $this->unitPrice($product, $variant),
            'image' => $variant?->image ?: $product->image,
            'sku' => $variant?->sku,
            'variant_values' => $variantValues ?: null,
            'variant_label' => $this->variantLabel($variantValues),
            'stock' => $this->availableStock($product, $variant),
        ];
    }

    protected function unitPrice(Product $product, ?ProductVariant $variant): float
    {
        if ($variant) {
            return (float) (($variant->sale_price && $variant->sale_price > 0) ? $variant->sale_price : $variant->price);
        }

        return (float) (($product->sale_price && $product->sale_price > 0) ? $product->sale_price : $product->price);
    }

    protected function availableStock(Product $product, ?ProductVariant $variant): int
    {
        if ($variant) {
            return max((int) $variant->stock, 0);
        }

        return max((int) ($product->stock ?? 0), 0);
    }

    protected function lineKey(int $productId, ?int $variantId): string
    {
        return $variantId ? 'v-'.$variantId : 'p-'.$productId;
    }

    protected function resolveLineKey(string $id, array $cart): ?string
    {
        if (isset($cart[$id])) {
            return $id;
        }

        if (is_numeric($id)) {
            $simpleKey = 'p-'.$id;
            if (isset($cart[$simpleKey])) {
                return $simpleKey;
            }
        }

        return null;
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

    protected function variantLabel(?array $variantValues): ?string
    {
        if (! $variantValues) {
            return null;
        }

        $parts = [];
        foreach ($variantValues as $name => $value) {
            $parts[] = $name.': '.$value;
        }

        return $parts ? implode(' | ', $parts) : null;
    }
}