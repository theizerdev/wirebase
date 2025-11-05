<?php

namespace App\Livewire\Admin\Users;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\User;
use App\Models\ActiveSession;
use Livewire\WithPagination;

class Show extends Component
{
    use HasDynamicLayout;


    use WithPagination;

    public User $user;
    public $perPage = 10;

    public function mount(User $user)
    {
        $this->user = $user->load(['empresa', 'sucursal']);
    }

    public function render()
    {
        return view('livewire.admin.users.show', [
            'user' => $this->user ?? null,
            'sessions' => $sessions ?? null
        ])->layout($this->getLayout(), [
            'title' => 'Detalles del Usuario'
        ]);
    }
}



