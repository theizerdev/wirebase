<?php

declare(strict_types=1);

namespace App\Application\DTOs;

use Illuminate\Support\Collection;

class UserCreateDTO
{
    public string $name;
    public string $email;
    public string $password;
    public ?string $status;
    public ?int $empresaId;
    public ?int $sucursalId;
    public array $roleIds;
    public array $permissionIds;

    public function __construct(
        string $name,
        string $email,
        string $password,
        ?string $status = 'active',
        ?int $empresaId = null,
        ?int $sucursalId = null,
        array $roleIds = [],
        array $permissionIds = []
    ) {
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->status = $status;
        $this->empresaId = $empresaId;
        $this->sucursalId = $sucursalId;
        $this->roleIds = $roleIds;
        $this->permissionIds = $permissionIds;
    }

    public static function fromArray(array $data): self
    {
        return new self(
            $data['name'],
            $data['email'],
            $data['password'],
            $data['status'] ?? 'active',
            $data['empresa_id'] ?? null,
            $data['sucursal_id'] ?? null,
            $data['role_ids'] ?? [],
            $data['permission_ids'] ?? []
        );
    }

    public function toArray(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'status' => $this->status,
            'empresa_id' => $this->empresaId,
            'sucursal_id' => $this->sucursalId,
            'role_ids' => $this->roleIds,
            'permission_ids' => $this->permissionIds,
        ];
    }

    public function getMultitenancyContext(): array
    {
        return [
            'empresa_id' => $this->empresaId,
            'sucursal_id' => $this->sucursalId,
        ];
    }

    public function hasMultitenancyData(): bool
    {
        return $this->empresaId !== null || $this->sucursalId !== null;
    }

    public function getRoleNames(): array
    {
        return $this->roleIds;
    }

    public function getPermissionNames(): array
    {
        return $this->permissionIds;
    }

    public function shouldSendWelcomeEmail(): bool
    {
        return $this->status === 'active';
    }

    public function needsEmailVerification(): bool
    {
        return $this->status === 'pending';
    }

    public function getPasswordStrength(): string
    {
        $password = $this->password;
        $strength = 0;

        if (strlen($password) >= 8) $strength += 1;
        if (strlen($password) >= 12) $strength += 1;
        if (preg_match('/[a-z]/', $password)) $strength += 1;
        if (preg_match('/[A-Z]/', $password)) $strength += 1;
        if (preg_match('/[0-9]/', $password)) $strength += 1;
        if (preg_match('/[^a-zA-Z0-9]/', $password)) $strength += 1;

        if ($strength <= 2) return 'weak';
        if ($strength <= 4) return 'medium';
        return 'strong';
    }

    public function generateTemporaryPassword(): string
    {
        return bin2hex(random_bytes(8));
    }

    public function getDefaultPermissions(): array
    {
        return [
            'view dashboard',
            'view profile',
            'edit profile',
        ];
    }

    public function shouldCreateAuditLog(): bool
    {
        return true;
    }

    public function getAuditContext(): array
    {
        return [
            'name' => $this->name,
            'email' => $this->email,
            'status' => $this->status,
            'empresa_id' => $this->empresaId,
            'sucursal_id' => $this->sucursalId,
            'role_count' => count($this->roleIds),
            'permission_count' => count($this->permissionIds),
        ];
    }
}
