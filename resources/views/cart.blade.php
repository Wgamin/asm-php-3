@extends('layouts.client')

@section('title', 'Giỏ hàng')

@section('content')
<div class="bg-slate-50 py-10">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col sm:flex-row sm:items-end sm:justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black text-slate-900">Giỏ hàng của bạn</h1>
                <p class="text-slate-500 mt-2">Kiểm tra lại sản phẩm, số lượng và mã giảm giá trước khi sang bước thanh toán.</p>
            </div>

            @if(!empty($cart))
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('products.index') }}" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl border border-slate-200 text-slate-700 font-semibold hover:bg-white transition">
                        <i class="fas fa-arrow-left"></i>
                        <span>Tiếp tục mua sắm</span>
                    </a>
                    <a href="{{ route('cart.clear') }}" onclick="return confirm('Bạn muốn xóa toàn bộ giỏ hàng?')" class="inline-flex items-center gap-2 px-5 py-3 rounded-xl bg-red-50 text-red-600 font-semibold hover:bg-red-100 transition">
                        <i class="fas fa-trash"></i>
                        <span>Xóa giỏ hàng</span>
                    </a>
                </div>
            @endif
        </div>

        @if(!empty($cart))
            <div class="grid grid-cols-1 xl:grid-cols-[1.65fr_0.95fr] gap-8">
                <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
                    <div class="hidden md:grid grid-cols-[120px_1.5fr_0.8fr_0.9fr_0.9fr_44px] gap-4 px-6 py-4 text-xs font-bold uppercase tracking-wider text-slate-400 border-b border-slate-100">
                        <span>Ảnh</span>
                        <span>Sản phẩm</span>
                        <span>Đơn giá</span>
                        <span>Số lượng</span>
                        <span>Thành tiền</span>
                        <span></span>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @foreach($cart as $cartKey => $details)
                            @php
                                $imagePath = !empty($details['image']) ? asset('storage/' . $details['image']) : asset('images/default-product.png');
                                $lineTotal = (float) $details['price'] * (int) $details['quantity'];
                            @endphp

                            <div class="grid grid-cols-1 md:grid-cols-[120px_1.5fr_0.8fr_0.9fr_0.9fr_44px] gap-4 px-6 py-6 items-center">
                                <a href="{{ route('product.detail', $details['product_id']) }}" class="block rounded-2xl overflow-hidden bg-slate-100 border border-slate-100">
                                    <img src="{{ $imagePath }}" alt="{{ $details['name'] }}" class="w-full h-28 object-cover">
                                </a>

                                <div>
                                    <a href="{{ route('product.detail', $details['product_id']) }}" class="font-bold text-slate-900 hover:text-emerald-600 transition">
                                        {{ $details['name'] }}
                                    </a>

                                    @if(!empty($details['variant_label']))
                                        <p class="text-sm text-slate-500 mt-2">{{ $details['variant_label'] }}</p>
                                    @endif

                                    @if(isset($details['stock']) && (int) $details['quantity'] >= (int) $details['stock'])
                                        <p class="text-xs text-amber-600 mt-2 font-semibold">Bạn đang chọn mức tối đa theo tồn kho hiện tại.</p>
                                    @endif
                                </div>

                                <div class="text-slate-700 font-bold">
                                    {{ number_format((float) $details['price']) }}đ
                                </div>

                                <div class="flex items-center gap-2">
                                    <a href="{{ route('cart.update_quantity', ['id' => $cartKey, 'quantity' => max(1, $details['quantity'] - 1)]) }}"
                                        class="w-10 h-10 inline-flex items-center justify-center rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 transition">
                                        <i class="fas fa-minus text-xs"></i>
                                    </a>
                                    <span class="min-w-10 text-center font-bold text-slate-900">{{ $details['quantity'] }}</span>
                                    <a href="{{ route('cart.update_quantity', ['id' => $cartKey, 'quantity' => $details['quantity'] + 1]) }}"
                                        class="w-10 h-10 inline-flex items-center justify-center rounded-xl border border-slate-200 text-slate-700 hover:bg-slate-100 transition">
                                        <i class="fas fa-plus text-xs"></i>
                                    </a>
                                </div>

                                <div class="text-emerald-600 font-black text-lg">
                                    {{ number_format($lineTotal) }}đ
                                </div>

                                <a href="{{ route('cart.remove_item', $cartKey) }}"
                                    onclick="return confirm('Xóa sản phẩm này khỏi giỏ hàng?')"
                                    class="w-11 h-11 inline-flex items-center justify-center rounded-xl text-slate-400 hover:text-red-500 hover:bg-red-50 transition">
                                    <i class="fas fa-times"></i>
                                </a>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="space-y-6">
                    <div class="bg-white rounded-3xl shadow-sm border border-slate-100 p-6">
                        <h2 class="text-xl font-black text-slate-900">Mã giảm giá</h2>

                        @if($appliedCoupon)
                            <div class="mt-4 rounded-2xl bg-emerald-50 border border-emerald-100 p-4 flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-bold text-emerald-700">{{ $appliedCoupon->code }}</p>
                                    <p class="text-sm text-slate-600 mt-1">{{ $appliedCoupon->name }}</p>
                                </div>

                                <form action="{{ route('checkout.coupon.remove') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-sm font-semibold text-red-500 hover:text-red-600">Gỡ mã</button>
                                </form>
                            </div>
                        @else
                            <form action="{{ route('checkout.coupon.apply') }}" method="POST" class="mt-4 space-y-3">
                                @csrf
                                <input type="text" name="coupon_code" value="{{ old('coupon_code') }}" placeholder="Nhập mã coupon"
                                    class="w-full px-4 py-3 rounded-2xl border border-slate-200 bg-slate-50 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-50 outline-none transition">
                                <button type="submit" class="w-full px-5 py-3 rounded-2xl bg-slate-900 text-white font-bold hover:bg-slate-800 transition">
                                    Áp dụng mã giảm giá
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="bg-slate-900 text-white rounded-3xl shadow-xl p-6">
                        <h2 class="text-xl font-black">Tóm tắt đơn hàng</h2>

                        <div class="space-y-3 mt-5">
                            <div class="flex items-center justify-between text-slate-300">
                                <span>Tạm tính</span>
                                <span>{{ number_format($summary['subtotal']) }}đ</span>
                            </div>
                            <div class="flex items-center justify-between text-slate-300">
                                <span>Giảm giá</span>
                                <span>-{{ number_format($summary['discount']) }}đ</span>
                            </div>
                            <div class="flex items-center justify-between pt-4 border-t border-slate-700 text-xl font-black">
                                <span>Tổng cộng</span>
                                <span class="text-emerald-400">{{ number_format($summary['total']) }}đ</span>
                            </div>
                        </div>

                        @auth
                            <a href="{{ route('checkout') }}" class="mt-6 w-full inline-flex items-center justify-center gap-3 px-5 py-4 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-black transition">
                                <i class="fas fa-credit-card"></i>
                                <span>Tiến hành thanh toán</span>
                            </a>
                        @else
                            <a href="{{ route('login') }}" class="mt-6 w-full inline-flex items-center justify-center gap-3 px-5 py-4 rounded-2xl bg-emerald-500 hover:bg-emerald-600 text-white font-black transition">
                                <i class="fas fa-user-lock"></i>
                                <span>Đăng nhập để thanh toán</span>
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        @else
            <div class="bg-white rounded-3xl shadow-sm border border-slate-100 px-6 py-20 text-center">
                <div class="inline-flex items-center justify-center w-20 h-20 rounded-full bg-emerald-50 text-emerald-600 mb-6">
                    <i class="fas fa-shopping-basket text-3xl"></i>
                </div>
                <h2 class="text-2xl font-black text-slate-900">Giỏ hàng đang trống</h2>
                <p class="text-slate-500 mt-3 max-w-md mx-auto">Bạn chưa thêm sản phẩm nào vào giỏ. Hãy quay lại cửa hàng để chọn món trước khi thanh toán.</p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center gap-3 mt-8 px-6 py-4 rounded-2xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition">
                    <i class="fas fa-store"></i>
                    <span>Mua sắm ngay</span>
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
