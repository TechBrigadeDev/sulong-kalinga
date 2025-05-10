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

    @include('components.adminNavbar')
    @include('components.adminSidebar')

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
   
    <!-- Add Appointment Modal -->
    <div class="modal fade" id="addAppointmentModal" tabindex="-1" aria-labelledby="addAppointmentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAppointmentModalLabel">
                        <i class="bi bi-calendar-plus"></i> Schedule New Appointment
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modalErrors" class="alert alert-danger d-none mb-3">
                        <ul id="errorList" class="mb-0"></ul>
                    </div>

                    <div id="recurringWarningMessage" class="alert alert-warning mb-3" style="display: none;">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>Note:</strong> Editing a recurring appointment will only affect this and future occurrences. 
                        Past occurrences will remain unchanged.
                    </div>
                    
                    <form id="addAppointmentForm">
                        @csrf
                        <input type="hidden" id="visitationId" name="visitation_id">
                        
                        <!-- Care Worker Selection -->
                        <div class="form-group">
                            <label for="careWorkerSelect">
                                <i class="bi bi-person-badge"></i> Care Worker
                            </label>
                            <div class="select-container">
                                <select class="form-control" id="careWorkerSelect" name="care_worker_id" required>
                                    <option value="">Select Care Worker</option>
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
                                <i class="bi bi-person-heart"></i> Beneficiary
                            </label>
                            <div class="select-container">
                                <select class="form-control" id="beneficiarySelect" name="beneficiary_id" required>
                                    <option value="">Select Beneficiary</option>
                                </select>
                            </div>
                            <div class="error-feedback" id="beneficiary-error"></div>
                        </div>
                        
                        <!-- Beneficiary Details (Auto-filled) -->
                        <div class="form-group">
                            <label for="beneficiaryAddress">
                                <i class="bi bi-geo-alt"></i> Address
                            </label>
                            <input type="text" class="form-control" id="beneficiaryAddress" readonly>
                        </div>
                        
                        <div class="form-group">
                            <label for="beneficiaryPhone">
                                <i class="bi bi-telephone"></i> Phone
                            </label>
                            <input type="text" class="form-control" id="beneficiaryPhone" readonly>
                        </div>
                        
                        <!-- Visit Date -->
                        <div class="form-group">
                            <label for="visitDate">
                                <i class="bi bi-calendar-date"></i> Visit Date
                            </label>
                            <input type="date" class="form-control" id="visitDate" name="visitation_date" required>
                            <div class="error-feedback" id="visitation-date-error"></div>
                        </div>
                        
                        <!-- Time Selection -->
                        <div class="form-group">
                            <label>
                                <i class="bi bi-clock"></i> Appointment Time
                            </label>
                            <div class="row" id="timeSelectionContainer">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="startTime" class="form-label">Start Time</label>
                                        <input type="time" class="form-control" id="startTime" name="start_time">
                                        <div class="error-feedback" id="start-time-error"></div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="endTime" class="form-label">End Time</label>
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
                                        Open Time / Flexible Schedule (Care Worker will determine actual time)
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Visit Type -->
                        <div class="form-group">
                            <label for="visitType">
                                <i class="bi bi-clipboard2-pulse"></i> Visit Type
                            </label>
                            <div class="select-container">
                                <select class="form-control" id="visitType" name="visit_type" required>
                                    <option value="">Select Visit Type</option>
                                    <option value="routine_care_visit">Routine Care Visit</option>
                                    <option value="service_request">Service Request</option>
                                    <option value="emergency_visit">Emergency Visit</option>
                                </select>
                            </div>
                            <div class="error-feedback" id="visit-type-error"></div>
                        </div>
                        
                        <!-- Recurring Options -->
                        <div class="form-check mt-3 mb-3">
                            <input class="form-check-input" type="checkbox" id="recurringCheck" name="is_recurring">
                            <label class="form-check-label" for="recurringCheck">
                                Make this a recurring appointment
                            </label>
                        </div>
                        
                        <div id="recurringOptionsContainer" class="border rounded p-3 mb-3" style="display: none;">
                            <div class="form-group">
                                <label class="form-label">Recurrence Pattern</label>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pattern_type" id="patternDaily" value="daily">
                                    <label class="form-check-label" for="patternDaily">Daily</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pattern_type" id="patternWeekly" value="weekly" checked>
                                    <label class="form-check-label" for="patternWeekly">Weekly</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="pattern_type" id="patternMonthly" value="monthly">
                                    <label class="form-check-label" for="patternMonthly">Monthly</label>
                                </div>
                            </div>
                            
                            <div id="weeklyOptions" class="mt-3">
                                <label class="form-label">Repeat on</label>
                                <div class="day-checkboxes">
                                    <div class="day-checkbox">
                                        <input type="checkbox" id="daySun" name="day_of_week[]" value="0">
                                        <label for="daySun">Sun</label>
                                    </div>
                                    <div class="day-checkbox">
                                        <input type="checkbox" id="dayMon" name="day_of_week[]" value="1">
                                        <label for="dayMon">Mon</label>
                                    </div>
                                    <div class="day-checkbox">
                                        <input type="checkbox" id="dayTue" name="day_of_week[]" value="2">
                                        <label for="dayTue">Tue</label>
                                    </div>
                                    <div class="day-checkbox">
                                        <input type="checkbox" id="dayWed" name="day_of_week[]" value="3">
                                        <label for="dayWed">Wed</label>
                                    </div>
                                    <div class="day-checkbox">
                                        <input type="checkbox" id="dayThu" name="day_of_week[]" value="4">
                                        <label for="dayThu">Thu</label>
                                    </div>
                                    <div class="day-checkbox">
                                        <input type="checkbox" id="dayFri" name="day_of_week[]" value="5">
                                        <label for="dayFri">Fri</label>
                                    </div>
                                    <div class="day-checkbox">
                                        <input type="checkbox" id="daySat" name="day_of_week[]" value="6">
                                        <label for="daySat">Sat</label>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mt-3">
                                <label for="recurrenceEnd" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="recurrenceEnd" name="recurrence_end">
                            </div>
                        </div>
                        
                        <!-- Notes -->
                        <div class="form-group mb-0">
                            <label for="notes">
                                <i class="bi bi-journal-text"></i> Visit Notes
                            </label>
                            <textarea class="form-control" id="notes" name="notes" rows="3" placeholder="Enter any additional instructions or notes about this appointment..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary modal-action-btn btn-schedule" id="submitAppointment">
                        <i class="bi bi-calendar-check"></i> Schedule Appointment
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
                        <i class="bi bi-trash-fill"></i> Cancel Appointment
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="confirmationModalBody">
                    <!-- Modal content will be inserted here -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Confirm Cancellation</button>
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
                    url: '{{ route("admin.careworker.appointments.get") }}',
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
                url: '{{ route("admin.careworker.appointments.beneficiaries") }}',
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
                    url: '{{ route("admin.careworker.appointments.beneficiary.details", ["id" => "__id__"]) }}'.replace('__id__', beneficiaryId),
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
            // Enable action buttons
            if (editButton) editButton.disabled = false;
            if (deleteButton) deleteButton.disabled = false;
            
            // Store current event
            currentEvent = event;
            
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
                    addAppointmentModalLabel.innerHTML = '<i class="bi bi-calendar-plus"></i> Schedule New Appointment';
                }
                
                // Set submit button text for new appointment
                if (submitAppointment) {
                    submitAppointment.innerHTML = '<i class="bi bi-calendar-check"></i> Schedule Appointment';
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
                        addAppointmentModalLabel.innerHTML = '<i class="bi bi-pencil-square"></i> Edit Appointment';
                    }
                    
                    // Set form data from event
                    const visitationId = document.getElementById('visitationId');
                    if (visitationId) {
                        visitationId.value = currentEvent.extendedProps.visitation_id;
                    }
                    
                    // Set beneficiary address and phone
                    const beneficiaryAddress = document.getElementById('beneficiaryAddress');
                    if (beneficiaryAddress) {
                        beneficiaryAddress.value = currentEvent.extendedProps.address || 'Not Available';
                    }
                    
                    const beneficiaryPhone = document.getElementById('beneficiaryPhone');
                    if (beneficiaryPhone) {
                        beneficiaryPhone.value = currentEvent.extendedProps.phone || 'Not Available';
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
                            'Converting between recurring and non-recurring appointments is not allowed. ' +
                            'Please cancel this appointment and create a new one instead.';
                        
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
                                    <strong>Note:</strong> Editing this recurring appointment will affect this and future occurrences. 
                                    Past occurrences will remain unchanged. You cannot change it to a non-recurring appointment.
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
                        submitAppointment.innerHTML = '<i class="bi bi-check-circle"></i> Update Appointment';
                    }
                    
                    // Load beneficiaries for the form
                    loadBeneficiariesForEdit(currentEvent.extendedProps.beneficiary_id, currentEvent.extendedProps.care_worker_id);
                    
                    // Show the modal
                    addAppointmentModal.show();
                    
                    console.log('Edit modal should now be visible');
                } catch (error) {
                    console.error('Error opening edit modal:', error);
                    showErrorModal('There was an error opening the edit form. Please try again.');
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
                url: '{{ route("admin.careworker.appointments.beneficiaries") }}',
                method: 'GET',
                success: function(response) {
                    const select = document.getElementById('beneficiarySelect');
                    if (!select) return;
                    
                    select.innerHTML = '<option value="">Select Beneficiary</option>';
                    
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
                    console.error('Error loading beneficiaries:', xhr);
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
                
                // Set appropriate URL based on whether this is an edit or new appointment
                const url = visitationId ? 
                    '{{ route("admin.careworker.appointments.update") }}' : 
                    '{{ route("admin.careworker.appointments.store") }}';
                
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
                                    url: '{{ route("admin.careworker.appointments.get") }}',
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
                    confirmationModalLabel.innerHTML = '<i class="bi bi-trash-fill"></i> Cancel Appointment';
                }
                
                // Get appointment details
                const isRecurring = currentEvent.extendedProps.recurring;
                const eventDate = new Date(currentEvent.start).toLocaleDateString();
                const careWorkerName = currentEvent.extendedProps.care_worker || "Not assigned";
                const beneficiaryName = currentEvent.extendedProps.beneficiary || "Not specified";
                const visitationType = currentEvent.extendedProps.visit_type || "Not specified";
                
                // Build the modal content
                let modalContent = `
                    <div class="mb-4">
                        <p class="mb-1"><strong>Date:</strong> ${eventDate}</p>
                        <p class="mb-1"><strong>Care Worker:</strong> ${careWorkerName}</p>
                        <p class="mb-1"><strong>Beneficiary:</strong> ${beneficiaryName}</p>
                        <p class="mb-1"><strong>Type:</strong> ${visitationType}</p>
                        ${isRecurring ? '<p class="mb-1 text-danger"><strong><i class="bi bi-repeat"></i> Recurring Appointment</strong></p>' : ''}
                    </div>
                    
                    <input type="hidden" id="deleteVisitationId" value="${currentEvent.extendedProps.visitation_id}">
                `;
                
                // Add cancellation options for recurring appointments only
                if (isRecurring) {
                    modalContent += `
                        <div class="mb-3 border rounded p-3 bg-light">
                            <p class="mb-2"><strong>Cancellation Options:</strong></p>
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="cancel_option" id="cancelSingle" value="single" checked>
                                <label class="form-check-label" for="cancelSingle">
                                    Cancel only this occurrence (${eventDate})
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="cancel_option" id="cancelFuture" value="future">
                                <label class="form-check-label" for="cancelFuture">
                                    Cancel this and all future occurrences
                                </label>
                            </div>
                        </div>
                    `;
                } else {
                    modalContent += `
                        <div class="alert alert-info mb-3">
                            <i class="bi bi-info-circle me-2"></i>
                            This will cancel the appointment for ${eventDate}
                        </div>
                    `;
                }
                
                // Add reason and password fields
                modalContent += `
                    <div class="mb-3">
                        <label for="cancelReason" class="form-label">Reason for Cancellation</label>
                        <textarea class="form-control" id="cancelReason" rows="3" placeholder="Please provide a reason for cancellation..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cancelPassword" class="form-label">Confirm your password</label>
                        <input type="password" class="form-control" id="cancelPassword" placeholder="Enter your password">
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
                    document.getElementById('passwordError').textContent = 'Password is required';
                    isValid = false;
                } else {
                    document.getElementById('cancelPassword').classList.remove('is-invalid');
                    document.getElementById('passwordError').textContent = '';
                }
                
                if (!isValid) return;
                
                // Show loading state
                const originalText = confirmDeleteBtn.innerHTML;
                confirmDeleteBtn.disabled = true;
                confirmDeleteBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Processing...';
                
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
                    url: '{{ route("admin.careworker.appointments.cancel") }}',
                    method: 'POST',
                    data: formData,
                    success: function(response) {
                        if (response.success) {
                            // Show success toast
                            showToast('Success', response.message || 'Appointment cancelled successfully', 'success');

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
                                        <p class="mt-3 mb-0">Select an appointment to view details</p>
                                    </div>
                                `;
                            }
                        } else {
                            // Show error
                            if (response.passwordError) {
                                document.getElementById('cancelPassword').classList.add('is-invalid');
                                document.getElementById('passwordError').textContent = response.passwordError;
                            } else {
                                showErrorModal(response.message || 'Failed to cancel the appointment.');
                            }
                        }
                    },
                    error: function(xhr) {
                        console.error('Error response:', xhr);
                        
                        if (xhr.status === 401) {
                            document.getElementById('cancelPassword').classList.add('is-invalid');
                            document.getElementById('passwordError').textContent = 'Incorrect password.';
                        } else {
                            showErrorModal('An error occurred while cancelling the appointment. Please try again.');
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