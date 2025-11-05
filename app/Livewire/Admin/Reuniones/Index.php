<?php

namespace App\Livewire\Admin\Reuniones;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Reunion;
use App\Models\User;

class Index extends Component
{
    use HasDynamicLayout;


    public $showModal = false;
    public $editingId = null;

    // Propiedades del formulario
    public $titulo = '';
    public $descripcion = '';
    public $fecha_inicio = '';
    public $fecha_fin = '';
    public $ubicacion = '';
    public $color = '#007bff';
    public $participantes = [];

    protected $rules = [
        'titulo' => 'required|string|max:255',
        'descripcion' => 'nullable|string',
        'fecha_inicio' => 'required|date',
        'fecha_fin' => 'required|date|after:fecha_inicio',
        'ubicacion' => 'nullable|string|max:255',
        'color' => 'required|string|size:7',
        'participantes' => 'nullable|array'
    ];

    public function mount()
    {
        abort_unless(auth()->user()->can('access reuniones'), 403);
    }

    public function openModal($reunionId = null)
    {
        $this->resetForm();
        $this->editingId = $reunionId;

        if ($reunionId) {
            $reunion = Reunion::find($reunionId);
            if ($reunion) {
                $this->titulo = $reunion->titulo;
                $this->descripcion = $reunion->descripcion;
                $this->fecha_inicio = $reunion->fecha_inicio->format('Y-m-d\TH:i');
                $this->fecha_fin = $reunion->fecha_fin->format('Y-m-d\TH:i');
                $this->ubicacion = $reunion->ubicacion;
                $this->color = $reunion->color;
                $this->participantes = $reunion->participantes ?? [];
            }
        }

        $this->showModal = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'titulo' => $this->titulo,
            'descripcion' => $this->descripcion,
            'fecha_inicio' => $this->fecha_inicio,
            'fecha_fin' => $this->fecha_fin,
            'ubicacion' => $this->ubicacion,
            'color' => $this->color,
            'participantes' => $this->participantes,
            'empresa_id' => auth()->user()->empresa_id,
            'sucursal_id' => auth()->user()->sucursal_id,
        ];

        if ($this->editingId) {
            Reunion::find($this->editingId)->update($data);
            session()->flash('message', 'Reunión actualizada correctamente.');
        } else {
            $data['creado_por'] = auth()->id();
            Reunion::create($data);
            session()->flash('message', 'Reunión creada correctamente.');
        }

        $this->resetForm();
        $this->showModal = false;
        $this->dispatch('reunion-saved');
    }

    public function saveEvent($eventData)
    {
        $data = [
            'titulo' => $eventData['title'],
            'descripcion' => $eventData['extendedProps']['description'] ?? '',
            'fecha_inicio' => $eventData['start'],
            'fecha_fin' => $eventData['end'],
            'ubicacion' => $eventData['extendedProps']['location'] ?? '',
            'color' => $this->getCategoryColor($eventData['extendedProps']['calendar']),
            'participantes' => $eventData['extendedProps']['guests'] ?? [],
            'empresa_id' => auth()->user()->empresa_id,
            'sucursal_id' => auth()->user()->sucursal_id,
            'creado_por' => auth()->id(),
        ];

        Reunion::create($data);
        session()->flash('message', 'Evento creado correctamente.');
        $this->dispatch('reunion-saved');
    }

    public function updateEvent($eventData)
    {
        $reunion = Reunion::find($eventData['id']);
        if ($reunion && $reunion->creado_por === auth()->id()) {
            $data = [
                'titulo' => $eventData['title'],
                'descripcion' => $eventData['extendedProps']['description'] ?? '',
                'fecha_inicio' => $eventData['start'],
                'fecha_fin' => $eventData['end'],
                'ubicacion' => $eventData['extendedProps']['location'] ?? '',
                'color' => $this->getCategoryColor($eventData['extendedProps']['calendar']),
                'participantes' => $eventData['extendedProps']['guests'] ?? [],
            ];

            $reunion->update($data);
            session()->flash('message', 'Evento actualizado correctamente.');
            $this->dispatch('reunion-saved');
        }
    }

    public function deleteEvent($eventId)
    {
        $reunion = Reunion::find($eventId);
        if ($reunion && $reunion->creado_por === auth()->id()) {
            $reunion->delete();
            session()->flash('message', 'Evento eliminado correctamente.');
            $this->dispatch('reunion-deleted');
        }
    }

    private function getCategoryColor($category)
    {
        $colors = [
            'Business' => '#696cff',
            'Personal' => '#ff3e1d',
            'Family' => '#ffab00',
            'Holiday' => '#71dd37',
            'ETC' => '#03c3ec'
        ];

        return $colors[$category] ?? '#696cff';
    }

    public function delete($reunionId)
    {
        $reunion = Reunion::find($reunionId);
        if ($reunion && $reunion->creado_por === auth()->id()) {
            $reunion->delete();
            session()->flash('message', 'Reunión eliminada correctamente.');
            $this->dispatch('reunion-deleted');
        }
    }

    public function resetForm()
    {
        $this->titulo = '';
        $this->descripcion = '';
        $this->fecha_inicio = '';
        $this->fecha_fin = '';
        $this->ubicacion = '';
        $this->color = '#007bff';
        $this->participantes = [];
        $this->editingId = null;
    }

    public function getReunionesProperty()
    {
        $query = Reunion::with('creador');

        if (!auth()->user()->hasRole('Super Administrador')) {
            $query->where('empresa_id', auth()->user()->empresa_id)
                  ->where('sucursal_id', auth()->user()->sucursal_id);
        }

        return $query->orderBy('fecha_inicio')->get();
    }

    public function getUsuariosProperty()
    {
        $query = User::select('id', 'name', 'email');

        if (!auth()->user()->hasRole('Super Administrador')) {
            $query->where('empresa_id', auth()->user()->empresa_id)
                  ->where('sucursal_id', auth()->user()->sucursal_id);
        }

        return $query->get();
    }

    public function render()
    {
        return view('livewire.admin.reuniones.index')->layout($this->getLayout());
    }
}



