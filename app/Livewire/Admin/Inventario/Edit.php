<?php

namespace App\Livewire\Admin\Inventario;

use App\Models\InventarioIglesia;
use App\Models\Iglesia;
use Livewire\Component;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Log;
use App\Traits\HasDynamicLayout;

class Edit extends Component
{
    use HasDynamicLayout;

    public $itemId;
    
    // Datos del formulario (iguales al original)
    public $iglesiaSearch = '';
    public $iglesiasBuscadas = [];
    public $mostrarResultadosIglesia = false;
    public $iglesia_id = '';
    
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
    public bool $loadingRate = false;

    // Categorías predefinidas
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

    // Tipos de bienes
    public const TIPOS_BIENES = [
        'Inmuebles' => 'Inmuebles',
        'Equipos' => 'Equipos',
        'Mobiliario' => 'Mobiliario',
        'Enseres' => 'Enseres',
    ];

    public function mount($id)
    {
        $this->itemId = $id;
        $item = InventarioIglesia::find($id);
        
        if (!$item) {
            session()->flash('error', 'Ítem no encontrado.');
            return redirect()->route('admin.inventario.index');
        }

        $this->iglesia_id = $item->iglesia_id;
        $this->iglesiaSearch = $item->iglesia ? $item->iglesia->nombre : '';
        $this->nombre = $item->nombre;
        $this->descripcion = $item->descripcion;
        $this->categoria = $item->categoria;
        $this->cantidad = $item->cantidad;
        $this->valor = $item->valor;
        $this->numero_factura = $item->numero_factura;
        $this->moneda = $item->moneda ?? 'VES';
        $this->tasa_bcv = $item->tasa_bcv;
        $this->valor_bs = $item->valor_bs;
        $this->fecha_adquisicion = $item->fecha_adquisicion;
        $this->condicion = $item->condicion;
        $this->tipo_bien = $item->tipo_bien;
        $this->notas = $item->notas;
    }

    public function updatedIglesiaSearch($value)
    {
        if (strlen($value) >= 2) {
            $this->iglesiasBuscadas = Iglesia::activas()
                ->where(function($query) use ($value) {
                    $query->where('nombre', 'like', '%' . $value . '%')
                          ->orWhere('direccion', 'like', '%' . $value . '%');
                })
                ->orderBy('nombre')
                ->limit(10)
                ->get();
            $this->mostrarResultadosIglesia = true;
        } else {
            $this->iglesiasBuscadas = [];
            $this->mostrarResultadosIglesia = false;
        }
    }

    public function seleccionarIglesia($iglesiaId)
    {
        $iglesia = Iglesia::find($iglesiaId);
        if ($iglesia) {
            $this->iglesia_id = $iglesiaId;
            $this->iglesiaSearch = $iglesia->nombre;
            $this->mostrarResultadosIglesia = false;
        }
    }

    public function limpiarBusquedaIglesia()
    {
        $this->iglesia_id = '';
        $this->iglesiaSearch = '';
        $this->iglesiasBuscadas = [];
        $this->mostrarResultadosIglesia = false;
    }

    #[On('closeIglesiaDropdown')]
    public function closeIglesiaDropdown()
    {
        $this->mostrarResultadosIglesia = false;
    }

    public function cargarTasaDelDia(): void
    {
        $tasa = \App\Models\ExchangeRate::getOrCreateTodayRate();
        if ($tasa) {
            $this->tasa_bcv = number_format($tasa, 4, '.', '');
            session()->flash('message', 'Tasa BCV del día cargada automáticamente: Bs ' . number_format($tasa, 2, ',', '.'));
        } else {
            session()->flash('warning', 'No se pudo obtener la tasa BCV. Por favor ingrésela manualmente.');
        }
    }

    public function updatedFechaAdquisicion(): void
    {
        // Cuando cambia la fecha, obtener la tasa BCV de ese día
        if ($this->fecha_adquisicion) {
            $this->loadingRate = true;

            try {
                $tasa = \App\Models\ExchangeRate::getRateByDate($this->fecha_adquisicion);
                if ($tasa) {
                    $this->tasa_bcv = number_format($tasa, 4, '.', '');
                    session()->flash('message', "Tasa BCV del {$this->fecha_adquisicion} cargada: Bs " . number_format($tasa, 2, ',', '.'));
                    // Recalcular valor_bs si ya hay valor en USD
                    if ($this->valor) {
                        $this->calcularValorBs();
                    }
                } else {
                    session()->flash('warning', 'No se pudo obtener la tasa BCV para esta fecha. Por favor ingrésela manualmente.');
                }
            } finally {
                $this->loadingRate = false;
            }
        }
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

    public function update()
    {
        $this->validate([
            'iglesia_id' => 'required|exists:iglesias,id',
            'nombre' => ['required', 'string', 'max:255'],
            'descripcion' => ['nullable', 'string'],
            'categoria' => ['nullable', 'string', 'max:255'],
            'cantidad' => ['required', 'integer', 'min:0'],
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

        $item = InventarioIglesia::find($this->itemId);
        
        if (!$item) {
            session()->flash('error', 'Ítem no encontrado.');
            return redirect()->route('admin.inventario.index');
        }

        $item->update([
            'iglesia_id' => $this->iglesia_id,
            'nombre' => $this->nombre,
            'descripcion' => $this->descripcion,
            'categoria' => $this->categoria,
            'cantidad' => $this->cantidad,
            'valor' => $this->valor !== null && $this->valor !== '' ? (string) $this->valor : null,
            'numero_factura' => $this->numero_factura,
            'moneda' => $this->moneda,
            'tasa_bcv' => $this->tasa_bcv !== null && $this->tasa_bcv !== '' ? (string) $this->tasa_bcv : null,
            'valor_bs' => $this->valor_bs !== null && $this->valor_bs !== '' ? (string) $this->valor_bs : null,
            'fecha_adquisicion' => $this->fecha_adquisicion,
            'condicion' => $this->condicion,
            'tipo_bien' => $this->tipo_bien,
            'notas' => $this->notas,
        ]);

        session()->flash('message', 'Ítem actualizado exitosamente.');
        return redirect()->route('admin.inventario.index');
    }

    public function render()
    {
        return view('livewire.admin.inventario.edit')->layout($this->getLayout());
    }
}
