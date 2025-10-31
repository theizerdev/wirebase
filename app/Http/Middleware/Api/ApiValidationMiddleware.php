<?php

declare(strict_types=1);

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;

class ApiValidationMiddleware
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
        if (!config('api.validation.enabled', true)) {
            return $next($request);
        }

        try {
            // Validate request format
            $this->validateRequestFormat($request);

            // Validate content type for POST/PUT/PATCH requests
            $this->validateContentType($request);

            // Validate request size
            $this->validateRequestSize($request);

            // Validate API version if specified
            $this->validateApiVersion($request);

            // Validate rate limiting headers
            $this->validateRateLimitHeaders($request);

        } catch (ValidationException $e) {
            return $this->buildErrorResponse($e->errors(), 'Request validation failed', 400);
        }

        return $next($request);
    }

    /**
     * Validate request format.
     *
     * @param Request $request
     * @return void
     * @throws ValidationException
     */
    protected function validateRequestFormat(Request $request): void
    {
        $validator = Validator::make([], []);

        // Validate URL encoding
        if ($this->hasInvalidUrlEncoding($request->fullUrl())) {
            $validator->errors()->add('url', 'URL contains invalid encoding');
        }

        // Validate header format
        foreach ($request->headers->all() as $header => $values) {
            if (!$this->isValidHeaderName($header)) {
                $validator->errors()->add('headers', "Invalid header name: {$header}");
            }

            foreach ($values as $value) {
                if (!$this->isValidHeaderValue($value)) {
                    $validator->errors()->add('headers', "Invalid header value for {$header}");
                }
            }
        }

        if ($validator->errors()->isNotEmpty()) {
            throw new ValidationException($validator);
        }
    }

    /**
     * Validate content type for POST/PUT/PATCH requests.
     *
     * @param Request $request
     * @return void
     * @throws ValidationException
     */
    protected function validateContentType(Request $request): void
    {
        if (!in_array($request->getMethod(), ['POST', 'PUT', 'PATCH'])) {
            return;
        }

        $contentType = $request->header('Content-Type');
        $allowedContentTypes = config('api.validation.allowed_content_types', [
            'application/json',
            'application/x-www-form-urlencoded',
            'multipart/form-data',
            'application/xml',
            'text/xml',
        ]);

        if ($contentType && !$this->isAllowedContentType($contentType, $allowedContentTypes)) {
            $validator = Validator::make([], []);
            $validator->errors()->add(
                'content_type',
                "Content-Type '{$contentType}' not allowed. Allowed types: " . implode(', ', $allowedContentTypes)
            );
            throw new ValidationException($validator);
        }

        // Validate JSON format
        if ($this->isJsonContentType($contentType) && !$this->isValidJson($request->getContent())) {
            $validator = Validator::make([], []);
            $validator->errors()->add('body', 'Invalid JSON format');
            throw new ValidationException($validator);
        }
    }

    /**
     * Validate request size.
     *
     * @param Request $request
     * @return void
     * @throws ValidationException
     */
    protected function validateRequestSize(Request $request): void
    {
        $maxSize = config('api.validation.max_request_size', 10 * 1024 * 1024); // 10MB default
        $contentLength = $request->header('Content-Length', 0);

        if ($contentLength > $maxSize) {
            $validator = Validator::make([], []);
            $validator->errors()->add(
                'request_size',
                "Request size ({$contentLength} bytes) exceeds maximum allowed size ({$maxSize} bytes)"
            );
            throw new ValidationException($validator);
        }

        // Validate against post_max_size and upload_max_filesize
        $postMaxSize = $this->parsePhpIniSize(ini_get('post_max_size'));
        $uploadMaxFilesize = $this->parsePhpIniSize(ini_get('upload_max_filesize'));

        if ($contentLength > $postMaxSize) {
            $validator = Validator::make([], []);
            $validator->errors()->add('request_size', 'Request size exceeds PHP post_max_size limit');
            throw new ValidationException($validator);
        }
    }

    /**
     * Validate API version.
     *
     * @param Request $request
     * @return void
     * @throws ValidationException
     */
    protected function validateApiVersion(Request $request): void
    {
        $apiVersion = $request->header('X-API-Version');
        $supportedVersions = config('api.versioning.supported_versions', ['v1']);

        if ($apiVersion && !in_array($apiVersion, $supportedVersions)) {
            $validator = Validator::make([], []);
            $validator->errors()->add(
                'api_version',
                "API version '{$apiVersion}' not supported. Supported versions: " . implode(', ', $supportedVersions)
            );
            throw new ValidationException($validator);
        }
    }

    /**
     * Validate rate limiting headers.
     *
     * @param Request $request
     * @return void
     * @throws ValidationException
     */
    protected function validateRateLimitHeaders(Request $request): void
    {
        // Validate custom rate limit headers if present
        $customLimit = $request->header('X-RateLimit-Limit');
        $customWindow = $request->header('X-RateLimit-Window');

        if ($customLimit !== null && (!is_numeric($customLimit) || $customLimit < 1)) {
            $validator = Validator::make([], []);
            $validator->errors()->add('rate_limit', 'Invalid custom rate limit');
            throw new ValidationException($validator);
        }

        if ($customWindow !== null && (!is_numeric($customWindow) || $customWindow < 1)) {
            $validator = Validator::make([], []);
            $validator->errors()->add('rate_limit', 'Invalid custom rate limit window');
            throw new ValidationException($validator);
        }
    }

    /**
     * Check if URL has invalid encoding.
     *
     * @param string $url
     * @return bool
     */
    protected function hasInvalidUrlEncoding(string $url): bool
    {
        return $url !== urldecode($url) && urldecode($url) === '';
    }

    /**
     * Check if header name is valid.
     *
     * @param string $header
     * @return bool
     */
    protected function isValidHeaderName(string $header): bool
    {
        return preg_match('/^[a-zA-Z0-9\-_]+$/', $header) === 1;
    }

    /**
     * Check if header value is valid.
     *
     * @param string $value
     * @return bool
     */
    protected function isValidHeaderValue(string $value): bool
    {
        return !preg_match('/[\x00-\x1F\x7F-\xFF]/', $value);
    }

    /**
     * Check if content type is allowed.
     *
     * @param string $contentType
     * @param array $allowedTypes
     * @return bool
     */
    protected function isAllowedContentType(string $contentType, array $allowedTypes): bool
    {
        foreach ($allowedTypes as $allowedType) {
            if (stripos($contentType, $allowedType) !== false) {
                return true;
            }
        }
        return false;
    }

    /**
     * Check if content type is JSON.
     *
     * @param string $contentType
     * @return bool
     */
    protected function isJsonContentType(string $contentType): bool
    {
        return stripos($contentType, 'application/json') !== false;
    }

    /**
     * Validate JSON content.
     *
     * @param string $content
     * @return bool
     */
    protected function isValidJson(string $content): bool
    {
        if (empty($content)) {
            return true;
        }

        json_decode($content);
        return json_last_error() === JSON_ERROR_NONE;
    }

    /**
     * Parse PHP ini size value.
     *
     * @param string $size
     * @return int
     */
    protected function parsePhpIniSize(string $size): int
    {
        $unit = strtolower(substr($size, -1));
        $value = (int) $size;

        switch ($unit) {
            case 'g':
                $value *= 1024;
                // no break
            case 'm':
                $value *= 1024;
                // no break
            case 'k':
                $value *= 1024;
        }

        return $value;
    }

    /**
     * Build error response.
     *
     * @param array $errors
     * @param string $message
     * @param int $statusCode
     * @return Response
     */
    protected function buildErrorResponse(array $errors, string $message, int $statusCode = 400): Response
    {
        Log::channel('api_validation')->warning('API validation failed', [
            'errors' => $errors,
            'message' => $message,
            'url' => request()->fullUrl(),
            'method' => request()->getMethod(),
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);

        return response()->json([
            'success' => false,
            'message' => $message,
            'errors' => $errors,
            'timestamp' => now()->toIso8601String(),
            'request_id' => request()->header('X-Request-ID'),
        ], $statusCode);
    }
}
