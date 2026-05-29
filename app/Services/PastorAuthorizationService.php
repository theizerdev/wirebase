<?php

namespace App\Services;

use App\Models\SolicitudModificacionPastor;
use App\Models\Pastor;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;

class PastorAuthorizationService
{
    /**
     * Crear una solicitud de modificación para un pastor
     * 
     * @param Pastor $pastor
     * @param string $telefonoNuevo
     * @return SolicitudModificacionPastor
     */
    public function crearSolicitud(Pastor $pastor, string $telefonoNuevo): SolicitudModificacionPastor
    {
        // Verificar que el pastor tenga zona asignada
        if (empty($pastor->zona)) {
            throw new \Exception('El pastor no tiene una zona asignada.');
        }

        // Buscar presbítero en la misma zona
        $presbitero = $this->buscarPresbiteroPorZona($pastor->zona, $pastor->empresa_id);

        if (!$presbitero) {
            throw new \Exception('No se encontró un Presbítero en la zona: ' . $pastor->zona);
        }

        // Verificar que el presbítero tenga teléfono para WhatsApp
        if (empty($presbitero->phone)) {
            throw new \Exception('El Presbítero no tiene número de teléfono registrado.');
        }

        // Crear la solicitud
        $solicitud = SolicitudModificacionPastor::create([
            'pastor_id' => $pastor->id,
            'presbitero_user_id' => $presbitero->id,
            'token' => SolicitudModificacionPastor::generarToken(),
            'telefono_nuevo' => $telefonoNuevo,
            'estado' => 'pendiente',
            'token_expires_at' => now()->addHours(48), // Expira en 48 horas
        ]);

        // Enviar notificación WhatsApp al presbítero
        $this->enviarNotificacionWhatsApp($solicitud, $presbitero);

        return $solicitud;
    }

    /**
     * Buscar un usuario con rol de Presbítero en la zona especificada
     * 
     * @param string $zona
     * @param int $empresaId
     * @return User|null
     */
    private function buscarPresbiteroPorZona(string $zona, int $empresaId): ?User
    {
        $zonaSinEspacios = str_replace(' ', '', $zona);
        return User::where('empresa_id', $empresaId)
            ->where('zona', $zonaSinEspacios)
            ->role('Presbitero')
            ->where('status', true)
            ->first();
    }

    /**
     * Enviar notificación WhatsApp al presbítero
     * 
     * @param SolicitudModificacionPastor $solicitud
     * @param User $presbitero
     */
    private function enviarNotificacionWhatsApp(SolicitudModificacionPastor $solicitud, User $presbitero): void
    {
        try {
            $pastor = $solicitud->pastor;
            $linkAutorizacion = route('pastor.autorizar', ['token' => $solicitud->token]);

            // Mensaje profesional y simple para evitar detección de spam
            $mensaje = "Hola {$presbitero->name},\n\n" .
                       "Se ha recibido una solicitud de actualización de datos del pastor {$pastor->nombre_completo}.\n\n" .
                       "Cédula: {$pastor->documento}\n" .
                       "Zona: {$pastor->zona}\n" .
                       "Distrito: {$pastor->zona}\n" .
                       "Grado ministerial: {$pastor->nivel_ministerial}\n" .
                      "Por favor revise y autorice la solicitud en el siguiente enlace:\n" .
                       "{$linkAutorizacion}\n\n" .
                       "El enlace estará disponible por 48 horas.\n\n" .
                       "Saludos,\n" .
                       "Equipo de SAPRCOE";

            // Enviar vía WhatsApp usando el sistema existente con la empresa del pastor
            $this->enviarWhatsApp($presbitero->phone, $mensaje, $pastor->empresa_id);

            Log::info('Notificación WhatsApp enviada al presbítero', [
                'presbitero_id' => $presbitero->id,
                'solicitud_id' => $solicitud->id,
            ]);

        } catch (\Exception $e) {
            Log::error('Error enviando notificación WhatsApp', [
                'solicitud_id' => $solicitud->id,
                'error' => $e->getMessage(),
            ]);
            
            // No lanzar excepción para no bloquear el flujo
            // La solicitud se crea aunque falle el WhatsApp
            Log::warning('Se creó la solicitud pero falló la notificación WhatsApp');
        }
    }

