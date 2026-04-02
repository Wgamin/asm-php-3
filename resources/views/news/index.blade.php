@extends('layouts.client')

@section('title', 'Tin tức nông sản sạch, tư vấn chọn thực phẩm và cập nhật thị trường')
@section('meta_title', 'Tin tức nông sản sạch, mẹo chọn thực phẩm và xu hướng tiêu dùng')
@section('meta_description', 'Đọc tin tức nông sản, mẹo chọn thực phẩm sạch, xu hướng tiêu dùng và kiến thức bảo quản thực phẩm từ Nông Sản Việt.')
@section('meta_keywords', 'tin tức nông sản, mẹo chọn thực phẩm sạch, thị trường nông sản, blog nông sản')
@section('canonical', route('news.index'))
@section('meta_type', 'website')
@section('json_ld')
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Blog',
    'name' => 'Tin tức Nông Sản Việt',
    'description' => 'Tin tức nông sản, mẹo chọn thực phẩm sạch và xu hướng tiêu dùng mới nhất.',
    'url' => route('news.index'),
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
@endsection

@section('content')
<div class="bg-[radial-gradient(circle_at_top_left,_rgba(16,185,129,0.14),_transparent_35%),radial-gradient(circle_at_top_right,_rgba(245,158,11,0.12),_transparent_28%),linear-gradient(180deg,_#f8fafc,_#ffffff)] py-12 md:py-16">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="max-w-3xl mb-12">
            <span class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-[0.3em] text-emerald-600 bg-emerald-50 px-4 py-2 rounded-full border border-emerald-100">
                <i class="fas fa-seedling"></i>
                Chuyên mục biên tập
            </span>
            <h1 class="text-4xl md:text-5xl font-black text-slate-900 leading-tight mt-5">
                Tin tức nông sản, mẹo chọn thực phẩm và góc nhìn tiêu dùng thông minh
            </h1>
            <p class="text-lg text-slate-600 mt-5 leading-8">
                Cập nhật kiến thức hữu ích để mua đúng, ăn sạch và hiểu thị trường nông sản rõ hơn trước mỗi quyết định mua hàng.
            </p>
        </div>

        @if($featuredArticle)
            <section class="grid grid-cols-1 lg:grid-cols-[1.2fr_0.8fr] gap-8 mb-12">
                <article class="bg-slate-900 rounded-[32px] overflow-hidden shadow-2xl shadow-slate-200/70">
                    <a href="{{ route('news.show', $featuredArticle->slug) }}" class="grid md:grid-cols-2 h-full">
                        <div class="relative min-h-[280px]">
                            @if($featuredArticle->image)
                                <img src="{{ asset('storage/' . $featuredArticle->image) }}" alt="{{ $featuredArticle->title }}" class="absolute inset-0 w-full h-full object-cover">
                            @else
                                <div class="absolute inset-0 bg-gradient-to-br from-emerald-500 via-teal-500 to-slate-900"></div>
                            @endif
                        </div>
                        <div class="p-8 md:p-10 text-white flex flex-col justify-between">
                            <div>
                                <span class="inline-flex items-center gap-2 text-[11px] uppercase tracking-[0.25em] font-black text-emerald-200 mb-5">
                                    Bài nổi bật
                                </span>
                                <h2 class="text-3xl font-black leading-tight mb-4">{{ $featuredArticle->title }}</h2>
                                <p class="text-slate-300 leading-7">{{ \Illuminate\Support\Str::limit($featuredArticle->excerpt, 180) }}</p>
                            </div>
                            <div class="flex items-center justify-between mt-8 text-sm text-slate-300">
                                <span>{{ optional($featuredArticle->published_at)->format('d/m/Y') }}</span>
                                <span>{{ $featuredArticle->reading_time }} phút đọc</span>
                            </div>
                        </div>
                    </a>
                </article>

                <div class="bg-white rounded-[32px] border border-slate-100 p-8 shadow-sm">
                    <h2 class="text-xl font-black text-slate-900 mb-6">Tại sao nên theo dõi chuyên mục này?</h2>
                    <div class="space-y-5 text-slate-600">
                        <div class="flex gap-4">
                            <div class="w-11 h-11 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                            <div>
                                <p class="font-bold text-slate-900">Bám sát thị trường</p>
                                <p class="mt-1 leading-7">Theo dõi xu hướng giá, mùa vụ và hành vi tiêu dùng để tối ưu quyết định mua.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-11 h-11 rounded-2xl bg-amber-50 text-amber-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-lightbulb"></i>
                            </div>
                            <div>
                                <p class="font-bold text-slate-900">Nội dung thực dụng</p>
                                <p class="mt-1 leading-7">Không chỉ kể chuyện thị trường, mà còn chỉ ra cách chọn, bảo quản và dùng thực phẩm hiệu quả.</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-11 h-11 rounded-2xl bg-sky-50 text-sky-600 flex items-center justify-center shrink-0">
                                <i class="fas fa-search"></i>
                            </div>
                            <div>
                                <p class="font-bold text-slate-900">Cấu trúc dễ đọc, dễ index</p>
                                <p class="mt-1 leading-7">Mỗi bài được tối ưu tiêu đề, mô tả và liên kết nội bộ để người đọc lẫn công cụ tìm kiếm đều hiểu nhanh.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        @endif

        <section>
            <div class="flex flex-col md:flex-row md:items-end md:justify-between gap-4 mb-8">
                <div>
                    <span class="text-sm font-black uppercase tracking-[0.25em] text-slate-400">Danh sách bài viết</span>
                    <h2 class="text-3xl font-black text-slate-900 mt-2">Bài mới nhất</h2>
                </div>
                <p class="text-slate-500 text-sm">Hiển thị {{ $totalPublishedArticles }} bài viết công khai</p>
            </div>

            @if($articles->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                    @foreach($articles as $article)
                        @include('news._card', ['article' => $article])
                    @endforeach
                </div>

                <div class="mt-10">
                    {{ $articles->links() }}
                </div>
            @elseif(!$featuredArticle)
                <div class="bg-white rounded-3xl border border-dashed border-slate-200 p-14 text-center text-slate-500">
                    Chưa có bài viết nào được xuất bản.
                </div>
            @endif
        </section>
    </div>
</div>
@endsection
