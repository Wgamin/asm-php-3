<header class="bg-white border-b border-slate-200 h-16 flex items-center px-6 sticky top-0 z-30">
    <div class="flex items-center justify-between w-full">
        <div class="flex items-center gap-4">
            <!-- Toggle Sidebar -->
            <button @click="sidebarOpen = !sidebarOpen" 
                    x-show="!isMobile"
                    x-cloak
                    class="w-10 h-10 rounded-lg hover:bg-slate-100 text-slate-600 transition">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Mobile Toggle -->
            <button @click="mobileSidebar = true" 
                    x-show="isMobile"
                    x-cloak
                    class="w-10 h-10 rounded-lg hover:bg-slate-100 text-slate-600 transition">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Breadcrumb -->
            <nav class="hidden md:flex items-center space-x-2 text-sm">
                <a href="{{ route('admin.dashboard') }}" class="text-slate-400 hover:text-slate-600">Dashboard</a>
                <i class="fas fa-chevron-right text-xs text-slate-400"></i>
                <span class="text-slate-800 font-medium">@yield('title', 'Trang chủ')</span>
            </nav>
        </div>
        
        <!-- Right Menu -->
        <div class="flex items-center gap-3">
            <!-- Search -->
            @include('admin.layouts.partials.top-nav-search')
            
            <!-- Notifications -->
            @include('admin.layouts.partials.top-nav-notifications')
            
            <!-- User Menu -->
            @include('admin.layouts.partials.top-nav-user')
        </div>
    </div>
</header>
