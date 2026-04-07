<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - @yield('title')</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Alpine.js cho interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <style>
        [x-cloak] { display: none !important; }
        
        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #cbd5e0;
            border-radius: 4px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased"
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
      :class="{ 'overflow-hidden': mobileSidebar && isMobile }">

    <!-- Mobile Overlay -->
    @include('admin.layouts.partials.mobile-overlay')

    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        @include('admin.layouts.partials.sidebar')

        <!-- Main Content -->
        <main class="flex-1 flex flex-col overflow-hidden">
            <!-- Top Navigation -->
            @include('admin.layouts.partials.top-nav')

            <!-- Page Content -->
            <div class="flex-1 overflow-y-auto p-6 bg-slate-50">
                <!-- Alerts -->
                @if(session('success') || session('error'))
                    @include('admin.layouts.partials.alert')
                @endif

                @yield('content')
            </div>
        </main>
    </div>
</body>
</html>
