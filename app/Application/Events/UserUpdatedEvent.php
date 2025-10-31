<?php

declare(strict_types=1);

namespace App\Application\Events;

use App\Application\DTOs\UserDTO;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserUpdatedEvent
{
    use Dispatchable, SerializesModels;

    public UserDTO $user;
    public string $timestamp;
    public string $eventId;
    public array $changes;

    public function __construct(UserDTO $user, array $changes = [])
    {
        $this->user = $user;
        $this->timestamp = now()->toIso8601String();
        $this->eventId = uniqid('user_updated_', true);
        $this->changes = $changes;
    }

    public function getUser(): UserDTO
    {
        return $this->user;
    }

    public function getEventId(): string
    {
        return $this->eventId;
    }

    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    public function getChanges(): array
    {
        return $this->changes;
    }

    public function hasChanges(): bool
    {
        return !empty($this->changes);
    }

    public function getEventData(): array
    {
        return [
            'event_id' => $this->eventId,
            'event_type' => 'user_updated',
            'timestamp' => $this->timestamp,
            'user_id' => $this->user->id,
            'user_data' => $this->user->toArray(),
            'changes' => $this->changes,
        ];
    }

    public function broadcastOn(): array
    {
        return [];
    }

    public function broadcastAs(): string
    {
        return 'user.updated';
    }

    public function tags(): array
    {
        return [
            'user',
            'user:' . $this->user->id,
            'event:user_updated',
        ];
    }

    public function shouldBeLogged(): bool
    {
        return $this->hasChanges();
    }

    public function getLogLevel(): string
    {
        return 'info';
    }

    public function getLogMessage(): string
    {
        $changeCount = count($this->changes);
        return sprintf('User updated: %s (%s) - %d change(s)',
            $this->user->name,
            $this->user->email,
            $changeCount
        );
    }

    public function getLogContext(): array
    {
        return [
            'event_id' => $this->eventId,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'changes' => $this->changes,
            'change_count' => count($this->changes),
            'multitenancy' => $this->user->getMultitenancyContext(),
        ];
    }

    public function getChangedFields(): array
    {
        return array_keys($this->changes);
    }

    public function hasFieldChanged(string $field): bool
    {
        return isset($this->changes[$field]);
    }

    public function getOldValue(string $field): mixed
    {
        return $this->changes[$field]['old'] ?? null;
    }

    public function getNewValue(string $field): mixed
    {
        return $this->changes[$field]['new'] ?? null;
    }

    public function isStatusChanged(): bool
    {
        return $this->hasFieldChanged('status');
    }

    public function isEmailChanged(): bool
    {
        return $this->hasFieldChanged('email');
    }

    public function isPasswordChanged(): bool
    {
        return $this->hasFieldChanged('password');
    }

    public function isMultitenancyChanged(): bool
    {
        return $this->hasFieldChanged('empresa_id') || $this->hasFieldChanged('sucursal_id');
    }

    public function shouldNotifyUser(): bool
    {
        return $this->isEmailChanged() || $this->isPasswordChanged() || $this->isStatusChanged();
    }

    public function getNotificationType(): string
    {
        if ($this->isPasswordChanged()) {
            return 'password_changed';
        }

        if ($this->isEmailChanged()) {
            return 'email_changed';
        }

        if ($this->isStatusChanged()) {
            return 'status_changed';
        }

        return 'profile_updated';
    }
}
