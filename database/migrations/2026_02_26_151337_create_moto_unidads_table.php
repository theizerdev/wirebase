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
        Schema::create('moto_unidades', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moto_id')->constrained('motos')->onDelete('cascade');
            
            // Identificadores Únicos
            $table->string('vin', 50)->unique();
            $table->string('numero_motor', 50)->unique();
            $table->string('numero_chasis', 50)->nullable();
            $table->string('placa')->nullable();
            
            $table->string('color_especifico')->nullable();
            $table->integer('kilometraje')->default(0);
            
            // Costos y Precios específicos de la unidad
            $table->decimal('costo_compra', 10, 2);
            $table->decimal('precio_venta', 10, 2); // Puede variar del base
            
            // Estado y Ubicación
            $table->enum('estado', ['disponible', 'reservado', 'vendido', 'mantenimiento', 'baja'])->default('disponible');
            $table->enum('condicion', ['nuevo', 'usado'])->default('nuevo');
            
            $table->date('fecha_ingreso');
            $table->date('fecha_venta')->nullable();
            
            // Multitenancy
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');
            
            $table->text('notas')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['estado', 'sucursal_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('moto_unidades');
    }
};
