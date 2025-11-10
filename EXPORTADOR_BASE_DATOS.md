# Exportador de Base de Datos

Este módulo permite exportar datos de cualquier tabla de la base de datos de forma dinámica, con opciones avanzadas de filtrado y selección de columnas.

## Características

- ✅ **Exportación dinámica** de cualquier tabla de la base de datos
- ✅ **Filtros avanzados** con múltiples condiciones
- ✅ **Selección de columnas** específicas
- ✅ **Múltiples formatos**: Excel, CSV, PDF
- ✅ **Interfaz web amigable** con previsualización
- ✅ **Comando Artisan** para automatización
- ✅ **Multiempresa** y **multisucursal**
- ✅ **Proceso asíncrono** con barra de progreso
- ✅ **Formato de datos inteligente** (fechas, monedas, booleanos)

## Uso desde la Interfaz Web

### Acceso
1. Iniciar sesión en el sistema
2. Navegar a **Monitoreo > Exportar Base de Datos**
3. Requerir permiso: `access database export`

### Pasos para Exportar

1. **Seleccionar Tabla**: Elija la tabla que desea exportar del dropdown
2. **Seleccionar Columnas**: 
   - Click en "Seleccionar Todas" para exportar todas
   - Click en columnas individuales para seleccionar específicas
3. **Definir Condiciones** (opcional):
   - Click en "Agregar Condición"
   - Seleccionar columna, operador y valor
   - Ejemplos: `status = activo`, `created_at > 2024-01-01`
4. **Elegir Formato**: Excel, CSV o PDF
5. **Previsualizar**: El sistema muestra el nombre del archivo generado
6. **Exportar**: Click en "Iniciar Exportación"

### Características de la Interfaz

- **Validación en tiempo real** de campos
- **Barra de progreso** durante la exportación
- **Descarga automática** al completar
- **Mensajes de error** detallados
- **Diseño responsive** y moderno

## Uso desde Línea de Comandos

### Comando Básico
```bash
php artisan database:export tabla_estudiantes
```

### Con Opciones
```bash
# Exportar con filtros y columnas específicas
php artisan database:export students \
  --format=excel \
  --conditions="status=activo" \
  --conditions="created_at>2024-01-01" \
  --columns="id,nombre,apellido,email" \
  --output="estudiantes_activos_2024"
```

### Parámetros Disponibles

| Parámetro | Descripción | Ejemplo |
|-----------|-------------|---------|
| `table` | Nombre de la tabla (requerido) | `students` |
| `--format` | Formato de salida | `excel`, `csv`, `pdf` |
| `--conditions` | Condiciones de filtro (múltiples) | `"status=activo"` |
| `--columns` | Columnas a exportar | `"id,nombre,email"` |
| `--output` | Nombre del archivo | `"mi_exportacion"` |

### Ejemplos de Condiciones

```bash
# Igualdad
--conditions="status=activo"

# Mayor que
--conditions="edad>18"

# Contiene texto
--conditions="nombre=like:Juan"

# Múltiples condiciones (AND)
--conditions="status=activo" --conditions="edad>18"
```

## Formatos de Exportación

### Excel (.xlsx)
- **Recomendado** para análisis en Excel/Google Sheets
- Mantiene **formatos de datos** (fechas, monedas)
- **Auto-ajuste** de columnas
- **Estilos aplicados** automáticamente

### CSV
- **Ligero** y compatible con todos los sistemas
- Ideal para **importación** en otros sistemas
- **Sin formato**, solo datos

### PDF
- **Formato de informe** profesional
- Incluye **fecha de generación**
- **Diseño limpio** y legible

## Formato de Datos Inteligente

El sistema aplica formato automático según el tipo de dato:

| Tipo de Dato | Formato Aplicado | Ejemplo |
|--------------|------------------|---------|
| **Fechas** | `d/m/Y H:i` | `15/03/2024 14:30` |
| **Booleanos** | `Sí/No` | `Sí` |
| **JSON** | Texto formateado | `{ "campo": "valor" }` |
| **Moneda** | `1.234,56` | `1.234,56` |
| **Porcentaje** | `12,34%` | `12,34%` |
| **Números** | `1.234` | `1.234` |

## Seguridad y Permisos

### Permisos Requeridos
- `access database export`: Acceso al módulo
- `export database`: Ejecutar exportaciones

### Restricciones de Seguridad
- ✅ **Filtro por empresa** automático
- ✅ **Filtro por sucursal** automático
- ✅ **Validación de permisos** en cada operación
- ✅ **Protección contra inyección SQL**
- ✅ **Validación de entrada** de datos

## Tablas Disponibles

Las siguientes tablas están disponibles para exportación:

### Tablas Principales
- `students` - Estudiantes
- `matriculas` - Matrículas
- `pagos` - Pagos
- `programas` - Programas educativos
- `conceptos_pago` - Conceptos de pago

### Tablas de Configuración
- `empresas` - Empresas
- `sucursales` - Sucursales
- `turnos` - Turnos
- `niveles_educativos` - Niveles educativos
- `school_periods` - Períodos escolares

### Tablas de Sistema
- `users` - Usuarios
- `roles` - Roles
- `permissions` - Permisos
- `activity_log` - Registro de actividad

## Solución de Problemas

### Error: "No tiene permisos"
- Verificar que el usuario tenga el permiso `access database export`
- Contactar al administrador del sistema

### Error: "Tabla no encontrada"
- Verificar el nombre de la tabla (sensible a mayúsculas)
- La tabla debe existir en la base de datos

### Error: "Sin resultados"
- Verificar las condiciones aplicadas
- Intentar sin condiciones para verificar datos existentes

### Exportación Lenta
- Reducir el número de columnas
- Aplicar filtros más específicos
- Exportar en lotes más pequeños

## Mejores Prácticas

1. **Siempre aplicar filtros** cuando sea posible
2. **Seleccionar solo columnas necesarias**
3. **Usar formatos apropiados** según el uso
4. **Verificar datos** antes de exportar grandes volúmenes
5. **Mantener archivos exportados** organizados
6. **Respetar privacidad** de los datos personales

## Soporte Técnico

Para reportar problemas o solicitar nuevas funciones:
- Crear un ticket en el sistema
- Contactar al equipo de desarrollo
- Incluir detalles del error y pasos para reproducir

---

**Versión**: 1.0.0  
**Última actualización**: Noviembre 2024  
**Desarrollado por**: Sistema de Gestión Educativa
