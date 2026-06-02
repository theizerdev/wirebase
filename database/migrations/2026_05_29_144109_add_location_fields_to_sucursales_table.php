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
        Schema::table('sucursales', function (Blueprint $table) {
            $table->unsignedBigInteger('estado_id')->nullable()->after('status');
            $table->unsignedBigInteger('municipio_id')->nullable()->after('estado_id');
            $table->unsignedBigInteger('parroquia_id')->nullable()->after('municipio_id');
            
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
        Schema::table('sucursales', function (Blueprint $table) {
            $table->dropForeign(['parroquia_id']);
            $table->dropForeign(['municipio_id']);
            $table->dropForeign(['estado_id']);
            
            $table->dropColumn(['parroquia_id', 'municipio_id', 'estado_id']);
        });
    }
};