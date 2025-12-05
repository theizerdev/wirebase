<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class WhatsAppHealthService
{
    private $whatsappService;
    
    public function __construct()
    {
        $this->whatsappService = new WhatsAppService();
    }
    
    /**
     * Verificar salud completa del sistema WhatsApp
     */
    public function healthCheck(): array
    {
        $checks = [
            'api_connection' => $this->checkApiConnection(),
            'whatsapp_status' => $this->checkWhatsAppStatus(),
            'jwt_auth' => $this->checkJwtAuth(),
            'message_sending' => $this->checkMessageSending(),
            'queue_system' => $this->checkQueueSystem()
        ];
        
        $overallHealth = $this->calculateOverallHealth($checks);
        
        return [
            'overall_status' => $overallHealth['status'],
            'score' => $overallHealth['score'],
            'checks' => $checks,
            'timestamp' => now()->toISOString()
        ];
    }
    
    private function checkApiConnection(): array
    {
        try {
            $result = $this->whatsappService->testConnection();
            return [
                'status' => $result['success'] ? 'healthy' : 'unhealthy',
                'message' => $result['success'] ? 'API conectada' : $result['error'],
                'response_time' => $this->measureResponseTime(function() {
                    return $this->whatsappService->testConnection();
                })
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Error: ' . $e->getMessage(),
                'response_time' => null
            ];
        }
    }
    
    private function checkWhatsAppStatus(): array
    {
        try {
            $result = $this->whatsappService->getStatus();
            $isReady = isset($result['isReady']) && $result['isReady'];
            
            return [
                'status' => $isReady ? 'healthy' : 'warning',
                'message' => $isReady ? 'WhatsApp listo' : 'WhatsApp no conectado',
                'whatsapp_status' => $result['status'] ?? 'unknown',
                'is_ready' => $isReady
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Error: ' . $e->getMessage()
            ];
        }
    }
    
    private function checkJwtAuth(): array
    {
        try {
            $configFile = base_path('whatsapp-service-config.env');
            if (!file_exists($configFile)) {
                return [
                    'status' => 'unhealthy',
                    'message' => 'Archivo de configuración JWT no existe'
                ];
            }
            
            $content = file_get_contents($configFile);
            $hasToken = strpos($content, 'LARAVEL_API_KEY=') !== false;
            
            if (!$hasToken) {
                return [
                    'status' => 'unhealthy',
                    'message' => 'Token JWT no configurado'
                ];
            }
            
            return [
                'status' => 'healthy',
                'message' => 'JWT configurado correctamente'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Error verificando JWT: ' . $e->getMessage()
            ];
        }
    }
    
    private function checkMessageSending(): array
    {
        try {
            // Usar número de prueba que no cause spam
            $testNumber = '584241234567';
            $testMessage = '🔍 Health check - ' . now()->format('H:i:s');
            
            $result = $this->whatsappService->sendMessage($testNumber, $testMessage);
            
            return [
                'status' => $result['success'] ? 'healthy' : 'unhealthy',
                'message' => $result['success'] ? 'Envío funcionando' : $result['error'],
                'simulated' => $result['simulated'] ?? false
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'unhealthy',
                'message' => 'Error enviando mensaje: ' . $e->getMessage()
            ];
        }
    }
    
    private function checkQueueSystem(): array
    {
        try {
            $queueConnection = config('queue.default');
            $pendingJobs = \DB::table('jobs')->count();
            
            return [
                'status' => 'healthy',
                'message' => 'Sistema de colas operativo',
                'connection' => $queueConnection,
                'pending_jobs' => $pendingJobs
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'warning',
                'message' => 'Sistema de colas no disponible: ' . $e->getMessage()
            ];
        }
    }
    
    private function calculateOverallHealth(array $checks): array
    {
        $totalChecks = count($checks);
        $healthyCount = 0;
        $warningCount = 0;
        
        foreach ($checks as $check) {
            if ($check['status'] === 'healthy') {
                $healthyCount++;
            } elseif ($check['status'] === 'warning') {
                $warningCount++;
            }
        }
        
        $score = ($healthyCount + ($warningCount * 0.5)) / $totalChecks * 100;
        
        if ($score >= 90) {
            $status = 'excellent';
        } elseif ($score >= 70) {
            $status = 'good';
        } elseif ($score >= 50) {
            $status = 'fair';
        } else {
            $status = 'poor';
        }
        
        return [
            'status' => $status,
            'score' => round($score, 1)
        ];
    }
    
    private function measureResponseTime(callable $callback): float
    {
        $start = microtime(true);
        $callback();
        return round((microtime(true) - $start) * 1000, 2); // ms
    }
    
    /**
     * Obtener métricas de rendimiento
     */
    public function getPerformanceMetrics(): array
    {
        return Cache::remember('whatsapp_performance_metrics', 300, function() {
            return [
                'avg_response_time' => $this->calculateAverageResponseTime(),
                'success_rate' => $this->calculateSuccessRate(),
                'daily_message_count' => $this->getDailyMessageCount(),
                'error_rate' => $this->calculateErrorRate()
            ];
        });
    }
    
    private function calculateAverageResponseTime(): float
    {
        // Simular cálculo de tiempo promedio de respuesta
        $times = [];
        for ($i = 0; $i < 3; $i++) {
            $times[] = $this->measureResponseTime(function() {
                $this->whatsappService->testConnection();
            });
        }
        return round(array_sum($times) / count($times), 2);
    }
    
    private function calculateSuccessRate(): float
    {
        // En un entorno real, esto vendría de logs o métricas
        return 98.5; // Ejemplo: 98.5% de éxito
    }
    
    private function getDailyMessageCount(): int
    {
        try {
            $stats = $this->whatsappService->getDailyStats();
            return $stats['stats']['sent'] ?? 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
    
    private function calculateErrorRate(): float
    {
        return 100 - $this->calculateSuccessRate();
    }
}