<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Contrato;
use App\Models\Cliente;
use App\Models\MotoUnidad;
use App\Models\User;
use App\Models\Empresa;
use App\Models\Sucursal;
use App\Models\PlanPago;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ContratosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Obtener datos necesarios
        $empresa = Empresa::first();
        $sucursal = Sucursal::first();
        $vendedores = User::whereHas('roles', function($query) {
            $query->where('name', 'vendedor');
        })->get();
        
        // Si no hay vendedores, usar el primer usuario
        if ($vendedores->isEmpty()) {
            $vendedores = User::take(3)->get();
        }

        // Obtener clientes y unidades disponibles
        $clientes = Cliente::where('activo', true)->get();
        $unidades = MotoUnidad::where('estado', 'disponible')->get();

        if ($clientes->isEmpty() || $unidades->isEmpty()) {
            $this->command->info('No hay suficientes clientes o unidades disponibles para crear contratos.');
            return;
        }

        // Configuración de tipos de contrato
        $frecuencias = ['semanal', 'quincenal', 'mensual'];
        $estados = ['activo', 'mora', 'completado'];
        $plazos = [12, 18, 24, 36]; // meses
        $tasas_interes = [8.5, 12.0, 15.5, 18.0]; // porcentaje anual

        // Crear contratos
        foreach ($clientes->take(10) as $index => $cliente) {
            // Seleccionar una unidad aleatoria
            $unidad = $unidades->random();
            
            // Configurar contrato
            $frecuencia = $frecuencias[array_rand($frecuencias)];
            $plazo_meses = $plazos[array_rand($plazos)];
            $tasa_interes = $tasas_interes[array_rand($tasas_interes)];
            $estado = $estados[array_rand($estados)];
            
            // Calcular precios
            $precio_venta = $unidad->precio_venta;
            $cuota_inicial_porcentaje = rand(10, 30); // 10-30%
            $cuota_inicial = ($precio_venta * $cuota_inicial_porcentaje) / 100;
            $monto_financiado = $precio_venta - $cuota_inicial;
            
            // Generar número de contrato único
            $numero_contrato = $this->generarNumeroContrato();
            
            // Calcular fechas
            $fecha_inicio = Carbon::now()->subMonths(rand(1, 6))->startOfMonth();
            $fecha_fin = $fecha_inicio->copy()->addMonths($plazo_meses);
            
            // Calcular cuotas totales según frecuencia
            $cuotas_totales = $this->calcularCuotasTotales($plazo_meses, $frecuencia);
            
            // Calcular cuotas pagadas según estado
            $cuotas_pagadas = $this->calcularCuotasPagadas($cuotas_totales, $estado);
            $cuotas_vencidas = $estado === 'mora' ? rand(1, 3) : 0;
            
            // Calcular saldo pendiente
            $monto_cuota = $this->calcularMontoCuota($monto_financiado, $tasa_interes, $cuotas_totales);
            $total_pagado = $cuotas_pagadas * $monto_cuota;
            $saldo_pendiente = max(0, $monto_financiado - $total_pagado);

            // Crear contrato
            $contrato = Contrato::create([
                'numero_contrato' => $numero_contrato,
                'cliente_id' => $cliente->id,
                'moto_unidad_id' => $unidad->id,
                'vendedor_id' => $vendedores->random()->id,
                'empresa_id' => $empresa->id,
                'sucursal_id' => $sucursal->id,
                'fecha_inicio' => $fecha_inicio,
                'fecha_fin_estimada' => $fecha_fin,
                'precio_venta_final' => $precio_venta,
                'cuota_inicial' => $cuota_inicial,
                'monto_financiado' => $monto_financiado,
                'tasa_interes_anual' => $tasa_interes,
                'plazo_meses' => $plazo_meses,
                'frecuencia_pago' => $frecuencia,
                'dia_pago_mensual' => rand(1, 28),
                'estado' => $estado,
                'saldo_pendiente' => $saldo_pendiente,
                'cuotas_pagadas' => $cuotas_pagadas,
                'cuotas_totales' => $cuotas_totales,
                'cuotas_vencidas' => $cuotas_vencidas,
                'observaciones' => $this->generarObservaciones($estado)
            ]);

            // Actualizar estado de la unidad
            $unidad->update(['estado' => 'vendido']);

            // Crear plan de pagos
            $this->crearPlanPagos($contrato, $monto_financiado, $tasa_interes, $fecha_inicio, $frecuencia);

            $this->command->info("Contrato {$numero_contrato} creado para {$cliente->nombre} {$cliente->apellido}");
        }
    }

    private function generarNumeroContrato(): string
    {
        do {
            $numero = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (Contrato::where('numero_contrato', $numero)->exists());

        return $numero;
    }

    private function calcularCuotasTotales(int $plazo_meses, string $frecuencia): int
    {
        return match($frecuencia) {
            'semanal' => $plazo_meses * 4,
            'quincenal' => $plazo_meses * 2,
            'mensual' => $plazo_meses,
            default => $plazo_meses
        };
    }

    private function calcularCuotasPagadas(int $cuotas_totales, string $estado): int
    {
        return match($estado) {
            'activo' => rand(3, (int)($cuotas_totales * 0.8)),
            'mora' => rand(1, (int)($cuotas_totales * 0.6)),
            'completado' => $cuotas_totales,
            default => 0
        };
    }

    private function calcularMontoCuota(float $monto_financiado, float $tasa_interes, int $cuotas_totales): float
    {
        // Cálculo simplificado de cuota con interés
        $interes_total = ($monto_financiado * $tasa_interes * ($cuotas_totales / 12)) / 100;
        $monto_total = $monto_financiado + $interes_total;
        return round($monto_total / $cuotas_totales, 2);
    }

    private function crearPlanPagos(Contrato $contrato, float $monto_financiado, float $tasa_interes, Carbon $fecha_inicio, string $frecuencia): void
    {
        $cuotas_totales = $contrato->cuotas_totales;
        $monto_cuota = $this->calcularMontoCuota($monto_financiado, $tasa_interes, $cuotas_totales);
        
        // Calcular distribución capital/interés (simplificado)
        $interes_total = ($monto_financiado * $tasa_interes * ($cuotas_totales / 12)) / 100;
        $capital_total = $monto_financiado;
        $monto_total_con_interes = $capital_total + $interes_total;
        
        $capital_por_cuota = round($capital_total / $cuotas_totales, 2);
        $interes_por_cuota = round($interes_total / $cuotas_totales, 2);
        
        $fecha_vencimiento = $fecha_inicio->copy();
        $cuotas_pagadas = $contrato->cuotas_pagadas;
        
        for ($i = 1; $i <= $cuotas_totales; $i++) {
            // Calcular fecha de vencimiento según frecuencia
            $fecha_vencimiento = $this->calcularFechaVencimiento($fecha_vencimiento, $frecuencia, $i);
            
            // Determinar estado de la cuota
            $estado = $this->determinarEstadoCuota($i, $cuotas_pagadas, $fecha_vencimiento);
            
            // Calcular montos de pago
            $monto_pagado = 0;
            $saldo_pendiente = $monto_cuota;
            $fecha_pago_real = null;
            
            if ($i <= $cuotas_pagadas) {
                $monto_pagado = $monto_cuota;
                $saldo_pendiente = 0;
                // Fecha de pago aleatoria dentro del período de pago
                $fecha_pago_real = $fecha_vencimiento->copy()->subDays(rand(1, 15));
            }

            PlanPago::create([
                'contrato_id' => $contrato->id,
                'numero_cuota' => $i,
                'tipo_cuota' => 'mensual',
                'fecha_vencimiento' => $fecha_vencimiento,
                'fecha_pago_real' => $fecha_pago_real,
                'monto_capital' => $capital_por_cuota,
                'monto_interes' => $interes_por_cuota,
                'monto_total' => $monto_cuota,
                'saldo_pendiente' => $saldo_pendiente,
                'monto_pagado' => $monto_pagado,
                'mora_calculada' => $estado === 'vencido' ? round($monto_cuota * 0.05, 2) : 0,
                'mora_pagada' => 0,
                'dias_retraso' => $estado === 'vencido' ? rand(1, 30) : 0,
                'estado' => $estado,
                'empresa_id' => $contrato->empresa_id
            ]);
        }
    }

    private function calcularFechaVencimiento(Carbon $fecha_anterior, string $frecuencia, int $numero_cuota): Carbon
    {
        return match($frecuencia) {
            'semanal' => $fecha_anterior->copy()->addWeek(),
            'quincenal' => $fecha_anterior->copy()->addDays(15),
            'mensual' => $fecha_anterior->copy()->addMonth(),
            default => $fecha_anterior->copy()->addMonth()
        };
    }

    private function determinarEstadoCuota(int $numero_cuota, int $cuotas_pagadas, Carbon $fecha_vencimiento): string
    {
        if ($numero_cuota <= $cuotas_pagadas) {
            return 'pagado';
        }
        
        if ($fecha_vencimiento->isPast()) {
            return 'vencido';
        }
        
        return 'pendiente';
    }

    private function generarObservaciones(string $estado): ?string
    {
        $observaciones = [
            'activo' => ['Cliente al día con sus pagos', 'Contrato activo y funcionando normalmente', 'Sin observaciones particulares'],
            'mora' => ['Cliente con atrasos en pagos', 'Revisar situación de pago', 'Contactar para regularizar pagos'],
            'completado' => ['Contrato finalizado exitosamente', 'Todos los pagos realizados', 'Contrato concluido']
        ];

        $lista = $observaciones[$estado] ?? ['Sin observaciones'];
        return $lista[array_rand($lista)];
    }
}