<?php

// Script de prueba para verificar que las cuotas empiezan DESPUÉS de la fecha del contrato

require_once __DIR__ . '/vendor/autoload.php';

use Carbon\Carbon;

// Función para obtener el próximo día hábil (Lunes a Sábado)
function getNextBusinessDay(Carbon $fecha): Carbon
{
    // Si es domingo (dayOfWeek = 0), mover al lunes siguiente
    if ($fecha->dayOfWeek === 0) {
        return $fecha->copy()->addDay();
    }
    
    // Si es sábado (dayOfWeek = 6) o cualquier otro día hábil, mantener la fecha
    return $fecha->copy();
}

// Función para agregar días hábiles (Lunes a Sábado)
function addBusinessDays(Carbon $fecha, int $dias): Carbon
{
    $fechaResultado = $fecha->copy();
    $diasAgregados = 0;
    
    while ($diasAgregados < $dias) {
        $fechaResultado->addDay();
        // Solo contar Lunes a Sábado
        if ($fechaResultado->dayOfWeek >= 1 && $fechaResultado->dayOfWeek <= 6) {
            $diasAgregados++;
        }
    }
    
    return $fechaResultado;
}

// Función para generar múltiples cuotas quincenales
function generarCuotasQuincenales(Carbon $fechaInicio, $numCuotas = 5) {
    $cuotas = [];
    $fecha_actual = $fechaInicio->copy();
    
    for ($i = 1; $i <= $numCuotas; $i++) {
        // Sumar 15 días naturales desde la fecha actual
        $fecha_cuota = $fecha_actual->copy()->addDays(15);
        // Asegurar que sea día hábil
        $fecha_cuota = getNextBusinessDay($fecha_cuota);
        
        $cuotas[] = [
            'cuota' => $i,
            'fecha' => $fecha_cuota->format('Y-m-d'),
            'dia' => $fecha_cuota->format('l'),
            'diferencia_dias' => $fechaInicio->diffInDays($fecha_cuota)
        ];
        
        // Actualizar fecha actual para la siguiente cuota
        $fecha_actual = $fecha_cuota->copy();
    }
    
    return $cuotas;
}

// Función de prueba para generar fechas de pago CORRECTAS (después de la fecha de contrato)
function testFechasCorrectas($fechaContrato, $numCuotas = 4) {
    echo "\n=== FECHAS DE PAGO DESDE CONTRATO $fechaContrato ===\n";
    
    $fecha_inicio = Carbon::parse($fechaContrato);
    $fechas = [];
    
    // Calcular la fecha de la primera cuota según la frecuencia
    $fecha_pago = $fecha_inicio->copy();
    
    // MENSUAL: sumar 1 mes
    $fecha_mensual = $fecha_pago->copy()->addMonth();
    echo "  Mensual: " . $fecha_mensual->format('Y-m-d') . ' (' . $fecha_mensual->format('l') . ")\n";
    
    // QUINCENAL: sumar 15 días naturales (no hábiles) y asegurar día hábil
    $fecha_quincenal = $fecha_pago->copy()->addDays(15);
    $fecha_quincenal = getNextBusinessDay($fecha_quincenal);
    echo "  Quincenal (1ª): " . $fecha_quincenal->format('Y-m-d') . ' (' . $fecha_quincenal->format('l') . ")\n";
    
    // SEMANAL: sumar 7 días
    $fecha_semanal = $fecha_pago->copy()->addDays(7);
    $fecha_semanal = getNextBusinessDay($fecha_semanal);
    echo "  Semanal: " . $fecha_semanal->format('Y-m-d') . ' (' . $fecha_semanal->format('l') . ")\n";
    
    // Verificar que ninguna fecha es igual a la fecha del contrato
    $esMismoDiaMensual = $fecha_mensual->format('Y-m-d') === $fecha_inicio->format('Y-m-d');
    $esMismoDiaQuincenal = $fecha_quincenal->format('Y-m-d') === $fecha_inicio->format('Y-m-d');
    $esMismoDiaSemanal = $fecha_semanal->format('Y-m-d') === $fecha_inicio->format('Y-m-d');
    
    echo "\n  ✅ Verificación:\n";
    echo "  - Mensual es mismo día: " . ($esMismoDiaMensual ? '❌ SÍ' : '✅ NO') . "\n";
    echo "  - Quincenal es mismo día: " . ($esMismoDiaQuincenal ? '❌ SÍ' : '✅ NO') . "\n";
    echo "  - Semanal es mismo día: " . ($esMismoDiaSemanal ? '❌ SÍ' : '✅ NO') . "\n";
    
    // Verificar que todas las fechas son posteriores
    echo "\n  📅 Diferencias con fecha de contrato:\n";
    echo "  - Mensual: " . $fecha_inicio->diffInDays($fecha_mensual) . " días después\n";
    echo "  - Quincenal: " . $fecha_inicio->diffInDays($fecha_quincenal) . " días después\n";
    echo "  - Semanal: " . $fecha_inicio->diffInDays($fecha_semanal) . " días después\n";
}

// Pruebas con diferentes días de inicio
echo "🧪 PROBANDO FECHAS DE PAGO CORRECTAS (después del contrato)\n";
echo "=============================================================";

// Prueba 1: Lunes 15/01/2024
testFechasCorrectas('2024-01-15');

// Prueba 2: Domingo 14/01/2024  
testFechasCorrectas('2024-01-14');

// Prueba 3: Sábado 13/01/2024
testFechasCorrectas('2024-01-13');

// Prueba ESPECÍFICA: 20/03/2026 (el caso que mencionaste)
echo "\n=== PRUEBA ESPECÍFICA: 20/03/2026 ===\n";
$fecha_especifica = Carbon::parse('2026-03-20');
echo "Fecha de contrato: " . $fecha_especifica->format('Y-m-d') . ' (' . $fecha_especifica->format('l') . ")\n";

$cuotas_quincenales = generarCuotasQuincenales($fecha_especifica, 5);

echo "\n📅 CUOTAS QUINCENALES (15 días naturales entre cada una):\n";
foreach ($cuotas_quincenales as $cuota) {
    echo sprintf(
        "  Cuota %d: %s (%s) - %d días después del contrato",
        $cuota['cuota'],
        $cuota['fecha'],
        $cuota['dia'],
        $cuota['diferencia_dias']
    ) . "\n";
}

// Mostrar diferencias entre cuotas consecutivas
echo "\n📊 Diferencias entre cuotas consecutivas:\n";
for ($i = 1; $i < count($cuotas_quincenales); $i++) {
    $fecha_actual = Carbon::parse($cuotas_quincenales[$i]['fecha']);
    $fecha_anterior = Carbon::parse($cuotas_quincenales[$i-1]['fecha']);
    $diferencia = $fecha_anterior->diffInDays($fecha_actual);
    
    echo sprintf(
        "  Entre cuota %d y %d: %d días",
        $i,
        $i+1,
        $diferencia
    ) . "\n";
}

echo "\n✅ Pruebas de fechas completadas\n";