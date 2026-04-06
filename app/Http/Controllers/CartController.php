<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant; // Thêm dòng này để gọi model biến thể
use App\Services\CouponService;
use Illuminate\Http\Request;

class CartController extends Controller
{
    public function add(Request $request, $id, CouponService $couponService)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);
        $quantity = $request->input('quantity', 1);

        // 1. Mặc định lấy giá gốc (Ưu tiên giá khuyến mãi nếu có)
        $price = $product->sale_price > 0 ? $product->sale_price : $product->price;

        // 2. Xử lý giá nếu có chọn biến thể (Tùy chọn sản phẩm)
        // Lưu ý: Đảm bảo thẻ input hidden hoặc radio trong form của bạn có name="variant_id"
        if ($request->has('variant_id') && $request->variant_id != '') {
            $variant = ProductVariant::find($request->variant_id);
            if ($variant) {
                // Lấy giá của biến thể (ưu tiên sale_price của biến thể nếu có)
                $price = $variant->sale_price > 0 ? $variant->sale_price : $variant->price;
            }
        }

        // Ràng buộc an toàn: Tránh thêm sản phẩm giá 0đ vào giỏ làm lỗi VNPAY
        if ($price <= 0) {
            return redirect()->back()->with('error', 'Sản phẩm chưa được cập nhật giá hợp lệ!');
        }

        // 3. Thêm vào giỏ hàng
        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
            // Cập nhật lại giá mới nhất lỡ admin vừa đổi giá
            $cart[$id]['price'] = $price; 
        } else {
            $cart[$id] = [
                'name' => $product->name,
                'quantity' => $quantity,
                'price' => $price, // Giá đã được xử lý chuẩn
                'image' => $product->image,
                'variant_id' => $request->variant_id ?? null, // Lưu lại ID biến thể nếu cần dùng sau này
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