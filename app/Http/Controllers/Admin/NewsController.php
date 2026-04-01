<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NewsArticle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class NewsController extends Controller
{
    public function index(Request $request)
    {
        $query = NewsArticle::with('author');

        if ($request->filled('keyword')) {
            $query->where(function ($subQuery) use ($request) {
                $subQuery
                    ->where('title', 'like', '%' . $request->keyword . '%')
                    ->orWhere('slug', 'like', '%' . $request->keyword . '%');
            });
        }

        if ($request->filled('status')) {
            if ($request->status === 'published') {
                $query->where('is_published', true);
            }

            if ($request->status === 'draft') {
                $query->where('is_published', false);
            }
        }

        $articles = $query
            ->orderByDesc('published_at')
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('admin.news.index', compact('articles'));
    }

    public function create()
    {
        return view('admin.news.create');
    }

    public function store(Request $request)
    {
        $data = $this->validatedData($request);
        $data['slug'] = $this->buildUniqueSlug($data['slug'] ?: $data['title']);
        $data['user_id'] = Auth::id();

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('news', 'public');
        }

        $data = $this->preparePublishData($data);

        NewsArticle::create($data);

        return redirect()->route('admin.news.index')->with('success', 'Đã tạo bài viết tin tức.');
    }

    public function edit(NewsArticle $news)
    {
        return view('admin.news.edit', ['article' => $news]);
    }

    public function update(Request $request, NewsArticle $news)
    {
        $data = $this->validatedData($request, $news);
        $data['slug'] = $this->buildUniqueSlug($data['slug'] ?: $data['title'], $news->id);

        if ($request->hasFile('image')) {
            if ($news->image) {
                Storage::disk('public')->delete($news->image);
            }

            $data['image'] = $request->file('image')->store('news', 'public');
        }

        $data = $this->preparePublishData($data, $news);

        $news->update($data);

        return redirect()->route('admin.news.index')->with('success', 'Đã cập nhật bài viết.');
    }

    public function destroy(NewsArticle $news)
    {
        if ($news->image) {
            Storage::disk('public')->delete($news->image);
        }

        $news->delete();

        return redirect()->route('admin.news.index')->with('success', 'Đã xóa bài viết.');
    }

    protected function validatedData(Request $request, ?NewsArticle $article = null): array
    {
        return $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('news_articles', 'slug')->ignore($article?->id),
            ],
            'excerpt' => ['required', 'string', 'max:500'],
            'content' => ['required', 'string'],
            'image' => [$article ? 'nullable' : 'required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:3072'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string', 'max:255'],
            'meta_keywords' => ['nullable', 'string', 'max:255'],
            'published_at' => ['nullable', 'date'],
            'is_published' => ['nullable', 'boolean'],
        ]);
    }

    protected function preparePublishData(array $data, ?NewsArticle $article = null): array
    {
        $data['is_published'] = (bool) ($data['is_published'] ?? false);

        if ($data['is_published'] && empty($data['published_at'])) {
            $data['published_at'] = $article?->published_at ?? now();
        }

        if (! $data['is_published'] && empty($data['published_at'])) {
            $data['published_at'] = null;
        }

        return $data;
    }

    protected function buildUniqueSlug(string $source, ?int $ignoreId = null): string
    {
        $baseSlug = Str::slug($source);
        $baseSlug = $baseSlug !== '' ? $baseSlug : 'tin-tuc';
        $slug = $baseSlug;
        $counter = 1;

        while (
            NewsArticle::where('slug', $slug)
                ->when($ignoreId, fn ($query) => $query->where('id', '!=', $ignoreId))
                ->exists()
        ) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }
}
