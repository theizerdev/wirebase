<div class="app-chat card overflow-hidden">
    <div class="row g-0">
        <!-- Lista de Conversaciones -->
        <div class="col-12 col-lg-4 col-xl-3 app-chat-contacts border-end">
            <div class="p-3 border-bottom">
                <div class="d-flex align-items-center mb-3">
                    <div class="avatar avatar-online me-2">
                        <img src="{{ Auth::user()->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode(Auth::user()->name) }}" class="rounded-circle" width="40" />
                    </div>
                    <div>
                        <h6 class="mb-0">{{ Auth::user()->name }}</h6>
                        <small class="text-muted">{{ Auth::user()->getRoleNames()->first() }}</small>
                    </div>
                </div>
                <input type="text" wire:model.live="busqueda" class="form-control" placeholder="Buscar conversación..." />
            </div>

            <ul class="list-unstyled chat-contact-list mb-0" style="max-height: calc(100vh - 250px); overflow-y: auto;">
                @forelse($conversaciones as $conv)
                <li class="chat-contact-list-item {{ $conversacionActiva == $conv->id ? 'active' : '' }}" 
                    wire:click="seleccionarChat({{ $conv->id }})" 
                    style="cursor: pointer; padding: 12px 20px; transition: background 0.3s;">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-online me-3">
                            <img src="{{ $conv->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($conv->name) }}" class="rounded-circle" width="40" />
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="mb-0">{{ $conv->name }}</h6>
                            <small class="text-muted">{{ $conv->getRoleNames()->first() }}</small>
                        </div>
                        @if($conv->no_leidos > 0)
                        <span class="badge bg-danger rounded-pill">{{ $conv->no_leidos }}</span>
                        @endif
                    </div>
                </li>
                @empty
                <li class="text-center p-4 text-muted">
                    <i class="ri-message-3-line" style="font-size: 48px;"></i>
                    <p>No hay conversaciones</p>
                </li>
                @endforelse
            </ul>
        </div>

        <!-- Área de Conversación -->
        <div class="col-12 col-lg-8 col-xl-9 app-chat-history">
            @if($conversacionActiva && $usuarioActivo)
            <div class="d-flex flex-column h-100">
                <!-- Header -->
                <div class="p-3 border-bottom">
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-online me-3">
                            <img src="{{ $usuarioActivo->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($usuarioActivo->name) }}" class="rounded-circle" width="40" />
                        </div>
                        <div>
                            <h6 class="mb-0">{{ $usuarioActivo->name }}</h6>
                            <small class="text-success">En línea</small>
                        </div>
                    </div>
                </div>

                <!-- Mensajes -->
                <div class="flex-grow-1 p-3" style="overflow-y: auto; max-height: calc(100vh - 350px);" id="chatHistory">
                    <ul class="list-unstyled chat-history mb-0">
                        @foreach($mensajes as $mensaje)
                        <li class="chat-message mb-3 d-flex {{ $mensaje->remitente_id == Auth::id() ? 'justify-content-end' : '' }}">
                            @if($mensaje->remitente_id != Auth::id())
                            <div class="avatar me-2">
                                <img src="{{ $mensaje->remitente->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($mensaje->remitente->name) }}" class="rounded-circle" width="32" />
                            </div>
                            @endif
                            <div class="chat-message-wrapper" style="max-width: 70%;">
                                <div class="chat-message-text p-2 rounded {{ $mensaje->remitente_id == Auth::id() ? 'bg-primary text-white' : 'bg-light' }}">
                                    <p class="mb-0">{{ $mensaje->contenido }}</p>
                                </div>
                                <div class="text-muted mt-1" style="font-size: 0.75rem;">
                                    @if($mensaje->remitente_id == Auth::id())
                                    <i class="ri-check-double-line {{ $mensaje->leido ? 'text-success' : '' }}"></i>
                                    @endif
                                    <small>{{ $mensaje->created_at->format('h:i A') }}</small>
                                </div>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>

                <!-- Input -->
                <div class="p-3 border-top">
                    <form wire:submit.prevent="enviarMensaje" class="d-flex gap-2">
                        <input type="text" wire:model="nuevoMensaje" class="form-control" placeholder="Escribe tu mensaje..." />
                        <button type="submit" class="btn btn-primary" {{ empty($nuevoMensaje) ? 'disabled' : '' }}>
                            <i class="ri-send-plane-line"></i>
                        </button>
                    </form>
                </div>
            </div>
            @else
            <div class="d-flex align-items-center justify-content-center h-100">
                <div class="text-center text-muted">
                    <i class="ri-chat-3-line" style="font-size: 64px;"></i>
                    <p class="mt-3">Selecciona una conversación para comenzar</p>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.on('mensajeRecibido', () => {
            const chatHistory = document.getElementById('chatHistory');
            if (chatHistory) {
                chatHistory.scrollTop = chatHistory.scrollHeight;
            }
        });
    });
</script>
@endpush
