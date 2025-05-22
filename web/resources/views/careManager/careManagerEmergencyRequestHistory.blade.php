<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency & Request History</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        :root {
            --base-font-size: clamp(0.875rem, 2.5vw, 1rem);
            --heading-font-size: clamp(1.25rem, 3.5vw, 1.5rem);
            --section-title-size: clamp(1rem, 2.8vw, 1.25rem);
            --card-title-size: clamp(1rem, 2.5vw, 1.125rem);
            --small-text-size: clamp(0.75rem, 2vw, 0.875rem);
            --tab-font-size: clamp(0.75rem, 1.5vw, 1rem);
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
            flex-wrap: nowrap;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }
        .nav-tabs .nav-link {
            font-size: var(--tab-font-size);
            padding: 10px clamp(0.5rem, 1.5vw, 1rem);
            color: #495057;
            border: none;
            margin: 0 2px;
            border-radius: 5px 5px 0 0;
            transition: all 0.3s;
            white-space: nowrap;
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

        /* Updated Time range filter styles */
        .time-range-container {
            background-color: #f8f9fa;
            padding: 1rem;
            border-radius: 0.5rem;
        }

        .filter-row {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: 1rem;
        }

        .filter-main-group {
            display: flex;
            align-items: center;
            flex: 1;
            gap: 1rem;
        }

        .filter-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .filter-group label {
            margin-bottom: 0;
            font-weight: 500;
            white-space: nowrap;
        }

        #customRange {
            display: none;
            align-items: center;
            gap: 0.5rem;
            flex: 1;
        }

        .filter-actions {
            display: flex;
            gap: 0.5rem;
        }

        .time-range-selector {
            min-width: 150px;
        }

        /* Statistics Card Styles */
        .stat-card {
            border-radius: 0.5rem;
            margin-bottom: 1rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0,0,0,0.075);
        }
        .stat-header {
            border-bottom: 1px solid #dee2e6;
            padding-bottom: 0.75rem;
            margin-bottom: 1rem;
        }
        .stat-row {
            display: flex;
            justify-content: space-between;
            padding: 0.5rem 0;
            border-bottom: 1px solid #f8f9fa;
        }
        .stat-row:last-child {
            border-bottom: none;
        }
        .stat-total {
            font-weight: 600;
            font-size: 1.1rem;
        }
        .stat-label {
            color: #6c757d;
        }
        .stat-value {
            font-weight: 600;
        }
        .emergency-stat .stat-total { color: #dc3545; }
        .request-stat .stat-total { color: #0d6efd; }
        .breakdown-header {
            font-weight: 500;
            margin-top: 1rem;
            color: #495057;
        }
        .breakdown-item {
            padding-left: 1rem;
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

        /* Responsive adjustments */
        /* Large screens (1024px and above) - All filter elements in one line */
        @media (min-width: 1024px) {
            .filter-row {
                flex-wrap: nowrap;
            }
            #customRange {
                display: flex;
                max-width: 400px;
            }
            .filter-actions {
                margin-left: auto;
            }
        }

        /* Responsive adjustments */
        @media (min-width: 1024px) {
            .filter-row {
                flex-wrap: nowrap;
            }
            #customRange {
                display: flex;
                max-width: 400px;
            }
            .filter-actions {
                margin-left: auto;
            }
        }

        @media (min-width: 769px) and (max-width: 1023.98px) {
            .filter-main-group {
                flex: 1 0 70%;
                flex-wrap: nowrap;
            }
            #customRange {
                display: flex;
                max-width: 350px;
            }
            .filter-actions {
                flex: 1 0 30%;
                justify-content: flex-end;
            }
        }

        @media (max-width: 768px) {
            .filter-row {
                flex-direction: column;
                gap: 0.75rem;
            }
            .filter-main-group {
                width: 100%;
                flex-direction: column;
                gap: 0.75rem;
            }
            .filter-group {
                width: 100%;
            }
            .time-range-selector {
                width: 100%;
            }
            #customRange {
                width: 100%;
                display: flex;
                flex-wrap: wrap;
            }
            #customRange input {
                flex: 1;
                min-width: 120px;
            }
            .filter-actions {
                width: 100%;
                justify-content: center;
            }
            .nav-tabs .nav-link {
                padding: 8px 12px;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .nav-tabs .nav-link {
                font-size: clamp(0.7rem, 1.2vw, 0.9rem);
                padding: 8px clamp(0.4rem, 1.2vw, 0.8rem);
            }
        }
    </style>
