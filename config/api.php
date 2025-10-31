<?php

return [
    /*
    |--------------------------------------------------------------------------
    | API Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration for the API layer of the application.
    | It includes settings for rate limiting, pagination, versioning, and more.
    |
    */

    'version' => env('API_VERSION', 'v1'),

    'prefix' => env('API_PREFIX', 'api'),

    'rate_limiting' => [
        'enabled' => env('API_RATE_LIMITING_ENABLED', true),
        'default_limit' => env('API_RATE_LIMIT', 60),
        'authenticated_limit' => env('API_RATE_LIMIT_AUTH', 120),
        'burst_limit' => env('API_RATE_LIMIT_BURST', 10),
        'window' => env('API_RATE_LIMIT_WINDOW', 60), // seconds
        'header_limit' => env('API_RATE_LIMIT_HEADER', 'X-RateLimit-Limit'),
        'header_remaining' => env('API_RATE_LIMIT_HEADER_REMAINING', 'X-RateLimit-Remaining'),
        'header_reset' => env('API_RATE_LIMIT_HEADER_RESET', 'X-RateLimit-Reset'),
    ],

    'pagination' => [
        'default_per_page' => env('API_DEFAULT_PER_PAGE', 15),
        'max_per_page' => env('API_MAX_PER_PAGE', 100),
        'page_parameter' => env('API_PAGE_PARAMETER', 'page'),
        'per_page_parameter' => env('API_PER_PAGE_PARAMETER', 'per_page'),
    ],

    'caching' => [
        'enabled' => env('API_CACHING_ENABLED', true),
        'default_ttl' => env('API_CACHE_TTL', 3600), // seconds
        'etag_enabled' => env('API_ETAG_ENABLED', true),
        'last_modified_enabled' => env('API_LAST_MODIFIED_ENABLED', true),
    ],

    'response' => [
        'wrap_data' => env('API_WRAP_DATA', true),
        'include_metadata' => env('API_INCLUDE_METADATA', true),
        'include_links' => env('API_INCLUDE_LINKS', true),
        'success_http_code' => env('API_SUCCESS_CODE', 200),
        'created_http_code' => env('API_CREATED_CODE', 201),
    ],

    'validation' => [
        'strict_mode' => env('API_VALIDATION_STRICT', true),
        'include_field_paths' => env('API_VALIDATION_INCLUDE_PATHS', true),
        'custom_messages_enabled' => env('API_VALIDATION_CUSTOM_MESSAGES', true),
    ],

    'authentication' => [
        'token_expiration' => env('API_TOKEN_EXPIRATION', 86400), // 24 hours
        'refresh_token_expiration' => env('API_REFRESH_TOKEN_EXPIRATION', 604800), // 7 days
        'password_reset_expiration' => env('API_PASSWORD_RESET_EXPIRATION', 3600), // 1 hour
        'max_login_attempts' => env('API_MAX_LOGIN_ATTEMPTS', 5),
        'lockout_duration' => env('API_LOCKOUT_DURATION', 900), // 15 minutes
    ],

    'logging' => [
        'enabled' => env('API_LOGGING_ENABLED', true),
        'log_requests' => env('API_LOG_REQUESTS', true),
        'log_responses' => env('API_LOG_RESPONSES', false),
        'log_slow_requests' => env('API_LOG_SLOW_REQUESTS', true),
        'slow_request_threshold' => env('API_SLOW_REQUEST_THRESHOLD', 1000), // milliseconds
        'include_request_headers' => env('API_LOG_REQUEST_HEADERS', false),
        'include_response_headers' => env('API_LOG_RESPONSE_HEADERS', false),
        'exclude_paths' => [
            'api/v1/health',
            'api/v1/status',
        ],
    ],

    'features' => [
        'bulk_operations' => env('API_BULK_OPERATIONS', true),
        'export_enabled' => env('API_EXPORT_ENABLED', true),
        'import_enabled' => env('API_IMPORT_ENABLED', true),
        'search_enabled' => env('API_SEARCH_ENABLED', true),
        'filtering_enabled' => env('API_FILTERING_ENABLED', true),
        'sorting_enabled' => env('API_SORTING_ENABLED', true),
        'field_selection_enabled' => env('API_FIELD_SELECTION_ENABLED', true),
        'include_relations_enabled' => env('API_INCLUDE_RELATIONS_ENABLED', true),
    ],

    'export' => [
        'formats' => ['csv', 'json', 'xml', 'xlsx'],
        'default_format' => env('API_EXPORT_DEFAULT_FORMAT', 'csv'),
        'max_records' => env('API_EXPORT_MAX_RECORDS', 10000),
        'chunk_size' => env('API_EXPORT_CHUNK_SIZE', 1000),
        'use_queue' => env('API_EXPORT_USE_QUEUE', true),
        'expiration_time' => env('API_EXPORT_EXPIRATION_TIME', 3600), // 1 hour
    ],

    'import' => [
        'formats' => ['csv', 'json', 'xlsx'],
        'default_format' => env('API_IMPORT_DEFAULT_FORMAT', 'csv'),
        'max_file_size' => env('API_IMPORT_MAX_FILE_SIZE', 10485760), // 10MB
        'chunk_size' => env('API_IMPORT_CHUNK_SIZE', 100),
        'use_queue' => env('API_IMPORT_USE_QUEUE', true),
        'validation_rules' => [
            'required' => ['name', 'email'],
            'email' => ['email'],
            'unique' => ['email'],
        ],
    ],

    'error_handling' => [
        'include_trace' => env('API_ERROR_INCLUDE_TRACE', false),
        'include_previous_exceptions' => env('API_ERROR_INCLUDE_PREVIOUS', false),
        'custom_error_codes' => env('API_ERROR_CUSTOM_CODES', true),
        'log_errors' => env('API_ERROR_LOG_ERRORS', true),
        'notify_errors' => env('API_ERROR_NOTIFY_ERRORS', false),
    ],

    'security' => [
        'throttle_enabled' => env('API_SECURITY_THROTTLE_ENABLED', true),
        'throttle_max_attempts' => env('API_SECURITY_THROTTLE_MAX_ATTEMPTS', 10),
        'throttle_decay_minutes' => env('API_SECURITY_THROTTLE_DECAY_MINUTES', 1),
        'cors_enabled' => env('API_SECURITY_CORS_ENABLED', true),
        'cors_allowed_origins' => env('API_SECURITY_CORS_ORIGINS', '*'),
        'cors_allowed_methods' => env('API_SECURITY_CORS_METHODS', 'GET, POST, PUT, DELETE, OPTIONS'),
        'cors_allowed_headers' => env('API_SECURITY_CORS_HEADERS', 'Content-Type, Authorization, X-Requested-With'),
        'hsts_enabled' => env('API_SECURITY_HSTS_ENABLED', true),
        'hsts_max_age' => env('API_SECURITY_HSTS_MAX_AGE', 31536000), // 1 year
        'content_type_sniffing_protection' => env('API_SECURITY_CONTENT_TYPE_SNIFFING', true),
        'x_frame_options' => env('API_SECURITY_X_FRAME_OPTIONS', 'DENY'),
        'x_content_type_options' => env('API_SECURITY_X_CONTENT_TYPE_OPTIONS', 'nosniff'),
    ],

    'documentation' => [
        'enabled' => env('API_DOCUMENTATION_ENABLED', true),
        'swagger_ui_enabled' => env('API_SWAGGER_UI_ENABLED', true),
        'redoc_enabled' => env('API_REDOC_ENABLED', true),
        'postman_collection_enabled' => env('API_POSTMAN_COLLECTION_ENABLED', true),
        'auto_generate_examples' => env('API_AUTO_GENERATE_EXAMPLES', true),
        'include_request_examples' => env('API_INCLUDE_REQUEST_EXAMPLES', true),
        'include_response_examples' => env('API_INCLUDE_RESPONSE_EXAMPLES', true),
    ],

    'monitoring' => [
        'metrics_enabled' => env('API_METRICS_ENABLED', true),
        'health_check_enabled' => env('API_HEALTH_CHECK_ENABLED', true),
        'uptime_tracking_enabled' => env('API_UPTIME_TRACKING_ENABLED', true),
        'performance_tracking_enabled' => env('API_PERFORMANCE_TRACKING_ENABLED', true),
        'error_tracking_enabled' => env('API_ERROR_TRACKING_ENABLED', true),
        'request_tracking_enabled' => env('API_REQUEST_TRACKING_ENABLED', true),
    ],

    'versioning' => [
        'strategy' => env('API_VERSIONING_STRATEGY', 'uri'), // uri, header, query
        'header_name' => env('API_VERSION_HEADER', 'Accept-Version'),
        'query_parameter' => env('API_VERSION_QUERY_PARAM', 'version'),
        'default_version' => env('API_DEFAULT_VERSION', 'v1'),
        'supported_versions' => ['v1', 'v2'],
        'deprecation_header' => env('API_DEPRECATION_HEADER', 'Sunset'),
        'sunset_header' => env('API_SUNSET_HEADER', 'Sunset'),
    ],
];
