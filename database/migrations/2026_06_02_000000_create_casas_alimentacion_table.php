<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('casas_alimentacion', function (Blueprint $table) {
            $table->id();
            $table->date('fecha')->nullable();
            $table->string('codigo')->unique()->index();
            $table->unsignedBigInteger('estado_id')->nullable()->index();
            $table->unsignedBigInteger('municipio_id')->nullable()->index();
            $table->unsignedBigInteger('parroquia_id')->nullable()->index();
            $table->string('sector')->nullable();
            $table->string('calle_avenida')->nullable();
            $table->string('numero_vivienda')->nullable();
            $table->string('punto_referencia')->nullable();
            $table->decimal('longitud', 12, 8)->nullable();
            $table->decimal('latitud', 11, 8)->nullable();
            $table->boolean('zona_base_misiones')->default(false);
            $table->decimal('distancia_a_base_misiones', 8, 2)->nullable();
            $table->string('consejo_comunal')->nullable();
            $table->string('vocero_alimentacion')->nullable();
            $table->string('telefono_principal')->nullable();
            $table->string('telefono_secundario')->nullable();
            $table->enum('estado_cda', ['Operativa', 'Inoperativa', 'Inactiva'])->default('Operativa');
            $table->text('motivo_inoperatividad')->nullable();
            $table->timestamps();

            // Foreign key constraints
            $table->foreign('estado_id')->references('id')->on('estados')->onDelete('set null');
            $table->foreign('municipio_id')->references('id')->on('municipios')->onDelete('set null');
            $table->foreign('parroquia_id')->references('id')->on('parroquias')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('casas_alimentacion');
    }
};
