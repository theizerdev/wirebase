<?php

$sqlFile = 'C:\\laragon\\www\\mmmvnzla\\pastores.sql';

if (!file_exists($sqlFile)) {
    echo "Error: No se encontró el archivo $sqlFile\n";
    exit(1);
}

$content = file_get_contents($sqlFile);

// Buscar la estructura de la tabla (columnas)
if (preg_match('/CREATE TABLE.*?`pastors`?\s*\((.*?)\)/s', $content, $createTableMatch)) {
    echo "=== ESTRUCTURA DE LA TABLA PASTORS ===\n";
    $columnsSection = $createTableMatch[1];

    // Extraer nombres de columnas
    preg_match_all('/`(\w+)`/s', $columnsSection, $columnMatches);

    echo "Columnas encontradas:\n";
    foreach ($columnMatches[1] as $index => $column) {
        echo ($index + 1) . ". $column\n";
    }
    echo "\nTotal de columnas: " . count($columnMatches[1]) . "\n\n";
}

// Buscar un registro de ejemplo para ver la estructura
preg_match_all('/INSERT INTO\s+`?pastors`?\s+.*?VALUES\s*(.*?);/s', $content, $allMatches);

echo "=== ANÁLISIS DE REGISTROS DE EJEMPLO ===\n\n";

foreach ($allMatches[1] as $blockIndex => $valuesSection) {
    echo "Bloque " . ($blockIndex + 1) . ":\n";

    // Extraer los primeros 3 registros de cada bloque
    preg_match_all('/\((.*?)\)/s', $valuesSection, $registroMatches);

    for ($i = 0; $i < min(3, count($registroMatches[1])); $i++) {
        $registro = $registroMatches[1][$i];
        $valores = explode(',', $registro);

        echo "  Registro " . ($i + 1) . " (" . count($valores) . " valores):\n";

        // Mostrar los primeros 10 valores
        for ($j = 0; $j < min(10, count($valores)); $j++) {
            $valor = trim($valores[$j]);
            echo "    Campo " . ($j + 1) . ": $valor\n";
        }

        if (count($valores) > 10) {
            echo "    ... y " . (count($valores) - 10) . " campos más\n";
        }
        echo "\n";
    }

    echo "\n";
    break; // Solo analizar el primer bloque
}
