<?php

namespace App\Imports;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Concerns\OnEachRow;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Row;

class ProductsImport implements OnEachRow, WithHeadingRow
{
    protected int $importedCount = 0;

    protected int $importedVariantsCount = 0;

    protected int $failedCount = 0;

    /**
     * @var array<int, array{row:int,name:string,errors:array<int,string>}>
     */
    protected array $failures = [];

    /**
     * @var array<string, Product>
     */
    protected array $productsByHandle = [];

    public function onRow(Row $row): void
    {
        $rowNumber = (int) $row->getIndex();
        $rawRow = $row->toArray();

        $data = $this->normalizeRow($rawRow);
        $variant = $this->normalizeVariant($rawRow);
        $isVariantRow = $this->variantHasAnyData($variant);

        $handle = $this->normalizeHandle($data['handle'] ?: $data['name'] ?: '');
        if ($handle === '') {
            $this->recordFailure($rowNumber, '(no name)', ['Missing handle/name to group rows.']);
            return;
        }

        $product = $this->productsByHandle[$handle] ?? null;
        $productNameForErrors = (string) ($data['name'] ?: ($product?->name ?: '(no name)'));

        if ($this->rowIsEmpty(array_merge($data, $variant))) {
            return;
        }

        $validationData = array_merge($data, [
            'variant_sku' => $variant['sku'],
            'variant_price' => $variant['price'],
            'variant_sale_price' => $variant['sale_price'],
            'variant_stock' => $variant['stock'],
            'variant_image' => $variant['image'],
        ]);

        $rules = [
            'handle' => ['nullable', 'string', 'max:255'],
            'product_type' => ['nullable', 'in:simple,variable'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'gallery_images' => ['nullable', 'string'],
        ];

        if ($product === null) {
            $rules['name'] = ['required', 'string', 'max:255'];
            $rules['category_id'] = ['required', 'integer', 'exists:categories,id'];
        } else {
            $rules['name'] = ['nullable', 'string', 'max:255'];
            $rules['category_id'] = ['nullable', 'integer', 'exists:categories,id'];
        }

        if ($isVariantRow) {
            $rules['variant_sku'] = ['nullable', 'string', 'max:255'];
            $rules['variant_price'] = ['required', 'numeric', 'min:0'];
            $rules['variant_sale_price'] = ['nullable', 'numeric', 'min:0'];
            $rules['variant_stock'] = ['required', 'integer', 'min:0'];
            $rules['variant_image'] = ['nullable', 'string'];
        }

        $validator = Validator::make($validationData, $rules);

        if ($validator->fails()) {
            $this->recordFailure($rowNumber, $productNameForErrors, $validator->errors()->all());
            return;
        }

        $createdProduct = false;
        $createdVariant = false;

        try {
            DB::transaction(function () use (
                $data,
                $variant,
                $isVariantRow,
                $handle,
                &$product,
                &$createdProduct,
                &$createdVariant
            ) {
                if ($product === null) {
                    $productType = $data['product_type'] ?: ($isVariantRow ? 'variable' : 'simple');
                    if ($isVariantRow) {
                        $productType = 'variable';
                    }

                    $product = Product::create([
                        'name' => $data['name'],
                        'category_id' => (int) $data['category_id'],
                        'product_type' => $productType,
                        'price' => $productType === 'variable' ? 0 : ($data['price'] ?? 0),
                        'sale_price' => $productType === 'variable' ? null : ($data['sale_price'] ?? null),
                        'stock' => $productType === 'variable' ? 0 : ($data['stock'] ?? 0),
                        'description' => $data['description'] ?? '',
                        'content' => $data['content'] ?? '',
                        'image' => $this->resolveImagePath($data['image'] ?? null),
                    ]);

                    foreach ($this->parseGalleryImages($data['gallery_images'] ?? null) as $sortOrder => $imageSource) {
                        $product->images()->create([
                            'image_path' => $this->resolveImagePath($imageSource, 'products/gallery'),
                            'sort_order' => $sortOrder,
                        ]);
                    }

                    $createdProduct = true;
                } elseif ($isVariantRow && $product->product_type !== 'variable') {
                    $product->update([
                        'product_type' => 'variable',
                        'price' => 0,
                        'sale_price' => null,
                    ]);
                }

                if ($product === null || ! $isVariantRow) {
                    return;
                }

                $sku = $variant['sku'] ?: strtoupper(Str::random(12));
                $variantValues = $variant['variant_values'] ?: null;

                $product->variants()->create([
                    'sku' => $sku,
                    'price' => $variant['price'] ?? 0,
                    'sale_price' => $variant['sale_price'],
                    'stock' => $variant['stock'] ?? 0,
                    'variant_values' => $variantValues,
                    'image' => $variant['image'] ? $this->resolveImagePath($variant['image'], 'products/variants') : null,
                ]);

                $createdVariant = true;
            });
        } catch (\Throwable $e) {
            $this->recordFailure($rowNumber, $productNameForErrors, [$e->getMessage()]);
            return;
        }

        if ($product instanceof Product) {
            $this->productsByHandle[$handle] = $product;
        }

        if ($createdProduct) {
            $this->importedCount++;
        }

        if ($createdVariant) {
            $this->importedVariantsCount++;
        }
    }

    public function importedCount(): int
    {
        return $this->importedCount;
    }

    public function importedVariantsCount(): int
    {
        return $this->importedVariantsCount;
    }

    public function failedCount(): int
    {
        return $this->failedCount;
    }

    public function failures(): array
    {
        return $this->failures;
    }

    protected function normalizeRow(array $row): array
    {
        $categoryId = $this->rowValue($row, ['category_id', 'id_danh_muc', 'danh_muc_id']);

        if (! $categoryId) {
            $categoryName = $this->rowValue($row, ['category', 'danh_muc', 'category_name', 'ten_danh_muc']);
            if ($categoryName) {
                $categoryId = Category::where('name', trim((string) $categoryName))->value('id');
            }
        }

        return [
            'handle' => trim((string) $this->rowValue($row, ['handle', 'product_handle', 'product_code', 'ma_san_pham', 'ma'])),
            'name' => trim((string) $this->rowValue($row, ['name', 'ten_san_pham', 'ten_sanpham', 'ten', 'title'])),
            'category_id' => $categoryId ? (int) $categoryId : null,
            'product_type' => $this->normalizeProductType($this->rowValue($row, ['product_type', 'loai_san_pham'])),
            'price' => $this->numericValue($this->rowValue($row, ['price', 'gia_ban', 'gia'])),
            'sale_price' => $this->numericValue($this->rowValue($row, ['sale_price', 'gia_giam', 'gia_khuyen_mai'])),
            'stock' => $this->intValue($this->rowValue($row, ['stock', 'ton_kho', 'so_luong'])),
            'description' => trim((string) $this->rowValue($row, ['description', 'mo_ta'])),
            'content' => trim((string) $this->rowValue($row, ['content', 'noi_dung'])),
            'image' => trim((string) $this->rowValue($row, ['image', 'anh_dai_dien', 'image_path', 'main_image'])),
            'gallery_images' => trim((string) $this->rowValue($row, ['gallery_images', 'images', 'anh_phu', 'gallery'])),
        ];
    }

    /**
     * @return array{sku:string,price:?float,sale_price:?float,stock:?int,image:string,variant_values:array<string,string>}
     */
    protected function normalizeVariant(array $row): array
    {
        $variantValues = $this->extractVariantValues($row);
        $rawValues = $this->rowValue($row, ['variant_values', 'variant_attributes', 'attributes', 'thuoc_tinh_bien_the']);

        if ($rawValues) {
            foreach ($this->parseVariantValuesString((string) $rawValues) as $key => $value) {
                if (! array_key_exists($key, $variantValues) && $value !== '') {
                    $variantValues[$key] = $value;
                }
            }
        }

        return [
            'sku' => trim((string) $this->rowValue($row, ['variant_sku', 'sku'])),
            'price' => $this->numericValue($this->rowValue($row, ['variant_price', 'gia_bien_the', 'price_variant'])),
            'sale_price' => $this->numericValue($this->rowValue($row, ['variant_sale_price', 'variant_sale', 'sale_price_variant'])),
            'stock' => $this->intValue($this->rowValue($row, ['variant_stock', 'variant_ton_kho', 'stock_variant', 'so_luong_bien_the'])),
            'image' => trim((string) $this->rowValue($row, ['variant_image', 'image_variant'])),
            'variant_values' => $variantValues,
        ];
    }

    protected function normalizeProductType(mixed $value): ?string
    {
        $value = strtolower(trim((string) $value));

        return in_array($value, ['simple', 'variable'], true) ? $value : null;
    }

    protected function normalizeHandle(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        return Str::slug($value, '_');
    }

    protected function extractVariantValues(array $row): array
    {
        $values = [];

        for ($i = 1; $i <= 3; $i++) {
            $name = trim((string) $this->rowValue($row, [
                "option{$i}_name",
                "option_{$i}_name",
                "option{$i}name",
                "option_{$i}name",
            ]));
            $value = trim((string) $this->rowValue($row, [
                "option{$i}_value",
                "option_{$i}_value",
                "option{$i}value",
                "option_{$i}value",
            ]));

            if ($value === '') {
                continue;
            }

            if ($name === '') {
                $name = 'Option'.$i;
            }

            $values[$name] = $value;
        }

        return $values;
    }

    /**
     * @return array<string, string>
     */
    protected function parseVariantValuesString(string $raw): array
    {
        $raw = trim($raw);
        if ($raw === '') {
            return [];
        }

        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            $values = [];
            foreach ($decoded as $key => $value) {
                if (! is_string($key)) {
                    continue;
                }
                $values[trim($key)] = trim((string) $value);
            }

            return $values;
        }

        $pairs = preg_split('/[|;,]+/', $raw) ?: [];
        $values = [];

        foreach ($pairs as $pair) {
            $pair = trim($pair);
            if ($pair === '' || ! str_contains($pair, ':')) {
                continue;
            }

            [$key, $value] = explode(':', $pair, 2);
            $key = trim($key);
            $value = trim($value);

            if ($key === '' || $value === '') {
                continue;
            }

            $values[$key] = $value;
        }

        return $values;
    }

