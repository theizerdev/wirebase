<?php

namespace App\Services\Logging;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\JsonFormatter;
use Monolog\Processor\PsrLogMessageProcessor;
use Monolog\Processor\IntrospectionProcessor;
use Monolog\Processor\MemoryUsageProcessor;
use Monolog\Processor\WebProcessor;
use Ramsey\Uuid\Uuid;
use Exception;

class LoggingService
{
    protected array $context = [];
    protected string $correlationId;
    protected array $sensitiveFields = [
        'password',
        'password_confirmation',
        'token',
        'api_key',
        'secret',
        'credit_card',
        'cvv',
        'pin',
        'ssn',
        'social_security',
        'bank_account',
        'routing_number',
        'private_key',
        'client_secret',
        'access_token',
        'refresh_token',
        'authorization',
        'cookie',
        'session',
        'remember_token',
    ];

    public function __construct()
    {
        $this->correlationId = Uuid::uuid4()->toString();
        $this->initializeContext();
    }

    protected function initializeContext(): void
    {
        $this->context = [
            'correlation_id' => $this->correlationId,
            'timestamp' => now()->toIso8601String(),
            'environment' => app()->environment(),
            'application' => config('app.name'),
            'version' => config('app.version', '1.0.0'),
            'hostname' => gethostname(),
            'process_id' => getmypid(),
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
        ];

        if (Request::hasSession()) {
            $this->context['session_id'] = Request::session()->getId();
        }

        if (Auth::check()) {
            $user = Auth::user();
            $this->context['user'] = [
                'id' => $user->id ?? null,
                'email' => $user->email ?? null,
                'role' => $user->role ?? null,
                'permissions' => $user->permissions ?? [],
            ];
        }

        if (Request::has('empresa_id')) {
            $this->context['multitenancy'] = [
                'empresa_id' => Request::get('empresa_id'),
                'sucursal_id' => Request::get('sucursal_id'),
            ];
        }
    }

    public function logApiRequest(array $data): void
    {
        $sanitizedData = $this->sanitizeData($data);

        Log::channel('api_requests')->info('API Request', array_merge($this->context, [
            'type' => 'api_request',
            'method' => Request::method(),
            'url' => Request::fullUrl(),
            'headers' => $this->sanitizeHeaders(Request::header()),
            'ip' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'data' => $sanitizedData,
            'query_params' => Request::query(),
            'route' => Request::route()?->getName(),
            'controller' => Request::route()?->getControllerClass(),
            'action' => Request::route()?->getActionMethod(),
            'middleware' => Request::route()?->gatherMiddleware(),
        ]));
    }

    public function logApiResponse(array $data, int $statusCode, float $responseTime): void
    {
        $sanitizedData = $this->sanitizeData($data);

        Log::channel('api_responses')->info('API Response', array_merge($this->context, [
            'type' => 'api_response',
            'status_code' => $statusCode,
            'response_time' => $responseTime,
            'data' => $sanitizedData,
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
        ]));

        // Log slow requests
        if ($responseTime > config('api.slow_request_threshold', 1000)) {
            $this->logSlowRequest($data, $statusCode, $responseTime);
        }
    }

    public function logApiError(Exception $exception, array $context = []): void
    {
        $errorContext = array_merge($this->context, [
            'type' => 'api_error',
            'error' => [
                'class' => get_class($exception),
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'trace' => $exception->getTraceAsString(),
            ],
            'request' => [
                'method' => Request::method(),
                'url' => Request::fullUrl(),
                'headers' => $this->sanitizeHeaders(Request::header()),
                'input' => $this->sanitizeData(Request::all()),
            ],
        ], $context);

        Log::channel('api_errors')->error('API Error', $errorContext);
    }

    public function logValidationError(array $errors, array $input): void
    {
        Log::channel('api_validation')->warning('Validation Error', array_merge($this->context, [
            'type' => 'validation_error',
            'errors' => $errors,
            'input' => $this->sanitizeData($input),
            'rules' => Request::route()?->gatherRules(),
        ]));
    }

