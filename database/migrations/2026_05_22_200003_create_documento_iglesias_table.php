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
        Schema::create('documento_iglesias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('iglesia_id');
            $table->string('nombre');
            $table->string('tipo'); // Permiso de funcionamiento, licencia, contrato, etc.
            $table->text('descripcion')->nullable();
            $table->string('ruta_archivo'); // Ruta donde se almacena el archivo
            $table->date('fecha_emision')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('usuario_registro_id');
            $table->timestamps();
            
            $table->foreign('iglesia_id')->references('id')->on('iglesias')->onDelete('cascade');
            $table->foreign('usuario_registro_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('documento_iglesias');
    }
};