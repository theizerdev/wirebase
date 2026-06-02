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
        Schema::create('beneficiarios', function (Blueprint $table) {
            $table->id();
            $table->string('nombres');
            $table->string('apellidos');
            $table->string('cedula')->unique();
            $table->date('fecha_nacimiento')->nullable();
            $table->integer('edad')->nullable(); // Calculated automatically
            $table->string('telefono_principal')->nullable();
            $table->string('telefono_alternativo')->nullable();
            $table->enum('estado_civil', ['Soltero(a)', 'Casado(a)', 'Viudo(a)', 'Divorciado(a)'])->default('Soltero(a)');
            $table->boolean('estudia_actualmente')->default(false);
            $table->string('estudio_actual')->nullable();
            $table->enum('nivel_instruccion', ['Analfabeto', 'Básica', 'Media Diversificada', 'TSU', 'Universitario', 'Maestría', 'Doctorado']);
            $table->string('ultimo_titulo_obtenido')->nullable();
            $table->boolean('trabaja_actualmente')->default(false);
            $table->string('lugar_trabajo')->nullable();
            $table->string('ocupacion')->nullable();
            $table->decimal('ingreso_mensual', 10, 2)->nullable();
            $table->foreignId('responsable_id')->nullable()->constrained('responsables')->nullOnDelete();
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->nullOnDelete();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('beneficiarios');
    }
};