<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class SolicitudesPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Permisos para el módulo de Solicitudes
        $permisosSolicitudes = [
            ['name' => 'access solicitudes', 'module' => 'solicitudes', 'sector' => 'pastores'],
            ['name' => 'view solicitudes', 'module' => 'solicitudes', 'sector' => 'pastores'],
            ['name' => 'approve solicitudes', 'module' => 'solicitudes', 'sector' => 'pastores'],
            ['name' => 'reject solicitudes', 'module' => 'solicitudes', 'sector' => 'pastores'],
            ['name' => 'escalate solicitudes', 'module' => 'solicitudes', 'sector' => 'pastores'],
        ];

        // Crear permisos
        foreach ($permisosSolicitudes as $permiso) {
            Permission::firstOrCreate(
                ['name' => $permiso['name']],
                ['module' => $permiso['module'], 'sector' => $permiso['sector']]
            );
        }

        // Asignar permisos según roles jerárquicos
        
        // Supervisor Nacional - Acceso completo
        $supervisorNacional = Role::where('name', 'Supervisor Nacional')->first();
        if ($supervisorNacional) {
            $supervisorNacional->givePermissionTo(array_column($permisosSolicitudes, 'name'));
        }

        // Oficina Nacional - Acceso completo excepto escalar
        $oficinaNacional = Role::where('name', 'Oficina Nacional')->first();
        if ($oficinaNacional) {
            $oficinaNacional->givePermissionTo([
                'access solicitudes',
                'view solicitudes',
                'approve solicitudes',
                'reject solicitudes',
            ]);
        }

        // Presbítero - Solo ver y aprobar/rechazar sus propias solicitudes
        $presbitero = Role::where('name', 'Presbitero')->first();
        if ($presbitero) {
            $presbitero->givePermissionTo([
                'access solicitudes',
                'view solicitudes',
                'approve solicitudes',
                'reject solicitudes',
            ]);
        }

        // Administrador - Solo ver (lectura)
        $administrador = Role::where('name', 'Administrador')->first();
        if ($administrador) {
            $administrador->givePermissionTo([
                'access solicitudes',
                'view solicitudes',
            ]);
        }

     
        $this->command->info('✅ Permisos de solicitudes creados exitosamente:');
        $this->command->info('   - access solicitudes: Acceder al módulo');
        $this->command->info('   - view solicitudes: Ver detalles de solicitudes');
        $this->command->info('   - approve solicitudes: Aprobar solicitudes');
        $this->command->info('   - reject solicitudes: Rechazar solicitudes');
        $this->command->info('   - escalate solicitudes: Escalar solicitudes');
        $this->command->info('   Total: 5 permisos creados');
        $this->command->info('');
        $this->command->info('Asignación por roles:');
        $this->command->info('   - Super Admin: Todos los permisos');
        $this->command->info('   - Supervisor Nacional: Todos los permisos');
        $this->command->info('   - Oficina Nacional: Access, View, Approve, Reject');
        $this->command->info('   - Presbítero: Access, View, Approve, Reject (solo sus pastores)');
        $this->command->info('   - Administrador: Access, View (solo lectura)');
    }
}
