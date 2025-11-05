<?php

namespace App\Livewire\Admin\Matriculas;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\PaymentSchedule;
use App\Models\Matricula;

class CambiarCuotas extends Component
{
    use HasDynamicLayout;


    use WithPagination;

    public $search = '';
    public $nuevo_monto = '';
    public $selectedSchedules = [];
    public $showModal = false;

    protected $rules = [
        'nuevo_monto' => 'required|numeric|min:0.01'
    ];

    public function mount()
    {
        abort_unless(auth()->user()->can('cambiar cuotas matriculas'), 403);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function selectSchedule($scheduleId)
    {
        if (in_array($scheduleId, $this->selectedSchedules)) {
            $this->selectedSchedules = array_diff($this->selectedSchedules, [$scheduleId]);
        } else {
            $this->selectedSchedules[] = $scheduleId;
        }
    }

    public function selectAll()
    {
        $schedules = $this->getSchedulesQuery()->pluck('id')->toArray();
        $this->selectedSchedules = $schedules;
    }

    public function deselectAll()
    {
        $this->selectedSchedules = [];
    }

    public function openModal()
    {
        if (empty($this->selectedSchedules)) {
            session()->flash('error', 'Seleccione al menos una cuota para cambiar el monto.');
            return;
        }
        $this->showModal = true;
    }

    public function cambiarMontos()
    {
        $this->validate();

        try {
            $updated = PaymentSchedule::whereIn('id', $this->selectedSchedules)
                ->where('numero_cuota', '>', 0) // Excluir cuota inicial
                ->where('estado', 'pendiente') // Solo cuotas pendientes
                ->update(['monto' => $this->nuevo_monto]);

            session()->flash('message', "Se actualizaron {$updated} cuotas correctamente.");

            $this->reset(['selectedSchedules', 'nuevo_monto', 'showModal']);
        } catch (\Exception $e) {
            session()->flash('error', 'Error al actualizar las cuotas: ' . $e->getMessage());
        }
    }

    private function getSchedulesQuery()
    {
        $query = PaymentSchedule::with(['matricula.student'])
            ->where('numero_cuota', '>', 0) // Excluir cuota inicial
            ->where('estado', 'pendiente') // Solo cuotas pendientes
            ->whereHas('matricula', function($q) {
                $q->whereHas('student'); // Asegurar que exista el estudiante
            });

        if (!auth()->user()->hasRole('Super Administrador')) {
            $query->where('empresa_id', auth()->user()->empresa_id)
                  ->where('sucursal_id', auth()->user()->sucursal_id);
        }

        if ($this->search) {
            $query->whereHas('matricula.student', function($q) {
                $q->where('nombres', 'like', '%' . $this->search . '%')
                  ->orWhere('apellidos', 'like', '%' . $this->search . '%')
                  ->orWhere('codigo', 'like', '%' . $this->search . '%');
            });
        }

        return $query->orderBy('fecha_vencimiento');
    }

    public function render()
    {
        $schedules = $this->getSchedulesQuery()->paginate(10);

        return view('livewire.admin.matriculas.cambiar-cuotas', [
            'schedules' => $schedules
        ])->layout($this->getLayout());
    }
}
