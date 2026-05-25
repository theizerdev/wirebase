<?php

// Simular el proceso de extractInsertStatements para verificar
$sqlFile = 'C:\\laragon\\www\\laradmin\\pastores.sql';

if (!file_exists($sqlFile)) {
    echo "Error: No se encontró el archivo $sqlFile\n";
    exit(1);
}

$content = file_get_contents($sqlFile);

// Limpiar el contenido (igual que en el comando)
$content = str_replace(["\r\n", "\r"], "\n", $content);
$content = preg_replace('/[\x00-\x1F\x7F]/u', ' ', $content);
$content = preg_replace('/\s+/', ' ', $content);

// Buscar TODOS los bloques INSERT
preg_match_all('/INSERT INTO\s+`?pastors`?\s+.*?VALUES\s*(.*?);/s', $content, $allMatches);

echo "Total de bloques INSERT encontrados con regex: " . count($allMatches[0]) . "\n\n";

// Procesar cada bloque como lo haría el comando
$totalRegistros = 0;
foreach ($allMatches[1] as $index => $valuesSection) {
    $blockNumber = $index + 1;
    $valuesSection = trim($valuesSection);

    echo "Bloque $blockNumber:\n";
    echo "  - Longitud: " . strlen($valuesSection) . " caracteres\n";

    // Contar registros usando el mismo método que parseValuesRobust
    $registrosEnBloque = 0;
    $depth = 0;
    $inicio = -1;
    $registros = [];

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

    echo "  - Registros contados: $registrosEnBloque\n";
    echo "  - Primeros 50 caracteres: " . substr($valuesSection, 0, 50) . "...\n\n";

    $totalRegistros += $registrosEnBloque;
}

echo "Total de registros que deberían procesarse: $totalRegistros\n";
