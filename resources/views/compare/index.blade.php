@extends('layouts.client')

@section('title', 'So sánh sản phẩm')

@section('content')
<div class="bg-white min-h-screen py-16">
    <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">

        @php
            $count = $products->count();
            $cheapestId = $count ? optional($products->sortBy('price')->first())->id : null;
        @endphp

        {{-- Header tối giản --}}
        <div class="text-center mb-16">
            <h1 class="text-4xl md:text-5xl font-black text-slate-900 mb-4 tracking-tight">So sánh chi tiết</h1>
            <p class="text-slate-500 text-lg max-w-2xl mx-auto">Đối chiếu các thông số để tìm ra lựa chọn hoàn hảo nhất cho bạn.</p>

            @if($count > 0)
                <div class="mt-8 flex items-center justify-center gap-4">
                    <a href="{{ route('products.index') }}" class="px-6 py-3 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-800 font-semibold transition-all">
                        <i class="fas fa-plus mr-2"></i> Thêm sản phẩm
                    </a>
                    <form action="{{ route('compare.clear') }}" method="POST" class="inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-6 py-3 rounded-full bg-red-50 hover:bg-red-100 text-red-600 font-semibold transition-all">
                            Xóa tất cả
                        </button>
                    </form>
                </div>
            @endif
        </div>

        @if($count > 0)
            {{-- Bảng so sánh Minimalist --}}
            <div class="relative overflow-x-auto pb-10 custom-scrollbar">
                <table class="w-full text-left border-collapse table-fixed min-w-[800px]">
                    {{-- Chia tỷ lệ cột: Cột nhãn chiếm 1/4, các cột SP chia đều phần còn lại --}}
                    <colgroup>
                        <col class="w-1/4"> 
                        @for ($i = 0; $i < $count; $i++)
                            <col class="w-[{{ 75/$count }}%]"> 
                        @endfor
                    </colgroup>

                    {{-- Phần Header của Bảng: Ảnh, Tên, Giá, Nút Mua --}}
                    <thead class="align-bottom">
                        <tr>
                            <th class="pb-12 pl-4 border-b-2 border-slate-900 font-bold text-slate-900 text-lg align-bottom">
                                Thông số
                            </th>
                            @foreach($products as $product)
                                <th class="pb-12 px-6 border-b-2 border-slate-100 text-center relative group align-bottom">
                                    {{-- Nút Xóa (Chỉ hiện khi hover) --}}
                                    <form action="{{ route('compare.remove', $product->id) }}" method="POST" class="absolute top-0 right-6 z-20 opacity-0 group-hover:opacity-100 transition-opacity">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-8 h-8 rounded-full bg-slate-100 hover:bg-red-500 text-slate-400 hover:text-white flex items-center justify-center transition-colors shadow-sm">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </form>

                                    {{-- Badge Rẻ Nhất --}}
                                    @if($product->id === $cheapestId)
                                        <div class="absolute top-0 left-1/2 -translate-x-1/2 z-10">
                                            <span class="bg-emerald-100 text-emerald-700 text-[10px] font-black uppercase tracking-widest px-4 py-1.5 rounded-full border border-emerald-200 shadow-sm">
                                                Best Price
                                            </span>
                                        </div>
                                    @endif

                                    <a href="{{ route('product.detail', $product->id) }}" class="block text-center mt-8">
                                        {{-- Khung ảnh phá cách --}}
                                        <div class="w-40 h-40 mx-auto mb-6 relative">
                                            <div class="absolute inset-0 bg-slate-100 rounded-[2rem] transform rotate-3 scale-105 group-hover:rotate-6 transition-transform duration-300"></div>
                                            <img src="{{ $product->image ? asset('storage/' . $product->image) : asset('images/default-product.png') }}" 
                                                 alt="{{ $product->name }}" 
                                                 class="relative w-full h-full object-cover rounded-[2rem] shadow-sm group-hover:scale-105 transition-transform duration-500 bg-white">
                                        </div>
                                        <h3 class="text-lg font-extrabold text-slate-900 mb-2 line-clamp-2 h-14 hover:text-emerald-600 transition-colors">
                                            {{ $product->name }}
                                        </h3>
                                    </a>

                                    <div class="text-2xl font-black {{ $product->id === $cheapestId ? 'text-emerald-600' : 'text-slate-900' }} mb-6">
                                        {{ number_format($product->price, 0, ',', '.') }}<span class="text-sm font-medium text-slate-400 align-top relative top-1 ml-0.5">đ</span>
                                    </div>

                                    <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="w-full py-4 rounded-2xl {{ $product->id === $cheapestId ? 'bg-emerald-600 hover:bg-emerald-700 shadow-emerald-200' : 'bg-slate-900 hover:bg-slate-800 shadow-slate-200' }} text-white font-bold transition-all transform hover:-translate-y-1 shadow-lg flex items-center justify-center gap-2">
                                            <i class="fas fa-cart-plus"></i> Chọn mua
                                        </button>
                                    </form>
                                </th>
                            @endforeach
                        </tr>
                    </thead>

                    {{-- Phần Body của Bảng --}}
                    <tbody class="text-base text-slate-600">
                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-6 pl-4 font-semibold text-slate-900 border-b border-slate-100">
                                Nhóm hàng
                            </td>
                            @foreach($products as $product)
                                <td class="py-6 px-6 text-center border-b border-slate-100">
                                    <span class="text-sm font-medium text-slate-500 bg-slate-50 px-3 py-1 rounded-md">
                                        {{ $product->category->name ?? 'Nông sản' }}
                                    </span>
                                </td>
                            @endforeach
                        </tr>

                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-6 pl-4 font-semibold text-slate-900 border-b border-slate-100">
                                SKU
                            </td>
                            @foreach($products as $product)
                                <td class="py-6 px-6 text-center border-b border-slate-100 font-mono text-sm text-slate-400">
                                    NS-{{ $product->id + 1000 }}
                                </td>
                            @endforeach
                        </tr>

                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-6 pl-4 font-semibold text-slate-900 border-b border-slate-100 align-top">
                                Tóm tắt
                            </td>
                            @foreach($products as $product)
                                <td class="py-6 px-6 text-center border-b border-slate-100 align-top">
                                    <p class="text-slate-500 leading-relaxed text-sm">
                                        {{ $product->description ?: '—' }}
                                    </p>
                                </td>
                            @endforeach
                        </tr>

                        <tr class="hover:bg-slate-50/50 transition-colors">
                            <td class="py-6 pl-4 font-semibold text-slate-900 border-b border-slate-100 align-top">
                                Thông tin thêm
                            </td>
                            @foreach($products as $product)
                                <td class="py-6 px-6 text-center border-b border-slate-100 align-top">
                                    <p class="text-slate-500 line-clamp-3 text-sm leading-relaxed mb-3">
                                        {{ strip_tags($product->content) ?: '—' }}
                                    </p>
                                    <a href="{{ route('product.detail', $product->id) }}" class="inline-flex items-center gap-1 text-emerald-600 font-semibold hover:text-emerald-700 text-sm">
                                        Xem chi tiết <i class="fas fa-chevron-right text-[10px]"></i>
                                    </a>
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>

        @else
            {{-- Giao diện khi chưa có sản phẩm --}}
            <div class="text-center py-24">
                <div class="inline-flex items-center justify-center w-24 h-24 rounded-full bg-slate-50 text-slate-300 text-4xl mb-6 border border-slate-100">
                    <i class="fas fa-layer-group"></i>
                </div>
                <h2 class="text-2xl font-bold text-slate-900 mb-3">Chưa có sản phẩm nào</h2>
                <p class="text-slate-500 mb-8 max-w-md mx-auto">Tính năng này hoạt động tốt nhất khi bạn chọn từ 2 sản phẩm trở lên.</p>
                <a href="{{ route('products.index') }}" class="inline-flex items-center justify-center px-8 py-3.5 rounded-full bg-slate-900 hover:bg-slate-800 text-white font-bold transition-colors shadow-lg shadow-slate-200">
                    Bắt đầu mua sắm
                </a>
            </div>
        @endif
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { height: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
</style>
@endsection