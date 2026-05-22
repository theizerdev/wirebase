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
            VenezuelaStatesSeeder::class,
            VenezuelaMunicipalitiesSeeder::class,
            VenezuelaParishesSeeder::class,
            VenezuelaMigrationsSeeder::class,
        ]);

        $this->command->info('✅ Todos los datos geográficos de Venezuela han sido procesados exitosamente');
    }
}