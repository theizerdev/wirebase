<?php

namespace App\Services\Audit;

use App\Services\Logging\LoggingService;
use App\Models\AuditLog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Exception;
use Ramsey\Uuid\Uuid;

class AuditService
{
    protected LoggingService $loggingService;
    protected array $sensitiveFields = [
        'password',
        'password_confirmation',
        'token',
        'api_key',
        'secret',
        'credit_card',
        'cvv',
        'pin',
        'ssn',
        'social_security',
        'bank_account',
        'routing_number',
        'private_key',
        'client_secret',
        'access_token',
        'refresh_token',
        'remember_token',
    ];

    public function __construct(LoggingService $loggingService)
    {
        $this->loggingService = $loggingService;
    }

    public function logModelEvent(string $action, Model $model, array $oldAttributes = [], array $newAttributes = []): void
    {
        try {
            $auditLog = AuditLog::create([
                'id' => Uuid::uuid4()->toString(),
                'user_id' => Auth::id(),
                'action' => $action,
                'auditable_type' => get_class($model),
                'auditable_id' => $model->getKey(),
                'old_values' => $this->sanitizeData($oldAttributes),
                'new_values' => $this->sanitizeData($newAttributes),
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'url' => Request::fullUrl(),
                'method' => Request::method(),
                'tags' => $this->generateTags($model, $action),
                'metadata' => [
                    'model_class' => get_class($model),
                    'model_key' => $model->getKey(),
                    'connection' => $model->getConnectionName(),
                    'table' => $model->getTable(),
                    'changes' => $this->calculateChanges($oldAttributes, $newAttributes),
                    'correlation_id' => $this->loggingService->getCorrelationId(),
                ],
            ]);

            $this->loggingService->logAuditEvent($action, [
                'model' => get_class($model),
                'model_id' => $model->getKey(),
                'changes' => $this->calculateChanges($oldAttributes, $newAttributes),
                'audit_log_id' => $auditLog->id,
            ]);
        } catch (Exception $e) {
            $this->loggingService->logApiError($e, [
                'audit_action' => $action,
                'model_class' => get_class($model),
                'model_id' => $model->getKey(),
            ]);
        }
    }

    public function logUserAction(string $action, array $data = [], ?string $description = null): void
    {
        try {
            $auditLog = AuditLog::create([
                'id' => Uuid::uuid4()->toString(),
                'user_id' => Auth::id(),
                'action' => $action,
                'auditable_type' => 'UserAction',
                'auditable_id' => Auth::id(),
                'old_values' => [],
                'new_values' => $this->sanitizeData($data),
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'url' => Request::fullUrl(),
                'method' => Request::method(),
                'tags' => ['user_action', $action],
                'metadata' => [
                    'description' => $description,
                    'correlation_id' => $this->loggingService->getCorrelationId(),
                ],
            ]);

            $this->loggingService->logAuditEvent($action, [
                'data' => $data,
                'description' => $description,
                'audit_log_id' => $auditLog->id,
            ]);
        } catch (Exception $e) {
            $this->loggingService->logApiError($e, [
                'audit_action' => $action,
                'data' => $data,
            ]);
        }
    }

    public function logSecurityEvent(string $event, array $data = [], ?string $severity = 'info'): void
    {
        try {
            $auditLog = AuditLog::create([
                'id' => Uuid::uuid4()->toString(),
                'user_id' => Auth::id(),
                'action' => 'security.' . $event,
                'auditable_type' => 'SecurityEvent',
                'auditable_id' => Auth::id() ?? 0,
                'old_values' => [],
                'new_values' => $this->sanitizeData($data),
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'url' => Request::fullUrl(),
                'method' => Request::method(),
                'tags' => ['security', $event],
                'metadata' => [
                    'severity' => $severity,
                    'correlation_id' => $this->loggingService->getCorrelationId(),
                ],
            ]);

            if ($severity === 'warning' || $severity === 'error') {
                $this->loggingService->logSecurityViolation($event, $data);
            } else {
                $this->loggingService->logSecurityEvent($event, $data);
            }
        } catch (Exception $e) {
            $this->loggingService->logApiError($e, [
                'security_event' => $event,
                'data' => $data,
            ]);
        }
    }

    public function logDatabaseTransaction(string $operation, array $details = []): void
    {
        try {
            $auditLog = AuditLog::create([
                'id' => Uuid::uuid4()->toString(),
                'user_id' => Auth::id(),
                'action' => 'database.' . $operation,
                'auditable_type' => 'DatabaseTransaction',
                'auditable_id' => 0,
                'old_values' => [],
                'new_values' => $this->sanitizeData($details),
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'url' => Request::fullUrl(),
                'method' => Request::method(),
                'tags' => ['database', $operation],
                'metadata' => [
                    'connection' => DB::getDefaultConnection(),
                    'transaction_level' => DB::transactionLevel(),
                    'correlation_id' => $this->loggingService->getCorrelationId(),
                ],
            ]);

            $this->loggingService->logTransaction($operation, $details);
        } catch (Exception $e) {
            $this->loggingService->logApiError($e, [
                'database_operation' => $operation,
                'details' => $details,
            ]);
        }
    }

