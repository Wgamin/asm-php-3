@extends('layouts.client')

@section('content')
<div class="bg-gray-50 py-12">
    <div class="container mx-auto px-4">
        <h1 class="text-3xl font-bold text-gray-800 mb-8 flex items-center">
            <i class="fas fa-heart text-red-500 mr-3"></i> Danh sach yeu thich cua toi
        </h1>

        @if($wishlist->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
                @foreach($wishlist as $product)
                    <div class="bg-white rounded-2xl shadow-sm overflow-hidden border border-gray-100 transition hover:shadow-md product-card" data-id="{{ $product->id }}">
                        <div class="relative">
                            <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-48 object-cover">
                            <button class="absolute top-3 right-3 bg-white/80 backdrop-blur-sm p-2 rounded-full text-red-500 hover:bg-red-500 hover:text-white transition toggle-wishlist" data-id="{{ $product->id }}">
                                <i class="fas fa-heart"></i>
                            </button>
                        </div>
                        <div class="p-4">
                            <h3 class="font-bold text-gray-800 mb-2">{{ $product->name }}</h3>
                            <div class="flex justify-between items-center">
                                <span class="text-emerald-600 font-bold">{{ number_format($product->price) }}d</span>
                                @if($product->isVariable())
                                    <a href="{{ route('product.detail', $product->id) }}" class="text-gray-400 hover:text-emerald-500">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                @else
                                    <form action="{{ route('cart.add', $product->id) }}" method="POST">
                                        @csrf
                                        <button class="text-gray-400 hover:text-emerald-500"><i class="fas fa-shopping-cart"></i></button>
                                    </form>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-20 bg-white rounded-3xl shadow-sm">
                <i class="far fa-heart text-gray-200 text-6xl mb-4"></i>
                <p class="text-gray-500 text-lg">Danh sach yeu thich cua ban dang trong.</p>
                <a href="{{ route('home') }}" class="inline-block mt-6 bg-emerald-500 text-white px-8 py-3 rounded-xl font-bold hover:bg-emerald-600 transition">Kham pha san pham</a>
            </div>
        @endif
    </div>
</div>
@endsection
