@extends('admin.layouts.master')

@section('title', 'Chỉnh sửa coupon')

@section('content')
    <div class="mx-auto max-w-5xl space-y-8">
        <section class="flex items-end justify-between gap-4">
            <div>
                <p class="admin-kicker">Marketing setup</p>
                <h1 class="admin-headline mt-2 text-4xl font-bold tracking-[-0.05em] text-[var(--admin-text)]">Chỉnh sửa coupon</h1>
                <p class="admin-copy mt-3 max-w-2xl text-sm">Cập nhật điều kiện sử dụng, hạn dùng và trạng thái hoạt động của mã giảm giá.</p>
            </div>
            <a href="{{ route('admin.coupons.index') }}" class="admin-btn-secondary">
                <i class="fas fa-arrow-left text-sm"></i>
                Quay lại
            </a>
        </section>

        <section class="admin-surface-card p-7">
            <form action="{{ route('admin.coupons.update', $coupon) }}" method="POST" class="space-y-8">
                @csrf
                @method('PUT')
                @include('admin.coupons._form', ['coupon' => $coupon])

                <div class="flex flex-col gap-3 border-t border-[rgba(112,122,108,0.12)] pt-6 md:flex-row md:items-center md:justify-between">
                    <p class="text-sm text-[var(--admin-text-muted)]">
                        Đã dùng <strong class="text-[var(--admin-text)]">{{ $coupon->used_count }}</strong> lượt
                        @if($coupon->usage_limit)
                            / {{ $coupon->usage_limit }} lượt
                        @endif
                    </p>
                    <button type="submit" class="admin-btn-primary">
                        <i class="fas fa-floppy-disk text-sm"></i>
                        Cập nhật coupon
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection
