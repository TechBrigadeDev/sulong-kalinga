<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Care Worker Scheduling</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/locales-all.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/timegrid/main.min.js"></script>


    <style>
        /* Card Design */
        .modal-header-danger {
            background-color: #dc3545;
            color: white;
        }

        .card {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            border: 1px solid rgba(0,0,0,0.07);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            padding: 0.8rem 1.25rem;
            background-color: #f8f9fc;
            border-bottom: 1px solid rgba(0,0,0,0.07);
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        
        .section-heading {
            font-weight: 600;
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 0;
        }
        
        /* Calendar Enhancements */
        #calendar-container {
            border-radius: 8px;
            background-color: white;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            padding: 1rem;
            margin-bottom: 1.5rem;
            overflow-x: auto; /* Enable horizontal scrolling */
        }
        
        #calendar {
            background-color: white;
            min-height: 600px;
            min-width: 800px; /* Set minimum width to ensure functionality */
        }
        
        /* Event Styling */
        .fc-event {
            cursor: pointer;
            border: none !important;
            padding: 4px 6px;
            margin-bottom: 2px;
            border-radius: 6px;
        }
        
        .fc-event.open-time {
            border-left: 4px solid rgb(201, 20, 59) !important;
            background-color: #dc3545 !important;
            color: #333;
        }
        
        .fc-event-main {
            display: flex;
            flex-direction: column;
            padding: 4px 0;
        }
        
        .event-worker {
            font-weight: 600;
            font-size: 0.85rem;
            white-space: normal !important;
        }
        
        .event-details {
            font-size: 0.75rem;
            line-height: 1.3;
            white-space: normal !important;
        }
        
        .fc-daygrid-event-dot {
            display: none; /* Hide default event dots */
        }
        
        /* Action Buttons */
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }
        
        .action-btn {
            padding: 0.6rem;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 6px;
            transition: all 0.2s ease-in-out;
        }
        
        .action-btn i {
            margin-right: 8px;
        }
        
        .action-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        /* Search Bar Enhancements */
        .search-container {
            position: relative;
            margin-bottom: 1rem;
        }
        
        .search-container i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            transition: opacity 0.2s ease;
        }
        
        .search-container .search-input:focus + i,
        .search-container .search-input:not(:placeholder-shown) + i {
            opacity: 0;
        }
        
        .search-input {
            padding-left: 35px;
            border-radius: 6px;
            border: 1px solid #dee2e6;
        }
        
        /* Fix for search placeholder */
        .search-input::placeholder {
            color: #a0a5aa;
        }
        
        /* Appointment Details Panel */
        .details-container {
            border-radius: 8px;
            overflow: hidden;
        }
        
        .details-header {
            padding: 0.8rem 1.25rem;
            background-color: #4e73df;
            color: white;
        }
        
        .details-body {
            padding: 1rem;
            background-color: white;
            border: 1px solid rgba(0,0,0,0.07);
            border-top: none;
        }
        
        .detail-section {
            margin-bottom: 1rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid rgba(0,0,0,0.07);
        }
        
        .detail-section:last-child {
            border-bottom: none;
            margin-bottom: 0;
            padding-bottom: 0;
        }
        
        .section-title {
            font-weight: 600;
            color: #4e73df;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
            display: flex;
            align-items: center;
        }
        
        .section-title i {
            margin-right: 8px;
        }
        
        .detail-item {
            margin-bottom: 0.4rem;
            display: flex;
        }
        
        .detail-label {
            font-weight: 500;
            width: 80px;
            font-size: 0.85rem;
            color: #666;
        }
        
        .detail-value {
            font-size: 0.85rem;
            flex: 1;
        }
        
        /* Open time indicator */
        .open-time-indicator {
            display: inline-flex;
            align-items: center;
            font-size: 0.7rem;
            font-weight: 500;
            margin-top: 2px;
        }
        
        .open-time-indicator i {
            margin-right: 3px;
        }
        
        /* Dropdown select styling */
        .select-container {
            position: relative;
        }
        
        .select-container::after {
            content: "";
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid #666;
            pointer-events: none;
        }
        
        .select-container select {
            padding-right: 25px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
        }
        
        /* Modal Enhancements */
        .modal-content {
            border: none;
            border-radius: 12px;
            overflow: hidden;
        }
        
        .modal-header {
            background-color: #4e73df;
            color: white;
            padding: 1rem 1.5rem;
            border-bottom: none;
        }
        
        .modal-body {
            padding: 1.5rem;
        }
        
        .modal-footer {
            padding: 1rem 1.5rem;
            background-color: #f8f9fa;
            border-top: 1px solid #dee2e6;
        }
        
        /* Form Enhancements */
        .form-label {
            font-weight: 500;
            font-size: 0.9rem;
            margin-bottom: 0.4rem;
            color: #495057;
        }
        
        .form-group {
            margin-bottom: 1.2rem;
        }
        
        .form-control {
            border-radius: 6px;
            padding: 0.5rem 0.75rem;
            border: 1px solid #ced4da;
        }
        
        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        /* Day Checkboxes */
        .day-checkboxes {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 8px;
        }
        
        .day-checkbox {
            flex: 0 0 calc(25% - 8px);
            position: relative;
        }
        
        .day-checkbox input {
            position: absolute;
            opacity: 0;
        }
        
        .day-checkbox label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px 4px;
            border: 1px solid #dee2e6;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.2s;
            font-size: 0.8rem;
            width: 100%;
            text-align: center;
        }
        
        .day-checkbox input:checked + label {
            background-color: #4e73df;
            border-color: #4e73df;
            color: white;
        }

        .note-1{
            padding: 0.1rem, 0rem, 0.75rem, 0rem;
        }
        
        /* Time Input Group */
        .time-input-group {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .time-input {
            flex: 1;
        }
        
        .time-separator {
            font-weight: bold;
            color: #495057;
        }
        
        /* Open Time checkbox */
        .open-time-container {
            margin-top: 0.5rem;
            padding-top: 0.75rem;
            border-top: 1px solid #dee2e6;
        }
        
        /* Action Buttons in Modal */
        .modal-action-btn {
            padding: 0.5rem 1.5rem;
            border-radius: 6px;
            font-weight: 500;
        }
        
        .btn-schedule {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        
        .btn-schedule:hover {
            background-color: #3a5fc8;
            border-color: #3a5fc8;
        }
        
        /* Responsive Adjustments */
        @media (max-width: 767.98px) {
            .day-checkbox {
                flex: 0 0 calc(33.333% - 8px);
            }
            
            .fc-toolbar-title {
                font-size: 1.1rem !important;
            }
            
            .fc-button {
                font-size: 0.8rem;
                padding: 0.3rem 0.5rem;
            }
            
            .action-btn {
                font-size: 0.85rem;
            }
        }
        
        @media (max-width: 575.98px) {
            .day-checkbox {
                flex: 0 0 calc(50% - 8px);
            }
            
            .detail-label {
                width: 70px;
            }
        }
        
        /* FullCalendar Customizations */
        .fc .fc-toolbar.fc-header-toolbar {
            margin-bottom: 1.2rem;
        }
        
        .fc .fc-button-primary {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        
        .fc .fc-button-primary:hover {
            background-color: #3a5fc8;
            border-color: #3a5fc8;
        }
        
        .fc .fc-button-primary:disabled {
            background-color: #6c8ae4;
            border-color: #6c8ae4;
        }
        
        .fc .fc-button-primary:not(:disabled).fc-button-active, 
        .fc .fc-button-primary:not(:disabled):active {
            background-color: #3a5fc8;
            border-color: #3a5fc8;
        }
        
        .fc-daygrid-day-number {
            font-size: 0.9rem;
            font-weight: 500;
        }
        
        .fc-col-header-cell-cushion {
            font-weight: 600;
        }
        
        /* Open Time / All Day Events */
        .fc-daygrid-block-event .fc-event-time {
            font-weight: 600;
        }
        
        /* Tooltip Styling */
        .tooltip-inner {
            max-width: 300px;
            padding: 10px 12px;
            text-align: left;
            background-color: #343a40;
            border-radius: 6px;
        }
    </style>
</head>
<body>

    @include('components.careWorkerNavbar')
    @include('components.careWorkerSidebar')

    <div class="home-section">
        <div class="text-left">CARE WORKER SCHEDULING</div>
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
                                        <i class="bi bi-calendar3"></i> Appointment Calendar
                                    </h5>
                                    <div class="calendar-actions d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="toggleWeekView">
                                            <i class="bi bi-calendar-week"></i> Week View
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div id="calendar-container">
                                        <div id="calendar-loading-indicator" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10;">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">Loading...</span>
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
                                <input type="text" id="searchInput" class="form-control search-input" placeholder="     Search appointments..." aria-label="Search appointments">
                                <i class="bi bi-search"></i>
                            </div>
                            
                            <!-- Appointment Details Panel -->
                            <div class="card details-container">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="section-heading mb-0">
                                        <i class="bi bi-info-circle"></i> Appointment Details
                                    </h5>
                                </div>
                                <div class="card-body" id="appointmentDetails">
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-calendar-event" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                        <p class="mt-3 mb-0">Select an appointment to view details</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Error Modal -->
    <div class="modal fade error-modal" id="errorModal" tabindex="-1" aria-labelledby="errorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="errorModalLabel">Error</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="errorMessage"></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Setup CSRF token for all AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // DOM element references
        const calendarEl = document.getElementById('calendar');
        const appointmentDetailsEl = document.getElementById('appointmentDetails');
        const errorModal = new bootstrap.Modal(document.getElementById('errorModal'));
        const toggleWeekButton = document.getElementById('toggleWeekView');
        let currentEvent = null;
        let currentView = 'dayGridMonth';

        // Initialize tooltips
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        const tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
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
                const careWorkerId = document.getElementById('careWorkerSelect') ? document.getElementById('careWorkerSelect').value : '';
                
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
                    showErrorModal('Loading took too long. Try viewing a smaller date range or reset the calendar.');
                }, 15000); // 15 seconds timeout
                
                $.ajax({
                    url: '{{ route("care-worker.careworker.appointments.get") }}',
                    method: 'GET',
                    data: {
                        start: start,
                        end: end,
                        search: searchTerm,
                        care_worker_id: careWorkerId,
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
                        showErrorModal('Failed to fetch appointments: ' + (xhr.responseJSON?.message || 'Server error'));
                        console.error('Error fetching events:', xhr.responseText);
                    },
                    complete: function() {
                        // Always ensure loading indicator is hidden
                        if (document.getElementById('calendar-loading-indicator')) {
                            document.getElementById('calendar-loading-indicator').style.display = 'none';
                        }
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
                            <div class="open-time-indicator"><i class="bi bi-clock"></i> Flexible Time</div>
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
                            <div class="open-time-indicator"><i class="bi bi-clock"></i> Flexible Time</div>
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
            dayMaxEvents: true,
            firstDay: 0, // Start week on Sunday
            height: 'auto'
        });

        // Add a reset button to clear all events if needed
        const calendarActions = document.querySelector('.calendar-actions');
        if (calendarActions) {
            const resetButton = document.createElement('button');
            resetButton.type = 'button';
            resetButton.className = 'btn btn-sm btn-outline-secondary';
            resetButton.innerHTML = '<i class="bi bi-arrow-counterclockwise"></i> Reset';
            resetButton.addEventListener('click', function() {
                calendar.removeAllEvents();
                calendar.today();
                calendar.refetchEvents();
                showSuccessMessage('Calendar reset successfully');
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
                url: '{{ route("care-worker.careworker.appointments.beneficiaries") }}',
                method: 'GET',
                success: function(response) {
                    if (response.success && response.beneficiaries && response.beneficiaries.length > 0) {
                        beneficiarySelect.innerHTML = '<option value="">Select Beneficiary</option>';
                        
                        response.beneficiaries.forEach(function(beneficiary) {
                            const option = document.createElement('option');
                            option.value = beneficiary.id;
                            option.textContent = beneficiary.name;
                            beneficiarySelect.appendChild(option);
                        });
                    }
                },
                error: function(xhr) {
                    console.error('Failed to load beneficiaries:', xhr);
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
                document.getElementById('beneficiaryAddress').value = "Loading...";
                document.getElementById('beneficiaryPhone').value = "Loading...";
                
                $.ajax({
                    url: '{{ route("care-worker.careworker.appointments.beneficiary.details", ["id" => "__id__"]) }}'.replace('__id__', beneficiaryId),
                    method: 'GET',
                    success: function(response) {
                        if (response.success && response.beneficiary) {
                            document.getElementById('beneficiaryAddress').value = response.beneficiary.address || 'Not Available';
                            document.getElementById('beneficiaryPhone').value = response.beneficiary.phone || 'Not Available';
                        } else {
                            document.getElementById('beneficiaryAddress').value = 'Not Available';
                            document.getElementById('beneficiaryPhone').value = 'Not Available';
                        }
                    },
                    error: function(xhr) {
                        document.getElementById('beneficiaryAddress').value = 'Error loading details';
                        document.getElementById('beneficiaryPhone').value = 'Error loading details';
                        console.error('Failed to load beneficiary details:', xhr);
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
                    toggleWeekButton.innerHTML = '<i class="bi bi-calendar-month"></i> Month View';
                } else {
                    currentView = 'dayGridMonth';
                    calendar.changeView('dayGridMonth');
                    toggleWeekButton.innerHTML = '<i class="bi bi-calendar-week"></i> Week View';
                }
            });
        }
        
        // Show event details in side panel
        function showEventDetails(event) {
            
            // Format date for display
            const eventDate = new Date(event.start);
            const formattedDate = eventDate.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' });
            
            // Build details HTML
            if (appointmentDetailsEl) {
                appointmentDetailsEl.innerHTML = `
                    <div class="detail-section">
                        <div class="section-title"><i class="bi bi-person-fill"></i> Beneficiary</div>
                        <div class="detail-value">${event.extendedProps.beneficiary}</div>
                    </div>
                    
                    <div class="detail-section">
                        <div class="section-title"><i class="bi bi-person-badge-fill"></i> Care Worker</div>
                        <div class="detail-value">${event.extendedProps.care_worker}</div>
                    </div>
                    
                    <div class="detail-section">
                        <div class="section-title"><i class="bi bi-calendar-date-fill"></i> Visit Details</div>
                        <div class="detail-item">
                            <div class="detail-label">Date:</div>
                            <div class="detail-value">${formattedDate}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Time:</div>
                            <div class="detail-value">${event.extendedProps.is_flexible_time ? 'Flexible Time' : 
                                (formatTime(event.start) + ' - ' + formatTime(event.end))}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Type:</div>
                            <div class="detail-value">${event.extendedProps.visit_type}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Status:</div>
                            <div class="detail-value">${getStatusBadge(event.extendedProps.status)}</div>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <div class="section-title"><i class="bi bi-geo-alt-fill"></i> Location</div>
                        <div class="detail-value">${event.extendedProps.address || 'Not Available'}</div>
                    </div>
                    
                    ${event.extendedProps.notes ? `
                    <div class="detail-section">
                        <div class="section-title"><i class="bi bi-journal-text"></i> Notes</div>
                        <div class="detail-value">${event.extendedProps.notes}</div>
                    </div>
                    ` : ''}
                    
                    ${event.extendedProps.cancel_reason ? `
                    <div class="detail-section">
                        <div class="section-title"><i class="bi bi-exclamation-triangle-fill"></i> Cancellation Reason</div>
                        <div class="detail-value">${event.extendedProps.cancel_reason}</div>
                    </div>
                    ` : ''}
                `;
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
                
                // Show loading state
                const originalBtnHtml = submitAppointment.innerHTML;
                submitAppointment.disabled = true;
                submitAppointment.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                
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
                            showToast('Success', 
                                visitationId ? 'Appointment updated successfully!' : 'Appointment created successfully!', 
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
                                    url: '{{ route("care-worker.careworker.appointments.get") }}',
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
                                showErrorModal(response.message || 'An error occurred while saving the appointment.');
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error('Error submitting form:', xhr);
                        
                        if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                            showValidationErrors(xhr.responseJSON.errors);
                        } else {
                            showErrorModal('An error occurred while saving the appointment. Please try again.');
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
        
        // Helper function to format date for API
        function formatDateForAPI(date) {
            if (!date) return '';
            
            const d = new Date(date);
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            
            return `${year}-${month}-${day}`;
        }
        
        // Show error modal
        function showErrorModal(message) {
            const errorMessage = document.getElementById('errorMessage');
            if (errorMessage) {
                errorMessage.innerHTML = message;
            }
            
            errorModal.show();
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