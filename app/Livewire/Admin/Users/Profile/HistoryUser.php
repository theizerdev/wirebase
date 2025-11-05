<?php

namespace App\Livewire\Admin\Users\Profile;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\ActiveSession;
use Livewire\WithPagination;
use App\Models\User;

class HistoryUser extends Component
{
    use HasDynamicLayout;


    use WithPagination;

    public $user_id;
    public $user;

    public function mount()
    {
        $this->user_id = auth()->user()->id;
        $this->user = User::findOrFail(auth()->user()->id);
    }

    public function render()
    {
        return view('livewire.admin.users.profile.history-user', [
            'user' => $this->user ?? null,
            'sessions' => $sessions ?? null
        ])->layout($this->getLayout(), [
            'title' => 'Detalles del Usuario'
        ]);
    }
}




