<?php

// Leer el archivo SQL
$sqlContent = file_get_contents('pastores.sql');

// Buscar los INSERT statements
preg_match_all('/INSERT INTO\s+`?pastors`?\s+.*?VALUES\s*(.*?);/s', $sqlContent, $allMatches);

$problematicValues = ['SUPERVISOR NACIONAL', 'Presidenta Directiva Nacional de Damas', 'Presbitero', 'PASTOR', 'Colaborador'];

$recordsAnalyzed = 0;
$matchesFound = 0;

foreach ($allMatches[1] as $blockIndex => $valuesSection) {
    // Buscar cada registro individual
    preg_match_all('/\((.*?)\)/s', $valuesSection, $records);

    foreach ($records[1] as $recordIndex => $recordData) {
        $recordsAnalyzed++;

        // Dividir por comas respetando comillas
        $values = [];
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
                $values[] = trim($current);
                $current = '';
                continue;
            }

            $current .= $char;
        }

        if (!empty($current)) {
            $values[] = trim($current);
        }

        // Verificar si hay valores problemáticos
        foreach ($values as $fieldIndex => $value) {
            $cleanValue = trim($value, "'");
            foreach ($problematicValues as $problematic) {
                if (strpos($cleanValue, $problematic) !== false) {
                    $matchesFound++;
                    echo "Registro $recordsAnalyzed (Bloque " . ($blockIndex + 1) . ", Registro " . ($recordIndex + 1) . "):\n";
                    echo "  Campo " . ($fieldIndex + 1) . ": $cleanValue\n";
                    echo "  Total campos: " . count($values) . "\n";

                    // Mostrar algunos campos adicionales para contexto
                    if (isset($values[1])) echo "  Campo 2 (código): " . trim($values[1], "'") . "\n";
                    if (isset($values[2])) echo "  Campo 3 (nombres): " . trim($values[2], "'") . "\n";
                    if (isset($values[3])) echo "  Campo 4 (apellidos): " . trim($values[3], "'") . "\n";

                    echo "\n";
                    break;
                }
            }
        }

        if ($recordsAnalyzed >= 50) { // Limitar para no saturar
            break;
        }
    }

    if ($recordsAnalyzed >= 50) {
        break;
    }
}

echo "Total de registros analizados: $recordsAnalyzed\n";
echo "Total de coincidencias encontradas: $matchesFound\n";
