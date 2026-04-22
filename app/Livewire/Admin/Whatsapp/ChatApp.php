<?php

namespace App\Livewire\Admin\Whatsapp;

use Livewire\Component;

class ChatApp extends Component
{
    public string $title = 'WhatsApp';
    public function render()
    {
        $apiUrl = config('whatsapp.api_url', 'http://localhost:3001');
        $apiKey = config('whatsapp.api_key', '');
        return view('livewire.admin.whatsapp.chat-app', [
            'apiUrl' => $apiUrl,
            'apiKey' => $apiKey,
        ])->layout('components.layouts.admin');
    }
}
