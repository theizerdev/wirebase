<?php

declare(strict_types=1);

namespace App\Application\Events;

use App\Application\DTOs\UserDTO;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserCreatedEvent
{
    use Dispatchable, SerializesModels;

    public UserDTO $user;
    public string $timestamp;
    public string $eventId;

    public function __construct(UserDTO $user)
    {
        $this->user = $user;
        $this->timestamp = now()->toIso8601String();
        $this->eventId = uniqid('user_created_', true);
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

    public function getEventData(): array
    {
        return [
            'event_id' => $this->eventId,
            'event_type' => 'user_created',
            'timestamp' => $this->timestamp,
            'user_id' => $this->user->id,
            'user_data' => $this->user->toArray(),
        ];
    }

    public function broadcastOn(): array
    {
        return [];
    }

    public function broadcastAs(): string
    {
        return 'user.created';
    }

    public function tags(): array
    {
        return [
            'user',
            'user:' . $this->user->id,
            'event:user_created',
        ];
    }

    public function shouldBeLogged(): bool
    {
        return true;
    }

    public function getLogLevel(): string
    {
        return 'info';
    }

    public function getLogMessage(): string
    {
        return sprintf('User created: %s (%s)', $this->user->name, $this->user->email);
    }

    public function getLogContext(): array
    {
        return [
            'event_id' => $this->eventId,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'user_email' => $this->user->email,
            'user_status' => $this->user->status,
            'multitenancy' => $this->user->getMultitenancyContext(),
        ];
    }
}
