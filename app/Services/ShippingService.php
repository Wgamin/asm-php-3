<?php

namespace App\Services;

use App\Models\Warehouse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;

class ShippingService
{
    /**
     * Backward-compatible quote API used by the current checkout flow.
     */
    public function quote(float $itemsTotal, ?string $region = null, int $itemCount = 0): array
    {
        $quotes = $this->quoteOptions($itemsTotal, [
            'province' => $region,
            'district' => null,
            'ward' => null,
            'address_line' => null,
            'address' => $region,
        ], [
            [
                'name' => 'Cart',
                'quantity' => max($itemCount, 1),
            ],
        ]);

        return $this->resolveSelectedQuote($quotes);
    }

    /**
     * @param  array{province:?string,district:?string,ward:?string,address_line:?string,address:?string}  $delivery
     * @param  array<int|string, array<string, mixed>>  $cart
     * @return array<string, array<string, mixed>>
     */
    public function quoteOptions(float $itemsTotal, array $delivery, array $cart = []): array
    {
        $quotes = [];

        if ($this->canQuoteStructuredProvider('ghn', $delivery)) {
            try {
                $ghnQuote = $this->buildGhnQuote($itemsTotal, $delivery, $cart);
                $quotes[$ghnQuote['key']] = $ghnQuote;
            } catch (\Throwable $e) {
                report($e);
            }
        }

        if ($this->canQuoteStructuredProvider('ghtk', $delivery)) {
            try {
                $ghtkQuote = $this->buildGhtkQuote($itemsTotal, $delivery, $cart);
                $quotes[$ghtkQuote['key']] = $ghtkQuote;
            } catch (\Throwable $e) {
                report($e);
            }
        }

        uasort($quotes, function (array $left, array $right): int {
            $feeCompare = ((float) $left['fee']) <=> ((float) $right['fee']);

            if ($feeCompare !== 0) {
                return $feeCompare;
            }

            return strcmp((string) $left['key'], (string) $right['key']);
        });

        return $quotes;
    }

    /**
     * @param  array<string, array<string, mixed>>  $quotes
     */
    public function resolveSelectedQuote(array $quotes, ?string $selectedProvider = null): array
    {
        if ($selectedProvider && isset($quotes[$selectedProvider])) {
            return $quotes[$selectedProvider];
        }

        $defaultProvider = (string) config('shipping.default_provider', 'ghn');
        if (isset($quotes[$defaultProvider])) {
            return $quotes[$defaultProvider];
        }

        if ($quotes !== []) {
            return array_values($quotes)[0];
        }

        return [];
    }

    /**
     * @param  array{province:?string,district:?string,ward:?string,address_line:?string,address:?string}  $delivery
     * @param  array<int|string, array<string, mixed>>  $cart
     */
    protected function buildGhnQuote(float $itemsTotal, array $delivery, array $cart): array
    {
        $provider = config('shipping.providers.ghn');

        if (! is_array($provider) || empty($provider['token']) || empty($provider['shop_id'])) {
            throw new RuntimeException('Thiếu cấu hình GHN test.');
        }

        $destination = $this->resolveGhnAddressCodes($delivery);
        $pickup = $this->resolveGhnAddressCodes($this->pickupAddress());
        $dimensions = $this->estimateDimensions($cart);

        $payload = [
            'service_type_id' => 2,
            'from_district_id' => $pickup['district_id'],
            'from_ward_code' => $pickup['ward_code'],
            'to_district_id' => $destination['district_id'],
            'to_ward_code' => $destination['ward_code'],
            'weight' => $this->estimateWeightGrams($cart),
            'length' => $dimensions['length'],
            'width' => $dimensions['width'],
            'height' => $dimensions['height'],
            'insurance_value' => max(0, min((int) round($itemsTotal), 5000000)),
        ];

        $response = Http::acceptJson()
            ->timeout(8)
            ->withHeaders([
                'Token' => (string) $provider['token'],
                'ShopId' => (string) $provider['shop_id'],
                'Content-Type' => 'application/json',
            ])
            ->post($this->ghnUrl('/v2/shipping-order/fee'), $payload)
            ->throw()
            ->json();

        if ((int) ($response['code'] ?? 0) !== 200) {
            throw new RuntimeException((string) ($response['message'] ?? 'GHN test quote failed.'));
        }

        $fee = (float) data_get($response, 'data.total', 0);
        $estimatedDays = max((int) ($provider['estimated_days'] ?? 2), 1);

        return [
            'key' => 'ghn',
            'provider' => 'ghn',
            'method' => 'ghn',
            'label' => (string) ($provider['label'] ?? 'GHN Test'),
            'carrier' => (string) ($provider['carrier'] ?? 'Giao Hàng Nhanh'),
            'fee' => $fee,
            'estimated_days' => $estimatedDays,
            'estimated_delivery_at' => now()->addDays($estimatedDays),
            'description' => 'Phí ship lấy trực tiếp từ API GHN môi trường test.',
            'is_live' => true,
            'meta' => [
                'source' => 'ghn_test_api',
            ],
        ];
    }

