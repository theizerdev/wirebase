<?php

namespace App\Services\Notification;

use App\Services\Logging\LoggingService;
use App\Services\Cache\CacheService;
use App\Models\User;
use App\Models\Notification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification as LaravelNotification;
use Illuminate\Support\Facades\Log;
use Exception;
use Ramsey\Uuid\Uuid;

class NotificationService
{
    protected LoggingService $loggingService;
    protected CacheService $cacheService;
    protected array $channels = ['mail', 'database', 'broadcast', 'sms', 'push'];
    protected array $queueConfig = [
        'default' => 'notifications',
        'priority' => [
            'critical' => 'notifications-critical',
            'high' => 'notifications-high',
            'normal' => 'notifications',
            'low' => 'notifications-low',
        ],
    ];

    public function __construct(LoggingService $loggingService, CacheService $cacheService)
    {
        $this->loggingService = $loggingService;
        $this->cacheService = $cacheService;
    }

    public function send($notifiable, $notification, array $options = []): bool
    {
        try {
            $notificationId = Uuid::uuid4()->toString();
            $channels = $options['channels'] ?? $this->determineChannels($notifiable, $notification);
            $priority = $options['priority'] ?? 'normal';
            $delay = $options['delay'] ?? 0;
            $metadata = $options['metadata'] ?? [];

            $notificationData = [
                'id' => $notificationId,
                'notifiable_type' => get_class($notifiable),
                'notifiable_id' => $notifiable->id ?? null,
                'notification_type' => get_class($notification),
                'channels' => $channels,
                'priority' => $priority,
                'metadata' => $metadata,
                'options' => $options,
            ];

            $this->loggingService->logNotification('notification_sending', $notificationData);

            if ($delay > 0) {
                LaravelNotification::send($notifiable, (new $notification($notifiable))->delay(now()->addSeconds($delay)));
            } else {
                LaravelNotification::send($notifiable, new $notification($notifiable));
            }

            $this->loggingService->logNotification('notification_sent', $notificationData);

            $this->cacheNotification($notifiable, $notificationData);

            return true;
        } catch (Exception $e) {
            $this->loggingService->logNotificationError($e, [
                'notifiable' => get_class($notifiable),
                'notification' => get_class($notification),
                'options' => $options,
            ]);
            return false;
        }
    }

    public function sendToMultiple(array $notifiables, $notification, array $options = []): array
    {
        $results = [];

        foreach ($notifiables as $notifiable) {
            try {
                $success = $this->send($notifiable, $notification, $options);
                $results[] = [
                    'notifiable_id' => $notifiable->id ?? null,
                    'notifiable_type' => get_class($notifiable),
                    'success' => $success,
                ];
            } catch (Exception $e) {
                $this->loggingService->logNotificationError($e, [
                    'notifiable' => get_class($notifiable),
                    'notification' => get_class($notification),
                    'options' => $options,
                ]);
                $results[] = [
                    'notifiable_id' => $notifiable->id ?? null,
                    'notifiable_type' => get_class($notifiable),
                    'success' => false,
                    'error' => $e->getMessage(),
                ];
            }
        }

        $this->loggingService->logBusinessEvent('bulk_notification_sent', [
            'total' => count($notifiables),
            'successful' => count(array_filter($results, fn($r) => $r['success'])),
            'failed' => count(array_filter($results, fn($r) => !$r['success'])),
        ]);

        return $results;
    }

    public function sendToRole(string $role, $notification, array $options = []): array
    {
        try {
            $users = User::role($role)->get();
            return $this->sendToMultiple($users->all(), $notification, $options);
        } catch (Exception $e) {
            $this->loggingService->logNotificationError($e, [
                'role' => $role,
                'notification' => get_class($notification),
                'options' => $options,
            ]);
            return [];
        }
    }

    public function sendToPermission(string $permission, $notification, array $options = []): array
    {
        try {
            $users = User::permission($permission)->get();
            return $this->sendToMultiple($users->all(), $notification, $options);
        } catch (Exception $e) {
            $this->loggingService->logNotificationError($e, [
                'permission' => $permission,
                'notification' => get_class($notification),
                'options' => $options,
            ]);
            return [];
        }
    }

    public function sendToMultitenancy($empresaId = null, $sucursalId = null, $notification, array $options = []): array
    {
        try {
            $query = User::query();

            if ($empresaId !== null) {
                $query->where('empresa_id', $empresaId);
            }

            if ($sucursalId !== null) {
                $query->where('sucursal_id', $sucursalId);
            }

            $users = $query->get();
            return $this->sendToMultiple($users->all(), $notification, $options);
        } catch (Exception $e) {
            $this->loggingService->logNotificationError($e, [
                'empresa_id' => $empresaId,
                'sucursal_id' => $sucursalId,
                'notification' => get_class($notification),
                'options' => $options,
            ]);
            return [];
        }
    }

