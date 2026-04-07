@extends('admin.layouts.master')

@section('content')
<div class="max-w-5xl mx-auto" x-data="productFormEdit()">
    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center bg-gray-50/50 gap-4">
            <div>
                <h2 class="text-xl font-bold text-gray-800">Chinh Sua Nong San</h2>
                <p class="text-sm text-gray-500">Cap nhat thong tin cho san pham: <span class="font-bold text-emerald-600">{{ $product->name }}</span></p>
            </div>

            <div class="flex bg-white border border-gray-200 rounded-xl p-1 shadow-sm opacity-60 pointer-events-none">
                <button type="button"
                        :class="productType === 'simple' ? 'bg-emerald-500 text-white' : 'text-gray-500'"
                        class="px-5 py-2 rounded-lg text-sm font-bold transition-all duration-200">
                    San pham thuong
                </button>
                <button type="button"
                        :class="productType === 'variable' ? 'bg-emerald-500 text-white' : 'text-gray-500'"
                        class="px-5 py-2 rounded-lg text-sm font-bold transition-all duration-200">
                    San pham bien the
                </button>
            </div>
        </div>

        @if ($errors->any())
            <div class="bg-red-50 border-l-4 border-red-500 p-4 m-8 mb-0 rounded-r-xl">
                <h3 class="text-sm font-bold text-red-800">Co loi nhap lieu:</h3>
                <ul class="mt-2 text-sm text-red-700 list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('admin.products.update', $product->id) }}" method="POST" enctype="multipart/form-data" class="p-8 space-y-8">
            @csrf
            @method('PUT')
            <input type="hidden" name="product_type" :value="productType">

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700">Ten san pham <span class="text-red-500">*</span></label>
                    <input type="text" name="name" value="{{ old('name', $product->name) }}" required
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition">
                </div>

                <div class="space-y-2">
                    <label class="text-sm font-bold text-gray-700">Danh muc <span class="text-red-500">*</span></label>
                    <select name="category_id" required class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition appearance-none">
                        <option value="">Chon danh muc</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-2" x-show="productType === 'simple'" x-transition>
                    <label class="text-sm font-bold text-gray-700">Gia ban goc (VND)</label>
                    <input type="number" name="price" value="{{ old('price', $product->price) }}" placeholder="0" min="0"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition">
                </div>

                <div class="space-y-2" x-show="productType === 'simple'" x-transition>
                    <label class="text-sm font-bold text-gray-700">Gia giam (VND)</label>
                    <input type="number" name="sale_price" value="{{ old('sale_price', $product->sale_price) }}" placeholder="0" min="0"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition">
                </div>

                <div class="space-y-2" x-show="productType === 'simple'" x-transition>
                    <label class="text-sm font-bold text-gray-700">Ton kho</label>
                    <input type="number" name="stock" value="{{ old('stock', $product->stock ?? 0) }}" placeholder="0" min="0"
                           class="w-full px-4 py-3 rounded-xl border border-gray-200 focus:ring-4 focus:ring-emerald-50 focus:border-emerald-500 outline-none transition">
                </div>
            </div>

            <hr class="border-gray-100">

            <div x-show="productType === 'variable'" x-transition>
                @include('admin.products.Components.variant-form-edit')
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <div class="lg:col-span-1">
                    @include('admin.products.Components.image-upload-edit')
                </div>
                <div class="lg:col-span-2">
                    @include('admin.products.Components.description-form-edit')
                </div>
            </div>

            <div class="flex justify-end space-x-4 pt-6 border-t border-gray-100">
                <a href="{{ route('admin.products.index') }}" class="px-8 py-3 rounded-xl font-bold text-gray-500 hover:bg-gray-50 transition">
                    Huy bo
                </a>
                <button type="submit" class="bg-emerald-500 hover:bg-emerald-600 text-white font-bold py-3 px-12 rounded-xl shadow-lg shadow-emerald-100 transition transform hover:-translate-y-0.5">
                    Luu thay doi
                </button>
            </div>
        </form>
    </div>
</div>

@php
    $variantList = [];
    foreach ($product->variants as $v) {
        $variantValues = $v->variant_values;
        if (is_string($variantValues)) {
            $variantValues = json_decode($variantValues, true) ?: [];
        }

        $variantList[] = [
            'sku' => $v->sku,
            'price' => (float) $v->price,
            'sale_price' => $v->sale_price !== null ? (float) $v->sale_price : '',
            'stock' => (int) $v->stock,
            'image_url' => $v->image ? asset('storage/'.$v->image) : null,
            'existing_image' => $v->image,
            'attributes' => is_array($variantValues) ? $variantValues : [],
        ];
    }
@endphp

<script>
    function productFormEdit() {
        return {
            productType: "{{ old('product_type', $product->product_type) }}",
            variants: {!! json_encode($variantList) !!},

            addVariant() {
                this.variants.push({
                    sku: '',
                    price: '',
                    sale_price: '',
                    stock: '',
                    image_url: null,
                    existing_image: '',
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
