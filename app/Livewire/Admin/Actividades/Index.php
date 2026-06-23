<?php

namespace App\Livewire\Admin\Actividades;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Actividad;
use App\Models\Pastor;
use App\Traits\HasDynamicLayout;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination, HasDynamicLayout;

    public $search = '';
    public $sortBy = 'fecha_inicio';
    public $sortDirection = 'desc';
    public $perPage = 10;
    
    // Propiedades del formulario
    public $actividadId;
    public $nombre;
    public $tipo_actividad;
    public $fecha_inicio;
    public $fecha_fin;
    public $estado = 'Activo';
    public $zona;
    public $distrito;
    public $lugar;
    public $nota;
    public $coordinador_id;

    public $isOpen = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'sortBy' => ['except' => 'fecha_inicio'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 10]
    ];

    protected $rules = [
        'nombre' => 'required|string|max:255',
        'tipo_actividad' => 'required|string|max:255',
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'required|date|after_or_equal:fecha_inicio',
        'estado' => 'required|in:Activo,Finalizado,Suspendido',
        'zona' => 'nullable|integer',
        'distrito' => 'nullable|integer',
        'lugar' => 'nullable|string|max:255',
        'nota' => 'nullable|string',
        'coordinador_id' => 'nullable|exists:pastores,id',
    ];

    public function mount()
    {
        // En un futuro se podría requerir 'access actividades'
        // if (!Auth::user()->can('access actividades')) {
        //     abort(403, 'No tienes permiso para acceder a actividades.');
        // }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function create()
    {
        $this->resetInputFields();
        $this->isOpen = true;
        $this->dispatch('open-modal', 'actividad-modal');
    }

    public function store()
    {
        $this->validate();

        Actividad::create([
            'nombre' => $this->nombre,
            'tipo_actividad' => $this->tipo_actividad,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'estado' => $this->estado,
            'zona' => $this->zona,
            'distrito' => $this->distrito,
            'lugar' => $this->lugar,
            'nota' => $this->nota,
            'coordinador_id' => $this->coordinador_id,
        ]);

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Actividad creada exitosamente.'
        ]);

        $this->closeModal();
    }

    public function edit($id)
    {
        $actividad = Actividad::findOrFail($id);
        $this->actividadId = $id;
        $this->nombre = $actividad->nombre;
        $this->tipo_actividad = $actividad->tipo_actividad;
        // Asumiendo que vienen como objetos Carbon, ajustamos el formato para datetime-local
        $this->fecha_inicio = $actividad->fecha_inicio ? $actividad->fecha_inicio->format('Y-m-d\TH:i') : null;
        $this->fecha_fin = $actividad->fecha_fin ? $actividad->fecha_fin->format('Y-m-d\TH:i') : null;
        $this->estado = $actividad->estado;
        $this->zona = $actividad->zona;
        $this->distrito = $actividad->distrito;
        $this->lugar = $actividad->lugar;
        $this->nota = $actividad->nota;
        $this->coordinador_id = $actividad->coordinador_id;
        
        $this->isOpen = true;
        $this->dispatch('open-modal', 'actividad-modal');
    }

    public function update()
    {
        $this->validate();

        if ($this->actividadId) {
            $actividad = Actividad::findOrFail($this->actividadId);
            $actividad->update([
                'nombre' => $this->nombre,
                'tipo_actividad' => $this->tipo_actividad,
                'fecha_inicio' => $this->fecha_inicio,
                'fecha_fin' => $this->fecha_fin,
                'estado' => $this->estado,
                'zona' => $this->zona,
                'distrito' => $this->distrito,
                'lugar' => $this->lugar,
                'nota' => $this->nota,
                'coordinador_id' => $this->coordinador_id,
            ]);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Actividad actualizada exitosamente.'
            ]);

            $this->closeModal();
        }
    }

    public function deleteActividad($id)
    {
        Actividad::findOrFail($id)->delete();
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Actividad eliminada exitosamente.'
        ]);
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetInputFields();
        $this->dispatch('close-modal', 'actividad-modal');
    }

    private function resetInputFields()
    {
        $this->reset([
            'actividadId', 'nombre', 'tipo_actividad', 'fecha_inicio', 
            'fecha_fin', 'estado', 'zona', 'distrito', 'lugar', 'nota', 'coordinador_id'
        ]);
        $this->estado = 'Activo';
    }

    public function render()
    {
        $query = Actividad::with('coordinador');

        if ($this->search) {
            $query->where('nombre', 'like', '%' . $this->search . '%')
                  ->orWhere('tipo_actividad', 'like', '%' . $this->search . '%')
                  ->orWhere('lugar', 'like', '%' . $this->search . '%');
        }

        $actividades = $query->orderBy($this->sortBy, $this->sortDirection)
                             ->paginate($this->perPage);

        $pastores = Pastor::activos()->get();

        return $this->renderWithLayout('livewire.admin.actividades.index', compact('actividades', 'pastores'), [
            'title' => 'Actividades',
            'description' => 'Gestión de Actividades',
            'breadcrumb' => [
                'admin.dashboard' => 'Dashboard',
                'admin.actividades.index' => 'Actividades'
            ]
        ]);
    }
}
