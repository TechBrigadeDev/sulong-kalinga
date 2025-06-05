<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Visitation Schedule</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/familyPortalHomePage.css') }}"> 
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    
    <style>
        /* Calendar styles */
        .calendar-view-toggle .btn-group button {
            font-size: 0.85rem;
        }

        .card-header {
            padding: 1rem;
        }

        .fc .fc-toolbar.fc-header-toolbar{
            padding-top: 1rem;
        }
        /* Schedule cards */
        .schedule-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .schedule-card-header {
            display: flex;
            justify-content: space-between;
            padding: 10px 15px;
            background-color: #f8f9fa;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            border-bottom: 1px solid #dee2e6;
        }
        
        .schedule-date {
            font-weight: 500;
        }
        
        .schedule-status {
            font-size: 0.8rem;
            font-weight: 600;
            padding: 3px 8px;
            border-radius: 12px;
        }
        
        .status-scheduled {
            background-color: #e7effd;
            color: #4e73df;
        }
        
        .status-completed {
            background-color: #e6f8f1;
            color: #1cc88a;
        }
        
        .status-canceled {
            background-color: #fce8e8;
            color: #e74a3b;
        }
        
        .schedule-card-body {
            padding: 12px 15px;
        }
        
        .schedule-detail-item {
            margin-bottom: 6px;
            display: flex;
        }
        
        .schedule-detail-label {
            font-weight: 500;
            min-width: 90px;
            color: #495057;
            margin-right: 5px;  /* Add spacing between label and value */
        }
        
        .schedule-detail-value {
            color: #343a40;
        }
        
        /* Filter styles */
        .schedule-filter select {
            border-radius: 6px;
            font-size: 0.85rem;
        }
        
        /* Modal styling */
        .modal-header {
            background-color: #4e73df;
            color: white;
        }
        
        /* Event styling */
        .fc-event {
            border-radius: 4px;
            cursor: pointer;
        }

        /* Loading state for calendar */
        #calendar-container.loading {
            position: relative;
        }

        #calendar-container.loading::after {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.7);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 10;
        }

        #calendar-container.loading::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 30px;
            height: 30px;
            border: 3px solid #f3f3f3;
            border-top: 3px solid #3498db;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            z-index: 11;
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }
    </style>
