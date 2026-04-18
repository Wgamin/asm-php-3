@extends('admin.layouts.master')

@section('title', 'Thuộc tính')

@section('content')
    <div class="mx-auto max-w-7xl">
        <div class="grid gap-6 xl:grid-cols-[360px_1fr]">
            <section class="admin-surface-card h-fit p-6 xl:sticky xl:top-28">
                <p class="admin-kicker">Variants setup</p>
                <h1 class="admin-headline mt-2 text-3xl font-bold tracking-[-0.04em] text-[var(--admin-text)]">Tạo thuộc tính</h1>
                <x-admin-info class="mt-3">
                    Thiết lập các nhóm phân loại như kích cỡ, độ tươi, trọng lượng hoặc màu sắc để phục vụ sản phẩm biến thể.
                </x-admin-info>

                <form action="{{ route('admin.attributes.store') }}" method="POST" class="mt-6 space-y-5">
                    @csrf
                    <div>
                        <label class="admin-field-label">Tên thuộc tính</label>
                        <input type="text" name="name" required placeholder="Ví dụ: Kích cỡ, Độ tươi...">
                        @error('name')
                            <p class="mt-2 text-sm text-[var(--admin-danger-text)]">{{ $message }}</p>
                        @enderror
                    </div>
                    <button type="submit" class="admin-btn-primary w-full">
                        <i class="fas fa-plus text-sm"></i>
                        Lưu thuộc tính
                    </button>
                </form>
            </section>

            <section class="space-y-5">
                @forelse($attributes as $attr)
                    <article class="admin-surface-card overflow-hidden">
                        <div class="flex flex-wrap items-start justify-between gap-4 px-6 py-5">
                            <div>
                                <p class="admin-kicker">Attribute #{{ $attr->id }}</p>
                                <h3 class="admin-headline mt-2 text-2xl font-bold tracking-[-0.03em] text-[var(--admin-text)]">{{ $attr->name }}</h3>
                            </div>
                            <form action="{{ route('admin.attributes.destroy', $attr->id) }}" method="POST" onsubmit="return confirm('Xóa thuộc tính này sẽ xóa luôn tất cả giá trị con. Bạn chắc chứ?')">
                                @csrf
                                @method('DELETE')
                                <button class="admin-action-icon hover:!bg-[rgba(255,218,214,0.45)] hover:!text-[var(--admin-danger-text)]" title="Xóa thuộc tính">
                                    <i class="fas fa-trash text-sm"></i>
                                </button>
                            </form>
                        </div>

                        <div class="px-6 pb-6">
                            <div class="rounded-[1.2rem] bg-[var(--admin-surface-low)] p-5">
                                <p class="admin-field-label">Các giá trị hiện có</p>
                                <div class="flex flex-wrap gap-2">
                                    @forelse($attr->attributeValues as $val)
                                        <div class="inline-flex items-center gap-2 rounded-full bg-white px-4 py-2 text-sm font-semibold text-[var(--admin-text)] shadow-[0_18px_30px_-24px_rgba(25,28,30,0.2)]">
                                            <span>{{ $val->value }}</span>
                                            <form action="{{ route('admin.attributes.destroyValue', $val->id) }}" method="POST">
                                                @csrf
                                                @method('DELETE')
                                                <button class="text-[var(--admin-text-muted)] transition hover:text-[var(--admin-danger-text)]">
                                                    <i class="fas fa-xmark text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    @empty
                                        <p class="text-sm text-[var(--admin-text-muted)]">Chưa có giá trị nào cho thuộc tính này.</p>
                                    @endforelse
                                </div>

                                <form action="{{ route('admin.attributes.storeValue', $attr->id) }}" method="POST" class="mt-5 flex gap-3">
                                    @csrf
                                    <input type="text" name="value" required placeholder="Thêm giá trị mới cho {{ $attr->name }}...">
                                    <button type="submit" class="admin-btn-primary whitespace-nowrap">
                                        <i class="fas fa-plus text-xs"></i>
                                        Thêm
                                    </button>
                                </form>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="admin-empty-state rounded-[1.25rem] bg-[var(--admin-surface-low)] py-24">
                        <i class="fas fa-layer-group text-4xl opacity-30"></i>
                        <p class="text-sm">Chưa có thuộc tính nào được tạo.</p>
                    </div>
                @endforelse
            </section>
        </div>
    </div>
@endsection
