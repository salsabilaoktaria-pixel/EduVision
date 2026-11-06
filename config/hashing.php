<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Default Hash Driver
    |--------------------------------------------------------------------------
    | "ganda" = DOUBLE HASHING: HMAC-SHA256 (berkunci APP_KEY) lalu bcrypt.
    | Driver ini didaftarkan di App\Providers\AppServiceProvider.
    | PENTING: jangan ganti APP_KEY setelah ada akun — semua password akan tidak cocok lagi.
    */
    // Dikunci ke double hashing (tidak bisa diubah lewat .env): HMAC-SHA256 berkunci APP_KEY -> bcrypt.
    'driver' => 'ganda',

    'bcrypt' => [
        'rounds' => env('BCRYPT_ROUNDS', 12),
        'verify' => env('HASH_VERIFY', true),
        'limit' => env('BCRYPT_LIMIT', null),
    ],

    'argon' => [
        'memory' => env('ARGON_MEMORY', 65536),
        'threads' => env('ARGON_THREADS', 1),
        'time' => env('ARGON_TIME', 4),
        'verify' => env('HASH_VERIFY', true),
    ],

    // Laravel otomatis memperbarui hash saat login bila parameter bcrypt di atas dinaikkan.
    'rehash_on_login' => true,

];
