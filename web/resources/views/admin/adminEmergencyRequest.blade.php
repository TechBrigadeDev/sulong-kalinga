<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Emergency & Request</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        :root {
            --base-font-size: clamp(0.875rem, 2.5vw, 1rem);
            --heading-font-size: clamp(1.25rem, 3.5vw, 1.5rem);
            --section-title-size: clamp(1rem, 2.8vw, 1.25rem);
            --card-title-size: clamp(1rem, 2.5vw, 1.125rem);
            --small-text-size: clamp(0.75rem, 2vw, 0.875rem);
            --tab-font-size: clamp(0.875rem, 2vw, 1.125rem);
        }

        body {
            font-size: var(--base-font-size);
        }

        .notification-card {
            border-radius: 10px;
            margin-bottom: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }
        .notification-card:hover {
            transform: translateY(-3px);
        }
        .emergency-card {
            border-left: 5px solid #dc3545;
        }
        .request-card {
            border-left: 5px solid #0d6efd;
        }
        .notification-time {
            font-size: var(--small-text-size);
            color: #6c757d;
        }
        .section-title {
            border-bottom: 2px solid #dee2e6;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: var(--section-title-size);
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
            background-color: #f8f9fa;
            border-radius: 10px;
        }
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .history-btn {
            background-color: #6c757d;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
            font-size: var(--small-text-size);
        }
        .history-btn:hover {
            background-color: #5a6268;
        }
        .history-btn.active {
            background-color: #0d6efd;
        }
        .tab-content {
            padding: 20px 0;
        }
        .nav-tabs {
            border-bottom: 2px solid #dee2e6;
            justify-content: center;
        }
        .nav-tabs .nav-link {
            font-size: var(--tab-font-size);
            padding: 10px 20px;
            color: #495057;
            border: none;
            margin: 0 5px;
            border-radius: 5px 5px 0 0;
            transition: all 0.3s;
        }
        .nav-tabs .nav-link:hover {
            color: #0d6efd;
            background-color: #f8f9fa;
            border-color: transparent;
        }
        .nav-tabs .nav-link.active {
            font-weight: 600;
            color: #0d6efd;
            background-color: white;
            border-bottom: 3px solid #0d6efd;
        }
        .main-content {
            transition: all 0.3s ease;
            margin-top: 10px;
        }
        .card-title {
            font-size: var(--card-title-size);
            margin-bottom: 0.5rem;
        }
        .btn-sm {
            font-size: var(--small-text-size);
            padding: 0.25rem 0.5rem;
        }
        .info-item {
            margin-bottom: 0.5rem;
        }
        .info-label {
            font-weight: 600;
            color: #495057;
        }

        /* Mobile tabs for main content */
        @media (max-width: 767.98px) {
            .desktop-view {
                display: none;
            }
            .mobile-tabs {
                display: block;
            }
            .nav-tabs .nav-link {
                padding: 8px 12px;
            }
        }
        @media (min-width: 768px) {
            .mobile-tabs {
                display: none;
            }
            .desktop-view {
                display: flex;
            }
            .nav-tabs .nav-link {
                padding: 12px 24px;
            }
        }

        /* Custom styles */
        .home-content {
            background-color: #f8f9fa;
            padding: 1.5rem;
            border-radius: 0.5rem;
        }
        .notification-card {
            transition: all 0.2s ease;
            border-left-width: 4px;
            border: 1px solid #dee2e6;
            margin-bottom: 1rem;
        }
        .notification-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.1) !important;
        }
        .emergency-card {
            border-left-color: #dc3545;
        }
        .request-card {
            border-left-color: #0d6efd;
        }
        .pending-card {
            border-left-color: #ffc107;
        }
        .info-label {
            min-width: 120px;
            color: #6c757d;
        }
        .nav-tabs .nav-link {
            font-size: clamp(0.875rem, 1.2vw, 1rem);
            padding: 0.75rem 1rem;
        }
        .section-header {
            font-size: clamp(0.875rem, 1.2vw, 1rem);
            font-weight: 600;
            padding: 1rem 1.25rem;
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        .card-header-custom {
            background-color: #f8f9fa;
            border-bottom: 1px solid #dee2e6;
        }
        /* Timeline styling */
        .timeline-indicator {
            position: relative;
            width: 20px;
        }
        .timeline-badge {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            position: absolute;
            top: 5px;
            left: 0;
        }
        .timeline-content {
            border-left: 1px solid #dee2e6;
            padding-left: 15px;
            flex: 1;
        }
        .timeline-item:last-child .timeline-content {
            border-left-color: transparent;
        }
        @media (max-width: 991.98px) {
            .main-content-column {
                order: 1;
            }
            .pending-column {
                order: 2;
                margin-top: 1.5rem;
            }
        }
        @media (max-width: 575.98px) {
            .info-label {
                min-width: 100%;
                margin-bottom: 0.25rem;
            }
            .nav-tabs .nav-link {
                padding: 0.5rem 0.75rem;
            }
        }

        .card-footer-actions {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 1rem;
            border-top: 1px solid rgba(0,0,0,0.05);
            padding-top: 0.75rem;
        }
        
        .card-footer-actions .btn-group {
            white-space: nowrap;
        }
        
        .card-body {
            display: flex;
            flex-direction: column;
        }
        
        .card-content {
            flex-grow: 1;
        }

        .clickable-card {
            cursor: pointer;
        }

        /* Prevent text selection when clicking */
        .clickable-card:not(.btn) {
            user-select: none;
        }

        /* Hover effect */
        .clickable-card:hover {
            background-color: rgba(0, 0, 0, 0.01);
        }

        #successAlert {
            margin: 0 15px 15px 15px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            border-left: 4px solid #28a745;
        }

        #successAlert .btn-close:focus {
            box-shadow: none;
            outline: none;
        }
    </style>
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')

    <div class="home-section">
        <div class="page-header">
            <div class="text-left">EMERGENCY AND SERVICE REQUEST</div>
            <button class="history-btn" id="historyToggle" onclick="window.location.href='{{ route('admin.emergency.request.viewHistory') }}'">
                <i class="bi bi-clock-history me-1"></i> View History
            </button>
        </div>

        <!-- Add this success alert container -->
        <div id="successAlert" class="alert alert-success alert-dismissible fade show d-none" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <span id="successAlertMessage">Action completed successfully!</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
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
                                            <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i> Emergency
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="service-tab" data-bs-toggle="tab" data-bs-target="#service" type="button" role="tab">
                                            <i class="bi bi-hand-thumbs-up-fill text-primary me-2"></i> Service Request
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
                                                        <span class="badge bg-danger bg-opacity-10 text-danger">New</span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">Address:</span>
                                                        <span>{{ $notice->beneficiary->street_address }} ({{ $notice->beneficiary->barangay->barangay_name }}, {{ $notice->beneficiary->municipality->municipality_name }})</span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">Contact:</span>
                                                        <span >{{ $notice->beneficiary->mobile }}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">Emergency Contact: </span>
                                                        <span class="ms-2">{{ $notice->beneficiary->emergency_contact_name }} ({{ $notice->beneficiary->emergency_contact_relation }}) {{ $notice->beneficiary->emergency_contact_mobile }}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">Emergency Type: </span>
                                                        <span class="ms-2"><span class="badge" style="background-color: {{ $notice->emergencyType->color_code }}">{{ $notice->emergencyType->name }}</span></span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-3">
                                                        <span class="info-label">Message: </span>
                                                        <span>{{ Str::limit($notice->message, 100) }}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($notice->created_at)->diffForHumans() }}</small>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="viewEmergencyDetails({{ $notice->notice_id }})">
                                                                <i class="bi bi-eye me-1"></i> View
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="openRespondEmergencyModal({{ $notice->notice_id }})">
                                                                <i class="bi bi-reply me-1"></i> Respond
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="empty-state">
                                                <i class="bi bi-inbox-fill" style="font-size: 3rem;"></i>
                                                <h5 class="mt-3">No New Emergency Notices</h5>
                                                <p>There are no new emergency notices at the moment.</p>
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
                                                        <span class="badge bg-primary bg-opacity-10 text-primary">New</span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">Service Type:</span>
                                                        <span><span class="badge" style="background-color: {{ $request->serviceType->color_code }}">{{ $request->serviceType->name }}</span></span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">Requested Date:</span>
                                                        <span>{{ \Carbon\Carbon::parse($request->service_date)->format('M d, Y') }}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">Requested Time:</span>
                                                        <span>{{ $request->service_time ? \Carbon\Carbon::parse($request->service_time)->format('h:i A') : 'Flexible' }}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-3">
                                                        <span class="info-label">Message:</span>
                                                        <span>{{ Str::limit($request->message, 100) }}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($request->created_at)->diffForHumans() }}</small>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="viewServiceRequestDetails({{ $request->service_request_id }})">
                                                                <i class="bi bi-eye me-1"></i> View
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="openHandleServiceRequestModal({{ $request->service_request_id }})">
                                                                <i class="bi bi-reply me-1"></i> Handle
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="empty-state">
                                                <i class="bi bi-inbox-fill" style="font-size: 3rem;"></i>
                                                <h5 class="mt-3">No New Service Requests</h5>
                                                <p>There are no new service requests at the moment.</p>
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
                                    <i class="bi bi-hourglass-split me-2"></i>Pending/In Progress
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
                                                        <span class="badge bg-info bg-opacity-10 text-info">In Progress</span>
                                                    </div>
                                                    
                                                    @if($notice->action_taken_by)
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">Responded:</span>
                                                        <span>{{ $notice->actionTakenBy ? $notice->actionTakenBy->first_name . ' ' . $notice->actionTakenBy->last_name : 'Unknown' }}</span>
                                                    </div>
                                                    @endif
                                                    
                                                    <div class="d-flex flex-wrap mb-3">
                                                        <span class="info-label">Type:</span>
                                                        <span><span class="badge" style="background-color: {{ $notice->emergencyType->color_code }}">{{ $notice->emergencyType->name }}</span></span>
                                                    </div>
                                                </div>
                                                
                                                <div class="card-footer-actions">
                                                    <small class="text-muted">
                                                        <i class="bi bi-clock me-1"></i> Started {{ \Carbon\Carbon::parse($notice->action_taken_at)->diffForHumans() }}
                                                    </small>
                                                    <div class="btn-group" onclick="event.stopPropagation()">
                                                        <button class="btn btn-sm btn-outline-primary" onclick="openRespondEmergencyModal({{ $notice->notice_id }})">
                                                            <i class="bi bi-pencil-square me-1"></i> Update
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-success" onclick="openResolveEmergencyModal({{ $notice->notice_id }})">
                                                            <i class="bi bi-check-circle me-1"></i> Resolve
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="empty-state">
                                            <i class="bi bi-hourglass text-muted" style="font-size: 2rem;"></i>
                                            <h6 class="mt-3">No Active Emergencies</h6>
                                            <p class="small">No in-progress emergency notices at the moment.</p>
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
                                                        <span class="badge bg-success bg-opacity-10 text-success">Approved</span>
                                                    </div>

                                                    <div class="d-flex flex-wrap mb-2">
                                                        <h6 class="fw mb-0 text-primary">{{ $request->serviceType->name }}</h6>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">Approved:</span>
                                                        <span>{{ $request->actionTakenBy ? $request->actionTakenBy->first_name . ' ' . $request->actionTakenBy->last_name : 'Unknown' }}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-3">
                                                        <span class="info-label">Assigned To:</span>
                                                        <span>{{ $request->careWorker ? $request->careWorker->first_name . ' ' . $request->careWorker->last_name : 'Not assigned' }}</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="card-footer-actions">
                                                    <small class="text-muted">
                                                        <i class="bi bi-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($request->service_date)->format('M d') }}
                                                    </small>
                                                    <div class="btn-group" onclick="event.stopPropagation()">
                                                        <button class="btn btn-sm btn-outline-primary" onclick="openHandleServiceRequestModal({{ $request->service_request_id }})">
                                                            <i class="bi bi-pencil-square me-1"></i> Update
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-success" onclick="openCompleteServiceRequestModal({{ $request->service_request_id }})">
                                                            <i class="bi bi-check-circle me-1"></i> Complete
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="empty-state">
                                            <i class="bi bi-hourglass text-muted" style="font-size: 2rem;"></i>
                                            <h6 class="mt-3">No Active Service Requests</h6>
                                            <p class="small">No approved service requests at the moment.</p>
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

    <!-- Emergency Response Modal -->
    <div class="modal fade" id="respondEmergencyModal" tabindex="-1" aria-labelledby="respondEmergencyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="respondEmergencyModalLabel"><i class="bi bi-exclamation-triangle-fill"></i> Respond to Emergency</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="respondEmergencyForm">
            <input type="hidden" id="emergencyNoticeId" name="notice_id">
            
            <div class="emergency-details mb-4">
                <div class="alert alert-light border">
                <div class="d-flex justify-content-between">
                    <h6 class="text-dark" id="emergencyBeneficiaryName">Loading...</h6>
                    <span class="badge bg-danger" id="emergencyTypeBadge">Loading...</span>
                </div>
                <div class="text-muted mb-2" id="emergencyDateTime">Loading...</div>
                <div class="border-top pt-2 mt-2" id="emergencyMessage">Loading...</div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="updateType" class="form-label">Response Type</label>
                <select class="form-select" id="updateType" name="update_type" required>
                <option value="response">Emergency Response</option>
                <option value="resolution">Resolve Emergency</option>
                <option value="note">Add Note Only</option>
                </select>
                 <small id="resolutionWarning" class="text-danger d-none mt-1">
                <i class="bi bi-exclamation-circle"></i> Resolving will archive this emergency and move it to history.
                </small>
            </div>
            
            <div class="mb-3">
                <label for="responseMessage" class="form-label">Response Message</label>
                <textarea class="form-control" id="responseMessage" name="message" rows="4" placeholder="Enter your response message" required></textarea>
            </div>
            
            <div class="password-confirmation d-none mb-3">
                <div class="alert alert-warning">
                <i class="bi bi-shield-lock"></i> This action requires password confirmation
                </div>
                <label for="confirmPassword" class="form-label">Your Password</label>
                <input type="password" class="form-control" id="confirmPassword" name="password" placeholder="Enter your password">
                <div class="invalid-feedback">Incorrect password</div>
            </div>
            
            <div class="previous-updates d-none mb-4">
                <h6 class="border-bottom pb-2">Previous Updates</h6>
                <div id="updateHistoryContainer">
                <!-- Previous updates will be loaded here -->
                </div>
            </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-danger" id="submitEmergencyResponse">Submit Response</button>
        </div>
        </div>
    </div>
    </div>

    <!-- Service Request Response Modal -->
    <div class="modal fade" id="handleServiceRequestModal" tabindex="-1" aria-labelledby="handleServiceRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="handleServiceRequestModalLabel"><i class="bi bi-hand-thumbs-up"></i> Handle Service Request</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="handleServiceRequestForm">
            <input type="hidden" id="serviceRequestId" name="service_request_id">
            
            <div class="service-request-details mb-4">
                <div class="alert alert-light border">
                <div class="d-flex justify-content-between">
                    <h6 class="text-dark" id="requestBeneficiaryName">Loading...</h6>
                    <span class="badge bg-primary" id="requestTypeBadge">Loading...</span>
                </div>
                <div class="text-muted mb-2" id="requestDateTime">Loading...</div>
                <div class="border-top pt-2 mt-2" id="requestMessage">Loading...</div>
                <div class="mt-2">
                    <strong>Requested Date:</strong> <span id="requestedServiceDate">Loading...</span>
                </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="serviceUpdateType" class="form-label">Action</label>
                <select class="form-select" id="serviceUpdateType" name="update_type" required>
                <option value="approval">Approve Request</option>
                <option value="rejection">Reject Request</option>
                <option value="completion">Mark as Completed</option>
                <option value="note">Add Note Only</option>
                </select>
                <small id="completionWarning" class="text-danger d-none mt-1">
                <i class="bi bi-exclamation-circle"></i> Completing will archive this service request and move it to history
                </small>
            </div>
            
            <div class="mb-3 care-worker-options">
                <label for="serviceCareWorkerId" class="form-label">Assign Care Worker</label>
                <select class="form-select" id="serviceCareWorkerId" name="care_worker_id">
                <option value="">-- Select Care Worker --</option>
                <!-- Will be populated with care workers -->
                </select>
            </div>
            

            <div class="mb-3">
                <label for="serviceResponseMessage" class="form-label">Message</label>
                <textarea class="form-control" id="serviceResponseMessage" name="message" rows="4" placeholder="Enter your response message" required></textarea>
            </div>
            
            <div class="service-password-confirmation d-none mb-3">
                <div class="alert alert-warning">
                <i class="bi bi-shield-lock"></i> This action requires password confirmation
                </div>
                <label for="serviceConfirmPassword" class="form-label">Your Password</label>
                <input type="password" class="form-control" id="serviceConfirmPassword" name="password" placeholder="Enter your password">
                <div class="invalid-feedback">Incorrect password</div>
            </div>
            
            <div class="service-previous-updates d-none mb-4">
                <h6 class="border-bottom pb-2">Previous Updates</h6>
                <div id="serviceUpdateHistoryContainer">
                <!-- Previous updates will be loaded here -->
                </div>
            </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="button" class="btn btn-primary" id="submitServiceResponse">Submit</button>
        </div>
        </div>
    </div>
    </div>

    <!-- Emergency Details Modal (Read-only) -->
    <div class="modal fade" id="emergencyDetailsModal" tabindex="-1" aria-labelledby="emergencyDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-secondary text-white">
            <h5 class="modal-title" id="emergencyDetailsModalLabel"><i class="bi bi-info-circle"></i> Emergency Details</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div id="emergencyDetailsContent">
            <!-- Emergency details will be loaded here -->
            </div>
            
            <div class="updates-history mt-4">
            <h6 class="border-bottom pb-2">Updates History</h6>
            <div id="emergencyUpdatesTimeline">
                <!-- Updates will be loaded here -->
            </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="respondToEmergencyBtn">Respond</button>
        </div>
        </div>
    </div>
    </div>

    <!-- Service Request Details Modal -->
    <div class="modal fade" id="serviceRequestDetailsModal" tabindex="-1" aria-labelledby="serviceRequestDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-secondary text-white">
            <h5 class="modal-title" id="serviceRequestDetailsModalLabel"><i class="bi bi-info-circle"></i> Service Request Details</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div id="serviceRequestDetailsContent">
            <!-- Service request details will be loaded here -->
            </div>
            
            <div class="updates-history mt-4">
            <h6 class="border-bottom pb-2">Updates History</h6>
            <div id="serviceUpdatesTimeline">
                <!-- Updates will be loaded here -->
            </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            <button type="button" class="btn btn-primary" id="handleServiceRequestBtn">Respond</button>
        </div>
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
                success: function(message) { alert('Success: ' + message); },
                error: function(message) { alert('Error: ' + message); },
                warning: function(message) { alert('Warning: ' + message); },
                info: function(message) { alert('Info: ' + message); }
            };
        }

        // ===== EMERGENCY NOTICE HANDLERS =====
        function viewEmergencyDetails(noticeId) {
            // Show loading state
            $('#emergencyDetailsContent').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading details...</p></div>');
            
            // Open the modal
            $('#emergencyDetailsModal').modal('show');
            
            // Fetch emergency details
            $.ajax({
                url: "{{ route('admin.emergency.request.get.emergency', '') }}/" + noticeId,
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
                            $('#respondToEmergencyBtn').text('Send Reminder').removeClass('btn-primary').addClass('btn-info');
                        }
                    } else {
                        $('#emergencyDetailsContent').html(`<div class="alert alert-danger">Error loading details: ${response.message}</div>`);
                    }
                },
                error: function() {
                    $('#emergencyDetailsContent').html('<div class="alert alert-danger">Failed to load emergency details. Please try again.</div>');
                }
            });
        }

        function openRespondEmergencyModal(noticeId = null) {
            // If noticeId is provided, fetch fresh data, otherwise use current emergency
            if (noticeId) {
                $.ajax({
                    url: "{{ route('admin.emergency.request.get.emergency', '') }}/" + noticeId,
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            currentEmergency = response.emergency_notice;
                            populateEmergencyResponseModal(currentEmergency);
                            $('#respondEmergencyModal').modal('show');
                        }
                    },
                    error: function() {
                        alert('Failed to load emergency details. Please try again.');
                    }
                });
            } else if (currentEmergency) {
                populateEmergencyResponseModal(currentEmergency);
                $('#respondEmergencyModal').modal('show');
            }
        }

        function populateEmergencyResponseModal(emergency) {
            // Set basic details
            $('#emergencyNoticeId').val(emergency.notice_id);
            $('#emergencyBeneficiaryName').text(`${emergency.beneficiary.first_name} ${emergency.beneficiary.last_name}`);
            $('#emergencyTypeBadge').text(emergency.emergency_type.name);
            
            // Format date
            const createdDate = new Date(emergency.created_at);
            $('#emergencyDateTime').text(`Reported on: ${createdDate.toLocaleDateString()} at ${createdDate.toLocaleTimeString()}`);
            
            // Message
            $('#emergencyMessage').text(emergency.message);
            
            // Load previous updates if any
            if (emergency.updates && emergency.updates.length > 0) {
                $('.previous-updates').removeClass('d-none');
                let updatesHtml = '';
                
                emergency.updates.forEach(update => {
                    const updateDate = new Date(update.created_at);
                    const formattedDate = `${updateDate.toLocaleDateString()} ${updateDate.toLocaleTimeString()}`;
                    
                    updatesHtml += `
                        <div class="update-item mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="badge ${getUpdateTypeBadgeClass(update.update_type)}">${formatUpdateType(update.update_type)}</span>
                                <small class="text-muted">${formattedDate}</small>
                            </div>
                            <div class="update-message mt-1 border-bottom pb-2">${update.message}</div>
                        </div>
                    `;
                });
                
                $('#updateHistoryContainer').html(updatesHtml);
            } else {
                $('.previous-updates').addClass('d-none');
            }
            
            // Reset form fields
            $('#updateType').val('response');
            $('#responseMessage').val('');
            $('#statusChangeTo').val('in_progress');
            $('#confirmPassword').val('');
            
            // Handle form fields visibility based on selected update type
            handleUpdateTypeChange();
            
            // Load care workers for assignment option
            loadCareWorkers();
        }

        // Event handler for update type change
        $('#updateType').on('change', handleUpdateTypeChange);

        function handleUpdateTypeChange() {
            const updateType = $('#updateType').val();
            
            // Hide all conditional sections and warnings first
            // Remove assignment-options from this line
            $('.password-confirmation').addClass('d-none');
            $('#resolutionWarning').addClass('d-none');
            
            // Show relevant sections based on update type
            switch(updateType) {
                // Remove the assignment case entirely
                case 'resolution':
                    $('.password-confirmation').removeClass('d-none');
                    $('#resolutionWarning').removeClass('d-none'); // Show warning for resolution
                    break;
            }
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
                case 'response': return 'Response';
                case 'status_change': return 'Status Change';
                case 'assignment': return 'Assignment';
                case 'resolution': return 'Resolution';
                case 'note': return 'Note';
                default: return updateType;
            }
        }

        // Submit emergency response
        $('#submitEmergencyResponse').on('click', function() {
            const form = $('#respondEmergencyForm');
            const formData = new FormData(form[0]);
            
            // Validate form
            if (!form[0].checkValidity()) {
                form[0].reportValidity();
                return;
            }
            
            // Show loading state
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Submitting...');
            
            // Submit form
            $.ajax({
                url: "{{ route('admin.emergency.request.respond.emergency') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        toastr.success('Emergency response submitted successfully');
                        
                        // Close modal
                        $('#respondEmergencyModal').modal('hide');

                        // Show prominent success message if it was a resolution
                        if ($('#updateType').val() === 'resolution') {
                            const beneficiaryName = $('#emergencyBeneficiaryName').text();
                            const message = `Emergency for ${beneficiaryName} has been successfully resolved.`;
                            showSuccessAlert(message);
                        }
                        
                        // Reload page after a brief delay
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        // Show error message
                        toastr.error(response.message || 'Failed to submit response');
                        
                        // If password error
                        if (response.errors && response.errors.password) {
                            $('#confirmPassword').addClass('is-invalid');
                            $('.invalid-feedback').text(response.errors.password[0]).show();
                        }
                    }
                },
                error: function(xhr) {
                    // Parse error response
                    let errorMessage = 'Failed to submit response';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.errors && response.errors.password) {
                            $('#confirmPassword').addClass('is-invalid');
                            $('.invalid-feedback').text(response.errors.password[0]).show();
                            errorMessage = response.errors.password[0];
                        } else if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {}
                    
                    toastr.error(errorMessage);
                },
                complete: function() {
                    // Reset button state
                    $('#submitEmergencyResponse').prop('disabled', false).html('Submit Response');
                }
            });
        });

        // Add code to check for stored messages on page load
        $(document).ready(function() {
            const storedMessage = sessionStorage.getItem('emergencySuccessMessage');
            if (storedMessage) {
                showSuccessAlert(storedMessage);
                // Clear the message so it doesn't show again on next refresh
                sessionStorage.removeItem('emergencySuccessMessage');
            }
        });

        // ===== SERVICE REQUEST HANDLERS =====
        function viewServiceRequestDetails(requestId) {
            // Show loading state
            $('#serviceRequestDetailsContent').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading details...</p></div>');
            
            // Open the modal
            $('#serviceRequestDetailsModal').modal('show');
            
            // Fetch service request details
            $.ajax({
                url: "{{ route('admin.emergency.request.get.service', '') }}/" + requestId,
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
                            $('#handleServiceRequestBtn').text('Send Reminder').removeClass('btn-primary').addClass('btn-info');
                        }
                    } else {
                        $('#serviceRequestDetailsContent').html(`<div class="alert alert-danger">Error loading details: ${response.message}</div>`);
                    }
                },
                error: function() {
                    $('#serviceRequestDetailsContent').html('<div class="alert alert-danger">Failed to load service request details. Please try again.</div>');
                }
            });
        }

        // Similar functions for service requests...

        // ===== COMMON FUNCTIONS =====
        function loadCareWorkers() {
            $.ajax({
                url: "{{ route('admin.emergency.request.get.careworkers') }}",
                method: 'GET',
                success: function(response) {
                    console.log("Care workers loaded:", response); // Debug line
                    if (response.success && response.care_workers) {
                        let options = '<option value="">-- Select Care Worker --</option>';
                        response.care_workers.forEach(worker => {
                            options += `<option value="${worker.id}">${worker.first_name} ${worker.last_name}</option>`;
                        });
                        $('#serviceCareWorkerId').html(options);
                    } else {
                        console.error("Failed to load care workers:", response.message);
                        $('#serviceCareWorkerId').html('<option value="">No care workers available</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("Error loading care workers:", error);
                    $('#serviceCareWorkerId').html('<option value="">Error loading care workers</option>');
                }
            });
        }

        // Archive record function
        function openArchiveModal(recordId, recordType) {
            $('#archiveRecordId').val(recordId);
            $('#archiveRecordType').val(recordType);
            $('#archiveNote').val('');
            $('#archivePassword').val('');
            $('#archiveRecordModal').modal('show');
        }

        // Submit archive request
        $('#submitArchiveRecord').on('click', function() {
            const form = $('#archiveRecordForm');
            const formData = new FormData(form[0]);
            
            // Validate password
            if (!$('#archivePassword').val()) {
                $('#archivePassword').addClass('is-invalid');
                return;
            }
            
            // Show loading state
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Archiving...');
            
            // Submit form
            $.ajax({
                url: "{{ route('admin.emergency.request.archive') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        toastr.success('Record archived successfully');
                        
                        // Close modal
                        $('#archiveRecordModal').modal('hide');
                        
                        // Reload page after a brief delay
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        // Show error message
                        toastr.error(response.message || 'Failed to archive record');
                        
                        // If password error
                        if (response.errors && response.errors.password) {
                            $('#archivePassword').addClass('is-invalid');
                        }
                    }
                },
                error: function(xhr) {
                    // Parse error response
                    let errorMessage = 'Failed to archive record';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.errors && response.errors.password) {
                            $('#archivePassword').addClass('is-invalid');
                            errorMessage = response.errors.password[0];
                        } else if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {}
                    
                    toastr.error(errorMessage);
                },
                complete: function() {
                    // Reset button state
                    $('#submitArchiveRecord').prop('disabled', false).html('Archive');
                }
            });
        });

        // Care Worker Send Reminder Function
        function openSendReminderModal(recordId, recordType) {
            $('#reminderRecordId').val(recordId);
            $('#reminderRecordType').val(recordType);
            $('#reminderMessage').val('');
            $('#sendReminderModal').modal('show');
        }

        // Submit reminder
        $('#submitReminder').on('click', function() {
            const form = $('#sendReminderForm');
            const formData = new FormData(form[0]);
            
            // Show loading state
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Sending...');
            
            // Submit form
            $.ajax({
                url: "{{ route('care-worker.emergency.request.send.reminder') }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        toastr.success('Reminder sent successfully to your care manager');
                        
                        // Close modal
                        $('#sendReminderModal').modal('hide');
                    } else {
                        // Show error message
                        toastr.error(response.message || 'Failed to send reminder');
                    }
                },
                error: function() {
                    toastr.error('Failed to send reminder. Please try again.');
                },
                complete: function() {
                    // Reset button state
                    $('#submitReminder').prop('disabled', false).html('Send Reminder');
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

        // Service Request modal handler
        function openHandleServiceRequestModal(requestId = null) {
            // If requestId is provided, fetch fresh data, otherwise use current service request
            if (requestId) {
                $.ajax({
                    url: "{{ route('admin.emergency.request.get.service', '') }}/" + requestId,
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            currentServiceRequest = response.service_request;
                            populateServiceRequestModal(currentServiceRequest);
                            $('#handleServiceRequestModal').modal('show');
                        }
                    },
                    error: function() {
                        alert('Failed to load service request details. Please try again.');
                    }
                });
            } else if (currentServiceRequest) {
                populateServiceRequestModal(currentServiceRequest);
                $('#handleServiceRequestModal').modal('show');
            }
        }

        function populateServiceRequestModal(request) {
            // Set basic details
            $('#serviceRequestId').val(request.service_request_id);
            $('#requestBeneficiaryName').text(`${request.beneficiary.first_name} ${request.beneficiary.last_name}`);
            $('#requestTypeBadge').text(request.service_type.name);
            
            // Format date
            const createdDate = new Date(request.created_at);
            $('#requestDateTime').text(`Requested on: ${createdDate.toLocaleDateString()} at ${createdDate.toLocaleTimeString()}`);
            
            // Service date
            const serviceDate = new Date(request.service_date);
            $('#requestedServiceDate').text(serviceDate.toLocaleDateString());
            
            // Message
            $('#requestMessage').text(request.message);
            
            // Load previous updates if any
            if (request.updates && request.updates.length > 0) {
                $('.service-previous-updates').removeClass('d-none');
                let updatesHtml = '';
                
                request.updates.forEach(update => {
                    const updateDate = new Date(update.created_at);
                    const formattedDate = `${updateDate.toLocaleDateString()} ${updateDate.toLocaleTimeString()}`;
                    
                    updatesHtml += `
                        <div class="update-item mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="badge ${getUpdateTypeBadgeClass(update.update_type)}">${formatUpdateType(update.update_type)}</span>
                                <small class="text-muted">${formattedDate}</small>
                            </div>
                            <div class="update-message mt-1 border-bottom pb-2">${update.message}</div>
                        </div>
                    `;
                });
                
                $('#serviceUpdateHistoryContainer').html(updatesHtml);
            } else {
                $('.service-previous-updates').addClass('d-none');
            }
            
            // Reset form fields
            $('#serviceUpdateType').val('approval');
            $('#serviceResponseMessage').val('');
            $('#serviceStatusChangeTo').val('approved');
            $('#serviceConfirmPassword').val('');
            
            // Handle form fields visibility based on selected update type
            handleServiceUpdateTypeChange();
            
            // Load care workers for assignment option
            loadCareWorkers();
        }

        // Add function for handling service request form
        function handleServiceUpdateTypeChange() {
            const updateType = $('#serviceUpdateType').val();
            
            // Hide all conditional sections and warnings first
            $('.service-password-confirmation, .care-worker-options').addClass('d-none');
            $('#completionWarning, #statusValidationWarning').addClass('d-none');
            
            // Show/hide care worker dropdown
            if (updateType === 'approval' || updateType === 'assignment') {
                $('.care-worker-options').removeClass('d-none');
                loadCareWorkers(); // Ensure care workers are loaded
            }
            
            // Show warning for completion
            if (updateType === 'completion') {
                $('.service-password-confirmation').removeClass('d-none');
                $('#completionWarning').removeClass('d-none');
                
                // Check if the service request is in an approvable state
                if (currentServiceRequest && currentServiceRequest.status !== 'approved') {
                    $('#statusValidationWarning').removeClass('d-none');
                    $('#submitServiceResponse').prop('disabled', true);
                } else {
                    $('#submitServiceResponse').prop('disabled', false);
                }
            } else {
                $('#submitServiceResponse').prop('disabled', false);
            }
        }

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
                    <h5 class="border-bottom pb-2">Emergency Information</h5>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Beneficiary:</div>
                        <div class="col-md-8">${emergency.beneficiary.first_name} ${emergency.beneficiary.last_name}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Address:</div>
                        <div class="col-md-8">${emergency.beneficiary.street_address} (${emergency.beneficiary.barangay.barangay_name}, ${emergency.beneficiary.municipality.municipality_name})</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Contact Number:</div>
                        <div class="col-md-8">${emergency.beneficiary.mobile || 'Not provided'}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Emergency Contact:</div>
                        <div class="col-md-8">${emergency.beneficiary.emergency_contact_name || 'Not provided'} ${emergency.beneficiary.emergency_contact_name ? `(${emergency.beneficiary.emergency_contact_relation}) - ${emergency.beneficiary.emergency_contact_mobile}` : ''}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Type:</div>
                        <div class="col-md-8"><span class="badge me-2" style="background-color: ${emergency.emergency_type.color_code}">${emergency.emergency_type.name}</span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Status:</div>
                        <div class="col-md-8">${formatStatus(emergency.status)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Created:</div>
                        <div class="col-md-8">${formatDateTime(emergency.created_at)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Message:</div>
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
                                    <small class="text-muted">By: ${update.updated_by_name || 'System'}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                $('#emergencyUpdatesTimeline').html(updatesHtml);
            } else {
                $('#emergencyUpdatesTimeline').html('<p class="text-muted">No updates yet</p>');
            }
        }

        // Render service request details in view modal
        function renderServiceRequestDetails(request) {
            let content = `
                <div class="mb-4">
                    <h5 class="border-bottom pb-2">Service Request Information</h5>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Beneficiary:</div>
                        <div class="col-md-8">${request.beneficiary.first_name} ${request.beneficiary.last_name}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Service Type:</div>
                        <div class="col-md-8"><span class="badge" style="background-color: ${request.service_type.color_code}">${request.service_type.name}</span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Status:</div>
                        <div class="col-md-8">${formatStatus(request.status)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Requested Date:</div>
                        <div class="col-md-8">${formatDate(request.service_date)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Created:</div>
                        <div class="col-md-8">${formatDateTime(request.created_at)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Message:</div>
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
                                    <small class="text-muted">By: ${update.updated_by_name || 'System'}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                $('#serviceUpdatesTimeline').html(updatesHtml);
            } else {
                $('#serviceUpdatesTimeline').html('<p class="text-muted">No updates yet</p>');
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

        function formatStatus(status) {
            switch(status) {
                case 'new': return '<span class="badge bg-danger">New</span>';
                case 'in_progress': return '<span class="badge bg-info">In Progress</span>';
                case 'approved': return '<span class="badge bg-success">Approved</span>';
                case 'rejected': return '<span class="badge bg-danger">Rejected</span>';
                case 'completed': return '<span class="badge bg-primary">Completed</span>';
                case 'resolved': return '<span class="badge bg-success">Resolved</span>';
                case 'archived': return '<span class="badge bg-secondary">Archived</span>';
                default: return `<span class="badge bg-secondary">${status}</span>`;
            }
        }

        function openResolveEmergencyModal(noticeId) {
            // Fetch emergency details and then open modal with resolution pre-selected
            $.ajax({
                url: "{{ route('admin.emergency.request.get.emergency', '') }}/" + noticeId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        currentEmergency = response.emergency_notice;
                        populateEmergencyResponseModal(currentEmergency);
                        
                        // Pre-select resolution
                        $('#updateType').val('resolution').trigger('change');
                        $('#responseMessage').val('Emergency has been resolved.');
                        
                        // Show modal
                        $('#respondEmergencyModal').modal('show');
                    }
                },
                error: function() {
                    toastr.error('Failed to load emergency details. Please try again.');
                }
            });
        }

        function openCompleteServiceRequestModal(requestId) {
            $.ajax({
                url: "{{ route('admin.emergency.request.get.service', '') }}/" + requestId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        currentServiceRequest = response.service_request;
                        
                        // Validate if the request can be completed
                        if (currentServiceRequest.status !== 'approved') {
                            toastr.error('Only approved service requests can be marked as completed.');
                            return;
                        }
                        
                        populateServiceRequestModal(currentServiceRequest);
                        
                        // Pre-select completion
                        $('#serviceUpdateType').val('completion').trigger('change');
                        $('#serviceResponseMessage').val('Service request has been completed successfully.');
                        
                        // Show modal
                        $('#handleServiceRequestModal').modal('show');
                    }
                },
                error: function() {
                    toastr.error('Failed to load service request details. Please try again.');
                }
            });
        }

        function showSuccessAlert(message) {
            $('#successAlertMessage').text(message);
            $('#successAlert').removeClass('d-none');
            // Save to session storage so it persists after page reload
            sessionStorage.setItem('emergencySuccessMessage', message);
        }
        

    </script>
</body>
</html>