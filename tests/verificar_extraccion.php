<?php

// Verificar por qué no se extrae toda la sección VALUES
$sqlFile = 'pastores.sql';

if (!file_exists($sqlFile)) {
    echo "Archivo no encontrado: $sqlFile\n";
    exit(1);
}

$content = file_get_contents($sqlFile);

// Limpiar el contenido
function cleanSqlContent(string $content): string {
    $content = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]/', ' ', $content);
    $content = str_replace(["\r\n", "\r"], "\n", $content);
    $content = preg_replace('/\s+/', ' ', $content);
    return $content;
}

$content = cleanSqlContent($content);

// Probar diferentes patrones
$patterns = [
    'patron1' => '/INSERT INTO\s+`?pastors`?\s+.*?VALUES\s*(.*?);/s',
    'patron2' => '/INSERT INTO `pastors`.*?VALUES\s*(.*?);/s',
    'patron3' => '/INSERT INTO pastores.*?VALUES\s*(.*?);/s',
    'patron4' => '/INSERT INTO\s+`pastors`\s+\([^)]+\)\s+VALUES\s*(.*?);/s'
];

foreach ($patterns as $name => $pattern) {
    echo "Probando $name: $pattern\n";
    if (preg_match($pattern, $content, $matches)) {
        $valuesSection = $matches[1];
        echo "  ✓ Encontrado! Longitud: " . strlen($valuesSection) . " caracteres\n";

        // Ver si hay más contenido después
        $pos = strpos($content, $valuesSection);
        $remaining = substr($content, $pos + strlen($valuesSection));
        echo "  Contenido restante después de VALUES: " . strlen($remaining) . " caracteres\n";
        echo "  Primeros 100 caracteres del resto: " . substr($remaining, 0, 100) . "\n";

        // Buscar manualmente el final
        $endPos = strpos($content, ';', $pos);
        if ($endPos !== false) {
            $fullSection = substr($content, $pos, $endPos - $pos);
            echo "  Sección completa hasta ';': " . strlen($fullSection) . " caracteres\n";
        }

    } else {
        echo "  ✗ No encontrado\n";
    }
    echo "\n";
}

// Intentar un enfoque diferente: encontrar el inicio y fin manualmente
echo "Enfoque manual:\n";
$insertPos = strpos($content, 'INSERT INTO');
$valuesPos = strpos($content, 'VALUES', $insertPos);
$semicolonPos = strpos($content, ';', $valuesPos);

if ($insertPos !== false && $valuesPos !== false && $semicolonPos !== false) {
    $valuesSection = substr($content, $valuesPos + 6, $semicolonPos - $valuesPos - 6);
    echo "Sección VALUES encontrada: " . strlen($valuesSection) . " caracteres\n";

    // Contar registros manualmente
    $count = 0;
    $depth = 0;
    $inRecord = false;

    for ($i = 0; $i < strlen($valuesSection); $i++) {
        $char = $valuesSection[$i];

        if ($char === '(' && $depth === 0) {
            $inRecord = true;
            $depth = 1;
        } elseif ($inRecord) {
            if ($char === '(') {
                $depth++;
            } elseif ($char === ')') {
                $depth--;
                if ($depth === 0) {
                    $count++;
                    $inRecord = false;
                }
            }
        }
    }

    echo "Registros encontrados: $count\n";
}
