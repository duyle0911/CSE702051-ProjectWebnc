<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Cấu hình CORS quyết định request từ domain nào, với method nào,
    | header nào được phép gọi API của bạn.
    | Trong môi trường dev có thể để *, nhưng production nên chỉ định rõ.
    |
    | Tài liệu: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    // Những route cần áp dụng CORS
    'paths' => [
        'api/*',
        'sanctum/csrf-cookie',
    ],

    // Method được phép (GET, POST, PUT, DELETE...)
    'allowed_methods' => explode(',', env('CORS_ALLOWED_METHODS', 'GET,POST,PUT,PATCH,DELETE,OPTIONS')),

    // Origin được phép (domain truy cập API)
    // Có thể để '*' trong dev, còn production thì để rõ ràng VD: https://mydomain.com
    'allowed_origins' => explode(',', env('CORS_ALLOWED_ORIGINS', '*')),

    // Regex để match nhiều origin động (ít dùng)
    'allowed_origins_patterns' => [],

    // Header được phép gửi từ client
    'allowed_headers' => explode(',', env('CORS_ALLOWED_HEADERS', 'Content-Type, Authorization, X-Requested-With, Accept, Origin')),

    // Header sẽ trả về cho client
    'exposed_headers' => explode(',', env('CORS_EXPOSED_HEADERS', 'Authorization, X-CSRF-TOKEN')),

    // Thời gian (giây) cache preflight request (OPTIONS)
    'max_age' => env('CORS_MAX_AGE', 3600),

    // Cho phép gửi cookie/Authorization header không
    'supports_credentials' => env('CORS_SUPPORTS_CREDENTIALS', false),

];
