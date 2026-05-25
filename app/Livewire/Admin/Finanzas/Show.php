<?php

namespace App\Livewire\Admin\Finanzas;

use App\Models\TransaccionFinanciera;
use Livewire\Component;
use App\Traits\HasDynamicLayout;

class Show extends Component
{
    use HasDynamicLayout;

    public $transactionId;
    public $transaction;

    public function mount($id)
    {
        $this->transactionId = $id;
        $this->transaction = TransaccionFinanciera::with(['iglesia', 'asientoContable'])->find($id);
        
        if (!$this->transaction) {
            session()->flash('error', 'Transacción no encontrada.');
            return redirect()->route('admin.finanzas.index');
        }
    }

    public function deleteTransaction()
    {
        $transactionType = $this->transaction->tipo_label;
        $this->transaction->delete();
        
        session()->flash('message', "Transacción '{$transactionType}' eliminada correctamente.");
        return redirect()->route('admin.finanzas.index');
    }

    public function render()
    {
        return view('livewire.admin.finanzas.show')->layout($this->getLayout());
    }
}
