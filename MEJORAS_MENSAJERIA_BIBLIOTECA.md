# 📧 Mejoras para Mensajería y Biblioteca Digital

## 🎯 Objetivo
Adaptar los módulos de **Mensajería** y **Biblioteca Digital** al estilo del **app-chat** de la plantilla Materialize para una mejor experiencia de usuario.

---

## 📊 Análisis de Módulos Actuales

### **Mensajería Actual**
**Ubicación**: `app/Livewire/Admin/Mensajeria/MensajeriaIndex.php`

**Características**:
- ✅ Sistema de mensajes entre usuarios
- ✅ Bandeja de entrada, enviados y archivados
- ✅ Búsqueda de mensajes
- ✅ Prioridades (baja, media, alta, urgente)
- ✅ Adjuntos
- ✅ Marcar como leído/archivado
- ✅ Multitenancy

**Problemas**:
- ❌ Interfaz básica sin estilo chat
- ❌ No hay vista de conversación en tiempo real
- ❌ Falta sidebar con lista de contactos
- ❌ No hay indicadores visuales de estado

### **Biblioteca Digital Actual**
**Ubicación**: `app/Livewire/Admin/Biblioteca/BibliotecaIndex.php`

**Características**:
- ✅ Subida de archivos
- ✅ Categorías
- ✅ Control de visibilidad (público, privado, restringido)
- ✅ Registro de descargas
- ✅ Búsqueda y filtros
- ✅ Multitenancy

**Problemas**:
- ❌ Vista grid/list básica
- ❌ Falta previsualización de archivos
- ❌ No hay drag & drop
- ❌ Interfaz poco intuitiva

---

## 🎨 Estructura del App-Chat de Materialize

### **Layout Principal**
```html
<div class="app-chat card overflow-hidden">
  <div class="row g-0">
    <!-- Sidebar Left (Perfil usuario) -->
    <div class="col app-chat-sidebar-left">
      <!-- Avatar, nombre, estado -->
      <!-- Configuraciones -->
    </div>
    
    <!-- Lista de Contactos/Chats -->
    <div class="col app-chat-contacts">
      <!-- Búsqueda -->
      <!-- Lista de chats -->
      <!-- Lista de contactos -->
    </div>
    
    <!-- Área de Conversación -->
    <div class="col app-chat-history">
      <!-- Header del chat -->
      <!-- Mensajes -->
      <!-- Input para enviar -->
    </div>
    
    <!-- Sidebar Right (Info del contacto) -->
    <div class="col app-chat-sidebar-right">
      <!-- Info del contacto -->
      <!-- Opciones -->
    </div>
  </div>
</div>
```

### **Características Clave**
1. **3 Columnas Responsivas**:
   - Sidebar izquierdo (perfil)
   - Lista de chats/contactos
   - Área de conversación

2. **Componentes Visuales**:
   - Avatares con estado online/offline
   - Badges de mensajes no leídos
   - Timestamps relativos
   - Indicadores de lectura (check doble)
   - Búsqueda en tiempo real

3. **Interacciones**:
   - Click en contacto abre conversación
   - Sidebar colapsable en móvil
   - Scroll infinito en mensajes
   - Adjuntar archivos
   - Grabación de voz

---

## 🚀 Propuesta de Mejora: Mensajería

### **Nueva Estructura**

```
app-mensajeria/
├── Sidebar Left (Perfil del usuario)
│   ├── Avatar + Estado
│   ├── Configuraciones
│   └── Logout
│
├── Lista de Conversaciones
│   ├── Búsqueda
│   ├── Chats Recientes
│   │   ├── Avatar + Nombre
│   │   ├── Último mensaje
│   │   ├── Timestamp
│   │   └── Badge no leídos
│   └── Todos los Usuarios
│
├── Área de Conversación
│   ├── Header
│   │   ├── Info del destinatario
│   │   ├── Acciones (llamada, video, buscar)
│   │   └── Menú opciones
│   ├── Historial de Mensajes
│   │   ├── Mensajes enviados (derecha)
│   │   ├── Mensajes recibidos (izquierda)
│   │   ├── Timestamps
│   │   └── Estado de lectura
│   └── Input de Mensaje
│       ├── Campo de texto
│       ├── Adjuntar archivo
│       ├── Emojis
│       └── Botón enviar
│
└── Sidebar Right (Info del contacto)
    ├── Avatar + Nombre
    ├── Información personal
    ├── Archivos compartidos
    └── Opciones (archivar, bloquear, eliminar)
```

### **Componentes a Crear**

