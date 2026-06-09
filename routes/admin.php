<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Rutas Administrativas - Laravel Admin
|--------------------------------------------------------------------------
|
| Este archivo incluye todas las rutas administrativas organizadas por módulos.
| Cada módulo tiene su propio archivo en la carpeta routes/admin/ para facilitar
| la organización y mantenimiento del código.
|
| Módulos disponibles:
| - empresas.php: Gestión de empresas
| - paises.php: Gestión de países
| - sucursales.php: Gestión de sucursales
| - usuarios.php: Gestión de usuarios y perfiles
| - roles_permisos.php: Roles y permisos (RBAC)
| - sesiones.php: Sesiones activas
| - monitoreo.php: Monitoreo del sistema
| - tasas_cambio.php: Tasas de cambio
| - configuracion.php: Configuraciones generales
| - activity_log.php: Registro de actividad
| - chat.php: Chat interno
| - exportaciones.php: Exportaciones de base de datos
| - whatsapp.php: WhatsApp
| - contabilidad.php: Módulo contable completo
| - seniat.php: Libro de ventas/compras SENIAT
| - template.php: Personalización de plantillas
| - responsables.php: Gestión de responsables
| - beneficiarios.php: Gestión de beneficiarios
|
*/

// Incluir todos los archivos de rutas modulares
require_once __DIR__ . '/admin/empresas.php';
require_once __DIR__ . '/admin/paises.php';
require_once __DIR__ . '/admin/sucursales.php';
require_once __DIR__ . '/admin/usuarios.php';
require_once __DIR__ . '/admin/roles_permisos.php';
require_once __DIR__ . '/admin/sesiones.php';
require_once __DIR__ . '/admin/monitoreo.php';
require_once __DIR__ . '/admin/tasas_cambio.php';
require_once __DIR__ . '/admin/configuracion.php';
require_once __DIR__ . '/admin/activity_log.php';
require_once __DIR__ . '/admin/chat.php';
require_once __DIR__ . '/admin/exportaciones.php';
require_once __DIR__ . '/admin/whatsapp.php';
require_once __DIR__ . '/admin/contabilidad.php';
require_once __DIR__ . '/admin/template.php';
require_once __DIR__ . '/admin/responsables.php';
require_once __DIR__ . '/admin/beneficiarios.php';
require_once __DIR__ . '/admin/casas_alimentacion.php';
