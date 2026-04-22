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
        Schema::create('sorteos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->nullable();
            $table->datetime('fecha_sorteo');
            $table->string('numero_contrato_ganador', 6);
            $table->string('hash_validacion', 64);
            $table->integer('total_contratos_elegibles');
            $table->foreignId('ejecutado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->enum('estado', ['completado', 'anulado'])->default('completado');
            $table->timestamps();

            $table->index(['empresa_id', 'fecha_sorteo']);
            $table->index('numero_contrato_ganador');
        });

        Schema::create('sorteo_contratos_ganadores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sorteo_id')->constrained('sorteos')->onDelete('cascade');
            $table->foreignId('contrato_id')->constrained('contratos')->onDelete('restrict');
            $table->string('numero_contrato', 6);
            $table->datetime('fecha_ganador');
            $table->string('hash_verificacion', 64);
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->timestamps();

            $table->unique(['numero_contrato', 'empresa_id']);
            $table->index('contrato_id');
        });

        Schema::create('sorteo_auditoria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sorteo_id')->nullable()->constrained('sorteos')->onDelete('cascade');
            $table->string('accion');
            $table->json('detalle')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->foreignId('ejecutado_por')->nullable()->constrained('users')->onDelete('set null');
            $table->foreignId('empresa_id')->constrained('empresas')->onDelete('cascade');
            $table->timestamps();

            $table->index('sorteo_id');
            $table->index(['empresa_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sorteo_auditoria');
        Schema::dropIfExists('sorteo_contratos_ganadores');
        Schema::dropIfExists('sorteos');
    }
};
