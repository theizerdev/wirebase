<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create demo users
          $office =  User::create([
                    'name' => 'Rebeca Gutiérrez',
                    'username' => 'rgutierrez',
                    'email' => 'rebeca@example.com',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'empresa_id' => 1,
                    'sucursal_id' => 1,
        ]);


        $office->assignRole('Oficina Nacional');

         $adminUser =  User::create([
                    'name' => 'Usuario Administrador',
                    'username' => 'administrador',
                    'email' => 'admin@example.com',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                    'empresa_id' => 1,
                    'sucursal_id' => 1,
        ]);


        $adminUser->assignRole('Administrador');
    }
}
