<?php

namespace App\Traits;

use App\Models\Pais;
use App\Services\RegionalConfigurationService;

trait HasRegionalConfiguration
{
    // Propiedades de configuración regional
    public $moneda_principal = '';
    public $zona_horaria = '';
    public $formato_fecha = '';
    public $formato_moneda = '';
    public $simbolo_moneda = '';
    public $idioma_principal = '';

    /**
     * Método que se ejecuta cuando cambia el país seleccionado
     */
    public function updatedPaisId($value)
    {
        if ($value) {
            $pais = Pais::find($value);
            if ($pais) {
                $this->moneda_principal = $pais->moneda_principal ?? 'USD';
                $this->zona_horaria = $pais->zona_horaria ?? 'UTC';
                $this->formato_fecha = $pais->formato_fecha ?? 'd/m/Y';
                $this->formato_moneda = $pais->formato_moneda ?? '#,##0.00';
                $this->simbolo_moneda = $pais->simbolo_moneda ?? '$';
                $this->idioma_principal = $pais->idioma_principal ?? 'es';

                // Emitir evento para actualizar la configuración regional
                $this->dispatch('regional-configuration-updated', [
                    'moneda' => $this->moneda_principal,
                    'zona_horaria' => $this->zona_horaria,
                    'formato_fecha' => $this->formato_fecha,
                    'formato_moneda' => $this->formato_moneda,
                    'simbolo_moneda' => $this->simbolo_moneda,
                    'idioma' => $this->idioma_principal,
                ]);
            }
        } else {
            $this->resetRegionalConfiguration();
        }
    }

    /**
     * Resetear configuración regional
     */
    protected function resetRegionalConfiguration()
    {
        $this->moneda_principal = '';
        $this->zona_horaria = '';
        $this->formato_fecha = '';
        $this->formato_moneda = '';
        $this->simbolo_moneda = '';
        $this->idioma_principal = '';
    }

    /**
     * Inicializar configuración regional desde un país
     */
    protected function initializeRegionalConfiguration($pais)
    {
        if ($pais) {
            $this->moneda_principal = $pais->moneda_principal ?? 'USD';
            $this->zona_horaria = $pais->zona_horaria ?? 'UTC';
            $this->formato_fecha = $pais->formato_fecha ?? 'd/m/Y';
            $this->formato_moneda = $pais->formato_moneda ?? '#,##0.00';
            $this->simbolo_moneda = $pais->simbolo_moneda ?? '$';
            $this->idioma_principal = $pais->idioma_principal ?? 'es';
        } else {
            $this->resetRegionalConfiguration();
        }
    }

    /**
     * Aplicar configuración regional para una empresa
     */
    protected function applyRegionalConfiguration($empresa)
    {
        if ($empresa && $empresa->pais) {
            RegionalConfigurationService::setRegionalConfiguration($empresa);

            // Guardar configuración en sesión
            session([
                'current_empresa_id' => $empresa->id,
                'current_pais_id' => $empresa->pais_id,
                'regional_configuration' => RegionalConfigurationService::getCurrentConfiguration()
            ]);

            return true;
        }

        return false;
    }

    /**
     * Obtener configuración regional actual
     */
    protected function getCurrentRegionalConfiguration()
    {
        return RegionalConfigurationService::getCurrentConfiguration();
    }
}
