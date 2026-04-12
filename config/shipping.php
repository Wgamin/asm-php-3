<?php

return [
    'default_provider' => env('SHIPPING_DEFAULT_PROVIDER', 'fast'),

    'free_shipping_threshold' => (float) env('SHIPPING_FREE_SHIPPING_THRESHOLD', 300000),
    'inner_city_fee' => (float) env('SHIPPING_INNER_CITY_FEE', 20000),
    'standard_fee' => (float) env('SHIPPING_STANDARD_FEE', 35000),
    'bulk_fee' => (float) env('SHIPPING_BULK_FEE', 10000),
    'bulk_item_threshold' => (int) env('SHIPPING_BULK_ITEM_THRESHOLD', 5),

    'estimated_days' => [
        'fast' => (int) env('SHIPPING_FAST_ESTIMATED_DAYS', 1),
        'standard' => (int) env('SHIPPING_STANDARD_ESTIMATED_DAYS', 3),
        'free_shipping' => (int) env('SHIPPING_FREE_ESTIMATED_DAYS', 2),
    ],

    'inner_city_regions' => [
        'Ha Noi',
        'Hanoi',
        'TP HCM',
        'Ho Chi Minh',
        'Sai Gon',
    ],
];
