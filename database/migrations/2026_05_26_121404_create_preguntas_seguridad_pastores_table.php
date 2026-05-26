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
        Schema::create('preguntas_seguridad_pastores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pastor_id')->constrained('pastores')->onDelete('cascade');
            $table->json('preguntas'); // [{pregunta: "¿Nombre de tu madre?", respuesta_hash: "..."}]
            $table->json('backup_codes'); // Códigos de respaldo encriptados
            $table->integer('intentos_fallidos')->default(0);
            $table->timestamp('ultimo_intento')->nullable();
            $table->boolean('activado')->default(false);
            $table->timestamps();
            
            // Índice para búsqueda rápida
            $table->index('pastor_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('preguntas_seguridad_pastores');
    }
};
