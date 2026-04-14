@props(['product'])

@php
    $isFavorite = false;
    $compareIds = session('compare', []);
    $isCompared = in_array($product->id, $compareIds, true);

    if (auth()->check()) {
        $isFavorite = auth()->user()->wishlists->contains($product->id);
    }

    if ($product->isVariable() && $product->variants->count() > 0) {
        $minVariantPrice = $product->variants->min(function ($variant) {
            return $variant->sale_price > 0 ? $variant->sale_price : $variant->price;
        });
    }
@endphp

<div class="bg-white rounded-2xl shadow-sm overflow-hidden hover:shadow-xl transition-all duration-300 group border border-gray-100 flex flex-col h-full relative">
    <div class="relative overflow-hidden aspect-square">
        <a href="{{ route('product.detail', $product->id) }}">
            <img
                src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/default-product.png') }}"
                alt="{{ $product->name }}"
                class="w-full h-full object-cover group-hover:scale-110 transition duration-700"
            >
        </a>

        <span class="absolute top-3 left-3 bg-white/90 backdrop-blur-sm text-emerald-700 text-[10px] px-2 py-1 rounded-lg font-bold uppercase tracking-wider shadow-sm">
            {{ $product->category->name ?? 'Nông sản' }}
        </span>

        <button
            type="button"
            class="toggle-wishlist absolute top-3 right-3 w-9 h-9 flex items-center justify-center bg-white/90 backdrop-blur-sm rounded-full shadow-sm hover:bg-red-50 transition-colors duration-300 z-10"
            data-id="{{ $product->id }}"
        >
            <i class="{{ $isFavorite ? 'fas text-red-500' : 'far text-gray-400' }} fa-heart transition-transform active:scale-125"></i>
        </button>

        @if($isCompared)
            <a
                href="{{ route('compare.index') }}"
                class="absolute top-14 right-3 w-9 h-9 flex items-center justify-center bg-white/90 backdrop-blur-sm rounded-full shadow-sm text-emerald-600 hover:bg-emerald-50 transition-colors duration-300 z-10"
                title="Đang có trong danh sách so sánh"
            >
                <i class="fas fa-scale-balanced"></i>
            </a>
        @else
            <form action="{{ route('compare.add', $product->id) }}" method="POST" class="absolute top-14 right-3 z-10">
                @csrf
                <button
                    type="submit"
                    class="w-9 h-9 flex items-center justify-center bg-white/90 backdrop-blur-sm rounded-full shadow-sm text-gray-400 hover:bg-sky-50 hover:text-sky-600 transition-colors duration-300"
                    title="Thêm vào so sánh"
                >
                    <i class="fas fa-scale-balanced"></i>
                </button>
            </form>
        @endif
    </div>

    <div class="p-4 flex flex-col flex-1">
        <div class="mb-2">
            <a href="{{ route('product.detail', $product->id) }}" class="hover:text-emerald-600 transition-colors">
                <h3 class="font-bold text-gray-800 line-clamp-1 text-base">{{ $product->name }}</h3>
            </a>
            <p class="text-xs text-gray-500 line-clamp-2 mt-1 min-h-[32px]">{{ $product->description }}</p>
        </div>

        <div class="mt-auto pt-3 flex items-center justify-between">
            <div class="flex flex-col">
                @if($product->isVariable() && $product->variants->count() > 0)
                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">Giá từ</span>
                    <span class="text-emerald-600 font-extrabold text-lg">
                        {{ number_format($minVariantPrice ?? 0, 0, ',', '.') }}đ
                    </span>
                @else
                    <span class="text-[10px] text-transparent select-none">Giá</span>
                    <span class="text-emerald-600 font-extrabold text-lg">
                        {{ number_format($product->sale_price > 0 ? $product->sale_price : $product->price, 0, ',', '.') }}đ
                    </span>
                @endif
            </div>

            @if($product->isVariable())
                <a href="{{ route('product.detail', $product->id) }}" class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-600 hover:text-white transition-all duration-300 flex items-center justify-center">
                    <i class="fas fa-eye text-sm"></i>
                </a>
            @else
                <form action="{{ route('cart.add', $product->id) }}" method="POST">
                    @csrf
                    <button type="submit" class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl hover:bg-emerald-600 hover:text-white transition-all duration-300 flex items-center justify-center">
                        <i class="fas fa-plus text-sm"></i>
                    </button>
                </form>
            @endif
        </div>
    </div>
</div>
