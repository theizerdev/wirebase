<?php

// Script mejorado para parsear el archivo SQL
$sqlFile = 'pastores.sql';

if (!file_exists($sqlFile)) {
    echo "Archivo no encontrado: $sqlFile\n";
    exit(1);
}

$content = file_get_contents($sqlFile);

// Buscar el bloque INSERT con un patrón más flexible
if (preg_match('/INSERT INTO\s+`?pastors`?\s+.*?VALUES\s*(.*?);/s', $content, $matches)) {
    $valuesSection = $matches[1];
    echo "Sección VALUES encontrada, longitud: " . strlen($valuesSection) . "\n";

    // Método 1: Contar patrones de cierre de paréntesis
    preg_match_all('/\)\s*,/s', $valuesSection, $matches1);
    $count1 = count($matches1[0]);
    echo "Método 1 (patrón \\)),: $count1 registros\n";

    // Método 2: Contar manualmente los paréntesis balanceados
    $count2 = 0;
    $depth = 0;
    $inRecord = false;
    $length = strlen($valuesSection);

    for ($i = 0; $i < $length; $i++) {
        $char = $valuesSection[$i];

        if ($char === '(' && $depth === 0) {
            $inRecord = true;
            $depth = 1;
        } elseif ($inRecord) {
            if ($char === '(') {
                $depth++;
            } elseif ($char === ')') {
                $depth--;
                if ($depth === 0) {
                    $count2++;
                    $inRecord = false;
                }
            }
        }
    }
    echo "Método 2 (paréntesis balanceados): $count2 registros\n";

    // Método 3: Extraer todos los registros completos
    preg_match_all('/\([^)]+\)/s', $valuesSection, $matches3);
    $count3 = count($matches3[0]);
    echo "Método 3 (patrón simple): $count3 registros\n";

    // Mostrar algunos ejemplos de registros extraídos
    echo "\nPrimeros 3 registros extraídos:\n";
    for ($i = 0; $i < min(3, $count3); $i++) {
        echo "Registro " . ($i + 1) . ": " . substr($matches3[0][$i], 0, 100) . "...\n";
    }

    // Verificar si hay registros truncados
    echo "\nVerificando registros truncados...\n";
    $truncated = 0;
    foreach ($matches3[0] as $record) {
        if (substr_count($record, "'") % 2 !== 0) {
            $truncated++;
        }
    }
    echo "Registros con comillas desbalanceadas (posiblemente truncados): $truncated\n";

} else {
    echo "No se encontró el bloque INSERT en el archivo\n";
}
