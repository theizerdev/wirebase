<?php

$sqlFile = 'C:\\laragon\\www\\mmmvnzla\\pastores.sql';

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

echo "Total de bloques INSERT encontrados: " . count($allMatches[0]) . "\n\n";

// Función para extraer registros individualmente
function extraerRegistros($valuesSection) {
    $registros = [];
    $depth = 0;
    $inicio = -1;
    $registroActual = '';

    for ($i = 0; $i < strlen($valuesSection); $i++) {
        $char = $valuesSection[$i];

        if ($char === '(' && $depth === 0) {
            $inicio = $i;
            $depth = 1;
            $registroActual = '(';
        } elseif ($char === '(') {
            $depth++;
            $registroActual .= $char;
        } elseif ($char === ')' && $depth === 1) {
            $registroActual .= ')';
            $registros[] = $registroActual;
            $depth = 0;
            $inicio = -1;
            $registroActual = '';
        } elseif ($char === ')') {
            $depth--;
            if ($depth > 0) {
                $registroActual .= $char;
            }
        } elseif ($depth > 0) {
            $registroActual .= $char;
        }
    }

    return $registros;
}

$totalRegistros = 0;
$registrosConProblemas = [];

foreach ($allMatches[1] as $blockIndex => $valuesSection) {
    $blockNumber = $blockIndex + 1;
    $valuesSection = trim($valuesSection);

    echo "Analizando Bloque $blockNumber...\n";

    // Extraer todos los registros del bloque
    $registros = extraerRegistros($valuesSection);
    $registrosEnBloque = count($registros);

    echo "  - Registros encontrados: $registrosEnBloque\n";

    // Analizar cada registro para detectar problemas
    foreach ($registros as $index => $registro) {
        $problemas = [];

        // Verificar si el registro está incompleto o mal formado
        if (substr_count($registro, "'") % 2 !== 0) {
            $problemas[] = "Comillas simples desbalanceadas";
        }

        if (substr_count($registro, '(') !== substr_count($registro, ')')) {
            $problemas[] = "Paréntesis desbalanceados";
        }

        // Verificar si hay valores NULL mal formados
        if (preg_match("/'[^']*'NULL|'NULL[^']*'/", $registro)) {
            $problemas[] = "Valor NULL mal formado";
        }

        // Verificar si hay caracteres extraños
        if (preg_match('/[\x00-\x08\x0B-\x0C\x0E-\x1F\x7F-\x9F]/', $registro)) {
            $problemas[] = "Caracteres de control no válidos";
        }

        // Verificar si hay comillas dobles sin escapar
        if (preg_match('/(?<!\\\\)"/', $registro)) {
            $problemas[] = "Comillas dobles sin escapar";
        }

        if (!empty($problemas)) {
            $registrosConProblemas[] = [
                'bloque' => $blockNumber,
                'registro_index' => $index,
                'registro' => substr($registro, 0, 100) . (strlen($registro) > 100 ? '...' : ''),
                'problemas' => $problemas,
                'longitud' => strlen($registro)
            ];
        }
    }

    $totalRegistros += $registrosEnBloque;
    echo "\n";
}

echo "Total de registros analizados: $totalRegistros\n";
echo "Registros con problemas detectados: " . count($registrosConProblemas) . "\n\n";

if (!empty($registrosConProblemas)) {
    echo "=== REGISTROS CON PROBLEMAS ===\n\n";

    foreach ($registrosConProblemas as $i => $problema) {
        echo "Problema " . ($i + 1) . ":\n";
        echo "  Bloque: " . $problema['bloque'] . "\n";
        echo "  Índice: " . $problema['registro_index'] . "\n";
        echo "  Registro: " . $problema['registro'] . "\n";
        echo "  Problemas: " . implode(', ', $problema['problemas']) . "\n";
        echo "  Longitud: " . $problema['longitud'] . " caracteres\n";
        echo "\n";
    }
} else {
    echo "No se detectaron problemas obvios en los registros.\n";
}
