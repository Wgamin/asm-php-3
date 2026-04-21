@extends('admin.layouts.master')

@section('title', 'Cài đặt hệ thống')

@section('content')
    <div class="mx-auto max-w-6xl space-y-8">
        @if(session('success'))
            <div class="rounded-[1.2rem] bg-[rgba(223,243,219,0.85)] px-5 py-4 text-sm font-semibold text-[var(--admin-success-text)]">
                {{ session('success') }}
            </div>
        @endif

        <section>
            <p class="admin-kicker">System & security</p>
            <h1 class="admin-headline mt-2 text-4xl font-bold tracking-[-0.05em] text-[var(--admin-text)]">Cài đặt hệ thống</h1>
            <x-admin-info class="mt-3">
                Quản lý hồ sơ quản trị viên và cấu hình kho mặc định để phục vụ luồng vận hành, thanh toán và vận chuyển trong toàn bộ hệ thống.
            </x-admin-info>
        </section>

        <form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8">
            @csrf
            @method('PUT')

            <div class="grid gap-6 xl:grid-cols-[1.25fr_1fr]">
                <section class="admin-surface-card p-7">
                    <div class="mb-8 flex items-start gap-4">
                        <img
                            src="https://ui-avatars.com/api/?name={{ urlencode($admin->name) }}&background=206223&color=fff&size=128"
                            alt="{{ $admin->name }}"
                            class="h-24 w-24 rounded-[1.5rem] object-cover ring-4 ring-[rgba(255,255,255,0.85)]"
                        >
                        <div>
                            <p class="admin-kicker">Admin profile</p>
                            <h3 class="admin-headline mt-2 text-2xl font-bold tracking-[-0.03em]">{{ $admin->name }}</h3>
                            <p class="mt-2 text-sm text-[var(--admin-text-muted)]">{{ $admin->email }}</p>
                            <span class="admin-badge admin-badge--success mt-4 inline-flex normal-case tracking-normal">Tài khoản quản trị</span>
                        </div>
                    </div>

                    <div class="grid gap-5 md:grid-cols-2">
                        <div>
                            <label class="admin-field-label">Tên hiển thị</label>
                            <input type="text" name="name" value="{{ old('name', $admin->name) }}" required>
                        </div>
                        <div>
                            <label class="admin-field-label">Email quản trị</label>
                            <input type="email" name="email" value="{{ old('email', $admin->email) }}" required>
                        </div>
                        <div>
                            <label class="admin-field-label">Mật khẩu mới</label>
                            <input type="password" name="password" placeholder="Để trống nếu không đổi">
                        </div>
                        <div>
                            <label class="admin-field-label">Xác nhận mật khẩu</label>
                            <input type="password" name="password_confirmation" placeholder="Nhập lại mật khẩu mới">
                        </div>
                    </div>
                </section>

                <section class="admin-panel-muted p-7">
                    <p class="admin-kicker">Vận hành</p>
                    <h3 class="admin-headline mt-2 text-2xl font-bold tracking-[-0.03em]">Thông tin cấu hình nhanh</h3>
                    <div class="mt-6 space-y-4 text-sm text-[var(--admin-text-muted)]">
                        <div class="rounded-[1.1rem] bg-white px-4 py-4">
                            <div class="flex items-center gap-2">
                                <p class="font-semibold text-[var(--admin-text)]">Phạm vi cấu hình</p>
                                <x-admin-info>
                                    Cập nhật đồng thời hồ sơ admin và địa chỉ kho mặc định đang được sử dụng cho toàn bộ luồng checkout và shipment.
                                </x-admin-info>
                            </div>
                        </div>
                        <div class="rounded-[1.1rem] bg-white px-4 py-4">
                            <div class="flex items-center gap-2">
                                <p class="font-semibold text-[var(--admin-text)]">Bảo mật</p>
                                <x-admin-info>
                                    Nếu thay đổi mật khẩu, hệ thống sẽ lưu lại bằng cơ chế hash chuẩn Laravel. Để trống nếu chỉ muốn cập nhật tên hoặc email.
                                </x-admin-info>
                            </div>
                        </div>
                    </div>
                </section>
            </div>

            <section class="admin-surface-card p-7">
                <div class="mb-8">
                    <p class="admin-kicker">Warehouse default</p>
                    <h3 class="admin-headline mt-2 text-2xl font-bold tracking-[-0.03em]">Kho mặc định</h3>
                    <div class="mt-3">
                        <x-admin-info>
                            Kho này được dùng làm điểm xuất phát mặc định cho các luồng vận chuyển hiện tại của dự án.
                        </x-admin-info>
                    </div>
                </div>

                <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-3">
                    <div>
                        <label class="admin-field-label">Tên kho</label>
                        <input type="text" name="warehouse_name" value="{{ old('warehouse_name', $warehouse?->name) }}" required>
                    </div>
                    <div>
                        <label class="admin-field-label">Số điện thoại</label>
                        <input type="text" name="warehouse_phone" value="{{ old('warehouse_phone', $warehouse?->phone) }}" required>
                    </div>
                    <div>
                        <label class="admin-field-label">Tỉnh / Thành phố</label>
                        <input type="text" name="warehouse_province" value="{{ old('warehouse_province', $warehouse?->province) }}" required>
                    </div>
                    <div>
                        <label class="admin-field-label">Quận / Huyện</label>
                        <input type="text" name="warehouse_district" value="{{ old('warehouse_district', $warehouse?->district) }}" required>
                    </div>
                    <div>
                        <label class="admin-field-label">Phường / Xã</label>
                        <input type="text" name="warehouse_ward" value="{{ old('warehouse_ward', $warehouse?->ward) }}" required>
                    </div>
                    <div class="md:col-span-2 xl:col-span-3">
                        <label class="admin-field-label">Địa chỉ chi tiết</label>
                        <textarea name="warehouse_address_line" rows="4" required>{{ old('warehouse_address_line', $warehouse?->address_line) }}</textarea>
                    </div>
                </div>
            </section>

            <div class="admin-glass sticky bottom-4 z-20 flex flex-col gap-3 rounded-[1.2rem] border border-[rgba(112,122,108,0.12)] px-5 py-4 shadow-[0_30px_60px_-30px_rgba(25,28,30,0.22)] md:flex-row md:items-center md:justify-between">
                <div>
                    <div class="flex items-center gap-2">
                        <p class="text-sm font-bold text-[var(--admin-text)]">Sẵn sàng lưu thay đổi</p>
                        <x-admin-info>
                            Áp dụng cho hồ sơ admin và kho mặc định của hệ thống.
                        </x-admin-info>
                    </div>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('admin.dashboard') }}" class="admin-btn-ghost">Hủy bỏ</a>
                    <button type="submit" class="admin-btn-primary">
                        <i class="fas fa-floppy-disk text-sm"></i>
                        Lưu thay đổi
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection
