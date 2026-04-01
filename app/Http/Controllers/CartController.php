<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CouponService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request, $id, CouponService $couponService)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);
        $quantity = $request->input('quantity', 1);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            $cart[$id] = [
                'name' => $product->name,
                'quantity' => $quantity,
                'price' => $product->price,
                'image' => $product->image,
            ];
        }

        session()->put('cart', $cart);
        $couponService->getAppliedCouponFromSession($cart);

        return redirect()->back()->with('success', 'Đã thêm vào giỏ!');
    }

    public function updateQuantity($id, $quantity, CouponService $couponService)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            $newQty = $quantity < 1 ? 1 : $quantity;
            $cart[$id]['quantity'] = $newQty;

            session()->put('cart', $cart);
            $couponService->getAppliedCouponFromSession($cart);

            return redirect()->back()->with('success', 'Đã cập nhật số lượng!');
        }

        return redirect()->back()->with('error', 'Không tìm thấy sản phẩm trong giỏ!');
    }

    public function removeItem($id, CouponService $couponService)
    {
        $cart = session()->get('cart', []);

        if (isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);

            if (empty($cart)) {
                $couponService->clearAppliedCoupon();
            } else {
                $couponService->getAppliedCouponFromSession($cart);
            }

            return redirect()->back()->with('success', 'Đã xóa sản phẩm khỏi giỏ!');
        }

        return redirect()->back()->with('error', 'Sản phẩm không tồn tại!');
    }

    public function clear(CouponService $couponService)
    {
        session()->forget('cart');
        $couponService->clearAppliedCoupon();

        return redirect()->back()->with('success', 'Giỏ hàng đã được dọn sạch!');
    }
}
