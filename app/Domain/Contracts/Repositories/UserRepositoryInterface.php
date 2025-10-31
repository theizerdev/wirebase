<?php

declare(strict_types=1);

namespace App\Domain\Contracts\Repositories;

use App\Domain\Entities\User;
use App\Domain\ValueObjects\UserId;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

interface UserRepositoryInterface
{
    public function findById(UserId $id): ?User;

    public function findByEmail(string $email): ?User;

    public function findByEmpresaId(int $empresaId): Collection;

    public function findBySucursalId(int $sucursalId): Collection;

    public function create(array $data): User;

    public function update(UserId $id, array $data): bool;

    public function delete(UserId $id): bool;

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator;

    public function search(string $query, int $perPage = 15): LengthAwarePaginator;

    public function attachRole(UserId $userId, int $roleId): void;

    public function detachRole(UserId $userId, int $roleId): void;

    public function syncRoles(UserId $userId, array $roleIds): void;

    public function attachPermission(UserId $userId, int $permissionId): void;

    public function detachPermission(UserId $userId, int $permissionId): void;

    public function syncPermissions(UserId $userId, array $permissionIds): void;
}
