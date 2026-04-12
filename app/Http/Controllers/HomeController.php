<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\NewsArticle;
use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('products')
            ->orderByDesc('products_count')
            ->limit(6)
            ->get();
        $products = Product::with(['category', 'variants'])
            ->withCount('approvedReviews as reviews_count')
            ->withAvg('approvedReviews as reviews_avg_rating', 'rating')
            ->latest()
            ->take(8)
            ->get();
        $latestNews = NewsArticle::published()->latest('published_at')->take(3)->get();

        return view('welcome', compact('categories', 'products', 'latestNews'));
    }

    public function category($id)
    {
        $category = Category::findOrFail($id);
        $products = Product::where('category_id', $id)->paginate(12);
        $categories = Category::all();

        return view('category_detail', compact('category', 'products', 'categories'));
    }
}
