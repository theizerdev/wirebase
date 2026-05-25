<?php

// Script para verificar los valores de edad y fe_nacimiento en el SQL

$content = file_get_contents('pastores.sql');

// Buscar patrones INSERT
preg_match_all('/INSERT INTO `pastores`.*?\((.*?)\).*?\((.*?)\)/s', $content, $matches);

echo "=== ANÁLISIS DE CAMPOS EDAD Y FE_NACIMIENTO ===\n\n";

$edadExamples = [];
$fechaExamples = [];
$totalRecords = 0;

foreach ($matches[2] as $index => $valuesStr) {
    $totalRecords++;

    // Parsear valores manualmente
    $values = [];
    $current = '';
    $inQuotes = false;
    $escapeNext = false;

    for ($i = 0; $i < strlen($valuesStr); $i++) {
        $char = $valuesStr[$i];

        if ($escapeNext) {
            $current .= $char;
            $escapeNext = false;
            continue;
        }

        if ($char === '\\') {
            $escapeNext = true;
            $current .= $char;
            continue;
        }

        if ($char === "'" && !$escapeNext) {
            $inQuotes = !$inQuotes;
        }

        if ($char === ',' && !$inQuotes) {
            $values[] = trim($current);
            $current = '';
            continue;
        }

        $current .= $char;
    }

    if ($current !== '') {
        $values[] = trim($current);
    }

    // Extraer edad (índice 9) y fe_nacimiento (índice 12)
    $edad = isset($values[9]) ? $values[9] : 'NO ENCONTRADO';
    $fecha = isset($values[12]) ? $values[12] : 'NO ENCONTRADO';

    // Guardar ejemplos únicos
    if (!in_array($edad, $edadExamples) && count($edadExamples) < 20) {
        $edadExamples[] = $edad;
    }

    if (!in_array($fecha, $fechaExamples) && count($fechaExamples) < 20) {
        $fechaExamples[] = $fecha;
    }

    if ($totalRecords >= 50) break; // Limitar a los primeros 50 registros
}

echo "=== EJEMPLOS DE VALORES EDAD (campo 9) ===\n";
foreach ($edadExamples as $example) {
    echo "Edad: $example\n";
}

echo "\n=== EJEMPLOS DE VALORES FE_NACIMIENTO (campo 12) ===\n";
foreach ($fechaExamples as $example) {
    echo "Fecha: $example\n";
}

echo "\n=== TOTAL REGISTROS ANALIZADOS: $totalRecords ===\n";
