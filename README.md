# Laradmin - Plantilla Administrativa Laravel

<p align="center">
  <img src="https://laravel.com/assets/img/components/logo-laravel.svg" alt="Laravel Logo" width="200"/>
</p>

<p align="center">
  Plantilla administrativa completa desarrollada con Laravel 12, Livewire y arquitectura DDD
</p>

<p align="center">
  <a href="#características"><strong>Características</strong></a> ·
  <a href="#módulos-principales"><strong>Módulos</strong></a> ·
  <a href="#instalación"><strong>Instalación</strong></a> ·
  <a href="#arquitectura"><strong>Arquitectura</strong></a> ·
  <a href="#documentación"><strong>Documentación</strong></a>
</p>

---

## 📋 Descripción

**Laradmin** es una plantilla administrativa profesional y completa construida sobre **Laravel 12**, diseñada para acelerar el desarrollo de sistemas de gestión empresarial. Implementa las mejores prácticas de desarrollo incluyendo **Domain-Driven Design (DDD)**, **Repository Pattern**, **Service Layer** y **Event-Driven Architecture**.

Esta plantilla proporciona una base sólida y modular que incluye gestión de empresas, sucursales, usuarios, roles, permisos, contabilidad, notificaciones en tiempo real, integración con WhatsApp Business API, y mucho más. Es el punto de partida ideal para cualquier aplicación administrativa o ERP.

## ✨ Características Generales

### 🔐 Sistema de Autenticación y Seguridad
- ✅ Autenticación robusta con Laravel Sanctum/JWT
- ✅ Verificación de correo electrónico
- ✅ Autenticación de dos factores (2FA)
- ✅ Control de acceso basado en roles (RBAC - Spatie Permission)
- ✅ Perfiles de usuario con avatar personalizado
- ✅ Gestión de sesiones activas
- ✅ Códigos de verificación temporales
- ✅ Protección CSRF y validación robusta
- ✅ Encriptación de datos sensibles
- ✅ Rate limiting en APIs

### 👥 Gestión de Usuarios
- ✅ CRUD completo de usuarios
- ✅ Perfiles de usuario personalizables
- ✅ Cambio de contraseñas seguro
- ✅ Historial de actividad por usuario
- ✅ Estados activo/inactivo
- ✅ Asignación de roles y permisos
- ✅ Avatares y fotografías de perfil

### 🏢 Multitenancia Empresarial
- ✅ Soporte multiempresa para cadenas de negocios
- ✅ Soporte multisucursal con aislamiento de datos
- ✅ Configuración independiente por tenant
- ✅ API Keys específicas por empresa
- ✅ Personalización regional por sucursal
- ✅ Aislamiento completo de datos entre tenants

### 🌍 Configuración Regional
- ✅ Formato de fechas localizado (español/inglés)
- ✅ Formato de monedas configurable
- ✅ Configuración específica por empresa/sucursal
- ✅ Soporte multi-moneda con tasas dinámicas
- ✅ Formato de números y decimales adaptable
- ✅ Países y regiones configurables

### 🔔 Sistema de Notificaciones
- ✅ Notificaciones en tiempo real (WebSockets)
- ✅ Priorización (baja, media, alta, urgente)
- ✅ Historial completo y tracking
- ✅ Marcado de leídas/no leídas
- ✅ Notificaciones por correo electrónico
- ✅ Push notifications en navegador
- ✅ Campana de notificaciones en UI

### 💬 Mensajería Interna
- ✅ Chat entre usuarios del sistema
- ✅ Conversaciones grupales
- ✅ Priorización de mensajes importantes
- ✅ Adjuntar archivos multimedia
- ✅ Control de lectura y archivado
- ✅ Historial persistente de conversaciones
- ✅ Widget flotante de chat

### 📱 Integración con WhatsApp Business
- ✅ Envío de mensajes de texto personalizados
- ✅ Envío de documentos (Excel, PDF, Word)
- ✅ Programación de mensajes automáticos
- ✅ Reintento inteligente de mensajes fallidos
- ✅ Plantillas de mensajes configurables
- ✅ Sistema de colas para envío masivo
- ✅ Monitoreo de estado de conexión
- ✅ Jobs programados para envíos automáticos

