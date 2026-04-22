<?php

// Script de prueba para verificar el cálculo correcto de quincenas (15 días hábiles)

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

// Función de prueba para generar fechas de pago quincenales CORRECTAS
function testQuincenasCorrectas($fechaInicio, $numCuotas = 4) {
    echo "\n=== QUINCENAS CORRECTAS desde $fechaInicio ===\n";
    
    $fecha_pago = Carbon::parse($fechaInicio);
    $fechas = [];
    
    // Primera cuota: buscar el próximo día hábil
    $fecha_pago = getNextBusinessDay($fecha_pago);
    
    for ($i = 1; $i <= $numCuotas; $i++) {
        $fechas[] = [
            'cuota' => $i,
            'fecha' => $fecha_pago->format('Y-m-d'),
            'dia' => $fecha_pago->format('l')
        ];
        
        // Para la próxima cuota: sumar exactamente 15 días hábiles
        $fecha_pago = addBusinessDays($fecha_pago, 15);
    }
    
    foreach ($fechas as $item) {
        echo "  Cuota {$item['cuota']}: {$item['fecha']} ({$item['dia']})\n";
    }
    
    // Mostrar diferencia en días entre cuotas
    echo "  Diferencias entre cuotas:\n";
    for ($i = 1; $i < count($fechas); $i++) {
        $fechaAnterior = Carbon::parse($fechas[$i-1]['fecha']);
        $fechaActual = Carbon::parse($fechas[$i]['fecha']);
        $diasDiferencia = $fechaAnterior->diffInDays($fechaActual);
        echo "    Entre cuota " . ($i) . " y " . ($i+1) . ": $diasDiferencia días\n";
    }
}

// Pruebas con diferentes días de inicio
echo "🧪 PROBANDO QUINCENAS CORRECTAS (15 días hábiles)\n";
echo "=====================================================";

// Prueba 1: Lunes
testQuincenasCorrectas('2026-03-20'); // Viernes



echo "\n✅ Pruebas de quincenas completadas\n";