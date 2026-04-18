@extends('admin.layouts.master')

@section('title', 'Đơn hàng')

@php
    $statusMap = [
        'pending' => ['Chờ xác nhận', 'admin-badge admin-badge--warning'],
        'processing' => ['Đang xử lý', 'admin-badge admin-badge--info'],
        'shipping' => ['Đang giao', 'admin-badge admin-badge--info'],
        'completed' => ['Hoàn thành', 'admin-badge admin-badge--success'],
        'cancelled' => ['Đã hủy', 'admin-badge admin-badge--danger'],
    ];
    $shipmentMap = [
        'pending' => ['Chờ lấy hàng', 'admin-badge admin-badge--warning'],
        'preparing' => ['Đang chuẩn bị', 'admin-badge admin-badge--info'],
        'shipping' => ['Đang giao', 'admin-badge admin-badge--info'],
        'delivered' => ['Đã giao', 'admin-badge admin-badge--success'],
        'cancelled' => ['Đã hủy', 'admin-badge admin-badge--danger'],
    ];
    $paymentMap = [
        'pending' => ['Chờ thanh toán', 'admin-badge admin-badge--warning'],
        'paid' => ['Đã thanh toán', 'admin-badge admin-badge--success'],
        'failed' => ['Thất bại', 'admin-badge admin-badge--danger'],
        'cancelled' => ['Đã hủy', 'admin-badge admin-badge--danger'],
    ];
@endphp

