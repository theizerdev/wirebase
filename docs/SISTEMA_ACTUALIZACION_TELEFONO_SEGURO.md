# Sistema de Actualización de Teléfono con Aprobación y Seguridad

## Descripción General

Este sistema implementa un flujo seguro de 5 pasos para que los pastores actualicen su número de teléfono, requiriendo aprobación del presbítero y configuración obligatoria de seguridad.

### Diagrama del Flujo

```
PASTOR (/busqueda)
    ↓
¿Tiene protección activada?
    ├─ SÍ → Mostrar OTP → Verificar → Permitir editar datos
    └─ NO → Solicitar cambio teléfono → Crear solicitud → WhatsApp al Presbítero
                ↓
        Esperando aprobación...
                ↓
        PRESBÍTERO (recibe WhatsApp)
                ↓
        ¿Aprueba o Rechaza?
            ├─ RECHAZA → Fin (pastor puede intentar de nuevo)
            └─ APRUEBA → Actualiza teléfono → WhatsApp al Pastor
                            ↓
                    Pastor debe configurar seguridad
                            ↓
                    CONFIGURAR SEGURIDAD
                    - 3 preguntas
                    - 10 backup codes
                    - Activar protección
                            ↓
                    PROTECCIÓN ACTIVADA ✅
                    - Puede editar con OTP/seguridad
                    - Rate limiting (3 intentos/15min)
```

## Flujo Completo

### Paso 1: Pastor Solicita Cambio de Teléfono
**Ubicación**: `/busqueda`

**Proceso CORRECTO**:
1. Pastor busca su cédula en el sistema
2. Hace clic en su tarjeta
3. **Sistema verifica si tiene protección activada:**
   - **SI tiene protección**: Muestra modal OTP para verificar identidad → luego permite editar
   - **NO tiene protección**: Muestra modal para solicitar cambio de teléfono
4. Pastor ingresa nuevo número de teléfono
5. Sistema verifica zona del pastor
6. Busca Presbítero en la misma zona
7. Crea solicitud con token único (48 horas de expiración)
8. Envía WhatsApp al Presbítero con link de autorización
9. Pastor ve mensaje "Esperando aprobación del presbítero"

**IMPORTANTE**: NO se envía OTP al pastor en este paso. El OTP solo se usa cuando el pastor YA tiene protección activada y quiere editar sus datos.

**Archivos involucrados**:
- `app/Livewire/Public/Pastores/Busqueda.php` - Método `solicitarCambioTelefono()`
- `resources/views/livewire/public/pastores/busqueda.blade.php` - Modal de solicitud
- `app/Services/PastorAuthorizationService.php` - Método `crearSolicitud()`

### Paso 2: Presbítero Recibe Notificación
**Ubicación**: Link recibido por WhatsApp

**Proceso**:
1. Presbítero recibe mensaje WhatsApp con detalles de la solicitud
2. Hace clic en el link de autorización
3. Ve datos completos del pastor y solicitud
4. Puede aprobar o rechazar la solicitud

**Archivos involucrados**:
- `app/Livewire/Public/PastorAutorizacion.php` - Componente de autorización
- `resources/views/livewire/public/pastor-autorizacion.blade.php` - Vista de autorización
- Ruta: `/pastor/autorizar/{token}`

### Paso 3: Aprobación de la Solicitud
**Proceso**:
1. Presbítero aprueba la solicitud
2. Teléfono del pastor se actualiza automáticamente
3. Pastor recibe WhatsApp de confirmación con link para configurar seguridad
4. Solicitud cambia estado a "aprobado"

**Archivos involucrados**:
- `app/Services/PastorAuthorizationService.php` - Método `aprobarSolicitud()`
- Método privado `enviarConfirmacionAlPastor()` - Envía WhatsApp al pastor

**Mensaje WhatsApp enviado al pastor**:
```
✅ Solicitud Aprobada

Estimado(a) [Nombre],

Su solicitud para cambiar su número de teléfono ha sido APROBADA por el Presbítero [Nombre].

📱 Nuevo teléfono: [número]

⚠️ IMPORTANTE: Ahora debe configurar sus preguntas de seguridad para proteger su cuenta.

Para configurar su seguridad, visite: [link]
```