</head>
<body>

    @include('components.careManagerNavbar')
    @include('components.careManagerSidebar')

    <div class="home-section">
        <div class="page-header">
            <div class="text-left">EMERGENCY AND SERVICE REQUEST HISTORY</div>
            <button class="history-btn active" onclick="window.location.href='{{ route('care-manager.emergency.request.index') }}'">
                <i class="bi bi-arrow-left me-1"></i> Back to Current
            </button>
        </div>
        
        <div class="container-fluid">
            <div class="row" id="home-content">
                <div class="col-12">
                    <!-- Time Range Filter -->
                    <div class="time-range-container">
                        <div class="filter-row">
                            <div class="filter-main-group">
                                <div class="filter-group">
                                    <label for="timeRange">Time Range:</label>
                                    <select class="form-select form-select-sm time-range-selector" id="timeRange" name="time_range">
                                        <option value="30days" selected>Last 30 Days</option>
                                        <option value="7days">Last 7 Days</option>
                                        <option value="90days">Last 90 Days</option>
                                        <option value="custom">Custom Range</option>
                                    </select>
                                </div>
                                
                                <div id="customRange" class="filter-group">
                                    <label for="startDate">From:</label>
                                    <input type="date" class="form-control form-control-sm" id="startDate" name="start_date">
                                    
                                    <label for="endDate">To:</label>
                                    <input type="date" class="form-control form-control-sm" id="endDate" name="end_date">
                                </div>
                            </div>
                            
                            <div class="filter-actions">
                                <button class="btn btn-sm btn-primary" id="applyFilter">
                                    <i class="bi bi-funnel-fill me-1"></i>Apply Filter
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" id="resetFilter">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                                </button>
                            </div>
                        </div>
                    </div>

                    <ul class="nav nav-tabs justify-content-center" id="historyTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="emergency-history-tab" data-bs-toggle="tab" data-bs-target="#emergency-history" type="button" role="tab">
                                <i class="bi bi-exclamation-triangle me-1"></i>Emergency History
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="request-history-tab" data-bs-toggle="tab" data-bs-target="#request-history" type="button" role="tab">
                                <i class="bi bi-hand-thumbs-up me-1"></i>Service Request History
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="statistics-tab" data-bs-toggle="tab" data-bs-target="#statistics" type="button" role="tab">
                                <i class="bi bi-pie-chart me-1"></i>Statistics
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="historyTabContent">
                        <!-- Emergency History Tab -->
                        <div class="tab-pane fade show active" id="emergency-history" role="tabpanel">
                            @forelse($resolvedEmergencies ?? [] as $emergency)
                                <div class="card emergency-card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title">Resolved: {{ $emergency->emergencyType->name }}</h5>
                                            <span class="badge bg-success bg-opacity-10 text-success">Resolved</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">Beneficiary:</span>
                                            <span class="ms-2">{{ $emergency->beneficiary->first_name }} {{ $emergency->beneficiary->last_name }}</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">Address:</span>
                                            <span class="ms-2">{{ $emergency->beneficiary->street_address }} ({{ $emergency->beneficiary->barangay->barangay_name }}, {{ $emergency->beneficiary->municipality->municipality_name }})</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">Contact:</span>
                                            <span class="ms-2">{{ $emergency->beneficiary->mobile }}</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">Message:</span>
                                            <span class="ms-2">{{ \Illuminate\Support\Str::limit($emergency->message, 100) }}</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i> Resolved {{ \Carbon\Carbon::parse($emergency->updated_at)->diffForHumans() }} 
                                                @if($emergency->actionTakenBy)
                                                    by {{ $emergency->actionTakenBy->first_name }} {{ $emergency->actionTakenBy->last_name }}
                                                @endif
                                            </small>
                                            <button class="btn btn-sm btn-outline-secondary" onclick="viewEmergencyHistory({{ $emergency->notice_id }})">
                                                <i class="bi bi-eye me-1"></i> View Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <i class="bi bi-archive text-muted" style="font-size: 2rem;"></i>
                                    <h6 class="mt-3">No Resolved Emergencies</h6>
                                    <p class="small">No emergency notices have been resolved in the selected time period.</p>
                                </div>
                            @endforelse
                        </div>
                        
                        <!-- Service Request History Tab -->
                        <div class="tab-pane fade" id="request-history" role="tabpanel">
                            @forelse($completedServiceRequests ?? [] as $request)
                                <div class="card request-card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title">{{ ucfirst($request->status) }}: {{ $request->serviceType->name }}</h5>
                                            <span class="badge {{ $request->status == 'completed' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">Beneficiary:</span>
                                            <span class="ms-2">{{ $request->beneficiary->first_name }} {{ $request->beneficiary->last_name }}</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">Address:</span>
                                            <span class="ms-2">{{ $request->beneficiary->street_address }} ({{ $request->beneficiary->barangay->barangay_name }}, {{ $request->beneficiary->municipality->municipality_name }})</span>
                                        </div>
                                        
                                        @if($request->status == 'completed' && $request->careWorker)
                                            <div class="d-flex flex-wrap mb-1">
                                                <span class="info-label">Care Worker:</span>
                                                <span class="ms-2">{{ $request->careWorker->first_name }} {{ $request->careWorker->last_name }}</span>
                                            </div>
                                        @endif
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">Service Date:</span>
                                            <span class="ms-2">{{ \Carbon\Carbon::parse($request->service_date)->format('M d, Y') }}</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i> {{ ucfirst($request->status) }} {{ \Carbon\Carbon::parse($request->updated_at)->diffForHumans() }} 
                                                @if($request->actionTakenBy)
                                                    by {{ $request->actionTakenBy->first_name }} {{ $request->actionTakenBy->last_name }}
                                                @endif
                                            </small>
                                            <button class="btn btn-sm btn-outline-secondary" onclick="viewServiceRequestHistory({{ $request->service_request_id }})">
                                                <i class="bi bi-eye me-1"></i> View Details
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <i class="bi bi-archive text-muted" style="font-size: 2rem;"></i>
                                    <h6 class="mt-3">No Completed/Rejected Service Requests</h6>
                                    <p class="small">No service requests have been completed or rejected in the selected time period.</p>
                                </div>
                            @endforelse
                        </div>
                        
                        <!-- Statistics Tab -->
                        <div class="tab-pane fade" id="statistics" role="tabpanel">
                            <div class="row">
                                <!-- Emergency Statistics -->
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="card-title mb-0">
                                                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                                                    Emergency Statistics
                                                </h5>
                                                <small class="text-muted" id="emergencyStatsDateRange">Last 30 days</small>
                                            </div>
                                            
                                            <div class="row text-center mb-4">
                                                <div class="col-4">
                                                    <div class="h3 text-danger" id="totalEmergencies">{{ ($resolvedEmergencies ?? collect())->count() + ($emergencyNotices->where('status', 'new')->count() ?? 0) + ($emergencyNotices->where('status', 'in_progress')->count() ?? 0) }}</div>
                                                    <small class="text-muted">Total</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="h3" id="resolvedEmergencies">{{ ($resolvedEmergencies ?? collect())->count() }}</div>
                                                    <small class="text-muted">Resolved</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="h3" id="pendingEmergencies">{{ ($emergencyNotices->where('status', 'new')->count() ?? 0) + ($emergencyNotices->where('status', 'in_progress')->count() ?? 0) }}</div>
                                                    <small class="text-muted">Pending</small>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <h6 class="fw-bold">Breakdown by Type:</h6>
                                                <div class="list-group list-group-flush" id="emergencyTypeBreakdown">
                                                    @foreach($emergencyTypeStats ?? [] as $stat)
                                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                                            {{ $stat['name'] }}
                                                            <span class="badge bg-danger rounded-pill">{{ $stat['count'] }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Service Request Statistics -->
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="card-title mb-0">
                                                    <i class="bi bi-hand-thumbs-up text-primary me-2"></i>
                                                    Service Request Statistics
                                                </h5>
                                                <small class="text-muted" id="serviceStatsDateRange">Last 30 days</small>
                                            </div>
                                            
                                            <div class="row text-center mb-4">
                                                <div class="col-4">
                                                    <div class="h3 text-primary" id="totalServiceRequests">{{ ($completedServiceRequests ?? collect())->count() + ($serviceRequests->where('status', 'new')->count() ?? 0) + ($serviceRequests->where('status', 'approved')->count() ?? 0) }}</div>
                                                    <small class="text-muted">Total</small>
                                                </div>
                                                <div class="col-4">
                                                    <!-- Change from just "completed" to showing both completed and rejected combined -->
                                                    <div class="h3" id="completedServiceRequests">{{ ($completedServiceRequests ?? collect())->count() }}</div>
                                                    <small class="text-muted">Completed / Rejected</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="h3" id="pendingServiceRequests">{{ ($serviceRequests->where('status', 'new')->count() ?? 0) + ($serviceRequests->where('status', 'approved')->count() ?? 0) }}</div>
                                                    <small class="text-muted">Pending</small>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <h6 class="fw-bold">Breakdown by Type:</h6>
                                                <div class="list-group list-group-flush" id="serviceTypeBreakdown">
                                                    @foreach($serviceTypeStats ?? [] as $stat)
                                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                                            {{ $stat['name'] }}
                                                            <span class="badge bg-primary rounded-pill">{{ $stat['count'] }}</span>
                                                        </div>
                                                    @endforeach
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
    </div>

    <!-- Emergency History Details Modal -->
    <div class="modal fade" id="emergencyHistoryDetailModal" tabindex="-1" aria-labelledby="emergencyHistoryDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="emergencyHistoryDetailModalLabel"><i class="bi bi-file-earmark-text"></i> Emergency History Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="emergencyHistoryContent">
                        <!-- Emergency history details will be loaded here -->
                    </div>
                    
                    <div class="updates-history mt-4">
                        <h6 class="border-bottom pb-2">Updates History</h6>
                        <div id="emergencyHistoryTimeline">
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

    <!-- Service Request History Details Modal -->
    <div class="modal fade" id="serviceHistoryDetailModal" tabindex="-1" aria-labelledby="serviceHistoryDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="serviceHistoryDetailModalLabel"><i class="bi bi-file-earmark-text"></i> Service Request History Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="serviceHistoryContent">
                        <!-- Service request history details will be loaded here -->
                    </div>
                    
                    <div class="updates-history mt-4">
                        <h6 class="border-bottom pb-2">Updates History</h6>
                        <div id="serviceHistoryTimeline">
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
   
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <script>
        // Time range filter functionality
        document.getElementById('timeRange').addEventListener('change', function() {
            const customRange = document.getElementById('customRange');
            if (this.value === 'custom') {
                customRange.style.display = 'flex';
            } else {
                customRange.style.display = 'none';
            }
        });

        // Apply filter button
        $('#applyFilter').on('click', function() {
            const timeRange = $('#timeRange').val();
            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();
            
            // Validation for custom date range
            if (timeRange === 'custom') {
                if (!startDate || !endDate) {
                    toastr.error('Please select both start and end dates');
                    return;
                }
                
                if (new Date(startDate) > new Date(endDate)) {
                    toastr.error('Start date cannot be after end date');
                    return;
                }
            }
            
            // Show loading state
            $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...');
            $(this).prop('disabled', true);
            
            // Send filter request
            $.ajax({
                url: "{{ route('care-manager.emergency.request.filter.history') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    time_range: timeRange,
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    if (response.success) {
                        // Update date range labels
                        $('#emergencyStatsDateRange').text(response.dateRangeLabel);
                        $('#serviceStatsDateRange').text(response.dateRangeLabel);
                        
                        // Update emergency history
                        if (response.resolvedEmergencies.length === 0) {
                            $('#emergency-history').html(`
                                <div class="empty-state">
                                    <i class="bi bi-archive text-muted" style="font-size: 2rem;"></i>
                                    <h6 class="mt-3">No Resolved Emergencies</h6>
                                    <p class="small">No emergency notices have been resolved in the selected time period.</p>
                                </div>
                            `);
                        } else {
                            let emergencyHtml = '';
                            response.resolvedEmergencies.forEach(emergency => {
                                emergencyHtml += renderEmergencyHistoryCard(emergency);
                            });
                            $('#emergency-history').html(emergencyHtml);
                        }
                        
                        // Update service request history
                        if (response.completedServiceRequests.length === 0) {
                            $('#request-history').html(`
                                <div class="empty-state">
                                    <i class="bi bi-archive text-muted" style="font-size: 2rem;"></i>
                                    <h6 class="mt-3">No Completed/Rejected Service Requests</h6>
                                    <p class="small">No service requests have been completed or rejected in the selected time period.</p>
                                </div>
                            `);
                        } else {
                            let serviceHtml = '';
                            response.completedServiceRequests.forEach(request => {
                                serviceHtml += renderServiceHistoryCard(request);
                            });
                            $('#request-history').html(serviceHtml);
                        }
                        
                        // Update statistics
                        updateEmergencyStatistics(response.emergencyStats);
                        updateServiceStatistics(response.serviceStats);
                        
                        toastr.success('Date range filter applied successfully');
                    } else {
                        toastr.error('Failed to apply filter');
                    }
                },
                error: function(xhr) {
                    let errorMessage = 'Failed to apply filter';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        const firstError = Object.values(errors)[0];
                        if (firstError && firstError[0]) {
                            errorMessage = firstError[0];
                        }
                    }
                    toastr.error(errorMessage);
                },
                complete: function() {
                    $('#applyFilter').html('<i class="bi bi-funnel-fill me-1"></i>Apply Filter');
                    $('#applyFilter').prop('disabled', false);
                }
            });
        });
        
        // Reset filter
        $('#resetFilter').on('click', function() {
            // Set default values first
            $('#timeRange').val('30days');
            
            // Set date values with proper formatting
            const today = new Date();
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
            
            // Format dates as YYYY-MM-DD
            const formatDate = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };
            
            $('#endDate').val(formatDate(today));
            $('#startDate').val(formatDate(thirtyDaysAgo));
            
            // Hide custom range
            $('#customRange').hide();
            
            // Then click apply filter
            $('#applyFilter').click();
        });
        
        // Initialize with current date in end date field for custom range
        document.getElementById('endDate').valueAsDate = new Date();
        
        // Set start date 30 days ago for custom range by default
        const thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
        document.getElementById('startDate').valueAsDate = thirtyDaysAgo;

        // View emergency history details
        function viewEmergencyHistory(noticeId) {
            $('#emergencyHistoryContent').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading details...</p></div>');
            $('#emergencyHistoryDetailModal').modal('show');
            
            $.ajax({
                url: "{{ route('care-manager.emergency.request.get.emergency', '') }}/" + noticeId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        renderEmergencyHistoryDetails(response.emergency_notice);
                    } else {
                        $('#emergencyHistoryContent').html(`<div class="alert alert-danger">Error loading details: ${response.message}</div>`);
                    }
                },
                error: function() {
                    $('#emergencyHistoryContent').html('<div class="alert alert-danger">Failed to load emergency details. Please try again.</div>');
                }
            });
        }
        
        // View service request history details
        function viewServiceRequestHistory(requestId) {
            $('#serviceHistoryContent').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">Loading details...</p></div>');
            $('#serviceHistoryDetailModal').modal('show');
            
            $.ajax({
                url: "{{ route('care-manager.emergency.request.get.service', '') }}/" + requestId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        renderServiceHistoryDetails(response.service_request);
                    } else {
                        $('#serviceHistoryContent').html(`<div class="alert alert-danger">Error loading details: ${response.message}</div>`);
                    }
                },
                error: function() {
                    $('#serviceHistoryContent').html('<div class="alert alert-danger">Failed to load service request details. Please try again.</div>');
                }
            });
        }
        
        // Render emergency history details
        function renderEmergencyHistoryDetails(emergency) {
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
                        <div class="col-md-4 fw-bold">Resolved:</div>
                        <div class="col-md-8">${formatDateTime(emergency.updated_at)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Message:</div>
                        <div class="col-md-8">${emergency.message}</div>
                    </div>
                </div>
            `;
            
            $('#emergencyHistoryContent').html(content);
            
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
                
                $('#emergencyHistoryTimeline').html(updatesHtml);
            } else {
                $('#emergencyHistoryTimeline').html('<p class="text-muted">No updates available</p>');
            }
        }
        
        // Render service request history details
        function renderServiceHistoryDetails(request) {
            let content = `
                <div class="mb-4">
                    <h5 class="border-bottom pb-2">Service Request Information</h5>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Beneficiary:</div>
                        <div class="col-md-8">${request.beneficiary ? request.beneficiary.first_name + ' ' + request.beneficiary.last_name : 'Unknown'}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Address:</div>
                        <div class="col-md-8">${(request.beneficiary && request.beneficiary.street_address) || 'Unknown'} 
                            ${request.beneficiary && request.beneficiary.barangay && request.beneficiary.municipality ? 
                                `(${request.beneficiary.barangay.barangay_name || 'Unknown'}, ${request.beneficiary.municipality.municipality_name || 'Unknown'})` : 
                                ''}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Service Type:</div>
                        <div class="col-md-8"><span class="badge" style="background-color: ${request.service_type ? request.service_type.color_code : '#6c757d'}">${request.service_type ? request.service_type.name : 'Unknown'}</span></div>
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
                        <div class="col-md-4 fw-bold">Completed/Rejected:</div>
                        <div class="col-md-8">${formatDateTime(request.updated_at)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Message:</div>
                        <div class="col-md-8">${request.message || 'No message provided'}</div>
                    </div>
                </div>
            `;
            
            $('#serviceHistoryContent').html(content);

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
                
                $('#serviceHistoryTimeline').html(updatesHtml);
            } else {
                $('#serviceHistoryTimeline').html('<p class="text-muted">No updates available</p>');
            }
        }
        
        // Helper function to render emergency history card
        function renderEmergencyHistoryCard(emergency) {
            return `
                <div class="card emergency-card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title">Resolved: ${emergency.emergency_type ? emergency.emergency_type.name : 'Unknown'}</h5>
                            <span class="badge bg-success bg-opacity-10 text-success">Resolved</span>
                        </div>
                        
                        <div class="d-flex flex-wrap mb-1">
                            <span class="info-label">Beneficiary:</span>
                            <span>${emergency.beneficiary ? emergency.beneficiary.first_name + ' ' + emergency.beneficiary.last_name : 'Unknown'}</span>
                        </div>
                        
                        <div class="d-flex flex-wrap mb-1">
                            <span class="info-label">Address:</span>
                            <span>
                                ${emergency.beneficiary ? (emergency.beneficiary.street_address || 'Unknown address') : 'Unknown'}
                                ${emergency.beneficiary && emergency.beneficiary.barangay && emergency.beneficiary.municipality ? 
                                    `(${emergency.beneficiary.barangay.barangay_name || ''}, ${emergency.beneficiary.municipality.municipality_name || ''})` : 
                                    ''}
                            </span>
                        </div>
                        
                        <div class="d-flex flex-wrap mb-1">
                            <span class="info-label">Message:</span>
                            <span>${emergency.message ? (emergency.message.length > 100 ? emergency.message.substring(0, 100) + '...' : emergency.message) : 'No message provided'}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i> ${formatTimeAgo(emergency.updated_at)} 
                                ${emergency.action_taken_by ? `by ${emergency.action_taken_by_name || 'Staff'}` : ''}
                            </small>
                            <button class="btn btn-sm btn-outline-secondary" onclick="viewEmergencyHistory(${emergency.notice_id})">
                                <i class="bi bi-eye me-1"></i> View Details
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Helper function to render service history card
       function renderServiceHistoryCard(request) {
            return `
                <div class="card request-card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title">${capitalizeFirst(request.status)}: ${request.service_type ? request.service_type.name : 'Unknown'}</h5>
                            <span class="badge ${request.status == 'completed' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger'}">
                                ${capitalizeFirst(request.status)}
                            </span>
                        </div>
                        
                        <div class="d-flex flex-wrap mb-1">
                            <span class="info-label">Beneficiary:</span>
                            <span>${request.beneficiary ? request.beneficiary.first_name + ' ' + request.beneficiary.last_name : 'Unknown'}</span>
                        </div>
                        
                        <div class="d-flex flex-wrap mb-1">
                            <span class="info-label">Address:</span>
                            <span>
                                ${request.beneficiary ? (request.beneficiary.street_address || 'Unknown address') : 'Unknown'}
                                ${request.beneficiary && request.beneficiary.barangay && request.beneficiary.municipality ? 
                                    `(${request.beneficiary.barangay.barangay_name || ''}, ${request.beneficiary.municipality.municipality_name || ''})` : 
                                    ''}
                            </span>
                        </div>
                        
                        ${request.status == 'completed' && request.care_worker ? `
                        <div class="d-flex flex-wrap mb-1">
                            <span class="info-label">Care Worker:</span>
                            <span>${request.care_worker.first_name} ${request.care_worker.last_name}</span>
                        </div>
                        ` : ''}
                        
                        <div class="d-flex flex-wrap mb-1">
                            <span class="info-label">Service Date:</span>
                            <span>${formatDate(request.service_date)}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i> ${formatTimeAgo(request.updated_at)} 
                                ${request.action_taken_by ? `by ${request.action_taken_by_name || 'Staff'}` : ''}
                            </small>
                            <button class="btn btn-sm btn-outline-secondary" onclick="viewServiceRequestHistory(${request.service_request_id})">
                                <i class="bi bi-eye me-1"></i> View Details
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Update emergency statistics
        function updateEmergencyStatistics(stats) {
            $('#totalEmergencies').text(stats.total);
            $('#resolvedEmergencies').text(stats.resolved);
            $('#pendingEmergencies').text(stats.pending);
            
            
            
            // Add this block to update the emergency type breakdown
            let breakdownHtml = '';
            if (stats.byType && stats.byType.length > 0) {
                stats.byType.forEach(type => {
                    // Handle both color and color_code property names for compatibility
                    const backgroundColor = type.color || type.color_code || '#6c757d';
                    breakdownHtml += `
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="me-2">${type.name}</span>
                            </div>
                            <span class="badge bg-danger rounded-pill">${type.count}</span>
                        </div>
                    `;
                });
            } else {
                breakdownHtml = '<div class="list-group-item text-muted">No data available</div>';
            }
            $('#emergencyTypeBreakdown').html(breakdownHtml);
            
            // Log information for debugging
            console.log('Emergency stats:', stats);
            if (stats.byType) console.log('Emergency byType count:', stats.byType.length);
        }
        
        // Update service request statistics
        function updateServiceStatistics(stats) {
            $('#totalServiceRequests').text(stats.total);
            // Change this line to use completed + rejected:
            $('#completedServiceRequests').text(stats.completed + stats.rejected);
            $('#pendingServiceRequests').text(stats.pending);
            
            // Rest of the function remains unchanged
            let breakdownHtml = '';
            stats.byType.forEach(type => {
                breakdownHtml += `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        ${type.name}
                        <span class="badge bg-primary rounded-pill">${type.count}</span>
                    </div>
                `;
            });
            $('#serviceTypeBreakdown').html(breakdownHtml);
            
        }
        
        // Helper functions
        function formatDateTime(dateTimeStr) {
            if (!dateTimeStr) return 'N/A';
            const date = new Date(dateTimeStr);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
        }
        
        function formatDate(dateStr) {
            if (!dateStr) return 'N/A';
            const date = new Date(dateStr);
            return date.toLocaleDateString();
        }
        
        function formatTimeAgo(dateTimeStr) {
            if (!dateTimeStr) return 'N/A';
            const date = new Date(dateTimeStr);
            const now = new Date();
            const diffMs = now - date;
            const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
            const diffHours = Math.floor((diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            
            if (diffDays > 30) {
                return formatDate(dateTimeStr);
            } else if (diffDays > 0) {
                return `${diffDays} day${diffDays > 1 ? 's' : ''} ago`;
            } else if (diffHours > 0) {
                return `${diffHours} hour${diffHours > 1 ? 's' : ''} ago`;
            } else {
                return 'Less than an hour ago';
            }
        }
        
        function formatStatus(status) {
            switch(status) {
                case 'new': return '<span class="badge bg-danger">New</span>';
                case 'in_progress': return '<span class="badge bg-info">In Progress</span>';
                case 'approved': return '<span class="badge bg-success">Approved</span>';
                case 'rejected': return '<span class="badge bg-danger">Rejected</span>';
                case 'completed': return '<span class="badge bg-primary">Completed</span>';
                case 'resolved': return '<span class="badge bg-success">Resolved</span>';
                default: return `<span class="badge bg-secondary">${status}</span>`;
            }
        }
        
        function getUpdateTypeBadgeClass(updateType) {
            switch(updateType) {
                case 'response': return 'bg-info';
                case 'status_change': return 'bg-warning';
                case 'assignment': return 'bg-primary';
                case 'resolution': return 'bg-success';
                case 'completion': return 'bg-success';
                case 'approval': return 'bg-success';
                case 'rejection': return 'bg-danger';
                case 'note': return 'bg-secondary';
                default: return 'bg-secondary';
            }
        }
        
        function formatUpdateType(updateType) {
            switch(updateType) {
                case 'response': return 'Response';
                case 'status_change': return 'Status Change';
                case 'assignment': return 'Assignment';
                case 'resolution': return 'Resolution';
                case 'completion': return 'Completion';
                case 'approval': return 'Approval';
                case 'rejection': return 'Rejection';
                case 'note': return 'Note';
                default: return updateType;
            }
        }
        
        function capitalizeFirst(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
        
        // Initialize Bootstrap tabs
        var tabEls = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabEls.forEach(function(tabEl) {
            new bootstrap.Tab(tabEl);
        });
    </script>
</body>
</html>