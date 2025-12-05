<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use App\Models\WhatsAppMessage;
use App\Services\WhatsAppService;
use Carbon\Carbon;

class RetryFailedWhatsAppMessages implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Número de días hacia atrás para buscar mensajes
     *
     * @var int
     */
    protected $days;

    /**
     * Máximo de reintentos por mensaje
     *
     * @var int
     */
    protected $maxRetries;

    /**
     * Create a new job instance.
     */
    public function __construct(int $days = 7, int $maxRetries = 3)
    {
        $this->days = $days;
        $this->maxRetries = $maxRetries;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Iniciando proceso de reenvío de mensajes fallidos de WhatsApp', [
            'days' => $this->days,
            'max_retries' => $this->maxRetries
        ]);

        try {
            // Verificar si el servicio de WhatsApp está disponible
            $whatsAppService = app(WhatsAppService::class);
            $connectionTest = $whatsAppService->testConnection();

            if (!$connectionTest['success']) {
                Log::warning('Servicio de WhatsApp no disponible, cancelando reenvío', [
                    'message' => $connectionTest['message']
                ]);
                return;
            }

            // Obtener mensajes que pueden ser reenviados
            $failedMessages = $this->getRetryableMessages();
            
            if ($failedMessages->isEmpty()) {
                Log::info('No se encontraron mensajes para reenviar');
                return;
            }

            Log::info("Se encontraron {$failedMessages->count()} mensajes para reenviar");

            $successCount = 0;
            $failedCount = 0;

            foreach ($failedMessages as $message) {
                try {
                    $result = $this->retryMessage($message, $whatsAppService);
                    
                    if ($result['success']) {
                        $successCount++;
                        Log::info("Mensaje #{$message->id} reenviado exitosamente");
                    } else {
                        $failedCount++;
                        Log::warning("Error al reenviar mensaje #{$message->id}: " . $result['message']);
                    }
                } catch (\Exception $e) {
                    $failedCount++;
                    Log::error("Excepción al reenviar mensaje #{$message->id}: " . $e->getMessage(), [
                        'exception' => $e
                    ]);
                }

                // Pequeña pausa entre mensajes para evitar sobrecarga
                usleep(500000); // 0.5 segundos
            }

            Log::info("Proceso de reenvío completado", [
                'total_processed' => $failedMessages->count(),
                'successful' => $successCount,
                'failed' => $failedCount
            ]);

        } catch (\Exception $e) {
            Log::error('Error en el proceso de reenvío de mensajes fallidos', [
                'exception' => $e
            ]);
            throw $e;
        }
    }

    /**
     * Obtener mensajes que pueden ser reenviados
     */
    private function getRetryableMessages()
    {
        $cutoffDate = Carbon::now()->subDays($this->days);

        return WhatsAppMessage::where('created_at', '>=', $cutoffDate)
            ->where('direction', 'outbound')
            ->retryable() // Usar el scope retryable que definimos
            ->orderBy('created_at', 'asc')
            ->get();
    }

    /**
     * Reintentar el envío de un mensaje
     */
    private function retryMessage(WhatsAppMessage $message, WhatsAppService $service): array
    {
        try {
            // Preparar el mensaje para reenvío
            $template = $message->template;
            $content = $message->message_content;
            $variables = $message->variables ?? [];

            // Si tiene plantilla, usar el contenido procesado
            if ($template) {
                $content = $template->getProcessedContent($variables);
            }

            // Intentar reenviar el mensaje
            $result = $service->sendMessage([
                'phone' => $message->recipient_phone,
                'message' => $content,
                'template' => $template ? $template->name : null,
                'parameters' => $variables
            ]);

            // Actualizar el mensaje con el resultado del reenvío
            $message->markAsRetried($result);

            // Si el reenvío fue exitoso, crear un nuevo registro para el historial
            if ($result['success']) {
                WhatsAppMessage::create([
                    'message_id' => $result['message_id'],
                    'template_id' => $message->template_id,
                    'recipient_phone' => $message->recipient_phone,
                    'recipient_name' => $message->recipient_name,
                    'message_content' => $content,
                    'variables' => $variables,
                    'status' => 'sent',
                    'direction' => 'outbound',
                    'sent_at' => now(),
                    'created_by' => $message->created_by,
                    'metadata' => [
                        'is_retry' => true,
                        'original_message_id' => $message->id,
                        'retry_successful' => true,
                        'auto_retry' => true // Indicar que fue un reenvío automático
                    ]
                ]);
            }

            return $result;
        } catch (\Exception $e) {
            // Manejar excepciones
            $message->incrementRetryCount();
            
            return ['success' => false, 'message' => 'Excepción: ' . $e->getMessage()];
        }
    }

    /**
     * The job failed to process.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Job de reenvío de mensajes fallidos falló', [
            'exception' => $exception,
            'days' => $this->days,
            'max_retries' => $this->maxRetries
        ]);
    }
}