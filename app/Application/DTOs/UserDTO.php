<?php

declare(strict_types=1);

namespace App\Application\DTOs;

use App\Domain\Entities\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class UserDTO
{
    public int $id;
    public string $name;
    public string $email;
    public ?string $emailVerifiedAt;
    public string $status;
    public ?int $empresaId;
    public ?int $sucursalId;
    public ?Carbon $createdAt;
    public ?Carbon $updatedAt;
    public Collection $roles;
    public Collection $permissions;

    public function __construct(
        int $id,
        string $name,
        string $email,
        string $status,
        ?int $empresaId = null,
        ?int $sucursalId = null,
        ?string $emailVerifiedAt = null,
        ?Carbon $createdAt = null,
        ?Carbon $updatedAt = null,
        Collection $roles = null,
        Collection $permissions = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->status = $status;
        $this->empresaId = $empresaId;
        $this->sucursalId = $sucursalId;
        $this->emailVerifiedAt = $emailVerifiedAt;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->roles = $roles ?? collect();
        $this->permissions = $permissions ?? collect();
    }

    public static function fromEntity(User $user): self
    {
        return new self(
            $user->getId()->getValue(),
            $user->getName(),
            $user->getEmail()->getValue(),
            $user->getStatus()->getValue(),
            $user->getEmpresaId(),
            $user->getSucursalId(),
            $user->getEmailVerifiedAt(),
            $user->getCreatedAt(),
            $user->getUpdatedAt(),
            $user->getRoles(),
            $user->getPermissions()
        );
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'email_verified_at' => $this->emailVerifiedAt,
            'status' => $this->status,
            'empresa_id' => $this->empresaId,
            'sucursal_id' => $this->sucursalId,
            'created_at' => $this->createdAt?->toIso8601String(),
            'updated_at' => $this->updatedAt?->toIso8601String(),
            'roles' => $this->roles->toArray(),
            'permissions' => $this->permissions->toArray(),
        ];
    }

    public function getRoleNames(): array
    {
        return $this->roles->pluck('name')->toArray();
    }

    public function getPermissionNames(): array
    {
        return $this->permissions->pluck('name')->toArray();
    }

    public function hasRole(string $role): bool
    {
        return $this->roles->contains('name', $role);
    }

    public function hasPermission(string $permission): bool
    {
        return $this->permissions->contains('name', $permission);
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function isVerified(): bool
    {
        return $this->emailVerifiedAt !== null;
    }

    public function getFullName(): string
    {
        return $this->name;
    }

    public function getInitials(): string
    {
        $nameParts = explode(' ', $this->name);
        $initials = '';

        foreach ($nameParts as $part) {
            if (!empty($part)) {
                $initials .= strtoupper($part[0]);
            }
        }

        return substr($initials, 0, 2);
    }

    public function getAvatarUrl(): string
    {
        return sprintf('https://ui-avatars.com/api/?name=%s&background=4CAF50&color=fff',
            urlencode($this->getInitials())
        );
    }

    public function getLastActivityDate(): ?string
    {
        return $this->updatedAt?->diffForHumans();
    }

    public function getAccountAge(): string
    {
        if (!$this->createdAt) {
            return 'Unknown';
        }

        return $this->createdAt->diffForHumans();
    }

    public function getStatusBadge(): string
    {
        $badges = [
            'active' => '<span class="badge badge-success">Active</span>',
            'inactive' => '<span class="badge badge-secondary">Inactive</span>',
            'pending' => '<span class="badge badge-warning">Pending</span>',
            'suspended' => '<span class="badge badge-danger">Suspended</span>',
        ];

        return $badges[$this->status] ?? '<span class="badge badge-light">Unknown</span>';
    }

    public function canImpersonate(): bool
    {
        return $this->hasPermission('impersonate users');
    }

    public function canBeImpersonated(): bool
    {
        return !$this->hasRole('Super Administrador') && $this->isActive();
    }

    public function getMultitenancyContext(): array
    {
        return [
            'empresa_id' => $this->empresaId,
            'sucursal_id' => $this->sucursalId,
        ];
    }

    public function hasMultitenancyAccess(int $empresaId, ?int $sucursalId = null): bool
    {
        if ($this->hasRole('Super Administrador')) {
            return true;
        }

        if ($this->empresaId !== $empresaId) {
            return false;
        }

        if ($sucursalId !== null && $this->sucursalId !== null) {
            return $this->sucursalId === $sucursalId;
        }

        return true;
    }
}
