@extends('admin.layouts.master')

@section('title', 'Tin tức')

@section('content')
    <div class="mx-auto max-w-7xl space-y-8">
        <section class="flex flex-col gap-5 lg:flex-row lg:items-end lg:justify-between">
            <div>
                <p class="admin-kicker">Content & CMS</p>
                <h1 class="admin-headline mt-2 text-4xl font-bold tracking-[-0.05em] text-[var(--admin-text)]">Quản lý tin tức</h1>
                <p class="admin-copy mt-3 max-w-2xl text-sm">Biên tập bài viết, chuẩn hóa SEO, lên lịch xuất bản và quản lý nội dung tư vấn nông nghiệp từ một module CMS tập trung.</p>
            </div>
            <a href="{{ route('admin.news.create') }}" class="admin-btn-primary">
                <i class="fas fa-plus text-sm"></i>
                Thêm bài viết
            </a>
        </section>

        <section class="admin-panel p-6">
            <form method="GET" action="{{ route('admin.news.index') }}" class="grid gap-4 md:grid-cols-[1fr_240px_auto]">
                <div>
                    <label class="admin-field-label">Tìm kiếm</label>
                    <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tiêu đề hoặc slug bài viết">
                </div>
                <div>
                    <label class="admin-field-label">Trạng thái</label>
                    <select name="status">
                        <option value="">Tất cả trạng thái</option>
                        <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                        <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Bản nháp</option>
                    </select>
                </div>
                <div class="flex items-end gap-3">
                    <button class="admin-btn-primary" type="submit">
                        <i class="fas fa-filter text-sm"></i>
                        Lọc
                    </button>
                    <a href="{{ route('admin.news.index') }}" class="admin-btn-secondary">Reset</a>
                </div>
            </form>
        </section>

        <section class="admin-table-shell">
            <div class="overflow-x-auto">
                <table class="min-w-[1120px]">
                    <thead>
                        <tr>
                            <th class="px-7 py-4 text-left">Bài viết</th>
                            <th class="px-5 py-4 text-left">Slug</th>
                            <th class="px-5 py-4 text-left">Tác giả</th>
                            <th class="px-5 py-4 text-left">Xuất bản</th>
                            <th class="px-5 py-4 text-left">Trạng thái</th>
                            <th class="px-7 py-4 text-right">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($articles as $article)
                            <tr>
                                <td class="px-7 py-5">
                                    <div class="flex items-center gap-4">
                                        <div class="h-16 w-20 overflow-hidden rounded-2xl bg-[var(--admin-surface-low)]">
                                            @if($article->image)
                                                <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" class="h-full w-full object-cover">
                                            @else
                                                <div class="flex h-full w-full items-center justify-center text-[var(--admin-text-muted)]">
                                                    <i class="fas fa-newspaper"></i>
                                                </div>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="max-w-sm truncate text-sm font-bold text-[var(--admin-text)]">{{ $article->title }}</p>
                                            <p class="mt-2 max-w-md text-xs leading-6 text-[var(--admin-text-muted)]">{{ \Illuminate\Support\Str::limit($article->excerpt, 90) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-5 text-sm text-[var(--admin-text-muted)]">{{ $article->slug }}</td>
                                <td class="px-5 py-5 text-sm text-[var(--admin-text-muted)]">{{ $article->author->name ?? 'Không rõ' }}</td>
                                <td class="px-5 py-5 text-sm text-[var(--admin-text-muted)]">{{ $article->published_at ? $article->published_at->format('d/m/Y H:i') : 'Chưa hẹn lịch' }}</td>
                                <td class="px-5 py-5">
                                    <span class="{{ $article->is_published ? 'admin-badge admin-badge--success' : 'admin-badge admin-badge--warning' }}">
                                        {{ $article->is_published ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td class="px-7 py-5">
                                    <div class="flex justify-end gap-2">
                                        @if($article->is_published && $article->published_at && $article->published_at->lte(now()))
                                            <a href="{{ route('news.show', $article->slug) }}" target="_blank" class="admin-action-icon" title="Xem">
                                                <i class="fas fa-eye text-sm"></i>
                                            </a>
                                        @endif
                                        <a href="{{ route('admin.news.edit', $article) }}" class="admin-action-icon" title="Sửa">
                                            <i class="fas fa-pen text-sm"></i>
                                        </a>
                                        <form action="{{ route('admin.news.destroy', $article) }}" method="POST" onsubmit="return confirm('Xóa bài viết này?')">
                                            @csrf
                                            @method('DELETE')
                                            <button class="admin-action-icon hover:!bg-[rgba(255,218,214,0.45)] hover:!text-[var(--admin-danger-text)]" title="Xóa">
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
                                        <i class="fas fa-newspaper text-4xl opacity-30"></i>
                                        <p class="text-sm">Chưa có bài viết nào.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($articles->hasPages())
                <div class="admin-pagination-shell border-t border-[rgba(112,122,108,0.12)] px-6 py-5">
                    {{ $articles->links() }}
                </div>
            @endif
        </section>
    </div>
@endsection