@section('content')
    <div class="mx-auto max-w-7xl space-y-8">
        <section class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="admin-kicker">Vận hành giao dịch</p>
                <h1 class="admin-headline mt-2 text-4xl font-bold tracking-[-0.05em] text-[var(--admin-text)]">Danh sách đơn hàng</h1>
                <x-admin-info class="mt-3">
                    Theo dõi luồng xử lý đơn, thanh toán và vận chuyển từ một màn hình tập trung có filter theo mã đơn, khách hàng, trạng thái và thời gian đặt.
                </x-admin-info>
            </div>
            <div class="admin-panel-muted flex items-center gap-3 px-4 py-3 text-sm text-[var(--admin-text-muted)]">
                <i class="fas fa-boxes-stacked text-[#206223]"></i>
                <span>Hiển thị {{ $orders->count() }} / {{ $orders->total() }} đơn hàng</span>
            </div>
        </section>

        <section class="admin-panel p-6">
            <form method="GET" action="{{ route('admin.orders.index') }}" class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
                <div>
                    <label class="admin-field-label">Mã đơn</label>
                    <input type="text" name="order_number" value="{{ $filters['order_number'] ?? '' }}" placeholder="Ví dụ: ORD-69D..." />
                </div>
                <div>
                    <label class="admin-field-label">Khách hàng</label>
                    <input type="text" name="customer" value="{{ $filters['customer'] ?? '' }}" placeholder="Tên, email hoặc số điện thoại" />
                </div>
                <div>
                    <label class="admin-field-label">Trạng thái</label>
                    <select name="status">
                        <option value="">Tất cả trạng thái</option>
                        <option value="pending" @selected(($filters['status'] ?? '') === 'pending')>Chờ xác nhận</option>
                        <option value="processing" @selected(($filters['status'] ?? '') === 'processing')>Đang xử lý</option>
                        <option value="shipping" @selected(($filters['status'] ?? '') === 'shipping')>Đang giao</option>
                        <option value="completed" @selected(($filters['status'] ?? '') === 'completed')>Hoàn thành</option>
                        <option value="cancelled" @selected(($filters['status'] ?? '') === 'cancelled')>Đã hủy</option>
                    </select>
                </div>
                <div>
                    <label class="admin-field-label">Từ ngày</label>
                    <input type="date" name="date_from" value="{{ $filters['date_from'] ?? '' }}" />
                </div>
                <div>
                    <label class="admin-field-label">Đến ngày</label>
                    <input type="date" name="date_to" value="{{ $filters['date_to'] ?? '' }}" />
                </div>

                <div class="md:col-span-2 xl:col-span-5 flex flex-wrap items-center gap-3 pt-2">
                    <button type="submit" class="admin-btn-primary">
                        <i class="fas fa-filter text-sm"></i>
                        Áp dụng bộ lọc
                    </button>
                    <a href="{{ route('admin.orders.index') }}" class="admin-btn-secondary">
                        <i class="fas fa-rotate-right text-sm"></i>
                        Làm mới
                    </a>
                </div>
            </form>
        </section>

        <section class="admin-table-shell">
            <div class="overflow-x-auto">
                <table class="min-w-[1120px]">
                    <thead>
                        <tr>
                            <th class="px-7 py-4 text-left">Mã đơn</th>
                            <th class="px-5 py-4 text-left">Khách hàng</th>
                            <th class="px-5 py-4 text-left">Tổng tiền</th>
                            <th class="px-5 py-4 text-left">Thanh toán</th>
                            <th class="px-5 py-4 text-left">Vận chuyển</th>
                            <th class="px-5 py-4 text-left">Trạng thái</th>
                            <th class="px-5 py-4 text-left">Ngày đặt</th>
                            <th class="px-7 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $order)
                            @php
                                [$statusLabel, $statusClass] = $statusMap[$order->status] ?? [$order->status, 'admin-badge admin-badge--muted'];
                                [$paymentLabel, $paymentClass] = $paymentMap[$order->payment?->status] ?? [$order->payment?->status_text ?? 'Chưa có', 'admin-badge admin-badge--muted'];
                                [$shipmentLabel, $shipmentClass] = $shipmentMap[$order->shipment?->status] ?? [$order->shipment?->status_text ?? 'Chưa có', 'admin-badge admin-badge--muted'];
                            @endphp
                            <tr>
                                <td class="px-7 py-5">
                                    <a href="{{ route('admin.orders.show', $order) }}" class="text-sm font-bold text-[#206223] hover:underline">{{ $order->order_number }}</a>
                                </td>
                                <td class="px-5 py-5">
                                    <p class="text-sm font-semibold text-[var(--admin-text)]">{{ $order->full_name }}</p>
                                    <p class="mt-1 text-xs text-[var(--admin-text-muted)]">{{ $order->phone }}</p>
                                    <p class="mt-1 text-xs text-[rgba(95,103,92,0.78)]">{{ $order->email }}</p>
                                </td>
                                <td class="px-5 py-5 text-sm font-bold text-[var(--admin-text)]">{{ number_format($order->payable_amount ?? $order->total_amount, 0, ',', '.') }}đ</td>
                                <td class="px-5 py-5">
                                    <div class="space-y-2">
                                        <p class="text-xs font-semibold uppercase tracking-[0.12em] text-[var(--admin-text-muted)]">{{ strtoupper($order->payment_method ?? 'COD') }}</p>
                                        <span class="{{ $paymentClass }}">{{ $paymentLabel }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-5">
                                    <div class="space-y-2">
                                        <p class="text-xs text-[var(--admin-text-muted)]">{{ $order->shipment?->carrier ?? 'Chưa có' }}</p>
                                        <span class="{{ $shipmentClass }}">{{ $shipmentLabel }}</span>
                                    </div>
                                </td>
                                <td class="px-5 py-5">
                                    <span class="{{ $statusClass }}">{{ $statusLabel }}</span>
                                </td>
                                <td class="px-5 py-5 text-sm text-[var(--admin-text-muted)]">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                <td class="px-7 py-5">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.orders.show', $order) }}" class="admin-action-icon" title="Chi tiết">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        <form action="{{ route('admin.orders.destroy', $order) }}" method="POST" onsubmit="return confirm('Xóa đơn hàng này? Hành động này không thể hoàn tác.')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-action-icon hover:!bg-[rgba(255,218,214,0.45)] hover:!text-[var(--admin-danger-text)]" title="Xóa">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-7 py-20">
                                    <div class="admin-empty-state">
                                        <i class="fas fa-receipt text-4xl opacity-30"></i>
                                        <p class="text-sm">Không tìm thấy đơn hàng phù hợp với bộ lọc hiện tại.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($orders->hasPages())
                <div class="admin-pagination-shell border-t border-[rgba(112,122,108,0.12)] px-6 py-5">
                    {{ $orders->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
