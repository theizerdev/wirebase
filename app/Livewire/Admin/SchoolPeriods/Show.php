<?php

namespace App\Livewire\Admin\SchoolPeriods;

use App\Models\SchoolPeriod;
use App\Traits\HasDynamicLayout;
use Livewire\Component;

class Show extends Component
{
    use HasDynamicLayout;


    public $schoolPeriod;

    public function mount(SchoolPeriod $schoolPeriod)
    {
        $this->schoolPeriod = $schoolPeriod;
    }

    public function render()
    {
        return view('livewire.admin.school-periods.show')->layout($this->getLayout());
    }
}



