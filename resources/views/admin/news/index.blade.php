@extends('admin.layouts.master')

@section('content')
<div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
    <div class="p-6 border-b border-gray-100 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h2 class="text-xl font-bold text-gray-800">Quản lý tin tức</h2>
            <p class="text-sm text-gray-500">Biên tập bài viết, lên lịch đăng và quản lý nội dung chuẩn SEO.</p>
            <p class="text-xs text-gray-400 mt-1">Hiển thị {{ $articles->count() }} / {{ $articles->total() }} bài viết</p>
        </div>

        <a href="{{ route('admin.news.create') }}" class="bg-green-500 hover:bg-green-600 text-white px-6 py-2.5 rounded-xl font-bold text-sm shadow-lg shadow-green-100 transition inline-flex items-center">
            <i class="fas fa-plus mr-2"></i> Thêm bài viết
        </a>
    </div>

    <div class="p-6 border-b border-gray-100 bg-gray-50/50">
        <form method="GET" action="{{ route('admin.news.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <input type="text" name="keyword" value="{{ request('keyword') }}" placeholder="Tìm theo tiêu đề hoặc slug..."
                   class="px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-green-400 text-sm">

            <select name="status" class="px-4 py-2.5 rounded-xl border border-gray-200 focus:ring-2 focus:ring-green-400 text-sm bg-white">
                <option value="">Tất cả trạng thái</option>
                <option value="published" {{ request('status') === 'published' ? 'selected' : '' }}>Đã xuất bản</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Bản nháp</option>
            </select>

            <div class="flex gap-2">
                <button class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2.5 rounded-xl text-sm font-bold">Lọc</button>
                <a href="{{ route('admin.news.index') }}" class="px-4 py-2.5 text-sm rounded-xl border border-gray-200 hover:bg-gray-100">Reset</a>
            </div>
        </form>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left">
            <thead class="bg-gray-50/50">
                <tr class="text-xs uppercase tracking-wider text-gray-400 font-bold border-b border-gray-100">
                    <th class="px-6 py-4">Bài viết</th>
                    <th class="px-6 py-4">Slug</th>
                    <th class="px-6 py-4">Tác giả</th>
                    <th class="px-6 py-4">Xuất bản</th>
                    <th class="px-6 py-4">Trạng thái</th>
                    <th class="px-6 py-4 text-center">Thao tác</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-50">
                @forelse($articles as $article)
                <tr class="hover:bg-green-50/30 transition">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="w-20 h-16 rounded-xl overflow-hidden border border-gray-100 bg-slate-100 shrink-0">
                                @if($article->image)
                                    <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-slate-300">
                                        <i class="fas fa-newspaper"></i>
                                    </div>
                                @endif
                            </div>
                            <div>
                                <p class="font-bold text-gray-800 max-w-sm">{{ $article->title }}</p>
                                <p class="text-sm text-gray-500 mt-1 max-w-md">{{ \Illuminate\Support\Str::limit($article->excerpt, 90) }}</p>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $article->slug }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $article->author->name ?? 'Không rõ' }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $article->published_at ? $article->published_at->format('d/m/Y H:i') : 'Chưa hẹn lịch' }}</td>
                    <td class="px-6 py-4">
                        <span class="inline-flex px-3 py-1 rounded-full text-xs font-bold {{ $article->is_published ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-700' }}">
                            {{ $article->is_published ? 'Published' : 'Draft' }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex justify-center gap-2">
                            @if($article->is_published && $article->published_at && $article->published_at->lte(now()))
                                <a href="{{ route('news.show', $article->slug) }}" target="_blank" class="p-2 bg-slate-50 text-slate-600 rounded-lg hover:bg-slate-700 hover:text-white transition">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                            @else
                                <span class="p-2 bg-slate-100 text-slate-300 rounded-lg cursor-not-allowed">
                                    <i class="fas fa-eye-slash text-xs"></i>
                                </span>
                            @endif
                            <a href="{{ route('admin.news.edit', $article) }}" class="p-2 bg-blue-50 text-blue-600 rounded-lg hover:bg-blue-600 hover:text-white transition">
                                <i class="fas fa-edit text-xs"></i>
                            </a>
                            <form action="{{ route('admin.news.destroy', $article) }}" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa bài viết này?')">
                                @csrf
                                @method('DELETE')
                                <button class="p-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition">
                                    <i class="fas fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center text-gray-400">Chưa có bài viết nào.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="p-6 border-t border-gray-100">
        {{ $articles->links() }}
    </div>
</div>
@endsection
