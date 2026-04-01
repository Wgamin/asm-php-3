@extends('admin.layouts.master')

@section('content')
<div class="max-w-5xl mx-auto" x-data="productFormEdit()">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        {{-- Header & Switcher (Giữ nguyên thiết kế Create) --}}
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center bg-gray-50/50 gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Chỉnh sửa Nông Sản</h2>
                <p class="text-sm text-gray-500">Cập nhật thông tin cho sản phẩm: <span class="font-bold text-emerald-600">{{ $product->name }}</span></p>
            </div>
            
            {{-- Switcher chỉ hiển thị để xem, không nên cho đổi loại sản phẩm khi edit để tránh mất data --}}
            <div class="flex bg-white border border-gray-200 rounded-xl p-1 shadow-sm opacity-60 pointer-events-none">
                <button type="button" 
                    :class="productType === 'simple' ? 'bg-emerald-500 text-white' : 'text-gray-500'"
                    class="px-5 py-2 rounded-lg text-sm font-bold transition-all duration-200">
                    Sản phẩm thường
                </button>
                <button type="button" 
                    :class="productType === 'variable' ? 'bg-emerald-500 text-white' : 'text-gray-500'"
                    class="px-5 py-2 rounded-lg text-sm font-bold transition-all duration-200">
                    Sản phẩm biến thể
                </button>
            </div>
        </div>

        {{-- Khối hiển thị lỗi Validation (Nếu có) --}}
        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 m-8 mb-0 rounded-r-xl">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-bold text-red-800">Có lỗi nhập liệu:</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        {{-- Form Action trỏ tới Route Update, dùng PUT và enctype --}}
        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
            @csrf
            @method('PUT') {{-- Bắt buộc phải có khi Edit --}}
            
            {{-- Input ẩn gửi loại sản phẩm --}}
            <input type="hidden" name="product_type" :value="productType">

            {{-- 1. Thông tin chung (Đổ old value hoặc database value) --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700">Tên sản phẩm <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required placeholder="VD: Gạo ST25, Cà phê Robusta..."
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700">Danh mục <span class="text-red-500">*</span></label>
                    <select name="category_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition appearance-none">
                        <option value="">Chọn danh mục</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Giá gốc & Giá giảm (Chỉ hiện khi là sản phẩm thường) --}}
                <div class="space-y-2" x-show="productType === 'simple'" x-transition>
                    <label class="text-sm font-bold text-gray-700">Giá bán gốc (VNĐ)</label>
                    <input type="number" name="price" value="{{ old('price', $product->price) }}" placeholder="0" min="0"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition">
                </div>

                <div class="space-y-2" x-show="productType === 'simple'" x-transition>
                    <label class="text-sm font-bold text-gray-700">Giá giảm (VNĐ) <span class="text-gray-400 font-normal text-xs">(Không bắt buộc)</span></label>
                    <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" placeholder="0" min="0"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition">
                </div>
            </div>

            <hr class="border-gray-100">

            {{-- 2. Khối Biến thể (Chỉ hiện khi là sản phẩm biến thể) --}}
            <div x-show="productType === 'variable'" x-transition>
                {{-- Dùng Component riêng cho Edit để xử lý ảnh cũ biến thể --}}
                @include('admin.products.Components.variant-form-edit')
            </div>
            
            {{-- 3. Khối Upload Ảnh và Mô tả (Layout grid 1-2) --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-1">
                    {{-- Dùng Component Upload Ảnh riêng cho Edit để hiển thị ảnh cũ --}}
                    @include('admin.products.Components.image-upload-edit')
                </div>
                <div class="lg:col-span-2">
                    {{-- Dùng Component Mô tả riêng cho Edit để đổ dữ liệu cũ --}}
                    @include('admin.products.Components.description-form-edit')
                </div>
            </div>

            {{-- 4. Nút hành động --}}
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.products.index') }}" class="px-8 py-3 rounded-xl font-bold text-gray-500 hover:bg-gray-50 transition">
                    Hủy bỏ
                </a>
                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-12 rounded-xl shadow-lg shadow-emerald-100 transition transform hover:-translate-y-0.5">
                    <i class="fas fa-save mr-2"></i> Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Logic Alpine.js --}}
@php
    // Chuẩn bị mảng dữ liệu biến thể bằng PHP thuần để tránh lỗi biên dịch Blade
    $variantList = [];
    if (isset($product) && $product->variants) {
        foreach ($product->variants as $v) {
            $variantList[] = [
                'sku' => $v->sku,
                'price' => (int)$v->price,
                'sale_price' => $v->sale_price ? (int)$v->sale_price : '',
                'stock' => (int)$v->stock,
                'image_url' => $v->image ? asset('storage/' . $v->image) : null,
                // Chuyển variant_values về mảng, nếu trống thì để mảng rỗng []
                'attributes' => is_string($v->variant_values) 
                                ? json_decode($v->variant_values, true) 
                                : ($v->variant_values ?? [])
            ];
        }
    }
@endphp

<script>
    function productFormEdit() {
        return {
            // Khởi tạo productType từ database
            productType: "{{ old('product_type', $product->product_type) }}", 

            // Đổ mảng đã chuẩn bị ở trên vào Javascript qua hàm json_encode
            // Cách này an toàn 100% với mọi loại dấu ngoặc
            variants: {!! json_encode($variantList) !!},
            
            // Hàm thêm biến thể rỗng
            addVariant() {
                this.variants.push({
                    sku: '',
                    price: '',
                    sale_price: '', 
                    stock: '',
                    image_url: null,
                    attributes: {} 
                });
            },
            
            // Hàm xóa biến thể
            removeVariant(index) {
                this.variants.splice(index, 1);
            }
        }
    }
</script>
@endsection