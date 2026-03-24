@extends('admin.layouts.master')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100">
            <h2 class="text-xl font-bold text-gray-800">Thêm Nông Sản Mới</h2>
        </div>

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Tên sản phẩm</label>
                    <input type="text" name="name" placeholder="Ví dụ: Cam sành Hàm Yên" required
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-green-50 focus:border-primary-green outline-none transition">
                </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Giá sản phẩm (VNĐ)</label>
                    <input type="number" name="price" placeholder="45000" required
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-green-50 focus:border-primary-green outline-none transition">
                </div>
            </div>

            <div x-data="{ imageUrl: null }" class="space-y-2">
                <label class="block text-sm font-bold text-gray-700 mb-2">Ảnh sản phẩm</label>
                
                <div class="mt-1 flex justify-center w-full">
                    <label class="relative flex flex-col items-center justify-center w-full h-64 border-2 border-gray-300 border-dashed rounded-2xl cursor-pointer bg-gray-50 hover:border-primary-green hover:bg-gray-100 transition overflow-hidden group">
                        
                        <div x-show="!imageUrl" class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                            <i class="fas fa-cloud-upload-alt text-gray-400 text-4xl mb-4 group-hover:text-primary-green transition"></i>
                            <p class="mb-2 text-sm text-gray-500">
                                <span class="font-bold text-primary-green">Click để tải ảnh</span> hoặc kéo thả
                            </p>
                            <p class="text-xs text-gray-400">PNG, JPG, JPEG (Tối đa 2MB)</p>
                        </div>

                        <template x-if="imageUrl">
                            <div class="absolute inset-0 w-full h-full p-2 bg-white">
                                <img :src="imageUrl" class="w-full h-full object-contain rounded-xl">
                                
                                <button type="button" 
                                        @click.prevent="imageUrl = null; $refs.imageInput.value = ''"
                                        class="absolute top-3 right-3 bg-red-500 text-white h-9 w-9 rounded-full hover:bg-red-600 shadow-lg transition flex items-center justify-center z-10">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                        </template>

                        <input type="file" 
                               name="image" 
                               class="sr-only" 
                               x-ref="imageInput"
                               required
                               accept="image/*"
                               @change="
                                const file = $event.target.files[0];
                                if (file) {
                                    const reader = new FileReader();
                                    reader.onload = (e) => { imageUrl = e.target.result };
                                    reader.readAsDataURL(file);
                                }
                               ">
                    </label>
                </div>
                @error('image')
                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Mô tả ngắn</label>
                <input type="text" name="description" placeholder="Tóm tắt đặc điểm nổi bật..." required
                       class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary-green outline-none transition">
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Nội dung chi tiết</label>
                <textarea name="content" rows="6" placeholder="Viết chi tiết về nguồn gốc, cách sử dụng..." required
                          class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:border-primary-green outline-none transition"></textarea>
            </div>
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Danh mục</label>
                    <select name="category_id" required
                            class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-green-50 focus:border-primary-green outline-none transition">
                        <option value="">-- Chọn danh mục --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>


            <div class="flex justify-end space-x-4 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.products.index') }}" class="px-6 py-3 text-gray-500 font-bold hover:text-gray-700 transition">Hủy</a>
                <button type="submit" class="bg-green-500 hover:bg-green-600 text-white font-bold py-3 px-10 rounded-xl shadow-lg shadow-green-100 transition transform hover:-translate-y-0.5">
                    Lưu sản phẩm
                </button>
            </div>
        </form>
    </div>
</div>
@endsection