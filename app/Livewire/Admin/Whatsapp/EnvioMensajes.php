<?php

namespace App\Livewire\Admin\Whatsapp;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;

class EnvioMensajes extends Component
{
    public $to = '';
    public $message = '';
    public $sending = false;
    public $success = null;
    public $error = null;
    public $jwtToken = null;
    public $recentMessages = [];

    public function mount()
    {
        $this->generateToken();
        $this->loadRecentMessages();
    }

    public function generateToken()
    {
        $empresa = \DB::table('empresas')->where('id', 1)->first();
        if ($empresa && $empresa->api_key) {
            $this->jwtToken = $empresa->api_key;
        } else {
            $jwtSecret = config('whatsapp.jwt_secret');
            $payload = [
                'company_id' => 1,
                'company_name' => 'Instituto Vargas Centro',
                'iat' => time(),
                'exp' => time() + (365 * 24 * 60 * 60)
            ];
            $this->jwtToken = JWT::encode($payload, $jwtSecret, 'HS256');
        }
    }

    public function rules()
    {
        return [
            'to' => 'required|string|min:10',
            'message' => 'required|string|min:1|max:1000'
        ];
    }

    public function sendMessage()
    {
        $this->validate();
        
        $this->sending = true;
        $this->success = null;
        $this->error = null;

        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->jwtToken,
                'Content-Type' => 'application/json'
            ])->post(config('whatsapp.api_url') . '/api/whatsapp/send', [
                'to' => $this->to,
                'message' => $this->message
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->success = 'Mensaje enviado exitosamente';
                $this->reset(['to', 'message']);
                $this->loadRecentMessages();
            } else {
                $this->error = $response->json()['error'] ?? 'Error enviando mensaje';
            }
        } catch (\Exception $e) {
            $this->error = 'Error: ' . $e->getMessage();
        }

        $this->sending = false;
    }

    public function loadRecentMessages()
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->jwtToken
            ])->get(config('whatsapp.api_url') . '/api/whatsapp/messages?limit=5');

            if ($response->successful()) {
                $data = $response->json();
                $this->recentMessages = $data['messages'] ?? [];
            }
        } catch (\Exception $e) {
            // Error silencioso
        }
    }

    public function clearMessages()
    {
        $this->reset(['success', 'error']);
    }

    public function render()
    {
        return view('livewire.admin.whatsapp.envio-mensajes');
    }
}