<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use App\Services\WhatsAppService;
use App\Services\Audit\AuditService;

class VerifyCode extends Component
{
    public $code = '';
    public $codeInputs = ['', '', '', '', '', '']; // 6 campos separados
    public $resent = false;
    public $errors = [];
    public $canResend = true;
    public $resendCountdown = 0;

    // Reglas de validación
    protected $rules = [
        'code' => 'required|regex:/^[0-9]{6}$/',
    ];

    public function mount()
    {
        // Si el usuario ya ha verificado su correo, redirigir
        if (Auth::user()->hasVerifiedEmail()) {
            $user = Auth::user();
            if ($user->cliente_id) {
                return redirect('/cliente/app');
            }
            return redirect()->intended('/');
        }
        
        // Verificar si hay un límite de reenvío
        $throttleKey = $this->throttleKey();
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $this->canResend = false;
            $this->resendCountdown = RateLimiter::availableIn($throttleKey, 3);
        }
    }

    // Generar clave para limitar intentos
    protected function throttleKey()
    {
        return 'resend-verification-code|' . Auth::id();
    }

    // Actualizar código cuando cambian los inputs individuales
    public function updatedCodeInputs($value, $index)
    {
        // Limpiar errores cuando el usuario empieza a escribir
        unset($this->errors['code']);
        
        // Convertir a mayúsculas
        $this->codeInputs[$index] = strtoupper($value);
        
        // Combinar todos los inputs en un solo código
        $this->code = implode('', $this->codeInputs);
        
        // Si todos los campos están llenos, verificar automáticamente
        if (strlen($this->code) == 6 && !in_array('', $this->codeInputs)) {
            $this->verifyCode();
        }
        
        // Mover foco al siguiente campo si se llena uno
        if (strlen($value) == 1 && $index < 5) {
            $this->dispatch('focus-next', $index + 1);
        }
    }

    // Enviar o reenviar el código de verificación
    public function sendCode()
    {
        // Verificar límite de intentos
        $throttleKey = $this->throttleKey();
        if (RateLimiter::tooManyAttempts($throttleKey, 3)) {
            $this->canResend = false;
            $this->resendCountdown = RateLimiter::availableIn($throttleKey, 3);
            return;
        }

        // Registrar intento
        RateLimiter::hit($throttleKey, 300); // 5 minutos

        // Generar un nuevo código y obtener el texto plano
        $plainCode = Auth::user()->generateVerificationCode();

        // Resolver teléfono destino
        $user = Auth::user();
        $telefono = null;
        if ($user->cliente) {
            $telefono = $user->cliente->telefono;
        }
        // Si no hay teléfono, abortar con notificación
        if (!$telefono) {
            $this->errors['code'] = 'No se puede enviar el código: no hay teléfono asociado.';
            app(AuditService::class)->logSecurityEvent('otp.send.failed', [
                'reason' => 'no_phone',
                'user_id' => $user->id,
            ], 'Intento de envío OTP sin teléfono');
            return;
        }

        // Enviar vía WhatsApp
        $wa = WhatsAppService::forCompany($user->empresa_id);
        $message = "Tu código de verificación es: {$plainCode}. Vence en 30 minutos.";
        $wa->send($telefono, $message);

        app(AuditService::class)->logSecurityEvent('otp.send.success', [
            'user_id' => $user->id,
            'phone_mask' => substr(preg_replace('/\\D+/', '', $telefono), -4),
        ], 'OTP enviado por WhatsApp');
        
        $this->resent = true;
        $this->canResend = false;
        // Limpiar campos de código
        $this->codeInputs = ['', '', '', '', '', ''];
        $this->code = '';
        session()->flash('resent', 'Se ha enviado un nuevo código de verificación a tu correo electrónico.');
        
        // Iniciar contador regresivo
        $this->resendCountdown = 300; // 5 minutos en segundos
        
        // Actualizar estado de reenvío cada segundo
        $this->js('
            let countdown = ' . $this->resendCountdown . ';
            const interval = setInterval(() => {
                countdown--;
                if (countdown <= 0) {
                    clearInterval(interval);
                    window.livewire.emit("countdownFinished");
                }
            }, 1000);
        ');
    }

    // Escuchar cuando termina el contador
    protected $listeners = ['countdownFinished' => 'enableResend'];

    public function enableResend()
    {
        $this->canResend = true;
        $this->resendCountdown = 0;
    }

    // Verificar el código ingresado
    public function verifyCode()
    {
        $this->errors = [];
        
        // Validar entrada numérica de 6 dígitos
        if (!preg_match('/^[0-9]{6}$/', $this->code)) {
            $this->errors['code'] = 'El código debe tener 6 dígitos.';
            return;
        }

        // Verificar si el código es válido
        if (Auth::user()->isVerificationCodeValid($this->code)) {
            // Marcar el correo como verificado
            Auth::user()->markEmailAsVerified();
            app(AuditService::class)->logSecurityEvent('otp.verify.success', [
                'user_id' => Auth::id(),
            ]);
            
            // Regenerar sesión para prevenir session fixation
            request()->session()->regenerate();
            
            // Redirigir según tipo de usuario
            $user = Auth::user();
            if ($user->cliente_id) {
                return redirect('/cliente/app');
            }
            return redirect()->intended('/');
        } else {
            $this->errors['code'] = 'El código ingresado no es válido o ha expirado.';
            app(AuditService::class)->logSecurityEvent('otp.verify.failed', [
                'user_id' => Auth::id(),
            ], 'OTP inválido o expirado');
        }
    }

    // Método para verificar si un campo tiene error
    public function hasError($field)
    {
        return isset($this->errors[$field]) && !empty($this->errors[$field]);
    }

    // Método para obtener los mensajes de error de un campo
    public function getError($field)
    {
        return $this->hasError($field) ? $this->errors[$field][0] : '';
    }

    public function render()
    {
        return view('livewire.auth.verify-code', [
            'hasError' => $this->hasError(...),
            'getError' => $this->getError(...),
            'canResend' => $this->canResend,
            'resendCountdown' => $this->resendCountdown,
        ])->layout('components.layouts.auth-basic', ['title' => 'Verificar Código']);
    }
}
