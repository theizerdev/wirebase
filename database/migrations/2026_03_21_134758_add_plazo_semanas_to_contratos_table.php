<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->integer('plazo_semanas')->after('tasa_interes_anual')->default(0);
        });

        // Migrar datos existentes: convertir meses a semanas (meses * 4)
        DB::table('contratos')->update([
            'plazo_semanas' => DB::raw('plazo_meses * 4')
        ]);
    }

    public function down(): void
    {
        Schema::table('contratos', function (Blueprint $table) {
            $table->dropColumn('plazo_semanas');
        });
    }
};
