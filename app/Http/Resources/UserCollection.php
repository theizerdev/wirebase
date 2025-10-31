<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Facades\Cache;

class UserCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Cache the collection data for better performance
        $cacheKey = $this->getCacheKey($request);
        $ttl = config('api.cache.ttl', 300); // 5 minutes default

        return Cache::remember($cacheKey, $ttl, function () use ($request) {
            return $this->transformCollection($request);
        });
    }

    /**
     * Transform the collection data.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    protected function transformCollection(Request $request): array
    {
        return [
            'data' => $this->collection->map(function ($user) use ($request) {
                return (new UserResource($user))->toArray($request);
            }),
            'meta' => $this->getMetaData($request),
            'links' => $this->getPaginationLinks($request),
        ];
    }

    /**
     * Get meta data for the collection.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    protected function getMetaData(Request $request): array
    {
        $meta = [
            'total' => $this->total(),
            'per_page' => $this->perPage(),
            'current_page' => $this->currentPage(),
            'last_page' => $this->lastPage(),
            'from' => $this->firstItem(),
            'to' => $this->lastItem(),
            'path' => $this->path(),
            'first_page_url' => $this->url(1),
            'last_page_url' => $this->url($this->lastPage()),
            'next_page_url' => $this->nextPageUrl(),
            'prev_page_url' => $this->previousPageUrl(),
        ];

        // Add request information
        $meta['request'] = [
            'fields' => $request->get('fields'),
            'include' => $request->get('include'),
            'sort' => $request->get('sort'),
            'filter' => $request->get('filter'),
            'search' => $request->get('search'),
            'include_roles' => $request->has('include_roles'),
            'include_permissions' => $request->has('include_permissions'),
            'include_activity' => $request->has('include_activity'),
            'include_settings' => $request->has('include_settings'),
            'include_counts' => $request->has('include_counts'),
            'include_metadata' => $request->has('include_metadata'),
        ];

        // Add performance metrics
        $meta['performance'] = [
            'cached_at' => now()->toIso8601String(),
            'cache_ttl' => config('api.cache.ttl', 300),
            'request_id' => $request->header('X-Request-ID'),
        ];

        // Add additional counts if requested
        if ($request->has('include_counts')) {
            $meta['counts'] = $this->getCollectionCounts();
        }

        return $meta;
    }

    /**
     * Get pagination links.
     *
     * @param Request $request
     * @return array<string, string|null>
     */
    protected function getPaginationLinks(Request $request): array
    {
        return [
            'first' => $this->url(1),
            'last' => $this->url($this->lastPage()),
            'prev' => $this->previousPageUrl(),
            'next' => $this->nextPageUrl(),
            'self' => $this->url($this->currentPage()),
        ];
    }

    /**
     * Get counts for the collection.
     *
     * @return array<string, mixed>
     */
    protected function getCollectionCounts(): array
    {
        return [
            'active_users' => $this->collection->where('status', 'active')->count(),
            'inactive_users' => $this->collection->where('status', 'inactive')->count(),
            'verified_users' => $this->collection->whereNotNull('email_verified_at')->count(),
            'unverified_users' => $this->collection->whereNull('email_verified_at')->count(),
            'users_with_empresa' => $this->collection->whereNotNull('empresa_id')->count(),
            'users_with_sucursal' => $this->collection->whereNotNull('sucursal_id')->count(),
        ];
    }

    /**
     * Get cache key for the collection.
     *
     * @param Request $request
     * @return string
     */
    protected function getCacheKey(Request $request): string
    {
        $page = $this->currentPage();
        $perPage = $this->perPage();
        $requestFingerprint = md5(implode('|', [
            $request->get('fields', ''),
            $request->get('include', ''),
            $request->get('sort', ''),
            $request->get('filter', ''),
            $request->get('search', ''),
            $request->get('include_roles', ''),
            $request->get('include_permissions', ''),
            $request->get('include_activity', ''),
            $request->get('include_settings', ''),
            $request->get('include_counts', ''),
            $request->get('include_metadata', ''),
            $request->user()?->id ?? 'guest',
            $page,
            $perPage,
        ]));

        return "api.users.collection.page_{$page}.per_{$perPage}.{$requestFingerprint}";
    }

    /**
     * Get additional data that should be returned with the resource collection.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function with(Request $request): array
    {
        return [
            'jsonapi' => [
                'version' => '1.0',
                'meta' => [
                    'copyright' => config('app.name', 'Laravel Enterprise') . ' © ' . date('Y'),
                    'authors' => [
                        ['name' => config('app.name', 'Laravel Enterprise'), 'email' => 'support@example.com'],
                    ],
                    'generated_at' => now()->toIso8601String(),
                    'api_version' => config('api.version', 'v1'),
                ],
            ],
        ];
    }

    /**
     * Customize the response for a request.
     *
     * @param Request $request
     * @param \Illuminate\Http\JsonResponse $response
     * @return void
     */
    public function withResponse(Request $request, $response): void
    {
        // Add custom headers
        $response->header('X-Resource-Type', 'user_collection');
        $response->header('X-API-Version', config('api.version', 'v1'));
        $response->header('X-Total-Count', (string) $this->total());
        $response->header('X-Per-Page', (string) $this->perPage());
        $response->header('X-Current-Page', (string) $this->currentPage());
        $response->header('X-Last-Page', (string) $this->lastPage());

        // Add cache headers if collection is cached
        if (Cache::has($this->getCacheKey($request))) {
            $response->header('X-Cache', 'HIT');
            $response->header('X-Cache-TTL', (string) config('api.cache.ttl', 300));
        } else {
            $response->header('X-Cache', 'MISS');
        }

        // Add pagination headers
        $response->header('Link', $this->getLinkHeader($request));
    }

    /**
     * Get Link header for pagination.
     *
     * @param Request $request
     * @return string
     */
    protected function getLinkHeader(Request $request): string
    {
        $links = [];

        if ($this->previousPageUrl()) {
            $links[] = '<' . $this->previousPageUrl() . '>; rel="prev"';
        }

        if ($this->nextPageUrl()) {
            $links[] = '<' . $this->nextPageUrl() . '>; rel="next"';
        }

        $links[] = '<' . $this->url(1) . '>; rel="first"';
        $links[] = '<' . $this->url($this->lastPage()) . '>; rel="last"';

        return implode(', ', $links);
    }
}
