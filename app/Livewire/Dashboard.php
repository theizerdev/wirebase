<?php

namespace App\Livewire;

use Livewire\Component;
use App\Traits\HasDynamicLayout;

class Dashboard extends Component
{
    use HasDynamicLayout;
    
    public function render()
    {
        return redirect()->route('admin.dashboard');
    }
}
