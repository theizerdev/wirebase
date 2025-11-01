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
        Schema::table('matriculas', function (Blueprint $table) {
            // Primero hacemos nullable el campo
            //DB::statement('ALTER TABLE matriculas MODIFY school_periods_id BIGINT UNSIGNED NULL');
            // Luego agregamos la restricción de clave foránea
            //$table->foreign('school_periods_id')->references('id')->on('school_periods');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('matriculas', function (Blueprint $table) {
            //
        });
    }
};
