<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Empleados
        if (!Schema::hasTable('empleados')) {
            Schema::create('empleados', function (Blueprint $table) {
                $table->id();
                $table->foreignId('empresa_id')->constrained('empresas');
                $table->foreignId('sucursal_id')->constrained('sucursales');
                $table->string('nombre');
                $table->string('apellido')->nullable();
                $table->string('documento')->nullable();
                $table->string('puesto')->nullable();
                $table->decimal('salario_base', 12, 2)->default(0);
                $table->string('metodo_pago')->nullable();
                $table->string('telefono')->nullable();
                $table->string('email')->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        // Conceptos de Nómina
        if (!Schema::hasTable('conceptos_nomina')) {
            Schema::create('conceptos_nomina', function (Blueprint $table) {
                $table->id();
                $table->foreignId('empresa_id')->constrained('empresas');
                $table->string('nombre');
                $table->enum('tipo', ['percepcion', 'deduccion']);
                $table->decimal('porcentaje', 8, 4)->nullable();
                $table->decimal('monto_fijo', 12, 2)->nullable();
                $table->boolean('activo')->default(true);
                $table->timestamps();
            });
        }

        // Calendarios de Pago de Nómina
        if (!Schema::hasTable('calendarios_nomina')) {
            Schema::create('calendarios_nomina', function (Blueprint $table) {
                $table->id();
                $table->foreignId('empresa_id')->constrained('empresas');
                $table->string('nombre');
                $table->enum('frecuencia', ['semanal', 'quincenal', 'mensual'])->default('mensual');
                $table->date('periodo_inicio');
                $table->date('periodo_fin');
                $table->enum('estado', ['borrador', 'precalculada', 'aprobada', 'cerrada'])->default('borrador');
                $table->timestamps();
            });
        }

        // Nóminas
        if (!Schema::hasTable('nominas')) {
            Schema::create('nominas', function (Blueprint $table) {
                $table->id();
                $table->foreignId('empresa_id')->constrained('empresas');
                $table->foreignId('sucursal_id')->constrained('sucursales');
                $table->foreignId('calendario_id')->constrained('calendarios_nomina');
                $table->date('periodo_inicio');
                $table->date('periodo_fin');
                $table->enum('estado', ['borrador', 'precalculada', 'aprobada', 'cerrada'])->default('borrador');
                $table->decimal('total', 14, 2)->default(0);
                $table->timestamps();
            });
        }

        // Detalles de Nómina
        if (!Schema::hasTable('nomina_items')) {
            Schema::create('nomina_items', function (Blueprint $table) {
                $table->id();
                $table->foreignId('nomina_id')->constrained('nominas')->cascadeOnDelete();
                $table->foreignId('empleado_id')->constrained('empleados');
                $table->string('concepto_nombre');
                $table->enum('tipo', ['percepcion', 'deduccion']);
                $table->decimal('cantidad', 12, 2)->default(1);
                $table->decimal('monto_unitario', 12, 2)->default(0);
                $table->decimal('subtotal', 12, 2)->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('nomina_items');
        Schema::dropIfExists('nominas');
        Schema::dropIfExists('calendarios_nomina');
        Schema::dropIfExists('conceptos_nomina');
        Schema::dropIfExists('empleados');
    }
};
