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
        Schema::table('beneficiarios', function (Blueprint $table) {
            // Check if columns already exist to avoid duplicate column errors
            if (!Schema::hasColumn('beneficiarios', 'tiene_habilidad_productiva')) {
                $table->boolean('tiene_habilidad_productiva')->default(false);
            }
            if (!Schema::hasColumn('beneficiarios', 'habilidad_productiva')) {
                $table->string('habilidad_productiva')->nullable();
            }
            if (!Schema::hasColumn('beneficiarios', 'pertenece_organizacion_social')) {
                $table->boolean('pertenece_organizacion_social')->default(false);
            }
            if (!Schema::hasColumn('beneficiarios', 'organizacion_social')) {
                $table->string('organizacion_social')->nullable();
            }
            if (!Schema::hasColumn('beneficiarios', 'asignaciones_economicas')) {
                $table->json('asignaciones_economicas')->nullable(); // Para almacenar múltiples selecciones
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiarios', function (Blueprint $table) {
            $table->dropColumn([
                'tiene_habilidad_productiva',
                'habilidad_productiva',
                'pertenece_organizacion_social',
                'organizacion_social',
                'asignaciones_economicas'
            ]);
        });
    }
};