</head>
<body>
    @include('components.beneficiaryPortalNavbar')
    @include('components.beneficiaryPortalSidebar')

    <div class="home-section">
        <div class="text-left">VISITATION SCHEDULE</div>
        <div class="container-fluid">
            <div class="row" id="home-content">
                <!-- Main Content Row with 2 columns -->
                <div class="row row-cols-1 row-cols-lg-2">
                    <!-- Calendar Column -->
                    <div class="col-lg-7">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <h5 class="mb-0">
                                    <i class="bi bi-calendar3"></i> Visitation Calendar
                                </h5>
                                <div class="calendar-view-toggle">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <button type="button" class="btn btn-outline-primary active" id="monthViewBtn">
                                            <i class="bi bi-calendar-month"></i> Month
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" id="weekViewBtn">
                                            <i class="bi bi-calendar-week"></i> Week
                                        </button>
                                        <button type="button" class="btn btn-outline-primary" id="dayViewBtn">
                                            <i class="bi bi-calendar-day"></i> Day
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body p-2">
                                <div id="calendar-container">
                                    <div id="calendar"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Upcoming Visits Column -->
                    <div class="col-lg-5">
                        <div class="card h-100">
                            <div class="card-header mb-0">
                                <h5 class="mb-0">
                                    <i class="bi bi-list-check"></i> Upcoming Visits
                                </h5>
                            </div>
                            <div class="card-body visits-column">
                                <!-- Filter Options -->
                                <div class="schedule-filter mb-3">
                                    <div class="row">
                                        <div class="col-md-6 mb-2 mb-md-0">
                                            <label for="statusFilter" class="form-label">Filter by Status</label>
                                            <select class="form-select" id="statusFilter">
                                                <option value="all">All Visits</option>
                                                <option value="scheduled">Scheduled</option>
                                                <option value="completed">Completed</option>
                                                <option value="canceled">Canceled</option>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="timeframeFilter" class="form-label">Timeframe</label>
                                            <select class="form-select" id="timeframeFilter">
                                                <option value="upcoming">Upcoming</option>
                                                <option value="past">Past Visits</option>
                                                <option value="all">All Visits</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Visits List Container -->
                                <div id="visitsList">
                                    <!-- Visits will be loaded here dynamically -->
                                    <div class="text-center py-4" id="loadingVisits">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                        <p class="mt-2">Loading visits...</p>
                                    </div>
                                    <div class="text-center py-4 d-none" id="noVisitsMessage">
                                        <i class="bi bi-calendar-x" style="font-size: 2rem; color: #6c757d;"></i>
                                        <p class="mt-2">No visits found with the selected filters.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Visit Details Modal -->
    <div class="modal fade" id="visitDetailsModal" tabindex="-1" aria-labelledby="visitDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="visitDetailsModalLabel">Visit Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="visit-details">
                        <div class="schedule-detail-item">
                            <span class="schedule-detail-label">Date:</span>
                            <span class="schedule-detail-value" id="modalDate"></span>
                        </div>
                        <div class="schedule-detail-item">
                            <span class="schedule-detail-label">Time:</span>
                            <span class="schedule-detail-value" id="modalTime"></span>
                        </div>
                        <div class="schedule-detail-item">
                            <span class="schedule-detail-label">Visit Type:</span>
                            <span class="schedule-detail-value" id="modalVisitType"></span>
                        </div>
                        <div class="schedule-detail-item">
                            <span class="schedule-detail-label">Care Worker:</span>
                            <span class="schedule-detail-value" id="modalCareWorker"></span>
                        </div>
                        <div class="schedule-detail-item">
                            <span class="schedule-detail-label">Status:</span>
                            <span class="schedule-detail-value" id="modalStatus"></span>
                        </div>
                        <div class="schedule-detail-item">
                            <span class="schedule-detail-label">Notes:</span>
                            <span class="schedule-detail-value" id="modalNotes"></span>
                        </div>
                    </div>
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
    
    <script>
        
        document.addEventListener('DOMContentLoaded', function() {
            // Explicit cookie handling for AJAX
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                xhrFields: {
                    withCredentials: true
                },
                beforeSend: function(xhr) {
                    // Add this to enhance cookie handling
                    xhr.withCredentials = true;
                }
            });
            
            // Test authentication immediately
            $.ajax({
                url: '{{ secure_url("/session-check") }}',
                method: 'GET',
                success: function(response) {
                    console.log('Session check successful:', response);
                },
                error: function(xhr) {
                    console.error('Session check failed:', xhr.status);
                }
            });
            
            // Initialize the modal
            const visitDetailsModal = new bootstrap.Modal(document.getElementById('visitDetailsModal'));
            
            let currentPage = 1;
            const visitsPerPage = 3;
            let allVisits = [];

            // Replace the loadUpcomingVisits function with this new version:
            function loadUpcomingVisits() {
                const statusFilter = document.getElementById('statusFilter').value;
                const timeframeFilter = document.getElementById('timeframeFilter').value;
                const visitsListContainer = document.getElementById('visitsList');
                const loadingElement = document.getElementById('loadingVisits');
                const noVisitsMessage = document.getElementById('noVisitsMessage');
                
                // Show loading indicator
                loadingElement.classList.remove('d-none');
                noVisitsMessage.classList.add('d-none');
                
                // Clear existing visits except loading and no visits message
                const existingVisits = visitsListContainer.querySelectorAll('.schedule-card, .pagination-container');
                existingVisits.forEach(card => card.remove());
                
                // Reset pagination
                currentPage = 1;
                
                // Make AJAX request to get upcoming visits
                $.ajax({
                    url: "{{ route('beneficiary.visitation.schedule.upcoming') }}", // or beneficiary.visitation.schedule.upcoming
                    type: 'GET',
                    data: {
                        status: statusFilter,
                        timeframe: timeframeFilter
                    },
                    success: function(response) {
                        // Hide loading indicator
                        loadingElement.classList.add('d-none');
                        
                        if (response.success && response.visits && response.visits.length > 0) {
                            // Store all visits for pagination
                            allVisits = response.visits;
                            
                            // Render first page
                            renderVisitsPage(currentPage);
                        } else {
                            // Show no visits message
                            noVisitsMessage.classList.remove('d-none');
                        }
                    },
                    error: function(error) {
                        // Hide loading indicator and show error message
                        loadingElement.classList.add('d-none');
                        console.error('Error loading upcoming visits:', error);
                        
                        // Create and append error message
                        const errorMessage = document.createElement('div');
                        errorMessage.className = 'alert alert-danger';
                        errorMessage.textContent = 'Error loading visits. Please try again.';
                        visitsListContainer.appendChild(errorMessage);
                    }
                });
            }

            // Make sure the renderVisitsPage function is complete:
            function renderVisitsPage(page) {
                const visitsListContainer = document.getElementById('visitsList');
                
                // Calculate start and end indices for current page
                const startIndex = (page - 1) * visitsPerPage;
                const endIndex = startIndex + visitsPerPage;
                
                // Get visits for current page
                const visitsForPage = allVisits.slice(startIndex, endIndex);
                
                // Remove existing visit cards and pagination
                const existingVisits = visitsListContainer.querySelectorAll('.schedule-card, .pagination-container');
                existingVisits.forEach(card => card.remove());
                
                // Create and append visit cards for current page
                visitsForPage.forEach(visit => {
                    visitsListContainer.appendChild(createVisitCard(visit));
                });
                
                // Add pagination controls
                if (allVisits.length > visitsPerPage) {
                    const totalPages = Math.ceil(allVisits.length / visitsPerPage);
                    const paginationContainer = document.createElement('div');
                    paginationContainer.className = 'pagination-container d-flex justify-content-center mt-3';
                    
                    const pagination = document.createElement('nav');
                    pagination.setAttribute('aria-label', 'Visits pagination');
                    
                    const paginationList = document.createElement('ul');
                    paginationList.className = 'pagination pagination-sm';
                    
                    // Previous button
                    const prevItem = document.createElement('li');
                    prevItem.className = `page-item ${page === 1 ? 'disabled' : ''}`;
                    
                    const prevLink = document.createElement('a');
                    prevLink.className = 'page-link';
                    prevLink.href = '#';
                    prevLink.innerHTML = '&laquo;';
                    prevLink.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (currentPage > 1) {
                            currentPage--;
                            renderVisitsPage(currentPage);
                        }
                    });
                    
                    prevItem.appendChild(prevLink);
                    paginationList.appendChild(prevItem);
                    
                    // Page numbers
                    for (let i = 1; i <= totalPages; i++) {
                        const pageItem = document.createElement('li');
                        pageItem.className = `page-item ${i === page ? 'active' : ''}`;
                        
                        const pageLink = document.createElement('a');
                        pageLink.className = 'page-link';
                        pageLink.href = '#';
                        pageLink.textContent = i;
                        pageLink.addEventListener('click', function(e) {
                            e.preventDefault();
                            currentPage = i;
                            renderVisitsPage(currentPage);
                        });
                        
                        pageItem.appendChild(pageLink);
                        paginationList.appendChild(pageItem);
                    }
                    
                    // Next button
                    const nextItem = document.createElement('li');
                    nextItem.className = `page-item ${page === totalPages ? 'disabled' : ''}`;
                    
                    const nextLink = document.createElement('a');
                    nextLink.className = 'page-link';
                    nextLink.href = '#';
                    nextLink.innerHTML = '&raquo;';
                    nextLink.addEventListener('click', function(e) {
                        e.preventDefault();
                        if (currentPage < totalPages) {
                            currentPage++;
                            renderVisitsPage(currentPage);
                        }
                    });
                    
                    nextItem.appendChild(nextLink);
                    paginationList.appendChild(nextItem);
                    
                    pagination.appendChild(paginationList);
                    paginationContainer.appendChild(pagination);
                    visitsListContainer.appendChild(paginationContainer);
                }
            }

            // Initialize calendar
            const calendarEl = document.getElementById('calendar');
            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'dayGridMonth',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay'
                },
                height: 'auto',
                contentHeight: 'auto',
                aspectRatio: 1.5,
                events: function(info, successCallback, failureCallback) {
                    // Get the status and timeframe filters
                    const statusFilter = document.getElementById('statusFilter').value;
                    const timeframeFilter = document.getElementById('timeframeFilter').value;
                    
                    // Show loading indicator
                    document.getElementById('calendar-container').classList.add('loading');
                    
                    // Make AJAX request to get events with explicit token and timestamp
                    $.ajax({
                        url: "{{ route('beneficiary.visitation.schedule.events') }}",
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        },
                        data: {
                            start: info.startStr,
                            end: info.endStr,
                            status: statusFilter,
                            timeframe: timeframeFilter,
                            _token: "{{ csrf_token() }}",
                            _t: new Date().getTime() // Prevent caching
                        },
                        success: function(result) {
                            console.log("Events received:", result);
                            
                            // Add detailed debug information
                            if (Array.isArray(result)) {
                                console.log("Event count:", result.length);
                                if (result.length > 0) {
                                    console.log("First event details:", {
                                        id: result[0].id,
                                        title: result[0].title,
                                        start: result[0].start,
                                        end: result[0].end,
                                        backgroundColor: result[0].backgroundColor
                                    });
                                }
                            }
                            
                            document.getElementById('calendar-container').classList.remove('loading');
                            
                            if (Array.isArray(result) && result.length > 0) {
                                // Fix date format if needed
                                const fixedEvents = result.map(event => {
                                    // Properly format dates for FullCalendar
                                    if (event.start) {
                                        // Fix the timestamp format by removing the double time part
                                        event.start = event.start.replace('00:00:00T', '');
                                    }
                                    if (event.end) {
                                        event.end = event.end.replace('00:00:00T', '');
                                    }
                                    
                                    // Ensure backgroundColor is set for visibility
                                    if (!event.backgroundColor) {
                                        event.backgroundColor = '#4e73df'; // Default blue color
                                    }
                                    
                                    // Add border and text color for visibility
                                    event.borderColor = event.backgroundColor || '#3a57e8';
                                    event.textColor = '#ffffff';
                                    
                                    // Make sure events display properly
                                    event.display = 'block';
                                    
                                    console.log("Fixed event:", {
                                        title: event.title,
                                        start: event.start,
                                        end: event.end,
                                        display: event.display
                                    });
                                    
                                    return event;
                                });
                                
                                successCallback(fixedEvents);
                            } else {
                                successCallback([]);
                            }
                        },
                        error: function(xhr) {
                            document.getElementById('calendar-container').classList.remove('loading');
                            console.error("Could not load events:", xhr);
                            
                            // Improved error handling with session debug info
                            if (xhr.status === 401) {
                                // Try to verify session first
                                $.ajax({
                                    url: "{{ secure_url('/session-check') }}",
                                    method: 'GET',
                                    success: function(response) {
                                        console.log("Session check result:", response);
                                        
                                        if (!response.authenticated) {
                                            alert("Your session has expired. Please log in again.");
                                            window.location.href = "{{ route('login') }}";
                                        } else {
                                            // Session exists but still getting 401 - might be CSRF mismatch
                                            alert("Authentication error. Please refresh the page.");
                                            location.reload();
                                        }
                                    },
                                    error: function() {
                                        alert("Session verification failed. Please log in again.");
                                        window.location.href = "{{ route('login') }}";
                                    }
                                });
                            } else {
                                failureCallback(xhr);
                            }
                        }
                    });
                },
                eventClick: function(info) {
                    const event = info.event;
                    const occurrenceId = event.id;
                    
                    // Fetch detailed information about this occurrence
                    $.ajax({
                        url: `{{ route('beneficiary.visitation.schedule.details', '') }}/${occurrenceId}`,
                        type: 'GET',
                        success: function(response) {
                            if (response.success) {
                                const details = response.details;
                                
                                // Update modal content
                                document.getElementById('modalDate').textContent = details.date;
                                document.getElementById('modalTime').textContent = details.time;
                                document.getElementById('modalVisitType').textContent = details.visit_type;
                                document.getElementById('modalCareWorker').textContent = details.care_worker;
                                document.getElementById('modalNotes').textContent = details.notes;
                                
                                // Set status with appropriate styling
                                const statusElement = document.getElementById('modalStatus');
                                statusElement.textContent = details.status;
                                statusElement.className = 'schedule-detail-value schedule-status';
                                statusElement.classList.add(`status-${details.status.toLowerCase()}`);
                                
                                // Show the modal
                                visitDetailsModal.show();
                            } else {
                                console.error('Failed to fetch visit details');
                            }
                        },
                        error: function(error) {
                            console.error('Error fetching visit details:', error);
                        }
                    });
                },
                eventContent: function(arg) {
                    // Get event details
                    const event = arg.event;
                    const title = event.title;
                    const status = event.extendedProps ? event.extendedProps.status || 'scheduled' : 'scheduled';
                    
                    // Create a custom element for the event
                    const eventEl = document.createElement('div');
                    eventEl.className = 'fc-event-custom';
                    eventEl.style.padding = '2px 4px';
                    eventEl.style.overflow = 'hidden';
                    
                    // Create the title element
                    const titleEl = document.createElement('div');
                    titleEl.className = 'fc-event-title';
                    titleEl.textContent = title;
                    titleEl.style.fontWeight = 'bold';
                    
                    // Add status indicator
                    const statusEl = document.createElement('div');
                    statusEl.className = 'fc-event-status';
                    statusEl.textContent = status.charAt(0).toUpperCase() + status.slice(1);
                    statusEl.style.fontSize = '0.8em';
                    
                    // Assemble the event content
                    eventEl.appendChild(titleEl);
                    eventEl.appendChild(statusEl);
                    
                    return { domNodes: [eventEl] };
                },
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: 'short',
                },
                views: {
                    dayGridMonth: {
                        dayMaxEventRows: 3,
                        dayHeaderFormat: { weekday: 'short' }
                    },
                    timeGridWeek: {
                        dayHeaderFormat: { weekday: 'short', month: 'numeric', day: 'numeric', omitCommas: true }
                    },
                    timeGridDay: {
                        dayHeaderFormat: { weekday: 'long', month: 'long', day: 'numeric', year: 'numeric' }
                    }
                }
            });
            
            // Render the calendar
            calendar.render();
            
            // Load upcoming visits
            loadUpcomingVisits();
            
            // Calendar view toggle buttons
            document.getElementById('monthViewBtn').addEventListener('click', function() {
                calendar.changeView('dayGridMonth');
                this.classList.add('active');
                document.getElementById('weekViewBtn').classList.remove('active');
                document.getElementById('dayViewBtn').classList.remove('active');
            });
            
            document.getElementById('weekViewBtn').addEventListener('click', function() {
                calendar.changeView('timeGridWeek');
                this.classList.add('active');
                document.getElementById('monthViewBtn').classList.remove('active');
                document.getElementById('dayViewBtn').classList.remove('active');
            });
            
            document.getElementById('dayViewBtn').addEventListener('click', function() {
                calendar.changeView('timeGridDay');
                this.classList.add('active');
                document.getElementById('monthViewBtn').classList.remove('active');
                document.getElementById('weekViewBtn').classList.remove('active');
            });
            
            // Filter functionality - reload data when filters change
            document.getElementById('statusFilter').addEventListener('change', function() {
                calendar.refetchEvents();
                loadUpcomingVisits();
            });
            
            document.getElementById('timeframeFilter').addEventListener('change', function() {
                calendar.refetchEvents();
                loadUpcomingVisits();
            });
            
            // Function to load upcoming visits
            function loadUpcomingVisits() {
                const statusFilter = document.getElementById('statusFilter').value;
                const timeframeFilter = document.getElementById('timeframeFilter').value;
                const visitsListContainer = document.getElementById('visitsList');
                const loadingElement = document.getElementById('loadingVisits');
                const noVisitsMessage = document.getElementById('noVisitsMessage');
                
                // Show loading indicator
                loadingElement.classList.remove('d-none');
                noVisitsMessage.classList.add('d-none');
                
                // Clear existing visits except loading and no visits message
                const existingVisits = visitsListContainer.querySelectorAll('.schedule-card, .pagination-container');
                existingVisits.forEach(card => card.remove());
                
                // Reset pagination
                currentPage = 1;
                
                // Make AJAX request to get upcoming visits with improved error handling
                $.ajax({
                    url: "{{ route('beneficiary.visitation.schedule.upcoming') }}", // Changed from family to beneficiary
                    method: 'GET',
                    data: {
                        status: statusFilter,
                        timeframe: timeframeFilter
                    },
                    success: function(response) {
                        // Hide loading indicator
                        loadingElement.classList.add('d-none');
                        
                        if (response.success && response.visits && response.visits.length > 0) {
                            // Store all visits for pagination
                            allVisits = response.visits;
                            
                            // Render first page
                            renderVisitsPage(currentPage);
                        } else {
                            // Show no visits message
                            noVisitsMessage.classList.remove('d-none');
                        }
                    },
                    error: function(error) {
                        // Hide loading indicator and show error message
                        loadingElement.classList.add('d-none');
                        
                        if (error.status === 401) {
                            // Redirect to login if unauthorized
                            alert("Your session has expired. Please log in again.");
                            window.location.href = "{{ route('login') }}";
                        } else {
                            console.error('Error loading upcoming visits:', error);
                            
                            // Create and append error message
                            const errorMessage = document.createElement('div');
                            errorMessage.className = 'alert alert-danger';
                            errorMessage.textContent = 'Error loading visits. Please try again.';
                            visitsListContainer.appendChild(errorMessage);
                        }
                    }
                });
            }
            
            // Function to create a visit card element
            function createVisitCard(visit) {
                const card = document.createElement('div');
                card.className = 'schedule-card';
                
                // Create card header
                const header = document.createElement('div');
                header.className = 'schedule-card-header';
                
                const dateSpan = document.createElement('span');
                dateSpan.className = 'schedule-date';
                dateSpan.textContent = visit.date;
                
                const statusSpan = document.createElement('span');
                statusSpan.className = `schedule-status status-${visit.status}`;
                statusSpan.textContent = visit.status_label;
                
                header.appendChild(dateSpan);
                header.appendChild(statusSpan);
                
                // Create card body
                const body = document.createElement('div');
                body.className = 'schedule-card-body';
                
                // Add time detail
                const timeItem = createDetailItem('Time:', visit.time);
                body.appendChild(timeItem);
                
                // Add care worker detail
                const careWorkerItem = createDetailItem('Care Worker:', visit.care_worker);
                body.appendChild(careWorkerItem);
                
                // Add visit type detail
                const visitTypeItem = createDetailItem('Visit Type:', visit.visit_type);
                body.appendChild(visitTypeItem);
                
                // Add notes if available
                // if (visit.notes) {
                //     const notesItem = createDetailItem('Notes:', visit.notes);
                //     body.appendChild(notesItem);
                // }
                
                // Add "View Details" button
                const viewDetailsDiv = document.createElement('div');
                viewDetailsDiv.className = 'mt-2 text-end';
                
                const viewDetailsBtn = document.createElement('button');
                viewDetailsBtn.className = 'btn btn-sm btn-outline-primary';
                viewDetailsBtn.innerHTML = '<i class="bi bi-info-circle me-1"></i> View Details';
                viewDetailsBtn.addEventListener('click', function() {
                    // Fetch and show visit details in modal
                    $.ajax({
                        url: `{{ url('beneficiary/visitation-schedule/details') }}/${visit.occurrence_id}`,
                        type: 'GET',
                        success: function(response) {
                            if (response.success) {
                                const details = response.details;
                                
                                // Update modal content
                                document.getElementById('modalDate').textContent = details.date;
                                document.getElementById('modalTime').textContent = details.time;
                                document.getElementById('modalVisitType').textContent = details.visit_type;
                                document.getElementById('modalCareWorker').textContent = details.care_worker;
                                document.getElementById('modalNotes').textContent = details.notes;
                                
                                // Set status with appropriate styling
                                const statusElement = document.getElementById('modalStatus');
                                statusElement.textContent = details.status;
                                statusElement.className = 'schedule-detail-value schedule-status';
                                statusElement.classList.add(`status-${details.status.toLowerCase()}`);
                                
                                // Show the modal
                                visitDetailsModal.show();
                            } else {
                                console.error('Failed to fetch visit details');
                            }
                        },
                        error: function(error) {
                            console.error('Error fetching visit details:', error);
                        }
                    });
                });
                
                viewDetailsDiv.appendChild(viewDetailsBtn);
                body.appendChild(viewDetailsDiv);
                
                // Assemble the card
                card.appendChild(header);
                card.appendChild(body);
                
                return card;
            }
            
            // Helper function to create detail items
            function createDetailItem(label, value) {
                const item = document.createElement('div');
                item.className = 'schedule-detail-item';
                
                const labelSpan = document.createElement('span');
                labelSpan.className = 'schedule-detail-label';
                // Add a space after the label
                labelSpan.textContent = label + ' ';  // Add space here
                
                const valueSpan = document.createElement('span');
                valueSpan.className = 'schedule-detail-value';
                valueSpan.textContent = value;
                
                item.appendChild(labelSpan);
                item.appendChild(valueSpan);
                
                return item;
            }

            // Add this at the end of your DOMContentLoaded function
            window.addEventListener('resize', function() {
                calendar.updateSize();
            });

            // Also force a re-render after the initial load
            setTimeout(function() {
                calendar.updateSize();
            }, 500);
        });
    </script>
</body>
</html>