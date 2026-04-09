@extends('layouts.client')

@section('title', 'Thanh toán')

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

                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Thông tin giao hàng</h2>
                            <p class="text-sm text-gray-500 mt-2">Chọn địa chỉ đã lưu trong hồ sơ để xác nhận đơn nhanh hơn.</p>
                        </div>

                        <div class="flex flex-wrap items-center gap-3">
                            <a href="{{ route('cart.index') }}" class="inline-flex items-center gap-2 text-sm font-bold text-slate-600 hover:text-slate-800">
                                <i class="fas fa-arrow-left"></i>
                                <span>Quay lại giỏ hàng</span>
                            </a>
                            <a href="{{ route('profile', ['tab' => 'addresses']) }}" class="inline-flex items-center gap-2 text-sm font-bold text-emerald-600 hover:text-emerald-700">
                                <i class="fas fa-map-marker-alt"></i>
                                <span>Quản lý địa chỉ</span>
                            </a>
                        </div>
                    </div>

                    @if($addresses->isNotEmpty())
                        <div class="space-y-4">
                            @foreach($addresses as $address)
                                <label class="block cursor-pointer">
                                    <input
                                        type="radio"
                                        name="selected_address_id"
                                        value="{{ $address->id }}"
                                        class="peer sr-only"
                                        {{ (int) old('selected_address_id', $selectedAddressId) === $address->id ? 'checked' : '' }}
                                    >

                                    <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5 transition peer-checked:border-emerald-500 peer-checked:bg-emerald-50 peer-checked:ring-4 peer-checked:ring-emerald-100">
                                        <div class="flex items-start justify-between gap-4">
                                            <div>
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <p class="font-bold text-slate-800">{{ $address->full_name }}</p>
                                                    @if($address->is_default)
                                                        <span class="text-[10px] uppercase tracking-widest font-black bg-emerald-600 text-white px-2 py-1 rounded-full">Mặc định</span>
                                                    @endif
                                                </div>
                                                <p class="text-sm text-slate-500 mt-1">{{ $address->phone }}</p>
                                            </div>

                                            <div class="w-5 h-5 rounded-full border-2 border-slate-300 peer-checked:border-emerald-500 peer-checked:bg-emerald-500"></div>
                                        </div>

                                        <div class="mt-4 text-sm text-slate-600 leading-6">
                                            {{ $address->full_address }}
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-2xl border border-dashed border-amber-200 bg-amber-50 px-6 py-8 text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-white text-amber-500 mb-4">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <p class="font-bold text-slate-800">Bạn chưa có địa chỉ giao hàng.</p>
                            <p class="text-sm text-slate-500 mt-2">Vào hồ sơ để thêm địa chỉ trước khi xác nhận đơn hàng.</p>
                            <a href="{{ route('profile', ['tab' => 'addresses']) }}" class="inline-flex items-center gap-2 mt-5 px-5 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold transition">
                                <i class="fas fa-plus"></i>
                                <span>Thêm địa chỉ ngay</span>
                            </a>
                        </div>
                    @endif

                    @error('selected_address_id')
                        <p class="text-red-500 text-sm mt-3">{{ $message }}</p>
                    @enderror

                    <div class="mt-6 space-y-4">
                        <textarea name="note" placeholder="Ghi chú đơn hàng (không bắt buộc)" class="w-full px-5 py-3 rounded-2xl bg-gray-50 border border-slate-200">{{ old('note') }}</textarea>
                        <input type="hidden" name="payment_method" value="cod">
                    </div>
                </form>

                <div class="bg-slate-800 text-white p-8 rounded-3xl shadow-xl h-fit">
                    <h2 class="text-2xl font-bold mb-6">Đơn hàng của bạn</h2>

                    <div class="space-y-4 mb-6">
                        @foreach($cart as $cartKey => $details)
                            <div class="flex justify-between items-start gap-4 border-b border-slate-700 pb-4">
                                <div class="min-w-0">
                                    <p class="font-bold text-sm">{{ $details['name'] }}</p>
                                    @if(!empty($details['variant_label']))
                                        <p class="text-xs text-slate-300 mt-1">{{ $details['variant_label'] }}</p>
                                    @endif
                                    <p class="text-xs text-slate-400 mt-2">Số lượng: {{ $details['quantity'] }}</p>
                                </div>

                                <div class="text-right">
                                    <p class="text-emerald-400 font-bold">{{ number_format($details['price'] * $details['quantity']) }}d</p>
                                    <p class="text-xs text-slate-400 mt-1">{{ number_format($details['price']) }}đ / sản phẩm</p>
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
                                    <button type="submit" class="text-xs text-red-300 hover:text-red-200 transition">Gỡ mã</button>
                                </form>
                            </div>
                        @else
                            <div class="text-sm text-slate-300">
                                Bạn chưa áp mã giảm giá.
                                <a href="{{ route('cart.index') }}" class="font-bold text-emerald-400 hover:text-emerald-300">Quay lại giỏ hàng để áp mã</a>.
                            </div>
                        @endif
                    </div>

                    <div class="space-y-3 pt-4 border-t border-slate-700">
                        <div class="flex justify-between text-slate-300">
                            <span>Tạm tính</span>
                            <span>{{ number_format($summary['subtotal']) }}d</span>
                        </div>
                        <div class="flex justify-between text-slate-300">
                            <span>Giảm giá</span>
                            <span>-{{ number_format($summary['discount']) }}d</span>
                        </div>
                        <div class="flex justify-between text-xl font-bold pt-2">
                            <span>TỔNG CỘNG</span>
                            <span class="text-emerald-400">{{ number_format($summary['total']) }}d</span>
                        </div>
                    </div>

                    <button type="submit" form="checkout-form" class="w-full bg-emerald-500 py-4 rounded-2xl mt-8 font-bold hover:bg-emerald-600 transition disabled:bg-slate-400 disabled:cursor-not-allowed" {{ $addresses->isEmpty() ? 'disabled' : '' }}>
                        XÁC NHẬN ĐẶT HÀNG
                    </button>
                </div>
            </div>
        @else
            <div class="text-center py-20 bg-white rounded-3xl shadow-sm">
                <p class="text-gray-500 mb-4">Giỏ hàng trống!</p>
                <a href="{{ route('cart.index') }}" class="text-white bg-emerald-500 px-6 py-2 rounded-full">Về giỏ hàng</a>
            </div>
        @endif
    </div>
</div>
@endsection
