<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppApiIntegrationService
{
    private $apiUrl;
    private $adminToken;

    public function __construct()
    {
        $this->apiUrl = config('whatsapp.api_url', 'http://localhost:3001');
        $this->adminToken = config('whatsapp.admin_token', 'admin-token-123');
    }

    public function createCompany($empresa)
    {
        try {
            Log::info('Iniciando creación de empresa en WhatsApp API', [
                'empresa_id' => $empresa->id,
                'empresa_nombre' => $empresa->razon_social,
                'api_url' => $this->apiUrl
            ]);

            // Usar el endpoint correcto: /api/whatsapp/register-company con autenticación
            $response = Http::timeout(10)
                ->withHeaders([
                    'Content-Type' => 'application/json',
                    'X-API-Key' => $this->adminToken // Usar X-API-Key como se requiere
                ])
                ->post($this->apiUrl . '/api/whatsapp/register-company', [
                    'company_id' => $empresa->id
                ]);

            Log::info('Respuesta de WhatsApp API', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if ($response->successful()) {
                $data = $response->json();
                
                // El servicio puede devolver la API key directamente
                if (isset($data['api_key'])) {
                    // Guardar API key en la empresa
                    $empresa->update([
                        'whatsapp_api_key' => $data['api_key']
                    ]);

                    Log::info('Empresa sincronizada con WhatsApp API', [
                        'empresa_id' => $empresa->id,
                        'api_key' => $data['api_key']
                    ]);

                    return $data['api_key'];
                }
                
                // O puede devolver un mensaje de éxito
                if (isset($data['success']) && $data['success']) {
                    Log::info('Empresa registrada exitosamente', [
                        'empresa_id' => $empresa->id,
                        'response' => $data
                    ]);
                    return true;
                }
            }

            Log::error('Error creando empresa en WhatsApp API', [
                'status' => $response->status(),
                'response' => $response->body()
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('Excepción creando empresa en WhatsApp API', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'empresa_id' => $empresa->id ?? 'unknown'
            ]);

            return false;
        }
    }

    public function updateCompany($empresa)
    {
        // Implementar actualización si es necesario
    }

    public function deleteCompany($empresa)
    {
        // Implementar eliminación si es necesario
    }
}