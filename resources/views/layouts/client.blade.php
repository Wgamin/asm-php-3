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
<header class="bg-white shadow sticky top-0 z-50">
    <!-- TẦNG 1: TOP INFO -->
    <div class="bg-gray-100 text-[12px] py-2 ">
        <div class="max-w-7xl mx-auto px-4 flex justify-between items-center text-gray-600 font-medium">
            <div class="flex items-center gap-4">
                <span class="flex items-center gap-2"><i class="fas fa-phone-alt text-primary-green"></i> Hotline: 1900 1234</span>
                <span class="hidden md:flex items-center gap-2 border-l pl-4 border-gray-300"><i class="fas fa-map-marker-alt text-primary-green"></i> 123 Đường Lúa, TP.HCM</span>
            </div>
            <div class="flex items-center gap-5">
                <a href="{{ route('news.index') }}" class="hover:text-primary-green transition">Tin tức</a>
                <a href="{{ route('contact') }}" class="hover:text-primary-green transition">Liên hệ</a>
                <div class="border-l h-4 border-gray-300"></div>
                @guest
                    <a href="{{ route('login') }}" class="hover:text-primary-green transition font-bold uppercase tracking-tight">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="text-primary-green hover:text-dark-green transition font-bold uppercase tracking-tight">Đăng ký</a>
                @else
                    <div class="relative group" x-data="{ open: false }">
                        <button @click="open = !open" @click.away="open = false" class="flex items-center gap-2 hover:text-primary-green transition">
                            <i class="fas fa-user-circle text-lg"></i>
                            <span class="font-bold uppercase tracking-tight">{{ auth()->user()->name }}</span>
                            <i class="fas fa-chevron-down text-[10px] transition" :class="open ? 'rotate-180' : ''"></i>
                        </button>
                        <div x-show="open" x-cloak x-transition class="absolute right-0 mt-2 w-56 bg-white shadow-2xl rounded-xl py-3 z-[60] border border-gray-100 overflow-hidden text-sm">
                            <div class="px-5 py-2 mb-2 bg-green-50 text-xs font-bold text-primary-green uppercase tracking-widest border-b border-green-100">Tài khoản cá nhân</div>
                            <a href="{{ route('profile') }}" class="block px-5 py-2.5 hover:bg-green-50 hover:text-primary-green transition"><i class="fas fa-id-badge w-5 mr-2 opacity-50"></i> Hồ sơ cá nhân</a>
                            @if(auth()->user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}" class="block px-5 py-2.5 bg-red-50 text-red-600 font-bold hover:bg-red-100 transition"><i class="fas fa-user-shield w-5 mr-2"></i> Quản trị viên</a>
                            @endif
                            <form action="{{ route('logout') }}" method="POST" class="border-t mt-2">
                                @csrf
                                <button type="submit" class="w-full text-left px-5 py-3 hover:bg-gray-50 text-gray-500 transition"><i class="fas fa-sign-out-alt w-5 mr-2 opacity-50"></i> Đăng xuất</button>
                            </form>
                        </div>
                    </div>
                @endguest
            </div>
        </div>
    </div>

    <!-- TẦNG 2: LOGO + SEARCH + CART -->
    <div class="bg-white">
        <div class="max-w-7xl mx-auto px-4 py-4  flex items-center justify-between gap-8">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group shrink-0">
                <div class="bg-gradient-to-br from-primary-green to-dark-green p-2 rounded-xl group-hover:scale-105 transition-transform duration-300">
                    <i class="fas fa-leaf text-white text-2xl"></i>
                </div>
                <div class="hidden sm:block">
                    <span class="font-black text-2xl tracking-tight text-gray-800 uppercase leading-none block">
                        Nông Sản <span class="text-primary-green">Việt</span>
                    </span>
                    <span class="text-[9px] uppercase tracking-[0.2em] font-bold text-gray-400 mt-1 block">Tinh hoa đất Việt</span>
                </div>
            </a>

            <!-- Search -->
            <form action="{{ route('products.index') }}" method="GET" class="flex-1 max-w-2xl relative group hidden md:flex">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Bạn muốn tìm thực phẩm gì hôm nay?" 
                    class="w-full border-2 border-gray-200 rounded-2xl px-6 py-3 text-sm focus:outline-none focus:border-primary-green transition-all bg-gray-50 focus:bg-white pr-16 shadow-sm group-hover:shadow-md">
                <button type="submit" class="absolute right-2 top-2 bottom-2 bg-primary-green hover:bg-dark-green text-white px-5 rounded-xl transition flex items-center shadow-lg shadow-green-100">
                    <i class="fas fa-search text-sm"></i>
                </button>
            </form>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                <!-- Wishlist (Optional if exists) -->
                @auth
                <a href="{{ route('wishlist.index') }}" class="p-3 bg-gray-50 hover:bg-red-50 text-gray-500 hover:text-red-500 rounded-2xl transition relative group">
                    <i class="fas fa-heart text-xl"></i>
                    <span class="absolute -top-1 -right-1 bg-red-500 text-white text-[10px] w-5 h-5 flex items-center justify-center rounded-full font-bold shadow-md">
                        {{ auth()->user()->wishlists()->count() }}
                    </span>
                </a>
                @endauth

                <!-- Cart -->
                <a href="{{ route('cart.index') }}" class="flex items-center gap-3 p-1 pr-5 bg-green-50 hover:bg-green-100 text-primary-green rounded-2xl transition group relative shadow-sm border border-green-100">
                    <div class="bg-primary-green text-black p-3 rounded-xl shadow-lg shadow-green-200 group-hover:scale-105 transition">
                        <i class="fas fa-shopping-basket text-xl"></i>
                    </div>
                    <div class="hidden sm:block">
                        <p class="text-[10px] uppercase font-bold opacity-60 leading-none">Giỏ hàng</p>
                        <p class="font-black text-sm text-gray-800 mt-1 leading-none">{{ session('cart') ? count(session('cart')) : 0 }} sản phẩm</p>
                    </div>
                    <!-- Badge Mobile -->
                    <span class="sm:hidden absolute -top-1 -right-1 bg-primary-green text-white text-[10px] w-5 h-5 flex items-center justify-center rounded-full font-bold shadow-md border-2 border-white">
                        {{ session('cart') ? count(session('cart')) : 0 }}
                    </span>
                </a>
                
                <!-- Mobile Menu Button -->
                <button @click="mobileMenu = !mobileMenu" class="md:hidden p-3 bg-gray-100 rounded-2xl text-gray-600">
                    <i class="fas" :class="mobileMenu ? 'fa-times' : 'fa-bars'"></i>
                </button>
            </div>
        </div>
    </div>

    <!-- TẦNG 3: MENU -->
    <div class="bg-white hidden md:block">
        <div class="max-w-7xl mx-auto px-4 flex justify-between">
            <nav class="flex items-center space-x-2">
                <a href="{{ route('home') }}" class="px-6 font-bold text-sm uppercase tracking-wider {{ request()->routeIs('home') ? 'text-primary-green border-b-4 border-primary-green bg-green-50/50' : 'text-gray-600 hover:text-primary-green hover:bg-gray-50' }} transition">
                    Trang chủ
                </a>

                <!-- MEGA DROPDOWN SẢN PHẨM -->
                <div class="relative group py-4">
                    <a href="{{ route('products.index') }}" class="px-6 font-bold text-sm uppercase tracking-wider {{ request()->routeIs('products.*') ? 'text-primary-green border-b-4 border-primary-green bg-green-50/50' : 'text-gray-600 hover:text-primary-green hover:bg-gray-50' }} transition inline-flex items-center gap-2">
                        Sản phẩm <i class="fas fa-chevron-down text-[10px] opacity-50 group-hover:rotate-180 transition"></i>
                    </a>

                    <!-- Vùng đệm và Dropdown nội dung -->
                    <div class="absolute left-0 top-full pt-0 w-[600px] hidden group-hover:block z-[100] transition-all">
                        <div class="bg-white shadow-2xl rounded-b-3xl border border-gray-100 grid grid-cols-2 p-6 gap-6">
                            @if(isset($categories) && count($categories) > 0)
                                @foreach($categories as $cat)
                                    <div>
                                        <a href="{{ route('products.index', ['category' => $cat->id]) }}" class="flex items-center gap-3 p-3 rounded-2xl hover:bg-green-50 group/cat transition">
                                            <div class="w-10 h-10 bg-gray-100 rounded-xl flex items-center justify-center text-gray-400 group-hover/cat:bg-white group-hover/cat:text-primary-green transition">
                                                <i class="fas fa-leaf"></i>
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800 text-sm group-hover/cat:text-primary-green">{{ $cat->name }}</p>
                                                <p class="text-[10px] text-gray-400 uppercase tracking-tighter">{{ $cat->children->count() }} loại khác nhau</p>
                                            </div>
                                        </a>
                                        @if($cat->children->count() > 0)
                                            <div class="ml-14 mt-1 flex flex-wrap gap-x-4 gap-y-1">
                                                @foreach($cat->children as $child)
                                                    <a href="{{ route('products.index', ['category' => $child->id]) }}" class="text-xs text-gray-500 hover:text-primary-green transition">• {{ $child->name }}</a>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @else
                                <div class="col-span-2 text-center py-4 text-gray-400 italic text-sm">Chưa có danh mục sản phẩm</div>
                            @endif
                        </div>
                    </div>
                </div>

                <a href="{{ route('news.index') }}" class="px-6 font-bold text-sm uppercase tracking-wider {{ request()->routeIs('news.*') ? 'text-primary-green border-b-4 border-primary-green bg-green-50/50' : 'text-gray-600 hover:text-primary-green hover:bg-gray-50' }} transition">
                    Tin tức
                </a>
                <a href="{{ route('about') }}" class="px-6 font-bold text-sm uppercase tracking-wider {{ request()->routeIs('about') ? 'text-primary-green border-b-4 border-primary-green bg-green-50/50' : 'text-gray-600 hover:text-primary-green hover:bg-gray-50' }} transition">
                    Giới thiệu
                </a>
                <a href="{{ route('contact') }}" class="px-6 font-bold text-sm uppercase tracking-wider {{ request()->routeIs('contact') ? 'text-primary-green border-b-4 border-primary-green bg-green-50/50' : 'text-gray-600 hover:text-primary-green hover:bg-gray-50' }} transition">
                    Liên hệ
                </a>
            </nav>
        </div>
    </div>

    <!-- MOBILE MENU SIDEBAR (Optional Enhancement) -->
    <div class="md:hidden bg-white border-t border-gray-100" x-show="mobileMenu" x-cloak x-transition>
        <div class="px-4 py-6 space-y-4">
            <!-- Mobile Search -->
            <form action="{{ route('products.index') }}" method="GET" class="relative">
                <input type="text" name="search" placeholder="Tìm kiếm..." class="w-full border-2 border-gray-100 rounded-xl px-4 py-2 text-sm focus:outline-none focus:border-primary-green">
                <button type="submit" class="absolute right-3 top-2.5 text-gray-400">
                    <i class="fas fa-search"></i>
                </button>
            </form>

            <nav class="flex flex-col gap-2">
                <a href="{{ route('home') }}" class="p-3 rounded-xl font-bold {{ request()->routeIs('home') ? 'bg-green-50 text-primary-green' : 'text-gray-600' }}">Trang chủ</a>
                <a href="{{ route('products.index') }}" class="p-3 rounded-xl font-bold {{ request()->routeIs('products.*') ? 'bg-green-50 text-primary-green' : 'text-gray-600' }}">Sản phẩm</a>
                <a href="{{ route('news.index') }}" class="p-3 rounded-xl font-bold {{ request()->routeIs('news.*') ? 'bg-green-50 text-primary-green' : 'text-gray-600' }}">Tin tức</a>
                <a href="{{ route('contact') }}" class="p-3 rounded-xl font-bold {{ request()->routeIs('contact') ? 'bg-green-50 text-primary-green' : 'text-gray-600' }}">Liên hệ</a>
            </nav>
        </div>
    </div>
