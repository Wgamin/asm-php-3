<?php

namespace App\Http\Controllers;

use App\Models\Product;

class CompareController extends Controller
{
    public function index()
    {
        $compareIds = array_values(array_map('intval', session()->get('compare', [])));

        $products = Product::with(['category', 'variants'])
            ->whereIn('id', $compareIds)
            ->get()
            ->sortBy(function ($product) use ($compareIds) {
                return array_search($product->id, $compareIds);
            });

        return view('compare.index', compact('products'));
    }

    public function add(Product $product)
    {
        $compare = array_values(array_map('intval', session()->get('compare', [])));

        if (! in_array($product->id, $compare, true)) {
            if (count($compare) >= 4) {
                return back()->with('error', 'Chỉ có thể so sánh tối đa 4 sản phẩm.');
            }

            $compare[] = $product->id;
            session()->put('compare', $compare);
        }

        return back()->with('success', 'Đã thêm sản phẩm vào danh sách so sánh.');
    }

    public function remove(Product $product)
    {
        $compare = array_values(array_map('intval', session()->get('compare', [])));

        $compare = array_values(array_filter($compare, function ($id) use ($product) {
            return $id != $product->id;
        }));

        session()->put('compare', $compare);

        return back()->with('success', 'Đã xóa sản phẩm khỏi danh sách so sánh.');
    }

    public function clear()
    {
        session()->forget('compare');

        return back()->with('success', 'Đã xóa toàn bộ danh sách so sánh.');
    }
}
