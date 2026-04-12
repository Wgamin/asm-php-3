<?php

namespace App\Services;

use Illuminate\Support\Str;

class ShippingService
{
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
     * @param  array{province:?string,district:?string,ward:?string,address_line:?string,address:?string,shipping_region?:?string}  $delivery
     * @param  array<int|string, array<string, mixed>>  $cart
     * @return array<string, array<string, mixed>>
     */
    public function quoteOptions(float $itemsTotal, array $delivery, array $cart = []): array
    {
        $region = (string) ($delivery['province'] ?? $delivery['shipping_region'] ?? $delivery['address'] ?? '');
        $itemCount = $this->cartQuantity($cart);
        $quote = $this->buildInternalQuote($itemsTotal, $region, $itemCount);

        return [
            $quote['key'] => $quote,
        ];
    }

    /**
     * @param  array<string, array<string, mixed>>  $quotes
     */
    public function resolveSelectedQuote(array $quotes, ?string $selectedProvider = null): array
    {
        if ($selectedProvider && isset($quotes[$selectedProvider])) {
            return $quotes[$selectedProvider];
        }

        $defaultProvider = (string) config('shipping.default_provider', 'fast');
        if (isset($quotes[$defaultProvider])) {
            return $quotes[$defaultProvider];
        }

        return $quotes !== [] ? array_values($quotes)[0] : [];
    }

    protected function buildInternalQuote(float $itemsTotal, ?string $region, int $itemCount): array
    {
        $freeShippingThreshold = (float) config('shipping.free_shipping_threshold', 300000);
        $innerCityFee = (float) config('shipping.inner_city_fee', 20000);
        $standardFee = (float) config('shipping.standard_fee', 35000);
        $bulkFee = (float) config('shipping.bulk_fee', 10000);
        $bulkItemThreshold = (int) config('shipping.bulk_item_threshold', 5);
        $extraFee = $itemCount >= $bulkItemThreshold ? $bulkFee : 0.0;

        if ($itemsTotal >= $freeShippingThreshold) {
            return [
                'key' => 'free_shipping',
                'provider' => 'internal',
                'method' => 'free_shipping',
                'label' => 'Miễn phí vận chuyển',
                'carrier' => 'Nông Sản Việt Express',
                'fee' => 0.0,
                'estimated_days' => (int) config('shipping.estimated_days.free_shipping', 2),
                'estimated_delivery_at' => now()->addDays((int) config('shipping.estimated_days.free_shipping', 2)),
                'description' => 'Áp dụng miễn phí ship cho đơn hàng từ ' . number_format($freeShippingThreshold, 0, ',', '.') . 'đ.',
                'is_live' => false,
                'meta' => [
                    'source' => 'internal_rule',
                ],
            ];
        }

        $isInnerCity = $this->isInnerCityRegion($region);
        $baseFee = $isInnerCity ? $innerCityFee : $standardFee;
        $fee = $baseFee + $extraFee;
        $method = $isInnerCity ? 'fast' : 'standard';
        $estimatedDays = (int) config('shipping.estimated_days.' . $method, $isInnerCity ? 1 : 3);

        return [
            'key' => $method,
            'provider' => 'internal',
            'method' => $method,
            'label' => $isInnerCity ? 'Giao nhanh nội thành' : 'Giao hàng tiêu chuẩn',
            'carrier' => $isInnerCity ? 'Nông Sản Việt Express' : 'Nông Sản Việt Delivery',
            'fee' => $fee,
            'estimated_days' => $estimatedDays,
            'estimated_delivery_at' => now()->addDays($estimatedDays),
            'description' => $this->buildDescription($isInnerCity, $extraFee),
            'is_live' => false,
            'meta' => [
                'source' => 'internal_rule',
                'region' => $region,
            ],
        ];
    }

    protected function buildDescription(bool $isInnerCity, float $extraFee): string
    {
        $description = $isInnerCity
            ? 'Phí ship nội thành Hà Nội/TP.HCM.'
            : 'Phí ship tiêu chuẩn cho khu vực ngoài nội thành.';

        if ($extraFee > 0) {
            $description .= ' Đơn nhiều sản phẩm cộng thêm phí xử lý.';
        }

        return $description;
    }

    protected function isInnerCityRegion(?string $region): bool
    {
        $normalizedRegion = $this->normalizeLocation((string) $region);
        $regions = array_map(
            fn (string $value): string => $this->normalizeLocation($value),
            (array) config('shipping.inner_city_regions', ['Ha Noi', 'TP HCM', 'Ho Chi Minh'])
        );

        return in_array($normalizedRegion, $regions, true);
    }

    protected function normalizeLocation(string $value): string
    {
        return Str::of($value)
            ->ascii()
            ->lower()
            ->replace(['tp hcm', 'tphcm', 'hcm', 'sai gon'], 'ho chi minh')
            ->replace(['tp.', 'tp '], 'thanh pho ')
            ->replaceMatches('/\b(thanh pho|tinh)\b/u', ' ')
            ->replaceMatches('/[^a-z0-9]+/', ' ')
            ->squish()
            ->value();
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
}
