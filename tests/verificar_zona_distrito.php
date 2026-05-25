<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFICACIÓN DE ZONA Y DISTRITO EN PASTORES ===\n\n";

// Verificar algunos ejemplos de pastores con sus zonas y distritos
$pastores = DB::table('pastores')
    ->select('codigo', 'nombres', 'apellidos', 'zona', 'distrito')
    ->limit(15)
    ->get();

echo "Primeros 15 pastores con sus zonas y distritos:\n";
foreach ($pastores as $pastor) {
    echo "- Código: {$pastor->codigo} - {$pastor->nombres} {$pastor->apellidos}\n";
    echo "  Zona: " . ($pastor->zona ?? 'NULL') . " | Distrito: " . ($pastor->distrito ?? 'NULL') . "\n\n";
}

// Verificar valores únicos de zona y distrito
$zonas = DB::table('pastores')
    ->select('zona')
    ->whereNotNull('zona')
    ->where('zona', '!=', '')
    ->distinct()
    ->orderBy('zona')
    ->pluck('zona');

$distritos = DB::table('pastores')
    ->select('distrito')
    ->whereNotNull('distrito')
    ->where('distrito', '!=', '')
    ->distinct()
    ->orderBy('distrito')
    ->pluck('distrito');

echo "=== VALORES ÚNICOS ===\n";
echo "Zonas únicas encontradas: " . $zonas->count() . "\n";
echo "Valores: " . $zonas->implode(', ') . "\n\n";

echo "Distritos únicos encontrados: " . $distritos->count() . "\n";
echo "Valores: " . $distritos->implode(', ') . "\n\n";

// Verificar cuántos tienen valores 0 o vacíos
$ceroZona = DB::table('pastores')->where('zona', '0')->count();
$ceroDistrito = DB::table('pastores')->where('distrito', '0')->count();
$vacioZona = DB::table('pastores')->whereNull('zona')->count();
$vacioDistrito = DB::table('pastores')->whereNull('distrito')->count();

echo "=== ANÁLISIS DE VALORES ===\n";
echo "Pastores con zona = '0': $ceroZona\n";
echo "Pastores con distrito = '0': $ceroDistrito\n";
echo "Pastores con zona NULL: $vacioZona\n";
echo "Pastores con distrito NULL: $vacioDistrito\n";
