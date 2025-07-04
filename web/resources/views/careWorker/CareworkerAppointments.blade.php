<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Care Worker Scheduling | Care Worker</title>
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
    @include('components.careWorkerNavbar')
    @include('components.careWorkerSidebar')

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
                                        <i class="bi bi-calendar3"></i> {{ T::translate('Appointment Calendar', 'Kalendaryo ng Appointment')}}
                                    </h5>
                                    <div class="calendar-actions d-flex gap-2">
                                        <button type="button" class="btn btn-sm btn-outline-primary" id="toggleWeekView">
                                            <i class="bi bi-calendar-week"></i> {{ T::translate('Week View', 'Lingguhang Tingnan')}}
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body p-0">
                                    <div id="calendar-container">
                                        <div id="calendar-loading-indicator" style="display: none; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); z-index: 10;">
                                            <div class="spinner-border text-primary" role="status">
                                                <span class="visually-hidden">{{ T::translate('Loading', 'Naglo-load')}}...</span>
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
                                <input type="text" id="searchInput" class="form-control search-input" placeholder="     {{ T::translate('Search appointments', 'Maghanap ng mga appointment')}}..." aria-label="Search appointments">
                                <i class="bi bi-search"></i>
                            </div>
                            
                            <!-- Appointment Details Panel -->
                            <div class="card details-container">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <h5 class="section-heading mb-0">
                                        <i class="bi bi-info-circle"></i> {{ T::translate('Appointment Details', 'Mga Detalye ng Appointment')}}
                                    </h5>
                                </div>
                                <div class="card-body" id="appointmentDetails">
                                    <div class="text-center text-muted py-4">
                                        <i class="bi bi-calendar-event" style="font-size: 2.5rem; opacity: 0.3;"></i>
                                        <p class="mt-3 mb-0">{{ T::translate('Select an appointment to view details', 'Pumili ng Appointment upang makita ang mga detalye')}}</p>
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
                
                // Cache-busting timestamp
                const timestamp = new Date().getTime();
                
                // Set a timeout to prevent UI freezing if server is slow
                const timeoutId = setTimeout(() => {
                    if (document.getElementById('calendar-loading-indicator')) {
                        document.getElementById('calendar-loading-indicator').style.display = 'none';
                    }
                    failureCallback({ message: "Request timed out" });
                    showErrorModal('{{ T::translate('Loading took too long. Try viewing a smaller date range or reset the calendar.', 'Masyadong tumagal ang paglo-load. Subukang tumingin ng mas maliit na hanay ng petsa o i-reset ang kalendaryo.')}}');
                }, 15000); // 15 seconds timeout
                
                $.ajax({
                    url: '/care-worker/careworker-appointments/get-visitations',
                    method: 'GET',
                    data: {
                        start: start,
                        end: end,
                        search: searchTerm,
                        cache_buster: timestamp,
                        care_worker_id: careWorkerId,
                        view_type: info.view ? info.view.type : 'dayGridMonth' // Safe access with fallback
                    },
                    success: function(response) {
                        clearTimeout(timeoutId);
                        successCallback(response);
                        
                        // FIX: Safe access to view title with fallback
                        const viewName = info.view && info.view.title ? info.view.title : 'current view';
                        console.log(`Loaded ${response.length} events for ${viewName}` + (searchTerm ? ` with search: "${searchTerm}"` : ''));
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
            // Update reset button code (should be around line 564 in your full file)
            resetButton.addEventListener('click', function() {
                // Show loading indicator
                document.getElementById('calendar-loading-indicator').style.display = 'block';
                
                // Clear search input and make sure the change is detected
                if (document.getElementById('searchInput')) {
                    document.getElementById('searchInput').value = '';
                    
                    // Force the calendar to recognize the search change
                    const searchEvent = new Event('input', { bubbles: true });
                    document.getElementById('searchInput').dispatchEvent(searchEvent);
                }
                
                // Reset calendar
                calendar.removeAllEvents();
                calendar.today();
                
                // Use explicit parameter when refetching to ensure search term is included
                calendar.refetchEvents();
                
                // Hide loading indicator after a short delay
                setTimeout(function() {
                    document.getElementById('calendar-loading-indicator').style.display = 'none';
                    showToast('Success', 'Calendar reset successfully', 'success');
                }, 1000);
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
                url: '/care-worker/careworker-appointments/beneficiaries',
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
                    console.error('{{ T::translate('Failed to load beneficiaries', 'Nabigong i-load ang mga benepisyaryo')}}:', xhr);
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
                     url: `/care-worker/careworker-appointments/beneficiary/${beneficiaryId}`,
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
                        document.getElementById('beneficiaryAddress').value = '{{ T::translate('Error loading details', 'Error sa pagload ng mga detalye')}}';
                        document.getElementById('beneficiaryPhone').value = '{{ T::translate('Error loading details', 'Error sa pagload ng mga detalye')}}';
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
                    toggleWeekButton.innerHTML = '<i class="bi bi-calendar-month"></i> {{ T::translate('Month View', 'Buwanang Tingnan')}}';
                } else {
                    currentView = 'dayGridMonth';
                    calendar.changeView('dayGridMonth');
                    toggleWeekButton.innerHTML = '<i class="bi bi-calendar-week"></i> {{ T::translate('Week View', 'Lungguhang Tingnan')}}';
                }
            });
        }
        
        // Show event details in side panel
        function showEventDetails(event) {
            
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
                        <div class="section-title"><i class="bi bi-calendar-date-fill"></i> {{ T::translate('Visit Details', 'Mga Detalye ng Pagbisita')}}</div>
                        <div class="detail-item">
                            <div class="detail-label">{{ T::translate('Date', 'Petsa')}}:</div>
                            <div class="detail-value">${formattedDate}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">{{ T::translate('Time', 'Oras')}}:</div>
                            <div class="detail-value">${event.extendedProps.is_flexible_time ? 'Flexible Time' : 
                                (formatTime(event.start) + ' - ' + formatTime(event.end))}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">{{ T::translate('Type', 'Uri')}}:</div>
                            <div class="detail-value">${event.extendedProps.visit_type}</div>
                        </div>
                        <div class="detail-item">
                            <div class="detail-label">Status:</div>
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
        
        // Search functionality with immediate refresh
        const searchInput = document.getElementById('searchInput');
        if (searchInput) {
            searchInput.addEventListener('input', debounce(function() {
                // Show loading indicator
                document.getElementById('calendar-loading-indicator').style.display = 'block';
                
                // Simply refetch events - the events function will use the current search term
                calendar.refetchEvents();
                
                // Log for debugging
                console.log('Searching for:', searchInput.value);
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