<?php

namespace App\Services;

use App\Models\ExchangeRate;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ExchangeRateService
{
    private const DOLARVZLA_API = 'https://api.dolarvzla.com/public/exchange-rate';
    private const BACKUP_API = 'https://api.exchangerate-api.com/v4/latest/USD';

    public function fetchAndStoreRates(): bool
    {
        try {
            $rates = $this->fetchFromDolarVzla() ?? $this->fetchFromBackupAPI();

            if ($rates) {
                return $this->storeRates($rates);
            }

            return false;
        } catch (\Exception $e) {
            Log::error('Error fetching exchange rates: ' . $e->getMessage());
            return false;
        }
    }

    private function fetchFromDolarVzla(): ?array
    {
        try {
            $context = stream_context_create([
                'http' => [
                    'timeout' => 15,
                    'method' => 'GET',
                    'header' => 'User-Agent: Mozilla/5.0'
                ]
            ]);
            
            $response = file_get_contents(self::DOLARVZLA_API, false, $context);
            
            if ($response !== false) {
                $data = json_decode($response, true);
                
                if (isset($data['current']['usd'])) {
                    $usdRate = (float) $data['current']['usd'];
                    $eurRate = (float) $data['current']['eur'];
                    
                    Log::info('DolarVzla rates fetched successfully', ['usd' => $usdRate, 'eur' => $eurRate]);
                    
                    return [
                        'usd_rate' => $usdRate,
                        'eur_rate' => $eurRate,
                        'source' => 'dolarvzla'
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('DolarVzla API fetch failed: ' . $e->getMessage());
        }

        return null;
    }

    private function fetchFromBackupAPI(): ?array
    {
        try {
            $response = Http::timeout(15)->get(self::BACKUP_API);
            
            if ($response->successful()) {
                $data = $response->json();
                
                $vesRate = $data['rates']['VES'] ?? null;
                $eurRate = $data['rates']['EUR'] ?? null;
                
                if ($vesRate) {
                    return [
                        'usd_rate' => $vesRate,
                        'eur_rate' => $eurRate ? $vesRate / $eurRate : null,
                        'source' => 'backup_api'
                    ];
                }
            }
        } catch (\Exception $e) {
            Log::warning('Backup API fetch failed: ' . $e->getMessage());
        }

        return null;
    }

    private function storeRates(array $rates): bool
    {
        try {
            // Actualizar o crear la tasa del día (solo una por día)
            ExchangeRate::updateOrCreate(
                ['date' => today()],
                [
                    'usd_rate' => $rates['usd_rate'],
                    'eur_rate' => $rates['eur_rate'],
                    'source' => $rates['source'],
                    'fetch_time' => now()->format('H:i:s'),
                    'raw_data' => $rates
                ]
            );

            Log::info('Exchange rates stored successfully', $rates);
            return true;
        } catch (\Exception $e) {
            Log::error('Error storing exchange rates: ' . $e->getMessage());
            return false;
        }
    }

    public function getLatestRate(string $currency = 'USD'): ?float
    {
        return ExchangeRate::getLatestRate($currency);
    }

    public function getTodayRates()
    {
        return ExchangeRate::getTodayRates();
    }
}