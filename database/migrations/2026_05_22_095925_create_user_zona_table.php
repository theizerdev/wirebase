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
        Schema::create('user_zona', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('zona_id')->constrained('zonas')->onDelete('cascade');
            $table->timestamps();
            
            // Prevenir duplicados
            $table->unique(['user_id', 'zona_id']);
            
            // Índices para optimizar consultas
            $table->index('user_id');
            $table->index('zona_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_zona');
    }
};
