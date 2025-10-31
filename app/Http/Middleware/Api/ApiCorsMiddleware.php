<?php

declare(strict_types=1);

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ApiCorsMiddleware
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
        if (!config('api.cors.enabled', true)) {
            return $next($request);
        }

        // Handle preflight OPTIONS requests
        if ($request->getMethod() === 'OPTIONS') {
            return $this->handlePreflight($request);
        }

        // Process the request
        $response = $next($request);

        // Add CORS headers to the response
        return $this->addCorsHeaders($request, $response);
    }

    /**
     * Handle preflight OPTIONS request.
     *
     * @param Request $request
     * @return Response
     */
    protected function handlePreflight(Request $request): Response
    {
        $response = response('', 204);

        return $this->addCorsHeaders($request, $response);
    }

    /**
     * Add CORS headers to response.
     *
     * @param Request $request
     * @param mixed $response
     * @return mixed
     */
    protected function addCorsHeaders(Request $request, $response): mixed
    {
        if (!$response instanceof Response) {
            return $response;
        }

        $config = config('api.cors');

        // Get the origin from the request
        $origin = $request->header('Origin');

        // Validate origin against allowed origins
        if ($this->isOriginAllowed($origin, $config['allowed_origins'] ?? ['*'])) {
            $response->headers->set('Access-Control-Allow-Origin', $origin ?: '*');
        }

        // Set allowed methods
        $allowedMethods = $config['allowed_methods'] ?? ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS'];
        $response->headers->set('Access-Control-Allow-Methods', implode(', ', $allowedMethods));

        // Set allowed headers
        $allowedHeaders = $this->getAllowedHeaders($request, $config['allowed_headers'] ?? []);
        if (!empty($allowedHeaders)) {
            $response->headers->set('Access-Control-Allow-Headers', implode(', ', $allowedHeaders));
        }

        // Set exposed headers
        $exposedHeaders = $config['exposed_headers'] ?? [
            'X-Request-ID',
            'X-Response-Time',
            'X-RateLimit-Limit',
            'X-RateLimit-Remaining',
            'X-RateLimit-Reset',
            'X-Pagination-Total',
            'X-Pagination-Per-Page',
            'X-Pagination-Current-Page',
            'X-Pagination-Last-Page',
            'Content-Disposition',
        ];

        if (!empty($exposedHeaders)) {
            $response->headers->set('Access-Control-Expose-Headers', implode(', ', $exposedHeaders));
        }

        // Set max age for preflight caching
        $maxAge = $config['max_age'] ?? 86400; // 24 hours default
        $response->headers->set('Access-Control-Max-Age', (string) $maxAge);

        // Set credentials support
        $supportsCredentials = $config['supports_credentials'] ?? true;
        if ($supportsCredentials) {
            $response->headers->set('Access-Control-Allow-Credentials', 'true');
        }

        // Add security headers
        $this->addSecurityHeaders($response, $config);

        // Add additional API headers
        $this->addApiHeaders($response);

        return $response;
    }

    /**
     * Check if origin is allowed.
     *
     * @param string|null $origin
     * @param array $allowedOrigins
     * @return bool
     */
    protected function isOriginAllowed(?string $origin, array $allowedOrigins): bool
    {
        if (empty($origin)) {
            return false;
        }

        // Allow all origins if wildcard is present
        if (in_array('*', $allowedOrigins)) {
            return true;
        }

        // Check exact match
        if (in_array($origin, $allowedOrigins)) {
            return true;
        }

        // Check regex patterns
        foreach ($allowedOrigins as $allowedOrigin) {
            if (str_starts_with($allowedOrigin, '/') && preg_match($allowedOrigin, $origin)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get allowed headers based on request and configuration.
     *
     * @param Request $request
     * @param array $configHeaders
     * @return array
     */
    protected function getAllowedHeaders(Request $request, array $configHeaders): array
    {
        // If headers are explicitly requested in the preflight
        $requestedHeaders = $request->header('Access-Control-Request-Headers');

        if ($requestedHeaders) {
            $requestedHeaders = array_map('strtolower', array_map('trim', explode(',', $requestedHeaders)));

            // Allow standard headers by default
            $standardHeaders = [
                'content-type',
                'authorization',
                'x-requested-with',
                'x-csrf-token',
                'x-api-key',
                'x-api-version',
                'accept',
                'accept-language',
                'accept-encoding',
                'cache-control',
                'pragma',
                'origin',
                'referer',
                'user-agent',
            ];

            // Merge with configured headers
            $allowedHeaders = array_unique(array_merge($standardHeaders, array_map('strtolower', $configHeaders)));

            // Filter to only include requested headers that are allowed
            return array_intersect($requestedHeaders, $allowedHeaders);
        }

        return array_merge([
            'Content-Type',
            'Authorization',
            'X-Requested-With',
            'X-CSRF-Token',
            'X-API-Key',
            'X-API-Version',
            'Accept',
            'Accept-Language',
        ], $configHeaders);
    }

    /**
     * Add security headers to response.
     *
     * @param Response $response
     * @param array $config
     * @return void
     */
    protected function addSecurityHeaders(Response $response, array $config): void
    {
        // Content Security Policy
        if ($config['content_security_policy'] ?? false) {
            $csp = $config['content_security_policy'];
            $response->headers->set('Content-Security-Policy', $csp);
        }

        // X-Frame-Options
        $xFrameOptions = $config['x_frame_options'] ?? 'DENY';
        $response->headers->set('X-Frame-Options', $xFrameOptions);

        // X-Content-Type-Options
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Referrer Policy
        $referrerPolicy = $config['referrer_policy'] ?? 'strict-origin-when-cross-origin';
        $response->headers->set('Referrer-Policy', $referrerPolicy);

        // Permissions Policy
        $permissionsPolicy = $config['permissions_policy'] ?? 'geolocation=(), microphone=(), camera=()';
        $response->headers->set('Permissions-Policy', $permissionsPolicy);

        // Strict Transport Security (HSTS)
        if ($config['hsts_enabled'] ?? true) {
            $hstsMaxAge = $config['hsts_max_age'] ?? 31536000; // 1 year
            $hstsIncludeSubDomains = $config['hsts_include_subdomains'] ?? true;
            $hstsPreload = $config['hsts_preload'] ?? true;

            $hstsValue = "max-age={$hstsMaxAge}";
            if ($hstsIncludeSubDomains) {
                $hstsValue .= '; includeSubDomains';
            }
            if ($hstsPreload) {
                $hstsValue .= '; preload';
            }

            $response->headers->set('Strict-Transport-Security', $hstsValue);
        }
    }

    /**
     * Add additional API headers.
     *
     * @param Response $response
     * @return void
     */
    protected function addApiHeaders(Response $response): void
    {
        // API version
        $apiVersion = config('api.version', 'v1');
        $response->headers->set('X-API-Version', $apiVersion);

        // Server information
        $serverName = config('api.server_name', 'Laravel Enterprise API');
        $response->headers->set('X-Powered-By', $serverName);

        // Cache control for API responses
        if (!$response->headers->has('Cache-Control')) {
            $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }
    }

    /**
     * Log CORS violations for monitoring.
     *
     * @param Request $request
     * @param string $reason
     * @return void
     */
    protected function logCorsViolation(Request $request, string $reason): void
    {
        if (!config('api.cors.log_violations', false)) {
            return;
        }

        \Illuminate\Support\Facades\Log::channel('api_cors')->warning('CORS violation', [
            'origin' => $request->header('Origin'),
            'method' => $request->getMethod(),
            'url' => $request->fullUrl(),
            'reason' => $reason,
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
