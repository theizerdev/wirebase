<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inventory_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('moto_unidad_id')->constrained('moto_unidades')->onDelete('cascade');
            $table->string('tipo'); // entrada, salida, transferencia
            $table->foreignId('origen_sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->foreignId('destino_sucursal_id')->nullable()->constrained('sucursales')->nullOnDelete();
            $table->foreignId('responsable_id')->nullable()->constrained('users')->nullOnDelete();
            $table->decimal('cantidad', 10, 2)->default(1);
            $table->text('observaciones')->nullable();
            $table->timestamp('occurred_at')->index();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_movements');
    }
};
