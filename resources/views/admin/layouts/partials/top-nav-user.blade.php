<div class="relative" x-data="{ open: false }">
    <button
        @click="open = !open"
        class="flex items-center gap-3 rounded-2xl px-2 py-1.5 transition hover:bg-[rgba(95,103,92,0.08)]"
    >
        <img
            src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=206223&color=fff&bold=true"
            class="h-9 w-9 rounded-2xl object-cover ring-2 ring-white/80"
            alt="{{ auth()->user()->name }}"
        >
        <div class="hidden text-left md:block">
            <p class="max-w-[140px] truncate text-sm font-bold text-[var(--admin-text)]">{{ auth()->user()->name }}</p>
            <p class="text-[11px] text-[var(--admin-text-muted)]">Quản trị viên</p>
        </div>
        <i class="fas fa-chevron-down text-[11px] text-[var(--admin-text-muted)] transition" :class="open ? 'rotate-180' : ''"></i>
    </button>

    <div
        x-show="open"
        @click.away="open = false"
        x-cloak
        x-transition
        class="admin-glass absolute right-0 mt-3 w-56 rounded-[1.2rem] border border-[rgba(112,122,108,0.12)] py-2 shadow-[0_30px_60px_-30px_rgba(25,28,30,0.22)]"
    >
        <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-[var(--admin-text-muted)] transition hover:bg-[rgba(95,103,92,0.08)] hover:text-[var(--admin-text)]">
            <i class="fas fa-user-gear w-4 text-center"></i>
            <span>Hồ sơ quản trị</span>
        </a>
        <a href="{{ route('admin.settings.index') }}" class="flex items-center gap-3 px-4 py-3 text-sm text-[var(--admin-text-muted)] transition hover:bg-[rgba(95,103,92,0.08)] hover:text-[var(--admin-text)]">
            <i class="fas fa-gear w-4 text-center"></i>
            <span>Cài đặt hệ thống</span>
        </a>
        <div class="mx-4 my-2 h-px bg-[rgba(112,122,108,0.12)]"></div>
        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit" class="flex w-full items-center gap-3 px-4 py-3 text-sm text-[var(--admin-danger-text)] transition hover:bg-[rgba(255,218,214,0.45)]">
                <i class="fas fa-arrow-right-from-bracket w-4 text-center"></i>
                <span>Đăng xuất</span>
            </button>
        </form>
    </div>
</div>
