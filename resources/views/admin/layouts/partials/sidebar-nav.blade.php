<nav class="flex-1 overflow-y-auto py-6 px-4">
    <div class="space-y-1">
        <a href="{{ route('admin.dashboard') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-all duration-200
                  {{ request()->routeIs('admin.dashboard') ? 'bg-emerald-50 text-emerald-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
            <i class="fas fa-tachometer-alt w-5 text-center {{ request()->routeIs('admin.dashboard') ? 'text-emerald-500' : 'text-slate-400' }}"></i>
            <span>Tổng quan</span>
        </a>

        <a href="{{ route('admin.users.index') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-all duration-200
                  {{ request()->routeIs('admin.users.*') ? 'bg-emerald-50 text-emerald-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
            <i class="fas fa-user-shield w-5 text-center {{ request()->routeIs('admin.users.*') ? 'text-emerald-500' : 'text-slate-400' }}"></i>
            <span>Người dùng</span>
        </a>

        <a href="{{ route('admin.products.index') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-all duration-200
                  {{ request()->routeIs('admin.products.*') ? 'bg-emerald-50 text-emerald-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
            <i class="fas fa-apple-alt w-5 text-center {{ request()->routeIs('admin.products.*') ? 'text-emerald-500' : 'text-slate-400' }}"></i>
            <span>Sản phẩm</span>
        </a>

        <a href="{{ route('admin.attributes.index') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-all duration-200
                  {{ request()->routeIs('admin.attributes.*') ? 'bg-emerald-50 text-emerald-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
            <i class="fas fa-tags w-5 text-center {{ request()->routeIs('admin.attributes.*') ? 'text-emerald-500' : 'text-slate-400' }}"></i>
            <span>Thuộc tính</span>
        </a>


        <a href="{{ route('admin.categories.index') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-all duration-200
                  {{ request()->routeIs('admin.categories.*') ? 'bg-emerald-50 text-emerald-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
            <i class="fas fa-list w-5 text-center {{ request()->routeIs('admin.categories.*') ? 'text-emerald-500' : 'text-slate-400' }}"></i>
            <span>Danh mục</span>
        </a>

        <a href="{{ route('admin.coupons.index') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-all duration-200
                  {{ request()->routeIs('admin.coupons.*') ? 'bg-emerald-50 text-emerald-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
            <i class="fas fa-ticket-alt w-5 text-center {{ request()->routeIs('admin.coupons.*') ? 'text-emerald-500' : 'text-slate-400' }}"></i>
            <span>Coupon</span>
        </a>

        <a href="{{ route('admin.orders.index') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-all duration-200
                  {{ request()->routeIs('admin.orders.*') ? 'bg-emerald-50 text-emerald-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
            <i class="fas fa-shopping-bag w-5 text-center {{ request()->routeIs('admin.orders.*') ? 'text-emerald-500' : 'text-slate-400' }}"></i>
            <span>Đơn hàng</span>
        </a>
        
        <!-- Tin tức -->
        <a href="{{ route('admin.news.index') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-all duration-200 text-slate-600 hover:bg-slate-50">
            <i class="fas fa-newspaper w-5 text-center text-slate-400"></i>
            <span>Tin tức</span>
        </a>

        <div class="border-t border-slate-100 my-4"></div>

        <a href="{{ route('admin.settings.index') }}"
           class="flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-all duration-200
                  {{ request()->routeIs('admin.settings.*') ? 'bg-emerald-50 text-emerald-600 font-medium' : 'text-slate-600 hover:bg-slate-50' }}">
            <i class="fas fa-cogs w-5 text-center {{ request()->routeIs('admin.settings.*') ? 'text-emerald-500' : 'text-slate-400' }}"></i>
            <span>Cài đặt hệ thống</span>
        </a>

        <form action="{{ route('logout') }}" method="POST">
            @csrf
            <button type="submit"
                    class="w-full flex items-center gap-3 px-4 py-3 text-sm rounded-xl transition-all duration-200 text-red-500 hover:bg-red-50">
                <i class="fas fa-sign-out-alt w-5 text-center"></i>
                <span>Đăng xuất</span>
            </button>
        </form>
    </div>
</nav>
