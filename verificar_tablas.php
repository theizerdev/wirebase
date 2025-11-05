<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use App\Models\User;

// Autenticar al primer usuario Administrador
$usuario = User::whereHas('roles', function($q) {
    $q->where('name', 'Administrador');
})->first();

if (!$usuario) {
    echo "No se encontró usuario Administrador\n";
    exit;
}

auth()->login($usuario);

echo "=== Datos del usuario autenticado ===\n";
echo "ID: " . $usuario->id . "\n";
echo "Nombre: " . $usuario->name . "\n";
echo "Rol: " . ($usuario->roles->first()->name ?? 'Sin rol') . "\n";
echo "Empresa ID: " . $usuario->empresa_id . "\n";
echo "Sucursal ID: " . $usuario->sucursal_id . "\n";
echo "Es Super Admin: " . ($usuario->hasRole('Super Administrador') ? 'SI' : 'NO') . "\n\n";

echo "=== Verificación de datos por tabla ===\n\n";

// Verificar pagos
$pagosTotal = DB::table('pagos')->count();
$pagosPorEmpresa = DB::table('pagos')->where('empresa_id', $usuario->empresa_id)->count();
$pagosPorEmpresaSucursal = DB::table('pagos')->where('empresa_id', $usuario->empresa_id)->where('sucursal_id', $usuario->sucursal_id)->count();

echo "PAGOS:\n";
echo "  - Total: $pagosTotal\n";
echo "  - Por empresa ({$usuario->empresa_id}): $pagosPorEmpresa\n";
echo "  - Por empresa y sucursal ({$usuario->empresa_id}/{$usuario->sucursal_id}): $pagosPorEmpresaSucursal\n\n";

// Verificar matriculas
$matriculasTotal = DB::table('matriculas')->count();
$matriculasPorEmpresa = DB::table('matriculas')->where('empresa_id', $usuario->empresa_id)->count();
$matriculasPorEmpresaSucursal = DB::table('matriculas')->where('empresa_id', $usuario->empresa_id)->where('sucursal_id', $usuario->sucursal_id)->count();

echo "MATRICULAS:\n";
echo "  - Total: $matriculasTotal\n";
echo "  - Por empresa ({$usuario->empresa_id}): $matriculasPorEmpresa\n";
echo "  - Por empresa y sucursal ({$usuario->empresa_id}/{$usuario->sucursal_id}): $matriculasPorEmpresaSucursal\n\n";

// Verificar students
$studentsTotal = DB::table('students')->count();
$studentsPorEmpresa = DB::table('students')->where('empresa_id', $usuario->empresa_id)->count();
$studentsPorEmpresaSucursal = DB::table('students')->where('empresa_id', $usuario->empresa_id)->where('sucursal_id', $usuario->sucursal_id)->count();

echo "STUDENTS:\n";
echo "  - Total: $studentsTotal\n";
echo "  - Por empresa ({$usuario->empresa_id}): $studentsPorEmpresa\n";
echo "  - Por empresa y sucursal ({$usuario->empresa_id}/{$usuario->sucursal_id}): $studentsPorEmpresaSucursal\n\n";

echo "=== Verificación de relaciones completas ===\n\n";

// Verificar pagos con matriculas y students con scope global
$pagosCompletos = DB::table('pagos')
    ->join('matriculas', 'pagos.matricula_id', '=', 'matriculas.id')
    ->join('students', 'matriculas.estudiante_id', '=', 'students.id')
    ->where('pagos.empresa_id', $usuario->empresa_id)
    ->where('pagos.sucursal_id', $usuario->sucursal_id)
    ->where('matriculas.empresa_id', $usuario->empresa_id)
    ->where('matriculas.sucursal_id', $usuario->sucursal_id)
    ->where('students.empresa_id', $usuario->empresa_id)
    ->where('students.sucursal_id', $usuario->sucursal_id)
    ->count();

echo "Pagos con matriculas y students (scope completo): $pagosCompletos\n";

// Verificar con query builder como lo hace el componente
use App\Models\Pago;

$pagoQuery = Pago::query()
    ->whereHas('matricula', function($q) use ($usuario) {
        $q->withoutGlobalScope('multitenancy')
          ->whereHas('student', function($q) use ($usuario) {
              $q->withoutGlobalScope('multitenancy');
          });
    });

$pagosConFiltro = $pagoQuery->count();
echo "Pagos con filtro sin scope en relaciones: $pagosConFiltro\n";
