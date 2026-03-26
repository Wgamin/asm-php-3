@extends('admin.layouts.master')

@section('content')
<div class="p-6">
    <div class="flex justify-between items-center mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Quản lý đơn hàng</h2>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <table class="w-full text-left border-collapse">
            <thead class="bg-gray-50 text-gray-500 uppercase text-xs font-bold">
                <tr>
                    <th class="px-6 py-4">Mã đơn</th>
                    <th class="px-6 py-4">Khách hàng</th>
                    <th class="px-6 py-4">Tổng tiền</th>
                    <th class="px-6 py-4">Trạng thái</th>
                    <th class="px-6 py-4">Ngày đặt</th>
                    <th class="px-6 py-4 text-right">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @foreach($orders as $order)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-bold text-emerald-600">{{ $order->order_number }}</td>
                    <td class="px-6 py-4">
                        <div class="text-sm font-medium text-gray-900">{{ $order->full_name }}</div>
                        <div class="text-xs text-gray-500">{{ $order->phone }}</div>
                    </td>
                    <td class="px-6 py-4 font-bold">{{ number_format($order->total_amount) }}đ</td>
                    <td class="px-6 py-4">
                        @php
                            $statusColors = [
                                'pending' => 'bg-amber-100 text-amber-600',
                                'processing' => 'bg-blue-100 text-blue-600',
                                'shipping' => 'bg-indigo-100 text-indigo-600',
                                'completed' => 'bg-emerald-100 text-emerald-600',
                                'cancelled' => 'bg-red-100 text-red-600',
                            ];
                        @endphp
                        <span class="px-3 py-1 rounded-full text-xs font-bold {{ $statusColors[$order->status] }}">
                            {{ strtoupper($order->status_text) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                    <td class="px-6 py-4 text-right">
                        <a href="{{ route('admin.orders.show', $order->id) }}" class="text-gray-400 hover:text-emerald-600 transition">
                            <i class="fas fa-eye"></i> Chi tiết
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="mt-6">
        {{ $orders->links() }}
    </div>
</div>
@endsection