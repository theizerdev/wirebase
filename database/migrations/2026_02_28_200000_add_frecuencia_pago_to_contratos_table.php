<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->enum('frecuencia_pago', ['semanal', 'quincenal', 'mensual'])
                  ->default('mensual')
                  ->after('dia_pago_mensual');
        });
    }

    public function down(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->dropColumn('frecuencia_pago');
        });
    }
};
