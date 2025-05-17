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

    @include('components.adminNavbar')
    @include('components.adminSidebar')

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
                            
                            <!-- Action Buttons -->
                            <div class="action-buttons mb-4">
                                <button type="button" class="btn btn-primary action-btn" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
                                    <i class="bi bi-plus-circle"></i> Schedule New Appointment
                                </button>
                                <button type="button" class="btn btn-outline-warning action-btn" id="editAppointmentButton" disabled>
                                    <i class="bi bi-pencil-square"></i> Edit Selected Appointment
                                </button>
                                <button type="button" class="btn btn-outline-danger action-btn" id="deleteAppointmentButton" disabled>
                                    <i class="bi bi-trash3"></i> Cancel Selected Appointment
                                </button>
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
   
    <div class="modal fade" id="addAppointmentModal" tabindex="-1" aria-labelledby="addAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAppointmentModalLabel">Add Appointment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modalErrorContainer" class="alert alert-danger mb-3" style="display: none;">
                        <h6 class="alert-heading mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Please correct the following:</h6>
                        <ul id="modalErrorList" class="mb-0 ms-3">
                            <!-- Errors will be inserted here dynamically -->
                        </ul>
                    </div>
                    
                    <div id="recurringWarningMessage" class="alert alert-warning mb-3" style="display: none;">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Note:</strong> Editing a recurring appointment will only affect this and future occurrences. 
                        Past occurrences will remain unchanged. You cannot change a recurring appointment to a single appointment or vice versa.
                    </div>
                    <form id="addAppointmentForm">
                        <input type="hidden" id="appointmentId" name="appointment_id" value="">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointmentTitle" class="form-label">Title <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="appointmentTitle" name="title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointmentType" class="form-label">Type <span class="text-danger">*</span></label>
                                    <select class="form-control" id="appointmentType" name="appointment_type_id" required>
                                        <option value="">Select type</option>
                                        @foreach($appointmentTypes as $type)
                                        <option value="{{ $type->appointment_type_id }}">{{ $type->type_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Other type details -->
                        <div class="form-group mb-3" id="otherTypeContainer" style="display: none;">
                            <label for="otherAppointmentType" class="form-label">Specify Other Type <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="otherAppointmentType" name="other_type_details">
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointmentDate" class="form-label">Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="appointmentDate" name="date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointmentPlace" class="form-label">Location <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="appointmentPlace" name="meeting_location" required>
                                </div>
                            </div>
                        </div>

                        <!-- Flexible time option -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="flexibleTimeCheck" name="is_flexible_time">
                            <label class="form-check-label" for="flexibleTimeCheck">
                                Flexible time (no specific start/end time)
                            </label>
                        </div>
                        
                        <!-- Time fields (shown when not flexible) -->
                        <div id="timeFieldsContainer" class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointmentTime" class="form-label">Start Time</label>
                                    <input type="time" class="form-control" id="appointmentTime" name="start_time">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointmentEndTime" class="form-label">End Time</label>
                                    <input type="time" class="form-control" id="appointmentEndTime" name="end_time">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recurring appointment options -->
                        <div class="form-group">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="recurringCheck" name="is_recurring">
                                <label class="form-check-label" for="recurringCheck">
                                    Recurring appointment
                                </label>
                            </div>

                            <div id="recurringOptions" class="border rounded p-3 mb-3" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">Recurrence Pattern</label>
                                    <div class="form-check">
                                        <input class="form-check-input pattern-radio" type="radio" name="pattern_type" id="patternDaily" value="daily">
                                        <label class="form-check-label" for="patternDaily">Daily</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input pattern-radio" type="radio" name="pattern_type" id="patternWeekly" value="weekly" checked>
                                        <label class="form-check-label" for="patternWeekly">Weekly</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input pattern-radio" type="radio" name="pattern_type" id="patternMonthly" value="monthly">
                                        <label class="form-check-label" for="patternMonthly">Monthly</label>
                                    </div>
                                </div>

                                <div class="mb-3" id="weeklyOptions">
                                    <label class="form-label">Repeat on:</label>
                                    <div class="d-flex flex-wrap">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" name="day_of_week[]" value="0" id="daySunday">
                                            <label class="form-check-label" for="daySunday">Sunday</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" name="day_of_week[]" value="1" id="dayMonday">
                                            <label class="form-check-label" for="dayMonday">Monday</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" name="day_of_week[]" value="2" id="dayTuesday">
                                            <label class="form-check-label" for="dayTuesday">Tuesday</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" name="day_of_week[]" value="3" id="dayWednesday">
                                            <label class="form-check-label" for="dayWednesday">Wednesday</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" name="day_of_week[]" value="4" id="dayThursday">
                                            <label class="form-check-label" for="dayThursday">Thursday</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" name="day_of_week[]" value="5" id="dayFriday">
                                            <label class="form-check-label" for="dayFriday">Friday</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" name="day_of_week[]" value="6" id="daySaturday">
                                            <label class="form-check-label" for="daySaturday">Saturday</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="recurrenceEnd" class="form-label">End Date</label>
                                    <input type="date" class="form-control" id="recurrenceEnd" name="recurrence_end">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Attendees Section -->
                        <div class="form-group mb-4">
                            <label class="form-label">Attendees</label>
                            
                            <!-- COSE Staff Attendees -->
                            <div class="mb-3">
                                <label class="form-label fw-medium">COSE Staff</label>
                                <div class="attendees-container">
                                    <div class="attendees-input-container" id="staffAttendees">
                                        <!-- Selected staff attendees will appear here as tags -->
                                        <input type="text" class="attendees-input" id="staffSearch" placeholder="Type to search for staff...">
                                    </div>
                                    <div class="attendees-dropdown staff-dropdown" id="staffDropdown">
                                        <div class="dropdown-section">
                                            <div class="dropdown-header">Administrators</div>
                                            @foreach($usersByRole['administrators'] as $admin)
                                            <div class="attendee-option" data-id="{{ $admin->id }}" data-type="cose_user">
                                                <input type="checkbox" class="attendee-checkbox" name="participants[cose_user][]" value="{{ $admin->id }}">
                                                <span>{{ $admin->first_name }} {{ $admin->last_name }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                        <div class="dropdown-section">
                                            <div class="dropdown-header">Care Managers</div>
                                            @foreach($usersByRole['care_managers'] as $manager)
                                            <div class="attendee-option" data-id="{{ $manager->id }}" data-type="cose_user">
                                                <input type="checkbox" class="attendee-checkbox" name="participants[cose_user][]" value="{{ $manager->id }}">
                                                <span>{{ $manager->first_name }} {{ $manager->last_name }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                        <div class="dropdown-section">
                                            <div class="dropdown-header">Care Workers</div>
                                            @foreach($usersByRole['care_workers'] as $worker)
                                            <div class="attendee-option" data-id="{{ $worker->id }}" data-type="cose_user">
                                                <input type="checkbox" class="attendee-checkbox" name="participants[cose_user][]" value="{{ $worker->id }}">
                                                <span>{{ $worker->first_name }} {{ $worker->last_name }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Beneficiary Attendees (disabled by default) -->
                            <div class="mb-3">
                                <label class="form-label fw-medium">Beneficiaries</label>
                                <div class="attendees-container">
                                    <div class="attendees-input-container disabled" id="beneficiaryAttendees">
                                        <input type="text" class="attendees-input" id="beneficiarySearch" placeholder="Type to search for beneficiaries..." disabled>
                                    </div>
                                    <div class="attendees-dropdown beneficiary-dropdown" id="beneficiaryDropdown">
                                        @isset($beneficiaries)
                                            @foreach($beneficiaries as $beneficiary)
                                            <div class="attendee-option" data-id="{{ $beneficiary->beneficiary_id }}" data-type="beneficiary">
                                                <input type="checkbox" class="attendee-checkbox" name="participants[beneficiary][]" value="{{ $beneficiary->beneficiary_id }}" disabled>
                                                <span>{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</span>
                                            </div>
                                            @endforeach
                                        @endisset
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Family Member Attendees (disabled by default) -->
                            <div class="mb-3">
                                <label class="form-label fw-medium">Family Members</label>
                                <div class="attendees-container">
                                    <div class="attendees-input-container disabled" id="familyAttendees">
                                        <input type="text" class="attendees-input" id="familySearch" placeholder="Type to search for family members..." disabled>
                                    </div>
                                    <div class="attendees-dropdown family-dropdown" id="familyDropdown">
                                        @isset($familyMembers)
                                            @foreach($familyMembers as $family)
                                            <div class="attendee-option" data-id="{{ $family->family_member_id }}" data-type="family_member">
                                                <input type="checkbox" class="attendee-checkbox" name="participants[family_member][]" value="{{ $family->family_member_id }}" disabled>
                                                <span>{{ $family->first_name }} {{ $family->last_name }}</span>
                                            </div>
                                            @endforeach
                                        @endisset
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Notes -->
                        <div class="form-group mb-3">
                            <label for="appointmentNotes" class="form-label">Notes</label>
                            <textarea class="form-control" id="appointmentNotes" name="notes" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="submitAppointment"><i class="bi bi-plus-circle"></i> Create Appointment</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancellation Modal -->
    <!-- Update the cancelModal to match careworker styling -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="cancelModalLabel"><i class="bi bi-trash-fill"></i> Cancel Appointment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <p class="mb-1" id="cancelAppointmentDetails"></p>
                    </div>
                    
                    <div id="recurringCancelOptions" class="mb-3 border rounded p-3 bg-light" style="display:none;">
                        <p class="mb-2"><strong>Cancellation Options:</strong></p>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="cancelOption" id="cancelSingle" value="single" checked>
                            <label class="form-check-label" for="cancelSingle">
                                Cancel only this occurrence
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cancelOption" id="cancelFuture" value="future">
                            <label class="form-check-label" for="cancelFuture">
                                Cancel this and all future occurrences
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="cancelReason" class="form-label">Reason for Cancellation</label>
                        <textarea class="form-control" id="cancelReason" rows="3" placeholder="Please provide a reason for cancellation..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cancelPassword" class="form-label">Confirm your password</label>
                        <input type="password" class="form-control" id="cancelPassword" placeholder="Enter your password">
                        <div id="passwordError" class="text-danger mt-1"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="confirmCancel">Confirm Cancellation</button>
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

            // Remove the automatic modal trigger attributes
            scheduleNewButton.removeAttribute('data-bs-toggle');
            scheduleNewButton.removeAttribute('data-bs-target');

            // Add a click handler that properly resets everything
            scheduleNewButton.addEventListener('click', function() {
                // Reset form completely
                resetAppointmentForm();
                
                // Reset modal title
                document.getElementById('addAppointmentModalLabel').innerHTML = '<i class="bi bi-plus-circle"></i> Add New Appointment';
                
                // Reset submit button text
                document.getElementById('submitAppointment').innerHTML = '<i class="bi bi-plus-circle"></i> Create Appointment';
                
                // Clear any appointment ID
                document.getElementById('appointmentId').value = '';
                
                // Set current date in the date field
                document.getElementById('appointmentDate').valueAsDate = new Date();
                
                // Reset the recurring checkbox (and ensure it's enabled for new appointments)
                const recurringCheckbox = document.getElementById('recurringCheck');
                recurringCheckbox.checked = false;
                recurringCheckbox.disabled = false;
                
                // Remove any lock icon added during edit
                const checkboxLabel = recurringCheckbox.nextElementSibling;
                if (checkboxLabel) {
                    checkboxLabel.textContent = 'Recurring appointment'; // Reset to original text
                }
                
                // Clear current event selection
                currentEvent = null;
                
                // Hide recurring options and warning
                document.getElementById('recurringOptions').style.display = 'none';
                document.getElementById('recurringWarningMessage').style.display = 'none';
                
                // Show the modal
                addAppointmentModal.show();
            });

            // Listen for modal close event to reset the form to "add mode"
            document.getElementById('addAppointmentModal').addEventListener('hidden.bs.modal', function() {
                // Reset form
                resetAppointmentForm();
                
                // Reset modal title to "Add New Appointment"
                document.getElementById('appointmentModalLabel').innerHTML = '<i class="bi bi-plus-circle"></i> Add New Appointment';
                
                // Reset submit button text
                document.getElementById('submitAppointment').innerHTML = '<i class="bi bi-plus-circle"></i> Create Appointment';

                // Reset appointment ID to ensure we're in "create" mode
                document.getElementById('appointmentId').value = '';
                
                // THIS IS THE CRITICAL LINE THAT'S MISSING:
                currentEvent = null;
            });

        var calendarEl = document.getElementById('calendar');
        var appointmentDetailsEl = document.getElementById('appointmentDetails');
        var addAppointmentForm = document.getElementById('addAppointmentForm');
        var addAppointmentModal = new bootstrap.Modal(document.getElementById('addAppointmentModal'));
        var cancelModal = new bootstrap.Modal(document.getElementById('cancelModal'));
        var editButton = document.getElementById('editAppointmentButton');
        var deleteButton = document.getElementById('deleteAppointmentButton');
        var toggleWeekButton = document.getElementById('toggleWeekView');
        var recurringCheck = document.getElementById('recurringCheck');
        var recurringOptions = document.getElementById('recurringOptions');
        var weeklyOptions = document.getElementById('weeklyOptions');
        var currentEvent = null;
        var currentView = 'dayGridMonth';
        var csrf_token = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Set default date to today
        document.getElementById('appointmentDate').valueAsDate = new Date();
        
        // Recurring options toggle
        recurringCheck.addEventListener('change', function() {
            recurringOptions.style.display = this.checked ? 'block' : 'none';
        });
        
        // Pattern type selection
        const patternRadios = document.querySelectorAll('.pattern-radio');
        patternRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                weeklyOptions.style.display = this.value === 'weekly' ? 'block' : 'none';
            });
        });
        
        // Handle appointment type change
        document.getElementById('appointmentType').addEventListener('change', function() {
            const selectedType = this.value;
            const selectedTypeText = this.options[this.selectedIndex].text;
            
            // Show/hide other field based on selection
            document.getElementById('otherTypeContainer').style.display = 
                selectedTypeText === 'Others' ? 'block' : 'none';
            
            // Enable/disable beneficiary and family attendance based on type
            // Now checking by text content for greater reliability
            const isAssessmentOrOther = selectedTypeText === 'Assessment and Review of Care Needs' || selectedTypeText === 'Others';
            
            // Beneficiary options
            const beneficiaryAttendees = document.getElementById('beneficiaryAttendees');
            const beneficiarySearch = document.getElementById('beneficiarySearch');
            const beneficiaryCheckboxes = document.querySelectorAll('input[name="participants[beneficiary][]"]');
            
            beneficiaryAttendees.classList.toggle('disabled', !isAssessmentOrOther);
            beneficiarySearch.disabled = !isAssessmentOrOther;
            beneficiaryCheckboxes.forEach(checkbox => {
                checkbox.disabled = !isAssessmentOrOther;
            });
        });
        
        // Handle appointment type change
        document.getElementById('appointmentType').addEventListener('change', function() {
            const selectedType = this.value;
            
            // Family member options
            const familyAttendees = document.getElementById('familyAttendees');
            const familySearch = document.getElementById('familySearch');
            const familyCheckboxes = document.querySelectorAll('input[name="participants[family_member][]"]');
            
            familyAttendees.classList.toggle('disabled', !isAssessmentOrOther);
            familySearch.disabled = !isAssessmentOrOther;
            familyCheckboxes.forEach(checkbox => {
                checkbox.disabled = !isAssessmentOrOther;
            });
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
        });
        
        // Setup attendee search and selection for each participant type
        setupAttendeeSearch('staffSearch', 'staffDropdown', 'staffAttendees');
        setupAttendeeSearch('beneficiarySearch', 'beneficiaryDropdown', 'beneficiaryAttendees');
        setupAttendeeSearch('familySearch', 'familyDropdown', 'familyAttendees');
        
        function setupAttendeeSearch(searchId, dropdownId, containerDivId) {
            const searchInput = document.getElementById(searchId);
            const dropdown = document.getElementById(dropdownId);
            const containerDiv = document.getElementById(containerDivId);
            
            toast.show();
            
            // Remove from DOM after hiding
            toastElement.addEventListener('hidden.bs.toast', function() {
                this.remove();
            });
        }
        
        // Setup attendee search and selection for each participant type
        setupAttendeeSearch('staffSearch', 'staffDropdown', 'staffAttendees');
        setupAttendeeSearch('beneficiarySearch', 'beneficiaryDropdown', 'beneficiaryAttendees');
        setupAttendeeSearch('familySearch', 'familyDropdown', 'familyAttendees');
        
        function setupAttendeeSearch(searchId, dropdownId, containerDivId) {
            const searchInput = document.getElementById(searchId);
            const dropdown = document.getElementById(dropdownId);
            const containerDiv = document.getElementById(containerDivId);
            
            if (!searchInput || !dropdown || !containerDiv) return;
            
            // Show dropdown when clicking on the container
            containerDiv.addEventListener('click', function(e) {
                if (!searchInput.disabled) {
                    dropdown.style.display = 'block';
                    searchInput.focus();
                }
            });
            
            // Hide dropdown when clicking outside
            document.addEventListener('click', function(e) {
                if (!containerDiv.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });
            
            // Filter options based on search input
            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const options = dropdown.querySelectorAll('.attendee-option');
                
                options.forEach(option => {
                    const name = option.querySelector('span').textContent.toLowerCase();
                    option.style.display = name.includes(searchTerm) ? 'block' : 'none';
                });
            });
            
            // Handle checkbox selection
            const checkboxes = dropdown.querySelectorAll('.attendee-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    const option = this.closest('.attendee-option');
                    const id = option.dataset.id;
                    const type = option.dataset.type;
                    const name = option.querySelector('span').textContent;
                    
                    if (this.checked) {
                        addAttendeeTag(containerDiv, id, type, name);
                    } else {
                        removeAttendeeTag(containerDiv, id, type);
                    }
                });
            });
        }
        
        // Add attendee tag to the container
        function addAttendeeTag(container, id, type, name) {
            // Check if tag already exists
            if (container.querySelector(`.attendee-tag[data-id="${id}"][data-type="${type}"]`)) {
                return;
            }
            
            const tag = document.createElement('div');
            tag.className = 'attendee-tag';
            tag.dataset.id = id;
            tag.dataset.type = type;
            tag.innerHTML = `
                ${name}
                <span class="attendee-remove"><i class="bi bi-x"></i></span>
            `;
            
            // Add remove event
            tag.querySelector('.attendee-remove').addEventListener('click', function(e) {
                e.stopPropagation();
                const tagElement = this.closest('.attendee-tag');
                const id = tagElement.dataset.id;
                const type = tagElement.dataset.type;
                
                // Uncheck the corresponding checkbox
                const dropdown = container.nextElementSibling;
                const checkbox = dropdown.querySelector(`.attendee-option[data-id="${id}"][data-type="${type}"] input`);
                if (checkbox) checkbox.checked = false;
                
                // Remove the tag
                tagElement.remove();
            });
            
            // Add before the input
            const input = container.querySelector('input');
            container.insertBefore(tag, input);
        }
        
        // Remove attendee tag from the container
        function removeAttendeeTag(container, id, type) {
            const tag = container.querySelector(`.attendee-tag[data-id="${id}"][data-type="${type}"]`);
            if (tag) {
                tag.remove();
            }
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
                fetch('/admin/internal-appointments/get-appointments?start=' + info.startStr + '&end=' + info.endStr + '&view_type=' + currentView, {
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
                const timeText = startTime && endTime ? `${startTime} - ${endTime}` : '';
                
                let eventEl = document.createElement('div');
                eventEl.className = 'fc-event-main';
                
                if (arg.view.type === 'dayGridMonth') {
                    // Simplified view for month
                    eventEl.innerHTML = `
                        <div class="event-title">${event.title}</div>
                        <div class="event-details">
                            <div class="event-time"><i class="bi bi-clock"></i> ${startTime}</div>
                        </div>
                    `;
                } else {
                    // Detailed view for week/day
                    eventEl.innerHTML = `
                        <div class="event-title">${event.title}</div>
                        <div class="event-details">
                            <div class="event-time"><i class="bi bi-clock"></i> ${timeText}</div>
                            <div class="event-location"><i class="bi bi-geo-alt"></i> ${event.extendedProps.meeting_location || ''}</div>
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
                    const timeFormat = { hour: '2-digit', minute: '2-digit', hour12: true };
                    const startTime = event.start ? event.start.toLocaleTimeString([], timeFormat) : '';
                    const endTime = event.end ? event.end.toLocaleTimeString([], timeFormat) : '';
                    const timeText = startTime && endTime ? `${startTime} - ${endTime}` : startTime;
                    
                    let tooltipTitle = `${event.title}\n` +
                                    `Time: ${timeText}\n` +
                                    `Location: ${event.extendedProps.meeting_location || ''}`;
                    
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
                
                // Format participants list
                let attendeesList = '';
                if (event.extendedProps.participants && event.extendedProps.participants.length > 0) {
                    attendeesList = event.extendedProps.participants.map(p => 
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
                            <span class="detail-value"><span class="badge bg-warning text-dark">Flexible</span></span>
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

                const status = event.extendedProps.status?.toLowerCase() || '';
                const isEditable = status !== 'completed' && status !== 'canceled';
                
                // Enable edit and delete buttons only if event is editable
                // First check if can_edit property is true, then check if status allows editing
                editButton.disabled = !event.extendedProps.can_edit || !isEditable;
                deleteButton.disabled = !event.extendedProps.can_edit || !isEditable;
                
                // Add visual indicator if buttons are disabled due to status
                if (!isEditable) {
                    editButton.title = `Cannot edit ${status} appointments`;
                    deleteButton.title = `Cannot cancel ${status} appointments`;
                } else {
                    editButton.title = '';
                    deleteButton.title = '';
                }
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

        function showModalErrors(errors) {
            const errorContainer = document.getElementById('modalErrorContainer');
            const errorList = document.getElementById('modalErrorList');
            
            // Clear previous errors
            errorList.innerHTML = '';
            
            // Add each error as a list item
            errors.forEach(error => {
                const li = document.createElement('li');
                li.textContent = error;
                errorList.appendChild(li);
            });
            
            // Show the error container
            errorContainer.style.display = 'block';
            
            // Scroll to the top of the modal
            const modalBody = errorContainer.closest('.modal-body');
            if (modalBody) {
                modalBody.scrollTop = 0;
            }
        }

        function hideModalErrors() {
            const errorContainer = document.getElementById('modalErrorContainer');
            const errorList = document.getElementById('modalErrorList');
            
            // Clear error list
            errorList.innerHTML = '';
            
            // Hide the container
            errorContainer.style.display = 'none';
            
            // Remove invalid feedback from all fields
            document.querySelectorAll('.is-invalid').forEach(field => {
                field.classList.remove('is-invalid');
            });
        }
        
        function resetAppointmentForm() {
            if (!addAppointmentForm) return; // Add this safety check

            // Hide any displayed errors
            hideModalErrors();
            
            addAppointmentForm.reset();
            
            const appointmentId = document.getElementById('appointmentId');
            if (appointmentId) appointmentId.value = '';
            
            // Reset attendance tags
            document.querySelectorAll('#staffAttendees .attendee-tag, #beneficiaryAttendees .attendee-tag, #familyAttendees .attendee-tag')
                .forEach(tag => tag.remove());
            
            // Reset checkboxes
            document.querySelectorAll('.attendee-checkbox').forEach(checkbox => {
                checkbox.checked = false;
            });
            
            // Hide conditional sections
            const otherTypeContainer = document.getElementById('otherTypeContainer');
            if (otherTypeContainer) otherTypeContainer.style.display = 'none';
            
            const recurringOptions = document.getElementById('recurringOptions');
            if (recurringOptions) recurringOptions.style.display = 'none';
            
            // Reset beneficiary and family selection states
            const beneficiaryAttendees = document.getElementById('beneficiaryAttendees');
            const beneficiarySearch = document.getElementById('beneficiarySearch');
            if (beneficiaryAttendees) beneficiaryAttendees.classList.add('disabled');
            if (beneficiarySearch) beneficiarySearch.disabled = true;
            
            // Reset submit button
            const submitButton = document.getElementById('submitAppointment');
            if (submitButton) {
                submitButton.innerHTML = '<i class="bi bi-plus-circle"></i> Create Appointment';
            }
        }
        
        // Search functionality
        const searchInput = document.querySelector('.search-input');
        searchInput.addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            
            if (searchTerm.length < 2) {
                // If search term is too short, show all events
                calendar.getEvents().forEach(event => {
                    event.setProp('display', 'auto');
                });
                return;
            }
            
            // Filter events
            calendar.getEvents().forEach(event => {
                const title = event.title.toLowerCase();
                const type = (event.extendedProps.type || '').toLowerCase();
                const location = (event.extendedProps.meeting_location || '').toLowerCase();
                const notes = (event.extendedProps.notes || '').toLowerCase();
                
                // Search in participants
                let participantsText = '';
                if (event.extendedProps.participants) {
                    participantsText = event.extendedProps.participants.map(p => p.name.toLowerCase()).join(' ');
                }
                
                if (title.includes(searchTerm) || 
                    type.includes(searchTerm) || 
                    location.includes(searchTerm) || 
                    notes.includes(searchTerm) || 
                    participantsText.includes(searchTerm)) {
                    event.setProp('display', 'auto');
                } else {
                    event.setProp('display', 'none');
                }
            });
        });
        
        // Edit button click handler
        editButton.addEventListener('click', function() {
            if (!currentEvent) {
                console.log('No event selected');
                return;
            }
            
            // Set editing flag to true
            const isEditing = true;
            
            try {
                // Reset form
                resetAppointmentForm();

                // Hide any displayed errors
                hideModalErrors();
                
                // Hide recurring warning message
                document.getElementById('recurringWarningMessage').style.display = 'none';
                
                // Set modal title
                const modalTitle = document.getElementById('addAppointmentModalLabel');
                if (modalTitle) {
                    modalTitle.textContent = 'Edit Appointment';
                } else {
                    console.warn('Modal title element not found');
                }
                
                // Show warning for recurring appointments
                const isRecurring = !!currentEvent.extendedProps.recurring_pattern;
                document.getElementById('recurringWarningMessage').style.display = 
                    (isEditing && isRecurring) ? 'block' : 'none';
                    
                // Fill form with event data
                const form = document.getElementById('addAppointmentForm');
                
                // Set hidden appointment ID field
                document.getElementById('appointmentId').value = currentEvent.extendedProps.appointment_id;
                console.log("Setting appointment_id to:", currentEvent.extendedProps.appointment_id);
                
                // Fill basic details
                document.getElementById('appointmentTitle').value = currentEvent.title;
                document.getElementById('appointmentType').value = currentEvent.extendedProps.type_id;
                document.getElementById('appointmentDate').value = currentEvent.startStr.split('T')[0];
                document.getElementById('appointmentPlace').value = currentEvent.extendedProps.meeting_location || '';
                document.getElementById('appointmentNotes').value = currentEvent.extendedProps.notes || '';
                
                // Handle flexible time setting
                const isFlexibleTime = currentEvent.extendedProps.is_flexible_time;
                document.getElementById('flexibleTimeCheck').checked = isFlexibleTime;
                document.getElementById('timeFieldsContainer').style.display = isFlexibleTime ? 'none' : 'block';

                
                // Set time values if not flexible
                if (!isFlexibleTime && currentEvent.start && currentEvent.end) {
                    document.getElementById('appointmentTime').value = formatTime(currentEvent.start);
                    document.getElementById('appointmentEndTime').value = formatTime(currentEvent.end);
                }
                
                // Handle other type details if relevant
                if (currentEvent.extendedProps.type_id == 11) { // "Others" type
                    document.getElementById('otherTypeContainer').style.display = 'block';
                    document.getElementById('otherAppointmentType').value = currentEvent.extendedProps.other_type_details || '';
                }
                
                // Handle recurring settings
                document.getElementById('recurringCheck').checked = isRecurring;
                document.getElementById('recurringOptions').style.display = isRecurring ? 'block' : 'none';
                
                originalIsRecurring = isRecurring;
                console.log("Original recurring status stored:", originalIsRecurring);

                // DISABLE THE RECURRING CHECKBOX IN EDIT MODE
                if (isEditing) {
                    const recurringCheckbox = document.getElementById('recurringCheck');
                    recurringCheckbox.disabled = true;
                    recurringCheckbox.title = "Converting between recurring and non-recurring is not supported";
                    
                    // Add visual indicator next to the checkbox
                    const checkboxLabel = recurringCheckbox.nextElementSibling;
                    if (checkboxLabel) {
                        // Add lock icon to indicate it's locked
                        checkboxLabel.innerHTML += ' <i class="bi bi-lock-fill text-secondary" title="Cannot be changed"></i>';
                    }
                }

                if (isRecurring) {
                    // Set pattern type - make this more robust
                    const pattern = currentEvent.extendedProps.recurring_pattern;
                    
                    // Log pattern data to help diagnose the issue
                    console.log("Pattern data:", pattern);
                    
                    // Account for different property naming
                    const patternType = pattern.pattern_type || pattern.type || 'weekly';
                    console.log("Using pattern type:", patternType);
                    
                    // Try to find the matching radio button
                    const patternRadio = document.querySelector(`input[name="pattern_type"][value="${patternType}"]`);
                    
                    // Add null check and fallback
                    if (patternRadio) {
                        patternRadio.checked = true;
                        console.log(`Selected pattern radio for: ${patternType}`);
                    } else {
                        console.warn(`No pattern radio found for: ${patternType}`);
                        // Set default to weekly
                        const defaultRadio = document.querySelector('input[name="pattern_type"][value="weekly"]');
                        if (defaultRadio) {
                            defaultRadio.checked = true;
                            console.log("Defaulted to weekly pattern");
                        } else {
                            console.error("Could not find any pattern radio buttons");
                        }
                    }
                    
                    // Show relevant options based on pattern type
                    const weeklyOptions = document.getElementById('weeklyOptions');
                    if (weeklyOptions) {
                        weeklyOptions.style.display = patternType === 'weekly' ? 'block' : 'none';
                    }
                    
                    // Set days of week for weekly pattern
                    if (patternType === 'weekly') {
                        const daysOfWeek = currentEvent.extendedProps.recurring_pattern.day_of_week || '';
                        
                        // Reset all checkboxes first
                        document.querySelectorAll('input[name="day_of_week[]"]').forEach(cb => {
                            cb.checked = false;
                        });
                        
                        // If there are days selected, check the appropriate boxes
                        if (daysOfWeek) {
                            // Split into array if it contains commas
                            const dayArray = daysOfWeek.includes(',') ? 
                                daysOfWeek.split(',').map(d => d.trim()) : 
                                [daysOfWeek];
                                
                            // Check the appropriate checkboxes
                            dayArray.forEach(day => {
                                const checkbox = document.querySelector(`input[name="day_of_week[]"][value="${day}"]`);
                                if (checkbox) checkbox.checked = true;
                            });
                        }
                    }
                    
                    // Set recurrence end date
                    if (currentEvent.extendedProps.recurring_pattern.recurrence_end) {
                        document.getElementById('recurrenceEnd').value = 
                            currentEvent.extendedProps.recurring_pattern.recurrence_end.split('T')[0];
                    }
                }
                
                // Handle participants
                loadParticipantsForEdit(currentEvent.extendedProps.participants);
                
                // Show the modal
                addAppointmentModal.show();
                
                console.log('Edit modal should now be visible');
            } catch (error) {
                console.error('Error setting up edit form:', error);
            }
        });

        // Helper function to load participants into the edit form
        function loadParticipantsForEdit(participants) {
            if (!participants) return;
            
            console.log("Loading participants for edit:", participants);
            
            // Clear existing selections
            document.querySelectorAll('.attendee-tag').forEach(tag => tag.remove());
            document.querySelectorAll('.attendee-checkbox').forEach(checkbox => {
                checkbox.checked = false; // Reset all checkboxes first
            });
        });
        
        function resetAppointmentForm() {
            if (!addAppointmentForm) return; // Add this safety check
            
            // Group participants by type
            const groupedParticipants = {
                cose_user: [],
                beneficiary: [],
                family_member: []
            };
            
            participants.forEach(p => {
                if (p && p.type && groupedParticipants[p.type]) {
                    groupedParticipants[p.type].push(p);
                    
                    // Also check the corresponding checkbox
                    const checkbox = document.querySelector(`.attendee-option[data-id="${p.id}"][data-type="${p.type}"] input`);
                    if (checkbox) {
                        checkbox.checked = true;
                        console.log(`Checked checkbox for ${p.type} ${p.id} (${p.name})`);
                    } else {
                        console.warn(`Checkbox not found for ${p.type} ${p.id} (${p.name})`);
                    }
                }
            });
            
            // Add staff participants
            const staffContainer = document.getElementById('staffAttendees');
            groupedParticipants.cose_user.forEach(p => {
                addAttendeeTag(staffContainer, p.id, 'cose_user', p.name);
            });
            
            // Enable beneficiary selection if this is the right appointment type
            const appointmentType = document.getElementById('appointmentType').value;
            const isAssessmentOrOther = ['7', '11'].includes(appointmentType); // 7=Assessment, 11=Others
            
            // Add beneficiary participants if relevant
            const beneficiaryContainer = document.getElementById('beneficiaryAttendees');
            if (isAssessmentOrOther) {
                beneficiaryContainer.classList.remove('disabled');
                document.getElementById('beneficiarySearch').disabled = false;
                document.querySelectorAll('input[name="participants[beneficiary][]"]').forEach(cb => cb.disabled = false);
            }
            groupedParticipants.beneficiary.forEach(p => {
                addAttendeeTag(beneficiaryContainer, p.id, 'beneficiary', p.name);
            });
            
            // Add family member participants if relevant
            const familyContainer = document.getElementById('familyAttendees');
            if (isAssessmentOrOther) {
                familyContainer.classList.remove('disabled');
                document.getElementById('familySearch').disabled = false;
                document.querySelectorAll('input[name="participants[family_member][]"]').forEach(cb => cb.disabled = false);
            }
            groupedParticipants.family_member.forEach(p => {
                addAttendeeTag(familyContainer, p.id, 'family_member', p.name);
            });
        }

        // Update the form submission handler to handle editing
        document.getElementById('submitAppointment').addEventListener('click', function(e) {
            e.preventDefault();
            
            // Reset error messages
            hideModalErrors();
            
            // Build the form data
            const formData = new FormData(document.getElementById('addAppointmentForm'));
            const appointmentId = formData.get('appointment_id');
            const isEditing = appointmentId && appointmentId.trim() !== '';

            console.log("Edit mode detection - appointmentId:", appointmentId, "isEditing:", isEditing);
                        
            // Handle recurring pattern data
            if (document.getElementById('recurringCheck').checked) {
                formData.append('is_recurring', '1');
                
                // Get pattern type
                const patternType = document.querySelector('input[name="pattern_type"]:checked').value;
                formData.append('pattern_type', patternType);
                
                // For weekly pattern, get selected days
                if (patternType === 'weekly') {
                    // Clear any existing day_of_week entries to avoid duplication
                    const entries = [...formData.entries()];
                    entries.forEach(entry => {
                        if (entry[0] === 'day_of_week[]') {
                            formData.delete('day_of_week[]');
                        }
                    });
                    
                    // Collect selected days
                    const selectedDays = [];
                    document.querySelectorAll('input[name="day_of_week[]"]:checked').forEach(checkbox => {
                        selectedDays.push(checkbox.value);
                    });
                    
                    console.log('Selected days for weekly pattern:', selectedDays);
                    
                    // If no days selected, use the current day from the appointment date
                    if (selectedDays.length === 0) {
                        const appointmentDate = document.getElementById('appointmentDate').value;
                        if (appointmentDate) {
                            const date = new Date(appointmentDate);
                            const dayOfWeek = date.getDay();
                            selectedDays.push(dayOfWeek.toString());
                            console.log('No days selected, using appointment date day:', dayOfWeek);
                        }
                    }
                    
                    // Add each day as separate form field entry
                    selectedDays.forEach(day => {
                        formData.append('day_of_week[]', day);
                    });
                }
                
                // Add recurrence end date
                const recurrenceEnd = document.getElementById('recurrenceEnd').value;
                if (recurrenceEnd) {
                    formData.append('recurrence_end', recurrenceEnd);
                }
            }
            
            // Show loading state
            this.disabled = true;
            const originalBtnHtml = this.innerHTML;
            this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
            
            // Determine URL based on whether this is a create or update
            const url = appointmentId ? 
                '/admin/internal-appointments/update' : 
                '/admin/internal-appointments/store';

           // Add explicit debug for date value
            const appointmentDate = document.getElementById('appointmentDate').value;
            console.log("Submitting date:", appointmentDate);

            // Always ensure date is set correctly
            formData.delete('date');
            formData.append('date', appointmentDate);

            // Handle flexible time properly
            const isFlexible = document.getElementById('flexibleTimeCheck').checked;
            formData.delete('is_flexible_time');
            formData.append('is_flexible_time', isFlexible ? '1' : '0');

            // Handle time fields based on flexible time setting 
            if (isFlexible) {
                // When flexible time is checked, remove time fields completely
                formData.delete('start_time');
                formData.delete('end_time');
            } else {
                // Get time values from the form
                const startTime = document.getElementById('appointmentTime').value;
                const endTime = document.getElementById('appointmentEndTime').value;
                
                if (!startTime || !endTime) {
                    showModalErrors([
                        !startTime ? 'Start time is required when flexible time is not selected.' : null,
                        !endTime ? 'End time is required when flexible time is not selected.' : null
                    ].filter(Boolean));
                    
                    // Reset button state and prevent form submission
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalBtnHtml;
                    return;
                }
                
                // Set the time values correctly
                formData.delete('start_time');
                formData.delete('end_time');
                formData.append('start_time', startTime);
                formData.append('end_time', endTime);
            }
            
            // Send the AJAX request
            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                success: function(response) {
                    if (response.success) {
                        addAppointmentModal.hide();
                        
                        // Show success message
                        showToast('Success', 
                            isEditing ? 'Appointment updated successfully!' : 'Appointment created successfully!',
                            'success');
                        
                        // Refresh calendar
                        setTimeout(function() {
                            calendar.refetchEvents();
                        }, 500);
                    } else {
                        // Show error message
                        showModalErrors([response.message || 'An unknown error occurred']);
                    }
                },
                error: function(xhr) {
                    console.error('Error submitting form:', xhr);
                    
                    if (xhr.status === 422 && xhr.responseJSON) {
                        console.log('Validation response:', xhr.responseJSON); // Log the full response
                        
                        if (xhr.responseJSON.errors) {
                            // Show validation errors
                            const errorMessages = [];
                            for (const field in xhr.responseJSON.errors) {
                                errorMessages.push(`${field}: ${xhr.responseJSON.errors[field][0]}`);
                                console.log(`Field '${field}' error:`, xhr.responseJSON.errors[field]);
                            }
                            showModalErrors(errorMessages);
                        } else if (xhr.responseJSON.message) {
                            showModalErrors([xhr.responseJSON.message]);
                        }
                    } else {
                        showModalErrors(['An error occurred while saving the appointment. Please try again.']);
                    }
                },
                complete: function() {
                    // Reset button state
                    document.getElementById('submitAppointment').disabled = false;
                    document.getElementById('submitAppointment').innerHTML = originalBtnHtml;
                }
            });
        });

        // Helper function to format time from a Date object to HH:MM format
        function formatTime(date) {
            if (!date) return '';
            const d = new Date(date);
            const hours = d.getHours().toString().padStart(2, '0');
            const minutes = d.getMinutes().toString().padStart(2, '0');
            return `${hours}:${minutes}`;
        }

        // Delete button click handler
        deleteButton.addEventListener('click', function() {
            if (!currentEvent) return;
            
            // Clear previous values
            document.getElementById('cancelPassword').value = '';
            document.getElementById('cancelReason').value = '';
            document.getElementById('passwordError').textContent = '';
            
            // Get appointment details
            const eventDate = new Date(currentEvent.start).toLocaleDateString();
            const appointmentTitle = currentEvent.title || "Unknown";
            const appointmentLocation = currentEvent.extendedProps.meeting_location || "Not specified";
            const appointmentType = currentEvent.extendedProps.type || "Not specified";
            
            // Set appointment details in the modal
            document.getElementById('cancelAppointmentDetails').innerHTML = `
                <strong>Appointment:</strong> ${appointmentTitle}<br>
                <strong>Date:</strong> ${eventDate}<br>
                <strong>Location:</strong> ${appointmentLocation}<br>
                <strong>Type:</strong> ${appointmentType}
            `;
            
            // Determine if this is a recurring event
            const isRecurring = currentEvent.extendedProps.recurring;
            document.getElementById('recurringCancelOptions').style.display = isRecurring ? 'block' : 'none';
            
            // Set default option to "single" for recurring events
            if (isRecurring) {
                document.getElementById('cancelSingle').checked = true;
            }
            
            // Show the modal
            cancelModal.show();
        });

        // Confirm cancel button handler
        document.getElementById('confirmCancel').addEventListener('click', function() {
            const appointmentId = currentEvent.extendedProps.appointment_id;
            const occurrenceId = currentEvent.extendedProps.occurrence_id || '';
            const isRecurring = currentEvent.extendedProps.recurring;
            const password = document.getElementById('cancelPassword').value;
            const reason = document.getElementById('cancelReason').value;
            
            // Clear previous errors
            document.getElementById('passwordError').textContent = '';
            document.getElementById('cancelPassword').classList.remove('is-invalid');
            
            // Validate password
            if (!password) {
                document.getElementById('passwordError').textContent = 'Password is required';
                document.getElementById('cancelPassword').classList.add('is-invalid');
                return;
            }
            
            // Get the cancellation type for recurring appointments
            let cancelType = 'single';
            if (isRecurring) {
                const cancelOptions = document.getElementsByName('cancelOption');
                for (const option of cancelOptions) {
                    if (option.checked) {
                        cancelType = option.value;
                        break;
                    }
                }
            }
            
            // Show loading state on button
            const confirmButton = document.getElementById('confirmCancel');
            const originalButtonText = confirmButton.innerHTML;
            confirmButton.disabled = true;
            confirmButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
            
            // Create form data
            const formData = new FormData();
            formData.append('appointment_id', appointmentId);
            if (occurrenceId) {
                formData.append('occurrence_id', occurrenceId);
            }
            formData.append('password', password);
            formData.append('reason', reason);
            formData.append('cancel_type', cancelType);
            formData.append('occurrence_date', currentEvent.start ? currentEvent.start.toISOString().split('T')[0] : '');
            
            // Send cancel request
            fetch('/admin/internal-appointments/cancel', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 500) {
                        throw new Error('Server error: The server encountered an issue. Please try again later.');
                    } else if (response.status === 422) {
                        return response.json().then(data => {
                            throw new Error(data.message || 'Validation error. Please check your input.');
                        });
                    }
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                // Close modal
                bootstrap.Modal.getInstance(document.getElementById('cancelModal')).hide();
                
                // Show success message
                showToast('Success', data.message || 'Appointment cancelled successfully', 'success');
                
                // Update the calendar
                calendar.refetchEvents();
                
                // Clear the details panel
                if (appointmentDetailsEl) {
                    appointmentDetailsEl.innerHTML = '<div class="alert alert-info">Select an appointment to view details</div>';
                }
                
                // Clear current event selection
                currentEvent = null;
                editButton.disabled = true;
                deleteButton.disabled = true;
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error', error.message || 'An unexpected error occurred', 'danger');
            })
            .finally(() => {
                // Reset button state
                confirmButton.disabled = false;
                confirmButton.innerHTML = originalButtonText;
            });
        });

    });
    </script>
</body>
</html>