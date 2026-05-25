<?php

require_once __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Actualizando matrículas con valores de estudiantes ===\n";

// Primero, mostrar cuántas vamos a actualizar
$pendientes = DB::select('
    SELECT COUNT(*) as total
    FROM matriculas m
    JOIN students s ON m.estudiante_id = s.id
    WHERE (m.empresa_id IS NULL OR m.sucursal_id IS NULL)
    AND (s.empresa_id IS NOT NULL OR s.sucursal_id IS NOT NULL)
')[0];

echo "Matrículas pendientes de actualización: {$pendientes->total}\n\n";

// Actualizar matrículas con valores de estudiantes
$actualizadas = DB::update('
    UPDATE matriculas m
    JOIN students s ON m.estudiante_id = s.id
    SET m.empresa_id = s.empresa_id,
        m.sucursal_id = s.sucursal_id
    WHERE (m.empresa_id IS NULL OR m.sucursal_id IS NULL)
    AND (s.empresa_id IS NOT NULL OR s.sucursal_id IS NOT NULL)
');

echo "Matrículas actualizadas: {$actualizadas}\n\n";

// Verificar el resultado
$resultado = DB::select('
    SELECT
        empresa_id,
        sucursal_id,
        COUNT(*) as total
    FROM matriculas
    GROUP BY empresa_id, sucursal_id
    ORDER BY total DESC
');

echo "=== Distribución después de la actualización ===\n";
foreach($resultado as $row) {
    echo "empresa_id: " . ($row->empresa_id ?? 'NULL') . ", sucursal_id: " . ($row->sucursal_id ?? 'NULL') . " = {$row->total} matrículas\n";
}
