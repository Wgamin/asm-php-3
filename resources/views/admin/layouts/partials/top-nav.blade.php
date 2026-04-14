<header class="admin-topbar sticky top-0 z-30 flex h-[76px] items-center border-b border-[rgba(112,122,108,0.12)] px-5 lg:px-8">
    <div class="flex w-full items-center justify-between gap-4">
        <div class="flex min-w-0 items-center gap-3 lg:gap-5">
            <button
                @click="sidebarOpen = !sidebarOpen"
                x-show="!isMobile"
                x-cloak
                class="hidden h-11 w-11 items-center justify-center rounded-2xl text-[var(--admin-text-muted)] transition hover:bg-[rgba(95,103,92,0.08)] lg:inline-flex"
            >
                <i class="fas fa-bars"></i>
            </button>

            <button
                @click="mobileSidebar = true"
                x-show="isMobile"
                x-cloak
                class="inline-flex h-11 w-11 items-center justify-center rounded-2xl text-[var(--admin-text-muted)] transition hover:bg-[rgba(95,103,92,0.08)] lg:hidden"
            >
                <i class="fas fa-bars"></i>
            </button>

            <div class="min-w-0">
                <nav class="mb-1 hidden items-center gap-2 text-[11px] font-semibold uppercase tracking-[0.16em] text-[var(--admin-text-muted)] md:flex">
                    <a href="{{ route('admin.dashboard') }}" class="transition hover:text-[#206223]">Trang chủ</a>
                    <i class="fas fa-chevron-right text-[9px] opacity-50"></i>
                    <span class="truncate">@yield('title', 'Trang quản trị')</span>
                </nav>
                <h2 class="admin-headline truncate text-xl font-bold tracking-[-0.03em] text-[var(--admin-text)]">@yield('title', 'Trang quản trị')</h2>
            </div>
        </div>

        <div class="flex items-center gap-3">
            @include('admin.layouts.partials.top-nav-search')
            @include('admin.layouts.partials.top-nav-notifications')
            @include('admin.layouts.partials.top-nav-user')
        </div>
    </div>
</header>
