<?php

namespace App\Livewire\Admin\Users;

use Livewire\Component;
use App\Models\User;
use App\Models\ActiveSession;
use Livewire\WithPagination;

class Show extends Component
{
    use WithPagination;

    public User $user;
    public $perPage = 10;

    public function mount(User $user)
    {
        $this->user = $user->load(['empresa', 'sucursal']);
    }

    public function render()
    {
        $sessions = ActiveSession::where('user_id', $this->user->id)
            ->orderBy('login_at', 'desc')
            ->paginate($this->perPage);

        return view('livewire.admin.users.show', [
            'sessions' => $sessions
        ])->layout('components.layouts.admin', [
            'title' => 'Detalles del Usuario'
        ]);
    }
}