    public function sendCritical($notifiable, $notification, array $options = []): bool
    {
        $options['priority'] = 'critical';
        $options['channels'] = $options['channels'] ?? ['mail', 'database', 'broadcast', 'sms'];
        return $this->send($notifiable, $notification, $options);
    }

    public function sendHighPriority($notifiable, $notification, array $options = []): bool
    {
        $options['priority'] = 'high';
        $options['channels'] = $options['channels'] ?? ['mail', 'database', 'broadcast'];
        return $this->send($notifiable, $notification, $options);
    }

    public function scheduleNotification($notifiable, $notification, \DateTimeInterface $sendAt, array $options = []): bool
    {
        try {
            $scheduledNotification = new Notification();
            $scheduledNotification->id = Uuid::uuid4()->toString();
            $scheduledNotification->type = get_class($notification);
            $scheduledNotification->notifiable_type = get_class($notifiable);
            $scheduledNotification->notifiable_id = $notifiable->id ?? null;
            $scheduledNotification->data = [
                'notification_class' => get_class($notification),
                'notifiable_class' => get_class($notifiable),
                'options' => $options,
            ];
            $scheduledNotification->read_at = null;
            $scheduledNotification->scheduled_at = $sendAt;
            $scheduledNotification->save();

            $this->loggingService->logNotification('notification_scheduled', [
                'scheduled_id' => $scheduledNotification->id,
                'send_at' => $sendAt->format('Y-m-d H:i:s'),
                'notifiable' => get_class($notifiable),
                'notification' => get_class($notification),
            ]);

            return true;
        } catch (Exception $e) {
            $this->loggingService->logNotificationError($e, [
                'scheduled_notification' => get_class($notification),
                'notifiable' => get_class($notifiable),
                'send_at' => $sendAt->format('Y-m-d H:i:s'),
            ]);
            return false;
        }
    }

    public function markAsRead($notificationId, $userId): bool
    {
        try {
            $notification = Notification::where('id', $notificationId)
                ->where('notifiable_id', $userId)
                ->whereNull('read_at')
                ->first();

            if ($notification) {
                $notification->markAsRead();

                $this->loggingService->logNotification('notification_read', [
                    'notification_id' => $notificationId,
                    'user_id' => $userId,
                ]);

                return true;
            }

            return false;
        } catch (Exception $e) {
            $this->loggingService->logNotificationError($e, [
                'mark_as_read' => $notificationId,
                'user_id' => $userId,
            ]);
            return false;
        }
    }

    public function markAllAsRead($userId): int
    {
        try {
            $count = Notification::where('notifiable_id', $userId)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            $this->loggingService->logNotification('all_notifications_read', [
                'user_id' => $userId,
                'count' => $count,
            ]);

            return $count;
        } catch (Exception $e) {
            $this->loggingService->logNotificationError($e, [
                'mark_all_as_read' => $userId,
            ]);
            return 0;
        }
    }

