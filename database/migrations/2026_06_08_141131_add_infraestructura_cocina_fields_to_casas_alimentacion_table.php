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
        Schema::table('casas_alimentacion', function (Blueprint $table) {
            // Infraestructura del área de la Cocina
            $table->enum('condicion_fachada', ['Pintada', 'Frisada', 'Sin Frisar'])->nullable()->after('observaciones_operatividad');

            $table->enum('condicion_area_cocina', ['Buena', 'Regular', 'Mala'])->nullable()->after('condicion_fachada');
            $table->enum('condicion_despensa', ['Buena', 'Regular', 'Mala'])->nullable()->after('condicion_area_cocina');
            $table->enum('condicion_cableado_electrico', ['Buena', 'Regular', 'Mala'])->nullable()->after('condicion_despensa');

            $table->enum('condicion_aguas_servidas', ['Buena', 'Regular', 'Mala'])->nullable()->after('condicion_cableado_electrico');
            $table->enum('condicion_agua_potable', ['Buena', 'Regular', 'Mala'])->nullable()->after('condicion_aguas_servidas');

            $table->enum('condicion_techo', ['Buena', 'Regular', 'Mala'])->nullable()->after('condicion_agua_potable');
            $table->enum('condicion_piso', ['Buena', 'Regular', 'Mala'])->nullable()->after('condicion_techo');
            $table->enum('condicion_paredes', ['Buena', 'Regular', 'Mala'])->nullable()->after('condicion_piso');

            $table->text('observaciones_infraestructura_cocina')->nullable()->after('condicion_paredes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('casas_alimentacion', function (Blueprint $table) {
            $table->dropColumn([
                'condicion_fachada',
                'condicion_area_cocina',
                'condicion_despensa',
                'condicion_cableado_electrico',
                'condicion_aguas_servidas',
                'condicion_agua_potable',
                'condicion_techo',
                'condicion_piso',
                'condicion_paredes',
                'observaciones_infraestructura_cocina',
            ]);
        });
    }
};
