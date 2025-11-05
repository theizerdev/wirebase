<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Pago;

// Autenticar al usuario Administrador
$usuario = User::whereHas('roles', function($q) {
    $q->where('name', 'Administrador');
})->first();

if (!$usuario) {
    echo "No se encontró usuario Administrador\n";
    exit;
}

auth()->login($usuario);

echo "=== Probando componente de Pagos con Administrador ===\n";
echo "Usuario: " . $usuario->name . " (ID: " . $usuario->id . ")\n";
echo "Empresa: " . $usuario->empresa_id . ", Sucursal: " . $usuario->sucursal_id . "\n\n";

// Simular el método getQuery() del componente
$query = Pago::with(['matricula.student', 'detalles.conceptoPago', 'user', 'serieModel'])
    ->whereHas('matricula', function($q) {
        $q->whereHas('student');
    });

$pagos = $query->paginate(10);

echo "Resultados encontrados: " . $pagos->total() . "\n";
echo "En esta página: " . $pagos->count() . "\n\n";

if ($pagos->count() > 0) {
    echo "Primeros pagos encontrados:\n";
    foreach ($pagos as $pago) {
        echo "- Pago ID: " . $pago->id . ", Documento: " . $pago->documento . ", Total: " . $pago->total . "\n";
        if ($pago->matricula && $pago->matricula->student) {
            echo "  Estudiante: " . $pago->matricula->student->nombres . " " . $pago->matricula->student->apellidos . "\n";
        }
    }
} else {
    echo "No se encontraron pagos.\n";
}

// Verificar SQL generado
echo "\n=== SQL Generado ===\n";
echo $query->toSql() . "\n";
echo "Bindings: " . json_encode($query->getBindings()) . "\n";
