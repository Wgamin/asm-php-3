@extends('admin.layouts.master')

@section('title', 'Chi tiết sản phẩm')

@section('content')
    @php
        $mainImage = $product->image ? asset('storage/' . $product->image) : null;
    @endphp

    <div class="mx-auto max-w-7xl space-y-8" x-data="{ activeImage: @js($mainImage) }">
        <section class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="admin-kicker">Sản phẩm & kho hàng</p>
                <h1 class="admin-headline mt-2 text-4xl font-bold tracking-[-0.05em] text-[var(--admin-text)]">{{ $product->name }}</h1>
                <p class="admin-copy mt-3 max-w-3xl text-sm">Theo dõi hồ sơ hàng hóa, thư viện ảnh, giá bán, giá vốn và cấu hình biến thể của sản phẩm ngay trên một màn hình.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.products.index') }}" class="admin-btn-secondary">
                    <i class="fas fa-arrow-left text-sm"></i>
                    Danh sách sản phẩm
                </a>
                <a href="{{ route('admin.products.edit', $product) }}" class="admin-btn-primary">
                    <i class="fas fa-pen text-sm"></i>
                    Chỉnh sửa sản phẩm
                </a>
            </div>
        </section>

        <div class="grid gap-8 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="space-y-8">
                <section class="admin-surface-card overflow-hidden">
                    <div class="grid gap-0 lg:grid-cols-[1fr_0.95fr]">
                        <div class="border-b border-[rgba(112,122,108,0.12)] bg-[rgba(242,244,246,0.55)] p-6 lg:border-b-0 lg:border-r">
                            <div class="overflow-hidden rounded-[1.25rem] bg-white shadow-sm">
                                @if($mainImage)
                                    <img :src="activeImage || '{{ $mainImage }}'" alt="{{ $product->name }}" class="h-[24rem] w-full object-cover">
                                @else
                                    <div class="flex h-[24rem] items-center justify-center bg-[var(--admin-surface-low)] text-[var(--admin-text-muted)]">
                                        <div class="text-center">
                                            <i class="fas fa-image text-4xl opacity-40"></i>
                                            <p class="mt-3 text-sm">Chưa có ảnh đại diện</p>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            @if($mainImage || $product->images->isNotEmpty())
                                <div class="mt-4 grid grid-cols-5 gap-3">
                                    @if($mainImage)
                                        <button type="button" @click="activeImage = '{{ $mainImage }}'" class="overflow-hidden rounded-[1rem] border border-[rgba(112,122,108,0.16)] bg-white shadow-sm">
                                            <img src="{{ $mainImage }}" alt="{{ $product->name }}" class="h-20 w-full object-cover">
                                        </button>
                                    @endif
                                    @foreach($product->images as $galleryImage)
                                        <button type="button" @click="activeImage = '{{ asset('storage/' . $galleryImage->image_path) }}'" class="overflow-hidden rounded-[1rem] border border-[rgba(112,122,108,0.16)] bg-white shadow-sm">
                                            <img src="{{ asset('storage/' . $galleryImage->image_path) }}" alt="{{ $product->name }}" class="h-20 w-full object-cover">
                                        </button>
                                    @endforeach
                                </div>
                            @endif
                        </div>

                        <div class="p-7">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="admin-badge admin-badge--success">{{ $product->category?->name ?? 'Chưa phân loại' }}</span>
                                <span class="admin-badge admin-badge--muted">{{ $product->isVariable() ? 'Biến thể' : 'Sản phẩm đơn' }}</span>
                            </div>

                            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                                <article class="admin-panel-muted p-4">
                                    <p class="admin-kicker">Giá hiệu lực</p>
                                    <p class="mt-3 text-2xl font-bold text-[var(--admin-text)]">{{ number_format($product->effective_price, 0, ',', '.') }}đ</p>
                                </article>
                                <article class="admin-panel-muted p-4">
                                    <p class="admin-kicker">Giá vốn</p>
                                    <p class="mt-3 text-2xl font-bold text-[var(--admin-text)]">{{ number_format($product->effective_cost_price, 0, ',', '.') }}đ</p>
                                </article>
                                <article class="admin-panel-muted p-4">
                                    <p class="admin-kicker">Tồn kho</p>
                                    <p class="mt-3 text-2xl font-bold text-[var(--admin-text)]">{{ $product->isVariable() ? number_format($product->variants->sum('stock'), 0, ',', '.') : number_format($product->stock ?? 0, 0, ',', '.') }}</p>
                                </article>
                                <article class="admin-panel-muted p-4">
                                    <p class="admin-kicker">Khối lượng</p>
                                    <p class="mt-3 text-2xl font-bold text-[var(--admin-text)]">{{ number_format($product->weight_grams ?? 0, 0, ',', '.') }}g</p>
                                </article>
                            </div>

                            <dl class="mt-6 space-y-4 text-sm">
                                <div class="flex items-center justify-between gap-4 border-b border-[rgba(112,122,108,0.12)] pb-4">
                                    <dt class="text-[var(--admin-text-muted)]">Mã sản phẩm</dt>
                                    <dd class="font-semibold text-[var(--admin-text)]">#{{ $product->id }}</dd>
                                </div>
                                <div class="flex items-center justify-between gap-4 border-b border-[rgba(112,122,108,0.12)] pb-4">
                                    <dt class="text-[var(--admin-text-muted)]">Ngày tạo</dt>
                                    <dd class="font-semibold text-[var(--admin-text)]">{{ $product->created_at?->format('d/m/Y H:i') }}</dd>
                                </div>
                                <div class="flex items-center justify-between gap-4">
                                    <dt class="text-[var(--admin-text-muted)]">Cập nhật gần nhất</dt>
                                    <dd class="font-semibold text-[var(--admin-text)]">{{ $product->updated_at?->format('d/m/Y H:i') }}</dd>
                                </div>
                            </dl>
                        </div>
                    </div>
                </section>

                <section class="admin-surface-card p-7">
                    <div class="mb-5">
                        <p class="admin-kicker">Mô tả ngắn</p>
                        <h2 class="admin-headline mt-2 text-2xl font-bold tracking-[-0.03em]">Tóm tắt bán hàng</h2>
                    </div>
                    <p class="text-sm leading-7 text-[var(--admin-text-muted)]">{{ $product->description ?: 'Chưa có mô tả ngắn cho sản phẩm này.' }}</p>
                </section>

                <section class="admin-surface-card p-7">
                    <div class="mb-5">
                        <p class="admin-kicker">Nội dung chi tiết</p>
                        <h2 class="admin-headline mt-2 text-2xl font-bold tracking-[-0.03em]">Thông tin hiển thị trên storefront</h2>
                    </div>
                    <div class="prose max-w-none text-sm leading-7 text-[var(--admin-text-muted)]">
                        {!! $product->content ?: '<p>Chưa có nội dung chi tiết.</p>' !!}
                    </div>
                </section>
            </div>

            <div class="space-y-8">
                <section class="admin-panel-muted p-7">
                    <div class="mb-5">
                        <p class="admin-kicker">Tập ảnh</p>
                        <h2 class="admin-headline mt-2 text-2xl font-bold tracking-[-0.03em]">Thư viện sản phẩm</h2>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        @forelse($product->images as $galleryImage)
                            <button type="button" @click="activeImage = '{{ asset('storage/' . $galleryImage->image_path) }}'" class="overflow-hidden rounded-[1rem] bg-white shadow-sm">
                                <img src="{{ asset('storage/' . $galleryImage->image_path) }}" alt="{{ $product->name }}" class="h-32 w-full object-cover">
                            </button>
                        @empty
                            <div class="col-span-2 rounded-[1rem] bg-white px-5 py-8 text-center text-sm text-[var(--admin-text-muted)]">
                                Chưa có ảnh phụ cho sản phẩm này.
                            </div>
                        @endforelse
                    </div>
                </section>

                @if($product->isVariable())
                    <section class="admin-surface-card p-7">
                        <div class="mb-5 flex items-center justify-between gap-4">
                            <div>
                                <p class="admin-kicker">Biến thể</p>
                                <h2 class="admin-headline mt-2 text-2xl font-bold tracking-[-0.03em]">Danh sách cấu hình</h2>
                            </div>
                            <span class="admin-badge admin-badge--info normal-case tracking-normal">{{ $product->variants->count() }} biến thể</span>
                        </div>

                        <div class="space-y-4">
                            @foreach($product->variants as $variant)
                                <article class="rounded-[1rem] bg-[var(--admin-surface-low)] p-4">
                                    <div class="flex items-start justify-between gap-4">
                                        <div>
                                            <p class="font-semibold text-[var(--admin-text)]">{{ $variant->sku ?: 'Chưa có SKU' }}</p>
                                            <div class="mt-2 flex flex-wrap gap-2">
                                                @foreach(($variant->variant_values ?? []) as $key => $value)
                                                    <span class="admin-badge admin-badge--muted normal-case tracking-normal">{{ $key }}: {{ $value }}</span>
                                                @endforeach
                                            </div>
                                        </div>
                                        @if($variant->image)
                                            <img src="{{ asset('storage/' . $variant->image) }}" alt="{{ $variant->sku }}" class="h-14 w-14 rounded-[0.9rem] object-cover">
                                        @endif
                                    </div>
                                    <div class="mt-4 grid gap-3 sm:grid-cols-4">
                                        <div class="rounded-[0.9rem] bg-white px-3 py-3 text-sm">
                                            <p class="admin-kicker">Giá bán</p>
                                            <p class="mt-2 font-semibold text-[var(--admin-text)]">{{ number_format($variant->price, 0, ',', '.') }}đ</p>
                                        </div>
                                        <div class="rounded-[0.9rem] bg-white px-3 py-3 text-sm">
                                            <p class="admin-kicker">Giá giảm</p>
                                            <p class="mt-2 font-semibold text-[var(--admin-text)]">{{ $variant->sale_price ? number_format($variant->sale_price, 0, ',', '.') . 'đ' : 'Không có' }}</p>
                                        </div>
                                        <div class="rounded-[0.9rem] bg-white px-3 py-3 text-sm">
                                            <p class="admin-kicker">Giá vốn</p>
                                            <p class="mt-2 font-semibold text-[var(--admin-text)]">{{ number_format($variant->cost_price ?? 0, 0, ',', '.') }}đ</p>
                                        </div>
                                        <div class="rounded-[0.9rem] bg-white px-3 py-3 text-sm">
                                            <p class="admin-kicker">Tồn kho</p>
                                            <p class="mt-2 font-semibold text-[var(--admin-text)]">{{ number_format($variant->stock, 0, ',', '.') }}</p>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    </section>
                @endif
            </div>
        </div>
    </div>
@endsection