    /**
     * @param  array{province:?string,district:?string,ward:?string,address_line:?string,address:?string}  $delivery
     * @param  array<int|string, array<string, mixed>>  $cart
     */
    protected function buildGhtkQuote(float $itemsTotal, array $delivery, array $cart): array
    {
        $provider = config('shipping.providers.ghtk');

        if (! is_array($provider) || empty($provider['token']) || empty($provider['x_client_source'])) {
            throw new RuntimeException('Thiếu cấu hình GHTK staging.');
        }

        $pickup = $this->pickupAddress();
        $estimatedDays = max((int) ($provider['estimated_days'] ?? 3), 1);

        $response = Http::acceptJson()
            ->timeout(8)
            ->withHeaders([
                'Token' => (string) $provider['token'],
                'X-Client-Source' => (string) $provider['x_client_source'],
            ])
            ->get($this->ghtkUrl('/services/shipment/fee'), [
                'pick_address' => (string) ($pickup['address_line'] ?? ''),
                'pick_province' => (string) ($pickup['province'] ?? ''),
                'pick_district' => (string) ($pickup['district'] ?? ''),
                'pick_ward' => (string) ($pickup['ward'] ?? ''),
                'address' => (string) ($delivery['address_line'] ?? $delivery['address'] ?? ''),
                'province' => (string) ($delivery['province'] ?? ''),
                'district' => (string) ($delivery['district'] ?? ''),
                'ward' => (string) ($delivery['ward'] ?? ''),
                'weight' => $this->estimateWeightGrams($cart),
                'value' => max(0, (int) round($itemsTotal)),
                'transport' => 'road',
            ])
            ->throw()
            ->json();

        if (! ($response['success'] ?? false)) {
            throw new RuntimeException((string) ($response['message'] ?? 'GHTK staging quote failed.'));
        }

        if (! data_get($response, 'fee.delivery', true)) {
            throw new RuntimeException('GHTK staging chưa hỗ trợ giao đến địa chỉ này.');
        }

        $fee = (float) data_get($response, 'fee.fee', 0);

        return [
            'key' => 'ghtk',
            'provider' => 'ghtk',
            'method' => 'ghtk',
            'label' => (string) ($provider['label'] ?? 'GHTK Staging'),
            'carrier' => (string) ($provider['carrier'] ?? 'Giao Hàng Tiết Kiệm'),
            'fee' => $fee,
            'estimated_days' => $estimatedDays,
            'estimated_delivery_at' => now()->addDays($estimatedDays),
            'description' => 'Phí ship lấy trực tiếp từ API GHTK môi trường staging/test.',
            'is_live' => true,
            'meta' => [
                'source' => 'ghtk_staging_api',
                'package' => (string) data_get($response, 'fee.name', ''),
            ],
        ];
    }

    /**
     * @param  array{province:?string,district:?string,ward:?string,address_line:?string,address:?string}  $delivery
     */
    protected function canQuoteStructuredProvider(string $providerKey, array $delivery): bool
    {
        $provider = config('shipping.providers.' . $providerKey);

        if (! is_array($provider) || ! ($provider['enabled'] ?? false)) {
            return false;
        }

        $pickup = $this->pickupAddress();

        return filled($pickup['province'] ?? null)
            && filled($pickup['district'] ?? null)
            && filled($pickup['ward'] ?? null)
            && filled($delivery['province'] ?? null)
            && filled($delivery['district'] ?? null)
            && filled($delivery['ward'] ?? null);
    }

