@extends('layouts.client')

@section('content')
<div class="bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        @php $cart = session()->get('cart', []); $total = 0; @endphp

        @if(count($cart) > 0)
        <form action="{{ route('order.store') }}" method="POST">
            @csrf
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
                {{-- Cột trái: Form nhập liệu --}}
                <div class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                    <h2 class="text-2xl font-bold mb-6 text-gray-800">Thông tin giao hàng</h2>
                    <div class="space-y-4">
                        <input type="text" name="full_name" value="{{ Auth::user()->name }}" required placeholder="Họ tên" class="w-full px-5 py-3 rounded-2xl bg-gray-50 border-none border">
                        <input type="text" name="phone" required placeholder="Số điện thoại" class="w-full px-5 py-3 rounded-2xl bg-gray-50 border-none border">
                        <textarea name="address" required placeholder="Địa chỉ cụ thể" class="w-full px-5 py-3 rounded-2xl bg-gray-50 border-none border"></textarea>
                        
                        {{-- BỔ SUNG: Input ẩn cho phương thức thanh toán mặc định nếu form của bạn chưa có --}}
                        <input type="hidden" name="payment_method" value="cod">
                    </div>
                </div>

                {{-- Cột phải: Đơn hàng --}}
                <div class="bg-slate-800 text-white p-8 rounded-3xl shadow-xl h-fit">
                    <h2 class="text-2xl font-bold mb-6">Đơn hàng của bạn</h2>
                    <div class="space-y-4 mb-6">
                        @foreach($cart as $id => $details)
                            @php $total += $details['price'] * $details['quantity'] @endphp
                            <div class="flex justify-between items-center border-b border-slate-700 pb-4">
                                <div>
                                    <p class="font-bold text-sm">{{ $details['name'] }}</p>
                                    <div class="flex items-center gap-3 mt-2">
                                        {{-- Nút Giảm số lượng --}}
                                        <a href="{{ route('cart.update_quantity', ['id' => $id, 'quantity' => $details['quantity'] - 1]) }}" 
                                           class="bg-slate-700 px-3 py-1 rounded">-</a>
                                        
                                        <span class="qty-text">{{ $details['quantity'] }}</span>
                                        
                                        {{-- Nút Tăng số lượng --}}
                                        <a href="{{ route('cart.update_quantity', ['id' => $id, 'quantity' => $details['quantity'] + 1]) }}" 
                                           class="bg-slate-700 px-3 py-1 rounded">+</a>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-emerald-400 font-bold">{{ number_format($details['price'] * $details['quantity']) }}đ</p>
                                    
                                    {{-- Nút Xóa (Dùng thẻ A để thay thế AJAX) --}}
                                    <a href="{{ route('cart.remove_item', $id) }}" 
                                       onclick="return confirm('Xóa món này?')"
                                       class="text-xs text-slate-500 hover:text-red-400 mt-1 block">Xóa</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex justify-between text-xl font-bold pt-4 border-t border-slate-700">
                        <span>TỔNG CỘNG</span>
                        <span class="text-emerald-400">{{ number_format($total) }}đ</span>
                    </div>
                    <button type="submit" class="w-full bg-emerald-500 py-4 rounded-2xl mt-8 font-bold hover:bg-emerald-600 transition">XÁC NHẬN ĐẶT HÀNG</button>
                </div>
            </div>
        </form>
        @else
            <div class="text-center py-20 bg-white rounded-3xl shadow-sm">
                <p class="text-gray-500 mb-4">Giỏ hàng trống!</p>
                <a href="/" class="text-white bg-emerald-500 px-6 py-2 rounded-full">Mua sắm ngay</a>
            </div>
        @endif
    </div>
</div>
@endsection