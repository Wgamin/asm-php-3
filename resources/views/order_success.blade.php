@extends('layouts.client')

@section('title', 'Đặt hàng thành công')

@section('content')
<div class="bg-gray-50 py-16 min-h-screen">
    <div class="container mx-auto px-4 max-w-2xl">

        {{-- Success Card --}}
        <div class="rounded-3xl bg-white shadow-xl p-10 text-center">

            {{-- Icon --}}
            <div class="mx-auto mb-6 flex h-24 w-24 items-center justify-center rounded-full bg-emerald-100">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-emerald-600" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                </svg>
            </div>

            <h1 class="text-3xl font-extrabold text-gray-800 mb-2">Đặt hàng thành công!</h1>
            <p class="text-gray-500 mb-8">Cảm ơn bạn đã mua hàng. Chúng tôi đã nhận được đơn hàng của bạn.</p>

            @if($order_number)
                <div class="rounded-2xl bg-emerald-50 border border-emerald-200 px-6 py-4 mb-8 inline-block">
                    <p class="text-sm text-emerald-700 font-semibold">Mã đơn hàng</p>
                    <p class="text-2xl font-black text-emerald-800 tracking-widest mt-1">{{ $order_number }}</p>
                </div>
            @endif

            @if($order)
                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-6 text-left mb-8 space-y-3">
                    <h2 class="text-base font-bold text-gray-700 mb-4">Chi tiết đơn hàng</h2>

                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Người nhận</span>
                        <span class="font-semibold text-gray-800">{{ $order->full_name }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Số điện thoại</span>
                        <span class="font-semibold text-gray-800">{{ $order->phone }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Địa chỉ giao hàng</span>
                        <span class="font-semibold text-gray-800 text-right max-w-xs">{{ $order->address }}</span>
                    </div>

                    <div class="border-t border-gray-200 pt-3 mt-3">
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Tạm tính</span>
                            <span>{{ number_format($order->subtotal_amount) }}đ</span>
                        </div>
                        @if((float)$order->discount_amount > 0)
                        <div class="flex justify-between text-sm text-emerald-600">
                            <span>Giảm giá</span>
                            <span>-{{ number_format($order->discount_amount) }}đ</span>
                        </div>
                        @endif
                        <div class="flex justify-between text-sm text-gray-600">
                            <span>Phí vận chuyển</span>
                            <span>{{ number_format($order->shipping_fee_amount) }}đ</span>
                        </div>
                        <div class="flex justify-between text-base font-extrabold text-gray-800 mt-2 pt-2 border-t border-gray-200">
                            <span>Tổng thanh toán</span>
                            <span class="text-emerald-600">{{ number_format($order->payable_amount) }}đ</span>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-3 mt-3 flex justify-between text-sm text-gray-600">
                        <span>Phương thức thanh toán</span>
                        <span class="font-semibold text-gray-800 uppercase">
                            @switch($order->payment_method)
                                @case('cod') Thanh toán khi nhận hàng @break
                                @case('vnpay') VNPay @break
                                @case('momo') MoMo @break
                                @case('zalopay') ZaloPay @break
                                @default {{ $order->payment_method }}
                            @endswitch
                        </span>
                    </div>

                    <div class="flex justify-between text-sm text-gray-600">
                        <span>Trạng thái thanh toán</span>
                        <span class="font-semibold
                            @if($order->payment?->status === 'paid') text-emerald-600
                            @elseif($order->payment?->status === 'failed') text-red-500
                            @else text-amber-500
                            @endif">
                            @switch($order->payment?->status)
                                @case('paid') Đã thanh toán ✓ @break
                                @case('failed') Thanh toán thất bại @break
                                @case('pending') Chờ xử lý @break
                                @default Đang cập nhật
                            @endswitch
                        </span>
                    </div>
                </div>
            @endif

            <p class="text-sm text-gray-400 mb-8">
                Chúng tôi sẽ xử lý và giao hàng sớm nhất có thể. Bạn có thể theo dõi đơn hàng trong trang cá nhân.
            </p>

            <div class="flex flex-col sm:flex-row gap-3 justify-center">
                <a href="{{ route('home') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-2xl border border-gray-200 bg-white px-6 py-3 text-sm font-bold text-gray-700 transition hover:bg-gray-50">
                    <i class="fas fa-home"></i>
                    Về trang chủ
                </a>

                @auth
                <a href="{{ route('profile', ['tab' => 'orders']) }}"
                   class="inline-flex items-center justify-center gap-2 rounded-2xl bg-emerald-500 px-6 py-3 text-sm font-bold text-white transition hover:bg-emerald-600">
                    <i class="fas fa-box-open"></i>
                    Xem đơn hàng của tôi
                </a>
                @endauth

                <a href="{{ route('products.index') }}"
                   class="inline-flex items-center justify-center gap-2 rounded-2xl bg-slate-800 px-6 py-3 text-sm font-bold text-white transition hover:bg-slate-700">
                    <i class="fas fa-shopping-bag"></i>
                    Tiếp tục mua sắm
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
