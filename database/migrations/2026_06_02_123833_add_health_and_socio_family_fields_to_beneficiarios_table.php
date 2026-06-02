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
            // Step 4: Datos de Salud
            $table->boolean('tiene_evaluacion_antropometrica')->default(false);
            $table->string('evaluacion_realizada_por')->nullable();
            $table->enum('condicion_ingreso', [
                'Persona en situación de calle',
                'Mujer embarazada',
                'Desnutrición',
                'Adulto mayor sin recursos',
                'Incapacitado para trabajar',
                'Persona discapacitada',
                'Estudiante sin programa de estudio',
                'Desempleado'
            ])->nullable();
            $table->date('fecha_ingreso')->nullable();
            $table->boolean('padece_discapacidad_enfermedad')->default(false);
            $table->text('diagnostico')->nullable();
            $table->text('recipe_ayuda_tecnica')->nullable();
            
            // Step 5: Datos Socio-Familiares
            $table->integer('personas_nucleo_familiar')->default(0);
            $table->integer('ninos_niñas')->default(0);
            $table->integer('adolescentes')->default(0);
            $table->integer('mujeres')->default(0);
            $table->integer('hombres')->default(0);
            $table->integer('adultos_mayores')->default(0);
            $table->integer('mujeres_embarazadas')->default(0);
            $table->text('recipe_socio_familiar')->nullable();
            $table->boolean('es_mujer_embarazada')->default(false);
            $table->date('fecha_ultima_menstruacion')->nullable();
            $table->integer('edad_gestacion')->nullable();
            $table->text('observaciones')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('beneficiarios', function (Blueprint $table) {
            // Step 4: Datos de Salud
            $table->dropColumn('tiene_evaluacion_antropometrica');
            $table->dropColumn('evaluacion_realizada_por');
            $table->dropColumn('condicion_ingreso');
            $table->dropColumn('fecha_ingreso');
            $table->dropColumn('padece_discapacidad_enfermedad');
            $table->dropColumn('diagnostico');
            $table->dropColumn('recipe_ayuda_tecnica');
            
            // Step 5: Datos Socio-Familiares
            $table->dropColumn('personas_nucleo_familiar');
            $table->dropColumn('ninos_niñas');
            $table->dropColumn('adolescentes');
            $table->dropColumn('mujeres');
            $table->dropColumn('hombres');
            $table->dropColumn('adultos_mayores');
            $table->dropColumn('mujeres_embarazadas');
            $table->dropColumn('recipe_socio_familiar');
            $table->dropColumn('es_mujer_embarazada');
            $table->dropColumn('fecha_ultima_menstruacion');
            $table->dropColumn('edad_gestacion');
            $table->dropColumn('observaciones');
        });
    }
};
