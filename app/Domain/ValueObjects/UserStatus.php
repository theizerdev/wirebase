<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

final class UserStatus
{
    private const ACTIVE = 'active';
    private const INACTIVE = 'inactive';
    private const PENDING = 'pending';
    private const SUSPENDED = 'suspended';

    private string $value;

    private static array $validStatuses = [
        self::ACTIVE,
        self::INACTIVE,
        self::PENDING,
        self::SUSPENDED,
    ];

    public function __construct(string $value)
    {
        if (!in_array($value, self::$validStatuses, true)) {
            throw new InvalidArgumentException(
                sprintf('Invalid user status: %s. Valid statuses are: %s',
                    $value,
                    implode(', ', self::$validStatuses)
                )
            );
        }

        $this->value = $value;
    }

    public static function from(string $value): self
    {
        return new self($value);
    }

    public static function active(): self
    {
        return new self(self::ACTIVE);
    }

    public static function inactive(): self
    {
        return new self(self::INACTIVE);
    }

    public static function pending(): self
    {
        return new self(self::PENDING);
    }

    public static function suspended(): self
    {
        return new self(self::SUSPENDED);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function isActive(): bool
    {
        return $this->value === self::ACTIVE;
    }

    public function isInactive(): bool
    {
        return $this->value === self::INACTIVE;
    }

    public function isPending(): bool
    {
        return $this->value === self::PENDING;
    }

    public function isSuspended(): bool
    {
        return $this->value === self::SUSPENDED;
    }

    public function canLogin(): bool
    {
        return $this->isActive();
    }

    public function equals(UserStatus $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
