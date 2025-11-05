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
        Schema::table('cajas', function (Blueprint $table) {
            // Primero eliminamos las claves foráneas que puedan depender del índice
            $table->dropForeign(['empresa_id']);
            $table->dropForeign(['sucursal_id']);

            // Ahora podemos eliminar la restricción única
            $table->dropUnique(['empresa_id', 'sucursal_id', 'fecha']);

            // Volvemos a agregar las claves foráneas
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cajas', function (Blueprint $table) {
            // Eliminar las claves foráneas
            $table->dropForeign(['empresa_id']);
            $table->dropForeign(['sucursal_id']);

            // Volver a agregar la restricción única
            $table->unique(['empresa_id', 'sucursal_id', 'fecha']);

            // Volver a agregar las claves foráneas
            $table->foreign('empresa_id')->references('id')->on('empresas')->onDelete('cascade');
            $table->foreign('sucursal_id')->references('id')->on('sucursales')->onDelete('cascade');
        });
    }
};
