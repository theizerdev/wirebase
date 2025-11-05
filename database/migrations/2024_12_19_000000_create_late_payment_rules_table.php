<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('late_payment_rules', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->enum('tipo', ['porcentaje', 'monto_fijo']);
            $table->decimal('valor', 8, 2);
            $table->integer('dias_gracia')->default(0);
            $table->boolean('activo')->default(true);
            $table->unsignedBigInteger('empresa_id');
            $table->unsignedBigInteger('sucursal_id');
            $table->timestamps();

            $table->foreign('empresa_id')->references('id')->on('empresas');
            $table->foreign('sucursal_id')->references('id')->on('sucursales');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('late_payment_rules');
    }
};