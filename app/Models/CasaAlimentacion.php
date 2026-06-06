<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\HasSpanishActivityLog;

class CasaAlimentacion extends Model
{
    use HasFactory, LogsActivity, HasSpanishActivityLog;

    protected $table = 'casas_alimentacion';

    protected $fillable = [
        'fecha',
        'codigo',
        'estado_id',
        'municipio_id',
        'parroquia_id',
        'sector',
        'calle_avenida',
        'numero_vivienda',
        'punto_referencia',
        'longitud',
        'latitud',
        'zona_base_misiones',
        'distancia_a_base_misiones',
        'consejo_comunal',
        'vocero_alimentacion',
        'telefono_principal',
        'telefono_secundario',
        'estado_cda',
        'motivo_inoperatividad',
        // Campos de operatividad
        'posee_sello',
        'posee_listado_beneficiarios',
        'num_beneficiarios_registrados',
        'num_promedio_diario_beneficiarios',
        'posee_identificacion_fundaproal',
        'identificacion_visible',
        'posee_cartelera_informativa',
        'posee_certificado_manipulacion_alimentos',
        'posee_certificado_salud',
        'posee_gorros_delantales',
        'recibe_suministro_gas',
        'cantidad_reguladores_kg',
        'bombonas_propias_cantidad',
        'bombonas_prestadas_cantidad',
        'manipulacion_alimentos_adecuada',
        'dias_preparacion_semana',
        'itinerantes_femeninos',
        'itinerantes_masculinos',
        'itinerantes_menores_masculinos',
        'itinerantes_menores_femeninos',
        'condicion_cocina',
        'tipo_cocina',
        'dominio_cocina',
        'condicion_nevera',
        'dominio_congelador',
        'condicion_congelador',
        'estatus_utensilios',
        'posee_meson',
        'posee_fregadero',
        'posee_tanque_agua',
        'posee_estante_almacenamiento',
        'observaciones_operatividad',
    ];

    protected $casts = [
        'fecha' => 'date',
        'zona_base_misiones' => 'boolean',
        'longitud' => 'decimal:8',
        'latitud' => 'decimal:8',
        'distancia_a_base_misiones' => 'decimal:2',
        // Campos de operatividad
        'posee_sello' => 'boolean',
        'posee_listado_beneficiarios' => 'boolean',
        'posee_identificacion_fundaproal' => 'boolean',
        'identificacion_visible' => 'boolean',
        'posee_cartelera_informativa' => 'boolean',
        'posee_certificado_manipulacion_alimentos' => 'boolean',
        'posee_certificado_salud' => 'boolean',
        'posee_gorros_delantales' => 'boolean',
        'recibe_suministro_gas' => 'boolean',
        'manipulacion_alimentos_adecuada' => 'boolean',
        'posee_meson' => 'boolean',
        'posee_fregadero' => 'boolean',
        'posee_tanque_agua' => 'boolean',
        'posee_estante_almacenamiento' => 'boolean',
    ];

    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    public function parroquia()
    {
        return $this->belongsTo(Parroquia::class);
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['codigo', 'estado_id', 'municipio_id', 'parroquia_id', 'estado_cda'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs()
            ->setDescriptionForEvent(fn(string $eventName) => static::getSpanishDescription($eventName));
    }
}
