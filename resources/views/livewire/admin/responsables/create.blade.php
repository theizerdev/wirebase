<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header border-bottom">
                    <h5 class="card-title mb-1">Crear Responsable</h5>
                    <p class="mb-0">Completa la información para registrar un nuevo responsable</p>
                </div>
                <div class="card-body">
                    @if (session()->has('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form wire:submit.prevent="save">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('nombre_completo') is-invalid @enderror"
                                       wire:model="nombre_completo" placeholder="Ingrese el nombre completo">
                                @error('nombre_completo')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Cédula <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('cedula') is-invalid @enderror"
                                       wire:model="cedula" placeholder="Ingrese la cédula">
                                @error('cedula')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Estado</label>
                                <select class="form-select @error('estado_id') is-invalid @enderror" wire:model.live="estado_id">
                                    <option value="">Seleccione un estado</option>
                                    @foreach($estados as $estado)
                                        <option value="{{ $estado->id }}">{{ $estado->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('estado_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Municipio</label>
                                <select class="form-select @error('municipio_id') is-invalid @enderror" wire:model.live="municipio_id" :disabled="!estado_id">
                                    <option value="">Seleccione un municipio</option>
                                    @foreach($municipios as $municipio)
                                        <option value="{{ $municipio->id }}">{{ $municipio->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('municipio_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">Parroquia</label>
                                <select class="form-select @error('parroquia_id') is-invalid @enderror" wire:model.live="parroquia_id" :disabled="!municipio_id">
                                    <option value="">Seleccione una parroquia</option>
                                    @foreach($parroquias as $parroquia)
                                        <option value="{{ $parroquia->id }}">{{ $parroquia->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('parroquia_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Teléfono</label>
                                <input type="text" class="form-control @error('telefono') is-invalid @enderror"
                                       wire:model="telefono" placeholder="Ingrese el teléfono">
                                @error('telefono')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Fecha de Levantamiento</label>
                                <input type="date" class="form-control @error('fecha_levantamiento') is-invalid @enderror"
                                       wire:model.change="fecha_levantamiento">
                                @error('fecha_levantamiento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Dirección</label>
                                <textarea class="form-control @error('direccion') is-invalid @enderror"
                                          wire:model="direccion" rows="2" placeholder="Ingrese la dirección"></textarea>
                                @error('direccion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">Punto de Referencia</label>
                                <textarea class="form-control @error('punto_referencia') is-invalid @enderror"
                                          wire:model="punto_referencia" rows="2" placeholder="Ingrese un punto de referencia"></textarea>
                                @error('punto_referencia')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-12 mb-3">
                                <label class="form-label">Código de Casa de Alimentación</label>
                                <input type="text" class="form-control @error('codigo_casa_alimentacion') is-invalid @enderror"
                                       wire:model="codigo_casa_alimentacion" placeholder="Ingrese el código">
                                @error('codigo_casa_alimentacion')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Empresa</label>
                                <select class="form-select @error('empresa_id') is-invalid @enderror" wire:model.live="empresa_id" :disabled="!parroquia_id">
                                    <option value="">Seleccione una empresa</option>
                                    @foreach($empresas as $empresa)
                                        <option value="{{ $empresa->id }}">{{ $empresa->razon_social }}</option>
                                    @endforeach
                                </select>
                                @error('empresa_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Sucursal</label>
                                <select class="form-select @error('sucursal_id') is-invalid @enderror" wire:model="sucursal_id" :disabled="!empresa_id">
                                    <option value="">Seleccione una sucursal</option>
                                    @foreach($sucursales as $sucursal)
                                        <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                                    @endforeach
                                </select>
                                @error('sucursal_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Opción para crear usuario automáticamente -->
                            <div class="col-12 mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="createUserSwitch" wire:model.live="create_user">
                                    <label class="form-check-label" for="createUserSwitch">
                                        <strong>Crear usuario asociado</strong> - Se creará un usuario con los datos del responsable
                                    </label>
                                </div>
                            </div>

                            <!-- Campos para la creación de usuario (solo visibles si create_user es true) -->
                            @if($create_user)
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Nombre de Usuario</label>
                                <input type="text" class="form-control @error('username') is-invalid @enderror"
                                       wire:model="username" placeholder="Nombre de usuario generado">
                                @error('username')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror"
                                       wire:model="email" placeholder="correo@proal.gob.ve">
                                @error('email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Contraseña</label>
                                <input type="text" class="form-control @error('user_password') is-invalid @enderror"
                                       wire:model="user_password" value="12345678" readonly>
                                <div class="form-text">La contraseña por defecto es 12345678</div>
                                @error('user_password')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Rol</label>
                                <select class="form-select @error('user_role') is-invalid @enderror" wire:model="user_role">
                                    <option value="">Seleccione un rol</option>
                                    @foreach($roles as $role)
                                        <option value="{{ $role->name }}">{{ $role->name }}</option>
                                    @endforeach
                                </select>
                                @error('user_role')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Estado del Usuario</label>
                                <select class="form-select @error('user_status') is-invalid @enderror" wire:model="user_status">
                                    <option value="1">Activo</option>
                                    <option value="0">Inactivo</option>
                                </select>
                                @error('user_status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            @endif
                        </div>

                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('admin.responsables.index') }}" class="btn btn-label-secondary">
                                <i class="ri ri-arrow-left-line"></i> Volver
                            </a>
                            @can('create responsables')
                            <button type="submit" class="btn btn-primary">
                                <i class="ri ri-save-line"></i> Guardar Responsable
                            </button>
                            @endcan
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>