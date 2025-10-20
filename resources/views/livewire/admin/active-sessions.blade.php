<div>
  @if (session()->has('status'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      {{ session('status') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
  @endif

  <div class="row">
    <div class="col-12">
      <div class="card">
        <div class="card-header border-bottom">
          <div class="d-flex justify-content-between align-items-center">
            <div>
              <h5 class="card-title mb-1">Sesiones Activas</h5>
              <p class="mb-0">Administra tus sesiones activas en diferentes dispositivos</p>
            </div>
            <div>
              <button type="button" class="btn btn-primary" wire:click="loadSessions">
                <i class="ri-refresh-line"></i> Actualizar
              </button>
            </div>
          </div>
        </div>

        <!-- Filtros -->
        <div class="card-header border-bottom">
          <div class="row g-3">
            <div class="col-md-3">
              <label class="form-label">Buscar</label>
              <input type="text" class="form-control" placeholder="IP, ubicación, dispositivo..." wire:model.live.debounce.300ms="search">
            </div>

            <div class="col-md-3">
              <label class="form-label">Estado</label>
              <select class="form-select" wire:model.live="status">
                <option value="">Todos los estados</option>
                <option value="active">Activa</option>
                <option value="inactive">Inactiva</option>
                <option value="current">Sesión actual</option>
              </select>
            </div>

            <div class="col-md-3">
              <label class="form-label">Mostrar</label>
              <select class="form-select" wire:model.live="perPage">
                <option value="10">10 por página</option>
                <option value="25">25 por página</option>
                <option value="50">50 por página</option>
                <option value="100">100 por página</option>
              </select>
            </div>

            <div class="col-md-3 d-flex align-items-end">
              <button type="button" class="btn btn-label-secondary" wire:click="clearFilters">
                <i class="ri-eraser-line"></i> Limpiar filtros
              </button>
            </div>
          </div>
        </div>

        <div class="card-datatable table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th wire:click="sortBy('user_agent')" style="cursor: pointer;">
                  Dispositivo
                  @if($sortBy === 'user_agent')
                    <i class="ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                  @endif
                </th>
                <th wire:click="sortBy('ip_address')" style="cursor: pointer;">
                  IP
                  @if($sortBy === 'ip_address')
                    <i class="ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                  @endif
                </th>
                <th wire:click="sortBy('location')" style="cursor: pointer;">
                  Ubicación
                  @if($sortBy === 'location')
                    <i class="ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                  @endif
                </th>
                <th wire:click="sortBy('last_activity')" style="cursor: pointer;">
                  Última Actividad
                  @if($sortBy === 'last_activity')
                    <i class="ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                  @endif
                </th>
                <th wire:click="sortBy('is_active')" style="cursor: pointer;">
                  Estado
                  @if($sortBy === 'is_active')
                    <i class="ri-arrow-{{ $sortDirection === 'asc' ? 'up' : 'down' }}-line"></i>
                  @endif
                </th>
                <th>Acciones</th>
              </tr>
            </thead>
            <tbody>
              @forelse($activeSessions as $session)
              <tr>
                <td>
                  <div class="d-flex align-items-center">
                    <div class="me-2">
                      <i class="ri ri-computer-line ri-24px text-primary"></i>
                    </div>
                    <div>
                      <span class="d-block">{{ Str::limit($session->user_agent, 50) }}</span>
                      <small class="text-muted">
                        {{ $session->is_current ? 'Esta sesión' : 'Otra sesión' }}
                      </small>
                    </div>
                  </div>
                </td>
                <td>{{ $session->ip_address }}</td>
                <td>
                  @if($session->location)
                    {{ $session->location }}
                  @else
                    Desconocida
                  @endif
                  @if($session->latitude && $session->longitude)
                    <br><small class="text-muted">({{ $session->latitude }}, {{ $session->longitude }})</small>
                  @endif
                </td>
                <td>{{ $session->last_activity->diffForHumans() }}</td>
                <td>
                  @if($session->is_active)
                    <span class="badge bg-label-success">Activa</span>
                  @else
                    <span class="badge bg-label-secondary">Inactiva</span>
                  @endif
                </td>
                <td>
                  @if(!$session->is_current && $session->is_active)
                    <button type="button"
                            class="btn btn-sm btn-danger"
                            wire:click="destroy({{ $session->id }})"
                            wire:confirm="¿Estás seguro de que deseas terminar esta sesión?">
                      Terminar Sesión
                    </button>
                  @elseif($session->is_current)
                    <button type="button"
                            class="btn btn-sm btn-danger"
                            wire:click="destroy({{ $session->id }})"
                            wire:confirm="¿Estás seguro de que deseas cerrar esta sesión?">
                      Cerrar Sesión
                    </button>
                  @else
                    <span class="text-muted">Sesión inactiva</span>
                  @endif
                </td>
              </tr>
              @empty
              <tr>
                <td colspan="6" class="text-center">No se encontraron sesiones que coincidan con los filtros</td>
              </tr>
              @endforelse
            </tbody>
          </table>
        </div>

        <!-- Paginación -->
        <div class="card-footer">
          {{ $activeSessions->links('vendor.pagination.materialize') }}
        </div>
      </div>
    </div>
  </div>
</div>
