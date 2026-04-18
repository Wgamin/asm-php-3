@extends('admin.layouts.master')

@section('title', 'Sản phẩm')

@section('content')
    <div class="mx-auto max-w-7xl space-y-8">
        @if(session('success'))
            <div class="rounded-[1.2rem] bg-[rgba(223,243,219,0.85)] px-5 py-4 text-sm font-semibold text-[var(--admin-success-text)]">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="rounded-[1.2rem] bg-[rgba(255,218,214,0.7)] px-5 py-4 text-sm font-semibold text-[var(--admin-danger-text)]">
                {{ session('error') }}
            </div>
        @endif

        <section class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="admin-kicker">Core module</p>
                <h1 class="admin-headline mt-2 text-4xl font-bold tracking-[-0.05em] text-[var(--admin-text)]">Danh sách sản phẩm</h1>
                <x-admin-info class="mt-3">
                    Quản lý nông sản, biến thể, giá bán, tồn kho, ảnh và dữ liệu import từ Excel trong cùng một không gian thao tác.
                </x-admin-info>
            </div>
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('admin.products.create') }}" class="admin-btn-primary">
                    <i class="fas fa-plus text-sm"></i>
                    Thêm sản phẩm
                </a>
            </div>
        </section>

        <section class="grid gap-5 md:grid-cols-3">
            <article class="admin-surface-card admin-card-accent p-6">
                <p class="admin-kicker">Tổng sản phẩm</p>
                <p class="admin-headline mt-3 text-4xl font-bold tracking-[-0.05em] text-[var(--admin-text)]">{{ number_format($products->total(), 0, ',', '.') }}</p>
                <p class="mt-3 text-sm text-[var(--admin-text-muted)]">Hiển thị {{ $products->count() }} sản phẩm trên trang hiện tại.</p>
            </article>

            <article class="admin-surface-card p-6">
                <p class="admin-kicker">Cần bổ sung tồn kho</p>
                <p class="admin-headline mt-3 text-4xl font-bold tracking-[-0.05em] text-[var(--admin-text)]">{{ number_format($products->getCollection()->filter(fn ($product) => ($product->product_type === 'simple' ? ($product->stock ?? 0) : $product->variants->sum('stock')) <= 5)->count(), 0, ',', '.') }}</p>
                <p class="mt-3 text-sm text-[var(--admin-text-muted)]">Dựa trên dữ liệu sản phẩm hiện đang hiển thị.</p>
            </article>

            <article class="admin-surface-card p-6">
                <p class="admin-kicker">Sản phẩm biến thể</p>
                <p class="admin-headline mt-3 text-4xl font-bold tracking-[-0.05em] text-[var(--admin-text)]">{{ number_format($products->getCollection()->where('product_type', 'variable')->count(), 0, ',', '.') }}</p>
                <p class="mt-3 text-sm text-[var(--admin-text-muted)]">Nhóm nông sản có nhiều lựa chọn SKU, giá và tồn kho riêng.</p>
            </article>
        </section>

        <section class="admin-panel p-6">
            <form action="{{ route('admin.products.import') }}" method="POST" enctype="multipart/form-data" class="w-full space-y-4 rounded-[1.25rem] bg-[var(--admin-surface-low)] p-5">
                @csrf
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="admin-kicker">Import dữ liệu</p>
                        <h3 class="admin-headline mt-2 text-2xl font-bold tracking-[-0.03em]">Nhập sản phẩm từ Excel/CSV</h3>
                        <div class="mt-3 flex items-center gap-2">
                            <x-admin-info>
                                Hỗ trợ cả sản phẩm thường và sản phẩm biến thể theo nhóm handle.
                            </x-admin-info>
                            <p class="admin-kicker">Cột được hỗ trợ</p>
                            <x-admin-info>
                                <div class="space-y-3">
                                    <p>
                                        <span class="font-semibold text-[var(--admin-text)]">Cơ bản:</span>
                                        <code>handle</code>, <code>name</code>, <code>category_id</code> hoặc <code>category</code>,
                                        <code>product_type</code>, <code>price</code>, <code>sale_price</code>, <code>cost_price</code>,
                                        <code>weight_grams</code>, <code>image</code>, <code>gallery_images</code>.
                                    </p>
                                    <p>
                                        <span class="font-semibold text-[var(--admin-text)]">Biến thể:</span>
                                        <code>option1_name</code>/<code>option1_value</code>, <code>variant_sku</code>,
                                        <code>variant_price</code>, <code>variant_sale_price</code>, <code>variant_cost_price</code>,
                                        <code>variant_stock</code>, <code>variant_image</code>.
                                    </p>
                                </div>
                            </x-admin-info>
                        </div>
                    </div>
                    <span class="admin-badge admin-badge--muted">Import</span>
                </div>
                <input type="file" name="file_excel" accept=".xlsx,.xls,.csv" required class="cursor-pointer file:mr-4 file:rounded-xl file:border-0 file:bg-[rgba(32,98,35,0.12)] file:px-4 file:py-3 file:text-sm file:font-bold file:text-[#206223]" />
                <button type="submit" class="admin-btn-primary">
                    <i class="fas fa-file-import text-sm"></i>
                    Thực hiện import
                </button>
            </form>
        </section>

        @if(session('import_failures') && count(session('import_failures')))
            <section class="admin-panel p-6">
                <p class="admin-kicker">Import failures</p>
                <h3 class="admin-headline mt-2 text-2xl font-bold tracking-[-0.03em]">Một số dòng import bị lỗi</h3>
                <div class="mt-5 grid gap-3">
                    @foreach(array_slice(session('import_failures'), 0, 5) as $failure)
                        <div class="rounded-[1.1rem] bg-[rgba(255,237,216,0.58)] px-4 py-4 text-sm">
                            <p class="font-bold text-[var(--admin-text)]">Dòng {{ $failure['row'] }} - {{ $failure['name'] }}</p>
                            <p class="mt-2 text-[var(--admin-warning-text)]">{{ implode(' | ', $failure['errors']) }}</p>
                        </div>
                    @endforeach
                </div>
            </section>
        @endif

        <section class="admin-panel p-6">
            <form method="GET" action="{{ route('admin.products.index') }}" class="grid gap-4 md:grid-cols-[1fr_auto]">
                <div>
                    <label class="admin-field-label">Tìm kiếm sản phẩm</label>
                    <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tên sản phẩm..." />
                </div>
                <div class="flex items-end gap-3">
                    <button type="submit" class="admin-btn-primary">
                        <i class="fas fa-filter text-sm"></i>
                        Lọc dữ liệu
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="admin-btn-secondary">
                        <i class="fas fa-rotate-right text-sm"></i>
                        Reset
                    </a>
                </div>
            </form>
        </section>

        <section class="admin-table-shell">
            <div class="overflow-x-auto">
                <table class="min-w-[1080px]">
                    <thead>
                        <tr>
                            <th class="px-7 py-4 text-left">Sản phẩm</th>
                            <th class="px-5 py-4 text-left">Loại</th>
                            <th class="px-5 py-4 text-left">Giá bán</th>
                            <th class="px-5 py-4 text-left">Danh mục</th>
                            <th class="px-5 py-4 text-left">Tồn kho</th>
                            <th class="px-7 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                            @php
                                $isVariable = $product->product_type === 'variable';
                                $minVariantPrice = $isVariable ? $product->variants->map(fn ($variant) => $variant->sale_price && $variant->sale_price > 0 ? $variant->sale_price : $variant->price)->filter()->min() : null;
                                $stock = $isVariable ? $product->variants->sum('stock') : ($product->stock ?? 0);
                                $stockClass = $stock <= 5 ? 'admin-badge admin-badge--warning' : 'admin-badge admin-badge--success';
                            @endphp
                            <tr>
                                <td class="px-7 py-5">
                                    <div class="flex items-center gap-4">
                                        <img
                                            src="{{ $product->image ? asset('storage/' . $product->image) : 'https://ui-avatars.com/api/?name=' . urlencode($product->name) . '&background=206223&color=fff' }}"
                                            alt="{{ $product->name }}"
                                            class="h-14 w-14 rounded-2xl object-cover"
                                        >
                                        <div class="min-w-0">
                                            <p class="truncate text-sm font-bold text-[var(--admin-text)]">{{ $product->name }}</p>
                                            <p class="mt-1 text-xs text-[var(--admin-text-muted)]">{{ \Illuminate\Support\Str::limit($product->description, 64) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-5">
                                    <span class="{{ $isVariable ? 'admin-badge admin-badge--info' : 'admin-badge admin-badge--muted' }}">
                                        {{ $isVariable ? 'Biến thể' : 'Thường' }}
                                    </span>
                                </td>
                                <td class="px-5 py-5 text-sm font-bold text-[var(--admin-text)]">
                                    @if($isVariable)
                                        Từ {{ number_format((float) $minVariantPrice, 0, ',', '.') }}đ
                                    @else
                                        {{ number_format((float) ($product->sale_price && $product->sale_price > 0 ? $product->sale_price : $product->price), 0, ',', '.') }}đ
                                    @endif
                                </td>
                                <td class="px-5 py-5 text-sm text-[var(--admin-text-muted)]">{{ $product->category->name ?? 'Chưa phân loại' }}</td>
                                <td class="px-5 py-5">
                                    <span class="{{ $stockClass }}">{{ number_format($stock, 0, ',', '.') }} SP</span>
                                </td>
                                <td class="px-7 py-5">
                                    <div class="flex justify-end gap-2">
                                        <a href="{{ route('admin.products.show', $product) }}" class="admin-action-icon" title="Chi tiết">
                                            <i class="fas fa-eye text-sm"></i>
                                        </a>
                                        <a href="{{ route('admin.products.edit', $product) }}" class="admin-action-icon" title="Chỉnh sửa">
                                            <i class="fas fa-pen text-sm"></i>
                                        </a>
                                        <form action="{{ route('admin.products.destroy', $product) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="admin-action-icon hover:!bg-[rgba(255,218,214,0.45)] hover:!text-[var(--admin-danger-text)]" title="Xóa">
                                                <i class="fas fa-trash text-sm"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-7 py-20">
                                    <div class="admin-empty-state">
                                        <i class="fas fa-box-open text-4xl opacity-30"></i>
                                        <p class="text-sm">Không tìm thấy sản phẩm nào phù hợp với bộ lọc hiện tại.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($products->hasPages())
                <div class="admin-pagination-shell border-t border-[rgba(112,122,108,0.12)] px-6 py-5">
                    {{ $products->appends(request()->query())->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
