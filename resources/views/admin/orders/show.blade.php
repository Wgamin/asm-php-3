@extends('admin.layouts.master')

@section('title', 'Chi tiết đơn hàng')

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
    [$statusLabel, $statusClass] = $statusMap[$order->status] ?? [$order->status_text, 'admin-badge admin-badge--muted'];
    [$paymentLabel, $paymentClass] = $paymentMap[$order->payment?->status] ?? [$order->payment?->status_text ?? 'Chưa có', 'admin-badge admin-badge--muted'];
    [$shipmentLabel, $shipmentClass] = $shipmentMap[$order->shipment?->status] ?? [$order->shipment?->status_text ?? 'Chưa có', 'admin-badge admin-badge--muted'];
    $subtotal = $order->subtotal_amount ?? $order->total_amount;
    $shippingFee = $order->shipping_fee_amount ?? 0;
    $grandTotal = $order->payable_amount ?? $order->payable_total ?? $order->total_amount;
@endphp

@section('content')
    <div class="mx-auto max-w-7xl space-y-8">
        <section class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <div class="mb-2 flex items-center gap-3">
                    <p class="admin-kicker">Đơn hàng</p>
                    <span class="{{ $statusClass }}">{{ $statusLabel }}</span>
                </div>
                <h1 class="admin-headline text-4xl font-bold tracking-[-0.05em] text-[var(--admin-text)]">{{ $order->order_number }}</h1>
                <p class="admin-copy mt-3 text-sm">Đặt lúc {{ $order->created_at->format('H:i, d/m/Y') }}. Theo dõi thông tin giao hàng, thanh toán, trạng thái và lịch sử xử lý trên cùng một màn hình.</p>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.orders.index') }}" class="admin-btn-secondary">
                    <i class="fas fa-arrow-left text-sm"></i>
                    Quay lại danh sách
                </a>
            </div>
        </section>

        <div class="grid gap-6 xl:grid-cols-[1.6fr_1fr]">
            <div class="space-y-6">
                <section class="admin-surface-card p-6">
                    <div class="mb-6 flex items-start justify-between gap-3">
                        <div>
                            <h3 class="admin-headline text-2xl font-bold tracking-[-0.03em]">Danh sách sản phẩm</h3>
                            <p class="admin-copy mt-2 text-sm">Chi tiết từng sản phẩm, biến thể, số lượng và giá trị thành tiền trong đơn.</p>
                        </div>
                        <span class="admin-badge admin-badge--muted">{{ $order->items->count() }} mục</span>
                    </div>

                    <div class="space-y-3">
                        @foreach($order->items as $item)
                            @php
                                $product = $item->product;
                                $variantValues = is_array($item->variant_values) ? $item->variant_values : [];
                                $variantText = collect($variantValues)->map(fn ($value, $name) => $name . ': ' . $value)->implode(' • ');
                            @endphp
                            <div class="rounded-[1.2rem] bg-[var(--admin-surface-low)] px-4 py-4">
                                <div class="grid gap-4 md:grid-cols-[1.8fr_auto_auto_auto] md:items-center">
                                    <div class="flex items-center gap-4">
                                        <img
                                            src="{{ $product && $product->image ? asset('storage/' . $product->image) : asset('images/default-product.png') }}"
                                            class="h-16 w-16 rounded-2xl object-cover"
                                            alt="{{ $product?->name ?? 'Sản phẩm' }}"
                                        >
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-bold text-[var(--admin-text)]">{{ $product?->name ?? 'Sản phẩm đã xóa' }}</p>
                                            @if($item->variant_sku)
                                                <p class="mt-1 text-[11px] font-semibold uppercase tracking-[0.14em] text-[var(--admin-text-muted)]">SKU: {{ $item->variant_sku }}</p>
                                            @endif
                                            @if($variantText)
                                                <p class="mt-2 text-xs text-[var(--admin-text-muted)]">{{ $variantText }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="text-sm text-[var(--admin-text-muted)]">x{{ $item->quantity }}</div>
                                    <div class="text-sm font-semibold text-[var(--admin-text)]">{{ number_format($item->price, 0, ',', '.') }}đ</div>
                                    <div class="text-sm font-bold text-[#206223]">{{ number_format($item->price * $item->quantity, 0, ',', '.') }}đ</div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </section>

                <section class="admin-surface-card p-6">
                    <div class="mb-6">
                        <h3 class="admin-headline text-2xl font-bold tracking-[-0.03em]">Lịch sử trạng thái</h3>
                        <p class="admin-copy mt-2 text-sm">Ghi nhận toàn bộ thay đổi đơn hàng từ lúc tạo, cập nhật bởi admin, thanh toán đến vận chuyển.</p>
                    </div>

                    <div class="space-y-5">
                        @forelse($order->statusHistories as $history)
                            @php([$historyLabel, $historyClass] = $statusMap[$history->status] ?? [$history->status, 'admin-badge admin-badge--muted'])
                            <div class="relative pl-10">
                                <span class="absolute left-0 top-1.5 flex h-6 w-6 items-center justify-center rounded-full bg-[rgba(32,98,35,0.12)] text-[#206223]">
                                    <i class="fas fa-check text-[10px]"></i>
                                </span>
                                <div class="rounded-[1.15rem] bg-[var(--admin-surface-low)] px-4 py-4">
                                    <div class="flex flex-wrap items-center gap-3">
                                        <span class="{{ $historyClass }}">{{ $historyLabel }}</span>
                                        <p class="text-xs font-semibold uppercase tracking-[0.14em] text-[var(--admin-text-muted)]">{{ $history->created_at?->format('H:i d/m/Y') }}</p>
                                    </div>
                                    <p class="mt-3 text-sm font-semibold text-[var(--admin-text)]">Nguồn cập nhật: {{ ucfirst($history->source ?? 'system') }}</p>
                                    @if($history->note)
                                        <p class="mt-2 text-sm leading-7 text-[var(--admin-text-muted)]">{{ $history->note }}</p>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="admin-empty-state rounded-[1.2rem] bg-[var(--admin-surface-low)] py-10">
                                <i class="fas fa-timeline text-3xl opacity-30"></i>
                                <p class="text-sm">Chưa có lịch sử trạng thái nào được ghi nhận.</p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>

            <div class="space-y-6">
                <section class="admin-surface-card p-6">
                    <h3 class="admin-headline text-2xl font-bold tracking-[-0.03em]">Cập nhật trạng thái</h3>
                    <p class="admin-copy mt-2 text-sm">Đồng bộ trạng thái đơn hàng với payment và shipment theo quy tắc hiện tại của hệ thống.</p>

                    <form action="{{ route('admin.orders.updateStatus', $order) }}" method="POST" class="mt-6 space-y-4">
                        @csrf
                        <div>
                            <label class="admin-field-label">Trạng thái đơn</label>
                            <select name="status">
                                <option value="pending" @selected($order->status === 'pending')>Chờ xác nhận</option>
                                <option value="processing" @selected($order->status === 'processing')>Đang xử lý</option>
                                <option value="shipping" @selected($order->status === 'shipping')>Đang giao</option>
                                <option value="completed" @selected($order->status === 'completed')>Hoàn thành</option>
                                <option value="cancelled" @selected($order->status === 'cancelled')>Đã hủy</option>
                            </select>
                        </div>
                        <button type="submit" class="admin-btn-primary w-full">
                            <i class="fas fa-floppy-disk text-sm"></i>
                            Lưu trạng thái mới
                        </button>
                    </form>
                </section>

                <section class="admin-surface-card p-6">
                    <h3 class="admin-headline text-2xl font-bold tracking-[-0.03em]">Thông tin khách hàng</h3>
                    <div class="mt-6 space-y-4">
                        <div>
                            <p class="admin-kicker">Người nhận</p>
                            <p class="mt-2 text-sm font-bold text-[var(--admin-text)]">{{ $order->full_name }}</p>
                        </div>
                        <div>
                            <p class="admin-kicker">Liên hệ</p>
                            <p class="mt-2 text-sm text-[var(--admin-text)]">{{ $order->phone }}</p>
                            <p class="mt-1 text-sm text-[var(--admin-text-muted)]">{{ $order->email }}</p>
                        </div>
                        <div>
                            <p class="admin-kicker">Địa chỉ giao hàng</p>
                            <p class="mt-2 text-sm leading-7 text-[var(--admin-text)]">{{ $order->address }}</p>
                        </div>
                        @if($order->note)
                            <div>
                                <p class="admin-kicker">Ghi chú</p>
                                <p class="mt-2 rounded-2xl bg-[var(--admin-surface-low)] px-4 py-3 text-sm leading-7 text-[var(--admin-text-muted)]">{{ $order->note }}</p>
                            </div>
                        @endif
                    </div>
                </section>

                <section class="admin-surface-card p-6">
                    <h3 class="admin-headline text-2xl font-bold tracking-[-0.03em]">Thanh toán & vận chuyển</h3>
                    <div class="mt-6 space-y-5">
                        <div>
                            <p class="admin-kicker">Thanh toán</p>
                            <div class="mt-3 flex flex-wrap items-center gap-3">
                                <span class="admin-badge admin-badge--muted">{{ strtoupper($order->payment_method ?? 'COD') }}</span>
                                <span class="{{ $paymentClass }}">{{ $paymentLabel }}</span>
                            </div>
                            @if($order->payment?->transaction_code)
                                <p class="mt-3 text-sm text-[var(--admin-text-muted)]">Mã giao dịch: <span class="font-semibold text-[var(--admin-text)]">{{ $order->payment->transaction_code }}</span></p>
                            @endif
                        </div>
                        <div>
                            <p class="admin-kicker">Vận chuyển</p>
                            <div class="mt-3 flex flex-wrap items-center gap-3">
                                <span class="admin-badge admin-badge--muted">{{ $order->shipment?->carrier ?? 'Chưa có' }}</span>
                                <span class="{{ $shipmentClass }}">{{ $shipmentLabel }}</span>
                            </div>
                            @if($order->shipment?->tracking_code)
                                <p class="mt-3 text-sm text-[var(--admin-text-muted)]">Mã vận đơn: <span class="font-semibold text-[var(--admin-text)]">{{ $order->shipment->tracking_code }}</span></p>
                            @endif
                        </div>
                    </div>
                </section>

                <section class="admin-surface-card p-6 bg-[linear-gradient(180deg,#1f2d22,#191c1e)] text-white">
                    <h3 class="admin-headline text-2xl font-bold tracking-[-0.03em]">Tổng giá trị đơn</h3>
                    <div class="mt-6 space-y-3 text-sm">
                        <div class="flex items-center justify-between text-white/72">
                            <span>Tạm tính</span>
                            <span>{{ number_format($subtotal, 0, ',', '.') }}đ</span>
                        </div>
                        <div class="flex items-center justify-between text-white/72">
                            <span>Giảm giá</span>
                            <span>-{{ number_format($order->discount_amount ?? 0, 0, ',', '.') }}đ</span>
                        </div>
                        <div class="flex items-center justify-between text-white/72">
                            <span>Phí vận chuyển</span>
                            <span>{{ number_format($shippingFee, 0, ',', '.') }}đ</span>
                        </div>
                        <div class="h-px bg-white/10"></div>
                        <div class="flex items-end justify-between">
                            <span class="text-base font-semibold">Tổng thanh toán</span>
                            <span class="admin-headline text-3xl font-bold tracking-[-0.04em] text-[#acf4a4]">{{ number_format($grandTotal, 0, ',', '.') }}đ</span>
                        </div>
                    </div>
                </section>
            </div>
        </div>
    </div>
@endsection
