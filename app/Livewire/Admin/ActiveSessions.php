<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ActiveSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Str;

class ActiveSessions extends Component
{
    use WithPagination;
    private $paginationTheme = 'bootstrap';

    public $search = '';
    public $status = '';
    public $sortBy = 'last_activity';
    public $sortDirection = 'desc';
    public $perPage = 10;

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'sortBy' => ['except' => 'last_activity'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
    ];

    public function loadSessions()
    {
        $this->resetPage();
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
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
        $this->resetPage();
    }

    public function destroy($id)
    {
        $session = ActiveSession::where('user_id', Auth::id())
            ->where('id', $id)
            ->first();

        if ($session) {
            // Si es la sesión actual, no la eliminamos, solo la marcamos como inactiva
            if ($session->is_current) {
                $session->update([
                    'is_current' => false,
                    'is_active' => false,
                    'logout_at' => now(),
                ]);

                // Cerrar la sesión actual
                Auth::logout();
                Session::flush();
                session()->flash('status', 'Sesión actual cerrada.');
                return redirect()->to('/login');
            } else {
                // Para otras sesiones, las marcamos como inactivas
                $session->update([
                    'is_active' => false,
                    'logout_at' => now(),
                ]);

                session()->flash('status', 'Sesión terminada exitosamente.');
                $this->resetPage();
            }

            return;
        }

        session()->flash('error', 'Sesión no encontrada.');
    }

    public function clearFilters()
    {
        $this->search = '';
        $this->status = '';
        $this->sortBy = 'last_activity';
        $this->sortDirection = 'desc';
        $this->perPage = 10;
        $this->resetPage();
    }

    public function render()
    {
        $query = ActiveSession::where('user_id', Auth::id());

        // Aplicar búsqueda
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('ip_address', 'like', '%' . $this->search . '%')
                  ->orWhere('location', 'like', '%' . $this->search . '%')
                  ->orWhere('user_agent', 'like', '%' . $this->search . '%');
            });
        }

        // Aplicar filtro de estado
        if (!empty($this->status)) {
            if ($this->status === 'active') {
                $query->where('is_active', true);
            } elseif ($this->status === 'inactive') {
                $query->where('is_active', false);
            } elseif ($this->status === 'current') {
                $query->where('is_current', true);
            }
        }

        // Aplicar ordenamiento
        $query->orderBy($this->sortBy, $this->sortDirection);

        // Obtener resultados con paginación
        $activeSessions = $query->paginate($this->perPage);

        return view('livewire.admin.active-sessions', [
            'activeSessions' => $activeSessions
        ])
            ->layout('components.layouts.admin', [
                'title' => 'Sesiones Activas'
            ]);
    }
}
