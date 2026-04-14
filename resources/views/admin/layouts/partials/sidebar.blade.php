<aside x-show="isMobile ? mobileSidebar : sidebarOpen"
       x-cloak
       x-transition:enter="transition ease-out duration-300 transform"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-200 transform"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       :class="isMobile ? 'fixed inset-y-0 left-0 z-50' : 'fixed inset-y-0 left-0 z-40'"
       class="admin-sidebar w-64 flex flex-col border-r border-[rgba(112,122,108,0.12)] shadow-[0_24px_40px_-28px_rgba(25,28,30,0.28)]">
    
    <!-- Logo -->
    @include('admin.layouts.partials.sidebar-logo')
    
    <!-- Profile Summary -->
    @include('admin.layouts.partials.sidebar-profile')
    
    <!-- Navigation -->
    @include('admin.layouts.partials.sidebar-nav')
    
    <!-- Version -->
    @include('admin.layouts.partials.sidebar-version')
</aside>
