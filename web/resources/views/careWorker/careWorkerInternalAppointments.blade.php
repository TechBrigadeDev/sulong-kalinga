<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Internal Appointments</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <style>

        /* Add these styles to your CSS section to ensure checkboxes are visible */
        .attendee-option {
            padding: 8px 12px;
            display: flex;
            align-items: center;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.15s ease;
        }
        
        .attendee-option:hover {
            background-color: #f0f4ff;
        }
        
        .attendee-option.selected {
            background-color: #e9ecff;
        }
        
        .attendee-checkbox {
            margin-right: 10px;
            min-width: 16px;
            min-height: 16px;
            opacity: 1 !important;
            position: static !important;
            pointer-events: auto !important;
            visibility: visible !important;
            display: inline-block !important;
            border: 1px solid #adb5bd;
        }

        /* Card Design */
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
        
        .fc-event-main {
            display: flex;
            flex-direction: column;
            padding: 4px 0;
        }
        
        .event-title {
            font-weight: 600;
            font-size: 0.85rem;
            white-space: normal !important;
            line-height: 1.3;
        }
        
        .event-details {
            font-size: 0.75rem;
            line-height: 1.3;
            white-space: normal !important;
        }
        
        .event-time {
            display: inline-flex;
            align-items: center;
            font-size: 0.7rem;
            font-weight: 500;
            margin-top: 2px;
        }
        
        .event-time i {
            margin-right: 3px;
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
            width: 100px;
            font-size: 0.85rem;
            color: #666;
        }
        
        .detail-value {
            font-size: 0.85rem;
            flex: 1;
        }
        
        /* Attendees Multi-Select */
        .attendees-container {
            position: relative;
            margin-bottom: 1.2rem;
        }
        
        .attendees-input-container {
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 0.5rem;
            background-color: #fff;
            min-height: 42px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            cursor: text;
        }
        
        .attendees-input-container:focus-within {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }
        
        .attendee-tag {
            background-color: #e9ecef;
            border-radius: 4px;
            display: flex;
            align-items: center;
            padding: 2px 8px;
            font-size: 0.8rem;
        }
        
        .attendee-remove {
            margin-left: 6px;
            cursor: pointer;
            color: #6c757d;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }
        
        .attendee-remove:hover {
            color: #dc3545;
        }
        
        .attendees-input {
            flex: 1;
            border: none;
            outline: none;
            font-size: 0.9rem;
            min-width: 100px;
        }
        
        .attendees-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 0 0 6px 6px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }
        
        .attendee-option {
            padding: 0.5rem 0.75rem;
            cursor: pointer;
            display: flex;
            align-items: center;
        }
        
        .attendee-option:hover {
            background-color: #f8f9fa;
        }
        
        .attendee-checkbox {
            margin-right: 8px;
        }
        
        .attendee-option.selected {
            background-color: #e9ecef;
        }
        
        .other-field-container {
            display: none;
            margin-top: 1rem;
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

        .attendees-container {
            position: relative;
            margin-bottom: 1.2rem;
        }

        .attendees-input-container {
            border: 1px solid #ced4da;
            border-radius: 6px;
            padding: 0.5rem;
            background-color: #fff;
            min-height: 42px;
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            cursor: text;
        }

        .attendees-input-container.disabled {
            background-color: #f8f9fa;
            opacity: 0.7;
            cursor: not-allowed;
        }

        .attendees-input {
            flex: 1;
            border: none;
            outline: none;
            font-size: 0.9rem;
            min-width: 100px;
            background-color: transparent;
        }

        .attendees-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background-color: #fff;
            border: 1px solid #ced4da;
            border-radius: 0 0 6px 6px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            max-height: 200px;
            overflow-y: auto;
            z-index: 1000;
            display: none;
        }

        .dropdown-section {
            border-bottom: 1px solid #f0f0f0;
            padding-bottom: 8px;
            margin-bottom: 8px;
        }

        .dropdown-header {
            font-weight: 600;
            padding: 8px 12px;
            color: #4e73df;
            font-size: 0.85rem;
        }

        .attendee-option {
            padding: 6px 12px;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .attendee-option:hover {
            background-color: #f8f9fa;
        }

        .attendee-checkbox {
            margin-right: 8px;
        }

        .attendee-tag {
            background-color: #e9ecef;
            border-radius: 4px;
            display: flex;
            align-items: center;
            padding: 2px 8px;
            font-size: 0.8rem;
        }

        .attendee-remove {
            margin-left: 6px;
            cursor: pointer;
            color: #6c757d;
            font-size: 0.9rem;
            display: flex;
            align-items: center;
        }

        .attendee-remove:hover {
            color: #dc3545;
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
        
        /* Responsive Adjustments */
        @media (max-width: 767.98px) {
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
            .detail-label {
                width: 85px;
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
        
        /* Tooltip Styling */
        .tooltip-inner {
            max-width: 300px;
            padding: 10px 12px;
            text-align: left;
            background-color: #343a40;
            border-radius: 6px;
        }
        
       /* Color coding for different appointment types */
        .event-quarterly-feedback {
            background-color: #4e73df !important;
            border-color: #4668cc !important;
        }

        .event-skills-enhancement {
            background-color: #1cc88a !important;
            border-color: #19b77d !important;
        }

        .event-council {
            background-color: #36b9cc !important;
            border-color: #31a8ba !important;
        }

        .event-health-board {
            background-color: #f6c23e !important;
            border-color: #e4b138 !important;
        }

        .event-liga {
            background-color: #e74a3b !important;
            border-color: #d93a2b !important;
        }

        .event-hmo {
            background-color: #6f42c1 !important;
            border-color: #643ab0 !important;
        }

        .event-assessment {
            background-color: #fd7e14 !important;
            border-color: #e77014 !important;
        }

        .event-careplan {
            background-color: #20c997 !important;
            border-color: #1cb888 !important;
        }

        .event-team {
            background-color: #3949ab !important;
            border-color: #303f9f !important;
        }

        .event-mentoring {
            background-color: #ec407a !important;
            border-color: #d81b60 !important;
        }

        .event-other {
            background-color: #a435f0 !important;
            border-color: #9922e8 !important;
        }

        .fc-event.canceled {
            opacity: 0.7;
            background-color: #6c757d !important;  /* Gray color for canceled */
            border-color: #6c757d !important;
            color: white !important;
            text-decoration: line-through;
        }

        .fc-event.canceled .fc-event-title {
            text-decoration: line-through;
        }

    </style>
</head>
<body>

    @include('components.careWorkerNavbar')
    @include('components.careWorkerSidebar')

    <div class="home-section">
        <div class="text-left">INTERNAL APPOINTMENTS</div>
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
                                        <i class="bi bi-calendar3"></i> Internal Appointment Calendar
                                    </h5>
                                    <div class="calendar-actions d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="resetCalendarButton">
                                            <i class="bi bi-arrow-clockwise"></i> Reset
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="toggleWeekView">
                                            <i class="bi bi-calendar-week"></i> Week View
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div id="calendar-container">
                                        <div id="calendar" class="p-3"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Details Column -->
                        <div class="col-lg-4 col-md-5">
                            <!-- Search Bar -->
                            <div class="search-container">
                                <input type="text" class="form-control search-input" placeholder="     Search appointments..." aria-label="Search appointments">
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

    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <!-- FullCalendar JS -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {

            let originalIsRecurring = false;

            // Find the button that opens the "Add Appointment" modal
            const scheduleNewButton = document.querySelector('.action-btn[data-bs-toggle="modal"][data-bs-target="#addAppointmentModal"]');

        var calendarEl = document.getElementById('calendar');
        var appointmentDetailsEl = document.getElementById('appointmentDetails');
        var currentEvent = null;
        var toggleWeekButton = document.getElementById('toggleWeekView');
        var currentView = 'dayGridMonth';
        var csrf_token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        document.getElementById('resetCalendarButton').addEventListener('click', function() {
            // Clear search input
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {
                searchInput.value = '';
            }
            
            // Reset calendar view to month if not already
            if (currentView !== 'dayGridMonth') {
                calendar.changeView('dayGridMonth');
                toggleWeekButton.innerHTML = '<i class="bi bi-calendar-week"></i> Week View';
                currentView = 'dayGridMonth';
            }
            
            // First remove all events (like in CareworkerAppointments)
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
                    <p class="mt-3 mb-0">Select an appointment to view details</p>
                </div>
            `;
            
            // Show success message
            showToast('Success', 'Calendar reset successfully', 'success');
        });

        // Add the toast function from CareworkerAppointments
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
            toast.show(); // Add this line
        }

        
        // Initialize calendar
        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: currentView,
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            eventDisplay: 'block',
            events: function(info, successCallback, failureCallback) {
                // Fetch events from server based on date range
                fetch('/care-worker/internal-appointments/get-appointments?start=' + info.startStr + '&end=' + info.endStr + '&view_type=' + currentView, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf_token
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Failed to load appointments');
                    }
                    return response.json();
                })
                .then(data => {
                    successCallback(data);
                })
                .catch(error => {
                    console.error('Error loading appointments:', error);
                    failureCallback(error);
                });
            },
            eventContent: function(arg) {
                const event = arg.event;
                const timeFormat = { hour: '2-digit', minute: '2-digit', hour12: true };
                const startTime = event.start ? event.start.toLocaleTimeString([], timeFormat) : '';
                const endTime = event.end ? event.end.toLocaleTimeString([], timeFormat) : '';
                const isFlexibleTime = event.extendedProps.is_flexible_time;
                const timeText = isFlexibleTime ? 'Flexible' : (startTime && endTime ? `${startTime} - ${endTime}` : startTime);
                
                let eventEl = document.createElement('div');
                eventEl.className = 'fc-event-main';
                
                if (arg.view.type === 'dayGridMonth') {
                    // Simplified view for month
                    eventEl.innerHTML = `
                        <div class="event-title">${event.title}</div>
                        <div class="event-details">
                            <div class="event-time"><i class="bi bi-clock"></i> ${isFlexibleTime ? 'Flexible' : startTime}</div>
                        </div>
                    `;
                } else {
                    // Detailed view for week/day
                    eventEl.innerHTML = `
                        <div class="event-title">${event.title}</div>
                        <div class="event-details">
                            <div class="event-time"><i class="bi bi-clock"></i> ${isFlexibleTime ? 'Flexible' : timeText}</div>
                            <div class="event-location"><i class="bi bi-geo-alt"></i> ${event.extendedProps.meeting_location || 'No location'}</div>
                        </div>
                    `;
                }
                return { domNodes: [eventEl] };
            },
            eventDidMount: function(arg) {
                // 1. Apply special styling for canceled events (case-insensitive check)
                const status = arg.event.extendedProps.status?.toLowerCase() || '';
                if (status === 'canceled') {
                    arg.el.classList.add('canceled');
                }
                
                // 2. Apply tooltips (from your second handler)
                if (arg.el) {
                    const event = arg.event;
                    const isFlexibleTime = event.extendedProps.is_flexible_time;
                    
                    const timeFormat = { hour: '2-digit', minute: '2-digit', hour12: true };
                    const startTime = event.start ? event.start.toLocaleTimeString([], timeFormat) : '';
                    const endTime = event.end ? event.end.toLocaleTimeString([], timeFormat) : '';
                    
                    const timeText = isFlexibleTime ? 'Flexible Scheduling' : 
                        (startTime && endTime ? `${startTime} - ${endTime}` : startTime);
                    
                    let tooltipTitle = `${event.title}\n` +
                                `Time: ${timeText}\n` +
                                `Location: ${event.extendedProps.meeting_location || ''}`;
                    
                    // Apply tooltip
                    arg.el.setAttribute('data-bs-toggle', 'tooltip');
                    arg.el.setAttribute('data-bs-placement', 'top');
                    arg.el.setAttribute('title', tooltipTitle);
                    new bootstrap.Tooltip(arg.el);
                }
            },
            eventClick: function(info) {
                // Save current event for editing/deletion
                currentEvent = info.event;
                
                // Display full details in the side panel
                const event = info.event;
                
                // Format participants list - WITH DEDUPLICATION
                let attendeesList = '';
                if (event.extendedProps.participants && event.extendedProps.participants.length > 0) {
                    // Deduplicate participants first
                    const uniqueParticipants = deduplicateParticipants(event.extendedProps.participants);
                    
                    attendeesList = uniqueParticipants.map(p => 
                        `<li>${p.name} ${p.is_organizer ? '<span class="badge bg-info">Organizer</span>' : ''}</li>`
                    ).join('');
                } else {
                    attendeesList = '<li>No attendees specified</li>';
                }
                
                // Check if recurring
                const isRecurring = event.extendedProps.recurring;
                let recurringInfo = '';
                if (isRecurring && event.extendedProps.recurring_pattern) {
                    const pattern = event.extendedProps.recurring_pattern;
                    const patternTypes = {
                        'daily': 'Daily',
                        'weekly': 'Weekly',
                        'monthly': 'Monthly'
                    };
                    
                    recurringInfo = `
                    <div class="detail-item">
                        <span class="detail-label">Recurrence:</span>
                        <span class="detail-value">${patternTypes[pattern.type] || 'Custom'}</span>
                    </div>
                    ${pattern.recurrence_end ? `
                    <div class="detail-item">
                        <span class="detail-label">Until:</span>
                        <span class="detail-value">${new Date(pattern.recurrence_end).toLocaleDateString()}</span>
                    </div>
                    ` : ''}`;
                }
                
                appointmentDetailsEl.innerHTML = `
                    <div class="detail-section">
                        <div class="section-title"><i class="bi bi-calendar-event"></i> Appointment</div>
                        <h5 class="mb-2">${event.title}</h5>
                        <div class="detail-item">
                            <span class="detail-label">Type:</span>
                            <span class="detail-value">${event.extendedProps.type}</span>
                        </div>
                        ${event.extendedProps.other_type_details ? `
                        <div class="detail-item">
                            <span class="detail-label">Details:</span>
                            <span class="detail-value">${event.extendedProps.other_type_details}</span>
                        </div>
                        ` : ''}
                        <div class="detail-item">
                            <span class="detail-label">Status:</span>
                            <span class="detail-value">
                                <span class="badge ${event.extendedProps.status === 'Scheduled' ? 'bg-primary' : 
                                                event.extendedProps.status === 'Completed' ? 'bg-success' : 
                                                event.extendedProps.status === 'Canceled' ? 'bg-danger' : 'bg-secondary'}">
                                    ${event.extendedProps.status}
                                </span>
                            </span>
                        </div>
                    </div>
                    
                    <div class="detail-section">
                        <div class="section-title"><i class="bi bi-clock"></i> Schedule</div>
                        <div class="detail-item">
                            <span class="detail-label">Date:</span>
                            <span class="detail-value">${event.start ? event.start.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : 'Not specified'}</span>
                        </div>
                        ${!event.extendedProps.is_flexible_time ? `
                            <div class="detail-item">
                                <span class="detail-label">Time:</span>
                                <span class="detail-value">${event.start ? event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true }) : ''} - ${event.end ? event.end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true }) : ''}</span>
                            </div>
                            ` : `
                            <div class="detail-item">
                                <span class="detail-label">Time:</span>
                                <span class="detail-value"><span class="badge bg-info">Flexible Scheduling</span></span>
                            </div>
                            `}
                        <div class="detail-item">
                            <span class="detail-label">Location:</span>
                            <span class="detail-value">${event.extendedProps.meeting_location || 'Not specified'}</span>
                        </div>
                        ${recurringInfo}
                    </div>
                    
                    <div class="detail-section">
                        <div class="section-title"><i class="bi bi-people"></i> Attendees</div>
                        <ul class="mb-0 ps-3">
                            ${attendeesList}
                        </ul>
                    </div>
                    
                    <div class="detail-section">
                        <div class="section-title"><i class="bi bi-journal-text"></i> Notes</div>
                        <p class="mb-0">${event.extendedProps.notes || 'No notes available'}</p>
                    </div>
                `;
            }
        });

        calendar.render();
        
        // Toggle week view button
        toggleWeekButton.addEventListener('click', function() {
            if (currentView === 'dayGridMonth') {
                calendar.changeView('timeGridWeek');
                toggleWeekButton.innerHTML = '<i class="bi bi-calendar-month"></i> Month View';
                currentView = 'timeGridWeek';
            } else {
                calendar.changeView('dayGridMonth');
                toggleWeekButton.innerHTML = '<i class="bi bi-calendar-week"></i> Week View';
                currentView = 'dayGridMonth';
            }
        });
        
        // Search functionality
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase().trim();
                
                // Get all events currently on the calendar
                const allEvents = calendar.getEvents();
                
                // If search is empty, show all events
                if (!searchTerm) {
                    allEvents.forEach(event => {
                        event.setProp('display', 'auto');
                    });
                    return;
                }
                
                // Filter events based on search term
                allEvents.forEach(event => {
                    // Get searchable data from event
                    const title = event.title ? event.title.toLowerCase() : '';
                    const type = event.extendedProps.type ? event.extendedProps.type.toLowerCase() : '';
                    const description = event.extendedProps.description ? event.extendedProps.description.toLowerCase() : '';
                    const location = event.extendedProps.meeting_location ? event.extendedProps.meeting_location.toLowerCase() : '';
                    const otherDetails = event.extendedProps.other_type_details ? event.extendedProps.other_type_details.toLowerCase() : '';
                    const date = event.start ? event.start.toLocaleDateString().toLowerCase() : '';
                    
                    // Build participants text from the array of participants
                    let participantsText = '';
                    if (event.extendedProps.participants && Array.isArray(event.extendedProps.participants)) {
                        participantsText = event.extendedProps.participants
                            .map(p => (p.name || '').toLowerCase())
                            .join(' ');
                    }
                    
                    // Check if any field contains the search term
                    const matches = 
                        title.includes(searchTerm) || 
                        type.includes(searchTerm) || 
                        description.includes(searchTerm) || 
                        location.includes(searchTerm) || 
                        otherDetails.includes(searchTerm) || 
                        participantsText.includes(searchTerm) ||
                        date.includes(searchTerm);
                    
                    // Update visibility based on match
                    if (matches) {
                        event.setProp('display', 'auto');
                    } else {
                        event.setProp('display', 'none');
                    }
                });
                
                // Show message if no results found
                const visibleEvents = calendar.getEvents().filter(e => e.display !== 'none');
                if (visibleEvents.length === 0 && searchTerm.length > 0) {
                    showToast('Search', 'No appointments match your search', 'info');
                }
            });
        }
        

        // Helper function to format time from a Date object to HH:MM format
        function formatTime(date) {
            if (!date) return '';
            const d = new Date(date);
            const hours = d.getHours().toString().padStart(2, '0');
            const minutes = d.getMinutes().toString().padStart(2, '0');
            return `${hours}:${minutes}`;
        }

        function deduplicateParticipants(participants) {
            if (!participants || !Array.isArray(participants)) return [];
            
            const uniqueMap = new Map();
            
            // Use a Map with composite key of type+id to ensure uniqueness
            participants.forEach(p => {
                if (p && p.id && p.type) {
                    const key = `${p.type}-${p.id}`;
                    uniqueMap.set(key, p);
                }
            });
            
            // Convert back to array
            return Array.from(uniqueMap.values());
        }

        
    });
    
    </script>
</body>
</html>