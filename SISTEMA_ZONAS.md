# Sistema de Zonas de Acceso

## Descripción General

El sistema de **Zonas de Acceso** permite gestionar y controlar las áreas o zonas específicas a las que cada usuario tiene acceso dentro del sistema. Esta funcionalidad es especialmente útil para implementar controles de acceso granular basados en ubicaciones físicas o áreas lógicas de la organización.

## Características Principales

- ✅ **Relación Muchos a Muchos**: Un usuario puede tener acceso a múltiples zonas, y una zona puede tener múltiples usuarios asignados.
- ✅ **Zonas Globales y por Sucursal**: Las zonas pueden ser globales (aplican a toda la empresa) o específicas de una sucursal.
- ✅ **Control Multitenant**: Las zonas están aisladas por empresa y sucursal.
- ✅ **Gestión Completa**: CRUD completo de zonas con interfaz intuitiva.
- ✅ **Asignación Dinámica**: Selector de zonas integrado en los formularios de creación y edición de usuarios.
- ✅ **Auditoría**: Registro completo de cambios en zonas y asignaciones.

## Estructura de Base de Datos

### Tabla `zonas`

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | BIGINT UNSIGNED | Identificador único |
| nombre | VARCHAR(255) | Nombre descriptivo de la zona |
| codigo | VARCHAR(100) | Código único opcional (ej: ZN-001) |
| descripcion | TEXT | Descripción detallada de la zona |
| empresa_id | BIGINT UNSIGNED | ID de la empresa propietaria |
| sucursal_id | BIGINT UNSIGNED NULL | ID de la sucursal (NULL = zona global) |
| activo | BOOLEAN | Estado de la zona (true/false) |
| created_at | TIMESTAMP | Fecha de creación |
| updated_at | TIMESTAMP | Fecha de última actualización |

**Índices:**
- Primary Key: `id`
- Unique: `codigo`
- Index: `(empresa_id, sucursal_id)`
- Index: `activo`

**Foreign Keys:**
- `empresa_id` → `empresas.id` (CASCADE DELETE)
- `sucursal_id` → `sucursales.id` (CASCADE DELETE)

### Tabla `user_zona` (Pivote)

| Campo | Tipo | Descripción |
|-------|------|-------------|
| id | BIGINT UNSIGNED | Identificador único |
| user_id | BIGINT UNSIGNED | ID del usuario |
| zona_id | BIGINT UNSIGNED | ID de la zona |
| created_at | TIMESTAMP | Fecha de asignación |
| updated_at | TIMESTAMP | Fecha de última actualización |

**Índices:**
- Primary Key: `id`
- Unique: `(user_id, zona_id)` - Previene duplicados
- Index: `user_id`
- Index: `zona_id`

**Foreign Keys:**
- `user_id` → `users.id` (CASCADE DELETE)
- `zona_id` → `zonas.id` (CASCADE DELETE)

## Modelos Eloquent

### Modelo Zona

```php
namespace App\Models;

class Zona extends Model
{
    use HasFactory, LogsActivity, HasSpanishActivityLog, Multitenantable;

    // Relaciones
    public function users()
    {
        return $this->belongsToMany(User::class, 'user_zona')
            ->withTimestamps();
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    // Scopes
    public function scopeActivas($query)
    {
        return $query->where('activo', true);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    public function scopePorSucursal($query, $sucursalId)
    {
        return $query->where('sucursal_id', $sucursalId);
    }
}
```

### Relación en Modelo User

```php
// En app/Models/User.php

/**
 * Get the zonas assigned to the user.
 */
public function zonas()
{
    return $this->belongsToMany(Zona::class, 'user_zona')
        ->withTimestamps();
}
```

## Trait Multitenantable Actualizado

El trait `Multitenantable` ha sido mejorado para soportar el filtrado por zonas:

```php
// Nuevo scope para filtrar por zonas del usuario
public function scopePorZonasUsuario(Builder $query, ?int $userId = null)
{
    $userId = $userId ?? auth()->id();
    
    if (!$userId) {
        return $query;
    }
    
    $user = auth()->user() ?? \App\Models\User::find($userId);
    
    if (!$user || $user->hasRole('Super Administrador')) {
        return $query;
    }
    
    // Obtener las zonas del usuario
    $zonasIds = $user->zonas()->pluck('zonas.id')->toArray();
    
    if (empty($zonasIds)) {
        return $query->whereRaw('1 = 0'); // Sin resultados
    }
    
    // Filtrar por zona_id si el modelo lo tiene
    if (in_array('zona_id', $this->getFillable())) {
        return $query->whereIn('zona_id', $zonasIds);
    }
    
    return $query;
}
```

## Componentes Livewire

### 1. Gestión de Zonas

#### Index (`App\Livewire\Admin\Zonas\Index`)
- Listado paginado de zonas
- Filtros por búsqueda, sucursal y estado
- Acciones: editar, eliminar, toggle activo/inactivo
- Contador de usuarios por zona

