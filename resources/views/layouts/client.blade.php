<!DOCTYPE html>
<html lang="vi">
<head>
    @php
        $pageTitle = trim($__env->yieldContent('meta_title', $__env->yieldContent('title', 'Nông Sản Việt')));
        $metaDescription = trim($__env->yieldContent('meta_description', 'Nông Sản Việt cung cấp nông sản sạch, tin tức thị trường, mẹo chọn thực phẩm và ưu đãi mới nhất.'));
        $metaKeywords = trim($__env->yieldContent('meta_keywords', 'nông sản sạch, tin tức nông sản, thực phẩm sạch, nông sản Việt'));
        $canonicalUrl = trim($__env->yieldContent('canonical', url()->current()));
        $metaType = trim($__env->yieldContent('meta_type', 'website'));
        $metaImage = trim($__env->yieldContent('meta_image', ''));
        $jsonLd = trim($__env->yieldContent('json_ld', ''));
    @endphp

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $pageTitle }}</title>
    <meta name="description" content="{{ $metaDescription }}">
    <meta name="keywords" content="{{ $metaKeywords }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    <meta property="og:locale" content="vi_VN">
    <meta property="og:type" content="{{ $metaType }}">
    <meta property="og:title" content="{{ $pageTitle }}">
    <meta property="og:description" content="{{ $metaDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:site_name" content="Nông Sản Việt">

    <meta name="twitter:card" content="{{ $metaImage !== '' ? 'summary_large_image' : 'summary' }}">
    <meta name="twitter:title" content="{{ $pageTitle }}">
    <meta name="twitter:description" content="{{ $metaDescription }}">

    @if($metaImage !== '')
        <meta property="og:image" content="{{ $metaImage }}">
        <meta name="twitter:image" content="{{ $metaImage }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>

    <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'Nông Sản Việt',
            'url' => url('/'),
            'contactPoint' => [
                '@type' => 'ContactPoint',
                'telephone' => '19001234',
                'contactType' => 'customer service',
                'areaServed' => 'VN',
                'availableLanguage' => ['vi'],
            ],
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
    </script>

    @if($jsonLd !== '')
        <script type="application/ld+json">{!! $jsonLd !!}</script>
    @endif
