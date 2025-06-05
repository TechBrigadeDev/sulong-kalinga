<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ Auth::guard('beneficiary')->check() ? 'Beneficiary' : 'Family' }} Portal - Emergency & Service</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/emergencyAndService.css') }}">
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
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="recurring" name="is_recurring">
                                                <label class="form-check-label" for="recurring">Recurring service</label>
                                            </div>
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
                                                <tr>
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
                                                            <button class="btn btn-sm btn-outline-danger cancel-request" 
                                                                    data-request-id="{{ $emergency->notice_id }}" 
                                                                    data-request-type="emergency">
                                                                Cancel
                                                            </button>
                                                        @else
                                                            <span class="text-muted">Processing</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                            
                                            @foreach($activeServiceRequests as $service)
                                                <tr>
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
                                                            <button class="btn btn-sm btn-outline-danger cancel-request" 
                                                                    data-request-id="{{ $service->service_request_id }}" 
                                                                    data-request-type="service">
                                                                Cancel
                                                            </button>
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
                                    <div class="status-card status-emergency p-3 mb-3">
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
                                    <div class="status-card status-service p-3 mb-3">
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
                <div class="modal-header">
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
            $('.cancel-request').on('click', function() {
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
                    // Hide confirmation modal
                    bootstrap.Modal.getInstance(document.getElementById('confirmationModal')).hide();
                    
                    // Show success alert
                    alert('Request cancelled successfully.');
                    
                    // Refresh active requests and history
                    refreshActiveRequests();
                    refreshRequestHistory();
                },
                error: function(xhr) {
                    let errorMsg = 'Failed to cancel request.';
                    if (xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    
                    // Hide confirmation modal
                    bootstrap.Modal.getInstance(document.getElementById('confirmationModal')).hide();
                    
                    // Show error alert
                    alert('Error: ' + errorMsg);
                }
            });
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
                                <tr>
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
                                            `<button class="btn btn-sm btn-outline-danger cancel-request" 
                                                data-request-id="${emergency.notice_id}" 
                                                data-request-type="emergency">
                                                Cancel
                                            </button>` : 
                                            `<span class="text-muted">Processing</span>`}
                                    </td>
                                </tr>
                            `;
                        });
                        
                        // Add service requests
                        response.serviceRequests.forEach(function(service) {
                            tableContent += `
                                <tr>
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
                                            `<button class="btn btn-sm btn-outline-danger cancel-request" 
                                                data-request-id="${service.service_request_id}" 
                                                data-request-type="service">
                                                Cancel
                                            </button>` : 
                                            `<span class="text-muted">Processing</span>`}
                                    </td>
                                </tr>
                            `;
                        });
                    }
                    
                    // Update the table
                    $('#activeRequestsTable tbody').html(tableContent);
                    
                    // Re-attach event handlers
                    attachCancelEventHandlers();
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
    </script>
</body>
</html>