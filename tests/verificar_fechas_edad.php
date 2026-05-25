<?php

// Verificar cuántos registros tienen valores válidos de edad y fecha

$content = file_get_contents('pastores.sql');

// Buscar patrones INSERT
preg_match_all('/INSERT INTO `pastores`.*?\((.*?)\)/s', $content, $matches);

$total = 0;
$conEdad = 0;
$conFecha = 0;
$conAmbos = 0;

foreach ($matches[1] as $valuesStr) {
    $total++;

    // Parsear valores
    preg_match_all('/(?:[^,\'\(]*(?:\([^)]*\))?[^,\'\(]*(?:\'[^\']*\')?[^,\'\(]*)/', $valuesStr, $valueMatches);
    $values = $valueMatches[0];

    // Limpiar valores
    $cleanedValues = [];
    foreach ($values as $value) {
        $value = trim($value);
        if (preg_match('/^\'(.+)\'$/', $value, $matches)) {
            $cleanedValues[] = $matches[1];
        } else {
            $cleanedValues[] = $value;
        }
    }

    // Verificar edad (índice 9) y fecha (índice 12)
    $edad = isset($cleanedValues[9]) ? trim($cleanedValues[9]) : null;
    $fecha = isset($cleanedValues[12]) ? trim($cleanedValues[12]) : null;

    $tieneEdad = $edad && $edad !== '' && $edad !== 'NULL' && is_numeric($edad);
    $tieneFecha = $fecha && $fecha !== '' && $fecha !== 'NULL' && strlen($fecha) > 4;

    if ($tieneEdad) $conEdad++;
    if ($tieneFecha) $conFecha++;
    if ($tieneEdad && $tieneFecha) $conAmbos++;

    if ($total <= 10) {
        echo "Registro $total: Edad='$edad' | Fecha='$fecha'\n";
    }
}

echo "\n=== RESUMEN ===\n";
echo "Total registros: $total\n";
echo "Con edad válida: $conEdad (" . round($conEdad/$total*100, 1) . "%)\n";
echo "Con fecha válida: $conFecha (" . round($conFecha/$total*100, 1) . "%)\n";
echo "Con ambos: $conAmbos (" . round($conAmbos/$total*100, 1) . "%)\n";