    protected function variantHasAnyData(array $variant): bool
    {
        return filled($variant['sku'])
            || $variant['price'] !== null
            || $variant['sale_price'] !== null
            || $variant['stock'] !== null
            || filled($variant['image'])
            || ! empty($variant['variant_values']);
    }

    protected function rowValue(array $row, array $keys, mixed $default = null): mixed
    {
        foreach ($keys as $key) {
            if (array_key_exists($key, $row) && $row[$key] !== null && $row[$key] !== '') {
                return $row[$key];
            }
        }

        return $default;
    }

    protected function numericValue(mixed $value): ?float
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_numeric($value)) {
            return (float) $value;
        }

        $normalized = preg_replace('/[^\d\.\-]/', '', (string) $value);

        return is_numeric($normalized) ? (float) $normalized : null;
    }

    protected function intValue(mixed $value): ?int
    {
        if ($value === null || $value === '') {
            return null;
        }

        if (is_int($value)) {
            return $value;
        }

        if (is_numeric($value)) {
            return (int) $value;
        }

        $normalized = preg_replace('/[^\d\-]/', '', (string) $value);

        return is_numeric($normalized) ? (int) $normalized : null;
    }

    protected function rowIsEmpty(array $data): bool
    {
        foreach ($data as $value) {
            if (is_array($value)) {
                if (! empty($value)) {
                    return false;
                }

                continue;
            }

            if (filled($value)) {
                return false;
            }
        }

        return true;
    }

    protected function parseGalleryImages(?string $value): array
    {
        if (! $value) {
            return [];
        }

        $value = str_replace(["\r\n", "\r", "\n"], ',', $value);
        $parts = preg_split('/[,\|;]+/', $value) ?: [];

        return array_values(array_filter(array_map('trim', $parts)));
    }

    protected function resolveImagePath(?string $source, string $folder = 'products/import'): string
    {
        $source = trim((string) $source);

        if ($source !== '') {
            if (Storage::disk('public')->exists($source)) {
                return $source;
            }

            if (filter_var($source, FILTER_VALIDATE_URL)) {
                try {
                    $response = Http::timeout(20)->get($source);

                    if ($response->successful()) {
                        $path = $folder.'/'.Str::random(24).'.'.$this->guessImageExtension($response->header('content-type'));
                        Storage::disk('public')->put($path, $response->body());

                        return $path;
                    }
                } catch (\Throwable) {
                    // Fall back to placeholder.
                }
            }

            if (is_file($source)) {
                $path = $folder.'/'.basename($source);
                Storage::disk('public')->put($path, file_get_contents($source));

                return $path;
            }
        }

        return $this->placeholderImagePath();
    }

    protected function guessImageExtension(?string $contentType): string
    {
        $contentType = strtolower(trim((string) $contentType));
        $contentType = explode(';', $contentType)[0] ?? $contentType;

        return match ($contentType) {
            'image/png' => 'png',
            'image/webp' => 'webp',
            'image/gif' => 'gif',
            'image/jpeg', 'image/jpg' => 'jpg',
            'image/svg+xml' => 'svg',
            default => 'png',
        };
    }

    protected function placeholderImagePath(): string
    {
        $path = 'products/placeholders/default-product.svg';

        if (! Storage::disk('public')->exists($path)) {
            Storage::disk('public')->put($path, <<<'SVG'
<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 1200">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0%" stop-color="#e2e8f0"/>
      <stop offset="100%" stop-color="#cbd5e1"/>
    </linearGradient>
  </defs>
  <rect width="1200" height="1200" rx="96" fill="url(#g)"/>
  <rect x="220" y="180" width="760" height="760" rx="72" fill="#ffffff" opacity="0.88"/>
  <circle cx="460" cy="420" r="72" fill="#94a3b8"/>
  <path d="M280 760l170-170 130 130 160-160 180 180v120H280z" fill="#94a3b8" opacity="0.85"/>
  <text x="600" y="1020" text-anchor="middle" font-family="Arial, sans-serif" font-size="64" font-weight="700" fill="#475569">
    No image
  </text>
</svg>
SVG);
        }

        return $path;
    }

    protected function recordFailure(int $rowNumber, string $name, array $errors): void
    {
        $this->failedCount++;
        $this->failures[] = [
            'row' => $rowNumber,
            'name' => $name,
            'errors' => $errors,
        ];
    }
}
