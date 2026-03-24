@extends('layouts.client')

@section('title', 'Trang chủ - Nông Sản Sạch')

@section('content')
    {{-- 1. Banner Section (Full Width hoặc Container) --}}
    <div class="bg-slate-50 py-6">
        <div class="container mx-auto px-4">
            <x-banner />
        </div>
    </div>

    {{-- 2. Features Section (Lợi ích khách hàng) --}}
    <div class="container mx-auto px-4 py-10">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
            <div class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm border border-slate-100">
                <div class="w-12 h-12 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center text-xl">
                    <i class="fas fa-shipping-fast"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 text-sm">Giao hàng nhanh</h4>
                    <p class="text-xs text-slate-500 text-12px">Trong vòng 2h nội thành</p>
                </div>
            </div>
            <div class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm border border-slate-100">
                <div class="w-12 h-12 bg-blue-100 text-blue-600 rounded-full flex items-center justify-center text-xl">
                    <i class="fas fa-certificate"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 text-sm">Chứng nhận VietGAP</h4>
                    <p class="text-xs text-slate-500 text-12px">An toàn tuyệt đối</p>
                </div>
            </div>
            <div class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm border border-slate-100">
                <div class="w-12 h-12 bg-amber-100 text-amber-600 rounded-full flex items-center justify-center text-xl">
                    <i class="fas fa-headset"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 text-sm">Hỗ trợ 24/7</h4>
                    <p class="text-xs text-slate-500 text-12px">Tận tâm phục vụ</p>
                </div>
            </div>
            <div class="flex items-center gap-4 p-4 bg-white rounded-xl shadow-sm border border-slate-100">
                <div class="w-12 h-12 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center text-xl">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <div>
                    <h4 class="font-bold text-slate-800 text-sm">Đổi trả dễ dàng</h4>
                    <p class="text-xs text-slate-500 text-12px">Trong vòng 24h</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 3. Main Products Section --}}
    <div class="container mx-auto px-4 pb-16">
        {{-- Header Section --}}
        <div class="flex justify-between items-end mb-8 border-b border-slate-100 pb-4">
            <div>
                <span class="text-emerald-600 font-bold text-sm uppercase tracking-wider">Lựa chọn tốt nhất</span>
                <h2 class="text-3xl font-bold text-slate-800 mt-1">Sản phẩm nổi bật</h2>
            </div>
            <a href="/san-pham" class="group text-emerald-600 font-semibold flex items-center hover:text-emerald-700 transition">
                Xem tất cả danh mục 
                <i class="fas fa-arrow-right ml-2 transform group-hover:translate-x-1 transition text-sm"></i>
            </a>
        </div>

        {{-- Product Grid --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8">
            @forelse($products as $product)
                <x-product-card :product="$product" />
            @empty
                <div class="col-span-full py-20 text-center bg-white rounded-3xl border border-dashed border-slate-200">
                    <div class="inline-flex items-center justify-center w-24 h-24 bg-slate-50 rounded-full mb-4 text-slate-200">
                        <i class="fas fa-box-open text-5xl"></i>
                    </div>
                    <h3 class="text-xl font-bold text-slate-800">Ối! Trống trơn rồi</h3>
                    <p class="text-slate-500 mt-2">Hiện chưa có sản phẩm nào được đăng bán. Quay lại sau nhé!</p>
                </div>
            @endforelse
        </div>

        {{-- 4. Newsletter/Banner phụ (Tùy chọn) --}}
        <div class="mt-20 bg-emerald-900 rounded-3xl p-8 md:p-16 relative overflow-hidden shadow-2xl">
            <div class="relative z-10 max-w-2xl">
                <h2 class="text-3xl md:text-4xl font-bold text-white mb-4">Đăng ký nhận ưu đãi giảm 20% cho đơn hàng đầu tiên</h2>
                <p class="text-emerald-100 mb-8 opacity-80">Đừng bỏ lỡ những đợt nông sản tươi ngon nhất vừa cập bến vườn.</p>
                <div class="flex flex-col sm:flex-row gap-3">
                    <input type="email" placeholder="Địa chỉ email của bạn..." class="flex-1 px-6 py-4 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400">
                    <button class="bg-emerald-500 hover:bg-emerald-400 text-white font-bold px-8 py-4 rounded-xl transition shadow-lg">Đăng ký ngay</button>
                </div>
            </div>
            {{-- Trang trí hình lá cây/nông sản mờ phía sau --}}
            <i class="fas fa-leaf absolute -right-10 -bottom-10 text-[200px] text-white opacity-5 rotate-12"></i>
        </div>
    </div>
@endsection