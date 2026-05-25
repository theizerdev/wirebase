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
        // Agregar iglesia_id a cuentas_contables
        if (!Schema::hasColumn('cuentas_contables', 'iglesia_id')) {
            Schema::table('cuentas_contables', function (Blueprint $table) {
                $table->foreignId('iglesia_id')->nullable()->after('sucursal_id')->constrained('iglesias')->nullOnDelete();
            });
        }

        // Modificar índice unique para que sea por iglesia
        try {
            Schema::table('cuentas_contables', function (Blueprint $table) {
                $table->dropUnique(['codigo']);
            });
        } catch (\Exception $e) {
            // Ignorar si no existe
        }

        try {
            Schema::table('cuentas_contables', function (Blueprint $table) {
                $table->unique(['codigo', 'iglesia_id']);
            });
        } catch (\Exception $e) {
            // Ignorar si ya existe
        }

        // Agregar iglesia_id a asientos_contables
        if (!Schema::hasColumn('asientos_contables', 'iglesia_id')) {
            Schema::table('asientos_contables', function (Blueprint $table) {
                $table->foreignId('iglesia_id')->nullable()->after('sucursal_id')->constrained('iglesias')->nullOnDelete();
            });
        }

        // Crear tabla transacciones_financieras si no existe
        if (!Schema::hasTable('transacciones_financieras')) {
        Schema::create('transacciones_financieras', function (Blueprint $table) {
            $table->id();
            $table->string('numero_comprobante', 50)->unique();
            $table->date('fecha');
            $table->enum('tipo', [
                'diezmo',
                'ofrenda',
                'aporte_especial',
                'gasto_operativo',
                'gasto_ministerio',
                'otro_ingreso',
                'otro_egreso'
            ]);
            $table->string('categoria')->nullable(); // Subcategoría personalizada
            $table->text('descripcion')->nullable();
            $table->decimal('monto', 15, 2);
            $table->string('moneda', 3)->default('VES'); // VES o USD
            $table->decimal('tasa_cambio', 10, 4)->nullable(); // Tasa BCV del día
            $table->decimal('monto_bs', 15, 2)->nullable(); // Monto convertido a Bs
            $table->enum('metodo_pago', ['efectivo', 'transferencia', 'punto_venta', 'cheque', 'otro'])->default('efectivo');
            $table->string('referencia_bancaria')->nullable(); // Número de referencia/transferencia
            $table->foreignId('cuenta_origen_id')->nullable()->constrained('cuentas_contables')->nullOnDelete();
            $table->foreignId('cuenta_destino_id')->nullable()->constrained('cuentas_contables')->nullOnDelete();
            $table->foreignId('iglesia_id')->constrained('iglesias')->onDelete('cascade');
            $table->foreignId('empresa_id')->nullable()->constrained('empresas')->nullOnDelete();
            $table->foreignId('sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('asiento_contable_id')->nullable()->constrained('asientos_contables')->nullOnDelete();
            $table->boolean('conciliado')->default(false);
            $table->date('fecha_conciliacion')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['fecha', 'tipo']);
            $table->index(['iglesia_id', 'fecha']);
            $table->index(['iglesia_id', 'tipo']);
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transacciones_financieras');

        Schema::table('asientos_contables', function (Blueprint $table) {
            $table->dropForeign(['iglesia_id']);
            $table->dropColumn('iglesia_id');
        });

        Schema::table('cuentas_contables', function (Blueprint $table) {
            $table->dropForeign(['iglesia_id']);
            $table->dropColumn('iglesia_id');
        });
    }
};