    protected function estimateWeightGrams(array $cart): int
    {
        $defaultWeight = max((int) config('shipping.package.default_weight_grams', 500), 100);
        $totalWeight = array_sum(array_map(function (array $line) use ($defaultWeight): int {
            $quantity = max((int) ($line['quantity'] ?? 0), 1);
            $weight = max((int) ($line['weight_grams'] ?? 0), 0);

            return ($weight > 0 ? $weight : $defaultWeight) * $quantity;
        }, $cart));

        return max((int) $totalWeight, $defaultWeight);
    }

    /**
     * @param  array<int|string, array<string, mixed>>  $cart
     * @return array{length:int,width:int,height:int}
     */
    protected function estimateDimensions(array $cart): array
    {
        $quantity = max($this->cartQuantity($cart), 1);
        $baseLength = max((int) config('shipping.package.default_length_cm', 20), 1);
        $baseWidth = max((int) config('shipping.package.default_width_cm', 15), 1);
        $baseHeight = max((int) config('shipping.package.default_height_cm', 10), 1);

        return [
            'length' => min($baseLength, 200),
            'width' => min($baseWidth, 200),
            'height' => min($baseHeight + (($quantity - 1) * 2), 200),
        ];
    }

    /**
     * @param  array<int|string, array<string, mixed>>  $cart
     */
    protected function cartQuantity(array $cart): int
    {
        return max((int) array_sum(array_map(
            static fn (array $line): int => max((int) ($line['quantity'] ?? 0), 0),
            $cart
        )), 0);
    }

