@extends('layouts.client')

@section('title', $article->seo_title)
@section('meta_title', $article->seo_title)
@section('meta_description', $article->seo_description)
@section('meta_keywords', $article->meta_keywords ?: 'tin tức nông sản, kiến thức thực phẩm sạch')
@section('canonical', route('news.show', $article->slug))
@section('meta_type', 'article')
@section('meta_image', $article->image_url ?: '')
@section('json_ld')
{!! json_encode([
    '@context' => 'https://schema.org',
    '@graph' => [
        [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'Trang chủ',
                    'item' => route('home'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'Tin tức',
                    'item' => route('news.index'),
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 3,
                    'name' => $article->title,
                    'item' => route('news.show', $article->slug),
                ],
            ],
        ],
        [
            '@type' => 'NewsArticle',
            'headline' => $article->seo_title,
            'description' => $article->seo_description,
            'datePublished' => optional($article->published_at)->toIso8601String(),
            'dateModified' => optional($article->updated_at)->toIso8601String(),
            'mainEntityOfPage' => route('news.show', $article->slug),
            'author' => [
                '@type' => 'Person',
                'name' => $article->author->name ?? 'Nông Sản Việt',
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => 'Nông Sản Việt',
            ],
            'image' => $article->image_url,
        ],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
@endsection

@section('content')
<div class="bg-slate-50 py-10 md:py-14">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center gap-2 text-sm text-slate-500 mb-8">
            <a href="{{ route('home') }}" class="hover:text-emerald-600 transition">Trang chủ</a>
            <span>/</span>
            <a href="{{ route('news.index') }}" class="hover:text-emerald-600 transition">Tin tức</a>
            <span>/</span>
            <span class="text-slate-800 font-medium line-clamp-1">{{ $article->title }}</span>
        </nav>

        <article class="bg-white rounded-[36px] border border-slate-100 shadow-sm overflow-hidden">
            <div class="px-6 md:px-12 pt-10 md:pt-14">
                <div class="max-w-3xl">
                    <div class="flex flex-wrap items-center gap-4 text-xs uppercase tracking-[0.22em] font-black text-slate-400">
                        <span>{{ optional($article->published_at)->format('d/m/Y') }}</span>
                        <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                        <span>{{ $article->reading_time }} phút đọc</span>
                        <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                        <span>{{ $article->author->name ?? 'Nông Sản Việt' }}</span>
                    </div>

                    <h1 class="text-4xl md:text-5xl font-black text-slate-900 leading-tight mt-6">
                        {{ $article->title }}
                    </h1>

                    <p class="text-xl text-slate-600 mt-6 leading-8">
                        {{ $article->excerpt }}
                    </p>
                </div>
            </div>

            @if($article->image)
                <div class="px-6 md:px-12 mt-10">
                    <div class="rounded-[32px] overflow-hidden bg-slate-100 shadow-inner">
                        <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" class="w-full h-auto object-cover">
                    </div>
                </div>
            @endif

            <div class="px-6 md:px-12 py-10 md:py-14">
                <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_320px] gap-10">
                    <div class="prose prose-slate max-w-none prose-headings:font-black prose-headings:text-slate-900 prose-p:text-slate-700 prose-p:leading-8 prose-li:text-slate-700">
                        {!! nl2br(e($article->content)) !!}
                    </div>

                    <aside class="space-y-6">
                        <div class="bg-slate-50 rounded-[28px] p-6 border border-slate-100">
                            <p class="text-xs uppercase tracking-[0.24em] font-black text-emerald-600 mb-3">Tối ưu đọc nhanh</p>
                            <ul class="space-y-3 text-sm text-slate-600">
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-calendar-day text-emerald-500"></i>
                                    <span>Xuất bản: {{ optional($article->published_at)->format('d/m/Y H:i') }}</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-pen-nib text-emerald-500"></i>
                                    <span>Tác giả: {{ $article->author->name ?? 'Nông Sản Việt' }}</span>
                                </li>
                                <li class="flex items-center gap-3">
                                    <i class="fas fa-stopwatch text-emerald-500"></i>
                                    <span>Thời gian đọc: {{ $article->reading_time }} phút</span>
                                </li>
                            </ul>
                        </div>

                        <div class="bg-slate-900 text-white rounded-[28px] p-6">
                            <p class="text-xs uppercase tracking-[0.24em] font-black text-emerald-300 mb-3">Tiếp tục khám phá</p>
                            <p class="text-slate-300 leading-7 mb-5">Xem thêm các bài viết mới để cập nhật xu hướng tiêu dùng và kiến thức chọn nông sản sạch.</p>
                            <a href="{{ route('news.index') }}" class="inline-flex items-center gap-2 text-white font-bold">
                                Quay lại danh sách
                                <i class="fas fa-arrow-right text-xs"></i>
                            </a>
                        </div>
                    </aside>
                </div>
            </div>
        </article>

        @if($relatedArticles->isNotEmpty())
            <section class="mt-14">
                <div class="flex items-end justify-between mb-8">
                    <div>
                        <span class="text-sm font-black uppercase tracking-[0.25em] text-slate-400">Gợi ý tiếp theo</span>
                        <h2 class="text-3xl font-black text-slate-900 mt-2">Bài viết liên quan</h2>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    @foreach($relatedArticles as $article)
                        @include('news._card', ['article' => $article])
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</div>
@endsection
