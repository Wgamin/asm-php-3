@extends('layouts.client')

@section('title', 'Trang chủ - Nông Sản Sạch')

@section('content')
    <div class="relative bg-blue-500 from-primary-green to-dark-green rounded-2xl overflow-hidden mb-12">
        <div class="absolute inset-0 opacity-10">
            <svg class="w-full h-full" viewBox="0 0 100 100" preserveAspectRatio="none">
                <path d="M0 0 L100 100 M100 0 L0 100" stroke="white" stroke-width="0.5"/>
            </svg>
        </div>
        <div class="relative max-w-7xl mx-auto px-6 py-12 lg:px-8">
            <div class="flex flex-col md:flex-row items-center justify-between">
                <div class="text-center md:text-left mb-6 md:mb-0">
                    <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">Nông Sản Tươi Ngon</h1>
                    <p class="text-lg text-green-50">Từ trang trại đến bàn ăn</p>
                </div>
                <div>
                    <a href="#" class="inline-flex items-center bg-white text-primary-green px-6 py-3 rounded-lg font-semibold hover:bg-gray-100 transition shadow-md">
                        <i class="fas fa-leaf mr-2"></i> Mua sắm ngay <i class="fas fa-arrow-right ml-2 text-sm"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="flex justify-between items-center mb-8">
        <h2 class="text-2xl font-bold text-gray-800">Sản phẩm nổi bật</h2>
        <a href="#" class="text-primary-green hover:underline flex items-center">
            Xem tất cả <i class="fas fa-arrow-right ml-1 text-sm"></i>
        </a>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
        @forelse($products as $product)
            {{-- 
                Thay vì viết đống HTML dài dòng ở đây, 
                ta gọi component duy nhất và truyền biến $product vào 
            --}}
            <x-product-card :product="$product" />
            
        @empty
            {{-- Phần hiển thị khi không có sản phẩm nào --}}
            <div class="col-span-full py-20 text-center bg-white rounded-2xl border border-dashed border-slate-200">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-slate-50 rounded-full mb-4">
                    <i class="fas fa-box-open text-slate-300 text-64px"></i>
                </div>
                <p class="text-slate-400 font-medium">Hiện chưa có sản phẩm nào được đăng bán.</p>
            </div>
        @endforelse
    </div>
@endsection