# Sistema de Reenvío Automático de Mensajes WhatsApp Fallidos

## 📋 Descripción

Este sistema permite reenviar automáticamente mensajes de WhatsApp que hayan fallado o estén en estado "simulado" (sin message_id real). Incluye funcionalidades de reenvío manual, programado y automático con monitoreo y estadísticas.

## ✨ Características

- **Reenvío Manual**: Reenvío individual o masivo de mensajes fallidos
- **Reenvío Programado**: Programación de reenvíos con parámetros configurables
- **Reenvío Automático**: Configuración de tareas cron para reenvío periódico
- **Estadísticas**: Dashboard con métricas y gráficos de rendimiento
- **Monitoreo**: Verificación del estado del servicio WhatsApp
- **Control de Reintentos**: Límite de 3 reintentos por mensaje por defecto

## 🚀 Instalación

### 1. Migración de Base de Datos

El sistema requiere un campo adicional en la tabla `whatsapp_messages`:

```bash
php artisan migrate --path=database/migrations/2024_12_19_000000_add_retry_count_to_whatsapp_messages_table.php
```

### 2. Registro de Comandos

Los comandos ya están registrados en `app/Console/Kernel.php`:

```php
protected $commands = [
    // ... otros comandos
    \App\Console\Commands\RetryFailedWhatsAppMessages::class,
    \App\Console\Commands\ScheduleWhatsAppRetry::class,
    \App\Console\Commands\SetupWhatsAppAutoRetry::class,
    \App\Console\Commands\WhatsAppRetryStatus::class,
];
```

### 3. Programación de Tareas

Las tareas automáticas ya están configuradas en el scheduler:

```php
// En app/Console/Kernel.php
$schedule->command('whatsapp:schedule-retry')
    ->hourly()
    ->when(function () {
        return WhatsAppMessage::where('direction', 'outbound')
            ->retryable()
            ->exists();
    });
```

## 📖 Uso

### Interfaz Web

#### Acceso al Sistema de Reenvío
1. Navegar a: `/admin/whatsapp/retry-failed`
2. Verificar el estado del servicio WhatsApp
3. Usar los botones de acción:
   - **Reenviar Seleccionados**: Reenvío manual de mensajes seleccionados
   - **Programar Reenvío**: Configurar reenvío programado
   - **Reenvío Manual**: Ejecutar reenvío inmediato

#### Estadísticas
1. Navegar a: `/admin/whatsapp/retry-stats`
2. Ver métricas en tiempo real
3. Analizar gráficos de tendencias
4. Consultar distribución por estado y reintentos

### Comandos de Consola

#### 1. Reenvío Manual Inmediato
```bash
# Reenviar mensajes de los últimos 7 días con máximo 3 reintentos
php artisan whatsapp:schedule-retry --days=7 --max-retries=3

# Forzar reenvío sin verificar el servicio
php artisan whatsapp:schedule-retry --days=7 --max-retries=3 --force
```

#### 2. Configuración de Reenvío Automático
```bash
# Configurar reenvío diario a las 2 AM
php artisan whatsapp:setup-auto-retry --interval=daily --days=7 --max-retries=3

# Configurar reenvío cada hora
php artisan whatsapp:setup-auto-retry --interval=hourly --days=7 --max-retries=3

# Configurar reenvío semanal
php artisan whatsapp:setup-auto-retry --interval=weekly --days=7 --max-retries=3

# Desactivar reenvío automático
php artisan whatsapp:setup-auto-retry --disable
```

#### 3. Ver Estado del Sistema
```bash
# Ver estado básico
php artisan whatsapp:retry-status

# Ver estado detallado con estadísticas
php artisan whatsapp:retry-status --days=7 --detailed
```

## 📊 Métricas y Estadísticas

### Métricas Principales
- **Mensajes Reenviables**: Mensajes fallidos o simulados pendientes
- **Máximos Reintentos Excedidos**: Mensajes que han superado el límite de reintentos
- **Reenvíos Exitosos**: Mensajes reenviados con éxito
- **Tasa de Éxito**: Porcentaje de reenvíos exitosos

