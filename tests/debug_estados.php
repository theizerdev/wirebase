<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Verificar qué estados tenemos en la base de datos
$estados = DB::table('estados')->select('id', 'nombre')->get();

echo "=== ESTADOS EN LA BASE DE DATOS ===\n\n";
foreach ($estados as $estado) {
    echo "ID: {$estado->id} - Nombre: {$estado->nombre}\n";
}

// Ver algunos datos de pastores para ver qué estados están llegando
$pastores = DB::table('pastores')
    ->select('id', 'nombres', 'apellidos', 'estado', 'municipio', 'parroquia')
    ->whereNotNull('estado')
    ->limit(10)
    ->get();

echo "\n=== DATOS DE UBICACIÓN EN PASTORES ===\n\n";
foreach ($pastores as $pastor) {
    echo "Pastor: {$pastor->nombres} {$pastor->apellidos}\n";
    echo "  Estado: " . ($pastor->estado ?? 'NULL') . "\n";
    echo "  Municipio: " . ($pastor->municipio ?? 'NULL') . "\n";
    echo "  Parroquia: " . ($pastor->parroquia ?? 'NULL') . "\n";
    echo "\n";
}
