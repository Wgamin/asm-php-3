@props(['product'])

<div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-xl transition-all duration-300 group border border-gray-100 flex flex-col h-full relative">
    {{-- Phần Ảnh --}}
    <div class="relative overflow-hidden aspect-square">
        <a href="{{ route('product.detail', $product->id) }}">
            <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/default-product.png') }}"
                alt="{{ $product->name }}"
                class="w-full h-full object-cover group-hover:scale-110 transition duration-700">
        </a>

        {{-- Nhãn Danh mục --}}
        <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-emerald-700 text-[10px] px-2 py-1 rounded-lg font-bold uppercase tracking-wider shadow-sm">
            {{ $product->category->name ?? 'Nông sản' }}
        </span>



        {{-- NÚT WISHLIST (Cập nhật vị trí & Style) --}}
        @php
        $isFavorite = false;
        if(auth()->check()) {
        $isFavorite = auth()->user()->wishlists->contains($product->id);
        }
        @endphp
        <button type="button"
            class="toggle-wishlist absolute top-3 right-3 w-9 h-9 flex items-center justify-center bg-white/90 backdrop-blur-sm rounded-full shadow-sm hover:bg-red-50 transition-colors duration-300 z-10"
            data-id="{{ $product->id }}">
            <i class="{{ $isFavorite ? 'fas text-red-500' : 'far text-gray-400' }} fa-heart transition-transform active:scale-125"></i>
        </button>

    </div>

    {{-- Phần Nội dung --}}
    <div class="p-4 flex flex-col flex-1">
        <div class="mb-2">
            <a href="{{ route('product.detail', $product->id) }}" class="hover:text-emerald-600 transition-colors">
                <h3 class="font-bold text-gray-800 line-clamp-1 text-base">{{ $product->name }}</h3>
            </a>
            <p class="text-xs text-gray-500 line-clamp-2 mt-1 min-h-[32px]">{{ $product->description }}</p>
        </div>

        <div class="mt-auto pt-3 flex items-center justify-between">
            <div class="flex flex-col">
                <span class="text-emerald-600 font-extrabold text-lg">
                    {{ number_format($product->price, 0, ',', '.') }}đ
                </span>
            </div>

            {{-- Nút Thêm nhanh vào giỏ --}}
            <form action="{{ route('cart.add', $product->id) }}" method="POST">
                @csrf
                <button type="submit" class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-600 hover:text-white transition-all duration-300 flex items-center justify-center">
                    <i class="fas fa-plus text-sm"></i>
                </button>
            </form>
            <form action="{{ route('compare.add', $product->id) }}" method="POST">
                @csrf
                <button type="submit"
                    class="w-10 h-10 bg-slate-50 text-slate-600 rounded-xl hover:bg-slate-700 hover:text-white transition-all duration-300 flex items-center justify-center"
                    title="So sánh sản phẩm">
                    <i class="fas fa-balance-scale text-sm"></i>
                </button>
            </form>
        </div>
    </div>
</div>