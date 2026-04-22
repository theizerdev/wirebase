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
        if (!Schema::hasTable('pagos')) {
            Schema::create('pagos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('caja_id')->nullable()->constrained('cajas')->onDelete('set null');
                $table->foreignId('serie_id')->nullable()->constrained('series')->onDelete('set null');
                $table->string('serie')->nullable();
                $table->string('numero');
                $table->string('numero_completo')->nullable(); // Para búsqueda rápida serie-numero
                $table->string('tipo_pago')->default('recibo'); // factura, boleta, nota_credito, recibo
                $table->date('fecha');
                $table->foreignId('cliente_id')->nullable()->constrained('clientes')->onDelete('set null');
                $table->foreignId('user_id')->constrained('users'); // Usuario que registra
                $table->decimal('subtotal', 10, 2)->default(0);
                $table->decimal('descuento', 10, 2)->default(0);
                $table->decimal('total', 10, 2);
                $table->decimal('tasa_cambio', 10, 4)->nullable(); // Tasa del día si aplica
                $table->decimal('total_bolivares', 10, 2)->nullable();
                
                // Detalles de método de pago
                $table->string('metodo_pago')->nullable(); // efectivo, transferencia, pago_movil, mixto
                $table->string('referencia')->nullable();
                $table->boolean('es_pago_mixto')->default(false);
                $table->json('detalles_pago_mixto')->nullable();
                
                $table->string('estado')->default('pendiente'); // pendiente, aprobado, cancelado
                $table->text('observaciones')->nullable();
                
                // Multitenancy
                $table->foreignId('empresa_id')->constrained('empresas');
                $table->foreignId('sucursal_id')->nullable()->constrained('sucursales');
                
                $table->timestamps();
                $table->softDeletes();
            });
        }
        
        // Tabla de detalles del pago
        if (!Schema::hasTable('pago_detalles')) {
            Schema::create('pago_detalles', function (Blueprint $table) {
                $table->id();
                $table->foreignId('pago_id')->constrained('pagos')->onDelete('cascade');
                $table->foreignId('concepto_pago_id')->nullable()->constrained('conceptos_pago')->onDelete('set null');
                $table->string('descripcion');
                $table->decimal('cantidad', 10, 2)->default(1);
                $table->decimal('precio_unitario', 10, 2);
                $table->decimal('subtotal', 10, 2);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pago_detalles');
        Schema::dropIfExists('pagos');
    }
};
