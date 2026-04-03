<?php

namespace App\Http\Controllers\Admin;

use App\Imports\ProductsImport;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute; // Thêm Attribute
use App\Models\ProductVariant; // Thêm ProductVariant
use App\Models\ProductImage; // Thêm ProductImage
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;


class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category', 'variants'); // Load thêm variants

        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%' . $request->keyword . '%');
        }

        $products = $query->latest()->paginate(10);
        return view('admin.products.index', compact('products'));
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => ['required', 'file', 'mimes:xlsx,xls,csv', 'max:5120'],
        ], [
            'file_excel.required' => 'Vui lòng chọn file Excel để nhập dữ liệu.',
            'file_excel.mimes' => 'File phải có định dạng .xlsx hoặc .csv.',
        ]);

        $import = new ProductsImport();
        $uploadedFile = $request->file('file_excel');

        try {
            Excel::import($import, $uploadedFile);
        } catch (\Throwable $e) {
            return back()->with('error', 'Không thể đọc file import: ' . $e->getMessage());
        }


        $message = 'Đã nhập ' . $import->importedCount() . ' sản phẩm.';

        if ($import->failedCount() > 0) {
            $message .= ' Có ' . $import->failedCount() . ' dòng lỗi.';
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', $message)
            ->with('import_failures', $import->failures());
    }

    public function show($id)
    {
        // Thêm with('variants') để lấy các biến thể của sản phẩm đó
        $product = Product::with(['category', 'variants'])->findOrFail($id);

        return view('admin.products.show', compact('product'));
    }
    public function create()
    {
        $categories = Category::all();
        // Phải khớp với tên hàm trong Model Attribute
        $attributes = Attribute::with('attributeValues')->get(); 

        return view('admin.products.create', compact('categories', 'attributes'));
    }

