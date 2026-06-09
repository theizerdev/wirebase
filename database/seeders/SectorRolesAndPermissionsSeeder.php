<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class SectorRolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Definir sectores y sus permisos organizados por categorías
        $sectors = [
            // 💰 SECTOR ADMINISTRACIÓN





            // ⚙️ SECTOR CONFIGURACIÓN
            'configuracion' => [
                'empresas' => [
                    'name' => 'Empresas',
                    'permissions' => [
                        'access empresas',
                        'create empresas',
                        'edit empresas',
                        'delete empresas',
                        'view empresas',
                        'activate empresas',
                        'deactivate empresas',
                        'assign empresas',
                        'configure empresas',
                        'export empresas',
                    ]
                ],

                'sucursales' => [
                    'name' => 'Sucursales',
                    'permissions' => [
                        'access sucursales',
                        'create sucursales',
                        'edit sucursales',
                        'delete sucursales',
                        'view sucursales',
                        'assign sucursales',
                        'configure sucursales',
                    ]
                ],
                'paises' => [
                    'name' => 'Países',
                    'permissions' => [
                        'access paises',
                        'create paises',
                        'edit paises',
                        'delete paises',
                        'view paises',
                        'activate paises',
                        'configure paises',
                    ]
                ],
                'usuarios' => [
                    'name' => 'Usuarios',
                    'permissions' => [
                        'access users',
                        'create users',
                        'edit users',
                        'delete users',
                        'view users',
                        'activate users',
                        'deactivate users',
                        'reset users password',
                        'manage users profile',
                        'assign users roles',
                        'export users',
                        'view users history',
                    ]
                ],
                'roles' => [
                    'name' => 'Roles',
                    'permissions' => [
                        'access roles',
                        'create roles',
                        'edit roles',
                        'delete roles',
                        'view roles',
                        'assign roles',
                        'manage roles',
                    ]
                ],
                'permisos' => [
                    'name' => 'Permisos',
                    'permissions' => [
                        'access permissions',
                        'create permissions',
                        'edit permissions',
                        'delete permissions',
                        'view permissions',
                        'assign permissions',
                        'manage permissions',
                    ]
                ],
                'personalizacion' => [
                    'name' => 'Personalización',
                    'permissions' => [
                        'access template customization',
                        'edit template customization',
                        'configure template customization',
                        'manage template customization',
                    ]
                ],


            ],

            // 📊 SECTOR MONITOREO
            'monitoreo' => [
                'sesiones' => [
                    'name' => 'Sesiones Activas',
                    'permissions' => [
                        'view active sessions',
                        'delete active sessions',
                        'monitor active sessions',
                        'export active sessions',
                        'manage active sessions',
                    ]
                ],
                'actividad' => [
                    'name' => 'Actividad del Sistema',
                    'permissions' => [
                        'access activity log',
                        'view activity log',
                        'delete activity log',
                        'export activity log',
                        'filter activity log',
                        'monitor activity log',
                    ]
                ],
                'respaldo' => [
                    'name' => 'Respaldo de Base de Datos',
                    'permissions' => [
                        'access database export',
                        'export database',
                        'schedule database export',
                        'download database export',
                        'manage database export',
                    ]
                ],
                'monitoreo_sistema' => [
                    'name' => 'Monitoreo del Sistema',
                    'permissions' => [
                        'access monitoreo',
                        'view monitoreo servidor',
                        'view monitoreo base-datos',
                        'view monitoreo estudiantes',
                        'view monitoreo accesos',
                        'export monitoreo accesos',
                        'view system status',
                        'view system performance',
                    ]
                ],
                'notificaciones' => [
                    'name' => 'Notificaciones',
                    'permissions' => [
                        'access notifications',
                        'view notifications',
                        'create notifications',
                        'send notifications',
                        'schedule notifications',
                        'manage notifications',
                    ]
                ],

            ],


            // 👥 SECTOR BENEFICIARIOS
            'institucional' => [
                'responsables' => [
                    'name' => 'Responsables',
                    'permissions' => [
                        'view responsables',
                        'create responsables',
                        'edit responsables',
                        'delete responsables',
                        'access responsables',
                    ]
                ],
                'beneficiarios' => [
                    'name' => 'Beneficiarios',
                    'permissions' => [
                        'view beneficiarios',
                        'create beneficiarios',
                        'edit beneficiarios',
                        'delete beneficiarios',
                        'access beneficiarios',
                    ]
                ],
                'casas_alimentacion' => [
                    'name' => 'Casas de Alimentación',
                    'permissions' => [
                        'access casas_alimentacion',
                        'create casas_alimentacion',
                        'edit casas_alimentacion',
                        'delete casas_alimentacion',
                        'view casas_alimentacion',
                    ]
                ],
            ],



            // 💬 SECTOR CHAT INTERNO
            'chat' => [
                'chat_interno' => [
                    'name' => 'Chat Interno',
                    'permissions' => [
                        'access chat interno',
                    ]
                ],
            ],


        ];

        // Crear permisos organizados por sectores y módulos
        foreach ($sectors as $sector => $modules) {
            foreach ($modules as $module => $moduleData) {
                foreach ($moduleData['permissions'] as $permission) {
                    Permission::firstOrCreate(
                        ['name' => $permission],
                        [
                            'module' => $module,
                            'sector' => $sector
                        ]
                    );
                }
            }
        }

        // Crear roles base con la nueva estructura
        $this->createRoles();
    }

    /**
     * Crear roles y asignar permisos por sectores
     */
    private function createRoles(): void
    {
        // Rol Super Administrador - Acceso total
        $superAdmin = Role::firstOrCreate(['name' => 'Super Administrador']);
        $superAdmin->syncPermissions(Permission::all());

        // Rol Super Administrador - Acceso total
        $responsable = Role::firstOrCreate(['name' => 'Responsable']);
        //$responsable->syncPermissions(Permission::all());

        // Rol Administrador - Todos los sectores excepto asignación de roles/permisos
        $admin = Role::firstOrCreate(['name' => 'Administrador']);
        $adminPermissions = Permission::whereNotIn('name', [
            'assign roles',
            'assign permissions',
            'manage permissions',
        ])->get();
        $admin->syncPermissions($adminPermissions);


         $this->command->info('✅ Roles y permisos procesados exitosamente');
        $this->command->info('📊 Total de permisos procesados: ' . count(Permission::all()));
    }
}
