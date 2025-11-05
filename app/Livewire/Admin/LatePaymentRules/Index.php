<?php

namespace App\Livewire\Admin\LatePaymentRules;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\LatePaymentRule;

class Index extends Component
{
    use HasDynamicLayout;


    public $nombre;
    public $tipo = 'porcentaje';
    public $valor;
    public $dias_gracia = 0;
    public $editingId = null;

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'tipo' => 'required|in:porcentaje,monto_fijo',
        'valor' => 'required|numeric|min:0',
        'dias_gracia' => 'required|integer|min:0'
    ];

    public function save()
    {
        $this->validate();

        // Desactivar reglas existentes
        LatePaymentRule::where('empresa_id', auth()->user()->empresa_id)
            ->where('sucursal_id', auth()->user()->sucursal_id)
            ->update(['activo' => false]);

        LatePaymentRule::updateOrCreate(
            ['id' => $this->editingId],
            [
                'nombre' => $this->nombre,
                'tipo' => $this->tipo,
                'valor' => $this->valor,
                'dias_gracia' => $this->dias_gracia,
                'activo' => true,
                'empresa_id' => auth()->user()->empresa_id,
                'sucursal_id' => auth()->user()->sucursal_id
            ]
        );

        $this->reset(['nombre', 'tipo', 'valor', 'dias_gracia', 'editingId']);
        session()->flash('message', 'Regla de morosidad guardada exitosamente.');
    }

    public function edit($id)
    {
        $rule = LatePaymentRule::findOrFail($id);
        $this->editingId = $rule->id;
        $this->nombre = $rule->nombre;
        $this->tipo = $rule->tipo;
        $this->valor = $rule->valor;
        $this->dias_gracia = $rule->dias_gracia;
    }

    public function delete($id)
    {
        LatePaymentRule::findOrFail($id)->delete();
        session()->flash('message', 'Regla eliminada exitosamente.');
    }

    public function render()
    {
        $rules = LatePaymentRule::orderBy('created_at', 'desc')->get();

        return view('livewire.admin.late-payment-rules.index', compact('rules'))->layout($this->getLayout());
    }
}
