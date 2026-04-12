<?php

return [
    'default_provider' => env('SHIPPING_DEFAULT_PROVIDER', 'ghn'),

    'package' => [
        'default_weight_grams' => (int) env('SHIPPING_DEFAULT_WEIGHT_GRAMS', 500),
        'default_length_cm' => (int) env('SHIPPING_DEFAULT_LENGTH_CM', 20),
        'default_width_cm' => (int) env('SHIPPING_DEFAULT_WIDTH_CM', 15),
        'default_height_cm' => (int) env('SHIPPING_DEFAULT_HEIGHT_CM', 10),
    ],

    'pickup' => [
        'full_name' => env('SHIPPING_PICKUP_NAME', env('APP_NAME', 'Nong San Viet')),
        'phone' => env('SHIPPING_PICKUP_PHONE', '0900000000'),
        'province' => env('SHIPPING_PICKUP_PROVINCE', 'Ha Noi'),
        'district' => env('SHIPPING_PICKUP_DISTRICT', 'Quan Cau Giay'),
        'ward' => env('SHIPPING_PICKUP_WARD', 'Phuong Dich Vong'),
        'address_line' => env('SHIPPING_PICKUP_ADDRESS', 'So 1 Duong Test'),
    ],

    'providers' => [
        'ghn' => [
            'enabled' => env('GHN_TEST_ENABLED', false),
            'label' => 'GHN Test',
            'carrier' => 'Giao Hàng Nhanh',
            'base_url' => env('GHN_TEST_BASE_URL', 'https://dev-online-gateway.ghn.vn/shiip/public-api'),
            'token' => env('GHN_TEST_TOKEN'),
            'shop_id' => env('GHN_TEST_SHOP_ID'),
            'estimated_days' => (int) env('GHN_TEST_ESTIMATED_DAYS', 2),
        ],

        'ghtk' => [
            'enabled' => env('GHTK_TEST_ENABLED', false),
            'label' => 'GHTK Staging',
            'carrier' => 'Giao Hàng Tiết Kiệm',
            'base_url' => env('GHTK_TEST_BASE_URL', 'https://services-staging.ghtklab.com'),
            'token' => env('GHTK_TEST_TOKEN'),
            'x_client_source' => env('GHTK_TEST_CLIENT_SOURCE'),
            'estimated_days' => (int) env('GHTK_TEST_ESTIMATED_DAYS', 3),
        ],
    ],
];