</head>
<body class="bg-slate-50 font-sans text-gray-900 flex flex-col min-h-screen">
    <nav class="bg-white border-b border-gray-100 shadow-sm sticky top-0 z-50" x-data="{ mobileMenu: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-20">
                <div class="flex items-center gap-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
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
                        <a href="{{ route('about') }}" class="px-4 py-2 rounded-lg text-gray-600 hover:text-primary-green hover:bg-green-50 font-semibold transition flex items-center space-x-1">
                            <i class="fas fa-info-circle text-sm"></i>
                            <span>Giới thiệu</span>
                        </a>
                    </div>
                </div>

                <div class="hidden md:flex items-center space-x-4">
                    <a href="{{ route('compare.index') }}" class="relative p-2 hover:bg-gray-100 rounded-full group">
                        <i class="fas fa-scale-balanced text-gray-500 group-hover:text-primary-green text-lg"></i>
                        <span class="absolute -top-1 -right-1 bg-sky-500 text-white text-[10px] min-w-4 h-4 px-1 flex items-center justify-center rounded-full">
                            {{ count(session('compare', [])) }}
                        </span>
                    </a>

                    <a href="{{ route('checkout') }}" class="relative p-2 hover:bg-gray-100 rounded-full group">
                        <i class="fas fa-shopping-bag text-gray-500 group-hover:text-primary-green text-lg"></i>
                        <span class="absolute -top-1 -right-1 bg-primary-green text-white text-[10px] w-4 h-4 flex items-center justify-center rounded-full">
                            {{ session('cart') ? count(session('cart')) : 0 }}
                        </span>
                    </a>

                    @guest
                        <div class="flex items-center gap-3">
                            <a href="{{ route('login') }}" class="text-gray-700 hover:text-primary-green text-sm font-bold px-4 py-2 rounded-lg hover:bg-gray-50">Đăng nhập</a>
                            <a href="{{ route('register') }}" class="bg-[#28a745] hover:bg-[#23913c] text-white px-6 py-2.5 rounded-xl text-sm font-bold shadow-md transition">Đăng ký</a>
                        </div>
                    @else
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" @click.away="open = false"
                                    class="flex items-center gap-2 bg-gradient-to-r from-gray-50 to-gray-100 hover:from-gray-100 hover:to-gray-200 text-gray-800 px-3 py-1.5 rounded-xl border border-gray-200 transition">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=28a745&color=fff&bold=true" class="h-8 w-8 rounded-lg" alt="{{ auth()->user()->name }}">
                                <div class="text-left">
                                    <p class="font-bold text-sm">{{ auth()->user()->name }}</p>
                                    <p class="text-[10px] text-gray-400">{{ auth()->user()->role === 'admin' ? 'Quản trị viên' : 'Thành viên' }}</p>
                                </div>
                                <i class="fas fa-chevron-down text-[10px] transition ml-2" :class="open ? 'rotate-180' : ''"></i>
                            </button>

                            <div x-show="open" x-cloak x-transition class="absolute right-0 mt-3 w-64 bg-white rounded-2xl shadow-xl py-2 border border-gray-100 overflow-hidden">
                                <div class="px-5 py-3 bg-gradient-to-r from-green-50 to-emerald-50 border-b border-green-100">
                                    <p class="text-xs text-primary-green font-bold">Tài khoản</p>
                                    <p class="text-sm font-bold text-gray-800 truncate">{{ auth()->user()->email }}</p>
                                </div>

                                <a href="{{ route('profile') }}" class="flex items-center px-5 py-3 text-sm text-gray-700 hover:bg-green-50 hover:text-primary-green transition">
                                    <i class="fas fa-user-circle w-5 mr-3 text-gray-400"></i> Hồ sơ của tôi
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

                <div class="md:hidden flex items-center space-x-3">
                    <a href="{{ route('compare.index') }}" class="relative p-2">
                        <i class="fas fa-scale-balanced text-gray-600 text-xl"></i>
                        <span class="absolute -top-1 -right-1 bg-sky-500 text-white text-[10px] min-w-4 h-4 px-1 flex items-center justify-center rounded-full">
                            {{ count(session('compare', [])) }}
                        </span>
                    </a>
                    <a href="{{ route('checkout') }}" class="relative p-2">
                        <i class="fas fa-shopping-bag text-gray-600 text-xl"></i>
                        <span class="absolute -top-1 -right-1 bg-primary-green text-white text-[10px] w-4 h-4 flex items-center justify-center rounded-full">
                            {{ session('cart') ? count(session('cart')) : 0 }}
                        </span>
                    </a>
                    <button @click="mobileMenu = !mobileMenu" class="text-gray-800 text-2xl p-2 hover:bg-gray-100 rounded-lg">
                        <i class="fas" :class="mobileMenu ? 'fa-times' : 'fa-bars'"></i>
                    </button>
                </div>
            </div>
        </div>

        <div class="md:hidden bg-white border-t border-gray-100" x-show="mobileMenu" x-cloak x-transition>
            <div class="px-4 py-4 space-y-1">
                <a href="{{ route('home') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-green-50 hover:text-primary-green font-semibold">
                    <i class="fas fa-home w-5"></i>
                    <span>Trang chủ</span>
                </a>
                <a href="{{ route('products.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-green-50 hover:text-primary-green font-semibold">
                    <i class="fas fa-apple-alt w-5"></i>
                    <span>Sản phẩm</span>
                </a>
                <a href="{{ route('compare.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-green-50 hover:text-primary-green font-semibold">
                    <i class="fas fa-scale-balanced w-5"></i>
                    <span>So sÃ¡nh</span>
                </a>
                <a href="{{ route('news.index') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-green-50 hover:text-primary-green font-semibold">
                    <i class="fas fa-newspaper w-5"></i>
                    <span>Tin tức</span>
                </a>
                <a href="{{ route('contact') }}" class="flex items-center space-x-3 px-4 py-3 rounded-xl text-gray-700 hover:bg-green-50 hover:text-primary-green font-semibold">
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
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(auth()->user()->name) }}&background=28a745&color=fff" class="h-12 w-12 rounded-xl" alt="{{ auth()->user()->name }}">
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

    <footer class="bg-gradient-to-b from-slate-900 to-slate-950 text-slate-400 pt-16 pb-8 mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 pb-12 border-b border-slate-800">
                <div class="space-y-4">
                    <div class="flex items-center gap-3">
                        <div class="bg-gradient-to-br from-primary-green to-dark-green p-2 rounded-xl">
                            <i class="fas fa-leaf text-white text-xl"></i>
                        </div>
                        <span class="font-bold text-2xl text-white">Nông Sản Việt</span>
                    </div>
                    <p class="text-sm leading-relaxed">
                        Mang tinh hoa nông sản Việt Nam đến mọi nhà. Cập nhật sản phẩm mới, bài viết tư vấn và kiến thức chọn thực phẩm sạch mỗi ngày.
                    </p>
                </div>

                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Liên kết nhanh</h3>
                    <ul class="space-y-3 text-sm">
                        <li><a href="{{ route('home') }}" class="hover:text-primary-green transition-colors">Trang chủ</a></li>
                        <li><a href="{{ route('products.index') }}" class="hover:text-primary-green transition-colors">Sản phẩm</a></li>
                        <li><a href="{{ route('news.index') }}" class="hover:text-primary-green transition-colors">Tin tức</a></li>
                        <li><a href="{{ route('contact') }}" class="hover:text-primary-green transition-colors">Liên hệ</a></li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-white font-bold text-lg mb-4">Thông tin liên hệ</h3>
                    <ul class="space-y-4 text-sm">
                        <li class="flex items-start gap-3">
                            <i class="fas fa-map-marker-alt mt-1 text-primary-green"></i>
                            <span>123 Đường Lúa, Phường X, Quận Y, TP. Hồ Chí Minh</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-phone-alt text-primary-green"></i>
                            <span>1900 1234 - 0909 123 456</span>
                        </li>
                        <li class="flex items-center gap-3">
                            <i class="fas fa-envelope text-primary-green"></i>
                            <span>info@nongsanviet.com</span>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="pt-8 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-sm">© 2026 Nông Sản Việt. Phát triển nội dung và thương mại số cho nông sản sạch.</p>
                <div class="text-sm text-slate-500">Tin tức chuẩn SEO, sản phẩm rõ nguồn gốc, mua sắm an tâm.</div>
            </div>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).on('click', '.toggle-wishlist', function (e) {
            e.preventDefault();

            let btn = $(this);
            let productId = btn.data('id');
            let icon = btn.find('i');

            $.ajax({
                url: "/wishlist/toggle/" + productId,
                method: "POST",
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function (response) {
                    if (response.status === 'added') {
                        icon.removeClass('far text-gray-400').addClass('fas text-red-500');
                    } else {
                        icon.removeClass('fas text-red-500').addClass('far text-gray-400');
                    }
                },
                error: function (xhr) {
                    if (xhr.status === 401) {
                        alert('Vui lòng đăng nhập để thực hiện!');
                        window.location.href = "{{ route('login') }}";
                    } else {
                        console.error(xhr.responseText);
                        alert('Lỗi hệ thống, kiểm tra console log!');
                    }
                }
            });
        });
    </script>
    @stack('scripts')
</body>
</html>
