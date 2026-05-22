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
        // Primero eliminar la clave foránea existente
        Schema::table('municipios', function (Blueprint $table) {
            $table->dropForeign(['ciudad_id']);
        });
        
        // Renombrar la columna ciudad_id a estado_id
        Schema::table('municipios', function (Blueprint $table) {
            $table->renameColumn('ciudad_id', 'estado_id');
        });
        
        // Agregar la nueva clave foránea a la tabla estados
        Schema::table('municipios', function (Blueprint $table) {
            $table->foreign('estado_id')->references('id')->on('estados')->onDelete('cascade');
            $table->index(['estado_id', 'activo']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revertir los cambios
        Schema::table('municipios', function (Blueprint $table) {
            $table->dropForeign(['estado_id']);
            $table->dropIndex(['estado_id', 'activo']); // Eliminar índice
        });
        
        Schema::table('municipios', function (Blueprint $table) {
            $table->renameColumn('estado_id', 'ciudad_id');
        });
        
        Schema::table('municipios', function (Blueprint $table) {
            $table->foreign('ciudad_id')->references('id')->on('ciudades')->onDelete('cascade');
            $table->index(['ciudad_id', 'activo']);
        });
    }
};