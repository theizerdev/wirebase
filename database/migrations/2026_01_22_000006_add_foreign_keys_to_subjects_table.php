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
        Schema::table('subjects', function (Blueprint $table) {
            // Agregar claves foráneas
            //$table->foreign('program_id')->references('id')->on('programas')->onDelete('restrict');
           // $table->foreign('educational_level_id')->references('id')->on('niveles_educativos')->onDelete('restrict');
            //$table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
            //$table->foreign('updated_by')->references('id')->on('users')->onDelete('set null');
            
            // Agregar índices
           // $table->index('program_id');
            //$table->index('educational_level_id');
            //$table->index('is_active');
            //$table->index('created_by');
            //$table->index('updated_by');
            
            // Agregar constraint único para código
            //$table->unique('code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('subjects', function (Blueprint $table) {
            // Eliminar claves foráneas
            $table->dropForeign(['program_id']);
            $table->dropForeign(['educational_level_id']);
            $table->dropForeign(['created_by']);
            $table->dropForeign(['updated_by']);
            
            // Eliminar índices
            $table->dropIndex(['program_id']);
            $table->dropIndex(['educational_level_id']);
            $table->dropIndex(['is_active']);
            $table->dropIndex(['created_by']);
            $table->dropIndex(['updated_by']);
            
            // Eliminar constraint único
            $table->dropUnique(['code']);
        });
    }
};