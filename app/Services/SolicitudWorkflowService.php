<?php

namespace App\Services;

use App\Models\SolicitudModificacionPastor;
use App\Models\AuditoriaSolicitud;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class SolicitudWorkflowService
{
    /**
     * Verificar y procesar escalamiento automático de solicitudes pendientes
     */
    public function procesarEscalamientoAutomatico()
    {
        $solicitudesPendientes = SolicitudModificacionPastor::where('estado', 'pendiente')
            ->get();
        
        foreach ($solicitudesPendientes as $solicitud) {
            $this->verificarYEscalar($solicitud);
        }
    }
    
    /**
     * Verificar si una solicitud necesita escalamiento
     */
    private function verificarYEscalar($solicitud)
    {
        $horasTranscurridas = $solicitud->created_at->diffInHours(now());
        
        // Después de 24 horas - Escalar al Supervisor Regional
        if ($horasTranscurridas >= 24 && $horasTranscurridas < 48) {
            $this->escalarASupervisorRegional($solicitud);
        }
        
        // Después de 48 horas - Escalar a Oficina Nacional y marcar como URGENTE
        if ($horasTranscurridas >= 48 && $horasTranscurridas < 72) {
            $this->escalarAOficinaNacional($solicitud);
        }
        
        // Después de 72 horas - Escalar a Supervisor Nacional (CRÍTICO)
        if ($horasTranscurridas >= 72) {
            $this->escalarASupervisorNacional($solicitud);
        }
    }
    
    /**
     * Escalar al Supervisor Regional
     */
    private function escalarASupervisorRegional($solicitud)
    {
        // Verificar si ya se escaló a este nivel
        $yaEscalado = AuditoriaSolicitud::where('solicitud_id', $solicitud->id)
            ->where('accion', 'escalada')
            ->whereJsonContains('metadata->nivel', 'supervisor_regional')
            ->exists();
        
        if (!$yaEscalado) {
            // TODO: Implementar lógica para encontrar supervisor regional
            // Por ahora solo registramos en auditoría
            
            AuditoriaSolicitud::create([
                'solicitud_id' => $solicitud->id,
                'accion' => 'escalada',
                'usuario_id' => null,
                'usuario_nombre' => 'Sistema Automático',
                'usuario_rol' => 'scheduler',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Laravel Scheduler',
                'metadata' => [
                    'nivel' => 'supervisor_regional',
                    'horas_transcurridas' => $solicitud->created_at->diffInHours(now()),
                    'motivo' => 'Sin respuesta después de 24 horas'
                ]
            ]);
            
            Log::info("Solicitud #{$solicitud->id} escalada a Supervisor Regional");
        }
    }
    
    /**
     * Escalar a Oficina Nacional
     */
    private function escalarAOficinaNacional($solicitud)
    {
        $yaEscalado = AuditoriaSolicitud::where('solicitud_id', $solicitud->id)
            ->where('accion', 'escalada')
            ->whereJsonContains('metadata->nivel', 'oficina_nacional')
            ->exists();
        
        if (!$yaEscalado) {
            AuditoriaSolicitud::create([
                'solicitud_id' => $solicitud->id,
                'accion' => 'escalada',
                'usuario_id' => null,
                'usuario_nombre' => 'Sistema Automático',
                'usuario_rol' => 'scheduler',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Laravel Scheduler',
                'metadata' => [
                    'nivel' => 'oficina_nacional',
                    'urgente' => true,
                    'horas_transcurridas' => $solicitud->created_at->diffInHours(now()),
                    'motivo' => 'Sin respuesta después de 48 horas - MARCADO COMO URGENTE'
                ]
            ]);
            
            Log::warning("Solicitud #{$solicitud->id} escalada a Oficina Nacional (URGENTE)");
        }
    }
    
    /**
     * Escalar a Supervisor Nacional (CRÍTICO)
     */
    private function escalarASupervisorNacional($solicitud)
    {
        $yaEscalado = AuditoriaSolicitud::where('solicitud_id', $solicitud->id)
            ->where('accion', 'escalada')
            ->whereJsonContains('metadata->nivel', 'supervisor_nacional')
            ->exists();
        
        if (!$yaEscalado) {
            AuditoriaSolicitud::create([
                'solicitud_id' => $solicitud->id,
                'accion' => 'escalada',
                'usuario_id' => null,
                'usuario_nombre' => 'Sistema Automático',
                'usuario_rol' => 'scheduler',
                'ip_address' => '127.0.0.1',
                'user_agent' => 'Laravel Scheduler',
                'metadata' => [
                    'nivel' => 'supervisor_nacional',
                    'critico' => true,
                    'horas_transcurridas' => $solicitud->created_at->diffInHours(now()),
                    'motivo' => 'Sin respuesta después de 72 horas - ALERTA CRÍTICA'
                ]
            ]);
            
            Log::error("Solicitud #{$solicitud->id} escalada a Supervisor Nacional (CRÍTICO)");
        }
    }
    
    /**
     * Enviar notificación WhatsApp al pastor cuando su solicitud es procesada
     */
    public function notificarPastor($solicitud, string $tipo)
    {
        try {
            if (!$solicitud->pastor || !$solicitud->pastor->telefono_tlf || $solicitud->pastor->telefono_tlf === '0') {
                Log::warning("Pastor {$solicitud->pastor_id} no tiene teléfono válido para notificación");
                return false;
            }
            
            $whatsappService = app(WhatsAppService::class, ['empresa' => $solicitud->pastor->empresa_id]);
            
            if (!$whatsappService->isConfigured()) {
                Log::warning("WhatsApp no configurado para empresa {$solicitud->pastor->empresa_id}");
                return false;
            }
            
            $telefono = $this->formatearTelefono($solicitud->pastor->telefono_tlf);
            
            $mensaje = match($tipo) {
                'aprobada' => $this->generarMensajeAprobacion($solicitud),
                'rechazada' => $this->generarMensajeRechazo($solicitud),
                default => null
            };
            
            if ($mensaje) {
                $resultado = $whatsappService->sendMessage($telefono, $mensaje);
                
                // Registrar en auditoría
                AuditoriaSolicitud::create([
                    'solicitud_id' => $solicitud->id,
                    'accion' => 'notificacion_enviada',
                    'usuario_id' => null,
                    'usuario_nombre' => 'Sistema Automático',
                    'usuario_rol' => 'notification',
                    'ip_address' => '127.0.0.1',
                    'user_agent' => 'WhatsApp Service',
                    'metadata' => [
                        'tipo' => $tipo,
                        'telefono' => $telefono,
                        'resultado' => $resultado ? 'enviado' : 'fallido'
                    ]
                ]);
                
                return $resultado;
            }
            
            return false;
        } catch (\Exception $e) {
            Log::error("Error al enviar notificación WhatsApp: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Generar mensaje de aprobación
     */
    private function generarMensajeAprobacion($solicitud)
    {
        $pastor = $solicitud->pastor;
        
        return " ¡SOLICITUD APROBADA!\n\n" .
               "Hola *{$pastor->nombre_completo}*,\n\n" .
               "Nos complace informarle que su solicitud de actualización de datos ha sido *APROBADA* exitosamente.\n\n" .
               "━━━━━━━━━━━━━━━━━━━━\n" .
               "📋 *PRÓXIMOS PASOS:*\n" .
               "━━━━━━━━━━━━━━━━━━━━\n\n" .
               "1️ Ingrese al sistema SAPRCOE\n" .
               "2️ Actualice sus datos personales y eclesiásticos\n" .
               "3️⃣ Configure sus preguntas de seguridad\n" .
               "4️⃣ ¡Listo! Su información estará actualizada\n\n" .
               "━━━━━━━━━━━━━━━━━━━━\n\n" .
               " *IMPORTANTE:*\n" .
               "Mantener su información actualizada es fundamental para el registro y control de obreros.\n\n" .
               "Si tiene alguna duda o necesita ayuda, no dude en contactar a su Presbítero.\n\n" .
               "━━━━━━━━━━━━━━━━━━━━\n" .
               "🙏 *Equipo SAPRCOE*\n" .
               "Movimiento Misionero Mundial - Venezuela\n" .
               "Sistema Automatizado para el Registro y Control de Obreros y Extensiones";
    }
    
    /**
     * Generar mensaje de rechazo
     */
    private function generarMensajeRechazo($solicitud)
    {
        $pastor = $solicitud->pastor;
        $motivo = $solicitud->motivo_rechazo ?? 'Sin motivo especificado';
        
        return "❌ *SOLICITUD RECHAZADA*\n\n" .
               "Hola *{$pastor->nombre_completo}*,\n\n" .
               "Lamentamos informarle que su solicitud de actualización de datos ha sido *RECHAZADA*.\n\n" .
               "━━━━━━━━━━━━━━━━━━━━\n" .
               "📄 *MOTIVO DEL RECHAZO:*\n" .
               "━━━━━━━━━━━━━━━━━━━━\n\n" .
               "{$motivo}\n\n" .
               "━━━━━━━━━━━━━━━━━━━━\n\n" .
               " *¿QUÉ DEBE HACER?*\n\n" .
               "1️ Revise el motivo indicado arriba\n" .
               "2️ Corrija la información requerida\n" .
               "3️⃣ Vuelva a enviar su solicitud\n" .
               "4️⃣ Si tiene dudas, contacte a su Presbítero\n\n" .
               "━━━━━━━━━━━━━━━━━━━━\n\n" .
               " *NOTA:*\n" .
               "Puede realizar una nueva solicitud en cualquier momento una vez que haya corregido los problemas indicados.\n\n" .
               "━━━━━━━━━━━━━━━━━━━━\n" .
               "🙏 *Equipo SAPRCOE*\n" .
               "Movimiento Misionero Mundial - Venezuela\n" .
               "Sistema Automatizado para el Registro y Control de Obreros y Extensiones";
    }
    
    /**
     * Formatear número de teléfono para WhatsApp
     */
    private function formatearTelefono(string $telefono): string
    {
        // Remover caracteres no numéricos
        $cleanNumber = preg_replace('/[^0-9]/', '', $telefono);
        
        // Si comienza con 04, reemplazar por 58
        if (substr($cleanNumber, 0, 2) === '04') {
            $cleanNumber = '58' . substr($cleanNumber, 1);
        }
        
        return $cleanNumber;
    }
}
