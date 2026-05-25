<?php

// Leer el archivo SQL
$sqlContent = file_get_contents('pastores.sql');

// Buscar patrones de INSERT
preg_match_all('/INSERT INTO[^;]+/', $sqlContent, $matches);

echo "=== EXAMINANDO VALORES DE ZONA Y DISTRITO ===\n\n";

$ejemplos = 0;
foreach ($matches[0] as $insert) {
    if ($ejemplos >= 10) break; // Limitar a 10 ejemplos

    // Extraer los valores entre paréntesis
    preg_match_all('/\(([^)]+)\)/', $insert, $valueMatches);

    foreach ($valueMatches[1] as $values) {
        // Separar por comas, pero manejar comillas
        $parts = [];
        $current = '';
        $inQuotes = false;
        $quoteChar = '';

        for ($i = 0; $i < strlen($values); $i++) {
            $char = $values[$i];

            if (($char === "'" || $char === '"') && !$inQuotes) {
                $inQuotes = true;
                $quoteChar = $char;
            } elseif ($char === $quoteChar && $inQuotes) {
                // Verificar si está escapado
                $prevChar = $i > 0 ? $values[$i - 1] : '';
                if ($prevChar !== '\\') {
                    $inQuotes = false;
                    $quoteChar = '';
                }
            } elseif ($char === ',' && !$inQuotes) {
                $parts[] = trim($current);
                $current = '';
                continue;
            }

            $current .= $char;
        }

        if (!empty($current)) {
            $parts[] = trim($current);
        }

        // Verificar que tengamos suficientes columnas
        if (count($parts) >= 8) {
            $zona = $parts[6]; // Columna 7 (índice 6)
            $distrito = $parts[7]; // Columna 8 (índice 7)
            $nombre = $parts[2]; // Nombre para referencia
            $apellido = $parts[3]; // Apellido para referencia

            echo "Ejemplo " . ($ejemplos + 1) . ":\n";
            echo "  Pastor: " . trim($nombre, "'\"") . " " . trim($apellido, "'\"") . "\n";
            echo "  Zona: $zona\n";
            echo "  Distrito: $distrito\n";
            echo "\n";

            $ejemplos++;
        }
    }
}

echo "=== RESUMEN ===\n";
echo "Se examinaron $ejemplos registros.\n";