</header>

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

    <div
        x-data="aiWidget({
            available: @js(filled(config('services.gemini.api_key'))),
            messagesUrl: @js(route('ai-chat.messages')),
            sendUrl: @js(route('ai-chat.send')),
            clearUrl: @js(route('ai-chat.clear')),
            csrfToken: @js(csrf_token()),
            supportUrl: @js(auth()->check() ? route('chat.index') : route('login')),
            authenticated: @js(auth()->check()),
        })"
        x-init="init()"
        class="fixed bottom-5 right-5 z-[70] flex flex-col items-end gap-3"
    >
        <template x-if="open">
            <div
                x-cloak
                x-transition
                class="w-[22rem] max-w-[calc(100vw-1.5rem)] overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl"
            >
                <div class="flex items-center justify-between border-b border-slate-100 bg-[linear-gradient(180deg,#f7faf8,#ffffff)] px-4 py-3">
                    <div class="flex items-center gap-3">
                        <span class="relative flex h-11 w-11 items-center justify-center rounded-2xl bg-[linear-gradient(180deg,#e9f7ef,#d4efe0)] text-emerald-700 shadow-sm ring-1 ring-emerald-100">
                            <span class="absolute inset-[5px] rounded-[1rem] border border-white/70"></span>
                            <i class="fas fa-robot relative text-base"></i>
                            <span class="absolute -right-1 -top-1 flex h-4 w-4 items-center justify-center rounded-full bg-slate-900 text-[8px] font-black text-white">AI</span>
                        </span>
                        <div>
                        <p class="text-sm font-bold text-slate-800">AI Chatbot</p>
                        <p class="text-[11px] text-slate-500">Hỏi nhanh về sản phẩm và mua sắm</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        <button
                            type="button"
                            @click="clearMessages()"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                            title="Xóa hội thoại"
                        >
                            <i class="fas fa-rotate-left text-xs"></i>
                        </button>
                        <button
                            type="button"
                            @click="open = false"
                            class="inline-flex h-8 w-8 items-center justify-center rounded-full bg-white text-slate-500 transition hover:bg-slate-100 hover:text-slate-700"
                            title="Đóng"
                        >
                            <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                </div>

                <div class="h-80 space-y-3 overflow-y-auto bg-white px-4 py-4" x-ref="messagesBox">
                    <template x-if="!available">
                        <div class="rounded-2xl bg-amber-50 px-4 py-3 text-sm leading-6 text-amber-700">
                            Gemini chưa được cấu hình API key.
                        </div>
                    </template>

                    <template x-if="available && messages.length === 0">
                        <div class="rounded-2xl bg-slate-50 px-4 py-3 text-sm leading-6 text-slate-500">
                            Bạn có thể hỏi về sản phẩm, cách mua hàng, đánh giá, giao hàng hoặc thanh toán.
                        </div>
                    </template>

                    <template x-for="(message, index) in messages" :key="index">
                        <div :class="message.role === 'user' ? 'flex justify-end' : 'flex justify-start'">
                            <div
                                class="max-w-[85%] rounded-2xl px-4 py-3 text-sm leading-6 shadow-sm"
                                :class="message.role === 'user'
                                    ? 'bg-emerald-600 text-white'
                                    : 'bg-slate-100 text-slate-700'"
                                x-text="message.text"
                            ></div>
                        </div>
                    </template>

                    <template x-if="loading">
                        <div class="flex justify-start">
                            <div class="rounded-2xl bg-slate-100 px-4 py-3 text-sm text-slate-500">
                                Đang trả lời...
                            </div>
                        </div>
                    </template>
                </div>

                <div class="border-t border-slate-100 bg-white p-3">
                    <form @submit.prevent="send()" class="flex items-end gap-2">
                        <textarea
                            x-model="draft"
                            rows="1"
                            maxlength="1000"
                            placeholder="Nhập câu hỏi..."
                            class="max-h-28 min-h-[44px] flex-1 resize-none rounded-2xl border border-slate-200 px-4 py-3 text-sm focus:border-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-100"
                        ></textarea>
                        <button
                            type="submit"
                            :disabled="loading || !draft.trim() || !available"
                            class="inline-flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-600 text-white transition hover:bg-emerald-700 disabled:cursor-not-allowed disabled:bg-slate-300"
                            title="Gửi"
                        >
                            <i class="fas fa-paper-plane text-sm"></i>
                        </button>
                    </form>
                </div>
            </div>
        </template>

        <div class="flex flex-col items-end gap-3">
            <a
                href="{{ auth()->check() ? route('chat.index') : route('login') }}"
                class="flex h-14 w-14 items-center justify-center rounded-full bg-emerald-600 text-white shadow-xl transition hover:-translate-y-0.5 hover:bg-emerald-700"
                title="{{ auth()->check() ? 'Chat hỗ trợ' : 'Đăng nhập để chat hỗ trợ' }}"
            >
                <i class="fas fa-headset text-lg"></i>
            </a>

            <button
                type="button"
                @click="toggle()"
                class="flex h-14 w-14 items-center justify-center rounded-full bg-emerald-600 text-white shadow-xl transition hover:-translate-y-0.5 hover:bg-emerald-700"
                title="Mở AI chatbot"
            >
                <i class="fas fa-robot text-lg"></i>
            </button>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function aiWidget(config) {
            return {
                open: false,
                loading: false,
                messages: [],
                draft: '',
                available: Boolean(config.available),
                messagesUrl: config.messagesUrl,
                sendUrl: config.sendUrl,
                clearUrl: config.clearUrl,
                csrfToken: config.csrfToken,

                normalizeMessage(message) {
                    return {
                        id: message?.id || String(Date.now()),
                        role: message?.role === 'model' ? 'assistant' : (message?.role || 'assistant'),
                        text: message?.text || message?.message || '',
                        created_at: message?.created_at || null,
                    };
                },

                normalizeMessages(messages) {
                    if (!Array.isArray(messages)) {
                        return [];
                    }

                    return messages
                        .map((message) => this.normalizeMessage(message))
                        .filter((message) => message.text);
                },

                init() {
                    if (this.available) {
                        this.fetchMessages();
                    }
                },

                toggle() {
                    this.open = !this.open;

                    if (this.open && this.available) {
                        this.fetchMessages();
                    }
                },

                async fetchMessages() {
                    try {
                        const response = await fetch(this.messagesUrl, {
                            headers: {
                                'Accept': 'application/json',
                            },
                            cache: 'no-store',
                        });

                        const data = await response.json();
                        this.available = Boolean(data.available ?? this.available);
                        this.messages = this.normalizeMessages(data.messages);
                        this.$nextTick(() => this.scrollToBottom());
                    } catch (error) {
                        console.error(error);
                    }
                },

                async send() {
                    const message = this.draft.trim();

                    if (!message || this.loading || !this.available) {
                        return;
                    }

                    this.loading = true;
                    this.messages.push(this.normalizeMessage({ role: 'user', text: message }));
                    this.draft = '';
                    this.$nextTick(() => this.scrollToBottom());

                    try {
                        const response = await fetch(this.sendUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                            body: JSON.stringify({ message }),
                        });

                        const data = await response.json();

                        if (!response.ok) {
                            throw new Error(data.message || 'Không thể gửi tin nhắn.');
                        }

                        if (Array.isArray(data.messages)) {
                            this.messages = this.normalizeMessages(data.messages);
                        } else if (data.message) {
                            this.messages.push(this.normalizeMessage(data.message));
                        }
                    } catch (error) {
                        this.messages.push({
                            role: 'assistant',
                            text: error.message || 'Hệ thống AI đang bận, vui lòng thử lại.',
                        });
                    } finally {
                        this.loading = false;
                        this.$nextTick(() => this.scrollToBottom());
                    }
                },

                async clearMessages() {
                    if (!this.available || this.loading) {
                        return;
                    }

                    try {
                        const response = await fetch(this.clearUrl, {
                            method: 'DELETE',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': this.csrfToken,
                            },
                        });

                        if (!response.ok) {
                            throw new Error('Không thể xóa hội thoại.');
                        }

                        this.messages = [];
                    } catch (error) {
                        console.error(error);
                    }
                },

                scrollToBottom() {
                    if (this.$refs.messagesBox) {
                        this.$refs.messagesBox.scrollTop = this.$refs.messagesBox.scrollHeight;
                    }
                },
            };
        }

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
