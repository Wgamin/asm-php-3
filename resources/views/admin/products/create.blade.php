@extends('admin.layouts.master')

@section('content')
<div class="max-w-5xl mx-auto" x-data="productForm()">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center bg-gray-50/50 gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Them Nong San Moi</h2>
                <p class="text-sm text-gray-500">Thiet lap thong tin va loai san pham</p>
            </div>

            <div class="flex bg-white border border-gray-200 rounded-xl p-1 shadow-sm">
                <button type="button" @click="productType = 'simple'"
                        :class="productType === 'simple' ? 'bg-emerald-500 text-white' : 'text-gray-500 hover:bg-gray-50'"
                        class="px-5 py-2 rounded-lg text-sm font-bold transition-all duration-200">
                    San pham thuong
                </button>
                <button type="button" @click="productType = 'variable'"
                        :class="productType === 'variable' ? 'bg-emerald-500 text-white' : 'text-gray-500 hover:bg-gray-50'"
                        class="px-5 py-2 rounded-lg text-sm font-bold transition-all duration-200">
                    San pham bien the
                </button>
            </div>
        </div>

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
            @csrf
            <input type="hidden" name="product_type" :value="productType">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700">Ten san pham <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                           placeholder="VD: Gao ST25, Ca phe Robusta..."
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700">Danh muc <span class="text-red-500">*</span></label>
                    <select name="category_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition appearance-none">
                        <option value="">Chon danh muc</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2" x-show="productType === 'simple'" x-transition>
                    <label class="text-sm font-bold text-gray-700">Gia ban goc (VND)</label>
                    <input type="number" name="price" value="{{ old('price') }}" placeholder="0" min="0"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition">
                </div>

                <div class="space-y-2" x-show="productType === 'simple'" x-transition>
                    <label class="text-sm font-bold text-gray-700">Gia giam (VND)</label>
                    <input type="number" name="sale_price" value="{{ old('sale_price') }}" placeholder="0" min="0"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition">
                </div>

                <div class="space-y-2" x-show="productType === 'simple'" x-transition>
                    <label class="text-sm font-bold text-gray-700">Ton kho</label>
                    <input type="number" name="stock" value="{{ old('stock', 0) }}" placeholder="0" min="0"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition">
                </div>
            </div>

            <hr class="border-gray-50">

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

            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.products.index') }}" class="px-8 py-3 rounded-xl font-bold text-gray-500 hover:bg-gray-50 transition">
                    Huy bo
                </a>
                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-12 rounded-xl shadow-lg shadow-emerald-100 transition transform hover:-translate-y-0.5">
                    Luu san pham
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function productForm() {
        return {
            productType: '{{ old('product_type', 'simple') }}',
            variants: [],

            addVariant() {
                this.variants.push({
                    sku: '',
                    price: '',
                    sale_price: '',
                    stock: '',
                    attributes: {}
                });
            },

            removeVariant(index) {
                this.variants.splice(index, 1);
            }
        };
    }
</script>
@endsection
