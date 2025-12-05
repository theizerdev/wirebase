<div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">
                <i class="fas fa-paper-plane"></i>
                Enviar Mensaje
            </h4>
        </div>
        <div class="card-body">
            @if($success)
                <div class="alert alert-success alert-dismissible">
                    {{ $success }}
                    <button type="button" class="btn-close" wire:click="clearMessages"></button>
                </div>
            @endif

            @if($error)
                <div class="alert alert-danger alert-dismissible">
                    {{ $error }}
                    <button type="button" class="btn-close" wire:click="clearMessages"></button>
                </div>
            @endif

            <form wire:submit.prevent="sendMessage">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="to" class="form-label">Número de Teléfono</label>
                            <input type="text" 
                                   class="form-control @error('to') is-invalid @enderror" 
                                   id="to" 
                                   wire:model="to" 
                                   placeholder="584121234567"
                                   maxlength="15">
                            @error('to')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Formato: código de país + número (ej: 584121234567)
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label for="message" class="form-label">Mensaje</label>
                            <textarea class="form-control @error('message') is-invalid @enderror" 
                                      id="message" 
                                      wire:model="message" 
                                      rows="4" 
                                      placeholder="Escribe tu mensaje aquí..."
                                      maxlength="1000"></textarea>
                            @error('message')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div class="form-text">
                                Máximo 1000 caracteres
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <button type="submit" 
                            class="btn btn-success" 
                            wire:loading.attr="disabled"
                            @disabled($sending)>
                        <span wire:loading.remove wire:target="sendMessage">
                            <i class="fab fa-whatsapp"></i> Enviar Mensaje
                        </span>
                        <span wire:loading wire:target="sendMessage">
                            <i class="fas fa-spinner fa-spin"></i> Enviando...
                        </span>
                    </button>

                    <button type="button" 
                            class="btn btn-outline-secondary" 
                            wire:click="loadRecentMessages">
                        <i class="fas fa-sync"></i> Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if(count($recentMessages) > 0)
        <div class="card mt-4">
            <div class="card-header">
                <h5 class="card-title">Mensajes Enviados Recientemente</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Fecha</th>
                                <th>Para</th>
                                <th>Mensaje</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentMessages as $msg)
                                @if($msg['status'] === 'sent')
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($msg['createdAt'])->format('d/m H:i') }}</td>
                                        <td>{{ $msg['to'] }}</td>
                                        <td>{{ Str::limit($msg['message'], 40) }}</td>
                                        <td>
                                            <span class="badge bg-success">
                                                <i class="fas fa-check"></i> Enviado
                                            </span>
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif
</div>