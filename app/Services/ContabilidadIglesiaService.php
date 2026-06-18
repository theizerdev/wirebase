<?php

namespace App\Services;

use App\Models\TransaccionFinanciera;
use App\Models\AsientoContable;
use App\Models\AsientoDetalle;
use App\Models\CuentaContable;
use App\Models\Iglesia;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ContabilidadIglesiaService
{
    /**
     * Registrar un diezmo
     */
    public function registrarDiezmo(array $data): TransaccionFinanciera
    {
        $data['tipo'] = TransaccionFinanciera::TIPO_DIEZMO;
        return $this->registrarTransaccion($data);
    }

    /**
     * Registrar una ofrenda
     */
    public function registrarOfrenda(array $data): TransaccionFinanciera
    {
        $data['tipo'] = TransaccionFinanciera::TIPO_OFRENDA;
        return $this->registrarTransaccion($data);
    }

    /**
     * Registrar un aporte especial
     */
    public function registrarAporteEspecial(array $data): TransaccionFinanciera
    {
        $data['tipo'] = TransaccionFinanciera::TIPO_APORTE_ESPECIAL;
        return $this->registrarTransaccion($data);
    }

    /**
     * Registrar un gasto operativo
     */
    public function registrarGastoOperativo(array $data): TransaccionFinanciera
    {
        $data['tipo'] = TransaccionFinanciera::TIPO_GASTO_OPERATIVO;
        return $this->registrarTransaccion($data);
    }

    /**
     * Registrar un gasto de ministerio
     */
    public function registrarGastoMinisterio(array $data): TransaccionFinanciera
    {
        $data['tipo'] = TransaccionFinanciera::TIPO_GASTO_MINISTERIO;
        return $this->registrarTransaccion($data);
    }

    /**
     * Registrar una transacción financiera genérica
     */
    public function registrarTransaccion(array $data): TransaccionFinanciera
    {
        return DB::transaction(function () use ($data) {
            // Validar datos requeridos
            $this->validarDatosTransaccion($data);

            // Calcular monto en Bs si es USD
            if (strtoupper($data['moneda']) === 'USD' && !isset($data['monto_bs'])) {
                $data['tasa_cambio'] = $data['tasa_cambio'] ?? $this->obtenerTasaBCV();
                $data['monto_bs'] = $data['monto'] * $data['tasa_cambio'];
            }

            // Generar número de comprobante
            $data['numero_comprobante'] = $this->generarNumeroComprobante($data['iglesia_id']);

            // Crear la transacción
            $transaccion = TransaccionFinanciera::create($data);

            // Generar asiento contable automático
            $this->generarAsientoContable($transaccion);

            return $transaccion;
        });
    }

    /**
     * Generar asiento contable desde una transacción
     */
    public function generarAsientoContable(TransaccionFinanciera $transaccion): AsientoContable
    {
        return DB::transaction(function () use ($transaccion) {
            // Obtener empresa_id y sucursal_id de la iglesia si no están en la transacción
            $iglesia = Iglesia::find($transaccion->iglesia_id);
            $empresaId = $transaccion->empresa_id ?? $iglesia?->empresa_id;
            $sucursalId = $transaccion->sucursal_id ?? $iglesia?->sucursal_id;

            $asiento = AsientoContable::create([
                'numero' => $this->generarNumeroAsiento($transaccion->iglesia_id),
                'fecha' => $transaccion->fecha,
                'tipo' => 'diario',
                'descripcion' => $this->generarDescripcionAsiento($transaccion),
                'estado' => 'aprobado',
                'referencia_tipo' => 'transaccion_financiera',
                'referencia_id' => $transaccion->id,
                'user_id' => $transaccion->user_id,
                'iglesia_id' => $transaccion->iglesia_id,
                'empresa_id' => $empresaId,
                'sucursal_id' => $sucursalId,
            ]);

            // Determinar cuentas según tipo de transacción
            if ($transaccion->esIngreso()) {
                $this->crearAsientoIngreso($asiento, $transaccion);
            } else {
                $this->crearAsientoEgreso($asiento, $transaccion);
            }

            // Validar partida doble
            self::validarPartidaDoble($asiento);

            // Actualizar la transacción con el ID del asiento
            $transaccion->update(['asiento_contable_id' => $asiento->id]);

            return $asiento;
        });
    }

    /**
     * Crear detalles de asiento para ingresos
     */
    private function crearAsientoIngreso(AsientoContable $asiento, TransaccionFinanciera $transaccion): void
    {
        // DEBE: Caja o Banco (según método de pago)
        $cuentaCajaBanco = $this->obtenerCuentaPorMetodoPago($transaccion->metodo_pago, $transaccion->iglesia_id);

        if ($cuentaCajaBanco) {
            $asiento->detalles()->create([
                'cuenta_id' => $cuentaCajaBanco->id,
                'debe' => $transaccion->monto_bs,
                'haber' => 0,
                'descripcion' => "Registro de {$transaccion->tipo_label} - {$transaccion->metodo_pago_label}",
            ]);
        } else {
            \Log::warning('No se encontró cuenta de caja/banco para método de pago', [
                'metodo_pago' => $transaccion->metodo_pago,
                'iglesia_id' => $transaccion->iglesia_id,
                'transaccion_id' => $transaccion->id
            ]);
        }

        // HABER: Cuenta de ingreso según tipo
        $cuentaIngreso = $this->obtenerCuentaIngreso($transaccion->tipo, $transaccion->iglesia_id);

        if ($cuentaIngreso) {
            $asiento->detalles()->create([
                'cuenta_id' => $cuentaIngreso->id,
                'debe' => 0,
                'haber' => $transaccion->monto_bs,
                'descripcion' => "{$transaccion->tipo_label} registrado",
            ]);
        } else {
            \Log::warning('No se encontró cuenta de ingreso para tipo de transacción', [
                'tipo' => $transaccion->tipo,
                'iglesia_id' => $transaccion->iglesia_id,
                'transaccion_id' => $transaccion->id
            ]);
        }
    }

    /**
     * Crear detalles de asiento para egresos/gastos
     */
    private function crearAsientoEgreso(AsientoContable $asiento, TransaccionFinanciera $transaccion): void
    {
        // DEBE: Cuenta de gasto según categoría
        $cuentaGasto = $this->obtenerCuentaGasto($transaccion->tipo, $transaccion->categoria, $transaccion->iglesia_id);

        if ($cuentaGasto) {
            $asiento->detalles()->create([
                'cuenta_id' => $cuentaGasto->id,
                'debe' => $transaccion->monto_bs,
                'haber' => 0,
                'descripcion' => $transaccion->descripcion ?? "Gasto registrado",
            ]);
        } else {
            \Log::warning('No se encontró cuenta de gasto', [
                'tipo' => $transaccion->tipo,
                'categoria' => $transaccion->categoria,
                'iglesia_id' => $transaccion->iglesia_id,
                'transaccion_id' => $transaccion->id
            ]);
        }

        // HABER: Caja o Banco
        $cuentaCajaBanco = $this->obtenerCuentaPorMetodoPago($transaccion->metodo_pago, $transaccion->iglesia_id);

        if ($cuentaCajaBanco) {
            $asiento->detalles()->create([
                'cuenta_id' => $cuentaCajaBanco->id,
                'debe' => 0,
                'haber' => $transaccion->monto_bs,
                'descripcion' => "Pago de gasto - {$transaccion->metodo_pago_label}",
            ]);
        } else {
            \Log::warning('No se encontró cuenta de caja/banco para método de pago', [
                'metodo_pago' => $transaccion->metodo_pago,
                'iglesia_id' => $transaccion->iglesia_id,
                'transaccion_id' => $transaccion->id
            ]);
        }
    }

    /**
     * Obtener cuenta de ingreso según tipo de transacción
     */
    private function obtenerCuentaIngreso(string $tipo, int $iglesiaId): ?CuentaContable
    {
        $codigoCuenta = match($tipo) {
            TransaccionFinanciera::TIPO_DIEZMO => config('contabilidad.cuentas.diezmos'),
            TransaccionFinanciera::TIPO_OFRENDA => config('contabilidad.cuentas.ofrendas'),
            TransaccionFinanciera::TIPO_APORTE_ESPECIAL => config('contabilidad.cuentas.aportes_especiales'),
            TransaccionFinanciera::TIPO_OTRO_INGRESO => config('contabilidad.cuentas.otros_ingresos'),
            default => null,
        };

        if (!$codigoCuenta) {
            \Log::warning('Código de cuenta no configurado para tipo de ingreso', ['tipo' => $tipo]);
            return null;
        }

        $cuenta = CuentaContable::where('codigo', $codigoCuenta)
            ->where(function($query) use ($iglesiaId) {
                $query->where('iglesia_id', $iglesiaId)
                      ->orWhereNull('iglesia_id');
            })
            ->where('activo', true)
            ->first();

        // Si no existe, crearla automáticamente
        if (!$cuenta) {
            $cuenta = $this->crearCuentaSiNoExiste($codigoCuenta, $tipo, $iglesiaId);
        }

        return $cuenta;
    }

    /**
     * Obtener cuenta de gasto según tipo y categoría
     */
    private function obtenerCuentaGasto(string $tipo, ?string $categoria, int $iglesiaId): ?CuentaContable
    {
        if ($tipo === TransaccionFinanciera::TIPO_GASTO_OPERATIVO && $categoria) {
            $categorias = config('contabilidad.categorias_gastos_operativos', []);
            $codigoCuenta = $categorias[$categoria] ?? config('contabilidad.cuentas.gastos_operativos');
        } elseif ($tipo === TransaccionFinanciera::TIPO_GASTO_MINISTERIO && $categoria) {
            $categorias = config('contabilidad.categorias_gastos_ministerio', []);
            $codigoCuenta = $categorias[$categoria] ?? config('contabilidad.cuentas.gastos_ministerio');
        } else {
            $codigoCuenta = config('contabilidad.cuentas.otros_gastos');
        }

        if (!$codigoCuenta) {
            return null;
        }

        $cuenta = CuentaContable::where('codigo', $codigoCuenta)
            ->where(function($query) use ($iglesiaId) {
                $query->where('iglesia_id', $iglesiaId)
                      ->orWhereNull('iglesia_id');
            })
            ->where('activo', true)
            ->first();

        // Auto-crear cuenta de gasto si no existe
        if (!$cuenta) {
            $cuenta = $this->crearCuentaSiNoExiste($codigoCuenta, $tipo, $iglesiaId);
        }

        return $cuenta;
    }

    /**
     * Obtener cuenta de caja/banco según método de pago
     */
    private function obtenerCuentaPorMetodoPago(string $metodoPago, int $iglesiaId): ?CuentaContable
    {
        $codigoCuenta = config("contabilidad.metodos_pago.{$metodoPago}");

        if (!$codigoCuenta) {
            $codigoCuenta = config('contabilidad.metodos_pago.efectivo');
        }

        $cuenta = CuentaContable::where('codigo', $codigoCuenta)
            ->where(function($query) use ($iglesiaId) {
                $query->where('iglesia_id', $iglesiaId)
                      ->orWhereNull('iglesia_id');
            })
            ->where('activo', true)
            ->first();

        // Auto-crear cuenta de caja/banco si no existe
        if (!$cuenta) {
            $cuenta = $this->crearCuentaCajaBancoSiNoExiste($codigoCuenta, $metodoPago, $iglesiaId);
        }

        return $cuenta;
    }

    /**
     * Generar número de comprobante único
     */
    private function generarNumeroComprobante(int $iglesiaId): string
    {
        $prefijo = config('contabilidad.prefijo_comprobante', 'TF');
        $longitud = config('contabilidad.longitud_correlativo', 6);

        $ultimaTransaccion = TransaccionFinanciera::where('iglesia_id', $iglesiaId)
            ->orderBy('id', 'desc')
            ->first();

        $correlativo = $ultimaTransaccion ?
            intval(substr($ultimaTransaccion->numero_comprobante, -($longitud))) + 1 :
            1;

        return $prefijo . '-' . str_pad($correlativo, $longitud, '0', STR_PAD_LEFT);
    }

    /**
     * Generar número de asiento contable
     */
    private function generarNumeroAsiento(int $iglesiaId): string
    {
        $iglesia = Iglesia::find($iglesiaId);
        $empresaId = $iglesia?->empresa_id ?? $iglesiaId;
        return AsientoContable::generarNumero($empresaId);
    }

    /**
     * Generar descripción del asiento
     */
    private function generarDescripcionAsiento(TransaccionFinanciera $transaccion): string
    {
        return "{$transaccion->tipo_label} - {$transaccion->numero_comprobante}";
    }

    /**
     * Obtener tasa de cambio BCV del día
     */
    private function obtenerTasaBCV(): float
    {
        try {
            $exchangeRateService = app(ExchangeRateService::class);
            $tasa = $exchangeRateService->getLatestRate('USD');
            return $tasa ?? config('contabilidad.tasa_cambio_default', 1.0);
        } catch (\Exception $e) {
            \Log::warning('No se pudo obtener tasa BCV, usando default', [
                'error' => $e->getMessage()
            ]);
            return config('contabilidad.tasa_cambio_default', 1.0);
        }
    }

    /**
     * Validar datos de transacción
     */
    private function validarDatosTransaccion(array $data): void
    {
        $required = ['fecha', 'tipo', 'monto', 'moneda', 'iglesia_id', 'user_id'];

        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new \InvalidArgumentException("El campo '{$field}' es requerido");
            }
        }

        if ($data['monto'] <= 0) {
            throw new \InvalidArgumentException("El monto debe ser mayor a cero");
        }

        if (!in_array($data['moneda'], config('contabilidad.monedas_soportadas', ['VES', 'USD']))) {
            throw new \InvalidArgumentException("Moneda no soportada: {$data['moneda']}");
        }
    }

    /**
     * Validar que un asiento contable cumpla la partida doble (Debe = Haber)
     */
    public static function validarPartidaDoble(AsientoContable $asiento): void
    {
        $asiento->load('detalles');
        $totalDebe = round((float) $asiento->detalles->sum('debe'), 2);
        $totalHaber = round((float) $asiento->detalles->sum('haber'), 2);

        if ($totalDebe !== $totalHaber) {
            throw new \Exception(
                "Asiento {$asiento->numero} desbalanceado: Debe={$totalDebe}, Haber={$totalHaber}. " .
                "Diferencia: " . round(abs($totalDebe - $totalHaber), 2)
            );
        }
    }

    /**
     * Obtener saldo de una cuenta en un período
     */
    public function getSaldoPeriodo(CuentaContable $cuenta, Carbon $desde, Carbon $hasta): float
    {
        $query = AsientoDetalle::where('cuenta_id', $cuenta->id)
            ->whereHas('asiento', fn($q) => $q->where('estado', 'aprobado')
                ->where('tipo', '!=', 'cierre')
                ->whereBetween('fecha', [$desde, $hasta]));

        $debe = (float) $query->sum('debe');
        $haber = (float) (clone $query)->sum('haber');

        return $cuenta->naturaleza === 'deudora' ? ($debe - $haber) : ($haber - $debe);
    }

    /**
     * Obtener saldo acumulado de una cuenta hasta una fecha
     */
    public function getSaldoAcumulado(CuentaContable $cuenta, Carbon $hastaFecha): float
    {
        $query = AsientoDetalle::where('cuenta_id', $cuenta->id)
            ->whereHas('asiento', fn($q) => $q->where('estado', 'aprobado')
                ->whereDate('fecha', '<=', $hastaFecha));

        $debe = (float) $query->sum('debe');
        $haber = (float) (clone $query)->sum('haber');

        return $cuenta->naturaleza === 'deudora' ? ($debe - $haber) : ($haber - $debe);
    }

    /**
     * Generar reporte de ingresos vs egresos por período
     */
    public function reporteIngresosEgresos(int $iglesiaId, Carbon $fechaInicio, Carbon $fechaFin): array
    {
        $ingresos = TransaccionFinanciera::porIglesia($iglesiaId)
            ->ingresos()
            ->porFecha($fechaInicio, $fechaFin)
            ->sum('monto_bs');

        $egresos = TransaccionFinanciera::porIglesia($iglesiaId)
            ->egresos()
            ->porFecha($fechaInicio, $fechaFin)
            ->sum('monto_bs');

        return [
            'fecha_inicio' => $fechaInicio->format('Y-m-d'),
            'fecha_fin' => $fechaFin->format('Y-m-d'),
            'total_ingresos' => $ingresos,
            'total_egresos' => $egresos,
            'balance' => $ingresos - $egresos,
            'desglose_ingresos' => $this->desglosePorTipo($iglesiaId, $fechaInicio, $fechaFin, true),
            'desglose_egresos' => $this->desglosePorTipo($iglesiaId, $fechaInicio, $fechaFin, false),
        ];
    }

    /**
     * Desglose de transacciones por tipo
     */
    private function desglosePorTipo(int $iglesiaId, Carbon $fechaInicio, Carbon $fechaFin, bool $sonIngresos): array
    {
        $query = TransaccionFinanciera::porIglesia($iglesiaId)
            ->porFecha($fechaInicio, $fechaFin);

        if ($sonIngresos) {
            $query->ingresos();
        } else {
            $query->egresos();
        }

        return $query->selectRaw('tipo, SUM(monto_bs) as total')
            ->groupBy('tipo')
            ->get()
            ->mapWithKeys(function($item) {
                return [$item->tipo => $item->total];
            })
            ->toArray();
    }

    /**
     * Anular una transacción y su asiento contable
     */
    public function anularTransaccion(TransaccionFinanciera $transaccion, int $userId): void
    {
        DB::transaction(function () use ($transaccion, $userId) {
            // Anular asiento contable si existe
            if ($transaccion->asientoContable) {
                $transaccion->asientoContable->update([
                    'estado' => 'anulado',
                ]);
            }

            // Eliminar la transacción (soft delete)
            $transaccion->delete();

            // Registrar en activity log
            activity()
                ->performedOn($transaccion)
                ->causedById($userId)
                ->log('Transacción financiera anulada');
        });
    }

    /**
     * Crear cuenta contable si no existe
     */
    private function crearCuentaSiNoExiste(string $codigo, string $tipo, int $iglesiaId): ?CuentaContable
    {
        try {
            // Determinar nombre y tipo de cuenta según el código
            $nombre = match($tipo) {
                TransaccionFinanciera::TIPO_DIEZMO => 'Diezmos',
                TransaccionFinanciera::TIPO_OFRENDA => 'Ofrendas',
                TransaccionFinanciera::TIPO_APORTE_ESPECIAL => 'Aportes Especiales',
                TransaccionFinanciera::TIPO_OTRO_INGRESO => 'Otros Ingresos',
                TransaccionFinanciera::TIPO_GASTO_OPERATIVO => 'Gastos Operativos',
                TransaccionFinanciera::TIPO_GASTO_MINISTERIO => 'Gastos de Ministerio',
                TransaccionFinanciera::TIPO_OTRO_EGRESO => 'Otros Egresos',
                default => 'Cuenta Genérica',
            };

            // Determinar si es ingreso o gasto para asignar naturaleza
            $esIngreso = in_array($tipo, [
                TransaccionFinanciera::TIPO_DIEZMO,
                TransaccionFinanciera::TIPO_OFRENDA,
                TransaccionFinanciera::TIPO_APORTE_ESPECIAL,
                TransaccionFinanciera::TIPO_OTRO_INGRESO,
            ]);

            // Verificar si ya existe para esta iglesia
            $existente = CuentaContable::where('codigo', $codigo)
                ->where('iglesia_id', $iglesiaId)
                ->first();

            if ($existente) {
                return $existente;
            }

            // Crear nueva cuenta
            $cuenta = CuentaContable::create([
                'codigo' => $codigo,
                'nombre' => $nombre,
                'tipo' => $esIngreso ? 'ingreso' : 'gasto',
                'naturaleza' => $esIngreso ? 'acreedora' : 'deudora',
                'nivel' => 4,
                'activo' => true,
                'iglesia_id' => $iglesiaId,
            ]);

            \Log::info('Cuenta contable creada automáticamente', [
                'codigo' => $codigo,
                'nombre' => $nombre,
                'iglesia_id' => $iglesiaId,
            ]);

            return $cuenta;
        } catch (\Exception $e) {
            \Log::error('Error al crear cuenta contable automática', [
                'codigo' => $codigo,
                'tipo' => $tipo,
                'iglesia_id' => $iglesiaId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }

    /**
     * Crear cuenta de caja/banco si no existe
     */
    private function crearCuentaCajaBancoSiNoExiste(string $codigo, string $metodoPago, int $iglesiaId): ?CuentaContable
    {
        try {
            $nombre = match($codigo) {
                '1.1.01' => 'Caja General',
                '1.1.02' => 'Caja Chica',
                '1.1.03' => 'Banco - Cuenta Corriente',
                '1.1.04' => 'Banco - Cuenta de Ahorros',
                default => "Caja/Banco ({$metodoPago})",
            };

            $iglesia = Iglesia::find($iglesiaId);

            $cuenta = CuentaContable::create([
                'codigo' => $codigo,
                'nombre' => $nombre,
                'tipo' => 'activo',
                'naturaleza' => 'deudora',
                'nivel' => 2,
                'activo' => true,
                'iglesia_id' => $iglesiaId,
                'empresa_id' => $iglesia?->empresa_id,
                'sucursal_id' => $iglesia?->sucursal_id,
            ]);

            \Log::info('Cuenta de caja/banco creada automáticamente', [
                'codigo' => $codigo,
                'nombre' => $nombre,
                'metodo_pago' => $metodoPago,
                'iglesia_id' => $iglesiaId,
            ]);

            return $cuenta;
        } catch (\Exception $e) {
            \Log::error('Error al crear cuenta de caja/banco automática', [
                'codigo' => $codigo,
                'metodo_pago' => $metodoPago,
                'iglesia_id' => $iglesiaId,
                'error' => $e->getMessage(),
            ]);
            return null;
        }
    }
}
