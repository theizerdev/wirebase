<?php

$sqlFile = 'C:\\laragon\\www\\laradmin\\pastores.sql';

if (!file_exists($sqlFile)) {
    echo "Error: No se encontró el archivo $sqlFile\n";
    exit(1);
}

$content = file_get_contents($sqlFile);

// Buscar la estructura de la tabla para ver los tipos de datos
if (preg_match('/CREATE TABLE.*?`pastors`?\s*\((.*?)\)/s', $content, $createTableMatch)) {
    echo "=== ESTRUCTURA DE LA TABLA PASTORS ===\n";
    $columnsSection = $createTableMatch[1];

    // Extraer definiciones de columnas
    preg_match_all('/`(\w+)`\s+([^,\n]+)/s', $columnsSection, $columnMatches, PREG_SET_ORDER);

    echo "Columnas con sus tipos de datos:\n";
    foreach ($columnMatches as $index => $match) {
        $columnName = $match[1];
        $columnType = trim($match[2]);
        echo ($index + 1) . ". $columnName => $columnType\n";
    }
    echo "\n";
}

// Ahora vamos a ver qué valores están causando problemas
preg_match_all('/INSERT INTO\s+`?pastors`?\s+.*?VALUES\s*(.*?);/s', $content, $allMatches);

echo "=== ANALIZANDO REGISTROS CON ERRORES ===\n\n";

// Buscar registros que contienen los valores problemáticos
$valoresProblematicos = ['PRESBITERO', 'SECRETARIA DE ZONA', 'Colaborador', 'pastor', 'DOCENTE SEMINARIO ELIM', 'Presbitero'];

foreach ($allMatches[1] as $blockIndex => $valuesSection) {
    preg_match_all('/\((.*?)\)/s', $valuesSection, $registroMatches);

    foreach ($registroMatches[1] as $regIndex => $registro) {
        $encontrado = false;
        foreach ($valoresProblematicos as $valor) {
            if (stripos($registro, $valor) !== false) {
                $encontrado = true;
                break;
            }
        }

        if ($encontrado) {
            echo "Registro $regIndex (Bloque " . ($blockIndex + 1) . "):\n";

            // Parsear los valores
            $valores = [];
            $current = '';
            $inQuotes = false;
            $quoteChar = '';

            for ($i = 0; $i < strlen($registro); $i++) {
                $char = $registro[$i];

                if (($char === "'" || $char === '"') && !$inQuotes) {
                    $inQuotes = true;
                    $quoteChar = $char;
                } elseif ($char === $quoteChar && $inQuotes) {
                    $prevChar = $i > 0 ? $registro[$i - 1] : '';
                    if ($prevChar !== '\\') {
                        $inQuotes = false;
                        $quoteChar = '';
                    }
                } elseif ($char === ',' && !$inQuotes) {
                    $valores[] = trim($current);
                    $current = '';
                    continue;
                }

                $current .= $char;
            }

            if (!empty($current)) {
                $valores[] = trim($current);
            }

            echo "  Total de campos: " . count($valores) . "\n";

            // Mostrar solo los campos que contienen valores problemáticos
            foreach ($valores as $i => $valor) {
                foreach ($valoresProblematicos as $problematico) {
                    if (stripos($valor, $problematico) !== false) {
                        echo "  Campo " . ($i + 1) . ": $valor\n";
                        break;
                    }
                }
            }
            echo "\n";

            // Solo mostrar unos pocos ejemplos
            if ($regIndex >= 5) break 2;
        }
    }
}
