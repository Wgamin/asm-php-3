<article class="group bg-white rounded-3xl border border-slate-100 overflow-hidden shadow-sm hover:shadow-xl transition-all duration-300">
    <a href="{{ route('news.show', $article->slug) }}" class="block">
        <div class="aspect-[4/3] overflow-hidden bg-slate-100">
            @if($article->image)
                <img src="{{ asset('storage/' . $article->image) }}" alt="{{ $article->title }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
            @else
                <div class="w-full h-full bg-gradient-to-br from-emerald-100 via-white to-amber-100 flex items-center justify-center text-5xl text-emerald-400">
                    <i class="fas fa-newspaper"></i>
                </div>
            @endif
        </div>
    </a>

    <div class="p-6">
        <div class="flex items-center gap-3 text-xs text-slate-400 uppercase tracking-[0.2em] font-bold mb-4">
            <span>{{ optional($article->published_at)->format('d/m/Y') }}</span>
            <span class="w-1 h-1 rounded-full bg-slate-300"></span>
            <span>{{ $article->reading_time }} phút đọc</span>
        </div>

        <h3 class="text-xl font-bold text-slate-900 leading-tight mb-3">
            <a href="{{ route('news.show', $article->slug) }}" class="hover:text-emerald-600 transition">
                {{ $article->title }}
            </a>
        </h3>

        <p class="text-slate-600 leading-7 mb-5">
            {{ \Illuminate\Support\Str::limit($article->excerpt, 140) }}
        </p>

        <a href="{{ route('news.show', $article->slug) }}" class="inline-flex items-center gap-2 text-emerald-600 font-semibold hover:text-emerald-700 transition">
            Đọc bài viết
            <i class="fas fa-arrow-right text-xs"></i>
        </a>
    </div>
</article>
