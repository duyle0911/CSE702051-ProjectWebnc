<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Xác định guard và password broker mặc định cho ứng dụng.
    |
    */
    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Các guard xác định cách người dùng được xác thực.
    | Mặc định sử dụng session và provider 'users'.
    |
    */
    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],

        // Thêm guard cho admin nếu cần
        // 'admin' => [
        //     'driver' => 'session',
        //     'provider' => 'admins',
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | Xác định cách lấy dữ liệu người dùng từ database.
    | Hỗ trợ 'eloquent' hoặc 'database'.
    |
    */
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\User::class,
        ],

        // Ví dụ thêm provider cho admin
        // 'admins' => [
        //     'driver' => 'eloquent',
        //     'model' => App\Models\Admin::class,
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | Cấu hình password reset cho từng loại user.
    | 'expire' = token hợp lệ trong bao nhiêu phút
    | 'throttle' = thời gian chờ trước khi yêu cầu token tiếp theo
    |
    */
    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'password_reset_tokens',
            'expire' => 60,   // token hợp lệ 60 phút
            'throttle' => 60, // phải chờ 60 giây trước khi yêu cầu token mới
        ],

        // Ví dụ thêm cấu hình cho admin
        // 'admins' => [
        //     'provider' => 'admins',
        //     'table' => 'admin_password_reset_tokens',
        //     'expire' => 30,
        //     'throttle' => 60,
        // ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Thời gian (giây) trước khi yêu cầu người dùng nhập lại mật khẩu.
    | Mặc định: 3 giờ (10800 giây)
    |
    */
    'password_timeout' => 10800,

];
