<div>
    <div class="card mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Información Básica</h5>
        <div>
            <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-warning btn-sm">
                <i class="ri ri-edit-line"></i> Editar
            </a>
        </div>
    </div>

    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label text-muted small">Nombre completo</label>
                    <p class="fs-5">{{ $user->name }}</p>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small">Correo electrónico</label>
                    <p class="fs-5">{{ $user->email }}</p>
                </div>
            </div>

            <div class="col-md-6">
                <div class="mb-3">
                    <label class="form-label text-muted small">Estado</label>
                    <p>
                        <span class="badge bg-{{ $user->status ? 'success' : 'danger' }} fs-6">
                            {{ $user->status ? 'Activo' : 'Inactivo' }}
                        </span>
                    </p>
                </div>

                <div class="mb-3">
                    <label class="form-label text-muted small">Fecha de registro</label>
                    <p class="fs-5">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Empresa</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted small">Nombre</label>
                    <p class="fs-5">{{ $user->empresa->nombre ?? 'N/A' }}</p>
                </div>
                @if($user->empresa)
                <div class="mb-3">
                    <label class="form-label text-muted small">Contacto</label>
                    <p class="fs-5">{{ $user->empresa->contacto ?? 'N/A' }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-header">
                <h6 class="mb-0">Sucursal</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="form-label text-muted small">Nombre</label>
                    <p class="fs-5">{{ $user->sucursal->nombre ?? 'N/A' }}</p>
                </div>
                @if($user->sucursal)
                <div class="mb-3">
                    <label class="form-label text-muted small">Dirección</label>
                    <p class="fs-5">{{ $user->sucursal->direccion ?? 'N/A' }}</p>
                </div>
                <div class="mb-3">
                    <label class="form-label text-muted small">Teléfono</label>
                    <p class="fs-5">{{ $user->sucursal->telefono ?? 'N/A' }}</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Historial de Accesos</h5>
        <div class="text-muted small">Últimos 30 días</div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Fecha/Hora</th>
                        <th>IP</th>
                        <th>Ubicación</th>
                        <th>Dispositivo</th>
                        <th class="text-center">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($sessions as $session)
                        <tr>
                            <td>
                                @if($session->login_at)
                                    {{ $session->login_at->format('d/m/Y H:i') }}
                                @else
                                    N/A
                                @endif
                            </td>
                            <td>{{ $session->ip_address ?? 'N/A' }}</td>
                            <td>{{ $session->location ?? 'N/A' }}</td>
                            <td>{{ $session->user_agent ?? 'N/A' }}</td>
                            <td class="text-center">
                                <span class="badge bg-{{ $session->is_active ? 'success' : 'secondary' }}">
                                    {{ $session->is_active ? 'Activo' : 'Finalizado' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No hay registros de acceso</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            {{ $sessions->links() }}
            <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary">
                <i class="ri ri-arrow-left-line"></i> Volver al listado
            </a>
        </div>
    </div>
</div>

</div>
