<?php

use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SyslogUdpHandler;
use Monolog\Handler\RotatingFileHandler;
use Monolog\Handler\SlackWebhookHandler;
use Monolog\Handler\TelegramBotHandler;
use Monolog\Formatter\JsonFormatter;
use Monolog\Formatter\LineFormatter;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Log Channel
    |--------------------------------------------------------------------------
    |
    | This option defines the default log channel that gets used when writing
    | messages to the logs. The name specified in this option should match
    | one of the channels defined in the "channels" configuration array.
    |
    */

    'default' => env('LOG_CHANNEL', 'stack'),

    /*
    |--------------------------------------------------------------------------
    | Deprecations Log Channel
    |--------------------------------------------------------------------------
    |
    | This option controls the log channel that should be used to log warnings
    | regarding deprecated PHP and library features. This will channel will
    | be assigned to all deprecation handlers and will be logged separately.
    |
    */

    'deprecations' => [
        'channel' => env('LOG_DEPRECATIONS_CHANNEL', 'null'),
        'trace' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Log Channels
    |--------------------------------------------------------------------------
    |
    | Here you may configure the log channels for your application. Out of
    | the box, Laravel uses the Monolog PHP logging library. This gives
    | you a variety of powerful log handlers / formatters to utilize.
    |
    | Available Drivers: "single", "daily", "slack", "syslog",
    |                    "errorlog", "monolog",
    |                    "custom", "stack"
    |
    */

    'channels' => [
        'stack' => [
            'driver' => 'stack',
            'channels' => ['single', 'daily', 'api_requests', 'api_errors'],
            'ignore_exceptions' => false,
        ],

        'single' => [
            'driver' => 'single',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        'daily' => [
            'driver' => 'daily',
            'path' => storage_path('logs/laravel.log'),
            'level' => env('LOG_LEVEL', 'debug'),
            'days' => 14,
            'replace_placeholders' => true,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        'slack' => [
            'driver' => 'slack',
            'url' => env('LOG_SLACK_WEBHOOK_URL'),
            'username' => 'Laravel Enterprise Log',
            'emoji' => ':boom:',
            'level' => env('LOG_LEVEL', 'critical'),
            'replace_placeholders' => true,
            'formatter' => JsonFormatter::class,
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
            'processors' => [\Monolog\Processor\PsrLogMessageProcessor::class],
            'formatter' => JsonFormatter::class,
        ],

        'stderr' => [
            'driver' => 'monolog',
            'level' => env('LOG_LEVEL', 'debug'),
            'handler' => StreamHandler::class,
            'formatter' => env('LOG_STDERR_FORMATTER', JsonFormatter::class),
            'with' => [
                'stream' => 'php://stderr',
            ],
            'processors' => [\Monolog\Processor\PsrLogMessageProcessor::class],
        ],

        'syslog' => [
            'driver' => 'syslog',
            'level' => env('LOG_LEVEL', 'debug'),
            'facility' => LOG_USER,
            'replace_placeholders' => true,
            'formatter' => JsonFormatter::class,
        ],

        'errorlog' => [
            'driver' => 'errorlog',
            'level' => env('LOG_LEVEL', 'debug'),
            'replace_placeholders' => true,
            'formatter' => JsonFormatter::class,
        ],

        'null' => [
            'driver' => 'monolog',
            'handler' => NullHandler::class,
        ],

        'emergency' => [
            'path' => storage_path('logs/emergency.log'),
        ],

        // API-specific logging channels
        'api_requests' => [
            'driver' => 'daily',
            'path' => storage_path('logs/api/requests.log'),
            'level' => 'info',
            'days' => 30,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        'api_responses' => [
            'driver' => 'daily',
            'path' => storage_path('logs/api/responses.log'),
            'level' => 'info',
            'days' => 7,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        'api_errors' => [
            'driver' => 'daily',
            'path' => storage_path('logs/api/errors.log'),
            'level' => 'error',
            'days' => 60,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        'api_slow_requests' => [
            'driver' => 'daily',
            'path' => storage_path('logs/api/slow_requests.log'),
            'level' => 'warning',
            'days' => 14,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        'api_validation' => [
            'driver' => 'daily',
            'path' => storage_path('logs/api/validation.log'),
            'level' => 'warning',
            'days' => 30,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        'api_cors' => [
            'driver' => 'daily',
            'path' => storage_path('logs/api/cors.log'),
            'level' => 'warning',
            'days' => 30,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        'api_rate_limit' => [
            'driver' => 'daily',
            'path' => storage_path('logs/api/rate_limit.log'),
            'level' => 'warning',
            'days' => 30,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        // Security logging channels
        'security_auth' => [
            'driver' => 'daily',
            'path' => storage_path('logs/security/auth.log'),
            'level' => 'info',
            'days' => 90,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        'security_access' => [
            'driver' => 'daily',
            'path' => storage_path('logs/security/access.log'),
            'level' => 'warning',
            'days' => 90,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        'security_audit' => [
            'driver' => 'daily',
            'path' => storage_path('logs/security/audit.log'),
            'level' => 'info',
            'days' => 365,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        // Business logic logging channels
        'business_events' => [
            'driver' => 'daily',
            'path' => storage_path('logs/business/events.log'),
            'level' => 'info',
            'days' => 60,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        'business_errors' => [
            'driver' => 'daily',
            'path' => storage_path('logs/business/errors.log'),
            'level' => 'error',
            'days' => 60,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        'business_transactions' => [
            'driver' => 'daily',
            'path' => storage_path('logs/business/transactions.log'),
            'level' => 'info',
            'days' => 90,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        // Performance logging channels
        'performance_queries' => [
            'driver' => 'daily',
            'path' => storage_path('logs/performance/queries.log'),
            'level' => 'warning',
            'days' => 14,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        'performance_cache' => [
            'driver' => 'daily',
            'path' => storage_path('logs/performance/cache.log'),
            'level' => 'info',
            'days' => 7,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        'performance_memory' => [
            'driver' => 'daily',
            'path' => storage_path('logs/performance/memory.log'),
            'level' => 'warning',
            'days' => 7,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        // Integration logging channels
        'integration_webhooks' => [
            'driver' => 'daily',
            'path' => storage_path('logs/integration/webhooks.log'),
            'level' => 'info',
            'days' => 30,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        'integration_api' => [
            'driver' => 'daily',
            'path' => storage_path('logs/integration/api.log'),
            'level' => 'info',
            'days' => 30,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        'integration_errors' => [
            'driver' => 'daily',
            'path' => storage_path('logs/integration/errors.log'),
            'level' => 'error',
            'days' => 60,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        // External service logging channels
        'external_services' => [
            'driver' => 'daily',
            'path' => storage_path('logs/external/services.log'),
            'level' => 'info',
            'days' => 30,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        'external_errors' => [
            'driver' => 'daily',
            'path' => storage_path('logs/external/errors.log'),
            'level' => 'error',
            'days' => 60,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        // Notification logging channels
        'notifications_email' => [
            'driver' => 'daily',
            'path' => storage_path('logs/notifications/email.log'),
            'level' => 'info',
            'days' => 30,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        'notifications_sms' => [
            'driver' => 'daily',
            'path' => storage_path('logs/notifications/sms.log'),
            'level' => 'info',
            'days' => 30,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        'notifications_push' => [
            'driver' => 'daily',
            'path' => storage_path('logs/notifications/push.log'),
            'level' => 'info',
            'days' => 30,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        // Queue logging channels
        'queue_jobs' => [
            'driver' => 'daily',
            'path' => storage_path('logs/queue/jobs.log'),
            'level' => 'info',
            'days' => 14,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        'queue_failed' => [
            'driver' => 'daily',
            'path' => storage_path('logs/queue/failed.log'),
            'level' => 'error',
            'days' => 60,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        // Database logging channels
        'database_queries' => [
            'driver' => 'daily',
            'path' => storage_path('logs/database/queries.log'),
            'level' => 'info',
            'days' => 7,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        'database_slow' => [
            'driver' => 'daily',
            'path' => storage_path('logs/database/slow.log'),
            'level' => 'warning',
            'days' => 14,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        'database_errors' => [
            'driver' => 'daily',
            'path' => storage_path('logs/database/errors.log'),
            'level' => 'error',
            'days' => 60,
            'formatter' => JsonFormatter::class,
            'formatter_with' => [
                'batch_mode' => true,
                'append_newline' => true,
            ],
        ],

        // Custom processors for all channels
        'processors' => [
            \Monolog\Processor\PsrLogMessageProcessor::class,
            \Monolog\Processor\IntrospectionProcessor::class,
            \Monolog\Processor\MemoryUsageProcessor::class,
            \Monolog\Processor\MemoryPeakUsageProcessor::class,
            \Monolog\Processor\ProcessIdProcessor::class,
            \Monolog\Processor\WebProcessor::class,
        ],
    ],

];
