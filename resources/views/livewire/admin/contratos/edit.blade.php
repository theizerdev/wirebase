<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Editar Contrato #{{ $contrato->numero_contrato }}</h5>
                    <a href="{{ route('admin.contratos.show', $contrato->id) }}" class="btn btn-label-secondary">
                        <i class="ri ri-arrow-left-line me-1"></i> Volver
                    </a>
                </div>
                <div class="card-body">
                    <form wire:submit="save">
                        <div class="alert alert-warning">
                            <i class="ri ri-alert-line me-2"></i>
                            Los datos financieros del contrato no pueden ser editados una vez creado. Si hay un error, por favor cancele este contrato y cree uno nuevo.
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Observaciones</label>
                            <textarea class="form-control" wire:model="observaciones" rows="5" 
                                      placeholder="Notas adicionales sobre el contrato..."></textarea>
                            @error('observaciones') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <button type="submit" class="btn btn-primary">
                                <i class="ri ri-save-line me-1"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
