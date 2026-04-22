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
            RolesAndPermissionsSeeder::class,
            WhatsAppPermissionsSeeder::class, // Agregar permisos específicos de WhatsApp
            PaisSeeder::class, // Agregar países antes que empresas
            EmpresaSeeder::class,
            SucursalSeeder::class,
            UsersTableSeeder::class,
            SerieSeeder::class,
            ConceptoPagoMejoradoSeeder::class,
            MotosSeeder::class, // Agregar motos
            ClientesSeeder::class, // Agregar clientes
            MotoUnidadesSeeder::class, // Agregar inventario de unidades
            ContratosSeeder::class, // Agregar contratos con planes de pago
        ]);
    }
}