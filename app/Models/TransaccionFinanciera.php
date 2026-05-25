<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TransaccionFinanciera extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'transacciones_financieras';

    // Tipos de transacciones
    const TIPO_DIEZMO = 'diezmo';
    const TIPO_OFRENDA = 'ofrenda';
    const TIPO_APORTE_ESPECIAL = 'aporte_especial';
    const TIPO_GASTO_OPERATIVO = 'gasto_operativo';
    const TIPO_GASTO_MINISTERIO = 'gasto_ministerio';
    const TIPO_OTRO_INGRESO = 'otro_ingreso';
    const TIPO_OTRO_EGRESO = 'otro_egreso';

    // Métodos de pago
    const METODO_EFECTIVO = 'efectivo';
    const METODO_TRANSFERENCIA = 'transferencia';
    const METODO_PUNTO_VENTA = 'punto_venta';
    const METODO_CHEQUE = 'cheque';
    const METODO_OTRO = 'otro';

    protected $fillable = [
        'numero_comprobante',
        'fecha',
        'tipo',
        'categoria',
        'descripcion',
        'monto',
        'moneda',
        'tasa_cambio',
        'monto_bs',
        'metodo_pago',
        'referencia_bancaria',
        'cuenta_origen_id',
        'cuenta_destino_id',
        'iglesia_id',
        'empresa_id',
        'sucursal_id',
        'user_id',
        'asiento_contable_id',
        'conciliado',
        'fecha_conciliacion',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
        'tasa_cambio' => 'decimal:4',
        'monto_bs' => 'decimal:2',
        'conciliado' => 'boolean',
        'fecha_conciliacion' => 'date',
    ];

    /**
     * Relación con la iglesia
     */
    public function iglesia()
    {
        return $this->belongsTo(Iglesia::class);
    }

    /**
     * Relación con el usuario que registró
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relación con el asiento contable generado
     */
    public function asientoContable()
    {
        return $this->belongsTo(AsientoContable::class);
    }

    /**
     * Relación con cuenta de origen (para transferencias)
     */
    public function cuentaOrigen()
    {
        return $this->belongsTo(CuentaContable::class, 'cuenta_origen_id');
    }

    /**
     * Relación con cuenta de destino (para transferencias)
     */
    public function cuentaDestino()
    {
        return $this->belongsTo(CuentaContable::class, 'cuenta_destino_id');
    }

    /**
     * Scope para filtrar ingresos
     */
    public function scopeIngresos($query)
    {
        return $query->whereIn('tipo', [
            self::TIPO_DIEZMO,
            self::TIPO_OFRENDA,
            self::TIPO_APORTE_ESPECIAL,
            self::TIPO_OTRO_INGRESO
        ]);
    }

    /**
     * Scope para filtrar egresos/gastos
     */
    public function scopeEgresos($query)
    {
        return $query->whereIn('tipo', [
            self::TIPO_GASTO_OPERATIVO,
            self::TIPO_GASTO_MINISTERIO,
            self::TIPO_OTRO_EGRESO
        ]);
    }

    /**
     * Scope para filtrar por tipo específico
     */
    public function scopePorTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Scope para filtrar por rango de fechas
     */
    public function scopePorFecha($query, $desde, $hasta)
    {
        return $query->whereBetween('fecha', [$desde, $hasta]);
    }

    /**
     * Scope para filtrar por iglesia
     */
    public function scopePorIglesia($query, $iglesiaId)
    {
        return $query->where('iglesia_id', $iglesiaId);
    }

    /**
     * Verificar si es un ingreso
     */
    public function esIngreso(): bool
    {
        return in_array($this->tipo, [
            self::TIPO_DIEZMO,
            self::TIPO_OFRENDA,
            self::TIPO_APORTE_ESPECIAL,
            self::TIPO_OTRO_INGRESO
        ]);
    }

    /**
     * Verificar si es un egreso/gasto
     */
    public function esEgreso(): bool
    {
        return in_array($this->tipo, [
            self::TIPO_GASTO_OPERATIVO,
            self::TIPO_GASTO_MINISTERIO,
            self::TIPO_OTRO_EGRESO
        ]);
    }

    /**
     * Obtener etiqueta legible del tipo
     */
    public function getTipoLabelAttribute(): string
    {
        return match($this->tipo) {
            self::TIPO_DIEZMO => 'Diezmo',
            self::TIPO_OFRENDA => 'Ofrenda',
            self::TIPO_APORTE_ESPECIAL => 'Aporte Especial',
            self::TIPO_GASTO_OPERATIVO => 'Gasto Operativo',
            self::TIPO_GASTO_MINISTERIO => 'Gasto de Ministerio',
            self::TIPO_OTRO_INGRESO => 'Otro Ingreso',
            self::TIPO_OTRO_EGRESO => 'Otro Egreso',
            default => $this->tipo
        };
    }

    /**
     * Obtener etiqueta legible del método de pago
     */
    public function getMetodoPagoLabelAttribute(): string
    {
        return match($this->metodo_pago) {
            self::METODO_EFECTIVO => 'Efectivo',
            self::METODO_TRANSFERENCIA => 'Transferencia',
            self::METODO_PUNTO_VENTA => 'Punto de Venta',
            self::METODO_CHEQUE => 'Cheque',
            self::METODO_OTRO => 'Otro',
            default => $this->metodo_pago
        };
    }

    /**
     * Accessor para monto en VES (compatibilidad)
     */
    public function getMontoVesAttribute()
    {
        return $this->monto_bs ?? 0;
    }

    /**
     * Accessor para monto en USD (compatibilidad)
     */
    public function getMontoUsdAttribute()
    {
        if ($this->moneda === 'USD') {
            return $this->monto;
        }
        // Si está en VES, convertir a USD usando la tasa
        if ($this->tasa_cambio > 0) {
            return round($this->monto / $this->tasa_cambio, 2);
        }
        return 0;
    }

    /**
     * Opciones de logging de actividad
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'numero_comprobante',
                'fecha',
                'tipo',
                'monto',
                'moneda',
                'monto_bs',
                'metodo_pago',
                'descripcion',
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }
}
