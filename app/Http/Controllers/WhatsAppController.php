<?php

namespace App\Http\Controllers;

use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class WhatsAppController extends Controller
{
    private $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

    /**
     * Obtener estado de WhatsApp
     */
    public function status(): JsonResponse
    {
        $status = $this->whatsappService->getStatus();
        
        return response()->json([
            'success' => $status !== null,
            'data' => $status
        ]);
    }

    /**
     * Obtener código QR
     */
    public function qrCode(): JsonResponse
    {
        $qr = $this->whatsappService->getQRCode();
        
        return response()->json([
            'success' => $qr !== null,
            'data' => $qr
        ]);
    }

    /**
     * Enviar mensaje
     */
    public function sendMessage(Request $request): JsonResponse
    {
        $request->validate([
            'to' => 'required|string',
            'message' => 'required|string'
        ]);

        $result = $this->whatsappService->sendMessage(
            $request->to,
            $request->message
        );

        return response()->json([
            'success' => $result !== null && ($result['success'] ?? false),
            'data' => $result
        ]);
    }

    /**
     * Obtener mensajes
     */
    public function messages(Request $request): JsonResponse
    {
        $filters = $request->only(['page', 'limit', 'status', 'from', 'to']);
        $messages = $this->whatsappService->getMessages($filters);

        return response()->json([
            'success' => $messages !== null,
            'data' => $messages
        ]);
    }

    /**
     * Conectar WhatsApp
     */
    public function connect(): JsonResponse
    {
        $result = $this->whatsappService->connect();

        return response()->json([
            'success' => $result !== null,
            'data' => $result
        ]);
    }

    /**
     * Desconectar WhatsApp
     */
    public function disconnect(): JsonResponse
    {
        $result = $this->whatsappService->disconnect();

        return response()->json([
            'success' => $result !== null,
            'data' => $result
        ]);
    }

    /**
     * Webhook para recibir mensajes (desde Node.js)
     */
    public function webhook(Request $request): JsonResponse
    {
        // Procesar mensaje entrante desde la API de WhatsApp
        $data = $request->all();
        
        // Aquí puedes procesar el mensaje como necesites
        // Por ejemplo, guardarlo en tu base de datos Laravel
        
        return response()->json(['success' => true]);
    }
}