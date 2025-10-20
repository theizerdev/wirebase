<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'verification_code',
        'verification_code_sent_at',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'verification_code',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'verification_code_sent_at' => 'datetime',
        ];
    }

    /**
     * Generar un código de verificación de 6 dígitos numéricos
     */
    public function generateVerificationCode()
    {
        // Generar código numérico de 6 dígitos
        $code = '';
        for ($i = 0; $i < 6; $i++) {
            $code .= rand(0, 9);
        }
        
        // Almacenar el código cifrado
        $this->verification_code = Hash::make($code);
        $this->verification_code_sent_at = Carbon::now();
        $this->save();
        
        // Devolver el código sin cifrar para enviar por correo
        return $code;
    }

    /**
     * Verificar si el código proporcionado es válido
     */
    public function isVerificationCodeValid($code)
    {
        // Verificar que el código exista y no haya expirado (15 minutos)
        if (!$this->verification_code || !$this->verification_code_sent_at) {
            return false;
        }

        if (Carbon::now()->diffInMinutes($this->verification_code_sent_at) > 15) {
            return false;
        }

        // Verificar que el código coincida
        return Hash::check($code, $this->verification_code);
    }

    /**
     * Marcar el correo electrónico como verificado
     */
    public function markEmailAsVerified()
    {
        $this->email_verified_at = Carbon::now();
        $this->verification_code = null;
        $this->verification_code_sent_at = null;
        $this->save();
    }

    /**
     * Get the active sessions for the user.
     */
    public function activeSessions()
    {
        return $this->hasMany(ActiveSession::class);
    }
}