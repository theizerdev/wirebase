<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->boolean('es_pago_mixto')->default(false)->after('metodo_pago');
            $table->json('detalles_pago_mixto')->nullable()->after('es_pago_mixto');
        });
    }

    public function down()
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropColumn(['es_pago_mixto', 'detalles_pago_mixto']);
        });
    }
};
