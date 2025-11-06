# Configuración Regional - Guía de Uso

## 📋 Resumen

El sistema de configuración regional permite adaptar automáticamente la moneda, zona horaria, formato de fecha y otros aspectos regionales según el país seleccionado.

## 🚀 Componentes Implementados

### 1. Servicio Principal
- **Archivo**: `app/Services/RegionalConfigurationService.php`
- **Función**: Gestiona la configuración regional global de la aplicación
- **Métodos principales**:
  - `getCurrentConfiguration()`: Obtiene la configuración actual
  - `setRegionalConfiguration($empresa)`: Aplica configuración según empresa
  - `formatMoney($amount)`: Formatea montos según configuración regional
  - `formatDate($date)`: Formatea fechas según configuración regional

### 2. Componentes Livewire

#### Indicador de Configuración
- **Archivo**: `app/Livewire/RegionalConfigurationIndicator.php`
- **Vista**: `resources/views/livewire/regional-configuration-indicator.blade.php`
- **Ubicación**: Barra de navegación (navbar)
- **Función**: Muestra la configuración regional actual y se actualiza automáticamente

#### Componente de Prueba
- **Archivo**: `app/Livewire/TestRegionalConfiguration.php`
- **Vista**: `resources/views/livewire/test-regional-configuration.blade.php`
- **Ruta**: `/test/regional-configuration`
- **Función**: Interfaz para probar la configuración regional con diferentes empresas

### 3. Trait para Componentes
- **Archivo**: `app/Livewire/Traits/HasRegionalConfiguration.php`
- **Función**: Proporciona funcionalidad de configuración regional a componentes Livewire
- **Uso**: Incluir en componentes que necesiten configuración regional

### 4. Middleware
- **Archivo**: `app/Http/Middleware/RegionalConfiguration.php`
- **Función**: Aplica configuración regional automáticamente a todas las rutas web

### 5. Helpers (Opcional)
- **Archivo**: `app/Helpers/RegionalHelper.php`
- **Funciones disponibles**:
  - `get_regional_config($key = null)`
  - `get_current_currency()`
  - `get_current_timezone()`
  - `get_current_locale()`
  - `get_current_date_format()`
  - `get_current_currency_symbol()`

## 🧪 Comandos de Prueba

### Verificar Configuración
```bash
php artisan test:regional-configuration
```

### Verificar Configuración Completa
```bash
php artisan test:regional-configuration-complete
```

### Verificar Todos los Componentes
```bash
php artisan verify:regional-configuration
```

### Probar con Empresa Específica
```bash
php artisan test:regional-configuration-complete --empresa_id=1
```

## 📝 Uso en Componentes

### Para Componentes Livewire
```php
use App\Livewire\Traits\HasRegionalConfiguration;

class MiComponente extends Component
{
    use HasRegionalConfiguration;
    
    public function mount()
    {
        // Inicializar configuración
        $this->initializeRegionalConfiguration($empresa->pais);
    }
    
    public function save()
    {
        // Aplicar configuración a empresa
        $this->applyRegionalConfigurationToEmpresa($empresa);
    }
}
```

### Para Formatear Dinero y Fechas
```php
use App\Services\RegionalConfigurationService;

// Formatear dinero
$importeFormateado = RegionalConfigurationService::formatMoney(1234.56);

// Formatear fecha
$fechaFormateada = RegionalConfigurationService::formatDate($fecha);
```

## 🌍 Configuración por País

Los países deben tener configurados estos campos en la base de datos:
- `moneda`: Código de moneda (ej: USD, EUR, Bs)
- `zona_horaria`: Zona horaria (ej: America/Caracas, Europe/Madrid)
- `formato_fecha`: Formato de fecha (ej: d/m/Y, m/d/Y)
- `formato_moneda`: Formato numérico (ej: #,##0.00, ###0.00)
- `simbolo_moneda`: Símbolo de moneda (ej: $, €, Bs)
- `idioma`: Código de idioma (ej: es, en)

## 🔧 Solución de Problemas

### El Helper No Funciona
- Verificar que `composer dump-autoload` se haya ejecutado
- El helper se carga automáticamente mediante composer.json

### La Configuración No Se Aplica
- Verificar que el middleware esté registrado en `bootstrap/app.php`
- Verificar que la empresa tenga país asignado
- Verificar que el país tenga configuración completa

### El Indicador No Se Actualiza
- Verificar que el componente esté incluido en el navbar
- Verificar que los eventos Livewire se estén emitiendo correctamente

## 🎯 Próximos Pasos

1. **Integración Completa**: Aplicar el trait a todos los componentes que manejen dinero o fechas
2. **Validación**: Agregar validación de formatos regionales en formularios
3. **Reportes**: Aplicar configuración regional a todos los reportes y exportaciones
4. **Base de Datos**: Asegurar que todos los países tengan configuración completa
5. **Testing**: Crear pruebas automatizadas para diferentes configuraciones regionales
