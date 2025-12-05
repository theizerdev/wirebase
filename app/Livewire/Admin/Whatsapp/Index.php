<?php

namespace App\Livewire\Admin\Whatsapp;

use Livewire\Component;
use App\Traits\HasDynamicLayout;
use Illuminate\Support\Facades\Http;
use Firebase\JWT\JWT;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use HasDynamicLayout;

    public $status = 'disconnected';
    public $user = null;
    public $lastSeen = null;
    public $jwtToken = null;
    public $messages = [];
    public $stats = [
        'sent' => 0,
        'received' => 0,
        'failed' => 0
    ];

    public function mount()
    {
        if (!Auth::user()->can('access whatsapp')) {
            abort(403, 'No tienes permiso para acceder a WhatsApp.');
        }
        
        $this->generateToken();
        $this->loadDashboard();
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

    public function loadDashboard()
    {
        $this->checkStatus();
        $this->loadMessages();
    }

    public function checkStatus()
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->jwtToken
            ])->get(config('whatsapp.api_url') . '/api/whatsapp/status');

            if ($response->successful()) {
                $data = $response->json();
                $this->status = $data['connectionState'] ?? 'disconnected';
                $this->user = $data['user'] ?? null;
                $this->lastSeen = $data['lastSeen'] ?? null;
            }
        } catch (\Exception $e) {
            // Error silencioso
           
        }
    }

    public function loadMessages()
    {
        try {
            $response = Http::withHeaders([
                'X-API-Key' => $this->jwtToken
            ])->get(config('whatsapp.api_url') . '/api/whatsapp/messages?limit=10');

            if ($response->successful()) {
                $data = $response->json();
                $this->messages = $data['messages'] ?? [];
                
                $this->stats = [
                    'sent' => collect($this->messages)->where('status', 'sent')->count(),
                    'received' => collect($this->messages)->where('status', 'received')->count(),
                    'failed' => collect($this->messages)->where('status', 'failed')->count()
                ];
            }
        } catch (\Exception $e) {
            // Error silencioso
        }
    }

    public function refresh()
    {
        $this->loadDashboard();
        session()->flash('message', 'Dashboard actualizado correctamente.');
    }

    protected function getPageTitle(): string
    {
        return 'WhatsApp - Panel de Control';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.whatsapp.index' => 'WhatsApp'
        ];
    }

    public function render()
    {
        return $this->renderWithLayout('livewire.admin.whatsapp.index', [
            'status' => $this->status,
            'user' => $this->user,
            'lastSeen' => $this->lastSeen,
            'messages' => $this->messages,
            'stats' => $this->stats
        ], [
            'title' => 'WhatsApp - Panel de Control',
            'description' => 'Panel de control para gestión de WhatsApp',
            'breadcrumb' => $this->getBreadcrumb()
        ]);
    }
}