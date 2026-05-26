<?php

namespace App\Livewire\Public\Pastores;

use Livewire\Component;
use App\Models\Pastor;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class Busqueda extends Component
{
    public $cedula = '';
    public $pastores = [];
    public $searched = false;
    
    // Variables para la validación OTP
    public $showOtpModal = false;
    public $selectedPastorId = null;
    public $phoneNumber = '';
    public $otpCode = '';
    public $otpSent = false;
    public $otpVerified = false;
    public $verificationError = '';
    public $pastorData = null; // Variable para almacenar los datos del pastor
    
    // Variables para solicitudes de autorización
    public $showSolicitudModal = false;
    public $solicitudPendiente = null;
    public $modoSolicitud = false; // true si hay solicitud pendiente
    
    // Variables para solicitar cambio de teléfono
    public $showPhoneRequestModal = false;
    public $nuevoTelefono = '';
    public $phoneRequestError = '';
    public $phoneRequestSuccess = '';

    public function updatedCedula()
    {
        $this->searched = true;

        if (strlen($this->cedula) >= 6 || strlen($this->cedula) >= 8) {
            $this->pastores = Pastor::where('documento', 'like', '%' . $this->cedula . '%')
                ->where('status', 1)
                ->take(5)
                ->get();
        } else {
            $this->pastores = [];
        }
    }

    public function buscar()
    {
        $this->searched = true;

        if (!empty($this->cedula)) {
            $this->pastores = Pastor::where('documento', 'like', '%' . $this->cedula . '%')
                ->where('status', 1)
                ->take(10)
                ->get();
        } else {
            $this->pastores = [];
        }
    }

    public function seleccionarPastor($id)
    {
        Log::info('seleccionarPastor llamado con ID:', ['id' => $id]);
        
        $pastor = Pastor::find($id);
        
        if (!$pastor) {
            Log::warning('Pastor no encontrado:', ['id' => $id]);
            session()->flash('error', 'Pastor no encontrado.');
            return;
        }
        
        Log::info('Pastor encontrado:', [
            'id' => $pastor->id,
            'nombre' => $pastor->nombres,
            'telefono' => $pastor->telefono_tlf,
        ]);
        
        // Guardar el ID del pastor seleccionado
        $this->selectedPastorId = $id;
        $this->pastorData = $pastor; // Almacenar los datos del pastor
        
        // VERIFICAR SI HAY SOLICITUD PENDIENTE
        $solicitudPendiente = $pastor->solicitudesModificacion()
            ->pendientes()
            ->noExpiradas()
            ->first();
        
        if ($solicitudPendiente) {
            // Hay una solicitud pendiente - mostrar estado
            $this->solicitudPendiente = $solicitudPendiente;
            $this->modoSolicitud = true;
            $this->showSolicitudModal = true;
            return;
        }
        
        // VERIFICAR SI YA FUE APROBADO PERO NO HA CONFIGURADO SEGURIDAD
        $solicitudAprobada = $pastor->solicitudesModificacion()
            ->where('estado', 'aprobado')
            ->orderBy('created_at', 'desc')
            ->first();
        
        if ($solicitudAprobada && (!$pastor->preguntasSeguridad || !$pastor->preguntasSeguridad->activado)) {
            // Aprobado pero falta configurar seguridad - redirigir a configurar
            session()->flash('info', 'Su solicitud fue aprobada. Ahora debe configurar su seguridad.');
            return redirect()->route('pastor.configurar-seguridad', $pastor->id);
        }
        
        // VERIFICAR SI TIENE PROTECCIÓN ACTIVADA
        Log::info('Verificando protección:', [
            'tiene_relacion' => $pastor->preguntasSeguridad ? 'SÍ' : 'NO',
            'activado' => $pastor->preguntasSeguridad ? ($pastor->preguntasSeguridad->activado ? 'SÍ' : 'NO') : 'N/A',
        ]);
        
        if ($pastor->preguntasSeguridad && $pastor->preguntasSeguridad->activado) {
            // Protección activada - usar OTP para verificar identidad antes de editar
            if ($pastor->telefono_tlf) {
                $this->phoneNumber = $pastor->telefono_tlf;
            }
            
            $this->showOtpModal = true;
            $this->modoSolicitud = false;
            $this->otpSent = false;
            $this->otpVerified = false;
            $this->otpCode = '';
            $this->verificationError = '';
            return;
        }
        
        // NO TIENE PROTECCIÓN ACTIVADA - Crear solicitud automáticamente
        Log::info('No tiene protección, creando solicitud automática');
        $this->crearSolicitudAutomaticamente($pastor);
    }

    /**
     * Crear solicitud de cambio de teléfono automáticamente
     */
    public function crearSolicitudAutomaticamente(Pastor $pastor)
    {
        try {
            Log::info('Creando solicitud automática para pastor:', [
                'pastor_id' => $pastor->id,
                'telefono' => $pastor->telefono_tlf,
            ]);
            
            // Verificar que el pastor tenga teléfono
            if (empty($pastor->telefono_tlf)) {
                session()->flash('error', 'No se puede crear la solicitud porque no tiene un número de teléfono registrado.');
                return;
            }
            
            $service = app(\App\Services\PastorAuthorizationService::class);
            
            // Crear solicitud con el teléfono actual del pastor
            $solicitud = $service->crearSolicitud($pastor, $pastor->telefono_tlf);
            
            Log::info('Solicitud creada exitosamente:', [
                'solicitud_id' => $solicitud->id,
                'token' => substr($solicitud->token, 0, 10) . '...',
            ]);
            
            // Mostrar modal con información de la solicitud
            $this->solicitudPendiente = $solicitud;
            $this->modoSolicitud = true;
            $this->showSolicitudModal = true;
            
        } catch (\Exception $e) {
            Log::error('Error al crear solicitud automática:', [
                'pastor_id' => $pastor->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            
            session()->flash('error', 'Error al crear la solicitud: ' . $e->getMessage());
        }
    }

    /**
     * Redirigir al formulario de edición sin cambiar teléfono
     */
    public function continuarSinCambiarTelefono()
    {
        $this->showSolicitudModal = false;
        return redirect()->route('public.pastores.editar', $this->selectedPastorId);
    }

    public function sendOtp()
    {
        $validatedData = $this->validate([
            'phoneNumber' => 'required|numeric|digits_between:10,15'
        ], [
            'phoneNumber.required' => 'El número de teléfono es obligatorio.',
            'phoneNumber.numeric' => 'El número de teléfono debe contener solo dígitos.',
            'phoneNumber.digits_between' => 'El número de teléfono debe tener entre 10 y 15 dígitos.'
        ]);

        // Formatear el número de teléfono para WhatsApp
        $formattedNumber = $this->formatPhoneNumber($this->phoneNumber);

        // Generar código OTP de 6 dígitos
        $otp = rand(100000, 999999);
        
        // Guardar el OTP en caché con expiración de 5 minutos
        Cache::put('otp_' . $this->selectedPastorId, $otp, now()->addMinutes(5));

        // Enviar OTP por WhatsApp
        try {
            // Obtener los datos del pastor primero
            $pastor = $this->pastorData;
            
            // Usar el servicio de WhatsApp con la empresa del pastor
            $whatsappService = app(\App\Services\WhatsAppService::class, ['empresa' => $pastor->empresa_id]);
            
            // Verificar si el servicio está correctamente configurado
            if (!$whatsappService->isConfigured()) {
                $this->verificationError = 'Servicio de WhatsApp no configurado. Contacte al administrador.';
                return;
            }
            
            // Preparar el mensaje de verificación con los datos del pastor
            $message = "Sr(a). {$pastor->nombres} {$pastor->apellidos}, su código de verificación es: {$otp}\n";
            $message .= "Cédula: {$pastor->documento}\n";
            $message .= "Usted está solicitando modificar sus datos personales.";
            
            $result = $whatsappService->sendMessage($formattedNumber, $message);
            
            if ($result) {
                $this->otpSent = true;
                $this->verificationError = '';
                session()->flash('success', "Código de verificación enviado a {$formattedNumber} para {$pastor->nombres} {$pastor->apellidos}");
            } else {
                $this->verificationError = 'Error al enviar el código. Intente nuevamente.';
            }
        } catch (\Exception $e) {
            Log::error('Error sending OTP via WhatsApp: ' . $e->getMessage());
            $this->verificationError = 'Error al enviar el código: ' . $e->getMessage();
        }
    }

    public function verifyOtp()
    {
        $validatedData = $this->validate([
            'otpCode' => 'required|numeric|digits:6'
        ], [
            'otpCode.required' => 'El código OTP es obligatorio.',
            'otpCode.numeric' => 'El código OTP debe contener solo dígitos.',
            'otpCode.digits' => 'El código OTP debe tener 6 dígitos.'
        ]);

        $storedOtp = Cache::get('otp_' . $this->selectedPastorId);
        
        if ($storedOtp && (int)$storedOtp === (int)$this->otpCode) {
            // OTP válido
            $this->otpVerified = true;
            $this->verificationError = '';
            
            // Limpiar el OTP de la caché después de verificarlo
            Cache::forget('otp_' . $this->selectedPastorId);
            
            // Redirigir al formulario de edición (ya tiene protección activada)
            return redirect()->route('public.pastores.editar', $this->selectedPastorId);
        } else {
            $this->verificationError = 'Código OTP inválido. Intente nuevamente.';
            $this->otpCode = '';
        }
    }

    private function formatPhoneNumber($number)
    {
        // Remover caracteres no numéricos
        $cleanNumber = preg_replace('/[^0-9]/', '', $number);
        
        // Debug: Mostrar los valores para entender qué está ocurriendo
        Log::info("FormatPhone: Original: $number, Clean: $cleanNumber");
        
        // Si comienza con 04, reemplazar por 58 manteniendo todos los dígitos restantes
        if (substr($cleanNumber, 0, 1) === '0') {
            // Tomar los dígitos desde la posición 2 en adelante (después de '04') y anteponer '58'
            $remainingDigits = substr($cleanNumber, 1);
            $formattedNumber = '58' . $remainingDigits;
            Log::info("FormatPhone: Converted from 0 to 58: $cleanNumber -> $formattedNumber");
            return $formattedNumber;
        }
        
        // Si comienza con 0, reemplazar por el código del país
        if (substr($cleanNumber, 0, 1) === '0' && strlen($cleanNumber) === 11) {
            $cleanNumber = '58' . substr($cleanNumber, 1);
        }
        
        return $cleanNumber;
    }

    public function render()
    {
        return view('livewire.public.pastores.busqueda')
            ->layout('components.layouts.auth-basic', ['title' => 'Buscar Pastor']);
    }
}