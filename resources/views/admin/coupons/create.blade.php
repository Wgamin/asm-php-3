@extends('admin.layouts.master')

@section('title', 'Tạo coupon')

@section('content')
    <div class="mx-auto max-w-5xl space-y-8">
        <section class="flex items-end justify-between gap-4">
            <div>
                <p class="admin-kicker">Marketing setup</p>
                <h1 class="admin-headline mt-2 text-4xl font-bold tracking-[-0.05em] text-[var(--admin-text)]">Tạo coupon mới</h1>
                <x-admin-info class="mt-3">
                    Thiết lập mã giảm giá để khách hàng sử dụng tại checkout với điều kiện áp dụng rõ ràng.
                </x-admin-info>
            </div>
            <a href="{{ route('admin.coupons.index') }}" class="admin-btn-secondary">
                <i class="fas fa-arrow-left text-sm"></i>
                Quay lại
            </a>
        </section>

        <section class="admin-surface-card p-7">
            <form action="{{ route('admin.coupons.store') }}" method="POST" class="space-y-8">
                @csrf
                @include('admin.coupons._form')

                <div class="flex justify-end gap-3 border-t border-[rgba(112,122,108,0.12)] pt-6">
                    <a href="{{ route('admin.coupons.index') }}" class="admin-btn-ghost">Hủy</a>
                    <button type="submit" class="admin-btn-primary">
                        <i class="fas fa-floppy-disk text-sm"></i>
                        Lưu coupon
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection
