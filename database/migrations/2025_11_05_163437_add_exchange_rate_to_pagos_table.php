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
        Schema::table('pagos', function (Blueprint $table) {
            $table->decimal('tasa_cambio', 10, 4)->nullable()->after('total');
            $table->decimal('total_bolivares', 10, 2)->nullable()->after('tasa_cambio');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropColumn(['tasa_cambio', 'total_bolivares']);
        });
    }
};
