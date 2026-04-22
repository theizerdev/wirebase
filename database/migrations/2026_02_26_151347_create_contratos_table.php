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
        Schema::create('contratos', function (Blueprint $table) {
            $table->id();
            $table->string('numero_contrato')->unique();
            
            // Relaciones
            $table->foreignId('cliente_id')->constrained('clientes')->onDelete('restrict');
            $table->foreignId('moto_unidad_id')->constrained('moto_unidades')->onDelete('restrict');
            $table->foreignId('vendedor_id')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->foreignId('sucursal_id')->constrained('sucursales')->onDelete('cascade');
            
            // Condiciones Financieras
            $table->date('fecha_inicio');
            $table->date('fecha_fin_estimada');
            $table->decimal('precio_venta_final', 10, 2);
            $table->decimal('cuota_inicial', 10, 2)->default(0);
            $table->decimal('monto_financiado', 10, 2);
            $table->decimal('tasa_interes_anual', 5, 2)->default(0); // Porcentaje
            $table->integer('plazo_meses');
            $table->integer('dia_pago_mensual'); // 1-31
            
            // Estado y Totales
            $table->enum('estado', ['borrador', 'activo', 'completado', 'cancelado', 'mora', 'reposicion'])->default('borrador');
            $table->decimal('saldo_pendiente', 10, 2);
            $table->integer('cuotas_pagadas')->default(0);
            $table->integer('cuotas_totales')->default(0);
            $table->integer('cuotas_vencidas')->default(0);
            
            $table->text('observaciones')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['cliente_id', 'estado']);
            $table->index(['moto_unidad_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contratos');
    }
};
