@extends('admin.layouts.admin_auth')

@section('title', 'Đăng ký Admin')

@section('content')
    <div class="space-y-6">
        <div>
            <p class="admin-kicker">Admin onboarding</p>
            <h1 class="admin-headline mt-2 text-3xl font-bold tracking-[-0.04em] text-[var(--admin-text)]">Tạo tài khoản quản trị</h1>
            <x-admin-info class="mt-3">
                Tạo tài khoản admin mới cho môi trường nội bộ. Chỉ dùng cho nhu cầu quản trị và kiểm thử.
            </x-admin-info>
        </div>

        <form action="{{ route('admin.register') }}" method="POST" class="space-y-5">
            @csrf

            <div>
                <label class="admin-field-label">Họ và tên</label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ví dụ: Nguyễn Văn Admin">
                @error('name')
                    <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="admin-field-label">Email quản trị</label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="admin@nongsanviet.vn">
                @error('email')
                    <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="admin-field-label">Mật khẩu</label>
                <input type="password" name="password" required placeholder="Tối thiểu 6 ký tự">
                @error('password')
                    <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="admin-field-label">Xác nhận mật khẩu</label>
                <input type="password" name="password_confirmation" required placeholder="Nhập lại mật khẩu">
            </div>

            <button type="submit" class="admin-btn-primary w-full rounded-[1rem] py-4 text-base">
                <i class="fas fa-shield-halved text-sm"></i>
                Tạo tài khoản admin
            </button>
        </form>

        <div class="rounded-[1rem] bg-[var(--admin-surface-low)] px-4 py-4 text-sm text-[var(--admin-text-muted)]">
            Đã có tài khoản?
            <a href="{{ route('admin.login') }}" class="font-semibold text-[var(--admin-primary)] hover:underline">Đăng nhập quản trị</a>
        </div>
    </div>
@endsection