    public function getUnreadNotifications($userId, int $limit = 50): array
    {
        try {
            $notifications = Notification::where('notifiable_id', $userId)
                ->whereNull('read_at')
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            return $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->toIso8601String(),
                ];
            })->toArray();
        } catch (Exception $e) {
            $this->loggingService->logNotificationError($e, [
                'get_unread_notifications' => $userId,
            ]);
            return [];
        }
    }

    public function getNotificationHistory($userId, array $filters = [], int $limit = 100): array
    {
        try {
            $query = Notification::where('notifiable_id', $userId);

            if (isset($filters['type'])) {
                $query->where('type', $filters['type']);
            }

            if (isset($filters['read'])) {
                if ($filters['read']) {
                    $query->whereNotNull('read_at');
                } else {
                    $query->whereNull('read_at');
                }
            }

            if (isset($filters['date_from'])) {
                $query->where('created_at', '>=', $filters['date_from']);
            }

            if (isset($filters['date_to'])) {
                $query->where('created_at', '<=', $filters['date_to']);
            }

            $notifications = $query->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            return $notifications->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'data' => $notification->data,
                    'read_at' => $notification->read_at,
                    'created_at' => $notification->created_at->toIso8601String(),
                ];
            })->toArray();
        } catch (Exception $e) {
            $this->loggingService->logNotificationError($e, [
                'get_notification_history' => $userId,
                'filters' => $filters,
            ]);
            return [];
        }
    }

    public function deleteNotification($notificationId, $userId): bool
    {
        try {
            $notification = Notification::where('id', $notificationId)
                ->where('notifiable_id', $userId)
                ->first();

            if ($notification) {
                $notification->delete();

                $this->loggingService->logNotification('notification_deleted', [
                    'notification_id' => $notificationId,
                    'user_id' => $userId,
                ]);

                return true;
            }

            return false;
        } catch (Exception $e) {
            $this->loggingService->logNotificationError($e, [
                'delete_notification' => $notificationId,
                'user_id' => $userId,
            ]);
            return false;
        }
    }

    public function getNotificationPreferences($userId): array
    {
        try {
            $cacheKey = "user:{$userId}:notification_preferences";

            return $this->cacheService->remember($cacheKey, 3600, function () use ($userId) {
                $user = User::find($userId);

                if (!$user) {
                    return [];
                }

                return [
                    'email' => $user->notifications_email ?? true,
                    'sms' => $user->notifications_sms ?? false,
                    'push' => $user->notifications_push ?? true,
                    'database' => $user->notifications_database ?? true,
                    'broadcast' => $user->notifications_broadcast ?? true,
                    'quiet_hours' => [
                        'enabled' => $user->quiet_hours_enabled ?? false,
                        'start' => $user->quiet_hours_start ?? '22:00',
                        'end' => $user->quiet_hours_end ?? '08:00',
                    ],
                    'frequency' => [
                        'email' => $user->email_frequency ?? 'immediate',
                        'sms' => $user->sms_frequency ?? 'immediate',
                    ],
                ];
            });
        } catch (Exception $e) {
            $this->loggingService->logNotificationError($e, [
                'get_notification_preferences' => $userId,
            ]);
            return [];
        }
    }

    public function updateNotificationPreferences($userId, array $preferences): bool
    {
        try {
            $user = User::find($userId);

            if (!$user) {
                return false;
            }

            $user->update([
                'notifications_email' => $preferences['email'] ?? true,
                'notifications_sms' => $preferences['sms'] ?? false,
                'notifications_push' => $preferences['push'] ?? true,
                'notifications_database' => $preferences['database'] ?? true,
                'notifications_broadcast' => $preferences['broadcast'] ?? true,
                'quiet_hours_enabled' => $preferences['quiet_hours']['enabled'] ?? false,
                'quiet_hours_start' => $preferences['quiet_hours']['start'] ?? '22:00',
                'quiet_hours_end' => $preferences['quiet_hours']['end'] ?? '08:00',
                'email_frequency' => $preferences['frequency']['email'] ?? 'immediate',
                'sms_frequency' => $preferences['frequency']['sms'] ?? 'immediate',
            ]);

            $cacheKey = "user:{$userId}:notification_preferences";
            $this->cacheService->forget($cacheKey);

            $this->loggingService->logNotification('preferences_updated', [
                'user_id' => $userId,
                'preferences' => $preferences,
            ]);

            return true;
        } catch (Exception $e) {
            $this->loggingService->logNotificationError($e, [
                'update_notification_preferences' => $userId,
                'preferences' => $preferences,
            ]);
            return false;
        }
    }

    public function isInQuietHours($userId): bool
    {
        try {
            $preferences = $this->getNotificationPreferences($userId);

            if (!($preferences['quiet_hours']['enabled'] ?? false)) {
                return false;
            }

            $startTime = $preferences['quiet_hours']['start'];
            $endTime = $preferences['quiet_hours']['end'];
            $currentTime = now()->format('H:i');

            if ($startTime <= $endTime) {
                return $currentTime >= $startTime && $currentTime <= $endTime;
            } else {
                return $currentTime >= $startTime || $currentTime <= $endTime;
            }
        } catch (Exception $e) {
            $this->loggingService->logNotificationError($e, [
                'is_in_quiet_hours' => $userId,
            ]);
            return false;
        }
    }

    protected function determineChannels($notifiable, $notification): array
    {
        $preferences = $this->getNotificationPreferences($notifiable->id ?? null);

        $channels = [];

        if ($preferences['email'] ?? true) {
            $channels[] = 'mail';
        }

        if ($preferences['database'] ?? true) {
            $channels[] = 'database';
        }

        if ($preferences['broadcast'] ?? true) {
            $channels[] = 'broadcast';
        }

        if ($preferences['sms'] ?? false) {
            $channels[] = 'sms';
        }

        if ($preferences['push'] ?? true) {
            $channels[] = 'push';
        }

        return $channels;
    }

    protected function cacheNotification($notifiable, array $notificationData): void
    {
        try {
            $cacheKey = "notifications:recent:{$notifiable->id}";
            $this->cacheService->put($cacheKey, $notificationData, 3600);
        } catch (Exception $e) {
            $this->loggingService->logCacheError($e, [
                'cache_notification' => $notifiable->id,
            ]);
        }
    }
}