    public function logCorsViolation(string $origin, string $method, array $headers): void
    {
        Log::channel('api_cors')->warning('CORS Violation', array_merge($this->context, [
            'type' => 'cors_violation',
            'origin' => $origin,
            'method' => $method,
            'headers' => $headers,
            'allowed_origins' => config('api.cors.allowed_origins', []),
            'allowed_methods' => config('api.cors.allowed_methods', []),
            'allowed_headers' => config('api.cors.allowed_headers', []),
        ]));
    }

    public function logRateLimitExceeded(string $key, int $limit, int $window): void
    {
        Log::channel('api_rate_limit')->warning('Rate Limit Exceeded', array_merge($this->context, [
            'type' => 'rate_limit_exceeded',
            'key' => $key,
            'limit' => $limit,
            'window' => $window,
            'retry_after' => $window,
        ]));
    }

    public function logSecurityEvent(string $event, array $data = []): void
    {
        Log::channel('security_auth')->info('Security Event', array_merge($this->context, [
            'type' => 'security_event',
            'event' => $event,
            'data' => $this->sanitizeData($data),
        ]));
    }

    public function logSecurityViolation(string $violation, array $data = []): void
    {
        Log::channel('security_access')->warning('Security Violation', array_merge($this->context, [
            'type' => 'security_violation',
            'violation' => $violation,
            'data' => $this->sanitizeData($data),
        ]));
    }

    public function logAuditEvent(string $action, array $data = []): void
    {
        Log::channel('security_audit')->info('Audit Event', array_merge($this->context, [
            'type' => 'audit_event',
            'action' => $action,
            'data' => $this->sanitizeData($data),
            'ip' => Request::ip(),
            'user_agent' => Request::userAgent(),
        ]));
    }

    public function logBusinessEvent(string $event, array $data = []): void
    {
        Log::channel('business_events')->info('Business Event', array_merge($this->context, [
            'type' => 'business_event',
            'event' => $event,
            'data' => $this->sanitizeData($data),
        ]));
    }

    public function logBusinessError(string $error, array $context = []): void
    {
        Log::channel('business_errors')->error('Business Error', array_merge($this->context, [
            'type' => 'business_error',
            'error' => $error,
            'context' => $this->sanitizeData($context),
        ]));
    }

    public function logTransaction(string $operation, array $data, bool $success = true): void
    {
        Log::channel('business_transactions')->info('Transaction', array_merge($this->context, [
            'type' => 'transaction',
            'operation' => $operation,
            'data' => $this->sanitizeData($data),
            'success' => $success,
            'duration' => $this->calculateDuration(),
        ]));
    }

    public function logPerformanceMetric(string $metric, float $value, array $tags = []): void
    {
        if ($metric === 'query_time' && $value > config('api.slow_query_threshold', 1000)) {
            Log::channel('performance_queries')->warning('Slow Query', array_merge($this->context, [
                'type' => 'slow_query',
                'query_time' => $value,
                'tags' => $tags,
            ]));
        }

        if ($metric === 'memory_usage' && $value > config('api.memory_threshold', 134217728)) { // 128MB
            Log::channel('performance_memory')->warning('High Memory Usage', array_merge($this->context, [
                'type' => 'high_memory',
                'memory_usage' => $value,
                'memory_limit' => ini_get('memory_limit'),
                'tags' => $tags,
            ]));
        }
    }

    public function logCacheEvent(string $event, string $key, $value = null, array $metadata = []): void
    {
        $sanitizedValue = $this->sanitizeData($value);

        Log::channel('performance_cache')->info('Cache Event', array_merge($this->context, [
            'type' => 'cache_event',
            'event' => $event,
            'key' => $key,
            'value' => $sanitizedValue,
            'metadata' => $metadata,
        ]));
    }

    public function logDatabaseQuery(string $query, array $bindings, float $time, array $metadata = []): void
    {
        $sanitizedBindings = $this->sanitizeData($bindings);

        Log::channel('database_queries')->info('Database Query', array_merge($this->context, [
            'type' => 'database_query',
            'query' => $query,
            'bindings' => $sanitizedBindings,
            'time' => $time,
            'connection' => $metadata['connection'] ?? config('database.default'),
            'database' => $metadata['database'] ?? null,
        ]));
    }

