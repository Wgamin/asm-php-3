@extends('layouts.client')

@section('content')
<div class="bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        @if(count($cart) > 0)
        @if($errors->any())
            <div class="mb-6 p-4 rounded-2xl border border-red-100 bg-red-50 text-red-700">
                <ul class="space-y-1 text-sm">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10">
            <form id="checkout-form" action="{{ route('order.store') }}" method="POST" class="bg-white p-8 rounded-3xl shadow-sm border border-gray-100">
                @csrf
                <h2 class="text-2xl font-bold mb-6 text-gray-800">Thông tin giao hàng</h2>
                <div class="space-y-4">
                    <input type="text" name="full_name" value="{{ old('full_name', Auth::user()->name) }}" required placeholder="Họ tên" class="w-full px-5 py-3 rounded-2xl bg-gray-50 border-none border">
                    <input type="text" name="phone" value="{{ old('phone') }}" required placeholder="Số điện thoại" class="w-full px-5 py-3 rounded-2xl bg-gray-50 border-none border">
                    <textarea name="address" required placeholder="Địa chỉ cụ thể" class="w-full px-5 py-3 rounded-2xl bg-gray-50 border-none border">{{ old('address') }}</textarea>
                    <textarea name="note" placeholder="Ghi chú đơn hàng (không bắt buộc)" class="w-full px-5 py-3 rounded-2xl bg-gray-50 border-none border">{{ old('note') }}</textarea>
                    <input type="hidden" name="payment_method" value="cod">
                </div>
            </form>

            <div class="bg-slate-800 text-white p-8 rounded-3xl shadow-xl h-fit">
                <h2 class="text-2xl font-bold mb-6">Đơn hàng của bạn</h2>
                <div class="space-y-4 mb-6">
                    @foreach($cart as $id => $details)
                        <div class="flex justify-between items-center border-b border-slate-700 pb-4">
                            <div>
                                <p class="font-bold text-sm">{{ $details['name'] }}</p>
                                <div class="flex items-center gap-3 mt-2">
                                    <a href="{{ route('cart.update_quantity', ['id' => $id, 'quantity' => $details['quantity'] - 1]) }}"
                                       class="bg-slate-700 px-3 py-1 rounded">-</a>

                                    <span class="qty-text">{{ $details['quantity'] }}</span>

                                    <a href="{{ route('cart.update_quantity', ['id' => $id, 'quantity' => $details['quantity'] + 1]) }}"
                                       class="bg-slate-700 px-3 py-1 rounded">+</a>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-emerald-400 font-bold">{{ number_format($details['price'] * $details['quantity']) }}đ</p>

                                <a href="{{ route('cart.remove_item', $id) }}"
                                   onclick="return confirm('Xóa món này?')"
                                   class="text-xs text-slate-500 hover:text-red-400 mt-1 block">Xóa</a>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="bg-slate-900/40 rounded-2xl p-4 mb-6 border border-slate-700">
                    <p class="text-sm font-semibold mb-3">Mã giảm giá</p>

                    @if($appliedCoupon)
                        <div class="flex items-start justify-between gap-4">
                            <div>
                                <p class="font-bold text-emerald-400">{{ $appliedCoupon->code }}</p>
                                <p class="text-sm text-slate-300">{{ $appliedCoupon->name }}</p>
                            </div>
                            <form action="{{ route('checkout.coupon.remove') }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-xs text-red-300 hover:text-red-200 transition">
                                    Gỡ mã
                                </button>
                            </form>
                        </div>
                    @else
                        <form action="{{ route('checkout.coupon.apply') }}" method="POST" class="flex flex-col sm:flex-row gap-3">
                            @csrf
                            <input type="text" name="coupon_code" value="{{ old('coupon_code') }}" placeholder="Nhập mã coupon"
                                   class="flex-1 px-4 py-3 rounded-xl bg-slate-800 border border-slate-600 text-white placeholder:text-slate-400 focus:ring-2 focus:ring-emerald-500 outline-none">
                            <button type="submit" class="px-5 py-3 rounded-xl bg-white text-slate-900 font-bold hover:bg-slate-100 transition">
                                Áp dụng
                            </button>
                        </form>
                    @endif
                </div>

                <div class="space-y-3 pt-4 border-t border-slate-700">
                    <div class="flex justify-between text-slate-300">
                        <span>Tạm tính</span>
                        <span>{{ number_format($summary['subtotal']) }}đ</span>
                    </div>
                    <div class="flex justify-between text-slate-300">
                        <span>Giảm giá</span>
                        <span>-{{ number_format($summary['discount']) }}đ</span>
                    </div>
                    <div class="flex justify-between text-xl font-bold pt-2">
                        <span>TỔNG CỘNG</span>
                        <span class="text-emerald-400">{{ number_format($summary['total']) }}đ</span>
                    </div>
                </div>

                <button type="submit" form="checkout-form" class="w-full bg-emerald-500 py-4 rounded-2xl mt-8 font-bold hover:bg-emerald-600 transition">
                    XÁC NHẬN ĐẶT HÀNG
                </button>
            </div>
        </div>
        @else
            <div class="text-center py-20 bg-white rounded-3xl shadow-sm">
                <p class="text-gray-500 mb-4">Giỏ hàng trống!</p>
                <a href="/" class="text-white bg-emerald-500 px-6 py-2 rounded-full">Mua sắm ngay</a>
            </div>
        @endif
    </div>
</div>
@endsection
