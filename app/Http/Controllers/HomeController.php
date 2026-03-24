<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Lấy tất cả danh mục để làm menu
        $categories = Category::all();
        
        // Lấy sản phẩm mới nhất (ví dụ 8 sản phẩm)
        $products = Product::with('category')->latest()->take(8)->get();

        return view('welcome', compact('categories', 'products'));
    }

    public function category($id)
    {
        $category = Category::findOrFail($id);
        // Lấy sản phẩm theo danh mục
        $products = Product::where('category_id', $id)->paginate(12);
        $categories = Category::all();

        return view('category_detail', compact('category', 'products', 'categories'));
    }
}