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
        Schema::create('responsables', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_completo');
            $table->string('cedula')->unique();
            $table->unsignedBigInteger('estado_id')->nullable();
            $table->unsignedBigInteger('municipio_id')->nullable();
            $table->unsignedBigInteger('parroquia_id')->nullable();
            $table->string('telefono')->nullable();
            $table->text('direccion')->nullable();
            $table->text('punto_referencia')->nullable();
            $table->date('fecha_levantamiento')->nullable();
            $table->string('codigo_casa_alimentacion')->nullable();
            $table->unsignedBigInteger('empresa_id')->nullable();
            $table->unsignedBigInteger('sucursal_id')->nullable();
            $table->timestamps();
            
            $table->foreign('estado_id')->references('id')->on('estados')->onDelete('set null');
            $table->foreign('municipio_id')->references('id')->on('municipios')->onDelete('set null');
            $table->foreign('parroquia_id')->references('id')->on('parroquias')->onDelete('set null');
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('set null');
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('responsables');
    }
};