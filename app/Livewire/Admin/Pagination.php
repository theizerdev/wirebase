<?php

namespace App\Livewire\Admin;

use App\Traits\HasDynamicLayout;
use Livewire\Component;

class Pagination extends Component
{
    use HasDynamicLayout;


    public function render()
    {
        return view('livewire.admin.pagination')->layout($this->getLayout());
    }
}



