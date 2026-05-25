<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

use App\Models\Pastor;

echo "=== VERIFICACIÓN DE DATOS MIGRADOS ===" . PHP_EOL;
echo "Total de pastores: " . Pastor::count() . PHP_EOL;
echo "Pastores casados: " . Pastor::where('estado_civil', 'Casado')->count() . PHP_EOL;
echo "Pastores con cónyuge: " . Pastor::whereNotNull('conyuge_id')->count() . PHP_EOL . PHP_EOL;

echo "=== EJEMPLOS DE PASTORES CON CÓNYUGE ===" . PHP_EOL;

$pastores = Pastor::whereNotNull('conyuge_id')->with('conyuge')->limit(5)->get();

foreach ($pastores as $pastor) {
    echo "- {$pastor->nombres} {$pastor->apellidos} (Código: {$pastor->codigo})" . PHP_EOL;
    echo "  Estado civil: {$pastor->estado_civil}" . PHP_EOL;
    echo "  Cónyuge: {$pastor->conyuge->nombres} {$pastor->conyuge->apellidos} (Código: {$pastor->conyuge->codigo})" . PHP_EOL;
    echo "  Estado civil del cónyuge: {$pastor->conyuge->estado_civil}" . PHP_EOL;
    echo "  ID del cónyuge: {$pastor->conyuge_id} <=> {$pastor->conyuge->id}" . PHP_EOL . PHP_EOL;
}

echo "=== VERIFICACIÓN DE RELACIONES BIDIRECCIONALES ===" . PHP_EOL;

$pastoresConConyuge = Pastor::whereNotNull('conyuge_id')->get();
$relacionesCorrectas = 0;
$relacionesIncorrectas = 0;

foreach ($pastoresConConyuge as $pastor) {
    $conyuge = $pastor->conyuge;
    if ($conyuge && $conyuge->conyuge_id == $pastor->id) {
        $relacionesCorrectas++;
    } else {
        $relacionesIncorrectas++;
        echo "Relación incorrecta: {$pastor->nombres} {$pastor->apellidos} -> ";
        if ($conyuge) {
            echo "{$conyuge->nombres} {$conyuge->apellidos} (conyuge_id: {$conyuge->conyuge_id})" . PHP_EOL;
        } else {
            echo "cónyuge no encontrado" . PHP_EOL;
        }
    }
}

echo "Relaciones bidireccionales correctas: {$relacionesCorrectas}" . PHP_EOL;
echo "Relaciones con problemas: {$relacionesIncorrectas}" . PHP_EOL;
