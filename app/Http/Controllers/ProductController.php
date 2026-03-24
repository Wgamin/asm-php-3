<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // 1. Khởi tạo query
        $query = Product::query();

        // 2. Lọc theo danh mục
        if ($request->has('categories') && is_array($request->categories)) {
            $query->whereIn('category_id', $request->categories);
        }

        // 3. Lọc theo giá tối thiểu
        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        // 4. Lọc theo giá tối đa
        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // 5. Sắp xếp (Logic Toolbar)
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

        // 6. Thực thi TRUY NHẤT 1 LẦN và phân trang
        // withQueryString() cực kỳ quan trọng để giữ các dấu tick lọc khi bấm sang trang 2
        $products = $query->paginate(12)->withQueryString();

        // 7. Lấy danh mục kèm số lượng sản phẩm để hiện ở Sidebar
        $categories = Category::withCount('products')->get();

        return view('products.index', compact('products', 'categories'));
    }

    public function show($id)
    {
        // Eager load 'category' để tránh lỗi N+1 query khi hiển thị tên danh mục
        $product = Product::with('category')->findOrFail($id);

        // Lấy 4 sản phẩm liên quan (cùng danh mục, trừ chính nó)
        $relatedProducts = Product::where('category_id', $product->category_id)
                                    ->where('id', '!=', $id)
                                    ->limit(4)
                                    ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}