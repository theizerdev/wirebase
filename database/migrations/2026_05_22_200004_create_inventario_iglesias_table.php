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
        Schema::create('inventario_iglesias', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('iglesia_id');
            $table->string('nombre');
            $table->text('descripcion')->nullable();
            $table->string('categoria')->nullable(); // Mobiliario, equipo electrónico, vehículo, etc.
            $table->integer('cantidad')->default(1);
            $table->decimal('valor', 15, 2)->nullable();
            $table->string('moneda')->default('USD');
            $table->date('fecha_adquisicion')->nullable();
            $table->string('condicion')->nullable(); // Nuevo, usado, dañado, etc.
            $table->text('notas')->nullable();
            $table->unsignedBigInteger('usuario_registro_id');
            $table->timestamps();
            
            $table->foreign('iglesia_id')->references('id')->on('iglesias')->onDelete('cascade');
            $table->foreign('usuario_registro_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inventario_iglesias');
    }
};