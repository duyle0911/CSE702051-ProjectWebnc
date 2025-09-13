<?php

use Laravel\Sanctum\Sanctum;

return [

    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    |
    | Danh sách domain / host được coi là "stateful", nghĩa là khi request
    | từ các domain này sẽ nhận cookie xác thực (SPA, frontend).
    | Thường khai báo cả domain dev (localhost) và domain production.
    |
    */

    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s',
        'localhost,localhost:3000,127.0.0.1,127.0.0.1:8000,::1,yourdomain.com,yourdomain.test',
        Sanctum::currentApplicationUrlWithPort()
    ))),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Guards
    |--------------------------------------------------------------------------
    |
    | Các guard được Sanctum kiểm tra khi xác thực request.
    | Nếu không guard nào thành công -> Sanctum dùng Bearer Token.
    |
    */

    'guard' => ['web', 'api'],

    /*
    |--------------------------------------------------------------------------
    | Expiration Minutes
    |--------------------------------------------------------------------------
    |
    | Thời gian sống (TTL) của token (tính bằng phút).
    | Nếu = null, token không bao giờ hết hạn (không khuyến khích).
    | Có thể cấu hình trong file .env: SANCTUM_EXPIRATION=60
    |
    */

    'expiration' => env('SANCTUM_EXPIRATION', 60),

    /*
    |--------------------------------------------------------------------------
    | Token Prefix
    |--------------------------------------------------------------------------
    |
    | Prefix token giúp dễ phát hiện token rò rỉ trong repo/public logs.
    | Ví dụ: SANCTUM_TOKEN_PREFIX=st_
    |
    */

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', 'st_'),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Middleware
    |--------------------------------------------------------------------------
    |
    | Middleware xử lý khi SPA login với Sanctum.
    | Có thể thay đổi hoặc mở rộng theo nhu cầu.
    |
    */

    'middleware' => [
        'authenticate_session' => Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        'encrypt_cookies'      => App\Http\Middleware\EncryptCookies::class,
        'verify_csrf_token'    => App\Http\Middleware\VerifyCsrfToken::class,
    ],

];
