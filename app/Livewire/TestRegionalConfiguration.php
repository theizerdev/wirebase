<?php

namespace App\Livewire;

use Livewire\Component;
use App\Services\RegionalConfigurationService;
use App\Models\Empresa;
use App\Models\Pais;
use App\Traits\HasDynamicLayout;

class TestRegionalConfiguration extends Component
{
    use HasDynamicLayout;

    public $empresas = [];
    public $selectedEmpresaId = null;
    public $testAmount = 1234.56;
    public $testDate;
    public $currentConfig = [];

    public function mount()
    {
        $this->empresas = Empresa::with('pais')->get();
        $this->testDate = now();
        $this->currentConfig = RegionalConfigurationService::getCurrentConfiguration();
    }

    public function selectEmpresa($empresaId)
    {
        $this->selectedEmpresaId = $empresaId;

        if ($empresaId) {
            $empresa = Empresa::with('pais')->find($empresaId);
            if ($empresa) {
                RegionalConfigurationService::setRegionalConfiguration($empresa);
                $this->currentConfig = RegionalConfigurationService::getCurrentConfiguration();

                session()->flash('message', 'Configuración regional actualizada para: ' . $empresa->razon_social);
            }
        }
    }

    public function render()
    {
        return $this->renderWithLayout('livewire.test-regional-configuration', [
            'formattedAmount' => RegionalConfigurationService::formatMoney($this->testAmount),
            'formattedDate' => RegionalConfigurationService::formatDate($this->testDate),
        ], [
            'title' => 'Prueba de Configuración Regional',
            'description' => 'Prueba y configuración de formatos regionales por empresa',
            'breadcrumb' => [
                'admin.dashboard' => 'Dashboard',
                'test.regional-configuration' => 'Configuración Regional'
            ]
        ]);
    }
}