### 📊 Exportación Avanzada de Datos
- ✅ Exportación dinámica de cualquier tabla
- ✅ Múltiples formatos (Excel, CSV, PDF)
- ✅ Filtros avanzados con condiciones múltiples
- ✅ Selección granular de columnas
- ✅ Interfaz web intuitiva
- ✅ Comandos Artisan para automatización
- ✅ Procesamiento asíncrono con progreso visual
- ✅ Descarga automática de archivos generados

### 🔍 Sistema de Auditoría Completo
- ✅ Registro exhaustivo de todas las acciones
- ✅ Tracking de cambios en datos sensibles
- ✅ Seguimiento por usuario y rol
- ✅ Registro de IPs y user agents
- ✅ Tags y metadatos personalizables
- ✅ Exportación de logs de auditoría
- ✅ Logs en español descriptivos

### ⚙️ Sistema de Tareas Programadas
- ✅ Procesamiento inteligente de colas
- ✅ Reintento automático con backoff exponencial
- ✅ Envío programado de notificaciones
- ✅ Procesamiento de eventos recurrentes
- ✅ Monitoreo de jobs fallidos
- ✅ Workers optimizados para producción

### 🎨 Personalización de Plantillas
- ✅ Temas visuales configurables
- ✅ Layouts adaptables
- ✅ Componentes reutilizables
- ✅ Blade components personalizados
- ✅ Tailwind CSS integrado
- ✅ Diseño responsive moderno

### 📈 Dashboard y Analytics
- ✅ Dashboard principal con métricas clave
- ✅ Widgets configurables
- ✅ Búsqueda global en todo el sistema
- ✅ Estadísticas y reportes rápidos
- ✅ Indicadores de rendimiento

## 🏗️ Módulos Principales

### 1. 🏢 Empresas
Gestión completa de organizaciones empresariales:
- ✅ CRUD de empresas
- ✅ Configuración independiente por empresa
- ✅ Logos e información corporativa
- ✅ Datos fiscales y tributarios
- ✅ Configuración regional específica
- ✅ API Keys por empresa
- ✅ Aislamiento de datos multinivel

### 2. 🏪 Sucursales
Administración de ubicaciones físicas:
- ✅ CRUD de sucursales por empresa
- ✅ Direcciones y datos de contacto
- ✅ Configuración local por sucursal
- ✅ Asignación de usuarios responsables
- ✅ Horarios de operación
- ✅ Información geográfica

### 3. 👤 Usuarios
Sistema completo de gestión de usuarios:
- ✅ Registro y administración de usuarios
- ✅ Perfiles detallados con avatares
- ✅ Cambio de contraseñas seguro
- ✅ Historial de actividad
- ✅ Estados de cuenta (activo/inactivo)
- ✅ Asignación de roles y permisos
- ✅ Perfiles públicos y privados

### 4. 🎭 Roles (RBAC)
Control de acceso basado en roles:
- ✅ CRUD de roles personalizados
- ✅ Asignación de permisos granulares
- ✅ Roles jerárquicos
- ✅ Visualización de permisos asignados
- ✅ Roles predefinidos y custom
- ✅ Integración con Spatie Permission

### 5. 🔑 Permisos
Sistema granular de permisos:
- ✅ CRUD de permisos
- ✅ Agrupación por categorías
- ✅ Descripciones detalladas
- ✅ Asignación a roles
- ✅ Permisos dinámicos
- ✅ Middleware de protección de rutas

### 6. 🌐 Países
Configuración geográfica:
- ✅ Gestión de países
- ✅ Códigos ISO
- ✅ Prefijos telefónicos
- ✅ Configuración regional
- ✅ Monedas por país

### 7. 💱 Tasas de Cambio
Control financiero multicurrency:
- ✅ Tasas de cambio USD/EUR/Bs
- ✅ Configuración de tasas
- ✅ Historial de tasas diarias
- ✅ Historial mensual
- ✅ Actualización manual/automática
- ✅ Conversión en tiempo real