    /**
     * Enviar mensaje WhatsApp (usando el sistema existente del proyecto)
     * 
     * @param string $telefono
     * @param string $mensaje
     * @param int|null $empresaId
     */
    private function enviarWhatsApp(string $telefono, string $mensaje, ?int $empresaId = null): void
    {
        // Formatear número de teléfono
        $telefonoFormateado = $this->formatearTelefono($telefono);

        // Usar el servicio de WhatsApp existente del proyecto con la empresa correcta
        if ($empresaId) {
            $whatsappService = app(\App\Services\WhatsAppService::class, ['empresa' => $empresaId]);
        } else {
            $whatsappService = app(\App\Services\WhatsAppService::class);
        }
        
        if (method_exists($whatsappService, 'sendMessage')) {
            $whatsappService->sendMessage($telefonoFormateado, $mensaje);
        } else {
            // Fallback: intentar con Twilio directamente si está configurado
            $this->enviarViaTwilio($telefonoFormateado, $mensaje);
        }
    }

    /**
     * Formatear número de teléfono para WhatsApp
     * 
     * @param string $telefono
     * @return string
     */
    private function formatearTelefono(string $telefono): string
    {
        // Eliminar espacios, guiones y paréntesis
        $telefono = preg_replace('/[\s\-\(\)]/', '', $telefono);
        
        // Si comienza con 0, reemplazar con código de país (Venezuela: 58)
        if (str_starts_with($telefono, '0')) {
            $telefono = '58' . substr($telefono, 1);
        }
        
        // Si no tiene código de país, agregar 58
        if (!str_starts_with($telefono, '58')) {
            $telefono = '58' . $telefono;
        }
        
        return $telefono;
    }

    /**
     * Enviar mensaje vía Twilio (fallback)
     * 
     * @param string $telefono
     * @param string $mensaje
     */
    private function enviarViaTwilio(string $telefono, string $mensaje): void
    {
        if (!class_exists('Twilio\Rest\Client')) {
            throw new \Exception('La librería de Twilio no está instalada.');
        }

        $accountSid = config('services.twilio.sid');
        $authToken = config('services.twilio.token');
        $fromNumber = config('services.twilio.from');

        if (!$accountSid || !$authToken || !$fromNumber) {
            throw new \Exception('Configuración de Twilio incompleta.');
        }

        $twilio = new \Twilio\Rest\Client($accountSid, $authToken);
        
        $twilio->messages->create(
            "whatsapp:+" . $telefono,
            [
                'from' => "whatsapp:+" . $fromNumber,
                'body' => $mensaje,
            ]
        );
    }

    /**
     * Aprobar una solicitud
     * 
     * @param SolicitudModificacionPastor $solicitud
     * @param User $aprobador
     * @param string $ip
     * @return bool
     */
    public function aprobarSolicitud(SolicitudModificacionPastor $solicitud, User $aprobador, string $ip): bool
    {
        if (!$solicitud->estaPendiente()) {
            throw new \Exception('La solicitud ya fue procesada.');
        }

        if ($solicitud->estaExpirada()) {
            throw new \Exception('La solicitud ha expirado.');
        }

        // Verificar que el aprobador sea el presbítero correcto
        if ($solicitud->presbitero_user_id !== $aprobador->id) {
            throw new \Exception('No tienes permiso para aprobar esta solicitud.');
        }

        $solicitud->update([
            'estado' => 'aprobado',
            'aprobado_por' => $aprobador->id,
            'aprobado_en' => now(),
            'ip_aprobacion' => $ip,
        ]);

        // Obtener el pastor para notificación y logging
        $pastor = $solicitud->pastor;

        // NOTA: No actualizamos automáticamente los datos del pastor
        // El pastor debe ingresar/actualizar sus datos manualmente después de configurar su seguridad

        Log::info('Solicitud aprobada', [
            'solicitud_id' => $solicitud->id,
            'pastor_id' => $pastor->id,
            'aprobador_id' => $aprobador->id,
        ]);

        // Enviar notificación WhatsApp al pastor
        $this->enviarConfirmacionAlPastor($solicitud, $aprobador);

        return true;
    }

