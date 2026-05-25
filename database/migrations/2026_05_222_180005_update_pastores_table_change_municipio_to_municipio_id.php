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
        Schema::table('pastores', function (Blueprint $table) {
            // Primero eliminamos el campo municipio existente
            $table->dropColumn('municipio');

            // Luego agregamos el campo municipio_id como clave foránea
            $table->unsignedBigInteger('municipio_id')->nullable()->after('urbanizacion');
            $table->foreign('municipio_id')->references('id')->on('municipios')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pastores', function (Blueprint $table) {
            // Eliminar la clave foránea primero
            $table->dropForeign(['municipio_id']);

            // Eliminar el campo municipio_id
            $table->dropColumn('municipio_id');

            // Recrear el campo municipio original
            $table->string('municipio')->nullable()->after('urbanizacion');
        });
    }
};
