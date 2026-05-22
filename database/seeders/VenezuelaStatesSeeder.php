<?php

namespace Database\Seeders;

use App\Models\Estado;
use App\Models\Pais;
use Illuminate\Database\Seeder;

class VenezuelaStatesSeeder extends Seeder
{
    /**
     * Run the database seeds for Venezuelan states.
     */
    public function run(): void
    {
        // Obtener el país Venezuela
        $venezuela = Pais::where('codigo_iso2', 'VE')->first();
        
        if (!$venezuela) {
            $this->command->error('❌ País Venezuela no encontrado. Ejecute primero PaisSeeder.');
            return;
        }

        // Estados de Venezuela con sus códigos oficiales
        $estados = [
            ['nombre' => 'Amazonas', 'codigo' => 'Z'],
            ['nombre' => 'Anzoátegui', 'codigo' => 'B'],
            ['nombre' => 'Apure', 'codigo' => 'C'],
            ['nombre' => 'Aragua', 'codigo' => 'D'],
            ['nombre' => 'Barinas', 'codigo' => 'E'],
            ['nombre' => 'Bolívar', 'codigo' => 'F'],
            ['nombre' => 'Carabobo', 'codigo' => 'G'],
            ['nombre' => 'Cojedes', 'codigo' => 'H'],
            ['nombre' => 'Delta Amacuro', 'codigo' => 'Y'],
            ['nombre' => 'Distrito Capital', 'codigo' => 'A'],
            ['nombre' => 'Falcón', 'codigo' => 'I'],
            ['nombre' => 'Guárico', 'codigo' => 'J'],
            ['nombre' => 'Lara', 'codigo' => 'K'],
            ['nombre' => 'Mérida', 'codigo' => 'L'],
            ['nombre' => 'Miranda', 'codigo' => 'M'],
            ['nombre' => 'Monagas', 'codigo' => 'N'],
            ['nombre' => 'Nueva Esparta', 'codigo' => 'O'],
            ['nombre' => 'Portuguesa', 'codigo' => 'P'],
            ['nombre' => 'Sucre', 'codigo' => 'R'],
            ['nombre' => 'Táchira', 'codigo' => 'S'],
            ['nombre' => 'Trujillo', 'codigo' => 'T'],
            ['nombre' => 'Vargas', 'codigo' => 'X'],
            ['nombre' => 'Yaracuy', 'codigo' => 'U'],
            ['nombre' => 'Zulia', 'codigo' => 'V']
        ];

        $createdCount = 0;
        
        foreach ($estados as $estadoData) {
            $estado = Estado::updateOrCreate(
                [
                    'nombre' => $estadoData['nombre'],
                    'pais_id' => $venezuela->id
                ],
                [
                    'nombre' => $estadoData['nombre'],
                    'codigo' => $estadoData['codigo'],
                    'pais_id' => $venezuela->id,
                    'activo' => true
                ]
            );
            
            $createdCount++;
        }

        $this->command->info('✅ Estados de Venezuela procesados exitosamente');
        $this->command->info("📊 Total de estados procesados: {$createdCount}");
    }
}