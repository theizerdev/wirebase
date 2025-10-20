<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Http\Request;
use App\Models\ActiveSession;
use Illuminate\Support\Facades\Session;

class TrackUserLogin
{
    protected $request;

    /**
     * Create the event listener.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        
        // Verificar si existe una sesión activa
        if (!Session::isStarted()) {
            return;
        }
        
        $sessionId = $this->request->session()->getId();
        $ipAddress = $this->request->ip();
        
        // Obtener información de geolocalización
        $locationData = $this->getLocationData($ipAddress);
        
        // Marcar cualquier sesión existente como no actual
        ActiveSession::where('user_id', $user->id)
            ->update(['is_current' => false]);
        
        // Verificar si ya existe un registro para esta sesión
        $activeSession = ActiveSession::where('user_id', $user->id)
            ->where('session_id', $sessionId)
            ->first();
        
        $sessionData = [
            'last_activity' => now(),
            'ip_address' => $ipAddress,
            'user_agent' => $this->request->userAgent(),
            'is_current' => true,
            'is_active' => true,
            'login_at' => now(),
            'location' => $locationData['location'] ?? null,
            'latitude' => $locationData['latitude'] ?? null,
            'longitude' => $locationData['longitude'] ?? null,
        ];
        
        if ($activeSession) {
            // Actualizar la sesión existente
            $activeSession->update($sessionData);
        } else {
            // Crear un nuevo registro de sesión
            $sessionData['user_id'] = $user->id;
            $sessionData['session_id'] = $sessionId;
            ActiveSession::create($sessionData);
        }
    }
    
    /**
     * Obtener información de geolocalización basada en la dirección IP
     */
    private function getLocationData($ipAddress)
    {
        // Datos por defecto
        $locationData = [
            'location' => null,
            'latitude' => null,
            'longitude' => null,
        ];
        
        // No intentar obtener ubicación para IPs locales
        if ($ipAddress === '127.0.0.1' || $ipAddress === '::1' || strpos($ipAddress, '192.168.') === 0) {
            $locationData['location'] = 'Local';
            return $locationData;
        }
        
        try {
            // Usar ip-api.com para obtener información de geolocalización (servicio gratuito)
            $url = "http://ip-api.com/json/{$ipAddress}";
            $response = file_get_contents($url);
            $data = json_decode($response, true);
            
            if ($data && $data['status'] === 'success') {
                $locationData['location'] = "{$data['city']}, {$data['regionName']}, {$data['country']}";
                $locationData['latitude'] = $data['lat'];
                $locationData['longitude'] = $data['lon'];
            }
        } catch (\Exception $e) {
            // En caso de error, simplemente devolver los datos por defecto
            \Log::warning("Error obteniendo geolocalización para IP {$ipAddress}: " . $e->getMessage());
        }
        
        return $locationData;
    }
}