<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class InventarioIglesia extends Model
{
    use HasFactory, LogsActivity;

    protected $fillable = [
        'iglesia_id',
        'nombre',
        'descripcion',
        'categoria',
        'cantidad',
        'valor',
        'numero_factura',
        'moneda',
        'tasa_bcv',
        'valor_bs',
        'fecha_adquisicion',
        'condicion',
        'tipo_bien',
        'notas',
        'usuario_registro_id',
    ];

    protected $casts = [
        'fecha_adquisicion' => 'date',
        'valor' => 'decimal:2',
        'tasa_bcv' => 'decimal:4',
        'valor_bs' => 'decimal:2',
        'cantidad' => 'integer',
    ];

    /**
     * Relación con la iglesia
     */
    public function iglesia()
    {
        return $this->belongsTo(Iglesia::class);
    }

    /**
     * Relación con el usuario que registró el item
     */
    public function usuarioRegistro()
    {
        return $this->belongsTo(User::class, 'usuario_registro_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