public function store(Request $request)
    {
        // 1. Validation (Bổ sung validate cho sale_price)
        $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'product_type' => 'required|in:simple,variable',
            'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048', 
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048', 
            'price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lte:price', // Giá giảm phải <= giá thường
            'variants.*.price' => 'required_if:product_type,variable|numeric|min:0',
            // Nếu biến thể có giá giảm: 'variants.*.sale_price' => 'nullable|numeric|min:0',
            'variants.*.stock' => 'required_if:product_type,variable|integer|min:0',
        ], [
            'name.required' => 'Vui lòng nhập tên sản phẩm',
            'image.required' => 'Vui lòng chọn ảnh đại diện chính',
            'images.*.image' => 'File tải lên phải là hình ảnh',
            'sale_price.lte' => 'Giá giảm không được lớn hơn giá thường',
            'variants.*.price.required_if' => 'Giá biến thể không được để trống',
        ]);

        // 2. Xử lý lưu ảnh đại diện chính
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        // 3. Tạo sản phẩm chính (Bổ sung sale_price)
        $product = Product::create([
            'name'         => $request->name,
            'slug'         => Str::slug($request->name) . '-' . time(),
            'category_id'  => $request->category_id,
            'price'        => $request->product_type === 'simple' ? ($request->price ?? 0) : 0,
            'sale_price'   => $request->product_type === 'simple' ? $request->sale_price : null,
            'product_type' => $request->product_type,
            'image'        => $imagePath,
            'description'  => $request->description,
            'content'      => $request->content,
        ]);

        // 4. Xử lý lưu mảng Ảnh phụ (Gallery)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('products/gallery', 'public');
                $product->images()->create([
                    'image_path' => $path,
                    'sort_order' => 0 
                ]);
            }
        }

        // 5. Lưu biến thể
        if ($request->product_type === 'variable' && $request->has('variants')) {
            foreach ($request->variants as $index => $variantData) {
                
                // --- Xử lý upload ảnh cho từng biến thể ---
                $variantImagePath = null;
                // Kiểm tra xem biến thể ở vị trí $index này có upload file ảnh không
                if ($request->hasFile("variants.{$index}.image")) {
                    $variantImagePath = $request->file("variants.{$index}.image")->store('variants', 'public');
                }

                $product->variants()->create([
                    'sku'            => $variantData['sku'] ?? strtoupper(\Str::random(8)),
                    'price'          => $variantData['price'],
                    'sale_price'     => $variantData['sale_price'] ?? null,
                    'stock'          => $variantData['stock'],
                    'variant_values' => $variantData['attributes'] ?? [], 
                    'image'          => $variantImagePath, // Lưu đường dẫn ảnh vào Database
                ]);
            }
        }

        return redirect()->route('admin.products.index')
                         ->with('success', 'Thêm nông sản "' . $product->name . '" thành công!');
    }


    public function edit($id)
    {
        // Phải load kèm biến thể để Alpine.js render form mượt mà
        $product = Product::with('variants')->findOrFail($id); 
        $categories = Category::all();
        $attributes = Attribute::with('attributeValues')->get(); // Gửi cả thuộc tính nếu file variant-form cần dùng

        return view('admin.products.edit', compact('product', 'categories', 'attributes'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::findOrFail($id);

        // 1. Validation (Giống store nhưng ảnh chính là nullable)
        $request->validate([
            'name' => 'required|max:255',
            'category_id' => 'required|exists:categories,id',
            'product_type' => 'required|in:simple,variable',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', 
            'images.*' => 'image|mimes:jpeg,png,jpg,webp|max:2048', 
            'price' => 'nullable|numeric|min:0',
            'sale_price' => 'nullable|numeric|min:0|lte:price', // Giá giảm <= giá thường
            'variants.*.price' => 'required_if:product_type,variable|numeric|min:0',
            'variants.*.stock' => 'required_if:product_type,variable|integer|min:0',
        ], [
            'name.required' => 'Vui lòng nhập tên sản phẩm',
            'images.*.image' => 'File tải lên phải là hình ảnh',
            'sale_price.lte' => 'Giá giảm không được lớn hơn giá thường',
            'variants.*.price.required_if' => 'Giá biến thể không được để trống',
        ]);

        try {
            DB::beginTransaction();

            // 2. Chuẩn bị mảng dữ liệu cập nhật
            $data = [
                'name'         => $request->name,
                'category_id'  => $request->category_id,
                'product_type' => $request->product_type,
                'description'  => $request->description,
                'content'      => $request->content,
                'price'        => $request->product_type === 'simple' ? ($request->price ?? 0) : 0,
                'sale_price'   => $request->product_type === 'simple' ? $request->sale_price : null,
            ];

            // 3. KHÔI PHỤC LẠI: Xử lý ảnh đại diện chính (image)
            if ($request->hasFile('image')) {
                // Xóa ảnh chính cũ nếu tồn tại trong ổ cứng
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }
                // Lưu ảnh chính mới
                $data['image'] = $request->file('image')->store('products', 'public');
            }

            // Cập nhật thông tin sản phẩm
            $product->update($data);

            // 4. Xử lý lưu mảng Ảnh phụ MỚI (Gallery)
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('products/gallery', 'public');
                    $product->images()->create([
                        'image_path' => $path,
                        'sort_order' => 0 
                    ]);
                }
            }

            // 5. MỚI BỔ SUNG: Xóa các ảnh phụ CŨ mà user đã bấm nút [X]
            if ($request->has('deleted_gallery_ids')) {
                // Lấy các bản ghi ảnh cần xóa từ mảng ID gửi lên
                $imagesToDelete = ProductImage::whereIn('id', $request->deleted_gallery_ids)->get();
                
                foreach ($imagesToDelete as $img) {
                    // Xóa file vật lý trong thư mục
                    if (Storage::disk('public')->exists($img->image_path)) {
                        Storage::disk('public')->delete($img->image_path);
                    }
                    // Xóa data trong DB
                    $img->delete();
                }
            }

            // 6. Xử lý cập nhật biến thể
            if ($request->product_type === 'variable') {
                // Xóa các biến thể cũ để tạo lại
                $product->variants()->delete(); 

                if ($request->has('variants')) {
                    foreach ($request->variants as $variantData) {
                        $product->variants()->create([
                            'sku'            => $variantData['sku'] ?? strtoupper(\Str::random(8)),
                            'price'          => $variantData['price'],
                            'stock'          => $variantData['stock'],
                            'variant_values' => $variantData['attributes'] ?? [], 
                        ]);
                    }
                }
            } else {
                // Nếu đổi từ Variable -> Simple, dọn dẹp biến thể thừa
                $product->variants()->delete();
            }

            DB::commit();
            return redirect()->route('admin.products.index')
                             ->with('success', 'Cập nhật nông sản "' . $product->name . '" thành công!');
                             
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Lỗi hệ thống: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);
        if ($product->image) Storage::disk('public')->delete($product->image);
        
        // Vì đã có cascade trong DB nên variants sẽ tự động bị xóa
        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Đã xóa sản phẩm!');
    }
    
}
