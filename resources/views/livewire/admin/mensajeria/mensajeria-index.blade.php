<div>
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-gray-800">Mensajería Interna</h2>
        <p class="text-gray-600">Sistema de mensajería interna de la institución</p>
    </div>

    <!-- Estadísticas rápidas -->
    <div class="row mb-4">
        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-label-primary rounded p-3 me-3">
                        <i class="ri ri-mail ti-lg text-primary"></i>
                    </div>
                    <div>
                        <p class="mb-1 text-muted">No leídos</p>
                        <h4 class="mb-0">{{ $this->noLeidosCount }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-label-success rounded p-3 me-3">
                        <i class="ri ri-send ti-lg text-success"></i>
                    </div>
                    <div>
                        <p class="mb-1 text-muted">Enviados</p>
                        <h4 class="mb-0">{{ auth()->user()->mensajesEnviados()->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-label-info rounded p-3 me-3">
                        <i class="ri ri-archive ti-lg text-info"></i>
                    </div>
                    <div>
                        <p class="mb-1 text-muted">Archivados</p>
                        <h4 class="mb-0">{{ auth()->user()->mensajesRecibidos()->where('mensaje_destinatarios.archivado', true)->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-lg-3 mb-3">
            <div class="card h-100">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-label-warning rounded p-3 me-3">
                        <i class="ri ri-users ti-lg text-warning"></i>
                    </div>
                    <div>
                        <p class="mb-1 text-muted">Usuarios</p>
                        <h4 class="mb-0">{{ $this->usuarios->count() }}</h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="border-b border-gray-200">
            <ul class="nav nav-tabs card-header-tabs" role="tablist">
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'inbox' ? 'active' : '' }}" 
                       wire:click="cambiarTab('inbox')" 
                       role="tab" style="cursor: pointer;">
                        <i class="ri ri-inbox me-2"></i>Bandeja de entrada
                        @if($this->noLeidosCount > 0)
                            <span class="badge bg-danger ms-2">{{ $this->noLeidosCount }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'sent' ? 'active' : '' }}" 
                       wire:click="cambiarTab('sent')" 
                       role="tab" style="cursor: pointer;">
                        <i class="ri ri-send me-2"></i>Enviados
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ $activeTab === 'archived' ? 'active' : '' }}" 
                       wire:click="cambiarTab('archived')" 
                       role="tab" style="cursor: pointer;">
                        <i class="ri ri-archive me-2"></i>Archivados
                    </a>
                </li>
            </ul>
        </div>

        <div class="card-body">
            <!-- Barra de búsqueda y acciones -->
            <div class="row align-items-center mb-4">
                <div class="col-md-6 mb-3 mb-md-0">
                    <div class="input-group">
                        <span class="input-group-text"><i class="ri ri-search"></i></span>
                        <input type="text" 
                               class="form-control" 
                               wire:model.live="search" 
                               placeholder="Buscar mensajes...">
                    </div>
                </div>
                <div class="col-md-6 text-md-end">
                    <button type="button" 
                            class="btn btn-primary" 
                            wire:click="abrirModalNuevo">
                        <i class="ri ri-plus me-2"></i>Nuevo mensaje
                    </button>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Lista de mensajes -->
                <div class="lg:col-span-1">
                    <div class="space-y-2">
                        @forelse($this->mensajes as $mensaje)
                            <div class="col-12 mb-3">
                        <div class="card {{ $selectedMessage && $selectedMessage->id === $mensaje->id ? 'border-primary border-2' : '' }}"
                             wire:click="seleccionarMensaje({{ $mensaje->id }})" 
                             style="cursor: pointer;">
                            <div class="card-body">
                                <div class="d-flex align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="mb-0 text-truncate">
                                                {{ $mensaje->asunto }}
                                            </h6>
                                            <small class="text-muted">{{ $mensaje->created_at->diffForHumans() }}</small>
                                        </div>
                                        <p class="text-muted mb-1">
                                            @if($activeTab === 'sent')
                                                Para: {{ $mensaje->destinatarios->pluck('name')->join(', ') }}
                                            @else
                                                De: {{ $mensaje->remitente->name }}
                                            @endif
                                        </p>
                                        <div class="d-flex align-items-center">
                                            @if($activeTab !== 'sent' && !$mensaje->pivot->leido)
                                                <span class="badge bg-primary me-2">Nuevo</span>
                                            @endif
                                            @if($mensaje->prioridad === 'urgente')
                                                <span class="badge bg-danger">Urgente</span>
                                            @elseif($mensaje->prioridad === 'alta')
                                                <span class="badge bg-warning">Alta</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                        @empty
                            <div class="text-center py-8">
                                <i class="fas fa-inbox text-gray-400 text-4xl mb-4"></i>
                                <p class="text-gray-500">No hay mensajes en esta carpeta</p>
                            </div>
                        @endforelse
                    </div>

                    <div class="mt-4">
                        {{ $this->mensajes->links() }}
                    </div>
                </div>

                <!-- Vista del mensaje seleccionado -->
                <div class="lg:col-span-2">
                    @if($selectedMessage)
                        <div class="card">
                            <div class="card-header d-flex justify-content-between align-items-start">
                                <div>
                                    <h5 class="card-title mb-1">{{ $selectedMessage->asunto }}</h5>
                                    <p class="card-text text-muted mb-1">
                                        @if($activeTab === 'sent')
                                            Para: {{ $selectedMessage->destinatarios->pluck('name')->join(', ') }}
                                        @else
                                            De: {{ $selectedMessage->remitente->name }}
                                        @endif
                                    </p>
                                    <small class="text-muted">{{ $selectedMessage->created_at->format('d/m/Y H:i') }}</small>
                                </div>
                                <div class="btn-group" role="group">
                                    @if($activeTab !== 'sent')
                                        <button type="button" 
                                                class="btn btn-outline-primary btn-sm" 
                                                wire:click="marcarComoLeido({{ $selectedMessage->id }})">
                                            <i class="ri ri-check me-1"></i>Marcar como leído
                                        </button>
                                    @endif
                                    
                                    @if($activeTab !== 'archived')
                                        <button type="button" 
                                                class="btn btn-outline-warning btn-sm" 
                                                wire:click="archivarMensaje({{ $selectedMessage->id }})">
                                            <i class="ri ri-archive me-1"></i>Archivar
                                        </button>
                                    @else
                                        <button type="button" 
                                                class="btn btn-outline-success btn-sm" 
                                                wire:click="desarchivarMensaje({{ $selectedMessage->id }})">
                                            <i class="ri ri-refresh me-1"></i>Desarchivar
                                        </button>
                                    @endif
                                    
                                    <button type="button" 
                                            class="btn btn-outline-danger btn-sm" 
                                            wire:click="eliminarMensaje({{ $selectedMessage->id }})"
                                            onclick="confirm('¿Estás seguro de eliminar este mensaje?') || event.stopImmediatePropagation()">
                                        <i class="ri ri-trash me-1"></i>Eliminar
                                    </button>
                                </div>
                            </div>
                            
                            <div class="card-body">
                                <div class="mb-4">
                                    {!! nl2br(e($selectedMessage->contenido)) !!}
                                </div>
                                
                                @if($selectedMessage->archivos->count() > 0)
                                    <div class="pt-3 border-top">
                                        <h6 class="mb-3">Archivos adjuntos</h6>
                                        <div class="row">
                                            @foreach($selectedMessage->archivos as $archivo)
                                                <div class="col-md-6 mb-2">
                                                    <div class="d-flex align-items-center p-2 bg-light rounded">
                                                        <i class="ri ri-file text-muted me-2"></i>
                                                        <span class="text-muted">{{ $archivo->nombre_original }}</span>
                                                        <small class="text-muted ms-auto">{{ $archivo->tamaño_formateado }}</small>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @else
                        <div class="card">
                            <div class="card-body text-center py-5">
                                <i class="ri ri-mail text-muted mb-3" style="font-size: 3rem;"></i>
                                <h5 class="text-muted">Selecciona un mensaje</h5>
                                <p class="text-muted">Elige un mensaje de la lista para ver su contenido.</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Modal para nuevo mensaje -->
