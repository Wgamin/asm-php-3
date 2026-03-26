<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    // Hiển thị trang Checkout
    public function checkout()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('products.index')->with('error', 'Giỏ hàng trống!');
        }
        return view('checkout', compact('cart'));
    }

    // Xử lý lưu đơn hàng
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'payment_method' => 'required',
        ]);

        $cart = session()->get('cart', []);
        $total = 0;
        foreach($cart as $item) { $total += $item['price'] * $item['quantity']; }

        // Dùng DB Transaction để đảm bảo nếu lỗi thì không lưu gì cả
        DB::beginTransaction();
        try {
            $order = Order::create([
                'user_id' => Auth::id(),
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'full_name' => $request->full_name,
                'phone' => $request->phone,
                'email' => Auth::user()->email,
                'address' => $request->address,
                'note' => $request->note,
                'total_amount' => $total,
                'status' => 'pending',
                'payment_method' => $request->payment_method,
            ]);

            foreach ($cart as $id => $details) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $id,
                    'quantity' => $details['quantity'],
                    'price' => $details['price'],
                ]);
            }

            DB::commit();
            session()->forget('cart'); // Xóa giỏ hàng sau khi đặt thành công

            return redirect()->route('order.success')->with('success_order', $order->order_number);
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', 'Có lỗi xảy ra, vui lòng thử lại!');
        }
    }
    // Tìm đến hàm success trong OrderController

    public function success()
    {
        // Lấy số đơn hàng vừa lưu từ session
        $order_number = session('success_order');

        // Nếu không có (truy cập thủ công), có thể chuyển hướng về trang chủ
        if (!$order_number) {
            return redirect()->route('home');
        }

        // Truyền biến ra view
        return view('order_success', compact('order_number'));
    }
}