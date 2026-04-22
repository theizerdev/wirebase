<?php

// Script de prueba para verificar el cálculo de fechas de pago con días hábiles

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

// Función de prueba para generar fechas de pago
function testPaymentDates($fechaInicio, $frecuencia, $numCuotas = 5) {
    echo "\n=== PRUEBA: $frecuencia desde $fechaInicio ===\n";
    
    $fecha_pago = Carbon::parse($fechaInicio);
    $fechas = [];
    
    if ($frecuencia === 'semanal') {
        // Para semanal: buscar el próximo día hábil (Lunes a Sábado) a partir de la fecha
        $fecha_pago = getNextBusinessDay($fecha_pago);
        
        for ($i = 1; $i <= $numCuotas; $i++) {
            $fechas[] = $fecha_pago->format('Y-m-d') . ' (' . $fecha_pago->format('l') . ')';
            // Sumar 7 días y buscar el próximo día hábil
            $fecha_pago->addDays(7);
            $fecha_pago = getNextBusinessDay($fecha_pago);
        }
    } elseif ($frecuencia === 'quincenal') {
        // Para quincenal: buscar el próximo día hábil (Lunes a Sábado) a partir de la fecha
        $fecha_pago = getNextBusinessDay($fecha_pago);
        
        for ($i = 1; $i <= $numCuotas; $i++) {
            // Buscar la próxima quincena (15 o fin de mes)
            $d15 = Carbon::create($fecha_pago->year, $fecha_pago->month, 15);
            $eom = $fecha_pago->copy()->endOfMonth();
            
            if ($fecha_pago->lte($d15)) {
                $fecha_pago = $d15;
            } elseif ($fecha_pago->lte($eom)) {
                $fecha_pago = $eom;
            } else {
                // Si ya pasó el fin de mes, ir al 15 del siguiente mes
                $fecha_pago = Carbon::create($fecha_pago->copy()->addMonth()->year, $fecha_pago->copy()->addMonth()->month, 15);
            }
            
            // Asegurar que sea día hábil
            $fecha_pago = getNextBusinessDay($fecha_pago);
            
            $fechas[] = $fecha_pago->format('Y-m-d') . ' (' . $fecha_pago->format('l') . ')';
            
            // Mover al día siguiente para la próxima iteración
            $fecha_pago->addDay();
        }
    }
    
    echo "Fechas generadas:\n";
    foreach ($fechas as $index => $fecha) {
        echo "  Cuota " . ($index + 1) . ": $fecha\n";
    }
}

// Pruebas con diferentes días de inicio
echo "🧪 PROBANDO SISTEMA DE CÁLCULO DE FECHAS DE PAGO\n";
echo "=====================================================";

// Prueba 1: Lunes
testPaymentDates('2024-01-15', 'semanal'); // Lunes
testPaymentDates('2024-01-15', 'quincenal'); // Lunes

// Prueba 2: Domingo
testPaymentDates('2024-01-14', 'semanal'); // Domingo
testPaymentDates('2024-01-14', 'quincenal'); // Domingo

// Prueba 3: Sábado
testPaymentDates('2024-01-13', 'semanal'); // Sábado
testPaymentDates('2024-01-13', 'quincenal'); // Sábado

// Prueba 4: Miércoles
testPaymentDates('2024-01-17', 'semanal'); // Miércoles
testPaymentDates('2024-01-17', 'quincenal'); // Miércoles

echo "\n✅ Pruebas completadas\n";