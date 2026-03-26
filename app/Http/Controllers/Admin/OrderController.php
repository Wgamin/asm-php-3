<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    // Hiển thị danh sách đơn hàng
    public function index()
    {
        $orders = Order::with('user')->orderBy('created_at', 'desc')->paginate(10);
        return view('admin.orders.index', compact('orders'));
    }

    // Xem chi tiết đơn hàng
    public function show($id)
    {
        $order = Order::with('items.product', 'user')->findOrFail($id);
        return view('admin.orders.show', compact('order'));
    }

    // Cập nhật trạng thái đơn hàng (Ví dụ: Chờ xử lý -> Đang giao)
    public function updateStatus(Request $request, $id)
    {
        $order = Order::findOrFail($id);
        $order->update(['status' => $request->status]);
        
        return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công!');
    }
    
    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        
        // Xóa các chi tiết đơn hàng trước (nếu database chưa thiết lập xóa tự động)
        $order->items()->delete(); 
        
        // Xóa đơn hàng
        $order->delete();
        
        return redirect()->route('admin.orders.index')->with('success', 'Xóa đơn hàng thành công!');
    }
}