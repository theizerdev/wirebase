<?php

namespace App\Livewire\Auth;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class Login extends Component
{
    public $email = '';
    public $password = '';
    public $remember = false;

    // Propiedades para manejar errores de validación
    public $errors = [];

    public function rules()
    {
        return [
            'email' => 'required|string|email',
            'password' => 'required|string',
        ];
    }

    public function updated($field)
    {
        // Limpiar errores cuando el usuario empieza a escribir
        if (isset($this->errors[$field])) {
            unset($this->errors[$field]);
        }
    }

    public function authenticate()
    {
        $this->errors = []; // Limpiar errores anteriores
        
        try {
            $this->validate();
        } catch (ValidationException $e) {
            $this->errors = $e->validator->errors()->messages();
            return;
        }

        $throttleKey = Str::transliterate(Str::lower($this->email).'|'.request()->ip());

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $this->errors['email'] = [trans('auth.throttle', [
                'seconds' => RateLimiter::availableIn($throttleKey, 5),
            ])];
            return;
        }

        if (! Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
            RateLimiter::hit($throttleKey);
            
            $this->errors['email'] = [trans('auth.failed')];
            return;
        }

        RateLimiter::clear($throttleKey);

        // Regenerar sesión para prevenir session fixation
        request()->session()->regenerate();

        return redirect()->intended('/');
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
        return view('livewire.auth.login', [
            'hasError' => $this->hasError(...),
            'getError' => $this->getError(...),
        ])->layout('components.layouts.auth-basic', ['title' => 'Login']);
    }
}