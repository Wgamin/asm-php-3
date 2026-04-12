<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with(['category', 'variants'])
            ->withCount('approvedReviews as reviews_count')
            ->withAvg('approvedReviews as reviews_avg_rating', 'rating');

        if ($request->filled('q')) {
            $keyword = trim((string) $request->q);
            $query->where(function ($builder) use ($keyword) {
                $builder
                    ->where('name', 'like', '%' . $keyword . '%')
                    ->orWhere('description', 'like', '%' . $keyword . '%');
            });
        }

        if ($request->has('categories') && is_array($request->categories)) {
            $query->whereIn('category_id', $request->categories);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        switch ($request->sort) {
            case 'price_asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price_desc':
                $query->orderBy('price', 'desc');
                break;
            case 'oldest':
                $query->oldest();
                break;
            default:
                $query->latest();
                break;
        }

        $products = $query->paginate(12)->withQueryString();
        $categories = Category::withCount('products')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function show($id)
    {
        $product = Product::with([
            'category',
            'variants',
            'images',
            'approvedReviews' => fn ($query) => $query->with('user')->latest(),
        ])
            ->withAvg('approvedReviews as reviews_avg_rating', 'rating')
            ->withCount('approvedReviews as reviews_count')
            ->findOrFail($id);

        $relatedProducts = $this->buildRelatedProducts($product);

        return view('products.show', compact('product', 'relatedProducts'));
    }

    protected function buildRelatedProducts(Product $product)
    {
        $product->loadMissing(['category', 'variants']);
        $targetPrice = max($product->effective_price, 1);
        $minPrice = $targetPrice * 0.8;
        $maxPrice = $targetPrice * 1.2;

        $categoryMatches = Product::with(['category', 'variants'])
            ->where('id', '!=', $product->id)
            ->where('category_id', $product->category_id)
            ->latest()
            ->limit(8)
            ->get();

        $priceMatches = Product::with(['category', 'variants'])
            ->where('id', '!=', $product->id)
            ->latest()
            ->limit(24)
            ->get()
            ->filter(function ($candidate) use ($minPrice, $maxPrice) {
                return $candidate->effective_price >= $minPrice
                    && $candidate->effective_price <= $maxPrice;
            });

        return $categoryMatches
            ->concat($priceMatches)
            ->unique('id')
            ->take(4)
            ->values();
    }
}
