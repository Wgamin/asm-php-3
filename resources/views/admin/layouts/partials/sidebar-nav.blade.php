<nav class="flex-1 overflow-y-auto px-4 pb-6">
    <div class="space-y-1.5">
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'border-l-4 border-[#206223] bg-[rgba(32,98,35,0.1)] font-bold text-[#1c5920]' : 'text-[var(--admin-text-muted)] hover:bg-[rgba(95,103,92,0.08)] hover:text-[#1c5920]' }}">
            <i class="fas fa-chart-pie w-5 text-center {{ request()->routeIs('admin.dashboard') ? 'text-[#206223]' : 'text-[rgba(95,103,92,0.84)]' }}"></i>
            <span class="admin-headline text-sm tracking-[-0.02em]">Bảng điều khiển</span>
        </a>

        <a href="{{ route('admin.products.index') }}"
           class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm transition-all duration-200 {{ request()->routeIs('admin.products.*') ? 'border-l-4 border-[#206223] bg-[rgba(32,98,35,0.1)] font-bold text-[#1c5920]' : 'text-[var(--admin-text-muted)] hover:bg-[rgba(95,103,92,0.08)] hover:text-[#1c5920]' }}">
            <i class="fas fa-box-open w-5 text-center {{ request()->routeIs('admin.products.*') ? 'text-[#206223]' : 'text-[rgba(95,103,92,0.84)]' }}"></i>
            <span class="admin-headline text-sm tracking-[-0.02em]">Sản phẩm</span>
        </a>

        <a href="{{ route('admin.orders.index') }}"
           class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm transition-all duration-200 {{ request()->routeIs('admin.orders.*') ? 'border-l-4 border-[#206223] bg-[rgba(32,98,35,0.1)] font-bold text-[#1c5920]' : 'text-[var(--admin-text-muted)] hover:bg-[rgba(95,103,92,0.08)] hover:text-[#1c5920]' }}">
            <i class="fas fa-bag-shopping w-5 text-center {{ request()->routeIs('admin.orders.*') ? 'text-[#206223]' : 'text-[rgba(95,103,92,0.84)]' }}"></i>
            <span class="admin-headline text-sm tracking-[-0.02em]">Đơn hàng</span>
        </a>

        <a href="{{ route('admin.users.index') }}"
           class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm transition-all duration-200 {{ request()->routeIs('admin.users.*') ? 'border-l-4 border-[#206223] bg-[rgba(32,98,35,0.1)] font-bold text-[#1c5920]' : 'text-[var(--admin-text-muted)] hover:bg-[rgba(95,103,92,0.08)] hover:text-[#1c5920]' }}">
            <i class="fas fa-users w-5 text-center {{ request()->routeIs('admin.users.*') ? 'text-[#206223]' : 'text-[rgba(95,103,92,0.84)]' }}"></i>
            <span class="admin-headline text-sm tracking-[-0.02em]">Khách hàng</span>
        </a>

        <a href="{{ route('admin.coupons.index') }}"
           class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm transition-all duration-200 {{ request()->routeIs('admin.coupons.*') ? 'border-l-4 border-[#206223] bg-[rgba(32,98,35,0.1)] font-bold text-[#1c5920]' : 'text-[var(--admin-text-muted)] hover:bg-[rgba(95,103,92,0.08)] hover:text-[#1c5920]' }}">
            <i class="fas fa-ticket-simple w-5 text-center {{ request()->routeIs('admin.coupons.*') ? 'text-[#206223]' : 'text-[rgba(95,103,92,0.84)]' }}"></i>
            <span class="admin-headline text-sm tracking-[-0.02em]">Coupon</span>
        </a>

        <a href="{{ route('admin.news.index') }}"
           class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm transition-all duration-200 {{ request()->routeIs('admin.news.*') ? 'border-l-4 border-[#206223] bg-[rgba(32,98,35,0.1)] font-bold text-[#1c5920]' : 'text-[var(--admin-text-muted)] hover:bg-[rgba(95,103,92,0.08)] hover:text-[#1c5920]' }}">
            <i class="fas fa-newspaper w-5 text-center {{ request()->routeIs('admin.news.*') ? 'text-[#206223]' : 'text-[rgba(95,103,92,0.84)]' }}"></i>
            <span class="admin-headline text-sm tracking-[-0.02em]">Tin tức</span>
        </a>

        <a href="{{ route('admin.attributes.index') }}"
           class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm transition-all duration-200 {{ request()->routeIs('admin.attributes.*') ? 'border-l-4 border-[#206223] bg-[rgba(32,98,35,0.1)] font-bold text-[#1c5920]' : 'text-[var(--admin-text-muted)] hover:bg-[rgba(95,103,92,0.08)] hover:text-[#1c5920]' }}">
            <i class="fas fa-sliders w-5 text-center {{ request()->routeIs('admin.attributes.*') ? 'text-[#206223]' : 'text-[rgba(95,103,92,0.84)]' }}"></i>
            <span class="admin-headline text-sm tracking-[-0.02em]">Thuộc tính</span>
        </a>

        <a href="{{ route('admin.categories.index') }}"
           class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm transition-all duration-200 {{ request()->routeIs('admin.categories.*') ? 'border-l-4 border-[#206223] bg-[rgba(32,98,35,0.1)] font-bold text-[#1c5920]' : 'text-[var(--admin-text-muted)] hover:bg-[rgba(95,103,92,0.08)] hover:text-[#1c5920]' }}">
            <i class="fas fa-sitemap w-5 text-center {{ request()->routeIs('admin.categories.*') ? 'text-[#206223]' : 'text-[rgba(95,103,92,0.84)]' }}"></i>
            <span class="admin-headline text-sm tracking-[-0.02em]">Danh mục</span>
        </a>

        <a href="{{ route('admin.chat.index') }}"
           class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm transition-all duration-200 {{ request()->routeIs('admin.chat.*') ? 'border-l-4 border-[#206223] bg-[rgba(32,98,35,0.1)] font-bold text-[#1c5920]' : 'text-[var(--admin-text-muted)] hover:bg-[rgba(95,103,92,0.08)] hover:text-[#1c5920]' }}">
            <i class="fas fa-headset w-5 text-center {{ request()->routeIs('admin.chat.*') ? 'text-[#206223]' : 'text-[rgba(95,103,92,0.84)]' }}"></i>
            <span class="admin-headline text-sm tracking-[-0.02em]">Hỗ trợ</span>
        </a>

        <a href="{{ route('admin.settings.index') }}"
           class="flex items-center gap-3 rounded-2xl px-4 py-3 text-sm transition-all duration-200 {{ request()->routeIs('admin.settings.*') ? 'border-l-4 border-[#206223] bg-[rgba(32,98,35,0.1)] font-bold text-[#1c5920]' : 'text-[var(--admin-text-muted)] hover:bg-[rgba(95,103,92,0.08)] hover:text-[#1c5920]' }}">
            <i class="fas fa-gear w-5 text-center {{ request()->routeIs('admin.settings.*') ? 'text-[#206223]' : 'text-[rgba(95,103,92,0.84)]' }}"></i>
            <span class="admin-headline text-sm tracking-[-0.02em]">Hệ thống</span>
        </a>
    </div>
</nav>
