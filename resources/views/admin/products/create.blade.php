@extends('admin.layouts.master')

@section('content')
<div class="max-w-5xl mx-auto" x-data="productForm()">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        
        {{-- Header & Switcher --}}
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center bg-gray-50/50 gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Thêm Nông Sản Mới</h2>
                <p class="text-sm text-gray-500">Thiết lập thông tin và loại sản phẩm</p>
            </div>
            
            <div class="flex bg-white border border-gray-200 rounded-xl p-1 shadow-sm">
                <button type="button" @click="productType = 'simple'" 
                    :class="productType === 'simple' ? 'bg-emerald-500 text-white' : 'text-gray-500 hover:bg-gray-50'"
                    class="px-5 py-2 rounded-lg text-sm font-bold transition-all duration-200">
                    Sản phẩm thường
                </button>
                <button type="button" @click="productType = 'variable'" 
                    :class="productType === 'variable' ? 'bg-emerald-500 text-white' : 'text-gray-500 hover:bg-gray-50'"
                    class="px-5 py-2 rounded-lg text-sm font-bold transition-all duration-200">
                    Sản phẩm biến thể
                </button>
            </div>
        </div>

        {{-- Bỏ comment khối này nếu bạn muốn hiển thị lỗi validation --}}
        {{-- @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-circle text-red-500"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-bold text-red-800">Có lỗi nhập liệu:</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif --}}

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
            @csrf
            {{-- Input ẩn gửi loại sản phẩm về server --}}
            <input type="hidden" name="product_type" :value="productType">

            {{-- 1. Thông tin chung --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700">Tên sản phẩm <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required placeholder="VD: Gạo ST25, Cà phê Robusta..."
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700">Danh mục <span class="text-red-500">*</span></label>
                    <select name="category_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition appearance-none">
                        <option value="">Chọn danh mục</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- MỚI: Giá gốc & Giá giảm (chỉ hiện khi là sản phẩm thường) --}}
                <div class="space-y-2" x-show="productType === 'simple'" x-transition>
                    <label class="text-sm font-bold text-gray-700">Giá bán gốc (VNĐ)</label>
                    <input type="number" name="price" placeholder="0" min="0"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition">
                </div>

                <div class="space-y-2" x-show="productType === 'simple'" x-transition>
                    <label class="text-sm font-bold text-gray-700">Giá giảm (VNĐ) <span class="text-gray-400 font-normal text-xs">(Không bắt buộc)</span></label>
                    <input type="number" name="sale_price" placeholder="0" min="0"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition">
                </div>
            </div>

            <hr class="border-gray-50">

            {{-- 2. Các Components --}}
            <div x-show="productType === 'variable'" x-transition>
                @include('admin.products.Components.variant-form')
            </div>
            
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-1">
                    @include('admin.products.Components.image-upload')
                </div>
                <div class="lg:col-span-2">
                    @include('admin.products.Components.description-form')
                </div>
            </div>

            {{-- 3. Nút hành động --}}
            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.products.index') }}" class="px-8 py-3 rounded-xl font-bold text-gray-500 hover:bg-gray-50 transition">
                    Hủy bỏ
                </a>
                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-12 rounded-xl shadow-lg shadow-emerald-100 transition transform hover:-translate-y-0.5">
                    Lưu sản phẩm
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Logic Alpine.js --}}
<script>
    function productForm() {
        return {
            productType: 'simple', // Mặc định là sản phẩm thường
            variants: [], // Danh sách biến thể
            
            addVariant() {
                this.variants.push({
                    sku: '',
                    price: '',
                    sale_price: '', // Bổ sung thuộc tính này nếu bạn có làm giá giảm cho từng biến thể
                    stock: '',
                    attributes: {} // Object chứa cặp key-value cho Size/Màu
                });
            },
            
            removeVariant(index) {
                this.variants.splice(index, 1);
            }
        }
    }
</script>
@endsection