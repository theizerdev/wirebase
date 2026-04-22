<?php

namespace App\Livewire\Admin\Whatsapp;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Traits\HasDynamicLayout;
use App\Models\WhatsAppMessage;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

class WhatsAppChatModule extends Component
{
    use HasDynamicLayout, WithFileUploads;

    // Connection state
    public $connectionStatus = 'disconnected';
    public $whatsappApiKey = null;
    public $companyId = null;
    public $empresaNombre = null;
    public $connectionError = null;

    // Chat state
    public $conversations = [];
    public $currentPeer = null;
    public $currentPeerName = null;
    public $currentPeerStatus = null;
    public $messages = [];
    public $messageText = '';
    public $searchQuery = '';
    public $activeFilter = 'all'; // all, unread, groups, contacts
    
    // UI state
    public $isLoading = false;
    public $isSending = false;
    public $showContactInfo = false;
    public $showNewChatModal = false;
    public $newChatPhone = '';
    public $newChatMessage = '';
    
    // File upload
    public $attachment = null;
    
    // Voice recording state
    public $isRecording = false;
    
    // Stats
    public $stats = [
        'total_chats' => 0,
        'unread' => 0,
        'sent_today' => 0,
        'received_today' => 0,
    ];
    
    // Pagination
    public $conversationPage = 1;
    public $messagesPage = 1;
    public $hasMoreConversations = true;
    public $hasMoreMessages = true;

    protected $listeners = [
        'refreshChat' => 'loadConversations',
        'messageReceived' => 'handleIncomingMessage',
    ];

    protected $rules = [
        'messageText' => 'required|string|min:1|max:4096',
        'newChatPhone' => 'required|string|min:10|max:20',
        'newChatMessage' => 'required|string|min:1|max:4096',
        'attachment' => 'nullable|file|max:16384', // 16MB max
    ];

    public function mount()
    {
        if (!Auth::user()->can('access whatsapp')) {
            abort(403, 'No tienes permiso para acceder a WhatsApp.');
        }

        $this->initializeWhatsApp();
        $this->checkConnection();
        $this->loadConversations();
        $this->loadStats();
    }

    public function initializeWhatsApp()
    {
        $empresa = auth()->user()->empresa ?? null;
        if ($empresa) {
            $this->companyId = $empresa->id;
            $this->whatsappApiKey = $empresa->whatsapp_api_key;
            $this->empresaNombre = $empresa->razon_social;
            
            if (empty($this->whatsappApiKey)) {
                $this->connectionError = 'Esta empresa no tiene configurada la API Key de WhatsApp.';
            }
        } else {
            $this->connectionError = 'Usuario sin empresa asignada.';
        }
    }

    private function getApiHeaders(): array
    {
        return [
            'X-API-Key' => $this->whatsappApiKey,
            'X-Company-Id' => (string) $this->companyId,
            'Content-Type' => 'application/json',
        ];
    }

    private function getApiUrl(): string
    {
        return rtrim(config('whatsapp.api_url', 'http://localhost:3001'), '/');
    }

    public function checkConnection()
    {
        if (!$this->whatsappApiKey) {
            $this->connectionStatus = 'error';
            return;
        }

        try {
            $response = Http::timeout(config('whatsapp.timeouts.status', 5))
                ->withHeaders($this->getApiHeaders())
                ->get($this->getApiUrl() . '/api/whatsapp/status');

            if ($response->successful()) {
                $data = $response->json();
                $this->connectionStatus = $data['connectionState'] ?? 'disconnected';
                $this->connectionError = null;
            }
        } catch (\Illuminate\Http\Client\ConnectionException $e) {
            $this->connectionStatus = 'service_unavailable';
            $this->connectionError = 'Servicio de WhatsApp no disponible.';
        } catch (\Exception $e) {
            $this->connectionStatus = 'error';
            $this->connectionError = 'Error al verificar estado.';
        }
    }

