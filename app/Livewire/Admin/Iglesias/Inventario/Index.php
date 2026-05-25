<?php

namespace App\Livewire\Admin\Iglesias\Inventario;

use App\Models\InventarioIglesia;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Iglesia;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use App\Traits\HasDynamicLayout;
use App\Traits\Exportable;

class Index extends Component
{
    use WithPagination;
    use HasDynamicLayout;
    use Exportable;

    public int $iglesiaId;

    public string $search = '';
    public string $category = '';
    public string $condition = '';
    public string $sortBy = 'fecha_adquisicion';
    public string $sortDirection = 'desc';

    public int $perPage = 10;

    // Estadísticas
    public array $stats = [];

    // Categorías predefinidas para inventario de iglesias
    public const CATEGORIAS_INVENTARIO = [
        'Mobiliario' => 'Mobiliario',
        'Equipos de Sonido' => 'Equipos de Sonido',
        'Equipos de Video' => 'Equipos de Video',
        'Instrumentos Musicales' => 'Instrumentos Musicales',
        'Equipos Eléctricos' => 'Equipos Eléctricos',
        'Decoración' => 'Decoración',
        'Utensilios de Cocina' => 'Utensilios de Cocina',
        'Herramientas' => 'Herramientas',
        'Vestimenta Litúrgica' => 'Vestimenta Litúrgica',
        'Libros y Material Educativo' => 'Libros y Material Educativo',
        'Equipos de Limpieza' => 'Equipos de Limpieza',
        'Aire Acondicionado' => 'Aire Acondicionado',
        'Computadoras y Tecnología' => 'Computadoras y Tecnología',
        'Otros' => 'Otros',
    ];

    // Tipos de bienes para clasificación contable
    public const TIPOS_BIENES = [
        'Inmuebles' => 'Inmuebles',
        'Equipos' => 'Equipos',
        'Mobiliario' => 'Mobiliario',
        'Enseres' => 'Enseres',
    ];

    public function mount(int $iglesiaId): void
    {
        $this->iglesiaId = $iglesiaId;
        $this->loadStats();
    }

    protected function loadStats(): void
    {
        $query = InventarioIglesia::where('iglesia_id', $this->iglesiaId);

        $this->stats = [
            'total_items' => (clone $query)->count(),
            'total_valor' => (clone $query)->sum('valor') ?? 0,
            'categorias_unicas' => (clone $query)->distinct('categoria')->whereNotNull('categoria')->count('categoria'),
            'items_nuevos' => (clone $query)->where('condicion', 'like', '%nuevo%')->count(),
        ];
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingCategory(): void
    {
        $this->resetPage();
    }

    public function updatingCondition(): void
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->category = '';
        $this->condition = '';
        $this->sortBy = 'fecha_adquisicion';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    public function setSortBy(string $column): void
    {
        if ($this->sortBy === $column) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $column;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function deleteItem(int $itemId): void
    {
        $item = InventarioIglesia::where('iglesia_id', $this->iglesiaId)
            ->where('id', $itemId)
            ->first();

        if (! $item) {
            session()->flash('error', 'Ítem no encontrado.');
            return;
        }

        $itemName = $item->nombre;
        $item->delete();

        $this->loadStats();
        session()->flash('message', "Ítem '{$itemName}' eliminado correctamente.");
    }

    protected function getExportQuery()
    {
        return InventarioIglesia::query()
            ->where('iglesia_id', $this->iglesiaId)
            ->when($this->search !== '', function ($query) {
                $query->where(function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                        ->orWhere('categoria', 'like', '%' . $this->search . '%')
                        ->orWhere('condicion', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->category !== '', function ($query) {
                $query->where('categoria', 'like', '%' . $this->category . '%');
            })
            ->when($this->condition !== '', function ($query) {
                $query->where('condicion', 'like', '%' . $this->condition . '%');
            })
            ->orderBy($this->sortBy, $this->sortDirection);
    }

    protected function getExportHeaders(): array
    {
        return [
            'ID',
            'Nombre',
            'Categoría',
            'Cantidad',
            'Valor',
            'Tipo de Bien',
            'Condición',
            'Fecha Adquisición',
            'Descripción'
        ];
    }

    public function getExportFileName(): string
    {
        $iglesia = Iglesia::find($this->iglesiaId);
        return 'inventario_' . ($iglesia ? Str::slug($iglesia->nombre) : 'extension') . '_' . now()->format('Y-m-d');
    }

    protected function formatExportRow($item): array
    {
        return [
            $item->id,
            $item->nombre,
            $item->categoria ?? '-',
            $item->cantidad,
            $item->valor ?? 0,
            $item->tipo_bien ?? '-',
            $item->condicion ?? '-',
            $item->fecha_adquisicion ? \Carbon\Carbon::parse($item->fecha_adquisicion)->format('d/m/Y') : '-',
            $item->descripcion ?? '-'
        ];
    }

    public function render()
    {
        $query = InventarioIglesia::query()
            ->where('iglesia_id', $this->iglesiaId);

        if ($this->search !== '') {
            $query->where(function ($q) {
                $q->where('nombre', 'like', '%' . $this->search . '%')
                    ->orWhere('categoria', 'like', '%' . $this->search . '%')
                    ->orWhere('condicion', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->category !== '') {
            $query->where('categoria', 'like', '%' . $this->category . '%');
        }

        if ($this->condition !== '') {
            $query->where('condicion', 'like', '%' . $this->condition . '%');
        }

        // Obtener condiciones únicas para los filtros
        $conditions = InventarioIglesia::where('iglesia_id', $this->iglesiaId)
            ->distinct('condicion')
            ->whereNotNull('condicion')
            ->pluck('condicion')
            ->filter()
            ->values()
            ->toArray();

        $items = $query
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate($this->perPage);

        return view('livewire.admin.iglesias.inventario.index', [
            'items' => $items,
            'iglesiaId' => $this->iglesiaId,
            'categories' => self::CATEGORIAS_INVENTARIO,
            'conditions' => $conditions,
        ])->layout($this->getLayout());
    }
}

