<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Emergency and Request | Care Worker</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/emergencyAndService.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body>

    @php
    use App\Helpers\TranslationHelper as T;
    @endphp

    @include('components.careWorkerNavbar')
    @include('components.careWorkerSidebar')

    <div class="home-section">
        <div class="page-header mb-2">
            <div class="text-left">{{ T::translate('EMERGENCY AND SERVICE REQUEST', 'MGA EMERGENCY AT PAKIUSAP NA SERBISYO') }}</div>
            <a href="{{ route('care-worker.emergency.request.viewHistory') }}">
                <button class="history-btn" id="historyToggle" onclick="window.location.href='/care-worker/emergency-request/view-history'">
                    <i class="bi bi-clock-history me-1"></i> {{ T::translate('View History', 'Tingnan ang Kasaysayan') }}
                </button>
            </a>
        </div>
        
        <div class="container-fluid">
            <div class="row" id="home-content">
                <!-- Main Content Column (Emergency/Service Tabs) -->
                <div class="col-lg-8 col-12 main-content-column">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-0">
                                <ul class="nav nav-tabs px-3" id="requestTypeTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="emergency-tab" data-bs-toggle="tab" data-bs-target="#emergency" type="button" role="tab">
                                            <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i> {{ T::translate('Emergency', 'Mga Emergency') }}
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="service-tab" data-bs-toggle="tab" data-bs-target="#service" type="button" role="tab">
                                            <i class="bi bi-hand-thumbs-up-fill text-primary me-2"></i> {{ T::translate('Service Request', 'Pakiusap na Serbisyo') }}
                                        </button>
                                    </li>
                                </ul>
                                
                                <div class="tab-content p-3" id="requestTypeTabContent">
                                    <!-- Emergency Tab -->
                                    <div class="tab-pane fade show active" id="emergency" role="tabpanel">
                                        @forelse($emergencyNotices->where('status', 'new') as $notice)
                                            <div class="card notification-card emergency-card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h5 class="card-title fw-bold mb-0 text-dark">{{ $notice->beneficiary->first_name }} {{ $notice->beneficiary->last_name }}</h5>
                                                        <span class="badge bg-danger bg-opacity-10 text-danger">{{ T::translate('New', 'Bago') }}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">{{ T::translate('Address:', 'Tirahan:') }}</span>
                                                        <span>{{ $notice->beneficiary->street_address }} ({{ $notice->beneficiary->barangay->barangay_name }}, {{ $notice->beneficiary->municipality->municipality_name }})</span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">{{ T::translate('Contact:', 'Kontak:') }}</span>
                                                        <span >{{ $notice->beneficiary->mobile }}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">{{ T::translate('Emergency Contact:', 'Emergency Contact:') }}</span>
                                                        <span class="ms-2">{{ $notice->beneficiary->emergency_contact_name }} ({{ $notice->beneficiary->emergency_contact_relation }}) {{ $notice->beneficiary->emergency_contact_mobile }}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">{{ T::translate('Emergency Type:', 'Uri ng Emergency:') }}</span>
                                                        <span class="ms-2"><span class="badge" style="background-color: {{ $notice->emergencyType->color_code }}">{{ $notice->emergencyType->name }}</span></span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-3">
                                                        <span class="info-label">{{ T::translate('Message:', 'Mensahe:') }}</span>
                                                        <span>{{ Str::limit($notice->message, 100) }}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($notice->created_at)->diffForHumans() }}</small>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="viewEmergencyDetails({{ $notice->notice_id }})">
                                                                <i class="bi bi-eye me-1"></i> {{ T::translate('View', 'Tingnan') }}
                                                            </button>
                                                           <button type="button" class="btn btn-sm btn-outline-primary" onclick="openSendReminderModal({{ $notice->notice_id }}, 'emergency')">
                                                                <i class="bi bi-bell me-1"></i> {{ T::translate('Follow Up', 'Follow Up') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="empty-state">
                                                <i class="bi bi-inbox-fill" style="font-size: 3rem;"></i>
                                                <h5 class="mt-3">{{ T::translate('No New Emergency Notices', 'Walang mga bagong emergency notice') }}</h5>
                                                <p>{{ T::translate('There are no new emergency notices at the moment.', 'Walang bagong emergency notice sa ngayon.') }}</p>
                                            </div>
                                        @endforelse
                                    </div>
                                    
                                    <!-- Service Request Tab -->
                                    <div class="tab-pane fade" id="service" role="tabpanel">
                                        @forelse($serviceRequests->where('status', 'new') as $request)
                                            <div class="card notification-card request-card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h5 class="card-title fw-bold mb-0 text-dark">{{ $request->beneficiary->first_name }} {{ $request->beneficiary->last_name }}</h5>
                                                        <span class="badge bg-primary bg-opacity-10 text-primary">{{ T::translate('New', 'Bago') }}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">{{ T::translate('Service Type:', 'Uri ng Serbisyo:') }}</span>
                                                        <span><span class="badge" style="background-color: {{ $request->serviceType->color_code }}">{{ $request->serviceType->name }}</span></span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">{{ T::translate('Requested Date:', 'Petsa ng Pakiusap:') }}</span>
                                                        <span>{{ \Carbon\Carbon::parse($request->service_date)->format('M d, Y') }}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">{{ T::translate('Requested Time:', 'Oras ng Pakiusap:') }}</span>
                                                        <span>{{ $request->service_time ? \Carbon\Carbon::parse($request->service_time)->format('h:i A') : T::translate('Flexible', 'Flexible') }}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-3">
                                                        <span class="info-label">{{ T::translate('Message:', 'Mensahe:') }}</span>
                                                        <span>{{ Str::limit($request->message, 100) }}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($request->created_at)->diffForHumans() }}</small>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="viewServiceRequestDetails({{ $request->service_request_id }})">
                                                                <i class="bi bi-eye me-1"></i> {{ T::translate('View', 'Tingnan') }}
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="openSendReminderModal({{ $request->service_request_id }}, 'service')">
                                                                <i class="bi bi-bell me-1"></i> {{ T::translate('Follow Up', 'Follow Up') }}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="empty-state">
                                                <i class="bi bi-inbox-fill" style="font-size: 3rem;"></i>
                                                <h5 class="mt-3">{{ T::translate('No New Service Requests', 'Walang mga bagong Pakiusap na Serbisyo') }}</h5>
                                                <p>{{ T::translate('There are no new service requests at the moment.', 'Walang mga bagong pakiusap na serbisyo sa ngayon.') }}</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pending/In Progress Column -->
                    <div class="col-lg-4 col-12 pending-column">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header-custom">
                                <h5 class="card-title mb-0 section-header text-warning">
                                    <i class="bi bi-hourglass-split me-2"></i>{{ T::translate('Pending/In Progress', 'Nakabinbin/Isinasagawa') }}
                                </h5>
                            </div>
                            <div class="card-body p-3">
                                <!-- Tab-specific content containers -->
                                <div id="emergency-pending-content" class="pending-tab-content active">
                                    <!-- In Progress Emergency Notices -->
                                    @forelse($emergencyNotices->where('status', 'in_progress') as $notice)
                                        <div class="card notification-card pending-card mb-3 clickable-card" onclick="viewEmergencyDetails({{ $notice->notice_id }})">
                                            <div class="card-body">
                                                <div class="card-content">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="fw-bold mb-0 text-dark">{{ $notice->beneficiary->first_name }} {{ $notice->beneficiary->last_name }}</h6>
                                                        <span class="badge bg-info bg-opacity-10 text-info">{{ T::translate('In Progress', 'Isinasagawa') }}</span>
                                                    </div>
                                                    
                                                    @if($notice->action_taken_by)
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">{{ T::translate('Responded:', 'Tinugunan:') }}</span>
                                                        <span>{{ $notice->actionTakenBy ? $notice->actionTakenBy->first_name . ' ' . $notice->actionTakenBy->last_name : T::translate('Unknown', 'Hindi kilala') }}</span>
                                                    </div>
                                                    @endif
                                                    
                                                    <div class="d-flex flex-wrap mb-3">
                                                        <span class="info-label">{{ T::translate('Type:', 'Uri:') }}</span>
                                                        <span><span class="badge" style="background-color: {{ $notice->emergencyType->color_code }}">{{ $notice->emergencyType->name }}</span></span>
                                                    </div>
                                                </div>
                                                
                                                <div class="card-footer-actions">
                                                    <small class="text-muted">
                                                        <i class="bi bi-clock me-1"></i> {{ T::translate('Started', 'Sinimulan') }} {{ \Carbon\Carbon::parse($notice->action_taken_at)->diffForHumans() }}
                                                    </small>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); openSendReminderModal({{ $notice->notice_id }}, 'emergency')">
                                                        <i class="bi bi-bell me-1"></i> {{ T::translate('Follow Up', 'Follow Up') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="empty-state">
                                            <i class="bi bi-hourglass text-muted" style="font-size: 2rem;"></i>
                                            <h6 class="mt-3">{{ T::translate('No Active Emergencies', 'Walang Aktibong Emergencies') }}</h6>
                                            <p class="small">{{ T::translate('No in-progress emergency notices at the moment.', 'Walang isinasagawa na emergency notice sa ngayon.') }}</p>
                                        </div>
                                    @endforelse
                                </div>
                                
                                <div id="service-pending-content" class="pending-tab-content" style="display: none;">
                                    <!-- Approved Service Requests -->
                                    @forelse($serviceRequests->where('status', 'approved') as $request)
                                        <div class="card notification-card pending-card mb-3 clickable-card" onclick="viewServiceRequestDetails({{ $request->service_request_id }})">
                                            <div class="card-body">
                                                <div class="card-content">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="fw-bold mb-0 text-dark">{{ $request->beneficiary->first_name }} {{ $request->beneficiary->last_name }}</h6>
                                                        <span class="badge bg-success bg-opacity-10 text-success">{{ T::translate('Approved', 'Naaprubahan') }}</span>
                                                    </div>

                                                    <div class="d-flex flex-wrap mb-2">
                                                        <h6 class="fw mb-0 text-primary">{{ $request->serviceType->name }}</h6>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">{{ T::translate('Approved:', 'Naaprubahan:') }}</span>
                                                        <span>{{ $request->actionTakenBy ? $request->actionTakenBy->first_name . ' ' . $request->actionTakenBy->last_name : T::translate('Unknown', 'Hindi kilala') }}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-3">
                                                        <span class="info-label">{{ T::translate('Assigned To:', 'Itinalaga kay:') }}</span>
                                                        <span>{{ $request->careWorker ? $request->careWorker->first_name . ' ' . $request->careWorker->last_name : T::translate('Not assigned', 'Hindi itinalaga') }}</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="card-footer-actions">
                                                    <small class="text-muted">
                                                        <i class="bi bi-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($request->service_date)->format('M d') }}
                                                    </small>
                                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); openSendReminderModal({{ $request->service_request_id }}, 'service')">
                                                        <i class="bi bi-bell me-1"></i> {{ T::translate('Follow Up', 'Follow Up') }}
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="empty-state">
                                            <i class="bi bi-hourglass text-muted" style="font-size: 2rem;"></i>
                                            <h6 class="mt-3">{{ T::translate('No Active Service Requests', 'Walang mga Aktibong Pakiusap na Serbisyo') }}</h6>
                                            <p class="small">{{ T::translate('No approved service requests at the moment.', 'Walang naaprubahan na mga pakiusap na serbisyo sa ngayon.') }}</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Emergency Details Modal (Read-only) -->
    <div class="modal fade" id="emergencyDetailsModal" tabindex="-1" aria-labelledby="emergencyDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-secondary text-white">
            <h5 class="modal-title" id="emergencyDetailsModalLabel"><i class="bi bi-info-circle"></i> {{ T::translate('Emergency Details', 'Mga Detalye ng Emergency') }}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div id="emergencyDetailsContent">
            <!-- Emergency details will be loaded here -->
            </div>
            
            <div class="updates-history mt-4">
            <h6 class="border-bottom pb-2">{{ T::translate('Updates History', 'Kasaysayan ng mga Update') }}</h6>
            <div id="emergencyUpdatesTimeline">
                <!-- Updates will be loaded here -->
            </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ T::translate('Close', 'Isara') }}</button>
            <button type="button" class="btn btn-info" onclick="openSendReminderModal(currentEmergency.notice_id, 'emergency')">
                <i class="bi bi-bell me-1"></i> {{ T::translate('Follow Up', 'Follow Up') }}
            </button>
        </div>
        </div>
    </div>
    </div>

    <!-- Service Request Details Modal -->
    <div class="modal fade" id="serviceRequestDetailsModal" tabindex="-1" aria-labelledby="serviceRequestDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-secondary text-white">
            <h5 class="modal-title" id="serviceRequestDetailsModalLabel"><i class="bi bi-info-circle"></i> {{ T::translate('Service Request Details', 'Detalye ng mga Pakiusap na Serbisyo') }}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div id="serviceRequestDetailsContent">
            <!-- Service request details will be loaded here -->
            </div>
            
            <div class="updates-history mt-4">
            <h6 class="border-bottom pb-2">{{ T::translate('Updates History', 'Kasaysayan ng mga Update') }}</h6>
            <div id="serviceUpdatesTimeline">
                <!-- Updates will be loaded here -->
            </div>
            </div>
        </div>
       <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ T::translate('Close', 'Isara') }}</button>
            <button type="button" class="btn btn-info" onclick="openSendReminderModal(currentServiceRequest.service_request_id, 'service')">
                <i class="bi bi-bell me-1"></i> {{ T::translate('Follow Up', 'Follow Up') }}
            </button>
        </div>
        </div>
    </div>
    </div>

    <!-- Send Reminder Modal -->
    <div class="modal fade" id="sendReminderModal" tabindex="-1" aria-labelledby="reminderModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="reminderModalLabel"><i class="bi bi-bell"></i> {{ T::translate('Send Reminder', 'Magpadala ng Paalala') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form id="sendReminderForm">
                    <div class="modal-body">
                        <input type="hidden" id="reminderRecordId" name="record_id">
                        <input type="hidden" id="reminderRecordType" name="record_type">
                        
                        <div class="mb-3">
                            <p>{{ T::translate('Send a reminder to your care manager about this', 'Magpadala ng paalala sa iyong care manager tungkol sa') }} <span id="reminderTypeLabel">{{ T::translate('request', 'pakiusap') }}</span>.</p>
                        </div>
                        
                        <div class="mb-3">
                            <label for="reminderMessage" class="form-label">{{ T::translate('Message', 'Mensahe') }}</label>
                            <textarea class="form-control" id="reminderMessage" name="message" rows="4" placeholder="{{ T::translate('Enter your reminder message', 'Ilagay ang iyong mensahe ng paalala') }}" required></textarea>
                            <small class="text-muted">{{ T::translate('Explain why this needs attention from your care manager as soon as possible.', 'Ipaliwanag kung bakit kailangan ng atensyon ng iyong care manager ito sa lalong madaling panahon.') }}</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ T::translate('Cancel', 'Kanselahin') }}</button>
                        <button type="button" class="btn btn-primary" id="submitReminder">{{ T::translate('Send Follow Up Reminder', 'Magpadala ng Follow Up') }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Initialize Bootstrap tabs
        var tabEls = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabEls.forEach(function(tabEl) {
            new bootstrap.Tab(tabEl);
        });
    </script>

    <script>
        // Global variables to track current records
        let currentEmergency = null;
        let currentServiceRequest = null;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Fallback for toastr if it's not defined
        if (typeof toastr === 'undefined') {
            window.toastr = {
                success: function(message) { alert('{{ T::translate("Success:", "Tagumpay:") }} ' + message); },
                error: function(message) { alert('{{ T::translate("Error:", "Error:") }} ' + message); },
                warning: function(message) { alert('{{ T::translate("Warning:", "Babala:") }} ' + message); },
                info: function(message) { alert('{{ T::translate("Info:", "Impormasyon:") }} ' + message); }
            };
        }

        // ===== EMERGENCY NOTICE HANDLERS =====
        function viewEmergencyDetails(noticeId) {
            // Show loading state
            $('#emergencyDetailsContent').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">{{ T::translate("Loading details...", "Naglo-load ng mga detalye...") }}</p></div>');
            
            // Open the modal
            $('#emergencyDetailsModal').modal('show');
            
            // Fetch emergency details
            $.ajax({
                url: "/care-worker/emergency-request/emergency/" + noticeId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        // Store current emergency for later use
                        currentEmergency = response.emergency_notice;
                        
                        // Render emergency details
                        renderEmergencyDetails(currentEmergency);
                        
                        // Show or hide respond button based on role
                        const userRole = '{{ Auth::user()->role_id }}';
                        if (userRole <= 2) { // Admin or Care Manager
                            $('#respondToEmergencyBtn').show();
                        } else {
                            // For care workers, show remind button instead
                            $('#respondToEmergencyBtn').text('{{ T::translate("Send Reminder", "Magpadala ng Paalala") }}').removeClass('btn-primary').addClass('btn-info');
                        }
                    } else {
                        $('#emergencyDetailsContent').html(`<div class="alert alert-danger">{{ T::translate("Error loading details:", "Error sa pag-load ng mga detalye:") }} ${response.message}</div>`);
                    }
                },
                error: function() {
                    $('#emergencyDetailsContent').html('<div class="alert alert-danger">{{ T::translate("Failed to load emergency details. Please try again.", "Nabigong i-load ang mga detalye ng emergency. Mangyaring subukan muli.") }}</div>');
                }
            });
        }

        // Helper function to get badge class based on update type
        function getUpdateTypeBadgeClass(updateType) {
            switch(updateType) {
                case 'response': return 'bg-primary';
                case 'status_change': return 'bg-warning text-dark';
                case 'assignment': return 'bg-info text-dark';
                case 'resolution': return 'bg-success';
                case 'note': return 'bg-secondary';
                default: return 'bg-secondary';
            }
        }

        // Helper function to format update type
        function formatUpdateType(updateType) {
            switch(updateType) {
                case 'response': return '{{ T::translate("Response", "Tugon") }}';
                case 'status_change': return '{{ T::translate("Status Change", "Pagbabago ng Status") }}';
                case 'assignment': return '{{ T::translate("Assignment", "Assignment") }}';
                case 'resolution': return '{{ T::translate("Resolution", "Resolution") }}';
                case 'note': return '{{ T::translate("Note", "Tala") }}';
                default: return updateType;
            }
        }

        // Add code to check for stored messages on page load
        $(document).ready(function() {
            const storedMessage = sessionStorage.getItem('emergencySuccessMessage');
            if (storedMessage) {
                showSuccessAlert(storedMessage);
                // Clear the message so it doesn't show again on next refresh
                sessionStorage.removeItem('emergencySuccessMessage');
            }

             // Check if we need to switch to the service tab
            const activeTab = sessionStorage.getItem('activeTab');
            if (activeTab === 'service') {
                // Activate the service tab
                $('#service-tab').tab('show');
                
                // Update the pending column view
                $('#emergency-pending-content').hide();
                $('#service-pending-content').show();
                
                // Clear the stored tab
                sessionStorage.removeItem('activeTab');
            }

            // Service request action success message
            const serviceActionType = sessionStorage.getItem('serviceActionType');
            if (serviceActionType) {
                // Show success message if not already shown
                if (!storedMessage) {
                    const message = `{{ T::translate("Service request has been", "Ang pakiusap na serbisyo ay") }} ${serviceActionType} {{ T::translate("successfully.", "matagumpay.") }}`;
                    showSuccessAlert(message);
                }
                // Clear the stored action type
                sessionStorage.removeItem('serviceActionType');
            }
            
            // For debugging
            console.log('{{ T::translate("Service request handlers initialized", "Ang mga handler ng pakiusap na serbisyo ay na-initialize") }}');
        });

        // ===== SERVICE REQUEST HANDLERS =====
        function viewServiceRequestDetails(requestId) {
            // Show loading state
            $('#serviceRequestDetailsContent').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">{{ T::translate("Loading details...", "Naglo-load ng mga detalye...") }}</p></div>');
            
            // Open the modal
            $('#serviceRequestDetailsModal').modal('show');
            
            // Fetch service request details
            $.ajax({
                url: "/care-worker/emergency-request/service-request/" + requestId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        // Store current service request for later use
                        currentServiceRequest = response.service_request;
                        
                        // Render service request details
                        renderServiceRequestDetails(currentServiceRequest);
                        
                        // Show or hide respond button based on role
                        const userRole = '{{ Auth::user()->role_id }}';
                        if (userRole <= 2) { // Admin or Care Manager
                            $('#handleServiceRequestBtn').show();
                        } else {
                            // For care workers, show remind button instead
                            $('#handleServiceRequestBtn').text('{{ T::translate("Send Reminder", "Magpadala ng Paalala") }}').removeClass('btn-primary').addClass('btn-info');
                        }
                    } else {
                        $('#serviceRequestDetailsContent').html(`<div class="alert alert-danger">{{ T::translate("Error loading details:", "Error sa pag-load ng mga detalye:") }} ${response.message}</div>`);
                    }
                },
                error: function() {
                    $('#serviceRequestDetailsContent').html('<div class="alert alert-danger">{{ T::translate("Failed to load service request details. Please try again.", "Nabigong i-load ang mga detalye ng pakiusap na serbisyo. Mangyaring subukan muli.") }}</div>');
                }
            });
        }

        // Similar functions for service requests...

        // ===== COMMON FUNCTIONS =====

        // Archive record function
        function openArchiveModal(recordId, recordType) {
            $('#archiveRecordId').val(recordId);
            $('#archiveRecordType').val(recordType);
            $('#archiveNote').val('');
            $('#archivePassword').val('');
            $('#archiveRecordModal').modal('show');
        }

        // Care Worker Send Reminder Function
        function openSendReminderModal(recordId, recordType) {
            // Set form values
            $('#reminderRecordId').val(recordId);
            $('#reminderRecordType').val(recordType);
            $('#reminderMessage').val('');
            
            // Reset the submit button state
            $('#submitReminder').prop('disabled', false).html('{{ T::translate("Send Follow Up Reminder", "Magpadala ng Follow Up") }}');
            
            // Remove any validation styling
            $('#reminderMessage').removeClass('is-invalid');
            $('#message-error').remove();
            
            // Update modal title based on record type
            if (recordType === 'service') {
                $('#reminderModalLabel').text('{{ T::translate("Send Service Request Reminder", "Magpadala ng Paalala sa Pakiusap na Serbisyo") }}');
                $('#reminderTypeLabel').text('{{ T::translate("Service Request", "Pakiusap na Serbisyo") }}');
            } else {
                $('#reminderModalLabel').text('{{ T::translate("Send Emergency Reminder", "Magpadala ng Paalala sa Emergency") }}');
                $('#reminderTypeLabel').text('{{ T::translate("Emergency", "Emergency") }}');
            }
            
            // Show modal
            $('#sendReminderModal').modal('show');
        }

        // Submit reminder
        $('#submitReminder').on('click', function() {
            const form = $('#sendReminderForm');
            const message = $('#reminderMessage').val().trim();
            
            // Explicit message validation
            if (!message) {
                // Remove any existing validation styles
                $('#reminderMessage').addClass('is-invalid');
                
                // Show error message
                if (!$('#message-error').length) {
                    $('#reminderMessage').after('<div id="message-error" class="invalid-feedback">{{ T::translate("Please enter a reminder message", "Mangyaring maglagay ng mensahe ng paalala") }}</div>');
                }
                
                toastr.error('{{ T::translate("Please enter a reminder message", "Mangyaring maglagay ng mensahe ng paalala") }}');
                return;
            }
            
            // Remove validation error if it was previously shown
            $('#reminderMessage').removeClass('is-invalid');
            $('#message-error').remove();
            
            const formData = new FormData(form[0]);
            
            // Show loading state
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ T::translate("Sending...", "Ipinapadala...") }}');
            
            // Submit form
            $.ajax({
                url: "/care-worker/emergency-request/send-reminder",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        toastr.success('{{ T::translate("Reminder sent successfully to your care manager", "Matagumpay na naipadala ang paalala sa iyong care manager") }}');
                        
                        // Clear the form
                        $('#reminderMessage').val('');
                        
                        // Close modal
                        $('#sendReminderModal').modal('hide');
                    } else {
                        // Show error message
                        toastr.error(response.message || '{{ T::translate("Failed to send reminder", "Nabigong magpadala ng paalala") }}');
                        $('#submitReminder').prop('disabled', false).html('{{ T::translate("Send Follow Up Reminder", "Magpadala ng Follow Up") }}');
                    }
                },
                error: function(xhr) {
                    // Improved error handling with response details
                    let errorMsg = '{{ T::translate("Failed to send reminder. Please try again.", "Nabigong magpadala ng paalala. Mangyaring subukan muli.") }}';
                    
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    
                    toastr.error(errorMsg);
                    $('#submitReminder').prop('disabled', false).html('{{ T::translate("Send Follow Up Reminder", "Magpadala ng Follow Up") }}');
                },
                complete: function() {
                    // Reset button state on success (error case is handled separately)
                    if ($('#sendReminderModal').hasClass('show')) {
                        $('#submitReminder').prop('disabled', false).html('{{ T::translate("Send Follow Up Reminder", "Magpadala ng Follow Up") }}');
                    }
                }
            });
        });

        // Initialize tooltips
        $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
        });

        // Connect modal opening buttons
        $('#respondToEmergencyBtn').on('click', function() {
            $('#emergencyDetailsModal').modal('hide');
            openRespondEmergencyModal();
        });

        $('#handleServiceRequestBtn').on('click', function() {
            $('#serviceRequestDetailsModal').modal('hide');
            openHandleServiceRequestModal();
        });

        // Tab switcher for main tabs and pending column
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            const targetId = $(e.target).attr('data-bs-target');
            
            // Update pending column content based on active tab
            if (targetId === '#emergency') {
                $('#emergency-pending-content').show();
                $('#service-pending-content').hide();
            } else if (targetId === '#service') {
                $('#emergency-pending-content').hide();
                $('#service-pending-content').show();
            }
        });

        // Event handler for service update type change
        $('#serviceUpdateType').on('change', handleServiceUpdateTypeChange);

        // Render emergency details in view modal
        function renderEmergencyDetails(emergency) {
            let content = `
                <div class="mb-4">
                    <h5 class="border-bottom pb-2">{{ T::translate("Emergency Information", "Impormasyon ng Emergency") }}</h5>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Beneficiary:", "Benepisyaryo:") }}</div>
                        <div class="col-md-8">${emergency.beneficiary.first_name} ${emergency.beneficiary.last_name}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Address:", "Tirahan:") }}</div>
                        <div class="col-md-8">${emergency.beneficiary.street_address} (${emergency.beneficiary.barangay.barangay_name}, ${emergency.beneficiary.municipality.municipality_name})</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Contact Number:", "Numero ng Kontak:") }}</div>
                        <div class="col-md-8">${emergency.beneficiary.mobile || '{{ T::translate("Not provided", "Hindi ibinigay") }}'}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Emergency Contact:", "Emergency Contact:") }}</div>
                        <div class="col-md-8">${emergency.beneficiary.emergency_contact_name || '{{ T::translate("Not provided", "Hindi ibinigay") }}'} ${emergency.beneficiary.emergency_contact_name ? `(${emergency.beneficiary.emergency_contact_relation}) - ${emergency.beneficiary.emergency_contact_mobile}` : ''}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Type:", "Uri:") }}</div>
                        <div class="col-md-8"><span class="badge me-2" style="background-color: ${emergency.emergency_type.color_code}">${emergency.emergency_type.name}</span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Status:", "Status:") }}</div>
                        <div class="col-md-8">${formatStatus(emergency.status)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Created:", "Nalikha:") }}</div>
                        <div class="col-md-8">${formatDateTime(emergency.created_at)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Message:", "Mensahe:") }}</div>
                        <div class="col-md-8">${emergency.message}</div>
                    </div>
                </div>
            `;
            
            $('#emergencyDetailsContent').html(content);
            
            // Load updates timeline if any
            if (emergency.updates && emergency.updates.length > 0) {
                let updatesHtml = '';
                
                emergency.updates.forEach(update => {
                    updatesHtml += `
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-indicator">
                                    <div class="timeline-badge ${getUpdateTypeBadgeClass(update.update_type)}"></div>
                                </div>
                                <div class="timeline-content ms-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="fw-bold">${formatUpdateType(update.update_type)}</span>
                                        <small class="text-muted">${formatDateTime(update.created_at)}</small>
                                    </div>
                                    <p class="mb-1">${update.message}</p>
                                    <small class="text-muted">{{ T::translate("By:", "Ni:") }} ${update.updated_by_name || '{{ T::translate("System", "Sistema") }}'}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                $('#emergencyUpdatesTimeline').html(updatesHtml);
            } else {
                $('#emergencyUpdatesTimeline').html('<p class="text-muted">{{ T::translate("No updates yet", "Wala pang mga update") }}</p>');
            }
        }

        // Render service request details in view modal
        function renderServiceRequestDetails(request) {
            let content = `
                <div class="mb-4">
                    <h5 class="border-bottom pb-2">{{ T::translate("Service Request Information", "Impormasyon ng Pakiusap na Serbisyo") }}</h5>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Beneficiary:", "Benepisyaryo:") }}</div>
                        <div class="col-md-8">${request.beneficiary.first_name} ${request.beneficiary.last_name}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Service Type:", "Uri ng Serbisyo:") }}</div>
                        <div class="col-md-8"><span class="badge" style="background-color: ${request.service_type.color_code}">${request.service_type.name}</span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Status:", "Status:") }}</div>
                        <div class="col-md-8">${formatStatus(request.status)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Requested Date:", "Petsa ng Pakiusap:") }}</div>
                        <div class="col-md-8">${formatDate(request.service_date)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Requested Time:", "Oras ng Pakiusap:") }}</div>
                        <div class="col-md-8">${request.service_time ? formatTime(request.service_time) : '{{ T::translate("Not Specified", "Hindi Tinukoy") }}'}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Created:", "Nalikha:") }}</div>
                        <div class="col-md-8">${formatDateTime(request.created_at)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Message:", "Mensahe:") }}</div>
                        <div class="col-md-8">${request.message}</div>
                    </div>
                </div>
            `;
            
            $('#serviceRequestDetailsContent').html(content);
            
            // Load updates timeline if any
            if (request.updates && request.updates.length > 0) {
                let updatesHtml = '';
                
                request.updates.forEach(update => {
                    updatesHtml += `
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-indicator">
                                    <div class="timeline-badge ${getUpdateTypeBadgeClass(update.update_type)}"></div>
                                </div>
                                <div class="timeline-content ms-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="fw-bold">${formatUpdateType(update.update_type)}</span>
                                        <small class="text-muted">${formatDateTime(update.created_at)}</small>
                                    </div>
                                    <p class="mb-1">${update.message}</p>
                                    <small class="text-muted">{{ T::translate("By:", "Ni:") }} ${update.updated_by_name || '{{ T::translate("System", "Sistema") }}'}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                $('#serviceUpdatesTimeline').html(updatesHtml);
            } else {
                $('#serviceUpdatesTimeline').html('<p class="text-muted">{{ T::translate("No updates yet", "Wala pang mga update") }}</p>');
            }
        }

        // Helper functions for formatting
        function formatDateTime(dateTimeStr) {
            const date = new Date(dateTimeStr);
            return date.toLocaleString();
        }

        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString();
        }

        function formatTime(timeStr) {
            // Handle cases where timeStr might be just the time portion
            if (timeStr.length <= 8) {
                // Create a dummy date with the time value
                const dummyDate = new Date(`2000-01-01T${timeStr}`);
                return dummyDate.toLocaleTimeString([], {hour: 'numeric', minute: '2-digit'});
            }
            
            // Handle full datetime strings
            const date = new Date(timeStr);
            return date.toLocaleTimeString([], {hour: 'numeric', minute: '2-digit'});
        }

        function formatStatus(status) {
            switch(status) {
                case 'new': return '<span class="badge bg-danger">{{ T::translate("New", "Bago") }}</span>';
                case 'in_progress': return '<span class="badge bg-info">{{ T::translate("In Progress", "Isinasagawa") }}</span>';
                case 'approved': return '<span class="badge bg-success">{{ T::translate("Approved", "Naaprubahan") }}</span>';
                case 'rejected': return '<span class="badge bg-danger">{{ T::translate("Rejected", "Tinanggihan") }}</span>';
                case 'completed': return '<span class="badge bg-primary">{{ T::translate("Completed", "Nakumpleto") }}</span>';
                case 'resolved': return '<span class="badge bg-success">{{ T::translate("Resolved", "Nalutas") }}</span>';
                case 'archived': return '<span class="badge bg-secondary">{{ T::translate("Archived", "Na-archive") }}</span>';
                default: return `<span class="badge bg-secondary">${status}</span>`;
            }
        }

        function openResolveEmergencyModal(noticeId) {
            // Fetch emergency details and then open modal with resolution pre-selected
            $.ajax({
                url: "/care-worker/emergency-request/emergency/" + noticeId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        currentEmergency = response.emergency_notice;
                        populateEmergencyResponseModal(currentEmergency);
                        
                        // Pre-select resolution
                        $('#updateType').val('resolution').trigger('change');
                        $('#responseMessage').val('{{ T::translate("Emergency has been resolved.", "Ang emergency ay nalutas na.") }}');
                        
                        // Show modal
                        $('#respondEmergencyModal').modal('show');
                    }
                },
                error: function() {
                    toastr.error('{{ T::translate("Failed to load emergency details. Please try again.", "Nabigong i-load ang mga detalye ng emergency. Mangyaring subukan muli.") }}');
                }
            });
        }

        function openCompleteServiceRequestModal(requestId) {
            $.ajax({
                url: "/care-worker/emergency-request/service-request/" + requestId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        currentServiceRequest = response.service_request;
                        
                        // Validate if the request can be completed
                        if (currentServiceRequest.status !== 'approved') {
                            toastr.error('{{ T::translate("Only approved service requests can be marked as completed.", "Mga naaprubahang pakiusap na serbisyo lamang ang maaaring markahan bilang natapos.") }}');
                            return;
                        }
                        
                        populateServiceRequestModal(currentServiceRequest);
                        
                        // Pre-select completion
                        $('#serviceUpdateType').val('completion').trigger('change');
                        $('#serviceResponseMessage').val('{{ T::translate("Service request has been completed successfully.", "Ang pakiusap na serbisyo ay matagumpay na natapos.") }}');
                        
                        // Show modal
                        $('#handleServiceRequestModal').modal('show');
                    }
                },
                error: function() {
                    toastr.error('{{ T::translate("Failed to load service request details. Please try again.", "Nabigong i-load ang mga detalye ng pakiusap na serbisyo. Mangyaring subukan muli.") }}');
                }
            });
        }

        function showSuccessAlert(message) {
            $('#successAlertMessage').text(message);
            $('#successAlert').removeClass('d-none');
            
            // Save to session storage so it persists after page reload
            // Check if this is an emergency message or service message
            if (message.includes('Emergency')) {
                sessionStorage.setItem('emergencySuccessMessage', message);
            } else if (message.includes('Service')) {
                sessionStorage.setItem('serviceSuccessMessage', message);
            } else {
                sessionStorage.setItem('generalSuccessMessage', message);
            }
        }
        

    </script>
</body>
</html>