<div class="modal fade @if($mostrarModalNuevo) show d-block @endif" id="nuevoMensajeModal" tabindex="-1" aria-labelledby="nuevoMensajeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
@if($mostrarModalNuevo)
    <div class="modal-backdrop fade show"></div>
@endif
                <div class="modal-header">
                    <h5 class="modal-title">Nuevo Mensaje</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form wire:submit.prevent="enviarMensaje">
                    <div class="modal-body">
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Para</label>
                            <div class="col-sm-10">
                                <select wire:model="selectedDestinatarios" 
                                        class="form-select" 
                                        multiple 
                                        size="5">
                                    @foreach($this->usuarios as $usuario)
                                        <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                                    @endforeach
                                </select>
                                <small class="text-muted">Mantén presionado Ctrl para seleccionar múltiples usuarios</small>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Asunto</label>
                            <div class="col-sm-10">
                                <input type="text" 
                                       wire:model="nuevoAsunto" 
                                       class="form-control" 
                                       placeholder="Asunto del mensaje">
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Prioridad</label>
                            <div class="col-sm-10">
                                <select wire:model="nuevoPrioridad" class="form-select">
                                    <option value="baja">Baja</option>
                                    <option value="media">Media</option>
                                    <option value="alta">Alta</option>
                                    <option value="urgente">Urgente</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label">Mensaje</label>
                            <div class="col-sm-10">
                                <textarea wire:model="nuevoContenido" 
                                          class="form-control" 
                                          rows="5" 
                                          placeholder="Escribe tu mensaje aquí..."></textarea>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Enviar Mensaje</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('page-js')
<script>
    // Sin JavaScript - Livewire controla todo mediante el estado $mostrarModalNuevo
</script>
@endpush