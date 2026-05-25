<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== INVESTIGACIÓN DE REGISTROS FALTANTES ===" . PHP_EOL . PHP_EOL;

// 1. Obtener todos los códigos de pastores en la base de datos
$codigosEnBD = \DB::table('pastores')->pluck('codigo')->toArray();
echo "✅ Pastores en base de datos: " . count($codigosEnBD) . PHP_EOL;

// 2. Leer el archivo SQL y extraer todos los códigos
$sqlContent = file_get_contents('pastores.sql');
$codigosEnSQL = [];
$registrosNoImportados = [];
$registrosProcesados = 0;

// Buscar todos los bloques INSERT
preg_match_all('/INSERT INTO\s+`?pastors`?\s+.*?VALUES\s*(.*?);/s', $sqlContent, $allMatches);

echo "📊 Se encontraron " . count($allMatches[1]) . " bloques INSERT" . PHP_EOL;

foreach ($allMatches[1] as $bloque) {
    // Extraer cada registro individual del bloque VALUES
    preg_match_all('/\(([^)]+)\)/', $bloque, $registros);
    
    foreach ($registros[1] as $registro) {
        $registrosProcesados++;
        
        // Parsear los valores del registro
        $valores = parsearValoresSQL($registro);
        
        if (count($valores) >= 2) {
            $codigo = trim($valores[1], "'\""); // El código está en la segunda posición
            $codigosEnSQL[] = $codigo;
            
            // Verificar si este código está en la BD
            if (!in_array($codigo, $codigosEnBD)) {
                $registrosNoImportados[] = [
                    'codigo' => $codigo,
                    'nombre' => isset($valores[2]) ? trim($valores[2], "'\"") : 'Desconocido',
                    'apellido' => isset($valores[3]) ? trim($valores[3], "'\"") : 'Desconocido',
                    'cedula' => isset($valores[4]) ? trim($valores[4], "'\"") : 'Desconocida',
                    'valores_completos' => $valores
                ];
            }
        }
    }
}

echo "📋 Total de registros analizados en SQL: " . $registrosProcesados . PHP_EOL;
echo "❌ Registros no importados: " . count($registrosNoImportados) . PHP_EOL;

// 3. Análisis detallado de los no importados
if (count($registrosNoImportados) > 0) {
    echo PHP_EOL . "=== ANÁLISIS DE REGISTROS NO IMPORTADOS ===" . PHP_EOL;
    
    // Agrupar por posibles razones
    $porCedulaVacia = [];
    $porNombreVacio = [];
    $porCodigoDuplicado = [];
    $otros = [];
    
    foreach ($registrosNoImportados as $registro) {
        if (empty($registro['cedula']) || $registro['cedula'] == 'NULL') {
            $porCedulaVacia[] = $registro;
        } elseif (empty($registro['nombre']) || $registro['nombre'] == 'NULL') {
            $porNombreVacio[] = $registro;
        } elseif (\DB::table('pastores')->where('documento', $registro['cedula'])->exists()) {
            $porCodigoDuplicado[] = $registro;
        } else {
            $otros[] = $registro;
        }
    }
    
    echo "📝 Cédula vacía: " . count($porCedulaVacia) . PHP_EOL;
    echo "📝 Nombre vacío: " . count($porNombreVacio) . PHP_EOL;
    echo "📝 Cédula duplicada: " . count($porCodigoDuplicado) . PHP_EOL;
    echo "📝 Otros motivos: " . count($otros) . PHP_EOL;
    
    // Mostrar algunos ejemplos de cada categoría
    if (count($otros) > 0) {
        echo PHP_EOL . "=== EJEMPLOS DE 'OTROS' MOTIVOS ===" . PHP_EOL;
        for ($i = 0; $i < min(5, count($otros)); $i++) {
            $reg = $otros[$i];
            echo "Código: {$reg['codigo']} - Nombre: {$reg['nombre']} {$reg['apellido']} - Cédula: {$reg['cedula']}" . PHP_EOL;
        }
    }
    
    // Guardar todos los registros no importados en un archivo
    $archivoSalida = 'registros_no_importados.json';
    file_put_contents($archivoSalida, json_encode($registrosNoImportados, JSON_PRETTY_PRINT));
    echo PHP_EOL . "💾 Lista completa guardada en: {$archivoSalida}" . PHP_EOL;
}

// 4. Verificar si hay códigos duplicados en el SQL
$codigosUnicosSQL = array_unique($codigosEnSQL);
$codigosDuplicadosSQL = array_diff_assoc($codigosEnSQL, $codigosUnicosSQL);

if (count($codigosDuplicadosSQL) > 0) {
    echo PHP_EOL . "⚠️  CÓDIGOS DUPLICADOS EN SQL: " . count($codigosDuplicadosSQL) . PHP_EOL;
    $conteoDuplicados = array_count_values($codigosDuplicadosSQL);
    foreach ($conteoDuplicados as $codigo => $cantidad) {
        echo "   - Código {$codigo}: aparece {$cantidad} veces" . PHP_EOL;
    }
}

// 5. Comparación final
echo PHP_EOL . "=== RESUMEN FINAL ===" . PHP_EOL;
echo "📊 Total en SQL: " . count($codigosEnSQL) . PHP_EOL;
echo "✅ Total en BD: " . count($codigosEnBD) . PHP_EOL;
echo "❌ No importados: " . count($registrosNoImportados) . PHP_EOL;
echo "📈 Porcentaje de éxito: " . round((count($codigosEnBD) / count($codigosEnSQL)) * 100, 2) . "%" . PHP_EOL;

/**
 * Función para parsear valores de SQL
 */
function parsearValoresSQL($registro) {
    $valores = [];
    $actual = '';
    $enString = false;
    $escape = false;
    
    for ($i = 0; $i < strlen($registro); $i++) {
        $char = $registro[$i];
        
        if ($escape) {
            $actual .= $char;
            $escape = false;
            continue;
        }
        
        if ($char === '\\') {
            $escape = true;
            continue;
        }
        
        if ($char === "'" && !$escape) {
            $enString = !$enString;
            continue;
        }
        
        if ($char === ',' && !$enString) {
            $valores[] = trim($actual);
            $actual = '';
            continue;
        }
        
        $actual .= $char;
    }
    
    if ($actual !== '') {
        $valores[] = trim($actual);
    }
    
    return $valores;
}