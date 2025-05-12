<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
        .event-skills {
            background-color: #4e73df !important;
            border-color: #4668cc !important;
        }
        
        .event-feedback {
            background-color: #1cc88a !important;
            border-color: #19b77d !important;
        }
        
        .event-council {
            background-color: #36b9cc !important;
            border-color: #31a8ba !important;
        }
        
        .event-health {
            background-color: #f6c23e !important;
            border-color: #e4b138 !important;
        }
        
        .event-liga {
            background-color: #e74a3b !important;
            border-color: #d93a2b !important;
        }
        
        .event-referrals {
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
            background-color: #5a5c69 !important;
            border-color: #505259 !important;
        }
        
        .event-mentoring {
            background-color: #858796 !important;
            border-color: #7a7c89 !important;
        }
        
        .event-other {
            background-color: #a435f0 !important;
            border-color: #9922e8 !important;
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
                                    <h5 class="section-heading text-white mb-0">
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
   
    <!-- Add Appointment Modal -->
    <div class="modal fade" id="addAppointmentModal" tabindex="-1" aria-labelledby="addAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="appointmentModalLabel"><i class="bi bi-plus-circle"></i> Add New Appointment</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
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

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="bi bi-exclamation-triangle"></i> Confirm Cancellation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel this appointment?</p>
                    
                    <div id="recurringCancellationOptions" style="display: none;">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="cancelOption" id="cancelSingle" value="single" checked>
                            <label class="form-check-label" for="cancelSingle">
                                Cancel only this occurrence
                            </label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="cancelOption" id="cancelSeries" value="series">
                            <label class="form-check-label" for="cancelSeries">
                                Cancel all future occurrences
                            </label>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirmPassword" class="form-label">Enter password to confirm</label>
                        <input type="password" class="form-control" id="confirmPassword" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep It</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Yes, Cancel</button>
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

            // Listen for modal close event to reset the form to "add mode"
            document.getElementById('addAppointmentModal').addEventListener('hidden.bs.modal', function() {
                // Reset form
                resetAppointmentForm();
                
                // Reset modal title to "Add New Appointment"
                document.getElementById('appointmentModalLabel').innerHTML = '<i class="bi bi-plus-circle"></i> Add New Appointment';
                
                // Reset submit button text
                document.getElementById('submitAppointment').innerHTML = '<i class="bi bi-plus-circle"></i> Create Appointment';
            });

        var calendarEl = document.getElementById('calendar');
        var appointmentDetailsEl = document.getElementById('appointmentDetails');
        var addAppointmentForm = document.getElementById('addAppointmentForm');
        var addAppointmentModal = new bootstrap.Modal(document.getElementById('addAppointmentModal'));
        var confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
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
            
            // Show/hide other field based on selection
            document.getElementById('otherTypeContainer').style.display = 
                selectedType === 'other' ? 'block' : 'none';
            
            // Enable/disable beneficiary and family attendance based on type
            const isAssessmentOrOther = selectedType === 'assessment' || selectedType === 'other';
            
            // Beneficiary options
            const beneficiaryAttendees = document.getElementById('beneficiaryAttendees');
            const beneficiarySearch = document.getElementById('beneficiarySearch');
            const beneficiaryCheckboxes = document.querySelectorAll('input[name="participants[beneficiary][]"]');
            
            beneficiaryAttendees.classList.toggle('disabled', !isAssessmentOrOther);
            beneficiarySearch.disabled = !isAssessmentOrOther;
            beneficiaryCheckboxes.forEach(checkbox => {
                checkbox.disabled = !isAssessmentOrOther;
            });
            
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
                
                // Enable edit and delete buttons when an event is selected
                editButton.disabled = !event.extendedProps.can_edit;
                deleteButton.disabled = !event.extendedProps.can_edit;
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

        // Form submission handler
        document.getElementById('submitAppointment').addEventListener('click', function(e) {
            e.preventDefault();
            
            // Reset error messages
            document.querySelectorAll('.is-invalid').forEach(el => {
                el.classList.remove('is-invalid');
            });
            document.querySelectorAll('.invalid-feedback').forEach(el => {
                el.remove();
            });
            
            // Get form values
            const formData = new FormData(addAppointmentForm);
            const appointmentId = document.getElementById('appointmentId')?.value || '';
            
            // Add appointment ID if editing
            if (appointmentId) {
                formData.append('appointment_id', appointmentId);
            }
            
            // Add recurring data if checked
            if (recurringCheck.checked) {
                formData.append('is_recurring', '1');
                
                // Get selected pattern type
                const patternType = document.querySelector('input[name="pattern_type"]:checked')?.value || 'weekly';
                formData.append('pattern_type', patternType);
                
                // For weekly pattern, get selected days
                if (patternType === 'weekly') {
                    const selectedDays = [];
                    document.querySelectorAll('input[name="day_of_week[]"]:checked').forEach(checkbox => {
                        selectedDays.push(checkbox.value);
                    });
                    
                    // Ensure at least one day is selected
                    if (selectedDays.length === 0) {
                        // Select the day of the selected date
                        const selectedDate = new Date(document.getElementById('appointmentDate').value);
                        const dayOfWeek = selectedDate.getDay().toString();
                        document.getElementById(`day${['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'][parseInt(dayOfWeek)]}`).checked = true;
                        selectedDays.push(dayOfWeek);
                    }
                    
                    selectedDays.forEach(day => {
                        formData.append('day_of_week[]', day);
                    });
                }
                
                // Get recurrence end date
                const recurrenceEnd = document.getElementById('recurrenceEnd').value;
                if (recurrenceEnd) {
                    formData.append('recurrence_end', recurrenceEnd);
                }
            } else {
                formData.append('is_recurring', '0');
            }
            
            // Add participants data
            const staffAttendees = document.querySelectorAll('#staffAttendees .attendee-tag');
            const beneficiaryAttendees = document.querySelectorAll('#beneficiaryAttendees .attendee-tag');
            const familyAttendees = document.querySelectorAll('#familyAttendees .attendee-tag');
            
            // Check if at least one staff member is selected
            if (staffAttendees.length === 0) {
                const staffContainer = document.getElementById('staffAttendees');
                staffContainer.classList.add('is-invalid');
                const errorFeedback = document.createElement('div');
                errorFeedback.className = 'invalid-feedback';
                errorFeedback.textContent = 'Please select at least one staff member';
                staffContainer.after(errorFeedback);
                return;
            }
            
            // Add staff participants
            staffAttendees.forEach(tag => {
                formData.append('participants[cose_user][]', tag.dataset.id);
            });
            
            // Add beneficiary participants if not disabled
            if (!document.getElementById('beneficiarySearch').disabled) {
                beneficiaryAttendees.forEach(tag => {
                    formData.append('participants[beneficiary][]', tag.dataset.id);
                });
            }
            
            // Add family participants if not disabled
            if (!document.getElementById('familySearch').disabled) {
                familyAttendees.forEach(tag => {
                    formData.append('participants[family_member][]', tag.dataset.id);
                });
            }
            
            // Add organizer flag (first staff member is organizer by default)
            if (staffAttendees.length > 0) {
                formData.append('organizer_id', staffAttendees[0].dataset.id);
                formData.append('organizer_type', 'cose_user');
            }
            
            // Submit form via AJAX
            fetch('/admin/internal-appointments' + (appointmentId ? '/update' : '/store'), {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf_token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'An error occurred');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    // Close modal and refresh calendar
                    addAppointmentModal.hide();
                    calendar.refetchEvents();
                    
                    // Reset form
                    resetAppointmentForm();
                    
                    // Show success message
                    alert(data.message || 'Appointment saved successfully');
                } else {
                    alert(data.message || 'Failed to save appointment');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Failed to process request');
            });
        });
        
        function resetAppointmentForm() {
            if (!addAppointmentForm) return; // Add this safety check
            
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
        
        // Edit button click handler
        editButton.addEventListener('click', function() {
            if (!currentEvent) return;
            
            const event = currentEvent;
            
            // Reset form first
            resetAppointmentForm();
            
            // Set form ID and title
            document.getElementById('appointmentId').value = event.extendedProps.appointment_id;
            document.getElementById('appointmentModalLabel').innerHTML = '<i class="bi bi-pencil-square"></i> Edit Appointment';
            
            // Set basic fields
            document.getElementById('appointmentTitle').value = event.title;
            document.getElementById('appointmentType').value = event.extendedProps.type_id;
            document.getElementById('appointmentPlace').value = event.extendedProps.meeting_location || '';
            document.getElementById('appointmentNotes').value = event.extendedProps.notes || '';
            
            // Set date and time
            if (event.start) {
                document.getElementById('appointmentDate').value = event.start.toISOString().split('T')[0];
                if (!event.extendedProps.is_flexible_time && event.start) {
                    document.getElementById('appointmentTime').value = 
                        event.start.toTimeString().substring(0, 5);
                }
            }
            
            // Handle flexible time
            document.getElementById('flexibleTimeCheck').checked = event.extendedProps.is_flexible_time;
            document.getElementById('timeFieldsContainer').style.display = 
                event.extendedProps.is_flexible_time ? 'none' : 'flex';
            
            // Handle other appointment type
            if (event.extendedProps.type_id === '11') { // ID for "Other" type
                document.getElementById('otherTypeContainer').style.display = 'block';
                document.getElementById('otherAppointmentType').value = event.extendedProps.other_type_details || '';
            }
            
            // Handle recurring appointment
            if (event.extendedProps.recurring) {
                document.getElementById('recurringCheck').checked = true;
                document.getElementById('recurringOptions').style.display = 'block';
                
                // Set pattern type
                const pattern = event.extendedProps.recurring_pattern;
                if (pattern) {
                    document.getElementById(`pattern${pattern.type.charAt(0).toUpperCase() + pattern.type.slice(1)}`).checked = true;
                    
                    // Show/hide weekly options
                    document.getElementById('weeklyOptions').style.display = pattern.type === 'weekly' ? 'block' : 'none';
                    
                    // Set day of week for weekly pattern
                    if (pattern.type === 'weekly' && pattern.day_of_week) {
                        const days = pattern.day_of_week.split(',').map(d => d.trim());
                        days.forEach(day => {
                            const checkbox = document.querySelector(`input[name="day_of_week[]"][value="${day}"]`);
                            if (checkbox) checkbox.checked = true;
                        });
                    }
                    
                    // Set recurrence end date
                    if (pattern.recurrence_end) {
                        document.getElementById('recurrenceEnd').value = pattern.recurrence_end;
                    }
                }
            }
            
            // Set participants
            if (event.extendedProps.participants) {
                // Process staff participants
                event.extendedProps.participants.forEach(participant => {
                    if (participant.type === 'cose_user') {
                        // Check the corresponding checkbox
                        const checkbox = document.querySelector(`.attendee-option[data-id="${participant.id}"][data-type="cose_user"] input`);
                        if (checkbox) {
                            checkbox.checked = true;
                            // Add tag
                            addAttendeeTag(
                                document.getElementById('staffAttendees'),
                                participant.id,
                                'cose_user',
                                participant.name
                            );
                        }
                    }
                });
            }
            
            // Update submit button text
            document.getElementById('submitAppointment').innerHTML = '<i class="bi bi-save"></i> Save Changes';
            
            // Show modal
            addAppointmentModal.show();
        });
        
        // Delete button click handler
        deleteButton.addEventListener('click', function() {
            if (!currentEvent) return;
            document.getElementById('confirmPassword').value = '';
            confirmationModal.show();
        });
        
        // Confirm delete handler
        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (!currentEvent) return;
            
            const appointmentId = currentEvent.extendedProps.appointment_id;
            const occurrenceId = currentEvent.extendedProps.occurrence_id;
            const isRecurring = currentEvent.extendedProps.recurring;
            
            // Prepare form data
            const formData = new FormData();
            formData.append('appointment_id', appointmentId);
            formData.append('occurrence_id', occurrenceId);
            formData.append('password', document.getElementById('confirmPassword').value);
            
            // For recurring appointments, ask if they want to cancel just this instance or the whole series
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
            formData.append('cancel_type', cancelType);
            
            // Submit cancellation request
            fetch('/admin/internal-appointments/cancel', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': csrf_token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    return response.json().then(data => {
                        throw new Error(data.message || 'An error occurred');
                    });
                }
                return response.json();
            })
            .then(data => {
                if (data.status === 'success') {
                    // Close modal and refresh calendar
                    confirmationModal.hide();
                    calendar.refetchEvents();
                    
                    // Reset current event
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
                    alert(data.message || 'Appointment cancelled successfully');
                } else {
                    alert(data.message || 'Failed to cancel appointment');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert(error.message || 'Failed to process request');
            });
        });
        
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
    });
    </script>
</body>
</html>