<div>
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">Editar Nivel Educativo</h5>
        </div>
        <div class="card-body">
            <form wire:submit="save">
                <div class="mb-3">
                    <label for="nombre" class="form-label">Nombre</label>
                    <input wire:model="nombre" type="text" class="form-control" id="nombre">
                    @error('nombre') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label for="costo" class="form-label">Costo</label>
                    <input wire:model="costo" type="number" step="0.01" class="form-control" id="costo">
                    @error('costo') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="mb-3">
                    <label for="cuotas" class="form-label">Cuotas</label>
                    <input wire:model="cuotas" type="number" class="form-control" id="cuotas">
                    @error('cuotas') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="d-flex justify-content-end">
                    <a href="{{ route('admin.niveles-educativos.index') }}" class="btn btn-secondary me-2">
                        Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        Actualizar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
