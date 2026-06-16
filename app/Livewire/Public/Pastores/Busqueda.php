<?php

namespace App\Livewire\Public\Pastores;

use Livewire\Component;
use App\Models\Pastor;
use App\Models\HistorialVerificacionPastor;
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
    
    // Variables para verificación de seguridad
    public $showSecurityQuestionModal = false;
    public $securityQuestion = '';
    public $securityAnswer = '';
    public $securityVerificationStep = 'question'; // 'question' o 'otp'
    public $lastFailedAttempt = null; // Timestamp del último intento fallido
    public $failedAttemptsCount = 0; // Contador de intentos fallidos consecutivos
    
    // Variables para CAPTCHA matemático (después de 2 intentos fallidos)
    public $showCaptcha = false;
    public $captchaQuestion = '';
    public $captchaAnswer = 0;
    public $captchaInput = '';
    
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
        
        // VERIFICAR BLOQUEO POR IP
        $ip = request()->ip();
        $ipBlocked = Cache::get('ip_blocked_' . $ip);
        
        if ($ipBlocked) {
            Log::warning('Intento bloqueado por IP', [
                'ip' => $ip,
                'pastor_id' => $id
            ]);
            session()->flash('error', 'Demasiados intentos fallidos desde su ubicación. Por favor espere 15 minutos antes de intentar nuevamente.');
            return;
        }
        
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
            'pastor_id' => $pastor->id,
            'tiene_relacion' => $pastor->preguntasSeguridad ? 'SÍ' : 'NO',
            'activado' => $pastor->preguntasSeguridad ? ($pastor->preguntasSeguridad->activado ? 'SÍ' : 'NO') : 'N/A',
            'tiene_preguntas' => $pastor->preguntasSeguridad ? (!empty($pastor->preguntasSeguridad->preguntas) ? 'SÍ (' . count($pastor->preguntasSeguridad->preguntas) . ')' : 'NO') : 'N/A',
            'tiene_telefono' => $pastor->telefono_tlf ? 'SÍ' : 'NO',
        ]);
        
        if ($pastor->preguntasSeguridad && $pastor->preguntasSeguridad->activado) {
            // Protección activada
            
            // Si tiene teléfono registrado Y tiene preguntas de seguridad, mostrar pregunta de seguridad
            if ($pastor->telefono_tlf && $pastor->telefono_tlf !== '0' && !empty($pastor->preguntasSeguridad->preguntas)) {
                Log::info('Mostrando pregunta de seguridad antes de OTP');
                
                // Verificar rate limiting (cooldown después de intentos fallidos)
                $cooldownKey = 'security_cooldown_' . $pastor->id;
                $cooldownUntil = Cache::get($cooldownKey);
                
                if ($cooldownUntil && now()->lt($cooldownUntil)) {
                    $remainingSeconds = now()->diffInSeconds($cooldownUntil);
                    $this->verificationError = "Demasiados intentos fallidos. Por favor espere {$remainingSeconds} segundos antes de intentar nuevamente.";
                    Log::warning('Intento bloqueado por rate limiting', [
                        'pastor_id' => $pastor->id,
                        'cooldown_remaining' => $remainingSeconds
                    ]);
                    return;
                }
                
                // Obtener todas las preguntas de seguridad
                $preguntas = collect($pastor->preguntasSeguridad->preguntas);
                if ($preguntas->isNotEmpty()) {
                    // Seleccionar pregunta de forma inteligente para evitar repeticiones
                    $preguntaSeleccionada = $this->seleccionarPreguntaInteligente($preguntas, $pastor->id);
                    
                    Log::info('Pregunta seleccionada inteligentemente:', [
                        'pregunta' => $preguntaSeleccionada['pregunta'],
                        'total_preguntas' => $preguntas->count(),
                        'intentos_fallidos' => $this->failedAttemptsCount
                    ]);
                    
                    $this->securityQuestion = $preguntaSeleccionada['pregunta'];
                    $this->securityAnswer = '';
                    $this->securityVerificationStep = 'question';
                    $this->showSecurityQuestionModal = true;
                    $this->modoSolicitud = false;
                    return;
                }
            }
            
            // Si no tiene teléfono o no tiene preguntas, usar el flujo antiguo con OTP
            Log::info('Usando flujo antiguo con OTP (no cumple condiciones para pregunta de seguridad)');
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
            
            $this->dispatch('notify', [
                'type' => 'info',
                'message' => 'Error al crear solicitud automática.',
                'duration' => 5000
            ]);
            
            
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
           $this->dispatch('notify', [
                'type' => 'error',
                'message' => '¡Error al enviar el código.',
                'duration' => 5000
            ]);
            $this->verificationError = 'Error al enviar el código: ' . $e->getMessage();
        }
    }

    /**
     * Verificar respuesta de seguridad y enviar OTP si es correcta
     */
    public function verifySecurityAnswer()
    {
        // Validar CAPTCHA si está activo (después de 2 intentos fallidos)
        if ($this->showCaptcha) {
            $this->validate([
                'captchaInput' => 'required|numeric'
            ], [
                'captchaInput.required' => 'Debe resolver el CAPTCHA.',
                'captchaInput.numeric' => 'El CAPTCHA debe ser un número.'
            ]);
            
            if ((int)$this->captchaInput !== $this->captchaAnswer) {
                $this->verificationError = 'CAPTCHA incorrecto. Intente nuevamente.';
                $this->generarCaptcha(); // Generar nuevo CAPTCHA
                return;
            }
        }
        
        $this->validate([
            'securityAnswer' => 'required|string|min:1'
        ], [
            'securityAnswer.required' => 'Debe ingresar una respuesta.'
        ]);

        $pastor = $this->pastorData;
        
        if (!$pastor || !$pastor->preguntasSeguridad) {
            $this->verificationError = 'Error: No se encontraron datos de seguridad.';
            return;
        }

        // Verificar la respuesta
        $respuestaCorrecta = $pastor->preguntasSeguridad->verificarRespuesta(
            $this->securityQuestion,
            $this->securityAnswer
        );

        if ($respuestaCorrecta) {
            // Respuesta correcta - cerrar modal de pregunta y mostrar OTP
            $this->showSecurityQuestionModal = false;
            
            // Resetear contador de intentos fallidos
            $this->failedAttemptsCount = 0;
            Cache::forget('security_cooldown_' . $pastor->id);
            Cache::forget('last_question_' . $pastor->id);
            
            // REGISTRAR EN HISTORIAL - Verificación exitosa de pregunta de seguridad
            HistorialVerificacionPastor::create([
                'pastor_id' => $pastor->id,
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metodo_verificacion' => 'seguridad_otp',
                'exitoso' => true,
                'pregunta_mostrada' => $this->securityQuestion,
                'verificado_en' => now(),
            ]);
            
            Log::info('Verificación de seguridad exitosa registrada en historial', [
                'pastor_id' => $pastor->id,
                'ip' => request()->ip()
            ]);
            
            // Pre-llenar el teléfono y mostrar modal de OTP
            if ($pastor->telefono_tlf) {
                $this->phoneNumber = $pastor->telefono_tlf;
            }
            
            $this->showOtpModal = true;
            $this->modoSolicitud = false;
            $this->otpSent = false;
            $this->otpVerified = false;
            $this->otpCode = '';
            $this->verificationError = '';
            
             $this->dispatch('notify', [
                'type' => 'success',
                'message' => '¡Respuesta correcta! Ahora ingrese su código de verificación.',
                'duration' => 5000
            ]);
        } else {
            // Respuesta incorrecta - incrementar contador y aplicar cooldown
            $this->failedAttemptsCount++;
            $this->lastFailedAttempt = now();
            
            // TRACKING POR IP - Incrementar contador de fallos por IP
            $ip = request()->ip();
            $ipFailuresKey = 'ip_failures_' . $ip;
            $ipFailures = Cache::get($ipFailuresKey, 0) + 1;
            Cache::put($ipFailuresKey, $ipFailures, now()->addMinutes(30));
            
            // REGISTRAR EN HISTORIAL - Intento fallido
            HistorialVerificacionPastor::create([
                'pastor_id' => $pastor->id,
                'ip_address' => $ip,
                'user_agent' => request()->userAgent(),
                'metodo_verificacion' => 'seguridad_otp',
                'exitoso' => false,
                'pregunta_mostrada' => $this->securityQuestion,
            ]);
            
             $this->dispatch('notify', [
                'type' => 'info',
                'message' => 'Intento fallido de respuesta de seguridad',
                'duration' => 5000
            ]);
            
            // BLOQUEO POR IP después de 5 intentos fallidos (acumulados entre todos los pastores)
            if ($ipFailures >= 5) {
                Cache::put('ip_blocked_' . $ip, true, now()->addMinutes(15));
                Cache::forget($ipFailuresKey);
                
                Log::error('IP BLOQUEADA por múltiples intentos fallidos', [
                    'ip' => $ip,
                    'total_fallos' => $ipFailures,
                    'pastor_actual' => $pastor->id
                ]);
                
                $this->verificationError = 'Demasiados intentos fallidos desde su ubicación. Su acceso ha sido bloqueado por 15 minutos por seguridad.';
                $this->showSecurityQuestionModal = false;
                return;
            }
            
            // NOTIFICAR AL PASTOR después de 2 intentos fallidos
            if ($this->failedAttemptsCount == 2 && $pastor->telefono_tlf && $pastor->telefono_tlf !== '0') {
                $this->notificarPastorIntentosSospechosos($pastor, $ip);
            }
            
            // GENERAR CAPTCHA después de 2 intentos fallidos
            if ($this->failedAttemptsCount >= 2) {
                $this->generarCaptcha();
                $this->showCaptcha = true;
            }
            
            // Aplicar cooldown progresivo según número de intentos
            if ($this->failedAttemptsCount >= 3) {
                // Después de 3 intentos fallidos: cooldown de 60 segundos
                $cooldownSeconds = 60;
                Cache::put('security_cooldown_' . $pastor->id, now()->addSeconds($cooldownSeconds), now()->addMinutes(5));
                
                $this->verificationError = "Demasiados intentos fallidos. Debe esperar {$cooldownSeconds} segundos antes de intentar nuevamente.";
                
                Log::warning('Cooldown aplicado por múltiples intentos fallidos', [
                    'pastor_id' => $pastor->id,
                    'cooldown_seconds' => $cooldownSeconds,
                    'intentos' => $this->failedAttemptsCount
                ]);
            } elseif ($this->failedAttemptsCount >= 2) {
                // Después de 2 intentos fallidos: cooldown de 30 segundos
                $cooldownSeconds = 30;
                Cache::put('security_cooldown_' . $pastor->id, now()->addSeconds($cooldownSeconds), now()->addMinutes(5));
                
                $this->verificationError = "Intento fallido. Debe esperar {$cooldownSeconds} segundos antes de intentar nuevamente.";
            } else {
                // Primer intento fallido: solo mensaje de error
                $this->verificationError = 'Respuesta incorrecta. Intente nuevamente.';
            }
            
            // Limpiar respuesta para reintentar
            $this->securityAnswer = '';
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

    /**
     * Seleccionar pregunta de seguridad de forma inteligente para evitar repeticiones
     * 
     * @param \Illuminate\Support\Collection $preguntas Colección de preguntas disponibles
     * @param int $pastorId ID del pastor
     * @return array Pregunta seleccionada
     */
    private function seleccionarPreguntaInteligente($preguntas, $pastorId)
    {
        // Obtener la última pregunta mostrada (almacenada en caché por 10 minutos)
        $cacheKey = 'last_question_' . $pastorId;
        $ultimaPregunta = Cache::get($cacheKey);
        
        // Filtrar preguntas que no sean la última mostrada
        $preguntasDisponibles = $preguntas->filter(function($pregunta) use ($ultimaPregunta) {
            return $pregunta['pregunta'] !== $ultimaPregunta;
        });
        
        // Si hay preguntas disponibles (diferentes a la última), seleccionar una aleatoria entre ellas
        if ($preguntasDisponibles->isNotEmpty()) {
            $preguntaSeleccionada = $preguntasDisponibles->random();
        } else {
            // Si todas las preguntas son iguales a la última (caso raro con solo 1 pregunta),
            // seleccionar cualquier pregunta aleatoriamente
            $preguntaSeleccionada = $preguntas->random();
        }
        
        // Guardar la pregunta seleccionada en caché para la próxima vez (10 minutos)
        Cache::put($cacheKey, $preguntaSeleccionada['pregunta'], now()->addMinutes(10));
        
        return $preguntaSeleccionada;
    }

    /**
     * Generar CAPTCHA matemático simple
     */
    private function generarCaptcha()
    {
        $num1 = rand(2, 15);
        $num2 = rand(2, 15);
        $this->captchaQuestion = "¿Cuánto es {$num1} + {$num2}?";
        $this->captchaAnswer = $num1 + $num2;
        $this->captchaInput = '';
    }

    /**
     * Notificar al pastor sobre intentos sospechosos de acceso
     * 
     * @param Pastor $pastor
     * @param string $ip Dirección IP del intento
     */
    private function notificarPastorIntentosSospechosos($pastor, $ip)
    {
        try {
            // Obtener información de geolocalización básica (si está disponible)
            $geoInfo = '';
            try {
                $geoData = \Illuminate\Support\Facades\Http::timeout(2)->get("https://ipapi.co/{$ip}/json/")->json();
                if (isset($geoData['city']) && isset($geoData['country_name'])) {
                    $geoInfo = " desde {$geoData['city']}, {$geoData['country_name']}";
                }
            } catch (\Exception $e) {
                // Si falla la geolocalización, continuar sin ella
            }
            
            $mensaje = "⚠️ ALERTA DE SEGURIDAD\n\n" .
                       "Hola {$pastor->nombre_completo}, hemos detectado 2 intentos fallidos de verificación en su cuenta.\n\n" .
                       "Hora: " . now()->format('d/m/Y H:i') . "\n" .
                       "IP: {$ip}{$geoInfo}\n\n" .
                       "Si no fue usted, contacte inmediatamente a su presbítero o administrador del sistema.\n\n" .
                       "Equipo SAPRCOE";
            
            // Formatear teléfono
            $telefono = $this->formatPhoneNumber($pastor->telefono_tlf);
            
            // Enviar WhatsApp usando el servicio con la empresa del pastor
            $whatsappService = app(\App\Services\WhatsAppService::class, ['empresa' => $pastor->empresa_id]);
            
            if ($whatsappService->isConfigured()) {
                $whatsappService->sendMessage($telefono, $mensaje);
                
                Log::info('Notificación de seguridad enviada al pastor', [
                    'pastor_id' => $pastor->id,
                    'telefono' => $telefono,
                    'ip' => $ip
                ]);
            } else {
                Log::warning('No se pudo enviar notificación de seguridad - WhatsApp no configurado', [
                    'pastor_id' => $pastor->id
                ]);
            }
        } catch (\Exception $e) {
            // Mostrar notificación de que se generó la contraseña
            $this->dispatch('notify', [
                'type' => 'info',
                'message' => 'Error al enviar notificación de seguridad al pastor',
                'duration' => 5000
            ]);
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