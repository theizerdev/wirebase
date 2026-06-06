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
            // Sello e identificación
            $table->boolean('posee_sello')->default(false)->after('motivo_inoperatividad');
            $table->boolean('posee_listado_beneficiarios')->default(false)->after('posee_sello');
            $table->integer('num_beneficiarios_registrados')->nullable()->after('posee_listado_beneficiarios');
            $table->integer('num_promedio_diario_beneficiarios')->nullable()->after('num_beneficiarios_registrados');
            $table->boolean('posee_identificacion_fundaproal')->default(false)->after('num_promedio_diario_beneficiarios');
            $table->boolean('identificacion_visible')->nullable()->default(false)->after('posee_identificacion_fundaproal');
            $table->boolean('posee_cartelera_informativa')->default(false)->after('identificacion_visible');

            // Certificados y equipamiento personal
            $table->boolean('posee_certificado_manipulacion_alimentos')->default(false)->after('posee_cartelera_informativa');
            $table->boolean('posee_certificado_salud')->default(false)->after('posee_certificado_manipulacion_alimentos');
            $table->boolean('posee_gorros_delantales')->default(false)->after('posee_certificado_salud');

            // Suministro de gas
            $table->boolean('recibe_suministro_gas')->default(false)->after('posee_gorros_delantales');
            $table->integer('cantidad_reguladores_kg')->nullable()->after('recibe_suministro_gas');
            $table->integer('bombonas_propias_cantidad')->nullable()->default(0)->after('cantidad_reguladores_kg');
            $table->integer('bombonas_prestadas_cantidad')->nullable()->default(0)->after('bombonas_propias_cantidad');

            // Manipulación de alimentos
            $table->boolean('manipulacion_alimentos_adecuada')->default(false)->after('bombonas_prestadas_cantidad');
            $table->integer('dias_preparacion_semana')->nullable()->after('manipulacion_alimentos_adecuada');

            // Personas itinerantes
            $table->integer('itinerantes_femeninos')->nullable()->default(0)->after('dias_preparacion_semana');
            $table->integer('itinerantes_masculinos')->nullable()->default(0)->after('itinerantes_femeninos');
            $table->integer('itinerantes_menores_masculinos')->nullable()->default(0)->after('itinerantes_masculinos');
            $table->integer('itinerantes_menores_femeninos')->nullable()->default(0)->after('itinerantes_menores_masculinos');

            // Cocina
            $table->enum('condicion_cocina', ['Operativa', 'Inoperativa', 'No posee'])->nullable()->after('itinerantes_menores_femeninos');
            $table->enum('tipo_cocina', ['Industrial', 'Domestica', 'Fogon/Reverbero'])->nullable()->after('condicion_cocina');
            $table->enum('dominio_cocina', ['Propia', 'Fundaproal', 'Prestada'])->nullable()->after('tipo_cocina');

            // Nevera
            $table->enum('condicion_nevera', ['Operativa', 'Inoperativa', 'No posee'])->nullable()->after('dominio_cocina');

            // Congelador
            $table->enum('dominio_congelador', ['Propia', 'Fundaproal', 'Prestada'])->nullable()->after('condicion_nevera');
            $table->enum('condicion_congelador', ['Operativa', 'Inoperativa', 'No posee'])->nullable()->after('dominio_congelador');

            // Utensilios
            $table->enum('estatus_utensilios', ['Buenos', 'Regular', 'Malos'])->nullable()->after('condicion_congelador');

            // Mobiliario
            $table->boolean('posee_meson')->default(false)->after('estatus_utensilios');
            $table->boolean('posee_fregadero')->default(false)->after('posee_meson');
            $table->boolean('posee_tanque_agua')->default(false)->after('posee_fregadero');
            $table->boolean('posee_estante_almacenamiento')->default(false)->after('posee_tanque_agua');

            $table->text('observaciones_operatividad')->nullable()->after('posee_estante_almacenamiento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('casas_alimentacion', function (Blueprint $table) {
            $table->dropColumn([
                'posee_sello',
                'posee_listado_beneficiarios',
                'num_beneficiarios_registrados',
                'num_promedio_diario_beneficiarios',
                'posee_identificacion_fundaproal',
                'identificacion_visible',
                'posee_cartelera_informativa',
                'posee_certificado_manipulacion_alimentos',
                'posee_certificado_salud',
                'posee_gorros_delantales',
                'recibe_suministro_gas',
                'cantidad_reguladores_kg',
                'bombonas_propias_cantidad',
                'bombonas_prestadas_cantidad',
                'manipulacion_alimentos_adecuada',
                'dias_preparacion_semana',
                'itinerantes_femeninos',
                'itinerantes_masculinos',
                'itinerantes_menores_masculinos',
                'itinerantes_menores_femeninos',
                'condicion_cocina',
                'tipo_cocina',
                'dominio_cocina',
                'condicion_nevera',
                'dominio_congelador',
                'condicion_congelador',
                'estatus_utensilios',
                'posee_meson',
                'posee_fregadero',
                'posee_tanque_agua',
                'posee_estante_almacenamiento',
                'observaciones_operatividad',
            ]);
        });
    }
};
