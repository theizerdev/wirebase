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
        Schema::create('tipo_locales', function (Blueprint $table) {
            $table->id();
            $table->string('nombre'); // Ej: "Propio", "Alquilado"
            $table->text('descripcion')->nullable();
            $table->boolean('status')->default(true);
            $table->timestamps();
            
            $table->index('status');
            $table->index('nombre');
        });

        // Insertar datos por defecto
        //$this->seedDefaultData();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tipo_locales');
    }

    /**
     * Seed default data
     */
    private function seedDefaultData(): void
    {
        $tipos = [
            [
                'nombre' => 'Propio',
                'descripcion' => 'Local propiedad de la iglesia/empresa',
                'status' => true
            ],
            [
                'nombre' => 'Alquilado',
                'descripcion' => 'Local alquilado o en arrendamiento',
                'status' => true
            ],
            [
                'nombre' => 'Comodato',
                'descripcion' => 'Local en comodato o préstamo',
                'status' => true
            ],
            [
                'nombre' => 'Compartido',
                'descripcion' => 'Local compartido con otra organización',
                'status' => true
            ]
        ];

        foreach ($tipos as $tipo) {
            \DB::table('tipo_locales')->insert($tipo);
        }
    }
};