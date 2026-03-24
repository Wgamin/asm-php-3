<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category; // 1. Bổ sung Model Category
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    // 1. Trang danh sách (Index)
    public function index(Request $request)
    {
        // Sử dụng with('category') để lấy luôn tên danh mục, tránh lỗi N+1
        $query = Product::with('category');

        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        $products = $query->latest()->paginate(10);

        return view('admin.products.index', compact('products'));
    }

    // 2. Trang hiển thị Form thêm mới
    public function create()
    {
        // 2. Lấy danh sách danh mục để truyền sang Form
        $categories = Category::all();
        return view('admin.products.create', compact('categories'));
    }

    // 3. Xử lý lưu dữ liệu
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id', // 3. Validate category_id
            'description' => 'required',
            'content' => 'required',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $imagePath = $request->file('image')->store('products', 'public');

        Product::create([
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $request->category_id, // 4. Lưu category_id
            'description' => $request->description,
            'content' => $request->content,
            'image' => $imagePath,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Thêm sản phẩm thành công!');
    }

    // 4. Trang hiển thị Form chỉnh sửa
    public function edit($id)
    {
        $product = Product::findOrFail($id);
        // 5. Lấy danh sách danh mục để người dùng chọn lại khi sửa
        $categories = Category::all();
        return view('admin.products.edit', compact('product', 'categories'));
    }

    // 5. Xử lý cập nhật dữ liệu
    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        $request->validate([
            'name' => 'required',
            'price' => 'required|numeric',
            'category_id' => 'required|exists:categories,id', // 6. Validate category_id
            'description' => 'required',
            'content' => 'required',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
        ]);

        $data = [
            'name' => $request->name,
            'price' => $request->price,
            'category_id' => $request->category_id, // 7. Cập nhật category_id
            'description' => $request->description,
            'content' => $request->content,
        ];

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $data['image'] = $request->file('image')->store('products', 'public');
        }

        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Cập nhật sản phẩm thành công!');
    }

    // 6. Xử lý xóa sản phẩm
    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Đã xóa sản phẩm!');
    }
}