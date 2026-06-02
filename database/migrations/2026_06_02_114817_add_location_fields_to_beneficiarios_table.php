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
        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->unsignedBigInteger('estado_id')->nullable();
            $table->unsignedBigInteger('municipio_id')->nullable();
            $table->unsignedBigInteger('parroquia_id')->nullable();
            
            // Agregar las claves foráneas
            $table->foreign('estado_id')->references('id')->on('estados')->onDelete('set null');
            $table->foreign('municipio_id')->references('id')->on('municipios')->onDelete('set null');
            $table->foreign('parroquia_id')->references('id')->on('parroquias')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->dropForeign(['estado_id']);
            $table->dropForeign(['municipio_id']);
            $table->dropForeign(['parroquia_id']);
            
            $table->dropColumn([
                'estado_id',
                'municipio_id',
                'parroquia_id'
            ]);
        });
    }
};