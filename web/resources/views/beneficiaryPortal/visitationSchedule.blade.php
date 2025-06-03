<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Family Portal - Visitation Schedule</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/familyPortalHomePage.css') }}">
    <link rel="stylesheet" href="{{ asset('css/familyPortalVisitationSchedule.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <!-- FullCalendar CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
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
                    <!-- Calendar Column (col-7 equivalent) -->
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
                    
                    <!-- Upcoming Visits Column (col-5 equivalent) -->
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
                                                <option value="pending">Pending Verification</option>
                                                <option value="missed">Missed</option>
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
                                
                                <!-- Visits List -->
                                <div id="visitsList">
                                    <!-- Sample visit cards - these would be dynamically generated in a real app -->
                                    <div class="schedule-card">
                                        <div class="schedule-card-header">
                                            <span class="schedule-date">Tomorrow, June 15, 2023</span>
                                            <span class="schedule-status status-scheduled">Scheduled</span>
                                        </div>
                                        <div class="schedule-card-body">
                                            <div class="schedule-detail-item">
                                                <span class="schedule-detail-label">Time:</span>
                                                <span class="schedule-detail-value">10:00 AM - 12:00 PM</span>
                                            </div>
                                            <div class="schedule-detail-item">
                                                <span class="schedule-detail-label">Care Worker:</span>
                                                <span class="schedule-detail-value">Maria Garcia</span>
                                            </div>
                                            <div class="schedule-detail-item">
                                                <span class="schedule-detail-label">Visit Type:</span>
                                                <span class="schedule-detail-value">Routine Care Visit</span>
                                            </div>
                                            <div class="schedule-detail-item">
                                                <span class="schedule-detail-label">Notes:</span>
                                                <span class="schedule-detail-value">Medication administration and light housekeeping</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="schedule-card">
                                        <div class="schedule-card-header">
                                            <span class="schedule-date">Today, June 14, 2023</span>
                                            <span class="schedule-status status-pending-verification">Pending Verification</span>
                                        </div>
                                        <div class="schedule-card-body">
                                            <div class="schedule-detail-item">
                                                <span class="schedule-detail-label">Time:</span>
                                                <span class="schedule-detail-value">2:00 PM - 4:00 PM</span>
                                            </div>
                                            <div class="schedule-detail-item">
                                                <span class="schedule-detail-label">Care Worker:</span>
                                                <span class="schedule-detail-value">John Smith</span>
                                            </div>
                                            <div class="schedule-detail-item">
                                                <span class="schedule-detail-label">Visit Type:</span>
                                                <span class="schedule-detail-value">Physical Therapy</span>
                                            </div>
                                            <div class="verification-section">
                                                <p class="mb-2"><strong>Verify this visit:</strong></p>
                                                <div class="verification-buttons">
                                                    <button class="btn btn-sm btn-success confirm-visit-btn" data-visit-id="123">
                                                        <i class="bi bi-check-circle"></i> Confirm Visit Occurred
                                                    </button>
                                                    <button class="btn btn-sm btn-danger report-missed-btn" data-visit-id="123">
                                                        <i class="bi bi-exclamation-triangle"></i> Report Missed Visit
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="schedule-card">
                                        <div class="schedule-card-header">
                                            <span class="schedule-date">June 12, 2023</span>
                                            <span class="schedule-status status-completed">Completed</span>
                                        </div>
                                        <div class="schedule-card-body">
                                            <div class="schedule-detail-item">
                                                <span class="schedule-detail-label">Time:</span>
                                                <span class="schedule-detail-value">9:00 AM - 11:00 AM</span>
                                            </div>
                                            <div class="schedule-detail-item">
                                                <span class="schedule-detail-label">Care Worker:</span>
                                                <span class="schedule-detail-value">Maria Garcia</span>
                                            </div>
                                            <div class="schedule-detail-item">
                                                <span class="schedule-detail-label">Visit Type:</span>
                                                <span class="schedule-detail-value">Routine Care Visit</span>
                                            </div>
                                            <div class="schedule-detail-item">
                                                <span class="schedule-detail-label">Verified On:</span>
                                                <span class="schedule-detail-value">June 12, 2023 at 11:15 AM</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="schedule-card">
                                        <div class="schedule-card-header">
                                            <span class="schedule-date">June 10, 2023</span>
                                            <span class="schedule-status status-missed">Missed</span>
                                        </div>
                                        <div class="schedule-card-body">
                                            <div class="schedule-detail-item">
                                                <span class="schedule-detail-label">Time:</span>
                                                <span class="schedule-detail-value">1:00 PM - 3:00 PM</span>
                                            </div>
                                            <div class="schedule-detail-item">
                                                <span class="schedule-detail-label">Care Worker:</span>
                                                <span class="schedule-detail-value">Robert Johnson</span>
                                            </div>
                                            <div class="schedule-detail-item">
                                                <span class="schedule-detail-label">Visit Type:</span>
                                                <span class="schedule-detail-value">Meal Preparation</span>
                                            </div>
                                            <div class="schedule-detail-item">
                                                <span class="schedule-detail-label">Reported On:</span>
                                                <span class="schedule-detail-value">June 10, 2023 at 3:30 PM</span>
                                            </div>
                                            <div class="schedule-detail-item">
                                                <span class="schedule-detail-label">Reason:</span>
                                                <span class="schedule-detail-value">Care worker did not arrive</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    <!-- Verification Confirmation Modal -->
    <div class="modal fade" id="verificationModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="verificationModalLabel">Confirm Visit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="verificationModalBody">
                    <p>Are you sure you want to confirm that this visit occurred as scheduled?</p>
                    <div class="mb-3">
                        <label for="verificationNotes" class="form-label">Additional Notes (Optional)</label>
                        <textarea class="form-control" id="verificationNotes" rows="3" placeholder="Add any notes about the visit..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmVerification">Confirm Visit</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Missed Visit Modal -->
    <div class="modal fade" id="missedVisitModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="missedVisitModalLabel">Report Missed Visit</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="missedVisitModalBody">
                    <p>Please provide details about why this visit was missed:</p>
                    <div class="mb-3">
                        <label for="missedReason" class="form-label">Reason</label>
                        <select class="form-select" id="missedReason">
                            <option value="">Select a reason</option>
                            <option value="no_show">Care worker did not arrive</option>
                            <option value="late">Care worker arrived too late</option>
                            <option value="wrong_time">Care worker came at wrong time</option>
                            <option value="other">Other reason</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="missedNotes" class="form-label">Additional Details</label>
                        <textarea class="form-control" id="missedNotes" rows="3" placeholder="Provide more details about the missed visit..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmMissedVisit">Report Missed Visit</button>
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
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="visit-details">
                        <div class="schedule-detail-item">
                            <span class="schedule-detail-label">Care Worker:</span>
                            <span class="schedule-detail-value" id="modalCareWorker"></span>
                        </div>
                        <div class="schedule-detail-item">
                            <span class="schedule-detail-label">Visit Type:</span>
                            <span class="schedule-detail-value" id="modalVisitType"></span>
                        </div>
                        <div class="schedule-detail-item">
                            <span class="schedule-detail-label">Date:</span>
                            <span class="schedule-detail-value" id="modalDate"></span>
                        </div>
                        <div class="schedule-detail-item">
                            <span class="schedule-detail-label">Status:</span>
                            <span class="schedule-detail-value" id="modalStatus"></span>
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
            // Initialize modals
            const verificationModal = new bootstrap.Modal(document.getElementById('verificationModal'));
            const missedVisitModal = new bootstrap.Modal(document.getElementById('missedVisitModal'));
            
            // Current visit ID for verification
            let currentVisitId = null;
            
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
                events: [
                    // Sample events - in a real app these would come from an API
                    {
                        title: 'Routine Care Visit',
                        start: new Date(new Date().setDate(new Date().getDate() + 1)), // Tomorrow
                        end: new Date(new Date().setDate(new Date().getDate() + 1)),
                        extendedProps: {
                            careWorker: 'Maria Garcia',
                            visitType: 'Routine Care',
                            status: 'scheduled'
                        },
                        backgroundColor: '#4e73df',
                        borderColor: '#4e73df'
                    },
                    {
                        title: 'Physical Therapy',
                        start: new Date(), // Today
                        end: new Date(),
                        extendedProps: {
                            careWorker: 'John Smith',
                            visitType: 'Physical Therapy',
                            status: 'pending'
                        },
                        backgroundColor: '#f6c23e',
                        borderColor: '#f6c23e'
                    },
                    {
                        title: 'Routine Care Visit',
                        start: new Date(new Date().setDate(new Date().getDate() - 2)), // 2 days ago
                        end: new Date(new Date().setDate(new Date().getDate() - 2)),
                        extendedProps: {
                            careWorker: 'Maria Garcia',
                            visitType: 'Routine Care',
                            status: 'completed'
                        },
                        backgroundColor: '#1cc88a',
                        borderColor: '#1cc88a'
                    },
                    {
                        title: 'Meal Preparation',
                        start: new Date(new Date().setDate(new Date().getDate() - 4)), // 4 days ago
                        end: new Date(new Date().setDate(new Date().getDate() - 4)),
                        extendedProps: {
                            careWorker: 'Robert Johnson',
                            visitType: 'Meal Preparation',
                            status: 'missed'
                        },
                        backgroundColor: '#e74a3b',
                        borderColor: '#e74a3b'
                    }
                ],
                eventClick: function(info) {
                const event = info.event;
                const visitDetailsModal = new bootstrap.Modal(document.getElementById('visitDetailsModal'));
                
                // Update modal content
                document.getElementById('modalCareWorker').textContent = event.extendedProps.careWorker;
                document.getElementById('modalVisitType').textContent = event.extendedProps.visitType;
                document.getElementById('modalDate').textContent = event.start.toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric',
                    hour: '2-digit',
                    minute: '2-digit'
                });
                
                // Add status with appropriate styling
                const statusElement = document.getElementById('modalStatus');
                statusElement.textContent = event.extendedProps.status.charAt(0).toUpperCase() + 
                                        event.extendedProps.status.slice(1);
                
                // Remove any existing status classes
                statusElement.className = 'schedule-detail-value schedule-status';
                
                // Add appropriate status class
                statusElement.classList.add(`status-${event.extendedProps.status}`);
                
                visitDetailsModal.show();
            },
                eventContent: function(arg) {
                    // Custom event display
                    let eventEl = document.createElement('div');
                    eventEl.className = 'fc-event-main';
                    
                    const startTime = arg.event.start ? arg.event.start.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'}) : '';
                    
                    eventEl.innerHTML = `
                        <div class="event-title">${arg.event.title}</div>
                        <div class="event-time">${startTime} - ${arg.event.extendedProps.careWorker}</div>
                    `;
                    
                    return { domNodes: [eventEl] };
                },
                eventDisplay: 'block',
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    hour12: true
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
            
            calendar.render();
            
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
            
            // Filter functionality
            document.getElementById('statusFilter').addEventListener('change', function() {
                filterVisits();
            });
            
            document.getElementById('timeframeFilter').addEventListener('change', function() {
                filterVisits();
            });
            
            function filterVisits() {
                const statusFilter = document.getElementById('statusFilter').value;
                const timeframeFilter = document.getElementById('timeframeFilter').value;
                const today = new Date();
                today.setHours(0, 0, 0, 0);
                
                document.querySelectorAll('.schedule-card').forEach(card => {
                    const statusElement = card.querySelector('.schedule-status');
                    const status = statusElement ? 
                        statusElement.className.replace('schedule-status', '').trim().replace('status-', '') : 
                        '';
                    
                    const dateElement = card.querySelector('.schedule-date');
                    let dateText = dateElement ? dateElement.textContent : '';
                    
                    // Extract date from text (this is simplified - in a real app you'd have actual date data)
                    let isPast = false;
                    if (dateText.includes('Today') || dateText.includes('Tomorrow')) {
                        isPast = dateText.includes('Today') && new Date().getHours() > 18; // Assume past if evening
                    } else {
                        // Simple check for demo purposes - would need proper date parsing in real app
                        const dateMatch = dateText.match(/(\w+ \d{1,2}, \d{4})/);
                        if (dateMatch) {
                            const cardDate = new Date(dateMatch[0]);
                            isPast = cardDate < today;
                        }
                    }
                    
                    let showCard = true;
                    
                    // Apply status filter
                    if (statusFilter !== 'all' && !status.includes(statusFilter)) {
                        showCard = false;
                    }
                    
                    // Apply timeframe filter
                    if (timeframeFilter === 'upcoming' && isPast) {
                        showCard = false;
                    } else if (timeframeFilter === 'past' && !isPast) {
                        showCard = false;
                    }
                    
                    card.style.display = showCard ? 'block' : 'none';
                });
            }
            
            // Verification buttons
            document.querySelectorAll('.confirm-visit-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    currentVisitId = this.getAttribute('data-visit-id');
                    verificationModal.show();
                });
            });
            
            document.querySelectorAll('.report-missed-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    currentVisitId = this.getAttribute('data-visit-id');
                    missedVisitModal.show();
                });
            });
            
            // Confirm verification
            document.getElementById('confirmVerification').addEventListener('click', function() {
                const notes = document.getElementById('verificationNotes').value;
                
                // In a real app, this would be an AJAX call to the server
                console.log(`Confirming visit ${currentVisitId} with notes: ${notes}`);
                
                // Simulate success
                setTimeout(() => {
                    verificationModal.hide();
                    alert('Visit confirmed successfully!');
                    // In a real app, you would update the UI to reflect the new status
                }, 1000);
            });
            
            // Confirm missed visit
            document.getElementById('confirmMissedVisit').addEventListener('click', function() {
                const reason = document.getElementById('missedReason').value;
                const notes = document.getElementById('missedNotes').value;
                
                if (!reason || !notes) {
                    alert('Please provide both a reason and details for the missed visit');
                    return;
                }
                
                // In a real app, this would be an AJAX call to the server
                console.log(`Reporting missed visit ${currentVisitId} with reason: ${reason} and notes: ${notes}`);
                
                // Simulate success
                setTimeout(() => {
                    missedVisitModal.hide();
                    alert('Missed visit reported successfully!');
                    // In a real app, you would update the UI to reflect the new status
                }, 1000);
            });
            
            // Reset form when modals are hidden
            verificationModal._element.addEventListener('hidden.bs.modal', function() {
                document.getElementById('verificationNotes').value = '';
            });
            
            missedVisitModal._element.addEventListener('hidden.bs.modal', function() {
                document.getElementById('missedReason').value = '';
                document.getElementById('missedNotes').value = '';
            });
        });
    </script>
</body>
</html>