@extends('admin.layouts.master')

@section('title', 'Khách hàng')

@section('content')
    <div class="mx-auto max-w-7xl space-y-8">
        @if(session('success'))
            <div class="rounded-[1.2rem] bg-[rgba(223,243,219,0.85)] px-5 py-4 text-sm font-semibold text-[var(--admin-success-text)]">
                {{ session('success') }}
            </div>
        @endif

        <section class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="admin-kicker">CRM cơ bản</p>
                <h1 class="admin-headline mt-2 text-4xl font-bold tracking-[-0.05em] text-[var(--admin-text)]">Danh sách khách hàng</h1>
                <x-admin-info class="mt-3">
                    Quản lý tài khoản mua hàng, theo dõi thời gian tham gia và thực hiện thao tác nhanh như chỉnh sửa hoặc xóa hồ sơ người dùng.
                </x-admin-info>
            </div>
            <a href="{{ route('admin.users.create') }}" class="admin-btn-primary">
                <i class="fas fa-user-plus text-sm"></i>
                Thêm khách hàng
            </a>
        </section>

        <section class="admin-panel p-6">
            <div class="grid gap-5 md:grid-cols-3">
                <article class="admin-surface-card admin-card-accent p-6">
                    <p class="admin-kicker">Tổng khách hàng</p>
                    <p class="admin-headline mt-3 text-4xl font-bold tracking-[-0.05em] text-[var(--admin-text)]">{{ number_format($users->total(), 0, ',', '.') }}</p>
                    <p class="mt-3 text-sm text-[var(--admin-text-muted)]">Tất cả tài khoản role <strong class="text-[var(--admin-text)]">user</strong> hiện có trong hệ thống.</p>
                </article>
                <article class="admin-surface-card p-6">
                    <p class="admin-kicker">Đang hiển thị</p>
                    <p class="admin-headline mt-3 text-4xl font-bold tracking-[-0.05em] text-[var(--admin-text)]">{{ number_format($users->count(), 0, ',', '.') }}</p>
                    <p class="mt-3 text-sm text-[var(--admin-text-muted)]">Số lượng bản ghi trên trang hiện tại.</p>
                </article>
                <article class="admin-surface-card p-6">
                    <p class="admin-kicker">Trang hiện tại</p>
                    <p class="admin-headline mt-3 text-4xl font-bold tracking-[-0.05em] text-[var(--admin-text)]">{{ number_format($users->currentPage(), 0, ',', '.') }}</p>
                    <p class="mt-3 text-sm text-[var(--admin-text-muted)]">Phân trang theo {{ number_format($users->perPage(), 0, ',', '.') }} khách hàng mỗi lượt xem.</p>
                </article>
            </div>
        </section>

        <section class="admin-table-shell">
            <div class="overflow-x-auto">
                <table class="min-w-[860px]">
                    <thead>
                        <tr>
                            <th class="px-7 py-4 text-left">Khách hàng</th>
                            <th class="px-5 py-4 text-left">Email</th>
                            <th class="px-5 py-4 text-left">Ngày tham gia</th>
                            <th class="px-7 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td class="px-7 py-5">
                                    <div class="flex items-center gap-4">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=206223&color=fff&bold=true" alt="{{ $user->name }}" class="h-11 w-11 rounded-2xl object-cover">
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-bold text-[var(--admin-text)]">{{ $user->name }}</p>
                                            <p class="mt-1 text-xs text-[var(--admin-text-muted)]">ID #{{ $user->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-5 text-sm text-[var(--admin-text-muted)]">{{ $user->email }}</td>
                                <td class="px-5 py-5">
                                    <p class="text-sm font-semibold text-[var(--admin-text)]">{{ $user->created_at->format('d/m/Y') }}</p>
                                    <p class="mt-1 text-xs text-[var(--admin-text-muted)]">{{ $user->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="px-7 py-5">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="admin-action-icon" title="Sửa">
                                            <i class="fas fa-pen text-sm"></i>
                                        </a>
                                        <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Xóa khách hàng này?')">
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
                                <td colspan="4" class="px-7 py-20">
                                    <div class="admin-empty-state">
                                        <i class="fas fa-user-slash text-4xl opacity-30"></i>
                                        <p class="text-sm">Không có khách hàng nào để hiển thị.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="admin-pagination-shell border-t border-[rgba(112,122,108,0.12)] px-6 py-5">
                    {{ $users->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
