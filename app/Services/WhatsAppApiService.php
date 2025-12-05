<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class WhatsAppApiService
{
    private string $baseUrl;
    private string $jwtSecret;

    public function __construct()
    {
        $this->baseUrl = env('WHATSAPP_API_URL', 'http://localhost:3001');
        $this->jwtSecret = env('WHATSAPP_JWT_SECRET', 'base64:ItiVlmjSSgrh2LFDfR0JGtPXHRAthPOWSMw6WyrgwIk=');
    }

    private function generateToken(): string
    {
        $payload = [
            'id' => auth()->id() ?? 1,
            'name' => auth()->user()->name ?? 'VargasCentro',
            'exp' => time() + (24 * 60 * 60)
        ];

        return JWT::encode($payload, $this->jwtSecret, 'HS256');
    }

    private function makeRequest(string $method, string $endpoint, array $data = [], int $companyId = null)
    {
        try {
            $token = $this->generateToken();
            
            $headers = [
                'Authorization' => 'Bearer ' . $token,
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ];

            if ($companyId) {
                $empresa = \App\Models\Empresa::find($companyId);
                if ($empresa && $empresa->api_key) {
                    $headers['X-API-Key'] = $empresa->api_key;
                    $headers['X-Company-ID'] = 1; // ID empresa por defecto
                }
            }

            $response = Http::withHeaders($headers)->timeout(30);
            $url = $this->baseUrl . $endpoint;

            switch (strtoupper($method)) {
                case 'GET':
                    $response = $response->get($url, $data);
                    break;
                case 'POST':
                    $response = $response->post($url, $data);
                    break;
                case 'PUT':
                    $response = $response->put($url, $data);
                    break;
                case 'DELETE':
                    $response = $response->delete($url);
                    break;
                default:
                    throw new \InvalidArgumentException("Método HTTP no soportado: {$method}");
            }

            if ($response->successful()) {
                return $response->json();
            }

            $error = $response->json()['error'] ?? 'Error desconocido en la API';
            Log::error('WhatsApp API Error', [
                'url' => $url,
                'method' => $method,
                'status' => $response->status(),
                'error' => $error,
                'response' => $response->body()
            ]);

            return [
                'success' => false,
                'error' => $error,
                'status_code' => $response->status()
            ];

        } catch (\Exception $e) {
            Log::error('WhatsApp API Exception', [
                'message' => $e->getMessage(),
                'endpoint' => $endpoint,
                'trace' => $e->getTraceAsString()
            ]);

            return [
                'success' => false,
                'error' => 'Error de conexión: ' . $e->getMessage()
            ];
        }
    }

    public function getStatus(int $companyId = null): array
    {
        // Si no se proporciona companyId, usar la primera empresa activa
        if (!$companyId) {
            $empresa = \App\Models\Empresa::where('status', 1)->first();
            $companyId = $empresa ? $empresa->id : 1; // ID 1 empresa por defecto
        }
        return $this->makeRequest('GET', '/api/whatsapp/status', [], $companyId);
    }

    public function getQRCode(int $companyId): array
    {
        $this->registerCompany($companyId);
        return $this->makeRequest('GET', '/api/whatsapp/qr', [], $companyId);
    }

    public function sendMessage(string $to, string $message, int $companyId, string $type = 'text', ?string $mediaUrl = null): array
    {
        return $this->makeRequest('POST', '/api/whatsapp/send-message', [
            'to' => $to,
            'message' => $message,
            'type' => $type,
            'mediaUrl' => $mediaUrl
        ], $companyId);
    }

    public function registerCompany(int $companyId): array
    {
        $empresa = \App\Models\Empresa::find($companyId);
        if (!$empresa) {
            return ['success' => false, 'error' => 'Empresa no encontrada'];
        }

        // Generar api_key si no existe
        if (!$empresa->api_key) {
            $empresa->update(['api_key' => \App\Models\Empresa::generateApiKey()]);
            $empresa->refresh();
        }

        $payload = [
            'company_id' => $empresa->id,
            'api_key' => $empresa->api_key,
            'name' => $empresa->razon_social,
            'phone' => $empresa->telefono,
            'is_active' => $empresa->status
        ];

        return $this->makeRequest('POST', '/api/companies/register', $payload);
    }

    public function testConnection(): array
    {
        return $this->makeRequest('GET', '/health');
    }
}