<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Sucursal;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use App\Traits\Exportable;
use App\Traits\HasDynamicLayout;
use Illuminate\Support\Facades\Hash;
use App\Services\WhatsAppService;
use Illuminate\Support\Str;

class Index extends Component
{
    use WithPagination, Exportable, HasDynamicLayout;

    public $search = '';
    public $status = '';
    public $empresa_id = '';
    public $sucursal_id = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'empresa_id' => ['except' => ''],
        'sucursal_id' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
    ];

    public function mount()
    {
        // Verificar permiso para ver usuarios
        if (!Auth::user()->can('access users')) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingEmpresaId()
    {
        $this->resetPage();
    }

    public function updatingSucursalId()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortDirection = 'asc';
        }

        $this->sortBy = $field;
    }

    protected function getExportQuery()
    {
        return $this->getBaseQuery();
    }

    protected function getExportHeaders(): array
    {
        return ['ID', 'Nombre', 'Email', 'Empresa', 'Sucursal', 'Zona', 'Rol', 'Status'];
    }

    protected function formatExportRow($user): array
    {
        return [
            $user->id,
            $user->name,
            $user->email,
            $user->empresa->razon_social ?? 'N/A',
            $user->sucursal->nombre ?? 'N/A',
            $user->zona ?? '-',
            $user->roles->pluck('name')->join(', '),
            $user->status ? 'Activo' : 'Inactivo'
        ];
    }

    private function getBaseQuery()
    {
        return User::forUser()->with(['empresa', 'sucursal', 'roles'])
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            })
            ->when($this->status !== '', function ($query) {
                $query->where('status', $this->status);
            })
            ->when($this->empresa_id, function ($query) {
                $query->where('empresa_id', $this->empresa_id);
            })
            ->when($this->sucursal_id, function ($query) {
                $query->where('sucursal_id', $this->sucursal_id);
            });
    }

    public function render()
    {
        $users = $this->getBaseQuery()
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        $empresas = Empresa::forUser()->where('status', 'active')->get();
        $sucursales = Sucursal::forUser()->where('status', 'active')
            ->when($this->empresa_id, function ($query) {
                $query->where('empresa_id', $this->empresa_id);
            })
            ->get();

        $roles = Role::all();

        // Calcular estadísticas
        $totalUsers = User::forUser()->count();
        $activeUsers = User::forUser()->where('status', 1)->count();
        $pendingUsers = 0;
        $inactiveUsers = User::forUser()->where('status', 0)->count();

        return $this->renderWithLayout('livewire.admin.users.index', compact('users', 'empresas', 'sucursales', 'roles', 'totalUsers', 'activeUsers', 'pendingUsers', 'inactiveUsers'), [
            'title' => 'Lista de Usuarios',
            'breadcrumb' => [
                'admin.dashboard' => 'Dashboard',
                'admin.users.index' => 'Usuarios'
            ]
        ]);
    }

    public function toggleStatus(User $user)
    {
        if (!Auth::user()->can('edit users')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No tienes permiso para editar usuarios.',
                'duration' => 4000
            ]);
            return;
        }

        if ($user->id === Auth::id()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No puedes desactivar tu propia cuenta.',
                'duration' => 4000
            ]);
            return;
        }

        $user->status = !$user->status;
        $user->save();

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "Estado de usuario '{$user->name}' actualizado a " . ($user->status ? 'activo' : 'inactivo') . ".",
            'duration' => 4000
        ]);
    }

    public function unlockUser(User $user)
    {
        if (!Auth::user()->can('edit users')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No tienes permiso para desbloquear usuarios.',
                'duration' => 4000
            ]);
            return;
        }

        $user->update([
            'locked_until' => null,
            'failed_login_attempts' => 0,
            'status' => true // Reactivar usuario al desbloquear
        ]);

        // Enviar notificación por WhatsApp si el usuario tiene teléfono
        if ($user->phone) {
            try {
                $whatsappService = app(\App\Services\WhatsAppService::class);
                
                // Si la empresa tiene su propia configuración, usarla
                if ($user->empresa_id) {
                    $whatsappService = \App\Services\WhatsAppService::forCompany($user->empresa_id);
                }

                if ($whatsappService->isConfigured()) {
                    $mensaje = "Hola {$user->name}, tu cuenta ha sido desbloqueada exitosamente por el administrador. Ya puedes acceder al sistema nuevamente.";
                    $whatsappService->sendMessage($user->phone, $mensaje);
                }
            } catch (\Exception $e) {
                \Log::error('Error enviando notificación WhatsApp de desbloqueo: ' . $e->getMessage());
            }
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "Usuario '{$user->name}' desbloqueado exitosamente.",
            'duration' => 4000
        ]);
    }

    public function delete(User $user)
    {
        // Verificar permiso para eliminar usuarios
        if (!Auth::user()->can('delete users')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No tienes permiso para eliminar usuarios.',
                'duration' => 4000
            ]);
            return;
        }

        // No permitir eliminar al usuario actual
        if ($user->id === Auth::id()) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No puedes eliminar tu propia cuenta.',
                'duration' => 4000
            ]);
            return;
        }

        $nombreUsuario = $user->name;
        $user->delete();
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "Usuario '{$nombreUsuario}' eliminado exitosamente.",
            'duration' => 4000
        ]);
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->status = '';
        $this->empresa_id = '';
        $this->sucursal_id = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->perPage = 10;
        $this->resetPage();
    }

    public function loadSucursales()
    {
        // Este método se llama cuando se cambia la empresa en los filtros
        $this->sucursal_id = '';
    }

    public function resendCredentials(User $user): void
    {
        if (!Auth::user()->can('edit users')) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'No tienes permiso para enviar credenciales.',
                'duration' => 4000,
            ]);
            return;
        }

        if (!$user->phone) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => "El usuario '{$user->name}' no tiene teléfono registrado.",
                'duration' => 4000,
            ]);
            return;
        }

        // Regenerar contraseña temporal
        $plainPassword = Str::random(12);
        $user->password = Hash::make($plainPassword);
        $user->save();

        // Determinar teléfonos/empresa
        try {
            $whatsappService = $user->empresa_id
                ? WhatsAppService::forCompany($user->empresa_id)
                : app(WhatsAppService::class);

            if (!$whatsappService->isConfigured()) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'WhatsApp no está configurado para la empresa de este usuario.',
                    'duration' => 4000,
                ]);
                return;
            }

            $telefono = $this->formatearTelefono($user->phone);

            $roles = method_exists($user, 'getRoleNames') ? $user->getRoleNames()->toArray() : [];
            $rolTexto = !empty($roles) ? implode(', ', $roles) : 'Sin rol';

            $empresaNombre = $user->empresa->razon_social ?? 'la institución';

            $mensaje = "Estimado(a) *{$user->name}*\n\n";
            $mensaje .= "Reciba un cordial saludo. Por instrucción de la administración, se ha actualizado y reenviado su acceso al sistema para *{$empresaNombre}*.\n\n";
            $mensaje .= "A continuación, sus credenciales de acceso:\n\n";
            $mensaje .= "*🌐 Plataforma:* " . env('APP_URL', 'http://localhost') . "\n";
            $mensaje .= "*👤 Usuario:* {$user->username}\n";
            $mensaje .= "*🔑 Contraseña:* {$plainPassword}\n";
            $mensaje .= "*🧩 Rol:* {$rolTexto}\n\n";
            $mensaje .= "*⚠️ Importante:* Por seguridad, le recomendamos cambiar su contraseña en su primer inicio de sesión.\n\n";
            $mensaje .= "Si tiene alguna consulta, por favor contáctenos.\n\n";
            $mensaje .= "Atentamente,\nAdministración / Soporte";

            $whatsappService->sendMessage($telefono, $mensaje, true);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Credenciales reenviadas por WhatsApp correctamente.',
                'duration' => 4000,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error reenviando credenciales WhatsApp: ' . $e->getMessage(), [
                'user_id' => $user->id,
            ]);

            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Ocurrió un error al reenviar las credenciales por WhatsApp.',
                'duration' => 4000,
            ]);
        }
    }

    protected function formatearTelefono(string $telefono): string
    {
        $limpio = preg_replace('/\D/', '', $telefono);

        if (str_starts_with($limpio, '0')) {
            $limpio = substr($limpio, 1);
        }

        $codigo = '51';

        $empId = auth()->user()->empresa_id;
        if (!$empId && auth()->check() && auth()->user()->empresa_id) {
            $empId = auth()->user()->empresa_id;
        }

        if ($empId) {
            $empresa = \DB::table('empresas')->where('id', $empId)->first();
            if ($empresa && $empresa->pais_id) {
                $pais = \DB::table('pais')->where('id', $empresa->pais_id)->first();
                if ($pais && $pais->codigo_telefonico) {
                    $codigo = ltrim($pais->codigo_telefonico, '+');
                }
            }
        }

        if (!str_starts_with($limpio, $codigo) && strlen($limpio) >= 7 && strlen($limpio) <= 12) {
            $limpio = $codigo . $limpio;
        }

        return '+' . $limpio;
    }
}
