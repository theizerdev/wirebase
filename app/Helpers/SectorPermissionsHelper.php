<?php

if (!function_exists('getPermissionSectors')) {
    function getPermissionSectors(): array
    {
        return [
            'pastores' => [
                'name' => '👥 Pastores',
                'description' => 'Gestión de pastores, iglesias, actividades y solicitudes de modificación',
                'color' => 'blue',
                'icon' => 'ri-user-line',
                'modules' => ['pastores', 'iglesias', 'inventario iglesias', 'finanzas iglesias','solicitudes']
            ],
            'configuracion' => [
                'name' => '⚙️ Configuración',
                'description' => 'Configuración del sistema, empresas, usuarios y roles',
                'color' => 'purple',
                'icon' => 'ri-settings-3-line',
                'modules' => ['empresas', 'sucursales', 'paises', 'users', 'roles', 'permissions', 'personalizacion', 'zonas', 'estados', 'ciudades', 'municipios', 'parroquias']
            ],
            'monitoreo' => [
                'name' => '📊 Monitoreo',
                'description' => 'Monitoreo del sistema, actividad y respaldos',
                'color' => 'orange',
                'icon' => 'ri-line-chart-line',
                'modules' => ['sesiones', 'actividad', 'respaldo', 'monitoreo_sistema', 'notificaciones']
            ],
            'comunicaciones' => [
                'name' => '📱 Comunicaciones',
                'description' => 'WhatsApp, mensajes y plantillas',
                'color' => 'teal',
                'icon' => 'ri-whatsapp-line',
                'modules' => ['whatsapp', 'whatsapp templates', 'whatsapp messages', 'chat interno']
            ],
            'sistema' => [
                'name' => '🔧 Sistema',
                'description' => 'Configuraciones del sistema y API',
                'color' => 'gray',
                'icon' => 'ri-tools-line',
                'modules' => ['system', 'api', 'jwt']
            ],
          
        ];
    }
}

if (!function_exists('getSectorPermissions')) {
    function getSectorPermissions(string $sector): \Illuminate\Support\Collection
    {
        return \Spatie\Permission\Models\Permission::where('sector', $sector)
            ->orderBy('module')
            ->orderBy('name')
            ->get();
    }
}

if (!function_exists('getUserSectors')) {
    function getUserSectors($user): array
    {
        $permissions = $user->permissions()->pluck('sector')->unique()->filter()->values();
        $sectors = getPermissionSectors();

        return $permissions->mapWithKeys(function ($sector) use ($sectors) {
            return [$sector => $sectors[$sector] ?? ['name' => ucfirst($sector)]];
        })->toArray();
    }
}

if (!function_exists('hasSectorAccess')) {
    function hasSectorAccess($user, string $sector): bool
    {
        return $user->permissions()->where('sector', $sector)->exists();
    }
}

if (!function_exists('getSectorColor')) {
    function getSectorColor(string $sector): string
    {
        $sectors = getPermissionSectors();
        return $sectors[$sector]['color'] ?? 'gray';
    }
}

if (!function_exists('getSectorIcon')) {
    function getSectorIcon(string $sector): string
    {
        $sectors = getPermissionSectors();
        return explode(' ', $sectors[$sector]['name'])[0] ?? '📋';
    }
}

if (!function_exists('formatSectorName')) {
    function formatSectorName(string $sector, bool $withIcon = true): string
    {
        $sectors = getPermissionSectors();
        $name = $sectors[$sector]['name'] ?? ucfirst($sector);

        if (!$withIcon) {
            return str_replace(['🏥', '💰', '⚙️', '📊', '📱', '🔧'], '', $name);
        }

        return $name;
    }
}

if (!function_exists('getSectorStats')) {
    function getSectorStats(): array
    {
        $sectors = getPermissionSectors();
        $stats = [];

        foreach ($sectors as $key => $sector) {
            $totalPermissions = \Spatie\Permission\Models\Permission::where('sector', $key)->count();
            $totalRoles = \Spatie\Permission\Models\Role::whereHas('permissions', function ($query) use ($key) {
                $query->where('sector', $key);
            })->count();

            $stats[$key] = [
                'name' => $sector['name'],
                'total_permissions' => $totalPermissions,
                'total_roles' => $totalRoles,
                'color' => $sector['color'],
                'description' => $sector['description']
            ];
        }

        return $stats;
    }
}

