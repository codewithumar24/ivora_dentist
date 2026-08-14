<?php

use Illuminate\Support\Facades\Facade;

return [
    'name' => env('APP_NAME', 'Ivora Dental Studio'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => [],
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Class aliases
    |--------------------------------------------------------------------------
    | Declared explicitly so `Str::`, `Auth::` and friends resolve inside Blade
    | templates without a leading backslash.
    */

    'aliases' => Facade::defaultAliases()->merge([
        // 'Excel' => Maatwebsite\Excel\Facades\Excel::class,
    ])->toArray(),
];
