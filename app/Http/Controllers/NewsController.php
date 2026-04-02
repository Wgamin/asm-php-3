<?php

namespace App\Http\Controllers;

use App\Models\NewsArticle;

class NewsController extends Controller
{
    public function index()
    {
        $totalPublishedArticles = NewsArticle::published()->count();

        $featuredArticle = NewsArticle::with('author')
            ->published()
            ->latest('published_at')
            ->first();

        $articles = NewsArticle::with('author')
            ->published()
            ->when($featuredArticle, fn ($query) => $query->where('id', '!=', $featuredArticle->id))
            ->latest('published_at')
            ->paginate(9);

        return view('news.index', compact('featuredArticle', 'articles', 'totalPublishedArticles'));
    }

    public function show(string $slug)
    {
        $article = NewsArticle::with('author')
            ->published()
            ->where('slug', $slug)
            ->firstOrFail();

        $relatedArticles = NewsArticle::with('author')
            ->published()
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->take(3)
            ->get();

        return view('news.show', compact('article', 'relatedArticles'));
    }
}