    /**
     * Format phone number for better display
     */
    private function formatPhoneNumber(string $peer): array
    {
        // Remove @s.whatsapp.net or @g.us suffix
        $phone = preg_replace('/@.*$/', '', $peer);
        
        // Extract device ID if present (format: number:device@s.whatsapp.net)
        $parts = explode(':', $phone);
        $baseNumber = $parts[0] ?? $phone;
        $deviceId = $parts[1] ?? null;
        
        // Clean the number - remove non-digits
        $cleanNumber = preg_replace('/\D/', '', $baseNumber);
        
        // Format for display
        $displayName = $cleanNumber;
        $formattedNumber = $cleanNumber;
        
        // Add country code formatting if it's a Venezuelan number
        if (strlen($cleanNumber) === 11 && str_starts_with($cleanNumber, '58')) {
            $formattedNumber = '+58 ' . substr($cleanNumber, 2, 3) . ' ' . substr($cleanNumber, 5, 3) . ' ' . substr($cleanNumber, 8);
            $displayName = $formattedNumber;
        } elseif (strlen($cleanNumber) === 10) {
            $formattedNumber = substr($cleanNumber, 0, 3) . ' ' . substr($cleanNumber, 3, 3) . ' ' . substr($cleanNumber, 6);
            $displayName = $formattedNumber;
        }
        
        return [
            'raw' => $peer,
            'base' => $baseNumber,
            'clean' => $cleanNumber,
            'formatted' => $formattedNumber,
            'display' => $displayName,
            'device' => $deviceId,
            'is_group' => str_contains($peer, '@g.us'),
            'is_newsletter' => str_contains($peer, '@newsletter'),
            'is_lid' => str_contains($peer, '@lid'),
        ];
    }

    /**
     * Get display name for a conversation
     */
    private function getConversationDisplayName(array $conv): string
    {
        $peer = $conv['peer'] ?? '';
        $name = $conv['name'] ?? '';
        
        // If we have a custom name, use it
        if (!empty($name) && $name !== $peer) {
            return $name;
        }
        
        // Format the phone number
        $formatted = $this->formatPhoneNumber($peer);
        
        // Handle different types
        if ($formatted['is_group']) {
            return 'Grupo ' . $formatted['base'];
        } elseif ($formatted['is_newsletter']) {
            return 'Newsletter ' . $formatted['base'];
        } elseif ($formatted['is_lid']) {
            return 'LID ' . $formatted['base'];
        } elseif (!empty($formatted['device'])) {
            return $formatted['formatted'] . ' (Dispositivo ' . $formatted['device'] . ')';
        } else {
            return $formatted['formatted'];
        }
    }

