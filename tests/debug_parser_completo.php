<?php

// Script completo para depurar el parser SQL
$sqlFile = 'pastores.sql';

if (!file_exists($sqlFile)) {
    echo "Archivo no encontrado: $sqlFile\n";
    exit(1);
}

$content = file_get_contents($sqlFile);

// Limpiar el contenido (mismo método que en el comando)
function cleanSqlContent(string $content): string {
    // Reemplazar caracteres de control con espacios
    $content = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', ' ', $content);

    // Normalizar saltos de línea
    $content = str_replace(["\r\n", "\r"], "\n", $content);

    // Reducir múltiples espacios a uno solo
    $content = preg_replace('/\s+/', ' ', $content);

    return $content;
}

function findMatchingParenthesis(string $str, int $startPos): ?int {
    $depth = 0;
    $inQuotes = false;
    $quoteChar = '';
    $length = strlen($str);

    for ($i = $startPos; $i < $length; $i++) {
        $char = $str[$i];

        if (!$inQuotes) {
            if ($char === '(') {
                $depth++;
            } elseif ($char === ')') {
                $depth--;
                if ($depth === 0) {
                    return $i;
                }
            } elseif ($char === '"' || $char === "'") {
                $inQuotes = true;
                $quoteChar = $char;
            }
        } else {
            if ($char === $quoteChar && ($i === 0 || $str[$i-1] !== '\\')) {
                $inQuotes = false;
            }
        }
    }

    return null;
}

function parseValuesRobust(string $valuesSection): array {
    $statements = [];
    $currentPos = 0;
    $length = strlen($valuesSection);
    $recordCount = 0;

    echo "Iniciando parsing robusto de valores...\n";
    echo "Longitud de la sección: $length caracteres\n";

    while ($currentPos < $length) {
        // Buscar el próximo paréntesis de apertura que inicie un registro
        $openPos = strpos($valuesSection, '(', $currentPos);
        if ($openPos === false) {
            echo "No se encontró más paréntesis de apertura en posición $currentPos\n";
            break;
        }

        // Encontrar el paréntesis de cierre correspondiente
        $closePos = findMatchingParenthesis($valuesSection, $openPos);
        if ($closePos === null) {
            echo "No se encontró paréntesis de cierre para el registro que empieza en posición $openPos\n";
            break;
        }

        // Extraer el contenido completo del registro (sin los paréntesis externos)
        $recordContent = substr($valuesSection, $openPos + 1, $closePos - $openPos - 1);
        $statements[] = trim($recordContent);
        $recordCount++;

        // Mostrar progreso cada 50 registros
        if ($recordCount % 50 === 0) {
            echo "Procesados $recordCount registros...\n";
        }

        // Avanzar a la posición después del paréntesis de cierre
        $currentPos = $closePos + 1;

        // Saltar cualquier espacio, coma, salto de línea o retorno de carro
        while ($currentPos < $length && preg_match('/[\s,]/', $valuesSection[$currentPos])) {
            $currentPos++;
        }

        // Si encontramos el final de la sección o un punto y coma, terminamos
        if ($currentPos >= $length || $valuesSection[$currentPos] === ';') {
            break;
        }
    }

    echo "Parsing completado. Se encontraron $recordCount registros\n";
    return $statements;
}

// Procesar el contenido
$content = cleanSqlContent($content);

// Buscar el bloque INSERT
if (preg_match('/INSERT INTO\s+`?pastors`?\s+.*?VALUES\s*(.*?);/s', $content, $matches)) {
    $valuesSection = trim($matches[1]);
    echo "Sección VALUES encontrada, longitud: " . strlen($valuesSection) . "\n";

    // Parsear los valores
    $statements = parseValuesRobust($valuesSection);

    echo "\nTotal de registros extraídos: " . count($statements) . "\n";

    // Mostrar algunos ejemplos
    echo "\nPrimeros 3 registros:\n";
    for ($i = 0; $i < min(3, count($statements)); $i++) {
        echo "Registro " . ($i + 1) . ": " . substr($statements[$i], 0, 100) . "...\n";
    }

    echo "\nÚltimos 3 registros:\n";
    $total = count($statements);
    for ($i = max(0, $total - 3); $i < $total; $i++) {
        echo "Registro " . ($i + 1) . ": " . substr($statements[$i], 0, 100) . "...\n";
    }

} else {
    echo "No se encontró el bloque INSERT en el archivo\n";
}
