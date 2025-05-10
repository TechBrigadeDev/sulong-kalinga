<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency & Request History</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
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

        /* Medium screens (769px to 991px) - Time range and custom range in one line */
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

        /* Small screens (768px and below) - Time range covers full width */
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

        /* Tab adjustments for medium screens */
        @media (min-width: 769px) and (max-width: 1024px) {
            .nav-tabs .nav-link {
                font-size: clamp(0.7rem, 1.2vw, 0.9rem);
                padding: 8px clamp(0.4rem, 1.2vw, 0.8rem);
            }
        }
    </style>
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')

    <div class="home-section">
        <div class="page-header">
            <div class="text-left">EMERGENCY AND SERVICE REQUEST HISTORY</div>
            <button class="history-btn active" onclick="window.location.href='{{ route('admin.emergency.request.index') }}'">
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
                                    <select id="timeRange" class="form-select form-select-sm time-range-selector">
                                        <option value="7">Last 7 days</option>
                                        <option value="30" selected>Last 30 days</option>
                                        <option value="90">Last 90 days</option>
                                        <option value="custom">Custom Range</option>
                                    </select>
                                </div>
                                
                                <div id="customRange" class="filter-group">
                                    <input type="date" class="form-control form-control-sm">
                                    <span>to</span>
                                    <input type="date" class="form-control form-control-sm">
                                </div>
                            </div>
                            
                            <div class="filter-actions">
                                <button class="btn btn-sm btn-primary">
                                    <i class="bi bi-filter"></i> Apply
                                </button>
                                <button class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-download"></i> Export
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
                        <div class="tab-pane fade show active" id="emergency-history" role="tabpanel">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Resolved: High Blood Pressure Crisis - Lola Maria</h5>
                                    <p class="card-text">BP spike to 180/110, given emergency medication. Stabilized at 140/90 after 1 hour.</p>
                                    <small class="text-muted">Resolved 2 days ago by Nurse Juan</small>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Resolved: Food Poisoning Incident</h5>
                                    <p class="card-text">3 elders from Barangay 7 experienced vomiting after community meal. Treated with hydration and monitoring.</p>
                                    <small class="text-muted">Resolved 1 week ago by Dr. Santos</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="tab-pane fade" id="request-history" role="tabpanel">
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Completed: Home Care for Lolo Ramon</h5>
                                    <p class="card-text">2-week daily hygiene assistance completed. Family caregiver has returned.</p>
                                    <small class="text-muted">Completed 3 days ago by Care Worker Ana</small>
                                </div>
                            </div>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5 class="card-title">Completed: Health Center Transport</h5>
                                    <p class="card-text">Lola Sofia transported for diabetes check-up. Received new medication regimen.</p>
                                    <small class="text-muted">Completed 1 week ago by Driver Miguel</small>
                                </div>
                            </div>
                        </div>
                        
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
                                                <small class="text-muted">Last 30 days</small>
                                            </div>
                                            
                                            <div class="row text-center mb-4">
                                                <div class="col-4">
                                                    <div class="h3 text-danger">42</div>
                                                    <small class="text-muted">Total</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="h3">32</div>
                                                    <small class="text-muted">Resolved</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="h3">10</div>
                                                    <small class="text-muted">Pending</small>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <h6 class="fw-bold">Breakdown by Type:</h6>
                                                <div class="list-group list-group-flush">
                                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                                        Medical Emergencies
                                                        <span class="badge bg-danger rounded-pill">28</span>
                                                    </div>
                                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                                        Fall Incidents
                                                        <span class="badge bg-danger rounded-pill">9</span>
                                                    </div>
                                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                                        Medication Issues
                                                        <span class="badge bg-danger rounded-pill">5</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-3">
                                                <h6 class="fw-bold">Response Times:</h6>
                                                <div class="d-flex justify-content-between">
                                                    <small class="text-muted">Average</small>
                                                    <small class="fw-bold">2h 15m</small>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <small class="text-muted">Fastest</small>
                                                    <small class="fw-bold">35m</small>
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
                                                <small class="text-muted">Last 30 days</small>
                                            </div>
                                            
                                            <div class="row text-center mb-4">
                                                <div class="col-4">
                                                    <div class="h3 text-primary">98</div>
                                                    <small class="text-muted">Total</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="h3">85</div>
                                                    <small class="text-muted">Completed</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="h3">13</div>
                                                    <small class="text-muted">Pending</small>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <h6 class="fw-bold">Breakdown by Type:</h6>
                                                <div class="list-group list-group-flush">
                                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                                        Home Care Visits
                                                        <span class="badge bg-primary rounded-pill">45</span>
                                                    </div>
                                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                                        Transportation
                                                        <span class="badge bg-primary rounded-pill">30</span>
                                                    </div>
                                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                                        Workshops
                                                        <span class="badge bg-primary rounded-pill">15</span>
                                                    </div>
                                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                                        Meal Deliveries
                                                        <span class="badge bg-primary rounded-pill">8</span>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mt-3">
                                                <h6 class="fw-bold">Completion Times:</h6>
                                                <div class="d-flex justify-content-between">
                                                    <small class="text-muted">Average</small>
                                                    <small class="fw-bold">3d 2h</small>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <small class="text-muted">Fastest</small>
                                                    <small class="fw-bold">1d 5h</small>
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
   
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
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

        // Initialize Bootstrap tabs
        var tabEls = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabEls.forEach(function(tabEl) {
            new bootstrap.Tab(tabEl);
        });
    </script>
</body>
</html>