<?php

declare(strict_types=1);

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Cache\RateLimiter;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as SymfonyResponse;

class RateLimitMiddleware
{
    private RateLimiter $limiter;
    private int $defaultLimit;
    private int $authenticatedLimit;
    private int $window;

    public function __construct(RateLimiter $limiter)
    {
        $this->limiter = $limiter;
        $this->defaultLimit = config('api.rate_limiting.default_limit', 60);
        $this->authenticatedLimit = config('api.rate_limiting.authenticated_limit', 120);
        $this->window = config('api.rate_limiting.window', 60);
    }

    /**
     * Handle an incoming request.
     *
     * @param Request $request
     * @param Closure $next
     * @param string $limit
     * @param string $window
     * @return mixed
     */
    public function handle(Request $request, Closure $next, string $limit = null, string $window = null): mixed
    {
        if (!config('api.rate_limiting.enabled', true)) {
            return $next($request);
        }

        $key = $this->resolveRequestSignature($request);
        $maxAttempts = $this->resolveMaxAttempts($request, $limit);
        $decayMinutes = $window ? (int) $window : $this->window / 60;

        if ($this->limiter->tooManyAttempts($key, $maxAttempts)) {
            return $this->buildException($key, $maxAttempts, $decayMinutes);
        }

        $this->limiter->hit($key, $decayMinutes * 60);

        $response = $next($request);

        return $this->addHeaders(
            $response,
            $maxAttempts,
            $this->limiter->retriesLeft($key, $maxAttempts),
            $this->limiter->availableIn($key)
        );
    }

    /**
     * Resolve the number of attempts if the user is authenticated or not.
     *
     * @param Request $request
     * @param string|null $limit
     * @return int
     */
    protected function resolveMaxAttempts(Request $request, ?string $limit): int
    {
        if ($limit !== null) {
            return (int) $limit;
        }

        return $request->user() ? $this->authenticatedLimit : $this->defaultLimit;
    }

    /**
     * Resolve request signature.
     *
     * @param Request $request
     * @return string
     */
    protected function resolveRequestSignature(Request $request): string
    {
        if ($user = $request->user()) {
            return 'user:' . $user->id;
        }

        if ($route = $request->route()) {
            return 'guest:' . $request->ip() . '|' . $route->getDomain() . '|' . $route->getUri();
        }

        throw new \RuntimeException('Unable to generate the request signature. Route unavailable.');
    }

    /**
     * Create a too many attempts exception.
     *
     * @param string $key
     * @param int $maxAttempts
     * @param int $decayMinutes
     * @return Response
     */
    protected function buildException(string $key, int $maxAttempts, int $decayMinutes): Response
    {
        $retryAfter = $this->limiter->availableIn($key);

        $headers = [
            'X-RateLimit-Limit' => $maxAttempts,
            'X-RateLimit-Remaining' => 0,
            'X-RateLimit-Reset' => time() + $retryAfter,
            'Retry-After' => $retryAfter,
            'Content-Type' => 'application/json',
        ];

        $response = [
            'success' => false,
            'message' => 'Too Many Attempts.',
            'error' => [
                'code' => 'RATE_LIMIT_EXCEEDED',
                'message' => 'Too many requests. Please try again later.',
                'retry_after' => $retryAfter,
                'retry_after_human' => $this->formatRetryAfter($retryAfter),
            ],
        ];

        return response()->json($response, SymfonyResponse::HTTP_TOO_MANY_REQUESTS, $headers);
    }

    /**
     * Add the limit header information to the given response.
     *
     * @param mixed $response
     * @param int $maxAttempts
     * @param int $remainingAttempts
     * @param int|null $retryAfter
     * @return mixed
     */
    protected function addHeaders($response, int $maxAttempts, int $remainingAttempts, ?int $retryAfter = null): mixed
    {
        if ($response instanceof Response) {
            $response->headers->set('X-RateLimit-Limit', $maxAttempts);
            $response->headers->set('X-RateLimit-Remaining', $remainingAttempts);

            if ($retryAfter !== null) {
                $response->headers->set('X-RateLimit-Reset', time() + $retryAfter);
            }
        }

        return $response;
    }

    /**
     * Format retry after time in human readable format.
     *
     * @param int $seconds
     * @return string
     */
    protected function formatRetryAfter(int $seconds): string
    {
        if ($seconds < 60) {
            return $seconds . ' seconds';
        }

        $minutes = ceil($seconds / 60);

        if ($minutes < 60) {
            return $minutes . ' minutes';
        }

        $hours = ceil($minutes / 60);

        return $hours . ' hours';
    }

    /**
     * Clean up rate limiter for the given request.
     *
     * @param Request $request
     * @return void
     */
    public function terminate(Request $request): void
    {
        if (!config('api.rate_limiting.enabled', true)) {
            return;
        }

        $key = $this->resolveRequestSignature($request);

        // Optional: Clean up old entries periodically
        if (rand(1, 100) === 1) {
            $this->limiter->clean($key);
        }
    }
}
