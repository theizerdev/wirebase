<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Registrar Unidad</h5>
                        <p class="mb-0 text-muted">Modelo: <strong>{{ $moto->titulo }}</strong></p>
                    </div>
                    <a href="{{ route('admin.motos.unidades.index', $moto->id) }}" class="btn btn-label-secondary">
                        <i class="ri ri-arrow-left-line me-1"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    <form wire:submit="save">
                        <div class="row g-3">
                            <!-- Ubicación -->
                            <div class="col-12">
                                <h6 class="fw-bold text-primary">Ubicación y Propiedad</h6>
                                <hr class="mt-0">
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select wire:model.live="empresa_id" id="empresa_id" class="form-select @error('empresa_id') is-invalid @enderror">
                                        <option value="">Seleccione...</option>
                                        @foreach($empresas as $empresa)
                                            <option value="{{ $empresa->id }}">{{ $empresa->razon_social }}</option>
                                        @endforeach
                                    </select>
                                    <label for="empresa_id">Empresa Propietaria</label>
                                    @error('empresa_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline">
                                    <select wire:model="sucursal_id" id="sucursal_id" class="form-select @error('sucursal_id') is-invalid @enderror" 
                                            {{ empty($empresa_id) ? 'disabled' : '' }}>
                                        <option value="">Seleccione...</option>
                                        @foreach($sucursales as $sucursal)
                                            <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <label for="sucursal_id">Sucursal / Almacén</label>
                                    @error('sucursal_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <!-- Identificación -->
                            <div class="col-12 mt-4">
                                <h6 class="fw-bold text-primary">Identificación de la Unidad</h6>
                                <hr class="mt-0">
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="vin" class="form-control @error('vin') is-invalid @enderror" 
                                           wire:model="vin" placeholder="XXXXXXXXXXXXXXXXX">
                                    <label for="vin">VIN / Serial de Chasis</label>
                                    @error('vin') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="numero_chasis" class="form-control @error('numero_chasis') is-invalid @enderror" 
                                           wire:model="numero_chasis" placeholder="Igual al VIN si aplica">
                                    <label for="numero_chasis">Número de Chasis</label>
                                    @error('numero_chasis') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="numero_motor" class="form-control @error('numero_motor') is-invalid @enderror" 
                                           wire:model="numero_motor" placeholder="Número de motor">
                                    <label for="numero_motor">Número de Motor</label>
                                    @error('numero_motor') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="placa" class="form-control @error('placa') is-invalid @enderror" 
                                           wire:model="placa" placeholder="Placa (si posee)">
                                    <label for="placa">Placa (Opcional)</label>
                                    @error('placa') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="text" id="color_especifico" class="form-control @error('color_especifico') is-invalid @enderror" 
                                           wire:model="color_especifico" placeholder="Color exacto">
                                    <label for="color_especifico">Color Específico</label>
                                    @error('color_especifico') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <select wire:model="condicion" id="condicion" class="form-select @error('condicion') is-invalid @enderror">
                                        <option value="nuevo">Nuevo</option>
                                        <option value="usado">Usado</option>
                                    </select>
                                    <label for="condicion">Condición</label>
                                    @error('condicion') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <!-- Costos y Fechas -->
                            <div class="col-12 mt-4">
                                <h6 class="fw-bold text-primary">Detalles Financieros</h6>
                                <hr class="mt-0">
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" id="costo_compra" class="form-control @error('costo_compra') is-invalid @enderror" 
                                           wire:model="costo_compra" placeholder="0.00">
                                    <label for="costo_compra">Costo de Adquisición ($)</label>
                                    @error('costo_compra') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" step="0.01" id="precio_venta" class="form-control @error('precio_venta') is-invalid @enderror" 
                                           wire:model="precio_venta" placeholder="0.00">
                                    <label for="precio_venta">Precio de Venta ($)</label>
                                    @error('precio_venta') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="number" id="kilometraje" class="form-control @error('kilometraje') is-invalid @enderror" 
                                           wire:model="kilometraje" placeholder="0">
                                    <label for="kilometraje">Kilometraje</label>
                                    @error('kilometraje') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-floating form-floating-outline">
                                    <input type="date" id="fecha_ingreso" class="form-control @error('fecha_ingreso') is-invalid @enderror" 
                                           wire:model="fecha_ingreso">
                                    <label for="fecha_ingreso">Fecha de Ingreso</label>
                                    @error('fecha_ingreso') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>

                            <div class="col-12">
                                <div class="form-floating form-floating-outline">
                                    <textarea id="notas" class="form-control h-px-100 @error('notas') is-invalid @enderror" 
                                              wire:model="notas" placeholder="Observaciones sobre la unidad..."></textarea>
                                    <label for="notas">Notas / Observaciones</label>
                                    @error('notas') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2 mt-4">
                            <a href="{{ route('admin.motos.unidades.index', $moto->id) }}" class="btn btn-label-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri ri-save-line me-1"></i> Guardar Unidad
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
