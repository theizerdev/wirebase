<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class UbicacionPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permisos para Estados
        $permisosEstados = [
            ['name' => 'access estados', 'module' => 'estados', 'sector' => 'configuracion'],
            ['name' => 'view estados', 'module' => 'estados', 'sector' => 'configuracion'],
            ['name' => 'create estados', 'module' => 'estados', 'sector' => 'configuracion'],
            ['name' => 'edit estados', 'module' => 'estados', 'sector' => 'configuracion'],
            ['name' => 'delete estados', 'module' => 'estados', 'sector' => 'configuracion'],
        ];

        // Permisos para Ciudades
        $permisosCiudades = [
            ['name' => 'access ciudades', 'module' => 'ciudades', 'sector' => 'configuracion'],
            ['name' => 'view ciudades', 'module' => 'ciudades', 'sector' => 'configuracion'],
            ['name' => 'create ciudades', 'module' => 'ciudades', 'sector' => 'configuracion'],
            ['name' => 'edit ciudades', 'module' => 'ciudades', 'sector' => 'configuracion'],
            ['name' => 'delete ciudades', 'module' => 'ciudades', 'sector' => 'configuracion'],
        ];

        // Permisos para Municipios
        $permisosMunicipios = [
            ['name' => 'access municipios', 'module' => 'municipios', 'sector' => 'configuracion'],
            ['name' => 'view municipios', 'module' => 'municipios', 'sector' => 'configuracion'],
            ['name' => 'create municipios', 'module' => 'municipios', 'sector' => 'configuracion'],
            ['name' => 'edit municipios', 'module' => 'municipios', 'sector' => 'configuracion'],
            ['name' => 'delete municipios', 'module' => 'municipios', 'sector' => 'configuracion'],
        ];

        // Permisos para Parroquias
        $permisosParroquias = [
            ['name' => 'access parroquias', 'module' => 'parroquias', 'sector' => 'configuracion'],
            ['name' => 'view parroquias', 'module' => 'parroquias', 'sector' => 'configuracion'],
            ['name' => 'create parroquias', 'module' => 'parroquias', 'sector' => 'configuracion'],
            ['name' => 'edit parroquias', 'module' => 'parroquias', 'sector' => 'configuracion'],
            ['name' => 'delete parroquias', 'module' => 'parroquias', 'sector' => 'configuracion'],
        ];

        // Combinar todos los permisos
        $todosPermisos = array_merge($permisosEstados, $permisosCiudades, $permisosMunicipios, $permisosParroquias);

        // Crear permisos
        foreach ($todosPermisos as $permiso) {
            Permission::firstOrCreate(
                ['name' => $permiso['name']],
                ['module' => $permiso['module'], 'sector' => $permiso['sector']]
            );
        }

        // Asignar permisos a roles Super Admin y Admin
        $superAdmin = Role::where('name', 'Super Administrador')->first();
        $admin = Role::where('name', 'Administrador')->first();

        if ($superAdmin) {
            $superAdmin->givePermissionTo(array_column($todosPermisos, 'name'));
        }

        if ($admin) {
            $admin->givePermissionTo(array_column($todosPermisos, 'name'));
        }

        $this->command->info('✅ Permisos de ubicación creados exitosamente:');
        $this->command->info('   - 5 permisos para Estados');
        $this->command->info('   - 5 permisos para Ciudades');
        $this->command->info('   - 5 permisos para Municipios');
        $this->command->info('   - 5 permisos para Parroquias');
        $this->command->info('   Total: 20 permisos creados');
    }
}
