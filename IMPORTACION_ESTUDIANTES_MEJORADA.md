# 📚 Módulo de Importación de Estudiantes - Mejorado

## 🎯 Características Principales

### ✅ Mejoras Implementadas

1. **Vista Previa Completa**
   - Visualización de todas las filas antes de importar
   - Contador de filas totales
   - Selección individual o masiva de filas

2. **Selección de Filas**
   - Checkbox para seleccionar/deseleccionar todas las filas
   - Selección individual por fila
   - Contador de filas seleccionadas

3. **Actualización Inteligente**
   - Si el estudiante existe (por documento), se actualiza
   - Si no existe, se crea uno nuevo
   - Opción configurable para activar/desactivar actualización

4. **Manejo de Datos Faltantes**
   - Opción para llenar campos vacíos con 'n/a'
   - Validación de campos obligatorios
   - Filtrado automático de valores inválidos

5. **Mapeo Automático de Columnas**
   - Detección inteligente de columnas por nombre
   - Mapeo manual personalizable
   - Soporte para múltiples idiomas en encabezados

6. **Proceso en 4 Pasos**
   - Paso 1: Cargar archivo
   - Paso 2: Vista previa y selección
   - Paso 3: Mapeo de columnas
   - Paso 4: Importación con progreso

7. **Conversión Automática de Fechas**
   - Soporte para fechas de Excel (números seriales)
   - Conversión automática de formatos de fecha
   - Manejo de fechas inválidas

8. **Progreso en Tiempo Real**
   - Barra de progreso visual
   - Contadores de: creados, actualizados, fallidos
   - Lista detallada de errores

## 📋 Formato del Archivo Excel/CSV

### Columnas Soportadas

#### Datos del Estudiante (Obligatorios)
- **Nombres** * (requerido)
- **Apellidos** * (requerido)
- **Documento de Identidad** * (requerido)

#### Datos del Estudiante (Opcionales)
- Fecha de Nacimiento (formato: YYYY-MM-DD o fecha de Excel)
- Grado
- Sección
- Correo Electrónico
- Nivel Educativo (nombre del nivel)
- Turno (nombre del turno)
- Período Escolar (nombre del período)

#### Datos del Representante (Opcionales)
- Representante Nombres
- Representante Apellidos
- Representante Documento
- Representante Teléfonos (separar múltiples con comas)
- Representante Correo

### Ejemplo de Estructura Excel

```
| Nombres | Apellidos | Documento | Fecha Nacimiento | Grado | Sección | Nivel Educativo | Turno | Representante Nombres | Representante Teléfonos |
|---------|-----------|-----------|------------------|-------|---------|-----------------|-------|-----------------------|-------------------------|
| Juan    | Pérez     | 12345678  | 2010-05-15      | 5to   | A       | Primaria        | Mañana| María Pérez           | 555-1234,555-5678      |
| Ana     | García    | 87654321  | 2011-08-20      | 4to   | B       | Primaria        | Tarde | Carlos García         | 555-9999               |
```

## 🚀 Cómo Usar el Módulo

### Paso 1: Preparar el Archivo
1. Crea un archivo Excel (.xlsx, .xls) o CSV (.csv)
2. La primera fila debe contener los encabezados
3. Las siguientes filas contienen los datos de los estudiantes
4. Asegúrate de incluir al menos: Nombres, Apellidos y Documento

### Paso 2: Cargar el Archivo
1. Accede a: `/admin/students/import`
2. Haz clic en "Seleccionar Archivo"
3. Elige tu archivo Excel o CSV
4. El sistema procesará automáticamente el archivo

### Paso 3: Vista Previa y Configuración
1. Revisa la vista previa de los datos
2. Configura las opciones:
   - ✅ **Actualizar existentes**: Si está marcado, actualizará estudiantes que ya existan
   - ✅ **Llenar vacíos con 'n/a'**: Completará campos vacíos automáticamente
3. Selecciona las filas que deseas importar:
   - Usa el checkbox superior para seleccionar/deseleccionar todas
   - O selecciona individualmente cada fila
4. Haz clic en "Siguiente"

### Paso 4: Mapeo de Columnas
1. El sistema intentará mapear automáticamente las columnas
2. Verifica que cada campo esté correctamente mapeado
3. Los campos marcados con * son obligatorios
4. Ajusta manualmente si es necesario
5. Haz clic en "Iniciar Importación"

### Paso 5: Importación
1. Observa el progreso en tiempo real
2. Revisa los contadores:
   - **Creados**: Nuevos estudiantes agregados
   - **Actualizados**: Estudiantes existentes modificados
   - **Fallidos**: Filas con errores
