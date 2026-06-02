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
            ExchangeRateConfigSeeder::class, // Configuración de tasas de cambio por país
            EmpresaSeeder::class,
            SucursalSeeder::class,
            UsersTableSeeder::class,
            EstadosSeeder::class,
            MunicipiosSeeder::class,
            ParroquiasSeeder::class,
            ResponsablesPermissionsSeeder::class,
            BeneficiariosPermissionsSeeder::class,
        ]);
    }
}