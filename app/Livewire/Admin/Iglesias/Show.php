<?php

namespace App\Livewire\Admin\Iglesias;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\Iglesia;
use App\Models\InventarioIglesia;

class Show extends Component
{
    use HasDynamicLayout;

    public Iglesia $iglesia;

    public function mount(Iglesia $iglesia)
    {
        $this->iglesia = $iglesia->load(['pastor', 'estado', 'ciudad', 'municipio', 'parroquia', 'usuarioRegistro']);
    }

    protected function loadStats(): array
    {
        $query = InventarioIglesia::where('iglesia_id', $this->iglesia->id);

        return [
            'total_items' => (clone $query)->count(),
            'total_valor' => (clone $query)->sum('valor') ?? 0,
            'categorias_unicas' => (clone $query)->distinct('categoria')->whereNotNull('categoria')->count('categoria'),
            'items_nuevos' => (clone $query)->where('condicion', 'like', '%nuevo%')->count(),
        ];
    }

    public function render()
    {
        return $this->renderWithLayout('livewire.admin.iglesias.show', [
            'iglesia' => $this->iglesia,
            'stats' => $this->loadStats()
        ], [
            'title' => 'Detalles de la Extensión',
            'description' => 'Información detallada de la extensión'
        ]);
    }
}
