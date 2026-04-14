@extends('layouts.client')

@section('title', 'Sản phẩm')

@section('content')
@php
    $selectedCategoryIds = collect(request('categories', []))
        ->map(fn ($id) => (int) $id)
        ->all();
@endphp

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        <form action="{{ route('products.index') }}" method="GET">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 sticky top-24">
                <div class="p-5 border-b border-slate-200">
                    <h3 class="font-bold text-lg text-slate-800 flex items-center justify-between">
                        <span><i class="fas fa-filter mr-2 text-emerald-600"></i> Bộ lọc</span>
                        <a href="{{ route('products.index') }}" class="text-sm text-emerald-600 hover:text-emerald-700">
                            Xóa tất cả
                        </a>
                    </h3>
                </div>

                <div class="p-5 space-y-6">
                    <div>
                        <h4 class="font-semibold text-slate-800 mb-3">Tìm kiếm</h4>
                        <input
                            type="text"
                            name="q"
                            value="{{ request('q') }}"
                            placeholder="Tên sản phẩm..."
                            class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500"
                        >
                    </div>

                    <div>
                        <h4 class="font-semibold text-slate-800 mb-3">Danh mục</h4>
                        <div class="space-y-2">
                            @foreach($categories as $category)
                                <label class="flex items-center cursor-pointer">
                                    <input
                                        type="checkbox"
                                        name="categories[]"
                                        value="{{ $category->id }}"
                                        {{ in_array($category->id, $selectedCategoryIds, true) ? 'checked' : '' }}
                                        class="w-4 h-4 text-emerald-600 rounded border-slate-300"
                                    >
                                    <span class="ml-3 text-slate-600">{{ $category->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <h4 class="font-semibold text-slate-800 mb-3 text-sm">Khoảng giá (VNĐ)</h4>
                        <div class="flex gap-2 flex-col">
                            <input
                                type="number"
                                name="min_price"
                                value="{{ request('min_price') }}"
                                placeholder="Từ"
                                class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500"
                            >
                            <input
                                type="number"
                                name="max_price"
                                value="{{ request('max_price') }}"
                                placeholder="Đến"
                                class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500"
                            >
                        </div>
                    </div>
                </div>

                <div class="p-5 border-t border-slate-200">
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-700 text-white py-2 rounded-lg transition font-bold">
                        Áp dụng bộ lọc
                    </button>
                </div>
            </div>
        </form>

        <div class="flex-1">
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 mb-6 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4 flex-wrap">
                    <p class="text-slate-600">
                        Hiển thị <span class="font-semibold text-slate-800">{{ $products->firstItem() ?? 0 }}-{{ $products->lastItem() ?? 0 }}</span> trong tổng số
                        <span class="font-semibold text-slate-800">{{ $products->total() }}</span> sản phẩm
                    </p>

                    <div class="flex flex-wrap gap-2">
                        @if(request('q'))
                            <span class="inline-flex items-center bg-emerald-50 text-emerald-700 text-xs px-3 py-1 rounded-full">
                                Từ khóa: {{ request('q') }}
                            </span>
                        @endif

                        @foreach($categories as $category)
                            @if(in_array($category->id, $selectedCategoryIds, true))
                                <span class="inline-flex items-center bg-emerald-50 text-emerald-700 text-xs px-3 py-1 rounded-full">
                                    {{ $category->name }}
                                </span>
                            @endif
                        @endforeach

                        @if(request('min_price') || request('max_price'))
                            <span class="inline-flex items-center bg-emerald-50 text-emerald-700 text-xs px-3 py-1 rounded-full">
                                {{ request('min_price') ? number_format((int) request('min_price'), 0, ',', '.') . 'đ' : '0đ' }}
                                -
                                {{ request('max_price') ? number_format((int) request('max_price'), 0, ',', '.') . 'đ' : 'không giới hạn' }}
                            </span>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex border border-slate-200 rounded-lg overflow-hidden">
                        <button type="button" class="px-3 py-2 bg-emerald-600 text-white">
                            <i class="fas fa-th"></i>
                        </button>
                        <button type="button" class="px-3 py-2 hover:bg-slate-50 text-slate-600">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>

                    <form action="{{ route('products.index') }}" method="GET">
                        @foreach((array) request('categories', []) as $categoryId)
                            <input type="hidden" name="categories[]" value="{{ $categoryId }}">
                        @endforeach
                        @if(request('q'))
                            <input type="hidden" name="q" value="{{ request('q') }}">
                        @endif
                        @if(request('min_price'))
                            <input type="hidden" name="min_price" value="{{ request('min_price') }}">
                        @endif
                        @if(request('max_price'))
                            <input type="hidden" name="max_price" value="{{ request('max_price') }}">
                        @endif

                        <select name="sort" onchange="this.form.submit()" class="px-4 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                            <option value="">Sắp xếp theo</option>
                            <option value="price_asc" {{ request('sort') === 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                            <option value="price_desc" {{ request('sort') === 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                            <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>Cũ nhất</option>
                        </select>
                    </form>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($products as $product)
                    <x-product-card :product="$product" />
                @empty
                    <div class="col-span-full py-20 text-center bg-white rounded-3xl border border-dashed border-slate-200">
                        <div class="inline-flex items-center justify-center w-24 h-24 bg-slate-50 rounded-full mb-4 text-slate-200">
                            <i class="fas fa-box-open text-5xl"></i>
                        </div>
                        <h3 class="text-xl font-bold text-slate-800">Không tìm thấy sản phẩm phù hợp</h3>
                        <p class="text-slate-500 mt-2">Thử thay đổi từ khóa hoặc bỏ bớt bộ lọc để xem thêm kết quả.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-10">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