### 8. 📊 Contabilidad
Módulo contable completo:
- ✅ Plan de cuentas configurable
- ✅ Asientos contables
- ✅ Balance de comprobación
- ✅ Balance general
- ✅ Estado de resultados
- ✅ Libro mayor
- ✅ Libro diario
- ✅ Conciliación bancaria
- ✅ Cierre contable
- ✅ Honorarios médicos/profesionales
- ✅ Exportación a PDF y Excel

### 9. 📋 SENIAT (Libros Fiscales)
Cumplimiento fiscal venezolano:
- ✅ Libro de ventas
- ✅ Libro de compras
- ✅ Exportación TXT formato SENIAT
- ✅ Exportación Excel
- ✅ Cálculo automático de impuestos
- ✅ Control de alícuotas

### 10. 💰 Conceptos de Pago
Configuración de tipos de pago:
- ✅ CRUD de conceptos de pago
- ✅ Categorización
- ✅ Configuración contable
- ✅ Estados activo/inactivo
- ✅ Descripciones detalladas

### 11. 🖥️ Monitoreo del Sistema
Supervisión en tiempo real:
- ✅ Estado del servidor
- ✅ Monitoreo de base de datos
- ✅ Estadísticas de estudiantes/pacientes
- ✅ Control de accesos
- ✅ Métricas de rendimiento
- ✅ Alertas tempranas

### 12. 🔄 Sesiones Activas
Control de sesiones de usuario:
- ✅ Listado de sesiones activas
- ✅ Terminación remota de sesiones
- ✅ Información de dispositivo y ubicación
- ✅ Timestamps de actividad
- ✅ Seguridad proactiva

### 13. ⚙️ Configuración de Notificaciones
Personalización de alertas:
- ✅ Configuración por tipo de notificación
- ✅ Canales de envío (email, WhatsApp, push)
- ✅ Frecuencia y horarios
- ✅ Activación/desactivación selectiva
- ✅ Plantillas personalizables

### 14. 📜 Registro de Actividad (Activity Log)
Auditoría completa del sistema:
- ✅ Log de todas las acciones
- ✅ Filtrado por usuario, fecha, acción
- ✅ Detalles completos de cada operación
- ✅ Exportación de logs
- ✅ Búsqueda avanzada

### 15. 📤 Exportador de Base de Datos
Herramienta de exportación masiva:
- ✅ Exportación de tablas completas
- ✅ Filtros personalizados
- ✅ Múltiples formatos
- ✅ Descarga directa
- ✅ Historial de exportaciones

### 16. 📱 WhatsApp
Integración completa con WhatsApp:
- ✅ Envío de mensajes
- ✅ Programación de campañas
- ✅ Plantillas reutilizables
- ✅ Historial de mensajes
- ✅ Estado de entrega
- ✅ Reintentos automáticos

## 🛠️ Requisitos del Sistema

### Requisitos Mínimos
- **PHP**: >= 8.2
- **Composer**: >= 2.0
- **Base de Datos**: MySQL >= 5.7 o PostgreSQL >= 10
- **Node.js**: >= 18.x (para servicio WhatsApp opcional)
- **Redis**: Opcional (recomendado para colas y caché)

### Extensiones PHP Requeridas
- BCMath PHP Extension
- Ctype PHP Extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension
- GD o Imagick (para procesamiento de imágenes)

## 🚀 Instalación

### 1. Clonar el Repositorio
```bash
git clone [url-del-repositorio]
cd laradmin
```

### 2. Instalar Dependencias
```bash
# Dependencias PHP
composer install

# Dependencias Node.js
npm install
```

### 3. Configurar Variables de Entorno
```bash
cp .env.example .env
php artisan key:generate
```

