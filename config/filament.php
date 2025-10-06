<?php

return [

    'broadcasting' => [
        // ...
    ],

    'default_filesystem_disk' => env('FILAMENT_FILESYSTEM_DISK', 'public'),

    'assets_path' => null,

    'cache_path' => base_path('bootstrap/cache/filament'),

    'livewire_loading_delay' => 'default',

    /*
    |--------------------------------------------------------------------------
    | Filament Path
    |--------------------------------------------------------------------------
    |
    | Ganti sesuai keinginan. Ini akan membuat Filament tersedia di /admin
    |
    */

    'path' => 'admin',

    /*
    |--------------------------------------------------------------------------
    | Authentication
    |--------------------------------------------------------------------------
    |
    | Kita arahkan halaman login Filament ke form login lama (/login)
    |
    */

    'auth' => [
        'guard' => 'admin', // gunakan guard admin agar sesuai dengan sistem login kamu
        'pages' => [
            'login' => '/login',
        ],
    ],

];
