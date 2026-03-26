<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title') - Nông Sản Việt</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 font-sans text-gray-900 flex flex-col min-h-screen">

    <!-- Navbar -->
    <nav class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-50" x-data="{ mobileMenu: false, searchOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                
                <!-- Logo -->
                <div class="flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center space-x-2 group">
                        <div class="bg-gradient-to-br from-primary-green to-dark-green p-2 rounded-xl group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-leaf text-white text-xl"></i>
                        </div>
                        <div>
                            <span class="font-bold text-2xl tracking-tight text-gray-800">
                                Nông Sản <span class="text-primary-green">Việt</span>
                            </span>
                            <span class="hidden lg:block text-[10px] uppercase tracking-wider text-gray-400">Tinh hoa đất Việt</span>
                        </div>
                    </a>
                    
                    <!-- Desktop Menu -->
                    <div class="hidden md:ml-10 md:flex md:space-x-1">
                        <a href="/" class="px-4 py-2 rounded-lg text-gray-600 hover:text-primary-green hover:bg-green-50 font-semibold transition flex items-center space-x-1">
                            <i class="fas fa-home text-sm"></i>
                            <span>Trang chủ</span>
                        </a>
                        <a href="{{ route('products.index') }}" class="px-4 py-2 rounded-lg text-gray-600 hover:text-primary-green hover:bg-green-50 font-semibold transition flex items-center space-x-1">
                            <i class="fas fa-apple-alt text-sm"></i>
                            <span>Sản phẩm</span>
                        </a>
                        <a href="#" class="px-4 py-2 rounded-lg text-gray-600 hover:text-primary-green hover:bg-green-50 font-semibold transition flex items-center space-x-1">
                            <i class="fas fa-newspaper text-sm"></i>
                            <span>Tin tức</span>
                        </a>
                        <a href="{{ route('contact') }}" class="px-4 py-2 rounded-lg text-gray-600 hover:text-primary-green hover:bg-green-50 font-semibold transition flex items-center space-x-1">
                            <i class="fas fa-phone-alt text-sm"></i>
                            <span>Liên hệ</span>
                        </a>
                    </div>
                </div>

                <!-- Right Menu -->
                <div class="hidden md:flex items-center space-x-4">
                    <!-- Search -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="text-gray-500 hover:text-primary-green text-lg transition p-2 hover:bg-gray-100 rounded-full">
                            <i class="fas fa-search"></i>
                        </button>
                        
                        <div x-show="open" @click.away="open = false" x-cloak
                             class="absolute right-0 mt-2 w-72 bg-white rounded-2xl shadow-xl border border-gray-100 overflow-hidden">
                            <div class="p-2">
                                <div class="flex items-center bg-gray-50 rounded-xl px-3 py-2">
                                    <i class="fas fa-search text-gray-400 mr-2"></i>
                                    <input type="text" placeholder="Tìm kiếm nông sản..." 
                                           class="bg-transparent border-0 focus:ring-0 text-sm w-full">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Cart -->
                    {{-- <a href="" class="relative p-2 hover:bg-gray-100 rounded-full group">
                        <i class="fas fa-shopping-bag text-gray-500 group-hover:text-primary-green text-lg"></i>
                        <span class="absolute -top-1 -right-1 bg-primary-green text-white text-[10px] w-4 h-4 flex items-center justify-center rounded-full">3</span>
                    </a> --}}
                    <a href="{{ route('checkout') }}" class="relative p-2 hover:bg-gray-100 rounded-full group">
                        <i class="fas fa-shopping-bag text-gray-500 group-hover:text-primary-green text-lg"></i>
                        <span class="absolute -top-1 -right-1 bg-primary-green text-white text-[10px] w-4 h-4 flex items-center justify-center rounded-full">
                            {{ session('cart') ? count(session('cart')) : 0 }}
                        </span>
                    </a>

                    <!-- User Menu -->
                    @guest
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-primary-green text-sm font-bold px-4 py-2 rounded-lg hover:bg-gray-50">Đăng nhập</a>
                            <a href="{{ route('register') }}" class=" bg-[#28a745] to-dark-green hover:from-dark-green hover:to-primary-green text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-md transition transform hover:scale-105">Đăng ký</a>
                        </div>
                    @else
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false" 
                                    class="flex items-center space-x-2 bg-gradient-to-r from-gray-50 to-gray-100 hover:from-gray-100 hover:to-gray-200 text-gray-800 px-3 py-1.5 rounded-xl border border-gray-200 transition">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=28a745&color=fff&bold=true" class="h-8 w-8 rounded-lg">
                                <div class="text-left">
                                    <p class="font-bold text-sm">{{ auth()->user()->name }}</p>
                                    <p class="text-[10px] text-gray-400">{{ auth()->user()->role === 'admin' ? 'Quản trị viên' : 'Thành viên' }}</p>
                                </div>
                                <i class="fas fa-chevron-down text-[10px] transition ml-2" :class="open ? 'rotate-180' : ''"></i>
                            </button>

                            <div x-show="open" x-cloak x-transition:enter="transition ease-out duration-100" 
                                 x-transition:enter-start="transform opacity-0 scale-95" 
                                 x-transition:leave="transition ease-in duration-75"
                                 class="absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-xl py-2 border border-gray-100 overflow-hidden">
                                <div class="px-5 py-3 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-green-100">
                                    <p class="text-xs text-primary-green font-bold">Tài khoản</p>
                                    <p class="text-sm font-bold text-gray-800 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                
                                <a href="{{ route('profile') }}" class="flex items-center px-5 py-3 text-sm text-gray-700 hover:bg-green-50 hover:text-primary-green transition">
                                    <i class="fas fa-user-circle w-5 mr-3 text-gray-400"></i> Hồ sơ của tôi
                                </a>
                                
                                <a href="#" class="flex items-center px-5 py-3 text-sm text-gray-700 hover:bg-green-50 hover:text-primary-green transition">
                                    <i class="fas fa-shopping-bag w-5 mr-3 text-gray-400"></i> Đơn hàng của tôi
                                </a>
                                
                                @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="flex items-center px-5 py-3 text-sm text-red-600 hover:bg-red-50 font-bold border-t border-b border-gray-100">
                                    <i class="fas fa-user-shield w-5 mr-3"></i> Trang quản trị
                                </a>
                                @endif

                                <form action="{{ route('logout') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="w-full text-left px-5 py-3 text-sm text-gray-600 hover:bg-gray-50 flex items-center transition">
                                        <i class="fas fa-sign-out-alt w-5 mr-3 text-gray-400"></i> Đăng xuất
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endguest
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center space-x-3">
                    <a href="#" class="relative p-2">
                        <i class="fas fa-shopping-bag text-gray-600 text-xl"></i>
                        <span class="absolute -top-1 -right-1 bg-primary-green text-white text-[10px] w-4 h-4 flex items-center justify-center rounded-full">3</span>
                    </a>
                    <button @click="mobileMenu = !mobileMenu" class="text-gray-800 text-2xl p-2 hover:bg-gray-100 rounded-lg">
                        <i class="fas" :class="mobileMenu ? 'fa-times' : 'fa-bars'"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div class="md:hidden bg-white border-t border-gray-100" x-show="mobileMenu" x-cloak x-transition>
            <div class="px-4 py-4 space-y-1">
                <a href="/" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-green-50 hover:text-primary-green font-semibold">
                    <i class="fas fa-home w-5"></i>
                    <span>Trang chủ</span>
                </a>
                <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-green-50 hover:text-primary-green font-semibold">
                    <i class="fas fa-apple-alt w-5"></i>
                    <span>Sản phẩm</span>
                </a>
                <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-green-50 hover:text-primary-green font-semibold">
                    <i class="fas fa-newspaper w-5"></i>
                    <span>Tin tức</span>
                </a>
                <a href="#" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-green-50 hover:text-primary-green font-semibold">
                    <i class="fas fa-phone-alt w-5"></i>
                    <span>Liên hệ</span>
                </a>
                
                <div class="border-t border-gray-100 my-4"></div>
                
                @guest
                    <div class="grid grid-cols-2 gap-3 px-4">
                        <a href="{{ route('login') }}" class="text-center py-3 text-gray-700 font-bold border-2 border-gray-200 rounded-xl hover:border-primary-green">Đăng nhập</a>
                        <a href="{{ route('register') }}" class="text-center py-3 bg-gradient-to-r from-primary-green to-dark-green text-white font-bold rounded-xl">Đăng ký</a>
                    </div>
                @else
                    <div class="px-4 py-3 bg-gray-50 rounded-xl">
                        <div class="flex items-center space-x-3">
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=28a745&color=fff" class="h-12 w-12 rounded-xl">
                            <div>
                                <p class="font-bold">{{ auth()->user()->name }}</p>
                                <p class="text-sm text-gray-500">{{ auth()->user()->email }}</p>
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('logout') }}" method="POST" class="px-4 mt-3">
                        @csrf
                        <button type="submit" class="w-full py-3 text-red-600 border-2 border-red-200 rounded-xl font-bold hover:bg-red-50">
                            <i class="fas fa-sign-out-alt mr-2"></i> Đăng xuất
                        </button>
                    </form>
                @endguest
            </div>
        </div>
    </nav>

    <main class="flex-grow">
        @if(session('error') || session('success'))
        <div class="max-w-7xl mx-auto px-4 mt-6">
            <div class="p-4 rounded-2xl border flex items-center {{ session('success') ? 'bg-green-50 border-green-100 text-green-700' : 'bg-red-50 border-red-100 text-red-700' }}">
                <i class="fas {{ session('success') ? 'fa-check-circle' : 'fa-exclamation-circle' }} mr-3 text-lg"></i>
                <span class="font-semibold text-sm">{{ session('success') ?? session('error') }}</span>
            </div>
        </div>
        @endif

        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-gradient-to-b from-slate-900 to-slate-950 text-slate-400 pt-16 pb-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Main Footer -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 pb-12 border-b border-slate-800">
                
                <!-- About -->
                <div class="space-y-4">
                    <div class="flex items-center space-x-2">
                        <div class="bg-gradient-to-br from-primary-green to-dark-green p-2 rounded-xl">
                            <i class="fas fa-leaf text-white text-xl"></i>
                        </div>
                        <span class="font-bold text-2xl text-white">Nông Sản Việt</span>
                    </div>
                    <p class="text-sm leading-relaxed">
                        Mang tinh hoa nông sản Việt Nam đến mọi nhà. Cam kết chất lượng, an toàn và tươi ngon từ những vùng đất màu mỡ.
                    </p>
                    <div class="flex space-x-3">
                        <a href="#" class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-primary-green hover:text-white transition-colors">
                            <i class="fab fa-facebook-f"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-primary-green hover:text-white transition-colors">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-primary-green hover:text-white transition-colors">
                            <i class="fab fa-youtube"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-slate-800 rounded-lg flex items-center justify-center hover:bg-primary-green hover:text-white transition-colors">
                            <i class="fab fa-tiktok"></i>
                        </a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Liên kết nhanh</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="#" class="flex items-center space-x-2 hover:text-primary-green transition-colors">
                                <i class="fas fa-chevron-right text-xs"></i>
                                <span>Giới thiệu</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center space-x-2 hover:text-primary-green transition-colors">
                                <i class="fas fa-chevron-right text-xs"></i>
                                <span>Sản phẩm</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center space-x-2 hover:text-primary-green transition-colors">
                                <i class="fas fa-chevron-right text-xs"></i>
                                <span>Tin tức</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center space-x-2 hover:text-primary-green transition-colors">
                                <i class="fas fa-chevron-right text-xs"></i>
                                <span>Khuyến mãi</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center space-x-2 hover:text-primary-green transition-colors">
                                <i class="fas fa-chevron-right text-xs"></i>
                                <span>Tuyển dụng</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Support -->
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Hỗ trợ khách hàng</h3>
                    <ul class="space-y-3">
                        <li>
                            <a href="#" class="flex items-center space-x-2 hover:text-primary-green transition-colors">
                                <i class="fas fa-chevron-right text-xs"></i>
                                <span>Hướng dẫn mua hàng</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center space-x-2 hover:text-primary-green transition-colors">
                                <i class="fas fa-chevron-right text-xs"></i>
                                <span>Chính sách đổi trả</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center space-x-2 hover:text-primary-green transition-colors">
                                <i class="fas fa-chevron-right text-xs"></i>
                                <span>Chính sách bảo mật</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center space-x-2 hover:text-primary-green transition-colors">
                                <i class="fas fa-chevron-right text-xs"></i>
                                <span>Điều khoản dịch vụ</span>
                            </a>
                        </li>
                        <li>
                            <a href="#" class="flex items-center space-x-2 hover:text-primary-green transition-colors">
                                <i class="fas fa-chevron-right text-xs"></i>
                                <span>FAQ</span>
                            </a>
                        </li>
                    </ul>
                </div>

                <!-- Contact -->
                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Thông tin liên hệ</h3>
                    <ul class="space-y-4">
                        <li class="flex items-start space-x-3">
                            <i class="fas fa-map-marker-alt mt-1 text-primary-green"></i>
                            <span class="text-sm">123 Đường Lúa, Phường X, Quận Y, TP. Hồ Chí Minh</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-phone-alt text-primary-green"></i>
                            <span class="text-sm">1900 1234 - 0909 123 456</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-envelope text-primary-green"></i>
                            <span class="text-sm">info@nongsanviet.com</span>
                        </li>
                        <li class="flex items-center space-x-3">
                            <i class="fas fa-clock text-primary-green"></i>
                            <span class="text-sm">8:00 - 21:00 (T2 - CN)</span>
                        </li>
                    </ul>

                    <!-- Newsletter -->
                    <div class="mt-6">
                        <h4 class="text-white font-semibold text-sm mb-2">Đăng ký nhận tin</h4>
                        <div class="flex">
                            <input type="email" placeholder="Email của bạn" 
                                   class="bg-slate-800 text-white text-sm px-4 py-2 rounded-l-lg w-full focus:outline-none focus:ring-1 focus:ring-primary-green">
                            <button class="bg-primary-green hover:bg-dark-green text-white px-4 py-2 rounded-r-lg transition">
                                <i class="fas fa-paper-plane"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Footer -->
            <div class="pt-8 flex flex-col md:flex-row justify-between items-center space-y-4 md:space-y-0">
                <p class="text-sm">
                    © 2024 Nông Sản Việt. Phát triển bởi <span class="text-primary-green">Nông Sản Việt Team</span>
                </p>
                <div class="flex items-center space-x-4">
                    <img src="https://via.placeholder.com/40x25?text=SSL" alt="SSL" class="h-6">
                    <img src="https://via.placeholder.com/40x25?text=ATM" alt="ATM" class="h-6">
                    <img src="https://via.placeholder.com/40x25?text=VISA" alt="VISA" class="h-6">
                    <img src="https://via.placeholder.com/40x25?text=MasterCard" alt="MasterCard" class="h-6">
                </div>
            </div>
        </div>
    </footer>

</body>
</html>