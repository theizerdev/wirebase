<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->foreignId('casa_alimentacion_id')
                ->nullable()
                ->after('responsable_id')
                ->constrained('casas_alimentacion')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->dropForeign(['casa_alimentacion_id']);
            $table->dropColumn('casa_alimentacion_id');
        });
    }
};
