<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Care Worker Appointments | Manager</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon-white-16x16.png')}}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/careWorkerAppointment.css') }}">
    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/timegrid/main.min.js"></script>
</head>
<body>

    @php
    use App\Helpers\TranslationHelper as T;
    @endphp
    @include('components.careManagerNavbar')
    @include('components.careManagerSidebar')

    <div class="home-section">
        <div class="text-left">{{ T::translate('CARE WORKER SCHEDULING', 'PAG-IISKEDYUL NG CARE WORKER')}}</div>
        <div class="container-fluid">
            <div class="row p-3" id="home-content">
                <!-- Main content area -->
                <div class="col-12 mb-4">
                    <div class="row">
                        <!-- Calendar Column -->
                        <div class="col-lg-8 col-md-7 mb-4">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="section-heading">
                                        <i class="bi bi-calendar3"></i> {{ T::translate('Appointment Calendar', 'Kalendaryo ng Appointment') }}
                                    </h5>
                                    <div class="calendar-actions d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="toggleWeekView">
                                            <i class="bi bi-calendar-week"></i> {{ T::translate('Week View', 'Lingguhang Tingnan') }}
                                        </button>
                                        <!-- Care worker filter with improved styling -->
                                        <div class="filter-wrapper">
                                            <select id="careWorkerFilter" class="form-select form-select-sm" aria-label="Filter by care worker">
                                                <option value="">{{ T::translate('All Care Workers', 'Lahat ng Tagapag-alaga')}}</option>
                                                @foreach($careWorkers as $worker)
                                                    <option value="{{ $worker->id }}">{{ $worker->first_name }} {{ $worker->last_name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div id="calendar-container">
                                        <!-- Loading spinner -->
                                        <div id="calendar-spinner" class="position-absolute w-100 h-100" style="display: none; z-index: 10; background: rgba(255,255,255,0.8);">
                                            <div class="d-flex justify-content-center align-items-center h-100">
                                                <div class="text-center">
                                                    <div class="spinner-border text-primary" role="status">
                                                        <span class="visually-hidden">Loading...</span>
                                                    </div>
                                                    <p class="mt-2 spinner-message">{{ T::translate('Loading appointments...', 'Naglo-load ng mga appointment...')}}</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div id="calendar" class="p-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Details Column -->
                        <div class="col-lg-4 col-md-5">
                            <!-- Search Bar -->
                            <div class="search-container">
                                <input type="text" id="searchInput" class="form-control search-input" placeholder="     {{ T::translate('Search appointments...', 'Maghanap ng Appointments...')}}" aria-label="Search appointments">
                                <i class="bi bi-search"></i>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="action-buttons mb-4">
                                <button type="button" class="btn btn-primary action-btn" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
                                    <i class="bi bi-plus-circle"></i> {{ T::translate('Schedule New Appointment', 'Mag-skedyul ng Bagong Appointment')}}
                                </button>
                                <button type="button" class="btn btn-outline-warning action-btn" id="editAppointmentButton" disabled>
                                    <i class="bi bi-pencil-square"></i> {{ T::translate('Edit Selected Appointment', 'I-Edit ang Napiling Appointment')}}
                                </button>
                                <button type="button" class="btn btn-outline-danger action-btn" id="deleteAppointmentButton" disabled>
                                    <i class="bi bi-trash3"></i> {{ T::translate('Cancel Selected Appointment', 'Kanselahin ang Napiling Appointment')}}
                                </button>
                            </div>
                            
                            <!-- Appointment Details Panel -->
                            <div class="card details-container">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="section-heading mb-0">
                                        <i class="bi bi-info-circle"></i> {{ T::translate('Appointment Details', 'Detalye ng Appointment')}}
                                    </h5>
                                </div>
                                <div class="card-body" id="appointmentDetails">
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-calendar-event" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                        <p class="mt-3 mb-0">{{ T::translate('Select an appointment to view details', 'Pumili ng appointment para makita ang detalye')}}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    <!-- Add Appointment Modal -->
    <div class="modal fade" id="addAppointmentModal" tabindex="-1" aria-labelledby="addAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAppointmentModalLabel">
                        <i class="bi bi-calendar-plus"></i> {{ T::translate('Schedule New Appointment', 'Mag-iskedyul ng Bagong Appointment')}}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modalErrors" class="alert alert-danger d-none mb-3">
                        <ul id="errorList" class="mb-0"></ul>
                    </div>

                    <div id="recurringWarningMessage" class="alert alert-warning mb-3" style="display: none;">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>{{ T::translate('Note:', 'Paalala:')}}</strong> {{ T::translate('Editing a recurring appointment will only affect this and future occurrences. Past occurrences will remain unchanged.', 
                            'Ang pag-edit ng umuulit na appointment ay makakaapekto lamang dito at sa mga mangyayari sa hinaharap. Ang mga nakaraang pangyayari ay mananatiling hindi magbabago.')}}
                    </div>
                    
                    <form id="addAppointmentForm">
                        @csrf
                        <input type="hidden" id="visitationId" name="visitation_id">
                        <input type="hidden" id="original_care_worker_id" name="original_care_worker_id" value="">
                        <input type="hidden" id="edited_occurrence_date" name="edited_occurrence_date" value="">
                        
                        <!-- Care Worker Selection -->
                        <div class="form-group">
                            <label for="careWorkerSelect">
                                <i class="bi bi-person-badge"></i> {{ T::translate('Care Worker', 'Tagapag-alaga')}}
                            </label>
                            <div class="select-container">
                                <select class="form-control" id="careWorkerSelect" name="care_worker_id" required>
                                    <option value="">{{ T::translate('Select Care Worker', 'Pumili ng Tagapag-alaga')}}</option>
                                    @foreach($careWorkers as $worker)
                                        <option value="{{ $worker->id }}">{{ $worker->first_name }} {{ $worker->last_name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="error-feedback" id="care-worker-error"></div>
                        </div>
                        
                        <!-- Beneficiary Selection -->
                        <div class="form-group">
                            <label for="beneficiarySelect">
                                <i class="bi bi-person-heart"></i> {{ T::translate('Beneficiary', 'Benepisyaryo')}}
                            </label>
                            <div class="select-container">
                                <select class="form-control" id="beneficiarySelect" name="beneficiary_id" required>
                                    <option value="">{{ T::translate('Select Beneficiary', 'Pumili ng Benepisyaryo')}}</option>
                                </select>
                            </div>
                            <div class="error-feedback" id="beneficiary-error"></div>
                        </div>
                        
                        <!-- Beneficiary Details (Auto-filled) -->
                        <div class="form-group">
                            <label for="beneficiaryAddress">
                                <i class="bi bi-geo-alt"></i> {{ T::translate('Address', 'Address')}}
                            </label>
                            <input type="text" class="form-control" id="beneficiaryAddress" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="beneficiaryPhone">
                                <i class="bi bi-telephone"></i> {{ T::translate('Phone', 'Telepono')}}
                            </label>
                            <input type="text" class="form-control" id="beneficiaryPhone" readonly>
                        </div>
                        
                        <!-- Visit Date -->
                        <div class="form-group">
                            <label for="visitDate">
                                <i class="bi bi-calendar-date"></i> {{ T::translate('Visit Date', 'Petsa ng Pagbisita')}}
                            </label>
                            <input type="date" class="form-control" id="visitDate" name="visitation_date" required>
                            <div class="error-feedback" id="visitation-date-error"></div>
                        </div>
                        
                        <!-- Time Selection -->
                        <div class="form-group">
                            <label>
                                <i class="bi bi-clock"></i> {{ T::translate('Appointment Time', 'Oras ng Pagbisita')}}
                            </label>
                            <div class="row" id="timeSelectionContainer">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="startTime" class="form-label">{{ T::translate('Start Time', 'Oras ng Simula')}}</label>
                                        <input type="time" class="form-control" id="startTime" name="start_time">
                                        <div class="error-feedback" id="start-time-error"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="endTime" class="form-label">{{ T::translate('End Time', 'Oras ng Pagtapos')}}</label>
                                        <input type="time" class="form-control" id="endTime" name="end_time">
                                        <div class="error-feedback" id="end-time-error"></div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Open Time / Flexible Schedule Option -->
                            <div class="open-time-container">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="openTimeCheck" name="is_flexible_time">
                                    <label class="form-check-label" for="openTimeCheck">
                                        {{ T::translate('Open Time / Flexible Schedule (Care Worker will determine actual time)', 'Open Time / Flexible na Iskedyul (Tukuyin ng Care Worker ang aktwal na oras)')}}
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Visit Type -->
                        <div class="form-group">
                            <label for="visitType">
                                <i class="bi bi-clipboard2-pulse"></i> {{ T::translate('Visit Type', 'Uri ng Pagbisita')}}
                            </label>
                            <div class="select-container">
                                <select class="form-control" id="visitType" name="visit_type" required>
                                    <option value="">{{ T::translate('Select Visit Type', 'Pumili ng Uri')}}</option>
                                    <option value="routine_care_visit">{{ T::translate('Routine Care Visit', 'Regular na Pagbisita')}}</option>
                                    <option value="service_request">{{ T::translate('Service Request', 'Pakiusap na Serbisyo')}}</option>
                                    <option value="emergency_visit">{{ T::translate('Emergency Visit', 'Emergency na Pagbisita')}}</option>
                                </select>
                            </div>
                            <div class="error-feedback" id="visit-type-error"></div>
                        </div>
                        
                        <!-- Recurring Options -->
                        <div class="form-check mt-3 mb-3">
                            <input class="form-check-input" type="checkbox" id="recurringCheck" name="is_recurring">
                            <label class="form-check-label" for="recurringCheck">
                                {{ T::translate('Make this a recurring appointment', 'Gawin itong umuulit na appointment')}}
                            </label>
                        </div>
                        
                        <div id="recurringOptionsContainer" class="border rounded p-3 mb-3" style="display: none;">
                            <div class="form-group">
                                <label class="form-label">{{ T::translate('Recurrence Pattern', 'Pattern ng Pag-ulit')}}</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pattern_type" id="patternDaily" value="daily">
                                    <label class="form-check-label" for="patternDaily">{{ T::translate('Daily', 'Araw-araw')}}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pattern_type" id="patternWeekly" value="weekly" checked>
                                    <label class="form-check-label" for="patternWeekly">{{ T::translate('Weekly', 'Linguhan')}}</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pattern_type" id="patternMonthly" value="monthly">
                                    <label class="form-check-label" for="patternMonthly">{{ T::translate('Monthly', 'Buwanan')}}</label>
                                </div>
                            </div>
                            
                            <div id="weeklyOptions" class="mt-3">
                                <label class="form-label">{{ T::translate('Repeat on', 'Ulitin sa')}}</label>
                                <div class="day-checkboxes">
                                    <div class="day-checkbox">
                                        <input type="checkbox" id="daySun" name="day_of_week[]" value="0">
                                        <label for="daySun">{{ T::translate('Sun', 'Linggo')}}</label>
                                    </div>
                                    <div class="day-checkbox">
                                        <input type="checkbox" id="dayMon" name="day_of_week[]" value="1">
                                        <label for="dayMon">{{ T::translate('Mon', 'Lunes')}}</label>
                                    </div>
                                    <div class="day-checkbox">
                                        <input type="checkbox" id="dayTue" name="day_of_week[]" value="2">
                                        <label for="dayTue">{{ T::translate('Tue', 'Martes')}}</label>
                                    </div>
                                    <div class="day-checkbox">
                                        <input type="checkbox" id="dayWed" name="day_of_week[]" value="3">
                                        <label for="dayWed">{{ T::translate('Wed', 'Miyerkules')}}</label>
                                    </div>
                                    <div class="day-checkbox">
                                        <input type="checkbox" id="dayThu" name="day_of_week[]" value="4">
                                        <label for="dayThu">{{ T::translate('Thu', 'Huwebes')}}</label>
                                    </div>
                                    <div class="day-checkbox">
                                        <input type="checkbox" id="dayFri" name="day_of_week[]" value="5">
                                        <label for="dayFri">{{ T::translate('Fri', 'Biyernes')}}</label>
                                    </div>
                                    <div class="day-checkbox">
                                        <input type="checkbox" id="daySat" name="day_of_week[]" value="6">
                                        <label for="daySat">{{ T::translate('Sat', 'Sabado')}}</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mt-3">
                                <label for="recurrenceEnd" class="form-label">{{ T::translate('End Date', 'Petsa ng Pagtatapos')}}</label>
                                <input type="date" class="form-control" id="recurrenceEnd" name="recurrence_end">
                            </div>
                        </div>
                        
                        <!-- Notes -->
                        <div class="form-group mb-0">
                            <label for="notes">
                                <i class="bi bi-journal-text"></i> {{ T::translate('Visit Notes', 'Tala sa Pagbisita')}}
                            </label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="{{ T::translate('Enter any additional instructions or notes about this appointment...', 'Maglagay ng anumang karagdagang tagubilin o tala tungkol sa appointment na ito...')}}"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ T::translate('Cancel', 'Kanselahin')}}</button>
                    <button type="button" class="btn btn-primary modal-action-btn btn-schedule" id="submitAppointment">
                        <i class="bi bi-calendar-check"></i> {{ T::translate('Schedule Appointment', 'I-iskedyul ang Appointment')}}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancellation Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">
                        <i class="bi bi-trash-fill"></i> {{ T::translate('Cancel Appointment', 'Kanselahin ang Appointment')}}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="confirmationModalBody">
                    <!-- Modal content will be inserted here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ T::translate('Close', 'Isara')}}</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">{{ T::translate('Confirm Cancellation', 'Kumpirmahin ang Pagkansela')}}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade error-modal" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">{{ T::translate('Error', 'Error')}}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="errorMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ T::translate('Close', 'Isara')}}</button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>

    <!-- Add this line to include the timegrid plugin -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/timegrid/main.min.js"></script>
    <script>

        function showCalendarSpinner(message = '{{ T::translate('Loading appointments...', 'Naglo-load ng mga appointment...')}}') {
            const spinner = document.getElementById('calendar-spinner');
            const spinnerMessage = spinner.querySelector('.spinner-message');
            spinnerMessage.textContent = message;
            spinner.style.display = 'block';
        }

        function hideCalendarSpinner() {
            const spinner = document.getElementById('calendar-spinner');
            spinner.style.display = 'none';
        }

    document.addEventListener('DOMContentLoaded', function() {
        // Show spinner initially
        showCalendarSpinner('{{ T::translate('Loading appointments...', 'Naglo-load ng mga appointment...')}}');

        // Setup CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // DOM element references
        const calendarEl = document.getElementById('calendar');
        const appointmentDetailsEl = document.getElementById('appointmentDetails');
        const addAppointmentModal = new bootstrap.Modal(document.getElementById('addAppointmentModal'));
        const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
        const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        const editButton = document.getElementById('editAppointmentButton');
        const deleteButton = document.getElementById('deleteAppointmentButton');
        const toggleWeekButton = document.getElementById('toggleWeekView');
        const openTimeCheck = document.getElementById('openTimeCheck');
        const timeSelectionContainer = document.getElementById('timeSelectionContainer');
        const startTimeInput = document.getElementById('startTime');
        const endTimeInput = document.getElementById('endTime');
        const addAppointmentForm = document.getElementById('addAppointmentForm');
        const addAppointmentModalLabel = document.getElementById('addAppointmentModalLabel');
        const submitAppointment = document.getElementById('submitAppointment');
        const confirmationModalBody = document.getElementById('confirmationModalBody');
        let currentEvent = null;
        let currentView = 'dayGridMonth';
        let isEditing = false; // Add this variable to track editing state

        // Global variable to track selected care worker
        let currentCareWorkerId = '';

        // Store reference to the care worker filter dropdown
        const careWorkerFilter = document.getElementById('careWorkerFilter');

        // Add event listener to care worker filter dropdown
        if (careWorkerFilter) {
            careWorkerFilter.addEventListener('change', function() {
                // Store the selected care worker ID
                currentCareWorkerId = this.value;
                
                // Show spinner during filtering
                showCalendarSpinner('{{ T::translate('Filtering by care worker...', 'Pagsala ayon sa care worker...')}}');
                
                // Refresh calendar with new filter
                calendar.refetchEvents();
            });
        }

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Handle open time checkbox
        if (openTimeCheck) {
            openTimeCheck.addEventListener('change', function() {
                if (this.checked) {
                    // If checked, disable time inputs
                    startTimeInput.disabled = true;
                    endTimeInput.disabled = true;
                    startTimeInput.value = '';
                    endTimeInput.value = '';
                    timeSelectionContainer.style.opacity = '0.5';
                } else {
                    // If unchecked, enable time inputs
                    startTimeInput.disabled = false;
                    endTimeInput.disabled = false;
                    timeSelectionContainer.style.opacity = '1';
                }
            });
        }
        
        // Initialize recurring options
        const recurringCheck = document.getElementById('recurringCheck');
        if (recurringCheck) {
            recurringCheck.addEventListener('change', function() {
                document.getElementById('recurringOptionsContainer').style.display = this.checked ? 'block' : 'none';
            });
        }
        
        // Day selection based on appointment date
        const visitDate = document.getElementById('visitDate');
        if (visitDate) {
            visitDate.addEventListener('change', function() {
                if (recurringCheck && recurringCheck.checked && document.getElementById('patternWeekly').checked) {
                    const date = new Date(this.value);
                    const dayOfWeek = date.getDay();
                    const checkbox = document.querySelector(`input[name="day_of_week[]"][value="${dayOfWeek}"]`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                }
            });
        }
        
        // Pattern type change handling
        document.querySelectorAll('input[name="pattern_type"]').forEach(function(radio) {
            radio.addEventListener('change', function() {
                const weeklyOptions = document.getElementById('weeklyOptions');
                if (weeklyOptions) {
                    weeklyOptions.style.display = this.value === 'weekly' ? 'block' : 'none';
                }
            });
        });
        
        // Initialize calendar with dynamic events and lazy loading
        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: currentView,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            eventDisplay: 'block',
            
            // Performance optimization settings
            lazyFetching: true,
            progressiveEventRendering: true,
            initialDate: new Date(),
            dayMaxEvents: 300, // Use dayMaxEvents instead of maxEvents
            
            events: function(info, successCallback, failureCallback) {
                const start = info.startStr;
                const end = info.endStr;
                const searchTerm = document.getElementById('searchInput') ? document.getElementById('searchInput').value : '';
                
                // Show loading indicator
                if (document.getElementById('calendar-loading-indicator')) {
                    document.getElementById('calendar-loading-indicator').style.display = 'block';
                }
                
                // Set a timeout to prevent UI freezing if server is slow
                const timeoutId = setTimeout(() => {
                    if (document.getElementById('calendar-loading-indicator')) {
                        document.getElementById('calendar-loading-indicator').style.display = 'none';
                    }
                    failureCallback({ message: "Request timed out" });
                    showErrorModal('{{ T::translate('Loading took too long. Try viewing a smaller date range or reset the calendar.', 'Ang pag-load ay tumagal ng masyadong mahaba. Subukang tingnan ang mas maliit na saklaw ng petsa o i-reset ang kalendaryo.')}}');
                }, 15000); // 15 seconds timeout
                
                $.ajax({
                    url: '/care-manager/careworker-appointments/get-visitations',
                    method: 'GET',
                    data: {
                        start: start,
                        end: end,
                        search: searchTerm,
                        care_worker_id: currentCareWorkerId, // Add care worker filter parameter
                        view_type: info.view ? info.view.type : 'dayGridMonth' // Safe access with fallback
                    },
                    success: function(response) {
                        clearTimeout(timeoutId);
                        successCallback(response);
                        
                        // FIX: Safe access to view title with fallback
                        const viewName = info.view && info.view.title ? info.view.title : 'current view';
                        console.log(`Loaded ${response.length} events for ${viewName}`);
                    },
                    error: function(xhr) {
                        clearTimeout(timeoutId);
                        failureCallback(xhr);
                        showErrorModal('{{ T::translate('Failed to fetch appointments:', 'Nabigong kunin ang mga appointment:')}} ' + (xhr.responseJSON?.message || '{{ T::translate('Server error', 'Error sa server')}}'));
                        console.error('Error fetching events:', xhr.responseText);
                    },
                    complete: function() {
                        hideCalendarSpinner();
                    }
                });
            },
            
            // Memory management: Clear old events when changing dates
            datesSet: function(info) {
                console.log(`View changed: ${info.view.title} (${info.view.type})`);
                
                // Force garbage collection of old events
                const currentEvents = calendar.getEvents();
                if (currentEvents.length > 500) {
                    // When we have too many events, purge those far from current view
                    const viewStart = info.start;
                    const viewEnd = info.end;
                    const buffer = 30; // days buffer
                    
                    const bufferStart = new Date(viewStart);
                    bufferStart.setDate(bufferStart.getDate() - buffer);
                    
                    const bufferEnd = new Date(viewEnd);
                    bufferEnd.setDate(bufferEnd.getDate() + buffer);
                    
                    currentEvents.forEach(event => {
                        if (!event.start) return;
                        const eventDate = new Date(event.start);
                        if (eventDate < bufferStart || eventDate > bufferEnd) {
                            event.remove();
                        }
                    });
                    
                    console.log(`Removed far events, remaining: ${calendar.getEvents().length}`);
                }
            },
            
            // Event click handler
            eventClick: function(info) {
                showEventDetails(info.event);
                
                // Update current event reference
                currentEvent = info.event;
                
                // Enable action buttons
                if (editButton) editButton.disabled = false;
                if (deleteButton) deleteButton.disabled = false;
                
                // Only show action buttons for scheduled appointments
                if (info.event.extendedProps.status.toLowerCase() !== 'scheduled') {
                    if (editButton) editButton.disabled = true;
                    if (deleteButton) deleteButton.disabled = true;
                }
            },
            
            // Event content formatter
            eventContent: function(arg) {
                // Event content formatting logic
                const isFlexibleTime = arg.event.extendedProps.is_flexible_time;
                
                if (arg.view.type === 'dayGridMonth') {
                    // Month view formatting
                    let eventEl = document.createElement('div');
                    eventEl.className = 'fc-event-main';
                    
                    if (isFlexibleTime) {
                        eventEl.innerHTML = `
                            <div class="event-title">${arg.event.title}</div>
                            <div class="open-time-indicator"><i class="bi bi-clock"></i> {{ T::translate('Flexible Time', 'Flexible na Oras')}}</div>
                        `;
                    } else {
                        const startTime = arg.event.start ? new Date(arg.event.start).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '';
                        const endTime = arg.event.end ? new Date(arg.event.end).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '';
                        
                        eventEl.innerHTML = `
                            <div class="event-title">${arg.event.title}</div>
                            <div class="event-time">${startTime} - ${endTime}</div>
                        `;
                    }
                    return { domNodes: [eventEl] };
                } else {
                    // Week/day view formatting
                    let eventEl = document.createElement('div');
                    eventEl.className = 'fc-event-main';
                    
                    if (isFlexibleTime) {
                        eventEl.innerHTML = `
                            <div class="event-title">${arg.event.title}</div>
                            <div class="open-time-indicator"><i class="bi bi-clock"></i> {{ T::translate('Flexible Time', 'Flexible na Oras')}}</div>
                            <div class="event-details">${arg.event.extendedProps.visit_type}</div>
                        `;
                    } else {
                        eventEl.innerHTML = `
                            <div class="event-title">${arg.event.title}</div>
                            <div class="event-details">${arg.event.extendedProps.visit_type}</div>
                        `;
                    }
                    return { domNodes: [eventEl] };
                }
            },
            
            // Additional calendar settings for better performance
            eventTimeFormat: { 
                hour: '2-digit',
                minute: '2-digit',
                meridiem: false
            },
            dayMaxEvents: false, // Show all events, no matter how many
            firstDay: 0, // Start week on Sunday
            height: 'auto'
        });

        // Add a reset button to clear all events if needed
        const calendarActions = document.querySelector('.calendar-actions');
        if (calendarActions) {
            const resetButton = document.createElement('button');
            resetButton.type = 'button';
            resetButton.className = 'btn btn-sm btn-outline-secondary';
            resetButton.innerHTML = '<i class="bi bi-arrow-counterclockwise"></i> {{ T::translate('Reset', 'I-reset')}}';
            resetButton.addEventListener('click', function() {
                // Clear search input
                const searchInput = document.querySelector('.search-input');
                if (searchInput) {
                    searchInput.value = '';
                    currentSearchTerm = '';
                }
                
                // Clear care worker filter - ADD THIS
                if (careWorkerFilter) {
                    careWorkerFilter.selectedIndex = 0;
                    currentCareWorkerId = '';
                }
                
                // Show spinner during reset
                showCalendarSpinner('{{ T::translate('Resetting calendar...', 'Ine-reset ang kalendaryo...')}}');
                
                // Reset calendar view to month if not already
                if (currentView !== 'dayGridMonth') {
                    calendar.changeView('dayGridMonth');
                    toggleWeekButton.innerHTML = '<i class="bi bi-calendar-week"></i> {{ T::translate('Week View', 'Lingguhang Tingnan')}}';
                    currentView = 'dayGridMonth';
                }
                
                // First remove all events
                calendar.removeAllEvents();
                
                // Go to current month/date
                calendar.today();
                
                // Refresh events data
                calendar.refetchEvents();
                
                // Clear current event selection
                currentEvent = null;
                editButton.disabled = true;
                deleteButton.disabled = true;
                
                // Clear details panel
                appointmentDetailsEl.innerHTML = `
                    <div class="text-center text-muted py-4">
                        <i class="bi bi-calendar-event" style="font-size: 2.5rem; opacity: 0.3;"></i>
                        <p class="mt-3 mb-0">{{ T::translate('Select an appointment to view details', 'Pumili ng appointment para makita ang detalye')}}</p>
                    </div>
                `;

                // Hide spinner when done
                setTimeout(() => {
                    hideCalendarSpinner();
                    showToast('{{ T::translate('Success', 'Tagumpay')}}', '{{ T::translate('Calendar reset successfully', 'Matagumpay na na-reset ang kalendaryo')}}', 'success');
                }, 300);
            });
            
            calendarActions.insertBefore(resetButton, calendarActions.firstChild);
        }

        calendar.render();
        
        // Load beneficiaries on page load
        loadBeneficiaries();
        
        // Load beneficiaries for dropdown
        function loadBeneficiaries() {
            const beneficiarySelect = document.getElementById('beneficiarySelect');
            if (!beneficiarySelect) return;
            
            $.ajax({
                url: '/care-manager/careworker-appointments/beneficiaries',
                method: 'GET',
                success: function(response) {
                    if (response.success && response.beneficiaries && response.beneficiaries.length > 0) {
                        beneficiarySelect.innerHTML = '<option value="">{{ T::translate('Select Beneficiary', 'Pumili ng Benepisyaryo')}}</option>';
                        
                        response.beneficiaries.forEach(function(beneficiary) {
                            const option = document.createElement('option');
                            option.value = beneficiary.id;
                            option.textContent = beneficiary.name;
                            beneficiarySelect.appendChild(option);
                        });
                    }
                },
                error: function(xhr) {
                    console.error('{{ T::translate('Failed to load beneficiaries:', 'Nabigong i-load ang mga benepisyaryo:')}}', xhr);
                }
            });
        }
        
        // Get beneficiary details and autofill form fields
        const beneficiarySelect = document.getElementById('beneficiarySelect');
        if (beneficiarySelect) {
            beneficiarySelect.addEventListener('change', function() {
                const beneficiaryId = this.value;
                if (!beneficiaryId) {
                    document.getElementById('beneficiaryAddress').value = '';
                    document.getElementById('beneficiaryPhone').value = '';
                    return;
                }
                
                // Show loading indicator
                document.getElementById('beneficiaryAddress').value = "{{ T::translate('Loading...', 'Naglo-load...')}}";
                document.getElementById('beneficiaryPhone').value = "{{ T::translate('Loading...', 'Naglo-load...')}}";
                
                $.ajax({
                     url: `/care-manager/careworker-appointments/beneficiary/${beneficiaryId}`,
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.beneficiary) {
                            document.getElementById('beneficiaryAddress').value = response.beneficiary.address || '{{ T::translate('Not Available', 'Hindi Available')}}';
                            document.getElementById('beneficiaryPhone').value = response.beneficiary.phone || '{{ T::translate('Not Available', 'Hindi Available')}}';
                        } else {
                            document.getElementById('beneficiaryAddress').value = '{{ T::translate('Not Available', 'Hindi Available')}}';
                            document.getElementById('beneficiaryPhone').value = '{{ T::translate('Not Available', 'Hindi Available')}}';
                        }
                    },
                    error: function(xhr) {
                        document.getElementById('beneficiaryAddress').value = '{{ T::translate('Error loading details', 'Error sa pag-load ng detalye')}}';
                        document.getElementById('beneficiaryPhone').value = '{{ T::translate('Error loading details', 'Error sa pag-load ng detalye')}}';
                        console.error('{{ T::translate('Failed to load beneficiary details:', 'Nabigong i-load ang mga detalye ng benepisyaryo:')}}', xhr);
                    }
                });
            });
        }
        
        // Format time helper function
        function formatTime(date) {
            if (!date || isNaN(date.getTime())) return '';
            return date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
        }
        
        // Toggle week view button
        if (toggleWeekButton) {
            toggleWeekButton.addEventListener('click', function() {
                if (currentView === 'dayGridMonth') {
                    currentView = 'timeGridWeek';
                    calendar.changeView('timeGridWeek');
                    toggleWeekButton.innerHTML = '<i class="bi bi-calendar-month"></i> {{ T::translate('Month View', 'Buwanang Tingnan')}}';
                } else {
                    currentView = 'dayGridMonth';
                    calendar.changeView('dayGridMonth');
                    toggleWeekButton.innerHTML = '<i class="bi bi-calendar-week"></i> {{ T::translate('Week View', 'Lingguhang Tingnan')}}';
                }
            });
        }
        
        // Show event details in side panel
        function showEventDetails(event) {
            // Enable action buttons
            if (editButton) editButton.disabled = false;
            if (deleteButton) deleteButton.disabled = false;
            
            // Store current event
            currentEvent = event;
            
            // Format date for display
            const eventDate = new Date(event.start);
            const formattedDate = eventDate.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            
            let carePlanStatusHtml = '';
            const rawVisitType = String(event.extendedProps.visit_type || '').toLowerCase();
            const hideSection = rawVisitType.includes('service') || rawVisitType.includes('emergency');
            
            console.log('[DEBUG] Visit type:', rawVisitType, 'Hide section?', hideSection);
            
            if (!hideSection) {
                carePlanStatusHtml = `
                <div class="detail-section">
                    <div class="section-title"><i class="bi bi-check-circle"></i> {{ T::translate('Care Plan Status', 'Status ng Care Plan')}}</div>
                    ${event.extendedProps.has_weekly_care_plan ? `
                        <div class="detail-item">
                            <div class="detail-label">Beneficiary: </div>
                            <div class="detail-value">
                                <span class="badge ${event.extendedProps.confirmed_by_beneficiary ? 'bg-success' : 'bg-secondary'}">
                                    ${event.extendedProps.confirmed_by_beneficiary ? '{{ T::translate('Confirmed', 'Nakumpirma')}}' : '{{ T::translate('Not Confirmed', 'Hindi Nakumpirma')}}'}
                                </span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Family: </div>
                            <div class="detail-value">
                                <span class="badge ${event.extendedProps.confirmed_by_family ? 'bg-success' : 'bg-secondary'}">
                                    ${event.extendedProps.confirmed_by_family ? '{{ T::translate('Confirmed', 'Nakumpirma')}}' : '{{ T::translate('Not Confirmed', 'Hindi Nakumpirma')}}'}
                                </span>
                            </div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">{{ T::translate('Confirmed On', 'Nakumpirma sa')}}: </div>
                            <div class="detail-value">
                                ${event.extendedProps.confirmed_on ? new Date(event.extendedProps.confirmed_on).toLocaleString() : '{{ T::translate('Not confirmed yet', 'Hindi pa nakumpirma')}}'}
                            </div>
                        </div>
                    ` : `
                        <div class="detail-value text-muted">
                            <i class="bi bi-info-circle me-1"></i> {{ T::translate('No care plan has been created yet for this visit.', 'Walang care plan ang nagawa para sa pagbisita na ito.')}}
                        </div>
                    `}
                </div>
                `;
            }

            // Build details HTML
            if (appointmentDetailsEl) {
                appointmentDetailsEl.innerHTML = `
                    <div class="detail-section">
                        <div class="section-title"><i class="bi bi-person-fill"></i> {{ T::translate('Beneficiary', 'Benepisyaryo')}}</div>
                        <div class="detail-value">${event.extendedProps.beneficiary}</div>
                    </div>
                    
                    <div class="detail-section">
                        <div class="section-title"><i class="bi bi-person-badge-fill"></i> {{ T::translate('Care Worker', 'Tagapag-alaga')}}</div>
                        <div class="detail-value">${event.extendedProps.care_worker}</div>
                    </div>
                    
                    <div class="detail-section">
                        <div class="section-title"><i class="bi bi-calendar-date-fill"></i> {{ T::translate('Visit Details', 'Detalye ng Pagbisita')}}</div>
                        <div class="detail-item">
                            <div class="detail-label">{{ T::translate('Date:', 'Petsa:')}}</div>
                            <div class="detail-value">${formattedDate}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">{{ T::translate('Time:', 'Oras:')}}</div>
                            <div class="detail-value">${event.extendedProps.is_flexible_time ? '{{ T::translate('Flexible Time', 'Flexible na Oras')}}' : 
                                (formatTime(event.start) + ' - ' + formatTime(event.end))}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">{{ T::translate('Type:', 'Uri:')}}</div>
                            <div class="detail-value">${event.extendedProps.visit_type}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">{{ T::translate('Status:', 'Estado:')}}</div>
                            <div class="detail-value">${getStatusBadge(event.extendedProps.status)}</div>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <div class="section-title"><i class="bi bi-geo-alt-fill"></i> {{ T::translate('Location', 'Lokasyon')}}</div>
                        <div class="detail-value">${event.extendedProps.address || '{{ T::translate('Not Available', 'Hindi Available')}}'}</div>
                    </div>
                    
                    <!-- Add confirmation status section -->
                    ${carePlanStatusHtml}
                    
                    ${event.extendedProps.notes ? `
                    <div class="detail-section">
                        <div class="section-title"><i class="bi bi-journal-text"></i> {{ T::translate('Notes', 'Mga Tala')}}</div>
                        <div class="detail-value">${event.extendedProps.notes}</div>
                    </div>
                    ` : ''}
                    
                    ${event.extendedProps.cancel_reason ? `
                    <div class="detail-section">
                        <div class="section-title"><i class="bi bi-exclamation-triangle-fill"></i> {{ T::translate('Cancellation Reason', 'Dahilan sa Pagkansela')}}</div>
                        <div class="detail-value">${event.extendedProps.cancel_reason}</div>
                    </div>
                    ` : ''}
                `;
                
                // Only show action buttons for scheduled appointments
                if (event.extendedProps.status.toLowerCase() !== 'scheduled') {
                    if (editButton) editButton.disabled = true;
                    if (deleteButton) deleteButton.disabled = true;
                }
            }
        }
        
        // Helper function to create status badge
        function getStatusBadge(status) {
            const statusLower = status.toLowerCase();
            let badgeClass = 'bg-secondary';
            
            if (statusLower === 'scheduled') badgeClass = 'bg-primary';
            else if (statusLower === 'completed') badgeClass = 'bg-success';
            else if (statusLower === 'canceled') badgeClass = 'bg-danger';
            
            return `<span class="badge ${badgeClass}">${status}</span>`;
        }
        
        // Setup New Appointment button
        const addAppointmentBtn = document.querySelector('[data-bs-target="#addAppointmentModal"]');
        if (addAppointmentBtn) {
            addAppointmentBtn.addEventListener('click', function(e) {
                e.preventDefault(); // Prevent default button behavior

                // Set editing flag to false for new appointments
                isEditing = false;
                
                // Reset form and clear visitation ID (new appointment)
                if (addAppointmentForm) {
                    addAppointmentForm.reset();
                    
                    // Clear any existing hidden fields
                    const visitationIdField = document.getElementById('visitationId');
                    if (visitationIdField) {
                        visitationIdField.value = '';
                    }
                    
                    // Reset time fields
                    if (openTimeCheck) {
                        openTimeCheck.checked = false;
                    }
                    
                    if (timeSelectionContainer) {
                        timeSelectionContainer.style.opacity = '1';
                    }
                    
                    if (startTimeInput) {
                        startTimeInput.disabled = false;
                        startTimeInput.value = '';
                    }
                    
                    if (endTimeInput) {
                        endTimeInput.disabled = false;
                        endTimeInput.value = '';
                    }
                    
                    // Reset recurring options
                    if (recurringCheck) {
                        recurringCheck.checked = false;

                        recurringCheck.disabled = false; // Enable for new appointments
    
                        // Remove any explanatory text that might have been added
                        const recurringHelpText = recurringCheck.closest('.form-check')?.nextElementSibling;
                        if (recurringHelpText && recurringHelpText.classList.contains('form-text')) {
                            recurringHelpText.remove();
                        }
                    }
                    
                    const recurringOptionsContainer = document.getElementById('recurringOptionsContainer');
                    if (recurringOptionsContainer) {
                        recurringOptionsContainer.style.display = 'none';
                    }
                    
                    // Reset pattern type to weekly (default)
                    const patternWeekly = document.getElementById('patternWeekly');
                    if (patternWeekly) {
                        patternWeekly.checked = true;
                    }
                    
                    // Clear day of week checkboxes
                    document.querySelectorAll('input[name="day_of_week[]"]').forEach(cb => {
                        cb.checked = false;
                    });
                }
                
                // Set modal title for new appointment
                if (addAppointmentModalLabel) {
                    addAppointmentModalLabel.innerHTML = '<i class="bi bi-calendar-plus"></i> {{ T::translate('Schedule New Appointment', 'Mag-iskedyul ng Bagong Appointment')}}';
                }
                
                // Set submit button text for new appointment
                if (submitAppointment) {
                    submitAppointment.innerHTML = '<i class="bi bi-calendar-check"></i> {{ T::translate('Schedule Appointment', 'I-iskedyul ang Appointment')}}';
                }
                
                // Remove any recurring warning
                const existingWarning = document.getElementById('recurringWarning');
                if (existingWarning) {
                    existingWarning.remove();
                }
                
                // Show the modal
                addAppointmentModal.show();
            });
        }
        
        // Edit button click handler
        if (editButton) {
            editButton.addEventListener('click', function() {
                if (!currentEvent) {
                    console.log('No event selected');
                    return;
                }

                // Set editing flag to true
                isEditing = true;
                
                try {
                    // Reset form
                    if (addAppointmentForm) {
                        addAppointmentForm.reset();
                    }
                    
                    // Hide error messages
                    const modalErrors = document.getElementById('modalErrors');
                    if (modalErrors) {
                        modalErrors.classList.add('d-none');
                    }
                    
                    // Update modal title
                    if (addAppointmentModalLabel) {
                        addAppointmentModalLabel.innerHTML = '<i class="bi bi-pencil-square"></i> {{ T::translate('Edit Appointment', 'I-edit ang Appointment')}}';
                    }

                    // CRITICAL: Store the exact date being edited for proper cleanup
                    if (currentEvent && currentEvent.start) {
                        try {
                            // Convert to a definite Date object to ensure we're working with a proper date
                            let occDate = new Date(currentEvent.start);
                            
                            // Double-check that we have a valid date
                            if (isNaN(occDate.getTime())) {
                                console.error('Invalid date object:', currentEvent.start);
                                // Fall back to current date as a last resort
                                occDate = new Date();
                            }
                            
                            // Format with direct string manipulation for maximum browser compatibility
                            const year = occDate.getFullYear();
                            const month = String(occDate.getMonth() + 1).padStart(2, '0');
                            const day = String(occDate.getDate()).padStart(2, '0');
                            const formattedDate = `${year}-${month}-${day}`;
                            
                            console.log('Current event start date:', occDate);
                            console.log('Formatted date (YYYY-MM-DD):', formattedDate);
                            
                            // Explicitly set the value of the hidden field
                            const editedOccurrenceField = document.getElementById('edited_occurrence_date');
                            if (editedOccurrenceField) {
                                editedOccurrenceField.value = formattedDate;
                                // Verify the value was set correctly
                                console.log('Field value after setting:', editedOccurrenceField.value);
                            } else {
                                console.error('Could not find edited_occurrence_date field');
                            }
                        } catch (err) {
                            console.error('Error formatting date:', err);
                        }
                    }

                    // Store original care worker ID for comparing with the new selection
                    const originalCareWorkerId = document.getElementById('original_care_worker_id');
                    if (originalCareWorkerId) {
                        originalCareWorkerId.value = currentEvent.extendedProps.care_worker_id;
                        console.log('Setting original care worker ID:', currentEvent.extendedProps.care_worker_id);
                    }
                    
                    // Set form data from event
                    const visitationId = document.getElementById('visitationId');
                    if (visitationId) {
                        visitationId.value = currentEvent.extendedProps.visitation_id;
                    }
                    
                    // Set beneficiary address and phone
                    const beneficiaryAddress = document.getElementById('beneficiaryAddress');
                    if (beneficiaryAddress) {
                        beneficiaryAddress.value = currentEvent.extendedProps.address || '{{ T::translate('Not Available', 'Hindi Available')}}';
                    }
                    
                    const beneficiaryPhone = document.getElementById('beneficiaryPhone');
                    if (beneficiaryPhone) {
                        beneficiaryPhone.value = currentEvent.extendedProps.phone || '{{ T::translate('Not Available', 'Hindi Available')}}';
                    }
                    
                    // Set notes
                    const notes = document.getElementById('notes');
                    if (notes) {
                        notes.value = currentEvent.extendedProps.notes || '';
                    }
                    
                    // Set visit type
                    const visitType = document.getElementById('visitType');
                    if (visitType) {
                        visitType.value = getVisitTypeValue(currentEvent.extendedProps.visit_type);
                    }
                    
                    // Set date
                    const eventDate = new Date(currentEvent.start);
                    const formattedDate = `${eventDate.getFullYear()}-${String(eventDate.getMonth() + 1).padStart(2, '0')}-${String(eventDate.getDate()).padStart(2, '0')}`;
                    
                    const visitDate = document.getElementById('visitDate');
                    if (visitDate) {
                        visitDate.value = formattedDate;
                    }
                    
                    // Time settings
                    const isFlexibleTime = currentEvent.extendedProps.is_flexible_time;
                    if (openTimeCheck) {
                        openTimeCheck.checked = isFlexibleTime;
                    }
                    
                    if (timeSelectionContainer) {
                        timeSelectionContainer.style.opacity = isFlexibleTime ? '0.5' : '1';
                    }
                    
                    if (startTimeInput) {
                        startTimeInput.disabled = isFlexibleTime;
                        
                        if (!isFlexibleTime && currentEvent.start) {
                            const startTime = new Date(currentEvent.start);
                            startTimeInput.value = `${String(startTime.getHours()).padStart(2, '0')}:${String(startTime.getMinutes()).padStart(2, '0')}`;
                        } else {
                            startTimeInput.value = '';
                        }
                    }
                    
                    if (endTimeInput) {
                        endTimeInput.disabled = isFlexibleTime;
                        
                        if (!isFlexibleTime && currentEvent.end) {
                            const endTime = new Date(currentEvent.end);
                            endTimeInput.value = `${String(endTime.getHours()).padStart(2, '0')}:${String(endTime.getMinutes()).padStart(2, '0')}`;
                        } else {
                            endTimeInput.value = '';
                        }
                    }
                    
                    // Set recurring options
                    const isRecurring = currentEvent.extendedProps.recurring;
                    if (recurringCheck) {
                        recurringCheck.checked = isRecurring;

                        // Disable the checkbox when editing an existing appointment
                        recurringCheck.disabled = true;
                        
                        // Add an explanatory message
                        const recurringHelpText = document.createElement('div');
                        recurringHelpText.className = 'form-text text-muted mt-2 note-1';
                        recurringHelpText.innerHTML = '<i class="bi bi-info-circle me-1"></i> ' +
                            '{{ T::translate('Converting between recurring and non-recurring appointments is not allowed.', 'Hindi pinapayagan ang pag-convert sa pagitan ng umuulit at hindi umuulit na appointment.')}} ' +
                            '{{ T::translate('Please cancel this appointment and create a new one instead.', 'Mangyaring kanselahin ang appointment na ito at gumawa na lang ng bago.')}}';
                        
                        // Insert the message after the checkbox's parent element
                        const checkboxParent = recurringCheck.closest('.form-check');
                        if (checkboxParent) {
                            checkboxParent.parentNode.insertBefore(recurringHelpText, checkboxParent.nextSibling);
                        }
                    }

                    // Remove any explanatory text that might have been added
                    const recurringHelpText = recurringCheck.closest('.form-check')?.nextElementSibling;
                    if (recurringHelpText && recurringHelpText.classList.contains('form-text')) {
                        recurringHelpText.remove();
                    }
                    
                    // Show/hide recurring options container based on existing state
                    const recurringOptionsContainer = document.getElementById('recurringOptionsContainer');
                    if (recurringOptionsContainer) {
                        // Always show if it was already recurring, but don't allow toggling
                        recurringOptionsContainer.style.display = isRecurring ? 'block' : 'none';

                        // For existing recurring appointments, show a warning about the pattern
                        if (isRecurring) {
                            const recurringWarningMessage = document.getElementById('recurringWarningMessage');
                            if (recurringWarningMessage) {
                                recurringWarningMessage.style.display = 'block';
                                recurringWarningMessage.innerHTML = `
                                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                                    <strong>{{ T::translate('Note:', 'Paalala:')}}</strong> {{ T::translate('Editing this recurring appointment will affect this and future occurrences. Past occurrences will remain unchanged. You cannot change it to a non-recurring appointment.', 
                                    'Ang pag-edit ng umuulit na appointment na ito ay makakaapekto dito at sa mga susunod na pagkakataon. Ang mga nakaraang pagkakataon ay mananatiling hindi magbabago. Hindi mo ito mababago sa isang hindi umuulit na appointment.')}}
                                `;
                            }
                        }
                    }
                    
                    // FIX: Properly handle recurring pattern type selection
                    if (isRecurring && currentEvent.extendedProps.recurring_pattern) {
                        const pattern = currentEvent.extendedProps.recurring_pattern;
                        console.log('Pattern data:', pattern);
                        
                        // IMPORTANT: First reset all radio selections
                        document.querySelectorAll('input[name="pattern_type"]').forEach(radio => {
                            radio.checked = false;
                        });
                        
                        // Determine pattern type and check the appropriate radio
                        const patternType = pattern.type || 'weekly'; // Default to weekly if not set
                        
                        console.log('Setting pattern type to:', patternType);
                        
                        // Explicitly set the correct radio button
                        if (patternType === 'weekly') {
                            const patternWeekly = document.getElementById('patternWeekly');
                            if (patternWeekly) {
                                patternWeekly.checked = true;
                            }
                            
                            const weeklyOptions = document.getElementById('weeklyOptions');
                            if (weeklyOptions) {
                                weeklyOptions.style.display = 'block';
                            }
                        } else if (patternType === 'monthly') {
                            const patternMonthly = document.getElementById('patternMonthly');
                            if (patternMonthly) {
                                patternMonthly.checked = true;
                            }
                            
                            const weeklyOptions = document.getElementById('weeklyOptions');
                            if (weeklyOptions) {
                                weeklyOptions.style.display = 'none';
                            }
                        }
                        
                        // Handle weekly day selection if applicable
                        if (patternType === 'weekly' && pattern.day_of_week) {
                            // Clear existing selections
                            document.querySelectorAll('input[name="day_of_week[]"]').forEach(cb => {
                                cb.checked = false;
                            });
                            
                            // Parse and check the appropriate day(s)
                            const dayArray = typeof pattern.day_of_week === 'string' ? 
                                pattern.day_of_week.split(',').map(d => d.trim()) : 
                                [String(pattern.day_of_week)];
                            
                            console.log('Setting day of week:', dayArray);
                            
                            dayArray.forEach(day => {
                                const checkbox = document.querySelector(`input[name="day_of_week[]"][value="${day}"]`);
                                if (checkbox) {
                                    checkbox.checked = true;
                                }
                            });
                        }
                        
                        // Set end date if present
                        const recurrenceEnd = document.getElementById('recurrenceEnd');
                        if (recurrenceEnd && pattern.recurrence_end) {
                            recurrenceEnd.value = pattern.recurrence_end;
                        }
                    }
                    
                    // Show warning if editing a recurring event
                    if (isRecurring) {
                        const modalContent = document.querySelector('.modal-body');
                        if (modalContent) {
                            // Remove any existing warning first
                            const existingWarning = document.getElementById('recurringWarning');
                            if (existingWarning) {
                                existingWarning.remove();
                            }
                            
                            // Add fresh warning
                            // Use the existing warning element instead of creating a new one
                            const recurringWarningMessage = document.getElementById('recurringWarningMessage');
                            if (recurringWarningMessage) {
                                recurringWarningMessage.style.display = 'block';
                            } else {
                                console.warn('Warning message element not found');
                            }
                        }
                    }
                    
                    // Update submit button text
                    if (submitAppointment) {
                        submitAppointment.innerHTML = '<i class="bi bi-check-circle"></i> {{ T::translate('Update Appointment', 'I-update ang Appointment')}}';
                    }
                    
                    // Load beneficiaries for the form
                    loadBeneficiariesForEdit(currentEvent.extendedProps.beneficiary_id, currentEvent.extendedProps.care_worker_id);
                    
                    // Show the modal
                    addAppointmentModal.show();
                    
                    console.log('Edit modal should now be visible');
                } catch (error) {
                    console.error('Error opening edit modal:', error);
                    showErrorModal('{{ T::translate('There was an error opening the edit form. Please try again.', 'Nagkaroon ng error sa pagbukas ng edit form. Mangyaring subukang muli.')}}');
                }
            });
        }
        
        // Helper function to convert UI visit type to backend value
        function getVisitTypeValue(uiVisitType) {
            if (!uiVisitType) return '';
            
            const lower = uiVisitType.toLowerCase();
            if (lower.includes('routine')) return 'routine_care_visit';
            if (lower.includes('service')) return 'service_request';
            if (lower.includes('emergency')) return 'emergency_visit';
            return '';
        }
        
        // Load beneficiaries for edit form
        function loadBeneficiariesForEdit(beneficiaryId, careWorkerId) {
            // Load beneficiaries
            $.ajax({
                url: '/care-manager/careworker-appointments/beneficiaries',
                method: 'GET',
                success: function(response) {
                    const select = document.getElementById('beneficiarySelect');
                    if (!select) return;
                    
                    select.innerHTML = '<option value="">{{ T::translate('Select Beneficiary', 'Pumili ng Benepisyaryo')}}</option>';
                    
                    if (response.success && response.beneficiaries.length > 0) {
                        response.beneficiaries.forEach(function(beneficiary) {
                            const option = document.createElement('option');
                            option.value = beneficiary.id;
                            option.textContent = beneficiary.name;
                            option.selected = (beneficiary.id == beneficiaryId);
                            select.appendChild(option);
                        });
                    }
                },
                error: function(xhr) {
                    console.error('{{ T::translate('Error loading beneficiaries:', 'Error sa pag-load ng mga benepisyaryo:')}}', xhr);
                }
            });
            
            // Select care worker if provided
            if (careWorkerId) {
                const careWorkerSelect = document.getElementById('careWorkerSelect');
                if (!careWorkerSelect) return;
                
                const options = careWorkerSelect.options;
                
                for (let i = 0; i < options.length; i++) {
                    if (options[i].value == careWorkerId) {
                        careWorkerSelect.selectedIndex = i;
                        break;
                    }
                }
            }
        }
        
        // Form submission handler
        if (submitAppointment) {
            submitAppointment.addEventListener('click', function(e) {
                e.preventDefault();
                
                // Reset error messages
                const modalErrors = document.getElementById('modalErrors');
                if (modalErrors) {
                    modalErrors.classList.add('d-none');
                }
                
                document.querySelectorAll('.error-feedback').forEach(el => {
                    el.innerHTML = '';
                });
                
                document.querySelectorAll('.is-invalid').forEach(el => {
                    el.classList.remove('is-invalid');
                });
                
                // Get form data
                const formData = new FormData(document.getElementById('addAppointmentForm'));
                const visitationId = formData.get('visitation_id');
                
                // Ensure is_flexible_time is explicitly set
                const isFlexibleTime = document.getElementById('openTimeCheck').checked;
                formData.set('is_flexible_time', isFlexibleTime ? '1' : '0');
                
                // Add debugging
                console.log('Form submission - isEditing:', isEditing);
                console.log('Form submission - isRecurring:', recurringCheck && recurringCheck.checked);

                // Handle recurring pattern data (consolidated approach)
                if (recurringCheck && recurringCheck.checked) {
                    // IMPORTANT: Add this line to explicitly set is_recurring in form data
                    formData.append('is_recurring', '1'); 

                    // Get selected pattern type
                    const patternType = document.querySelector('input[name="pattern_type"]:checked').value;
                    console.log('Pattern type:', patternType);
                    
                    // For weekly pattern, properly handle days - IMPORTANT: Only process once
                    if (patternType === 'weekly') {
                        // Clear any existing day_of_week entries to avoid duplication
                        for(const pair of formData.entries()) {
                            if (pair[0] === 'day_of_week[]') {
                                formData.delete('day_of_week[]');
                            }
                        }
                        
                        // Collect selected days
                        const selectedDays = [];
                        document.querySelectorAll('input[name="day_of_week[]"]:checked').forEach(checkbox => {
                            selectedDays.push(checkbox.value);
                        });
                        
                        console.log('Selected days for weekly pattern:', selectedDays);
                        
                        // If no days selected, use the current day from the appointment date
                        if (selectedDays.length === 0) {
                            const visitDate = document.getElementById('visitDate').value;
                            if (visitDate) {
                                const date = new Date(visitDate);
                                const dayOfWeek = date.getDay();
                                selectedDays.push(dayOfWeek.toString());
                                console.log('No days selected, using visit date day:', dayOfWeek);
                            }
                        }
                        
                        // Add each day as separate form field entry
                        selectedDays.forEach(day => {
                            formData.append('day_of_week[]', day);
                        });
                    }
                }
                
                // Set appropriate URL based on whether this is an edit or new appointment
                const url = visitationId ? 
                   '/care-manager/careworker-appointments/update' : 
                    '/care-manager/careworker-appointments/store' ;

                // Show loading state
                const originalBtnHtml = submitAppointment.innerHTML;
                submitAppointment.disabled = true;
                submitAppointment.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ T::translate('Processing...', 'Pinoproseso...')}}';
                
                // Add form data debugging
                console.log('Form data day_of_week values:', formData.getAll('day_of_week[]'));
                
                $.ajax({
                    url: url,
                    method: 'POST',
                    data: formData,
                    contentType: false,
                    processData: false,
                    success: function(response) {
                        if (response.success) {
                            $('#addAppointmentModal').modal('hide');
                            
                            // Show success toast
                            showToast('{{ T::translate('Success', 'Tagumpay')}}', 
                                visitationId ? '{{ T::translate('Appointment updated successfully!', 'Matagumpay na na-update ang appointment!')}}' : '{{ T::translate('Appointment created successfully!', 'Matagumpay na nagawa ang appointment!')}}', 
                                'success');
                            
                            // Improved refresh sequence for create/edit operations
                            setTimeout(function() {
                                // First step: clear any display caching but don't remove sources
                                calendar.getEvents().forEach(e => e.remove());
                                
                                // Add timestamp parameter to avoid browser/server caching
                                const timestamp = new Date().getTime();
                                const originalEvents = calendar.getEventSources()[0];
                                if (originalEvents) {
                                    originalEvents.remove();
                                }
                                
                                // Add event source with cache-busting parameter
                                calendar.addEventSource({
                                    url: '/care-manager/careworker-appointments/get-visitations',
                                    extraParams: {
                                        cache_buster: timestamp
                                    }
                                });
                                
                                console.log(`Calendar refreshed with cache busting after create/edit (${timestamp})`);
                            }, 800); // Slightly longer delay for create/edit operations
                        } else {
                            // Show validation errors
                            if (response.errors) {
                                showValidationErrors(response.errors);
                            } else {
                                showErrorModal(response.message || '{{ T::translate('An error occurred while saving the appointment.', 'Nagkaroon ng error habang sinasave ang appointment.')}}');
                            }
                        }
                    },
                    error: function(xhr, textStatus, errorThrown) {
                        console.error('{{ T::translate('Error submitting form:', 'Error sa pagsusumite ng form:')}}', xhr);
                        console.error('Status:', textStatus);
                        console.error('Error thrown:', errorThrown);
                        
                        // Add this to see detailed validation errors
                        if (xhr.responseJSON) {
                            console.error('Detailed error:', xhr.responseJSON);
                            
                            if (xhr.responseJSON.errors) {
                                console.table(xhr.responseJSON.errors);
                                showValidationErrors(xhr.responseJSON.errors);
                            } else if (xhr.responseJSON.message) {
                                showErrorModal(xhr.responseJSON.message);
                            }
                        } else {
                            showErrorModal('{{ T::translate('An error occurred while saving the appointment. Please try again.', 'Nagkaroon ng error habang sinasave ang appointment. Mangyaring subukang muli.')}}');
                        }
                    },
                    complete: function() {
                        // Restore button state
                        submitAppointment.disabled = false;
                        submitAppointment.innerHTML = originalBtnHtml;
                    }
                });
            });
        }
        
        // Delete button click handler
        if (deleteButton) {
            deleteButton.addEventListener('click', function() {
                if (!currentEvent) {
                    console.log('No event selected');
                    return;
                }
                
                // Get modal elements
                const confirmationModalLabel = document.getElementById('confirmationModalLabel');
                const confirmationModalBody = document.getElementById('confirmationModalBody');
                const modalHeader = document.querySelector('#confirmationModal .modal-header');
                
                // Make header red
                if (modalHeader) {
                    modalHeader.classList.remove('bg-primary');
                    modalHeader.classList.add('bg-danger');
                }
                
                // Update modal title
                if (confirmationModalLabel) {
                    confirmationModalLabel.innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate('Cancel Appointment', 'Kanselahin ang Appointment')}}';
                }
                
                // Get appointment details
                const isRecurring = currentEvent.extendedProps.recurring;
                const eventDate = new Date(currentEvent.start).toLocaleDateString();
                const careWorkerName = currentEvent.extendedProps.care_worker || "{{ T::translate('Not assigned', 'Hindi itinalaga')}}";
                const beneficiaryName = currentEvent.extendedProps.beneficiary || "{{ T::translate('Not specified', 'Hindi tinukoy')}}";
                const visitationType = currentEvent.extendedProps.visit_type || "{{ T::translate('Not specified', 'Hindi tinukoy')}}";
                
                // Build the modal content
                let modalContent = `
                    <div class="mb-4">
                        <p class="mb-1"><strong>{{ T::translate('Date:', 'Petsa:')}}</strong> ${eventDate}</p>
                        <p class="mb-1"><strong>{{ T::translate('Care Worker:', 'Tagapag-alaga:')}}</strong> ${careWorkerName}</p>
                        <p class="mb-1"><strong>{{ T::translate('Beneficiary:', 'Benepisyaryo:')}}</strong> ${beneficiaryName}</p>
                        <p class="mb-1"><strong>{{ T::translate('Type:', 'Uri:')}}</strong> ${visitationType}</p>
                        ${isRecurring ? '<p class="mb-1 text-danger"><strong><i class="bi bi-repeat"></i> {{ T::translate('Recurring Appointment', 'Umuulit na Appointment')}}</strong></p>' : ''}
                    </div>
                    
                    <input type="hidden" id="deleteVisitationId" value="${currentEvent.extendedProps.visitation_id}">
                `;
                
                // Add cancellation options for recurring appointments only
                if (isRecurring) {
                    modalContent += `
                        <div class="mb-3 border rounded p-3 bg-light">
                            <p class="mb-2"><strong>{{ T::translate('Cancellation Options:', 'Mga Pagpipilian sa Pagkansela:')}}</strong></p>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="cancel_option" id="cancelSingle" value="single" checked>
                                <label class="form-check-label" for="cancelSingle">
                                    {{ T::translate('Cancel only this occurrence', 'Kanselahin lamang ang pagkakataong ito')}} (${eventDate})
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="cancel_option" id="cancelFuture" value="future">
                                <label class="form-check-label" for="cancelFuture">
                                    {{ T::translate('Cancel this and all future occurrences', 'Kanselahin ito at ang lahat ng susunod na pagkakataon')}}
                                </label>
                            </div>
                        </div>
                    `;
                } else {
                    modalContent += `
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-info-circle me-2"></i>
                            {{ T::translate('This will cancel the appointment for', 'Kakanselahin nito ang appointment para sa')}} ${eventDate}
                        </div>
                    `;
                }
                
                // Add reason and password fields
                modalContent += `
                    <div class="mb-3">
                        <label for="cancelReason" class="form-label">{{ T::translate('Reason for Cancellation', 'Dahilan sa Pagkansela')}}</label>
                        <textarea class="form-control" id="cancelReason" rows="3" placeholder="{{ T::translate('Please provide a reason for cancellation...', 'Mangyaring magbigay ng dahilan para sa pagkansela...')}}"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cancelPassword" class="form-label">{{ T::translate('Confirm your password', 'Kumpirmahin ang iyong password')}}</label>
                        <input type="password" class="form-control" id="cancelPassword" placeholder="{{ T::translate('Enter your password', 'Ilagay ang iyong password')}}">
                        <div id="passwordError" class="text-danger mt-1"></div>
                    </div>
                `;
                
                // Set the modal content
                confirmationModalBody.innerHTML = modalContent;
                
                // Show the confirmation modal
                confirmationModal.show();
            });
        }
        
        // Helper function to format date for API
        function formatDateForAPI(date) {
            if (!date) return '';
            
            const d = new Date(date);
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            
            return `${year}-${month}-${day}`;
        }
        
        // Confirm delete button handler
        const confirmDeleteBtn = document.getElementById('confirmDelete');
        if (confirmDeleteBtn) {
            confirmDeleteBtn.addEventListener('click', function() {
                const reason = document.getElementById('cancelReason')?.value.trim();
                const password = document.getElementById('cancelPassword')?.value.trim();
                let isValid = true;
                
                // Validate reason
                if (!reason) {
                    document.getElementById('cancelReason').classList.add('is-invalid');
                    isValid = false;
                } else {
                    document.getElementById('cancelReason').classList.remove('is-invalid');
                }
                
                // Validate password
                if (!password) {
                    document.getElementById('cancelPassword').classList.add('is-invalid');
                    document.getElementById('passwordError').textContent = '{{ T::translate('Password is required', 'Kinakailangan ang password')}}';
                    isValid = false;
                } else {
                    document.getElementById('cancelPassword').classList.remove('is-invalid');
                    document.getElementById('passwordError').textContent = '';
                }
                
                if (!isValid) return;
                
                // Show loading state
                const originalText = confirmDeleteBtn.innerHTML;
                confirmDeleteBtn.disabled = true;
                confirmDeleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ T::translate('Processing...', 'Pinoproseso...')}}';
                
                // Get form data
                const formData = {
                    _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    visitation_id: document.getElementById('deleteVisitationId').value,
                    reason: reason,
                    password: password
                };
                
                // Add cancel option for recurring appointments
                const isRecurring = currentEvent.extendedProps.recurring;
                if (isRecurring) {
                    const cancelOption = document.querySelector('input[name="cancel_option"]:checked')?.value;
                    if (cancelOption) {
                        formData.cancel_option = cancelOption;
                        
                        // IMPORTANT: Add the occurrence_id when canceling a single occurrence
                        if (cancelOption === 'single') {
                            formData.occurrence_id = currentEvent.extendedProps.occurrence_id;
                        }
                    }
                    
                    const occurrenceDate = currentEvent.start ? formatDateForAPI(currentEvent.start) : '';
                    formData.occurrence_date = occurrenceDate;
                }
                
                $.ajax({
                    url: '/care-manager/careworker-appointments/cancel',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            // Show success toast
                            showToast('{{ T::translate('Success', 'Tagumpay')}}', response.message || '{{ T::translate('Appointment cancelled successfully', 'Matagumpay na nakansela ang appointment')}}', 'success');

                            // Close the modal
                            confirmationModal.hide();

                            // Reset form fields
                            document.getElementById('cancelReason').value = '';
                            document.getElementById('cancelPassword').value = '';

                            // Replace the aggressive refresh with a gentler approach
                            setTimeout(function() {
                                // Don't remove event sources, just trigger a refresh
                                calendar.refetchEvents();
                                console.log('Calendar refreshed after cancellation');
                            }, 500);

                            // Reset current event and disable action buttons
                            currentEvent = null;
                            if (editButton) editButton.disabled = true;
                            if (deleteButton) deleteButton.disabled = true;
                            
                            // Reset appointment details view
                            if (appointmentDetailsEl) {
                                appointmentDetailsEl.innerHTML = `
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-calendar-event" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                        <p class="mt-3 mb-0">{{ T::translate('Select an appointment to view details', 'Pumili ng appointment para makita ang detalye')}}</p>
                                    </div>
                                `;
                            }
                        } else {
                            // Show error
                            if (response.passwordError) {
                                document.getElementById('cancelPassword').classList.add('is-invalid');
                                document.getElementById('passwordError').textContent = response.passwordError;
                            } else {
                                showErrorModal(response.message || '{{ T::translate('Failed to cancel the appointment.', 'Nabigong kanselahin ang appointment.')}}');
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error('Error response:', xhr);
                        
                        if (xhr.status === 401) {
                            document.getElementById('cancelPassword').classList.add('is-invalid');
                            document.getElementById('passwordError').textContent = '{{ T::translate('Incorrect password.', 'Hindi tamang password.')}}';
                        } else {
                            showErrorModal('{{ T::translate('An error occurred while cancelling the appointment. Please try again.', 'Nagkaroon ng error habang kinansela ang appointment. Mangyaring subukang muli.')}}');
                        }
                    },
                    complete: function() {
                        // Reset button state
                        confirmDeleteBtn.disabled = false;
                        confirmDeleteBtn.innerHTML = originalText;
                    }
                });
            });
        }
        
        // Show validation errors
        function showValidationErrors(errors) {
            const errorList = document.getElementById('errorList');
            if (!errorList) return;
            
            errorList.innerHTML = '';
            
            const modalErrors = document.getElementById('modalErrors');
            if (modalErrors) {
                modalErrors.classList.remove('d-none');
            }
            
            // Process each error
            Object.keys(errors).forEach(key => {
                const error = errors[key];
                const errorMsg = Array.isArray(error) ? error[0] : error;
                
                // Add to error list
                const li = document.createElement('li');
                li.textContent = errorMsg;
                errorList.appendChild(li);
                
                // Mark field as invalid
                const field = document.querySelector(`[name="${key}"]`);
                if (field) {
                    field.classList.add('is-invalid');
                }
                
                // Add error message to feedback div
                const feedbackDiv = document.getElementById(`${key.replace('_', '-')}-error`);
                if (feedbackDiv) {
                    feedbackDiv.innerHTML = errorMsg;
                }
            });
        }
        
        // Show error modal
        function showErrorModal(message) {
            const errorMessage = document.getElementById('errorMessage');
            if (errorMessage) {
                errorMessage.innerHTML = message;
            }
            
            errorModal.show();
        }
        
        // Show success message with toast
        function showSuccessMessage(message) {
            // Create toast element
            const toastContainer = document.createElement('div');
            toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
            toastContainer.style.zIndex = '5000';
            
            toastContainer.innerHTML = `
                <div class="toast align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi bi-check-circle-fill me-2"></i> ${message}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
            
            document.body.appendChild(toastContainer);
            
            const toastElement = toastContainer.querySelector('.toast');
            const toast = new bootstrap.Toast(toastElement, {
                delay: 5000
            });
            
            toast.show();
            
            // Remove from DOM after hiding
            toastElement.addEventListener('hidden.bs.toast', function() {
                toastContainer.remove();
            });
        }

        function showToast(title, message, type) {
            // Create toast container if it doesn't exist
            let toastContainer = document.getElementById('toast-container');
            if (!toastContainer) {
                toastContainer = document.createElement('div');
                toastContainer.id = 'toast-container';
                toastContainer.className = 'position-fixed bottom-0 end-0 p-3';
                toastContainer.style.zIndex = '5000';
                document.body.appendChild(toastContainer);
            }
            
            // Create unique ID for this toast
            const toastId = 'toast-' + Date.now();
            
            // Determine background color based on type
            let bgClass = 'bg-primary';
            let iconClass = 'bi-info-circle-fill';
            
            if (type === 'success') {
                bgClass = 'bg-success';
                iconClass = 'bi-check-circle-fill';
            } else if (type === 'danger' || type === 'error') {
                bgClass = 'bg-danger';
                iconClass = 'bi-exclamation-circle-fill';
            } else if (type === 'warning') {
                bgClass = 'bg-warning';
                iconClass = 'bi-exclamation-triangle-fill';
            }
            
            // Create toast HTML
            const toastHtml = `
                <div id="${toastId}" class="toast align-items-center text-white ${bgClass} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="d-flex">
                        <div class="toast-body">
                            <i class="bi ${iconClass} me-2"></i>
                            <strong>${title}</strong> ${message ? ': ' + message : ''}
                        </div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                </div>
            `;
            
            // Add toast to container
            toastContainer.innerHTML += toastHtml;
            
            // Initialize and show the toast
            const toastElement = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastElement, {
                delay: 5000,
                autohide: true
            });
            
            toast.show();
            
            // Remove from DOM after hiding
            toastElement.addEventListener('hidden.bs.toast', function() {
                this.remove();
            });
        }
        
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(function() {
                calendar.refetchEvents();
            }, 500));
        }
        
        // Debounce helper function
        function debounce(func, wait) {
            let timeout;
            
            return function executedFunction(...args) {
                const later = () => {
                    clearTimeout(timeout);
                    func(...args);
                };
                
                clearTimeout(timeout);
                timeout = setTimeout(later, wait);
            };
        }
    });
    
</script>
</body>
</html>