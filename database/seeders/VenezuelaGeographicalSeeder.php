<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class VenezuelaGeographicalSeeder extends Seeder
{
    /**
     * Run the database seeds for Venezuelan geographical entities.
     */
    public function run(): void
    {
        $this->call([
            EstadosSeeder::class,
            MunicipiosSeeder::class,
            ParroquiasSeeder::class,
            CiudadesSeeder::class,
        ]);

        $this->command->info('✅ Todos los datos geográficos de Venezuela han sido procesados exitosamente');
    }
}