<?php

// Script para depurar el proceso de edad en detalle
$sqlFile = 'pastores.sql';

if (!file_exists($sqlFile)) {
    die("Error: No se encontró el archivo $sqlFile\n");
}

$content = file_get_contents($sqlFile);

// Encontrar todos los bloques INSERT
preg_match_all('/INSERT INTO `pastors`[^;]+/', $content, $matches);

$totalRecords = 0;
$edadValidas = 0;
$edadCero = 0;
$edadVacia = 0;
$ejemplosEdad = [];

foreach ($matches[0] as $insert) {
    // Encontrar todos los VALUES
    preg_match_all('/\(([^)]+)\)/', $insert, $valuesMatches);

    foreach ($valuesMatches[1] as $valuesStr) {
        $totalRecords++;

        // Parsear los valores
        $values = [];
        $current = '';
        $inQuotes = false;
        $escape = false;

        for ($i = 0; $i < strlen($valuesStr); $i++) {
            $char = $valuesStr[$i];

            if ($escape) {
                $current .= $char;
                $escape = false;
                continue;
            }

            if ($char === '\\') {
                $escape = true;
                $current .= $char;
                continue;
            }

            if ($char === "'" && !$escape) {
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

        // El campo edad está en el índice 9 (10º campo)
        $edad = isset($values[9]) ? trim($values[9], "'\"") : '';

        // Depurar valores
        if ($edad === '' || $edad === 'NULL') {
            $edadVacia++;
        } elseif ($edad === '0' || $edad === '00') {
            $edadCero++;
        } elseif (is_numeric($edad) && $edad > 0) {
            $edadValidas++;
            if (count($ejemplosEdad) < 10) {
                $ejemplosEdad[] = [
                    'edad' => $edad,
                    'codigo' => isset($values[1]) ? trim($values[1], "'\"") : 'N/A',
                    'nombres' => isset($values[3]) ? trim($values[3], "'\"") : 'N/A'
                ];
            }
        }
    }
}

echo "=== ANÁLISIS DETALLADO DE EDADES EN SQL ===\n";
echo "Total de registros: $totalRecords\n";
echo "Edades válidas (>0): $edadValidas\n";
echo "Edades cero ('0', '00'): $edadCero\n";
echo "Edades vacías/NULL: $edadVacia\n";
echo "\nEjemplos de edades válidas:\n";
foreach ($ejemplosEdad as $ejemplo) {
    echo "- Código: {$ejemplo['codigo']}, Nombre: {$ejemplo['nombres']}, Edad: {$ejemplo['edad']}\n";
}
