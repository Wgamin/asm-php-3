@extends('admin.layouts.master')

@section('content')

{{-- thông báo --}}
@if(session('success'))
    <div class="mb-4 p-4 bg-green-50 text-green-700 rounded-xl border border-green-200">
        {{ session('success') }}
    </div>
@endif

<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">

    {{-- header --}}
    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Quản lý Nông Sản</h2>
            <p class="text-sm text-gray-500">Danh sách các sản phẩm đang hiển thị trên hệ thống</p>

            {{-- tổng số --}}
            <p class="text-xs text-gray-400 mt-1">
                Hiển thị {{ $products->count() }} / {{ $products->total() }} sản phẩm
            </p>
        </div>

        <a href="{{ route('admin.products.create') }}" 
           class="bg-green-500 hover:bg-green-600 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-green-100 transition transform hover:-translate-y-0.5 flex items-center">
            <i class="fas fa-plus mr-2"></i> Thêm mới
        </a>
    </div>

    {{-- filter --}}
    <div class="p-6 border-b border-gray-100 bg-gray-50/50">
        <form method="GET" action="{{ route('admin.products.index') }}" 
              class="grid grid-cols-1 md:grid-cols-4 gap-3">

            <input 
                type="text" 
                name="keyword"
                value="{{ request('keyword') }}"
                placeholder="Tìm theo tên..."
                class="px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-green-400 text-sm"
            >

            <input 
                type="number" 
                name="min_price"
                value="{{ request('min_price') }}"
                placeholder="Giá từ"
                class="px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-green-400 text-sm"
            >

            <input 
                type="number" 
                name="max_price"
                value="{{ request('max_price') }}"
                placeholder="Giá đến"
                class="px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-green-400 text-sm"
            >

            <div class="flex gap-2">
                <button class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2.5 rounded-xl text-sm font-bold">
                    Lọc
                </button>

                <a href="{{ route('admin.products.index') }}" 
                   class="px-4 py-2.5 text-sm rounded-xl border border-gray-200 hover:bg-gray-100">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- hiển thị keyword --}}
    @if(request('keyword'))
        <div class="px-6 py-2 text-sm text-gray-500">
            Kết quả tìm cho: 
            <span class="font-semibold text-green-600">"{{ request('keyword') }}"</span>
        </div>
    @endif

    {{-- table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50">
                <tr class="text-xs uppercase tracking-wider text-gray-400 font-bold border-b border-gray-100">
                    <th class="px-6 py-4">Hình ảnh</th>
                    <th class="px-6 py-4">Tên sản phẩm</th>
                    <th class="px-6 py-4">Giá bán</th>
                    <th class="px-6 py-4">Danh mục</th>
                    <th class="px-6 py-4">Mô tả ngắn</th>
                    <th class="px-6 py-4 text-center">Thao tác</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-50">
                @forelse($products as $product)
                <tr class="hover:bg-green-50/30 transition">

                    {{-- image --}}
                    <td class="px-6 py-4">
                        <img 
                            src="{{ asset('storage/' . $product->image) }}"
                            onerror="this.src='https://via.placeholder.com/80'"
                            class="w-16 h-16 rounded-xl object-cover shadow-sm border border-gray-100"
                        >
                    </td>

                    {{-- name + highlight --}}
                    <td class="px-6 py-4 font-bold text-gray-800">
                        @if(request('keyword'))
                            {!! str_ireplace(
                                request('keyword'), 
                                '<span class="bg-yellow-200 px-1 rounded">'.request('keyword').'</span>', 
                                $product->name
                            ) !!}
                        @else
                            {{ $product->name }}
                        @endif
                    </td>

                    {{-- price --}}
                    <td class="px-6 py-4">
                        <span class="text-primary-green font-bold">
                            {{ number_format($product->price) }}đ
                        </span>
                    </td>
                    {{-- category --}}
                    <td class="px-6 py-4">
                        <span class="text-sm text-gray-500 italic">
                            {{ $product->category ? $product->category->name : 'Chưa có' }}
                        </span>
                    </td>


                    {{-- description --}}
                    <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate">
                        {{ $product->description }}
                    </td>

                    {{-- actions --}}
                    <td class="px-6 py-4">
                        <div class="flex justify-center space-x-2">

                            {{-- edit --}}
                            <a href="{{ route('admin.products.edit', $product->id) }}" 
                               class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition">
                                <i class="fas fa-edit text-xs"></i>
                            </a>

                            {{-- delete --}}
                            <form action="{{ route('admin.products.destroy', $product->id) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Bạn có chắc muốn xóa?')">
                                @csrf 
                                @method('DELETE')
                                <button class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>

                        </div>
                    </td>

                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-10 text-center text-gray-400">
                        Chưa có sản phẩm nào.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- pagination --}}
    <div class="p-6 border-t border-gray-100">
        {{ $products->links() }}
    </div>

</div>
@endsection