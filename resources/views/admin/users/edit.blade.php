@extends('admin.layouts.master')

@section('title', 'Chỉnh sửa khách hàng')

@section('content')
    <div class="mx-auto max-w-4xl space-y-8">
        <section class="flex items-end justify-between gap-4">
            <div>
                <p class="admin-kicker">CRM cơ bản</p>
                <h1 class="admin-headline mt-2 text-4xl font-bold tracking-[-0.05em] text-[var(--admin-text)]">Chỉnh sửa khách hàng</h1>
                <p class="admin-copy mt-3 max-w-2xl text-sm">Đang chỉnh sửa hồ sơ của <strong class="text-[var(--admin-text)]">{{ $user->email }}</strong>.</p>
            </div>
            <a href="{{ route('admin.users.index') }}" class="admin-btn-secondary">
                <i class="fas fa-arrow-left text-sm"></i>
                Quay lại
            </a>
        </section>

        <section class="admin-surface-card p-7">
            <form action="{{ route('admin.users.update', $user->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <div class="grid gap-5 md:grid-cols-2">
                    <div>
                        <label class="admin-field-label">Họ và tên</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required>
                    </div>

                    <div>
                        <label class="admin-field-label">Địa chỉ email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
                    </div>
                </div>

                <div>
                    <label class="admin-field-label">Mật khẩu mới (để trống nếu không đổi)</label>
                    <input type="password" name="password" placeholder="Nhập mật khẩu mới nếu muốn thay đổi">
                </div>

                <div class="flex justify-end gap-3 border-t border-[rgba(112,122,108,0.12)] pt-6">
                    <a href="{{ route('admin.users.index') }}" class="admin-btn-ghost">Hủy</a>
                    <button type="submit" class="admin-btn-primary">
                        <i class="fas fa-floppy-disk text-sm"></i>
                        Cập nhật khách hàng
                    </button>
                </div>
            </form>
        </section>
    </div>
@endsection
