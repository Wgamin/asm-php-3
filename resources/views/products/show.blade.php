@extends('layouts.client')

@section('title', $product->name)

@section('content')
<div class="bg-slate-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <nav class="flex mb-6 text-sm text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-emerald-600">Trang chủ</a>
            <span class="mx-2">/</span>
            <a href="{{ route('products.index') }}" class="hover:text-emerald-600">Sản phẩm</a>
            <span class="mx-2">/</span>
            <span class="text-slate-800 font-medium line-clamp-1">{{ $product->name }}</span>
        </nav>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="flex flex-col lg:flex-row">
                
                <div class="lg:w-1/2 p-6 border-r border-slate-50">
                    <div class="sticky top-24">
                        <div class="aspect-square rounded-2xl overflow-hidden bg-slate-100 mb-4 border border-slate-100">
                            <img src="{{ asset('storage/' . $product->image) }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover hover:scale-105 transition duration-500">
                        </div>
                        {{-- Nếu bạn có album ảnh thì lặp ở đây --}}
                        <div class="grid grid-cols-4 gap-4">
                            <div class="aspect-square rounded-lg overflow-hidden border-2 border-emerald-500 cursor-pointer">
                                <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover">
                            </div>
                            </div>
                    </div>
                </div>

                <div class="lg:w-1/2 p-8 lg:p-12">
                    <div class="mb-6">
                        <span class="text-emerald-600 font-bold text-sm uppercase tracking-widest bg-emerald-50 px-3 py-1 rounded-full">
                            {{ $product->category->name ?? 'Nông sản sạch' }}
                        </span>
                        <h1 class="text-3xl lg:text-4xl font-extrabold text-slate-900 mt-4 mb-2 leading-tight">
                            {{ $product->name }}
                        </h1>
                        <div class="flex items-center gap-4 mb-4">
                            <div class="flex text-amber-400 text-sm">
                                <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                            </div>
                            <span class="text-slate-400 text-sm">(12 đánh giá từ khách hàng)</span>
                        </div>
                        <p class="text-3xl font-bold text-emerald-600">
                            {{ number_format($product->price, 0, ',', '.') }}đ
                        </p>
                    </div>

                    <div class="text-slate-600 leading-relaxed mb-8 border-t border-slate-100 pt-6">
                        <p>{{ $product->description }}</p>
                    </div>

                    <form action="#" method="POST" class="space-y-6">
                        @csrf
                        <div class="flex items-center gap-4">
                            <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden bg-slate-50">
                                <button type="button" class="px-4 py-3 hover:bg-slate-200 transition">-</button>
                                <input type="number" name="quantity" value="1" min="1" 
                                       class="w-16 text-center bg-transparent border-none focus:ring-0 font-bold">
                                <button type="button" class="px-4 py-3 hover:bg-slate-200 transition">+</button>
                            </div>
                            <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-4 rounded-xl shadow-lg shadow-emerald-100 transition flex items-center justify-center gap-3">
                                <i class="fas fa-shopping-cart"></i>
                                THÊM VÀO GIỎ HÀNG
                            </button>
                        </div>
                    </form>

                    <div class="mt-8 pt-6 border-t border-slate-100 space-y-2 text-sm">
                        <p><span class="text-slate-400 font-medium">Mã sản phẩm:</span> <span class="text-slate-800">NS-{{ $product->id + 1000 }}</span></p>
                        <p><span class="text-slate-400 font-medium">Danh mục:</span> <span class="text-slate-800">{{ $product->category->name }}</span></p>
                        <p><span class="text-slate-400 font-medium">Từ khóa:</span> <span class="text-slate-800">Sạch, VietGAP, Hữu cơ</span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
            <div class="border-b border-slate-100 mb-8">
                <nav class="flex gap-8">
                    <button class="border-b-2 border-emerald-600 pb-4 font-bold text-emerald-600">Mô tả chi tiết</button>
                    <button class="text-slate-400 pb-4 font-medium hover:text-slate-600">Đánh giá (12)</button>
                </nav>
            </div>
            <article class="prose max-w-none text-slate-600">
                {!! $product->content ?? 'Đang cập nhật nội dung chi tiết cho sản phẩm này...' !!}
            </article>
        </div>

        <div class="mt-16">
            <h2 class="text-2xl font-bold text-slate-900 mb-8 flex items-center gap-3">
                <span class="w-2 h-8 bg-emerald-600 rounded-full"></span>
                Sản phẩm tương tự
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                {{-- Giả sử biến $relatedProducts được truyền từ Controller --}}
                @foreach($relatedProducts as $item)
                    <x-product-card :product="$item" />
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection