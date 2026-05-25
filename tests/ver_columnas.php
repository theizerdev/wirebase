<?php

require_once __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$columns = DB::select('DESCRIBE pastores');

echo "=== COLUMNAS DE LA TABLA PASTORES ===\n\n";

foreach ($columns as $column) {
    echo "Campo: {$column->Field}\n";
    echo "  Tipo: {$column->Type}\n";
    echo "  Null: {$column->Null}\n";
    echo "  Key: {$column->Key}\n";
    echo "  Default: " . ($column->Default ?? 'NULL') . "\n";
    echo "\n";
}

echo "Total de columnas: " . count($columns) . "\n";
