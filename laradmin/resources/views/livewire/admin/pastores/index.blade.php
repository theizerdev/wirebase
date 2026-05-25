@else
    <div class="row g-4">
        @forelse($pastores as $data)
            <div class="col-md-6 col-lg-4 col-xl-3">
                <div class="card h-100 border-0 shadow-sm rounded-3">
                    <div class="card-body text-center p-3">
                        <div class="position-relative mb-3">
                            <div class="avatar avatar-lg mx-auto mb-3">
                                @if ($data->foto != null)
                                    <img class="rounded-circle img-fluid border border-2 border-primary"
                                        src="/pastores/{{ str_replace(' ', '',$data->foto) }}"
                                        alt="Foto de {{ $data->nombres }} {{ $data->apellidos }}" 
                                        style="width: 80px; height: 80px; object-fit: cover;" />
                                @else
                                    <span class="avatar-initial rounded-circle bg-primary border border-2 border-primary" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center; font-size: 1.5rem;">
                                        {{ strtoupper(substr($data->nombres, 0, 1)) . strtoupper(substr($data->apellidos, 0, 1)) }}
                                    </span>
                                @endif
                            </div>
                            @if ($data->status)
                                <span class="badge bg-success position-absolute top-0 end-0 mt-2">Activo</span>
                            @else
                                <span class="badge bg-secondary position-absolute top-0 end-0 mt-2">Inactivo</span>
                            @endif
                        </div>
                        
                        <h6 class="mb-1 text-truncate" title="{{ $data->nombres . ' ' . $data->apellidos }}">
                            {{ $data->nombres . ' ' . $data->apellidos }}
                        </h6>
                        
                        <div class="text-start small mb-3" style="min-height: 100px;">
                            <div class="d-flex align-items-center mb-1">
                                <i class="ri ri-id-card-line text-muted me-2 small" style="min-width: 20px;"></i>
                                <span class="text-truncate" title="{{ $data->documento }}">{{ $data->documento }}</span>
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <i class="ri ri-briefcase-line text-muted me-2 small" style="min-width: 20px;"></i>
                                <span class="text-truncate" title="{{ $data->nivel_ministerial }}">{{ $data->nivel_ministerial }}</span>
                            </div>
                            <div class="d-flex align-items-center mb-1">
                                <i class="ri ri-map-pin-line text-muted me-2 small" style="min-width: 20px;"></i>
                                <span class="text-truncate" title="{{ $data->zona }}">{{ $data->zona }}</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="ri ri-community-line text-muted me-2 small" style="min-width: 20px;"></i>
                                <span class="text-truncate" title="{{ $data->distrito }}">{{ $data->distrito }}</span>
                            </div>
                        </div>
                        
                        <div class="d-flex gap-2 justify-content-center">
                            @can('edit pastores')
                            <a href="{{ route('admin.pastores.edit', $data->id) }}"
                                class="btn btn-sm btn-outline-primary flex-fill" title="Editar">
                                <i class="ri ri-pencil-line"></i>
                            </a>
                            @endcan
                            @can('show pastores')
                            <a target="_blank" href="{{ route('admin.pastores.planilla', $data->id) }}"
                                class="btn btn-sm btn-outline-info flex-fill" title="Ver Planilla">
                                <i class="ri ri-file-text-line"></i>
                            </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="ri ri-user-line" style="font-size:3rem;opacity:.3;"></i>
                    <h5 class="mt-3 text-muted">No se encontraron pastores</h5>
                    <p class="mb-0">Intenta cambiar los filtros o crea nuevos pastores</p>
                </div>
            </div>
        @endforelse
    </div>
    
    <div class="mt-4">
        {{ $pastores->links('livewire.pagination') }}
    </div>
@endif