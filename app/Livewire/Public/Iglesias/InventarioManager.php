<?php

namespace App\Livewire\Public\Iglesias;

use Livewire\Component;
use App\Models\InventarioIglesia;
use App\Models\Iglesia;
use App\Traits\HasDynamicLayout;

class InventarioManager extends Component
{
    use HasDynamicLayout;

    public Iglesia $iglesia;
    public $mode = 'list'; // 'list', 'create', 'edit'
    public $itemId = null;

    // Estadísticas
    public array $stats = [];

    // Lista de items (para modo list)
    public $items = [];

    // Formulario (para modo create/edit)
    public string $nombre = '';
    public ?string $descripcion = null;
    public ?string $categoria = null;
    public int $cantidad = 1;
    public ?string $valor = null;
    public ?string $numero_factura = null;
    public string $moneda = 'VES';
    public ?string $tasa_bcv = null;
    public ?string $valor_bs = null;
    public ?string $fecha_adquisicion = null;
    public ?string $condicion = null;
    public ?string $tipo_bien = null;
    public ?string $notas = null;

    // Estado de carga
    public bool $loadingRate = false;

    // Constantes
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

    public const TIPOS_BIENES = [
        'Inmuebles' => 'Inmuebles',
        'Equipos' => 'Equipos',
        'Mobiliario' => 'Mobiliario',
        'Enseres' => 'Enseres',
    ];

    public const CONDICIONES = [
        'Nuevo',
        'Como Nuevo',
        'Buen Estado',
        'Usado',
        'Regular',
        'Malo',
        'Para Reparar',
    ];

    public function mount(Iglesia $iglesia)
    {
        $this->iglesia = $iglesia;
        $this->loadStats();
        $this->loadItems();
    }

    protected function loadStats(): void
    {
        $query = InventarioIglesia::where('iglesia_id', $this->iglesia->id);

        $this->stats = [
            'total_items' => (clone $query)->count(),
            'total_valor' => (clone $query)->sum('valor') ?? 0,
            'categorias_unicas' => (clone $query)->distinct('categoria')->whereNotNull('categoria')->count('categoria'),
            'items_nuevos' => (clone $query)->where('condicion', 'like', '%nuevo%')->count(),
        ];
    }

    protected function loadItems(): void
    {
        $this->items = InventarioIglesia::where('iglesia_id', $this->iglesia->id)
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get();
    }

    public function setMode(string $mode, $itemId = null): void
    {
        $this->mode = $mode;
        $this->itemId = $itemId;

        if ($mode === 'edit' && $itemId) {
            $this->loadItemForEdit($itemId);
        } elseif ($mode === 'create') {
            $this->resetForm();
        }
    }

    protected function loadItemForEdit(int $itemId): void
    {
        $item = InventarioIglesia::where('iglesia_id', $this->iglesia->id)
            ->where('id', $itemId)
            ->firstOrFail();

        $this->nombre = $item->nombre;
        $this->descripcion = $item->descripcion;
        $this->categoria = $item->categoria;
        $this->cantidad = (int) $item->cantidad;
        $this->valor = $item->valor !== null ? (string) $item->valor : null;
        $this->numero_factura = $item->numero_factura;
        $this->moneda = $item->moneda ?: 'VES';
        $this->tasa_bcv = $item->tasa_bcv !== null ? (string) $item->tasa_bcv : null;
        $this->valor_bs = $item->valor_bs !== null ? (string) $item->valor_bs : null;
        $this->fecha_adquisicion = $item->fecha_adquisicion ? $item->fecha_adquisicion->format('Y-m-d') : null;
        $this->condicion = $item->condicion;
        $this->tipo_bien = $item->tipo_bien;
        $this->notas = $item->notas;
    }

    protected function resetForm(): void
    {
        $this->nombre = '';
        $this->descripcion = null;
        $this->categoria = null;
        $this->cantidad = 1;
        $this->valor = null;
        $this->numero_factura = null;
        $this->moneda = 'VES';
        $this->tasa_bcv = null;
        $this->valor_bs = null;
        $this->fecha_adquisicion = now()->format('Y-m-d');
        $this->condicion = null;
        $this->tipo_bien = null;
        $this->notas = null;
    }

