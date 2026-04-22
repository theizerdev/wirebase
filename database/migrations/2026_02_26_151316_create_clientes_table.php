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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('apellido');
            $table->string('documento')->unique(); // CI, RIF, Pasaporte
            $table->string('tipo_documento')->default('CI');
            $table->string('email')->nullable();
            $table->string('telefono');
            $table->string('telefono_alternativo')->nullable();
            $table->text('direccion')->nullable();
            $table->string('ciudad')->nullable();
            $table->string('estado_region')->nullable();
            $table->boolean('activo')->default(true);
            
            // Datos laborales/financieros básicos
            $table->string('ocupacion')->nullable();
            $table->string('empresa_trabajo')->nullable();
            $table->decimal('ingreso_mensual_estimado', 10, 2)->nullable();
            
            // Multitenancy
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->onDelete('set null');
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
