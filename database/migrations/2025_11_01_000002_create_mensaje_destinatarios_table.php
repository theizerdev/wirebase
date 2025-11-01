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
        Schema::create('mensaje_destinatarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('mensaje_id')->constrained('mensajes')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->boolean('leido')->default(false);
            $table->timestamp('leido_en')->nullable();
            $table->boolean('archivado')->default(false);
            $table->timestamp('archivado_en')->nullable();
            $table->boolean('respondido')->default(false);
            $table->timestamp('respondido_en')->nullable();
            $table->timestamps();
            
            $table->unique(['mensaje_id', 'user_id']);
            $table->index(['user_id', 'leido']);
            $table->index(['user_id', 'archivado']);
            $table->index('mensaje_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mensaje_destinatarios');
    }
};