Edita el archivo `.env` con tus configuraciones:
```env
# Base de Datos
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laradmin
DB_USERNAME=root
DB_PASSWORD=

# WhatsApp API (opcional)
WHATSAPP_API_URL=http://tu-servidor-whatsapp:8092
WHATSAPP_API_KEY=tu-api-key

# Mail (configura según tu proveedor)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=tu-email@gmail.com
MAIL_PASSWORD=tu-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@tudominio.com
MAIL_FROM_NAME="${APP_NAME}"

# Colas (recomendado database o redis)
QUEUE_CONNECTION=database
```

### 4. Ejecutar Migraciones y Seeders
```bash
php artisan migrate --seed
```

### 5. Compilar Assets Frontend
```bash
npm run build
# O para desarrollo con hot-reload:
npm run dev
```

### 6. Iniciar Servidores
```bash
# Servidor Laravel
php artisan serve

# Worker de colas (en otra terminal)
php artisan queue:listen --tries=3

# O usar el comando compuesto (desarrollo):
composer run dev
```

### 7. Servicio WhatsApp (Opcional)
```bash
cd resources/js/whatsapp
npm start
```

## ⚙️ Configuración

### Comandos Útiles

```bash
# Limpiar cachés
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Optimizar para producción
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Ejecutar migraciones pendientes
php artisan migrate

# Regenerar autoload
composer dump-autoload

# Ejecutar tests
composer run test
```

### Estructura de Rutas Modulares

Las rutas administrativas están organizadas modularmente en `routes/admin/`:

```
routes/
├── admin.php                    # Archivo principal (include todos los módulos)
├── admin/
│   ├── empresas.php            # Gestión de empresas
│   ├── paises.php              # Gestión de países
│   ├── sucursales.php          # Gestión de sucursales
│   ├── usuarios.php            # Usuarios y perfiles
│   ├── roles_permisos.php      # Roles y permisos RBAC
│   ├── sesiones.php            # Sesiones activas
│   ├── monitoreo.php           # Monitoreo del sistema
│   ├── tasas_cambio.php        # Tasas de cambio
│   ├── configuracion.php       # Configuraciones generales
│   ├── activity_log.php        # Registro de actividad
│   ├── chat.php                # Chat interno
│   ├── exportaciones.php       # Exportaciones de BD
│   ├── whatsapp.php            # WhatsApp
│   ├── contabilidad.php        # Módulo contable completo
│   ├── seniat.php              # Libro SENIAT
│   └── template.php            # Personalización de plantillas
├── api.php                     # Rutas API REST
├── auth.php                    # Rutas de autenticación
└── web.php                     # Rutas web públicas
```

## 🏗️ Arquitectura

### Patrones de Diseño Implementados

- **🎯 Domain-Driven Design (DDD)**: Separación clara entre dominio y aplicación
- **📦 Repository Pattern**: Abstracción del acceso a datos
- **⚙️ Service Layer**: Encapsulamiento de lógica de negocio
- **📨 Event-Driven Architecture**: Desacoplamiento mediante eventos
- **💼 DTOs**: Transferencia de datos tipada y segura
- **🔒 Value Objects**: Objetos de valor inmutables

### Estructura del Proyecto

```
app/
├── Application/                 # Capa de Aplicación
│   ├── DTOs/                   # Data Transfer Objects
│   ├── Events/                 # Eventos de dominio
│   └── Services/               # Servicios de aplicación
├── Domain/                      # Capa de Dominio
│   ├── Contracts/              # Interfaces y contratos
│   ├── Entities/               # Entidades del dominio
│   └── ValueObjects/           # Objetos de valor
├── Infrastructure/              # Capa de Infraestructura
│   └── Repositories/           # Implementaciones de repositorios
├── Http/                        # Capa HTTP
│   ├── Controllers/            # Controladores tradicionales
│   ├── Middleware/             # Middlewares personalizados
│   ├── Requests/               # Form Requests de validación
│   └── Resources/              # API Resources
├── Livewire/                    # Componentes Livewire
│   ├── Admin/                  # Componentes administrativos
│   ├── Auth/                   # Componentes de autenticación
│   ├── Doctor/                 # Componentes específicos
│   └── SuperAdmin/             # Componentes superadmin
├── Jobs/                        # Jobs de cola
├── Listeners/                   # Listeners de eventos
├── Mail/                        # Mailables
├── Models/                      # Modelos Eloquent
├── Notifications/               # Notificaciones
├── Providers/                   # Service Providers
├── Services/                    # Servicios del sistema
├── Traits/                      # Traits reutilizables
└── View/Components/             # Blade Components

database/
├── migrations/                  # Migraciones de BD
├── seeders/                     # Seeders de datos
└── factories/                   # Factories para testing

routes/
├── admin/                       # Rutas administrativas modulares
├── api.php                      # API REST
├── auth.php                     # Autenticación
└── web.php                      # Rutas web

resources/
├── views/                       # Vistas Blade
├── js/                          # JavaScript (Vite)
└── css/                         # Estilos (Tailwind)
```

