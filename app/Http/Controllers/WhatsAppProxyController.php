<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WhatsAppProxyController extends Controller
{
    protected $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Enviar mensaje de WhatsApp con autenticación JWT
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string',
        ]);

        $phone = $request->input('phone');
        $message = $request->input('message');

        // El usuario ya está autenticado por JWT
        $user = auth()->user();

        // Intentar enviar el mensaje
        $result = $this->whatsappService->sendMessage($phone, $message);

        if ($result['success']) {
            $response = [
                'success' => true,
                'message' => 'Mensaje enviado exitosamente',
                'data' => $result,
                'sent_by' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                ]
            ];

            // Si es un envío simulado, agregar nota
            if (isset($result['simulated']) && $result['simulated']) {
                $response['note'] = 'Este fue un envío simulado. La API externa no está disponible.';
            }

            return response()->json($response);
        }

        return response()->json([
            'success' => false,
            'error' => $result['error'] ?? 'Error al enviar el mensaje'
        ], 400);
    }

    /**
     * Obtener estado de WhatsApp con autenticación JWT
     */
    public function getStatus()
    {
        $result = $this->whatsappService->getStatus();
        
        return response()->json($result);
    }

    /**
     * Obtener código QR
     */
    public function getQR()
    {
        $result = $this->whatsappService->getQR();
        
        return response()->json($result);
    }

    /**
     * Obtener contactos
     */
    public function getContacts()
    {
        $result = $this->whatsappService->getContacts();
        
        return response()->json($result);
    }
}