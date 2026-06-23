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
        Schema::create('asistencias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('actividad_id')->constrained('actividades')->onDelete('cascade');
            $table->foreignId('pastor_id')->constrained('pastores')->onDelete('cascade');
            $table->string('metodo')->default('QR'); // QR o Manual
            $table->timestamp('fecha_hora')->useCurrent();
            $table->timestamps();
            
            // Prevent duplicate attendance for same activity and pastor
            $table->unique(['actividad_id', 'pastor_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('asistencias');
    }
};
