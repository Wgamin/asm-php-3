@extends('admin.layouts.master')

@section('title', 'Coupon')

@section('content')
    <div class="mx-auto max-w-7xl space-y-8">
        <section class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="admin-kicker">Khách hàng & marketing</p>
                <h1 class="admin-headline mt-2 text-4xl font-bold tracking-[-0.05em] text-[var(--admin-text)]">Quản lý coupon</h1>
                <x-admin-info class="mt-3">
                    Tạo, chỉnh sửa và theo dõi hiệu quả của mã giảm giá theo điều kiện đơn hàng, giới hạn sử dụng và khung thời gian áp dụng.
                </x-admin-info>
            </div>
            <a href="{{ route('admin.coupons.create') }}" class="admin-btn-primary">
                <i class="fas fa-ticket-simple text-sm"></i>
                Tạo coupon
            </a>
        </section>

        <section class="admin-table-shell">
            <div class="overflow-x-auto">
                <table class="min-w-[1080px]">
                    <thead>
                        <tr>
                            <th class="px-7 py-4 text-left">Coupon</th>
                            <th class="px-5 py-4 text-left">Giảm giá</th>
                            <th class="px-5 py-4 text-left">Điều kiện</th>
                            <th class="px-5 py-4 text-left">Lượt dùng</th>
                            <th class="px-5 py-4 text-left">Trạng thái</th>
                            <th class="px-7 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($coupons as $coupon)
                            <tr>
                                <td class="px-7 py-5 align-top">
                                    <p class="text-sm font-bold text-[var(--admin-text)]">{{ $coupon->code }}</p>
                                    <p class="mt-1 text-sm text-[var(--admin-text-muted)]">{{ $coupon->name }}</p>
                                    @if($coupon->description)
                                        <p class="mt-2 max-w-sm text-xs leading-6 text-[rgba(95,103,92,0.85)]">{{ $coupon->description }}</p>
                                    @endif
                                </td>
                                <td class="px-5 py-5 align-top">
                                    <p class="text-sm font-bold text-[var(--admin-text)]">{{ $coupon->value_text }}</p>
                                    <p class="mt-1 text-xs text-[var(--admin-text-muted)]">{{ $coupon->type_text }}</p>
                                    @if($coupon->max_discount_amount)
                                        <p class="mt-2 text-xs text-[rgba(95,103,92,0.85)]">Tối đa {{ number_format($coupon->max_discount_amount, 0, ',', '.') }}đ</p>
                                    @endif
                                </td>
                                <td class="px-5 py-5 align-top text-sm text-[var(--admin-text-muted)]">
                                    <p>Đơn tối thiểu <strong class="text-[var(--admin-text)]">{{ number_format($coupon->min_order_amount ?? 0, 0, ',', '.') }}đ</strong></p>
                                    <p class="mt-2">Từ <strong class="text-[var(--admin-text)]">{{ $coupon->starts_at ? $coupon->starts_at->format('d/m/Y H:i') : 'Ngay' }}</strong></p>
                                    <p class="mt-1">Đến <strong class="text-[var(--admin-text)]">{{ $coupon->expires_at ? $coupon->expires_at->format('d/m/Y H:i') : 'Không giới hạn' }}</strong></p>
                                </td>
                                <td class="px-5 py-5 align-top text-sm text-[var(--admin-text-muted)]">
                                    <p><strong class="text-[var(--admin-text)]">{{ $coupon->used_count }}</strong> lượt đã dùng</p>
                                    <p class="mt-2">Giới hạn: <strong class="text-[var(--admin-text)]">{{ $coupon->usage_limit ?? 'Không giới hạn' }}</strong></p>
                                </td>
                                <td class="px-5 py-5 align-top">
                                    <span class="{{ $coupon->is_active ? 'admin-badge admin-badge--success' : 'admin-badge admin-badge--danger' }}">
                                        {{ $coupon->is_active ? 'Đang bật' : 'Tạm ngưng' }}
                                    </span>
                                </td>
                                <td class="px-7 py-5 align-top">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.coupons.edit', $coupon) }}" class="admin-action-icon" title="Sửa">
                                            <i class="fas fa-pen text-sm"></i>
                                        </a>
                                        <form action="{{ route('admin.coupons.destroy', $coupon) }}" method="POST" onsubmit="return confirm('Xóa coupon này?')">
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
                                <td colspan="6" class="px-7 py-20">
                                    <div class="admin-empty-state">
                                        <i class="fas fa-ticket-simple text-4xl opacity-30"></i>
                                        <p class="text-sm">Chưa có coupon nào được tạo.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($coupons->hasPages())
                <div class="admin-pagination-shell border-t border-[rgba(112,122,108,0.12)] px-6 py-5">
                    {{ $coupons->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
