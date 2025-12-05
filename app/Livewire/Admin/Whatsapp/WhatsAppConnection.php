<?php

namespace App\Livewire\Admin\Whatsapp;

use Livewire\Component;
use App\Traits\HasDynamicLayout;
use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;

class WhatsAppConnection extends Component
{
    use HasDynamicLayout;

    public $status = 'disconnected';
    public $qrCode = null;
    public $user = null;
    public $lastSeen = null;
    public $jwtToken = null;
    public $isConnecting = false;
    public $connectionError = null;
    public $autoRefresh = true;
    public $refreshInterval = 5;

    protected $listeners = ['refreshConnection' => 'checkConnection'];

    public function mount()
    {
        if (!Auth::user()->can('access whatsapp')) {
            abort(403, 'No tienes permiso para acceder a WhatsApp.');
        }
        
        $this->generateToken();
        $this->checkConnection();
        //dd($this->generateToken());
    }

    public function generateToken()
    {
        $this->jwtToken = config('whatsapp.api_key', 'test-api-key-vargas-centro');
    }

    public function checkConnection()
    {
        try {
            $apiUrl = config('whatsapp.api_url', 'http://localhost:3001');
            
            // Verificar si el servicio está disponible
            $healthResponse = Http::timeout(5)->get($apiUrl . '/health');
            
            if (!$healthResponse->successful()) {
                $this->status = 'service_unavailable';
                $this->connectionError = 'El servicio WhatsApp no está disponible. Por favor, verifica que el servicio esté ejecutándose.';
                return;
            }
            
            $response = Http::withHeaders([
                'X-API-Key' => $this->jwtToken
            ])->timeout(10)->get($apiUrl . '/api/whatsapp/status');
           
            if ($response->successful()) {
                $data = $response->json();
                $this->status = $data['connectionState'] ?? 'disconnected';
                $this->user = $data['user'] ?? null;
                $this->lastSeen = $data['lastSeen'] ?? null;
                $this->connectionError = null;

                // Si el estado es QR, obtener el código QR
                if ($this->status === 'qr_ready') {
                    $this->getQRCode();
                } else {
                    $this->qrCode = null;
                }
            } else {
                $this->status = 'error';
                $this->connectionError = 'Error al obtener el estado de conexión: ' . $response->status();
                $this->dispatch('showToast', [
                    'type' => 'error',
                    'message' => 'Error al conectar con el servicio WhatsApp'
                ]);
            }
        } catch (\Exception $e) {
            $this->status = 'error';
            $errorMessage = 'Error al conectar con el servicio WhatsApp: ' . $e->getMessage();
            $this->connectionError = $errorMessage;
            $this->dispatch('showToast', [
                'type' => 'error',
                'message' => $errorMessage
            ]);
        }
    }

    public function getQRCode()
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->jwtToken
            ])->timeout(10)->get(config('whatsapp.api_url', 'http://localhost:3001') . '/api/whatsapp/qr');

            if ($response->successful()) {
                $data = $response->json();
                $this->qrCode = $data['qr'] ?? null;
            }
        } catch (\Exception $e) {
            $this->qrCode = null;
        }
    }

    public function startConnection()
    {
        $this->isConnecting = true;
        $this->connectionError = null;

        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->jwtToken
            ])->timeout(30)->post(config('whatsapp.api_url', 'http://localhost:3001') . '/api/whatsapp/connect');

            if ($response->successful()) {
                $this->checkConnection();
                session()->flash('success', 'Conexión iniciada correctamente. Escanea el código QR con WhatsApp.');
            } else {
                $this->connectionError = 'Error al iniciar la conexión';
            }
        } catch (\Exception $e) {
            $this->connectionError = 'Error al conectar con el servicio WhatsApp';
        }

        $this->isConnecting = false;
    }

    public function disconnect()
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->jwtToken
            ])->timeout(10)->delete(config('whatsapp.api_url', 'http://localhost:3001') . '/api/whatsapp/disconnect');

            if ($response->successful()) {
                $this->status = 'disconnected';
                $this->user = null;
                $this->qrCode = null;
                $this->lastSeen = null;
                session()->flash('success', 'Conexión cerrada correctamente.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error al desconectar.');
        }
    }

    public function getStatusColorProperty()
    {
        return match($this->status) {
            'connected' => 'success',
            'connecting' => 'warning',
            'qr_ready' => 'info',
            'disconnected' => 'danger',
            'error' => 'danger',
            'service_unavailable' => 'warning',
            default => 'secondary'
        };
    }

    public function getStatusIconProperty()
    {
        return match($this->status) {
            'connected' => 'ri ri-checkbox-circle-line',
            'connecting' => 'ri ri-loader-4-line',
            'qr_ready' => 'ri ri-qr-code-line',
            'disconnected' => 'ri ri-close-circle-line',
            'error' => 'ri ri-error-warning-line',
            'service_unavailable' => 'ri ri-wifi-off-line',
            default => 'ri ri-help-circle-line'
        };
    }

    public function getStatusMessageProperty()
    {
        return match($this->status) {
            'connected' => 'Conectado exitosamente',
            'connecting' => 'Conectando...',
            'qr_ready' => 'Escanea el código QR con WhatsApp',
            'disconnected' => 'Desconectado',
            'error' => 'Error de conexión',
            'service_unavailable' => 'Servicio no disponible',
            default => 'Estado desconocido'
        };
    }

    protected function getPageTitle(): string
    {
        return 'WhatsApp Conexión';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.whatsapp.dashboard' => 'WhatsApp',
            'admin.whatsapp.connection' => 'Conexión'
        ];
    }

    public function render()
    {
        return $this->renderWithLayout('livewire.admin.whatsapp.whatsapp-connection', [
            'status' => $this->status,
            'qrCode' => $this->qrCode,
            'user' => $this->user,
            'lastSeen' => $this->lastSeen,
            'isConnecting' => $this->isConnecting,
            'connectionError' => $this->connectionError
        ], [
            'title' => 'WhatsApp Conexión',
            'description' => 'Gestiona la conexión de WhatsApp Business API',
            'breadcrumb' => $this->getBreadcrumb()
        ]);
    }
}