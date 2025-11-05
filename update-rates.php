<?php

require __DIR__.'/vendor/autoload.php';
require __DIR__.'/bootstrap/app.php';

use App\Services\ExchangeRateService;

$service = new ExchangeRateService();
$result = $service->fetchNow();

echo "Tasas actualizadas:\n";
echo "USD: " . ($result['usd_rate'] ?? 'N/A') . "\n";
echo "EUR: " . ($result['eur_rate'] ?? 'N/A') . "\n";
echo "Fuente: " . ($result['source'] ?? 'N/A') . "\n";
