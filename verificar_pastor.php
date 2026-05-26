<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$pastores = \App\Models\Pastor::where('documento', '25212293')->get();

echo "Pastores encontrados: " . $pastores->count() . "\n\n";

foreach ($pastores as $pastor) {
    echo "ID: {$pastor->id}\n";
    echo "Nombre: {$pastor->nombres} {$pastor->apellidos}\n";
    echo "Teléfono: " . ($pastor->telefono_tlf ?? 'NULL') . "\n";
    echo "Protección activada: " . ($pastor->preguntasSeguridad && $pastor->preguntasSeguridad->activado ? 'SÍ' : 'NO') . "\n";
    echo "\n";
}
