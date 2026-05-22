<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Nuevo sistema de roles y permisos por sectores
            SectorRolesAndPermissionsSeeder::class,
            PaisSeeder::class, // Agregar países antes que empresas
            VenezuelaGeographicalSeeder::class, // Datos geográficos de Venezuela
            ExchangeRateConfigSeeder::class, // Configuración de tasas de cambio por país
            EmpresaSeeder::class,
            SucursalSeeder::class,
            UsersTableSeeder::class,
            ZonaSeeder::class,
            ZonasPermissionsSeeder::class,
            UbicacionPermissionsSeeder::class,
            VenezuelaGeographicalSeeder::class,
        ]);
    }
}