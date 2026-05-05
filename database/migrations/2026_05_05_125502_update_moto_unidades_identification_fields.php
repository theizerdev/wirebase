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
        Schema::table('moto_unidades', function (Blueprint $table) {
            // Eliminar las restricciones unique existentes
            $table->dropUnique(['vin']);
            $table->dropUnique(['numero_motor']);
            
            // Hacer los campos nullable
            $table->string('vin', 50)->nullable()->change();
            $table->string('numero_motor', 50)->nullable()->change();
            $table->string('numero_chasis', 50)->nullable()->change();
            $table->string('placa', 20)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('moto_unidades', function (Blueprint $table) {
            // Revertir a no nullable
            $table->string('vin', 50)->nullable(false)->change();
            $table->string('numero_motor', 50)->nullable(false)->change();
            
            // Volver a agregar las restricciones unique
            $table->unique('vin');
            $table->unique('numero_motor');
        });
    }
};
