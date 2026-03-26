<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CartController extends Controller
{
    // 1. Thêm sản phẩm vào giỏ
    public function add(Request $request, $id)
    {
        $product = Product::findOrFail($id);
        $cart = session()->get('cart', []);
        $quantity = $request->input('quantity', 1);

        if(isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
        } else {
            $cart[$id] = [
                "name" => $product->name,
                "quantity" => $quantity,
                "price" => $product->price,
                "image" => $product->image
            ];
        }

        session()->put('cart', $cart);
        return redirect()->back()->with('success', 'Đã thêm vào giỏ!');
    }

    // 2. Cập nhật số lượng (Dùng thẻ <a> truyền thống, KHÔNG AJAX)
    public function updateQuantity($id, $quantity)
    {
        $cart = session()->get('cart', []);

        if(isset($cart[$id])) {
            // Đảm bảo số lượng không nhỏ hơn 1
            $newQty = ($quantity < 1) ? 1 : $quantity;
            $cart[$id]['quantity'] = $newQty;
            
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Đã cập nhật số lượng!');
        }

        return redirect()->back()->with('error', 'Không tìm thấy sản phẩm trong giỏ!');
    }

    // 3. Xóa một sản phẩm (Dùng thẻ <a> truyền thống, KHÔNG AJAX)
    public function removeItem($id)
    {
        $cart = session()->get('cart', []);

        if(isset($cart[$id])) {
            unset($cart[$id]);
            session()->put('cart', $cart);
            return redirect()->back()->with('success', 'Đã xóa sản phẩm khỏi giỏ!');
        }

        return redirect()->back()->with('error', 'Sản phẩm không tồn tại!');
    }

    // 4. Xóa toàn bộ giỏ hàng
    public function clear() {
        session()->forget('cart');
        return redirect()->back()->with('success', 'Giỏ hàng đã được dọn sạch!');
    }
}