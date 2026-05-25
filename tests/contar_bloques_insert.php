<?php

$sqlFile = 'C:\\laragon\\www\\mmmvnzla\\pastores.sql';

if (!file_exists($sqlFile)) {
    echo "Error: No se encontró el archivo $sqlFile\n";
    exit(1);
}

$content = file_get_contents($sqlFile);

// Contar todos los bloques INSERT
preg_match_all('/INSERT INTO\s+`?pastors`?\s+.*?VALUES\s*(.*?);/s', $content, $allMatches);

echo "Total de bloques INSERT encontrados: " . count($allMatches[0]) . "\n\n";

// Mostrar información de cada bloque
$totalRegistros = 0;
foreach ($allMatches[1] as $index => $valuesSection) {
    $valuesSection = trim($valuesSection);

    // Contar registros en este bloque (buscando conjuntos de valores)
    $registrosEnBloque = 0;
    $depth = 0;
    $inicio = -1;

    for ($i = 0; $i < strlen($valuesSection); $i++) {
        $char = $valuesSection[$i];

        if ($char === '(' && $depth === 0) {
            $inicio = $i;
            $depth = 1;
        } elseif ($char === '(') {
            $depth++;
        } elseif ($char === ')' && $depth === 1) {
            $registrosEnBloque++;
            $depth = 0;
            $inicio = -1;
        } elseif ($char === ')') {
            $depth--;
        }
    }

    echo "Bloque " . ($index + 1) . ":\n";
    echo "  - Longitud: " . strlen($valuesSection) . " caracteres\n";
    echo "  - Registros: $registrosEnBloque\n";
    echo "  - Primeros 100 caracteres: " . substr($valuesSection, 0, 100) . "...\n\n";

    $totalRegistros += $registrosEnBloque;
}

echo "Total de registros en todos los bloques: $totalRegistros\n";
