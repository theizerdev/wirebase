<?php

// Script para contar los registros en el archivo SQL
$sqlFile = 'pastores.sql';

if (!file_exists($sqlFile)) {
    echo "Archivo no encontrado: $sqlFile\n";
    exit(1);
}

$content = file_get_contents($sqlFile);

// Buscar el bloque INSERT
if (preg_match('/INSERT INTO `pastors`.*?VALUES\s*(.*?);/s', $content, $matches)) {
    $valuesSection = $matches[1];

    // Contar cuántos grupos de valores hay
    // Cada grupo está entre paréntesis y termina con ),
    preg_match_all('/\),?\s*\n?\r?/s', $valuesSection, $matches);

    $count = count($matches[0]);
    echo "Número de registros encontrados en el SQL: $count\n";

    // También contemos manualmente los paréntesis de apertura
    $openCount = substr_count($valuesSection, '(');
    echo "Número de paréntesis de apertura: $openCount\n";

    // Verificar si hay algún patrón especial
    echo "Primeros 500 caracteres de la sección VALUES:\n";
    echo substr($valuesSection, 0, 500) . "\n";

    // Ver los últimos 200 caracteres
    echo "\nÚltimos 200 caracteres de la sección VALUES:\n";
    echo substr($valuesSection, -200) . "\n";

} else {
    echo "No se encontró el bloque INSERT en el archivo\n";
}
