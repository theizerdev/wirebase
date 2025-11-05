<?php

namespace App\Livewire\Admin;

use App\Traits\HasDynamicLayout;
use Livewire\Component;
use App\Models\ExchangeRate;
use App\Services\ExchangeRateService;
use Livewire\Attributes\On;

class ExchangeRates extends Component
{
    use HasDynamicLayout;


    public $lastUpdate;

    public function mount()
    {
        abort_unless(auth()->user()->can('view exchange-rates'), 403);
        $this->lastUpdate = now()->format('H:i:s');
    }

    #[On('refresh-rates')]
    public function refreshData()
    {
        $this->lastUpdate = now()->format('H:i:s');
    }

    public function fetchNow()
    {
        try {
            $service = new ExchangeRateService();
            $success = $service->fetchAndStoreRates();

            if ($success) {
                $todayRate = ExchangeRate::getTodayRate();
                session()->flash('success', "Tasa actualizada: USD = {$todayRate->usd_rate} Bs. (Fuente: {$todayRate->source})");
            } else {
                session()->flash('error', 'No se pudo obtener la tasa. Verifique la conexión a internet.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error técnico: ' . $e->getMessage());
        }

        $this->refreshData();
    }

    public function render()
    {
        $todayRate = ExchangeRate::getTodayRate();
        $rates = ExchangeRate::orderBy('created_at', 'desc')->take(7)->get();
        $chartData = ExchangeRate::orderBy('created_at', 'desc')->take(30)->get();

        // Calcular estadísticas para la vista
        $stats = [
            'usd_rate' => $todayRate ? $todayRate->usd_rate : 0,
            'eur_rate' => $todayRate ? $todayRate->eur_rate : 0,
            'date' => $todayRate ? $todayRate->date->format('d/m/Y') : 'N/A',
            'last_fetch' => $todayRate ? $todayRate->fetch_time->format('H:i') : 'N/A',
            'source' => $todayRate ? $todayRate->source : 'N/A'
        ];

        return view('livewire.admin.exchange-rates', [
            'todayRate' => $todayRate,
            'rates' => $rates,
            'chartData' => $chartData,
            'stats' => $stats
        ])->layout($this->getLayout());
    }
}
