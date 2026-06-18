<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use App\Traits\Multitenantable;

class Pastor extends Model
{
    use HasFactory, LogsActivity, Multitenantable;
    protected $table = 'pastores';

    protected $fillable = [
        'codigo',
        'nombres',
        'apellidos',
        'documento',
        'nivel_ministerial',
        'zona',
        'distrito',
        'genero',
        'edad',
        'ano_promocion',
        'tiempo_colaborando',
        'fe_nacimiento',
        'foto',
        'nota',
        'status',
        'estado_civil',
        'batizado_espiritu_santo',
        'grado_instruccion',
        'titulo_obtenido',
        'estudio_teologico',
        'titulo_teologico',
        'tiempo_de_estudio_teologico',
        'instituto_teologico',
        'pertenece_ministerio',
        'nombre_conyuge',
        'conyuge_id',
        'edificio_casa_quinta',
        'piso',
        'apartamento',
        'calle_avenida',
        'urbanizacion',
        'municipio_id',
        'telefono_hab',
        'telefono_tlf',
        'telefono_otro',
        'mencion',
        'cargo_nacional',
        'user_id',
        'empresa_id',
        'sucursal_id',
        'ciudad_id',
        'estado_id',
        'parroquia_id',
        'latitud',
        'longitud',
        'municipio',
        'estado',
        'parroquia',
    ];

    protected $casts = [
        'status' => 'boolean',
        'batizado_espiritu_santo' => 'boolean',
        'estudio_teologico' => 'boolean',
        'pertenece_ministerio' => 'boolean',
        'fe_nacimiento' => 'date',
        'latitud' => 'decimal:8',
        'longitud' => 'decimal:8',
    ];

    /**
     * Scope para obtener solo pastores que pueden ser pastores principales
     * (excluye a los cónyuges de otros pastores)
     */
    public function scopePastoresPrincipales($query)
    {
        return $query->whereDoesntHave('pastoresConyuge');
    }

    /**
     * Scope para obtener todos los pastores activos
     */
    public function scopeActivos($query)
    {
        return $query->where('status', true);
    }

    /**
     * Verificar si este pastor es un cónyuge (es decir, la esposa, según la regla de negocio)
     */
    public function esConyuge()
    {
        // El cónyuge (esposa) siempre es Femenino
        return $this->conyuge_id !== null && $this->genero === 'Femenino';
    }

    /**
     * Obtener el pastor principal si este es un cónyuge
     */
    public function getPastorPrincipalAttribute()
    {
        if ($this->esConyuge()) {
            return $this->conyuge;
        }
        return null;
    }

    /**
     * Relación con usuario del sistema
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con empresa
     */
    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    /**
     * Relación con sucursal
     */
    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    /**
     * Relación con ciudad
     */
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class);
    }

    /**
     * Relación con estado
     */
    public function estado()
    {
        return $this->belongsTo(Estado::class);
    }

    /**
     * Relación con parroquia
     */
    public function parroquia()
    {
        return $this->belongsTo(Parroquia::class);
    }

    /**
     * Relación con municipio
     */
    public function municipio()
    {
        return $this->belongsTo(Municipio::class);
    }

    /**
     * Relación con iglesias
     */
    public function iglesias()
    {
        return $this->hasMany(Iglesia::class, 'pastor_id');
    }

    /**
     * Relación con el cónyuge (que también es un pastor)
     */
    public function conyuge()
    {
        return $this->belongsTo(Pastor::class, 'conyuge_id');
    }

    /**
     * Relación con los pastores de los que este es cónyuge
     */
    public function pastoresConyuge()
    {
        return $this->hasMany(Pastor::class, 'conyuge_id');
    }

    /**
     * Relación con solicitudes de modificación
     */
    public function solicitudesModificacion()
    {
        return $this->hasMany(SolicitudModificacionPastor::class, 'pastor_id');
    }

    /**
     * Relación con preguntas de seguridad
     */
    public function preguntasSeguridad()
    {
        return $this->hasOne(PreguntasSeguridadPastor::class, 'pastor_id');
    }

    /**
     * Obtener el nombre completo del pastor
     */
    public function getNombreCompletoAttribute()
    {
        return $this->nombres . ' ' . $this->apellidos;
    }

    /**
     * Obtener la dirección completa
     */
    public function getDireccionCompletaAttribute()
    {
        $direccion = '';
        if ($this->calle_avenida) {
            $direccion .= $this->calle_avenida;
        }
        if ($this->edificio_casa_quinta) {
            $direccion .= ', ' . $this->edificio_casa_quinta;
        }
        if ($this->urbanizacion) {
            $direccion .= ', ' . $this->urbanizacion;
        }
        if ($this->municipio) {
            $direccion .= ', ' . $this->municipio->nombre;
        }
        return $direccion;
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Generar código único del pastor con formato: {ID 3 dígitos}-{últimos 4 dígitos de cédula}
     * Ejemplo: 001-2293
     */
    public static function generarCodigoPastor(int $id, string $documento): string
    {
        $idPart = str_pad($id, 5, '0', STR_PAD_LEFT);
        $cedulaPart = substr(preg_replace('/[^0-9]/', '', $documento), -5);
        $cedulaPart = $cedulaPart ?: '0000';

        return $idPart . '-' . $cedulaPart;
    }
}