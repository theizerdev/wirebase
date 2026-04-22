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
        Schema::create('motos', function (Blueprint $table) {
            $table->id();
            $table->string('marca');
            $table->string('modelo');
            $table->integer('anio');
            $table->string('color_principal')->nullable();
            $table->string('cilindrada')->nullable(); // cc
            $table->string('tipo')->nullable(); // Paseo, Trabajo, Deportiva
            $table->text('descripcion')->nullable();
            
            // Precios Base
            $table->decimal('precio_venta_base', 10, 2);
            $table->decimal('costo_referencial', 10, 2)->nullable();
            
            $table->string('imagen_url')->nullable();
            $table->boolean('activo')->default(true);
            
            // Multitenancy (Global o por empresa)
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->onDelete('cascade');
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['marca', 'modelo', 'anio', 'empresa_id'], 'moto_unique_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('motos');
    }
};
