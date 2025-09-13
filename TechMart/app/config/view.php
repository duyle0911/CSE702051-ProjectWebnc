<?php

return [

    /*
    |--------------------------------------------------------------------------
    | View Storage Paths
    |--------------------------------------------------------------------------
    |
    | Laravel sẽ tìm kiếm file Blade trong các đường dẫn dưới đây.
    | Bạn có thể thêm nhiều thư mục (ví dụ: cho Admin, Frontend) để tách biệt.
    |
    */

    'paths' => [
        resource_path('views'),          // View mặc định
        resource_path('views/admin'),    // View cho admin (tùy chọn)
        resource_path('views/frontend'), // View cho giao diện người dùng
    ],

    /*
    |--------------------------------------------------------------------------
    | Compiled View Path
    |--------------------------------------------------------------------------
    |
    | Đây là nơi lưu các file Blade sau khi biên dịch.
    | Mặc định nằm trong storage/framework/views, nhưng có thể chỉnh lại.
    | Bạn cũng có thể phân môi trường: local, staging, production.
    |
    */

    'compiled' => env(
        'VIEW_COMPILED_PATH',
        realpath(storage_path('framework/views'))
    ),

    /*
    |--------------------------------------------------------------------------
    | Blade Caching (Tùy chọn cải tiến)
    |--------------------------------------------------------------------------
    |
    | Nếu muốn tăng tốc trong môi trường production, có thể bật cache view.
    | Trong local thì nên tắt để dễ debug.
    |
    */

    'cache' => env('VIEW_CACHE', app()->environment('production')),

    /*
    |--------------------------------------------------------------------------
    | View Namespaces (Tùy chọn nâng cao)
    |--------------------------------------------------------------------------
    |
    | Cho phép định nghĩa namespace cho view. Ví dụ: dùng @include('admin::dashboard')
    | thay vì phải viết 'views/admin/dashboard.blade.php'.
    |
    */

    'namespaces' => [
        'admin'    => resource_path('views/admin'),
        'frontend' => resource_path('views/frontend'),
    ],

];
