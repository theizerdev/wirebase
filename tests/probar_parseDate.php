<?php

require_once __DIR__ . '/vendor/autoload.php';

use Carbon\Carbon;

// Función parseDate copiada del archivo original
function parseDate(?string $date): ?string
{
    if (empty($date) || $date === '0000-00-00' || $date === '0000-00-00 00:00:00') {
        return null;
    }

    // Detectar si es una cadena que claramente no es una fecha
    $nonDatePatterns = [
        '/^[A-Z\s]+$/',           // Solo letras mayúsculas y espacios (ej: "SUPERVISOR NACIONAL")
        '/^(PASTOR|COLABORADOR|PRESIDENTE|DIRECTIVA|MINISTRO)/i', // Títulos
        '/^[A-Z][a-z]+\s+[A-Z][a-z]+$/', // Nombres propios (ej: "Juan Perez")
        '/^\d+$/',                // Solo números
        '/^(SI|NO)$/i',           // Valores booleanos
    ];

    foreach ($nonDatePatterns as $pattern) {
        if (preg_match($pattern, trim($date))) {
            echo "Campo detectado como no-fecha: '{$date}' - se omite\n";
            return null;
        }
    }

    try {
        // Intentar diferentes formatos de fecha
        $carbonDate = Carbon::parse($date);
        return $carbonDate->format('Y-m-d');
    } catch (\Exception $e) {
        // Si no se puede parsear, intentar formatos alternativos
        try {
            // Formato: d/m/Y
            if (preg_match('/^\d{1,2}\/\d{1,2}\/\d{4}$/', $date)) {
                return Carbon::createFromFormat('d/m/Y', $date)->format('Y-m-d');
            }

            // Formato: Y-m-d H:i:s
            if (preg_match('/^\d{4}-\d{1,2}-\d{1,2} \d{1,2}:\d{1,2}:\d{1,2}$/', $date)) {
                return Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('Y-m-d');
            }

            // Formato: d-m-Y
            if (preg_match('/^\d{1,2}-\d{1,2}-\d{4}$/', $date)) {
                return Carbon::createFromFormat('d-m-Y', $date)->format('Y-m-d');
            }
        } catch (\Exception $e2) {
            // Ningún formato funcionó
            echo "No se pudo parsear como fecha: '{$date}' - se omite\n";
            return null;
        }

        echo "Formato de fecha no reconocido: '{$date}' - se omite\n";
        return null;
    }
}

// Probar con los valores que vimos en el SQL
$fechasDePrueba = [
    '29-04-1965',
    '20-01-1966',
    '30-03-1970',
    '11-09-1969',
    '23-12-1962',
    '11-10-1970',
    '15-10-1964',
    '05-10-1968',
    '02-02-1970'
];

echo "=== PROBANDO PARSE DATE ===\n\n";

foreach ($fechasDePrueba as $fecha) {
    echo "Probando: '$fecha' -> ";
    $resultado = parseDate($fecha);
    echo $resultado ?? 'NULL';
    echo "\n";
}

echo "\n=== RESUMEN ===\n";
echo "Las fechas en formato d-m-Y deberían parsearse correctamente.\n";