if (!function_exists('getSectorMenuItems')) {
    function getSectorMenuItems(): array
    {
        return [
             'pastores' => [
                'label' => 'Pastores',
                'icon' => 'ri-user-line',
                'items' => [
                    [
                        'label' => 'Obreros',
                        'icon' => 'ri-user-line',
                        'permissions' => ['access pastores'],
                        'active' => 'admin.pastores.*',
                        'children' => [
                            ['label' => 'Listado general', 'permission' => 'access pastores', 'route' => 'admin.pastores.index', 'active' => 'admin.pastores.index'],
                            ['label' => 'Nuevo registro', 'permission' => 'access pastores', 'route' => 'admin.pastores.create', 'active' => 'admin.pastores.create'],
                           
                           
                        ]
                    ],
                    [
                        'label' => 'Extensiones',
                        'icon' => 'ri-hospital-line',
                        'permissions' => ['access iglesias'],
                        'active' => 'admin.iglesias.*',
                        'children' => [
                            ['label' => 'Listado general', 'permission' => 'access iglesias', 'route' => 'admin.iglesias.index', 'active' => 'admin.iglesias.index'],
                            ['label' => 'Nuevo registro', 'permission' => 'access iglesias', 'route' => 'admin.iglesias.create', 'active' => 'admin.iglesias.create'],
                           
                        ]
                    ],
                    [
                        'label' => 'Inventario',
                        'icon' => 'ri-archive-line',
                        'permissions' => ['access inventario iglesias'],
                        'active' => 'admin.inventario.*',
                        'children' => [
                            ['label' => 'Listado general', 'permission' => 'access inventario iglesias', 'route' => 'admin.inventario.index', 'active' => 'admin.inventario.index'],
                            ['label' => 'Nuevo ítem', 'permission' => 'create inventario iglesias', 'route' => 'admin.inventario.create', 'active' => 'admin.inventario.create'],
                        ]
                    ],
                    [
                        'label' => 'Finanzas',
                        'icon' => 'ri-money-dollar-circle-line',
                        'permissions' => ['access finanzas iglesias'],
                        'active' => 'admin.finanzas.*',
                        'children' => [
                            ['label' => 'Listado general', 'permission' => 'access finanzas iglesias', 'route' => 'admin.finanzas.index', 'active' => 'admin.finanzas.index'],
                            ['label' => 'Nueva transacción', 'permission' => 'create finanzas iglesias', 'route' => 'admin.finanzas.create', 'active' => 'admin.finanzas.create'],
                        ]
                    ],
                    [
                        'label' => 'Solicitudes',
                        'icon' => 'ri-file-list-3-line',
                        'permissions' => ['access solicitudes'],
                        'active' => 'admin.solicitudes.*',
                        'children' => [
                            ['label' => 'Dashboard', 'permission' => 'access solicitudes', 'route' => 'admin.solicitudes.dashboard', 'active' => 'admin.solicitudes.dashboard'],
                            ['label' => 'Todas las solicitudes', 'permission' => 'view solicitudes', 'route' => 'admin.solicitudes.index', 'active' => 'admin.solicitudes.index'],
                        ]
                    ],
                ],
            ],
               'administracion' => [
                'label' => 'Administración',
                'icon' => 'ri-money-dollar-circle-line',
                'items' => [
                  
                    [
                        'label' => 'Tasa de cambio',
                        'icon' => 'ri-exchange-dollar-line',
                        'permission' => 'view exchange-rates',
                        'route' => 'admin.exchange-rates',
                        'active' => 'admin.exchange-rates',
                    ],
                     ['label' => 'Actividades', 'permission' => 'access actividades', 'route' => 'admin.actividades.index', 'active' => 'admin.actividades.index'],
                    [
                        'label' => 'Contabilidad',
                        'icon' => 'ri-calculator-line',
                        'permissions' => ['access contabilidad', 'view contabilidad'],
                        'active' => 'admin.contabilidad.*|admin.seniat.*',
                        'children' => [
                            ['label' => 'Plan de Cuentas', 'permission' => 'access contabilidad', 'route' => 'admin.contabilidad.plan-cuentas', 'active' => 'admin.contabilidad.plan-cuentas'],
                            ['label' => 'Asientos Contables', 'permission' => 'access contabilidad', 'route' => 'admin.contabilidad.asientos', 'active' => 'admin.contabilidad.asientos'],
                            ['label' => 'Libro Diario', 'permission' => 'access contabilidad', 'route' => 'admin.contabilidad.libro-diario', 'active' => 'admin.contabilidad.libro-diario'],
                            ['label' => 'Libro Mayor', 'permission' => 'access contabilidad', 'route' => 'admin.contabilidad.libro-mayor', 'active' => 'admin.contabilidad.libro-mayor'],
                            ['label' => 'Balance Comprobación', 'permission' => 'access contabilidad', 'route' => 'admin.contabilidad.balance-comprobacion', 'active' => 'admin.contabilidad.balance-comprobacion'],
                            ['label' => 'Balance General', 'permission' => 'access contabilidad', 'route' => 'admin.contabilidad.balance-general', 'active' => 'admin.contabilidad.balance-general'],
                            ['label' => 'Estado de Resultados', 'permission' => 'access contabilidad', 'route' => 'admin.contabilidad.estado-resultados', 'active' => 'admin.contabilidad.estado-resultados'],
                            ['label' => 'Cierre Contable', 'permission' => 'access contabilidad', 'route' => 'admin.contabilidad.cierre-contable', 'active' => 'admin.contabilidad.cierre-contable'],
                            //['label' => 'Libro de Ventas', 'permission' => 'access contabilidad', 'route' => 'admin.seniat.libro-ventas', 'active' => 'admin.seniat.libro-ventas'],
                        ]
                    ],
                    ]
            ],

            'configuracion' => [
                'label' => 'Configuración',
                'icon' => 'ri-settings-3-line',
                'items' => [
                    [
                        'label' => 'Institucional',
                        'icon' => 'ri-building-4-line',
                        'permissions' => ['access empresas', 'access sucursales', 'access paises', 'access consultorios'],
                        'active' => 'admin.empresas.*|admin.sucursales.*|admin.paises.*|admin.consultorios.*',
                        'children' => [
                            ['label' => 'Empresas', 'permission' => 'access empresas', 'route' => 'admin.empresas.index', 'active' => 'admin.empresas.index'],
                            ['label' => 'Sucursales', 'permission' => 'access sucursales', 'route' => 'admin.sucursales.index', 'active' => 'admin.sucursales.index'],
                            //['label' => 'Consultorios', 'permission' => 'access consultorios', 'route' => 'admin.consultorios.index', 'active' => 'admin.consultorios.index'],
                            ['label' => 'Países', 'permission' => 'access paises', 'route' => 'admin.paises.index', 'active' => 'admin.paises.index'],
                        ]
                    ],
                    [
                        'label' => 'Zonas de Acceso',
                        'icon' => 'ri-map-pin-line',
                        'permission' => 'access zonas',
                        'route' => 'admin.zonas.index',
                        'active' => 'admin.zonas.*',
                    ],
                    [
                        'label' => 'Ubicación Geográfica',
                        'icon' => 'ri-earth-line',
                        'permissions' => ['access estados', 'access ciudades', 'access municipios', 'access parroquias'],
                        'active' => 'admin.estados.*|admin.ciudades.*|admin.municipios.*|admin.parroquias.*',
                        'children' => [
                            ['label' => 'Estados', 'permission' => 'access estados', 'route' => 'admin.estados.index', 'active' => 'admin.estados.index'],
                            ['label' => 'Ciudades', 'permission' => 'access ciudades', 'route' => 'admin.ciudades.index', 'active' => 'admin.ciudades.index'],
                            ['label' => 'Municipios', 'permission' => 'access municipios', 'route' => 'admin.municipios.index', 'active' => 'admin.municipios.index'],
                            ['label' => 'Parroquias', 'permission' => 'access parroquias', 'route' => 'admin.parroquias.index', 'active' => 'admin.parroquias.index'],
                        ]
                    ],

                    [
                        'label' => 'Usuarios y Acceso',
                        'icon' => 'ri-group-line',
                        'permissions' => ['access users', 'access roles', 'access permissions'],
                        'active' => 'admin.users.*|admin.roles.*|admin.permissions.*',
                        'children' => [
                            ['label' => 'Usuarios', 'permission' => 'access users', 'route' => 'admin.users.index', 'active' => 'admin.users.index'],
                            ['label' => 'Roles', 'permission' => 'access roles', 'route' => 'admin.roles.index', 'active' => 'admin.roles.index'],
                            ['label' => 'Permisos', 'permission' => 'access permissions', 'route' => 'admin.permissions.index', 'active' => 'admin.permissions.index'],
                        ]
                    ],
                    [
                        'label' => 'Personalización',
                        'icon' => 'ri-palette-line',
                        'permission' => 'access template customization',
                        'route' => 'admin.template-customization',
                        'active' => 'admin.template-customization',
                    ],

                ]
            ],
            'comunicaciones' => [
                'label' => 'Comunicaciones',
                'icon' => 'ri-whatsapp-line',
                'items' => [
                    [
                        'label' => 'WhatsApp',
                        'icon' => 'ri-whatsapp-line',
                        'permission' => 'access whatsapp',
                        'route' => 'admin.whatsapp.index',
                        'route_horizontal' => 'admin.whatsapp.index',
                        'active' => 'admin.whatsapp.*',
                    ],
                ]
            ],
            'monitoreo' => [
                'label' => 'Monitoreo',
                'icon' => 'ri-line-chart-line',
                'items' => [
                    [
                        'label' => 'Sesiones',
                        'icon' => 'ri-user-settings-line',
                        'permission' => 'view active sessions',
                        'route' => 'admin.active-sessions.index',
                        'active' => 'admin.active-sessions*',
                    ],
                    [
                        'label' => 'Actividad',
                        'icon' => 'ri-history-line',
                        'permission' => 'access activity log',
                        'route' => 'admin.activity-log',
                        'active_path' => 'admin/activity-log*',
                    ],
                    [
                        'label' => 'Exportar Base de Datos',
                        'icon' => 'ri-database-2-line',
                        'permission' => 'access database export',
                        'route' => 'admin.database-export',
                        'active' => 'admin.database-export',
                    ],
                ],
            ],
           
        ];
    }
}