#### Create (`App\Livewire\Admin\Zonas\Create`)
- Formulario para crear nuevas zonas
- Validación de campos obligatorios
- Selección de sucursal (opcional para zonas globales)
- Código único automático opcional

#### Edit (`App\Livewire\Admin\Zonas\Edit`)
- Formulario prellenado para editar zonas
- Misma validación que Create
- Actualización de datos existentes

### 2. Selector de Zonas para Usuarios

#### ZonaSelector (`App\Livewire\Admin\Users\ZonaSelector`)
- Componente reutilizable para asignar zonas a usuarios
- Carga dinámica según empresa/sucursal del usuario
- Checkbox múltiple con contador de selección
- Guardado asíncrono con feedback visual

## Integración en Formularios de Usuarios

Los componentes `Create` y `Edit` de usuarios han sido actualizados para incluir:

1. **Propiedades nuevas:**
   ```php
   public $zonasDisponibles = [];
   public $selectedZonas = [];
   ```

2. **Métodos de carga:**
   ```php
   public function loadZonasDisponibles()
   {
       // Carga zonas según empresa y sucursal
   }
   ```

3. **Guardado/Sincronización:**
   ```php
   // En Create
   if (!empty($this->selectedZonas)) {
       $user->zonas()->attach($this->selectedZonas);
   }

   // En Edit
   $user->zonas()->sync($this->selectedZonas);
   ```

4. **Vista Blade:**
   - Sección de checkboxes agrupados
   - Visualización clara de zonas disponibles
   - Contador de zonas seleccionadas

## Rutas

Las rutas de gestión de zonas están en `routes/admin/zonas.php`:

```php
Route::middleware(['checkAdminPermission:access zonas'])->group(function () {
    Route::get('/zonas', ZonasIndex::class)->name('zonas.index');
    Route::get('/zonas/crear', ZonasCreate::class)->name('zonas.create');
    Route::get('/zonas/{zona}/editar', ZonasEdit::class)->name('zonas.edit');
});
```

## Seeder

El seeder `ZonaSeeder` crea zonas de ejemplo:

```bash
php artisan db:seed --class=ZonaSeeder
```

Crea automáticamente:
- Zonas globales de empresa (Administrativa, Atención al Cliente)
- Zonas por sucursal (Planta Baja, Primer Piso, etc.)

## Casos de Uso

### 1. Control de Acceso Físico
```php
// Verificar si un usuario tiene acceso a una zona específica
if ($user->zonas->contains($zonaId)) {
    // Permitir acceso
}
```

### 2. Filtrado de Datos por Zona
```php
// En un modelo con zona_id
$datos = Modelo::porZonasUsuario()->get();
```

### 3. Reportes por Zona
```php
// Obtener todos los usuarios de una zona
$usuarios = $zona->users()->get();

// Obtener todas las zonas de un usuario
$zonas = $user->zonas()->activas()->get();
```

### 4. Asignación Masiva
```php
// Asignar múltiples zonas a un usuario
$user->zonas()->sync([1, 2, 3]);

// Agregar zona sin quitar las existentes
$user->zonas()->attach([4, 5]);

// Quitar zona específica
$user->zonas()->detach([2]);
```

## Mejores Prácticas

1. **Siempre usar `sync()` en actualizaciones** para evitar duplicados
2. **Validar permisos** antes de mostrar/ocultar zonas
3. **Usar zonas activas** por defecto en consultas
4. **Documentar códigos de zona** para mantener consistencia
5. **Considerar rendimiento** con muchas zonas (usar índices)

## Ejemplos de Consulta

```php
// Todas las zonas activas de una empresa
Zona::porEmpresa($empresaId)->activas()->get();

// Zonas de una sucursal específica
Zona::porSucursal($sucursalId)->activas()->get();

// Usuarios con acceso a zona específica
User::whereHas('zonas', function($q) use ($zonaId) {
    $q->where('zona_id', $zonaId);
})->get();

// Zonas sin usuarios asignados
Zona::doesntHave('users')->get();

// Contar usuarios por zona
Zona::withCount('users')->get();
```

## Consideraciones de Seguridad

- ✅ Las zonas están aisladas por empresa (multitenancy)
- ✅ Solo administradores pueden gestionar zonas
- ✅ Los usuarios solo ven zonas de su empresa/sucursal
- ✅ Auditoría completa de cambios en zonas
- ✅ Prevención de eliminación de zonas con usuarios asignados

## Futuras Mejoras

- [ ] Permisos específicos por zona (no solo acceso)
- [ ] Horarios de acceso por zona
- [ ] Geolocalización de zonas (mapas)
- [ ] Grupos de zonas
- [ ] Plantillas de zonas predefinidas
- [ ] Importación/exportación de zonas
- [ ] API RESTful para gestión de zonas

## Soporte

Para preguntas o problemas relacionados con el sistema de zonas, consulte la documentación general del proyecto o contacte al equipo de desarrollo.
