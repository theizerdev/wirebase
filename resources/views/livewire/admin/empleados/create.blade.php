<div>
    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="ri ri-error-warning-line me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    <div class="card">
        <div class="card-header">
            <h5 class="card-title mb-0">Nuevo Empleado</h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nombre</label>
                    <input type="text" class="form-control" wire:model.lazy="nombre">
                </div>
                <div class="col-md-6">
                    <label class="form-label">Apellido</label>
                    <input type="text" class="form-control" wire:model.lazy="apellido">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Documento</label>
                    <input type="text" class="form-control" wire:model.lazy="documento">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Puesto</label>
                    <input type="text" class="form-control" wire:model.lazy="puesto">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Salario Base</label>
                    <input type="number" step="0.01" class="form-control" wire:model.lazy="salario_base">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Horas Extra Base</label>
                    <input type="number" step="0.5" class="form-control" wire:model.lazy="horas_extra_base">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Bono Fijo</label>
                    <input type="number" step="0.01" class="form-control" wire:model.lazy="bono_fijo">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Comisión Fija</label>
                    <input type="number" step="0.01" class="form-control" wire:model.lazy="comision_fija">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Método de Pago</label>
                    <select class="form-select" wire:model.live="metodo_pago">
                        <option value="efectivo">Efectivo</option>
                        <option value="transferencia">Transferencia</option>
                        <option value="tarjeta">Tarjeta</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Teléfono</label>
                    <input type="text" class="form-control" wire:model.lazy="telefono">
                </div>
                <div class="col-md-4">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control" wire:model.lazy="email">
                </div>
                <div class="col-md-12">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="activoSwitch" wire:model="activo">
                        <label class="form-check-label" for="activoSwitch">Activo</label>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end gap-2">
            <a href="{{ route('admin.empleados.index') }}" class="btn btn-label-secondary">Cancelar</a>
            <button class="btn btn-primary" wire:click="store"><i class="ri ri-save-line me-1"></i> Guardar</button>
        </div>
    </div>
</div>
