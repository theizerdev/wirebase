<?php

declare(strict_types=1);

namespace App\Http\Requests\User;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;

class BulkDeleteUsersRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return $this->user()->can('bulk delete users');
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'user_ids' => [
                'required',
                'array',
                'min:1',
                'max:100', // Limit bulk operations to 100 users
            ],
            'user_ids.*' => [
                'required',
                'integer',
                'exists:users,id',
                Rule::notIn([$this->user()->id]), // Prevent self-deletion
            ],
            'force_delete' => [
                'sometimes',
                'boolean',
            ],
            'reason' => [
                'sometimes',
                'string',
                'max:500',
            ],
            'notification_message' => [
                'sometimes',
                'string',
                'max:1000',
            ],
            'send_notification' => [
                'sometimes',
                'boolean',
            ],
            'preserve_data' => [
                'sometimes',
                'boolean',
            ],
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'user_ids.required' => 'Debe proporcionar al menos un ID de usuario.',
            'user_ids.array' => 'Los IDs de usuario deben ser un array.',
            'user_ids.min' => 'Debe proporcionar al menos un ID de usuario.',
            'user_ids.max' => 'No puede eliminar más de 100 usuarios a la vez.',
            'user_ids.*.required' => 'Cada ID de usuario es obligatorio.',
            'user_ids.*.integer' => 'Cada ID de usuario debe ser un número entero.',
            'user_ids.*.exists' => 'Uno o más usuarios no existen.',
            'user_ids.*.not_in' => 'No puedes eliminar tu propia cuenta.',
            'force_delete.boolean' => 'El parámetro force_delete debe ser verdadero o falso.',
            'reason.max' => 'La razón no puede exceder 500 caracteres.',
            'notification_message.max' => 'El mensaje de notificación no puede exceder 1000 caracteres.',
            'send_notification.boolean' => 'El parámetro send_notification debe ser verdadero o falso.',
            'preserve_data.boolean' => 'El parámetro preserve_data debe ser verdadero o falso.',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'user_ids' => 'IDs de usuarios',
            'user_ids.*' => 'ID de usuario',
            'force_delete' => 'forzar eliminación',
            'reason' => 'razón',
            'notification_message' => 'mensaje de notificación',
            'send_notification' => 'enviar notificación',
            'preserve_data' => 'preservar datos',
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param Validator $validator
     * @return void
     */
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            // Additional validation logic
            $this->validateUserPermissions($validator);
            $this->validateProtectedUsers($validator);
            $this->validateBusinessRules($validator);
        });
    }

    /**
     * Validate user permissions for bulk operations.
     *
     * @param Validator $validator
     * @return void
     */
    protected function validateUserPermissions(Validator $validator): void
    {
        $userIds = $this->user_ids;
        $users = \App\Models\User::whereIn('id', $userIds)->get();

        foreach ($users as $user) {
            // Check if current user can delete each target user
            if (!$this->user()->can('delete', $user)) {
                $validator->errors()->add(
                    'user_ids',
                    "No tienes permiso para eliminar al usuario: {$user->name} (ID: {$user->id})"
                );
            }

            // Check multitenancy constraints
            if (!$this->user()->hasRole('super-admin')) {
                if ($user->empresa_id !== $this->user()->empresa_id) {
                    $validator->errors()->add(
                        'user_ids',
                        "No puedes eliminar usuarios de otra empresa: {$user->name} (ID: {$user->id})"
                    );
                }

                if ($this->user()->sucursal_id && $user->sucursal_id !== $this->user()->sucursal_id) {
                    $validator->errors()->add(
                        'user_ids',
                        "No puedes eliminar usuarios de otra sucursal: {$user->name} (ID: {$user->id})"
                    );
                }
            }

            // Check role-based constraints
            if ($user->hasRole('super-admin') && !$this->user()->hasRole('super-admin')) {
                $validator->errors()->add(
                    'user_ids',
                    "Solo los super administradores pueden eliminar a otros super administradores: {$user->name} (ID: {$user->id})"
                );
            }
        }
    }

    /**
     * Validate protected users that cannot be deleted.
     *
     * @param Validator $validator
     * @return void
     */
    protected function validateProtectedUsers(Validator $validator): void
    {
        $userIds = $this->user_ids;

        // Get system administrators from config
        $systemAdmins = config('app.system_administrators', []);

        // Check for system administrators
        foreach ($userIds as $userId) {
            if (in_array($userId, $systemAdmins)) {
                $validator->errors()->add(
                    'user_ids',
                    "No puedes eliminar usuarios del sistema administrativo (ID: {$userId})"
                );
            }
        }

        // Check for users with critical permissions
        $users = \App\Models\User::whereIn('id', $userIds)
            ->with('roles.permissions')
            ->get();

        foreach ($users as $user) {
            $hasCriticalPermissions = $user->roles->flatMap(function ($role) {
                return $role->permissions->pluck('name');
            })->contains(function ($permission) {
                return in_array($permission, [
                    'manage system',
                    'manage super-admin',
                    'access all resources',
                    'bypass permissions',
                ]);
            });

            if ($hasCriticalPermissions && !$this->user()->hasRole('super-admin')) {
                $validator->errors()->add(
                    'user_ids',
                    "No puedes eliminar usuarios con permisos críticos del sistema: {$user->name} (ID: {$user->id})"
                );
            }
        }
    }

    /**
     * Validate business rules for bulk deletion.
     *
     * @param Validator $validator
     * @return void
     */
    protected function validateBusinessRules(Validator $validator): void
    {
        $userIds = $this->user_ids;

        // Check for users with active sessions
        $activeSessions = \DB::table('sessions')
            ->whereIn('user_id', $userIds)
            ->count();

        if ($activeSessions > 0 && !$this->boolean('force_delete')) {
            $validator->errors()->add(
                'user_ids',
                "Algunos usuarios tienen sesiones activas. Usa force_delete=true para eliminarlos de todos modos."
            );
        }

        // Check for users with recent activity
        $recentActivity = \App\Models\Activity::whereIn('causer_id', $userIds)
            ->where('created_at', '>', now()->subDays(7))
            ->count();

        if ($recentActivity > 0 && !$this->boolean('force_delete')) {
            $validator->errors()->add(
                'user_ids',
                "Algunos usuarios han tenido actividad reciente. Usa force_delete=true para eliminarlos de todos modos."
            );
        }

        // Validate reason if provided
        if ($this->filled('reason')) {
            $reason = trim($this->reason);
            if (strlen($reason) < 10) {
                $validator->errors()->add('reason', 'La razón debe tener al menos 10 caracteres.');
            }

            // Check for suspicious patterns
            $suspiciousPatterns = ['test', 'prueba', 'demo', 'temp'];
            foreach ($suspiciousPatterns as $pattern) {
                if (stripos($reason, $pattern) !== false) {
                    $validator->errors()->add('reason', 'La razón parece ser temporal o de prueba.');
                    break;
                }
            }
        }

        // Validate notification message if provided
        if ($this->filled('notification_message')) {
            $message = trim($this->notification_message);
            if (strlen($message) < 20) {
                $validator->errors()->add('notification_message', 'El mensaje de notificación debe tener al menos 20 caracteres.');
            }
        }

        // Check if preserve_data and force_delete are both true (conflicting)
        if ($this->boolean('preserve_data') && $this->boolean('force_delete')) {
            $validator->errors()->add(
                'preserve_data',
                'No puedes preservar datos y forzar eliminación al mismo tiempo.'
            );
        }
    }

    /**
     * Handle a failed validation attempt.
     *
     * @param Validator $validator
     * @return void
     * @throws HttpResponseException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new HttpResponseException(response()->json([
            'success' => false,
            'message' => 'Los datos proporcionados no son válidos para la eliminación masiva.',
            'errors' => $validator->errors(),
            'timestamp' => now()->toIso8601String(),
            'request_id' => $this->header('X-Request-ID'),
        ], 422));
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation(): void
    {
        // Clean and normalize input data
        $this->merge([
            'user_ids' => array_unique(array_map('intval', (array) $this->user_ids)),
            'force_delete' => $this->boolean('force_delete', false),
            'send_notification' => $this->boolean('send_notification', false),
            'preserve_data' => $this->boolean('preserve_data', false),
            'reason' => $this->filled('reason') ? trim($this->reason) : null,
            'notification_message' => $this->filled('notification_message') ? trim($this->notification_message) : null,
        ]);
    }
}