    public function updatedValor(): void
    {
        $this->calcularValorBs();
    }

    public function updatedTasaBcv(): void
    {
        $this->calcularValorBs();
    }

    protected function calcularValorBs(): void
    {
        if ($this->valor && $this->tasa_bcv) {
            $this->valor_bs = number_format((float)$this->valor * (float)$this->tasa_bcv, 2, '.', '');
        } else {
            $this->valor_bs = null;
        }
    }

    public function cargarTasaDelDia(): void
    {
        $this->loadingRate = true;

        try {
            // Usar la fecha de adquisición si existe, sino usar hoy
            $fecha = $this->fecha_adquisicion ? \Carbon\Carbon::parse($this->fecha_adquisicion) : now();

            // Intentar obtener tasa del modelo ExchangeRate
            $tasa = \App\Models\ExchangeRate::getRateByDate($fecha);

            if ($tasa) {
                $this->tasa_bcv = number_format($tasa, 4, '.', '');
                session()->flash('message', 'Tasa BCV cargada exitosamente para ' . $fecha->format('Y-m-d'));
            } else {
                session()->flash('error', 'No se pudo obtener la tasa BCV. Por favor ingrésela manualmente.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al cargar tasa: ' . $e->getMessage());
        } finally {
            $this->loadingRate = false;
        }
    }

    public function save(): void
    {
        $validated = $this->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'categoria' => ['nullable', 'string', 'max:255'],
            'cantidad' => ['required', 'integer', 'min:1'],
            'valor' => ['nullable', 'numeric', 'min:0'],
            'numero_factura' => ['required', 'string', 'max:100'],
            'moneda' => ['required', 'string', 'max:10'],
            'tasa_bcv' => ['nullable', 'numeric', 'min:0'],
            'valor_bs' => ['nullable', 'numeric', 'min:0'],
            'fecha_adquisicion' => ['nullable', 'date'],
            'condicion' => ['nullable', 'string', 'max:255'],
            'tipo_bien' => ['nullable', 'string', 'max:50'],
            'notas' => ['nullable', 'string'],
        ]);

        if ($this->mode === 'create') {
            $item = new InventarioIglesia();
            $item->iglesia_id = $this->iglesia->id;
            $message = 'Ítem creado correctamente';
        } else {
            $item = InventarioIglesia::where('iglesia_id', $this->iglesia->id)
                ->where('id', $this->itemId)
                ->firstOrFail();
            $message = 'Ítem actualizado correctamente';
        }

        $item->fill([
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'categoria' => $this->categoria,
            'cantidad' => $this->cantidad,
            'valor' => $this->valor,
            'numero_factura' => $this->numero_factura,
            'moneda' => $this->moneda,
            'tasa_bcv' => $this->tasa_bcv,
            'valor_bs' => $this->valor_bs,
            'fecha_adquisicion' => $this->fecha_adquisicion,
            'condicion' => $this->condicion,
            'tipo_bien' => $this->tipo_bien,
            'notas' => $this->notas,
            'usuario_registro_id' => 1,
        ]);

        $item->save();

        session()->flash('message', $message);

        $this->loadStats();
        $this->loadItems();
        $this->setMode('list');
    }

    public function delete(int $itemId): void
    {
        $item = InventarioIglesia::where('iglesia_id', $this->iglesia->id)
            ->where('id', $itemId)
            ->firstOrFail();

        $item->delete();

        session()->flash('message', 'Ítem eliminado correctamente');

        $this->loadStats();
        $this->loadItems();
    }

    public function render()
    {
        return view('livewire.public.iglesias.inventario-manager', [
            'iglesia' => $this->iglesia,
            'stats' => $this->stats,
            'items' => $this->items,
            'categories' => self::CATEGORIAS_INVENTARIO,
            'conditions' => self::CONDICIONES,
            'tipos_bienes' => self::TIPOS_BIENES,
        ])->layout('components.layouts.public-wide', [
            'title' => 'Inventario - ' . $this->iglesia->nombre,
            'description' => 'Gestión de inventario de la iglesia ' . $this->iglesia->nombre,
            'backUrl' => route('public.iglesias.editar', [
                'pastor' => $this->iglesia->pastor->id,
                'iglesia' => $this->iglesia->id
            ])
        ]);
    }
}
