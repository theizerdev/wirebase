<div>
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title mb-0">Editar País</h4>
                </div>
                <div class="card-body">
                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form wire:submit.prevent="save">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombre" class="form-label">Nombre del País <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nombre') is-invalid @enderror"
                                       id="nombre" wire:model="nombre" placeholder="Ej: Venezuela">
                                @error('nombre')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="codigo_iso2" class="form-label">Código ISO 2 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('codigo_iso2') is-invalid @enderror"
                                       id="codigo_iso2" wire:model="codigo_iso2" placeholder="Ej: VE" maxlength="2">
                                @error('codigo_iso2')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="codigo_iso3" class="form-label">Código ISO 3 <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('codigo_iso3') is-invalid @enderror"
                                       id="codigo_iso3" wire:model="codigo_iso3" placeholder="Ej: VEN" maxlength="3">
                                @error('codigo_iso3')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="codigo_telefonico" class="form-label">Código Telefónico</label>
                                <input type="text" class="form-control @error('codigo_telefonico') is-invalid @enderror"
                                       id="codigo_telefonico" wire:model="codigo_telefonico" placeholder="Ej: +58">
                                @error('codigo_telefonico')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="moneda_principal" class="form-label">Moneda Principal <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('moneda_principal') is-invalid @enderror"
                                       id="moneda_principal" wire:model="moneda_principal" placeholder="Ej: USD" maxlength="3">
                                @error('moneda_principal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="idioma_principal" class="form-label">Idioma Principal <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('idioma_principal') is-invalid @enderror"
                                       id="idioma_principal" wire:model="idioma_principal" placeholder="Ej: es" maxlength="2">
                                @error('idioma_principal')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="continente" class="form-label">Continente <span class="text-danger">*</span></label>
                                <select class="form-select @error('continente') is-invalid @enderror" id="continente" wire:model="continente">
                                    <option value="">Seleccione un continente</option>
                                    <option value="América">América</option>
                                    <option value="Europa">Europa</option>
                                    <option value="Asia">Asia</option>
                                    <option value="África">África</option>
                                    <option value="Oceanía">Oceanía</option>
                                    <option value="Antártida">Antártida</option>
                                </select>
                                @error('continente')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="latitud" class="form-label">Latitud de la Capital</label>
                                <input type="number" class="form-control @error('latitud') is-invalid @enderror"
                                       id="latitud" wire:model="latitud" placeholder="Ej: 10.4806" 
                                       step="0.000001" min="-90" max="90">
                                @error('latitud')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Coordenada latitudinal de la capital</small>
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="longitud" class="form-label">Longitud de la Capital</label>
                                <input type="number" class="form-control @error('longitud') is-invalid @enderror"
                                       id="longitud" wire:model="longitud" placeholder="Ej: -66.9036"
                                       step="0.000001" min="-180" max="180">
                                @error('longitud')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text text-muted">Coordenada longitudinal de la capital</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="zona_horaria" class="form-label">Zona Horaria <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('zona_horaria') is-invalid @enderror"
                                       id="zona_horaria" wire:model="zona_horaria" placeholder="Ej: America/Caracas">
                                @error('zona_horaria')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="formato_fecha" class="form-label">Formato de Fecha <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('formato_fecha') is-invalid @enderror"
                                       id="formato_fecha" wire:model="formato_fecha" placeholder="Ej: d/m/Y">
                                @error('formato_fecha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="formato_fecha" class="form-label">Formato de Fecha <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('formato_fecha') is-invalid @enderror"
                                       id="formato_fecha" wire:model="formato_fecha" placeholder="Ej: d/m/Y">
                                @error('formato_fecha')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="formato_moneda" class="form-label">Formato de Moneda <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('formato_moneda') is-invalid @enderror"
                                       id="formato_moneda" wire:model="formato_moneda" placeholder="Ej: #,##0.00">
                                @error('formato_moneda')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label for="impuesto_predeterminado" class="form-label">Impuesto Predeterminado (%)</label>
                                <input type="number" class="form-control @error('impuesto_predeterminado') is-invalid @enderror"
                                       id="impuesto_predeterminado" wire:model="impuesto_predeterminado"
                                       step="0.01" min="0" max="100">
                                @error('impuesto_predeterminado')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="separador_miles" class="form-label">Separador de Miles <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('separador_miles') is-invalid @enderror"
                                       id="separador_miles" wire:model="separador_miles" maxlength="1">
                                @error('separador_miles')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="separador_decimales" class="form-label">Separador de Decimales <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('separador_decimales') is-invalid @enderror"
                                       id="separador_decimales" wire:model="separador_decimales" maxlength="1">
                                @error('separador_decimales')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-3">
                                <label for="decimales_moneda" class="form-label">Decimales de Moneda <span class="text-danger">*</span></label>
                                <input type="number" class="form-control @error('decimales_moneda') is-invalid @enderror"
                                       id="decimales_moneda" wire:model="decimales_moneda"
                                       min="0" max="4">
                                @error('decimales_moneda')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="activo" wire:model="activo">
                                    <label class="form-check-label" for="activo">
                                        País Activo
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary me-2">
                                    <i class="fas fa-save"></i> Actualizar
                                </button>
                                <a href="{{ route('admin.paises.index') }}" class="btn btn-secondary">
                                    <i class="fas fa-times"></i> Cancelar
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>