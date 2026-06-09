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
            // Paso 4: Factibilidad de espacio / Proyecto socio productivo
            $table->string('espacio_casa_alimentacion')->nullable(); // Propio | De uso comunal
            $table->string('ha_recibido_formacion_proyectos')->nullable(); // si | no
            $table->string('posee_proyecto_socio_productivo')->nullable(); // si | no
            $table->string('interesado_produccion_primaria')->nullable(); // si | no
            $table->string('cuenta_infraestructura_adecuada_proyecto')->nullable(); // si | no
            $table->decimal('metros_cuadrados_proyecto', 10, 2)->nullable();
            $table->text('observaciones_factibilidad_proyecto')->nullable();

            // Paso 5: Ficha técnica
            $table->string('encuestador_nombre')->nullable();
            $table->string('encuestador_telefono')->nullable();
            $table->string('tecnico_nombre')->nullable();
            $table->string('tecnico_telefono')->nullable();
            $table->string('transcriptor_nombre')->nullable();
            $table->string('transcriptor_telefono')->nullable();
            $table->text('observaciones_adicionales_ficha_tecnica')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('casas_alimentacion', function (Blueprint $table) {
            $table->dropColumn([
                // Paso 4
                'espacio_casa_alimentacion',
                'ha_recibido_formacion_proyectos',
                'posee_proyecto_socio_productivo',
                'interesado_produccion_primaria',
                'cuenta_infraestructura_adecuada_proyecto',
                'metros_cuadrados_proyecto',
                'observaciones_factibilidad_proyecto',

                // Paso 5
                'encuestador_nombre',
                'encuestador_telefono',
                'tecnico_nombre',
                'tecnico_telefono',
                'transcriptor_nombre',
                'transcriptor_telefono',
                'observaciones_adicionales_ficha_tecnica',
            ]);
        });
    }
};
