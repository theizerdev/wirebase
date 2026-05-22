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
        Schema::table('zonas', function (Blueprint $table) {
            $table->string('distrito', 100)->nullable()->after('sucursal_id')
                  ->comment('Distrito o área geográfica de la zona');
            
            // Agregar índice para búsquedas por distrito
            $table->index('distrito');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('zonas', function (Blueprint $table) {
            $table->dropIndex(['distrito']);
            $table->dropColumn('distrito');
        });
    }
};
