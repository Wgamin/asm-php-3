<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

uses(RefreshDatabase::class);

function createMinimalXlsx(array $headers, array $rows): UploadedFile
{
    $tempBase = tempnam(sys_get_temp_dir(), 'xlsx_import_');
    $xlsxPath = $tempBase . '.xlsx';
    rename($tempBase, $xlsxPath);

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->fromArray([$headers], null, 'A1');
    $sheet->fromArray($rows, null, 'A2');

    $writer = new Xlsx($spreadsheet);
    $writer->save($xlsxPath);

    return new UploadedFile(
        $xlsxPath,
        'products.xlsx',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        null,
        true
    );
}

it('imports products from csv and skips invalid rows', function () {
    Storage::fake('public');

    Storage::disk('public')->put('products/import-main.jpg', 'main-image');
    Storage::disk('public')->put('products/gallery-1.jpg', 'gallery-1');
    Storage::disk('public')->put('products/gallery-2.jpg', 'gallery-2');

    $category = Category::create([
        'name' => 'Rau cu',
        'slug' => 'rau-cu',
    ]);

    $csv = implode("\n", [
        'name,category_id,product_type,price,sale_price,stock,description,content,image,gallery_images',
        sprintf(
            'Ca rot,%d,simple,50000,45000,25,Mo ta,Noi dung,products/import-main.jpg,"products/gallery-1.jpg|products/gallery-2.jpg"',
            $category->id
        ),
        'San pham loi,999,simple,10000,,5,Mo ta,Noi dung,products/import-main.jpg,',
    ]);

    $file = UploadedFile::fake()->createWithContent('products.csv', $csv);

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this
        ->actingAs($admin)
        ->post(route('admin.products.import'), [
            'file_excel' => $file,
        ]);

    $response->assertRedirect(route('admin.products.index'));
    $response->assertSessionHas('success', fn ($value) => str_contains($value, 'Đã nhập 1 sản phẩm.'));
    $response->assertSessionHas('import_failures');

    $product = Product::with('images')->where('name', 'Ca rot')->first();

    expect($product)->not->toBeNull();
    expect($product->image)->toBe('products/import-main.jpg');
    expect($product->stock)->toBe(25);
    expect($product->images)->toHaveCount(2);
});

it('imports products from xlsx files', function () {
    Storage::fake('public');

    Storage::disk('public')->put('products/import-main.jpg', 'main-image');

    $category = Category::create([
        'name' => 'Trai cay',
        'slug' => 'trai-cay',
    ]);

    $file = createMinimalXlsx(
        ['name', 'category_id', 'product_type', 'price', 'sale_price', 'stock', 'description', 'content', 'image', 'gallery_images'],
        [[
            'Bo sap', $category->id, 'simple', 120000, 110000, 12, 'Mo ta', 'Noi dung', 'products/import-main.jpg', '',
        ]]
    );

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this
        ->actingAs($admin)
        ->post(route('admin.products.import'), [
            'file_excel' => $file,
        ]);

    $response->assertRedirect(route('admin.products.index'));
    $response->assertSessionHas('success', fn ($value) => str_contains($value, 'Đã nhập 1 sản phẩm.'));

    $product = Product::where('name', 'Bo sap')->first();

    expect($product)->not->toBeNull();
    expect($product->image)->toBe('products/import-main.jpg');
    expect($product->stock)->toBe(12);
});

it('imports variable products with variants grouped by handle', function () {
    Storage::fake('public');

    Storage::disk('public')->put('products/import-main.jpg', 'main-image');
    Storage::disk('public')->put('products/variant-1.jpg', 'variant-1');
    Storage::disk('public')->put('products/variant-2.jpg', 'variant-2');

    $category = Category::create([
        'name' => 'Ao quan',
        'slug' => 'ao-quan',
    ]);

    $csv = implode("\n", [
        'handle,name,category_id,product_type,description,content,image,gallery_images,option1_name,option1_value,variant_sku,variant_price,variant_stock,variant_image',
        sprintf(
            'ao-thun,Ao thun,%d,variable,Mo ta,Noi dung,products/import-main.jpg,,Mau,Do,TSHIRT-RED,100000,10,products/variant-1.jpg',
            $category->id
        ),
        'ao-thun,,,,,,,,Mau,Xanh,TSHIRT-BLUE,110000,5,products/variant-2.jpg',
    ]);

    $file = UploadedFile::fake()->createWithContent('products-variants.csv', $csv);

    $admin = User::factory()->create([
        'role' => 'admin',
    ]);

    $response = $this
        ->actingAs($admin)
        ->post(route('admin.products.import'), [
            'file_excel' => $file,
        ]);

    $response->assertRedirect(route('admin.products.index'));
    $response->assertSessionHas('success', fn ($value) => str_contains($value, 'Đã nhập 1 sản phẩm.'));

    $product = Product::with('variants')->where('name', 'Ao thun')->first();

    expect($product)->not->toBeNull();
    expect($product->product_type)->toBe('variable');
    expect((float) $product->price)->toBe(0.0);
    expect($product->stock)->toBe(0);
    expect($product->variants)->toHaveCount(2);

    $red = $product->variants->firstWhere('sku', 'TSHIRT-RED');
    $blue = $product->variants->firstWhere('sku', 'TSHIRT-BLUE');

    expect($red)->not->toBeNull();
    expect($red->stock)->toBe(10);
    expect((float) $red->price)->toBe(100000.0);
    expect($red->image)->toBe('products/variant-1.jpg');
    expect($red->variant_values)->toMatchArray(['Mau' => 'Do']);

    expect($blue)->not->toBeNull();
    expect($blue->stock)->toBe(5);
    expect((float) $blue->price)->toBe(110000.0);
    expect($blue->image)->toBe('products/variant-2.jpg');
    expect($blue->variant_values)->toMatchArray(['Mau' => 'Xanh']);
});
