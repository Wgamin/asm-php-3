<div class="mt-auto px-6 py-4 border-t border-[rgba(112,122,108,0.12)]">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button
            type="submit"
            class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-sm font-semibold text-[var(--admin-text-muted)] transition hover:bg-[rgba(255,218,214,0.45)] hover:text-[var(--admin-danger-text)]"
        >
            <i class="fas fa-arrow-right-from-bracket w-5 text-center"></i>
            <span class="admin-headline text-sm tracking-[-0.02em]">Đăng xuất</span>
        </button>
    </form>
    <p class="mt-4 text-center text-[11px] font-semibold uppercase tracking-[0.14em] text-[rgba(95,103,92,0.7)]">Version 2.0.0</p>
</div>