    public function logBulkOperation(string $operation, string $modelClass, array $ids, array $changes = []): void
    {
        try {
            $auditLog = AuditLog::create([
                'id' => Uuid::uuid4()->toString(),
                'user_id' => Auth::id(),
                'action' => 'bulk.' . $operation,
                'auditable_type' => $modelClass,
                'auditable_id' => 0,
                'old_values' => [],
                'new_values' => $this->sanitizeData($changes),
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'url' => Request::fullUrl(),
                'method' => Request::method(),
                'tags' => ['bulk', $operation, $modelClass],
                'metadata' => [
                    'model_class' => $modelClass,
                    'affected_ids' => $ids,
                    'affected_count' => count($ids),
                    'changes' => $this->sanitizeData($changes),
                    'correlation_id' => $this->loggingService->getCorrelationId(),
                ],
            ]);

            $this->loggingService->logBusinessEvent('bulk_operation', [
                'operation' => $operation,
                'model' => $modelClass,
                'count' => count($ids),
                'changes' => $changes,
            ]);
        } catch (Exception $e) {
            $this->loggingService->logApiError($e, [
                'bulk_operation' => $operation,
                'model_class' => $modelClass,
                'ids' => $ids,
            ]);
        }
    }

    public function logApiRequest(array $requestData, array $responseData, int $statusCode, float $duration): void
    {
        try {
            $auditLog = AuditLog::create([
                'id' => Uuid::uuid4()->toString(),
                'user_id' => Auth::id(),
                'action' => 'api.request',
                'auditable_type' => 'ApiRequest',
                'auditable_id' => 0,
                'old_values' => [],
                'new_values' => [
                    'request' => $this->sanitizeData($requestData),
                    'response' => $this->sanitizeData($responseData),
                    'status_code' => $statusCode,
                    'duration' => $duration,
                ],
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'url' => Request::fullUrl(),
                'method' => Request::method(),
                'tags' => ['api', 'request'],
                'metadata' => [
                    'endpoint' => Request::route()?->getName(),
                    'controller' => Request::route()?->getControllerClass(),
                    'action' => Request::route()?->getActionMethod(),
                    'correlation_id' => $this->loggingService->getCorrelationId(),
                ],
            ]);

            $this->loggingService->logApiRequest($requestData);
            $this->loggingService->logApiResponse($responseData, $statusCode, $duration);
        } catch (Exception $e) {
            $this->loggingService->logApiError($e, [
                'api_request' => $requestData,
                'api_response' => $responseData,
            ]);
        }
    }

    public function logFailedLoginAttempt(string $identifier, array $context = []): void
    {
        try {
            $auditLog = AuditLog::create([
                'id' => Uuid::uuid4()->toString(),
                'user_id' => null,
                'action' => 'auth.failed_login',
                'auditable_type' => 'FailedLogin',
                'auditable_id' => 0,
                'old_values' => [],
                'new_values' => [
                    'identifier' => $identifier,
                    'context' => $this->sanitizeData($context),
                ],
                'ip_address' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'url' => Request::fullUrl(),
                'method' => Request::method(),
                'tags' => ['auth', 'failed_login', 'security'],
                'metadata' => [
                    'severity' => 'warning',
                    'correlation_id' => $this->loggingService->getCorrelationId(),
                ],
            ]);

            $this->loggingService->logSecurityEvent('failed_login_attempt', [
                'identifier' => $identifier,
                'context' => $context,
            ], 'warning');
        } catch (Exception $e) {
            $this->loggingService->logApiError($e, [
                'failed_login' => $identifier,
                'context' => $context,
            ]);
        }
    }

