<div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Importar Estudiantes (CSV)</h5>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-outline-secondary">
                        <i class="ri ri-arrow-left-line me-1"></i>Volver
                    </a>
                </div>
                <div class="card-body">
                    @if (session()->has('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="ri ri-check-line me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if (session()->has('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="ri ri-error-warning-line me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="alert alert-info">
                        <h6 class="alert-heading">Formato del archivo CSV:</h6>
                        <p class="mb-2">El archivo debe tener las siguientes columnas en este orden:</p>
                        <ol class="mb-0">
                            <li><strong>Nombres</strong> (requerido)</li>
                            <li><strong>Apellidos</strong> (requerido)</li>
                            <li><strong>Documento de Identidad</strong></li>
                            <li><strong>Fecha de Nacimiento</strong> (formato: YYYY-MM-DD)</li>
                            <li><strong>Grado</strong></li>
                            <li><strong>Sección</strong></li>
                        </ol>
                    </div>

                    <form wire:submit="import">
                        <div class="mb-3">
                            <label class="form-label">Archivo CSV <span class="text-danger">*</span></label>
                            <input type="file" 
                                   class="form-control" 
                                   wire:model="file" 
                                   accept=".csv">
                        </div>

                        <div class="d-flex justify-content-between align-items-center">
                            <button type="submit" 
                                    class="btn btn-primary" 
                                    wire:loading.attr="disabled"
                                    wire:target="import">
                                <span wire:loading.remove wire:target="import">
                                    <i class="ri ri-upload-line me-1"></i>Importar Estudiantes
                                </span>
                                <span wire:loading wire:target="import">
                                    <i class="ri ri-loader-4-line me-1"></i>Importando...
                                </span>
                            </button>

                            @if($importing)
                                <div class="text-muted">
                                    <i class="ri ri-loader-4-line"></i> Procesando archivo...
                                </div>
                            @endif
                        </div>
                    </form>

                    @if(!empty($importErrors))
                        <div class="mt-4">
                            <h6 class="text-danger">Errores encontrados:</h6>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach($importErrors as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    @if($importedCount > 0 || $failedCount > 0)
                        <div class="mt-4">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <div class="card bg-success text-white">
                                        <div class="card-body text-center">
                                            <h4 class="mb-1">{{ $importedCount }}</h4>
                                            <p class="mb-0">Estudiantes Creados</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card bg-danger text-white">
                                        <div class="card-body text-center">
                                            <h4 class="mb-1">{{ $failedCount }}</h4>
                                            <p class="mb-0">Errores</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>