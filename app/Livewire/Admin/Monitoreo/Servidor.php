<?php

namespace App\Livewire\Admin\Monitoreo;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\Attributes\On;

class Servidor extends Component
{
    use HasDynamicLayout;


    public $lastUpdate;

    public function mount()
    {
        abort_unless(auth()->user()->can('view monitoreo servidor'), 403);
        $this->lastUpdate = now()->format('H:i:s');
    }

    #[On('refresh-servidor')]
    public function refreshData()
    {
        $this->lastUpdate = now()->format('H:i:s');
    }

    public function render()
    {
        return view('livewire.admin.monitoreo.servidor')->layout($this->getLayout());
    }
}




