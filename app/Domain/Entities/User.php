<?php

declare(strict_types=1);

namespace App\Domain\Entities;

use App\Domain\ValueObjects\Email;
use App\Domain\ValueObjects\UserId;
use App\Domain\ValueObjects\UserStatus;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class User
{
    private UserId $id;
    private string $name;
    private Email $email;
    private ?string $emailVerifiedAt;
    private string $password;
    private UserStatus $status;
    private ?int $empresaId;
    private ?int $sucursalId;
    private ?Carbon $createdAt;
    private ?Carbon $updatedAt;
    private Collection $roles;
    private Collection $permissions;

    public function __construct(
        UserId $id,
        string $name,
        Email $email,
        string $password,
        UserStatus $status,
        ?int $empresaId = null,
        ?int $sucursalId = null,
        ?string $emailVerifiedAt = null,
        ?Carbon $createdAt = null,
        ?Carbon $updatedAt = null
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->status = $status;
        $this->empresaId = $empresaId;
        $this->sucursalId = $sucursalId;
        $this->emailVerifiedAt = $emailVerifiedAt;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->roles = collect();
        $this->permissions = collect();
    }

    public function getId(): UserId
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): Email
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getStatus(): UserStatus
    {
        return $this->status;
    }

    public function getEmpresaId(): ?int
    {
        return $this->empresaId;
    }

    public function getSucursalId(): ?int
    {
        return $this->sucursalId;
    }

    public function getEmailVerifiedAt(): ?string
    {
        return $this->emailVerifiedAt;
    }

    public function getCreatedAt(): ?Carbon
    {
        return $this->createdAt;
    }

    public function getUpdatedAt(): ?Carbon
    {
        return $this->updatedAt;
    }

    public function getRoles(): Collection
    {
        return $this->roles;
    }

    public function getPermissions(): Collection
    {
        return $this->permissions;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setEmail(Email $email): void
    {
        $this->email = $email;
    }

    public function setPassword(string $password): void
    {
        $this->password = $password;
    }

    public function setStatus(UserStatus $status): void
    {
        $this->status = $status;
    }

    public function setEmpresaId(?int $empresaId): void
    {
        $this->empresaId = $empresaId;
    }

    public function setSucursalId(?int $sucursalId): void
    {
        $this->sucursalId = $sucursalId;
    }

    public function addRole(Role $role): void
    {
        if (!$this->roles->contains($role)) {
            $this->roles->push($role);
        }
    }

    public function removeRole(Role $role): void
    {
        $this->roles = $this->roles->reject(fn($r) => $r->getId() === $role->getId());
    }

    public function addPermission(Permission $permission): void
    {
        if (!$this->permissions->contains($permission)) {
            $this->permissions->push($permission);
        }
    }

    public function removePermission(Permission $permission): void
    {
        $this->permissions = $this->permissions->reject(fn($p) => $p->getId() === $permission->getId());
    }

    public function hasRole(string $roleName): bool
    {
        return $this->roles->contains(fn($role) => $role->getName() === $roleName);
    }

    public function hasPermission(string $permissionName): bool
    {
        return $this->permissions->contains(fn($permission) => $permission->getName() === $permissionName);
    }

    public function isActive(): bool
    {
        return $this->status->isActive();
    }

    public function isVerified(): bool
    {
        return $this->emailVerifiedAt !== null;
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id->getValue(),
            'name' => $this->name,
            'email' => $this->email->getValue(),
            'email_verified_at' => $this->emailVerifiedAt,
            'status' => $this->status->getValue(),
            'empresa_id' => $this->empresaId,
            'sucursal_id' => $this->sucursalId,
            'created_at' => $this->createdAt?->toIso8601String(),
            'updated_at' => $this->updatedAt?->toIso8601String(),
            'roles' => $this->roles->map(fn($role) => $role->toArray()),
            'permissions' => $this->permissions->map(fn($permission) => $permission->toArray()),
        ];
    }

    public static function fromArray(array $data): self
    {
        $user = new self(
            UserId::from($data['id']),
            $data['name'],
            Email::from($data['email']),
            $data['password'],
            UserStatus::from($data['status'] ?? 'active'),
            $data['empresa_id'] ?? null,
            $data['sucursal_id'] ?? null,
            $data['email_verified_at'] ?? null,
            isset($data['created_at']) ? Carbon::parse($data['created_at']) : null,
            isset($data['updated_at']) ? Carbon::parse($data['updated_at']) : null
        );

        if (isset($data['roles'])) {
            foreach ($data['roles'] as $roleData) {
                $user->addRole(Role::fromArray($roleData));
            }
        }

        if (isset($data['permissions'])) {
            foreach ($data['permissions'] as $permissionData) {
                $user->addPermission(Permission::fromArray($permissionData));
            }
        }

        return $user;
    }
}