#### 1. **ChatSidebar.blade.php**
```blade
<!-- Perfil del usuario actual -->
<div class="chat-sidebar-left-user">
    <div class="avatar avatar-xl avatar-online">
        <img src="{{ auth()->user()->avatar }}" />
    </div>
    <h5>{{ auth()->user()->name }}</h5>
    <span>{{ auth()->user()->getRoleNames()->first() }}</span>
</div>
```

#### 2. **ChatList.blade.php**
```blade
<!-- Lista de conversaciones -->
<ul class="chat-contact-list">
    @foreach($conversaciones as $conv)
    <li class="chat-contact-list-item" wire:click="seleccionarChat({{ $conv->id }})">
        <div class="avatar avatar-online">
            <img src="{{ $conv->usuario->avatar }}" />
        </div>
        <div class="chat-contact-info">
            <h6>{{ $conv->usuario->name }}</h6>
            <small>{{ Str::limit($conv->ultimo_mensaje, 30) }}</small>
        </div>
        @if($conv->no_leidos > 0)
        <span class="badge bg-danger">{{ $conv->no_leidos }}</span>
        @endif
    </li>
    @endforeach
</ul>
```

#### 3. **ChatHistory.blade.php**
```blade
<!-- Historial de mensajes -->
<ul class="chat-history">
    @foreach($mensajes as $mensaje)
    <li class="chat-message {{ $mensaje->esMio() ? 'chat-message-right' : '' }}">
        <div class="avatar">
            <img src="{{ $mensaje->remitente->avatar }}" />
        </div>
        <div class="chat-message-wrapper">
            <div class="chat-message-text">
                <p>{{ $mensaje->contenido }}</p>
            </div>
            <div class="text-muted">
                @if($mensaje->esMio())
                <i class="ri-check-double-line {{ $mensaje->leido ? 'text-success' : '' }}"></i>
                @endif
                <small>{{ $mensaje->created_at->format('h:i A') }}</small>
            </div>
        </div>
    </li>
    @endforeach
</ul>
```

#### 4. **ChatInput.blade.php**
```blade
<!-- Input para enviar mensajes -->
<form wire:submit.prevent="enviarMensaje" class="chat-history-footer">
    <input 
        type="text" 
        wire:model="nuevoMensaje" 
        class="form-control message-input"
        placeholder="Escribe tu mensaje..." 
    />
    <div class="message-actions">
        <button type="button" class="btn btn-icon">
            <i class="ri-attachment-2"></i>
        </button>
        <button type="submit" class="btn btn-primary">
            <span>Enviar</span>
            <i class="ri-send-plane-line"></i>
        </button>
    </div>
</form>
```

### **Componente Livewire Mejorado**

```php
<?php

namespace App\Livewire\Admin\Mensajeria;

use Livewire\Component;
use App\Models\Mensaje;
use App\Models\User;

class ChatIndex extends Component
{
    public $conversacionActiva = null;
    public $mensajes = [];
    public $nuevoMensaje = '';
    public $busqueda = '';
    
    public function seleccionarChat($userId)
    {
        $this->conversacionActiva = $userId;
        $this->cargarMensajes();
        $this->marcarComoLeido();
    }
    
    public function cargarMensajes()
    {
        $this->mensajes = Mensaje::where(function($q) {
                $q->where('remitente_id', auth()->id())
                  ->where('destinatario_id', $this->conversacionActiva);
            })
            ->orWhere(function($q) {
                $q->where('remitente_id', $this->conversacionActiva)
                  ->where('destinatario_id', auth()->id());
            })
            ->orderBy('created_at', 'asc')
            ->get();
    }
    
    public function enviarMensaje()
    {
        Mensaje::create([
            'remitente_id' => auth()->id(),
            'destinatario_id' => $this->conversacionActiva,
            'contenido' => $this->nuevoMensaje,
            'empresa_id' => auth()->user()->empresa_id,
        ]);
        
        $this->nuevoMensaje = '';
        $this->cargarMensajes();
    }
    
    public function getConversacionesProperty()
    {
        return User::whereHas('mensajesRecibidos', function($q) {
                $q->where('remitente_id', auth()->id());
            })
            ->orWhereHas('mensajesEnviados', function($q) {
                $q->where('destinatario_id', auth()->id());
            })
            ->with(['mensajesRecibidos' => function($q) {
                $q->where('remitente_id', auth()->id())
                  ->latest()
                  ->limit(1);
            }])
            ->get();
    }
    
    public function render()
    {
        return view('livewire.admin.mensajeria.chat-index')
            ->layout('components.layouts.admin');
    }
}
```

---

## 📚 Propuesta de Mejora: Biblioteca Digital

### **Nueva Estructura**

