<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Ver algunas matrículas y sus relaciones
echo "=== Verificando matrículas y sus relaciones ===\n";

$resultados = DB::select('
    SELECT
        m.id,
        m.empresa_id,
        m.sucursal_id,
        s.empresa_id as estudiante_empresa_id,
        s.sucursal_id as estudiante_sucursal_id,
        s.nombres,
        s.apellidos
    FROM matriculas m
    JOIN students s ON m.estudiante_id = s.id
    LIMIT 5
');

echo "Matrículas encontradas:\n";
foreach($resultados as $row) {
    echo "Matrícula ID: {$row->id}\n";
    echo "  - Matrícula empresa_id: " . ($row->empresa_id ?? 'NULL') . ", sucursal_id: " . ($row->sucursal_id ?? 'NULL') . "\n";
    echo "  - Estudiante: {$row->nombres} {$row->apellidos}\n";
    echo "  - Estudiante empresa_id: {$row->estudiante_empresa_id}, sucursal_id: {$row->estudiante_sucursal_id}\n\n";
}

// Contar cuántas matrículas necesitan actualización
echo "=== Contando matrículas con valores NULL ===\n";
$nulls = DB::select('SELECT COUNT(*) as total FROM matriculas WHERE empresa_id IS NULL OR sucursal_id IS NULL')[0];
echo "Matrículas con empresa_id o sucursal_id NULL: {$nulls->total}\n\n";

// Contar por valores
echo "=== Distribución de valores ===\n";
$distribucion = DB::select('
    SELECT
        empresa_id,
        sucursal_id,
        COUNT(*) as total
    FROM matriculas
    GROUP BY empresa_id, sucursal_id
    ORDER BY total DESC
');

foreach($distribucion as $row) {
    echo "empresa_id: " . ($row->empresa_id ?? 'NULL') . ", sucursal_id: " . ($row->sucursal_id ?? 'NULL') . " = {$row->total} matrículas\n";
}
