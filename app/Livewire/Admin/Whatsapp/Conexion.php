<?php

namespace App\Livewire\Admin\Whatsapp;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;

class Conexion extends Component
{
    public $status = 'disconnected';
    public $qrCode = null;
    public $isConnecting = false;
    public $error = null;
    public $jwtToken = null;

    public function mount()
    {
        $this->generateToken();
        $this->checkStatus();
    }

    public function generateToken()
    {
        $jwtSecret = config('whatsapp.jwt_secret');
        $payload = [
            'company_id' => 1,
            'company_name' => 'Instituto Vargas Centro',
            'iat' => time(),
            'exp' => time() + (365 * 24 * 60 * 60)
        ];
        $this->jwtToken = JWT::encode($payload, $jwtSecret, 'HS256');
    }

    public function connect()
    {
        $this->isConnecting = true;
        $this->error = null;

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->jwtToken,
                'Content-Type' => 'application/json'
            ])->post(config('whatsapp.api_url') . '/api/whatsapp/connect');

            if ($response->successful()) {
                $this->status = 'connecting';
                $this->checkQR();
            } else {
                $this->error = $response->json()['error'] ?? 'Error de conexión';
            }
        } catch (\Exception $e) {
            $this->error = 'Error: ' . $e->getMessage();
        }

        $this->isConnecting = false;
    }

    public function checkStatus()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->jwtToken
            ])->get(config('whatsapp.api_url') . '/api/whatsapp/status');

            if ($response->successful()) {
                $data = $response->json();
                $this->status = $data['connectionState'] ?? 'disconnected';
                
                if ($this->status === 'qr_ready') {
                    $this->checkQR();
                }
            }
        } catch (\Exception $e) {
            $this->error = 'Error verificando estado: ' . $e->getMessage();
        }
    }

    public function checkQR()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->jwtToken
            ])->get(config('whatsapp.api_url') . '/api/whatsapp/qr');

            if ($response->successful()) {
                $data = $response->json();
                if ($data['success'] && isset($data['qr'])) {
                    $this->qrCode = $data['qr'];
                }
            }
        } catch (\Exception $e) {
            $this->error = 'Error obteniendo QR: ' . $e->getMessage();
        }
    }

    public function disconnect()
    {
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->jwtToken
            ])->delete(config('whatsapp.api_url') . '/api/whatsapp/disconnect');

            if ($response->successful()) {
                $this->status = 'disconnected';
                $this->qrCode = null;
            }
        } catch (\Exception $e) {
            $this->error = 'Error desconectando: ' . $e->getMessage();
        }
    }

    public function render()
    {
        return view('livewire.admin.whatsapp.conexion');
    }
}