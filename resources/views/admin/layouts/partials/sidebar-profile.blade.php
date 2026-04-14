<div class="px-4 pb-4">
    <div class="admin-glass rounded-[1.35rem] px-4 py-4 shadow-[0_24px_40px_-28px_rgba(25,28,30,0.18)]">
        <div class="flex items-center gap-3">
            <img
                src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=206223&color=fff&bold=true"
                class="h-12 w-12 rounded-2xl object-cover ring-4 ring-white/80"
                alt="{{ auth()->user()->name }}"
            >
            <div class="min-w-0">
                <p class="truncate text-sm font-bold text-[var(--admin-text)]">{{ auth()->user()->name }}</p>
                <p class="mt-1 truncate text-xs text-[var(--admin-text-muted)]">{{ auth()->user()->email }}</p>
                <span class="admin-badge admin-badge--success mt-3 inline-flex normal-case tracking-normal">
                    <span class="h-2 w-2 rounded-full bg-current opacity-80"></span>
                    Quản trị viên
                </span>
            </div>
        </div>
    </div>
</div>
