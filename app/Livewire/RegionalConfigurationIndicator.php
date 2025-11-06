<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\RegionalConfigurationService;

class RegionalConfigurationIndicator extends Component
{
    public $currentConfig = null;
    public $showDetails = false;

    protected $listeners = ['regional-configuration-updated' => 'updateConfiguration'];

    public function mount()
    {
        $config = RegionalConfigurationService::getCurrentConfiguration();
        $this->moneda = $config['currency'] ?? 'USD';
        $this->zona_horaria = $config['timezone'] ?? 'UTC';
        $this->formato_fecha = $config['date_format'] ?? 'd/m/Y';
        $this->formato_moneda = $config['currency_format'] ?? '#,##0.00';
        $this->simbolo_moneda = $config['currency_symbol'] ?? '$';
        $this->idioma = $config['locale'] ?? 'es';
    }

    public function updateConfiguration($data = null)
    {
        $this->currentConfig = RegionalConfigurationService::getCurrentConfiguration();

        // Si hay datos del evento, actualizar con esos datos
        if ($data) {
            $this->currentConfig = [
                'currency' => $data['moneda'] ?? 'USD',
                'currency_symbol' => $data['simbolo_moneda'] ?? '$',
                'timezone' => $data['zona_horaria'] ?? 'UTC',
                'date_format' => $data['formato_fecha'] ?? 'd/m/Y',
                'idioma' => $data['idioma'] ?? 'es',
            ];
        }
    }

    public function toggleDetails()
    {
        $this->showDetails = !$this->showDetails;
    }

    public function render()
    {
        return view('livewire.regional-configuration-indicator');
    }
}
