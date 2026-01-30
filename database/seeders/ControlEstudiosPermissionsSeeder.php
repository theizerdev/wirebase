<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;

class ControlEstudiosPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Lapsos de Evaluación
            ['name' => 'access evaluation_periods', 'guard_name' => 'web', 'module' => 'control_estudios'],
            ['name' => 'create evaluation_periods', 'guard_name' => 'web', 'module' => 'control_estudios'],
            ['name' => 'edit evaluation_periods', 'guard_name' => 'web', 'module' => 'control_estudios'],
            ['name' => 'delete evaluation_periods', 'guard_name' => 'web', 'module' => 'control_estudios'],
            
            // Tipos de Evaluación
            ['name' => 'access evaluation_types', 'guard_name' => 'web', 'module' => 'control_estudios'],
            ['name' => 'create evaluation_types', 'guard_name' => 'web', 'module' => 'control_estudios'],
            ['name' => 'edit evaluation_types', 'guard_name' => 'web', 'module' => 'control_estudios'],
            ['name' => 'delete evaluation_types', 'guard_name' => 'web', 'module' => 'control_estudios'],
            
            // Evaluaciones
            ['name' => 'access evaluations', 'guard_name' => 'web', 'module' => 'control_estudios'],
            ['name' => 'create evaluations', 'guard_name' => 'web', 'module' => 'control_estudios'],
            ['name' => 'edit evaluations', 'guard_name' => 'web', 'module' => 'control_estudios'],
            ['name' => 'delete evaluations', 'guard_name' => 'web', 'module' => 'control_estudios'],
            ['name' => 'view evaluations', 'guard_name' => 'web', 'module' => 'control_estudios'],
            
            // Calificaciones
            ['name' => 'access grades', 'guard_name' => 'web', 'module' => 'control_estudios'],
            ['name' => 'create grades', 'guard_name' => 'web', 'module' => 'control_estudios'],
            ['name' => 'edit grades', 'guard_name' => 'web', 'module' => 'control_estudios'],
            ['name' => 'delete grades', 'guard_name' => 'web', 'module' => 'control_estudios'],
            ['name' => 'view grades', 'guard_name' => 'web', 'module' => 'control_estudios'],
        ];

        $created = 0;
        foreach ($permissions as $perm) {
            if (!Permission::where('name', $perm['name'])->exists()) {
                Permission::create($perm);
                $created++;
            }
        }
        
        $this->command->info("Se crearon {$created} permisos para Control de Estudios");
    }
}
