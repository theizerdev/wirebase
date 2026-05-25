<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Verificar algunos pastores con sus ubicaciones
$pastores = DB::table('pastores')
    ->select('id', 'nombres', 'apellidos', 'estado_id', 'municipio_id', 'parroquia_id')
    ->whereNotNull('estado_id')
    ->limit(10)
    ->get();

echo "=== VERIFICACIÓN DE UBICACIONES ===\n\n";

foreach ($pastores as $pastor) {
    echo "Pastor: {$pastor->nombres} {$pastor->apellidos}\n";
    echo "  Estado ID: {$pastor->estado_id}\n";
    echo "  Municipio ID: {$pastor->municipio_id}\n";
    echo "  Parroquia ID: {$pastor->parroquia_id}\n";
    echo "\n";
}

// Verificar también los nombres de las ubicaciones
$ubicaciones = DB::table('pastores')
    ->join('estados', 'pastores.estado_id', '=', 'estados.id')
    ->join('municipios', 'pastores.municipio_id', '=', 'municipios.id')
    ->join('parroquias', 'pastores.parroquia_id', '=', 'parroquias.id')
    ->select('pastores.nombres', 'pastores.apellidos', 'estados.nombre as estado', 'municipios.nombre as municipio', 'parroquias.nombre as parroquia')
    ->limit(5)
    ->get();

echo "=== UBICACIONES CON NOMBRES ===\n\n";

foreach ($ubicaciones as $ubicacion) {
    echo "Pastor: {$ubicacion->nombres} {$ubicacion->apellidos}\n";
    echo "  Estado: {$ubicacion->estado}\n";
    echo "  Municipio: {$ubicacion->municipio}\n";
    echo "  Parroquia: {$ubicacion->parroquia}\n";
    echo "\n";
}
