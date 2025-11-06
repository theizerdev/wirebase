<div>
    <div class="row mb-6">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1">Notificaciones</h4>
                    <p class="mb-0">Gestiona todas tus notificaciones</p>
                </div>
                <button wire:click="markAllAsRead" class="btn btn-primary">
                    <i class="ri ri-mail-open-line me-1"></i> Marcar todas como leídas
                </button>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body p-0">
                    @forelse($notifications as $notification)
                        <div class="d-flex align-items-start p-4 border-bottom {{ $notification->read_at ? 'bg-light bg-opacity-50' : '' }}">
                            <div class="avatar me-3">
                                <span class="avatar-initial rounded bg-label-{{ $notification->type === 'success' ? 'success' : ($notification->type === 'warning' ? 'warning' : ($notification->type === 'error' ? 'danger' : 'primary')) }}">
                                    <i class="ri ri-{{ $notification->type === 'success' ? 'check' : ($notification->type === 'warning' ? 'alert' : ($notification->type === 'error' ? 'close' : 'information')) }}-line ri-22px"></i>
                                </span>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="mb-0">{{ $notification->title }}</h6>
                                    <div class="d-flex align-items-center">
                                        <small class="text-body me-3">{{ $notification->created_at->diffForHumans() }}</small>
                                        @if(!$notification->read_at)
                                            <button wire:click="markAsRead({{ $notification->id }})" class="btn btn-sm btn-outline-primary">
                                                Marcar como leída
                                            </button>
                                        @else
                                            <span class="badge bg-label-success">Leída</span>
                                        @endif
                                    </div>
                                </div>
                                <p class="text-body mb-0">{{ $notification->message }}</p>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-5">
                            <i class="ri ri-notification-off-line ri-48px text-body mb-3"></i>
                            <h6 class="text-body">No tienes notificaciones</h6>
                            <p class="text-body">Las notificaciones aparecerán aquí cuando las recibas</p>
                        </div>
                    @endforelse
                </div>
            </div>

            @if($notifications->hasPages())
                <div class="mt-4">
                    {{ $notifications->links('livewire.pagination') }}
                </div>
            @endif
        </div>
    </div>
</div>
