<?php
/**
 * Script para actualizar todos los códigos de pastores con el formato:
 * {ID 5 dígitos}-{últimos 5 dígitos de cédula}
 * Ejemplo: 00001-45293
 */

define('LARAVEL_START', microtime(true));
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Pastor;

echo "=== Actualización de códigos de pastores ===\n";
echo "Formato: {ID 5 dígitos}-{últimos 5 dígitos de cédula}\n\n";

$pastores = Pastor::orderBy('id')->get();
$total = $pastores->count();
$actualizados = 0;
$errores = 0;

echo "Total de pastores encontrados: {$total}\n\n";
echo str_pad('ID', 8) . str_pad('Código Actual', 20) . str_pad('Documento', 18) . str_pad('Nombre', 35) . "Nuevo Código\n";
echo str_repeat('-', 100) . "\n";

foreach ($pastores as $pastor) {
    $codigoViejo = $pastor->codigo ?? '(vacío)';
    $nuevoCodigo = Pastor::generarCodigoPastor($pastor->id, $pastor->documento ?? '');

    // Verificar si el nuevo código ya existe en otro pastor (colisión)
    $existente = Pastor::where('codigo', $nuevoCodigo)
        ->where('id', '!=', $pastor->id)
        ->first();

    if ($existente) {
        echo str_pad($pastor->id, 8)
            . str_pad($codigoViejo, 20)
            . str_pad($pastor->documento ?? '', 18)
            . str_pad(mb_substr($pastor->nombre_completo, 0, 33), 35)
            . "CONFLICTO -> {$nuevoCodigo} (ya usado por ID: {$existente->id})\n";
        $errores++;
        continue;
    }

    try {
        $pastor->update(['codigo' => $nuevoCodigo]);
        echo str_pad($pastor->id, 8)
            . str_pad($codigoViejo, 20)
            . str_pad($pastor->documento ?? '', 18)
            . str_pad(mb_substr($pastor->nombre_completo, 0, 33), 35)
            . "{$nuevoCodigo}\n";
        $actualizados++;
    } catch (\Exception $e) {
        echo str_pad($pastor->id, 8)
            . str_pad($codigoViejo, 20)
            . str_pad($pastor->documento ?? '', 18)
            . str_pad(mb_substr($pastor->nombre_completo, 0, 33), 35)
            . "ERROR: {$e->getMessage()}\n";
        $errores++;
    }
}

echo "\n" . str_repeat('=', 50) . "\n";
echo "Resumen:\n";
echo "  Total procesados:  {$total}\n";
echo "  Actualizados:      {$actualizados}\n";
echo "  Errores/Conflictos: {$errores}\n";
echo "Listo!\n";
