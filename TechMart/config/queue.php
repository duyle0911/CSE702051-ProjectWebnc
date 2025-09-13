<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Queue Connection Name
    |--------------------------------------------------------------------------
    |
    | Đây là kết nối hàng đợi mặc định mà ứng dụng sẽ sử dụng.
    | Có thể thay đổi dễ dàng bằng cách set QUEUE_CONNECTION trong file .env
    |
    | Các driver hỗ trợ: "sync", "database", "beanstalkd", "sqs", "redis", "null"
    |
    */

    'default' => env('QUEUE_CONNECTION', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Queue Connections
    |--------------------------------------------------------------------------
    |
    | Cấu hình chi tiết cho từng loại hàng đợi.
    | Bạn có thể thêm nhiều queue với độ ưu tiên khác nhau.
    |
    */

    'connections' => [

        'sync' => [
            'driver' => 'sync',
        ],

        'database' => [
            'driver'       => 'database',
            'table'        => 'jobs',
            'queue'        => env('QUEUE_NAME', 'default'),
            'retry_after'  => env('QUEUE_RETRY_AFTER', 120), // 2 phút
            'after_commit' => true,
        ],

        'beanstalkd' => [
            'driver'       => 'beanstalkd',
            'host'         => env('BEANSTALKD_HOST', '127.0.0.1'),
            'queue'        => env('BEANSTALKD_QUEUE', 'default'),
            'retry_after'  => 90,
            'block_for'    => 0,
            'after_commit' => true,
        ],

        'sqs' => [
            'driver'       => 'sqs',
            'key'          => env('AWS_ACCESS_KEY_ID'),
            'secret'       => env('AWS_SECRET_ACCESS_KEY'),
            'prefix'       => env('SQS_PREFIX', 'https://sqs.us-east-1.amazonaws.com/'.env('AWS_ACCOUNT_ID')),
            'queue'        => env('SQS_QUEUE', 'default'),
            'suffix'       => env('SQS_SUFFIX'),
            'region'       => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'after_commit' => true,
        ],

        'redis' => [
            'driver'       => 'redis',
            'connection'   => 'default',
            'queue'        => env('REDIS_QUEUE', 'default'),
            'retry_after'  => env('REDIS_RETRY_AFTER', 90),
            'block_for'    => env('REDIS_BLOCK_FOR', null),
            'after_commit' => true,
        ],

        // Queue ưu tiên cao
        'high_priority' => [
            'driver'       => 'database',
            'table'        => 'jobs',
            'queue'        => 'high',
            'retry_after'  => 60,
            'after_commit' => true,
        ],

        // Queue chạy job dài (long-running jobs)
        'long_jobs' => [
            'driver'       => 'database',
            'table'        => 'jobs',
            'queue'        => 'long',
            'retry_after'  => 600, // 10 phút
            'after_commit' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Job Batching
    |--------------------------------------------------------------------------
    |
    | Cấu hình nơi lưu trữ thông tin batch job.
    |
    */

    'batching' => [
        'database' => env('DB_CONNECTION', 'mysql'),
        'table'    => 'job_batches',
    ],

    /*
    |--------------------------------------------------------------------------
    | Failed Queue Jobs
    |--------------------------------------------------------------------------
    |
    | Cấu hình cho việc lưu job bị fail để dễ debug / retry.
    |
    */

    'failed' => [
        'driver'   => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
        'database' => env('DB_CONNECTION', 'mysql'),
        'table'    => 'failed_jobs',
    ],

];
