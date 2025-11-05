<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card app-calendar-wrapper">
        <div class="row g-0">
            <!-- Calendar Sidebar -->
            <div class="col app-calendar-sidebar border-end" id="app-calendar-sidebar">
                <div class="border-bottom my-sm-0 mb-4 p-5">
                    <button
                        class="btn btn-primary btn-toggle-sidebar w-100"
                        data-bs-toggle="offcanvas"
                        data-bs-target="#addEventSidebar"
                        aria-controls="addEventSidebar">
                        <i class="icon-base ri ri-add-line icon-16px me-1_5"></i>
                        <span class="align-middle">Agregar Evento</span>
                    </button>
                </div>
                <div class="px-4">
                    <!-- inline calendar (flatpicker) -->
                    <div class="inline-calendar"></div>

                    <hr class="mb-5 mx-n4 mt-3" />
                    <!-- Filter -->
                    <div class="mb-4 ms-1">
                        <h5>Filtros de Eventos</h5>
                    </div>

                    <div class="form-check form-check-secondary mb-5 ms-3">
                        <input
                            class="form-check-input select-all"
                            type="checkbox"
                            id="selectAll"
                            data-value="all"
                            checked />
                        <label class="form-check-label" for="selectAll">Ver Todos</label>
                    </div>

                    <div class="app-calendar-events-filter text-heading">
                        <div class="form-check form-check-danger mb-5 ms-3">
                            <input
                                class="form-check-input input-filter"
                                type="checkbox"
                                id="select-personal"
                                data-value="personal"
                                checked />
                            <label class="form-check-label" for="select-personal">Personal</label>
                        </div>
                        <div class="form-check mb-5 ms-3">
                            <input
                                class="form-check-input input-filter"
                                type="checkbox"
                                id="select-business"
                                data-value="business"
                                checked />
                            <label class="form-check-label" for="select-business">Trabajo</label>
                        </div>
                        <div class="form-check form-check-warning mb-5 ms-3">
                            <input
                                class="form-check-input input-filter"
                                type="checkbox"
                                id="select-family"
                                data-value="family"
                                checked />
                            <label class="form-check-label" for="select-family">Familia</label>
                        </div>
                        <div class="form-check form-check-success mb-5 ms-3">
                            <input
                                class="form-check-input input-filter"
                                type="checkbox"
                                id="select-holiday"
                                data-value="holiday"
                                checked />
                            <label class="form-check-label" for="select-holiday">Feriados</label>
                        </div>
                        <div class="form-check form-check-info ms-3">
                            <input
                                class="form-check-input input-filter"
                                type="checkbox"
                                id="select-etc"
                                data-value="etc"
                                checked />
                            <label class="form-check-label" for="select-etc">Otros</label>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Calendar Sidebar -->

            <!-- Calendar & Modal -->
            <div class="col app-calendar-content">
                <div class="card shadow-none border-0">
                    <div class="card-body pb-0">
                        <!-- FullCalendar -->
                        <div id="calendar"></div>
                    </div>
                </div>
                <div class="app-overlay"></div>
                <!-- FullCalendar Offcanvas -->
                <div
                    class="offcanvas offcanvas-end event-sidebar"
                    tabindex="-1"
                    id="addEventSidebar"
                    aria-labelledby="addEventSidebarLabel">
                    <div class="offcanvas-header border-bottom">
                        <h5 class="offcanvas-title" id="addEventSidebarLabel">Agregar Evento</h5>
                        <button
                            type="button"
                            class="btn-close text-reset"
                            data-bs-dismiss="offcanvas"
                            aria-label="Close"></button>
                    </div>
                    <div class="offcanvas-body">
                        <form class="event-form pt-0" wire:submit="save">
                            <div class="mb-3">
                                <label class="form-label" for="titulo">Título</label>
                                <input type="text" class="form-control" id="titulo" wire:model="titulo" placeholder="Título de la reunión" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="descripcion">Descripción</label>
                                <textarea class="form-control" id="descripcion" wire:model="descripcion" rows="3" placeholder="Descripción de la reunión"></textarea>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="fecha_inicio">Fecha de Inicio</label>
                                <input type="datetime-local" class="form-control" id="fecha_inicio" wire:model="fecha_inicio" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="fecha_fin">Fecha de Fin</label>
                                <input type="datetime-local" class="form-control" id="fecha_fin" wire:model="fecha_fin" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="ubicacion">Ubicación</label>
                                <input type="text" class="form-control" id="ubicacion" wire:model="ubicacion" placeholder="Ubicación de la reunión">
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="color">Color</label>
                                <select class="form-select" id="color" wire:model="color">
                                    <option value="#696cff">Trabajo (Azul)</option>
                                    <option value="#ff3e1d">Personal (Rojo)</option>
                                    <option value="#ffab00">Familia (Amarillo)</option>
                                    <option value="#71dd37">Feriado (Verde)</option>
                                    <option value="#03c3ec">Otros (Cian)</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label" for="participantes">Participantes</label>
                                <select class="form-select" id="participantes" wire:model="participantes" multiple>
                                    @foreach($this->usuarios as $usuario)
                                        <option value="{{ $usuario->id }}">{{ $usuario->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="mb-3 d-flex justify-content-between">
                                <button type="submit" class="btn btn-primary">
                                    {{ $editingId ? 'Actualizar' : 'Crear' }} Reunión
                                </button>
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="offcanvas">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <!-- /Calendar & Modal -->
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="{{ asset('materialize/assets/vendor/libs/fullcalendar/fullcalendar.css') }}">
<link rel="stylesheet" href="{{ asset('materialize/assets/vendor/libs/flatpickr/flatpickr.css') }}">
<link rel="stylesheet" href="{{ asset('materialize/assets/vendor/libs/select2/select2.css') }}">
<link rel="stylesheet" href="{{ asset('materialize/assets/vendor/libs/@form-validation/form-validation.css') }}">
<link rel="stylesheet" href="{{ asset('materialize/assets/vendor/css/pages/app-calendar.css') }}">
    <link rel="stylesheet" href="/materialize/assets/vendor/css/pages/app-calendar.css" />
@endpush

@push('scripts')
<script src="/materialize/assets/vendor/libs/fullcalendar/fullcalendar.js"></script>
<script src="/materialize/assets/vendor/libs/@form-validation/popular.js"></script>
<script src="/materialize/assets/vendor/libs/@form-validation/bootstrap5.js"></script>
<script src="/materialize/assets/vendor/libs/@form-validation/auto-focus.js"></script>
<script src="/materialize/assets/vendor/libs/select2/select2.js"></script>
<script src="/materialize/assets/vendor/libs/moment/moment.js"></script>
<script src="/materialize/assets/vendor/libs/flatpickr/flatpickr.js"></script>

<script>
// Inicializar calendario con datos de Laravel
const reunionesEvents = {!! json_encode($this->reuniones->map(function($reunion) {
    $categoria = 'Business';
    if (strpos(strtolower($reunion->titulo), 'personal') !== false) {
        $categoria = 'Personal';
    } elseif (strpos(strtolower($reunion->titulo), 'familia') !== false) {
        $categoria = 'Family';
    } elseif (strpos(strtolower($reunion->titulo), 'feriado') !== false || strpos(strtolower($reunion->titulo), 'vacacion') !== false) {
        $categoria = 'Holiday';
    }

    return [
        'id' => $reunion->id,
        'title' => $reunion->titulo,
        'start' => $reunion->fecha_inicio->format('Y-m-d H:i:s'),
        'end' => $reunion->fecha_fin->format('Y-m-d H:i:s'),
        'allDay' => false,
        'extendedProps' => [
            'calendar' => $categoria,
            'location' => $reunion->ubicacion,
            'description' => $reunion->descripcion,
            'guests' => $reunion->participantes ?? []
        ]
    ];
})) !!};

// Inicializar datepickers y mostrar eventos
document.addEventListener('DOMContentLoaded', function () {
  // Mostrar eventos en el área del calendario
  const calendarEl = document.getElementById('calendar');
  if (calendarEl) {
    let html = '<div class="p-4"><h5>Reuniones Programadas</h5>';
    if (reunionesEvents.length > 0) {
      html += '<div class="row">';
      reunionesEvents.forEach(event => {
        html += `<div class="col-md-6 mb-3"><div class="card"><div class="card-body"><h6>${event.title}</h6><p class="text-muted mb-1">${new Date(event.start).toLocaleString()}</p><p class="mb-0">${event.extendedProps.location || 'Sin ubicación'}</p></div></div></div>`;
      });
      html += '</div>';
    } else {
      html += '<p class="text-muted">No hay reuniones programadas</p>';
    }
    html += '</div>';
    calendarEl.innerHTML = html;
  }
  
  // Inicializar Flatpickr
  if (typeof flatpickr !== 'undefined') {
    flatpickr('#eventStartDate', {
      enableTime: true,
      dateFormat: 'Y-m-d H:i'
    });
    flatpickr('#eventEndDate', {
      enableTime: true,
      dateFormat: 'Y-m-d H:i'
    });
    flatpickr('.inline-calendar', {
      inline: true
    });
  }
});
</script>
@endpush
