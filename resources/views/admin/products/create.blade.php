@extends('admin.layouts.master')

@section('title', 'Thêm sản phẩm')

@section('content')
    <div class="mx-auto max-w-7xl space-y-8" x-data="productFormCreate()">
        <section class="flex flex-col gap-5 xl:flex-row xl:items-end xl:justify-between">
            <div>
                <p class="admin-kicker">Sản phẩm & kho hàng</p>
                <h1 class="admin-headline mt-2 text-4xl font-bold tracking-[-0.05em] text-[var(--admin-text)]">Tạo sản phẩm mới</h1>
                <p class="admin-copy mt-3 max-w-3xl text-sm">Thiết lập thông tin cơ bản, giá bán, tồn kho, khối lượng và thư viện ảnh cho một nông sản mới trong hệ thống.</p>
            </div>

            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.products.index') }}" class="admin-btn-secondary">
                    <i class="fas fa-arrow-left text-sm"></i>
                    Quay lại danh sách
                </a>
                <div class="admin-glass inline-flex rounded-[1rem] border border-[rgba(112,122,108,0.12)] p-1 shadow-sm">
                    <button
                        type="button"
                        @click="productType = 'simple'"
                        class="rounded-[0.8rem] px-4 py-2 text-sm font-semibold transition"
                        :class="productType === 'simple' ? 'bg-[var(--admin-primary)] text-white shadow-sm' : 'text-[var(--admin-text-muted)] hover:text-[var(--admin-text)]'"
                    >
                        Sản phẩm thường
                    </button>
                    <button
                        type="button"
                        @click="productType = 'variable'"
                        class="rounded-[0.8rem] px-4 py-2 text-sm font-semibold transition"
                        :class="productType === 'variable' ? 'bg-[var(--admin-primary)] text-white shadow-sm' : 'text-[var(--admin-text-muted)] hover:text-[var(--admin-text)]'"
                    >
                        Sản phẩm biến thể
                    </button>
                </div>
            </div>
        </section>

        @if ($errors->any())
            <section class="rounded-[1.2rem] bg-[rgba(255,218,214,0.75)] px-6 py-5 text-sm text-[var(--admin-danger-text)] shadow-sm">
                <p class="font-bold">Có lỗi dữ liệu cần xử lý.</p>
                <ul class="mt-3 list-disc space-y-1 pl-5">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </section>
        @endif

        <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf
            <input type="hidden" name="product_type" :value="productType">

            <div class="grid gap-8 xl:grid-cols-[1.5fr_0.9fr]">
                <div class="space-y-8">
                    <section class="admin-surface-card p-7">
                        <div class="mb-6">
                            <p class="admin-kicker">Thông tin cơ bản</p>
                            <h2 class="admin-headline mt-2 text-2xl font-bold tracking-[-0.03em]">Hồ sơ sản phẩm</h2>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2">
                            <div class="md:col-span-2">
                                <label class="admin-field-label">Tên sản phẩm</label>
                                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Ví dụ: Gạo ST25 Sóc Trăng">
                            </div>

                            <div>
                                <label class="admin-field-label">Danh mục</label>
                                <select name="category_id" required>
                                    <option value="">Chọn danh mục</option>
                                    @foreach($categories as $cat)
                                        <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                            {{ $cat->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="admin-field-label">Khối lượng mặc định (gram)</label>
                                <input type="number" name="weight_grams" min="100" max="50000" value="{{ old('weight_grams', 500) }}" required placeholder="500">
                            </div>
                        </div>
                    </section>

                    <section class="admin-surface-card p-7" x-show="productType === 'simple'" x-transition>
                        <div class="mb-6">
                            <p class="admin-kicker">Giá & tồn kho</p>
                            <h2 class="admin-headline mt-2 text-2xl font-bold tracking-[-0.03em]">Thiết lập hàng hóa đơn</h2>
                        </div>

                        <div class="grid gap-5 md:grid-cols-2 xl:grid-cols-4">
                            <div>
                                <label class="admin-field-label">Giá bán</label>
                                <input type="number" name="price" min="0" value="{{ old('price') }}" placeholder="0">
                            </div>
                            <div>
                                <label class="admin-field-label">Giá khuyến mãi</label>
                                <input type="number" name="sale_price" min="0" value="{{ old('sale_price') }}" placeholder="0">
                            </div>
                            <div>
                                <label class="admin-field-label">Giá vốn</label>
                                <input type="number" name="cost_price" min="0" value="{{ old('cost_price') }}" placeholder="0">
                            </div>
                            <div>
                                <label class="admin-field-label">Tồn kho</label>
                                <input type="number" name="stock" min="0" value="{{ old('stock', 0) }}" placeholder="0">
                            </div>
                        </div>
                    </section>

                    <section class="admin-panel-muted p-6" x-show="productType === 'variable'" x-transition>
                        <div class="flex items-start gap-3">
                            <span class="material-symbols-outlined text-[var(--admin-primary)]">deployed_code_history</span>
                            <div>
                                <p class="font-bold text-[var(--admin-text)]">Sản phẩm đang dùng biến thể</p>
                                <p class="mt-2 text-sm leading-7 text-[var(--admin-text-muted)]">Giá bán, giá vốn và tồn kho sẽ được quản lý riêng theo từng biến thể bên dưới. Mỗi biến thể có thể gắn thuộc tính, SKU và hình ảnh độc lập.</p>
                            </div>
                        </div>
                    </section>

                    @include('admin.products.Components.variant-form')

                    <section class="admin-surface-card p-7">
                        @include('admin.products.Components.description-form')
                    </section>
                </div>

                <div class="space-y-8">
                    <section class="admin-surface-card p-7">
                        @include('admin.products.Components.image-upload')
                    </section>

                    <section class="admin-panel-muted p-7">
                        <p class="admin-kicker">Vận hành</p>
                        <h3 class="admin-headline mt-2 text-2xl font-bold tracking-[-0.03em]">Gợi ý nhập liệu</h3>
                        <div class="mt-5 space-y-4 text-sm leading-7 text-[var(--admin-text-muted)]">
                            <div class="rounded-[1rem] bg-white px-4 py-4">
                                <p class="font-semibold text-[var(--admin-text)]">Ảnh đại diện</p>
                                <p class="mt-2">Nên dùng ảnh vuông, nền sáng, kích thước tối thiểu 1200 x 1200 để đồng bộ với storefront.</p>
                            </div>
                            <div class="rounded-[1rem] bg-white px-4 py-4">
                                <p class="font-semibold text-[var(--admin-text)]">Biến thể</p>
                                <p class="mt-2">Chỉ tạo biến thể khi mỗi tổ hợp thật sự cần quản lý giá, ảnh hoặc tồn kho riêng.</p>
                            </div>
                        </div>
                    </section>
                </div>
            </div>

            <div class="admin-glass sticky bottom-4 z-20 flex flex-col gap-3 rounded-[1.2rem] border border-[rgba(112,122,108,0.12)] px-5 py-4 shadow-[0_30px_60px_-30px_rgba(25,28,30,0.22)] md:flex-row md:items-center md:justify-between">
                <div>
                    <p class="text-sm font-bold text-[var(--admin-text)]">Sẵn sàng tạo sản phẩm</p>
                    <p class="mt-1 text-xs text-[var(--admin-text-muted)]">Hệ thống sẽ lưu thông tin chính, thư viện ảnh và biến thể ngay trong một lần gửi.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('admin.products.index') }}" class="admin-btn-ghost">Hủy bỏ</a>
                    <button type="submit" class="admin-btn-primary">
                        <i class="fas fa-floppy-disk text-sm"></i>
                        Lưu sản phẩm
                    </button>
                </div>
            </div>
        </form>
    </div>
@endsection

@push('scripts')
    <script>
        function productFormCreate() {
            return {
                productType: @json(old('product_type', 'simple')),
                variants: @json(old('variants', [])),
                addVariant() {
                    this.variants.push({
                        sku: '',
                        price: '',
                        sale_price: '',
                        cost_price: '',
                        stock: '',
                        image_url: null,
                        attributes: {},
                    });
                },
                removeVariant(index) {
                    this.variants.splice(index, 1);
                },
            };
        }
    </script>
@endpush
