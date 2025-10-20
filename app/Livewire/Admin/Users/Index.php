<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Sucursal;

class Index extends Component
{
    use WithPagination;

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

    public function delete(User $user)
    {
        $user->delete();
        session()->flash('message', 'Usuario eliminado correctamente.');
    }

    public function toggleStatus($userId)
    {
        $user = User::find($userId);

        if ($user) {

            $user->status = !$user->status;
            $user->save();

            $this->dispatch('refreshComponent');
            session()->flash('message', 'Estado del usuario actualizado correctamente.');
        }
    }

    public function render()
    {
        $query = User::with(['empresa', 'sucursal']);

        // Apply search
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        // Apply status filter
        if ($this->status !== '') {
            $query->where('status', $this->status === 'active' ? 1 : 0);
        }

        // Apply empresa filter
        if ($this->empresa_id !== '') {
            $query->where('empresa_id', $this->empresa_id);
        }

        // Apply sucursal filter
        if ($this->sucursal_id !== '') {
            $query->where('sucursal_id', $this->sucursal_id);
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        // Get paginated results
        $users = $query->paginate($this->perPage);

        // Statistics
        $totalUsers = User::count();
        $activeUsers = User::where('status', true)->count();
        $inactiveUsers = User::where('status', false)->count();
        $unverifiedUsers = User::whereNull('email_verified_at')->count();

        // Empresas for filter
        $empresas = Empresa::where('status', true)->get();

        // Sucursales for filter (filtered by selected empresa if any)
        $sucursales = $this->empresa_id
            ? Sucursal::where('empresa_id', $this->empresa_id)->where('status', true)->get()
            : Sucursal::where('status', true)->get();

        return view('livewire.admin.users.index', [
            'users' => $users,
            'totalUsers' => $totalUsers,
            'activeUsers' => $activeUsers,
            'inactiveUsers' => $inactiveUsers,
            'unverifiedUsers' => $unverifiedUsers,
            'empresas' => $empresas,
            'sucursales' => $sucursales
        ])
            ->layout('components.layouts.admin', [
                'title' => 'Lista de Usuarios'
            ]);
    }
}