    public function loadConversations()
    {
        if (!$this->whatsappApiKey) return;

        $this->isLoading = true;

        try {
            $response = Http::timeout(config('whatsapp.timeouts.send_message', 10))
                ->withHeaders($this->getApiHeaders())
                ->get($this->getApiUrl() . '/api/whatsapp/conversations');

            if ($response->successful()) {
                $data = $response->json();
                $conversations = collect($data['conversations'] ?? [])
                    ->map(function ($conv) {
                        // Enhance conversation data with better formatting
                        $conv['displayName'] = $this->getConversationDisplayName($conv);
                        $conv['phoneInfo'] = $this->formatPhoneNumber($conv['peer'] ?? '');
                        $conv['isGroup'] = str_contains($conv['peer'] ?? '', '@g.us');
                        $conv['isNewsletter'] = str_contains($conv['peer'] ?? '', '@newsletter');
                        $conv['isLid'] = str_contains($conv['peer'] ?? '', '@lid');
                        
                        // Truncate last message if too long
                        if (isset($conv['lastMessage']) && strlen($conv['lastMessage']) > 60) {
                            $conv['lastMessage'] = substr($conv['lastMessage'], 0, 60) . '...';
                        }
                        
                        return $conv;
                    });
                
                // Apply search filter
                if (!empty($this->searchQuery)) {
                    $query = strtolower($this->searchQuery);
                    $conversations = $conversations->filter(function ($c) use ($query) {
                        $peer = strtolower($c['peer'] ?? '');
                        $name = strtolower($c['displayName'] ?? '');
                        $lastMsg = strtolower($c['lastMessage'] ?? '');
                        return str_contains($peer, $query) || str_contains($name, $query) || str_contains($lastMsg, $query);
                    });
                }

                // Apply type filter
                if ($this->activeFilter === 'unread') {
                    $conversations = $conversations->filter(fn($c) => ($c['unreadCount'] ?? 0) > 0);
                } elseif ($this->activeFilter === 'groups') {
                    $conversations = $conversations->filter(fn($c) => $c['isGroup'] ?? false);
                } elseif ($this->activeFilter === 'contacts') {
                    $conversations = $conversations->filter(fn($c) => !($c['isGroup'] ?? false) && !($c['isNewsletter'] ?? false) && !($c['isLid'] ?? false));
                }

                $this->conversations = $conversations->values()->toArray();
                $this->stats['total_chats'] = count($this->conversations);
                $this->stats['unread'] = $conversations->sum(fn($c) => $c['unreadCount'] ?? 0);
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp loadConversations error: ' . $e->getMessage());
        }

        $this->isLoading = false;
    }

    public function selectConversation(string $peer, ?string $name = null)
    {
        $this->currentPeer = $peer;
        $this->currentPeerName = $name ?: $this->getConversationDisplayName(['peer' => $peer, 'name' => $name]);
        $this->currentPeerStatus = null;
        $this->showContactInfo = false;
        $this->loadMessages();
        
        $this->dispatch('chatSelected');
    }

    public function loadMessages()
    {
        if (!$this->currentPeer || !$this->whatsappApiKey) return;

        try {
            $response = Http::timeout(config('whatsapp.timeouts.send_message', 10))
                ->withHeaders($this->getApiHeaders())
                ->get($this->getApiUrl() . '/api/whatsapp/thread', [
                    'peer' => $this->currentPeer,
                ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->messages = collect($data['messages'] ?? [])
                    ->map(function ($msg) {
                        // Parse message content
                        $text = '';
                        $type = 'text';
                        $mediaUrl = null;
                        
                        try {
                            $parsed = is_string($msg['message'] ?? '') 
                                ? json_decode($msg['message'], true) 
                                : ($msg['message'] ?? []);
                            
                            if (is_array($parsed)) {
                                $text = $parsed['conversation'] 
                                    ?? $parsed['extendedTextMessage']['text'] 
                                    ?? '';
                                
                                if (isset($parsed['imageMessage'])) {
                                    $type = 'image';
                                    $text = $parsed['imageMessage']['caption'] ?? '';
                                    $mediaUrl = $parsed['imageMessage']['url'] ?? null;
                                } elseif (isset($parsed['documentMessage'])) {
                                    $type = 'document';
                                    $text = $parsed['documentMessage']['fileName'] ?? 'Documento';
                                } elseif (isset($parsed['audioMessage'])) {
                                    $type = 'audio';
                                    $text = 'Mensaje de voz';
                                } elseif (isset($parsed['videoMessage'])) {
                                    $type = 'video';
                                    $text = $parsed['videoMessage']['caption'] ?? 'Video';
                                } elseif (isset($parsed['stickerMessage'])) {
                                    $type = 'sticker';
                                    $text = '🎨 Sticker';
                                } elseif (isset($parsed['locationMessage'])) {
                                    $type = 'location';
                                    $text = '📍 Ubicación';
                                } elseif (isset($parsed['contactMessage'])) {
                                    $type = 'contact';
                                    $text = '👤 Contacto';
                                }
                            } else {
                                $text = (string) $parsed;
                            }
                        } catch (\Exception $e) {
                            $text = '[Mensaje]';
                        }

                        if (empty($text)) {
                            $text = '[Media]';
                        }

                        return [
                            'id' => $msg['id'] ?? null,
                            'from' => $msg['from'] ?? '',
                            'to' => $msg['to'] ?? '',
                            'text' => $text,
                            'type' => $type,
                            'mediaUrl' => $mediaUrl,
                            'status' => $msg['status'] ?? '',
                            'timestamp' => $msg['createdAt'] ?? $msg['messageTimestamp'] ?? now()->toISOString(),
                            'isOutgoing' => isset($msg['key']['fromMe']) ? $msg['key']['fromMe'] : (str_contains($msg['from'] ?? '', '@s.whatsapp.net') && ($msg['from'] ?? '') !== $this->currentPeer),
                        ];
                    })
                    ->toArray();
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp loadMessages error: ' . $e->getMessage());
        }
        
        $this->dispatch('scrollToBottom');
    }

    /**
     * Extract phone number digits from a JID (peer@s.whatsapp.net -> digits)
     */
    private function peerToPhone(string $peer): string
    {
        $phone = preg_replace('/@.*$/', '', $peer);
        return preg_replace('/\D/', '', $phone);
    }

    public function sendMessage()
    {
        if (empty(trim($this->messageText)) || !$this->currentPeer) return;
        if ($this->connectionStatus !== 'connected') {
            $this->dispatch('notify', type: 'error', message: 'WhatsApp no está conectado.');
            return;
        }

        $this->isSending = true;
        $text = trim($this->messageText);
        $this->messageText = '';

        try {
            // The API expects phone digits only (no JID suffix)
            $phone = $this->peerToPhone($this->currentPeer);

            $response = Http::timeout(config('whatsapp.timeouts.send_message', 10))
                ->withHeaders($this->getApiHeaders())
                ->post($this->getApiUrl() . '/api/whatsapp/send', [
                    'to' => $phone,
                    'message' => $text,
                    'type' => 'text',
                ]);

            if ($response->successful()) {
                $this->loadMessages();
                $this->dispatch('messageSent');
            } else {
                $this->messageText = $text;
                $errorData = $response->json();
                $errorMsg = $errorData['error'] ?? $errorData['details'][0]['msg'] ?? 'Error al enviar el mensaje.';
                Log::error('WhatsApp sendMessage API error', ['status' => $response->status(), 'body' => $errorData]);
                $this->dispatch('notify', type: 'error', message: $errorMsg);
            }
        } catch (\Exception $e) {
            $this->messageText = $text;
            Log::error('WhatsApp sendMessage error: ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Error de conexión al enviar.');
        }

        $this->isSending = false;
    }

    public function sendAttachment()
    {
        if (!$this->attachment || !$this->currentPeer) return;
        if ($this->connectionStatus !== 'connected') return;

        $this->isSending = true;

        try {
            $file = $this->attachment;
            $response = Http::timeout(30)
                ->withHeaders([
                    'X-API-Key' => $this->whatsappApiKey,
                    'X-Company-Id' => (string) $this->companyId,
                ])
                ->attach('document', file_get_contents($file->getRealPath()), $file->getClientOriginalName())
                ->post($this->getApiUrl() . '/api/whatsapp/send-document', [
                    'to' => $this->currentPeer,
                    'caption' => '',
                ]);

            if ($response->successful()) {
                $this->attachment = null;
                $this->loadMessages();
                $this->dispatch('notify', type: 'success', message: 'Archivo enviado.');
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp sendAttachment error: ' . $e->getMessage());
            $this->dispatch('notify', type: 'error', message: 'Error al enviar archivo.');
        }

        $this->isSending = false;
    }

    public function startNewChat()
    {
        if (empty($this->newChatPhone)) return;
        
        try {
            $service = WhatsAppService::forCompany($this->companyId);
            $formattedPhone = $service->formatPhone($this->newChatPhone);
        } catch (\Exception $e) {
            $formattedPhone = preg_replace('/\D+/', '', $this->newChatPhone);
        }

        $peer = $formattedPhone . '@s.whatsapp.net';
        $this->currentPeer = $peer;
        $this->currentPeerName = $this->newChatPhone;
        $this->showNewChatModal = false;
        
        // Send initial message if provided
        if (!empty($this->newChatMessage)) {
            $this->messageText = $this->newChatMessage;
            $this->sendMessage();
            $this->newChatMessage = '';
        }
        
        $this->newChatPhone = '';
        $this->loadMessages();
    }

    public function setFilter(string $filter)
    {
        $this->activeFilter = $filter;
        $this->loadConversations();
    }

    public function updatedSearchQuery()
    {
        $this->loadConversations();
    }

    public function toggleContactInfo()
    {
        $this->showContactInfo = !$this->showContactInfo;
    }

    public function closeChat()
    {
        $this->currentPeer = null;
        $this->currentPeerName = null;
        $this->messages = [];
        $this->showContactInfo = false;
    }

    public function refreshAll()
    {
        $this->checkConnection();
        $this->loadConversations();
        if ($this->currentPeer) {
            $this->loadMessages();
        }
        $this->loadStats();
        $this->dispatch('notify', type: 'success', message: 'Chat actualizado.');
    }

    public function loadStats()
    {
        $this->stats['sent_today'] = WhatsAppMessage::whereDate('created_at', today())
            ->where('direction', 'outbound')->count();
        $this->stats['received_today'] = WhatsAppMessage::whereDate('created_at', today())
            ->where('direction', 'inbound')->count();
    }

    public function handleIncomingMessage($data = null)
    {
        if ($data && isset($data['from']) && $data['from'] === $this->currentPeer) {
            $this->loadMessages();
        }
        $this->loadConversations();
    }

    // Helper methods
    public function formatTimestamp($timestamp): string
    {
        try {
            $date = Carbon::parse($timestamp);
            if ($date->isToday()) return $date->format('H:i');
            if ($date->isYesterday()) return 'Ayer';
            if ($date->isCurrentWeek()) return $date->isoFormat('ddd');
            return $date->format('d/m/Y');
        } catch (\Exception $e) {
            return '';
        }
    }

    public function getStatusIconProperty(): string
    {
        return match ($this->connectionStatus) {
            'connected' => 'ri-checkbox-circle-fill',
            'connecting' => 'ri-loader-4-line',
            'qr_ready' => 'ri-qr-code-line',
            'service_unavailable' => 'ri-wifi-off-line',
            'error' => 'ri-error-warning-fill',
            default => 'ri-close-circle-fill',
        };
    }

    public function getStatusColorProperty(): string
    {
        return match ($this->connectionStatus) {
            'connected' => 'success',
            'connecting', 'qr_ready' => 'warning',
            'service_unavailable' => 'secondary',
            default => 'danger',
        };
    }

    public function getStatusTextProperty(): string
    {
        return match ($this->connectionStatus) {
            'connected' => 'Conectado',
            'connecting' => 'Conectando...',
            'qr_ready' => 'Esperando QR',
            'service_unavailable' => 'Servicio no disponible',
            'error' => 'Error',
            default => 'Desconectado',
        };
    }

    protected function getPageTitle(): string
    {
        return 'WhatsApp Chat';
    }

    protected function getBreadcrumb(): array
    {
        return [
            'admin.dashboard' => 'Dashboard',
            'admin.whatsapp.dashboard' => 'WhatsApp',
            'admin.whatsapp.chat-module' => 'Chat',
        ];
    }

    public function render()
    {
        return $this->renderWithLayout('livewire.admin.whatsapp.whatsapp-chat-module', [], [
            'title' => 'WhatsApp Chat',
            'description' => 'Módulo de chat WhatsApp',
            'breadcrumb' => $this->getBreadcrumb(),
        ]);
    }
}