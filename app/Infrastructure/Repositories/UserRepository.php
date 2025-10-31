<?php

declare(strict_types=1);

namespace App\Infrastructure\Repositories;

use App\Domain\Contracts\Repositories\UserRepositoryInterface;
use App\Domain\Entities\User;
use App\Domain\ValueObjects\UserId;
use App\Models\User as EloquentUser;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class UserRepository implements UserRepositoryInterface
{
    private const CACHE_TTL = 3600; // 1 hour
    private const CACHE_PREFIX = 'user_';

    public function findById(UserId $id): ?User
    {
        $cacheKey = self::CACHE_PREFIX . 'id_' . $id->getValue();

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($id) {
            $eloquentUser = EloquentUser::with(['roles', 'permissions'])->find($id->getValue());

            return $eloquentUser ? $this->mapToDomainEntity($eloquentUser) : null;
        });
    }

    public function findByEmail(string $email): ?User
    {
        $cacheKey = self::CACHE_PREFIX . 'email_' . md5($email);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($email) {
            $eloquentUser = EloquentUser::with(['roles', 'permissions'])
                ->where('email', $email)
                ->first();

            return $eloquentUser ? $this->mapToDomainEntity($eloquentUser) : null;
        });
    }

    public function findByEmpresaId(int $empresaId): Collection
    {
        $cacheKey = self::CACHE_PREFIX . 'empresa_' . $empresaId;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($empresaId) {
            return EloquentUser::with(['roles', 'permissions'])
                ->where('empresa_id', $empresaId)
                ->get()
                ->map(fn($user) => $this->mapToDomainEntity($user));
        });
    }

    public function findBySucursalId(int $sucursalId): Collection
    {
        $cacheKey = self::CACHE_PREFIX . 'sucursal_' . $sucursalId;

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($sucursalId) {
            return EloquentUser::with(['roles', 'permissions'])
                ->where('sucursal_id', $sucursalId)
                ->get()
                ->map(fn($user) => $this->mapToDomainEntity($user));
        });
    }

    public function create(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $eloquentUser = EloquentUser::create($data);
            $eloquentUser->load(['roles', 'permissions']);

            $user = $this->mapToDomainEntity($eloquentUser);

            $this->clearUserCache($user->getId()->getValue());

            return $user;
        });
    }

    public function update(UserId $id, array $data): bool
    {
        return DB::transaction(function () use ($id, $data) {
            $result = EloquentUser::where('id', $id->getValue())->update($data);

            if ($result) {
                $this->clearUserCache($id->getValue());
            }

            return (bool) $result;
        });
    }

    public function delete(UserId $id): bool
    {
        return DB::transaction(function () use ($id) {
            $result = EloquentUser::where('id', $id->getValue())->delete();

            if ($result) {
                $this->clearUserCache($id->getValue());
            }

            return (bool) $result;
        });
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = EloquentUser::with(['roles', 'permissions']);

        if (isset($filters['empresa_id'])) {
            $query->where('empresa_id', $filters['empresa_id']);
        }

        if (isset($filters['sucursal_id'])) {
            $query->where('sucursal_id', $filters['sucursal_id']);
        }

        if (isset($filters['status'])) {
            $query->where('status', $filters['status']);
        }

        if (isset($filters['role'])) {
            $query->whereHas('roles', function ($q) use ($filters) {
                $q->where('name', $filters['role']);
            });
        }

        return $query->paginate($perPage);
    }

    public function search(string $query, int $perPage = 15): LengthAwarePaginator
    {
        return EloquentUser::with(['roles', 'permissions'])
            ->where(function ($q) use ($query) {
                $q->where('name', 'like', "%{$query}%")
                    ->orWhere('email', 'like', "%{$query}%");
            })
            ->paginate($perPage);
    }

    public function attachRole(UserId $userId, int $roleId): void
    {
        DB::transaction(function () use ($userId, $roleId) {
            $user = EloquentUser::find($userId->getValue());
            if ($user) {
                $user->roles()->attach($roleId);
                $this->clearUserCache($userId->getValue());
            }
        });
    }

    public function detachRole(UserId $userId, int $roleId): void
    {
        DB::transaction(function () use ($userId, $roleId) {
            $user = EloquentUser::find($userId->getValue());
            if ($user) {
                $user->roles()->detach($roleId);
                $this->clearUserCache($userId->getValue());
            }
        });
    }

    public function syncRoles(UserId $userId, array $roleIds): void
    {
        DB::transaction(function () use ($userId, $roleIds) {
            $user = EloquentUser::find($userId->getValue());
            if ($user) {
                $user->roles()->sync($roleIds);
                $this->clearUserCache($userId->getValue());
            }
        });
    }

    public function attachPermission(UserId $userId, int $permissionId): void
    {
        DB::transaction(function () use ($userId, $permissionId) {
            $user = EloquentUser::find($userId->getValue());
            if ($user) {
                $user->permissions()->attach($permissionId);
                $this->clearUserCache($userId->getValue());
            }
        });
    }

    public function detachPermission(UserId $userId, int $permissionId): void
    {
        DB::transaction(function () use ($userId, $permissionId) {
            $user = EloquentUser::find($userId->getValue());
            if ($user) {
                $user->permissions()->detach($permissionId);
                $this->clearUserCache($userId->getValue());
            }
        });
    }

    public function syncPermissions(UserId $userId, array $permissionIds): void
    {
        DB::transaction(function () use ($userId, $permissionIds) {
            $user = EloquentUser::find($userId->getValue());
            if ($user) {
                $user->permissions()->sync($permissionIds);
                $this->clearUserCache($userId->getValue());
            }
        });
    }

    private function mapToDomainEntity(EloquentUser $eloquentUser): User
    {
        return User::fromArray([
            'id' => $eloquentUser->id,
            'name' => $eloquentUser->name,
            'email' => $eloquentUser->email,
            'password' => $eloquentUser->password,
            'status' => $eloquentUser->status ?? 'active',
            'empresa_id' => $eloquentUser->empresa_id,
            'sucursal_id' => $eloquentUser->sucursal_id,
            'email_verified_at' => $eloquentUser->email_verified_at,
            'created_at' => $eloquentUser->created_at,
            'updated_at' => $eloquentUser->updated_at,
            'roles' => $eloquentUser->roles->toArray(),
            'permissions' => $eloquentUser->permissions->toArray(),
        ]);
    }

    private function clearUserCache(int $userId): void
    {
        Cache::forget(self::CACHE_PREFIX . 'id_' . $userId);

        // Clear empresa and sucursal caches if needed
        $user = EloquentUser::find($userId);
        if ($user) {
            if ($user->empresa_id) {
                Cache::forget(self::CACHE_PREFIX . 'empresa_' . $user->empresa_id);
            }
            if ($user->sucursal_id) {
                Cache::forget(self::CACHE_PREFIX . 'sucursal_' . $user->sucursal_id);
            }
        }
    }
}
