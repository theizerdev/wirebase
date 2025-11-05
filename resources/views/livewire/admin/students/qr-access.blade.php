<div wire:poll.5s="loadStats">
    <!-- Header -->
    <div class="row mb-6">
        <div class="col-12">
            <h4 class="mb-1">Control de Acceso Estudiantil</h4>
            <p class="mb-0">Sistema de registro automático con código QR para estudiantes</p>
        </div>
    </div>

    <!-- Estadísticas -->
    <div class="row g-6 mb-6">
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-4">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ri ri-login-box-line ri-26px"></i>
                            </span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">Entradas Hoy</h6>
                                <small class="text-body">Accesos de entrada</small>
                            </div>
                            <div class="user-progress d-flex align-items-center gap-2">
                                <h6 class="mb-0">{{ number_format($stats['entries']) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-4">
                            <span class="avatar-initial rounded bg-label-danger">
                                <i class="ri ri-logout-box-line ri-26px"></i>
                            </span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">Salidas Hoy</h6>
                                <small class="text-body">Accesos de salida</small>
                            </div>
                            <div class="user-progress d-flex align-items-center gap-2">
                                <h6 class="mb-0">{{ number_format($stats['exits']) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-4">
                            <span class="avatar-initial rounded bg-label-primary">
                                <i class="ri ri-bar-chart-box-line ri-26px"></i>
                            </span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">Total Movimientos</h6>
                                <small class="text-body">Registros del día</small>
                            </div>
                            <div class="user-progress d-flex align-items-center gap-2">
                                <h6 class="mb-0">{{ number_format($stats['total']) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-sm-6">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="avatar flex-shrink-0 me-4">
                            <span class="avatar-initial rounded bg-label-info">
                                <i class="ri ri-graduation-cap-line ri-26px"></i>
                            </span>
                        </div>
                        <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                                <h6 class="mb-0">Estudiantes Activos</h6>
                                <small class="text-body">Total registrados</small>
                            </div>
                            <div class="user-progress d-flex align-items-center gap-2">
                                <h6 class="mb-0">{{ number_format($stats['activeStudents']) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Escáner QR -->
    <div class="row g-6 mb-6">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Escáner de Acceso</h5>
                    <div class="d-flex gap-2">
                        <button wire:click="toggleSound" class="btn btn-sm {{ $soundEnabled ? 'btn-primary' : 'btn-outline-secondary' }}" title="{{ $soundEnabled ? 'Desactivar' : 'Activar' }} sonido">
                            <i class="ri ri-volume-{{ $soundEnabled ? 'up' : 'mute' }}-line"></i>
                        </button>
                        <div class="btn-group" role="group">
                            <button wire:click="$set('scanMode', 'camera')" class="btn btn-sm {{ $scanMode === 'camera' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="ri ri-camera-line me-1"></i> Cámara
                            </button>
                            <button wire:click="$set('scanMode', 'manual')" class="btn btn-sm {{ $scanMode === 'manual' ? 'btn-primary' : 'btn-outline-primary' }}">
                                <i class="ri ri-keyboard-line me-1"></i> Manual
                            </button>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    @if($processing)
                        <div class="text-center py-4">
                            <div class="spinner-border text-primary mb-3" role="status">
                                <span class="visually-hidden">Procesando...</span>
                            </div>
                            <p class="text-muted">Procesando acceso...</p>
                        </div>
                    @else
                        @if($scanMode === 'camera')
                            <div class="text-center">
                                <div id="qr-reader" class="mb-3 border rounded" style="width: 100%; max-width: 400px; margin: 0 auto; min-height: 300px;"></div>
                                <p class="text-muted small">Apunte la cámara hacia el código QR del estudiante</p>
                            </div>
                        @else
                            <div class="row justify-content-center">
                                <div class="col-md-8">
                                    <div class="mb-3">
                                        <label class="form-label fw-medium">Código del Estudiante</label>
                                        <div class="input-group input-group-lg">
                                            <span class="input-group-text">
                                                <i class="ri ri-hashtag"></i>
                                            </span>
                                            <input type="text" wire:model="manualCode" class="form-control" placeholder="Ingrese el código del estudiante" wire:keydown.enter="searchByManualCode" autofocus>
                                            <button wire:click="searchByManualCode" class="btn btn-primary" type="button">
                                                <i class="ri ri-search-line me-1"></i> Registrar
                                            </button>
                                        </div>
                                        <div class="form-text">Presione Enter o haga clic en Registrar para procesar</div>
                                    </div>
                                </div>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
        </div>
        
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Estado del Sistema</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="avatar me-3">
                            <span class="avatar-initial rounded bg-label-success">
                                <i class="ri ri-wifi-line ri-22px"></i>
                            </span>
                        </div>
                        <div>
                            <h6 class="mb-0">Sistema Activo</h6>
                            <small class="text-body">Listo para registrar accesos</small>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-body">Sonido:</span>
                        <span class="badge bg-label-{{ $soundEnabled ? 'success' : 'secondary' }}">{{ $soundEnabled ? 'Activado' : 'Desactivado' }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <span class="text-body">Modo:</span>
                        <span class="badge bg-label-primary">{{ $scanMode === 'camera' ? 'Cámara' : 'Manual' }}</span>
                    </div>
                    
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="text-body">Última actualización:</span>
                        <span class="badge bg-label-info">{{ now()->format('H:i:s') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Historial de Accesos -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">Registros de Hoy</h5>
                        <small class="text-body">Últimos {{ count($todayLogs) }} movimientos registrados</small>
                    </div>
                    <button wire:click="loadTodayLogs" class="btn btn-sm btn-outline-primary">
                        <i class="ri ri-refresh-line me-1"></i> Actualizar
                    </button>
                </div>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Hora</th>
                                <th>Estudiante</th>
                                <th>Código</th>
                                <th>Tipo</th>
                                <th>Registrado Por</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($todayLogs as $log)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-xs me-2">
                                                <span class="avatar-initial rounded bg-label-{{ $log['type'] === 'entrada' ? 'success' : 'danger' }}">
                                                    <i class="ri ri-{{ $log['type'] === 'entrada' ? 'login' : 'logout' }}-box-line ri-12px"></i>
                                                </span>
                                            </div>
                                            <div>
                                                <h6 class="mb-0">{{ \Carbon\Carbon::parse($log['access_time'])->format('H:i:s') }}</h6>
                                                <small class="text-body">{{ \Carbon\Carbon::parse($log['access_time'])->diffForHumans() }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        @if(isset($log['student']))
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm me-2">
                                                    <span class="avatar-initial rounded bg-label-primary">
                                                        {{ substr($log['student']['nombres'], 0, 1) }}{{ substr($log['student']['apellidos'], 0, 1) }}
                                                    </span>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0">{{ $log['student']['nombres'] }} {{ $log['student']['apellidos'] }}</h6>
                                                    <small class="text-body">Estudiante</small>
                                                </div>
                                            </div>
                                        @else
                                            <span class="text-body">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($log['student']))
                                            <span class="badge bg-label-primary">{{ $log['student']['codigo'] }}</span>
                                        @else
                                            <span class="text-body">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($log['type'] === 'entrada')
                                            <span class="badge bg-label-success">
                                                <i class="ri ri-login-box-line me-1"></i> Entrada
                                            </span>
                                        @else
                                            <span class="badge bg-label-danger">
                                                <i class="ri ri-logout-box-line me-1"></i> Salida
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($log['registered_by_user']))
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-xs me-2">
                                                    <span class="avatar-initial rounded bg-label-secondary">
                                                        <i class="ri ri-user-line ri-12px"></i>
                                                    </span>
                                                </div>
                                                <span>{{ $log['registered_by_user']['name'] }}</span>
                                            </div>
                                        @else
                                            <span class="text-body">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        <button wire:click="deleteLog({{ $log['id'] }})" class="btn btn-sm btn-icon btn-text-danger" onclick="return confirm('¿Eliminar este registro?')">
                                            <i class="ri ri-delete-bin-line"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center">
                                            <i class="ri ri-history-line ri-48px text-body mb-2"></i>
                                            <h6 class="text-body">No hay registros de acceso hoy</h6>
                                            <small class="text-body">Los accesos aparecerán aquí automáticamente</small>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @assets
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    @endassets

    @script
    <script>
        let html5QrCode = null;
        let scannerActive = false;

        function initQrScanner() {
            const readerElement = document.getElementById('qr-reader');
            if (!readerElement || $wire.scanMode !== 'camera') return;

            if (scannerActive && html5QrCode) {
                html5QrCode.stop().then(() => startScanner()).catch(() => startScanner());
            } else {
                startScanner();
            }
        }

        function startScanner() {
            const readerElement = document.getElementById('qr-reader');
            if (!readerElement) return;

            html5QrCode = new Html5Qrcode('qr-reader');

            Html5Qrcode.getCameras().then(cameras => {
                if (cameras && cameras.length) {
                    html5QrCode.start(
                        { facingMode: 'environment' },
                        { fps: 10, qrbox: { width: 250, height: 250 } },
                        (decodedText) => {
                            $wire.call('processQrScan', decodedText);
                        }
                    ).then(() => {
                        scannerActive = true;
                    }).catch((err) => {
                        console.error('Error al iniciar escáner:', err);
                        showNotification('No se pudo acceder a la cámara. Use el modo manual.', 'error');
                    });
                } else {
                    showNotification('No se encontró ninguna cámara. Use el modo manual.', 'error');
                }
            }).catch(() => {
                showNotification('Error al detectar cámaras. Use el modo manual.', 'error');
            });
        }

        function showNotification(message, type) {
            const bgColor = type === 'success' ? 'bg-success' : 'bg-danger';
            const icon = type === 'success' ? 'ri-check-line' : 'ri-error-warning-line';

            const toast = `
                <div class="bs-toast toast toast-placement-ex m-2 ${bgColor} top-0 end-0 fade show" role="alert">
                    <div class="toast-header">
                        <i class="${icon} me-2"></i>
                        <div class="me-auto fw-medium">${type === 'success' ? 'Éxito' : 'Error'}</div>
                        <button type="button" class="btn-close" data-bs-dismiss="toast"></button>
                    </div>
                    <div class="toast-body">${message}</div>
                </div>
            `;

            document.body.insertAdjacentHTML('beforeend', toast);
            setTimeout(() => document.querySelector('.toast')?.remove(), 3000);
        }

        function playSound(type) {
            new Audio(`/sounds/${type}.mp3`).play().catch(() => {});
        }

        $wire.on('show-success', (event) => showNotification(event[0], 'success'));
        $wire.on('show-error', (event) => showNotification(event[0], 'error'));
        $wire.on('play-sound', (event) => playSound(event[0]));

        Livewire.hook('morph.updated', () => {
            if ($wire.scanMode === 'camera') {
                setTimeout(initQrScanner, 100);
            } else if (html5QrCode && scannerActive) {
                html5QrCode.stop().then(() => scannerActive = false).catch(() => {});
            }
        });

        setTimeout(initQrScanner, 500);
    </script>
    @endscript
</div>
