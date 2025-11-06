<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Pais extends Model
{
    use HasFactory, LogsActivity;

    protected $table = 'pais';

    protected $fillable = [
        'nombre',
        'codigo_iso2',
        'codigo_iso3',
        'codigo_telefonico',
        'moneda_principal',
        'idioma_principal',
        'continente',
        'zona_horaria',
        'formato_fecha',
        'formato_moneda',
        'impuesto_predeterminado',
        'separador_miles',
        'separador_decimales',
        'decimales_moneda',
        'activo'
    ];

    protected $casts = [
        'activo' => 'boolean',
        'impuesto_predeterminado' => 'decimal:2',
        'decimales_moneda' => 'integer'
    ];

    public function empresas()
    {
        return $this->hasMany(Empresa::class, 'pais_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'nombre',
                'codigo_iso2',
                'codigo_iso3',
                'codigo_telefonico',
                'moneda_principal',
                'idioma_principal',
                'continente',
                'activo'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Formatear un monto según la configuración del país
     */
    public function formatearMonto($monto)
    {
        return number_format(
            $monto,
            $this->decimales_moneda,
            $this->separador_decimales,
            $this->separador_miles
        );
    }

    /**
     * Obtener el símbolo de la moneda principal
     */
    public function getSimboloMonedaAttribute()
    {
        return match($this->moneda_principal) {
            'USD' => '$',
            'EUR' => '€',
            'GBP' => '£',
            'VES' => 'Bs.',
            'COP' => '$',
            'ARS' => '$',
            'BRL' => 'R$',
            'MXN' => '$',
            default => '$'
        };
    }
}