### Paso 4: Configuración de Seguridad
**Ubicación**: `/admin/pastores/{pastor}/configurar-seguridad`

**Proceso**:
1. Pastor configura 3 preguntas de seguridad con respuestas
2. Las respuestas se guardan hasheadas (no se pueden recuperar)
3. Sistema genera 10 códigos de respaldo (backup codes)
4. Pastor debe guardar los códigos (solo se muestran una vez)
5. Pastor activa la protección
6. Solicitud se marca como "completado"

**Archivos involucrados**:
- `app/Livewire/Admin/Pastores/ConfigurarSeguridadPastor.php` - Componente de configuración
- `resources/views/livewire/admin/pastores/configurar-seguridad-pastor.blade.php` - Vista multi-paso
- `app/Models/PreguntasSeguridadPastor.php` - Modelo con métodos de seguridad

**Características de seguridad**:
- Preguntas disponibles predefinidas (10 opciones)
- Respuestas hasheadas con bcrypt
- 10 backup codes alfanuméricos de 8 caracteres
- Códigos también hasheados en la base de datos

### Paso 5: Protección Activada ✅
**Funcionalidades activas**:

1. **Verificación antes de editar**: 
   - Al intentar modificar datos, se requiere autenticación
   - Opción 1: Responder 3 preguntas de seguridad
   - Opción 2: Usar uno de los 10 backup codes

2. **Backup codes para recuperación**:
   - Cada código solo se puede usar una vez
   - Se pueden regenerar si es necesario
   - Contador de códigos disponibles

3. **Rate limiting**:
   - Máximo 3 intentos fallidos
   - Bloqueo temporal de 15 minutos después del 3er intento
   - Reset automático después del período de bloqueo

**Archivos involucrados**:
- `app/Livewire/Public/Pastores/Editar.php` - Verificación de seguridad en método `save()`
- `resources/views/livewire/public/pastores/editar.blade.php` - Modal de verificación
- `app/Http/Middleware/VerifyPastorSecurityProtection.php` - Middleware de protección
- `app/Models/PreguntasSeguridadPastor.php` - Métodos `verificarRespuestas()`, `verificarBackupCode()`, `estaBloqueado()`

## Base de Datos

### Tabla: `solicitud_modificacion_pastores`
- `id` - Primary key
- `pastor_id` - Foreign key a pastores
- `presbitero_user_id` - Foreign key a users (presbítero asignado)
- `token` - Token único de 64 caracteres
- `telefono_nuevo` - Teléfono solicitado
- `estado` - Enum: pendiente, aprobado, rechazado, expirado, completado
- `token_expires_at` - Timestamp de expiración (48 horas)
- `aprobado_en` - Timestamp de aprobación
- `ip_solicitud`, `ip_aprobacion` - IPs para auditoría
- `metadata` - JSON con información adicional

### Tabla: `preguntas_seguridad_pastores`
- `id` - Primary key
- `pastor_id` - Foreign key a pastores
- `preguntas` - JSON array con preguntas y respuestas hasheadas
- `backup_codes` - JSON array con códigos hasheados
- `intentos_fallidos` - Contador de intentos fallidos
- `ultimo_intento` - Timestamp del último intento
- `activado` - Boolean indicando si la protección está activa

## Middleware de Protección

**Archivo**: `app/Http/Middleware/VerifyPastorSecurityProtection.php`

**Alias registrado**: `verify.pastor.security`

**Aplicado a**: Ruta `/pastor/{pastor}/actualizar`

**Funcionalidad**:
1. Verifica si el pastor existe en la ruta
2. Si no tiene protección activada y hay solicitud aprobada → redirige a configurar seguridad
3. Si tiene protección pero está bloqueado → muestra error con tiempo restante
4. Si todo está correcto → permite acceso normal

## Servicios Clave

### PastorAuthorizationService
**Ubicación**: `app/Services/PastorAuthorizationService.php`

