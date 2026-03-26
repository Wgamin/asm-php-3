@extends('layouts.client')

@section('title', 'Sản phẩm')

@section('content')

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="flex flex-col lg:flex-row gap-8">
        
        <!-- Sidebar Filters -->
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
                <h4 class="font-semibold text-slate-800 mb-3">Danh mục</h4>
                <div class="space-y-2">
                    @foreach($categories as $cat)
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" name="categories[]" value="{{ $cat->id }}" 
                                {{ is_array(request('categories')) && in_array($cat->id, request('categories')) ? 'checked' : '' }}
                                class="w-4 h-4 text-emerald-600 rounded border-slate-300">
                            <span class="ml-3 text-slate-600">{{ $cat->name }}</span>
                        </label>
                    @endforeach
                </div>
            </div>
            
            <div>
                <h4 class="font-semibold text-slate-800 mb-3 text-sm">Khoảng giá (VNĐ)</h4>
                <div class="flex gap-2 flex-col ">
                    <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Từ" 
                           class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500">
                    <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Đến" 
                           class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm focus:ring-2 focus:ring-emerald-500">
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
        
        <!-- Product List -->
        <div class="flex-1">
            <!-- Top bar -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-4 mb-6 flex flex-wrap items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <p class="text-slate-600">
                        Hiển thị <span class="font-semibold text-slate-800">1-12</span> trong tổng số 
                        <span class="font-semibold text-slate-800">245</span> sản phẩm
                    </p>
                    
                    <!-- Active filters -->
                    <div class="flex flex-wrap gap-2">
                        <span class="inline-flex items-center bg-emerald-50 text-emerald-700 text-xs px-3 py-1 rounded-full">
                            Trái cây
                            <i class="fas fa-times ml-2 cursor-pointer hover:text-emerald-900"></i>
                        </span>
                        <span class="inline-flex items-center bg-emerald-50 text-emerald-700 text-xs px-3 py-1 rounded-full">
                            50.000đ - 100.000đ
                            <i class="fas fa-times ml-2 cursor-pointer hover:text-emerald-900"></i>
                        </span>
                    </div>
                </div>
                
                <div class="flex items-center gap-3">
                    <!-- View mode -->
                    <div class="flex border border-slate-200 rounded-lg overflow-hidden">
                        <button class="px-3 py-2 bg-emerald-600 text-white">
                            <i class="fas fa-th"></i>
                        </button>
                        <button class="px-3 py-2 hover:bg-slate-50 text-slate-600">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                    
                    <!-- Sort -->
                    <select class="px-4 py-2 border border-slate-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500">
                        <option>Sắp xếp theo</option>
                        <option>Mới nhất</option>
                        <option>Giá tăng dần</option>
                        <option>Giá giảm dần</option>
                        <option>Bán chạy</option>
                        <option>Đánh giá cao</option>
                    </select>
                </div>
            </div>
            
            <!-- Products Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($products as $product)
                    {{-- Gọi component và truyền biến product vào --}}
                    <x-product-card :product="$product" />
                @endforeach
            </div>
            <div class="mt-10">
                {{ $products->links() }}
            </div>
            
            <!-- Pagination -->
            <div class="mt-10 flex justify-center">
                {{-- Chỉ cần một dòng này là đủ --}}
                <x-pagination :items="$products" />
            </div>
        </div>
    </div>
</div>


@push('scripts')
<script>
    // Clear filters
    document.getElementById('clearFilters')?.addEventListener('click', function() {
        document.querySelectorAll('input[type="checkbox"], input[type="radio"]').forEach(input => {
            input.checked = false;
        });
    });
</script>
@endpush
@endsection