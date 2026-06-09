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
        Schema::table('responsables', function (Blueprint $table) {
            if (!Schema::hasColumn('responsables', 'casa_alimentacion_id')) {
                $table->unsignedBigInteger('casa_alimentacion_id')->nullable()->after('codigo_casa_alimentacion');

                $table->foreign('casa_alimentacion_id')
                    ->references('id')
                    ->on('casas_alimentacion')
                    ->onDelete('set null');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('responsables', function (Blueprint $table) {
            if (Schema::hasColumn('responsables', 'casa_alimentacion_id')) {
                $table->dropForeign(['casa_alimentacion_id']);
                $table->dropColumn('casa_alimentacion_id');
            }
        });
    }
};
