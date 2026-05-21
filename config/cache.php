<?php

return [
    'default' => env('CACHE_STORE', 'redis'),
    'stores' => [
        'array' => ['driver' => 'array', 'serialize' => false],
        'file' => ['driver' => 'file', 'path' => storage_path('framework/cache/data')],
        'redis' => ['driver' => 'redis', 'connection' => 'cache'],
    ],
    'prefix' => env('CACHE_PREFIX', str()->slug(env('APP_NAME', 'laravel'), '_').'_cache_'),
];
