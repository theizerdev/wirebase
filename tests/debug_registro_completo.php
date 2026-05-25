<?php

$sqlFile = 'C:\\laragon\\www\\mmmvnzla\\pastores.sql';

if (!file_exists($sqlFile)) {
    echo "Error: No se encontró el archivo $sqlFile\n";
    exit(1);
}

$content = file_get_contents($sqlFile);

// Buscar un registro de ejemplo completo
preg_match_all('/INSERT INTO\s+`?pastors`?\s+.*?VALUES\s*(.*?);/s', $content, $allMatches);

// Tomar el primer bloque y analizar un registro con problemas
$valuesSection = $allMatches[1][0];

// Buscar un registro que contenga "PRESBITERO" o valores similares
preg_match_all('/\((.*?)\)/s', $valuesSection, $registroMatches);

echo "=== BUSCANDO REGISTROS CON VALORES COMO 'PRESBITERO' ===\n\n";

foreach ($registroMatches[1] as $index => $registro) {
    if (stripos($registro, 'PRESBITERO') !== false ||
        stripos($registro, 'SECRETARIA') !== false ||
        stripos($registro, 'Colaborador') !== false) {

        echo "Registro $index (contiene valores problemáticos):\n";
        echo "Contenido completo: $registro\n\n";

        // Dividir por comas respetando comillas
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

        // Agregar el último valor
        if (!empty($current)) {
            $valores[] = trim($current);
        }

        echo "Valores parseados (" . count($valores) . " campos):\n";
        foreach ($valores as $i => $valor) {
            echo "  Campo " . ($i + 1) . ": $valor\n";
        }
        echo "\n";

        // Solo mostrar unos pocos ejemplos
        if ($index >= 5) break;
    }
}