## 📚 Documentación

### Guías Técnicas
- [Flujo de Notificación y Confirmación](FLUJO_NOTIFICACION_CONFIRMACION.md)
- [Inventario de Mensajes WhatsApp](WHATSAPP_MESSAGE_INVENTORY.md)
- [Cambios en Calendario y Tiempo](CAMBIOS_CALENDARIO_TIEMPO.md)
- [Registro de Cambios](CHANGELOG.md)

### Módulos Destacados
- **Contabilidad**: Plan de cuentas, asientos, balances, libros oficiales
- **SENIAT**: Libro de ventas y compras con exportación TXT
- **WhatsApp**: Integración completa con API externa
- **Exportaciones**: Sistema dinámico de exportación de datos
- **Multitenancy**: Aislamiento de datos por empresa/sucursal
- **RBAC**: Control de acceso granular con roles y permisos

## 🔒 Seguridad

El sistema implementa múltiples capas de seguridad:

- ✅ **Autenticación 2FA**: Doble factor de autenticación
- ✅ **RBAC**: Control de acceso basado en roles (Spatie Permission)
- ✅ **Auditoría Completa**: Logging exhaustivo de todas las acciones
- ✅ **Validación Robusta**: Validación en servidor y cliente
- ✅ **Protección CSRF**: Tokens anti-falsificación
- ✅ **Sanitización**: Limpieza de inputs y outputs
- ✅ **Rate Limiting**: Límites de tasa en APIs
- ✅ **Encriptación**: Datos sensibles cifrados
- ✅ **Sesiones Seguras**: Control y tracking de sesiones activas
- ✅ **SQL Injection Protection**: Query builder y Eloquent ORM

## 🧪 Testing

```bash
# Ejecutar todos los tests
composer run test

# Tests unitarios
php artisan test --testsuite=Unit

# Tests de características
php artisan test --testsuite=Feature

# Tests con cobertura
composer run test:coverage
```

## 🤝 Contribuir

1. Fork el proyecto
2. Crea tu rama de feature (`git checkout -b feature/AmazingFeature`)
3. Commit tus cambios (`git commit -m 'Add some AmazingFeature'`)
4. Push a la rama (`git push origin feature/AmazingFeature`)
5. Abre un Pull Request

## 📞 Soporte

Para soporte técnico y consultas:
- 📧 Email: soporte@laradmin.com
- 📖 Documentación Laravel: [laravel.com/docs](https://laravel.com/docs)
- 🐛 Issue Tracker: [GitHub Issues](link-to-issues)

## 📄 Licencia

Este proyecto es propiedad de **TheizerDev**. Todos los derechos reservados.

El framework Laravel es software open-source licenciado bajo la [licencia MIT](https://opensource.org/licenses/MIT).

---

<p align="center">
  Desarrollado con ❤️ usando 
  <a href="https://laravel.com">Laravel 12</a>, 
  <a href="https://livewire.laravel.com">Livewire</a> y 
  <a href="https://tailwindcss.com">Tailwind CSS</a>
</p>

<p align="center">
  <sub>Plantilla Administrativa Profesional - Built with Laravel by TheizerDev</sub>
</p>
