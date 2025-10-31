<?php

declare(strict_types=1);

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Cache;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Cache the resource data for better performance
        $cacheKey = $this->getCacheKey($request);
        $ttl = config('api.cache.ttl', 300); // 5 minutes default

        return Cache::remember($cacheKey, $ttl, function () use ($request) {
            return $this->transformData($request);
        });
    }

    /**
     * Transform the user data.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    protected function transformData(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'email_verified_at' => $this->when($this->email_verified_at, fn() => $this->email_verified_at->toIso8601String()),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
            'last_login_at' => $this->when($this->last_login_at, fn() => $this->last_login_at->toIso8601String()),
            'profile' => $this->when($this->relationLoaded('profile'), fn() => [
                'phone' => $this->profile?->phone,
                'address' => $this->profile?->address,
                'city' => $this->profile?->city,
                'country' => $this->profile?->country,
                'timezone' => $this->profile?->timezone,
                'locale' => $this->profile?->locale,
            ]),
            'multitenancy' => $this->when($this->relationLoaded('empresa') || $this->relationLoaded('sucursal'), fn() => [
                'empresa' => $this->when($this->relationLoaded('empresa') && $this->empresa, fn() => [
                    'id' => $this->empresa->id,
                    'name' => $this->empresa->name,
                    'code' => $this->empresa->code,
                ]),
                'sucursal' => $this->when($this->relationLoaded('sucursal') && $this->sucursal, fn() => [
                    'id' => $this->sucursal->id,
                    'name' => $this->sucursal->name,
                    'code' => $this->sucursal->code,
                ]),
            ]),
            'roles' => $this->when($this->relationLoaded('roles') && $request->has('include_roles'), fn() =>
                $this->roles->map(fn($role) => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'display_name' => $role->display_name,
                    'description' => $role->description,
                    'permissions_count' => $role->permissions_count ?? $role->permissions()->count(),
                ])->values()
            ),
            'permissions' => $this->when($this->relationLoaded('permissions') && $request->has('include_permissions'), fn() =>
                $this->permissions->map(fn($permission) => [
                    'id' => $permission->id,
                    'name' => $permission->name,
                    'display_name' => $permission->display_name,
                    'description' => $permission->description,
                ])->values()
            ),
            'activity' => $this->when($request->has('include_activity'), fn() => [
                'last_active_at' => $this->last_active_at?->toIso8601String(),
                'login_count' => $this->login_count ?? 0,
                'failed_login_count' => $this->failed_login_count ?? 0,
            ]),
            'settings' => $this->when($request->has('include_settings'), fn() => [
                'notifications_enabled' => $this->notifications_enabled ?? true,
                'two_factor_enabled' => $this->two_factor_enabled ?? false,
                'theme' => $this->theme ?? 'light',
                'language' => $this->language ?? 'es',
            ]),
        ];

        // Include additional data based on request parameters
        if ($request->has('include_counts')) {
            $data = array_merge($data, $this->getCounts());
        }

        if ($request->has('include_metadata')) {
            $data['_metadata'] = $this->getMetadata($request);
        }

        return $data;
    }

    /**
     * Get counts for related resources.
     *
     * @return array<string, mixed>
     */
    protected function getCounts(): array
    {
        return [
            'roles_count' => $this->when($this->relationLoaded('roles'), fn() => $this->roles->count()),
            'permissions_count' => $this->when($this->relationLoaded('permissions'), fn() => $this->permissions->count()),
            'activity_logs_count' => $this->login_count ?? 0,
        ];
    }

    /**
     * Get metadata for the resource.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    protected function getMetadata(Request $request): array
    {
        return [
            'resource_type' => 'user',
            'api_version' => config('api.version', 'v1'),
            'cached_at' => now()->toIso8601String(),
            'cache_ttl' => config('api.cache.ttl', 300),
            'request_id' => $request->header('X-Request-ID'),
            'fields' => $request->get('fields'),
            'includes' => $request->get('include'),
        ];
    }

    /**
     * Get cache key for the resource.
     *
     * @param Request $request
     * @return string
     */
    protected function getCacheKey(Request $request): string
    {
        $userId = $this->id;
        $requestFingerprint = md5(implode('|', [
            $request->get('fields', ''),
            $request->get('include', ''),
            $request->get('include_roles', ''),
            $request->get('include_permissions', ''),
            $request->get('include_activity', ''),
            $request->get('include_settings', ''),
            $request->get('include_counts', ''),
            $request->get('include_metadata', ''),
            $request->user()?->id ?? 'guest',
            $this->updated_at->timestamp,
        ]));

        return "api.user.{$userId}.{$requestFingerprint}";
    }

    /**
     * Get additional data that should be returned with the resource array.
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
                ],
            ],
        ];
    }

    /**
     * Customize the response for a request.
     *
     * @param Request $request
     * @param Response $response
     * @return void
     */
    public function withResponse(Request $request, Response $response): void
    {
        // Add custom headers
        $response->header('X-Resource-Type', 'user');
        $response->header('X-API-Version', config('api.version', 'v1'));

        // Add cache headers if resource is cached
        if (Cache::has($this->getCacheKey($request))) {
            $response->header('X-Cache', 'HIT');
            $response->header('X-Cache-TTL', (string) config('api.cache.ttl', 300));
        } else {
            $response->header('X-Cache', 'MISS');
        }
    }
}
