<?php

use App\Models\Beneficiario;

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$estadoId = $argv[1] ?? null;
if (! $estadoId) {
    echo "Uso: php scripts/check_beneficiarios_estado.php <estado_id>\n";
    exit(1);
}

$beneficiarios = Beneficiario::where('estado_id', $estadoId)
    ->with(['estado', 'responsable.estado'])
    ->get();

echo "Beneficiarios con estado_id={$estadoId}: " . $beneficiarios->count() . "\n";

$withResponsable = $beneficiarios->filter(fn ($item) => $item->responsable !== null);
echo "Con responsable: " . $withResponsable->count() . "\n";

$withCasa = $beneficiarios->filter(fn ($item) => $item->responsable !== null && filled($item->responsable->codigo_casa_alimentacion));
echo "Con codigo_casa_alimentacion en responsable: " . $withCasa->count() . "\n";

$withMadreCasa = $beneficiarios->filter(fn ($item) => $item->responsable !== null && filled($item->responsable->codigo_casa_alimentacion) && $item->es_mujer_embarazada);
echo "Madres en casa de alimentación: " . $withMadreCasa->count() . "\n";

$groupByEstadoBeneficiario = $beneficiarios
    ->groupBy(fn ($item) => $item->estado?->nombre ?? 'Sin estado')
    ->map->count();

$groupByEstadoResponsable = $beneficiarios
    ->groupBy(fn ($item) => $item->responsable?->estado?->nombre ?? 'Sin estado')
    ->map->count();

echo "\nAgrupado por estado del beneficiario:\n";
foreach ($groupByEstadoBeneficiario as $estado => $count) {
    echo " - {$estado}: {$count}\n";
}

echo "\nAgrupado por estado del responsable:\n";
foreach ($groupByEstadoResponsable as $estado => $count) {
    echo " - {$estado}: {$count}\n";
}

if ($beneficiarios->isNotEmpty()) {
    echo "\nDetalles de registros:\n";
    foreach ($beneficiarios as $item) {
        $beneficiarioEstado = $item->estado?->nombre ?? 'NULL';
        $responsableEstado = $item->responsable?->estado?->nombre ?? 'NULL';
        $codigoCasa = $item->responsable?->codigo_casa_alimentacion ?? 'NULL';
        $embarazada = $item->es_mujer_embarazada ? 'Y' : 'N';
        echo sprintf(
            "ID %s | Beneficiario estado: %s | Responsable estado: %s | Casa codigo: %s | Embarazada: %s\n",
            $item->id,
            $beneficiarioEstado,
            $responsableEstado,
            $codigoCasa,
            $embarazada,
        );
    }
}
