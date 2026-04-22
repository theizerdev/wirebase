<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Nuevo Cliente</h5>
                    <a href="{{ route('admin.clientes.index') }}" class="btn btn-label-secondary">
                        <i class="ri ri-arrow-left-line me-1"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    <form wire:submit="save">
                        <div class="row g-3">
                            <!-- Datos Personales -->
                            <div class="col-12">
                                <h6 class="fw-bold">Información Personal</h6>
                                <hr class="mt-0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Empresa</label>
                                <select wire:model="empresa_id" class="form-select @error('empresa_id') is-invalid @enderror">
                                    <option value="">Seleccione una empresa</option>
                                    @foreach($empresas as $empresa)
                                        <option value="{{ $empresa->id }}">{{ $empresa->razon_social }}</option>
                                    @endforeach
                                </select>
                                @error('empresa_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label required">Tipo Documento</label>
                                <select wire:model="tipo_documento" class="form-select @error('tipo_documento') is-invalid @enderror">
                                    <option value="CI">Cédula (CI)</option>
                                    <option value="RIF">RIF</option>
                                    <option value="Pasaporte">Pasaporte</option>
                                    <option value="Otro">Otro</option>
                                </select>
                                @error('tipo_documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-3">
                                <label class="form-label required">Número Documento</label>
                                <input type="text" class="form-control @error('documento') is-invalid @enderror" 
                                       wire:model="documento" placeholder="Ej: 12345678">
                                @error('documento') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Nombres</label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror" 
                                       wire:model="nombre" placeholder="Ej: Juan Carlos">
                                @error('nombre') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Apellidos</label>
                                <input type="text" class="form-control @error('apellido') is-invalid @enderror" 
                                       wire:model="apellido" placeholder="Ej: Pérez Rodríguez">
                                @error('apellido') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Contacto -->
                            <div class="col-12 mt-4">
                                <h6 class="fw-bold">Información de Contacto</h6>
                                <hr class="mt-0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Teléfono Principal</label>
                                <input type="text" class="form-control @error('telefono') is-invalid @enderror" 
                                       wire:model="telefono" placeholder="Ej: 04121234567">
                                @error('telefono') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Teléfono Alternativo</label>
                                <input type="text" class="form-control @error('telefono_alternativo') is-invalid @enderror" 
                                       wire:model="telefono_alternativo" placeholder="Ej: 02121234567">
                                @error('telefono_alternativo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                       wire:model="email" placeholder="Ej: cliente@correo.com">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-12">
                                <label class="form-label">Dirección de Habitación</label>
                                <textarea class="form-control @error('direccion') is-invalid @enderror" 
                                          wire:model="direccion" rows="2" placeholder="Dirección completa"></textarea>
                                @error('direccion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Ciudad</label>
                                <input type="text" class="form-control @error('ciudad') is-invalid @enderror" 
                                       wire:model="ciudad" placeholder="Ej: Caracas">
                                @error('ciudad') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Estado/Región</label>
                                <input type="text" class="form-control @error('estado_region') is-invalid @enderror" 
                                       wire:model="estado_region" placeholder="Ej: Distrito Capital">
                                @error('estado_region') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Datos Laborales -->
                            <div class="col-12 mt-4">
                                <h6 class="fw-bold">Datos Laborales / Financieros</h6>
                                <hr class="mt-0">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Ocupación</label>
                                <input type="text" class="form-control @error('ocupacion') is-invalid @enderror" 
                                       wire:model="ocupacion" placeholder="Ej: Comerciante, Empleado">
                                @error('ocupacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Lugar de Trabajo</label>
                                <input type="text" class="form-control @error('empresa_trabajo') is-invalid @enderror" 
                                       wire:model="empresa_trabajo" placeholder="Nombre de la empresa o negocio">
                                @error('empresa_trabajo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Ingreso Mensual Estimado ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control @error('ingreso_mensual_estimado') is-invalid @enderror" 
                                           wire:model="ingreso_mensual_estimado" placeholder="0.00">
                                </div>
                                @error('ingreso_mensual_estimado') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <!-- Estado -->
                            <div class="col-12 mt-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="activo" wire:model="activo">
                                    <label class="form-check-label" for="activo">Cliente Activo</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.clientes.index') }}" class="btn btn-label-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri ri-save-line me-1"></i> Guardar Cliente
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
