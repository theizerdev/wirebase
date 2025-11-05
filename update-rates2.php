<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

use App\Services\ExchangeRateService;

$service = new ExchangeRateService();
$result = $service->fetchAndStoreRates();

echo "Tasas actualizadas: " . ($result ? 'ÉXITO' : 'FALLÓ') . "\n";

$todayRate = \App\Models\ExchangeRate::getTodayRate();
if ($todayRate) {
    echo "USD: " . $todayRate->usd_rate . "\n";
    echo "EUR: " . $todayRate->eur_rate . "\n";
    echo "Fuente: " . $todayRate->source . "\n";
} else {
    echo "No hay datos disponibles\n";
}
