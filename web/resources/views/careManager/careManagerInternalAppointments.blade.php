<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Internal Appointmet | Manager</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon-white-16x16.png')}}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/internalAppointment.css') }}">
    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
</head>
<body>

    @php
    use App\Helpers\TranslationHelper as T;
    @endphp

    @include('components.careManagerNavbar')
    @include('components.careManagerSidebar')

    <div class="home-section">
        <div class="text-left">{{ T::translate('INTERNAL APPOINTMENTS', 'MGA INTERNAL NA APPOINTMENT') }}</div>
        <div class="container-fluid">
            <div class="row p-3" id="home-content">
                <!-- Main content area -->
                <div class="col-12 mb-4">
                    <div class="row">
                        <!-- Calendar Column -->
                        <div class="col-lg-8 col-md-7 mb-4">
                            <div class="card">

                            <div id="calendar-spinner" class="calendar-loading-overlay">
                                <div class="spinner-container">
                                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                                        <span class="visually-hidden">{{ T::translate('Loading', 'Naglo-load') }}...</span>
                                    </div>
                                    <p class="mt-2 text-primary">{{ T::translate('Loading appointments', 'Naglo-load ng mga appointment') }}...</p>
                                </div>
                            </div>

                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="section-heading">
                                        <i class="bi bi-calendar3"></i> {{ T::translate('Internal Appointment Calendar', 'Kalendaryo ng mga Internal na Appointment') }}
                                    </h5>
                                    <div class="calendar-actions d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-secondary" id="resetCalendarButton">
                                            <i class="bi bi-arrow-clockwise"></i> {{ T::translate('Reset', 'I-reset') }}
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="toggleWeekView">
                                            <i class="bi bi-calendar-week"></i> {{ T::translate('Week View', 'Lingguhang Tingnan') }}
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
                                <input type="text" class="form-control search-input" placeholder="     {{ T::translate('Search appointments', 'Maghanap ng mga appointment') }}..." aria-label="{{ T::translate('Search appointments', 'Maghanap ng mga appointment') }}">
                                <i class="bi bi-search"></i>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="action-buttons mb-4">
                                <button type="button" class="btn btn-primary action-btn" data-bs-toggle="modal" data-bs-target="#addAppointmentModal">
                                    <i class="bi bi-plus-circle"></i> {{ T::translate('Schedule New Appointment', 'Mag-iskedyul ng Bagong Appointment') }}
                                </button>
                                <button type="button" class="btn btn-outline-warning action-btn" id="editAppointmentButton" disabled>
                                    <i class="bi bi-pencil-square"></i> {{ T::translate('Edit Selected Appointment', 'I-edit ang Napiling Appointment') }}
                                </button>
                                <button type="button" class="btn btn-outline-danger action-btn" id="deleteAppointmentButton" disabled>
                                    <i class="bi bi-trash3"></i> {{ T::translate('Cancel Selected Appointment', 'Kanselahin ang Napiling Appointment') }}
                                </button>
                            </div>
                            
                            <!-- Appointment Details Panel -->
                            <div class="card details-container">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="section-heading mb-0">
                                        <i class="bi bi-info-circle"></i> {{ T::translate('Appointment Details', 'Detalye ng Appointment') }}
                                    </h5>
                                </div>
                                <div class="card-body" id="appointmentDetails">
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-calendar-event" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                        <p class="mt-3 mb-0">{{ T::translate('Select an appointment to view details', 'Pumili ng appointment upang makita ang detalye') }}</p>
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
                    <h5 class="modal-title" id="addAppointmentModalLabel">{{ T::translate('Add Appointment', 'Magdagdag ng Appointment') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ T::translate('Close', 'Isara') }}"></button>
                </div>
                <div class="modal-body">
                    <div id="modalErrorContainer" class="alert alert-danger mb-3" style="display: none;">
                        <h6 class="alert-heading mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>{{ T::translate('Please correct the following', 'Mangyaring itama ang sumusunod') }}:</h6>
                        <ul id="modalErrorList" class="mb-0 ms-3">
                            <!-- Errors will be inserted here dynamically -->
                        </ul>
                    </div>
                    
                    <div id="recurringWarningMessage" class="alert alert-warning mb-3" style="display: none;">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        <strong>{{ T::translate('Note', 'Paalala') }}:</strong> {{ T::translate('Editing a recurring appointment will only affect this and future occurrences. Past occurrences will remain unchanged. You cannot change a recurring appointment to a single appointment or vice versa.', 'Ang pag-edit ng umuulit na appointment ay makakaapekto lamang dito at sa mga mangyayari sa hinaharap. Ang mga nakaraang pangyayari ay mananatiling hindi magbabago. Hindi mo mababago ang umuulit na appointment sa isang solong appointment o kabaliktaran.') }}
                    </div>
                    <form id="addAppointmentForm">
                        <input type="hidden" id="appointmentId" name="appointment_id" value="">
                        <input type="hidden" id="edited_occurrence_date" name="edited_occurrence_date" value="">

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointmentTitle" class="form-label">{{ T::translate('Title', 'Pamagat') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="appointmentTitle" name="title" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointmentType" class="form-label">{{ T::translate('Type', 'Uri') }} <span class="text-danger">*</span></label>
                                    <select class="form-control" id="appointmentType" name="appointment_type_id" required>
                                        <option value="">{{ T::translate('Select type', 'Pumili ng uri') }}</option>
                                        @foreach($appointmentTypes as $type)
                                        <option value="{{ $type->appointment_type_id }}">{{ $type->type_name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Other type details -->
                        <div class="form-group mb-3" id="otherTypeContainer" style="display: none;">
                            <label for="otherAppointmentType" class="form-label">{{ T::translate('Specify Other Type', 'Tukuyin ang Iba pang Uri') }} <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="otherAppointmentType" name="other_type_details">
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointmentDate" class="form-label">{{ T::translate('Date', 'Petsa') }} <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="appointmentDate" name="date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointmentPlace" class="form-label">{{ T::translate('Location', 'Lokasyon') }} <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="appointmentPlace" name="meeting_location" required>
                                </div>
                            </div>
                        </div>

                        <!-- Flexible time option -->
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="flexibleTimeCheck" name="is_flexible_time">
                            <label class="form-check-label" for="flexibleTimeCheck">
                                {{ T::translate('Flexible time (no specific start/end time)', 'Flexible na oras (walang tiyak na oras ng pagsisimula/pagtatapos)') }}
                            </label>
                        </div>
                        
                        <!-- Time fields (shown when not flexible) -->
                        <div id="timeFieldsContainer" class="row mb-3">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointmentTime" class="form-label">{{ T::translate('Start Time', 'Oras ng Simula') }}</label>
                                    <input type="time" class="form-control" id="appointmentTime" name="start_time">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="appointmentEndTime" class="form-label">{{ T::translate('End Time', 'Oras ng Pagtatapos') }}</label>
                                    <input type="time" class="form-control" id="appointmentEndTime" name="end_time">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recurring appointment options -->
                        <div class="form-group">
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" id="recurringCheck" name="is_recurring">
                                <label class="form-check-label" for="recurringCheck">
                                    {{ T::translate('Recurring appointment', 'Umuulit na Appointment') }}
                                </label>
                            </div>

                            <div id="recurringOptions" class="border rounded p-3 mb-3" style="display: none;">
                                <div class="mb-3">
                                    <label class="form-label">{{ T::translate('Recurrence Pattern', 'Pattern ng Pag-ulit') }}</label>
                                    <div class="form-check">
                                        <input class="form-check-input pattern-radio" type="radio" name="pattern_type" id="patternDaily" value="daily">
                                        <label class="form-check-label" for="patternDaily">{{ T::translate('Daily', 'Araw-araw') }}</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input pattern-radio" type="radio" name="pattern_type" id="patternWeekly" value="weekly" checked>
                                        <label class="form-check-label" for="patternWeekly">{{ T::translate('Weekly', 'Lingguhan') }}</label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input pattern-radio" type="radio" name="pattern_type" id="patternMonthly" value="monthly">
                                        <label class="form-check-label" for="patternMonthly">{{ T::translate('Monthly', 'Buwanan') }}</label>
                                    </div>
                                </div>

                                <div class="mb-3" id="weeklyOptions">
                                    <label class="form-label">{{ T::translate('Repeat on', 'Ulitin sa') }}:</label>
                                    <div class="d-flex flex-wrap">
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" name="day_of_week[]" value="0" id="daySunday">
                                            <label class="form-check-label" for="daySunday">{{ T::translate('Sunday', 'Linggo') }}</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" name="day_of_week[]" value="1" id="dayMonday">
                                            <label class="form-check-label" for="dayMonday">{{ T::translate('Monday', 'Lunes') }}</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" name="day_of_week[]" value="2" id="dayTuesday">
                                            <label class="form-check-label" for="dayTuesday">{{ T::translate('Tuesday', 'Martes') }}</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" name="day_of_week[]" value="3" id="dayWednesday">
                                            <label class="form-check-label" for="dayWednesday">{{ T::translate('Wednesday', 'Miyerkules') }}</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" name="day_of_week[]" value="4" id="dayThursday">
                                            <label class="form-check-label" for="dayThursday">{{ T::translate('Thursday', 'Huwebes') }}</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" name="day_of_week[]" value="5" id="dayFriday">
                                            <label class="form-check-label" for="dayFriday">{{ T::translate('Friday', 'Biyernes') }}</label>
                                        </div>
                                        <div class="form-check me-3">
                                            <input class="form-check-input" type="checkbox" name="day_of_week[]" value="6" id="daySaturday">
                                            <label class="form-check-label" for="daySaturday">{{ T::translate('Saturday', 'Sabado') }}</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <label for="recurrenceEnd" class="form-label">{{ T::translate('End Date', 'Petsa ng Pagtatapos') }}</label>
                                    <input type="date" class="form-control" id="recurrenceEnd" name="recurrence_end">
                                </div>
                            </div>
                        </div>
                        
                        <!-- Attendees Section -->
                        <div class="form-group mb-4">
                            <label class="form-label">{{ T::translate('Attendees', 'Mga Dadalo') }}</label>
                            
                            <!-- COSE Staff Attendees -->
                            <div class="mb-3">
                                <label class="form-label fw-medium">{{ T::translate('COSE Staff', 'Tauhan ng COSE') }}</label>
                                <div class="attendees-container">
                                    <div class="attendees-input-container" id="staffAttendees">
                                        <!-- Selected staff attendees will appear here as tags -->
                                        <input type="text" class="attendees-input" id="staffSearch" placeholder="{{ T::translate('Type to search for staff', 'Mag-type upang maghanap ng tauhan') }}...">
                                    </div>
                                    <div class="attendees-dropdown staff-dropdown" id="staffDropdown">
                                        <div class="dropdown-section">
                                            <div class="dropdown-header">{{ T::translate('Administrators', 'Mga Administrator') }}</div>
                                            @foreach($usersByRole['administrators'] as $admin)
                                            <div class="attendee-option" data-id="{{ $admin->id }}" data-type="cose_user">
                                                <input type="checkbox" class="attendee-checkbox" name="participants[cose_user][]" value="{{ $admin->id }}">
                                                <span>{{ $admin->first_name }} {{ $admin->last_name }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                        <div class="dropdown-section">
                                            <div class="dropdown-header">{{ T::translate('Care Managers', 'Mga Tagapangasiwa ng Pangangalaga') }}</div>
                                            @foreach($usersByRole['care_managers'] as $manager)
                                            <div class="attendee-option" data-id="{{ $manager->id }}" data-type="cose_user">
                                                <input type="checkbox" class="attendee-checkbox" name="participants[cose_user][]" value="{{ $manager->id }}">
                                                <span>{{ $manager->first_name }} {{ $manager->last_name }}</span>
                                            </div>
                                            @endforeach
                                        </div>
                                        <div class="dropdown-section">
                                            <div class="dropdown-header">{{ T::translate('Care Workers', 'Mga Tagapag-alaga') }}</div>
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
                                <label class="form-label fw-medium">{{ T::translate('Beneficiaries', 'Benepisyaryo') }}</label>
                                <div class="attendees-container">
                                    <div class="attendees-input-container disabled" id="beneficiaryAttendees">
                                        <input type="text" class="attendees-input" id="beneficiarySearch" placeholder="{{ T::translate('Type to search for beneficiaries', 'Mag-type upang maghanap ng benepisyaryo') }}..." disabled>
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
                                <label class="form-label fw-medium">{{ T::translate('Family Members', 'Miyembro ng Pamilya') }}</label>
                                <div class="attendees-container">
                                    <div class="attendees-input-container disabled" id="familyAttendees">
                                        <input type="text" class="attendees-input" id="familySearch" placeholder="{{ T::translate('Type to search for family members', 'Mag-type upang maghanap ng miyembro ng pamilya') }}..." disabled>
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
                            <label for="appointmentNotes" class="form-label">{{ T::translate('Notes', 'Mga Tala') }}</label>
                            <textarea class="form-control" id="appointmentNotes" name="notes" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ T::translate('Cancel', 'Ikansela') }}</button>
                    <button type="button" class="btn btn-primary" id="submitAppointment"><i class="bi bi-plus-circle"></i> {{ T::translate('Create Appointment', 'Gumawa ng Appointment') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Cancellation Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="cancelModalLabel"><i class="bi bi-trash-fill"></i> {{ T::translate('Cancel Appointment', 'Ikansela ang Appointment') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ T::translate('Close', 'Isara') }}"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <p class="mb-1" id="cancelAppointmentDetails"></p>
                    </div>
                    
                    <div id="recurringCancelOptions" class="mb-3 border rounded p-3 bg-light" style="display:none;">
                        <p class="mb-2"><strong>{{ T::translate('Cancellation Options', 'Pagpipilian sa Pagkansela') }}:</strong></p>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="cancelOption" id="cancelSingle" value="single" checked>
                            <label class="form-check-label" for="cancelSingle">
                                {{ T::translate('Cancel only this occurrence', 'Kanselahin lamang ang pagkakataong ito') }}
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="cancelOption" id="cancelFuture" value="future">
                            <label class="form-check-label" for="cancelFuture">
                                {{ T::translate('Cancel this and all future occurrences', 'Kanselahin ito at lahat ng susunod na pagkakataon') }}
                            </label>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="cancelReason" class="form-label">{{ T::translate('Reason for Cancellation', 'Dahilan sa Pagkansela') }}</label>
                        <textarea class="form-control" id="cancelReason" rows="3" placeholder="{{ T::translate('Please provide a reason for cancellation', 'Mangyaring magbigay ng dahilan para sa pagkansela') }}..."></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label for="cancelPassword" class="form-label">{{ T::translate('Confirm your password', 'Kumpirmahin ang iyong password') }}</label>
                        <input type="password" class="form-control" id="cancelPassword" placeholder="{{ T::translate('Enter your password', 'Ilagay ang iyong password') }}">
                        <div id="passwordError" class="text-danger mt-1"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ T::translate('Close', 'Isara') }}</button>
                    <button type="button" class="btn btn-danger" id="confirmCancel">{{ T::translate('Confirm Cancellation', 'Kumpirmahin ang Pagkansela') }}</button>
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

            let currentSearchTerm = '';
            let isApplyingStoredSearch = false;
            let originalIsRecurring = false;
            let eventCache = new Map();  // <-- Moved for persistent filtering

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
                document.getElementById('addAppointmentModalLabel').innerHTML = '<i class="bi bi-plus-circle"></i> {{ T::translate('Add New Appointment', 'Magdagdag ng Bagong Appointment') }}';
                
                // Reset submit button text
                document.getElementById('submitAppointment').innerHTML = '<i class="bi bi-plus-circle"></i> {{ T::translate('Create Appointment', 'Gumawa ng Appointment') }}';
                
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
                    checkboxLabel.textContent = '{{ T::translate('Recurring appointment', 'Umuulit na Appointment') }}';
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
                document.getElementById('appointmentModalLabel').innerHTML = '<i class="bi bi-plus-circle"></i> {{ T::translate('Add New Appointment', 'Magdagdag ng Bagong Appointment') }}';
                
                // Reset submit button text
                document.getElementById('submitAppointment').innerHTML = '<i class="bi bi-plus-circle"></i> {{ T::translate('Create Appointment', 'Gumawa ng Appointment') }}';

                // Reset appointment ID to ensure we're in "create" mode
                document.getElementById('appointmentId').value = '';
                
                // THIS IS THE CRITICAL LINE THAT'S MISSING:
                currentEvent = null;
            });

            // Add this new function and call it right away
            function initializeAttendeeCheckboxes() {
                // Initialize checkboxes for all dropdowns immediately
                document.querySelectorAll('.attendees-dropdown').forEach(dropdown => {
                    dropdown.querySelectorAll('.attendee-option').forEach(option => {
                        // Check if option already has a checkbox
                        let checkbox = option.querySelector('.attendee-checkbox');
                        
                        // If no checkbox found or checkbox is just an attribute without an element
                        if (!checkbox || checkbox.tagName !== 'INPUT') {
                            // Remove any existing checkbox attribute
                            if (option.attributes.checkbox) {
                                option.removeAttribute('checkbox');
                            }
                            
                            // Create a proper checkbox element
                            checkbox = document.createElement('input');
                            checkbox.type = 'checkbox';
                            checkbox.className = 'attendee-checkbox';
                            checkbox.name = `participants[${option.dataset.type}][]`;
                            checkbox.value = option.dataset.id;
                            
                            // Insert checkbox as the first child of the option
                            option.insertBefore(checkbox, option.firstChild);
                            
                            // Remove any unwanted text nodes
                            Array.from(option.childNodes).forEach(node => {
                                if (node.nodeType === 3 && node.textContent.trim()) {
                                    node.remove();
                                }
                            });
                        }
                    });
                });
            }
            
            // Call the initialization function immediately
            initializeAttendeeCheckboxes();

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
            const isAssessmentOrOther = selectedTypeText === 'Assessment and Review of Care Needs' || selectedTypeText === 'Others';
            
            // Beneficiary options
            const beneficiaryAttendees = document.getElementById('beneficiaryAttendees');
            const beneficiarySearch = document.getElementById('beneficiarySearch');
            const beneficiaryCheckboxes = document.querySelectorAll('input[name="participants[beneficiary][]"]');
            
            if (beneficiaryAttendees) beneficiaryAttendees.classList.toggle('disabled', !isAssessmentOrOther);
            if (beneficiarySearch) beneficiarySearch.disabled = !isAssessmentOrOther;
            beneficiaryCheckboxes.forEach(checkbox => {
                checkbox.disabled = !isAssessmentOrOther;
            });
            
            // Family member options
            const familyAttendees = document.getElementById('familyAttendees');
            const familySearch = document.getElementById('familySearch');
            const familyCheckboxes = document.querySelectorAll('input[name="participants[family_member][]"]');
            
            if (familyAttendees) familyAttendees.classList.toggle('disabled', !isAssessmentOrOther);
            if (familySearch) familySearch.disabled = !isAssessmentOrOther;
            familyCheckboxes.forEach(checkbox => {
                checkbox.disabled = !isAssessmentOrOther;
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
            const isAssessmentOrOther = selectedTypeText === 'Assessment and Review of Care Needs' || selectedTypeText === 'Others';
            
            // Beneficiary options
            const beneficiaryAttendees = document.getElementById('beneficiaryAttendees');
            const beneficiarySearch = document.getElementById('beneficiarySearch');
            const beneficiaryCheckboxes = document.querySelectorAll('input[name="participants[beneficiary][]"]');
            
            if (beneficiaryAttendees) beneficiaryAttendees.classList.toggle('disabled', !isAssessmentOrOther);
            if (beneficiarySearch) beneficiarySearch.disabled = !isAssessmentOrOther;
            beneficiaryCheckboxes.forEach(checkbox => {
                checkbox.disabled = !isAssessmentOrOther;
            });
            
            // Family member options
            const familyAttendees = document.getElementById('familyAttendees');
            const familySearch = document.getElementById('familySearch');
            const familyCheckboxes = document.querySelectorAll('input[name="participants[family_member][]"]');
            
            if (familyAttendees) familyAttendees.classList.toggle('disabled', !isAssessmentOrOther);
            if (familySearch) familySearch.disabled = !isAssessmentOrOther;
            familyCheckboxes.forEach(checkbox => {
                checkbox.disabled = !isAssessmentOrOther;
            });
        });

        document.getElementById('resetCalendarButton').addEventListener('click', function() {
            // Clear search input
            const searchInput = document.querySelector('.search-input');
            if (searchInput) {
                searchInput.value = '';
                currentSearchTerm = '';
            }
            
            // Show spinner during reset
            showCalendarSpinner('{{ T::translate('Resetting calendar', 'Ini-reset ang kalendaryo') }}...');
            
            // Reset calendar view to month if not already
            if (currentView !== 'dayGridMonth') {
                calendar.changeView('dayGridMonth');
                toggleWeekButton.innerHTML = '<i class="bi bi-calendar-week"></i> {{ T::translate('Week View', 'Lingguhang Tingnan') }}';
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
                    <p class="mt-3 mb-0">{{ T::translate('Select an appointment to view details', 'Pumili ng appointment upang makita ang detalye') }}</p>
                </div>
            `;

             // Hide spinner when done
            setTimeout(() => {
                hideCalendarSpinner();
                showToast('{{ T::translate('Success', 'Tagumpay') }}', '{{ T::translate('Calendar reset successfully', 'Matagumpay na na-reset ang kalendaryo') }}', 'success');
            }, 300);
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
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="{{ T::translate('Close', 'Isara') }}"></button>
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
                    // Make sure checkboxes exist before showing dropdown
                    dropdown.querySelectorAll('.attendee-option').forEach(option => {
                        if (!option.querySelector('.attendee-checkbox')) {
                            const checkbox = document.createElement('input');
                            checkbox.type = 'checkbox';
                            checkbox.className = 'attendee-checkbox';
                            checkbox.name = `participants[${option.dataset.type}][]`;
                            checkbox.value = option.dataset.id;
                            option.insertBefore(checkbox, option.firstChild);
                        }
                    });
                    
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
                    const nameEl = option.querySelector('span');
                    if (nameEl) {
                        const name = nameEl.textContent.toLowerCase();
                        option.style.display = name.includes(searchTerm) ? 'block' : 'none';
                    }
                });
            });
            
            // Fix all attendee options to ensure they have checkboxes
            dropdown.querySelectorAll('.attendee-option').forEach(option => {
                // Check if option already has a checkbox
                let checkbox = option.querySelector('.attendee-checkbox');
                if (!checkbox) {
                    // If not, create and insert a checkbox
                    checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.className = 'attendee-checkbox';
                    checkbox.name = `participants[${option.dataset.type}][]`;
                    checkbox.value = option.dataset.id;
                    
                    // Insert checkbox as the first child of the option
                    option.insertBefore(checkbox, option.firstChild);
                    
                    // Remove any text nodes that might be present (like "flex")
                    option.childNodes.forEach(node => {
                        if (node.nodeType === 3 && node.textContent.trim()) {
                            node.remove();
                        }
                    });
                }
            });
            
            // Make entire attendee option clickable
            dropdown.addEventListener('click', function(e) {
                // Find the closest attendee-option from the click target
                const option = e.target.closest('.attendee-option');
                if (!option) return; // Click wasn't on an option
                
                // Don't handle if clicking directly on the checkbox
                if (e.target.type === 'checkbox') return;
                
                // Find or create checkbox inside this option
                let checkbox = option.querySelector('.attendee-checkbox');
                if (!checkbox) {
                    checkbox = document.createElement('input');
                    checkbox.type = 'checkbox';
                    checkbox.className = 'attendee-checkbox';
                    checkbox.name = `participants[${option.dataset.type}][]`;
                    checkbox.value = option.dataset.id;
                    option.insertBefore(checkbox, option.firstChild);
                }
                
                // Toggle checkbox
                checkbox.checked = !checkbox.checked;
                
                // Get the data we need
                const id = option.dataset.id;
                const type = option.dataset.type;
                const nameEl = option.querySelector('span');
                const name = nameEl ? nameEl.textContent : '{{ T::translate('Unknown', 'Hindi Kilala') }}';
                
                // Handle selection status
                if (checkbox.checked) {
                    addAttendeeTag(containerDiv, id, type, name);
                    option.classList.add('selected');
                } else {
                    removeAttendeeTag(containerDiv, id, type);
                    option.classList.remove('selected');
                }
            });
        }
        
        // Add attendee tag to the container
        function addAttendeeTag(container, id, type, name) {
            // Check if tag already exists
            const existingTag = container.querySelector(`.attendee-tag[data-id="${id}"][data-type="${type}"]`);
            if (existingTag) {
                return; // Tag already exists, don't add again
            }
            
            // Create tag element
            const tag = document.createElement('div');
            tag.className = 'attendee-tag';
            tag.dataset.id = id;
            tag.dataset.type = type;
            
            // Add text content
            const nameSpan = document.createElement('span');
            nameSpan.textContent = name;
            tag.appendChild(nameSpan);
            
            // Add remove button
            const removeBtn = document.createElement('span');
            removeBtn.className = 'attendee-remove';
            removeBtn.innerHTML = '<i class="bi bi-x"></i>';
            removeBtn.addEventListener('click', function() {
                removeAttendeeTag(container, id, type);
            });
            tag.appendChild(removeBtn);
            
            // Add tag to container
            container.insertBefore(tag, container.querySelector('.attendees-input'));
            
            // Create hidden input for form submission and add it to the FORM element
            const form = document.getElementById('addAppointmentForm');
            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = `participants[${type}][]`;
            hiddenInput.value = id;
            hiddenInput.className = `participant-input-${type}-${id}`; // Add a class for easier identification
            
            // Add the hidden input to the form element, not the container
            form.appendChild(hiddenInput);
        }
        
        // Remove attendee tag from the container
        function removeAttendeeTag(container, id, type) {
            // Remove the tag
            const tag = container.querySelector(`.attendee-tag[data-id="${id}"][data-type="${type}"]`);
            if (tag) {
                tag.remove();
            }
            
            // Remove the hidden input from the form
            const form = document.getElementById('addAppointmentForm');
            const input = form.querySelector(`.participant-input-${type}-${id}`);
            if (input) {
                input.remove();
            }
            
            // If it's a checkbox, uncheck it
            const checkbox = document.querySelector(`input[type="checkbox"][value="${id}"][data-type="${type}"]`);
            if (checkbox) {
                checkbox.checked = false;
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
                showCalendarSpinner('{{ T::translate('Loading appointments', 'Naglo-load ng mga appointment') }}...');
                // Fetch events from server based on date range
                fetch('/care-manager/internal-appointments/get-appointments?start=' + info.startStr + '&end=' + info.endStr + '&view_type=' + currentView, {
                    method: 'GET',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': csrf_token
                    }
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('{{ T::translate('Failed to load appointments', 'Nabigong i-load ang mga appointment') }}');
                    }
                    return response.json();
                })
                .then(data => {
                    // First provide the data to the calendar
                    successCallback(data);                    
                    // If there's an active search term, reapply it after the events are rendered
                    if (currentSearchTerm && currentSearchTerm.trim() !== '') {
                        // Clear the event cache when loading new data
                        eventCache.clear();

                        // Keep spinner visible during filtering with message
                        showCalendarSpinner('{{ T::translate('Filtering appointments', 'Pagsasala ng mga appointment') }}...');
                        
                        // Give calendar time to render the events
                        setTimeout(() => {
                            applySearchFilter(currentSearchTerm);

                            // Hide spinner only after filtering is complete
                            hideCalendarSpinner();
                        }, 150);
                    }
                    
                    // Only hide spinner immediately if no filtering is needed
                    hideCalendarSpinner();
                })
                .catch(error => {
                    console.error('{{ T::translate('Error loading appointments', 'Error sa pag-load ng mga appointment') }}:', error);
                    failureCallback(error);
                    hideCalendarSpinner();
            
                    // Show error message
                    showToast('{{ T::translate('Error', 'Error') }}', '{{ T::translate('Failed to load appointments', 'Nabigong i-load ang mga appointment') }}', 'error');
                });
            },
            eventContent: function(arg) {
                const event = arg.event;
                const timeFormat = { hour: '2-digit', minute: '2-digit', hour12: true };
                const startTime = event.start ? event.start.toLocaleTimeString([], timeFormat) : '';
                const endTime = event.end ? event.end.toLocaleTimeString([], timeFormat) : '';
                const isFlexibleTime = event.extendedProps.is_flexible_time;
                const timeText = isFlexibleTime ? '{{ T::translate('Flexible', 'Flexible') }}' : (startTime && endTime ? `${startTime} - ${endTime}` : startTime);
                
                let eventEl = document.createElement('div');
                eventEl.className = 'fc-event-main';
                
                if (arg.view.type === 'dayGridMonth') {
                    // Simplified view for month
                    eventEl.innerHTML = `
                        <div class="event-title">${event.title}</div>
                        <div class="event-details">
                            <div class="event-time"><i class="bi bi-clock"></i> ${isFlexibleTime ? '{{ T::translate('Flexible', 'Flexible') }}' : startTime}</div>
                        </div>
                    `;
                } else {
                    // Detailed view for week/day
                    eventEl.innerHTML = `
                        <div class="event-title">${event.title}</div>
                        <div class="event-details">
                            <div class="event-time"><i class="bi bi-clock"></i> ${isFlexibleTime ? '{{ T::translate('Flexible', 'Flexible') }}' : timeText}</div>
                            <div class="event-location"><i class="bi bi-geo-alt"></i> ${event.extendedProps.meeting_location || '{{ T::translate('No location', 'Walang lokasyon') }}'}</div>
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
                    
                    const timeText = isFlexibleTime ? '{{ T::translate('Flexible Scheduling', 'Flexible na Iskedyul') }}' : 
                        (startTime && endTime ? `${startTime} - ${endTime}` : startTime);
                    
                    let tooltipTitle = `${event.title}\n` +
                                `{{ T::translate('Time', 'Oras') }}: ${timeText}\n` +
                                `{{ T::translate('Location', 'Lokasyon') }}: ${event.extendedProps.meeting_location || ''}`;
                    
                    // Apply tooltip
                    arg.el.setAttribute('data-bs-toggle', 'tooltip');
                    arg.el.setAttribute('data-bs-placement', 'top');
                    arg.el.setAttribute('title', tooltipTitle);
                    new bootstrap.Tooltip(arg.el);
                }
            }, 
            datesSet: function(info) {
                // This fires when the calendar view changes (month/week/day) or navigates between periods
                
                // Only show spinner for initial page load or if we have an active search
                if (currentSearchTerm) {
                    showCalendarSpinner('{{ T::translate('Loading appointments', 'Naglo-load ng mga appointment') }}...');
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
                    attendeesList = '<li>{{ T::translate('No attendees specified', 'Walang tinukoy na mga dadalo') }}</li>';
                }
                
                // Check if recurring
                const isRecurring = event.extendedProps.recurring;
                let recurringInfo = '';
                if (isRecurring && event.extendedProps.recurring_pattern) {
                    const pattern = event.extendedProps.recurring_pattern;
                    const patternTypes = {
                        'daily': '{{ T::translate('Daily', 'Araw-araw') }}',
                        'weekly': '{{ T::translate('Weekly', 'Lingguhan') }}',
                        'monthly': '{{ T::translate('Monthly', 'Buwanan') }}'
                    };
                    
                    recurringInfo = `
                    <div class="detail-item">
                        <span class="detail-label">{{ T::translate('Recurrence', 'Pag-ulit') }}:</span>
                        <span class="detail-value">${patternTypes[pattern.type] || '{{ T::translate('Custom', 'Pasadyang') }}'}</span>
                    </div>
                    ${pattern.recurrence_end ? `
                    <div class="detail-item">
                        <span class="detail-label">{{ T::translate('Until', 'Hanggang') }}:</span>
                        <span class="detail-value">${new Date(pattern.recurrence_end).toLocaleDateString()}</span>
                    </div>
                    ` : ''}`;
                }
                
                appointmentDetailsEl.innerHTML = `
                    <div class="detail-section">
                        <div class="section-title"><i class="bi bi-calendar-event"></i> {{ T::translate('Appointment', 'Appointment') }}</div>
                        <h5 class="mb-2">${event.title}</h5>
                        <div class="detail-item">
                            <span class="detail-label">{{ T::translate('Type', 'Uri') }}:</span>
                            <span class="detail-value">${event.extendedProps.type}</span>
                        </div>
                        ${event.extendedProps.other_type_details ? `
                        <div class="detail-item">
                            <span class="detail-label">{{ T::translate('Details', 'Detalye') }}:</span>
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
                        <div class="section-title"><i class="bi bi-clock"></i> {{ T::translate('Schedule', 'Iskedyul') }}</div>
                        <div class="detail-item">
                            <span class="detail-label">{{ T::translate('Date', 'Petsa') }}:</span>
                            <span class="detail-value">${event.start ? event.start.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : '{{ T::translate('Not specified', 'Hindi tinukoy') }}'}</span>
                        </div>
                        ${!event.extendedProps.is_flexible_time ? `
                            <div class="detail-item">
                                <span class="detail-label">{{ T::translate('Time', 'Oras') }}:</span>
                                <span class="detail-value">${event.start ? event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true }) : ''} - ${event.end ? event.end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true }) : ''}</span>
                            </div>
                            ` : `
                            <div class="detail-item">
                                <span class="detail-label">{{ T::translate('Time', 'Oras') }}:</span>
                                <span class="detail-value"><span class="badge bg-info">{{ T::translate('Flexible Scheduling', 'Flexible na Iskedyul') }}</span></span>
                            </div>
                            `}
                        <div class="detail-item">
                            <span class="detail-label">{{ T::translate('Location', 'Lokasyon') }}:</span>
                            <span class="detail-value">${event.extendedProps.meeting_location || '{{ T::translate('Not specified', 'Hindi tinukoy') }}'}</span>
                        </div>
                        ${recurringInfo}
                    </div>
                    
                    <div class="detail-section">
                        <div class="section-title"><i class="bi bi-people"></i> {{ T::translate('Attendees', 'Mga Dadalo') }}</div>
                        <ul class="mb-0 ps-3">
                            ${attendeesList}
                        </ul>
                    </div>
                    
                    <div class="detail-section">
                        <div class="section-title"><i class="bi bi-journal-text"></i> {{ T::translate('Notes', 'Mga Tala') }}</div>
                        <p class="mb-0">${event.extendedProps.notes || '{{ T::translate('No notes available', 'Walang available na mga tala') }}'}</p>
                    </div>
                `;

                const status = event.extendedProps.status?.toLowerCase() || '';
                const isEditable = status !== 'completed' && status !== 'canceled';

                // Enable edit button only if editable as before
                editButton.disabled = !event.extendedProps.can_edit || !isEditable;

                // Determine if scheduled or completed
                const isScheduled = status === 'scheduled';
                const isCompleted = status === 'completed';
                const isFuture = event.start && event.start > new Date();

                // Cancel button logic
                if (event.extendedProps.can_edit && (isScheduled || isCompleted)) {
                    deleteButton.disabled = false;
                    if (isScheduled && isFuture) {
                        // Future scheduled appointment
                        deleteButton.innerHTML = '<i class="bi bi-trash3"></i>{{ T::translate("Cancel Selected Appointment", "Kanselahin ang Napiling Appointment") }}';
                    } else {
                        // Past/present scheduled or completed appointment
                        deleteButton.innerHTML = '<i class="bi bi-x-circle"></i>{{ T::translate("Mark as Canceled", "Markahan Bilang Kinansela") }}';
                    }
                } else {
                    deleteButton.disabled = true;
                    deleteButton.innerHTML = '<i class="bi bi-trash3"></i>{{ T::translate("Cancel Selected Appointment", "Kanselahin ang Napiling Appointment") }}';
                }

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
                toggleWeekButton.innerHTML = '<i class="bi bi-calendar-month"></i> {{ T::translate('Month View', 'Buwanang Tingnan') }}';
                currentView = 'timeGridWeek';
            } else {
                calendar.changeView('dayGridMonth');
                toggleWeekButton.innerHTML = '<i class="bi bi-calendar-week"></i> {{ T::translate('Week View', 'Lingguhang Tingnan') }}';
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

            // Remove all participant hidden inputs
            document.querySelectorAll('input[name^="participants["]').forEach(input => {
                input.remove();
            });
            
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
                submitButton.innerHTML = '<i class="bi bi-plus-circle"></i> {{ T::translate('Create Appointment', 'Gumawa ng Appointment') }}';
            }
        }
        
        // Search functionality
        const searchInput = document.querySelector('.search-input');
        if (searchInput) {
            let searchTimeout = null;
            
            // Create a search spinner container and add it to the DOM
            const searchSpinner = document.createElement('div');
            searchSpinner.className = 'search-spinner';
            searchSpinner.innerHTML = `
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">{{ T::translate('Searching', 'Naghahanap') }}...</span>
                </div>
            `;
            document.querySelector('.search-container').appendChild(searchSpinner);
            
            // Add CSS for the search spinner
            const spinnerStyle = document.createElement('style');
            spinnerStyle.textContent = `
                .search-spinner {
                    position: absolute;
                    top: calc(50% - 10px);
                    right: 10px;
                    display: none;
                    z-index: 100;
                }
                .search-spinner .spinner-border {
                    width: 20px;
                    height: 20px;
                }
                .searching .search-spinner {
                    display: block;
                }
            `;
            document.head.appendChild(spinnerStyle);
            
            searchInput.addEventListener('input', function(e) {
                // Show spinner and set up UI
                this.classList.add('searching');
                searchSpinner.style.display = 'block';
                
                // Force browser to repaint spinner before continuing
                if (this.value.length > 1) {
                    // Show the calendar spinner
                    showCalendarSpinner('{{ T::translate('Searching appointments', 'Naghahanap ng mga appointment') }}...');
                    
                    // Force a browser reflow/repaint to ensure spinner is visible
                    document.getElementById('calendar-spinner').getBoundingClientRect();
                }
                
                // Clear any existing timeout
                if (searchTimeout) {
                    clearTimeout(searchTimeout);
                }
                
                // IMPORTANT: Use zero-delay timeout to let the UI update with the spinner before filtering
                setTimeout(() => {
                    // Set longer timeout for the actual search operation
                    searchTimeout = setTimeout(() => {
                        // Store the current search term
                        currentSearchTerm = this.value.toLowerCase().trim();
                        
                        // Skip full filtering for empty search or very short terms
                        if (!currentSearchTerm || currentSearchTerm.length < 2) {
                            // Simple reset for empty searches - much faster
                            calendar.getEvents().forEach(event => {
                                event.setProp('display', 'block');
                            });
                            
                            // Hide spinners quickly for empty searches
                            hideCalendarSpinner();
                            this.classList.remove('searching');
                            searchSpinner.style.display = 'none';
                            return;
                        }
                        
                        // We'll break the filtering into chunks using requestAnimationFrame
                        requestAnimationFrame(() => {
                            // Start the filtering process
                            applySearchFilter(currentSearchTerm);
                            
                            // Hide spinners AFTER filtering is complete
                            hideCalendarSpinner();
                            this.classList.remove('searching');
                            searchSpinner.style.display = 'none';
                        });
                    }, 700); // Reduced timeout for better responsiveness
                }, 0); // Zero-delay timeout to ensure UI updates first
            });

            // Extract search filter logic to a reusable function
            function applySearchFilter(searchTerm) {
                if (isApplyingStoredSearch) return; // Prevent recursion
                
                // Get all events
                const allEvents = calendar.getEvents();
                
                // Cache event data if needed
                allEvents.forEach(event => {
                    const eventId = event.id;
                    if (!eventCache.has(eventId)) {
                        eventCache.set(eventId, {
                            title: (event.title || '').toLowerCase(),
                            type: (event.extendedProps.type || '').toLowerCase(),
                            location: (event.extendedProps.meeting_location || '').toLowerCase(),
                            notes: (event.extendedProps.notes || '').toLowerCase(),
                            date: event.start ? event.start.toLocaleDateString().toLowerCase() : '',
                            participants: event.extendedProps.participants && Array.isArray(event.extendedProps.participants) ?
                                event.extendedProps.participants
                                    .map(p => (p && p.name) ? p.name.toLowerCase() : '')
                                    .filter(name => name)
                                    .join(' ') : '',
                            bgColor: event.extendedProps.backgroundColor || event.backgroundColor,
                            status: event.extendedProps.status?.toLowerCase() || ''
                        });
                    }
                });
                
                // If search is empty, reset all events
                if (!searchTerm) {
                    allEvents.forEach(event => {
                        event.setProp('display', 'block');
                        
                        const cachedData = eventCache.get(event.id);
                        if (cachedData && cachedData.bgColor) {
                            event.setProp('backgroundColor', cachedData.bgColor);
                            event.setProp('borderColor', cachedData.bgColor);
                        }
                    });
                    return;
                }
                
                // Track matches for feedback message
                let matchCount = 0;
                
                // Process all events
                allEvents.forEach(event => {
                    const cachedData = eventCache.get(event.id);
                    if (!cachedData) return;
                    
                    // Check if any field includes the search term
                    const matches = 
                        cachedData.title.includes(searchTerm) || 
                        cachedData.type.includes(searchTerm) || 
                        cachedData.location.includes(searchTerm) || 
                        cachedData.notes.includes(searchTerm) || 
                        cachedData.participants.includes(searchTerm) ||
                        cachedData.date.includes(searchTerm);
                    
                    // Toggle visibility based on match
                    event.setProp('display', matches ? 'block' : 'none');
                    
                    if (matches) {
                        matchCount++;
                        
                        // Restore colors for matches
                        if (cachedData.bgColor) {
                            event.setProp('backgroundColor', cachedData.bgColor);
                            event.setProp('borderColor', cachedData.bgColor);
                        }
                    }
                });
                
                // Apply special styling for canceled events
                setTimeout(() => {
                    document.querySelectorAll('.fc-event.canceled').forEach(el => {
                        if (el.style.display !== 'none') {
                            el.classList.add('canceled');
                        }
                    });
                }, 50);
                
                // Show feedback if no matches and search term is significant
                if (matchCount === 0 && searchTerm.length >= 2 && !isApplyingStoredSearch) {
                    showToast('{{ T::translate('Search', 'Paghahanap') }}', '{{ T::translate('No appointments match your search criteria', 'Walang appointment na tumutugma sa iyong pamantayan sa paghahanap') }}', 'info');
                }
            }
        }



        function showCalendarSpinner(message = '{{ T::translate('Loading appointments', 'Naglo-load ng mga appointment') }}...') {
            const spinner = document.getElementById('calendar-spinner');
            if (spinner) {
                // Update message if provided
                const messageEl = spinner.querySelector('p');
                if (messageEl) messageEl.textContent = message;
                
                // Show spinner by adding class
                spinner.classList.add('show');
            }
        }

        function hideCalendarSpinner() {
            const spinner = document.getElementById('calendar-spinner');
            if (spinner) {
                spinner.classList.remove('show');
            }
        }

        const spinnerStyles = document.createElement('style');
        spinnerStyles.textContent = `
            #calendar-container {
                position: relative;
            }
            
            #calendar-spinner {
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background-color: rgba(255, 255, 255, 0.8);
                display: flex;
                justify-content: center;
                align-items: center;
                z-index: 1050;
                opacity: 0;
                visibility: hidden;
                transition: opacity 0.2s ease;
            }

            /* These are the new styles that fix the alignment */
            .spinner-container {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-align: center;
            }
            
            .spinner-container .spinner-border {
                margin-bottom: 0.75rem;
            }
            
            #calendar-spinner.show {
                opacity: 1;
                visibility: visible;
            }
        `;
        document.head.appendChild(spinnerStyles);
        
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
                    modalTitle.textContent = '{{ T::translate('Edit Appointment', 'I-Edit ang Appointment')}}';
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
                document.getElementById('edited_occurrence_date').value = currentEvent.startStr.split('T')[0];
                console.log("Setting edited occurrence date to original occurrence date:", currentEvent.startStr.split('T')[0]);

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
                    recurringCheckbox.title = "{{ T::translate('Converting between recurring and non-recurring is not supported', 'Hindi sinusuportahan ang pag-convert sa pagitan ng umuulit at hindi umuulit') }}";
                    
                    // Add visual indicator next to the checkbox
                    const checkboxLabel = recurringCheckbox.nextElementSibling;
                    if (checkboxLabel) {
                        // Add lock icon to indicate it's locked
                        checkboxLabel.innerHTML += ' <i class="bi bi-lock-fill text-secondary" title="{{ T::translate('Cannot be changed', 'Hindi maaring mabago')}}"></i>';
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
                            console.log("{{ T::translate('Defaulted to weekly pattern', 'Ibinalik sa lingguhang pattern')}}");
                        } else {
                            console.error("{{ T::translate('Could not find any pattern radio buttons', 'Walang nahanap na mga pattern na radio button')}}");
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
            
             // Deduplicate participants before processing
            const uniqueParticipants = deduplicateParticipants(participants);

            // Process participants using the new function
            processParticipantsForEdit(participants);
        }
        
        function processParticipantsForEdit(participants) {
            if (!participants) return;
            
            // Group participants by type
            const groupedParticipants = {
                cose_user: [],
                beneficiary: [],
                family_member: []
            };
            
            participants.forEach(p => {
                if (p && p.type && groupedParticipants[p.type]) {
                    groupedParticipants[p.type].push(p);
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

            // Store a reference to the button
            const submitButton = this;

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
                '/care-manager/internal-appointments/update' : 
                '/care-manager/internal-appointments/store';

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
                        !startTime ? '{{ T::translate('Start time is required when flexible time is not selected', 'Ang oras ng simula ay kinakailangan kapag ang flexible time ay di-napili')}}.' : null,
                        !endTime ? '{{ T::translate('End time is required when flexible time is not selected', 'Ang oras ng pagtatapos ay kailangan kapag ang flexible time ay di-napili')}}.' : null
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
                        
                        // IMPORTANT: Add a slight delay to show the toast after modal is hidden
                        setTimeout(function() {
                            // Show success message
                            showToast('Success', 
                                isEditing ? 'Appointment updated successfully!' : 'Appointment created successfully!',
                                'success');
                            
                            // Refresh calendar
                            calendar.refetchEvents();
                        }, 300);
                    } else {
                        // Show error message
                        showModalErrors([response.message || 'An unknown error occurred']);

                        // Always re-enable the button on error
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalBtnHtml;
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
                        showModalErrors(['{{ T::translate('An error occurred while saving the appointment. Please try again.', 'May error na naganap habang sine-save ang appointment. Pakisubukan muli.')}}']);
                    }

                    // Always re-enable the button on error
                    submitButton.disabled = false;
                    submitButton.innerHTML = originalBtnHtml;

                },
                complete: function() {
                    // Reset button state
                    document.getElementById('submitAppointment').disabled = false;
                    document.getElementById('submitAppointment').innerHTML = originalBtnHtml;

                    // Reset button state if modal is still open
                    if (document.getElementById('addAppointmentModal').classList.contains('show')) {
                        submitButton.disabled = false;
                        submitButton.innerHTML = originalBtnHtml;
                    }
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
            fetch('/care-manager/internal-appointments/cancel', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    if (response.status === 500) {
                        throw new Error('{{ T::translate('Server error: The server encountered an issue. Please try again later.', 'Error sa server: Ang server ay nakatagpo ng isyu. Pakisubukan muli mamamaya.') }}');
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
                showToast('Success', data.message || '{{ T::translate('Appointment cancelled successfully', 'Ang appointment ay matagumpay na na-kansela')}}', 'success');
                
                // Update the calendar
                calendar.refetchEvents();
                
                // Clear the details panel
                if (appointmentDetailsEl) {
                    appointmentDetailsEl.innerHTML = '<div class="alert alert-info">{{ T::translate('Select an appointment to view details', 'Pumili ng appointment upang makita ang detalye')}}</div>';
                }
                
                // Clear current event selection
                currentEvent = null;
                editButton.disabled = true;
                deleteButton.disabled = true;
            })
            .catch(error => {
                console.error('Error:', error);
                showToast('Error', error.message || '{{ T::translate('An unexpected error occurred', 'Isang hindi inaasahang error ang naganap')}}', 'danger');
            })
            .finally(() => {
                // Reset button state
                confirmButton.disabled = false;
                confirmButton.innerHTML = originalButtonText;
            });
        });

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