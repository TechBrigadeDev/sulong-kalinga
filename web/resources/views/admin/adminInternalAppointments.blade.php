<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
                                <input type="text" class="form-control search-input" placeholder="Search appointments..." aria-label="Search appointments">
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
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addAppointmentModalLabel">
                        <i class="bi bi-calendar-plus"></i> Schedule New Appointment
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addAppointmentForm">
                        <!-- Appointment Title -->
                        <div class="form-group">
                            <label for="appointmentTitle">
                                <i class="bi bi-card-heading"></i> Appointment Title
                            </label>
                            <input type="text" class="form-control" id="appointmentTitle" placeholder="Enter appointment title" required>
                        </div>
                        
                        <!-- Appointment Type -->
                        <div class="form-group">
                            <label for="appointmentType">
                                <i class="bi bi-tag"></i> Appointment Type
                            </label>
                            <div class="select-container">
                                <select class="form-control" id="appointmentType" required>
                                    <option value="">Select Appointment Type</option>
                                    <option value="skills">Skills Enhancement Training</option>
                                    <option value="feedback">Quarterly Feedback Sessions</option>
                                    <option value="council">Municipal Development Council (MDC) Participation</option>
                                    <option value="health">Municipal Local Health Board Meeting</option>
                                    <option value="liga">LIGA Meeting</option>
                                    <option value="referrals">Referrals to MHO</option>
                                    <option value="assessment">Assessment and Review of Care Needs</option>
                                    <option value="careplan">General Care Plan Finalization</option>
                                    <option value="team">Project Team Meeting</option>
                                    <option value="mentoring">Mentoring Session</option>
                                    <option value="other">Other Appointment</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Other appointment type field (conditionally shown) -->
                        <div class="form-group other-field-container" id="otherTypeContainer">
                            <label for="otherAppointmentType">
                                <i class="bi bi-pencil"></i> Specify Appointment Type
                            </label>
                            <input type="text" class="form-control" id="otherAppointmentType" placeholder="Please specify the appointment type">
                        </div>
                        
                        <!-- Attendees Selection -->
                        <div class="form-group">
                            <label for="attendees">
                                <i class="bi bi-people"></i> Attendees
                            </label>
                            <div class="attendees-container">
                                <div class="attendees-input-container" id="attendeesInputContainer" onclick="focusAttendeesInput()">
                                    <input type="text" class="attendees-input" id="attendeesInput" placeholder="Type to search attendees">
                                </div>
                                <div class="attendees-dropdown" id="attendeesDropdown"></div>
                            </div>
                        </div>
                        
                        <!-- Date and Time -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointmentDate">
                                        <i class="bi bi-calendar-date"></i> Date
                                    </label>
                                    <input type="date" class="form-control" id="appointmentDate" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointmentTime">
                                        <i class="bi bi-clock"></i> Time
                                    </label>
                                    <input type="time" class="form-control" id="appointmentTime" required>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Location -->
                        <div class="form-group">
                            <label for="appointmentPlace">
                                <i class="bi bi-geo-alt"></i> Location
                            </label>
                            <input type="text" class="form-control" id="appointmentPlace" placeholder="Enter location" required>
                        </div>
                        
                        <!-- Include beneficiaries checkbox (for Referrals to MHO) -->
                        <div class="form-group" id="includeBeneficiariesContainer" style="display: none;">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="includeBeneficiaries">
                                <label class="form-check-label" for="includeBeneficiaries">
                                    Include beneficiaries in this referral
                                </label>
                            </div>
                        </div>
                        
                        <!-- Beneficiaries selection (conditionally shown) -->
                        <div class="form-group" id="beneficiariesContainer" style="display: none;">
                            <label for="beneficiarySelect">
                                <i class="bi bi-person-heart"></i> Beneficiaries
                            </label>
                            <div class="select-container">
                                <select class="form-control" id="beneficiarySelect" multiple size="3">
                                    <option value="1">Robert Chen</option>
                                    <option value="2">Margaret Williams</option>
                                    <option value="3">James Wilson</option>
                                    <option value="4">Patricia Taylor</option>
                                    <option value="5">Thomas Moore</option>
                                </select>
                            </div>
                            <small class="form-text text-muted">Hold Ctrl/Cmd to select multiple beneficiaries</small>
                        </div>
                        
                        <!-- Notes -->
                        <div class="form-group mb-0">
                            <label for="appointmentNotes">
                                <i class="bi bi-journal-text"></i> Notes
                            </label>
                            <textarea class="form-control" id="appointmentNotes" rows="3" placeholder="Enter any additional notes or agenda items..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary modal-action-btn" id="submitAppointment">
                        <i class="bi bi-calendar-check"></i> Schedule Appointment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Confirm Cancellation</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel this appointment?</p>
                    <div class="form-group mt-3">
                        <label for="confirmPassword" class="form-label">Enter your password to confirm:</label>
                        <input type="password" class="form-control" id="confirmPassword" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">No, Keep It</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Yes, Cancel It</button>
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
            var calendarEl = document.getElementById('calendar');
            var appointmentDetailsEl = document.getElementById('appointmentDetails');
            var addAppointmentForm = document.getElementById('addAppointmentForm');
            var addAppointmentModal = new bootstrap.Modal(document.getElementById('addAppointmentModal'));
            var confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
            var editButton = document.getElementById('editAppointmentButton');
            var deleteButton = document.getElementById('deleteAppointmentButton');
            var toggleWeekButton = document.getElementById('toggleWeekView');
            var currentEvent = null;
            var currentView = 'dayGridMonth';
            
            // Initialize tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Set default date to today
            document.getElementById('appointmentDate').valueAsDate = new Date();
            
            // Handle appointment type change
            document.getElementById('appointmentType').addEventListener('change', function() {
                const selectedType = this.value;
                
                // Show/hide other field based on selection
                document.getElementById('otherTypeContainer').style.display = 
                    selectedType === 'other' ? 'block' : 'none';
                
                // Show/hide beneficiaries field based on selection
                const isMhoReferral = selectedType === 'referrals';
                document.getElementById('includeBeneficiariesContainer').style.display = 
                    isMhoReferral ? 'block' : 'none';
                
                if (!isMhoReferral) {
                    document.getElementById('includeBeneficiaries').checked = false;
                    document.getElementById('beneficiariesContainer').style.display = 'none';
                }
            });
            
            // Handle include beneficiaries checkbox
            document.getElementById('includeBeneficiaries').addEventListener('change', function() {
                document.getElementById('beneficiariesContainer').style.display = 
                    this.checked ? 'block' : 'none';
            });
            
            // Attendees selection functionality
            const attendeesInput = document.getElementById('attendeesInput');
            const attendeesInputContainer = document.getElementById('attendeesInputContainer');
            const attendeesDropdown = document.getElementById('attendeesDropdown');
            const selectedAttendees = new Set();
            
            // Sample attendees data (in a real app, this would come from your backend)
            const availableAttendees = [
                { id: 1, name: "Sarah Johnson", role: "Care Worker" },
                { id: 2, name: "Michael Brown", role: "Care Worker" },
                { id: 3, name: "Emma Davis", role: "Care Worker" },
                { id: 4, name: "David Miller", role: "Care Worker" },
                { id: 5, name: "John Smith", role: "Administrator" },
                { id: 6, name: "Lisa Wilson", role: "Social Worker" },
                { id: 7, name: "Robert Garcia", role: "Doctor" },
                { id: 8, name: "Jennifer Lee", role: "Nurse" },
                { id: 9, name: "Carlos Mendez", role: "Supervisor" },
                { id: 10, name: "Maria Rodriguez", role: "Office Staff" }
            ];
            
            // Function to render attendee tags
            function renderAttendeeTags() {
                // Remove existing tags
                const tags = attendeesInputContainer.querySelectorAll('.attendee-tag');
                tags.forEach(tag => tag.remove());
                
                // Add attendee tags before the input
                selectedAttendees.forEach(attendeeId => {
                    const attendee = availableAttendees.find(a => a.id === attendeeId);
                    if (attendee) {
                        const tagEl = document.createElement('div');
                        tagEl.className = 'attendee-tag';
                        tagEl.innerHTML = `
                            ${attendee.name}
                            <span class="attendee-remove" onclick="removeAttendee(${attendee.id})">
                                <i class="bi bi-x"></i>
                            </span>
                        `;
                        attendeesInputContainer.insertBefore(tagEl, attendeesInput);
                    }
                });
            }
            
            // Function to render attendees dropdown
            function renderAttendeesDropdown(searchTerm = '') {
                attendeesDropdown.innerHTML = '';
                
                const filteredAttendees = availableAttendees.filter(attendee =>
                    !selectedAttendees.has(attendee.id) &&
                    attendee.name.toLowerCase().includes(searchTerm.toLowerCase())
                );
                
                if (filteredAttendees.length === 0) {
                    const noResults = document.createElement('div');
                    noResults.className = 'p-2 text-muted';
                    noResults.textContent = searchTerm ? 'No matching attendees found' : 'No more attendees available';
                    attendeesDropdown.appendChild(noResults);
                    return;
                }
                
                filteredAttendees.forEach(attendee => {
                    const option = document.createElement('div');
                    option.className = 'attendee-option';
                    option.innerHTML = `
                        <input type="checkbox" class="attendee-checkbox" id="attendee-${attendee.id}" value="${attendee.id}">
                        <label for="attendee-${attendee.id}">${attendee.name} <small class="text-muted">(${attendee.role})</small></label>
                    `;
                    option.addEventListener('click', () => {
                        selectedAttendees.add(attendee.id);
                        renderAttendeeTags();
                        attendeesInput.value = '';
                        attendeesDropdown.style.display = 'none';
                        attendeesInput.focus();
                    });
                    
                    attendeesDropdown.appendChild(option);
                });
            }
            
            // Handle input focus
            attendeesInput.addEventListener('focus', () => {
                renderAttendeesDropdown(attendeesInput.value);
                attendeesDropdown.style.display = 'block';
            });
            
            // Handle input change
            attendeesInput.addEventListener('input', () => {
                renderAttendeesDropdown(attendeesInput.value);
                attendeesDropdown.style.display = 'block';
            });
            
            // Handle outside click
            document.addEventListener('click', (e) => {
                if (!attendeesInputContainer.contains(e.target) && !attendeesDropdown.contains(e.target)) {
                    attendeesDropdown.style.display = 'none';
                }
            });
            
            // Global function to focus input
            window.focusAttendeesInput = function() {
                attendeesInput.focus();
            };
            
            // Global function to remove attendee
            window.removeAttendee = function(attendeeId) {
                selectedAttendees.delete(attendeeId);
                renderAttendeeTags();
            };
            
            // Color coding for different appointment types
            const appointmentTypeColors = {
                'skills': 'event-skills',
                'feedback': 'event-feedback',
                'council': 'event-council',
                'health': 'event-health',
                'liga': 'event-liga',
                'referrals': 'event-referrals',
                'assessment': 'event-assessment',
                'careplan': 'event-careplan',
                'team': 'event-team', 
                'mentoring': 'event-mentoring',
                'other': 'event-other'
            };
            
            // Helper function to format appointment types for display
            function formatAppointmentType(typeCode, customType = null) {
                const types = {
                    'skills': 'Skills Enhancement Training',
                    'feedback': 'Quarterly Feedback Session',
                    'council': 'MDC Participation',
                    'health': 'Health Board Meeting',
                    'liga': 'LIGA Meeting',
                    'referrals': 'MHO Referral',
                    'assessment': 'Care Needs Assessment',
                    'careplan': 'Care Plan Finalization',
                    'team': 'Project Team Meeting',
                    'mentoring': 'Mentoring Session',
                    'other': customType || 'Other Appointment'
                };
                
                return types[typeCode] || typeCode;
            }

            var calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: currentView,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                eventDisplay: 'block',
                events: [
                    {
                        id: '1',
                        title: 'Skills Enhancement Training',
                        start: new Date().setHours(10, 0),
                        end: new Date().setHours(12, 0),
                        extendedProps: {
                            type: 'skills',
                            typeDisplay: 'Skills Enhancement Training',
                            attendees: [
                                { id: 1, name: "Sarah Johnson", role: "Care Worker" },
                                { id: 2, name: "Michael Brown", role: "Care Worker" },
                                { id: 3, name: "Emma Davis", role: "Care Worker" }
                            ],
                            location: 'Training Room A',
                            notes: 'Focused on elderly care techniques and mobility assistance',
                            createdBy: 'Admin'
                        },
                        classNames: ['event-skills']
                    },
                    {
                        id: '2',
                        title: 'Quarterly Feedback Session',
                        start: new Date(new Date().getTime() + 86400000).setHours(14, 0), // Tomorrow
                        end: new Date(new Date().getTime() + 86400000).setHours(15, 30),
                        extendedProps: {
                            type: 'feedback',
                            typeDisplay: 'Quarterly Feedback Session',
                            attendees: [
                                { id: 5, name: "John Smith", role: "Administrator" },
                                { id: 9, name: "Carlos Mendez", role: "Supervisor" },
                                { id: 1, name: "Sarah Johnson", role: "Care Worker" }
                            ],
                            location: 'Conference Room B',
                            notes: 'Review of Q2 performance and goal setting for Q3',
                            createdBy: 'Admin'
                        },
                        classNames: ['event-feedback']
                    },
                    {
                        id: '3',
                        title: 'Municipal Health Board Meeting',
                        start: new Date(new Date().getTime() + 3 * 86400000).setHours(9, 0), // 3 days later
                        end: new Date(new Date().getTime() + 3 * 86400000).setHours(11, 0),
                        extendedProps: {
                            type: 'health',
                            typeDisplay: 'Municipal Local Health Board Meeting',
                            attendees: [
                                { id: 5, name: "John Smith", role: "Administrator" },
                                { id: 7, name: "Robert Garcia", role: "Doctor" },
                                { id: 8, name: "Jennifer Lee", role: "Nurse" }
                            ],
                            location: 'Municipal Hall - Meeting Room 1',
                            notes: 'Discussion of community health initiatives and resource allocation',
                            createdBy: 'Admin'
                        },
                        classNames: ['event-health']
                    },
                    {
                        id: '4',
                        title: 'MHO Referral for James Wilson',
                        start: new Date(new Date().getTime() + 2 * 86400000).setHours(13, 30), // 2 days later
                        end: new Date(new Date().getTime() + 2 * 86400000).setHours(14, 30),
                        extendedProps: {
                            type: 'referrals',
                            typeDisplay: 'Referrals to MHO',
                            attendees: [
                                { id: 7, name: "Robert Garcia", role: "Doctor" },
                                { id: 3, name: "Emma Davis", role: "Care Worker" }
                            ],
                            beneficiaries: ['James Wilson'],
                            location: 'Municipal Health Office',
                            notes: 'Review of elevated blood pressure readings and medication adjustment',
                            createdBy: 'Admin'
                        },
                        classNames: ['event-referrals']
                    }
                ],
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
                                <div class="event-location"><i class="bi bi-geo-alt"></i> ${event.extendedProps.location}</div>
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
                                          `Location: ${event.extendedProps.location}`;
                        
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
                    const hasBeneficiaries = event.extendedProps.beneficiaries && event.extendedProps.beneficiaries.length > 0;
                    
                    // Format the list of attendees
                    const attendeesList = event.extendedProps.attendees
                        .map(a => `<li>${a.name} <small class="text-muted">(${a.role})</small></li>`)
                        .join('');
                    
                    appointmentDetailsEl.innerHTML = `
                        <div class="detail-section">
                            <div class="section-title"><i class="bi bi-calendar-event"></i> Appointment</div>
                            <h5 class="mb-2">${event.title}</h5>
                            <div class="detail-item">
                                <span class="detail-label">Type:</span>
                                <span class="detail-value">${event.extendedProps.typeDisplay}</span>
                            </div>
                        </div>
                        
                        <div class="detail-section">
                            <div class="section-title"><i class="bi bi-clock"></i> Schedule</div>
                            <div class="detail-item">
                                <span class="detail-label">Date:</span>
                                <span class="detail-value">${event.start.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' })}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Time:</span>
                                <span class="detail-value">${event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true })} - ${event.end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true })}</span>
                            </div>
                            <div class="detail-item">
                                <span class="detail-label">Location:</span>
                                <span class="detail-value">${event.extendedProps.location}</span>
                            </div>
                        </div>
                        
                        <div class="detail-section">
                            <div class="section-title"><i class="bi bi-people"></i> Attendees</div>
                            <ul class="mb-0 ps-3">
                                ${attendeesList}
                            </ul>
                        </div>
                        
                        ${hasBeneficiaries ? `
                        <div class="detail-section">
                            <div class="section-title"><i class="bi bi-person-heart"></i> Beneficiaries</div>
                            <ul class="mb-0 ps-3">
                                ${event.extendedProps.beneficiaries.map(b => `<li>${b}</li>`).join('')}
                            </ul>
                        </div>
                        ` : ''}
                        
                        <div class="detail-section">
                            <div class="section-title"><i class="bi bi-journal-text"></i> Notes</div>
                            <p class="mb-0">${event.extendedProps.notes || 'No notes available'}</p>
                        </div>
                        
                        <div class="detail-section">
                            <div class="detail-item">
                                <span class="detail-label">Created By:</span>
                                <span class="detail-value">${event.extendedProps.createdBy}</span>
                            </div>
                        </div>
                    `;
                    
                    // Enable edit and delete buttons when an event is selected
                    editButton.disabled = false;
                    deleteButton.disabled = false;
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
                
                // Get form values
                const title = document.getElementById('appointmentTitle').value;
                const appointmentType = document.getElementById('appointmentType').value;
                const date = document.getElementById('appointmentDate').value;
                const time = document.getElementById('appointmentTime').value;
                const location = document.getElementById('appointmentPlace').value;
                const notes = document.getElementById('appointmentNotes').value;
                
                // Get other type if selected
                let typeDisplay;
                if (appointmentType === 'other') {
                    const otherType = document.getElementById('otherAppointmentType').value;
                    if (!otherType) {
                        alert('Please specify the other appointment type');
                        return;
                    }
                    typeDisplay = otherType;
                } else {
                    typeDisplay = formatAppointmentType(appointmentType);
                }
                
                // Validate required fields
                if (!title || !appointmentType || !date || !time || !location || selectedAttendees.size === 0) {
                    alert('Please fill in all required fields and select at least one attendee');
                    return;
                }
                
                // Check for beneficiaries if this is a referral
                let beneficiaries = [];
                if (appointmentType === 'referrals' && document.getElementById('includeBeneficiaries').checked) {
                    const beneficiarySelect = document.getElementById('beneficiarySelect');
                    const selectedBeneficiaries = Array.from(beneficiarySelect.selectedOptions).map(opt => opt.text);
                    
                    if (selectedBeneficiaries.length === 0) {
                        alert('Please select at least one beneficiary for the referral');
                        return;
                    }
                    
                    beneficiaries = selectedBeneficiaries;
                }
                
                // Convert selected attendees to attendee objects
                const attendees = Array.from(selectedAttendees).map(id => {
                    const attendee = availableAttendees.find(a => a.id === id);
                    return attendee;
                });
                
                // Convert date and time to Date object
                const startDate = new Date(`${date}T${time}`);
                // Set end time 1 hour later by default
                const endDate = new Date(startDate);
                endDate.setHours(endDate.getHours() + 1);
                
                // Add the event to calendar
                calendar.addEvent({
                    id: Date.now().toString(), // Generate a unique ID
                    title: title,
                    start: startDate,
                    end: endDate,
                    extendedProps: {
                        type: appointmentType,
                        typeDisplay: typeDisplay,
                        attendees: attendees,
                        beneficiaries: beneficiaries,
                        location: location,
                        notes: notes,
                        createdBy: 'Admin'
                    },
                    classNames: [appointmentTypeColors[appointmentType] || 'event-other']
                });
                
                // Reset form and close modal
                addAppointmentForm.reset();
                selectedAttendees.clear();
                renderAttendeeTags();
                document.getElementById('otherTypeContainer').style.display = 'none';
                document.getElementById('includeBeneficiariesContainer').style.display = 'none';
                document.getElementById('beneficiariesContainer').style.display = 'none';
                addAppointmentModal.hide();
                
                // Success message
                alert('Appointment scheduled successfully!');
            });
            
            // Edit button click handler
            editButton.addEventListener('click', function() {
                if (!currentEvent) return;
                
                // Here you would pre-fill the form with current event data
                const event = currentEvent;
                
                // Set basic fields
                document.getElementById('appointmentTitle').value = event.title;
                document.getElementById('appointmentType').value = event.extendedProps.type;
                document.getElementById('appointmentPlace').value = event.extendedProps.location;
                document.getElementById('appointmentNotes').value = event.extendedProps.notes || '';
                
                // Handle "other" appointment type
                if (event.extendedProps.type === 'other') {
                    document.getElementById('otherTypeContainer').style.display = 'block';
                    document.getElementById('otherAppointmentType').value = event.extendedProps.typeDisplay;
                } else {
                    document.getElementById('otherTypeContainer').style.display = 'none';
                }
                
                // Handle referrals with beneficiaries
                const hasBeneficiaries = 
                    event.extendedProps.beneficiaries && 
                    event.extendedProps.beneficiaries.length > 0;
                
                if (event.extendedProps.type === 'referrals') {
                    document.getElementById('includeBeneficiariesContainer').style.display = 'block';
                    document.getElementById('includeBeneficiaries').checked = hasBeneficiaries;
                    
                    if (hasBeneficiaries) {
                        document.getElementById('beneficiariesContainer').style.display = 'block';
                        // Would need to pre-select the beneficiaries here
                    } else {
                        document.getElementById('beneficiariesContainer').style.display = 'none';
                    }
                } else {
                    document.getElementById('includeBeneficiariesContainer').style.display = 'none';
                    document.getElementById('beneficiariesContainer').style.display = 'none';
                }
                
                // Set date and time
                const eventDate = event.start;
                document.getElementById('appointmentDate').value = eventDate.toISOString().split('T')[0];
                document.getElementById('appointmentTime').value = 
                    `${eventDate.getHours().toString().padStart(2, '0')}:${eventDate.getMinutes().toString().padStart(2, '0')}`;
                
                // Set attendees
                selectedAttendees.clear();
                if (event.extendedProps.attendees) {
                    event.extendedProps.attendees.forEach(attendee => {
                        selectedAttendees.add(attendee.id);
                    });
                }
                renderAttendeeTags();
                
                // Show modal
                addAppointmentModal.show();
            });
            
            // Delete button click handler
            deleteButton.addEventListener('click', function() {
                if (!currentEvent) return;
                document.getElementById('confirmPassword').value = '';
                confirmationModal.show();
            });
            
            // Confirm delete handler with password verification
            document.getElementById('confirmDelete').addEventListener('click', function() {
                const password = document.getElementById('confirmPassword').value;
                
                if (!password) {
                    alert('Please enter your password to confirm');
                    return;
                }
                
                if (!currentEvent) return;
                
                // In a real app, you would verify the password on the server
                if (password === 'admin') {  // For demo purposes only
                    // Remove the event from calendar
                    currentEvent.remove();
                    
                    // Reset state
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
                    
                    // Close modal
                    confirmationModal.hide();
                    
                    // Success message
                    alert('Appointment cancelled successfully');
                } else {
                    alert('Incorrect password. Cancellation denied.');
                }
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
                    const type = event.extendedProps.typeDisplay.toLowerCase();
                    const location = event.extendedProps.location.toLowerCase();
                    const notes = (event.extendedProps.notes || '').toLowerCase();
                    const attendees = event.extendedProps.attendees.map(a => a.name.toLowerCase()).join(' ');
                    const beneficiaries = (event.extendedProps.beneficiaries || []).map(b => b.toLowerCase()).join(' ');
                    
                    if (title.includes(searchTerm) || 
                        type.includes(searchTerm) || 
                        location.includes(searchTerm) || 
                        notes.includes(searchTerm) || 
                        attendees.includes(searchTerm) || 
                        beneficiaries.includes(searchTerm)) {
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