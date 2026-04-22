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
        Schema::create('plan_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contrato_id')->constrained('contratos')->onDelete('cascade');
            
            $table->integer('numero_cuota'); // 0 para inicial, 1..N para mensuales
            $table->string('tipo_cuota')->default('mensual'); // inicial, mensual, extraordinaria
            
            $table->date('fecha_vencimiento');
            $table->date('fecha_pago_real')->nullable();
            
            // Montos
            $table->decimal('monto_capital', 10, 2)->default(0);
            $table->decimal('monto_interes', 10, 2)->default(0);
            $table->decimal('monto_total', 10, 2); // Capital + Interés
            
            $table->decimal('saldo_pendiente', 10, 2); // Disminuye con pagos
            $table->decimal('monto_pagado', 10, 2)->default(0);
            
            // Mora
            $table->decimal('mora_calculada', 10, 2)->default(0);
            $table->decimal('mora_pagada', 10, 2)->default(0);
            $table->integer('dias_retraso')->default(0);
            
            $table->enum('estado', ['pendiente', 'parcial', 'pagado', 'vencido'])->default('pendiente');
            
            $table->timestamps();
            
            // Multitenancy (redundante pero útil para queries directos)
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            
            $table->unique(['contrato_id', 'numero_cuota']);
            $table->index(['fecha_vencimiento', 'estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('plan_pagos');
    }
};
