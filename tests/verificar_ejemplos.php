<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== EJEMPLOS DE PASTORES CON UBICACIONES ASIGNADAS ===\n\n";

// Pastores con estado_id
$pastores = DB::table('pastores')
    ->select('nombres', 'apellidos', 'estado_id', 'municipio_id', 'parroquia_id')
    ->whereNotNull('estado_id')
    ->limit(5)
    ->get();

echo "Pastores con estado_id asignado:\n";
foreach ($pastores as $pastor) {
    echo "- {$pastor->nombres} {$pastor->apellidos}\n";
    echo "  Estado: {$pastor->estado_id}, Municipio: {$pastor->municipio_id}, Parroquia: {$pastor->parroquia_id}\n\n";
}

// Verificar nombres de ubicaciones
$pastores_con_nombres = DB::table('pastores')
    ->join('estados', 'pastores.estado_id', '=', 'estados.id')
    ->leftJoin('municipios', 'pastores.municipio_id', '=', 'municipios.id')
    ->leftJoin('parroquias', 'pastores.parroquia_id', '=', 'parroquias.id')
    ->select(
        'pastores.nombres',
        'pastores.apellidos',
        'estados.nombre as estado_nombre',
        'municipios.nombre as municipio_nombre',
        'parroquias.nombre as parroquia_nombre'
    )
    ->whereNotNull('pastores.estado_id')
    ->limit(3)
    ->get();

echo "Pastores con nombres de ubicaciones:\n";
foreach ($pastores_con_nombres as $pastor) {
    echo "- {$pastor->nombres} {$pastor->apellidos}\n";
    echo "  Estado: {$pastor->estado_nombre}\n";
    echo "  Municipio: " . ($pastor->municipio_nombre ?? 'N/A') . "\n";
    echo "  Parroquia: " . ($pastor->parroquia_nombre ?? 'N/A') . "\n\n";
}

echo "=== RESUMEN ===\n";
echo "Total de pastores: " . DB::table('pastores')->count() . "\n";
echo "Pastores con estado_id: " . DB::table('pastores')->whereNotNull('estado_id')->count() . "\n";
echo "Pastores con municipio_id: " . DB::table('pastores')->whereNotNull('municipio_id')->count() . "\n";
echo "Pastores con parroquia_id: " . DB::table('pastores')->whereNotNull('parroquia_id')->count() . "\n";
