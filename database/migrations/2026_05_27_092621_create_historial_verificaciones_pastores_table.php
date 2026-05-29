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
        Schema::create('historial_verificaciones_pastores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pastor_id'); // Sin foreign key para evitar problemas
            $table->string('ip_address', 45)->nullable(); // IPv6 puede tener hasta 45 caracteres
            $table->text('user_agent')->nullable();
            $table->enum('metodo_verificacion', ['seguridad_otp', 'solo_otp'])->default('seguridad_otp');
            $table->boolean('exitoso')->default(false);
            $table->string('pregunta_mostrada')->nullable(); // La pregunta que se mostró
            $table->timestamp('verificado_en')->nullable(); // Cuándo se verificó correctamente
            $table->timestamps();
            
            // Índices para consultas frecuentes
            $table->index('pastor_id');
            $table->index('ip_address');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('historial_verificaciones_pastores');
    }
};
