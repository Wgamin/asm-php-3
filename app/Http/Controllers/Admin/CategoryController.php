<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index() {
        // Lấy danh mục kèm theo tên danh mục cha để hiển thị ở bảng admin
        $categories = Category::with('parent')->latest()->paginate(10);
        
        // Lấy danh mục gốc để đổ vào Select Box "Danh mục cha" trong Form
        $parentCategories = Category::whereNull('parent_id')->get();
        
        return view('admin.categories.index', compact('categories', 'parentCategories'));
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|unique:categories,name',
            'parent_id' => 'nullable|exists:categories,id', // Kiểm tra ID cha phải tồn tại
        ]);

        $data = $request->all();
        // Tự động tạo slug nếu trong request không có hoặc bạn muốn ghi đè
        $data['slug'] = Str::slug($request->name);

        Category::create($data);

        return back()->with('success', 'Thêm danh mục thành công!');
    }

    public function update(Request $request, Category $category) {
        $request->validate([
            'name' => 'required|unique:categories,name,' . $category->id,
            'parent_id' => 'nullable|exists:categories,id|not_in:' . $category->id, // Không thể chọn chính mình làm cha
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);

        $category->update($data);

        return redirect()->route('admin.categories.index')->with('success', 'Cập nhật thành công!');
    }

    public function destroy(Category $category) {
        // Vì trong migration bạn đã đặt onDelete('cascade'), 
        // nên khi xóa cha, các con sẽ tự động bị xóa trong DB.
        $category->delete();
        return back()->with('success', 'Đã xóa danh mục và các danh mục con liên quan!');
    }
}