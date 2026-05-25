<?php

echo "=== CONTADOR DE REGISTROS COMPLETOS ===" . PHP_EOL . PHP_EOL;

// Leer el archivo completo
$content = file_get_contents('pastores.sql');

// 1. Contar usando el patrón completo de INSERT
$totalInserts = substr_count($content, 'INSERT INTO `pastors`');
echo "📝 Bloques INSERT encontrados: " . $totalInserts . PHP_EOL;

// 2. Buscar todos los valores entre paréntesis que representan registros completos
preg_match_all('/\(\d+,\s*\d+/', $content, $matches);
$posiblesRegistros = count($matches[0]);
echo "🔍 Posibles registros (patrón id, codigo): " . $posiblesRegistros . PHP_EOL;

// 3. Método más preciso: buscar el patrón completo de cada registro
// Cada registro comienza con: (id, codigo, nombre, apellido, cedula, ...)
preg_match_all('/\(\d+,\s*\d+,\s*[\'"]/' , $content, $matchesPrecisos);
$registrosPrecisos = count($matchesPrecisos[0]);
echo "🎯 Registros completos (patrón preciso): " . $registrosPrecisos . PHP_EOL;

// 4. Verificar algunos ejemplos de líneas que comienzan con "("
echo PHP_EOL . "=== ANÁLISIS DE LÍNEAS QUE COMIENZAN CON '(' ===" . PHP_EOL;
$lineas = explode("\n", $content);
$lineasConParentesis = 0;
$lineasRegistroCompletas = 0;
$ejemplosNoRegistros = [];

foreach ($lineas as $numLinea => $linea) {
    $linea = trim($linea);
    if (strpos($linea, '(') === 0) {
        $lineasConParentesis++;
        
        // Verificar si es un registro completo (debe tener el patrón id, codigo)
        if (preg_match('/^\(\d+,\s*\d+,/', $linea)) {
            $lineasRegistroCompletas++;
        } else {
            // Es una continuación de registro anterior
            if (count($ejemplosNoRegistros) < 5) {
                $ejemplosNoRegistros[] = [
                    'linea' => $numLinea + 1,
                    'contenido' => substr($linea, 0, 100) . '...'
                ];
            }
        }
    }
}

echo "📊 Total líneas que comienzan con '(': " . $lineasConParentesis . PHP_EOL;
echo "✅ Líneas que son registros completos: " . $lineasRegistroCompletas . PHP_EOL;
echo "⚠️  Líneas que son continuaciones: " . ($lineasConParentesis - $lineasRegistroCompletas) . PHP_EOL;

if (count($ejemplosNoRegistros) > 0) {
    echo PHP_EOL . "=== EJEMPLOS DE CONTINUACIONES DE REGISTROS ===" . PHP_EOL;
    foreach ($ejemplosNoRegistros as $ejemplo) {
        echo "Línea {$ejemplo['linea']}: {$ejemplo['contenido']}" . PHP_EOL;
    }
}

// 5. Método final: extraer todos los registros completos manualmente
echo PHP_EOL . "=== MÉTODO DE EXTRACCIÓN MANUAL ===" . PHP_EOL;
$registrosManuales = [];

// Buscar todos los bloques VALUES
preg_match_all('/VALUES\s*\((.*?)\);/s', $content, $bloquesValues);

echo "📦 Bloques VALUES encontrados: " . count($bloquesValues[1]) . PHP_EOL;

foreach ($bloquesValues[1] as $bloque) {
    // En cada bloque, encontrar todos los registros completos
    // Un registro comienza con (id, codigo) y termina con ),
    preg_match_all('/\(\d+,\s*\d+.*?\)(?=,|$)/s', $bloque, $registrosEnBloque);
    
    foreach ($registrosEnBloque[0] as $registro) {
        // Limpiar el registro
        $registroLimpio = trim($registro);
        if (!empty($registroLimpio)) {
            $registrosManuales[] = $registroLimpio;
        }
    }
}

echo "🎉 REGISTROS TOTALES ENCONTRADOS: " . count($registrosManuales) . PHP_EOL;

// Comparación final
echo PHP_EOL . "=== COMPARACIÓN FINAL ===" . PHP_EOL;
echo "📊 Método grep (líneas con '('): 823" . PHP_EOL;
echo "📝 Patrón INSERT: {$totalInserts}" . PHP_EOL;
echo "🔍 Patrón id,codigo: {$posiblesRegistros}" . PHP_EOL;
echo "🎯 Patrón preciso: {$registrosPrecisos}" . PHP_EOL;
echo "✅ Extracción manual: " . count($registrosManuales) . PHP_EOL;
echo "📊 Base de datos: 705" . PHP_EOL;

// Conclusión
$totalReal = count($registrosManuales);
$importados = 705;
$faltantes = $totalReal - $importados;

echo PHP_EOL . "=== CONCLUSIÓN ===" . PHP_EOL;
echo "📋 Total real en SQL: {$totalReal}" . PHP_EOL;
echo "✅ Importados exitosamente: {$importados}" . PHP_EOL;
echo "❌ Faltantes: {$faltantes}" . PHP_EOL;
echo "📈 Porcentaje de éxito: " . round(($importados / $totalReal) * 100, 2) . "%" . PHP_EOL;

if ($faltantes > 0) {
    echo PHP_EOL . "⚠️  Hay {$faltantes} registros que no se importaron." . PHP_EOL;
    echo "   Esto podría deberse a:" . PHP_EOL;
    echo "   - Códigos duplicados" . PHP_EOL;
    echo "   - Cédulas duplicadas" . PHP_EOL;
    echo "   - Datos incompletos" . PHP_EOL;
}