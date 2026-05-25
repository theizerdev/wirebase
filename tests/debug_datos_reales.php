<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Ver algunos datos de pastores para ver qué estados están guardados
$pastores = DB::table('pastores')
    ->select('id', 'nombres', 'apellidos', 'estado_id', 'municipio_id', 'parroquia_id')
    ->whereNotNull('estado_id')
    ->limit(10)
    ->get();

echo "=== PASTORES CON ESTADO_ID ===\n\n";
foreach ($pastores as $pastor) {
    echo "Pastor: {$pastor->nombres} {$pastor->apellidos}\n";
    echo "  Estado ID: {$pastor->estado_id}\n";
    echo "  Municipio ID: {$pastor->municipio_id}\n";
    echo "  Parroquia ID: {$pastor->parroquia_id}\n";
    echo "\n";
}

// Ver también algunos sin estado_id
$pastoresSinEstado = DB::table('pastores')
    ->select('id', 'nombres', 'apellidos', 'estado_id', 'municipio_id', 'parroquia_id')
    ->whereNull('estado_id')
    ->limit(5)
    ->get();

echo "=== PASTORES SIN ESTADO_ID ===\n\n";
foreach ($pastoresSinEstado as $pastor) {
    echo "Pastor: {$pastor->nombres} {$pastor->apellidos}\n";
    echo "  Estado ID: " . ($pastor->estado_id ?? 'NULL') . "\n";
    echo "  Municipio ID: " . ($pastor->municipio_id ?? 'NULL') . "\n";
    echo "  Parroquia ID: " . ($pastor->parroquia_id ?? 'NULL') . "\n";
    echo "\n";
}
