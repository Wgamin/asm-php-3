@extends('admin.layouts.master')

@section('content')
<div class="p-8 max-w-7xl mx-auto">
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold text-gray-900">Quản lý Thuộc tính</h2>
        <p class="text-gray-500 mt-1 text-sm">Thiết lập các nhóm phân loại như Màu sắc, Kích thước để dùng cho sản phẩm biến thể.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        
        {{-- CỘT TRÁI: THÊM THUỘC TÍNH MỚI --}}
        <div class="lg:col-span-4">
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 sticky top-8">
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-plus-circle text-lg"></i>
                    </div>
                    <h3 class="font-bold text-gray-800">Tạo thuộc tính</h3>
                </div>

                <form action="{{ route('admin.attributes.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-gray-400 uppercase mb-2 tracking-widest">Tên thuộc tính</label>
                        <input type="text" name="name" required
                               placeholder="VD: Màu sắc, Size, Chất liệu..." 
                               class="w-full px-4 py-3 rounded-2xl border border-gray-200 focus:ring-2 focus:ring-emerald-500 outline-none transition bg-gray-50/50">
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <button type="submit" class="w-full bg-gray-900 text-white font-bold py-3.5 rounded-2xl hover:bg-emerald-600 transition-all shadow-lg shadow-gray-200">
                        Lưu thuộc tính
                    </button>
                </form>
            </div>
        </div>

        {{-- CỘT PHẢI: DANH SÁCH & GIÁ TRỊ CON --}}
        <div class="lg:col-span-8 space-y-6">
            @forelse($attributes as $attr)
            <div class="bg-white rounded-3xl border border-gray-100 shadow-sm overflow-hidden group hover:border-emerald-200 transition-all">
                {{-- Header Thuộc tính --}}
                <div class="p-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/30">
                    <div class="flex items-center gap-4">
                        <div class="bg-white p-3 rounded-2xl shadow-sm border border-gray-100">
                            <i class="fas fa-tag text-emerald-500"></i>
                        </div>
                        <div>
                            <h4 class="text-lg font-black text-gray-800">{{ $attr->name }}</h4>
                            <p class="text-[10px] text-gray-400 uppercase font-bold tracking-tighter">ID: #{{ $attr->id }}</p>
                        </div>
                    </div>
                    
                    <div class="flex items-center gap-2">
                        {{-- Nút Xóa Thuộc tính --}}
                        <form action="{{ route('admin.attributes.destroy', $attr->id) }}" method="POST" onsubmit="return confirm('Xóa thuộc tính sẽ xóa tất cả giá trị con. Bạn chắc chứ?')">
                            @csrf @method('DELETE')
                            <button class="w-8 h-8 flex items-center justify-center rounded-xl text-gray-300 hover:bg-red-50 hover:text-red-500 transition-all">
                                <i class="fas fa-trash-alt text-sm"></i>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Nội dung: Các giá trị con --}}
                <div class="p-6">
                    <label class="block text-[10px] font-bold text-gray-400 uppercase mb-3 tracking-widest">Các giá trị hiện có</label>
                    <div class="flex flex-wrap gap-2 mb-6">
                        @forelse($attr->attributeValues as $val)
                        <div class="inline-flex items-center bg-white border border-gray-100 px-4 py-1.5 rounded-2xl shadow-sm group/val hover:border-red-200 transition-all">
                            <span class="text-sm font-semibold text-gray-700">{{ $val->value }}</span>
                            <form action="{{ route('admin.attributes.destroyValue', $val->id) }}" method="POST" class="ml-2">
                                @csrf @method('DELETE')
                                <button class="text-gray-300 hover:text-red-500 transition-colors">
                                    <i class="fas fa-times-circle text-xs"></i>
                                </button>
                            </form>
                        </div>
                        @empty
                        <p class="text-xs text-gray-400 italic">Chưa có giá trị nào (như Đỏ, Xanh...)</p>
                        @endforelse
                    </div>

                    {{-- Form thêm giá trị nhanh --}}
                    <form action="{{ route('admin.attributes.storeValue', $attr->id) }}" method="POST">
                        @csrf
                        <div class="flex gap-2 p-1.5 bg-gray-50 rounded-2xl border border-gray-100 focus-within:border-emerald-300 transition-all">
                            <input type="text" name="value" required
                                   placeholder="Thêm giá trị cho {{ $attr->name }}..." 
                                   class="flex-1 bg-transparent px-4 py-2 text-sm outline-none">
                            <button type="submit" class="bg-white text-emerald-600 px-6 py-2 rounded-xl text-xs font-black shadow-sm border border-emerald-100 hover:bg-emerald-500 hover:text-white transition-all">
                                <i class="fas fa-plus mr-1"></i> THÊM
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            @empty
            <div class="text-center py-20 bg-gray-50 rounded-3xl border-2 border-dashed border-gray-200">
                <i class="fas fa-layer-group text-4xl text-gray-200 mb-4"></i>
                <p class="text-gray-500 font-bold">Chưa có thuộc tính nào được tạo.</p>
                <p class="text-xs text-gray-400">Hãy bắt đầu bằng cách thêm ở form bên trái.</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection