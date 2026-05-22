<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class ZonasPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Crear permisos de zonas
        $zonasPermissions = [
            'access zonas' => 'zonas',
            'view zonas' => 'zonas',
            'create zonas' => 'zonas',
            'edit zonas' => 'zonas',
            'delete zonas' => 'zonas',
        ];

        // Crear permisos que no existan
        foreach ($zonasPermissions as $permissionName => $module) {
            Permission::firstOrCreate(
                ['name' => $permissionName],
                ['module' => $module]
            );
        }

        // Obtener roles existentes
        $superAdminRole = Role::findByName('Super Administrador');
        $adminRole = Role::findByName('Administrador');
        
        // Asignar todos los permisos de zonas a Super Administrador y Administrador
        $allZonasPermissions = Permission::where('module', 'zonas')->get();
        
        if ($superAdminRole) {
            $superAdminRole->givePermissionTo($allZonasPermissions);
        }
        
        if ($adminRole) {
            $adminRole->givePermissionTo($allZonasPermissions);
        }

        $this->command->info('✅ Permisos de zonas creados exitosamente');
        $this->command->info('📊 Total de permisos de zonas: ' . count($zonasPermissions));
    }
}
