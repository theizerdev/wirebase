<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('solicitud_modificacion_pastores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pastor_id')->constrained('pastores')->onDelete('cascade');
            $table->foreignId('presbitero_user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->string('token', 64)->unique(); // Token único para el link
            $table->string('telefono_nuevo')->nullable(); // Teléfono que quiere registrar
            $table->enum('estado', [
                'pendiente',      // Esperando aprobación del presbítero
                'aprobado',       // Presbítero aprobó
                'rechazado',      // Presbítero rechazó
                'expirado',       // Token expiró
                'completado'      // Pastor ya configuró preguntas de seguridad
            ])->default('pendiente');
            $table->timestamp('token_expires_at'); // Expiración del token (24h)
            $table->timestamp('aprobado_en')->nullable();
            $table->text('motivo_rechazo')->nullable();
            $table->ipAddress('ip_solicitud')->nullable();
            $table->ipAddress('ip_aprobacion')->nullable();
            $table->timestamps();
            
            // Índices para búsquedas frecuentes
            $table->index('token');
            $table->index('estado');
            $table->index('pastor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('solicitud_modificacion_pastores');
    }
};
