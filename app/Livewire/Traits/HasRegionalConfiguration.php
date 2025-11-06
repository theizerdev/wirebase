<?php

namespace App\Livewire\Traits;

use App\Services\RegionalConfigurationService;
use App\Models\Pais;

trait HasRegionalConfiguration
{
    public $moneda = 'USD';
    public $zona_horaria = 'UTC';
    public $formato_fecha = 'd/m/Y';
    public $formato_moneda = '#,##0.00';
    public $simbolo_moneda = '$';
    public $idioma = 'es';

    /**
     * Inicializar la configuración regional basada en el país
     */
    public function initializeRegionalConfiguration($pais)
    {
        if ($pais) {
            $this->moneda = $pais->moneda ?? 'USD';
            $this->zona_horaria = $pais->zona_horaria ?? 'UTC';
            $this->formato_fecha = $pais->formato_fecha ?? 'd/m/Y';
            $this->formato_moneda = $pais->formato_moneda ?? '#,##0.00';
            $this->simbolo_moneda = $pais->simbolo_moneda ?? '$';
            $this->idioma = $pais->idioma ?? 'es';
        }
    }

    /**
     * Actualizar configuración cuando cambia el país
     */
    public function updatedPaisId($value)
    {
        if ($value) {
            $pais = Pais::find($value);
            if ($pais) {
                $this->moneda = $pais->moneda ?? 'USD';
                $this->zona_horaria = $pais->zona_horaria ?? 'UTC';
                $this->formato_fecha = $pais->formato_fecha ?? 'd/m/Y';
                $this->formato_moneda = $pais->formato_moneda ?? '#,##0.00';
                $this->simbolo_moneda = $pais->simbolo_moneda ?? '$';
                $this->idioma = $pais->idioma ?? 'es';

                $this->dispatch('regional-configuration-updated', [
                    'currency' => $this->moneda,
                    'timezone' => $this->zona_horaria,
                    'date_format' => $this->formato_fecha,
                    'currency_format' => $this->formato_moneda,
                    'currency_symbol' => $this->simbolo_moneda,
                    'locale' => $this->idioma,
                ]);
            }
        } else {
            $this->resetRegionalConfiguration();
        }
    }

    /**
     * Restablecer configuración regional a valores por defecto
     */
    public function resetRegionalConfiguration()
    {
        $this->moneda = 'USD';
        $this->zona_horaria = 'UTC';
        $this->formato_fecha = 'd/m/Y';
        $this->formato_moneda = '#,##0.00';
        $this->simbolo_moneda = '$';
        $this->idioma = 'es';

        $this->dispatch('regional-configuration-updated', [
            'currency' => $this->moneda,
            'timezone' => $this->zona_horaria,
            'date_format' => $this->formato_fecha,
            'currency_format' => $this->formato_moneda,
            'currency_symbol' => $this->simbolo_moneda,
            'locale' => $this->idioma,
        ]);
    }

    /**
     * Aplicar configuración regional a una empresa
     */
    public function applyRegionalConfigurationToEmpresa($empresa)
    {
        if ($empresa) {
            $empresa->moneda = $this->moneda;
            $empresa->zona_horaria = $this->zona_horaria;
            $empresa->formato_fecha = $this->formato_fecha;
            $empresa->formato_moneda = $this->formato_moneda;
            $empresa->simbolo_moneda = $this->simbolo_moneda;
            $empresa->idioma = $this->idioma;

            RegionalConfigurationService::setRegionalConfiguration($empresa);

            $this->dispatch('regional-configuration-updated', [
                'currency' => $this->moneda,
                'timezone' => $this->zona_horaria,
                'date_format' => $this->formato_fecha,
                'currency_format' => $this->formato_moneda,
                'currency_symbol' => $this->simbolo_moneda,
                'locale' => $this->idioma,
            ]);
        }
    }
}
