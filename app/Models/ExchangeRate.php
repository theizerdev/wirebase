<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class ExchangeRate extends Model
{
    protected $fillable = [
        'date',
        'usd_rate',
        'eur_rate',
        'source',
        'fetch_time',
        'raw_data'
    ];

    protected $casts = [
        'date' => 'date',
        'fetch_time' => 'datetime:H:i:s',
        'raw_data' => 'array',
        'usd_rate' => 'decimal:4',
        'eur_rate' => 'decimal:4'
    ];

    public static function getLatestRate($currency = 'USD')
    {
        $column = strtolower($currency) . '_rate';
        return self::whereDate('date', today())
            ->whereNotNull($column)
            ->latest('fetch_time')
            ->value($column);
    }

    public static function getTodayRate()
    {
        return self::whereDate('date', today())->first();
    }

    /**
     * Obtener la tasa BCV de una fecha específica
     * Si no existe en BD, intenta obtenerla de DolarAPI (solo para fechas actuales)
     *
     * @param string|Carbon $date Fecha de la tasa
     * @return float|null Tasa USD del BCV
     */
    public static function getRateByDate($date): ?float
    {
        $date = $date instanceof Carbon ? $date : Carbon::parse($date);

        // 1. Buscar la tasa exacta de esa fecha en BD
        $rate = self::whereDate('date', $date)
            ->whereNotNull('usd_rate')
            ->latest('fetch_time')
            ->value('usd_rate');

        if ($rate) {
            Log::info("Tasa encontrada en BD para {$date->format('Y-m-d')}: {$rate}");
            return (float) $rate;
        }

        // 2. Solo intentar obtener de DolarAPI si es hoy o ayer (DolarAPI solo da tasas actuales)
        if ($date->isToday() || $date->isYesterday()) {
            try {
                Log::info("Obteniendo tasa actual desde DolarAPI...");

                $context = stream_context_create([
                    'http' => [
                        'timeout' => 10,
                        'method' => 'GET',
                        'header' => 'User-Agent: Mozilla/5.0'
                    ]
                ]);

                $response = file_get_contents(
                    'https://ve.dolarapi.com/v1/cotizaciones',
                    false,
                    $context
                );

                if ($response !== false) {
                    $data = json_decode($response, true);

                    if (is_array($data)) {
                        // Buscar cotización del BCV
                        foreach ($data as $cotizacion) {
                            if (isset($cotizacion['fuente']) &&
                                strtolower($cotizacion['fuente']) === 'bcv' &&
                                isset($cotizacion['promedio'])) {

                                $tasaBcv = (float) $cotizacion['promedio'];

                                // Guardar en BD para uso futuro
                                self::updateOrCreate(
                                    ['date' => $date],
                                    [
                                        'usd_rate' => $tasaBcv,
                                        'eur_rate' => null,
                                        'source' => 'bcv',
                                        'fetch_time' => now()->format('H:i:s'),
                                        'raw_data' => $cotizacion
                                    ]
                                );

                                Log::info("Tasa BCV obtenida de API para {$date->format('Y-m-d')}: {$tasaBcv}");
                                return $tasaBcv;
                            }
                        }
                    }
                }
            } catch (\Exception $e) {
                Log::warning("No se pudo obtener tasa de DolarAPI para {$date->format('Y-m-d')}: " . $e->getMessage());
            }
        } else {
            Log::info("Fecha histórica ({$date->format('Y-m-d')}), buscando tasa más cercana en BD...");
        }

        // 3. Para fechas históricas o si falla la API, buscar la tasa más cercana anterior en BD
        $nearestRate = self::whereDate('date', '<=', $date)
            ->whereNotNull('usd_rate')
            ->orderBy('date', 'desc')
            ->latest('fetch_time')
            ->first();

        if ($nearestRate) {
            $daysDiff = $date->diffInDays(Carbon::parse($nearestRate->date));
            Log::info("Usando tasa más cercana para {$date->format('Y-m-d')}: {$nearestRate->usd_rate} (fecha: {$nearestRate->date->format('Y-m-d')}, diferencia: {$daysDiff} días)");
            return (float) $nearestRate->usd_rate;
        }

        Log::warning("No se encontró ninguna tasa para la fecha {$date->format('Y-m-d')}");
        return null;
    }

    /**
     * Obtener o crear tasa para hoy desde DolarAPI
     *
     * @return float|null Tasa USD actualizada
     */
    public static function getOrCreateTodayRate(): ?float
    {
        // Verificar si ya tenemos tasa de hoy
        $todayRate = self::getTodayRate();

        if ($todayRate && $todayRate->usd_rate) {
            return (float) $todayRate->usd_rate;
        }

        // Si no hay tasa, intentar obtenerla
        $service = new \App\Services\ExchangeRateService();
        $success = $service->fetchAndStoreRates();

        if ($success) {
            $newRate = self::getTodayRate();
            return $newRate ? (float) $newRate->usd_rate : null;
        }

        return null;
    }
}
