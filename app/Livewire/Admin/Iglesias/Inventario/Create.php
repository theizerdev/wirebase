<?php

namespace App\Livewire\Admin\Iglesias\Inventario;

use App\Models\InventarioIglesia;
use Livewire\Component;
use App\Traits\HasDynamicLayout;
use App\Livewire\Admin\Iglesias\Inventario\Index as InventarioIndex;

class Create extends Component
{
    use HasDynamicLayout;

    public int $iglesiaId;

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

    public function mount($iglesiaId)
    {
        $this->iglesiaId = $iglesiaId;
        // Establecer fecha de hoy por defecto
        $this->fecha_adquisicion = now()->format('Y-m-d');
        // Cargar tasa del día
        $this->cargarTasaDelDia();
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

    public function getCategoriasProperty(): array
    {
        return InventarioIndex::CATEGORIAS_INVENTARIO;
    }

    protected function rules(): array
    {
        return [
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
        ];
    }

    public function save()
    {
        $this->validate();

        $item = new InventarioIglesia();
        $item->iglesia_id = $this->iglesiaId;
        $item->nombre = $this->nombre;
        $item->descripcion = $this->descripcion;
        $item->categoria = $this->categoria;
        $item->cantidad = $this->cantidad;
        $item->valor = $this->valor !== null && $this->valor !== '' ? (string) $this->valor : null;
        $item->numero_factura = $this->numero_factura;
        $item->moneda = $this->moneda;
        $item->tasa_bcv = $this->tasa_bcv !== null && $this->tasa_bcv !== '' ? (string) $this->tasa_bcv : null;
        $item->valor_bs = $this->valor_bs !== null && $this->valor_bs !== '' ? (string) $this->valor_bs : null;
        $item->fecha_adquisicion = $this->fecha_adquisicion;
        $item->condicion = $this->condicion;
        $item->tipo_bien = $this->tipo_bien;
        $item->notas = $this->notas;
        $item->usuario_registro_id = auth()->id();

        $item->save();

        session()->flash('message', 'Ítem de inventario creado correctamente.');

        return redirect()->route('admin.iglesias.index', $this->iglesiaId);
    }

    public function render()
    {
        return view('livewire.admin.iglesias.inventario.create')
            ->layout($this->getLayout())
            ->with('iglesiaId', $this->iglesiaId);
    }
}

