<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Cache Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default cache connection that gets used while
    | using this caching library. This connection is used when another is
    | not explicitly specified when executing a given caching function.
    |
    | Supported: "apc", "array", "database", "file",
    |            "memcached", "redis", "dynamodb", "octane", "null"
    |
    */

    'default' => env('CACHE_DRIVER', 'file'),

    /*
    |--------------------------------------------------------------------------
    | Cache Stores
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the cache "stores" for your application as
    | well as their drivers. You may even define multiple stores for the same
    | cache driver to group types of items stored in your caches.
    |
    */

    'stores' => [

        'apc' => [
            'driver' => 'apc',
            'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_'),
        ],

        'array' => [
            'driver' => 'array',
            'serialize' => false,
        ],

        'database' => [
            'driver' => 'database',
            'table' => 'cache',
            'connection' => null,
            'lock_connection' => null,
            'lock_table' => 'cache_locks',
        ],

        'file' => [
            'driver' => 'file',
            'path' => storage_path('framework/cache/data'),
            'lock_path' => storage_path('framework/cache/data'),
        ],

        'memcached' => [
            'driver' => 'memcached',
            'persistent_id' => env('MEMCACHED_PERSISTENT_ID'),
            'sasl' => [
                env('MEMCACHED_USERNAME'),
                env('MEMCACHED_PASSWORD'),
            ],
            'options' => [
                // Memcached options
                'OPT_COMPRESSION' => true,
                'OPT_SERIALIZER' => 'igbinary',
                'OPT_PREFIX_KEY' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_'),
                'OPT_HASH' => 'HASH_MD5',
                'OPT_DISTRIBUTION' => 'DISTRIBUTION_CONSISTENT',
                'OPT_LIBKETAMA_COMPATIBLE' => true,
                'OPT_BUFFER_WRITES' => false,
                'OPT_BINARY_PROTOCOL' => true,
                'OPT_NO_BLOCK' => false,
                'OPT_TCP_NODELAY' => true,
                'OPT_CONNECTION_TIMEOUT' => 1000,
                'OPT_RETRY_TIMEOUT' => 2,
                'OPT_SEND_TIMEOUT' => 1000,
                'OPT_RECV_TIMEOUT' => 1000,
                'OPT_POLL_TIMEOUT' => 1000,
                'OPT_CACHE_LOOKUPS' => false,
                'OPT_SERVER_FAILURE_LIMIT' => 0,
                'OPT_AUTO_EJECT_HOSTS' => true,
                'OPT_REMOVE_FAILED_SERVERS' => true,
            ],
            'servers' => [
                [
                    'host' => env('MEMCACHED_HOST', '127.0.0.1'),
                    'port' => env('MEMCACHED_PORT', 11211),
                    'weight' => 100,
                ],
            ],
        ],

        'redis' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'lock_connection' => 'default',
            'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_'),
            'options' => [
                'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_'),
                'serializer' => 'igbinary',
                'compression' => 'zstd',
                'scan' => 'MATCH',
                'failover' => 'error',
                'persistent' => env('REDIS_PERSISTENT', false),
                'read_timeout' => env('REDIS_READ_TIMEOUT', 10),
                'timeout' => env('REDIS_TIMEOUT', 5),
                'retry_interval' => env('REDIS_RETRY_INTERVAL', 100),
                'max_retries' => env('REDIS_MAX_RETRIES', 3),
                'backoff' => 'default',
            ],
        ],

        'dynamodb' => [
            'driver' => 'dynamodb',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'table' => env('DYNAMODB_CACHE_TABLE', 'cache'),
            'endpoint' => env('DYNAMODB_ENDPOINT'),
            'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_'),
        ],

        'octane' => [
            'driver' => 'octane',
            'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_'),
        ],

        // Enterprise cache stores
        'enterprise' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'lock_connection' => 'default',
            'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_').'enterprise_',
            'options' => [
                'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_').'enterprise_',
                'serializer' => 'igbinary',
                'compression' => 'zstd',
                'scan' => 'MATCH',
                'failover' => 'error',
                'persistent' => env('REDIS_PERSISTENT', false),
                'read_timeout' => env('REDIS_READ_TIMEOUT', 10),
                'timeout' => env('REDIS_TIMEOUT', 5),
                'retry_interval' => env('REDIS_RETRY_INTERVAL', 100),
                'max_retries' => env('REDIS_MAX_RETRIES', 3),
                'backoff' => 'default',
            ],
        ],

        'session' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'lock_connection' => 'default',
            'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_').'session_',
            'options' => [
                'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_').'session_',
                'serializer' => 'igbinary',
                'compression' => 'zstd',
                'scan' => 'MATCH',
                'failover' => 'error',
                'persistent' => env('REDIS_PERSISTENT', false),
                'read_timeout' => env('REDIS_READ_TIMEOUT', 10),
                'timeout' => env('REDIS_TIMEOUT', 5),
                'retry_interval' => env('REDIS_RETRY_INTERVAL', 100),
                'max_retries' => env('REDIS_MAX_RETRIES', 3),
                'backoff' => 'default',
            ],
        ],

        'api' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'lock_connection' => 'default',
            'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_').'api_',
            'options' => [
                'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_').'api_',
                'serializer' => 'igbinary',
                'compression' => 'zstd',
                'scan' => 'MATCH',
                'failover' => 'error',
                'persistent' => env('REDIS_PERSISTENT', false),
                'read_timeout' => env('REDIS_READ_TIMEOUT', 10),
                'timeout' => env('REDIS_TIMEOUT', 5),
                'retry_interval' => env('REDIS_RETRY_INTERVAL', 100),
                'max_retries' => env('REDIS_MAX_RETRIES', 3),
                'backoff' => 'default',
            ],
        ],

        'user' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'lock_connection' => 'default',
            'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_').'user_',
            'options' => [
                'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_').'user_',
                'serializer' => 'igbinary',
                'compression' => 'zstd',
                'scan' => 'MATCH',
                'failover' => 'error',
                'persistent' => env('REDIS_PERSISTENT', false),
                'read_timeout' => env('REDIS_READ_TIMEOUT', 10),
                'timeout' => env('REDIS_TIMEOUT', 5),
                'retry_interval' => env('REDIS_RETRY_INTERVAL', 100),
                'max_retries' => env('REDIS_MAX_RETRIES', 3),
                'backoff' => 'default',
            ],
        ],

        'multitenancy' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'lock_connection' => 'default',
            'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_').'tenant_',
            'options' => [
                'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_').'tenant_',
                'serializer' => 'igbinary',
                'compression' => 'zstd',
                'scan' => 'MATCH',
                'failover' => 'error',
                'persistent' => env('REDIS_PERSISTENT', false),
                'read_timeout' => env('REDIS_READ_TIMEOUT', 10),
                'timeout' => env('REDIS_TIMEOUT', 5),
                'retry_interval' => env('REDIS_RETRY_INTERVAL', 100),
                'max_retries' => env('REDIS_MAX_RETRIES', 3),
                'backoff' => 'default',
            ],
        ],

        'permissions' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'lock_connection' => 'default',
            'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_').'permissions_',
            'options' => [
                'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_').'permissions_',
                'serializer' => 'igbinary',
                'compression' => 'zstd',
                'scan' => 'MATCH',
                'failover' => 'error',
                'persistent' => env('REDIS_PERSISTENT', false),
                'read_timeout' => env('REDIS_READ_TIMEOUT', 10),
                'timeout' => env('REDIS_TIMEOUT', 5),
                'retry_interval' => env('REDIS_RETRY_INTERVAL', 100),
                'max_retries' => env('REDIS_MAX_RETRIES', 3),
                'backoff' => 'default',
            ],
        ],

        'settings' => [
            'driver' => 'redis',
            'connection' => 'cache',
            'lock_connection' => 'default',
            'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_').'settings_',
            'options' => [
                'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_').'settings_',
                'serializer' => 'igbinary',
                'compression' => 'zstd',
                'scan' => 'MATCH',
                'failover' => 'error',
                'persistent' => env('REDIS_PERSISTENT', false),
                'read_timeout' => env('REDIS_READ_TIMEOUT', 10),
                'timeout' => env('REDIS_TIMEOUT', 5),
                'retry_interval' => env('REDIS_RETRY_INTERVAL', 100),
                'max_retries' => env('REDIS_MAX_RETRIES', 3),
                'backoff' => 'default',
            ],
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Key Prefix
    |--------------------------------------------------------------------------
    |
    | When utilizing the APC, database, memcached, Redis, or DynamoDB cache
    | stores there might be other applications using the same cache. For
    | that reason, you may prefix every cache key to avoid collisions.
    |
    */

    'prefix' => env('CACHE_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_cache_'),

    /*
    |--------------------------------------------------------------------------
    | Cache Timeouts
    |--------------------------------------------------------------------------
    |
    | Here you may define the default timeout values for different cache stores.
    | These values are used when no specific timeout is provided.
    |
    */

    'timeouts' => [
        'default' => env('CACHE_DEFAULT_TIMEOUT', 3600), // 1 hour
        'short' => env('CACHE_SHORT_TIMEOUT', 300),    // 5 minutes
        'medium' => env('CACHE_MEDIUM_TIMEOUT', 1800), // 30 minutes
        'long' => env('CACHE_LONG_TIMEOUT', 86400),    // 24 hours
        'session' => env('CACHE_SESSION_TIMEOUT', 7200), // 2 hours
        'api' => env('CACHE_API_TIMEOUT', 600),        // 10 minutes
        'user' => env('CACHE_USER_TIMEOUT', 3600),      // 1 hour
        'permissions' => env('CACHE_PERMISSIONS_TIMEOUT', 86400), // 24 hours
        'settings' => env('CACHE_SETTINGS_TIMEOUT', 604800), // 7 days
        'multitenancy' => env('CACHE_MULTITENANCY_TIMEOUT', 3600), // 1 hour
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Compression
    |--------------------------------------------------------------------------
    |
    | Here you may configure cache compression settings. Compression can help
    | reduce memory usage but may impact performance slightly.
    |
    */

    'compression' => [
        'enabled' => env('CACHE_COMPRESSION_ENABLED', true),
        'threshold' => env('CACHE_COMPRESSION_THRESHOLD', 1024), // 1KB
        'level' => env('CACHE_COMPRESSION_LEVEL', 6), // 1-9, higher = better compression but slower
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Serialization
    |--------------------------------------------------------------------------
    |
    | Here you may configure cache serialization settings. Different serializers
    | have different performance and compression characteristics.
    |
    */

    'serialization' => [
        'default' => env('CACHE_SERIALIZER', 'igbinary'),
        'options' => [
            'php' => [],
            'igbinary' => [
                'compression' => true,
                'compression_level' => 1,
            ],
            'msgpack' => [
                'compression' => true,
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Lock Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure cache lock settings. These settings control how
    | long locks should be held and how long to wait before timing out.
    |
    */

    'locks' => [
        'default_timeout' => env('CACHE_LOCK_TIMEOUT', 10),
        'default_wait' => env('CACHE_LOCK_WAIT', 0),
        'sleep' => env('CACHE_LOCK_SLEEP', 250), // milliseconds
        'max_attempts' => env('CACHE_LOCK_MAX_ATTEMPTS', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Warming
    |--------------------------------------------------------------------------
    |
    | Here you may configure cache warming settings. Cache warming can help
    | improve performance by pre-loading frequently accessed data.
    |
    */

    'warming' => [
        'enabled' => env('CACHE_WARMING_ENABLED', true),
        'schedule' => env('CACHE_WARMING_SCHEDULE', 'hourly'),
        'keys' => [
            'permissions',
            'settings',
            'user_profiles',
            'multitenancy_context',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Monitoring
    |--------------------------------------------------------------------------
    |
    | Here you may configure cache monitoring settings. Monitoring can help
    | track cache performance and identify issues.
    |
    */

    'monitoring' => [
        'enabled' => env('CACHE_MONITORING_ENABLED', true),
        'hit_rate_threshold' => env('CACHE_HIT_RATE_THRESHOLD', 0.8),
        'memory_threshold' => env('CACHE_MEMORY_THRESHOLD', 0.9),
        'slow_operation_threshold' => env('CACHE_SLOW_OPERATION_THRESHOLD', 100), // milliseconds
        'metrics' => [
            'hits',
            'misses',
            'hit_rate',
            'memory_usage',
            'keys_count',
            'evictions',
            'expirations',
        ],
    ],

];