if (!function_exists('isMenuItemActive')) {
    function isMenuItemActive(array $item): bool
    {
        if (isset($item['active'])) {
            $patterns = explode('|', $item['active']);
            foreach ($patterns as $pattern) {
                if (request()->routeIs(trim($pattern))) {
                    return true;
                }
            }
        }
        if (isset($item['active_path'])) {
            if (request()->is($item['active_path'])) {
                return true;
            }
        }
        return false;
    }
}

if (!function_exists('isSectorActive')) {
    function isSectorActive(array $sectorItems): bool
    {
        foreach ($sectorItems as $item) {
            if (isMenuItemActive($item)) {
                return true;
            }
            if (isset($item['children'])) {
                foreach ($item['children'] as $child) {
                    if (isMenuItemActive($child)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }
}

if (!function_exists('getEstadosConsultaMenuItems')) {
    /**
     * Devuelve los ítems de menú para los estados del flujo que son exclusivos
     * de especialidades (no los estados base comunes a todas).
     * Se basa en los estados_flujo configurados en las plantillas activas.
     */
    function getEstadosConsultaMenuItems(): array
    {
        // Estados base que ya tienen ruta fija en el menú
        $estadosBase = ['sala_espera', 'en_enfermeria', 'en_consultorio', 'en_consultorio_optometrista', 'en_estudio', 'finalizada', 'pagada', 'por_llegar', 'borrador'];

        // Rutas fijas existentes para algunos estados especiales
        $rutasFijas = [
            'en_gotas'  => 'admin.gestion.consultas.en-gotas',
            'dilatado'  => 'admin.gestion.consultas.dilatado',
            'en_optica' => 'admin.gestion.consultas.en-optica',
        ];

        try {
            $estadosExtra = \App\Models\EspecialidadPlantilla::where('activo', true)
                ->pluck('estados_flujo')
                ->filter()
                ->flatMap(fn($flujo) => $flujo)
                ->unique()
                ->diff($estadosBase)
                ->values();

            $items = [];
            foreach ($estadosExtra as $estado) {
                $label = \App\Models\EspecialidadPlantilla::ESTADOS_DISPONIBLES[$estado]
                      ?? \App\Models\Consulta::ESTADO_LABELS[$estado]
                      ?? ucfirst(str_replace('_', ' ', $estado));

                if (isset($rutasFijas[$estado])) {
                    $items[] = [
                        'label'  => $label,
                        'route'  => $rutasFijas[$estado],
                        'active' => $rutasFijas[$estado],
                    ];
                } else {
                    $items[] = [
                        'label'  => $label,
                        'route'  => 'admin.gestion.consultas.por-estado',
                        'params' => ['estado' => $estado],
                        'active' => 'admin.gestion.consultas.por-estado',
                    ];
                }
            }

            return $items;
        } catch (\Exception $e) {
            return [];
        }
    }
}
