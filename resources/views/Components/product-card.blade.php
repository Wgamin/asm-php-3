@props(['product'])

<div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-xl transition-shadow group border border-gray-100 flex flex-col h-full">
    {{-- Phần Ảnh --}}
    <div class="relative overflow-hidden">
        <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/default-product.png') }}" 
             alt="{{ $product->name }}" 
             class="w-full h-48 object-cover group-hover:scale-110 transition duration-500">
        
        <span class="absolute top-2 right-2 bg-green-500/80 backdrop-blur-sm text-white text-[10px] px-2 py-1 rounded font-bold uppercase tracking-wider">
            {{ $product->category->name ?? 'Nông sản' }}
        </span>
    </div>

    {{-- Phần Nội dung --}}
    <div class="p-4 flex flex-col flex-1">
        <a href="{{ route('product.detail', $product->id) }}" class="hover:text-green-600 transition">
            <h3 class="font-bold text-lg text-gray-800 line-clamp-1 mb-1">{{ $product->name }}</h3>
        </a>
        
        <p class="text-sm text-gray-500 mb-2 line-clamp-2 flex-1">{{ $product->description }}</p>
        
        <div class="mt-auto">
            <div class="flex justify-between items-center mb-4">
                <span class="text-emerald-600 font-bold text-xl">
                    {{ number_format($product->price, 0, ',', '.') }}đ
                </span>
            </div>
        </div>
    </div>
</div>