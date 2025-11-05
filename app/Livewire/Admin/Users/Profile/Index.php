<?php

namespace App\Livewire\Admin\Users\Profile;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\User;

class Index extends Component
{
    use HasDynamicLayout;

    public $user;

    public function mount()
    {
        $this->user = User::findOrFail(auth()->id());
    }

    public function render()
    {
        return $this->renderWithLayout('livewire.admin.users.profile.index', [], [
            'title' => 'Perfil de Usuario',
            'description' => 'Gestión de perfil de usuario',
            'breadcrumb' => [
                'admin.dashboard' => 'Dashboard',
                'admin.users.profile' => 'Mi Perfil'
            ]
        ]);
    }
}
