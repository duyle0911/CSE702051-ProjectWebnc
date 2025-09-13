<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Ổ đĩa mặc định Laravel sẽ sử dụng. 
    | Có thể là: "local", "public", "s3", hoặc bất kỳ disk nào bạn tự định nghĩa.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'public'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Bạn có thể định nghĩa nhiều "disk". 
    | Ví dụ: local, public, s3, google drive...
    |
    | Supported Drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        // Dùng cho lưu trữ tạm, private
        'local' => [
            'driver' => 'local',
            'root'   => storage_path('app'),
            'throw'  => false,
        ],

        // Dùng cho upload file public (ảnh, pdf…)
        'public' => [
            'driver'     => 'local',
            'root'       => storage_path('app/public'),
            'url'        => env('APP_URL').'/storage',
            'visibility' => 'public',
            'throw'      => false,
        ],

        // AWS S3 - lưu trữ cloud
        's3' => [
            'driver'                  => 's3',
            'key'                     => env('AWS_ACCESS_KEY_ID'),
            'secret'                  => env('AWS_SECRET_ACCESS_KEY'),
            'region'                  => env('AWS_DEFAULT_REGION', 'ap-southeast-1'),
            'bucket'                  => env('AWS_BUCKET'),
            'url'                     => env('AWS_URL'),
            'endpoint'                => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw'                   => false,
        ],

        // Google Drive (nếu cần tích hợp)
        'google' => [
            'driver' => 's3',
            'key'    => env('GOOGLE_DRIVE_KEY'),
            'secret' => env('GOOGLE_DRIVE_SECRET'),
            'bucket' => env('GOOGLE_DRIVE_BUCKET'),
            'url'    => env('GOOGLE_DRIVE_URL'),
            'throw'  => false,
        ],

        // FTP / SFTP (backup từ xa)
        'backup_ftp' => [
            'driver'   => 'ftp',
            'host'     => env('FTP_HOST'),
            'username' => env('FTP_USERNAME'),
            'password' => env('FTP_PASSWORD'),
            'root'     => env('FTP_ROOT', '/'),
            'port'     => env('FTP_PORT', 21),
            'ssl'      => env('FTP_SSL', false),
            'timeout'  => 30,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Khi chạy `php artisan storage:link`, Laravel sẽ tạo symbolic link
    | từ public/storage -> storage/app/public
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