3. Si hay errores, revisa la lista detallada
4. Al finalizar, haz clic en "Ver Estudiantes" o "Nueva Importación"

## 🔍 Detección de Estudiantes Existentes

El sistema identifica estudiantes existentes por:
- **Documento de Identidad** (campo único)
- **Empresa ID** (multitenancy)

Si encuentra un estudiante existente:
- Con "Actualizar existentes" ✅: Actualiza sus datos
- Con "Actualizar existentes" ❌: Omite la fila

## ⚠️ Manejo de Errores

### Errores Comunes

1. **"Datos insuficientes"**
   - Falta nombres, apellidos o documento
   - Solución: Completa los campos obligatorios

2. **"Formato de fecha inválido"**
   - La fecha no se puede interpretar
   - Solución: Usa formato YYYY-MM-DD o fechas de Excel

3. **"Nivel educativo no encontrado"**
   - El nombre del nivel no existe en el sistema
   - Solución: Crea el nivel primero o ajusta el nombre

4. **"Turno no encontrado"**
   - El nombre del turno no existe
   - Solución: Crea el turno primero o ajusta el nombre

### Valores Inválidos Automáticamente Filtrados

El sistema ignora estos valores y los trata como vacíos:
- `n/a`, `N/A`, `na`, `NA`
- `null`, `NULL`
- `undefined`
- Espacios vacíos
- `0` (cero)

## 📊 Límites y Rendimiento

- **Tamaño máximo de archivo**: 10 MB
- **Vista previa**: Primeras 100 filas
- **Importación**: Sin límite (procesamiento por lotes)
- **Formatos soportados**: .xlsx, .xls, .csv

## 🎨 Interfaz de Usuario

### Indicadores Visuales

- **Paso activo**: Círculo azul con número
- **Paso completado**: Línea azul conectora
- **Progreso**: Barra animada con porcentaje
- **Contadores**: Cards con colores:
  - Verde: Creados
  - Azul: Actualizados
  - Rojo: Fallidos

### Mensajes

- ✅ **Éxito**: Fondo verde con ícono de check
- ❌ **Error**: Fondo rojo con ícono de alerta
- ℹ️ **Info**: Fondo azul con ícono de información

## 🔧 Configuración Técnica

### Campos Generados Automáticamente

- **Código**: Generado automáticamente (formato: STU + 6 caracteres aleatorios)
- **Empresa ID**: Del usuario autenticado
- **Sucursal ID**: Del usuario autenticado
- **Status**: Activo por defecto

### Validaciones

- Documento de identidad único por empresa
- Fechas válidas (no futuras)
- Emails con formato válido
- Teléfonos separados por comas

## 📝 Notas Importantes

1. **Multitenancy**: Los estudiantes se crean en la empresa/sucursal del usuario autenticado
2. **Transacciones**: La importación usa transacciones de base de datos para garantizar integridad
3. **Logs**: Todos los errores se registran en el log de Laravel
4. **Seguridad**: Solo usuarios autenticados con permisos pueden importar

## 🆕 Diferencias con el Módulo Anterior

| Característica | Anterior | Nuevo |
|----------------|----------|-------|
| Vista previa | Limitada | Completa con scroll |
| Selección de filas | No | Sí, individual y masiva |
| Mapeo automático | No | Sí, inteligente |
| Progreso visual | Básico | Avanzado con contadores |
| Manejo de errores | Lista simple | Detallado con contexto |
| UX | Compleja | Simplificada en 4 pasos |
| Actualización | Siempre | Configurable |
| Datos faltantes | Error | Configurable (n/a) |

## 🎯 Casos de Uso

### Caso 1: Importación Inicial
- Archivo con 500 estudiantes nuevos
- Activar "Llenar vacíos con 'n/a'"
- Desactivar "Actualizar existentes"
- Resultado: 500 estudiantes creados

### Caso 2: Actualización Masiva
- Archivo con 200 estudiantes existentes
- Activar "Actualizar existentes"
- Activar "Llenar vacíos con 'n/a'"
- Resultado: 200 estudiantes actualizados

### Caso 3: Importación Mixta
- Archivo con 300 estudiantes (150 nuevos, 150 existentes)
- Activar ambas opciones
- Resultado: 150 creados, 150 actualizados

## 🔗 Rutas

- **Importación**: `/admin/students/import`
- **Listado**: `/admin/students`
- **Crear manual**: `/admin/students/crear`

## 📞 Soporte

Si encuentras algún problema:
1. Revisa los logs en `storage/logs/laravel.log`
2. Verifica que los datos del archivo sean correctos
3. Asegúrate de tener los permisos necesarios
4. Contacta al administrador del sistema
