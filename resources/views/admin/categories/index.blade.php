@extends('admin.layouts.master')

@section('content')
<div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8" x-data="{ editingId: null }">
    
    {{-- FORM THÊM DANH MỤC --}}
    <div class="md:col-span-1">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-8">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-plus-circle text-emerald-500 mr-2"></i> Thêm danh mục
            </h3>
            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                @csrf
                {{-- Tên danh mục --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Tên danh mục</label>
                    <input type="text" name="name" placeholder="Ví dụ: Trái cây nhập khẩu..." required
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-500 focus:ring-4 focus:ring-emerald-50 outline-none transition shadow-sm">
                </div>

                {{-- CHỌN DANH MỤC CHA --}}
                <div>
                    <label class="block text-xs font-bold text-gray-500 uppercase mb-1 ml-1">Danh mục cha</label>
                    <select name="parent_id" class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-emerald-500 outline-none transition shadow-sm bg-white">
                        <option value="">-- Là danh mục gốc --</option>
                        @foreach($parentCategories as $parent)
                            <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit" class="w-full bg-[#00C89D] text-white font-bold py-3 rounded-xl shadow-lg shadow-emerald-100 hover:bg-emerald-600 transition transform hover:-translate-y-1">
                    Lưu danh mục
                </button>
            </form>
        </div>
    </div>

    {{-- DANH SÁCH DANH MỤC --}}
    <div class="md:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50 text-[11px] uppercase font-bold text-gray-400 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Tên danh mục</th>
                        <th class="px-6 py-4">Danh mục cha</th>
                        <th class="px-6 py-4">Sản phẩm</th>
                        <th class="px-6 py-4 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($categories as $cat)
                    <tr class="hover:bg-emerald-50/20 transition">
                        <td class="px-6 py-4">
                            {{-- Hiển thị thông thường --}}
                            <div x-show="editingId !== {{ $cat->id }}" class="font-bold text-gray-700">
                                {{ $cat->name }}
                            </div>

                            {{-- Form sửa nhanh (Inline Edit) --}}
                            <div x-show="editingId === {{ $cat->id }}" x-cloak>
                                <form action="{{ route('admin.categories.update', $cat->id) }}" method="POST" class="space-y-2">
                                    @csrf @method('PUT')
                                    <input type="text" name="name" value="{{ $cat->name }}" 
                                           class="px-3 py-1.5 border border-emerald-500 rounded-lg outline-none w-full text-sm mb-1">
                                    
                                    <select name="parent_id" class="px-3 py-1.5 border border-emerald-500 rounded-lg outline-none w-full text-xs bg-white">
                                        <option value="">-- Danh mục gốc --</option>
                                        @foreach($parentCategories as $parent)
                                            @if($parent->id !== $cat->id) {{-- Không cho chọn chính mình làm cha --}}
                                                <option value="{{ $parent->id }}" {{ $cat->parent_id == $parent->id ? 'selected' : '' }}>
                                                    {{ $parent->name }}
                                                </option>
                                            @endif
                                        @endforeach
                                    </select>

                                    <div class="flex gap-2 justify-end mt-2">
                                        <button type="submit" class="text-emerald-600 font-bold text-xs bg-emerald-50 px-2 py-1 rounded">Lưu</button>
                                        <button type="button" @click="editingId = null" class="text-gray-400 text-xs bg-gray-50 px-2 py-1 rounded">Hủy</button>
                                    </div>
                                </form>
                            </div>
                        </td>

                        {{-- CỘT HIỂN THỊ DANH MỤC CHA --}}
                        <td class="px-6 py-4">
                            @if($cat->parent)
                                <span class="text-xs bg-blue-50 text-blue-600 px-2 py-1 rounded-md font-medium">
                                    {{ $cat->parent->name }}
                                </span>
                            @else
                                <span class="text-xs bg-gray-100 text-gray-400 px-2 py-1 rounded-md">Gốc</span>
                            @endif
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-500">
                            <span class="font-bold text-gray-700">{{ $cat->products->count() }}</span> món
                        </td>

                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end items-center space-x-3">
                                <button @click="editingId = {{ $cat->id }}" 
                                        x-show="editingId !== {{ $cat->id }}"
                                        class="text-blue-400 hover:text-blue-600 transition">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>

                                <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" 
                                      onsubmit="return confirm('Xóa danh mục này sẽ xóa luôn các danh mục con của nó. Bạn chắc chắn chứ?')">
                                    @csrf @method('DELETE')
                                    <button class="text-red-300 hover:text-red-500 transition">
                                        <i class="fas fa-trash-alt text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            
            {{-- Phân trang --}}
            <div class="px-6 py-4 bg-gray-50/30">
                {{ $categories->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    [x-cloak] { display: none !important; }
</style>
@endsection