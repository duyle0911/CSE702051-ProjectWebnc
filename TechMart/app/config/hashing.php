<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver
    |--------------------------------------------------------------------------
    |
    | Thuật toán băm mặc định. 
    | Có thể chọn: "bcrypt", "argon", "argon2id".
    | Khuyến nghị: "argon2id" vì an toàn hơn.
    |
    */

    'driver' => env('HASH_DRIVER', 'argon2id'),

    /*
    |--------------------------------------------------------------------------
    | Bcrypt Options
    |--------------------------------------------------------------------------
    |
    | rounds càng cao thì càng an toàn nhưng tốn CPU. 
    | Ở production nên để >= 12.
    |
    */

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),
        'verify' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Argon Options
    |--------------------------------------------------------------------------
    |
    | memory: dung lượng RAM (KB) dùng cho mỗi lần băm.
    | threads: số luồng CPU dùng.
    | time: số vòng lặp tính toán.
    |
    | Gợi ý:
    | - Dev: memory thấp hơn để test nhanh.
    | - Production: memory cao (>= 64MB) để chống brute-force.
    |
    */

    'argon' => [
        'memory' => env('ARGON_MEMORY', 65536),   // 64 MB
        'threads' => env('ARGON_THREADS', 2),
        'time' => env('ARGON_TIME', 4),
        'verify' => true,
    ],

];
