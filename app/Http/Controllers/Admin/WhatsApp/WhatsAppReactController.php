<?php

namespace App\Http\Controllers\Admin\WhatsApp;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Auth;

class WhatsAppReactController extends Controller
{
    private function getApiHeaders(): array
    {
        $empresa = Auth::user()->empresa ?? null;
        
        return [
            'X-API-Key' => $empresa->whatsapp_api_key ?? '',
            'X-Company-Id' => (string) ($empresa->id ?? 1),
            'Content-Type' => 'application/json',
        ];
    }

    private function getApiUrl(): string
    {
        return rtrim(config('whatsapp.api_url', 'http://localhost:3001'), '/');
    }

    public function getStatus(Request $request)
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders($this->getApiHeaders())
                ->get($this->getApiUrl() . '/api/whatsapp/status');

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'connectionState' => 'error',
                'error' => 'Error al obtener estado de WhatsApp'
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'connectionState' => 'service_unavailable',
                'error' => 'Servicio de WhatsApp no disponible'
            ], 503);
        }
    }

    public function connect(Request $request)
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders($this->getApiHeaders())
                ->post($this->getApiUrl() . '/api/whatsapp/connect', []);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            $errorData = $response->json();
            return response()->json([
                'error' => $errorData['error'] ?? 'Error al iniciar conexión',
                'details' => $errorData['details'] ?? []
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Servicio de WhatsApp no disponible'
            ], 503);
        }
    }

    public function disconnect(Request $request)
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders($this->getApiHeaders())
                ->delete($this->getApiUrl() . '/api/whatsapp/disconnect');

            if ($response->successful()) {
                return response()->json($response->json());
            }

            $errorData = $response->json();
            return response()->json([
                'error' => $errorData['error'] ?? 'Error al desconectar',
                'details' => $errorData['details'] ?? []
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Servicio de WhatsApp no disponible'
            ], 503);
        }
    }

    public function getQr(Request $request)
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders($this->getApiHeaders())
                ->get($this->getApiUrl() . '/api/whatsapp/qr');

            if ($response->successful()) {
                return response()->json($response->json());
            }

            $errorData = $response->json();
            return response()->json([
                'error' => $errorData['error'] ?? 'QR no disponible',
                'details' => $errorData['details'] ?? [],
                'connectionState' => $errorData['connectionState'] ?? null,
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Servicio de WhatsApp no disponible'
            ], 503);
        }
    }

    public function getConversations(Request $request)
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders($this->getApiHeaders())
                ->get($this->getApiUrl() . '/api/whatsapp/conversations');

            if ($response->successful()) {
                $data = $response->json();
                
                $conversations = collect($data['conversations'] ?? [])
                    ->map(function ($conv) {
                        $conv['displayName'] = $this->formatDisplayName($conv);
                        $conv['phoneInfo'] = $this->formatPhoneNumber($conv['peer'] ?? '');
                        $conv['isGroup'] = str_contains($conv['peer'] ?? '', '@g.us');
                        $conv['isNewsletter'] = str_contains($conv['peer'] ?? '', '@newsletter');
                        $conv['isLid'] = str_contains($conv['peer'] ?? '', '@lid');
                        
                        if (isset($conv['lastMessage']) && strlen($conv['lastMessage']) > 60) {
                            $conv['lastMessage'] = substr($conv['lastMessage'], 0, 60) . '...';
                        }
                        
                        return $conv;
                    });
                
                return response()->json([
                    'conversations' => $conversations->values()
                ]);
            }

            return response()->json([
                'conversations' => [],
                'error' => 'Error al obtener conversaciones'
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'conversations' => [],
                'error' => 'Error de conexión'
            ], 500);
        }
    }

    public function getThread(Request $request)
    {
        $request->validate([
            'peer' => 'required|string'
        ]);

        try {
            $response = Http::timeout(10)
                ->withHeaders($this->getApiHeaders())
                ->get($this->getApiUrl() . '/api/whatsapp/thread', [
                    'peer' => $request->peer
                ]);

            if ($response->successful()) {
                $data = $response->json();
                
                $messages = collect($data['messages'] ?? [])
                    ->map(function ($msg) {
                        $msg['text'] = $this->extractMessageText($msg);
                        $msg['type'] = $this->getMessageType($msg);
                        $msg['isOutgoing'] = $this->isOutgoingMessage($msg);
                        return $msg;
                    });
                
                return response()->json([
                    'messages' => $messages
                ]);
            }

            return response()->json([
                'messages' => [],
                'error' => 'Error al obtener mensajes'
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'messages' => [],
                'error' => 'Error de conexión'
            ], 500);
        }
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'to' => 'required|string',
            'message' => 'required|string|min:2|max:4096',
            'type' => 'nullable|string|in:text,image,document,audio'
        ]);

        try {
            $phoneInfo = $this->formatPhoneNumber($request->to);
            $to = $phoneInfo['clean'] ?: $request->to;
            if (!preg_match('/^\d{10,15}$/', $to)) {
                return response()->json([
                    'error' => 'Número inválido',
                    'details' => ['to' => $request->to]
                ], 422);
            }

            $response = Http::timeout(10)
                ->withHeaders($this->getApiHeaders())
                ->post($this->getApiUrl() . '/api/whatsapp/send', [
                    'to' => $to,
                    'message' => $request->message,
                    'type' => $request->input('type', 'text')
                ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }

            $errorData = $response->json();
            return response()->json([
                'error' => $errorData['error'] ?? 'Error al enviar mensaje',
                'details' => $errorData['details'] ?? []
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Error de conexión al enviar mensaje'
            ], 500);
        }
    }

    public function getContacts(Request $request)
    {
        try {
            $response = Http::timeout(10)
                ->withHeaders($this->getApiHeaders())
                ->get($this->getApiUrl() . '/api/whatsapp/conversations');

            if ($response->successful()) {
                $data = $response->json();
                $contacts = collect($data['conversations'] ?? [])
                    ->map(function ($conv) {
                        $peer = $conv['peer'] ?? '';
                        $phoneInfo = $this->formatPhoneNumber($peer);
                        $displayName = $this->formatDisplayName($conv);

                        return [
                            'id' => $peer,
                            'peer' => $peer,
                            'number' => $phoneInfo['clean'] ?: null,
                            'name' => $displayName,
                            'displayName' => $displayName,
                            'isGroup' => $phoneInfo['is_group'],
                            'isNewsletter' => $phoneInfo['is_newsletter'],
                            'isLid' => $phoneInfo['is_lid'],
                            'unreadCount' => (int) ($conv['unreadCount'] ?? 0),
                            'lastTime' => $conv['createdAt'] ?? null,
                        ];
                    })
                    ->filter(function ($c) {
                        if ($c['isGroup'] || $c['isNewsletter'] || $c['isLid']) return false;
                        return is_string($c['number']) && preg_match('/^\d{10,15}$/', $c['number']);
                    })
                    ->unique('number')
                    ->values();

                return response()->json(['contacts' => $contacts]);
            }

            return response()->json([
                'contacts' => [],
                'error' => 'Error al obtener contactos'
            ], $response->status());
        } catch (\Exception $e) {
            return response()->json([
                'contacts' => [],
                'error' => 'Error de conexión'
            ], 500);
        }
    }

    public function getStats(Request $request)
    {
        try {
            $response = Http::timeout(5)
                ->withHeaders($this->getApiHeaders())
                ->get($this->getApiUrl() . '/api/whatsapp/stats');

            if ($response->successful()) {
                return response()->json($response->json());
            }

            return response()->json([
                'stats' => [
                    'sent' => 0,
                    'received' => 0,
                    'contacts' => 0,
                    'deliveryRate' => '0%'
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'stats' => [
                    'sent' => 0,
                    'received' => 0,
                    'contacts' => 0,
                    'deliveryRate' => '0%'
                ]
            ]);
        }
    }

    private function formatPhoneNumber(string $peer): array
    {
        $phone = preg_replace('/@.*$/', '', $peer);

        $parts = explode(':', $phone);
        $baseNumber = $parts[0] ?? $phone;
        $deviceId = $parts[1] ?? null;

        $cleanNumber = preg_replace('/\D/', '', $baseNumber);

        $displayName = $cleanNumber;
        $formattedNumber = $cleanNumber;

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

    private function formatDisplayName(array $conv): string
    {
        $peer = $conv['peer'] ?? '';
        $name = $conv['name'] ?? '';
        
        if (!empty($name) && $name !== $peer) {
            return $name;
        }

        $formatted = $this->formatPhoneNumber($peer);

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

    private function extractMessageText(array $msg): string
    {
        try {
            $parsed = is_string($msg['message'] ?? '') 
                ? json_decode($msg['message'], true) 
                : ($msg['message'] ?? []);
            
            if (is_array($parsed)) {
                $text = $parsed['conversation'] 
                    ?? $parsed['extendedTextMessage']['text'] 
                    ?? '';
                
                if (isset($parsed['imageMessage'])) {
                    $text = $parsed['imageMessage']['caption'] ?? '[Imagen]';
                } elseif (isset($parsed['documentMessage'])) {
                    $text = $parsed['documentMessage']['fileName'] ?? '[Documento]';
                } elseif (isset($parsed['audioMessage'])) {
                    $text = '[Mensaje de voz]';
                } elseif (isset($parsed['videoMessage'])) {
                    $text = $parsed['videoMessage']['caption'] ?? '[Video]';
                } elseif (isset($parsed['stickerMessage'])) {
                    $text = '[Sticker]';
                } elseif (isset($parsed['locationMessage'])) {
                    $text = '[Ubicación]';
                } elseif (isset($parsed['contactMessage'])) {
                    $text = '[Contacto]';
                }
            } else {
                $text = (string) $parsed;
            }
            
            return empty($text) ? '[Media]' : $text;
        } catch (\Exception $e) {
            return '[Mensaje]';
        }
    }

    private function getMessageType(array $msg): string
    {
        try {
            $parsed = is_string($msg['message'] ?? '') 
                ? json_decode($msg['message'], true) 
                : ($msg['message'] ?? []);
            
            if (is_array($parsed)) {
                if (isset($parsed['imageMessage'])) return 'image';
                if (isset($parsed['documentMessage'])) return 'document';
                if (isset($parsed['audioMessage'])) return 'audio';
                if (isset($parsed['videoMessage'])) return 'video';
                if (isset($parsed['stickerMessage'])) return 'sticker';
                if (isset($parsed['locationMessage'])) return 'location';
                if (isset($parsed['contactMessage'])) return 'contact';
            }
            
            return 'text';
        } catch (\Exception $e) {
            return 'text';
        }
    }

    private function isOutgoingMessage(array $msg): bool
    {
        return isset($msg['key']['fromMe']) ? $msg['key']['fromMe'] : false;
    }
}
