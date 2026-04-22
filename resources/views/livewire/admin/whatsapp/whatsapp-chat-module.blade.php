@push('styles')
<link rel="stylesheet" href="{{ asset('css/app-whatsapp.css') }}">
@endpush

<div>
    {{-- Connection Status Bar --}}
    @if($connectionStatus !== 'connected')
    <div class="connection-status-bar bg-{{ $this->statusColor }} text-white mb-3 rounded">
        <i class="ri {{ $this->statusIcon }} me-1"></i>
        <span>{{ $this->statusText }}</span>
        @if($connectionError)
            <span class="ms-2">&mdash; {{ $connectionError }}</span>
        @endif
        <a href="{{ route('admin.whatsapp.connection') }}" class="text-white ms-2 text-decoration-underline small">
            Gestionar Conexión
        </a>
    </div>
    @endif

    {{-- Main App Container --}}
    <div class="app-whatsapp card">
        <div class="row g-0">
            {{-- ============================================ --}}
            {{-- LEFT SIDEBAR - Filters & Chat Categories --}}
            {{-- ============================================ --}}
            <div class="col app-whatsapp-sidebar border-end flex-grow-0" id="app-whatsapp-sidebar">
                {{-- New Chat Button --}}
                <div class="d-grid p-3">
                    <button class="btn btn-primary" wire:click="$set('showNewChatModal', true)">
                        <i class="ri ri-chat-new-line me-2"></i>Nuevo Chat
                    </button>
                </div>

                {{-- Chat Filters --}}
                <div class="chat-filters pt-2 pb-2">
                    <ul class="chat-filter-folders list-unstyled mb-0">
                        <li class="{{ $activeFilter === 'all' ? 'active' : '' }} d-flex justify-content-between align-items-center mb-1"
                            wire:click="setFilter('all')">
                            <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
                                <i class="icon-base ri ri-chat-3-line me-1"></i>
                                <span class="align-middle ms-2">Todos</span>
                            </a>
                            <div class="badge bg-label-primary rounded-pill">{{ $stats['total_chats'] }}</div>
                        </li>
                        <li class="{{ $activeFilter === 'unread' ? 'active' : '' }} d-flex justify-content-between align-items-center mb-1"
                            wire:click="setFilter('unread')">
                            <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
                                <i class="icon-base ri ri-chat-unread-line me-1"></i>
                                <span class="align-middle ms-2">No leídos</span>
                            </a>
                            @if($stats['unread'] > 0)
                            <div class="badge bg-label-danger rounded-pill">{{ $stats['unread'] }}</div>
                            @endif
                        </li>
                        <li class="{{ $activeFilter === 'contacts' ? 'active' : '' }} d-flex mb-1"
                            wire:click="setFilter('contacts')">
                            <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
                                <i class="icon-base ri ri-user-line me-1"></i>
                                <span class="align-middle ms-2">Contactos</span>
                            </a>
                        </li>
                        <li class="{{ $activeFilter === 'groups' ? 'active' : '' }} d-flex mb-1"
                            wire:click="setFilter('groups')">
                            <a href="javascript:void(0);" class="d-flex flex-wrap align-items-center">
                                <i class="icon-base ri ri-group-line me-1"></i>
                                <span class="align-middle ms-2">Grupos</span>
                            </a>
                        </li>
                    </ul>

                    {{-- Stats Labels --}}
                    <div class="pt-4">
                        <p class="small mx-4 text-body-secondary text-uppercase">Estadísticas Hoy</p>
                        <ul class="list-unstyled mb-2 px-4">
                            <li class="d-flex justify-content-between mb-1">
                                <span class="d-flex align-items-center">
                                    <i class="badge badge-dot bg-success me-2"></i>
                                    <span class="small">Enviados</span>
                                </span>
                                <span class="badge bg-label-success rounded-pill">{{ $stats['sent_today'] }}</span>
                            </li>
                            <li class="d-flex justify-content-between mb-1">
                                <span class="d-flex align-items-center">
                                    <i class="badge badge-dot bg-primary me-2"></i>
                                    <span class="small">Recibidos</span>
                                </span>
                                <span class="badge bg-label-primary rounded-pill">{{ $stats['received_today'] }}</span>
                            </li>
                        </ul>
                    </div>

                    {{-- Quick Links --}}
                    <div class="pt-3 border-top mx-3">
                        <ul class="list-unstyled mb-0">
                            <li class="mb-1 px-1">
                                <a href="{{ route('admin.whatsapp.dashboard') }}" class="d-flex align-items-center text-body small py-1">
                                    <i class="ri ri-dashboard-line me-2"></i> Dashboard
                                </a>
                            </li>
                            <li class="mb-1 px-1">
                                <a href="{{ route('admin.whatsapp.connection') }}" class="d-flex align-items-center text-body small py-1">
                                    <i class="ri ri-link me-2"></i> Conexión
                                </a>
                            </li>
                            <li class="mb-1 px-1">
                                <a href="{{ route('admin.whatsapp.templates.index') }}" class="d-flex align-items-center text-body small py-1">
                                    <i class="ri ri-file-text-line me-2"></i> Plantillas
                                </a>
                            </li>
                            <li class="mb-1 px-1">
                                <a href="{{ route('admin.whatsapp.history') }}" class="d-flex align-items-center text-body small py-1">
                                    <i class="ri ri-history-line me-2"></i> Historial
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- CENTER - Conversation List --}}
            {{-- ============================================ --}}
            <div class="col app-whatsapp-chat-list">
                {{-- Search Header --}}
                <div class="chat-list-header p-3 pb-2">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ri ri-menu-line icon-22px cursor-pointer d-lg-none"
                               id="whatsapp-sidebar-toggle"></i>
                        </div>
                        <div class="d-flex align-items-center gap-1">
                            <button class="btn btn-icon btn-text-secondary rounded-pill btn-sm"
                                    wire:click="refreshAll" wire:loading.attr="disabled" title="Actualizar">
                                <i class="ri ri-refresh-line icon-18px"></i>
                            </button>
                        </div>
                    </div>
                    <div class="input-group input-group-merge shadow-none">
                        <span class="input-group-text border-0 ps-2 pe-0">
                            <i class="ri ri-search-line icon-18px"></i>
                        </span>
                        <input type="text"
                               class="form-control border-0 shadow-none ps-2"
                               placeholder="Buscar conversación..."
                               wire:model.live.debounce.300ms="searchQuery"
                               autocomplete="off">
                        @if(!empty($searchQuery))
                        <span class="input-group-text border-0 cursor-pointer" wire:click="$set('searchQuery', '')">
                            <i class="ri ri-close-line icon-18px"></i>
                        </span>
                        @endif
                    </div>
                    <hr class="mx-n3 mb-0 mt-2">
                </div>

                {{-- Conversation List --}}
                <ul class="conversation-list" id="conversation-list">
                    @forelse($conversations as $conv)
                        @php
                            $peer = $conv['peer'] ?? '';
                            $name = $conv['name'] ?? $conv['peer'] ?? 'Desconocido';
                            $lastMsg = $conv['lastMessage'] ?? '';
                            $time = $conv['createdAt'] ?? $conv['timestamp'] ?? null;
                            $unread = $conv['unreadCount'] ?? 0;
                            $isGroup = str_contains($peer, '@g.us');
                            $initials = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $name) ?: 'W', 0, 1));
                            $avatarColors = ['bg-primary', 'bg-success', 'bg-info', 'bg-warning', 'bg-danger'];
                            $avatarColor = $avatarColors[crc32($peer) % count($avatarColors)];
                            $isActive = $currentPeer === $peer;
                        @endphp
                        <li class="conversation-item {{ $isActive ? 'active' : '' }} {{ $unread > 0 ? 'unread' : '' }}"
                            wire:click="selectConversation('{{ $peer }}', '{{ addslashes($name) }}')"
                            wire:key="conv-{{ $loop->index }}">

                            <div class="avatar avatar-sm">
                                <span class="avatar-initial rounded-circle {{ $avatarColor }}">
                                    @if($isGroup)
                                        <i class="ri ri-group-line"></i>
                                    @else
                                        {{ $initials }}
                                    @endif
                                </span>
                            </div>

                            <div class="conversation-info">
                                <div class="conversation-name text-truncate">{{ $name }}</div>
                                <div class="conversation-preview">
                                    @if(!empty($lastMsg))
                                        {{ \Illuminate\Support\Str::limit($lastMsg, 40) }}
                                    @else
                                        <span class="text-muted fst-italic">Sin mensajes</span>
                                    @endif
                                </div>
                            </div>

                            <div class="conversation-meta">
                                @if($time)
                                    <span class="conversation-time">{{ $this->formatTimestamp($time) }}</span>
                                @endif
                                @if($unread > 0)
                                    <span class="badge bg-success rounded-pill unread-badge">{{ $unread }}</span>
                                @endif
                            </div>
                        </li>
                    @empty
                        <li class="text-center py-5">
                            <div class="text-muted">
                                <i class="ri ri-chat-off-line d-block mb-2" style="font-size:2.5rem;opacity:0.3"></i>
                                @if(!empty($searchQuery))
                                    <p class="mb-1 small">Sin resultados para "{{ $searchQuery }}"</p>
                                    <button class="btn btn-sm btn-text-primary" wire:click="$set('searchQuery', '')">Limpiar búsqueda</button>
                                @elseif($connectionStatus !== 'connected')
                                    <p class="mb-1 small">Conecta WhatsApp para ver tus chats</p>
                                    <a href="{{ route('admin.whatsapp.connection') }}" class="btn btn-sm btn-primary">
                                        <i class="ri ri-link me-1"></i>Conectar
                                    </a>
                                @else
                                    <p class="mb-0 small">No hay conversaciones</p>
                                @endif
                            </div>
                        </li>
                    @endforelse
                </ul>

                {{-- Loading overlay --}}
                <div wire:loading.flex wire:target="loadConversations, setFilter"
                     class="position-absolute top-0 start-0 w-100 h-100 justify-content-center align-items-center"
                     style="background:rgba(255,255,255,0.6);z-index:3">
                    <div class="spinner-border spinner-border-sm text-primary"></div>
                </div>
            </div>

            {{-- ============================================ --}}
            {{-- RIGHT - Chat View / Message Thread --}}
            {{-- ============================================ --}}
            <div class="app-whatsapp-view {{ $currentPeer ? 'show' : '' }}" id="app-whatsapp-view">
                @if($currentPeer)
                    {{-- Chat Header --}}
                    <div class="chat-header border-bottom">
                        <button class="btn btn-icon btn-text-secondary rounded-pill d-lg-none me-1"
                                wire:click="closeChat">
                            <i class="ri ri-arrow-left-line icon-20px"></i>
                        </button>

                        @php
                            $peerInitials = strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $currentPeerName ?: 'W') ?: 'W', 0, 1));
                            $isGroupChat = str_contains($currentPeer ?? '', '@g.us');
                        @endphp
                        <div class="avatar avatar-sm cursor-pointer" wire:click="toggleContactInfo">
                            <span class="avatar-initial rounded-circle bg-primary">
                                @if($isGroupChat)
                                    <i class="ri ri-group-line"></i>
                                @else
                                    {{ $peerInitials }}
                                @endif
                            </span>
                        </div>

                        <div class="flex-grow-1 cursor-pointer" wire:click="toggleContactInfo">
                            <h6 class="mb-0 fw-semibold">{{ $currentPeerName }}</h6>
                            @if($currentPeerStatus)
                                <small class="text-muted">{{ $currentPeerStatus }}</small>
                            @else
                                <small class="text-{{ $this->statusColor }}">
                                    <i class="ri {{ $this->statusIcon }} me-1" style="font-size:0.6rem"></i>{{ $this->statusText }}
                                </small>
                            @endif
                        </div>

                        <div class="d-flex align-items-center gap-1">
                            <label class="btn btn-icon btn-text-secondary rounded-pill mb-0" title="Adjuntar archivo">
                                <i class="ri ri-attachment-2 icon-18px"></i>
                                <input type="file" wire:model="attachment" class="d-none">
                            </label>
                            <button class="btn btn-icon btn-text-secondary rounded-pill"
                                    wire:click="toggleContactInfo" title="Info del contacto">
                                <i class="ri ri-more-2-fill icon-18px"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Messages Area --}}
                    <div class="messages-container" id="messages-container" wire:ignore.self>
                        @php $lastDate = null; @endphp
                        @forelse($messages as $msg)
                            @php
                                $msgDate = \Carbon\Carbon::parse($msg['timestamp'])->format('Y-m-d');
                                $showDate = $msgDate !== $lastDate;
                                $lastDate = $msgDate;
                            @endphp

                            @if($showDate)
                                <div class="date-separator">
                                    <span>
                                        @if(\Carbon\Carbon::parse($msg['timestamp'])->isToday())
                                            Hoy
                                        @elseif(\Carbon\Carbon::parse($msg['timestamp'])->isYesterday())
                                            Ayer
                                        @else
                                            {{ \Carbon\Carbon::parse($msg['timestamp'])->format('d M Y') }}
                                        @endif
                                    </span>
                                </div>
                            @endif

                            <div class="message-wrapper {{ $msg['isOutgoing'] ? 'outgoing' : 'incoming' }}"
                                 wire:key="msg-{{ $loop->index }}">
                                <div class="message-bubble">
                                    @if($msg['type'] === 'image' && $msg['mediaUrl'])
                                        <div class="message-media">
                                            <img src="{{ $msg['mediaUrl'] }}" alt="Imagen" loading="lazy">
                                        </div>
                                    @elseif($msg['type'] === 'document')
                                        <div class="message-media-document">
                                            <i class="ri ri-file-text-line" style="font-size:1.5rem"></i>
                                            <span class="small text-truncate">{{ $msg['text'] }}</span>
                                        </div>
                                    @elseif($msg['type'] === 'audio')
                                        <div class="voice-message">
                                            <button class="btn btn-icon btn-sm rounded-pill {{ $msg['isOutgoing'] ? 'btn-text-white' : 'btn-text-secondary' }}">
                                                <i class="ri ri-play-fill"></i>
                                            </button>
                                            <div class="voice-progress">
                                                <div class="voice-progress-bar"></div>
                                            </div>
                                            <span class="small">0:00</span>
                                        </div>
                                    @endif

                                    @if($msg['type'] !== 'audio')
                                        <div class="message-text">{!! nl2br(e($msg['text'])) !!}</div>
                                    @endif

                                    <div class="message-meta">
                                        <span>{{ \Carbon\Carbon::parse($msg['timestamp'])->format('H:i') }}</span>
                                        @if($msg['isOutgoing'])
                                            @php $status = $msg['status'] ?? 'sent'; @endphp
                                            <span class="message-status {{ $status }}">
                                                @if($status === 'read')
                                                    <i class="ri ri-check-double-fill"></i>
                                                @elseif($status === 'delivered')
                                                    <i class="ri ri-check-double-line"></i>
                                                @elseif($status === 'sent')
                                                    <i class="ri ri-check-line"></i>
                                                @elseif($status === 'pending')
                                                    <i class="ri ri-time-line"></i>
                                                @elseif($status === 'failed')
                                                    <i class="ri ri-error-warning-line text-danger"></i>
                                                @endif
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-chat-state">
                                <i class="ri ri-chat-smile-2-line"></i>
                                <h6>Sin mensajes</h6>
                                <p class="small text-muted">Envía un mensaje para iniciar la conversación</p>
                            </div>
                        @endforelse
                    </div>

                    {{-- File Upload Preview --}}
                    @if($attachment)
                    <div class="px-3 py-2 border-top bg-light d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center gap-2">
                            <i class="ri ri-file-line text-primary"></i>
                            <span class="small text-truncate" style="max-width:200px">{{ $attachment->getClientOriginalName() }}</span>
                            <span class="badge bg-label-secondary small">{{ number_format($attachment->getSize() / 1024, 0) }} KB</span>
                        </div>
                        <div class="d-flex gap-1">
                            <button class="btn btn-sm btn-primary" wire:click="sendAttachment" wire:loading.attr="disabled">
                                <i class="ri ri-send-plane-2-line me-1"></i>Enviar
                            </button>
                            <button class="btn btn-sm btn-text-secondary" wire:click="$set('attachment', null)">
                                <i class="ri ri-close-line"></i>
                            </button>
                        </div>
                    </div>
                    @endif

                    {{-- Composer --}}
                    <div class="chat-composer">
                        <button class="btn btn-icon btn-text-secondary rounded-pill" title="Emoji">
                            <i class="ri ri-emotion-happy-line icon-20px"></i>
                        </button>

                        <label class="btn btn-icon btn-text-secondary rounded-pill mb-0" title="Adjuntar">
                            <i class="ri ri-attachment-2 icon-20px"></i>
                            <input type="file" wire:model="attachment" class="d-none">
                        </label>

                        <input type="text"
                               class="form-control message-input"
                               placeholder="Escribe un mensaje..."
                               wire:model.defer="messageText"
                               wire:keydown.enter="sendMessage"
                               autocomplete="off"
                               @if($connectionStatus !== 'connected') disabled @endif
                               id="message-input">

                        @if(!empty(trim($messageText ?? '')))
                            <button class="btn btn-icon btn-primary rounded-pill"
                                    wire:click="sendMessage"
                                    wire:loading.attr="disabled"
                                    wire:target="sendMessage"
                                    title="Enviar">
                                <i class="ri ri-send-plane-2-fill icon-20px"></i>
                            </button>
                        @else
                            <button class="btn btn-icon btn-text-secondary rounded-pill" title="Mensaje de voz" id="btn-voice-record">
                                <i class="ri ri-mic-line icon-20px"></i>
                            </button>
                        @endif
                    </div>

                @else
                    {{-- No Chat Selected --}}
                    <div class="empty-chat-state">
                        <i class="ri ri-whatsapp-line" style="font-size:5rem;opacity:0.15"></i>
                        <h5 class="mt-3 mb-2 text-body">WhatsApp Chat</h5>
                        <p class="text-muted mb-3">Selecciona una conversación para comenzar a chatear</p>
                        <div class="d-flex gap-2">
                            <button class="btn btn-primary btn-sm" wire:click="$set('showNewChatModal', true)">
                                <i class="ri ri-chat-new-line me-1"></i>Nuevo Chat
                            </button>
                            <button class="btn btn-label-primary btn-sm" wire:click="refreshAll">
                                <i class="ri ri-refresh-line me-1"></i>Actualizar
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            {{-- ============================================ --}}
            {{-- CONTACT INFO SIDEBAR --}}
            {{-- ============================================ --}}
            @if($currentPeer)
            <div class="contact-info-sidebar {{ $showContactInfo ? 'show' : '' }}">
                <div class="p-3 border-bottom d-flex align-items-center justify-content-between">
                    <h6 class="mb-0">Info del contacto</h6>
                    <button class="btn btn-icon btn-text-secondary rounded-pill btn-sm" wire:click="toggleContactInfo">
                        <i class="ri ri-close-line icon-20px"></i>
                    </button>
                </div>
                <div class="text-center py-4">
                    <div class="avatar avatar-xl mx-auto mb-3">
                        <span class="avatar-initial rounded-circle bg-primary" style="font-size:1.5rem">
                            {{ strtoupper(substr(preg_replace('/[^a-zA-Z]/', '', $currentPeerName ?: 'W') ?: 'W', 0, 1)) }}
                        </span>
                    </div>
                    <h5 class="mb-1">{{ $currentPeerName }}</h5>
                    <p class="text-muted small mb-0">{{ $currentPeer }}</p>
                </div>
                <div class="px-3">
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <span class="text-muted small d-block">Teléfono</span>
                            <span>{{ $currentPeer }}</span>
                        </li>
                        <li class="mb-3">
                            <span class="text-muted small d-block">Estado conexión</span>
                            <span class="badge bg-{{ $this->statusColor }}">{{ $this->statusText }}</span>
                        </li>
                    </ul>
                </div>
                <div class="px-3 pt-2 border-top">
                    <p class="text-muted small text-uppercase mb-2">Acciones</p>
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.whatsapp.send-messages') }}" class="btn btn-sm btn-label-primary">
                            <i class="ri ri-send-plane-line me-1"></i>Enviar desde plantilla
                        </a>
                        <a href="{{ route('admin.whatsapp.history') }}" class="btn btn-sm btn-label-info">
                            <i class="ri ri-history-line me-1"></i>Ver historial
                        </a>
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>

    {{-- ============================================ --}}
    {{-- NEW CHAT MODAL --}}
    {{-- ============================================ --}}
    @if($showNewChatModal)
    <div class="modal fade show d-block" tabindex="-1" style="background:rgba(0,0,0,0.5)">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="ri ri-chat-new-line me-2"></i>Nuevo Chat
                    </h5>
                    <button type="button" class="btn-close" wire:click="$set('showNewChatModal', false)"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Número de teléfono</label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="ri ri-phone-line"></i></span>
                            <input type="text"
                                   class="form-control"
                                   placeholder="Ej: +584241234567"
                                   wire:model.defer="newChatPhone"
                                   autocomplete="off">
                        </div>
                        <small class="text-muted">Incluye el código de país</small>
                        @error('newChatPhone')
                            <small class="text-danger d-block">{{ $message }}</small>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Mensaje inicial (opcional)</label>
                        <textarea class="form-control"
                                  rows="3"
                                  placeholder="Escribe un mensaje..."
                                  wire:model.defer="newChatMessage"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-label-secondary" wire:click="$set('showNewChatModal', false)">
                        Cancelar
                    </button>
                    <button type="button" class="btn btn-primary" wire:click="startNewChat"
                            wire:loading.attr="disabled" wire:target="startNewChat">
                        <span wire:loading.remove wire:target="startNewChat">
                            <i class="ri ri-send-plane-line me-1"></i>Iniciar Chat
                        </span>
                        <span wire:loading wire:target="startNewChat">
                            <span class="spinner-border spinner-border-sm me-1"></span>Iniciando...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- Loading Overlay --}}
    <div wire:loading.flex wire:target="refreshAll"
         class="position-fixed top-0 start-0 w-100 h-100 justify-content-center align-items-center"
         style="background:rgba(255,255,255,0.7);z-index:1060">
        <div class="text-center">
            <div class="spinner-border text-primary mb-2"></div>
            <p class="mb-0 small">Actualizando...</p>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('livewire:init', () => {
    Livewire.on('scrollToBottom', () => {
        setTimeout(() => {
            const container = document.getElementById('messages-container');
            if (container) container.scrollTop = container.scrollHeight;
        }, 100);
    });

    Livewire.on('chatSelected', () => {
        setTimeout(() => {
            const container = document.getElementById('messages-container');
            if (container) container.scrollTop = container.scrollHeight;
            const input = document.getElementById('message-input');
            if (input) input.focus();
        }, 200);
    });

    Livewire.on('messageSent', () => {
        setTimeout(() => {
            const container = document.getElementById('messages-container');
            if (container) container.scrollTop = container.scrollHeight;
            const input = document.getElementById('message-input');
            if (input) input.focus();
        }, 100);
    });

    Livewire.on('notify', (data) => {
        const d = Array.isArray(data) ? data[0] : data;
        if (typeof toastr !== 'undefined') {
            toastr[d.type](d.message);
        }
    });

    // Mobile sidebar toggle
    const sidebarToggle = document.getElementById('whatsapp-sidebar-toggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', () => {
            const sidebar = document.getElementById('app-whatsapp-sidebar');
            if (sidebar) sidebar.classList.toggle('show');
        });
    }

    // Socket.IO for real-time messages
    const apiUrl = @json(rtrim(config('whatsapp.api_url', 'http://localhost:3001'), '/'));
    try {
        const script = document.createElement('script');
        script.src = apiUrl + '/socket.io/socket.io.js';
        script.onload = () => {
            if (typeof io !== 'undefined') {
                const socket = io(apiUrl, { transports: ['websocket'] });
                socket.on('message-received', (msg) => {
                    Livewire.dispatch('messageReceived', { data: msg });
                });
                socket.on('message-updated', () => {
                    Livewire.dispatch('messageReceived', { data: null });
                });
            }
        };
        document.body.appendChild(script);
    } catch(e) {
        console.warn('Socket.IO not available:', e);
    }

    // Auto-refresh conversations every 30s using Livewire dispatch
    setInterval(() => {
        @this.call('loadConversations');
    }, 30000);
});
</script>
@endpush