    public function getAuditTrail(string $modelType, $modelId, int $limit = 50): array
    {
        try {
            $auditLogs = AuditLog::where('auditable_type', $modelType)
                ->where('auditable_id', $modelId)
                ->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            return $auditLogs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'user_id' => $log->user_id,
                    'user_email' => $log->user?->email,
                    'old_values' => $log->old_values,
                    'new_values' => $log->new_values,
                    'ip_address' => $log->ip_address,
                    'created_at' => $log->created_at->toIso8601String(),
                    'tags' => $log->tags,
                    'metadata' => $log->metadata,
                ];
            })->toArray();
        } catch (Exception $e) {
            $this->loggingService->logApiError($e, [
                'audit_trail' => $modelType,
                'model_id' => $modelId,
            ]);
            return [];
        }
    }

    public function searchAuditLogs(array $filters = [], int $limit = 50): array
    {
        try {
            $query = AuditLog::query();

            if (isset($filters['user_id'])) {
                $query->where('user_id', $filters['user_id']);
            }

            if (isset($filters['action'])) {
                $query->where('action', 'like', '%' . $filters['action'] . '%');
            }

            if (isset($filters['model_type'])) {
                $query->where('auditable_type', $filters['model_type']);
            }

            if (isset($filters['model_id'])) {
                $query->where('auditable_id', $filters['model_id']);
            }

            if (isset($filters['ip_address'])) {
                $query->where('ip_address', $filters['ip_address']);
            }

            if (isset($filters['tags'])) {
                foreach ($filters['tags'] as $tag) {
                    $query->whereJsonContains('tags', $tag);
                }
            }

            if (isset($filters['date_from'])) {
                $query->where('created_at', '>=', $filters['date_from']);
            }

            if (isset($filters['date_to'])) {
                $query->where('created_at', '<=', $filters['date_to']);
            }

            $auditLogs = $query->orderBy('created_at', 'desc')
                ->limit($limit)
                ->get();

            return $auditLogs->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'user_id' => $log->user_id,
                    'user_email' => $log->user?->email,
                    'auditable_type' => $log->auditable_type,
                    'auditable_id' => $log->auditable_id,
                    'old_values' => $log->old_values,
                    'new_values' => $log->new_values,
                    'ip_address' => $log->ip_address,
                    'user_agent' => $log->user_agent,
                    'url' => $log->url,
                    'method' => $log->method,
                    'tags' => $log->tags,
                    'metadata' => $log->metadata,
                    'created_at' => $log->created_at->toIso8601String(),
                ];
            })->toArray();
        } catch (Exception $e) {
            $this->loggingService->logApiError($e, [
                'search_audit_logs' => $filters,
            ]);
            return [];
        }
    }

    public function generateComplianceReport(array $filters = []): array
    {
        try {
            $query = AuditLog::query();

            if (isset($filters['date_from'])) {
                $query->where('created_at', '>=', $filters['date_from']);
            }

            if (isset($filters['date_to'])) {
                $query->where('created_at', '<=', $filters['date_to']);
            }

            $totalLogs = $query->count();
            $securityEvents = $query->where('action', 'like', 'security.%')->count();
            $failedLogins = $query->where('action', 'auth.failed_login')->count();
            $userActions = $query->where('action', 'like', 'user.%')->count();

            $topUsers = $query->select('user_id', DB::raw('count(*) as total'))
                ->groupBy('user_id')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get();

            $topActions = $query->select('action', DB::raw('count(*) as total'))
                ->groupBy('action')
                ->orderBy('total', 'desc')
                ->limit(10)
                ->get();

            return [
                'period' => [
                    'from' => $filters['date_from'] ?? AuditLog::min('created_at'),
                    'to' => $filters['date_to'] ?? AuditLog::max('created_at'),
                ],
                'summary' => [
                    'total_logs' => $totalLogs,
                    'security_events' => $securityEvents,
                    'failed_logins' => $failedLogins,
                    'user_actions' => $userActions,
                ],
                'top_users' => $topUsers->map(function ($item) {
                    return [
                        'user_id' => $item->user_id,
                        'total' => $item->total,
                    ];
                })->toArray(),
                'top_actions' => $topActions->map(function ($item) {
                    return [
                        'action' => $item->action,
                        'total' => $item->total,
                    ];
                })->toArray(),
                'generated_at' => now()->toIso8601String(),
            ];
        } catch (Exception $e) {
            $this->loggingService->logApiError($e, [
                'compliance_report' => $filters,
            ]);
            return [];
        }
    }

    protected function sanitizeData(array $data): array
    {
        return array_map(function ($value, $key) {
            if (in_array(strtolower($key), $this->sensitiveFields, true)) {
                return '***REDACTED***';
            }
            if (is_array($value)) {
                return $this->sanitizeData($value);
            }
            return $value;
        }, $data, array_keys($data));
    }

    protected function calculateChanges(array $oldAttributes, array $newAttributes): array
    {
        $changes = [];

        foreach ($newAttributes as $key => $newValue) {
            if (!array_key_exists($key, $oldAttributes) || $oldAttributes[$key] !== $newValue) {
                $changes[$key] = [
                    'old' => $oldAttributes[$key] ?? null,
                    'new' => $newValue,
                ];
            }
        }

        foreach ($oldAttributes as $key => $oldValue) {
            if (!array_key_exists($key, $newAttributes)) {
                $changes[$key] = [
                    'old' => $oldValue,
                    'new' => null,
                ];
            }
        }

        return $changes;
    }

    protected function generateTags(Model $model, string $action): array
    {
        $tags = [
            'model:' . class_basename($model),
            'action:' . $action,
        ];

        if (Auth::check()) {
            $tags[] = 'user:' . Auth::id();
        }

        if (Request::has('empresa_id')) {
            $tags[] = 'empresa:' . Request::get('empresa_id');
        }

        if (Request::has('sucursal_id')) {
            $tags[] = 'sucursal:' . Request::get('sucursal_id');
        }

        return $tags;
    }
}
