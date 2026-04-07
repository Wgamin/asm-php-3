@extends('admin.layouts.master')

@section('content')

{{-- thông báo --}}
@if(session('success'))
    <div class="mb-6 p-4 bg-emerald-50 text-emerald-700 rounded-2xl border border-emerald-100 flex items-center shadow-sm">
        <i class="fas fa-check-circle mr-3"></i>
        <span class="font-medium">{{ session('success') }}</span>
    </div>
@endif

@if(session('error'))
    <div class="mb-6 p-4 bg-red-50 text-red-700 rounded-2xl border border-red-100 flex items-center shadow-sm">
        <i class="fas fa-exclamation-triangle mr-3"></i>
        <span class="font-medium">{{ session('error') }}</span>
    </div>
@endif
        <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center gap-2 mb-4">
            @csrf
            <input type="file" name="file_excel" accept=".xlsx,.xls,.csv" required 
                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer border border-gray-200 rounded-xl bg-white">
            
            <button type="submit" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-6 rounded-xl shadow-sm transition">
                Nhập dữ liệu
            </button>
        </form>

        @if(session('import_failures') && count(session('import_failures')))
            <div class="mb-6 p-4 bg-amber-50 text-amber-900 rounded-2xl border border-amber-100 shadow-sm">
                <div class="font-bold mb-2">Một số dòng bị lỗi khi import</div>
                <ul class="space-y-2 text-sm">
                    @foreach(array_slice(session('import_failures'), 0, 5) as $failure)
                        <li class="p-3 rounded-xl bg-white/70 border border-amber-100">
                            <div class="font-semibold">Dòng {{ $failure['row'] }} - {{ $failure['name'] }}</div>
                            <div class="mt-1 text-amber-800">
                                {{ implode(' | ', $failure['errors']) }}
                            </div>
                        </li>
                    @endforeach
                </ul>
            </div>
        @endif
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    
    {{-- header --}}
    <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-md-center gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Quản lý Nông Sản</h2>
            <p class="text-sm text-gray-500">
                Hiển thị <span class="font-bold text-gray-700">{{ $products->count() }}</span> trên tổng số <span class="font-bold text-gray-700">{{ $products->total() }}</span> sản phẩm
            </p>
        </div>

        <a href="{{ route('admin.products.create') }}" 
           class="bg-emerald-500 hover:bg-emerald-600 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-emerald-100 transition transform hover:-translate-y-0.5 flex items-center justify-center">
            <i class="fas fa-plus mr-2 text-xs"></i> Thêm mới
        </a>
    </div>
    
    {{-- filter --}}
    <div class="p-6 border-b border-gray-100 bg-gray-50/30">
        <form method="GET" action="{{ route('admin.products.index') }}" 
              class="grid grid-cols-1 md:grid-cols-4 gap-4">

            <div class="relative">
                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 text-xs"></i>
                <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm tên sản phẩm..."
                       class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 text-sm outline-none transition">
            </div>

            <input type="number" name="min_price" value="{{ request('min_price') }}" placeholder="Giá thấp nhất"
                   class="px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 text-sm outline-none transition">

            <input type="number" name="max_price" value="{{ request('max_price') }}" placeholder="Giá cao nhất"
                   class="px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 text-sm outline-none transition">

            <div class="flex gap-2">
                <button type="submit" class="flex-1 bg-gray-800 hover:bg-gray-900 text-white px-4 py-2.5 rounded-xl text-sm font-bold transition">
                    Lọc dữ liệu
                </button>
                <a href="{{ route('admin.products.index') }}" 
                   class="px-4 py-2.5 text-sm rounded-xl border border-gray-200 bg-white hover:bg-gray-50 transition flex items-center justify-center" title="Làm mới">
                    <i class="fas fa-redo-alt text-gray-500"></i>
                </a>
            </div>
        </form>
    </div>

    {{-- table --}}
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="text-[10px] uppercase tracking-widest text-gray-400 font-black border-b border-gray-100 bg-gray-50/50">
                    <th class="px-6 py-4">Sản phẩm</th>
                    <th class="px-6 py-4">Loại</th>
                    <th class="px-6 py-4">Giá bán</th>
                    <th class="px-6 py-4">Danh mục</th>
                    <th class="px-6 py-4 text-center">Thao tác</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-gray-50">
                @forelse($products as $product)
                <tr class="hover:bg-emerald-50/20 transition group">
                    {{-- image + name --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <img src="{{ asset('storage/' . $product->image) }}"
                                 onerror="this.src='https://ui-avatars.com/api/?name={{ urlencode($product->name) }}&color=10b981&background=ecfdf5'"
                                 class="w-14 h-14 rounded-2xl object-cover shadow-sm border border-gray-100 group-hover:scale-105 transition">
                            <div class="max-w-[200px]">
                                <h4 class="font-bold text-gray-800 truncate">
                                    @if(request('keyword'))
                                        {!! str_ireplace(request('keyword'), '<span class="bg-yellow-200 px-0.5 rounded">'.request('keyword').'</span>', $product->name) !!}
                                    @else
                                        {{ $product->name }}
                                    @endif
                                </h4>
                                <p class="text-xs text-gray-400 truncate">{{ $product->description }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- type badge --}}
                    <td class="px-6 py-4">
                        @if($product->product_type === 'variable')
                            <span class="px-2.5 py-1 bg-purple-50 text-purple-600 rounded-lg text-[10px] font-bold uppercase tracking-wider">Biến thể</span>
                        @else
                            <span class="px-2.5 py-1 bg-blue-50 text-blue-600 rounded-lg text-[10px] font-bold uppercase tracking-wider">Thường</span>
                        @endif
                    </td>

                    {{-- price --}}
                    <td class="px-6 py-4 text-sm font-black text-emerald-600">
                        @if($product->product_type === 'variable')
                            <span class="text-[10px] text-gray-400 font-normal block uppercase">Từ</span>
                            {{ number_format($product->variants->min('price')) }}đ
                        @else
                            {{ number_format($product->price) }}đ
                        @endif
                    </td>

                    {{-- category --}}
                    <td class="px-6 py-4">
                        <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">
                            {{ $product->category->name ?? 'N/A' }}
                        </span>
                    </td>

                    {{-- actions --}}
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-center items-center space-x-2">
                            {{-- show --}}
                            <a href="{{ route('admin.products.show', $product->id) }}" 
                               class="w-9 h-9 flex items-center justify-center bg-gray-50 text-gray-500 rounded-xl hover:bg-emerald-500 hover:text-white transition shadow-sm">
                                <i class="fas fa-eye text-xs"></i>
                            </a>

                            {{-- edit --}}
                            <a href="{{ route('admin.products.edit', $product->id) }}" 
                               class="w-9 h-9 flex items-center justify-center bg-gray-50 text-blue-500 rounded-xl hover:bg-blue-500 hover:text-white transition shadow-sm">
                                <i class="fas fa-edit text-xs"></i>
                            </a>

                            {{-- delete --}}
                            <form action="{{ route('admin.products.destroy', $product->id) }}" 
                                  method="POST" class="inline"
                                  onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                @csrf 
                                @method('DELETE')
                                <button class="w-9 h-9 flex items-center justify-center bg-gray-50 text-red-500 rounded-xl hover:bg-red-500 hover:text-white transition shadow-sm">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-20 text-center">
                        <div class="flex flex-col items-center">
                            <i class="fas fa-box-open text-4xl text-gray-200 mb-4"></i>
                            <p class="text-gray-400 font-medium">Không tìm thấy sản phẩm nào phù hợp.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    
    {{-- pagination --}}
    <div class="p-6 border-t border-gray-100 bg-gray-50/30">
        {{ $products->appends(request()->query())->links() }}
    </div>

</div>

<style>
    /* Tùy chỉnh Pagination của Laravel sang style Tailwind cho đẹp */
    .pagination { @apply flex gap-2; }
    .page-item { @apply rounded-lg overflow-hidden border border-gray-200; }
    .page-link { @apply px-4 py-2 bg-white text-gray-600 text-sm; }
    .page-item.active .page-link { @apply bg-emerald-500 text-white border-emerald-500; }
</style>

@endsection
