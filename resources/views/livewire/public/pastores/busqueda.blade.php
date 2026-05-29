<div class="position-relative">
  <div class="authentication-wrapper authentication-basic container-p-y p-4 p-sm-0">
    <div class="authentication-inner py-6" style="max-width: 600px;">
      <div class="card p-md-7 p-1">
        <!-- Logo -->
        <div class="app-brand justify-content-center mt-5">
          <a href="{{ url('/') }}" class="app-brand-link gap-2">

            <div class="mb-5 text-center">
          @include('auth.header.logo')
        </div>
          </a>
        </div>
        <!-- /Logo -->

        <div class="card-body mt-1">
          <div class="text-center mb-5">
            <h3 class="mb-2 fw-bold text-primary">Actualización de Datos</h3>
            <p class="text-muted">Ingrese su número de cédula para gestionar su perfil pastoral e extensiones a cargo.</p>
          </div>

          <form wire:submit.prevent="buscar" class="mb-4">
            <div class="input-group input-group-merge input-group-lg mb-3 shadow-sm rounded-3">
              <span class="input-group-text border-end-0 bg-white" id="basic-addon-search31">
                <i class="ri ri-search-line ri-20px text-primary"></i>
              </span>
              <input
                type="text"
                class="form-control border-start-0 fs-4 ps-0"
                id="cedula"
                wire:model.live.debounce.300ms="cedula"
                placeholder="Ej. 12345678"
                aria-label="Buscar cédula"
                aria-describedby="basic-addon-search31"
                autofocus
                autocomplete="off" />
            </div>

            @if(!($searched && strlen($cedula) >= 4 && count($pastores) == 0))
            <div class="d-grid mt-4">
                <button class="btn btn-primary btn-lg rounded-pill shadow-sm" type="submit">
                  <span>Buscar Registro</span>
                </button>
            </div>
            @endif
          </form>

          @if($searched && strlen($cedula) >= 4)
            <div class="mt-5">
              @if(count($pastores) > 0)
                <h6 class="text-muted text-uppercase fw-semibold mb-3 fs-tiny letter-spacing-1">Resultados encontrados</h6>
                <div class="d-flex flex-column gap-3">
                  @foreach($pastores as $pastor)
                    <div class="card shadow-none border border-primary-subtle cursor-pointer hover-elevate-up transition-all rounded-4"
                         wire:click="seleccionarPastor({{ $pastor->id }})"
                         style="transition: all 0.2s ease-in-out;">
                      <div class="card-body p-3 d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                          <div class="avatar avatar-md me-3">
                            @if($pastor->foto && file_exists(public_path('pastores/'.str_replace(' ', '',$pastor->foto))))
                              <img src="{{ asset('pastores/'.str_replace(' ', '',$pastor->foto)) }}" alt="Foto" class="rounded-circle object-fit-cover shadow-sm" style="border: 2px solid #fff;">
                            @else
                              <span class="avatar-initial rounded-circle bg-label-primary shadow-sm">{{ substr($pastor->nombres, 0, 1) }}</span>
                            @endif
                          </div>
                          <div>
                            <h6 class="mb-0 fw-bold text-heading">{{ $pastor->nombres }} {{ $pastor->apellidos }}</h6>
                            <div class="d-flex align-items-center mt-1">
                                <span class="badge bg-label-secondary me-2 rounded-pill"><i class="ri ri-id-card-line ri-14px me-1"></i>{{ $pastor->documento }}</span>
                                @if($pastor->esConyuge())
                                    <span class="badge bg-label-info rounded-pill"><i class="ri ri-heart-line ri-14px me-1"></i>Cónyuge</span>
                                @else
                                    <span class="badge bg-label-success rounded-pill"><i class="ri ri-user-star-line ri-14px me-1"></i>Pastor</span>
                                @endif
                            </div>
                          </div>
                        </div>
                        <div class="bg-primary-subtle rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 36px; height: 36px;">
                            <i class="ri ri-arrow-right-line text-primary"></i>
                        </div>
                      </div>
                    </div>
                  @endforeach
                </div>
              @else
                <div class="text-center p-4 border rounded-4 bg-label-secondary">
                  <div class="bg-white rounded-circle d-inline-flex p-3 mb-3 shadow-sm">
                    <i class="ri ri-user-unfollow-line ri-3x text-warning"></i>
                  </div>
                  <h5 class="mb-2 fw-bold text-heading">Pastor no encontrado</h5>
                  <p class="text-muted mb-4">La cédula <strong>{{ $cedula }}</strong> no se encuentra registrada en nuestra base de datos o su perfil se encuentra inactivo.</p>

                  <div class="d-flex flex-column gap-2">
                    <a href="{{ route('public.pastores.registrar', ['cedula' => $cedula]) }}" class="btn btn-primary btn-lg rounded-pill shadow-sm">
                        <i class="ri ri-user-add-line me-2"></i> Registrarse ahora
                    </a>
                    <button wire:click="$set('cedula', '')" class="btn btn-outline-secondary btn-lg rounded-pill mt-2">
                        <i class="ri ri-refresh-line me-2"></i> Intentar de nuevo
                    </button>
                  </div>
                </div>
              @endif
            </div>
          @endif


        </div>
      </div>
    </div>
  </div>

  <!-- Modal de Solicitud Pendiente -->
  @if($showSolicitudModal && $solicitudPendiente)
  <div class="modal-backdrop fade show" style="z-index: 1050;"></div>
  <div class="modal fade show d-block" tabindex="-1" style="z-index: 1051;" id="solicitudModal">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg">
        <div class="modal-header bg-warning text-dark">
          <h5 class="modal-title">
            <i class="ri ri-time-line me-2"></i>
            Solicitud en Espera de Aprobación
          </h5>
          <button type="button" class="btn-close" wire:click="$set('showSolicitudModal', false)" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4">
          {{-- Alerta informativa --}}
          <div class="alert alert-warning mb-4">
            <div class="d-flex align-items-start">
              <i class="ri ri-information-line fs-4 me-3"></i>
              <div>
                <h6 class="alert-heading mb-2">Su solicitud está siendo procesada</h6>
                <p class="mb-0 small">
                  Ha solicitado modificar o ingresar sus datos personales y eclesiásticos en el sistema. El Presbítero de su zona debe aprobar esta solicitud antes de que pueda continuar.
                </p>
              </div>
            </div>
          </div>

          {{-- Información del Pastor --}}
          <div class="card bg-light border-0 mb-3">
            <div class="card-body py-3">
              <div class="d-flex align-items-center">
                @if($pastorData->foto)
                  <img src="{{ asset('pastores/' . str_replace(' ', '', $pastorData->foto)) }}" 
                       alt="{{ $pastorData->nombre_completo }}"
                       class="rounded-circle me-3"
                       style="width: 60px; height: 60px; object-fit: cover;">
                @else
                  <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3"
                       style="width: 60px; height: 60px;">
                    <i class="ri ri-user-fill fs-4"></i>
                  </div>
                @endif
                <div>
                  <h6 class="mb-1 fw-bold">{{ $pastorData->nombre_completo }}</h6>
                  <p class="mb-0 text-muted small">
                    <i class="ri ri-id-card-line me-1"></i>{{ $pastorData->documento }}
                    <br>
                    <i class="ri ri-map-pin-line me-1"></i>{{ $pastorData->zona ?? 'Sin zona' }}
                  </p>
                </div>
              </div>
            </div>
          </div>

          {{-- Detalles de la Solicitud --}}
          <div class="card border-warning border-2 mb-3">
            <div class="card-header bg-warning bg-opacity-25">
              <h6 class="mb-0 fw-bold">
                <i class="ri ri-file-text-line me-2"></i>
                Detalles de la Solicitud
              </h6>
            </div>
            <div class="card-body">
              <div class="alert alert-light border mb-3">
                <p class="mb-0 small">
                  <i class="ri ri-information-line me-1 text-primary"></i>
                  {{ $solicitudPendiente->descripcion_solicitud ?? 'El pastor desea modificar o ingresar sus datos personales y eclesiásticos en el sistema.' }}
                </p>
              </div>

              <hr>

              <div class="text-muted small">
                <div class="mb-2">
                  <i class="ri ri-time-line me-1"></i>
                  <strong>Solicitado el:</strong> {{ $solicitudPendiente->created_at->format('d/m/Y H:i') }}
                </div>
                <div class="mb-2">
                  <i class="ri ri-hourglass-line me-1"></i>
                  <strong>Expira el:</strong> {{ $solicitudPendiente->token_expires_at->format('d/m/Y H:i') }}
                </div>
                <div>
                  <i class="ri ri-map-pin-line me-1"></i>
                  <strong>Presbítero:</strong> Esperando aprobación del presbítero de su zona
                </div>
              </div>
            </div>
          </div>

          {{-- Estado actual --}}
          <div class="alert alert-info mb-0">
            <div class="d-flex align-items-center">
              <i class="ri ri-loader-4-line fs-4 me-3" style="animation: spin 1s linear infinite;"></i>
              <div>
                <h6 class="mb-1">Estado: En Espera de Aprobación</h6>
                <p class="mb-0 small">
                  El presbítero ha sido notificado vía WhatsApp. Una vez que apruebe su solicitud, podrá configurar su seguridad y actualizar sus datos.
                </p>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-outline-secondary" wire:click="$set('showSolicitudModal', false)">
            <i class="ri ri-close-line me-1"></i>Cerrar
          </button>
        </div>
      </div>
    </div>
  </div>
  
  <style>
    @keyframes spin {
      from { transform: rotate(0deg); }
      to { transform: rotate(360deg); }
    }
  </style>
  @endif

  <!-- Modal de Pregunta de Seguridad -->
  @if($showSecurityQuestionModal)
  <div class="modal-backdrop fade show" style="z-index: 1050;"></div>
  <div class="modal fade show d-block" tabindex="-1" style="z-index: 1051;" id="securityQuestionModal">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title"> Verificación de Seguridad</h5>
          <button type="button" class="btn-close" wire:click="$set('showSecurityQuestionModal', false)" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <p class="text-muted mb-3">Por seguridad, responda la siguiente pregunta:</p>
          
          @if($failedAttemptsCount > 0)
          <div class="alert alert-warning mb-3">
            <i class="ri ri-alert-line me-2"></i>
            <strong>Intentos fallidos:</strong> {{ $failedAttemptsCount }}
            @if($failedAttemptsCount >= 2)
            <br><small>Después de 3 intentos fallidos se aplicará un período de espera.</small>
            @endif
          </div>
          @endif
          
          <div class="alert alert-info mb-3">
            <i class="ri ri-question-line me-2"></i>
            <strong>{{ $securityQuestion }}</strong>
          </div>

          @if($showCaptcha)
          <div class="alert alert-warning mb-3">
            <i class="ri ri-shield-check-line me-2"></i>
            <strong>Verificación adicional:</strong>
            <p class="mb-1 mt-2">{{ $captchaQuestion }}</p>
            <input 
              type="number" 
              class="form-control form-control-sm" 
              wire:model="captchaInput"
              wire:keydown.enter="verifySecurityAnswer"
              placeholder="Ingrese el resultado"
              autocomplete="off">
            @error('captchaInput') <div class="text-danger mt-1 small">{{ $message }}</div> @enderror
          </div>
          @endif

          <div class="mb-3">
            <label for="securityAnswer" class="form-label">Su Respuesta</label>
            <input 
              type="text" 
              class="form-control" 
              id="securityAnswer" 
              wire:model="securityAnswer"
              wire:keydown.enter="verifySecurityAnswer"
              placeholder="Ingrese su respuesta"
              autocomplete="off">
            @error('securityAnswer') <div class="text-danger mt-1">{{ $message }}</div> @enderror
            @if($verificationError)
              <div class="alert alert-danger mt-2 mb-0">
                <i class="ri ri-error-warning-line me-1"></i>
                {{ $verificationError }}
              </div>
            @endif
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" wire:click="$set('showSecurityQuestionModal', false)">Cerrar</button>
          <button type="button" class="btn btn-primary" wire:click="verifySecurityAnswer" wire:loading.attr="disabled">
            <span wire:loading.remove>Verificar Respuesta</span>
            <span wire:loading>
              <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
              Verificando...
            </span>
          </button>
        </div>
      </div>
    </div>
  </div>
  @endif

  <!-- Modal de Validación OTP -->
  @if($showOtpModal)
  <div class="modal-backdrop fade show" style="z-index: 1050;"></div>
  <div class="modal fade show d-block" tabindex="-1" style="z-index: 1051;" id="otpModal">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Verificación de Identidad</h5>
          <button type="button" class="btn-close" wire:click="$set('showOtpModal', false)" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          @if(!$otpSent)
          @if($phoneNumber)
          <div class="alert alert-success mb-3">
            <i class="ri ri-check-line me-2"></i>
            <strong> Verificación de seguridad completada!</strong>
            <p class="mb-0 mt-1">Se enviará un código de verificación a su número registrado.</p>
          </div>
          @else
          <p class="text-muted">Por seguridad, ingrese su número de teléfono para recibir un código de verificación.</p>
          @endif
          
          <div class="mb-3">
            <label for="phoneNumber" class="form-label">Número de Teléfono</label>
            @if($phoneNumber)
            <input 
              type="tel" 
              class="form-control" 
              id="phoneNumber" 
              value="{{ $phoneNumber }}"
              disabled
              readonly>
              <input type="hidden" wire:model="phoneNumber">
            @else
            <input 
              type="tel" 
              class="form-control" 
              id="phoneNumber" 
              wire:model="phoneNumber"
              placeholder="Ingrese su número de teléfono">
            @endif
            @error('phoneNumber') <div class="text-danger mt-1">{{ $message }}</div> @enderror
          </div>
          @elseif($otpSent && !$otpVerified)
          <p class="text-muted">Se ha enviado un código de verificación a su número. Ingrese el código de 6 dígitos a continuación:</p>
          <div class="mb-3">
            <label for="otpCode" class="form-label">Código de Verificación</label>
            <input 
              type="text" 
              class="form-control text-center" 
              id="otpCode" 
              wire:model="otpCode"
              maxlength="6"
              inputmode="numeric"
              pattern="[0-9]*"
              placeholder="------">
            @error('otpCode') <div class="text-danger mt-1">{{ $message }}</div> @enderror
          </div>
          <div class="d-flex justify-content-between">
            <small class="text-muted">Código enviado a: <strong>{{ $phoneNumber }}</strong></small>
            @if(session()->has('success'))
            <small class="text-success">{{ session('success') }}</small>
            @endif
          </div>
          @endif

          @if($verificationError)
          <div class="alert alert-danger mt-3">
            {{ $verificationError }}
          </div>
          @endif

          @if(session()->has('error'))
          <div class="alert alert-danger mt-3">
            {{ session('error') }}
          </div>
          @endif
        </div>
        <div class="modal-footer">
          @if(!$otpSent)
          <button type="button" class="btn btn-primary flex-fill" wire:click="sendOtp" wire:loading.attr="disabled">
            <span wire:loading.remove><i class="ri ri-send-plane-line me-1"></i>Enviar Código de Verificación</span>
           
          </button>
          @elseif($otpSent && !$otpVerified)
          <button type="button" class="btn btn-success flex-fill" wire:click="verifyOtp" wire:loading.attr="disabled">
            <span wire:loading.remove><i class="ri ri-check-line me-1"></i>Verificar Código</span>
           
          </button>
          @endif
          
          <button type="button" class="btn btn-secondary" wire:click="$set('showOtpModal', false)">Cerrar</button>
        </div>
      </div>
    </div>
  </div>
  @endif

  <style>
    .hover-elevate-up:hover {
        transform: translateY(-3px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.08)!important;
        border-color: var(--bs-primary) !important;
    }
    .hover-primary:hover {
        color: var(--bs-primary) !important;
    }
    .hover-primary:hover .bg-secondary-subtle {
        background-color: var(--bs-primary-subtle) !important;
    }
    .hover-primary:hover .ri-shield-user-line {
        color: var(--bs-primary) !important;
    }
    .modal.show {
      display: block;
    }
    .form-control.text-center {
      font-size: 1.5rem;
      letter-spacing: 3px;
    }
  </style>
</div>