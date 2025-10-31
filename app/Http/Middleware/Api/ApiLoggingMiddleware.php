<?php

declare(strict_types=1);

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;

class ApiLoggingMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next): mixed
    {
        if (!config('api.logging.enabled', true)) {
            return $next($request);
        }

        $startTime = microtime(true);
        $requestId = $this->generateRequestId();

        // Add request ID to request for tracking
        $request->headers->set('X-Request-ID', $requestId);

        // Log incoming request
        if (config('api.logging.log_requests', true)) {
            $this->logRequest($request, $requestId);
        }

        // Execute request
        $response = $next($request);

        // Calculate execution time
        $executionTime = $this->calculateExecutionTime($startTime);

        // Log response
        if (config('api.logging.log_responses', false)) {
            $this->logResponse($response, $requestId, $executionTime);
        }

        // Log slow requests
        if (config('api.logging.log_slow_requests', true) &&
            $executionTime > config('api.logging.slow_request_threshold', 1000)) {
            $this->logSlowRequest($request, $response, $executionTime, $requestId);
        }

        // Add request ID to response headers
        if ($response instanceof Response) {
            $response->headers->set('X-Request-ID', $requestId);
            $response->headers->set('X-Response-Time', $executionTime . 'ms');
        }

        return $response;
    }

    /**
     * Log the incoming request.
     *
     * @param Request $request
     * @param string $requestId
     * @return void
     */
    protected function logRequest(Request $request, string $requestId): void
    {
        $logData = [
            'request_id' => $requestId,
            'method' => $request->getMethod(),
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toIso8601String(),
            'user_id' => $request->user()?->id,
        ];

        // Include request headers if configured
        if (config('api.logging.include_request_headers', false)) {
            $logData['headers'] = $this->sanitizeHeaders($request->headers->all());
        }

        // Include request body for non-GET requests
        if (!in_array($request->getMethod(), ['GET', 'HEAD'])) {
            $logData['body'] = $this->sanitizeBody($request->all());
        }

        // Include query parameters
        if (!empty($request->query())) {
            $logData['query'] = $request->query();
        }

        Log::channel('api_requests')->info('API Request', $logData);
    }

    /**
     * Log the response.
     *
     * @param mixed $response
     * @param string $requestId
     * @param float $executionTime
     * @return void
     */
    protected function logResponse($response, string $requestId, float $executionTime): void
    {
        if (!$response instanceof Response) {
            return;
        }

        $logData = [
            'request_id' => $requestId,
            'status_code' => $response->getStatusCode(),
            'content_type' => $response->headers->get('Content-Type'),
            'execution_time_ms' => $executionTime,
            'timestamp' => now()->toIso8601String(),
        ];

        // Include response headers if configured
        if (config('api.logging.include_response_headers', false)) {
            $logData['headers'] = $response->headers->all();
        }

        // Include response body for error responses
        if ($response->isClientError() || $response->isServerError()) {
            $logData['body'] = $response->getContent();
        }

        Log::channel('api_responses')->info('API Response', $logData);
    }

    /**
     * Log slow requests.
     *
     * @param Request $request
     * @param mixed $response
     * @param float $executionTime
     * @param string $requestId
     * @return void
     */
    protected function logSlowRequest(Request $request, $response, float $executionTime, string $requestId): void
    {
        $logData = [
            'request_id' => $requestId,
            'method' => $request->getMethod(),
            'url' => $request->fullUrl(),
            'path' => $request->path(),
            'execution_time_ms' => $executionTime,
            'threshold_ms' => config('api.logging.slow_request_threshold', 1000),
            'user_id' => $request->user()?->id,
            'ip' => $request->ip(),
            'timestamp' => now()->toIso8601String(),
        ];

        if ($response instanceof Response) {
            $logData['status_code'] = $response->getStatusCode();
        }

        Log::channel('api_slow_requests')->warning('Slow API Request', $logData);
    }

    /**
     * Sanitize headers to remove sensitive information.
     *
     * @param array $headers
     * @return array
     */
    protected function sanitizeHeaders(array $headers): array
    {
        $sensitiveHeaders = [
            'authorization',
            'cookie',
            'x-csrf-token',
            'x-xsrf-token',
            'x-api-key',
            'api-key',
        ];

        foreach ($sensitiveHeaders as $header) {
            if (isset($headers[$header])) {
                $headers[$header] = ['***REDACTED***'];
            }
        }

        return $headers;
    }

    /**
     * Sanitize request body to remove sensitive information.
     *
     * @param array $body
     * @return array
     */
    protected function sanitizeBody(array $body): array
    {
        $sensitiveFields = [
            'password',
            'password_confirmation',
            'current_password',
            'new_password',
            'api_key',
            'secret',
            'token',
            'credit_card',
            'cvv',
            'pin',
            'ssn',
            'social_security',
        ];

        array_walk_recursive($body, function (&$value, $key) use ($sensitiveFields) {
            foreach ($sensitiveFields as $field) {
                if (stripos($key, $field) !== false) {
                    $value = '***REDACTED***';
                    break;
                }
            }
        });

        return $body;
    }

    /**
     * Generate a unique request ID.
     *
     * @return string
     */
    protected function generateRequestId(): string
    {
        return uniqid('api_', true) . '_' . bin2hex(random_bytes(8));
    }

    /**
     * Calculate execution time in milliseconds.
     *
     * @param float $startTime
     * @return float
     */
    protected function calculateExecutionTime(float $startTime): float
    {
        return round((microtime(true) - $startTime) * 1000, 2);
    }

    /**
     * Clean up old log entries.
     *
     * @return void
     */
    public function terminate(Request $request): void
    {
        // Optional: Clean up old log entries periodically
        if (rand(1, 1000) === 1) {
            $this->cleanupOldLogs();
        }
    }

    /**
     * Clean up old log entries from cache or storage.
     *
     * @return void
     */
    protected function cleanupOldLogs(): void
    {
        // Implementation depends on your logging strategy
        // This is a placeholder for cleanup logic
        Log::channel('api_requests')->debug('Running periodic log cleanup');
    }
}
