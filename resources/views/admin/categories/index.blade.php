@extends('admin.layouts.master')

@section('title', 'Danh mục')

@section('content')
    <div class="mx-auto max-w-7xl" x-data="{ editingId: null }">
        <div class="grid gap-6 xl:grid-cols-[360px_1fr]">
            <section class="admin-surface-card h-fit p-6 xl:sticky xl:top-28">
                <p class="admin-kicker">Taxonomy</p>
                <h1 class="admin-headline mt-2 text-3xl font-bold tracking-[-0.04em] text-[var(--admin-text)]">Thêm danh mục</h1>
                <p class="admin-copy mt-3 text-sm">Tạo cấu trúc phân cấp để tổ chức nhóm nông sản và phục vụ lọc sản phẩm ngoài storefront.</p>

                <form action="{{ route('admin.categories.store') }}" method="POST" class="mt-6 space-y-5">
                    @csrf
                    <div>
                        <label class="admin-field-label">Tên danh mục</label>
                        <input type="text" name="name" placeholder="Ví dụ: Trái cây nhập khẩu..." required>
                    </div>
                    <div>
                        <label class="admin-field-label">Danh mục cha</label>
                        <select name="parent_id">
                            <option value="">Là danh mục gốc</option>
                            @foreach($parentCategories as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="admin-btn-primary w-full">
                        <i class="fas fa-plus text-sm"></i>
                        Lưu danh mục
                    </button>
                </form>
            </section>

            <section class="admin-table-shell">
                <div class="px-7 py-6">
                    <p class="admin-kicker">Hierarchy</p>
                    <h2 class="admin-headline mt-2 text-3xl font-bold tracking-[-0.04em] text-[var(--admin-text)]">Danh sách danh mục</h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="min-w-[760px]">
                        <thead>
                            <tr>
                                <th class="px-7 py-4 text-left">Tên danh mục</th>
                                <th class="px-5 py-4 text-left">Danh mục cha</th>
                                <th class="px-5 py-4 text-left">Sản phẩm</th>
                                <th class="px-7 py-4 text-right">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($categories as $cat)
                                <tr>
                                    <td class="px-7 py-5">
                                        <div x-show="editingId !== {{ $cat->id }}">
                                            <p class="text-sm font-bold text-[var(--admin-text)]">{{ $cat->name }}</p>
                                        </div>

                                        <div x-show="editingId === {{ $cat->id }}" x-cloak>
                                            <form action="{{ route('admin.categories.update', $cat->id) }}" method="POST" class="space-y-3">
                                                @csrf
                                                @method('PUT')
                                                <input type="text" name="name" value="{{ $cat->name }}">
                                                <select name="parent_id">
                                                    <option value="">Danh mục gốc</option>
                                                    @foreach($parentCategories as $parent)
                                                        @if($parent->id !== $cat->id)
                                                            <option value="{{ $parent->id }}" {{ $cat->parent_id == $parent->id ? 'selected' : '' }}>{{ $parent->name }}</option>
                                                        @endif
                                                    @endforeach
                                                </select>
                                                <div class="flex justify-end gap-2">
                                                    <button type="button" @click="editingId = null" class="admin-btn-ghost !px-4 !py-2">Hủy</button>
                                                    <button type="submit" class="admin-btn-primary !px-4 !py-2">
                                                        <i class="fas fa-floppy-disk text-xs"></i>
                                                        Lưu
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </td>
                                    <td class="px-5 py-5 text-sm text-[var(--admin-text-muted)]">
                                        @if($cat->parent)
                                            <span class="admin-badge admin-badge--info">{{ $cat->parent->name }}</span>
                                        @else
                                            <span class="admin-badge admin-badge--muted">Gốc</span>
                                        @endif
                                    </td>
                                    <td class="px-5 py-5 text-sm text-[var(--admin-text-muted)]">
                                        <strong class="text-[var(--admin-text)]">{{ $cat->products->count() }}</strong> sản phẩm
                                    </td>
                                    <td class="px-7 py-5">
                                        <div class="flex justify-end gap-2">
                                            <button type="button" @click="editingId = {{ $cat->id }}" x-show="editingId !== {{ $cat->id }}" class="admin-action-icon" title="Sửa">
                                                <i class="fas fa-pen text-sm"></i>
                                            </button>
                                            <form action="{{ route('admin.categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Xóa danh mục này sẽ xóa luôn danh mục con. Bạn chắc chắn chứ?')">
                                                @csrf
                                                @method('DELETE')
                                                <button class="admin-action-icon hover:!bg-[rgba(255,218,214,0.45)] hover:!text-[var(--admin-danger-text)]" title="Xóa">
                                                    <i class="fas fa-trash text-sm"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="admin-pagination-shell border-t border-[rgba(112,122,108,0.12)] px-6 py-5">
                    {{ $categories->links() }}
                </div>
            </section>
        </div>
    </div>
@endsection
