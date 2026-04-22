<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Nueva Motocicleta</h5>
                    <a href="{{ route('admin.motos.index') }}" class="btn btn-label-secondary">
                        <i class="ri ri-arrow-left-line me-1"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    <form wire:submit="save">
                        <div class="row g-3">
                            <!-- Datos Generales -->
                            <div class="col-12">
                                <h6 class="fw-bold">Información General</h6>
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

                            <div class="col-md-6">
                                <label class="form-label required">Marca</label>
                                <input type="text" class="form-control @error('marca') is-invalid @enderror" 
                                       wire:model="marca" placeholder="Ej: Bera, Empire, Suzuki">
                                @error('marca') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Modelo</label>
                                <input type="text" class="form-control @error('modelo') is-invalid @enderror" 
                                       wire:model="modelo" placeholder="Ej: SBR, Horse, GN125">
                                @error('modelo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Año</label>
                                <input type="number" class="form-control @error('anio') is-invalid @enderror" 
                                       wire:model="anio" placeholder="Ej: 2024">
                                @error('anio') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Detalles Técnicos -->
                            <div class="col-12 mt-4">
                                <h6 class="fw-bold">Detalles Técnicos</h6>
                                <hr class="mt-0">
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Color Principal</label>
                                <input type="text" class="form-control @error('color_principal') is-invalid @enderror" 
                                       wire:model="color_principal" placeholder="Ej: Rojo, Azul, Negro">
                                @error('color_principal') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Cilindrada</label>
                                <input type="text" class="form-control @error('cilindrada') is-invalid @enderror" 
                                       wire:model="cilindrada" placeholder="Ej: 150cc, 200cc">
                                @error('cilindrada') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">Tipo de Moto</label>
                                <select wire:model="tipo" class="form-select @error('tipo') is-invalid @enderror">
                                    <option value="">Seleccione...</option>
                                    <option value="Sincrónicas">Sincrónicas</option>
                                    <option value="Deportivas">Deportiva</option>
                                    <option value="Carga">Carga</option>
                                    <option value="Scooter">Scooter</option>
                                    <option value="Enduro">Enduro</option>
                                </select>
                                @error('tipo') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Precios -->
                            <div class="col-12 mt-4">
                                <h6 class="fw-bold">Información Financiera</h6>
                                <hr class="mt-0">
                            </div>

                            <div class="col-md-6">
                                <label class="form-label required">Precio de Venta Base ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control @error('precio_venta_base') is-invalid @enderror" 
                                           wire:model="precio_venta_base" placeholder="0.00">
                                </div>
                                @error('precio_venta_base') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">Costo Referencial ($)</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" step="0.01" class="form-control @error('costo_referencial') is-invalid @enderror" 
                                           wire:model="costo_referencial" placeholder="0.00">
                                </div>
                                <div class="form-text">Costo de adquisición referencial para cálculos de utilidad.</div>
                                @error('costo_referencial') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>

                            <!-- Descripción -->
                            <div class="col-12">
                                <label class="form-label">Descripción / Notas</label>
                                <textarea class="form-control @error('descripcion') is-invalid @enderror" 
                                          wire:model="descripcion" rows="3"></textarea>
                                @error('descripcion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <!-- Estado -->
                            <div class="col-12">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="activo" wire:model="activo">
                                    <label class="form-check-label" for="activo">Modelo Activo (Disponible para venta)</label>
                                </div>
                            </div>

                            <!-- Registro de Unidad Inicial -->
                            <div class="col-12 mt-4">
                                <div class="card bg-label-secondary border">
                                    <div class="card-header pb-0">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="registrar_unidad" wire:model.live="registrar_unidad">
                                            <label class="form-check-label fw-bold text-primary" for="registrar_unidad">
                                                ¿Registrar la primera unidad física ahora?
                                            </label>
                                        </div>
                                    </div>
                                    
                                    @if($registrar_unidad)
                                    <div class="card-body pt-3">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label required">Sucursal / Almacén</label>
                                                <select wire:model="sucursal_id" class="form-select @error('sucursal_id') is-invalid @enderror"
                                                        {{ empty($empresa_id) ? 'disabled' : '' }}>
                                                    <option value="">Seleccione...</option>
                                                    @foreach($sucursales as $sucursal)
                                                        <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                                    @endforeach
                                                </select>
                                                @error('sucursal_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label required">VIN / Chasis</label>
                                                <input type="text" class="form-control @error('vin') is-invalid @enderror" 
                                                       wire:model="vin" placeholder="Serial de Chasis">
                                                @error('vin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label required">N° Motor</label>
                                                <input type="text" class="form-control @error('numero_motor') is-invalid @enderror" 
                                                       wire:model="numero_motor">
                                                @error('numero_motor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label required">N° Chasis</label>
                                                <input type="text" class="form-control @error('numero_chasis') is-invalid @enderror" 
                                                       wire:model="numero_chasis">
                                                @error('numero_chasis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label">Placa</label>
                                                <input type="text" class="form-control @error('placa') is-invalid @enderror" 
                                                       wire:model="placa">
                                                @error('placa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label required">Condición</label>
                                                <select wire:model="condicion" class="form-select @error('condicion') is-invalid @enderror">
                                                    <option value="nuevo">Nuevo</option>
                                                    <option value="usado">Usado</option>
                                                </select>
                                                @error('condicion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label required">Kilometraje</label>
                                                <input type="number" class="form-control @error('kilometraje') is-invalid @enderror" 
                                                       wire:model="kilometraje">
                                                @error('kilometraje') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label required">Fecha Ingreso</label>
                                                <input type="date" class="form-control @error('fecha_ingreso') is-invalid @enderror" 
                                                       wire:model="fecha_ingreso">
                                                @error('fecha_ingreso') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.motos.index') }}" class="btn btn-label-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri ri-save-line me-1"></i> Guardar Modelo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
