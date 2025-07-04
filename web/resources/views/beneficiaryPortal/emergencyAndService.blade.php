<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Emergency & Service | {{ Auth::guard('beneficiary')->check() ? 'Beneficiary' : 'Family' }} Portal</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon-white-16x16.png')}}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/portalemergencyAndService.css') }}">
    <link rel="stylesheet" href="{{ asset('css/familyPortalHomePage.css') }}">

    <style>
        /* Timeline styling */
        .timeline-item {
            position: relative;
            padding-bottom: 1.5rem;
        }
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
        
        /* Make rows/cards clickable */
        .clickable-row, .clickable-card {
            cursor: pointer;
        }
        .clickable-row:hover {
            background-color: rgba(0, 0, 0, 0.05);
        }
        .clickable-card:hover {
            transform: translateY(-2px);
            transition: transform 0.2s ease;
        }
    </style>
</head>
<body>
    @if(Auth::guard('beneficiary')->check())
        @include('components.beneficiaryPortalNavbar')
        @include('components.beneficiaryPortalSidebar')
    @else
        @include('components.familyPortalNavbar')
        @include('components.familyPortalSidebar')
    @endif

    <div class="home-section">
        <div class="text-left">EMERGENCY AND SERVICE REQUEST</div>
        <div class="container-fluid">
            <div class="row" id="home-content">
                <div class="col-12">
                    <!-- Emergency and Service Request in One Row -->
                    <div class="request-container">                                               
                        <!-- Service Request Column -->
                        <div class="request-column">
                            <div class="card service-card">
                                <div class="card-header mb-0">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-clipboard2-pulse me-2" style="color: var(--primary); font-size: var(--fs-lg);"></i>
                                        <h5 class="mb-0">Service Request</h5>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <form id="serviceRequestForm">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="requestType" class="form-label">Service Type</label>
                                            <select class="form-select" id="requestType" name="service_type_id" required>
                                                <option value="" selected disabled>Select service type</option>
                                                @foreach($serviceTypes as $type)
                                                    <option value="{{ $type->service_type_id }}">{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label for="preferredDate" class="form-label">Preferred Date</label>
                                                <input type="date" class="form-control" id="preferredDate" name="service_date" min="{{ date('Y-m-d') }}" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="preferredTime" class="form-label">Preferred Time</label>
                                                <input type="time" class="form-control" id="preferredTime" name="service_time" required>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="serviceDetails" class="form-label">Service Details</label>
                                            <textarea class="form-control" id="serviceDetails" name="message" rows="2" placeholder="Please describe your needs..." required></textarea>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <button type="submit" class="btn btn-primary">
                                                <i class="bi bi-send-fill me-1"></i> Submit Request
                                            </button>
                                        </div>
                                    </form>
                                    
                                    <div id="serviceRequestAlert" class="alert alert-success mt-3 d-flex align-items-center d-none" role="alert">
                                        <i class="bi bi-check-circle-fill me-2"></i>
                                        <div>Your service request has been submitted successfully!</div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Emergency Column -->
                        <div class="request-column">
                            <div class="card emergency-card">
                                <div class="card-header text-white bg-transparent">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-exclamation-triangle-fill me-2" style="font-size: var(--fs-lg);"></i>
                                        <h5 class="mb-0">Emergency Assistance</h5>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p class="text-white-80 mb-3">Immediate help when you need it most. Our team will respond to you as soon as we can.</p>
                                    
                                    <form id="emergencyRequestForm">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="emergencyType" class="form-label text-white-80">Emergency Type</label>
                                            <select class="form-select bg-light border-0" id="emergencyType" name="emergency_type_id" required>
                                                <option value="" selected disabled>Select type of emergency</option>
                                                @foreach($emergencyTypes as $type)
                                                    <option value="{{ $type->emergency_type_id }}">{{ $type->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="emergencyMessage" class="form-label text-white-80">Describe the emergency</label>
                                            <textarea class="form-control bg-light border-0" id="emergencyMessage" name="message" rows="3" placeholder="Briefly describe the situation..." required></textarea>
                                        </div>
                                        
                                        <button type="submit" class="btn emergency-btn" id="emergencyButton">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            <span>Request Emergency Help</span>
                                        </button>
                                    </form>
                                    
                                    <div id="emergencyAlert" class="alert alert-light mt-3 mb-0 d-flex align-items-center d-none" role="alert">
                                        <i class="bi bi-check-circle-fill text-success me-2"></i>
                                        <div>
                                            <strong>Help is on the way!</strong> Our team has been notified and will contact you shortly.
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Current Status Section -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-hourglass-top me-2" style="color: var(--warning); font-size: var(--fs-lg);"></i>
                                <h5 class="mb-0">Active Requests</h5>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover mb-0" id="activeRequestsTable">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Description</th>
                                            <th>Date Submitted</th>
                                            <th>Status</th>
                                            <th>Assigned To</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if(count($activeEmergencies) === 0 && count($activeServiceRequests) === 0)
                                            <tr>
                                                <td colspan="6" class="text-center">No active requests</td>
                                            </tr>
                                        @else
                                            @foreach($activeEmergencies as $emergency)
                                                <tr class="clickable-row" data-request-id="{{ $emergency->notice_id }}" data-request-type="emergency">
                                                    <td>
                                                        <span class="badge bg-danger">
                                                            {{ $emergency->emergencyType ? $emergency->emergencyType->name : 'Emergency' }}
                                                        </span>
                                                    </td>
                                                    <td>{{ \Illuminate\Support\Str::limit($emergency->message, 50) }}</td>
                                                    <td>{{ $emergency->created_at->format('M j, Y g:i A') }}</td>
                                                    <td>
                                                        <span class="badge {{ $emergency->status == 'new' ? 'bg-warning' : 'bg-info' }}">
                                                            {{ ucfirst(str_replace('_', ' ', $emergency->status)) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        {{ $emergency->assignedUser ? $emergency->assignedUser->name : 'Not assigned' }}
                                                    </td>
                                                    <td>
                                                        @if($emergency->status == 'new')
                                                            <div class="d-flex">
                                                                <button class="btn btn-sm btn-outline-primary edit-request me-2" 
                                                                        data-request-id="{{ $emergency->notice_id }}" 
                                                                        data-request-type="emergency">
                                                                    <i class="bi bi-pencil"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-danger cancel-request" 
                                                                        data-request-id="{{ $emergency->notice_id }}" 
                                                                        data-request-type="emergency">
                                                                    Cancel
                                                                </button>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">Processing</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            
                                            @foreach($activeServiceRequests as $service)
                                                <tr class="clickable-row" data-request-id="{{ $service->service_request_id }}" data-request-type="service">
                                                    <td>
                                                        <span class="badge bg-primary">
                                                            {{ $service->serviceType ? $service->serviceType->name : 'Service' }}
                                                        </span>
                                                    </td>
                                                    <td>{{ \Illuminate\Support\Str::limit($service->message, 50) }}</td>
                                                    <td>{{ $service->created_at->format('M j, Y g:i A') }}</td>
                                                    <td>
                                                        <span class="badge {{ $service->status == 'new' ? 'bg-warning' : 'bg-success' }}">
                                                            {{ ucfirst($service->status) }}
                                                        </span>
                                                    </td>
                                                    <td>
                                                        {{ $service->careWorker ? $service->careWorker->name : 'Not assigned' }}
                                                    </td>
                                                    <td>
                                                        @if($service->status == 'new')
                                                            <div class="d-flex">
                                                                <button class="btn btn-sm btn-outline-primary edit-request me-2" 
                                                                        data-request-id="{{ $service->service_request_id }}" 
                                                                        data-request-type="service">
                                                                    <i class="bi bi-pencil"></i>
                                                                </button>
                                                                <button class="btn btn-sm btn-outline-danger cancel-request" 
                                                                        data-request-id="{{ $service->service_request_id }}" 
                                                                        data-request-type="service">
                                                                    Cancel
                                                                </button>
                                                            </div>
                                                        @else
                                                            <span class="text-muted">Processing</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Request History Section -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center mb-0">
                            <div class="d-flex align-items-center">
                                <i class="bi bi-clock-history me-2" style="color: var(--gray); font-size: var(--fs-lg);"></i>
                                <h5 class="mb-0">Request History</h5>
                            </div>
                            <button class="btn btn-md btn-outline-secondary" id="filterHistoryBtn">
                                <i class="bi bi-filter"></i> Filter
                            </button>
                        </div>
                        <div class="card-body mt-0 pt-0" id="historyContainer">
                            @if(count($emergencyHistory) === 0 && count($serviceRequestHistory) === 0)
                                <div class="alert alert-info my-3">
                                    No request history found.
                                </div>
                            @else
                                @foreach($emergencyHistory as $emergency)
                                    <div class="status-card status-emergency p-3 mb-3 clickable-card" 
                                        data-request-id="{{ $emergency->notice_id }}" 
                                        data-request-type="emergency">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="badge bg-danger me-2">{{ $emergency->emergencyType ? $emergency->emergencyType->name : 'Emergency' }}</span>
                                                    <span class="badge bg-secondary">{{ ucfirst($emergency->status) }}</span>
                                                </div>
                                                <h6 class="mb-1">{{ \Illuminate\Support\Str::limit($emergency->message, 150) }}</h6>
                                                <div class="notification-time">
                                                    <i class="bi bi-calendar-event me-1"></i>
                                                    {{ $emergency->created_at->format('M j, Y g:i A') }}
                                                    <i class="bi bi-person me-1 ms-2"></i>
                                                    {{ $emergency->assignedUser ? 'Handled by ' . $emergency->assignedUser->name : 'Not assigned' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                                
                                @foreach($serviceRequestHistory as $service)
                                    <div class="status-card status-service p-3 mb-3 clickable-card" 
                                        data-request-id="{{ $service->service_request_id }}" 
                                        data-request-type="service">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="badge bg-primary me-2">{{ $service->serviceType ? $service->serviceType->name : 'Service' }}</span>
                                                    <span class="badge {{ $service->status == 'completed' ? 'bg-success' : 'bg-danger' }}">{{ ucfirst($service->status) }}</span>
                                                </div>
                                                <h6 class="mb-1">{{ \Illuminate\Support\Str::limit($service->message, 150) }}</h6>
                                                <div class="notification-time">
                                                    <i class="bi bi-calendar-event me-1"></i>
                                                    {{ $service->created_at->format('M j, Y g:i A') }}
                                                    @if($service->service_date)
                                                        <i class="bi bi-calendar-check me-1 ms-2"></i>
                                                        Requested for {{ \Carbon\Carbon::parse($service->service_date)->format('M j, Y') }}
                                                        @if($service->service_time)
                                                            at {{ \Carbon\Carbon::parse($service->service_time)->format('g:i A') }}
                                                        @endif
                                                    @endif
                                                    @if($service->careWorker)
                                                        <i class="bi bi-person me-1 ms-2"></i>
                                                        Handled by {{ $service->careWorker->name }}
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filterModalLabel">Filter Requests</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="filterForm">
                        <div class="mb-3">
                            <label class="form-label">Request Types</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="filterEmergency" checked>
                                    <label class="form-check-label" for="filterEmergency">Emergency</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="filterService" checked>
                                    <label class="form-check-label" for="filterService">Service</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <div class="d-flex gap-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="filterCompleted" checked>
                                    <label class="form-check-label" for="filterCompleted">Completed/Resolved</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="filterRejected" checked>
                                    <label class="form-check-label" for="filterRejected">Rejected/Archived</label>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="dateRange" class="form-label">Date Range</label>
                            <select class="form-select" id="dateRange">
                                <option value="all">All Time</option>
                                <option value="today">Today</option>
                                <option value="week">This Week</option>
                                <option value="month">This Month</option>
                                <option value="custom">Custom Range</option>
                            </select>
                        </div>
                        <div id="customDateRange" class="row g-3 mb-3" style="display: none;">
                            <div class="col-md-6">
                                <label for="startDate" class="form-label">Start Date</label>
                                <input type="date" class="form-control" id="startDate">
                            </div>
                            <div class="col-md-6">
                                <label for="endDate" class="form-label">End Date</label>
                                <input type="date" class="form-control" id="endDate" max="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="applyFilter">Apply Filter</button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Confirmation Modal -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmationModalLabel">Confirm Cancellation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to cancel this request? This action cannot be undone.</p>
                    <input type="hidden" id="cancelRequestId">
                    <input type="hidden" id="cancelRequestType">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Keep Request</button>
                    <button type="button" class="btn btn-danger" id="confirmCancelBtn">Yes, Cancel Request</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Emergency Details Modal -->
    <div class="modal fade" id="emergencyDetailsModal" tabindex="-1" aria-labelledby="emergencyDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
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
                </div>
            </div>
        </div>
    </div>

    <!-- Service Request Details Modal -->
    <div class="modal fade" id="serviceRequestDetailsModal" tabindex="-1" aria-labelledby="serviceRequestDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
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
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Emergency Modal -->
    <div class="modal fade" id="editEmergencyModal" tabindex="-1" aria-labelledby="editEmergencyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="editEmergencyModalLabel">Edit Emergency Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editEmergencyForm">
                        @csrf
                        <input type="hidden" id="edit_emergency_notice_id" name="notice_id">
                        
                        <div class="mb-3">
                            <label for="edit_emergency_type_id" class="form-label">Emergency Type</label>
                            <select class="form-select" id="edit_emergency_type_id" name="emergency_type_id" required>
                                @foreach($emergencyTypes as $type)
                                    <option value="{{ $type->emergency_type_id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_emergency_message" class="form-label">Message</label>
                            <textarea class="form-control" id="edit_emergency_message" name="message" rows="4" placeholder="Describe the emergency situation" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="saveEmergencyChanges">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Service Request Modal -->
    <div class="modal fade" id="editServiceModal" tabindex="-1" aria-labelledby="editServiceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="editServiceModalLabel">Edit Service Request</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editServiceForm">
                        @csrf
                        <input type="hidden" id="edit_service_request_id" name="service_request_id">
                        
                        <div class="mb-3">
                            <label for="edit_service_type_id" class="form-label">Service Type</label>
                            <select class="form-select" id="edit_service_type_id" name="service_type_id" required>
                                @foreach($serviceTypes as $type)
                                    <option value="{{ $type->service_type_id }}">{{ $type->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="edit_service_date" class="form-label">Preferred Date</label>
                                <input type="date" class="form-control" id="edit_service_date" name="service_date" required min="{{ date('Y-m-d') }}">
                            </div>
                            <div class="col-md-6">
                                <label for="edit_service_time" class="form-label">Preferred Time</label>
                                <input type="time" class="form-control" id="edit_service_time" name="service_time" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_service_message" class="form-label">Details</label>
                            <textarea class="form-control" id="edit_service_message" name="message" rows="4" placeholder="Describe what you need help with" required></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveServiceChanges">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Set up CSRF token for AJAX requests
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        
        // Emergency form submission
        $('#emergencyRequestForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const alertDiv = $('#emergencyAlert');
            
            $.ajax({
                url: '{{ secure_url(route(Auth::guard("beneficiary")->check() ? "beneficiary.emergency.service.submit-emergency" : "family.emergency.service.submit-emergency", [], false)) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Show success alert
                    alertDiv.removeClass('d-none');
                    
                    // Reset form
                    $('#emergencyRequestForm')[0].reset();
                    
                    // Hide alert after 5 seconds
                    setTimeout(function() {
                        alertDiv.addClass('d-none');
                    }, 5000);
                    
                    // Refresh active requests
                    refreshActiveRequests();
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to submit emergency request.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMsg = Object.values(xhr.responseJSON.errors).flat()[0];
                    }
                    
                    alertDiv.removeClass('d-none alert-light')
                           .addClass('alert-danger')
                           .find('div').html('<strong>Error!</strong> ' + errorMsg);
                    
                    setTimeout(function() {
                        alertDiv.addClass('d-none')
                               .removeClass('alert-danger')
                               .addClass('alert-light')
                               .find('div').html('<strong>Help is on the way!</strong> Our team has been notified and will contact you shortly.');
                    }, 5000);
                }
            });
        });
        
        // Service request form submission
        $('#serviceRequestForm').on('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const alertDiv = $('#serviceRequestAlert');
            
            $.ajax({
                url: '{{ secure_url(route(Auth::guard("beneficiary")->check() ? "beneficiary.emergency.service.submit-service" : "family.emergency.service.submit-service", [], false)) }}',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    // Show success alert
                    alertDiv.removeClass('d-none');
                    
                    // Reset form
                    $('#serviceRequestForm')[0].reset();
                    
                    // Hide alert after 5 seconds
                    setTimeout(function() {
                        alertDiv.addClass('d-none');
                    }, 5000);
                    
                    // Refresh active requests
                    refreshActiveRequests();
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to submit service request.';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        errorMsg = Object.values(xhr.responseJSON.errors).flat()[0];
                    }
                    
                    alertDiv.removeClass('d-none alert-success')
                           .addClass('alert-danger')
                           .html('<i class="bi bi-exclamation-triangle-fill me-2"></i><div><strong>Error!</strong> ' + errorMsg + '</div>');
                    
                    setTimeout(function() {
                        alertDiv.addClass('d-none')
                               .removeClass('alert-danger')
                               .addClass('alert-success')
                               .html('<i class="bi bi-check-circle-fill me-2"></i><div>Your service request has been submitted successfully!</div>');
                    }, 5000);
                }
            });
        });
        
        // Initialize event handlers for cancel buttons
        $(document).ready(function() {
            attachCancelEventHandlers();
        });
        
        // Function to attach cancel event handlers
        function attachCancelEventHandlers() {
            $('.cancel-request').on('click', function(e) {
                // Stop event propagation to prevent row click from triggering
                e.stopPropagation();
                
                const requestId = $(this).data('request-id');
                const requestType = $(this).data('request-type');
                
                // Set values in the confirmation modal
                $('#cancelRequestId').val(requestId);
                $('#cancelRequestType').val(requestType);
                
                // Show confirmation modal
                const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
                confirmationModal.show();
            });
        }
        
        // Confirm cancel button click
        // Update the confirmation button click handler
        $('#confirmCancelBtn').on('click', function() {
            const requestId = $('#cancelRequestId').val();
            const requestType = $('#cancelRequestType').val();
            
            $.ajax({
                url: '{{ secure_url(route(Auth::guard("beneficiary")->check() ? "beneficiary.emergency.service.cancel" : "family.emergency.service.cancel", [], false)) }}',
                type: 'POST',
                data: {
                    request_id: requestId,
                    request_type: requestType
                },
                success: function(response) {
                    // Use the new function to properly close the modal
                    closeModalProperly('confirmationModal');
                    
                    // Show success alert
                    setTimeout(() => alert('Request cancelled successfully.'), 300);
                    
                    // Refresh active requests and history
                    refreshActiveRequests();
                    refreshRequestHistory();
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to cancel request.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    
                    // Use the new function to properly close the modal
                    closeModalProperly('confirmationModal');
                    
                    // Show error alert
                    setTimeout(() => alert('Error: ' + errorMsg), 300);
                }
            });
        });

        $('.modal .btn-secondary').on('click', function() {
            // Remove focus from the button before Bootstrap attempts to close the modal
            setTimeout(() => {
                document.activeElement.blur();
                document.body.focus(); 
            }, 10);
        });
        
        // Filter button click
        $('#filterHistoryBtn').on('click', function() {
            const filterModal = new bootstrap.Modal(document.getElementById('filterModal'));
            filterModal.show();
        });
        
        // Date range change
        $('#dateRange').on('change', function() {
            const selectedValue = $(this).val();
            
            if (selectedValue === 'custom') {
                $('#customDateRange').show();
            } else {
                $('#customDateRange').hide();
            }
        });
        
        // Apply filter button click
        $('#applyFilter').on('click', function() {
            refreshRequestHistory();
            bootstrap.Modal.getInstance(document.getElementById('filterModal')).hide();
        });
        
        // Function to refresh active requests
        function refreshActiveRequests() {
            $.ajax({
                url: '{{ secure_url(route(Auth::guard("beneficiary")->check() ? "beneficiary.emergency.service.active" : "family.emergency.service.active", [], false)) }}',
                type: 'GET',
                success: function(response) {
                    // Build the new table content
                    let tableContent = '';
                    
                    if (response.emergencies.length === 0 && response.serviceRequests.length === 0) {
                        tableContent += `
                            <tr>
                                <td colspan="6" class="text-center">No active requests</td>
                            </tr>
                        `;
                    } else {
                        // Add emergencies
                        response.emergencies.forEach(function(emergency) {
                            tableContent += `
                                <tr class="clickable-row" data-request-id="${emergency.notice_id}" data-request-type="emergency">
                                    <td>
                                        <span class="badge bg-danger">
                                            ${emergency.emergency_type ? emergency.emergency_type.name : 'Emergency'}
                                        </span>
                                    </td>
                                    <td>${emergency.message.length > 50 ? emergency.message.substring(0, 50) + '...' : emergency.message}</td>
                                    <td>${formatDateTime(emergency.created_at)}</td>
                                    <td>
                                        <span class="badge ${emergency.status === 'new' ? 'bg-warning' : 'bg-info'}">
                                            ${formatStatus(emergency.status)}
                                        </span>
                                    </td>
                                    <td>
                                        ${emergency.assigned_user ? emergency.assigned_user.name : 'Not assigned'}
                                    </td>
                                    <td>
                                        ${emergency.status === 'new' ? 
                                            `<div class="d-flex">
                                                <button class="btn btn-sm btn-outline-primary edit-request me-2" 
                                                    data-request-id="${emergency.notice_id}" 
                                                    data-request-type="emergency">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger cancel-request" 
                                                    data-request-id="${emergency.notice_id}" 
                                                    data-request-type="emergency">
                                                    Cancel
                                                </button>
                                            </div>` : 
                                            `<span class="text-muted">Processing</span>`}
                                    </td>
                                </tr>
                            `;
                        });
                        
                        // Add service requests
                        response.serviceRequests.forEach(function(service) {
                            tableContent += `
                                <tr class="clickable-row" data-request-id="${service.service_request_id}" data-request-type="service">
                                    <td>
                                        <span class="badge bg-primary">
                                            ${service.service_type ? service.service_type.name : 'Service'}
                                        </span>
                                    </td>
                                    <td>${service.message.length > 50 ? service.message.substring(0, 50) + '...' : service.message}</td>
                                    <td>${formatDateTime(service.created_at)}</td>
                                    <td>
                                        <span class="badge ${service.status === 'new' ? 'bg-warning' : 'bg-success'}">
                                            ${formatStatus(service.status)}
                                        </span>
                                    </td>
                                    <td>
                                        ${service.care_worker ? service.care_worker.name : 'Not assigned'}
                                    </td>
                                    <td>
                                        ${service.status === 'new' ? 
                                            `<div class="d-flex">
                                                <button class="btn btn-sm btn-outline-primary edit-request me-2" 
                                                    data-request-id="${service.service_request_id}" 
                                                    data-request-type="service">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger cancel-request" 
                                                    data-request-id="${service.service_request_id}" 
                                                    data-request-type="service">
                                                    Cancel
                                                </button>
                                            </div>` : 
                                            `<span class="text-muted">Processing</span>`}
                                    </td>
                                </tr>
                            `;
                        });
                    }
                    
                    // Update the table
                    $('#activeRequestsTable tbody').html(tableContent);
                    
                     // Re-attach event handlers
                    attachActionEventHandlers();
                },
                error: function(xhr) {
                    console.error('Error fetching active requests:', xhr);
                }
            });
        }
        
        // Function to refresh request history based on filters
        function refreshRequestHistory() {
            // Get filter values
            const includeEmergency = $('#filterEmergency').is(':checked');
            const includeService = $('#filterService').is(':checked');
            const includeCompleted = $('#filterCompleted').is(':checked');
            const includeRejected = $('#filterRejected').is(':checked');
            const dateRange = $('#dateRange').val();
            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();
            
            // Show loading state
            $('#historyContainer').html('<div class="text-center p-4"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div><p class="mt-2">Loading history...</p></div>');
            
            $.ajax({
                url: '{{ secure_url(route(Auth::guard("beneficiary")->check() ? "beneficiary.emergency.service.history" : "family.emergency.service.history", [], false)) }}',
                type: 'POST',
                data: {
                    include_emergency: includeEmergency ? 1 : 0,
                    include_service: includeService ? 1 : 0,
                    include_completed: includeCompleted ? 1 : 0,
                    include_rejected: includeRejected ? 1 : 0,
                    date_range: dateRange,
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    let historyContent = '';
                    
                    if ((!response.data.emergencies || response.data.emergencies.length === 0) && 
                        (!response.data.serviceRequests || response.data.serviceRequests.length === 0)) {
                        historyContent = `
                            <div class="alert alert-info my-3">
                                No request history found with the selected filters.
                            </div>
                        `;
                    } else {
                        // Add emergencies to history
                        if (response.data.emergencies && response.data.emergencies.length > 0) {
                            response.data.emergencies.forEach(function(emergency) {
                                historyContent += `
                                    <div class="status-card status-emergency p-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="badge bg-danger me-2">
                                                        ${emergency.emergency_type ? emergency.emergency_type.name : 'Emergency'}
                                                    </span>
                                                    <span class="badge bg-secondary">
                                                        ${formatStatus(emergency.status)}
                                                    </span>
                                                </div>
                                                <h6 class="mb-1">
                                                    ${emergency.message.length > 150 ? emergency.message.substring(0, 150) + '...' : emergency.message}
                                                </h6>
                                                <div class="notification-time">
                                                    <i class="bi bi-calendar-event me-1"></i>
                                                    ${formatDateTime(emergency.created_at)}
                                                    <i class="bi bi-person me-1 ms-2"></i>
                                                    ${emergency.assigned_user ? 'Handled by ' + emergency.assigned_user.name : 'Not assigned'}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                        }
                        
                        // Add service requests to history
                        if (response.data.serviceRequests && response.data.serviceRequests.length > 0) {
                            response.data.serviceRequests.forEach(function(service) {
                                historyContent += `
                                    <div class="status-card status-service p-3 mb-3">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div>
                                                <div class="d-flex align-items-center mb-1">
                                                    <span class="badge bg-primary me-2">
                                                        ${service.service_type ? service.service_type.name : 'Service'}
                                                    </span>
                                                    <span class="badge ${service.status === 'completed' ? 'bg-success' : 'bg-danger'}">
                                                        ${formatStatus(service.status)}
                                                    </span>
                                                </div>
                                                <h6 class="mb-1">
                                                    ${service.message.length > 150 ? service.message.substring(0, 150) + '...' : service.message}
                                                </h6>
                                                <div class="notification-time">
                                                    <i class="bi bi-calendar-event me-1"></i>
                                                    ${formatDateTime(service.created_at)}
                                                    ${service.service_date ? `
                                                        <i class="bi bi-calendar-check me-1 ms-2"></i>
                                                        Requested for ${formatDate(service.service_date)}
                                                        ${service.service_time ? ' at ' + formatTime(service.service_time) : ''}
                                                    ` : ''}
                                                    ${service.care_worker ? `
                                                        <i class="bi bi-person me-1 ms-2"></i>
                                                        Handled by ${service.care_worker.name}
                                                    ` : ''}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            });
                        }
                    }
                    
                    // Update the history container
                    $('#historyContainer').html(historyContent);
                },
                error: function(xhr) {
                    console.error('Error fetching request history:', xhr);
                    
                    $('#historyContainer').html(`
                        <div class="alert alert-danger my-3">
                            An error occurred while loading the request history. Please try again.
                        </div>
                    `);
                }
            });
        }
        
        // Helper functions for formatting
        function formatDateTime(dateTimeStr) {
            const date = new Date(dateTimeStr);
            return date.toLocaleDateString('en-US', {
                month: 'short', 
                day: 'numeric',
                year: 'numeric',
                hour: 'numeric',
                minute: '2-digit',
                hour12: true
            });
        }
        
        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString('en-US', {
                month: 'short', 
                day: 'numeric',
                year: 'numeric'
            });
        }
        
        function formatTime(timeStr) {
            // Handle cases where timeStr might be just the time portion
            if (timeStr.length <= 8) {
                const [hours, minutes] = timeStr.split(':');
                const date = new Date();
                date.setHours(parseInt(hours), parseInt(minutes));
                return date.toLocaleTimeString([], {hour: 'numeric', minute: '2-digit'});
            }
            
            // Handle full datetime strings
            const date = new Date(timeStr);
            return date.toLocaleTimeString([], {hour: 'numeric', minute: '2-digit'});
        }
        
        function formatStatus(status) {
            // Replace underscores with spaces and capitalize first letter
            return status.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
        }

        // Make table rows clickable
        $(document).on('click', '.clickable-row', function() {
            const requestId = $(this).data('request-id');
            const requestType = $(this).data('request-type');
            
            if (requestType === 'emergency') {
                viewEmergencyDetails(requestId);
            } else {
                viewServiceRequestDetails(requestId);
            }
        });

        // Make history cards clickable
        $(document).on('click', '.clickable-card', function() {
            const requestId = $(this).data('request-id');
            const requestType = $(this).data('request-type');
            
            if (requestType === 'emergency') {
                viewEmergencyDetails(requestId);
            } else {
                viewServiceRequestDetails(requestId);
            }
        });

        // View emergency details
        function viewEmergencyDetails(noticeId) {
            // Show loading state
            $('#emergencyDetailsContent').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading details...</p></div>');
            $('#emergencyUpdatesTimeline').html('');
            
            // Open the modal
            $('#emergencyDetailsModal').modal('show');
            
            // Fetch emergency details
            $.ajax({
                url: "{{ secure_url(route(Auth::guard('beneficiary')->check() ? 'beneficiary.emergency.service.emergency-details' : 'family.emergency.service.emergency-details', '', false)) }}/" + noticeId,
                method: 'GET',
                success: function(response) {
                    if (response.success && response.emergency_notice) {
                        renderEmergencyDetails(response.emergency_notice);
                    } else {
                        $('#emergencyDetailsContent').html('<div class="alert alert-warning"><i class="bi bi-exclamation-triangle-fill me-2"></i>Emergency details could not be loaded.</div>');
                    }
                },
                error: function(xhr) {
                    $('#emergencyDetailsContent').html('<div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Failed to load emergency details. Please try again.</div>');
                    console.error('Error loading emergency details:', xhr.responseText);
                }
            });
        }

        // View service request details
        function viewServiceRequestDetails(requestId) {
            // Show loading state
            $('#serviceRequestDetailsContent').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading details...</p></div>');
            $('#serviceUpdatesTimeline').html('');
            
            // Open the modal
            $('#serviceRequestDetailsModal').modal('show');
            
            // Fetch service request details
            $.ajax({
                url: "{{ secure_url(route(Auth::guard('beneficiary')->check() ? 'beneficiary.emergency.service.service-details' : 'family.emergency.service.service-details', '', false)) }}/" + requestId,
                method: 'GET',
                success: function(response) {
                    if (response.success && response.service_request) {
                        renderServiceRequestDetails(response.service_request);
                    } else {
                        $('#serviceRequestDetailsContent').html('<div class="alert alert-warning"><i class="bi bi-exclamation-triangle-fill me-2"></i>Service request details could not be loaded.</div>');
                    }
                },
                error: function(xhr) {
                    $('#serviceRequestDetailsContent').html('<div class="alert alert-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>Failed to load service request details. Please try again.</div>');
                    console.error('Error loading service request details:', xhr.responseText);
                }
            });
        }

        // Render emergency details in view modal
        function renderEmergencyDetails(emergency) {
            // Get formatted status and type
            const status = formatStatus(emergency.status || 'unknown');
            const type = emergency.emergency_type ? emergency.emergency_type.name : 'Unknown Type';
            const statusClass = getStatusBadgeClass(emergency.status);
            const assignedTo = emergency.assigned_user ? emergency.assigned_user.name : 'Not assigned';
            
            let content = `
                <div class="mb-4">
                    <h5 class="mb-3">Emergency Request Details</h5>
                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Status:</div>
                        <div class="col-md-8">
                            <span class="badge ${statusClass}">${status}</span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Type:</div>
                        <div class="col-md-8">${type}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Submitted On:</div>
                        <div class="col-md-8">${formatDateTime(emergency.created_at)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Assigned To:</div>
                        <div class="col-md-8">${assignedTo}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Message:</div>
                        <div class="col-md-8">${emergency.message}</div>
                    </div>
                </div>
            `;
            
            $('#emergencyDetailsContent').html(content);
            
            // Load updates timeline if any
            if (emergency.updates && emergency.updates.length > 0) {
                let updatesHtml = '';
                
                emergency.updates.forEach(update => {
                    const updateTime = formatDateTime(update.created_at);
                    const updateType = formatUpdateType(update.update_type);
                    const badgeClass = getUpdateTypeBadgeClass(update.update_type);
                    const staffName = update.staff_name || 'Unknown Staff';
                    
                    updatesHtml += `
                        <div class="timeline-item d-flex">
                            <div class="timeline-indicator">
                                <div class="timeline-badge ${badgeClass}"></div>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div>
                                        <span class="badge ${badgeClass} me-2">${updateType}</span>
                                        <span class="text-muted">by ${staffName}</span>
                                    </div>
                                    <small class="text-muted">${updateTime}</small>
                                </div>
                                <div>${update.message}</div>
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
            // Get formatted status and type
            const status = formatStatus(request.status || 'unknown');
            const type = request.service_type ? request.service_type.name : 'Unknown Type';
            const statusClass = getStatusBadgeClass(request.status);
            const careWorker = request.care_worker ? request.care_worker.first_name + ' ' + request.care_worker.last_name : 'Not assigned';
            
            let content = `
                <div class="mb-4">
                    <h5 class="mb-3">Service Request Details</h5>
                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Status:</div>
                        <div class="col-md-8">
                            <span class="badge ${statusClass}">${status}</span>
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Type:</div>
                        <div class="col-md-8">${type}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Requested For:</div>
                        <div class="col-md-8">${formatDate(request.service_date)} at ${formatTime(request.service_time)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Care Worker:</div>
                        <div class="col-md-8">${careWorker}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 text-muted">Submitted On:</div>
                        <div class="col-md-8">${formatDateTime(request.created_at)}</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-4 text-muted">Message:</div>
                        <div class="col-md-8">${request.message}</div>
                    </div>
                </div>
            `;
            
            $('#serviceRequestDetailsContent').html(content);
            
            // Load updates timeline if any
            if (request.updates && request.updates.length > 0) {
                let updatesHtml = '';
                
                request.updates.forEach(update => {
                    const updateTime = formatDateTime(update.created_at);
                    const updateType = formatUpdateType(update.update_type);
                    const badgeClass = getUpdateTypeBadgeClass(update.update_type);
                    const staffName = update.staff_name || 'Unknown Staff';
                    
                    updatesHtml += `
                        <div class="timeline-item d-flex">
                            <div class="timeline-indicator">
                                <div class="timeline-badge ${badgeClass}"></div>
                            </div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-center mb-1">
                                    <div>
                                        <span class="badge ${badgeClass} me-2">${updateType}</span>
                                        <span class="text-muted">by ${staffName}</span>
                                    </div>
                                    <small class="text-muted">${updateTime}</small>
                                </div>
                                <div>${update.message}</div>
                            </div>
                        </div>
                    `;
                });
                
                $('#serviceUpdatesTimeline').html(updatesHtml);
            } else {
                $('#serviceUpdatesTimeline').html('<p class="text-muted">No updates yet</p>');
            }
        }

        // Helper function to get badge class based on status
        function getStatusBadgeClass(status) {
            switch(status) {
                case 'new': return 'bg-warning';
                case 'in_progress': return 'bg-info';
                case 'resolved': return 'bg-success';
                case 'approved': return 'bg-success';
                case 'rejected': return 'bg-danger';
                case 'completed': return 'bg-success';
                default: return 'bg-secondary';
            }
        }

        // Helper function to get badge class based on update type
        function getUpdateTypeBadgeClass(updateType) {
            switch(updateType) {
                case 'response': return 'bg-primary';
                case 'status_change': return 'bg-warning';
                case 'assignment': return 'bg-info';
                case 'resolution': return 'bg-success';
                case 'completion': return 'bg-success';
                case 'rejection': return 'bg-danger';
                case 'approval': return 'bg-success';
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
                case 'completion': return 'Completion';
                case 'rejection': return 'Rejection';
                case 'approval': return 'Approval';
                case 'note': return 'Note';
                default: return updateType.replace('_', ' ').replace(/\b\w/g, l => l.toUpperCase());
            }
        }

        // Function to attach edit and cancel event handlers
        function attachActionEventHandlers() {
            $('.cancel-request').on('click', function(e) {
                // Stop event propagation to prevent row click from triggering
                e.stopPropagation();
                
                const requestId = $(this).data('request-id');
                const requestType = $(this).data('request-type');
                
                // Set values in the confirmation modal
                $('#cancelRequestId').val(requestId);
                $('#cancelRequestType').val(requestType);
                
                // Show confirmation modal
                const confirmationModal = new bootstrap.Modal(document.getElementById('confirmationModal'));
                confirmationModal.show();
            });
            
            $('.edit-request').on('click', function(e) {
                // Stop event propagation to prevent row click from triggering
                e.stopPropagation();
                
                const requestId = $(this).data('request-id');
                const requestType = $(this).data('request-type');
                
                // Load request details for editing
                if (requestType === 'emergency') {
                    loadEmergencyForEdit(requestId);
                } else {
                    loadServiceForEdit(requestId);
                }
            });
        }

        // Update attachCancelEventHandlers to call the new function
        function attachCancelEventHandlers() {
            attachActionEventHandlers();
        }

        // Load emergency details for editing
        function loadEmergencyForEdit(noticeId) {
            // Show loading state in button
            $(`button.edit-request[data-request-id="${noticeId}"]`).html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            
            $.ajax({
                url: "{{ secure_url(route(Auth::guard('beneficiary')->check() ? 'beneficiary.emergency.service.emergency-details' : 'family.emergency.service.emergency-details', '', false)) }}/" + noticeId,
                method: 'GET',
                success: function(response) {
                    if (response.success && response.emergency_notice) {
                        const emergency = response.emergency_notice;
                        
                        // Populate the edit form
                        $('#edit_emergency_notice_id').val(emergency.notice_id);
                        $('#edit_emergency_type_id').val(emergency.emergency_type_id);
                        $('#edit_emergency_message').val(emergency.message);
                        
                        // Show the modal
                        const editModal = new bootstrap.Modal(document.getElementById('editEmergencyModal'));
                        editModal.show();
                    } else {
                        alert('Could not load emergency details. Please try again.');
                    }
                    
                    // Reset button
                    $(`button.edit-request[data-request-id="${noticeId}"]`).html('<i class="bi bi-pencil"></i>');
                },
                error: function(xhr) {
                    alert('Failed to load emergency details. Please try again.');
                    console.error('Error loading emergency details:', xhr.responseText);
                    
                    // Reset button
                    $(`button.edit-request[data-request-id="${noticeId}"]`).html('<i class="bi bi-pencil"></i>');
                }
            });
        }

        // Load service request details for editing
        function loadServiceForEdit(requestId) {
            // Show loading state in button
            $(`button.edit-request[data-request-id="${requestId}"]`).html('<span class="spinner-border spinner-border-sm" role="status"></span>');
            
            $.ajax({
                url: "{{ secure_url(route(Auth::guard('beneficiary')->check() ? 'beneficiary.emergency.service.service-details' : 'family.emergency.service.service-details', '', false)) }}/" + requestId,
                method: 'GET',
                success: function(response) {
                    if (response.success && response.service_request) {
                        const service = response.service_request;
                        
                        // Populate the edit form
                        $('#edit_service_request_id').val(service.service_request_id);
                        $('#edit_service_type_id').val(service.service_type_id);
                        
                        // Fix for PostgreSQL date format - ensure YYYY-MM-DD format
                        const serviceDate = service.service_date ? new Date(service.service_date) : null;
                        if (serviceDate) {
                            // Format as YYYY-MM-DD
                            const formattedDate = serviceDate.toISOString().split('T')[0];
                            $('#edit_service_date').val(formattedDate);
                        }
                        
                        $('#edit_service_time').val(service.service_time);
                        $('#edit_service_message').val(service.message);
                        
                        // Show the modal
                        const editModal = new bootstrap.Modal(document.getElementById('editServiceModal'));
                        editModal.show();
                    } else {
                        alert('Could not load service request details. Please try again.');
                    }
                    
                    // Reset button
                    $(`button.edit-request[data-request-id="${requestId}"]`).html('<i class="bi bi-pencil"></i>');
                },
                error: function(xhr) {
                    alert('Failed to load service request details. Please try again.');
                    console.error('Error loading service request details:', xhr.responseText);
                    
                    // Reset button
                    $(`button.edit-request[data-request-id="${requestId}"]`).html('<i class="bi bi-pencil"></i>');
                }
            });
        }

        // Save emergency changes
        // Update the save emergency changes handler
        $('#saveEmergencyChanges').on('click', function() {
            const form = document.getElementById('editEmergencyForm');
            const formData = new FormData(form);
            
            // Show loading state
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Saving...');
            
            $.ajax({
                url: "{{ secure_url(route(Auth::guard('beneficiary')->check() ? 'beneficiary.emergency.service.update-emergency' : 'family.emergency.service.update-emergency', '', false)) }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Use the new function to properly close the modal
                        closeModalProperly('editEmergencyModal');
                        
                        // Show success message
                        setTimeout(() => alert('Emergency request updated successfully.'), 300);
                        
                        // Refresh the active requests list
                        refreshActiveRequests();
                    } else {
                        alert('Error updating emergency request: ' + response.message);
                    }
                    
                    // Reset button
                    $('#saveEmergencyChanges').prop('disabled', false).text('Save Changes');
                },
                error: function(xhr) {
                    let message = 'Failed to update emergency request.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                    
                    alert(message);
                    console.error('Error updating emergency request:', xhr.responseText);
                    
                    // Reset button
                    $('#saveEmergencyChanges').prop('disabled', false).text('Save Changes');
                }
            });
        });

        // Save service request changes
        $('#saveServiceChanges').on('click', function() {
            const form = document.getElementById('editServiceForm');
            const formData = new FormData(form);
            
            // Show loading state
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status"></span> Saving...');
            
            $.ajax({
                url: "{{ secure_url(route(Auth::guard('beneficiary')->check() ? 'beneficiary.emergency.service.update-service' : 'family.emergency.service.update-service', '', false)) }}",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Use the new function to properly close the modal
                        closeModalProperly('editServiceModal');
                        
                        // Show success message
                        setTimeout(() => alert('Service request updated successfully.'), 300);
                        
                        // Refresh the active requests list
                        refreshActiveRequests();
                    } else {
                        alert('Error updating service request: ' + response.message);
                    }
                    
                    // Reset button
                    $('#saveServiceChanges').prop('disabled', false).text('Save Changes');
                },
                error: function(xhr) {
                    let message = 'Failed to update service request.';
                    
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        message = Object.values(xhr.responseJSON.errors).flat().join('\n');
                    }
                    
                    alert(message);
                    console.error('Error updating service request:', xhr.responseText);
                    
                    // Reset button
                    $('#saveServiceChanges').prop('disabled', false).text('Save Changes');
                }
            });
        });

        // Initialize event handlers
        $(document).ready(function() {
            attachActionEventHandlers();

            // Safety measure - if Escape key is pressed, clean up any errant backdrops
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape') {
                    setTimeout(() => {
                        // If no modals are showing but we have backdrops or modal-open class
                        if (!document.querySelector('.modal.show') && 
                            (document.querySelector('.modal-backdrop') || 
                            document.body.classList.contains('modal-open'))) {
                            
                            // Remove all backdrop elements
                            document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                                backdrop.remove();
                            });
                            
                            // Reset body
                            document.body.classList.remove('modal-open');
                            document.body.style.overflow = '';
                            document.body.style.paddingRight = '';
                        }
                    }, 300);
                }
            });
        });

        /**
         * Advanced modal closing function that properly manages focus and accessibility
         * @param {string} modalId - The ID of the modal element
         */
        function closeModalProperly(modalId) {
            try {
                // Get the modal element
                const modalEl = document.getElementById(modalId);
                if (!modalEl) return;
                
                // Important: First remove aria-hidden to prevent accessibility violations
                modalEl.removeAttribute('aria-hidden');
                
                // First, find any focused element inside the modal and blur it
                const focusedElement = modalEl.querySelector(':focus');
                if (focusedElement) {
                    focusedElement.blur();
                }
                
                // Move focus to the body before closing
                document.body.focus();
                
                // Get the Bootstrap modal instance
                const modalInstance = bootstrap.Modal.getInstance(modalEl);
                
                if (modalInstance) {
                    // Before hiding, set up a onetime cleanup handler
                    const cleanupModal = function(e) {
                        try {
                            // Remove focus from any remaining elements
                            const modalButtons = modalEl.querySelectorAll('button');
                            modalButtons.forEach(btn => btn.blur());
                            
                            // Remove all backdrop elements with a small delay to ensure they're all caught
                            setTimeout(() => {
                                document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                                    backdrop.remove();
                                });
                                
                                // Reset body classes and styles
                                document.body.classList.remove('modal-open');
                                document.body.style.overflow = '';
                                document.body.style.paddingRight = '';
                                document.body.classList.remove('modal-backdrop-open');
                                
                                // Remove aria-hidden or set it to false on the modal
                                modalEl.removeAttribute('aria-hidden');
                                
                                // Set focus to document.body
                                document.body.focus();
                            }, 50);
                            
                            // Remove this event listener
                            modalEl.removeEventListener('hidden.bs.modal', cleanupModal);
                        } catch (err) {
                            console.error('Error in modal cleanup:', err);
                        }
                    };
                    
                    // Listen for the hidden event
                    modalEl.addEventListener('hidden.bs.modal', cleanupModal);
                    
                    // Add an immediate cleanup before hide() to ensure no focus issues occur
                    modalEl.querySelectorAll('button').forEach(btn => btn.blur());
                    
                    // Hide the modal
                    modalInstance.hide();
                    
                    // Final fallback cleanup - ensure modal backdrop is removed
                    setTimeout(() => {
                        if (!document.querySelector('.modal.show') && 
                            (document.querySelector('.modal-backdrop') || 
                            document.body.classList.contains('modal-open'))) {
                            
                            document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                                backdrop.remove();
                            });
                            
                            document.body.classList.remove('modal-open');
                            document.body.style.overflow = '';
                            document.body.style.paddingRight = '';
                        }
                    }, 500);
                } else {
                    // Direct cleanup if no modal instance
                    document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                        backdrop.remove();
                    });
                    document.body.classList.remove('modal-open');
                    document.body.style.overflow = '';
                    document.body.style.paddingRight = '';
                    
                    // Reset any aria-hidden attributes on the modal
                    modalEl.removeAttribute('aria-hidden');
                }
            } catch (error) {
                // Final fallback - emergency cleanup on any error
                console.error('Error closing modal:', error);
                document.querySelectorAll('.modal-backdrop').forEach(backdrop => backdrop.remove());
                document.body.classList.remove('modal-open');
                document.body.style.overflow = '';
                document.body.style.paddingRight = '';
                
                const modalEl = document.getElementById(modalId);
                if (modalEl) {
                    modalEl.removeAttribute('aria-hidden');
                    modalEl.style.display = 'none';
                }
            }
        }

    </script>
    <script>
        // Fix for close button (X) focus issue
        document.addEventListener('DOMContentLoaded', function() {
            // Target all modal close buttons (X buttons)
            const closeButtons = document.querySelectorAll('.modal .btn-close');
            
            closeButtons.forEach(button => {
                // Replace existing click handlers by cloning and replacing
                const newButton = button.cloneNode(true);
                button.parentNode.replaceChild(newButton, button);
                
                // Add our custom handler with proper focus management
                newButton.addEventListener('click', function(e) {
                    // Get the parent modal
                    const modal = this.closest('.modal');
                    
                    // Immediately blur the button and move focus away
                    this.blur();
                    document.body.focus();
                    
                    // Remove aria-hidden attribute from modal
                    modal.removeAttribute('aria-hidden');
                    
                    // Get modal instance and properly close it
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        // Set up a one-time cleanup handler
                        const cleanupHandler = function() {
                            // Remove all modal backdrops
                            document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                                backdrop.remove();
                            });
                            
                            // Clean up body classes
                            document.body.classList.remove('modal-open');
                            document.body.style.overflow = '';
                            document.body.style.paddingRight = '';
                            document.body.classList.remove('modal-backdrop-open');
                            
                            // Remove event listener
                            modal.removeEventListener('hidden.bs.modal', cleanupHandler);
                        };
                        
                        // Add listener for when the modal is fully hidden
                        modal.addEventListener('hidden.bs.modal', cleanupHandler);
                        
                        // Close the modal
                        modalInstance.hide();
                        
                        // Ensure backdrops are cleaned up if something goes wrong
                        setTimeout(() => {
                            if (!document.querySelector('.modal.show') && 
                                (document.querySelector('.modal-backdrop') || 
                                document.body.classList.contains('modal-open'))) {
                                
                                cleanupHandler();
                            }
                        }, 500);
                    }
                });
            });
        });
    </script>
    <script>
        // Fix for all modal dismiss buttons - add this at the bottom of both emergency pages
        document.addEventListener('DOMContentLoaded', function() {
            // Handle all buttons that dismiss modals (close buttons and secondary buttons)
            const dismissButtons = document.querySelectorAll('.modal .btn-close, .modal .btn-secondary[data-bs-dismiss="modal"]');
            
            dismissButtons.forEach(button => {
                // Clone the button to remove existing event listeners
                const newButton = button.cloneNode(true);
                if (button.parentNode) {
                    button.parentNode.replaceChild(newButton, button);
                }
                
                // Add our custom handler
                newButton.addEventListener('click', function(e) {
                    e.preventDefault(); // Prevent default behavior
                    
                    // Move focus away from button immediately
                    this.blur();
                    document.body.focus();
                    
                    // Get the parent modal
                    const modal = this.closest('.modal');
                    if (!modal) return;
                    
                    // Remove the aria-hidden attribute to avoid accessibility issues
                    modal.removeAttribute('aria-hidden');
                    
                    // Get the Bootstrap modal instance
                    const modalInstance = bootstrap.Modal.getInstance(modal);
                    if (modalInstance) {
                        // Set up cleanup handler before hide is called
                        const cleanupHandler = function() {
                            // Remove all backdrops
                            document.querySelectorAll('.modal-backdrop').forEach(backdrop => {
                                backdrop.remove();
                            });
                            
                            // Clean up body classes
                            document.body.classList.remove('modal-open');
                            document.body.style.overflow = '';
                            document.body.style.paddingRight = '';
                            
                            // Remove this event listener
                            modal.removeEventListener('hidden.bs.modal', cleanupHandler);
                        };
                        
                        // Listen for hidden event
                        modal.addEventListener('hidden.bs.modal', cleanupHandler);
                        
                        // Hide the modal
                        modalInstance.hide();
                        
                        // Safety timeout in case the hidden event doesn't fire
                        setTimeout(() => {
                            if (!document.querySelector('.modal.show') && 
                                (document.querySelector('.modal-backdrop') || 
                                document.body.classList.contains('modal-open'))) {
                                cleanupHandler();
                            }
                        }, 500);
                    }
                });
            });
        });
    </script>
    
</body>
</html>