    /**
     * @param  array{province?:mixed,district?:mixed,ward?:mixed,address_line?:mixed}  $address
     * @return array{province_id:int,district_id:int,ward_code:string}
     */
    protected function resolveGhnAddressCodes(array $address): array
    {
        $provinceName = (string) ($address['province'] ?? '');
        $districtName = (string) ($address['district'] ?? '');
        $wardName = (string) ($address['ward'] ?? '');

        $province = $this->matchLocation(
            $this->ghnProvinces(),
            $provinceName,
            ['ProvinceName', 'Name', 'province_name']
        );

        if ($province === null) {
            throw new RuntimeException('Không map được tỉnh/thành GHN: ' . $provinceName);
        }

        $provinceId = (int) ($province['ProvinceID'] ?? $province['ProvinceId'] ?? $province['province_id'] ?? 0);

        $district = $this->matchLocation(
            $this->ghnDistricts($provinceId),
            $districtName,
            ['DistrictName', 'Name', 'district_name']
        );

        if ($district === null) {
            throw new RuntimeException('Không map được quận/huyện GHN: ' . $districtName);
        }

        $districtId = (int) ($district['DistrictID'] ?? $district['DistrictId'] ?? $district['district_id'] ?? 0);

        $ward = $this->matchLocation(
            $this->ghnWards($districtId),
            $wardName,
            ['WardName', 'Name', 'ward_name']
        );

        if ($ward === null) {
            throw new RuntimeException('Không map được phường/xã GHN: ' . $wardName);
        }

        $wardCode = (string) ($ward['WardCode'] ?? $ward['WardCodeString'] ?? $ward['ward_code'] ?? '');

        if ($provinceId <= 0 || $districtId <= 0 || $wardCode === '') {
            throw new RuntimeException('Dữ liệu mã địa chỉ GHN không hợp lệ.');
        }

        return [
            'province_id' => $provinceId,
            'district_id' => $districtId,
            'ward_code' => $wardCode,
        ];
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function ghnProvinces(): array
    {
        return Cache::remember('shipping.ghn.provinces', now()->addHours(12), function (): array {
            return $this->requestGhn('/master-data/province');
        });
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function ghnDistricts(int $provinceId): array
    {
        return Cache::remember('shipping.ghn.districts.' . $provinceId, now()->addHours(12), function () use ($provinceId): array {
            return $this->requestGhn('/master-data/district', [
                'province_id' => $provinceId,
            ]);
        });
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    protected function ghnWards(int $districtId): array
    {
        return Cache::remember('shipping.ghn.wards.' . $districtId, now()->addHours(12), function () use ($districtId): array {
            return $this->requestGhn('/master-data/ward', [
                'district_id' => $districtId,
            ]);
        });
    }

    /**
     * @param  array<string, mixed>  $query
     * @return array<int, array<string, mixed>>
     */
    protected function requestGhn(string $path, array $query = []): array
    {
        $provider = config('shipping.providers.ghn');

        $response = Http::acceptJson()
            ->timeout(8)
            ->withHeaders([
                'Token' => (string) ($provider['token'] ?? ''),
                'ShopId' => (string) ($provider['shop_id'] ?? ''),
                'Content-Type' => 'application/json',
            ])
            ->get($this->ghnUrl($path), $query)
            ->throw()
            ->json();

        if ((int) ($response['code'] ?? 0) !== 200) {
            throw new RuntimeException((string) ($response['message'] ?? 'GHN request failed.'));
        }

        return array_values($response['data'] ?? []);
    }

    protected function ghnUrl(string $path): string
    {
        return rtrim((string) config('shipping.providers.ghn.base_url'), '/') . '/' . ltrim($path, '/');
    }

    protected function ghtkUrl(string $path): string
    {
        return rtrim((string) config('shipping.providers.ghtk.base_url'), '/') . '/' . ltrim($path, '/');
    }

    /**
     * @param  array<int, array<string, mixed>>  $items
     * @param  array<int, string>  $candidateKeys
     * @return array<string, mixed>|null
     */
    protected function matchLocation(array $items, string $input, array $candidateKeys): ?array
    {
        $normalizedInput = $this->normalizeLocation($input);

        if ($normalizedInput === '') {
            return null;
        }

        foreach ($items as $item) {
            foreach ($candidateKeys as $key) {
                $candidate = $this->normalizeLocation((string) ($item[$key] ?? ''));
                if ($candidate !== '' && $candidate === $normalizedInput) {
                    return $item;
                }
            }
        }

        foreach ($items as $item) {
            foreach ($candidateKeys as $key) {
                $candidate = $this->normalizeLocation((string) ($item[$key] ?? ''));
                if ($candidate !== '' && (
                    str_contains($candidate, $normalizedInput)
                    || str_contains($normalizedInput, $candidate)
                )) {
                    return $item;
                }
            }
        }

        return null;
    }

    protected function normalizeLocation(string $value): string
    {
        return Str::of($value)
            ->ascii()
            ->lower()
            ->replace(['tp hcm', 'tphcm', 'hcm', 'sai gon'], 'ho chi minh')
            ->replace(['tp.', 'tp '], 'thanh pho ')
            ->replaceMatches('/\b(thanh pho|tinh|quan|huyen|phuong|xa|thi tran)\b/u', ' ')
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish()
            ->value();
    }

    /**
     * @return array{name:string,phone:string,province:?string,district:?string,ward:?string,address_line:?string}
     */
    protected function pickupAddress(): array
    {
        $warehouse = Warehouse::query()
            ->active()
            ->default()
            ->latest('id')
            ->first();

        if ($warehouse instanceof Warehouse) {
            return [
                'name' => $warehouse->name,
                'phone' => $warehouse->phone,
                'province' => $warehouse->province,
                'district' => $warehouse->district,
                'ward' => $warehouse->ward,
                'address_line' => $warehouse->address_line,
            ];
        }

        return [
            'name' => (string) config('shipping.pickup.full_name', config('app.name', 'Nong San Viet')),
            'phone' => (string) config('shipping.pickup.phone', '0900000000'),
            'province' => config('shipping.pickup.province'),
            'district' => config('shipping.pickup.district'),
            'ward' => config('shipping.pickup.ward'),
            'address_line' => config('shipping.pickup.address_line'),
        ];
    }
}
