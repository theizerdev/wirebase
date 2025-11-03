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
        Schema::create('biblioteca_archivos', function (Blueprint $table) {
            $table->id();
            $table->string('titulo');
            $table->text('descripcion')->nullable();
            $table->string('nombre_archivo');
            $table->string('ruta_archivo');
            $table->unsignedBigInteger('tamaño');
            $table->string('tipo_mime');
            $table->foreignId('categoria_id')->nullable()->constrained('biblioteca_categorias')->onDelete('set null');
            $table->foreignId('usuario_subida_id')->constrained('users');
            $table->unsignedBigInteger('descargas')->default(0);
            $table->enum('visibilidad', ['publico', 'privado', 'restringido'])->default('privado');
            $table->foreignId('empresa_id')->constrained('empresas');
            $table->foreignId('sucursal_id')->constrained('sucursales');
            $table->json('etiquetas')->nullable();
            $table->timestamps();
            
            $table->index(['empresa_id', 'sucursal_id']);
            $table->index('categoria_id');
            $table->index('usuario_subida_id');
            $table->index('visibilidad');
            $table->index('descargas');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('biblioteca_archivos');
    }
};