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
        Schema::table('solicitud_modificacion_pastores', function (Blueprint $table) {
            // Agregar columna para registrar quién aprobó la solicitud
            $table->foreignId('aprobado_por')->nullable()->after('estado')->constrained('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('solicitud_modificacion_pastores', function (Blueprint $table) {
            $table->dropForeign(['aprobado_por']);
            $table->dropColumn('aprobado_por');
        });
    }
};
