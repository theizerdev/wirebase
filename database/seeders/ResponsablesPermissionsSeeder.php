<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ResponsablesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Definir permisos para el módulo de Responsables
        $responsablesPermissions = [
            'view responsables' => 'responsables',
            'create responsables' => 'responsables',
            'edit responsables' => 'responsables',
            'delete responsables' => 'responsables',
            'access responsables' => 'responsables',
        ];

        // Crear permisos que no existan
        foreach ($responsablesPermissions as $permissionName => $module) {
            Permission::firstOrCreate(
                ['name' => $permissionName],
                ['module' => $module]
            );
        }

        // Obtener roles existentes
        $superAdminRole = Role::findByName('Super Administrador');
        $adminRole = Role::findByName('Administrador');
        
        // Asignar todos los permisos de Responsables a Super Administrador
        $allResponsablesPermissions = Permission::where('module', 'responsables')->get();
        $superAdminRole->givePermissionTo($allResponsablesPermissions);
        
        // Asignar permisos de Responsables a Administrador
        $adminRole->givePermissionTo($allResponsablesPermissions);
    }
}