<?php

declare(strict_types=1);

namespace App\Application\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserDeletedEvent
{
    use Dispatchable, SerializesModels;

    public int $userId;
    public string $userName;
    public string $userEmail;
    public string $timestamp;
    public string $eventId;
    public ?int $deletedBy;
    public array $userData;

    public function __construct(
        int $userId,
        string $userName,
        string $userEmail,
        array $userData = [],
        ?int $deletedBy = null
    ) {
        $this->userId = $userId;
        $this->userName = $userName;
        $this->userEmail = $userEmail;
        $this->userData = $userData;
        $this->deletedBy = $deletedBy;
        $this->timestamp = now()->toIso8601String();
        $this->eventId = uniqid('user_deleted_', true);
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getUserName(): string
    {
        return $this->userName;
    }

    public function getUserEmail(): string
    {
        return $this->userEmail;
    }

    public function getEventId(): string
    {
        return $this->eventId;
    }

    public function getTimestamp(): string
    {
        return $this->timestamp;
    }

    public function getDeletedBy(): ?int
    {
        return $this->deletedBy;
    }

    public function getUserData(): array
    {
        return $this->userData;
    }

    public function getEventData(): array
    {
        return [
            'event_id' => $this->eventId,
            'event_type' => 'user_deleted',
            'timestamp' => $this->timestamp,
            'user_id' => $this->userId,
            'user_name' => $this->userName,
            'user_email' => $this->userEmail,
            'deleted_by' => $this->deletedBy,
            'user_data' => $this->userData,
        ];
    }

    public function broadcastOn(): array
    {
        return [];
    }

    public function broadcastAs(): string
    {
        return 'user.deleted';
    }

    public function tags(): array
    {
        return [
            'user',
            'user:' . $this->userId,
            'event:user_deleted',
        ];
    }

    public function shouldBeLogged(): bool
    {
        return true;
    }

    public function getLogLevel(): string
    {
        return 'warning';
    }

    public function getLogMessage(): string
    {
        return sprintf('User deleted: %s (%s)', $this->userName, $this->userEmail);
    }

    public function getLogContext(): array
    {
        return [
            'event_id' => $this->eventId,
            'user_id' => $this->userId,
            'user_name' => $this->userName,
            'user_email' => $this->userEmail,
            'deleted_by' => $this->deletedBy,
            'user_data' => $this->userData,
        ];
    }

    public function shouldNotifyAdmin(): bool
    {
        return true;
    }

    public function getNotificationRecipients(): array
    {
        // This would typically come from configuration or database
        return ['admin@example.com'];
    }

    public function getNotificationSubject(): string
    {
        return sprintf('User Deleted: %s', $this->userName);
    }

    public function getNotificationMessage(): string
    {
        $deletedBy = $this->deletedBy ? 'User ID: ' . $this->deletedBy : 'System';
        return sprintf(
            "User '%s' (%s) has been deleted by %s on %s",
            $this->userName,
            $this->userEmail,
            $deletedBy,
            $this->timestamp
        );
    }

    public function shouldTriggerDataCleanup(): bool
    {
        return true;
    }

    public function getRelatedDataToCleanup(): array
    {
        return [
            'user_sessions' => ['table' => 'sessions', 'column' => 'user_id'],
            'user_activities' => ['table' => 'activity_log', 'column' => 'causer_id'],
            'user_notifications' => ['table' => 'notifications', 'column' => 'notifiable_id'],
        ];
    }
}
