@extends('layouts.client')

@section('title', 'Chi tiết đơn hàng')

@section('content')
<div class="bg-slate-50 py-10">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="mb-6 flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
            <div>
                <a href="{{ route('profile', ['tab' => 'orders']) }}" class="inline-flex items-center gap-2 text-sm font-semibold text-slate-500 transition hover:text-emerald-600">
                    <i class="fas fa-arrow-left"></i>
                    Quay lại lịch sử đơn hàng
                </a>
                <h1 class="mt-3 text-3xl font-extrabold text-slate-900">Đơn hàng {{ $order->order_number ?: ('#' . $order->id) }}</h1>
                <p class="mt-2 text-sm text-slate-500">Đặt lúc {{ $order->created_at->format('d/m/Y H:i') }}</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <span class="rounded-full px-4 py-2 text-xs font-bold uppercase tracking-wider {{ $order->status_color }}">
                    {{ $order->status_text }}
                </span>

                @if($order->payment)
                    <span class="rounded-full px-4 py-2 text-xs font-bold uppercase tracking-wider {{ $order->payment->status_color }}">
                        {{ $order->payment->status_text }}
                    </span>
                @endif

                @if($order->shipment)
                    <span class="rounded-full px-4 py-2 text-xs font-bold uppercase tracking-wider {{ $order->shipment->status_color }}">
                        {{ $order->shipment->status_text }}
                    </span>
                @endif

                @if($order->canBeCancelledByCustomer())
                    <form action="{{ route('profile.orders.cancel', $order) }}" method="POST" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?')">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="inline-flex items-center justify-center rounded-2xl border border-red-200 bg-white px-4 py-2 text-sm font-bold text-red-600 transition hover:bg-red-50">
                            Hủy đơn
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_360px]">
            <div class="space-y-6">
                <div class="rounded-3xl border border-slate-200 bg-white shadow-sm">
                    <div class="border-b border-slate-100 px-6 py-5">
                        <h2 class="text-lg font-bold text-slate-900">Sản phẩm trong đơn</h2>
                    </div>

                    <div class="divide-y divide-slate-100">
                        @foreach($order->items as $item)
                            @php
                                $product = $item->product;
                                $image = $item->variant?->image ?: $product?->image;
                                $variantValues = is_array($item->variant_values) ? $item->variant_values : [];
                                $variantText = collect($variantValues)->map(fn ($value, $name) => $name . ': ' . $value)->implode(' | ');
                            @endphp

                            <div class="flex flex-col gap-4 px-6 py-5 md:flex-row md:items-center md:justify-between">
                                <div class="flex min-w-0 items-center gap-4">
                                    <div class="h-20 w-20 overflow-hidden rounded-2xl bg-slate-100">
                                        <img
                                            src="{{ $image ? asset('storage/' . $image) : asset('images/default-product.png') }}"
                                            alt="{{ $product?->name ?? 'Sản phẩm' }}"
                                            class="h-full w-full object-cover"
                                        >
                                    </div>

                                    <div class="min-w-0">
                                        <div class="text-base font-bold text-slate-900">
                                            @if($product)
                                                <a href="{{ route('product.detail', $product->id) }}" class="transition hover:text-emerald-600">
                                                    {{ $product->name }}
                                                </a>
                                            @else
                                                Sản phẩm không còn hiển thị
                                            @endif
                                        </div>

                                        @if($item->variant_sku || $variantText)
                                            <div class="mt-2 text-sm text-slate-500">
                                                @if($item->variant_sku)
                                                    <span class="font-mono">SKU: {{ $item->variant_sku }}</span>
                                                @endif
                                                @if($variantText)
                                                    <span class="{{ $item->variant_sku ? 'ml-2' : '' }}">{{ $variantText }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <div class="grid grid-cols-3 gap-4 text-sm md:min-w-[280px]">
                                    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-center">
                                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Số lượng</div>
                                        <div class="mt-1 text-base font-bold text-slate-900">{{ $item->quantity }}</div>
                                    </div>

                                    <div class="rounded-2xl bg-slate-50 px-4 py-3 text-center">
                                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Đơn giá</div>
                                        <div class="mt-1 text-base font-bold text-slate-900">{{ number_format($item->price) }}đ</div>
                                    </div>

                                    <div class="rounded-2xl bg-emerald-50 px-4 py-3 text-center">
                                        <div class="text-xs font-semibold uppercase tracking-wider text-emerald-600">Thành tiền</div>
                                        <div class="mt-1 text-base font-bold text-emerald-700">{{ number_format($item->price * $item->quantity) }}đ</div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-bold text-slate-900">Thông tin nhận hàng</h2>

                        <div class="mt-5 space-y-4 text-sm text-slate-600">
                            <div>
                                <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Người nhận</div>
                                <div class="mt-1 font-bold text-slate-900">{{ $order->full_name }}</div>
                            </div>

                            <div>
                                <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Số điện thoại</div>
                                <div class="mt-1 font-bold text-slate-900">{{ $order->phone }}</div>
                            </div>

                            <div>
                                <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Email</div>
                                <div class="mt-1 font-bold text-slate-900">{{ $order->email ?: 'Không có' }}</div>
                            </div>

                            <div>
                                <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Địa chỉ giao hàng</div>
                                <div class="mt-1 whitespace-pre-line font-bold text-slate-900">{{ $order->address }}</div>
                            </div>

                            @if($order->note)
                                <div>
                                    <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Ghi chú</div>
                                    <div class="mt-1 rounded-2xl bg-slate-50 px-4 py-3 leading-6 text-slate-700">{{ $order->note }}</div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                        <h2 class="text-lg font-bold text-slate-900">Thanh toán và giao hàng</h2>

                        <div class="mt-5 space-y-5 text-sm text-slate-600">
                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Payment</div>
                                        <div class="mt-1 text-base font-bold text-slate-900">{{ $order->payment?->method_text ?? strtoupper($order->payment_method) }}</div>
                                    </div>
                                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $order->payment?->status_color ?? 'bg-slate-200 text-slate-700' }}">
                                        {{ $order->payment?->status_text ?? 'Chưa có payment' }}
                                    </span>
                                </div>

                                @if($order->payment)
                                    <div class="mt-4 space-y-2">
                                        <div class="flex justify-between gap-4">
                                            <span>Số tiền</span>
                                            <span class="font-bold text-slate-900">{{ number_format($order->payment->amount) }}đ</span>
                                        </div>
                                        <div class="flex justify-between gap-4">
                                            <span>Mã giao dịch</span>
                                            <span class="font-bold text-slate-900">{{ $order->payment->transaction_code ?: 'Chưa có' }}</span>
                                        </div>
                                        <div class="flex justify-between gap-4">
                                            <span>Thanh toán lúc</span>
                                            <span class="font-bold text-slate-900">{{ $order->payment->paid_at?->format('d/m/Y H:i') ?? 'Chưa thanh toán' }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <div class="rounded-2xl border border-slate-100 bg-slate-50 p-4">
                                <div class="flex items-start justify-between gap-4">
                                    <div>
                                        <div class="text-xs font-semibold uppercase tracking-wider text-slate-400">Shipment</div>
                                        <div class="mt-1 text-base font-bold text-slate-900">{{ $order->shipment?->method_text ?? 'Đang cập nhật' }}</div>
                                    </div>
                                    <span class="rounded-full px-3 py-1 text-xs font-bold {{ $order->shipment?->status_color ?? 'bg-slate-200 text-slate-700' }}">
                                        {{ $order->shipment?->status_text ?? 'Chưa có shipment' }}
                                    </span>
                                </div>

                                @if($order->shipment)
                                    <div class="mt-4 space-y-2">
                                        <div class="flex justify-between gap-4">
                                            <span>Đơn vị giao</span>
                                            <span class="font-bold text-slate-900">{{ $order->shipment->carrier ?: 'Chưa cập nhật' }}</span>
                                        </div>
                                        <div class="flex justify-between gap-4">
                                            <span>Phí ship</span>
                                            <span class="font-bold text-slate-900">{{ number_format($order->shipment->fee_amount) }}đ</span>
                                        </div>
                                        <div class="flex justify-between gap-4">
                                            <span>Dự kiến giao</span>
                                            <span class="font-bold text-slate-900">{{ $order->shipment->estimated_delivery_at?->format('d/m/Y') ?? 'Đang cập nhật' }}</span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="space-y-6">
                <div class="rounded-3xl bg-slate-900 p-6 text-white shadow-xl shadow-slate-200">
                    <h2 class="text-lg font-bold">Tóm tắt đơn hàng</h2>

                    <div class="mt-5 space-y-3 text-sm text-slate-300">
                        <div class="flex justify-between gap-4">
                            <span>Tạm tính</span>
                            <span>{{ number_format($order->subtotal_amount ?? $order->total_amount) }}đ</span>
                        </div>

                        @if(($order->discount_amount ?? 0) > 0)
                            <div class="flex justify-between gap-4">
                                <span>Giảm giá{{ $order->coupon_code ? ' (' . $order->coupon_code . ')' : '' }}</span>
                                <span>-{{ number_format($order->discount_amount) }}đ</span>
                            </div>
                        @endif

                        <div class="flex justify-between gap-4">
                            <span>Tiền hàng sau giảm</span>
                            <span>{{ number_format($order->total_amount) }}đ</span>
                        </div>

                        <div class="flex justify-between gap-4">
                            <span>Phí vận chuyển</span>
                            <span>{{ number_format($order->shipping_fee_amount ?? 0) }}đ</span>
                        </div>
                    </div>

                    <div class="mt-5 border-t border-slate-700 pt-5">
                        <div class="flex items-center justify-between gap-4">
                            <span class="text-sm font-semibold uppercase tracking-wider text-slate-400">Tổng thanh toán</span>
                            <span class="text-2xl font-extrabold text-emerald-400">{{ number_format($order->payable_total) }}đ</span>
                        </div>
                    </div>
                </div>

                <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                    <h2 class="text-lg font-bold text-slate-900">Cần hỗ trợ?</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-500">
                        Bạn có thể nhắn trực tiếp với shop để hỏi thêm về đơn hàng, giao hàng hoặc sản phẩm đã đặt.
                    </p>

                    <a href="{{ route('chat.index') }}" class="mt-5 inline-flex items-center justify-center rounded-2xl bg-emerald-600 px-5 py-3 text-sm font-bold text-white transition hover:bg-emerald-700">
                        Mở chat hỗ trợ
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
