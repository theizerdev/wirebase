<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Hash;

class PreguntasSeguridadPastor extends Model
{
    use HasFactory;

    protected $table = 'preguntas_seguridad_pastores';

    protected $fillable = [
        'pastor_id',
        'preguntas',
        'backup_codes',
        'intentos_fallidos',
        'ultimo_intento',
        'activado',
    ];

    protected $casts = [
        'preguntas' => 'array',
        'backup_codes' => 'array',
        'ultimo_intento' => 'datetime',
        'activado' => 'boolean',
    ];

    /**
     * Relación con el Pastor
     */
    public function pastor()
    {
        return $this->belongsTo(Pastor::class, 'pastor_id');
    }

    /**
     * Guardar preguntas de seguridad (hashea las respuestas)
     * 
     * @param array $preguntas [{pregunta: string, respuesta: string}]
     */
    public function guardarPreguntas(array $preguntas): void
    {
        $preguntasHasheadas = collect($preguntas)->map(function ($item) {
            return [
                'pregunta' => $item['pregunta'],
                'respuesta_hash' => Hash::make(strtolower(trim($item['respuesta']))),
            ];
        })->toArray();

        $this->preguntas = $preguntasHasheadas;
        $this->save();
    }

    /**
     * Verificar una sola respuesta de seguridad
     * 
     * @param string $pregunta Texto de la pregunta
     * @param string $respuesta Respuesta ingresada por el usuario
     * @return bool
     */
    public function verificarRespuesta(string $pregunta, string $respuesta): bool
    {
        if (!$this->activado || empty($this->preguntas)) {
            return false;
        }

        // Buscar la pregunta en el array de preguntas
        $preguntaEncontrada = collect($this->preguntas)->firstWhere('pregunta', $pregunta);

        if (!$preguntaEncontrada) {
            return false;
        }

        // Verificar la respuesta (case-insensitive y trim)
        $respuestaCorrecta = Hash::check(
            strtolower(trim($respuesta)),
            $preguntaEncontrada['respuesta_hash']
        );

        // Registrar intento
        $this->registrarIntento($respuestaCorrecta);

        return $respuestaCorrecta;
    }

    /**
     * Verificar respuestas de seguridad
     * 
     * @param array $respuestas [{pregunta: string, respuesta: string}]
     * @return bool
     */
    public function verificarRespuestas(array $respuestas): bool
    {
        if (!$this->activado || empty($this->preguntas)) {
            return false;
        }

        $todasCorrectas = true;

        foreach ($respuestas as $respuestaInput) {
            $preguntaEncontrada = collect($this->preguntas)->firstWhere('pregunta', $respuestaInput['pregunta']);

            if (!$preguntaEncontrada) {
                $todasCorrectas = false;
                break;
            }

            if (!Hash::check(strtolower(trim($respuestaInput['respuesta'])), $preguntaEncontrada['respuesta_hash'])) {
                $todasCorrectas = false;
                break;
            }
        }

        // Registrar intento
        $this->registrarIntento($todasCorrectas);

        return $todasCorrectas;
    }

    /**
     * Generar códigos de respaldo (backup codes)
     * 
     * @return array Códigos generados (sin encriptar, mostrar al usuario una vez)
     */
    public function generarBackupCodes(): array
    {
        $codes = [];
        
        for ($i = 0; $i < 10; $i++) {
            $code = strtoupper(substr(str_shuffle('ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8));
            $codes[] = $code;
        }

        // Encriptar los códigos antes de guardar
        $this->backup_codes = collect($codes)->map(fn($code) => Hash::make($code))->toArray();
        $this->save();

        return $codes; // Retornar sin encriptar para mostrar al usuario
    }

    /**
     * Verificar y consumir un código de respaldo
     * 
     * @param string $codigo Código ingresado por el usuario
     * @return bool
     */
    public function verificarBackupCode(string $codigo): bool
    {
        if (empty($this->backup_codes)) {
            return false;
        }

        foreach ($this->backup_codes as $index => $hashedCode) {
            if (Hash::check($codigo, $hashedCode)) {
                // Eliminar el código usado
                unset($this->backup_codes[$index]);
                $this->backup_codes = array_values($this->backup_codes);
                $this->save();
                
                return true;
            }
        }

        return false;
    }

    /**
     * Registrar intento de verificación
     * 
     * @param bool $exitoso
     */
    public function registrarIntento(bool $exitoso): void
    {
        if ($exitoso) {
            $this->intentos_fallidos = 0;
        } else {
            $this->intentos_fallidos += 1;
        }

        $this->ultimo_intento = now();
        $this->save();
    }

    /**
     * Verificar si está bloqueado por demasiados intentos fallidos
     * 
     * @return bool
     */
    public function estaBloqueado(): bool
    {
        if ($this->intentos_fallidos < 3) {
            return false;
        }

        // Bloqueo temporal de 15 minutos después de 3 intentos fallidos
        if ($this->ultimo_intento && $this->ultimo_intento->addMinutes(15)->isFuture()) {
            return true;
        }

        // Resetear después del tiempo de bloqueo
        if ($this->ultimo_intento && $this->ultimo_intento->addMinutes(15)->isPast()) {
            $this->intentos_fallidos = 0;
            $this->save();
            return false;
        }

        return false;
    }

    /**
     * Activar la configuración de seguridad
     */
    public function activar(): void
    {
        $this->activado = true;
        $this->intentos_fallidos = 0;
        $this->save();
    }

    /**
     * Desactivar la configuración de seguridad
     */
    public function desactivar(): void
    {
        $this->activado = false;
        $this->save();
    }

    /**
     * Obtener cuántos backup codes quedan disponibles
     * 
     * @return int
     */
    public function backupCodesDisponibles(): int
    {
        return count($this->backup_codes ?? []);
    }
}
