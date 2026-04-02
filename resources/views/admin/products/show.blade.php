@extends('admin.layouts.master')

@section('content')
<div class="max-w-7xl mx-auto mt-8 px-4 pb-12" x-data="{ activeImg: '{{ asset('storage/' . $product->image) }}' }">
    {{-- Nút Quay lại --}}
    <div class="mb-6">
        <a href="{{ route('admin.products.index') }}" class="text-gray-500 hover:text-emerald-600 transition flex items-center gap-2 text-sm font-bold">
            <i class="fas fa-arrow-left"></i> QUAY LẠI DANH SÁCH
        </a>
    </div>

    <div class="bg-white rounded-[2rem] shadow-sm border border-gray-100 overflow-hidden">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-0">
            
            {{-- BÊN TRÁI: GALLERY HÌNH ẢNH (5 Cột) --}}
            <div class="lg:col-span-5 bg-gray-50/50 p-6 md:p-10 border-r border-gray-100">
                {{-- Ảnh lớn đang xem --}}
                <div class="sticky top-10 space-y-6">
                    <div class="relative aspect-square bg-white rounded-[1.5rem] shadow-xl shadow-gray-200/50 overflow-hidden border border-white">
                        <img :src="activeImg" class="w-full h-full object-cover transition duration-500 transform hover:scale-105">
                        
                        <div class="absolute top-4 left-4 flex flex-col gap-2">
                            <span class="bg-emerald-500 text-white px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-lg">
                                {{ $product->category->name }}
                            </span>
                            @if($product->isVariable())
                            <span class="bg-purple-600 text-white px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-lg">
                                Biến thể
                            </span>
                            @endif
                            
                            @if(!$product->isVariable() && $product->sale_price > 0 && $product->price > 0)
                                <span class="bg-red-500 text-white px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest shadow-lg">
                                    Giảm {{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- Danh sách ảnh phụ --}}
                    <div class="grid grid-cols-4 gap-3">
                        {{-- Thumbnail ảnh chính --}}
                        <button @click="activeImg = '{{ asset('storage/' . $product->image) }}'" 
                            class="aspect-square rounded-xl border-2 transition-all overflow-hidden bg-white"
                            :class="activeImg === '{{ asset('storage/' . $product->image) }}' ? 'border-emerald-500 ring-2 ring-emerald-100' : 'border-transparent opacity-60 hover:opacity-100'">
                            <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover">
                        </button>

                        {{-- Thumbnails ảnh phụ từ gallery --}}
                        @foreach($product->images as $img)
                        <button @click="activeImg = '{{ asset('storage/' . $img->image_path) }}'" 
                            class="aspect-square rounded-xl border-2 transition-all overflow-hidden bg-white"
                            :class="activeImg === '{{ asset('storage/' . $img->image_path) }}' ? 'border-emerald-500 ring-2 ring-emerald-100' : 'border-transparent opacity-60 hover:opacity-100'">
                            <img src="{{ asset('storage/' . $img->image_path) }}" class="w-full h-full object-cover">
                        </button>
                        @endforeach
                    </div>
                    <p class="text-[10px] text-gray-400 font-bold uppercase text-center tracking-widest">Click ảnh nhỏ hoặc ảnh biến thể để phóng to</p>
                </div>
            </div>

            {{-- BÊN PHẢI: THÔNG TIN CHI TIẾT (7 Cột) --}}
            <div class="lg:col-span-7 p-8 md:p-12 space-y-8">
                <div>
                    <div class="flex items-center gap-3 mb-4">
                        <span class="text-gray-400 font-bold text-xs tracking-tighter uppercase">Mã sản phẩm: #{{ $product->id }}{{ date('y') }}</span>
                        <span class="w-1.5 h-1.5 rounded-full bg-gray-300"></span>
                        <span class="text-emerald-600 font-bold text-xs uppercase">{{ $product->created_at->format('d/m/Y') }}</span>
                    </div>
                    <h1 class="text-4xl font-black text-gray-900 leading-[1.1] mb-4 uppercase">{{ $product->name }}</h1>
                    
                    <div class="inline-flex items-center bg-emerald-50 px-6 py-4 rounded-2xl border border-emerald-100 gap-3">
                        @if($product->isVariable())
                            <span class="text-emerald-600 font-bold">Giá từ:</span>
                            <span class="text-4xl font-black text-emerald-700">
                                {{ number_format($product->variants->min('price'), 0, ',', '.') }}đ
                            </span>
                        @else
                            @if($product->sale_price > 0)
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-gray-400 line-through">
                                        {{ number_format($product->price, 0, ',', '.') }}đ
                                    </span>
                                    <span class="text-4xl font-black text-emerald-700">
                                        {{ number_format($product->sale_price, 0, ',', '.') }}đ
                                    </span>
                                </div>
                            @else
                                <span class="text-4xl font-black text-emerald-700">
                                    {{ number_format($product->price, 0, ',', '.') }}đ
                                </span>
                            @endif
                        @endif
                    </div>
                </div>

                <div class="bg-gray-50 border-l-4 border-emerald-500 p-5 rounded-r-2xl">
                    <p class="text-gray-600 italic leading-relaxed">
                        {{ $product->description ?? 'Chưa có mô tả ngắn cho nông sản này.' }}
                    </p>
                </div>

                <hr class="border-gray-100">

                {{-- DANH SÁCH BIẾN THỂ --}}
                @if($product->isVariable())
                <div class="space-y-4">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Cấu hình biến thể</h3>
                        <span class="bg-gray-100 text-gray-500 px-3 py-1 rounded-full text-[10px] font-bold">{{ $product->variants->count() }} loại</span>
                    </div>
                    
                    <div class="overflow-hidden border border-gray-100 rounded-[1.5rem] shadow-sm">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-gray-50 text-gray-400 font-bold uppercase text-[10px] tracking-widest">
                                <tr>
                                    <th class="px-6 py-4">Ảnh</th>
                                    <th class="px-6 py-4">Phân loại</th>
                                    <th class="px-6 py-4">Mã SKU</th>
                                    <th class="px-6 py-4">Giá bán</th>
                                    <th class="px-6 py-4 text-center">Tồn kho</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @foreach($product->variants as $variant)
                                <tr class="hover:bg-emerald-50/30 transition-colors">
                                    {{-- Cột Ảnh Biến Thể --}}
                                    <td class="px-6 py-4">
                                        @if($variant->image)
                                            <button @click="activeImg = '{{ asset('storage/' . $variant->image) }}'" class="w-12 h-12 rounded-lg border border-gray-200 overflow-hidden hover:ring-2 hover:ring-emerald-400 transition-all">
                                                <img src="{{ asset('storage/' . $variant->image) }}" class="w-full h-full object-cover">
                                            </button>
                                        @else
                                            <div class="w-12 h-12 rounded-lg bg-gray-100 flex items-center justify-center text-gray-300">
                                                <i class="fas fa-image"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="flex flex-wrap gap-2">
                                            @foreach(is_string($variant->variant_values) ? json_decode($variant->variant_values, true) : $variant->variant_values as $key => $value)
                                                <span class="bg-white border border-gray-200 px-3 py-1 rounded-lg text-[11px] shadow-sm">
                                                    <span class="text-gray-400 font-medium">{{ $key }}:</span> 
                                                    <span class="text-gray-900 font-black">{{ $value }}</span>
                                                </span>
                                            @endforeach
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 font-mono text-xs text-gray-500">{{ $variant->sku }}</td>
                                    
                                    <td class="px-6 py-4">
                                        @if(isset($variant->sale_price) && $variant->sale_price > 0)
                                            <span class="block text-xs text-gray-400 line-through">{{ number_format($variant->price, 0, ',', '.') }}đ</span>
                                            <span class="font-black text-emerald-600">{{ number_format($variant->sale_price, 0, ',', '.') }}đ</span>
                                        @else
                                            <span class="font-black text-emerald-600">{{ number_format($variant->price, 0, ',', '.') }}đ</span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-center">
                                        <span class="px-3 py-1 rounded-full text-[11px] font-black {{ $variant->stock > 10 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $variant->stock }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @endif

                {{-- Nội dung bài viết --}}
                <div class="space-y-4">
                    <h3 class="text-sm font-black text-gray-800 uppercase tracking-widest">Mô tả chi tiết bài viết</h3>
                    <div class="prose prose-emerald max-w-none text-gray-700 leading-relaxed bg-white border border-gray-100 p-8 rounded-[1.5rem]">
                        {!! $product->content !!}
                    </div>
                </div>

                {{-- Nút thao tác --}}
                <div class="flex flex-col sm:flex-row gap-4 pt-6">
                    <a href="{{ route('admin.products.edit', $product->id) }}" 
                       class="flex-1 bg-gray-900 text-white text-center py-5 rounded-2xl font-black uppercase tracking-widest hover:bg-emerald-600 transition shadow-xl shadow-gray-200">
                        <i class="fas fa-edit mr-2"></i> Chỉnh sửa nông sản
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection