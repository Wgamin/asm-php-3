<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attribute;
use App\Models\AttributeValue;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttributeController extends Controller
{
    /**
     * Hiển thị danh sách thuộc tính và giá trị
     */
    public function index()
    {
        $attributes = Attribute::with('attributeValues')->latest()->get();
        return view('admin.attributes.index', compact('attributes'));
    }

    /**
     * Lưu thuộc tính mới (Ví dụ: Màu sắc)
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:attributes,name|max:255',
        ], [
            'name.required' => 'Tên thuộc tính không được để trống',
            'name.unique' => 'Thuộc tính này đã tồn tại',
        ]);

        Attribute::create(['name' => $request->name]);

        return redirect()->back()->with('success', 'Thêm thuộc tính thành công!');
    }

    /**
     * Cập nhật tên thuộc tính
     */
    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|max:255']);
        
        $attribute = Attribute::findOrFail($id);
        $attribute->update(['name' => $request->name]);

        return redirect()->back()->with('success', 'Cập nhật thành công!');
    }

    /**
     * Xóa thuộc tính (Sẽ xóa luôn các giá trị con nhờ Cascade)
     */
    public function destroy($id)
    {
        $attribute = Attribute::findOrFail($id);
        $attribute->delete();

        return redirect()->back()->with('success', 'Đã xóa thuộc tính và các giá trị liên quan!');
    }

    /* --- QUẢN LÝ GIÁ TRỊ THUỘC TÍNH (Attribute Values) --- */

    /**
     * Thêm giá trị cho thuộc tính (Ví dụ: Thêm "Đỏ" vào "Màu sắc")
     */
    public function storeValue(Request $request, $attributeId)
    {
        $request->validate([
            'value' => 'required|max:255',
        ]);

        AttributeValue::create([
            'attribute_id' => $attributeId,
            'value' => $request->value
        ]);

        return redirect()->back()->with('success', 'Thêm giá trị mới thành công!');
    }

    /**
     * Cập nhật giá trị thuộc tính
     */
    public function updateValue(Request $request, $id)
    {
        $request->validate(['value' => 'required|max:255']);
        
        $value = AttributeValue::findOrFail($id);
        $value->update(['value' => $request->value]);

        return redirect()->back()->with('success', 'Cập nhật giá trị thành công!');
    }

    /**
     * Xóa giá trị thuộc tính
     */
    public function destroyValue($id)
    {
        $value = AttributeValue::findOrFail($id);
        $value->delete();

        return redirect()->back()->with('success', 'Đã xóa giá trị!');
    }
}