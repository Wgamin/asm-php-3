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

    protected int $failedCount = 0;

    /**
     * @var array<int, array{row:int,name:string,errors:array<int,string>}>
     */
    protected array $failures = [];

    public function onRow(Row $row): void
    {
        $rowNumber = (int) $row->getIndex();
        $data = $this->normalizeRow($row->toArray());

        if ($this->rowIsEmpty($data)) {
            return;
        }

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'integer', 'exists:categories,id'],
            'product_type' => ['nullable', 'in:simple,variable'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'sale_price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'content' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'gallery_images' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            $this->recordFailure($rowNumber, (string) ($data['name'] ?? '(no name)'), $validator->errors()->all());
            return;
        }

        try {
            DB::transaction(function () use ($data) {
                $product = Product::create([
                    'name' => $data['name'],
                    'category_id' => (int) $data['category_id'],
                    'product_type' => $data['product_type'] ?: 'simple',
                    'price' => $data['price'] ?? 0,
                    'sale_price' => $data['sale_price'] ?? null,
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
            });

            $this->importedCount++;
        } catch (\Throwable $e) {
            $this->recordFailure($rowNumber, (string) ($data['name'] ?? '(no name)'), [$e->getMessage()]);
        }
    }

    public function importedCount(): int
    {
        return $this->importedCount;
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
            'name' => trim((string) $this->rowValue($row, ['name', 'ten_san_pham', 'ten_sanpham', 'ten'])),
            'category_id' => $categoryId ? (int) $categoryId : null,
            'product_type' => strtolower(trim((string) $this->rowValue($row, ['product_type', 'loai_san_pham']))) ?: 'simple',
            'price' => $this->numericValue($this->rowValue($row, ['price', 'gia_ban', 'gia'])),
            'sale_price' => $this->numericValue($this->rowValue($row, ['sale_price', 'gia_giam', 'gia_khuyen_mai'])),
            'description' => trim((string) $this->rowValue($row, ['description', 'mo_ta'])),
            'content' => trim((string) $this->rowValue($row, ['content', 'noi_dung'])),
            'image' => trim((string) $this->rowValue($row, ['image', 'anh_dai_dien', 'image_path'])),
            'gallery_images' => trim((string) $this->rowValue($row, ['gallery_images', 'images', 'anh_phu'])),
        ];
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

