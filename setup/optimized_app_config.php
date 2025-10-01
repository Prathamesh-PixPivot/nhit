<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Optimized Application Configuration
    |--------------------------------------------------------------------------
    |
    | This configuration optimizes the Laravel application for better
    | performance in production environments.
    |
    */

    'name' => env('APP_NAME', 'Laravel'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'locale' => env('APP_LOCALE', 'en'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'en'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'en_US'),
    'cipher' => 'AES-256-CBC',
    'key' => env('APP_KEY'),
    'previous_keys' => [...array_filter(explode(',', env('APP_PREVIOUS_KEYS', '')))],

    /*
    |--------------------------------------------------------------------------
    | Application Performance Settings
    |--------------------------------------------------------------------------
    */

    'performance' => [
        'enable_query_logging' => env('APP_ENABLE_QUERY_LOGGING', false),
        'enable_route_caching' => env('APP_ENABLE_ROUTE_CACHING', true),
        'enable_config_caching' => env('APP_ENABLE_CONFIG_CACHING', true),
        'enable_view_caching' => env('APP_ENABLE_VIEW_CACHING', true),
        'enable_event_caching' => env('APP_ENABLE_EVENT_CACHING', true),
        'enable_autoloader_optimization' => env('APP_ENABLE_AUTOLOADER_OPTIMIZATION', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Cache Settings
    |--------------------------------------------------------------------------
    */

    'cache' => [
        'default' => env('CACHE_DRIVER', 'redis'),
        'stores' => [
            'redis' => [
                'driver' => 'redis',
                'connection' => 'cache',
                'lock_connection' => 'default',
            ],
            'file' => [
                'driver' => 'file',
                'path' => storage_path('framework/cache/data'),
            ],
            'array' => [
                'driver' => 'array',
                'serialize' => false,
            ],
        ],
        'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Session Settings
    |--------------------------------------------------------------------------
    */

    'session' => [
        'driver' => env('SESSION_DRIVER', 'redis'),
        'lifetime' => env('SESSION_LIFETIME', 120),
        'expire_on_close' => false,
        'encrypt' => false,
        'files' => storage_path('framework/sessions'),
        'connection' => env('SESSION_CONNECTION', null),
        'table' => 'sessions',
        'store' => env('SESSION_STORE', null),
        'lottery' => [2, 100],
        'cookie' => env('SESSION_COOKIE', Str::slug(env('APP_NAME', 'laravel'), '_').'_session'),
        'path' => '/',
        'domain' => env('SESSION_DOMAIN', null),
        'secure' => env('SESSION_SECURE_COOKIE'),
        'http_only' => true,
        'same_site' => 'lax',
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Queue Settings
    |--------------------------------------------------------------------------
    */

    'queue' => [
        'default' => env('QUEUE_CONNECTION', 'redis'),
        'connections' => [
            'sync' => [
                'driver' => 'sync',
            ],
            'database' => [
                'driver' => 'database',
                'table' => 'jobs',
                'queue' => 'default',
                'retry_after' => 90,
                'after_commit' => false,
            ],
            'redis' => [
                'driver' => 'redis',
                'connection' => 'queue',
                'queue' => env('REDIS_QUEUE', 'default'),
                'retry_after' => 90,
                'block_for' => null,
                'after_commit' => false,
            ],
        ],
        'failed' => [
            'driver' => env('QUEUE_FAILED_DRIVER', 'database-uuids'),
            'database' => env('DB_CONNECTION', 'mysql'),
            'table' => 'failed_jobs',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Logging Settings
    |--------------------------------------------------------------------------
    */

    'logging' => [
        'default' => env('LOG_CHANNEL', 'stack'),
        'deprecations' => [
            'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
            'trace' => false,
        ],
        'channels' => [
            'stack' => [
                'driver' => 'stack',
                'channels' => ['single'],
                'ignore_exceptions' => false,
            ],
            'single' => [
                'driver' => 'single',
                'path' => storage_path('logs/laravel.log'),
                'level' => env('LOG_LEVEL', 'debug'),
            ],
            'daily' => [
                'driver' => 'daily',
                'path' => storage_path('logs/laravel.log'),
                'level' => env('LOG_LEVEL', 'debug'),
                'days' => 14,
            ],
            'slack' => [
                'driver' => 'slack',
                'url' => env('LOG_SLACK_WEBHOOK_URL'),
                'username' => 'Laravel Log',
                'emoji' => ':boom:',
                'level' => env('LOG_LEVEL', 'critical'),
            ],
            'papertrail' => [
                'driver' => 'monolog',
                'level' => env('LOG_LEVEL', 'debug'),
                'handler' => env('LOG_PAPERTRAIL_HANDLER', SyslogUdpHandler::class),
                'handler_with' => [
                    'host' => env('PAPERTRAIL_URL'),
                    'port' => env('PAPERTRAIL_PORT'),
                    'connectionString' => 'tls://'.env('PAPERTRAIL_URL').':'.env('PAPERTRAIL_PORT'),
                ],
            ],
            'stderr' => [
                'driver' => 'monolog',
                'level' => env('LOG_LEVEL', 'debug'),
                'handler' => StreamHandler::class,
                'formatter' => env('LOG_STDERR_FORMATTER'),
                'with' => [
                    'stream' => 'php://stderr',
                ],
            ],
            'syslog' => [
                'driver' => 'syslog',
                'level' => env('LOG_LEVEL', 'debug'),
            ],
            'errorlog' => [
                'driver' => 'errorlog',
                'level' => env('LOG_LEVEL', 'debug'),
            ],
            'null' => [
                'driver' => 'monolog',
                'handler' => NullHandler::class,
            ],
            'emergency' => [
                'path' => storage_path('logs/laravel.log'),
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Maintenance Settings
    |--------------------------------------------------------------------------
    */

    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Security Settings
    |--------------------------------------------------------------------------
    */

    'security' => [
        'force_https' => env('APP_FORCE_HTTPS', false),
        'trusted_proxies' => env('APP_TRUSTED_PROXIES', '*'),
        'rate_limiting' => [
            'enabled' => env('APP_RATE_LIMITING', true),
            'max_attempts' => env('APP_RATE_LIMIT_MAX_ATTEMPTS', 60),
            'decay_minutes' => env('APP_RATE_LIMIT_DECAY_MINUTES', 1),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Application Optimization Settings
    |--------------------------------------------------------------------------
    */

    'optimization' => [
        'enable_opcache' => env('APP_ENABLE_OPCACHE', true),
        'enable_apcu' => env('APP_ENABLE_APCU', true),
        'enable_memcached' => env('APP_ENABLE_MEMCACHED', false),
        'enable_redis' => env('APP_ENABLE_REDIS', true),
        'enable_compression' => env('APP_ENABLE_COMPRESSION', true),
        'enable_gzip' => env('APP_ENABLE_GZIP', true),
    ],
];