    public function logIntegrationEvent(string $service, string $event, array $data = []): void
    {
        Log::channel('integration_api')->info('Integration Event', array_merge($this->context, [
            'type' => 'integration_event',
            'service' => $service,
            'event' => $event,
            'data' => $this->sanitizeData($data),
        ]));
    }

    public function logIntegrationError(string $service, Exception $exception, array $context = []): void
    {
        Log::channel('integration_errors')->error('Integration Error', array_merge($this->context, [
            'type' => 'integration_error',
            'service' => $service,
            'error' => [
                'class' => get_class($exception),
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ],
            'context' => $this->sanitizeData($context),
        ]));
    }

    public function logNotificationEvent(string $type, string $recipient, string $status, array $data = []): void
    {
        Log::channel('notifications_'.$type)->info('Notification Event', array_merge($this->context, [
            'type' => 'notification_event',
            'notification_type' => $type,
            'recipient' => $recipient,
            'status' => $status,
            'data' => $this->sanitizeData($data),
        ]));
    }

    public function logQueueJob(string $queue, string $job, array $data = [], string $status = 'processed'): void
    {
        Log::channel('queue_jobs')->info('Queue Job', array_merge($this->context, [
            'type' => 'queue_job',
            'queue' => $queue,
            'job' => $job,
            'status' => $status,
            'data' => $this->sanitizeData($data),
        ]));
    }

    public function logFailedJob(string $queue, string $job, Exception $exception, array $data = []): void
    {
        Log::channel('queue_failed')->error('Failed Job', array_merge($this->context, [
            'type' => 'failed_job',
            'queue' => $queue,
            'job' => $job,
            'error' => [
                'class' => get_class($exception),
                'message' => $exception->getMessage(),
                'code' => $exception->getCode(),
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
            ],
            'data' => $this->sanitizeData($data),
        ]));
    }

    protected function sanitizeData($data)
    {
        if (is_array($data)) {
            return array_map(function ($value, $key) {
                if (in_array(strtolower($key), $this->sensitiveFields, true)) {
                    return '***REDACTED***';
                }
                return $this->sanitizeData($value);
            }, $data, array_keys($data));
        }

        if (is_object($data)) {
            return $this->sanitizeData((array) $data);
        }

        return $data;
    }

    protected function sanitizeHeaders(array $headers): array
    {
        $sensitiveHeaders = [
            'authorization',
            'cookie',
            'x-api-key',
            'x-secret',
            'x-token',
        ];

        return array_map(function ($value, $key) use ($sensitiveHeaders) {
            if (in_array(strtolower($key), $sensitiveHeaders, true)) {
                return '***REDACTED***';
            }
            return $value;
        }, $headers, array_keys($headers));
    }

    protected function logSlowRequest(array $data, int $statusCode, float $responseTime): void
    {
        Log::channel('api_slow_requests')->warning('Slow Request', array_merge($this->context, [
            'type' => 'slow_request',
            'response_time' => $responseTime,
            'status_code' => $statusCode,
            'data' => $this->sanitizeData($data),
            'memory_usage' => memory_get_usage(true),
            'peak_memory' => memory_get_peak_usage(true),
            'database_queries' => DB::getQueryLog(),
        ]));
    }

    protected function calculateDuration(): float
    {
        return microtime(true) - (defined('LARAVEL_START') ? LARAVEL_START : $_SERVER['REQUEST_TIME_FLOAT'] ?? microtime(true));
    }

    public function getCorrelationId(): string
    {
        return $this->correlationId;
    }

    public function setCorrelationId(string $correlationId): self
    {
        $this->correlationId = $correlationId;
        $this->context['correlation_id'] = $correlationId;
        return $this;
    }

    public function addContext(string $key, $value): self
    {
        $this->context[$key] = $this->sanitizeData($value);
        return $this;
    }

    public function removeContext(string $key): self
    {
        unset($this->context[$key]);
        return $this;
    }

    public function clearContext(): self
    {
        $this->initializeContext();
        return $this;
    }
}
