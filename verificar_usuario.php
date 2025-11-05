<?php

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Autenticar el primer usuario
$user = \App\Models\User::first();
if ($user) {
    auth()->login($user);
    echo "=== DATOS DEL USUARIO ===" . PHP_EOL;
    echo "Usuario ID: " . $user->id . PHP_EOL;
    echo "Nombre: " . $user->name . PHP_EOL;
    echo "Empresa ID: " . ($user->empresa_id ?? 'null') . PHP_EOL;
    echo "Sucursal ID: " . ($user->sucursal_id ?? 'null') . PHP_EOL;
    echo "Role: " . ($user->roles->first()->name ?? 'no role') . PHP_EOL;
    echo "¿Es Super Admin?: " . ($user->hasRole('Super Administrador') ? 'Sí' : 'No') . PHP_EOL;
    echo PHP_EOL;

    echo "=== ANÁLISIS DE PAGOS ===" . PHP_EOL;
    // Verificar si hay pagos con scope global (aplicando multitenancy)
    $totalPagos = \App\Models\Pago::count();
    echo "Total pagos (con scope global): " . $totalPagos . PHP_EOL;

    // Verificar sin scope global (todos los pagos)
    $totalPagosSinScope = \App\Models\Pago::withoutGlobalScopes()->count();
    echo "Total pagos (sin scope global): " . $totalPagosSinScope . PHP_EOL;

    // Verificar pagos con matricula y estudiante (con scope global)
    $totalPagosConMatricula = \App\Models\Pago::whereHas('matricula', function($q) {
        $q->whereHas('student');
    })->count();
    echo "Total pagos con matricula y estudiante (con scope global): " . $totalPagosConMatricula . PHP_EOL;

    // Verificar pagos con matricula y estudiante (sin scope global)
    $totalPagosConMatriculaSinScope = \App\Models\Pago::withoutGlobalScopes()
        ->whereHas('matricula', function($q) {
            $q->whereHas('student');
        })->count();
    echo "Total pagos con matricula y estudiante (sin scope global): " . $totalPagosConMatriculaSinScope . PHP_EOL;

    echo PHP_EOL;
    echo "=== ANÁLISIS DE MATRICULAS ===" . PHP_EOL;
    $totalMatriculas = \App\Models\Matricula::count();
    echo "Total matriculas (con scope global): " . $totalMatriculas . PHP_EOL;

    $totalMatriculasSinScope = \App\Models\Matricula::withoutGlobalScopes()->count();
    echo "Total matriculas (sin scope global): " . $totalMatriculasSinScope . PHP_EOL;

} else {
    echo "No hay usuarios en el sistema" . PHP_EOL;
}
