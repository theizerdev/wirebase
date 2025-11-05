/**
 * App Calendar - Reuniones
 * Integración personalizada con FullCalendar y Livewire
 */

'use strict';

// Función para inicializar el calendario de reuniones
function initReunionesCalendar(events) {
    document.addEventListener('DOMContentLoaded', function () {
        const direction = 'ltr';

        // DOM Elements
        const calendarEl = document.getElementById('calendar');
        const appCalendarSidebar = document.querySelector('.app-calendar-sidebar');
        const addEventSidebar = document.getElementById('addEventSidebar');
        const appOverlay = document.querySelector('.app-overlay');
        const offcanvasTitle = document.querySelector('.offcanvas-title');
        const btnToggleSidebar = document.querySelector('.btn-toggle-sidebar');
        const btnSubmit = document.getElementById('addEventBtn');
        const btnDeleteEvent = document.querySelector('.btn-delete-event');
        const btnCancel = document.querySelector('.btn-cancel');
        const eventTitle = document.getElementById('eventTitle');
        const eventStartDate = document.getElementById('eventStartDate');
        const eventEndDate = document.getElementById('eventEndDate');
        const eventUrl = document.getElementById('eventURL');
        const eventLocation = document.getElementById('eventLocation');
        const eventDescription = document.getElementById('eventDescription');
        const allDaySwitch = document.querySelector('.allDay-switch');
        const selectAll = document.querySelector('.select-all');
        const filterInputs = Array.from(document.querySelectorAll('.input-filter'));
        const inlineCalendar = document.querySelector('.inline-calendar');

        // Calendar settings
        const calendarColors = {
            Business: 'primary',
            Holiday: 'success',
            Personal: 'danger',
            Family: 'warning',
            ETC: 'info'
        };

        // Variables
        let currentEvents = events || [];
        let isFormValid = false;
        let eventToUpdate = null;
        let inlineCalInstance = null;

        // Offcanvas Instance
        const bsAddEventSidebar = new bootstrap.Offcanvas(addEventSidebar);

        // Initialize Select2
        const eventLabel = $('#eventLabel');
        const eventGuests = $('#eventGuests');

        if (eventLabel.length) {
            function renderBadges(option) {
                if (!option.id) return option.text;
                return `<span class='badge badge-dot bg-${$(option.element).data('label')} me-2'></span>${option.text}`;
            }
            
            eventLabel.select2({
                placeholder: 'Seleccionar valor',
                dropdownParent: eventLabel.parent(),
                templateResult: renderBadges,
                templateSelection: renderBadges,
                minimumResultsForSearch: -1,
                escapeMarkup: function (es) { return es; }
            });
        }

        if (eventGuests.length) {
            function renderGuestAvatar(option) {
                if (!option.id) return option.text;
                return `
                    <div class='d-flex flex-wrap align-items-center'>
                        <div class='avatar avatar-xs me-2'>
                            <img src='/materialize/assets/img/avatars/1.png' alt='avatar' class='rounded-circle' />
                        </div>
                        ${option.text}
                    </div>`;
            }
            
            eventGuests.select2({
                placeholder: 'Seleccionar valor',
                dropdownParent: eventGuests.parent(),
                closeOnSelect: false,
                templateResult: renderGuestAvatar,
                templateSelection: renderGuestAvatar,
                escapeMarkup: function (es) { return es; }
            });
        }

        // Initialize Flatpickr
        let start, end;
        if (eventStartDate) {
            start = eventStartDate.flatpickr({
                monthSelectorType: 'static',
                static: true,
                enableTime: true,
                altFormat: 'Y-m-dTH:i:S',
                onReady: function (selectedDates, dateStr, instance) {
                    if (instance.isMobile) {
                        instance.mobileInput.setAttribute('step', null);
                    }
                }
            });
        }

        if (eventEndDate) {
            end = eventEndDate.flatpickr({
                monthSelectorType: 'static',
                static: true,
                enableTime: true,
                altFormat: 'Y-m-dTH:i:S',
                onReady: function (selectedDates, dateStr, instance) {
                    if (instance.isMobile) {
                        instance.mobileInput.setAttribute('step', null);
                    }
                }
            });
        }

        // Inline calendar
        if (inlineCalendar) {
            inlineCalInstance = inlineCalendar.flatpickr({
                monthSelectorType: 'static',
                static: true,
                inline: true
            });
        }

        // Helper functions
        function modifyToggler() {
            const fcSidebarToggleButton = document.querySelector('.fc-sidebarToggle-button');
            const fcPrevButton = document.querySelector('.fc-prev-button');
            const fcNextButton = document.querySelector('.fc-next-button');
            const fcHeaderToolbar = document.querySelector('.fc-header-toolbar');
            
            if (fcPrevButton) fcPrevButton.classList.add('btn', 'btn-sm', 'btn-icon', 'btn-outline-secondary', 'me-2');
            if (fcNextButton) fcNextButton.classList.add('btn', 'btn-sm', 'btn-icon', 'btn-outline-secondary', 'me-4');
            if (fcHeaderToolbar) fcHeaderToolbar.classList.add('row-gap-4', 'gap-2');
            
            if (fcSidebarToggleButton) {
                fcSidebarToggleButton.classList.remove('fc-button-primary');
                fcSidebarToggleButton.classList.add('d-lg-none', 'd-inline-block', 'ps-0');
                while (fcSidebarToggleButton.firstChild) {
                    fcSidebarToggleButton.firstChild.remove();
                }
                fcSidebarToggleButton.setAttribute('data-bs-toggle', 'sidebar');
                fcSidebarToggleButton.setAttribute('data-overlay', '');
                fcSidebarToggleButton.setAttribute('data-target', '#app-calendar-sidebar');
                fcSidebarToggleButton.insertAdjacentHTML(
                    'beforeend',
                    '<i class="icon-base ri ri-menu-line icon-24px text-body"></i>'
                );
            }
        }

        function selectedCalendars() {
            let selected = [];
            const filterInputChecked = document.querySelectorAll('.input-filter:checked');
            filterInputChecked.forEach(item => {
                selected.push(item.getAttribute('data-value'));
            });
            return selected;
        }

        function fetchEvents(info, successCallback) {
            let calendars = selectedCalendars();
            let selectedEvents = currentEvents.filter(function (event) {
                return calendars.includes(event.extendedProps.calendar.toLowerCase());
            });
            successCallback(selectedEvents);
        }

        function resetValues() {
            if (eventEndDate) eventEndDate.value = '';
            if (eventUrl) eventUrl.value = '';
            if (eventStartDate) eventStartDate.value = '';
            if (eventTitle) eventTitle.value = '';
            if (eventLocation) eventLocation.value = '';
            if (allDaySwitch) allDaySwitch.checked = false;
            if (eventGuests) eventGuests.val('').trigger('change');
            if (eventDescription) eventDescription.value = '';
        }

        function eventClick(info) {
            eventToUpdate = info.event;
            if (eventToUpdate.url) {
                info.jsEvent.preventDefault();
                window.open(eventToUpdate.url, '_blank');
            }
            
            bsAddEventSidebar.show();
            
            if (offcanvasTitle) offcanvasTitle.innerHTML = 'Actualizar Evento';
            if (btnSubmit) {
                btnSubmit.innerHTML = 'Actualizar';
                btnSubmit.classList.add('btn-update-event');
                btnSubmit.classList.remove('btn-add-event');
            }
            if (btnDeleteEvent) btnDeleteEvent.classList.remove('d-none');

            if (eventTitle) eventTitle.value = eventToUpdate.title;
            if (start) start.setDate(eventToUpdate.start, true, 'Y-m-d');
            if (allDaySwitch) allDaySwitch.checked = eventToUpdate.allDay === true;
            if (end) {
                eventToUpdate.end !== null
                    ? end.setDate(eventToUpdate.end, true, 'Y-m-d')
                    : end.setDate(eventToUpdate.start, true, 'Y-m-d');
            }
            if (eventLabel) eventLabel.val(eventToUpdate.extendedProps.calendar).trigger('change');
            if (eventLocation && eventToUpdate.extendedProps.location) {
                eventLocation.value = eventToUpdate.extendedProps.location;
            }
            if (eventGuests && eventToUpdate.extendedProps.guests) {
                eventGuests.val(eventToUpdate.extendedProps.guests).trigger('change');
            }
            if (eventDescription && eventToUpdate.extendedProps.description) {
                eventDescription.value = eventToUpdate.extendedProps.description;
            }
        }

        // Initialize FullCalendar
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            events: fetchEvents,
            editable: true,
            dragScroll: true,
            dayMaxEvents: 2,
            eventResizableFromStart: true,
            customButtons: {
                sidebarToggle: { text: 'Sidebar' }
            },
            headerToolbar: {
                start: 'sidebarToggle, prev,next, title',
                end: 'dayGridMonth,timeGridWeek,timeGridDay,listMonth'
            },
            direction: direction,
            initialDate: new Date(),
            navLinks: true,
            eventClassNames: function ({ event: calendarEvent }) {
                const colorName = calendarColors[calendarEvent._def.extendedProps.calendar];
                return ['bg-label-' + colorName];
            },
            dateClick: function (info) {
                let date = moment(info.date).format('YYYY-MM-DD');
                resetValues();
                bsAddEventSidebar.show();

                if (offcanvasTitle) offcanvasTitle.innerHTML = 'Agregar Evento';
                if (btnSubmit) {
                    btnSubmit.innerHTML = 'Agregar';
                    btnSubmit.classList.remove('btn-update-event');
                    btnSubmit.classList.add('btn-add-event');
                }
                if (btnDeleteEvent) btnDeleteEvent.classList.add('d-none');
                if (eventStartDate) eventStartDate.value = date;
                if (eventEndDate) eventEndDate.value = date;
            },
            eventClick: eventClick,
            datesSet: modifyToggler,
            viewDidMount: modifyToggler
        });

        // Render calendar
        calendar.render();
        modifyToggler();

        // Form validation
        const eventForm = document.getElementById('eventForm');
        if (eventForm && typeof FormValidation !== 'undefined') {
            const fv = FormValidation.formValidation(eventForm, {
                fields: {
                    eventTitle: {
                        validators: {
                            notEmpty: { message: 'Por favor ingrese el título del evento' }
                        }
                    },
                    eventStartDate: {
                        validators: {
                            notEmpty: { message: 'Por favor ingrese la fecha de inicio' }
                        }
                    },
                    eventEndDate: {
                        validators: {
                            notEmpty: { message: 'Por favor ingrese la fecha de fin' }
                        }
                    }
                },
                plugins: {
                    trigger: new FormValidation.plugins.Trigger(),
                    bootstrap5: new FormValidation.plugins.Bootstrap5({
                        eleValidClass: '',
                        rowSelector: function (field, ele) {
                            return '.form-control-validation';
                        }
                    }),
                    submitButton: new FormValidation.plugins.SubmitButton(),
                    autoFocus: new FormValidation.plugins.AutoFocus()
                }
            })
            .on('core.form.valid', function () { isFormValid = true; })
            .on('core.form.invalid', function () { isFormValid = false; });
        }

        // Event handlers
        if (btnToggleSidebar) {
            btnToggleSidebar.addEventListener('click', e => {
                if (btnCancel) btnCancel.classList.remove('d-none');
            });
        }

        if (btnSubmit) {
            btnSubmit.addEventListener('click', e => {
                if (btnSubmit.classList.contains('btn-add-event')) {
                    if (isFormValid) {
                        let newEvent = {
                            title: eventTitle.value,
                            start: eventStartDate.value,
                            end: eventEndDate.value,
                            extendedProps: {
                                location: eventLocation ? eventLocation.value : '',
                                guests: eventGuests ? eventGuests.val() : [],
                                calendar: eventLabel ? eventLabel.val() : 'Business',
                                description: eventDescription ? eventDescription.value : ''
                            }
                        };
                        
                        if (eventUrl && eventUrl.value) newEvent.url = eventUrl.value;
                        if (allDaySwitch && allDaySwitch.checked) newEvent.allDay = true;
                        
                        // Llamar a Livewire
                        if (window.Livewire) {
                            window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).call('saveEvent', newEvent);
                        }
                        bsAddEventSidebar.hide();
                    }
                } else {
                    // Update event
                    if (isFormValid) {
                        let eventData = {
                            id: eventToUpdate.id,
                            title: eventTitle.value,
                            start: eventStartDate.value,
                            end: eventEndDate.value,
                            url: eventUrl ? eventUrl.value : '',
                            extendedProps: {
                                location: eventLocation ? eventLocation.value : '',
                                guests: eventGuests ? eventGuests.val() : [],
                                calendar: eventLabel ? eventLabel.val() : 'Business',
                                description: eventDescription ? eventDescription.value : ''
                            },
                            allDay: allDaySwitch ? allDaySwitch.checked : false
                        };

                        // Llamar a Livewire
                        if (window.Livewire) {
                            window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).call('updateEvent', eventData);
                        }
                        bsAddEventSidebar.hide();
                    }
                }
            });
        }

        if (btnDeleteEvent) {
            btnDeleteEvent.addEventListener('click', e => {
                if (eventToUpdate && window.Livewire) {
                    window.Livewire.find(document.querySelector('[wire\\:id]').getAttribute('wire:id')).call('deleteEvent', parseInt(eventToUpdate.id));
                }
                bsAddEventSidebar.hide();
            });
        }

        // Reset form when modal hides
        if (addEventSidebar) {
            addEventSidebar.addEventListener('hidden.bs.offcanvas', resetValues);
        }

        // Sidebar toggle
        if (btnToggleSidebar) {
            btnToggleSidebar.addEventListener('click', e => {
                if (offcanvasTitle) offcanvasTitle.innerHTML = 'Agregar Evento';
                if (btnSubmit) {
                    btnSubmit.innerHTML = 'Agregar';
                    btnSubmit.classList.remove('btn-update-event');
                    btnSubmit.classList.add('btn-add-event');
                }
                if (btnDeleteEvent) btnDeleteEvent.classList.add('d-none');
                if (appCalendarSidebar) appCalendarSidebar.classList.remove('show');
                if (appOverlay) appOverlay.classList.remove('show');
            });
        }

        // Filter functionality
        if (selectAll) {
            selectAll.addEventListener('click', e => {
                const checked = e.currentTarget.checked;
                document.querySelectorAll('.input-filter').forEach(c => c.checked = checked);
                calendar.refetchEvents();
            });
        }

        if (filterInputs) {
            filterInputs.forEach(item => {
                item.addEventListener('click', () => {
                    const checkedCount = document.querySelectorAll('.input-filter:checked').length;
                    const totalCount = document.querySelectorAll('.input-filter').length;
                    if (selectAll) selectAll.checked = checkedCount === totalCount;
                    calendar.refetchEvents();
                });
            });
        }

        // Inline calendar change
        if (inlineCalInstance) {
            inlineCalInstance.config.onChange.push(function (date) {
                calendar.changeView(calendar.view.type, moment(date[0]).format('YYYY-MM-DD'));
                modifyToggler();
                if (appCalendarSidebar) appCalendarSidebar.classList.remove('show');
                if (appOverlay) appOverlay.classList.remove('show');
            });
        }

        // Livewire event listeners
        window.addEventListener('reunion-saved', () => location.reload());
        window.addEventListener('reunion-deleted', () => location.reload());
    });
}