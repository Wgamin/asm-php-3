@extends('layouts.client')

@section('title', $product->name)

@section('content')
<div class="bg-slate-50 py-8" x-data="productDetail()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex mb-6 text-sm text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-emerald-600">Trang chủ</a>
            <span class="mx-2">/</span>
            <a href="{{ route('products.index') }}" class="hover:text-emerald-600">Sản phẩm</a>
            <span class="mx-2">/</span>
            <span class="text-slate-800 font-medium line-clamp-1">{{ $product->name }}</span>
        </nav>

        <div class="bg-white rounded-3xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="flex flex-col lg:flex-row">
                <div class="lg:w-1/2 p-6 border-r border-slate-50">
                    <div class="sticky top-24">
                        <div class="aspect-square rounded-2xl overflow-hidden bg-slate-100 mb-4 border border-slate-100 relative">
                            <img :src="activeImage" class="w-full h-full object-cover transition duration-500" alt="{{ $product->name }}">

                            @if(!$product->isVariable() && $product->sale_price > 0 && $product->price > 0)
                                <span class="absolute top-4 left-4 bg-red-500 text-white px-3 py-1 rounded-full text-xs font-bold uppercase tracking-widest shadow-lg">
                                    Giảm {{ round((($product->price - $product->sale_price) / $product->price) * 100) }}%
                                </span>
                            @endif
                        </div>

                        @if($product->images && $product->images->count() > 0)
                            <div class="grid grid-cols-5 gap-3">
                                <button @click="activeImage = '{{ asset('storage/' . $product->image) }}'"
                                    class="aspect-square rounded-xl border-2 transition-all overflow-hidden bg-white"
                                    :class="activeImage === '{{ asset('storage/' . $product->image) }}' ? 'border-emerald-500 ring-2 ring-emerald-100' : 'border-transparent opacity-60 hover:opacity-100'">
                                    <img src="{{ asset('storage/' . $product->image) }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                                </button>

                                @foreach($product->images as $img)
                                    <button @click="activeImage = '{{ asset('storage/' . $img->image_path) }}'"
                                        class="aspect-square rounded-xl border-2 transition-all overflow-hidden bg-white"
                                        :class="activeImage === '{{ asset('storage/' . $img->image_path) }}' ? 'border-emerald-500 ring-2 ring-emerald-100' : 'border-transparent opacity-60 hover:opacity-100'">
                                        <img src="{{ asset('storage/' . $img->image_path) }}" class="w-full h-full object-cover" alt="{{ $product->name }}">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <div class="lg:w-1/2 p-8 lg:p-12">
                    <div class="mb-6">
                        <span class="text-emerald-600 font-bold text-sm uppercase tracking-widest bg-emerald-50 px-3 py-1 rounded-full">
                            {{ $product->category->name ?? 'Nông sản sạch' }}
                        </span>
                        <h1 class="text-3xl lg:text-4xl font-extrabold text-slate-900 mt-4 mb-2 leading-tight">
                            {{ $product->name }}
                        </h1>

                        <div class="mt-4 flex items-baseline gap-2">
                            <template x-if="productType === 'variable' && !selectedVariantId">
                                @php
                                    if($product->isVariable()) {
                                        $minPrice = $product->variants->min(fn($v) => $v->sale_price > 0 ? $v->sale_price : $v->price);
                                        $maxPrice = $product->variants->max(fn($v) => $v->sale_price > 0 ? $v->sale_price : $v->price);
                                    }
                                @endphp
                                <p class="text-3xl lg:text-4xl font-black text-emerald-600">
                                    {{ number_format($minPrice ?? 0, 0, ',', '.') }}đ
                                    @if(isset($minPrice, $maxPrice) && $minPrice != $maxPrice)
                                        <span class="text-slate-400 font-medium text-2xl mx-1">-</span>
                                        {{ number_format($maxPrice, 0, ',', '.') }}đ
                                    @endif
                                </p>
                            </template>

                            <template x-if="productType === 'simple' || selectedVariantId">
                                <div class="flex items-end gap-3">
                                    <template x-if="currentSalePrice > 0">
                                        <div class="flex flex-col">
                                            <span class="text-lg font-bold text-slate-400 line-through" x-text="formatPrice(currentPrice)"></span>
                                            <span class="text-4xl font-black text-emerald-600" x-text="formatPrice(currentSalePrice)"></span>
                                        </div>
                                    </template>

                                    <template x-if="!currentSalePrice || currentSalePrice <= 0">
                                        <span class="text-4xl font-black text-emerald-600" x-text="formatPrice(currentPrice)"></span>
                                    </template>
                                </div>
                            </template>

                            <template x-if="isOutOfStock && selectedVariantId">
                                <span class="text-red-500 font-bold text-sm bg-red-50 px-2 py-1 rounded ml-3 mb-1">Hết hàng</span>
                            </template>
                        </div>
                    </div>

                    @if($product->isVariable())
                        <div class="space-y-6 mb-8 border-t border-slate-100 pt-6">
                            <h3 class="font-bold text-slate-800">Tùy chọn sản phẩm:</h3>
                            <div class="grid grid-cols-2 gap-3">
                                @foreach($product->variants as $variant)
                                    <button @click="selectVariant({{ json_encode($variant) }})"
                                        :class="selectedVariantId == {{ $variant->id }} ? 'border-emerald-600 bg-emerald-50 text-emerald-700' : 'border-slate-200 text-slate-600'"
                                        class="border-2 p-3 rounded-xl text-left transition-all hover:border-emerald-300 relative group overflow-hidden">
                                        <div class="text-[10px] font-bold uppercase text-slate-400 mb-1 tracking-tight">
                                            @if(is_string($variant->variant_values))
                                                {{ implode(' - ', json_decode($variant->variant_values, true)) }}
                                            @elseif(is_array($variant->variant_values))
                                                {{ implode(' - ', $variant->variant_values) }}
                                            @else
                                                Loại #{{ $loop->iteration }}
                                            @endif
                                        </div>

                                        <div class="font-bold text-lg text-slate-900">
                                            @if($variant->sale_price > 0)
                                                <span class="text-xs text-slate-400 line-through mr-1">{{ number_format($variant->price, 0, ',', '.') }}đ</span>
                                                <span class="text-emerald-600">{{ number_format($variant->sale_price, 0, ',', '.') }}đ</span>
                                            @else
                                                {{ number_format($variant->price, 0, ',', '.') }}đ
                                            @endif
                                        </div>

                                        <template x-if="selectedVariantId == {{ $variant->id }}">
                                            <div class="absolute top-2 right-2 text-emerald-600">
                                                <i class="fas fa-check-circle text-xs"></i>
                                            </div>
                                        </template>
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="text-slate-600 leading-relaxed mb-8">
                        <p>{{ $product->description }}</p>
                    </div>

                    @php
                        $isCompared = in_array($product->id, session('compare', []), true);
                    @endphp

                    <form action="{{ route('cart.add', $product->id) }}" method="POST" class="space-y-6">
                        @csrf
                        <input type="hidden" name="variant_id" :value="selectedVariantId">

                        <div class="flex items-center gap-4">
                            <div class="flex items-center border border-slate-200 rounded-xl overflow-hidden bg-slate-50">
                                <button type="button" @click="quantity > 1 ? quantity-- : null" class="px-4 py-3 hover:bg-slate-200 transition font-bold text-lg">-</button>
                                <input type="number" name="quantity" x-model="quantity" class="w-16 text-center bg-transparent border-none focus:ring-0 font-bold">
                                <button type="button" @click="(currentStock > 0 && quantity < currentStock) ? quantity++ : null" class="px-4 py-3 hover:bg-slate-200 transition font-bold text-lg">+</button>
                            </div>

                            <button type="submit" :disabled="isOutOfStock || (productType === 'variable' && !selectedVariantId)"
                                :class="(isOutOfStock || (productType === 'variable' && !selectedVariantId)) ? 'bg-slate-300 cursor-not-allowed text-slate-500' : 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-lg'"
                                class="flex-1 font-bold py-4 rounded-xl transition flex items-center justify-center gap-3 uppercase">
                                <i class="fas fa-shopping-cart"></i>
                                <span x-text="
                                    (productType === 'variable' && !selectedVariantId) ? 'Vui lòng chọn loại' :
                                    (isOutOfStock ? 'Hết hàng' : 'Thêm vào giỏ hàng')
                                "></span>
                            </button>
                        </div>
                    </form>

                    <div class="mt-4">
                        @if($isCompared)
                            <a href="{{ route('compare.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-emerald-600 hover:text-emerald-700">
                                <i class="fas fa-scale-balanced"></i>
                                <span>Đang có trong danh sách so sánh</span>
                            </a>
                        @else
                            <form action="{{ route('compare.add', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="inline-flex items-center gap-2 text-sm font-semibold text-sky-600 hover:text-sky-700">
                                    <i class="fas fa-scale-balanced"></i>
                                    <span>Thêm vào so sánh</span>
                                </button>
                            </form>
                        @endif
                    </div>

                    <div class="mt-8 pt-6 border-t border-slate-100 space-y-2 text-sm text-slate-500">
                        <p>Mã: <span class="text-slate-800 font-mono" x-text="currentSku || 'NS-{{ $product->id }}'"></span></p>
                        <p>Kho: <span class="font-bold" :class="currentStock > 0 ? 'text-emerald-600' : 'text-red-500'" x-text="currentStock > 0 ? currentStock + ' sản phẩm sẵn có' : 'Hết hàng'"></span></p>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-12 bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
            <div class="border-b border-slate-100 mb-8">
                <nav class="flex gap-8">
                    <button class="border-b-2 border-emerald-600 pb-4 font-bold text-emerald-600">Mô tả chi tiết</button>
                </nav>
            </div>
            <article class="prose prose-emerald max-w-none text-slate-600 leading-relaxed">
                {!! $product->content !!}
            </article>
        </div>

        <div class="mt-12 bg-white rounded-3xl shadow-sm border border-slate-100 p-8">
            <div class="flex items-center justify-between gap-4 mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900">Đánh giá sản phẩm</h2>
                    <p class="text-sm text-slate-500 mt-1">Hiển thị điểm trung bình và các nhận xét đã được gửi.</p>
                </div>
                <div class="text-right">
                    <div class="text-3xl font-black text-emerald-600">
                        {{ $product->reviews_count > 0 ? number_format((float) $product->reviews_avg_rating, 1) : '0.0' }}
                    </div>
                    <div class="text-sm text-slate-500">{{ number_format($product->reviews_count) }} đánh giá</div>
                </div>
            </div>

            @if($product->reviews_count > 0)
                <div class="space-y-4">
                    @foreach($product->approvedReviews as $review)
                        <div class="rounded-2xl border border-slate-100 bg-slate-50 px-5 py-4">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-full bg-emerald-100 text-emerald-700 flex items-center justify-center font-bold">
                                            {{ strtoupper(mb_substr($review->user->name ?? 'A', 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-slate-800">{{ $review->user->name ?? 'Khách hàng' }}</p>
                                            <p class="text-xs text-slate-400">{{ $review->created_at?->format('d/m/Y H:i') }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1 text-amber-400">
                                    @for($star = 1; $star <= 5; $star++)
                                        <i class="fas fa-star {{ $star <= $review->rating ? '' : 'text-slate-200' }}"></i>
                                    @endfor
                                </div>
                            </div>

                            @if($review->title)
                                <p class="mt-4 font-semibold text-slate-800">{{ $review->title }}</p>
                            @endif

                            <p class="mt-2 text-slate-600 leading-7">{{ $review->content }}</p>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-sm text-slate-500 italic">Chưa có đánh giá nào.</p>
            @endif
        </div>

        @if($relatedProducts->isNotEmpty())
            <div class="mt-12">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h2 class="text-2xl font-bold text-slate-900">Sản phẩm liên quan</h2>
                        <p class="text-sm text-slate-500 mt-1">Cùng danh mục hoặc mức giá tương tự.</p>
                    </div>
                    <a href="{{ route('products.index') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700">Xem thêm</a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $relatedProduct)
                        <x-product-card :product="$relatedProduct" />
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>

<script>
    function productDetail() {
        return {
            productType: '{{ $product->product_type }}',
            quantity: 1,
            currentPrice: {{ $product->isVariable() ? 0 : ($product->price ?? 0) }},
            currentSalePrice: {{ $product->isVariable() ? 0 : ($product->sale_price ?? 0) }},
            currentStock: {{ $product->isVariable() ? 0 : ($product->stock ?? 0) }},
            currentSku: '',
            selectedVariantId: '',
            activeImage: '{{ asset('storage/' . $product->image) }}',
            isOutOfStock: {{ $product->isVariable() ? 'true' : ($product->stock <= 0 ? 'true' : 'false') }},

            selectVariant(variant) {
                if (this.selectedVariantId === variant.id) {
                    this.selectedVariantId = '';
                    this.currentPrice = 0;
                    this.currentSalePrice = 0;
                    this.isOutOfStock = true;
                    this.activeImage = '{{ asset('storage/' . $product->image) }}';
                    return;
                }

                this.selectedVariantId = variant.id;
                this.currentPrice = parseInt(variant.price);
                this.currentSalePrice = variant.sale_price ? parseInt(variant.sale_price) : 0;
                this.currentStock = parseInt(variant.stock);
                this.currentSku = variant.sku;
                this.isOutOfStock = variant.stock <= 0;

                if (variant.image) {
                    this.activeImage = `/storage/${variant.image}`;
                } else {
                    this.activeImage = '{{ asset('storage/' . $product->image) }}';
                }
            },

            formatPrice(price) {
                if (!price || price === 0) return '';
                return new Intl.NumberFormat('vi-VN').format(price) + 'đ';
            }
        }
    }
</script>
@endsection
