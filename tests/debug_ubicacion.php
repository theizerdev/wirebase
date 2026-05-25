<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Estado;
use App\Models\Municipio;
use App\Models\Parroquia;

// Datos de prueba
$datosPrueba = [
    [
        'estado' => 'Carabobo',
        'municipio' => 'Valencia',
        'parroquia' => 'Catedral'
    ],
    [
        'estado' => 'CARABOBO',
        'municipio' => 'VALENCIA',
        'parroquia' => 'CATEDRAL'
    ],
    [
        'estado' => 'Miranda',
        'municipio' => 'Chacao',
        'parroquia' => 'Chacao'
    ],
    [
        'estado' => 'Distrito Capital',
        'municipio' => 'Libertador',
        'parroquia' => 'Catedral'
    ]
];

echo "=== PRUEBA DE CONVERSIÓN DE UBICACIONES ===\n\n";

foreach ($datosPrueba as $index => $data) {
    echo "Prueba " . ($index + 1) . ":\n";
    echo "  Estado: '{$data['estado']}'\n";
    echo "  Municipio: '{$data['municipio']}'\n";
    echo "  Parroquia: '{$data['parroquia']}'\n";

    // Buscar Estado
    $estado = Estado::where('nombre', 'LIKE', '%' . trim($data['estado']) . '%')
        ->orWhere('nombre', 'LIKE', '%' . ucwords(strtolower(trim($data['estado']))) . '%')
        ->first();

    if ($estado) {
        echo "  ✓ Estado encontrado: ID = {$estado->id}, Nombre = {$estado->nombre}\n";

        // Buscar Municipio
        $municipio = Municipio::where('nombre', 'LIKE', '%' . trim($data['municipio']) . '%')
            ->orWhere('nombre', 'LIKE', '%' . ucwords(strtolower(trim($data['municipio']))) . '%')
            ->where('estado_id', $estado->id)
            ->first();

        if ($municipio) {
            echo "  ✓ Municipio encontrado: ID = {$municipio->id}, Nombre = {$municipio->nombre}\n";

            // Buscar Parroquia
            $parroquia = Parroquia::where('nombre', 'LIKE', '%' . trim($data['parroquia']) . '%')
                ->orWhere('nombre', 'LIKE', '%' . ucwords(strtolower(trim($data['parroquia']))) . '%')
                ->where('municipio_id', $municipio->id)
                ->first();

            if ($parroquia) {
                echo "  ✓ Parroquia encontrada: ID = {$parroquia->id}, Nombre = {$parroquia->nombre}\n";
            } else {
                echo "  ✗ Parroquia NO encontrada\n";
                echo "    Buscando solo por nombre sin restricción de municipio...\n";

                $parroquia = Parroquia::where('nombre', 'LIKE', '%' . trim($data['parroquia']) . '%')
                    ->orWhere('nombre', 'LIKE', '%' . ucwords(strtolower(trim($data['parroquia']))) . '%')
                    ->first();

                if ($parroquia) {
                    echo "  ✓ Parroquia encontrada (sin restricción): ID = {$parroquia->id}, Nombre = {$parroquia->nombre}, Municipio ID = {$parroquia->municipio_id}\n";
                } else {
                    echo "  ✗ Parroquia NO encontrada incluso sin restricción\n";
                }
            }
        } else {
            echo "  ✗ Municipio NO encontrado\n";
        }
    } else {
        echo "  ✗ Estado NO encontrado\n";
    }

    echo "\n";
}

// Mostrar algunos estados disponibles
echo "=== ESTADOS DISPONIBLES (primeros 10) ===\n";
$estados = Estado::limit(10)->get();
foreach ($estados as $estado) {
    echo "ID: {$estado->id}, Nombre: {$estado->nombre}\n";
}

echo "\n=== MUNICIPIOS DE CARABOBO (primeros 5) ===\n";
$carabobo = Estado::where('nombre', 'LIKE', '%Carabobo%')->first();
if ($carabobo) {
    $municipios = Municipio::where('estado_id', $carabobo->id)->limit(5)->get();
    foreach ($municipios as $municipio) {
        echo "ID: {$municipio->id}, Nombre: {$municipio->nombre}\n";
    }
}

echo "\n=== PARROQUIAS DE VALENCIA (primeros 5) ===\n";
$valencia = Municipio::where('nombre', 'LIKE', '%Valencia%')->first();
if ($valencia) {
    $parroquias = Parroquia::where('municipio_id', $valencia->id)->limit(5)->get();
    foreach ($parroquias as $parroquia) {
        echo "ID: {$parroquia->id}, Nombre: {$parroquia->nombre}\n";
    }
}

echo "\n✅ Prueba completada\n";
