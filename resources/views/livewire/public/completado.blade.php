<div>
    <div class="container-xxl container-p-y mt-5">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="card text-center p-5 shadow border-0" style="border-radius: 15px;">
                    <div class="mb-4">
                        <div class="avatar-initial rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center icon-success-animation" style="width: 90px; height: 90px; box-shadow: 0 0 20px rgba(40, 199, 111, 0.4);">
                            <i class="ri ri-check-double-line" style="font-size: 3.5rem;"></i>
                        </div>
                    </div>

                    <h3 class="mb-3 text-success fw-bold">¡Datos registrados exitosamente!</h3>
                    <p class="text-muted mb-4 fs-5">El proceso se ha completado de manera satisfactoria y sus datos han sido guardados de forma segura.</p>

                    <div class="text-start bg-lighter p-4 rounded mb-4 shadow-sm border">
                        <h6 class="mb-3 border-bottom pb-2 fw-bold text-uppercase text-secondary">
                            <i class="ri ri-file-list-3-line me-1"></i> Resumen del Registro
                        </h6>

                        <div class="row mb-2">
                            <div class="col-5 text-muted">Nombre Completo:</div>
                            <div class="col-7 fw-semibold text-dark">{{ $pastor->nombres }} {{ $pastor->apellidos }}</div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-5 text-muted">Documento de Identidad:</div>
                            <div class="col-7 fw-semibold text-dark">{{ $pastor->documento }}</div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-5 text-muted">Nivel Ministerial:</div>
                            <div class="col-7 fw-semibold text-dark">
                                @if($pastor->nivel_ministerial)
                                    <span class="badge bg-label-primary">{{ $pastor->nivel_ministerial }}</span>
                                @else
                                    <span class="text-muted">No especificado</span>
                                @endif
                            </div>
                        </div>

                        <div class="row mb-2">
                            <div class="col-5 text-muted">Fecha y Hora:</div>
                            <div class="col-7 fw-semibold text-dark">{{ now()->format('d/m/Y h:i A') }}</div>
                        </div>
                    </div>

                    <div class="mt-4">
                        @if($pastor->iglesias->count() > 0)
                            @php
                                $ultimaIglesia = $pastor->iglesias->sortByDesc('created_at')->first();
                            @endphp
                            
                        @endif
                        <a href="{{ route('public.pastores.busqueda') }}" class="btn btn-primary btn-lg w-100 rounded-pill shadow-sm">
                            Finalizar y Volver al Inicio <i class="ri ri-arrow-right-line ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Animación sutil de entrada para la tarjeta */
        .card {
            animation: slideUpFade 0.6s ease-out forwards;
        }

        /* Animación de latido y escala para el ícono de check */
        @keyframes scaleInPulse {
            0% { transform: scale(0); opacity: 0; }
            50% { transform: scale(1.15); opacity: 1; }
            70% { transform: scale(0.95); }
            100% { transform: scale(1); opacity: 1; }
        }

        .icon-success-animation {
            animation: scaleInPulse 0.8s cubic-bezier(0.175, 0.885, 0.32, 1.275) forwards;
        }

        @keyframes slideUpFade {
            0% { transform: translateY(30px); opacity: 0; }
            100% { transform: translateY(0); opacity: 1; }
        }
    </style>
</div>
