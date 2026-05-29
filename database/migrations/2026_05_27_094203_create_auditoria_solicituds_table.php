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
        Schema::create('auditoria_solicitudes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('solicitud_id'); // Foreign key se agregará después
            $table->enum('accion', [
                'creada', 
                'vista', 
                'aprobada', 
                'rechazada', 
                'escalada', 
                'reabierta',
                'notificacion_enviada'
            ])->default('creada');
            $table->unsignedBigInteger('usuario_id')->nullable(); // Quién hizo la acción
            $table->string('usuario_nombre')->nullable(); // Nombre del usuario (para logging)
            $table->string('usuario_rol')->nullable(); // Rol del usuario
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->json('metadata')->nullable(); // Datos adicionales (motivo, comentarios, etc.)
            $table->timestamps();
            
            // Índices para consultas frecuentes
            $table->index('solicitud_id');
            $table->index('accion');
            $table->index('usuario_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('auditoria_solicituds');
    }
};