```
app-biblioteca/
├── Sidebar (Categorías y Filtros)
│   ├── Todas las categorías
│   ├── Mis archivos
│   ├── Compartidos conmigo
│   ├── Recientes
│   └── Favoritos
│
├── Área Principal
│   ├── Header
│   │   ├── Búsqueda
│   │   ├── Vista (Grid/List)
│   │   └── Botón subir archivo
│   ├── Grid de Archivos
│   │   ├── Card por archivo
│   │   │   ├── Icono/Preview
│   │   │   ├── Nombre
│   │   │   ├── Tamaño
│   │   │   ├── Fecha
│   │   │   └── Acciones
│   └── Paginación
│
└── Modal de Subida
    ├── Drag & Drop
    ├── Formulario
    │   ├── Título
    │   ├── Descripción
    │   ├── Categoría
    │   ├── Visibilidad
    │   └── Usuarios autorizados
    └── Progreso de subida
```

### **Componentes a Crear**

#### 1. **BibliotecaSidebar.blade.php**
```blade
<div class="biblioteca-sidebar">
    <ul class="list-unstyled">
        <li wire:click="filtrar('todos')" class="{{ $filtro === 'todos' ? 'active' : '' }}">
            <i class="ri-folder-line"></i>
            <span>Todos los archivos</span>
            <span class="badge">{{ $totalArchivos }}</span>
        </li>
        <li wire:click="filtrar('mis-archivos')">
            <i class="ri-file-user-line"></i>
            <span>Mis archivos</span>
        </li>
        <li wire:click="filtrar('compartidos')">
            <i class="ri-share-line"></i>
            <span>Compartidos conmigo</span>
        </li>
        <li wire:click="filtrar('recientes')">
            <i class="ri-time-line"></i>
            <span>Recientes</span>
        </li>
    </ul>
    
    <hr>
    
    <h6>Categorías</h6>
    <ul class="list-unstyled">
        @foreach($categorias as $cat)
        <li wire:click="filtrarCategoria({{ $cat->id }})">
            <i class="ri-folder-2-line"></i>
            <span>{{ $cat->nombre }}</span>
            <span class="badge">{{ $cat->archivos_count }}</span>
        </li>
        @endforeach
    </ul>
</div>
```

#### 2. **BibliotecaGrid.blade.php**
```blade
<div class="row g-4">
    @foreach($archivos as $archivo)
    <div class="col-md-3">
        <div class="card archivo-card">
            <div class="card-body text-center">
                <!-- Icono según tipo de archivo -->
                <div class="archivo-icon mb-3">
                    @if($archivo->esImagen())
                        <img src="{{ Storage::url($archivo->ruta_archivo) }}" class="img-fluid rounded" />
                    @else
                        <i class="ri-file-{{ $archivo->getIcono() }}-line" style="font-size: 48px;"></i>
                    @endif
                </div>
                
                <h6 class="mb-1">{{ Str::limit($archivo->titulo, 20) }}</h6>
                <small class="text-muted">{{ $archivo->formatoTamaño() }}</small>
                <small class="text-muted d-block">{{ $archivo->created_at->diffForHumans() }}</small>
                
                <div class="mt-3">
                    <button wire:click="descargar({{ $archivo->id }})" class="btn btn-sm btn-primary">
                        <i class="ri-download-line"></i>
                    </button>
                    <button wire:click="compartir({{ $archivo->id }})" class="btn btn-sm btn-info">
                        <i class="ri-share-line"></i>
                    </button>
                    @can('delete', $archivo)
                    <button wire:click="eliminar({{ $archivo->id }})" class="btn btn-sm btn-danger">
                        <i class="ri-delete-bin-line"></i>
                    </button>
                    @endcan
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
```

