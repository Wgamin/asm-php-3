@extends('admin.layouts.master')

@section('content')
<div class="max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-8" x-data="{ editingId: null }">
    
    <div class="md:col-span-1">
        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 sticky top-8">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
                <i class="fas fa-plus-circle text-primary-green mr-2"></i> Thêm danh mục
            </h3>
            <form action="{{ route('admin.categories.store') }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <input type="text" name="name" placeholder="Tên danh mục mới..." required
                           class="w-full px-4 py-2.5 rounded-xl border border-gray-200 focus:border-primary-green focus:ring-4 focus:ring-green-50 outline-none transition">
                </div>
                <button type="submit" class="w-full bg-[#00C89D] text-white font-bold py-2.5 rounded-xl shadow-lg shadow-green-100 hover:bg-dark-green transition transform hover:-translate-y-1">
                    Thêm ngay
                </button>
            </form>
        </div>
    </div>

    <div class="md:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50 text-[11px] uppercase font-bold text-gray-400 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4">Tên danh mục</th>
                        <th class="px-6 py-4">Sản phẩm</th>
                        <th class="px-6 py-4 text-right">Hành động</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @foreach($categories as $cat)
                    <tr class="hover:bg-green-50/20 transition">
                        <td class="px-6 py-4">
                            <div x-show="editingId !== {{ $cat->id }}" class="font-bold text-gray-700">
                                {{ $cat->name }}
                            </div>

                            <div x-show="editingId === {{ $cat->id }}" x-cloak>
                                <form action="{{ route('admin.categories.update', $cat->id) }}" method="POST" class="flex items-center gap-2">
                                    @csrf @method('PUT')
                                    <input type="text" name="name" value="{{ $cat->name }}" 
                                           class="px-3 py-1.5 border border-primary-green rounded-lg outline-none w-full text-sm">
                                    <button type="submit" class="text-green-600 hover:text-green-800 font-bold text-xs">Lưu</button>
                                    <button type="button" @click="editingId = null" class="text-gray-400 hover:text-gray-600 text-xs">Hủy</button>
                                </form>
                            </div>
                        </td>

                        <td class="px-6 py-4 text-sm text-gray-400 italic">
                            {{ $cat->products_count ?? $cat->products->count() }} món
                        </td>

                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end items-center space-x-3">
                                <button @click="editingId = {{ $cat->id }}" 
                                        x-show="editingId !== {{ $cat->id }}"
                                        class="text-blue-400 hover:text-blue-600 transition">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>

                                <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" 
                                      onsubmit="return confirm('Xóa danh mục này sẽ ảnh hưởng đến các sản phẩm thuộc về nó. Tiếp tục?')">
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
        </div>
    </div>
</div>
@endsection