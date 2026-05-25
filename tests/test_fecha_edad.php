<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "=== VERIFICANDO FECHAS Y EDADES REALES EN BD ===\n\n";

// Verificar algunos ejemplos concretos
$ejemplos = DB::table('pastores')
    ->select('codigo', 'nombres', 'apellidos', 'edad', 'fe_nacimiento')
    ->where('codigo', 'in', ['430211', '939705', '54638', '846455'])
    ->get();

echo "Ejemplos de pastores que deberían tener fecha/edad:\n\n";
foreach ($ejemplos as $pastor) {
    echo "Código: {$pastor->codigo}\n";
    echo "Nombre: {$pastor->nombres} {$pastor->apellidos}\n";
    echo "Edad: " . ($pastor->edad ?? 'NULL') . "\n";
    echo "Fecha Nacimiento: " . ($pastor->fe_nacimiento ?? 'NULL') . "\n";
    echo "---\n";
}

// Estadísticas generales
$total = DB::table('pastores')->count();
$conEdad = DB::table('pastores')->whereNotNull('edad')->count();
$conFecha = DB::table('pastores')->whereNotNull('fe_nacimiento')->count();

echo "\n=== ESTADÍSTICAS ===\n";
echo "Total pastores: $total\n";
echo "Con edad: $conEdad (" . round($conEdad/$total*100, 1) . "%)\n";
echo "Con fecha nacimiento: $conFecha (" . round($conFecha/$total*100, 1) . "%)\n";
