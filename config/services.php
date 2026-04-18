<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],
    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),
        'client_secret' => env('GOOGLE_CLIENT_SECRET'),
        'redirect' => env('GOOGLE_REDIRECT_URI'),
    ],

    'gemini' => [
        'api_key' => env('GEMINI_API_KEY'),
        'model' => env('GEMINI_MODEL', 'gemini-2.5-flash'),
        'base_url' => env('GEMINI_BASE_URL', 'https://generativelanguage.googleapis.com/v1beta'),
        'system_instruction' => env(
            'GEMINI_SYSTEM_INSTRUCTION',
            'Ban la tro ly AI cua Nong San Viet. Tra loi ngan gon, lich su, uu tien huong dan mua hang, giao hang, san pham, bao quan thuc pham va cac thong tin lien quan den cua hang. Neu khong chac, hay noi ro va huong nguoi dung sang chat ho tro voi shop.'
        ),
    ],

    'momo' => [
        'base_url' => env('MOMO_BASE_URL', 'https://test-payment.momo.vn'),
        'partner_code' => env('MOMO_PARTNER_CODE'),
        'access_key' => env('MOMO_ACCESS_KEY'),
        'secret_key' => env('MOMO_SECRET_KEY'),
        'redirect_url' => env('MOMO_REDIRECT_URL'),
        'ipn_url' => env('MOMO_IPN_URL'),
        'request_type' => env('MOMO_REQUEST_TYPE', 'payWithMethod'),
        'store_name' => env('MOMO_STORE_NAME', env('APP_NAME', 'Laravel')),
        'store_id' => env('MOMO_STORE_ID', 'nongsanviet'),
    ],

];
