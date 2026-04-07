<aside x-show="isMobile ? mobileSidebar : sidebarOpen"
       x-cloak
       x-transition:enter="transition ease-out duration-300 transform"
       x-transition:enter-start="-translate-x-full"
       x-transition:enter-end="translate-x-0"
       x-transition:leave="transition ease-in duration-200 transform"
       x-transition:leave-start="translate-x-0"
       x-transition:leave-end="-translate-x-full"
       :class="isMobile ? 'fixed inset-y-0 left-0 z-50' : 'relative'"
       class="w-72 bg-white border-r border-slate-200 flex flex-col">
    
    <!-- Logo -->
    @include('admin.layouts.partials.sidebar-logo')
    
    <!-- Profile Summary -->
    @include('admin.layouts.partials.sidebar-profile')
    
    <!-- Navigation -->
    @include('admin.layouts.partials.sidebar-nav')
    
    <!-- Version -->
    @include('admin.layouts.partials.sidebar-version')
</aside>
