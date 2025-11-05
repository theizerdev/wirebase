<?php

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Autenticar el primer usuario
$user = \App\Models\User::first();
if ($user) {
    auth()->login($user);

    echo "=== DEBUG DE FILTROS DE PAGOS ===" . PHP_EOL;

    // Simular los valores por defecto del componente
    $search = '';
    $status = '';
    $sortBy = 'created_at';
    $sortDirection = 'desc';
    $perPage = 10;

    echo "Filtros aplicados:" . PHP_EOL;
    echo "- Search: '" . $search . "'" . PHP_EOL;
    echo "- Status: '" . $status . "'" . PHP_EOL;
    echo "- SortBy: '" . $sortBy . "'" . PHP_EOL;
    echo "- SortDirection: '" . $sortDirection . "'" . PHP_EOL;
    echo "- PerPage: " . $perPage . PHP_EOL;
    echo PHP_EOL;

    // Construir la consulta igual que el componente
    $query = \App\Models\Pago::with(['matricula.student', 'detalles.conceptoPago', 'user', 'serieModel'])
        ->whereHas('matricula', function($q) {
            $q->whereHas('student');
        });

    // Aplicar filtros como en el componente
    if ($search !== '') {
        $query->whereHas('matricula.student', function ($subQuery) use ($search) {
            $subQuery->where('nombres', 'like', '%' . $search . '%')
                ->orWhere('apellidos', 'like', '%' . $search . '%')
                ->orWhere('documento_identidad', 'like', '%' . $search . '%');
        })
        ->orWhereHas('detalles.conceptoPago', function($subQuery) use ($search) {
            $subQuery->where('nombre', 'like', '%' . $search . '%');
        })
        ->orWhere('referencia', 'like', '%' . $search . '%')
        ->orWhere('serie', 'like', '%' . $search . '%')
        ->orWhere('numero', 'like', '%' . $search . '%');
    }

    if ($status !== '') {
        $query->where('estado', $status);
    }

    $query->orderBy($sortBy, $sortDirection);

    echo "SQL generado:" . PHP_EOL;
    echo $query->toSql() . PHP_EOL;
    echo PHP_EOL;

    echo "Resultados:" . PHP_EOL;
    $resultados = $query->get();
    echo "Total de resultados: " . $resultados->count() . PHP_EOL;

    if ($resultados->count() > 0) {
        echo "Primeros resultados:" . PHP_EOL;
        foreach ($resultados->take(3) as $pago) {
            echo "- Pago ID: " . $pago->id . ", Estado: " . $pago->estado . ", Total: " . $pago->total . PHP_EOL;
        }
    }

} else {
    echo "No hay usuarios en el sistema" . PHP_EOL;
}
