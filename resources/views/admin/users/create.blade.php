@extends('admin.layouts.master')

@section('title', 'Thêm khách hàng')

@section('content')
    <div class="mx-auto max-w-4xl space-y-8">
        <section class="flex items-end justify-between gap-4">
            <div>
                <p class="admin-kicker">CRM cơ bản</p>
                <h1 class="admin-headline mt-2 text-4xl font-bold tracking-[-0.05em] text-[var(--admin-text)]">Thêm khách hàng mới</h1>
                <p class="admin-copy mt-3 max-w-2xl text-sm">Tạo tài khoản mua hàng mới để quản lý hoặc hỗ trợ vận hành nội bộ trong môi trường admin.</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="admin-btn-secondary">
                <i class="fas fa-arrow-left text-sm"></i>
                Quay lại
            </a>
        </section>

        <section class="admin-surface-card p-7">
            <form action="{{ route('admin.users.store') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="admin-field-label">Họ và tên</label>
                        <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ví dụ: Nguyễn Văn An">
                        @error('name')
                            <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="admin-field-label">Địa chỉ email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required placeholder="an.nguyen@example.com">
                        @error('email')
                            <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label class="admin-field-label">Mật khẩu khởi tạo</label>
                    <input type="password" name="password" required placeholder="Tối thiểu 6 ký tự">
                    <p class="mt-2 text-xs text-[var(--admin-text-muted)]">Khách hàng có thể đổi lại mật khẩu sau khi đăng nhập.</p>
                    @error('password')
                        <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex justify-end gap-3 border-t border-[rgba(112,122,108,0.12)] pt-6">
                    <a href="{{ route('admin.users.index') }}" class="admin-btn-ghost">Hủy</a>
                    <button type="submit" class="admin-btn-primary">
                        <i class="fas fa-floppy-disk text-sm"></i>
                        Lưu khách hàng
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection
