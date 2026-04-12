<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Imports\ProductsImport;
use App\Models\Attribute;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::with('category', 'variants');

        if ($request->filled('keyword')) {
            $query->where('name', 'like', '%'.$request->keyword.'%');
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
            'file_excel.mimes' => 'File phải có định dạng .xlsx, .xls hoặc .csv.',
        ]);

        $import = new ProductsImport();
        $uploadedFile = $request->file('file_excel');

        try {
            Excel::import($import, $uploadedFile);
        } catch (\Throwable $e) {
            return back()->with('error', 'Không thể đọc file import: '.$e->getMessage());
        }

        $message = 'Đã nhập '.$import->importedCount().' sản phẩm.';

        if ($import->failedCount() > 0) {
            $message .= ' Có '.$import->failedCount().' dòng lỗi.';
        }

        return redirect()
            ->route('admin.products.index')
            ->with('success', $message)
            ->with('import_failures', $import->failures());
    }

    public function show($id)
    {
        $product = Product::with(['category', 'variants', 'images'])->findOrFail($id);

        return view('admin.products.show', compact('product'));
    }

    public function create()
    {
        $categories = Category::all();
        $attributes = Attribute::with('attributeValues')->get();

        return view('admin.products.create', compact('categories', 'attributes'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateProductRequest($request, false);

        DB::beginTransaction();

        try {
            $imagePath = $request->hasFile('image')
                ? $request->file('image')->store('products', 'public')
                : null;

            $isSimple = $validated['product_type'] === 'simple';

            $product = Product::create([
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'product_type' => $validated['product_type'],
                'price' => $isSimple ? ($validated['price'] ?? 0) : 0,
                'sale_price' => $isSimple ? ($validated['sale_price'] ?? null) : null,
                'cost_price' => $isSimple ? ($validated['cost_price'] ?? 0) : 0,
                'stock' => $isSimple ? ($validated['stock'] ?? 0) : 0,
                'weight_grams' => $validated['weight_grams'] ?? 500,
                'image' => $imagePath,
                'description' => $validated['description'] ?? '',
                'content' => $validated['content'] ?? '',
            ]);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('products/gallery', 'public');
                    $product->images()->create([
                        'image_path' => $path,
                        'sort_order' => 0,
                    ]);
                }
            }

            if ($validated['product_type'] === 'variable' && ! empty($validated['variants'])) {
                foreach ($validated['variants'] as $index => $variantData) {
                    $variantImagePath = null;
                    if ($request->hasFile("variants.{$index}.image")) {
                        $variantImagePath = $request->file("variants.{$index}.image")->store('variants', 'public');
                    }

                    $product->variants()->create([
                        'sku' => $variantData['sku'] ?: strtoupper(Str::random(10)),
                        'price' => $variantData['price'],
                        'sale_price' => $variantData['sale_price'] ?? null,
                        'cost_price' => $variantData['cost_price'] ?? 0,
                        'stock' => $variantData['stock'],
                        'variant_values' => $variantData['attributes'] ?? [],
                        'image' => $variantImagePath,
                    ]);
                }
            }

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Them nong san "'.$product->name.'" thanh cong!');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Loi he thong: '.$e->getMessage())->withInput();
        }
    }

    public function edit($id)
    {
        $product = Product::with(['variants', 'images'])->findOrFail($id);
        $categories = Category::all();
        $attributes = Attribute::with('attributeValues')->get();

        return view('admin.products.edit', compact('product', 'categories', 'attributes'));
    }

    public function update(Request $request, $id)
    {
        $product = Product::with('variants')->findOrFail($id);
        $validated = $this->validateProductRequest($request, true);

        DB::beginTransaction();

        try {
            $isSimple = $validated['product_type'] === 'simple';

            $data = [
                'name' => $validated['name'],
                'category_id' => $validated['category_id'],
                'product_type' => $validated['product_type'],
                'description' => $validated['description'] ?? '',
                'content' => $validated['content'] ?? '',
                'price' => $isSimple ? ($validated['price'] ?? 0) : 0,
                'sale_price' => $isSimple ? ($validated['sale_price'] ?? null) : null,
                'cost_price' => $isSimple ? ($validated['cost_price'] ?? 0) : 0,
                'stock' => $isSimple ? ($validated['stock'] ?? 0) : 0,
                'weight_grams' => $validated['weight_grams'] ?? 500,
            ];

            if ($request->hasFile('image')) {
                if ($product->image && Storage::disk('public')->exists($product->image)) {
                    Storage::disk('public')->delete($product->image);
                }

                $data['image'] = $request->file('image')->store('products', 'public');
            }

            $product->update($data);

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $path = $file->store('products/gallery', 'public');
                    $product->images()->create([
                        'image_path' => $path,
                        'sort_order' => 0,
                    ]);
                }
            }

            if ($request->filled('deleted_gallery_ids')) {
                $imagesToDelete = ProductImage::whereIn('id', (array) $request->deleted_gallery_ids)->get();

                foreach ($imagesToDelete as $img) {
                    if (Storage::disk('public')->exists($img->image_path)) {
                        Storage::disk('public')->delete($img->image_path);
                    }

                    $img->delete();
                }
            }

            if ($validated['product_type'] === 'variable') {
                $product->variants()->delete();

                foreach ($validated['variants'] ?? [] as $index => $variantData) {
                    $variantImagePath = null;
                    if ($request->hasFile("variants.{$index}.image")) {
                        $variantImagePath = $request->file("variants.{$index}.image")->store('variants', 'public');
                    }

                    $product->variants()->create([
                        'sku' => $variantData['sku'] ?: strtoupper(Str::random(10)),
                        'price' => $variantData['price'],
                        'sale_price' => $variantData['sale_price'] ?? null,
                        'cost_price' => $variantData['cost_price'] ?? 0,
                        'stock' => $variantData['stock'],
                        'variant_values' => $variantData['attributes'] ?? [],
                        'image' => $variantImagePath ?? ($variantData['existing_image'] ?? null),
                    ]);
                }
            } else {
                $product->variants()->delete();
            }

            DB::commit();

            return redirect()
                ->route('admin.products.index')
                ->with('success', 'Cap nhat nong san "'.$product->name.'" thanh cong!');
        } catch (\Throwable $e) {
            DB::rollBack();

            return back()->with('error', 'Loi he thong: '.$e->getMessage())->withInput();
        }
    }

    public function destroy($id)
    {
        $product = Product::findOrFail($id);

        if ($product->image && Storage::disk('public')->exists($product->image)) {
            Storage::disk('public')->delete($product->image);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Da xoa san pham!');
    }

    protected function validateProductRequest(Request $request, bool $isUpdate): array
    {
        return $request->validate([
            'name' => ['required', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'product_type' => ['required', 'in:simple,variable'],
            'image' => [$isUpdate ? 'nullable' : 'required', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'images.*' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
            'description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0', 'lte:price'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['required_if:product_type,simple', 'integer', 'min:0'],
            'weight_grams' => ['required', 'integer', 'min:100', 'max:50000'],
            'variants' => ['required_if:product_type,variable', 'array', 'min:1'],
            'variants.*.sku' => ['nullable', 'string', 'max:255'],
            'variants.*.price' => ['required_if:product_type,variable', 'numeric', 'min:0'],
            'variants.*.sale_price' => ['nullable', 'numeric', 'min:0', 'lte:variants.*.price'],
            'variants.*.cost_price' => ['nullable', 'numeric', 'min:0'],
            'variants.*.stock' => ['required_if:product_type,variable', 'integer', 'min:0'],
            'variants.*.attributes' => ['nullable', 'array'],
            'variants.*.existing_image' => ['nullable', 'string'],
        ], [
            'name.required' => 'Vui long nhap ten san pham.',
            'image.required' => 'Vui long chon anh dai dien chinh.',
            'sale_price.lte' => 'Gia giam khong duoc lon hon gia thuong.',
            'variants.*.price.required_if' => 'Gia bien the khong duoc de trong.',
            'weight_grams.required' => 'Vui long nhap khoi luong san pham.',
        ]);
    }
}
