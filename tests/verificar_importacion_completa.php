<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFICACIÓN COMPLETA DE IMPORTACIÓN ===" . PHP_EOL . PHP_EOL;

// 1. Obtener todos los códigos de la BD
$codigosBD = \DB::table('pastores')->pluck('codigo')->toArray();
sort($codigosBD);

echo "📊 Total en base de datos: " . count($codigosBD) . PHP_EOL;

// 2. Extraer todos los códigos del SQL
$sqlContent = file_get_contents('pastores.sql');
$codigosSQL = [];

// Buscar todos los bloques VALUES y extraer códigos
preg_match_all('/VALUES\s*\((.*?)\);/s', $sqlContent, $bloquesValues);

foreach ($bloquesValues[1] as $bloque) {
    // Encontrar cada registro completo
    preg_match_all('/\(\d+,\s*(\d+),/', $bloque, $matches);
    
    foreach ($matches[1] as $codigo) {
        $codigosSQL[] = $codigo;
    }
}

sort($codigosSQL);

echo "📋 Total en SQL: " . count($codigosSQL) . PHP_EOL;

// 3. Comparar los conjuntos de datos
$enBDNoEnSQL = array_diff($codigosBD, $codigosSQL);
$enSQLNoEnBD = array_diff($codigosSQL, $codigosBD);
$enAmbos = array_intersect($codigosBD, $codigosSQL);

echo PHP_EOL . "=== RESULTADO DE LA COMPARACIÓN ===" . PHP_EOL;
echo "✅ En ambos (SQL y BD): " . count($enAmbos) . PHP_EOL;
echo "❌ En BD pero NO en SQL: " . count($enBDNoEnSQL) . PHP_EOL;
echo "⚠️  En SQL pero NO en BD: " . count($enSQLNoEnBD) . PHP_EOL;

// 4. Mostrar los códigos que están en BD pero no en SQL (registros adicionales)
if (count($enBDNoEnSQL) > 0) {
    echo PHP_EOL . "=== REGISTROS ADICIONALES EN BD (no vienen del SQL) ===" . PHP_EOL;
    
    $registrosAdicionales = \DB::table('pastores')
        ->whereIn('codigo', array_slice($enBDNoEnSQL, 0, 10)) // Mostrar solo primeros 10
        ->select('codigo', 'nombres', 'apellidos', 'created_at')
        ->get();
    
    foreach ($registrosAdicionales as $registro) {
        echo "- Código: {$registro->codigo} - {$registro->nombres} {$registro->apellidos}" . PHP_EOL;
        echo "  Creado: {$registro->created_at}" . PHP_EOL;
    }
    
    if (count($enBDNoEnSQL) > 10) {
        echo "... y " . (count($enBDNoEnSQL) - 10) . " registros más" . PHP_EOL;
    }
}

// 5. Mostrar los que están en SQL pero no en BD (si los hay)
if (count($enSQLNoEnBD) > 0) {
    echo PHP_EOL . "=== REGISTROS DEL SQL QUE NO SE IMPORTARON ===" . PHP_EOL;
    
    // Buscar información de estos registros en el SQL
    foreach (array_slice($enSQLNoEnBD, 0, 5) as $codigoFaltante) {
        // Buscar este código en el SQL
        if (preg_match('/\(\d+,\s*' . preg_quote($codigoFaltante) . ',\s*\'([^\']*)\'\s*,\s*\'([^\']*)\'/', $sqlContent, $match)) {
            echo "- Código: {$codigoFaltante} - Nombre: {$match[1]} {$match[2]}" . PHP_EOL;
        }
    }
    
    if (count($enSQLNoEnBD) > 5) {
        echo "... y " . (count($enSQLNoEnBD) - 5) . " registros más" . PHP_EOL;
    }
}

// 6. Verificar fechas de creación para entender la cronología
$fechasCreacion = \DB::table('pastores')
    ->selectRaw('DATE(created_at) as fecha, COUNT(*) as total')
    ->groupBy('fecha')
    ->orderBy('fecha')
    ->get();

echo PHP_EOL . "=== CRONOLOGÍA DE CREACIÓN DE REGISTROS ===" . PHP_EOL;
foreach ($fechasCreacion as $fecha) {
    echo "{$fecha->fecha}: {$fecha->total} registros creados" . PHP_EOL;
}

// 7. Conclusión final
echo PHP_EOL . "=== CONCLUSIÓN FINAL ===" . PHP_EOL;

if (count($enSQLNoEnBD) === 0) {
    echo "🎉 ¡TODOS los registros del SQL fueron importados exitosamente!" . PHP_EOL;
    echo "📊 Total importado: " . count($codigosSQL) . " de " . count($codigosSQL) . " registros" . PHP_EOL;
    echo "📈 Porcentaje de éxito: 100%" . PHP_EOL;
    
    if (count($enBDNoEnSQL) > 0) {
        echo PHP_EOL . "ℹ️  Hay " . count($enBDNoEnSQL) . " registros adicionales en la BD" . PHP_EOL;
        echo "   Estos probablemente fueron creados antes o después de la migración" . PHP_EOL;
    }
} else {
    echo "⚠️  Hay " . count($enSQLNoEnBD) . " registros del SQL que no se importaron" . PHP_EOL;
    echo "   Esto representa un " . round((count($enSQLNoEnBD) / count($codigosSQL)) * 100, 2) . "% de fallo" . PHP_EOL;
}