    /**
     * Rechazar una solicitud
     * 
     * @param SolicitudModificacionPastor $solicitud
     * @param User $rechazador
     * @param string $motivo
     * @return bool
     */
    public function rechazarSolicitud(SolicitudModificacionPastor $solicitud, User $rechazador, string $motivo = ''): bool
    {
        if (!$solicitud->estaPendiente()) {
            throw new \Exception('La solicitud ya fue procesada.');
        }

        // Verificar que el rechazador sea el presbítero correcto
        if ($solicitud->presbitero_user_id !== $rechazador->id) {
            throw new \Exception('No tienes permiso para rechazar esta solicitud.');
        }

        $solicitud->update([
            'estado' => 'rechazado',
            'aprobado_por' => $rechazador->id,
            'aprobado_en' => now(),
            'motivo_rechazo' => $motivo,
        ]);

        Log::info('Solicitud rechazada', [
            'solicitud_id' => $solicitud->id,
            'rechazador_id' => $rechazador->id,
            'motivo' => $motivo,
        ]);

        return true;
    }

    /**
     * Marcar solicitud como completada (después de configurar preguntas de seguridad)
     * 
     * @param SolicitudModificacionPastor $solicitud
     * @return bool
     */
    public function completarSolicitud(SolicitudModificacionPastor $solicitud): bool
    {
        if (!$solicitud->estaAprobada()) {
            throw new \Exception('La solicitud debe estar aprobada primero.');
        }

        $solicitud->update([
            'estado' => 'completado',
        ]);

        Log::info('Solicitud completada', [
            'solicitud_id' => $solicitud->id,
        ]);

        return true;
    }

    /**
     * Enviar confirmación al pastor después de aprobación
     * 
     * @param SolicitudModificacionPastor $solicitud
     * @param User $aprobador
     */
    private function enviarConfirmacionAlPastor(SolicitudModificacionPastor $solicitud, User $aprobador): void
    {
        try {
            $pastor = $solicitud->pastor;
            
            if (empty($pastor->telefono_tlf)) {
                Log::warning('Pastor no tiene teléfono para notificación', [
                    'pastor_id' => $pastor->id,
                ]);
                return;
            }

            $linkConfiguracion = route('pastor.configurar-seguridad', $pastor->id);
            
            // Mensaje conversacional para evitar detección de spam
            $mensaje = "Hola {$pastor->nombre_completo}.\n" .
                       "Su solicitud fue aprobada por el Presbítero {$aprobador->name}.\n" .
                       "Ahora puede ingresar o actualizar sus datos personales y eclesiásticos.\n" .
                       "Configure también sus preguntas de seguridad cuando pueda. Gracias.";

            // Formatear teléfono
            $telefono = $this->formatearTelefono($pastor->telefono_tlf);

            // Usar servicio WhatsApp existente con la empresa del pastor
            $whatsappService = app(\App\Services\WhatsAppService::class, ['empresa' => $pastor->empresa_id]);
            
            if (method_exists($whatsappService, 'sendMessage')) {
                $whatsappService->sendMessage($telefono, $mensaje);
                
                Log::info('Notificación de aprobación enviada al pastor', [
                    'pastor_id' => $pastor->id,
                    'solicitud_id' => $solicitud->id,
                ]);
            }

        } catch (\Exception $e) {
            Log::error('Error enviando confirmación al pastor', [
                'pastor_id' => $solicitud->pastor->id,
                'solicitud_id' => $solicitud->id,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Obtener solicitud por token
     * 
     * @param string $token
     * @return SolicitudModificacionPastor|null
     */
    public function obtenerSolicitudPorToken(string $token): ?SolicitudModificacionPastor
    {
        return SolicitudModificacionPastor::with(['pastor', 'presbiteroUser'])
            ->where('token', $token)
            ->noExpiradas()
            ->first();
    }
}
