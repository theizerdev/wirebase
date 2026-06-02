<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class BeneficiariosPermissionsSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            'view beneficiarios',
            'create beneficiarios',
            'edit beneficiarios',
            'delete beneficiarios',
            'access beneficiarios',
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }
    }
}
