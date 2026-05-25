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
        Schema::table('inventario_iglesias', function (Blueprint $table) {
            $table->decimal('tasa_bcv', 10, 4)->nullable()->after('moneda')->comment('Tasa de cambio BCV al momento de la adquisición');
            $table->decimal('valor_bs', 15, 2)->nullable()->after('tasa_bcv')->comment('Valor en Bolívares según tasa BCV');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('inventario_iglesias', function (Blueprint $table) {
            $table->dropColumn(['tasa_bcv', 'valor_bs']);
        });
    }
};
