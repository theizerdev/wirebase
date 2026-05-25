<?php

namespace App\Livewire\Admin\Inventario;

use App\Models\InventarioIglesia;
use Livewire\Component;
use App\Traits\HasDynamicLayout;

class Show extends Component
{
    use HasDynamicLayout;

    public $itemId;
    public $item;

    public function mount($id)
    {
        $this->itemId = $id;
        $this->item = InventarioIglesia::with('iglesia')->find($id);
        
        if (!$this->item) {
            session()->flash('error', 'Ítem no encontrado.');
            return redirect()->route('admin.inventario.index');
        }
    }

    public function deleteItem()
    {
        $itemName = $this->item->nombre;
        $this->item->delete();
        
        session()->flash('message', "Ítem '{$itemName}' eliminado correctamente.");
        return redirect()->route('admin.inventario.index');
    }

    public function render()
    {
        return view('livewire.admin.inventario.show')->layout($this->getLayout());
    }
}
