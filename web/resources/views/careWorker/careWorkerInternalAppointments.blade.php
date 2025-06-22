<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <title>Internal Appointment | Care Worker</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/internalAppointment.css')}}">
    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
</head>
<body>

    @php
    use App\Helpers\TranslationHelper as T;
    @endphp

    @include('components.careWorkerNavbar')
    @include('components.careWorkerSidebar')

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
                                            <span class="visually-hidden">{{ T::translate('Loading', 'Naglo-load') }}</span>
                                        </div>
                                        <p class="mt-2 text-primary">{{ T::translate('Loading appointments...', 'Naglo-load ng mga appointment...') }}</p>
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
                                <input type="text" class="form-control search-input" placeholder="     {{ T::translate('Search appointments...', 'Maghanap ng mga appointment...') }}" aria-label="{{ T::translate('Search appointments', 'Maghanap ng mga appointment') }}">
                                <i class="bi bi-search"></i>
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
                currentSearchTerm = '';
            }
            
            // Show spinner during reset
            showCalendarSpinner('{{ T::translate("Resetting calendar...", "Ine-reset ang kalendaryo...") }}');
            
            // Reset calendar view to month if not already
            if (currentView !== 'dayGridMonth') {
                calendar.changeView('dayGridMonth');
                toggleWeekButton.innerHTML = '<i class="bi bi-calendar-week"></i> {{ T::translate("Week View", "Lingguhang Tingnan") }}';
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
                showToast('{{ T::translate("Success", "Tagumpay") }}', '{{ T::translate("Calendar reset successfully", "Matagumpay na na-reset ang kalendaryo") }}', 'success');
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
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="{{ T::translate("Close", "Isara") }}"></button>
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
                showCalendarSpinner('{{ T::translate("Loading appointments...", "Naglo-load ng mga appointment...") }}');
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
                        throw new Error('{{ T::translate("Failed to load appointments", "Nabigong i-load ang mga appointment") }}');
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
                        showCalendarSpinner('{{ T::translate("Filtering appointments...", "Pinipino ang mga appointment...") }}');
                        
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
                    console.error('{{ T::translate("Error loading appointments:", "Error sa pag-load ng mga appointment:") }}', error);
                    failureCallback(error);
                    hideCalendarSpinner();
            
                    // Show error message
                    showToast('{{ T::translate("Error", "Error") }}', '{{ T::translate("Failed to load appointments", "Nabigong i-load ang mga appointment") }}', 'error');
                });
            },
            eventContent: function(arg) {
                const event = arg.event;
                const timeFormat = { hour: '2-digit', minute: '2-digit', hour12: true };
                const startTime = event.start ? event.start.toLocaleTimeString([], timeFormat) : '';
                const endTime = event.end ? event.end.toLocaleTimeString([], timeFormat) : '';
                const isFlexibleTime = event.extendedProps.is_flexible_time;
                const timeText = isFlexibleTime ? '{{ T::translate("Flexible", "Flexible") }}' : (startTime && endTime ? `${startTime} - ${endTime}` : startTime);
                
                let eventEl = document.createElement('div');
                eventEl.className = 'fc-event-main';
                
                if (arg.view.type === 'dayGridMonth') {
                    // Simplified view for month
                    eventEl.innerHTML = `
                        <div class="event-title">${event.title}</div>
                        <div class="event-details">
                            <div class="event-time"><i class="bi bi-clock"></i> ${isFlexibleTime ? '{{ T::translate("Flexible", "Flexible") }}' : startTime}</div>
                        </div>
                    `;
                } else {
                    // Detailed view for week/day
                    eventEl.innerHTML = `
                        <div class="event-title">${event.title}</div>
                        <div class="event-details">
                            <div class="event-time"><i class="bi bi-clock"></i> ${isFlexibleTime ? '{{ T::translate("Flexible", "Flexible") }}' : timeText}</div>
                            <div class="event-location"><i class="bi bi-geo-alt"></i> ${event.extendedProps.meeting_location || '{{ T::translate("No location", "Walang lokasyon") }}'}</div>
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
                    
                    const timeText = isFlexibleTime ? '{{ T::translate("Flexible Scheduling", "Flexible na Oras") }}' : 
                        (startTime && endTime ? `${startTime} - ${endTime}` : startTime);
                    
                    let tooltipTitle = `${event.title}\n` +
                                `{{ T::translate("Time", "Oras") }}: ${timeText}\n` +
                                `{{ T::translate("Location", "Lokasyon") }}: ${event.extendedProps.meeting_location || ''}`;
                    
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
                    showCalendarSpinner('{{ T::translate("Loading appointments...", "Naglo-load ng mga appointment...") }}');
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
                        `<li>${p.name} ${p.is_organizer ? '<span class="badge bg-info">{{ T::translate("Organizer", "Organisador") }}</span>' : ''}</li>`
                    ).join('');
                } else {
                    attendeesList = '<li>{{ T::translate("No attendees specified", "Walang tinukoy na mga dadalo") }}</li>';
                }
                
                // Check if recurring
                const isRecurring = event.extendedProps.recurring;
                let recurringInfo = '';
                if (isRecurring && event.extendedProps.recurring_pattern) {
                    const pattern = event.extendedProps.recurring_pattern;
                    const patternTypes = {
                        'daily': '{{ T::translate("Daily", "Araw-araw") }}',
                        'weekly': '{{ T::translate("Weekly", "Lingguhan") }}',
                        'monthly': '{{ T::translate("Monthly", "Buwanan") }}'
                    };
                    
                    recurringInfo = `
                    <div class="detail-item">
                        <span class="detail-label">{{ T::translate("Recurrence:", "Pag-ulit:") }}</span>
                        <span class="detail-value">${patternTypes[pattern.type] || 'Custom'}</span>
                    </div>
                    ${pattern.recurrence_end ? `
                    <div class="detail-item">
                        <span class="detail-label">{{ T::translate("Until:", "Hanggang:") }}</span>
                        <span class="detail-value">${new Date(pattern.recurrence_end).toLocaleDateString()}</span>
                    </div>
                    ` : ''}`;
                }
                
                appointmentDetailsEl.innerHTML = `
                    <div class="detail-section">
                        <div class="section-title"><i class="bi bi-calendar-event"></i>Appointment</div>
                        <h5 class="mb-2">${event.title}</h5>
                        <div class="detail-item">
                            <span class="detail-label">{{ T::translate('Type:', 'Uri:') }}</span>
                            <span class="detail-value">${event.extendedProps.type}</span>
                        </div>
                        ${event.extendedProps.other_type_details ? `
                        <div class="detail-item">
                            <span class="detail-label">{{ T::translate('Details:', 'Detalye:') }}</span>
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
                            <span class="detail-label">{{ T::translate("Date:", "Petsa:") }}</span>
                            <span class="detail-value">${event.start ? event.start.toLocaleDateString(undefined, { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) : '{{ T::translate("Not specified", "Hindi tinukoy") }}'}</span>
                        </div>
                        ${!event.extendedProps.is_flexible_time ? `
                            <div class="detail-item">
                                <span class="detail-label">{{ T::translate("Time:", "Oras:") }}</span>
                                <span class="detail-value">${event.start ? event.start.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true }) : ''} - ${event.end ? event.end.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit', hour12: true }) : ''}</span>
                            </div>
                            ` : `
                            <div class="detail-item">
                                <span class="detail-label">{{ T::translate("Time:", "Oras:") }}</span>
                                <span class="detail-value"><span class="badge bg-info">{{ T::translate('Flexible Scheduling', 'Flexible na Oras') }}</span></span>
                            </div>
                            `}
                        <div class="detail-item">
                            <span class="detail-label">{{ T::translate("Location:", "Lokasyon:") }}</span>
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
            let searchTimeout = null;
            
            // Create a search spinner container and add it to the DOM
            const searchSpinner = document.createElement('div');
            searchSpinner.className = 'search-spinner';
            searchSpinner.innerHTML = `
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Searching...</span>
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
                    showCalendarSpinner('Searching appointments...');
                    
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
                    showToast('Search', 'No appointments match your search criteria', 'info');
                }
            }
        }



        function showCalendarSpinner(message = 'Loading appointments...') {
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