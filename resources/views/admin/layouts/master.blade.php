<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nông Sản Việt Admin - @yield('title', 'Trang quản trị')</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@400" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    @stack('styles')
</head>
<body
    class="admin-shell admin-body-font antialiased"
    x-data="{
        sidebarOpen: true,
        mobileSidebar: false,
        isMobile: window.innerWidth < 1024,
        init() {
            window.addEventListener('resize', () => {
                this.isMobile = window.innerWidth < 1024;

                if (!this.isMobile) {
                    this.mobileSidebar = false;
                }
            });
        }
    }"
    :class="{ 'overflow-hidden': mobileSidebar && isMobile }"
>
    @include('admin.layouts.partials.mobile-overlay')

    <div class="min-h-screen">
        @include('admin.layouts.partials.sidebar')

        <main class="min-h-screen lg:ml-64 flex flex-col">
            @include('admin.layouts.partials.top-nav')

            <div class="admin-content flex-1 overflow-y-auto">
                @if(session('success') || session('error'))
                    @include('admin.layouts.partials.alert')
                @endif

                @yield('content')
            </div>
        </main>
    </div>

    @stack('scripts')
</body>
</html>
