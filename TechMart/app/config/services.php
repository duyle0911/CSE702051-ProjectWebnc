<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | File này dùng để lưu cấu hình cho các dịch vụ bên thứ ba như Mailgun,
    | Postmark, AWS SES, Stripe, Slack... Việc gom chúng vào một nơi giúp
    | quản lý tập trung và dễ dàng sử dụng trong toàn bộ ứng dụng.
    |
    */

    'mailgun' => [
        'domain'   => env('MAILGUN_DOMAIN'),
        'secret'   => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme'   => env('MAILGUN_SCHEME', 'https'),
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key'    => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
        'token'  => env('AWS_SESSION_TOKEN'), // hỗ trợ IAM Role
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe
    |--------------------------------------------------------------------------
    |
    | Stripe là cổng thanh toán phổ biến. Bạn có thể sử dụng để tích hợp
    | checkout, subscription, billing... Hãy nhớ bảo mật các API key trong .env.
    |
    */

    'stripe' => [
        'secret'      => env('STRIPE_SECRET'),
        'public'      => env('STRIPE_PUBLIC'),
        'webhook'     => [
            'secret' => env('STRIPE_WEBHOOK_SECRET'),
            'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Slack
    |--------------------------------------------------------------------------
    |
    | Slack Webhook cho phép gửi thông báo lỗi hoặc sự kiện từ ứng dụng
    | trực tiếp vào kênh Slack để dev team theo dõi.
    |
    */

    'slack' => [
        'webhook_url' => env('SLACK_WEBHOOK_URL'),
    ],

];