#### 3. **BibliotecaUpload.blade.php**
```blade
<div class="modal fade" id="uploadModal">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Subir Archivo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Drag & Drop Area -->
                <div 
                    class="dropzone" 
                    x-data="{ uploading: false, progress: 0 }"
                    x-on:livewire-upload-start="uploading = true"
                    x-on:livewire-upload-finish="uploading = false"
                    x-on:livewire-upload-error="uploading = false"
                    x-on:livewire-upload-progress="progress = $event.detail.progress"
                >
                    <input type="file" wire:model="nuevoArchivo" class="d-none" id="fileInput" />
                    <label for="fileInput" class="dropzone-label">
                        <i class="ri-upload-cloud-line" style="font-size: 64px;"></i>
                        <p>Arrastra tu archivo aquí o haz click para seleccionar</p>
                    </label>
                    
                    <!-- Progress Bar -->
                    <div x-show="uploading" class="progress mt-3">
                        <div class="progress-bar" :style="`width: ${progress}%`"></div>
                    </div>
                </div>
                
                @if($nuevoArchivo)
                <div class="mt-4">
                    <div class="mb-3">
                        <label class="form-label">Título</label>
                        <input type="text" wire:model="titulo" class="form-control" />
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Descripción</label>
                        <textarea wire:model="descripcion" class="form-control" rows="3"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Categoría</label>
                        <select wire:model="categoriaId" class="form-select">
                            <option value="">Sin categoría</option>
                            @foreach($categorias as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Visibilidad</label>
                        <select wire:model="visibilidad" class="form-select">
                            <option value="publico">Público</option>
                            <option value="privado">Privado</option>
                            <option value="restringido">Restringido</option>
                        </select>
                    </div>
                    
                    @if($visibilidad === 'restringido')
                    <div class="mb-3">
                        <label class="form-label">Usuarios Autorizados</label>
                        <select wire:model="usuariosAutorizados" class="form-select" multiple>
                            @foreach($usuarios as $user)
                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" wire:click="subirArchivo" class="btn btn-primary" @if(!$nuevoArchivo) disabled @endif>
                    Subir Archivo
                </button>
            </div>
        </div>
    </div>
</div>
```

---

## 📝 Pasos de Implementación

### **Fase 1: Mensajería (2-3 días)**
1. ✅ Crear nuevo componente `ChatIndex.php`
2. ✅ Crear vistas con estructura de 3 columnas
3. ✅ Implementar lista de conversaciones
4. ✅ Implementar área de chat
5. ✅ Agregar indicadores de estado
6. ✅ Implementar búsqueda en tiempo real
7. ✅ Agregar notificaciones de nuevos mensajes

### **Fase 2: Biblioteca Digital (2-3 días)**
1. ✅ Crear nuevo componente `BibliotecaIndex.php` mejorado
2. ✅ Implementar sidebar con categorías
3. ✅ Crear vista grid con cards
4. ✅ Implementar drag & drop para subida
5. ✅ Agregar previsualización de archivos
6. ✅ Implementar compartir archivos
7. ✅ Agregar filtros avanzados

### **Fase 3: Integración (1 día)**
1. ✅ Actualizar rutas
2. ✅ Actualizar menú de navegación
3. ✅ Agregar CSS personalizado
4. ✅ Testing completo
5. ✅ Documentación

---

## 🎨 CSS Personalizado Necesario

```css
/* Chat Styles */
.app-chat {
    height: calc(100vh - 200px);
}

.chat-contact-list-item {
    padding: 12px 20px;
    cursor: pointer;
    transition: background 0.3s;
}

.chat-contact-list-item:hover {
    background: #f8f9fa;
}

.chat-contact-list-item.active {
    background: #e7f3ff;
    border-left: 3px solid #0d6efd;
}

.chat-message-right {
    justify-content: flex-end;
}

.chat-message-text {
    background: #f8f9fa;
    padding: 10px 15px;
    border-radius: 8px;
    max-width: 70%;
}

.chat-message-right .chat-message-text {
    background: #0d6efd;
    color: white;
}

/* Biblioteca Styles */
.archivo-card {
    transition: transform 0.3s, box-shadow 0.3s;
}

.archivo-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.dropzone {
    border: 2px dashed #dee2e6;
    border-radius: 8px;
    padding: 40px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s;
}

.dropzone:hover {
    border-color: #0d6efd;
    background: #f8f9fa;
}

.biblioteca-sidebar li {
    padding: 10px 15px;
    cursor: pointer;
    border-radius: 6px;
    transition: background 0.3s;
}

.biblioteca-sidebar li:hover {
    background: #f8f9fa;
}

.biblioteca-sidebar li.active {
    background: #e7f3ff;
    color: #0d6efd;
}
```

---

## 🚀 Beneficios de las Mejoras

### **Mensajería**
- ✅ Interfaz moderna estilo WhatsApp/Telegram
- ✅ Mejor UX con conversaciones en tiempo real
- ✅ Indicadores visuales claros
- ✅ Búsqueda rápida de contactos
- ✅ Responsive en todos los dispositivos

### **Biblioteca Digital**
- ✅ Vista más intuitiva de archivos
- ✅ Drag & drop para subida fácil
- ✅ Previsualización de imágenes
- ✅ Mejor organización por categorías
- ✅ Compartir archivos más fácil

---

## 📞 Próximos Pasos

¿Quieres que implemente alguno de estos módulos mejorados? Puedo:

1. **Crear el módulo de Mensajería estilo chat** completo
2. **Crear el módulo de Biblioteca Digital** mejorado
3. **Ambos módulos** con todas las características

Dime cuál prefieres y lo desarrollo para ti. 🚀
