<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

final class UserId
{
    private int $value;

    public function __construct(int $value)
    {
        if ($value <= 0) {
            throw new InvalidArgumentException('User ID must be a positive integer');
        }

        $this->value = $value;
    }

    public static function from(int $value): self
    {
        return new self($value);
    }

    public function getValue(): int
    {
        return $this->value;
    }

    public function equals(UserId $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return (string) $this->value;
    }
}
