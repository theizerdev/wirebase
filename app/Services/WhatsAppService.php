<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppService
{
    private $baseUrl;
    private $apiKey;
    private $timeout;

    public function __construct()
    {
        $this->baseUrl = config('whatsapp.api_url', 'http://localhost:3001');
        $this->apiKey = config('whatsapp.api_key', 'test-api-key-vargas-centro');
        $this->timeout = config('whatsapp.timeout', 30);
    }

    /**
     * Obtener el estado de la conexión WhatsApp
     */
    public function getStatus()
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['X-API-Key' => $this->apiKey])
                ->get("{$this->baseUrl}/api/whatsapp/status");

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('WhatsApp Status Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener código QR para conectar WhatsApp
     */
    public function getQRCode()
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['X-API-Key' => $this->apiKey])
                ->get("{$this->baseUrl}/api/whatsapp/qr");

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('WhatsApp QR Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Enviar mensaje de texto
     */
    public function sendMessage(string $to, string $message)
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['X-API-Key' => $this->apiKey])
                ->post("{$this->baseUrl}/api/whatsapp/send", [
                    'to' => $to,
                    'message' => $message,
                    'type' => 'text'
                ]);

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('WhatsApp Send Message Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Obtener historial de mensajes
     */
    public function getMessages(array $filters = [])
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['X-API-Key' => $this->apiKey])
                ->get("{$this->baseUrl}/api/whatsapp/messages", $filters);

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('WhatsApp Get Messages Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Conectar WhatsApp
     */
    public function connect()
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['X-API-Key' => $this->apiKey])
                ->post("{$this->baseUrl}/api/whatsapp/connect");

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('WhatsApp Connect Error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Desconectar WhatsApp
     */
    public function disconnect()
    {
        try {
            $response = Http::timeout($this->timeout)
                ->withHeaders(['X-API-Key' => $this->apiKey])
                ->delete("{$this->baseUrl}/api/whatsapp/disconnect");

            return $response->successful() ? $response->json() : null;
        } catch (\Exception $e) {
            Log::error('WhatsApp Disconnect Error: ' . $e->getMessage());
            return null;
        }
    }
}