@extends('admin.layouts.master')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex justify-between items-center">
            <h2 class="text-xl font-bold text-gray-800">Chỉnh sửa: {{ $product->name }}</h2>
            <span class="text-xs text-gray-400 italic">ID: #{{ $product->id }}</span>
        </div>

        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tên sản phẩm</label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-green-50 focus:border-primary-green outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Giá sản phẩm (VNĐ)</label>
                    <input type="number" name="price" value="{{ old('price', $product->price) }}" required
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-green-50 focus:border-primary-green outline-none transition">
                </div>
            </div>

            <div x-data="{ imageUrl: '{{ asset('storage/' . $product->image) }}' }" class="space-y-2">
                <label class="block text-sm font-bold text-gray-700 mb-2">Ảnh sản phẩm</label>
                
                <div class="mt-1 flex justify-center w-full">
                    <label class="relative flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-2xl cursor-pointer bg-gray-50 hover:border-primary-green transition overflow-hidden">
                        
                        <div class="absolute inset-0 w-full h-full p-2 bg-white">
                            <img :src="imageUrl" class="w-full h-full object-contain rounded-xl shadow-inner">
                            <div class="absolute bottom-4 left-1/2 -translate-x-1/2 bg-black/50 text-white text-[10px] px-3 py-1 rounded-full backdrop-blur-sm">
                                Nhấp để thay đổi ảnh
                            </div>
                        </div>

                        <input type="file" name="image" class="sr-only" @change="
                            const file = $event.target.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = (e) => { imageUrl = e.target.result };
                                reader.readAsDataURL(file);
                            }
                        " accept="image/*">
                    </label>
                </div>
                <p class="text-[11px] text-gray-400 text-center italic">Nếu không muốn đổi ảnh, vui lòng để trống trường này.</p>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Mô tả ngắn</label>
                <input type="text" name="description" value="{{ old('description', $product->description) }}" required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary-green outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Nội dung chi tiết</label>
                <textarea name="content" rows="6" required
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary-green outline-none transition">{{ old('content', $product->content) }}</textarea>
            </div>

            <div class="flex justify-end space-x-4 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.products.index') }}" class="px-6 py-3 text-gray-500 font-bold hover:text-gray-700 transition text-sm">Quay lại</a>
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-10 rounded-xl shadow-lg shadow-green-100 transition transform hover:-translate-y-0.5">
                    Cập nhật ngay
                </button>
            </div>
        </form>
    </div>
</div>
@endsection