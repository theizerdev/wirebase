<?php

// Script de depuración para el archivo SQL
$sqlFile = 'pastores.sql';

if (!file_exists($sqlFile)) {
    echo "Archivo no encontrado: $sqlFile\n";
    exit(1);
}

$content = file_get_contents($sqlFile);
echo "Tamaño del archivo: " . strlen($content) . " bytes\n";
echo "Primeros 500 caracteres:\n";
echo substr($content, 0, 500) . "\n\n";

// Buscar el patrón INSERT INTO
if (preg_match('/INSERT INTO `pastors`.*?VALUES\s*(.*?);/s', $content, $matches)) {
    echo "¡Encontré el bloque INSERT!\n";
    echo "Tamaño del bloque: " . strlen($matches[1]) . " bytes\n";

    // Ver los primeros 200 caracteres del bloque
    echo "Primeros 200 caracteres del bloque:\n";
    echo substr($matches[1], 0, 200) . "\n\n";

    // Contar paréntesis de apertura
    $openCount = substr_count($matches[1], '(');
    echo "Número de paréntesis de apertura: $openCount\n";

    // Contar paréntesis de cierre
    $closeCount = substr_count($matches[1], ')');
    echo "Número de paréntesis de cierre: $closeCount\n";

} else {
    echo "No encontré el patrón INSERT INTO...\n";

    // Intentar con un patrón más simple
    if (preg_match('/INSERT INTO/s', $content)) {
        echo "Pero sí encontré 'INSERT INTO' en el archivo\n";
    }
}

echo "\nÚltimos 200 caracteres del archivo:\n";
echo substr($content, -200) . "\n";
