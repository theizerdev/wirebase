<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Leer el archivo SQL y analizar la estructura
$sqlFile = 'C:\laragon\www\mmmvnzla\pastores.sql';

if (!file_exists($sqlFile)) {
    die("Archivo SQL no encontrado: $sqlFile");
}

$content = file_get_contents($sqlFile);

// Buscar la primera instrucción INSERT completa
if (preg_match('/INSERT INTO `pastores`\s*\((.*?)\)\s*VALUES\s*(.*?)\s*;/is', $content, $matches)) {
    $columns = $matches[1];
    $values = $matches[2];

    echo "=== COLUMNAS EN LA TABLA PASTORES ===\n\n";
    echo $columns . "\n\n";

    echo "=== PRIMER REGISTRO DE VALORES ===\n\n";
    echo $values . "\n\n";

    // Parsear las columnas
    $columnList = array_map(function($col) {
        return trim($col, '` ');
    }, explode(',', $columns));

    echo "=== LISTA DE COLUMNAS (" . count($columnList) . " total) ===\n\n";
    foreach ($columnList as $index => $column) {
        echo str_pad($index, 3) . ": " . str_pad($column, 25) . "\n";
    }

    // Parsear el primer registro
    if (preg_match('/\((.*?)\)/s', $values, $valueMatch)) {
        $recordData = $valueMatch[1];

        // Dividir por comas respetando comillas
        $valuesList = [];
        $current = '';
        $inQuotes = false;
        $quoteChar = '';

        for ($i = 0; $i < strlen($recordData); $i++) {
            $char = $recordData[$i];

            if (($char === "'" || $char === '"') && !$inQuotes) {
                $inQuotes = true;
                $quoteChar = $char;
            } elseif ($char === $quoteChar && $inQuotes) {
                $prevChar = $i > 0 ? $recordData[$i - 1] : '';
                if ($prevChar !== '\\') {
                    $inQuotes = false;
                    $quoteChar = '';
                }
            } elseif ($char === ',' && !$inQuotes) {
                $valuesList[] = trim($current);
                $current = '';
                continue;
            }

            $current .= $char;
        }

        if (!empty($current)) {
            $valuesList[] = trim($current);
        }

        echo "\n=== VALORES DEL PRIMER REGISTRO ===\n\n";
        foreach ($valuesList as $index => $value) {
            $columnName = $columnList[$index] ?? 'UNKNOWN';
            echo str_pad($index, 3) . ": " . str_pad($columnName, 25) . " => " . substr($value, 0, 50) . (strlen($value) > 50 ? '...' : '') . "\n";
        }
    }
} else {
    echo "No se encontró ninguna instrucción INSERT válida\n";
}
