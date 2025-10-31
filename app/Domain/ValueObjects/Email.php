<?php

declare(strict_types=1);

namespace App\Domain\ValueObjects;

use InvalidArgumentException;

final class Email
{
    private string $value;

    public function __construct(string $value)
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            throw new InvalidArgumentException('Invalid email format');
        }

        $this->value = strtolower(trim($value));
    }

    public static function from(string $value): self
    {
        return new self($value);
    }

    public function getValue(): string
    {
        return $this->value;
    }

    public function getDomain(): string
    {
        return substr(strrchr($this->value, "@"), 1);
    }

    public function getLocalPart(): string
    {
        return substr($this->value, 0, strpos($this->value, '@'));
    }

    public function equals(Email $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