### Distribución por Estado
- **Pending**: Mensajes pendientes de reenvío
- **Sent**: Mensajes reenviados exitosamente
- **Failed**: Mensajes que fallaron en el reenvío
- **Delivered**: Mensajes entregados
- **Read**: Mensajes leídos

### Control de Reintentos
- Cada mensaje puede ser reenviado máximo 3 veces
- El contador se almacena en el campo `retry_count`
- Los mensajes que exceden el límite se marcan como "max_retries_exceeded"

## 🔧 Configuración

### Parámetros de Reenvío

| Parámetro | Descripción | Valor por Defecto |
|-----------|-------------|-------------------|
| `--days` | Días hacia atrás para buscar mensajes | 7 |
| `--max-retries` | Máximo de reintentos por mensaje | 3 |
| `--interval` | Frecuencia del reenvío automático | hourly |
| `--force` | Forzar reenvío sin verificar servicio | false |

### Frecuencias Disponibles
- **hourly**: Cada hora
- **daily**: Diariamente (requiere especificar hora)
- **weekly**: Semanalmente

## 🛡️ Seguridad y Validaciones

### Verificaciones del Sistema
1. **Disponibilidad del Servicio**: Se verifica que WhatsApp esté disponible antes de reenviar
2. **Límite de Reintentos**: Control estricto del número máximo de reintentos
3. **Validación de Mensajes**: Solo se reenvían mensajes que cumplen los criterios
4. **Logs de Auditoría**: Registro completo de todas las operaciones

### Criterios de Reenvío
Un mensaje es reenviable si:
- Tiene dirección "outbound" (saliente)
- Está en estado "failed" o tiene error_message
- Es "simulado" (estado "sent" pero sin message_id real)
- No ha excedido el máximo de reintentos (3 por defecto)
- Fue creado en el período especificado

## 📋 Solución de Problemas

### Problemas Comunes

#### "Servicio de WhatsApp no disponible"
- Verificar la conexión con el servicio WhatsApp
- Revisar las credenciales y configuración
- Usar `--force` para omitir la verificación (no recomendado)

#### "No hay mensajes para reenviar"
- Verificar que existan mensajes fallidos en el período especificado
- Revisar los filtros aplicados
- Verificar el estado de los mensajes en la base de datos

#### "Máximo de reintentos excedido"
- Los mensajes han sido reenviados 3 veces sin éxito
- Revisar manualmente estos casos
- Considerar contactar al destinatario por otro medio

### Comandos de Diagnóstico
```bash
# Verificar estado del sistema
php artisan whatsapp:retry-status --detailed

# Probar conexión WhatsApp
php artisan whatsapp:test-connection

# Ver logs de reenvío
tail -f storage/logs/laravel.log | grep -i "retry"
```

## 🔗 Integración

### Con Sistema de Plantillas
El sistema utiliza las plantillas de WhatsApp para reenviar mensajes:
- Procesa las variables de la plantilla
- Mantiene el contenido original
- Registra el nuevo envío en el historial

### Con Sistema de Programación
Se integra con el sistema existente de mensajes programados:
- Respeta la lógica de reintentos existente
- Actualiza el estado de los mensajes
- Mantiene la trazabilidad completa

## 📈 Mejores Prácticas

1. **Monitoreo Regular**: Revisar las estadísticas semanalmente
2. **Ajuste de Parámetros**: Adaptar días y reintentos según necesidad
3. **Validación de Datos**: Mantener limpia la base de mensajes fallidos
4. **Comunicación**: Informar a usuarios sobre mensajes fallidos
5. **Backup**: Mantener respaldos de mensajes importantes

## 📝 Notas Adicionales

- El sistema respeta los límites de rate de WhatsApp
- Los mensajes se procesan con un delay de 0.5 segundos entre cada uno
- Se recomienda no exceder 100 mensajes por lote
- Los logs se guardan en `storage/logs/laravel.log`
- Las estadísticas se actualizan en tiempo real