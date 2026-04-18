@extends('admin.layouts.master')

@section('title', 'Bảng điều khiển')

@php
    $formatNumber = fn ($amount) => number_format((float) $amount, 0, '.', ',');
    $formatCurrency = fn ($amount) => $formatNumber($amount) . 'đ';
    $growthTone = function (array $growth) {
        return ($growth['is_positive'] ?? false)
            ? 'admin-badge admin-badge--success'
            : 'admin-badge admin-badge--danger';
    };
    $statusMap = [
        'pending' => ['Chờ xác nhận', 'admin-badge admin-badge--warning'],
        'processing' => ['Đang xử lý', 'admin-badge admin-badge--info'],
        'shipping' => ['Đang giao', 'admin-badge admin-badge--info'],
        'completed' => ['Hoàn thành', 'admin-badge admin-badge--success'],
        'cancelled' => ['Đã hủy', 'admin-badge admin-badge--danger'],
    ];
@endphp

@section('content')
    <div class="mx-auto max-w-7xl space-y-8">
        <section class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="admin-kicker">Dashboard vận hành</p>
                <h1 class="admin-headline mt-2 text-4xl font-bold tracking-[-0.05em] text-[var(--admin-text)]">Tổng quan hệ thống</h1>
                <x-admin-info class="mt-3">
                    Theo dõi người dùng, đơn hàng, doanh thu, lợi nhuận và tín hiệu vận hành quan trọng trong một màn hình tổng hợp.
                </x-admin-info>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <div class="admin-panel-muted flex items-center gap-3 px-4 py-3 text-sm text-[var(--admin-text-muted)]">
                    <i class="fas fa-calendar-days text-[#206223]"></i>
                    <span>{{ now()->format('d/m/Y') }}</span>
                </div>
                <a href="{{ route('admin.products.create') }}" class="admin-btn-primary">
                    <i class="fas fa-plus text-sm"></i>
                    Thêm sản phẩm
                </a>
            </div>
        </section>

        <section class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
            <article class="admin-surface-card admin-card-accent p-6">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[rgba(32,98,35,0.1)] text-[#206223]">
                        <i class="fas fa-user-plus"></i>
                    </div>
                    <span class="{{ $growthTone($stats['users_growth']) }}">
                        {{ ($stats['users_growth']['is_positive'] ?? false) ? '+' : '' }}{{ number_format((float) ($stats['users_growth']['percent'] ?? 0), 1, ',', '.') }}%
                    </span>
                </div>
                <p class="admin-kicker mt-5">Người dùng mới</p>
                <p class="admin-headline mt-2 text-4xl font-bold tracking-[-0.05em]">{{ $formatNumber((int) ($stats['total_users'] ?? 0)) }}</p>
                <p class="mt-3 text-sm text-[var(--admin-text-muted)]">
                    Biến động tháng:
                    <span class="font-semibold text-[var(--admin-text)]">{{ $formatNumber((float) ($stats['users_growth']['delta'] ?? 0)) }}</span>
                </p>
            </article>

            <article class="admin-surface-card admin-card-accent p-6">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[rgba(32,98,35,0.1)] text-[#206223]">
                        <i class="fas fa-bag-shopping"></i>
                    </div>
                    <span class="{{ $growthTone($stats['orders_growth']) }}">
                        {{ ($stats['orders_growth']['is_positive'] ?? false) ? '+' : '' }}{{ number_format((float) ($stats['orders_growth']['percent'] ?? 0), 1, ',', '.') }}%
                    </span>
                </div>
                <p class="admin-kicker mt-5">Đơn hàng hôm nay</p>
                <p class="admin-headline mt-2 text-4xl font-bold tracking-[-0.05em]">{{ $formatNumber((int) ($stats['orders_today'] ?? 0)) }}</p>
                <p class="mt-3 text-sm text-[var(--admin-text-muted)]">
                    So với hôm qua:
                    <span class="font-semibold text-[var(--admin-text)]">{{ $formatNumber((float) ($stats['orders_growth']['delta'] ?? 0)) }}</span>
                </p>
            </article>

            <article class="admin-surface-card admin-card-accent p-6">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[rgba(32,98,35,0.1)] text-[#206223]">
                        <i class="fas fa-coins"></i>
                    </div>
                    <span class="{{ $growthTone($stats['revenue_growth']) }}">
                        {{ ($stats['revenue_growth']['is_positive'] ?? false) ? '+' : '' }}{{ number_format((float) ($stats['revenue_growth']['percent'] ?? 0), 1, ',', '.') }}%
                    </span>
                </div>
                <p class="admin-kicker mt-5">Doanh thu tháng</p>
                <p class="admin-headline mt-2 text-4xl font-bold tracking-[-0.05em]">{{ $formatNumber((float) ($stats['monthly_revenue'] ?? 0)) }}</p>
                <p class="mt-3 text-sm text-[var(--admin-text-muted)]">{{ $formatCurrency($stats['revenue_growth']['delta'] ?? 0) }} so với tháng trước</p>
            </article>

            <article class="admin-surface-card p-6">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[rgba(186,26,26,0.09)] text-[var(--admin-danger-text)]">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <span class="{{ $growthTone($stats['profit_growth']) }}">
                        {{ ($stats['profit_growth']['is_positive'] ?? false) ? '+' : '' }}{{ number_format((float) ($stats['profit_growth']['percent'] ?? 0), 1, ',', '.') }}%
                    </span>
                </div>
                <p class="admin-kicker mt-5">Lợi nhuận tháng</p>
                <p class="admin-headline mt-2 text-4xl font-bold tracking-[-0.05em]">{{ $formatNumber((float) ($stats['monthly_profit'] ?? 0)) }}</p>
                <p class="mt-3 text-sm text-[var(--admin-text-muted)]">{{ $formatCurrency($stats['profit_growth']['delta'] ?? 0) }} so với tháng trước</p>
            </article>
        </section>

        <section class="grid gap-5 xl:grid-cols-[1.8fr_1fr]">
            <article class="admin-surface-card p-7">
                <div class="mb-8 flex items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="admin-headline text-2xl font-bold tracking-[-0.03em]">Doanh thu 7 ngày gần nhất</h3>
                            <x-admin-info>
                                Thống kê doanh thu đã hoàn tất theo từng ngày để theo dõi nhịp tăng trưởng ngắn hạn.
                            </x-admin-info>
                        </div>
                    </div>
                    <span class="admin-badge admin-badge--muted">7 ngày</span>
                </div>

                <div class="flex h-72 items-end gap-3">
                    @foreach($revenueSeries as $day)
                        <div class="flex flex-1 flex-col items-center gap-3">
                            <div class="group relative flex w-full items-end rounded-t-2xl bg-[var(--admin-surface-high)]" style="height: 220px;">
                                <div class="w-full rounded-t-2xl bg-[linear-gradient(180deg,#3a7b3a,#206223)] transition duration-200 group-hover:opacity-90" style="height: {{ $day['height'] }}px;"></div>
                                <div class="pointer-events-none absolute -top-10 left-1/2 hidden -translate-x-1/2 rounded-xl bg-[var(--admin-text)] px-3 py-2 text-[11px] font-semibold text-white group-hover:block">
                                    {{ $formatCurrency($day['revenue']) }}
                                </div>
                            </div>
                            <div class="text-center">
                                <p class="text-xs font-bold uppercase tracking-[0.16em] text-[rgba(95,103,92,0.8)]">{{ $day['day_label'] }}</p>
                                <p class="mt-1 text-xs text-[var(--admin-text-muted)]">{{ $day['label'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </article>

            <article class="admin-surface-card p-7">
                <div class="mb-6 flex items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="admin-headline text-2xl font-bold tracking-[-0.03em]">Top sản phẩm bán chạy</h3>
                            <x-admin-info>
                                Nhóm sản phẩm có sản lượng hoàn tất cao nhất trong kỳ gần nhất.
                            </x-admin-info>
                        </div>
                    </div>
                    <a href="{{ route('admin.products.index') }}" class="text-sm font-semibold text-[#206223] hover:underline">Xem tất cả</a>
                </div>

                <div class="space-y-4">
                    @forelse($topProducts as $item)
                        <div class="flex items-center gap-4 rounded-2xl bg-[var(--admin-surface-low)] px-4 py-4">
                            <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-[rgba(32,98,35,0.1)] text-[#206223]">
                                <i class="fas fa-seedling"></i>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-bold text-[var(--admin-text)]">{{ $item['product']->name }}</p>
                                <p class="mt-1 text-xs text-[var(--admin-text-muted)]">{{ number_format($item['sold_quantity'], 0, ',', '.') }} lượt bán</p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-[#206223]">{{ $formatCurrency($item['revenue_amount']) }}</p>
                                <p class="mt-1 text-[11px] text-[var(--admin-text-muted)]">Lợi nhuận {{ $formatCurrency($item['profit_amount']) }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="admin-empty-state min-h-[14rem] rounded-2xl bg-[var(--admin-surface-low)]">
                            <i class="fas fa-box-open text-3xl opacity-30"></i>
                            <p class="text-sm">Chưa có dữ liệu bán hàng để thống kê.</p>
                        </div>
                    @endforelse
                </div>
            </article>
        </section>

        <section class="grid gap-5 xl:grid-cols-[1.1fr_1.3fr]">
            <article class="admin-surface-card p-7">
                <div class="mb-6 flex items-start justify-between gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="admin-headline text-2xl font-bold tracking-[-0.03em]">Thành viên mới</h3>
                            <x-admin-info>
                                Những tài khoản người dùng mới đăng ký gần đây trên hệ thống.
                            </x-admin-info>
                        </div>
                    </div>
                    <a href="{{ route('admin.users.index') }}" class="text-sm font-semibold text-[#206223] hover:underline">Xem tất cả</a>
                </div>

                <div class="space-y-3">
                    @forelse($latestUsers as $user)
                        <div class="flex items-center gap-4 rounded-2xl bg-[var(--admin-surface-low)] px-4 py-4">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=206223&color=fff" alt="{{ $user->name }}" class="h-11 w-11 rounded-2xl object-cover">
                            <div class="min-w-0 flex-1">
                                <p class="truncate text-sm font-bold text-[var(--admin-text)]">{{ $user->name }}</p>
                                <p class="truncate text-xs text-[var(--admin-text-muted)]">{{ $user->email }}</p>
                            </div>
                            <div class="text-right text-[11px] font-semibold uppercase tracking-[0.14em] text-[var(--admin-text-muted)]">
                                {{ $user->created_at->diffForHumans() }}
                            </div>
                        </div>
                    @empty
                        <div class="admin-empty-state min-h-[12rem] rounded-2xl bg-[var(--admin-surface-low)]">
                            <i class="fas fa-user-group text-3xl opacity-30"></i>
                            <p class="text-sm">Chưa có thành viên mới.</p>
                        </div>
                    @endforelse
                </div>
            </article>

            <article class="admin-table-shell">
                <div class="flex items-start justify-between gap-3 px-7 py-6">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="admin-headline text-2xl font-bold tracking-[-0.03em]">Đơn hàng gần đây</h3>
                            <x-admin-info>
                                5 đơn mới nhất với thông tin thanh toán và trạng thái xử lý hiện tại.
                            </x-admin-info>
                        </div>
                    </div>
                    <a href="{{ route('admin.orders.index') }}" class="text-sm font-semibold text-[#206223] hover:underline">Quản lý đơn</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="min-w-[760px]">
                        <thead>
                            <tr>
                                <th class="px-7 py-4 text-left">Mã đơn</th>
                                <th class="px-5 py-4 text-left">Khách hàng</th>
                                <th class="px-5 py-4 text-left">Tổng tiền</th>
                                <th class="px-5 py-4 text-left">Trạng thái</th>
                                <th class="px-7 py-4 text-left">Ngày đặt</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($latestOrders as $order)
                                @php([$statusLabel, $statusClass] = $statusMap[$order->status] ?? [$order->status, 'admin-badge admin-badge--muted'])
                                <tr>
                                    <td class="px-7 py-5 text-sm font-bold text-[#206223]">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="hover:underline">{{ $order->order_number }}</a>
                                    </td>
                                    <td class="px-5 py-5">
                                        <p class="text-sm font-semibold text-[var(--admin-text)]">{{ $order->full_name }}</p>
                                        <p class="mt-1 text-xs text-[var(--admin-text-muted)]">{{ $order->phone }}</p>
                                    </td>
                                    <td class="px-5 py-5 text-sm font-bold text-[var(--admin-text)]">{{ $formatCurrency($order->payable_total) }}</td>
                                    <td class="px-5 py-5"><span class="{{ $statusClass }}">{{ $statusLabel }}</span></td>
                                    <td class="px-7 py-5 text-sm text-[var(--admin-text-muted)]">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-7 py-16">
                                        <div class="admin-empty-state">
                                            <i class="fas fa-receipt text-3xl opacity-30"></i>
                                            <p class="text-sm">Chưa có đơn hàng nào.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </article>
        </section>
    </div>
@endsection
