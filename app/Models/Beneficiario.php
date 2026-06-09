<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Beneficiario extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombres',
        'apellidos',
        'cedula',
        'fecha_nacimiento',
        'edad',
        'telefono_principal',
        'telefono_alternativo',
        'estado_civil',
        'estudia_actualmente',
        'estudio_actual',
        'nivel_instruccion',
        'ultimo_titulo_obtenido',
        'trabaja_actualmente',
        'lugar_trabajo',
        'ocupacion',
        'ingreso_mensual',
        'responsable_id',
        'casa_alimentacion_id',
        'posee_habilidad_productiva',
        'habilidad_productiva',
        'pertenece_organizacion_social',
        'tipo_organizacion_social',
        'otra_organizacion_social',
        'asignaciones_economicas',
        'estado_id',
        'municipio_id',
        'parroquia_id',
        // Step 4: Datos de Salud
        'tiene_evaluacion_antropometrica',
        'evaluacion_realizada_por',
        'condicion_ingreso',
        'fecha_ingreso',
        'padece_discapacidad_enfermedad',
        'diagnostico',
        'recipe_ayuda_tecnica',
        // Step 5: Datos Socio-Familiares
        'personas_nucleo_familiar',
        'ninos_niñas',
        'adolescentes',
        'mujeres',
        'hombres',
        'adultos_mayores',
        'mujeres_embarazadas',
        'recipe_socio_familiar',
        'es_mujer_embarazada',
        'fecha_ultima_menstruacion',
        'edad_gestacion',
        'observaciones'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'estudia_actualmente' => 'boolean',
        'trabaja_actualmente' => 'boolean',
        'ingreso_mensual' => 'decimal:2',
        'posee_habilidad_productiva' => 'boolean',
        'pertenece_organizacion_social' => 'boolean',
        'asignaciones_economicas' => 'array', // Cast to array for JSON handling
        'estado_id' => 'integer',
        'municipio_id' => 'integer',
        'parroquia_id' => 'integer',
        // Step 4: Datos de Salud
        'tiene_evaluacion_antropometrica' => 'boolean',
        'fecha_ingreso' => 'date',
        'padece_discapacidad_enfermedad' => 'boolean',
        // Step 5: Datos Socio-Familiares
        'es_mujer_embarazada' => 'boolean',
        'fecha_ultima_menstruacion' => 'date'
    ];

    public function responsable()
    {
        return $this->belongsTo(Responsable::class);
    }

    public function estado()
    {
        return $this->belongsTo(\App\Models\Estado::class);
    }

    public function municipio()
    {
        return $this->belongsTo(\App\Models\Municipio::class);
    }

    public function parroquia()
    {
        return $this->belongsTo(\App\Models\Parroquia::class);
    }

    public function casaAlimentacion()
    {
        return $this->belongsTo(\App\Models\CasaAlimentacion::class, 'casa_alimentacion_id');
    }
}
