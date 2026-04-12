@extends('admin.layouts.master')

@section('title', 'Tổng quan')

@php
    $formatCurrency = fn ($amount) => number_format((float) $amount, 0, '.', ',');
    $formatShortCurrency = fn ($amount) => number_format((float) $amount, 0, '.', ',');
    $growthBadge = function (array $growth, string $positiveClass, string $negativeClass) {
        return ($growth['is_positive'] ?? false) ? $positiveClass : $negativeClass;
    };
@endphp

@section('content')
<div class="mb-8">
    <h1 class="text-2xl font-bold text-slate-800">Tổng quan hệ thống</h1>
    <p class="text-slate-500 mt-1">Theo dõi nhanh tình hình người dùng, đơn hàng, doanh thu và lợi nhuận.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-users text-emerald-600 text-xl"></i>
            </div>
            <span class="text-xs font-medium px-2 py-1 rounded-full {{ $growthBadge($stats['users_growth'], 'text-emerald-600 bg-emerald-50', 'text-rose-600 bg-rose-50') }}">
                {{ ($stats['users_growth']['is_positive'] ?? false) ? '+' : '' }}{{ number_format((float) ($stats['users_growth']['percent'] ?? 0), 1) }}%
            </span>
        </div>
        <h3 class="text-sm font-medium text-slate-500 mb-1">Tổng người dùng</h3>
        <div class="flex items-baseline justify-between">
            <span class="text-2xl font-bold text-slate-800">{{ number_format((int) ($stats['total_users'] ?? 0), 0, ',', '.') }}</span>
            <span class="text-xs text-slate-400">người</span>
        </div>
        <div class="mt-3 pt-3 border-t border-slate-100">
            <div class="flex items-center text-xs">
                <span class="font-medium {{ ($stats['users_growth']['is_positive'] ?? false) ? 'text-emerald-500' : 'text-rose-500' }}">
                    {{ ($stats['users_growth']['is_positive'] ?? false) ? '+' : '' }}{{ number_format((float) ($stats['users_growth']['delta'] ?? 0), 0, ',', '.') }}
                </span>
                <span class="text-slate-400 ml-1">so với tháng trước</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-shopping-bag text-blue-600 text-xl"></i>
            </div>
            <span class="text-xs font-medium px-2 py-1 rounded-full {{ $growthBadge($stats['orders_growth'], 'text-blue-600 bg-blue-50', 'text-rose-600 bg-rose-50') }}">
                {{ ($stats['orders_growth']['is_positive'] ?? false) ? '+' : '' }}{{ number_format((float) ($stats['orders_growth']['percent'] ?? 0), 1) }}%
            </span>
        </div>
        <h3 class="text-sm font-medium text-slate-500 mb-1">Đơn hàng hôm nay</h3>
        <div class="flex items-baseline justify-between">
            <span class="text-2xl font-bold text-slate-800">{{ number_format((int) ($stats['orders_today'] ?? 0), 0, ',', '.') }}</span>
            <span class="text-xs text-slate-400">đơn</span>
        </div>
        <div class="mt-3 pt-3 border-t border-slate-100">
            <div class="flex items-center text-xs">
                <span class="font-medium {{ ($stats['orders_growth']['is_positive'] ?? false) ? 'text-blue-500' : 'text-rose-500' }}">
                    {{ ($stats['orders_growth']['is_positive'] ?? false) ? '+' : '' }}{{ number_format((float) ($stats['orders_growth']['delta'] ?? 0), 0, ',', '.') }}
                </span>
                <span class="text-slate-400 ml-1">so với hôm qua</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-amber-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-dollar-sign text-amber-600 text-xl"></i>
            </div>
            <span class="text-xs font-medium px-2 py-1 rounded-full {{ $growthBadge($stats['revenue_growth'], 'text-amber-600 bg-amber-50', 'text-rose-600 bg-rose-50') }}">
                {{ ($stats['revenue_growth']['is_positive'] ?? false) ? '+' : '' }}{{ number_format((float) ($stats['revenue_growth']['percent'] ?? 0), 1) }}%
            </span>
        </div>
        <h3 class="text-sm font-medium text-slate-500 mb-1">Doanh thu tháng</h3>
        <div class="flex items-baseline justify-between">
            <span class="text-2xl font-bold text-slate-800">{{ $formatShortCurrency($stats['monthly_revenue'] ?? 0) }}</span>
            <span class="text-xs text-slate-400">VNĐ</span>
        </div>
        <div class="mt-3 pt-3 border-t border-slate-100">
            <div class="flex items-center text-xs">
                <span class="font-medium {{ ($stats['revenue_growth']['is_positive'] ?? false) ? 'text-amber-500' : 'text-rose-500' }}">
                    {{ ($stats['revenue_growth']['is_positive'] ?? false) ? '+' : '' }}{{ $formatShortCurrency($stats['revenue_growth']['delta'] ?? 0) }}
                </span>
                <span class="text-slate-400 ml-1">so với tháng trước</span>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6 hover:shadow-md transition-shadow">
        <div class="flex items-center justify-between mb-4">
            <div class="w-12 h-12 bg-rose-100 rounded-xl flex items-center justify-center">
                <i class="fas fa-chart-line text-rose-600 text-xl"></i>
            </div>
            <span class="text-xs font-medium px-2 py-1 rounded-full {{ $growthBadge($stats['profit_growth'], 'text-rose-600 bg-rose-50', 'text-slate-600 bg-slate-100') }}">
                {{ ($stats['profit_growth']['is_positive'] ?? false) ? '+' : '' }}{{ number_format((float) ($stats['profit_growth']['percent'] ?? 0), 1) }}%
            </span>
        </div>
        <h3 class="text-sm font-medium text-slate-500 mb-1">Lợi nhuận tháng</h3>
        <div class="flex items-baseline justify-between">
            <span class="text-2xl font-bold text-slate-800">{{ $formatShortCurrency($stats['monthly_profit'] ?? 0) }}</span>
            <span class="text-xs text-slate-400">VNĐ</span>
        </div>
        <div class="mt-3 pt-3 border-t border-slate-100">
            <div class="flex items-center text-xs">
                <span class="font-medium {{ ($stats['profit_growth']['is_positive'] ?? false) ? 'text-rose-500' : 'text-slate-500' }}">
                    {{ ($stats['profit_growth']['is_positive'] ?? false) ? '+' : '' }}{{ $formatShortCurrency($stats['profit_growth']['delta'] ?? 0) }}
                </span>
                <span class="text-slate-400 ml-1">so với tháng trước</span>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h3 class="font-semibold text-slate-800">Biểu đồ doanh thu</h3>
                <p class="text-xs text-slate-500 mt-1">7 ngày gần nhất</p>
            </div>
            <span class="text-sm border border-slate-200 rounded-lg px-3 py-2 bg-slate-50 text-slate-500">7 ngày</span>
        </div>

        <div class="h-64 flex items-end justify-between gap-2">
            @foreach($revenueSeries as $day)
                <div class="flex-1 flex flex-col items-center gap-2">
                    <div class="w-full bg-emerald-50 rounded-lg relative group cursor-pointer">
                        <div class="bg-emerald-200 rounded-lg" style="height: {{ $day['height'] }}px"></div>
                        <div class="absolute bottom-full left-1/2 -translate-x-1/2 mb-2 hidden group-hover:block bg-slate-800 text-white text-xs rounded px-2 py-1 whitespace-nowrap">
                            {{ $formatCurrency($day['revenue']) }} VNĐ
                        </div>
                    </div>
                    <span class="text-xs text-slate-500">{{ $day['label'] }}</span>
                </div>
            @endforeach
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 p-6">
        <h3 class="font-semibold text-slate-800 mb-4">Sản phẩm bán chạy</h3>
        <div class="space-y-4">
            @forelse($topProducts as $item)
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-emerald-100 rounded-lg flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-apple-alt text-emerald-600 text-sm"></i>
                    </div>
                    <div class="flex-1">
                        <p class="text-sm font-medium text-slate-800">{{ $item['product']->name }}</p>
                        <div class="flex items-center gap-2 mt-1">
                            <span class="text-xs text-slate-500">{{ number_format($item['sold_quantity'], 0, ',', '.') }} đơn</span>
                            <span class="text-xs text-slate-300">•</span>
                            <span class="text-xs font-medium text-emerald-600">{{ $formatCurrency($item['revenue_amount']) }}</span>
                        </div>
                    </div>
                    <div class="text-xs text-slate-400 bg-slate-50 px-2 py-1 rounded-full">
                        {{ $loop->iteration }}
                    </div>
                </div>
            @empty
                <p class="text-sm text-slate-400 italic">Chưa có dữ liệu bán hàng.</p>
            @endforelse
        </div>

        <div class="mt-4 pt-4 border-t border-slate-100">
            <a href="{{ route('admin.products.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700 flex items-center justify-center gap-1">
                Xem tất cả sản phẩm
                <i class="fas fa-arrow-right text-xs"></i>
            </a>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-8">
    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-slate-800">Thành viên mới</h3>
                    <p class="text-xs text-slate-500 mt-1">Đăng ký gần đây nhất</p>
                </div>
                <a href="{{ route('admin.users.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700 font-medium">
                    Xem tất cả
                </a>
            </div>
        </div>

        <div class="divide-y divide-slate-100">
            @forelse($latestUsers as $user)
                <div class="p-4 hover:bg-slate-50 transition-colors">
                    <div class="flex items-center gap-3">
                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=10b981&color=fff" class="w-10 h-10 rounded-lg shadow-sm" alt="{{ $user->name }}">
                        <div class="flex-1">
                            <p class="font-medium text-slate-800 text-sm">{{ $user->name }}</p>
                            <p class="text-[11px] text-slate-500 mt-0.5 line-clamp-1">{{ $user->email }}</p>
                        </div>
                        <div class="text-right">
                            <span class="text-[10px] font-medium text-slate-400">{{ $user->created_at->diffForHumans() }}</span>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center">
                    <p class="text-sm text-slate-400 italic">Chưa có thành viên nào.</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-slate-800">Đơn hàng gần đây</h3>
                    <p class="text-xs text-slate-500 mt-1">5 đơn hàng mới nhất</p>
                </div>
                <a href="{{ route('admin.orders.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700">
                    Xem tất cả
                </a>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[520px]">
                <thead class="bg-slate-50 text-xs text-slate-500">
                    <tr>
                        <th class="px-4 py-3 text-left">Mã đơn</th>
                        <th class="px-4 py-3 text-left">Khách hàng</th>
                        <th class="px-4 py-3 text-left">Tổng tiền</th>
                        <th class="px-4 py-3 text-left">Trạng thái</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($latestOrders as $order)
                        @php
                            $statusClass = [
                                'completed' => 'bg-emerald-100 text-emerald-600',
                                'processing' => 'bg-blue-100 text-blue-600',
                                'shipping' => 'bg-sky-100 text-sky-600',
                                'pending' => 'bg-amber-100 text-amber-600',
                                'cancelled' => 'bg-rose-100 text-rose-600',
                            ][$order->status] ?? 'bg-slate-100 text-slate-600';

                            $statusText = [
                                'completed' => 'Hoàn thành',
                                'processing' => 'Đang xử lý',
                                'shipping' => 'Đang giao',
                                'pending' => 'Chờ xác nhận',
                                'cancelled' => 'Đã hủy',
                            ][$order->status] ?? $order->status;
                        @endphp
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-4 py-3">
                                <span class="font-medium text-slate-800">{{ $order->order_number }}</span>
                            </td>
                            <td class="px-4 py-3 text-sm text-slate-600">{{ $order->full_name }}</td>
                            <td class="px-4 py-3 text-sm font-medium text-slate-800">{{ $formatCurrency($order->payable_total) }}</td>
                            <td class="px-4 py-3">
                                <span class="text-xs px-2 py-1 rounded-full whitespace-nowrap {{ $statusClass }}">
                                    {{ $statusText }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-8 text-center text-sm text-slate-400 italic">Chưa có đơn hàng nào.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-4">
    <a href="{{ route('admin.products.create') }}" class="bg-white rounded-xl border border-slate-100 p-4 hover:shadow-md transition-shadow flex items-center gap-4">
        <div class="w-10 h-10 bg-emerald-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-plus text-emerald-600"></i>
        </div>
        <div>
            <h4 class="font-medium text-slate-800">Thêm sản phẩm mới</h4>
            <p class="text-xs text-slate-500">Cập nhật danh mục nông sản</p>
        </div>
    </a>

    <a href="{{ route('admin.orders.index') }}" class="bg-white rounded-xl border border-slate-100 p-4 hover:shadow-md transition-shadow flex items-center gap-4">
        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-truck text-blue-600"></i>
        </div>
        <div>
            <h4 class="font-medium text-slate-800">Quản lý vận chuyển</h4>
            <p class="text-xs text-slate-500">Cập nhật trạng thái đơn hàng</p>
        </div>
    </a>

    <a href="{{ route('admin.orders.index') }}" class="bg-white rounded-xl border border-slate-100 p-4 hover:shadow-md transition-shadow flex items-center gap-4">
        <div class="w-10 h-10 bg-amber-100 rounded-lg flex items-center justify-center">
            <i class="fas fa-chart-line text-amber-600"></i>
        </div>
        <div>
            <h4 class="font-medium text-slate-800">Báo cáo doanh thu</h4>
            <p class="text-xs text-slate-500">Xem dữ liệu đơn hàng và lợi nhuận</p>
        </div>
    </a>
</div>
@endsection
