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
        Schema::create('actividades', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('tipo_actividad');
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->enum('estado', ['Activo', 'Finalizado', 'Suspendido'])->default('Activo');
            $table->integer('zona')->nullable();
            $table->integer('distrito')->nullable();
            $table->string('lugar')->nullable();
            $table->text('nota')->nullable();
            $table->foreignId('coordinador_id')->nullable()->constrained('pastores')->nullOnDelete();
            
            // Required for Multitenantable and other system relations
            $table->foreignId('empresa_id')->nullable()->constrained('empresas');
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales');
            $table->foreignId('user_id')->nullable()->constrained('users');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('actividades');
    }
};
