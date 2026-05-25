<?php

namespace App\Livewire\Admin\Pastores;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Pastor;

class Show extends Component
{
    use HasDynamicLayout;

    public Pastor $pastor;

    public function mount(Pastor $pastor)
    {
        $this->pastor = $pastor->load([
            'ciudad', 
            'estado', 
            'municipio', 
            'parroquia', 
            'extensiones', 
            'conyuge.iglesias'
        ]);
    }

    /**
     * Get the churches that should be displayed for this pastor.
     * If the pastor has a spouse, show churches from both.
     */
    public function getChurchesToDisplayProperty()
    {
        $churches = collect();
        
        // Add churches directly associated with this pastor
        if ($this->pastor->iglesias) {
            $churches = $churches->concat($this->pastor->iglesias);
        }
        
        // If pastor has a spouse, also add churches associated with the spouse
        if ($this->pastor->conyuge) {
            if ($this->pastor->conyuge->iglesias) {
                $churches = $churches->concat($this->pastor->conyuge->iglesias);
            }
        }
        
        // Remove duplicates and return unique churches
        return $churches->unique('id')->values();
    }

    public function render()
    {
        return view('livewire.admin.pastores.show')->layout($this->getLayout());
    }
}