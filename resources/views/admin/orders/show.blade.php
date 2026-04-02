@extends('admin.layouts.master')

@section('content')
<div class="p-6">
    <div class="mb-6 flex items-center justify-between">
        <h2 class="text-2xl font-bold text-gray-800">Chi tiết đơn hàng: <span class="text-emerald-600">{{ $order->order_number }}</span></h2>
        <a href="{{ route('admin.orders.index') }}" class="text-gray-500 hover:text-gray-700 font-medium">
            <i class="fas fa-arrow-left mr-2"></i> Quay lại danh sách
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold mb-4 flex items-center gap-2">
                    <i class="fas fa-user text-emerald-500"></i> Thông tin giao hàng
                </h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div>
                        <p class="text-gray-400">Người nhận:</p>
                        <p class="font-bold text-gray-800">{{ $order->full_name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-400">Số điện thoại:</p>
                        <p class="font-bold text-gray-800">{{ $order->phone }}</p>
                    </div>
                    <div class="col-span-2">
                        <p class="text-gray-400">Địa chỉ:</p>
                        <p class="font-bold text-gray-800">{{ $order->address }}</p>
                    </div>
                    @if($order->note)
                    <div class="col-span-2">
                        <p class="text-gray-400">Ghi chú:</p>
                        <p class="font-bold text-gray-800">{{ $order->note }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <table class="w-full text-left">
                    <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                        <tr>
                            <th class="px-6 py-4">Sản phẩm</th>
                            <th class="px-6 py-4 text-center">Số lượng</th>
                            <th class="px-6 py-4 text-right">Đơn giá</th>
                            <th class="px-6 py-4 text-right">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach($order->items as $item)
                        <tr>
                            <td class="px-6 py-4 flex items-center gap-3">
                                <img src="{{ asset('storage/' . $item->product->image) }}" class="w-12 h-12 rounded-lg object-cover">
                                <span class="font-medium">{{ $item->product->name }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">{{ $item->quantity }}</td>
                            <td class="px-6 py-4 text-right">{{ number_format($item->price) }}đ</td>
                            <td class="px-6 py-4 text-right font-bold">{{ number_format($item->price * $item->quantity) }}đ</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                <h3 class="text-lg font-bold mb-4">Trạng thái đơn hàng</h3>
                <form action="{{ route('admin.orders.updateStatus', $order->id) }}" method="POST">
                    @csrf
                    <select name="status" class="w-full p-3 bg-gray-50 border-none rounded-xl focus:ring-2 focus:ring-emerald-500 mb-4">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Chờ xử lý</option>
                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Đang đóng gói</option>
                        <option value="shipping" {{ $order->status == 'shipping' ? 'selected' : '' }}>Đang giao hàng</option>
                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Đã hoàn thành</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Đã hủy</option>
                    </select>
                    <button type="submit" class="w-full bg-emerald-600 text-white font-bold py-3 rounded-xl hover:bg-emerald-700 transition">
                        Cập nhật trạng thái
                    </button>
                </form>
            </div>

            <div class="bg-slate-800 text-white p-6 rounded-2xl shadow-xl">
                @php
                    $subtotal = $order->subtotal_amount ?? $order->total_amount;
                @endphp

                <div class="flex justify-between text-slate-400 text-sm mb-2">
                    <span>Tạm tính:</span>
                    <span>{{ number_format($subtotal) }}đ</span>
                </div>

                @if(($order->discount_amount ?? 0) > 0)
                <div class="flex justify-between text-slate-400 text-sm mb-2">
                    <span>Coupon{{ $order->coupon_code ? ' (' . $order->coupon_code . ')' : '' }}:</span>
                    <span>-{{ number_format($order->discount_amount) }}đ</span>
                </div>
                @endif

                <div class="flex justify-between text-slate-400 text-sm mb-4">
                    <span>Phí vận chuyển:</span>
                    <span>0đ</span>
                </div>
                <hr class="border-slate-700 mb-4">
                <div class="flex justify-between text-xl font-bold">
                    <span>TỔNG CỘNG:</span>
                    <span class="text-emerald-400">{{ number_format($order->total_amount) }}đ</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