**Métodos principales**:
- `crearSolicitud(Pastor $pastor, string $telefonoNuevo)` - Crea solicitud y notifica presbítero
- `aprobarSolicitud(SolicitudModificacionPastor $solicitud, User $aprobador, string $ip)` - Aprueba y notifica pastor
- `rechazarSolicitud(SolicitudModificacionPastor $solicitud, User $rechazador, string $motivo)` - Rechaza solicitud
- `completarSolicitud(SolicitudModificacionPastor $solicitud)` - Marca como completada
- `obtenerSolicitudPorToken(string $token)` - Obtiene solicitud válida por token
- `buscarPresbiteroPorZona(string $zona, int $empresaId)` - Busca presbítero en zona
- `enviarNotificacionWhatsApp()` - Envía WhatsApp al presbítero
- `enviarConfirmacionAlPastor()` - Envía WhatsApp de confirmación al pastor

### PreguntasSeguridadPastor Model
**Ubicación**: `app/Models/PreguntasSeguridadPastor.php`

**Métodos principales**:
- `guardarPreguntas(array $preguntas)` - Guarda preguntas hasheadas
- `verificarRespuestas(array $respuestas)` - Verifica respuestas (case-insensitive)
- `generarBackupCodes()` - Genera 10 códigos alfanuméricos
- `verificarBackupCode(string $codigo)` - Verifica y consume un código
- `registrarIntento(bool $exitoso)` - Registra intento fallido/exitoso
- `estaBloqueado()` - Verifica si está bloqueado (3 intentos/15min)
- `activar()` - Activa la protección
- `backupCodesDisponibles()` - Retorna cantidad de códigos restantes

## Integración con WhatsApp

El sistema utiliza el servicio existente `App\Services\WhatsAppService` para todos los envíos:

1. **Notificación al presbítero**: Cuando pastor solicita cambio de teléfono - Detalles de la solicitud con link de autorización
2. **Confirmación al pastor**: Después de que presbítero aprueba - Notificación de aprobación con link para configurar seguridad
3. **OTP (solo para pastores con protección activada)**: Código de 6 dígitos para verificar identidad antes de permitir editar datos

**Formato de números**: Se formatean automáticamente agregando prefijo 58 para Venezuela

**Nota importante**: El OTP NO se envía cuando el pastor solicita cambio de teléfono por primera vez. Solo se usa cuando el pastor YA tiene protección activada y quiere modificar sus datos.

## Estados de la Solicitud

1. **pendiente**: Esperando aprobación del presbítero
2. **aprobado**: Presbítero aprobó, pastor debe configurar seguridad
3. **rechazado**: Presbítero rechazó la solicitud
4. **expirado**: Token expiró (48 horas sin acción)
5. **completado**: Pastor configuró seguridad exitosamente

## Consideraciones de Seguridad

1. **Tokens únicos**: 64 caracteres aleatorios para cada solicitud
2. **Expiración automática**: 48 horas para solicitudes pendientes
3. **Respuestas hasheadas**: Las respuestas de seguridad nunca se almacenan en texto plano
4. **Backup codes hasheados**: Los códigos también se almacenan hasheados
5. **Case-insensitive**: Las respuestas se comparan en minúsculas para mejor UX
6. **Rate limiting**: Bloqueo después de 3 intentos fallidos
7. **Auditoría**: IPs de solicitud y aprobación registradas
8. **One-time codes**: Cada backup code solo se puede usar una vez

## Pruebas Recomendadas

1. **Flujo completo**: Desde /busqueda hasta edición protegida
2. **Expiración de token**: Verificar que tokens expiren después de 48h
3. **Rate limiting**: Intentar 4 veces con respuestas incorrectas
4. **Backup codes**: Verificar que se consumen después de usar
5. **Regeneración**: Probar regeneración de backup codes
6. **Rechazo**: Verificar flujo cuando presbítero rechaza
7. **Sin presbítero**: Verificar error cuando no hay presbítero en zona
8. **WhatsApp**: Confirmar que todos los mensajes se envían correctamente

## Notas Técnicas

- El middleware se aplica solo a la ruta de edición pública
- La verificación de seguridad ocurre en el backend antes de guardar
- Los errores de linter en Editar.php son falsos positivos por tamaño del archivo
- Todas las operaciones están logueadas para auditoría
- El sistema es compatible con multi-tenancy (empresa_id)
