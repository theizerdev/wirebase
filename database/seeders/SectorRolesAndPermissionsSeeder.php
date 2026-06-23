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

            'administracion' => [
               
                'contabilidad' => [
                    'name' => 'Contabilidad',
                    'permissions' => [
                        'access contabilidad',
                        'view contabilidad',
                        'manage contabilidad',
                    ]
                ],
                'tasa' => [
                    'name' => 'Tasa de Cambio',
                    'permissions' => [
                        'access exchange-rates',
                        'view exchange-rates',
                        'manage exchange-rates',
                    ]
                ],
            ],
            'Registro nacional' => [
                'pastores' => [
                    'name' => 'Pastores',
                    'permissions' => [
                        'access pastores',
                        'create pastores',
                        'edit pastores',
                        'show pastores',
                        'delete pastores',
                    ],
                ],
                 'iglesias' => [
                    'name' => 'Iglesias',
                    'permissions' => [
                        'access iglesias',
                        'create iglesias',
                        'edit iglesias',
                        'show iglesias',
                        'delete iglesias',
                    ],
                ],
                 // Inventario de Iglesias
                'inventario_iglesias' => [
                    'name' => 'Inventario de Iglesias',
                    'permissions' => [
                        'access inventario iglesias',
                        'create inventario iglesias',
                        'edit inventario iglesias',
                        'show inventario iglesias',
                        'delete inventario iglesias',
                    ],
                ],
                // Finanzas de Iglesias
                'finanzas_iglesias' => [
                    'name' => 'Finanzas de Iglesias',
                    'permissions' => [
                        'access finanzas iglesias',
                        'create finanzas iglesias',
                        'edit finanzas iglesias',
                        'show finanzas iglesias',
                        'delete finanzas iglesias',
                    ],
                ],
                // Actividades
                'actividades' => [
                    'name' => 'Actividades',
                    'permissions' => [
                        'access actividades',
                        'create actividades',
                        'edit actividades',
                        'show actividades',
                        'delete actividades',
                    ],
                ],
          

            
            ],


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
                'impuestos' => [
                    'name' => 'Configuración de Impuestos',
                    'permissions' => [
                        'view impuestos',
                        'create impuestos',
                        'edit impuestos',
                        'delete impuestos',
                        'activate impuestos',
                        'deactivate impuestos',
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

            // 📱 SECTOR COMUNICACIONES (Adicional)
            'comunicaciones' => [
                'whatsapp' => [
                    'name' => 'WhatsApp',
                    'permissions' => [
                        'access whatsapp'

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
        $superAdmin = Role::firstOrCreate(['name' => 'Administrador']);
        $superAdmin->syncPermissions(Permission::all());

        // ============================================
        // ROLES NUEVOS PARA GESTIÓN DE IGLESIAS
        // ============================================

        // 1. PRESBÍTERO (Obreros, Extensiones, Inventario, Finanzas, Contabilidad)
        $presbitero = Role::firstOrCreate(['name' => 'Presbitero']);
        $presbiteroPermissions = Permission::whereIn('name', [
            // Obreros (Pastores)
            'access pastores',
            'create pastores',
            'edit pastores',
            'show pastores',
            'delete pastores',
            // Extensiones (Iglesias)
            'access iglesias',
            'create iglesias',
            'edit iglesias',
            'show iglesias',
            'delete iglesias',
            // Inventario de Iglesias
            'access inventario iglesias',
            'create inventario iglesias',
            'edit inventario iglesias',
            'show inventario iglesias',
            'delete inventario iglesias',
            // Finanzas de Iglesias
            'access finanzas iglesias',
            'create finanzas iglesias',
            'edit finanzas iglesias',
            'show finanzas iglesias',
            'delete finanzas iglesias',
            // Actividades
            'access actividades',
            'create actividades',
            'edit actividades',
            'show actividades',
            'delete actividades',
            // Contabilidad
            'access contabilidad',
            'view contabilidad',
            'manage contabilidad',
        ])->get();
        $presbitero->syncPermissions($presbiteroPermissions);

        // 2. JUNTA NACIONAL (Obreros, Extensiones, Inventario, Finanzas, Contabilidad)
        $juntaNacional = Role::firstOrCreate(['name' => 'Junta Nacional']);
        $juntaNacional->syncPermissions($presbiteroPermissions); // Mismos permisos que Presbítero

        // 3. CONTADOR (Inventario, Finanzas, Tasa de Cambio)
        $contador = Role::firstOrCreate(['name' => 'Contador']);
        $contadorPermissions = Permission::whereIn('name', [
            // Inventario de Iglesias
            'access inventario iglesias',
            'create inventario iglesias',
            'edit inventario iglesias',
            'show inventario iglesias',
            'delete inventario iglesias',
            // Finanzas de Iglesias
            'access finanzas iglesias',
            'create finanzas iglesias',
            'edit finanzas iglesias',
            'show finanzas iglesias',
            'delete finanzas iglesias',
            // Tasa de Cambio
            'access exchange-rates',
            'view exchange-rates',
            'manage exchange-rates',
        ])->get();
        $contador->syncPermissions($contadorPermissions);

        // 4. SUPERVISOR NACIONAL (Obreros, Extensiones, Inventario, Finanzas, Contabilidad, Tasa de Cambio)
        $supervisorNacional = Role::firstOrCreate(['name' => 'Supervisor Nacional']);
        $supervisorNacionalPermissions = Permission::whereIn('name', [
            // Obreros (Pastores)
            'access pastores',
            'create pastores',
            'edit pastores',
            'show pastores',
            'delete pastores',
            // Extensiones (Iglesias)
            'access iglesias',
            'create iglesias',
            'edit iglesias',
            'show iglesias',
            'delete iglesias',
            // Inventario de Iglesias
            'access inventario iglesias',
            'create inventario iglesias',
            'edit inventario iglesias',
            'show inventario iglesias',
            'delete inventario iglesias',
            // Finanzas de Iglesias
            'access finanzas iglesias',
            'create finanzas iglesias',
            'edit finanzas iglesias',
            'show finanzas iglesias',
            'delete finanzas iglesias',
            // Actividades
            'access actividades',
            'create actividades',
            'edit actividades',
            'show actividades',
            'delete actividades',
            // Contabilidad
            'access contabilidad',
            'view contabilidad',
            'manage contabilidad',
            // Tasa de Cambio
            'access exchange-rates',
            'view exchange-rates',
            'manage exchange-rates',
        ])->get();
        $supervisorNacional->syncPermissions($supervisorNacionalPermissions);

        // 5. OFICINA NACIONAL (Obreros, Extensiones, Inventario, Finanzas, Contabilidad, Usuarios, Tasa de Cambio)
        $oficinaNacional = Role::firstOrCreate(['name' => 'Oficina Nacional']);
        $oficinaNacionalPermissions = Permission::whereIn('name', [
            // Obreros (Pastores)
            'access pastores',
            'create pastores',
            'edit pastores',
            'show pastores',
            'delete pastores',
            // Extensiones (Iglesias)
            'access iglesias',
            'create iglesias',
            'edit iglesias',
            'show iglesias',
            'delete iglesias',
            // Inventario de Iglesias
            'access inventario iglesias',
            'create inventario iglesias',
            'edit inventario iglesias',
            'show inventario iglesias',
            'delete inventario iglesias',
            // Finanzas de Iglesias
            'access finanzas iglesias',
            'create finanzas iglesias',
            'edit finanzas iglesias',
            'show finanzas iglesias',
            'delete finanzas iglesias',
            // Actividades
            'access actividades',
            'create actividades',
            'edit actividades',
            'show actividades',
            'delete actividades',
            // Contabilidad
            'access contabilidad',
            'view contabilidad',
            'manage contabilidad',
            // Usuarios
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
            // Tasa de Cambio
            'access exchange-rates',
            'view exchange-rates',
            'manage exchange-rates',
        ])->get();
        $oficinaNacional->syncPermissions($oficinaNacionalPermissions);

        $this->command->info('✅ Roles y permisos procesados exitosamente');
        $this->command->info('📊 Total de permisos procesados: ' . count(Permission::all()));
        $this->command->info('👥 Roles creados:');
        $this->command->info('   - Presbitero');
        $this->command->info('   - Junta Nacional');
        $this->command->info('   - Contador');
        $this->command->info('   - Supervisor Nacional');
        $this->command->info('   - Oficina Nacional');
    }
}
