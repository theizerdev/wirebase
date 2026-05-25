<?php

// Script para depurar el proceso de migración de edades
require_once 'vendor/autoload.php';

use App\Models\Pastor;

$sqlFile = 'pastores.sql';

if (!file_exists($sqlFile)) {
    die("Error: No se encontró el archivo $sqlFile\n");
}

$content = file_get_contents($sqlFile);

// Encontrar un bloque INSERT de muestra
preg_match('/INSERT INTO `pastors`[^;]+/', $content, $matches);

if (empty($matches)) {
    die("No se encontraron bloques INSERT\n");
}

$insert = $matches[0];
echo "=== ANÁLISIS DE BLOQUE INSERT MUESTRA ===\n";
echo "Primeras 500 caracteres:\n" . substr($insert, 0, 500) . "...\n\n";

// Encontrar el primer VALUES
preg_match('/\(([^)]+)\)/', $insert, $valuesMatch);

if (empty($valuesMatch)) {
    die("No se encontraron valores\n");
}

$valuesStr = $valuesMatch[1];
echo "Primer VALUES completo:\n$valuesStr\n\n";

// Parsear los valores manualmente
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

echo "=== VALORES PARSEADOS ===\n";
for ($i = 0; $i < count($values); $i++) {
    echo "Índice $i: {$values[$i]}\n";
    if ($i === 9) {
        echo "  ⭐ ESTE ES EL CAMPO EDAD ⭐\n";
    }
}

// Análisis específico del campo edad
$edadRaw = isset($values[9]) ? $values[9] : 'NO EXISTE';
echo "\n=== ANÁLISIS CAMPO EDAD (ÍNDICE 9) ===\n";
echo "Valor crudo: '$edadRaw'\n";
echo "Trim: '" . trim($edadRaw) . "'\n";
echo "¿Está seteado? " . (isset($values[9]) ? 'SÍ' : 'NO') . "\n";
echo "¿No está vacío? " . (isset($values[9]) && trim($values[9]) !== '' ? 'SÍ' : 'NO') . "\n";
echo "¿No es NULL? " . (isset($values[9]) && trim($values[9]) !== 'NULL' ? 'SÍ' : 'NO') . "\n";

if (isset($values[9]) && trim($values[9]) !== '' && trim($values[9]) !== 'NULL') {
    $edadInt = (int) trim($values[9]);
    echo "Valor como int: $edadInt\n";
    echo "¿Mayor que 0? " . ($edadInt > 0 ? 'SÍ' : 'NO') . "\n";
}

// Verificar en la BD
$pastor = Pastor::where('codigo', trim($values[1], "'"))->first();
if ($pastor) {
    echo "\n=== ESTADO EN BASE DE DATOS ===\n";
    echo "Pastor encontrado: {$pastor->nombres} {$pastor->apellidos}\n";
    echo "Edad en BD: " . ($pastor->edad ?? 'NULL') . "\n";
} else {
    echo "\n=== ESTADO EN BASE DE DATOS ===\n";
    echo "Pastor no encontrado en BD\n";
}
