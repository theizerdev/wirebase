<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Crear Nuevo Usuario</h5>
    </div>

    <div class="card-body">
        <form wire:submit.prevent="save">
            <div class="row">
                <!-- Name -->
                <div class="col-md-6 mb-3">
                    <label for="name" class="form-label">Nombre</label>
                    <input
                        type="text"
                        wire:model="name"
                        class="form-control @error('name') is-invalid @enderror"
                        id="name"
                        placeholder="Nombre completo"
                    >
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Email -->
                <div class="col-md-6 mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input
                        type="email"
                        wire:model="email"
                        class="form-control @error('email') is-invalid @enderror"
                        id="email"
                        placeholder="Correo electrónico"
                    >
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="col-md-6 mb-3">
                    <label for="password" class="form-label">Contraseña</label>
                    <input
                        type="password"
                        wire:model="password"
                        class="form-control @error('password') is-invalid @enderror"
                        id="password"
                        placeholder="Contraseña"
                    >
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="col-md-6 mb-3">
                    <label for="password_confirmation" class="form-label">Confirmar Contraseña</label>
                    <input
                        type="password"
                        wire:model="password_confirmation"
                        class="form-control"
                        id="password_confirmation"
                        placeholder="Confirmar contraseña"
                    >
                </div>

                <!-- Empresa -->
                <div class="col-md-6 mb-3">
                    <label for="empresa_id" class="form-label">Empresa</label>
                    <select
                        wire:model.change="empresa_id"
                        class="form-select @error('empresa_id') is-invalid @enderror"
                        id="empresa_id"
                    >
                        <option value="">Seleccionar Empresa</option>
                        @foreach($empresas as $empresa)
                            <option value="{{ $empresa->id }}">{{ $empresa->razon_social }}</option>
                        @endforeach
                    </select>
                    @error('empresa_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Sucursal -->
                <div class="col-md-6 mb-3">
                    <label for="sucursal_id" class="form-label">Sucursal</label>
                    <select
                        wire:model="sucursal_id"
                        class="form-select @error('sucursal_id') is-invalid @enderror"
                        id="sucursal_id">
                        <option value="">Seleccionar Sucursal</option>
                        @foreach($sucursales as $sucursal)
                            <option value="{{ $sucursal->id }}">{{ $sucursal->nombre }}</option>
                        @endforeach
                    </select>
                    @error('sucursal_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Status -->
                <div class="col-md-6 mb-3">
                    <label class="form-label">Estado</label>
                    <div class="form-check form-switch">
                        <input
                            wire:model="status"
                            class="form-check-input"
                            type="checkbox"
                            id="status"
                            value="1"
                            checked
                        >
                        <label class="form-check-label" for="status">
                            {{ $status ? 'Activo' : 'Inactivo' }}
                        </label>
                    </div>
                </div>
            </div>

            <div class="d-flex justify-content-end mt-4">
                <a href="{{ route('admin.users.index') }}" class="btn btn-outline-secondary me-2">
                    <i class="ri ri-arrow-left-line"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="ri ri-save-line"></i> Guardar
                </button>
            </div>
        </form>
    </div>
</div>
