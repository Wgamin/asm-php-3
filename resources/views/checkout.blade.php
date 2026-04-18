@extends('layouts.client')

@section('title', 'Thanh toán')

@section('content')
<div class="bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        @if(count($cart) > 0)
            @php
                $selectedPaymentMethod = (string) old('payment_method', 'cod');
                $shippingFee = (float) ($shippingQuote['fee'] ?? 0);
            @endphp

            @if($errors->any())
                <div class="mb-6 rounded-2xl border border-red-100 bg-red-50 p-4 text-red-700">
                    <ul class="space-y-1 text-sm">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 gap-10 lg:grid-cols-2">
                <form id="checkout-form" action="{{ route('order.store') }}" method="POST" class="rounded-3xl border border-gray-100 bg-white p-8 shadow-sm">
                    @csrf

                    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Thông tin giao hàng</h2>
                            <div class="mt-3">
                                <x-admin-info>
                                    Chọn địa chỉ đã lưu trong hồ sơ để xác nhận đơn nhanh hơn.
                                </x-admin-info>
                            </div>
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
                                                        <span class="rounded-full bg-emerald-600 px-2 py-1 text-[10px] font-black uppercase tracking-widest text-white">Mặc định</span>
                                                    @endif
                                                </div>
                                                <p class="mt-1 text-sm text-slate-500">{{ $address->phone }}</p>
                                            </div>

                                            <div class="h-5 w-5 rounded-full border-2 border-slate-300 peer-checked:border-emerald-500 peer-checked:bg-emerald-500"></div>
                                        </div>

                                        <div class="mt-4 text-sm leading-6 text-slate-600">
                                            {{ $address->full_address }}
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div class="rounded-2xl border border-dashed border-amber-200 bg-amber-50 px-6 py-8 text-center">
                            <div class="mb-4 inline-flex h-12 w-12 items-center justify-center rounded-full bg-white text-amber-500">
                                <i class="fas fa-map-marked-alt"></i>
                            </div>
                            <p class="font-bold text-slate-800">Bạn chưa có địa chỉ giao hàng.</p>
                            <p class="mt-2 text-sm text-slate-500">Vào hồ sơ để thêm địa chỉ trước khi xác nhận đơn hàng.</p>
                            <a href="{{ route('profile', ['tab' => 'addresses']) }}" class="mt-5 inline-flex items-center gap-2 rounded-xl bg-emerald-600 px-5 py-3 font-bold text-white transition hover:bg-emerald-700">
                                <i class="fas fa-plus"></i>
                                <span>Thêm địa chỉ ngay</span>
                            </a>
                        </div>
                    @endif

                    @error('selected_address_id')
                        <p class="mt-3 text-sm text-red-500">{{ $message }}</p>
                    @enderror

                    <div class="mt-8 border-t border-slate-200 pt-8">
                        <div class="mb-4 flex items-start justify-between gap-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-800">Đơn vị vận chuyển</h3>
                                <div class="mt-3">
                                    <x-admin-info>
                                        Phí ship đang được tính theo rule nội bộ cũ của hệ thống.
                                    </x-admin-info>
                                </div>
                            </div>
                            @if($shippingQuote)
                                <span class="inline-flex items-center rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold uppercase tracking-wider text-emerald-700">
                                    {{ $shippingQuote['method'] ?? 'shipping' }}
                                </span>
                            @endif
                        </div>

                        @if($shippingQuote)
                            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-5">
                                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                                    <div>
                                        <p class="font-bold text-slate-800">{{ $shippingQuote['label'] ?? 'Vận chuyển' }}</p>
                                        <p class="mt-1 text-sm text-slate-500">{{ $shippingQuote['description'] ?? 'Đã áp dụng phí ship hiện tại.' }}</p>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-lg font-black text-emerald-600">{{ number_format($shippingFee) }}đ</p>
                                        @if(!empty($shippingQuote['estimated_days']))
                                            <p class="mt-1 text-xs text-slate-400">Dự kiến {{ $shippingQuote['estimated_days'] }} ngày</p>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <input type="hidden" name="shipping_provider" value="{{ $selectedShippingProvider ?: ($shippingQuote['key'] ?? '') }}">
                        @else
                            <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-700">
                                Chưa tính được phí ship cho địa chỉ này.
                            </div>
                        @endif

                        @error('shipping_provider')
                            <p class="mt-3 text-sm text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-8 space-y-4 border-t border-slate-200 pt-8">
                        <div>
                            <h3 class="mb-4 text-xl font-bold text-gray-800">Phương thức thanh toán</h3>

                            <div class="overflow-hidden rounded-[1.5rem] border border-slate-200 bg-white shadow-sm">
                                <div class="border-b border-slate-200 bg-[linear-gradient(180deg,#fafaf9,#ffffff)] px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-100 text-slate-600">
                                            <i class="fas fa-wallet text-lg"></i>
                                        </span>
                                        <div>
                                            <p class="text-sm font-bold text-slate-900">Chọn cách thanh toán</p>
                                            <p class="mt-1 text-xs uppercase tracking-[0.16em] text-slate-500">Thanh toán an toàn</p>
                                        </div>
                                    </div>
                                </div>

                                <div class="divide-y divide-slate-100">
                                    <label class="block cursor-pointer">
                                        <input
                                            type="radio"
                                            name="payment_method"
                                            value="cod"
                                            class="peer sr-only"
                                            {{ $selectedPaymentMethod === 'cod' ? 'checked' : '' }}
                                        >
                                        <div class="flex items-center gap-4 px-5 py-4 transition hover:bg-slate-50 peer-checked:bg-[linear-gradient(180deg,#fafaf9,#f3f4f6)]">
                                            <span class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-600">
                                                <i class="fas fa-money-bill-wave text-xl"></i>
                                            </span>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <p class="text-base font-bold text-slate-900">Thanh toán khi nhận hàng</p>
                                                    <span class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.16em] text-slate-500">COD</span>
                                                </div>
                                                <p class="mt-1 text-sm leading-6 text-slate-500">Thanh toán bằng tiền mặt sau khi nhận hàng từ shipper.</p>
                                            </div>
                                            <div class="flex items-center gap-4">
                                                <span class="hidden rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-500 md:inline-flex">Không cần chuyển cổng</span>
                                                <span class="relative flex h-6 w-6 items-center justify-center rounded-full border-2 border-slate-300 bg-white transition peer-checked:border-emerald-500 peer-checked:shadow-[0_0_0_4px_rgba(16,185,129,0.14)]">
                                                    <span class="h-3 w-3 rounded-full bg-emerald-500 opacity-0 scale-75 transition duration-150 peer-checked:scale-100 peer-checked:opacity-100"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </label>

                                    <label class="block cursor-pointer">
                                        <input
                                            type="radio"
                                            name="payment_method"
                                            value="vnpay"
                                            class="peer sr-only"
                                            {{ $selectedPaymentMethod === 'vnpay' ? 'checked' : '' }}
                                        >
                                        <div class="flex items-center gap-4 px-5 py-4 transition hover:bg-slate-50 peer-checked:bg-[linear-gradient(180deg,#fafaf9,#f3f4f6)]">
                                            <span class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-600">
                                                <i class="fas fa-building-columns text-xl"></i>
                                            </span>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <p class="text-base font-bold text-slate-900">VNPay</p>
                                                    <span class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.16em] text-slate-500">Online</span>
                                                </div>
                                                <p class="mt-1 text-sm leading-6 text-slate-500">Chuyển sang cổng VNPay để thanh toán qua ATM, QR hoặc thẻ nội địa.</p>
                                            </div>
                                            <div class="flex items-center gap-4">
                                                <span class="hidden rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-500 md:inline-flex">Cổng thanh toán</span>
                                                <span class="relative flex h-6 w-6 items-center justify-center rounded-full border-2 border-slate-300 bg-white transition peer-checked:border-emerald-500 peer-checked:shadow-[0_0_0_4px_rgba(16,185,129,0.14)]">
                                                    <span class="h-3 w-3 rounded-full bg-emerald-500 opacity-0 scale-75 transition duration-150 peer-checked:scale-100 peer-checked:opacity-100"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </label>

                                    <label class="block cursor-pointer">
                                        <input
                                            type="radio"
                                            name="payment_method"
                                            value="momo"
                                            class="peer sr-only"
                                            {{ $selectedPaymentMethod === 'momo' ? 'checked' : '' }}
                                        >
                                        <div class="flex items-center gap-4 px-5 py-4 transition hover:bg-slate-50 peer-checked:bg-[linear-gradient(180deg,#fafaf9,#f3f4f6)]">
                                            <span class="flex h-14 w-14 flex-shrink-0 items-center justify-center rounded-2xl bg-slate-100 text-slate-600">
                                                <i class="fas fa-mobile-screen-button text-xl"></i>
                                            </span>
                                            <div class="min-w-0 flex-1">
                                                <div class="flex flex-wrap items-center gap-2">
                                                    <p class="text-base font-bold text-slate-900">MoMo</p>
                                                    <span class="rounded-full border border-slate-200 bg-slate-50 px-2.5 py-1 text-[10px] font-black uppercase tracking-[0.16em] text-slate-500">Gateway</span>
                                                </div>
                                                <p class="mt-1 text-sm leading-6 text-slate-500">Chuyển sang MoMo sandbox để test ví, callback và luồng thanh toán thử.</p>
                                            </div>
                                            <div class="flex items-center gap-4">
                                                <span class="hidden rounded-full bg-slate-100 px-3 py-1 text-[11px] font-semibold text-slate-500 md:inline-flex">ATM / Card</span>
                                                <span class="relative flex h-6 w-6 items-center justify-center rounded-full border-2 border-slate-300 bg-white transition peer-checked:border-emerald-500 peer-checked:shadow-[0_0_0_4px_rgba(16,185,129,0.14)]">
                                                    <span class="h-3 w-3 rounded-full bg-emerald-500 opacity-0 scale-75 transition duration-150 peer-checked:scale-100 peer-checked:opacity-100"></span>
                                                </span>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            @error('payment_method')
                                <p class="mt-3 text-sm text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <textarea name="note" placeholder="Ghi chú đơn hàng (không bắt buộc)" class="w-full rounded-2xl border border-slate-200 bg-gray-50 px-5 py-3">{{ old('note') }}</textarea>
                    </div>
                </form>

                <div class="h-fit rounded-3xl bg-slate-800 p-8 text-white shadow-xl">
                    <h2 class="mb-6 text-2xl font-bold">Đơn hàng của bạn</h2>

                    <div class="mb-6 space-y-4">
                        @foreach($cart as $details)
                            <div class="flex items-start justify-between gap-4 border-b border-slate-700 pb-4">
                                <div class="min-w-0">
                                    <p class="text-sm font-bold">{{ $details['name'] }}</p>
                                    @if(!empty($details['variant_label']))
                                        <p class="mt-1 text-xs text-slate-300">{{ $details['variant_label'] }}</p>
                                    @endif
                                    <p class="mt-2 text-xs text-slate-400">Số lượng: {{ $details['quantity'] }}</p>
                                </div>

                                <div class="text-right">
                                    <p class="font-bold text-emerald-400">{{ number_format($details['price'] * $details['quantity']) }}đ</p>
                                    <p class="mt-1 text-xs text-slate-400">{{ number_format($details['price']) }}đ / sản phẩm</p>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="mb-6 rounded-2xl border border-slate-700 bg-slate-900/40 p-4">
                        <p class="mb-3 text-sm font-semibold">Mã giảm giá</p>

                        @if($appliedCoupon)
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="font-bold text-emerald-400">{{ $appliedCoupon->code }}</p>
                                    <p class="text-sm text-slate-300">{{ $appliedCoupon->name }}</p>
                                </div>
                                <form action="{{ route('checkout.coupon.remove') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-xs text-red-300 transition hover:text-red-200">Gỡ mã</button>
                                </form>
                            </div>
                        @else
                            <div class="text-sm text-slate-300">
                                Bạn chưa áp mã giảm giá.
                                <a href="{{ route('cart.index') }}" class="font-bold text-emerald-400 hover:text-emerald-300">Quay lại giỏ hàng để áp mã</a>.
                            </div>
                        @endif
                    </div>

                    <div class="space-y-3 border-t border-slate-700 pt-4">
                        <div class="flex justify-between text-slate-300">
                            <span>Tạm tính</span>
                            <span>{{ number_format($summary['subtotal']) }}đ</span>
                        </div>
                        <div class="flex justify-between text-slate-300">
                            <span>Giảm giá</span>
                            <span>-{{ number_format($summary['discount']) }}đ</span>
                        </div>
                        <div class="flex justify-between text-slate-300">
                            <span>Phí vận chuyển</span>
                            <span>{{ number_format($shippingFee) }}đ</span>
                        </div>
                        <div class="flex justify-between pt-2 text-xl font-bold">
                            <span>TỔNG THANH TOÁN</span>
                            <span class="text-emerald-400">{{ number_format($payableTotal) }}đ</span>
                        </div>
                    </div>

                    <button
                        type="submit"
                        form="checkout-form"
                        class="mt-8 w-full rounded-2xl bg-emerald-500 py-4 font-bold transition hover:bg-emerald-600 disabled:cursor-not-allowed disabled:bg-slate-400"
                        {{ $addresses->isEmpty() || ! $shippingQuote ? 'disabled' : '' }}
                    >
                        TIẾP TỤC THANH TOÁN
                    </button>
                </div>
            </div>
        @else
            <div class="rounded-3xl bg-white py-20 text-center shadow-sm">
                <p class="mb-4 text-gray-500">Giỏ hàng trống.</p>
                <a href="{{ route('cart.index') }}" class="rounded-full bg-emerald-500 px-6 py-2 text-white">Về giỏ hàng</a>
            </div>
        @endif
    </div>
</div>
@endsection
