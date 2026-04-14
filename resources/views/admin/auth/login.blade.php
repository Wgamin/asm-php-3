@extends('admin.layouts.admin_auth')

@section('title', 'Đăng nhập')

@section('content')
    <div class="admin-auth-shadow rounded-[1.75rem] bg-white px-7 py-8 sm:px-9 sm:py-10">
        <div class="mb-8 lg:hidden">
            <div class="flex items-center gap-3">
                <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-[linear-gradient(135deg,#206223,#3a7b3a)] text-white shadow-[0_18px_34px_-22px_rgba(32,98,35,0.6)]">
                    <i class="fas fa-leaf text-sm"></i>
                </div>
                <div>
                    <h1 class="admin-headline text-2xl font-extrabold tracking-[-0.04em] text-[#143716]">Nông Sản Việt</h1>
                    <p class="mt-1 text-xs font-semibold uppercase tracking-[0.2em] text-[var(--admin-text-muted)]">Admin Panel</p>
                </div>
            </div>
        </div>

        <div class="mb-8">
            <h2 class="admin-headline text-3xl font-bold tracking-[-0.04em] text-[var(--admin-text)]">Chào mừng trở lại</h2>
            <p class="mt-3 text-sm leading-7 text-[var(--admin-text-muted)]">Đăng nhập để quản lý đơn hàng, sản phẩm, khách hàng và các cấu hình vận hành của hệ thống.</p>
        </div>

        @if ($errors->any())
            <div class="mb-6 rounded-2xl bg-[rgba(255,218,214,0.55)] px-4 py-4 text-sm text-[var(--admin-danger-text)]">
                <p class="mb-2 font-bold">Không thể đăng nhập</p>
                <ul class="list-inside list-disc space-y-1 text-sm">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.login') }}" method="POST" class="space-y-6">
            @csrf

            <div>
                <label for="email" class="admin-field-label">Email Admin</label>
                <div class="relative">
                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[var(--admin-text-muted)]">
                        <i class="fas fa-user"></i>
                    </span>
                    <input id="email" type="email" name="email" class="pl-11" placeholder="admin@nongsanviet.vn" required value="{{ old('email') }}">
                </div>
            </div>

            <div>
                <div class="mb-2 flex items-center justify-between gap-3">
                    <label for="password" class="admin-field-label mb-0">Mật khẩu</label>
                    <span class="text-xs font-semibold text-[#206223]">Khu vực bảo mật</span>
                </div>
                <div class="relative">
                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-[var(--admin-text-muted)]">
                        <i class="fas fa-lock"></i>
                    </span>
                    <input id="password" type="password" name="password" class="pl-11" placeholder="••••••••" required>
                </div>
            </div>

            <div class="flex items-center justify-between gap-3 text-sm">
                <label class="inline-flex items-center gap-3 text-[var(--admin-text-muted)]">
                    <input type="checkbox" class="h-4 w-4 rounded border-[rgba(112,122,108,0.25)] text-[#206223] focus:ring-[#206223]">
                    <span>Ghi nhớ đăng nhập</span>
                </label>
                <a href="{{ route('password.request') }}" class="font-semibold text-[#206223] hover:underline">Quên mật khẩu?</a>
            </div>

            <button type="submit" class="admin-btn-primary w-full rounded-[1rem] py-4 text-base">
                Đăng nhập quản trị
                <i class="fas fa-arrow-right-to-bracket text-sm"></i>
            </button>
        </form>

        <div class="mt-8 border-t border-[rgba(112,122,108,0.12)] pt-6 text-center text-xs text-[var(--admin-text-muted)]">
            <p>© {{ now()->year }} Nông Sản Việt. Hệ thống quản trị nội bộ.</p>
            <p class="mt-3">
                <a href="{{ route('admin.register') }}" class="font-semibold text-[#206223] hover:underline">Tạo tài khoản Admin (dev/test)</a>
            </p>
        </div>
    </div>
@endsection
