<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class ExchangeRatesEditPermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Create the edit exchange-rates permission if it doesn't exist
        $permission = Permission::firstOrCreate(
            ['name' => 'edit exchange-rates'],
            ['module' => 'exchange_rates']
        );

        // Assign to Super Admin and Admin roles
        $superAdminRole = Role::where('name', 'Super Administrador')->first();
        $adminRole = Role::where('name', 'Administrador')->first();

        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($permission);
        }

        if ($adminRole) {
            $adminRole->givePermissionTo($permission);
        }
    }
}