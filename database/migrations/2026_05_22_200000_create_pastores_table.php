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
        Schema::create('pastores', function (Blueprint $table) {
            $table->id();
            $table->string('codigo')->unique(); // codigo
            $table->string('nombres'); // nombres
            $table->string('apellidos'); // apellidos
            $table->string('documento')->unique(); // documento
            $table->string('nivel_ministerial')->nullable(); // grado ministerial
            $table->string('zona')->nullable(); // zona
            $table->string('distrito')->nullable(); // distrito
            $table->string('genero')->nullable(); // genero
            $table->integer('edad')->nullable(); // edad
            $table->string('ano_promocion')->nullable(); // ano_promocion
            $table->string('tiempo_colaborando')->nullable(); // tiempo_colaborando
            $table->date('fe_nacimiento')->nullable(); // fe_nacimiento
            $table->string('foto')->nullable(); // foto
            $table->text('nota')->nullable(); // nota
            $table->boolean('status')->default(true); // status
            $table->string('estado_civil')->nullable(); // estado_civil
            $table->boolean('batizado_espiritu_santo')->default(false); // batizado_espiritu_santo
            $table->string('grado_instruccion')->nullable(); // grado_instruccion
            $table->string('titulo_obtenido')->nullable(); // titulo_obtenido
            $table->boolean('estudio_teologico')->default(false); // estudio_teologico
            $table->string('titulo_teologico')->nullable(); // titulo_teologico
            $table->string('tiempo_de_estudio_teologico')->nullable(); // tiempo_de_estudio_teologico
            $table->string('instituto_teologico')->nullable(); // instituto_teologico
            $table->boolean('pertenece_ministerio')->default(false); // pertenece_ministerio
            $table->string('nombre_conyuge')->nullable(); // nombre_conyuge
            $table->string('edificio_casa_quinta')->nullable(); // edificio_casa_quinta
            $table->string('piso')->nullable(); // piso
            $table->string('apartamento')->nullable(); // apartamento
            $table->string('calle_avenida')->nullable(); // calle_avenida
            $table->string('urbanizacion')->nullable(); // urbanizacion
            $table->string('telefono_hab')->nullable(); // telefono_hab
            $table->string('telefono_tlf')->nullable(); // telefono_tlf
            $table->string('telefono_otro')->nullable(); // telefono_otro
            $table->text('mencion')->nullable(); // mencion
            $table->text('municipio')->nullable(); // municipio
            $table->string('cargo_nacional')->nullable(); // cargo_nacional

            // Relaciones
            $table->unsignedBigInteger('user_id')->nullable(); // Relación con usuario del sistema
            $table->unsignedBigInteger('ciudad_id')->nullable(); // Relación con ciudad
            $table->unsignedBigInteger('estado_id')->nullable(); // Relación con estado
            $table->unsignedBigInteger('parroquia_id')->nullable(); // Relación con parroquia


            $table->timestamps();

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pastores');
    }
};
