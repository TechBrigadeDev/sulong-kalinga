<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency & Request</title>
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
                                        <div class="card notification-card emergency-card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h5 class="card-title fw-bold mb-0 text-dark">Lola Remedios</h5>
                                                    <span class="badge bg-danger bg-opacity-10 text-danger">New</span>
                                                </div>
                                                
                                                <div class="d-flex flex-wrap mb-1">
                                                    <span class="info-label">Address:</span>
                                                    <span>Mondragon, Barangay 12</span>
                                                </div>
                                                
                                                <div class="d-flex flex-wrap mb-1">
                                                    <span class="info-label">Contact:</span>
                                                    <span>0912-345-6789</span>
                                                </div>
                                                
                                                <div class="d-flex flex-wrap mb-1">
                                                    <span class="info-label">Emergency Contact:</span>
                                                    <span>Maria (Daughter) - 0917-890-1234</span>
                                                </div>
                                                
                                                <div class="d-flex flex-wrap mb-3">
                                                    <span class="info-label">Assigned To:</span>
                                                    <span>Nurse Juan Dela Cruz</span>
                                                </div>
                                                
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="bi bi-clock me-1"></i> 15 min ago
                                                    </small>
                                                    <button class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-arrow-right-circle me-1"></i> Respond
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="card notification-card emergency-card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h5 class="card-title fw-bold mb-0 text-dark">Tatay Juan</h5>
                                                    <span class="badge bg-danger bg-opacity-10 text-danger">New</span>
                                                </div>
                                                
                                                <div class="d-flex flex-wrap mb-1">
                                                    <span class="info-label">Address:</span>
                                                    <span>San Roque, Barangay 5</span>
                                                </div>
                                                
                                                <div class="d-flex flex-wrap mb-1">
                                                    <span class="info-label">Contact:</span>
                                                    <span>0922-333-4444</span>
                                                </div>
                                                
                                                <div class="d-flex flex-wrap mb-1">
                                                    <span class="info-label">Emergency Contact:</span>
                                                    <span>Pedro (Son) - 0918-555-6666</span>
                                                </div>
                                                
                                                <div class="d-flex flex-wrap mb-3">
                                                    <span class="info-label">Assigned To:</span>
                                                    <span>Care Worker Maria Santos</span>
                                                </div>
                                                
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="bi bi-clock me-1"></i> 1 hour ago
                                                    </small>
                                                    <button class="btn btn-sm btn-outline-danger">
                                                        <i class="bi bi-arrow-right-circle me-1"></i> Respond
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Service Request Tab -->
                                    <div class="tab-pane fade" id="service" role="tabpanel">
                                        <div class="card notification-card request-card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h5 class="card-title fw-bold mb-0 text-dark">Lolo Carlos</h5>
                                                    <span class="badge bg-primary bg-opacity-10 text-primary">New</span>
                                                </div>
                                                
                                                <div class="d-flex flex-wrap mb-1">
                                                    <span class="info-label">Request Type:</span>
                                                    <span>Home Care Visit</span>
                                                </div>
                                                
                                                <div class="d-flex flex-wrap mb-1">
                                                    <span class="info-label">Address:</span>
                                                    <span>San Roque, Barangay 8</span>
                                                </div>
                                                
                                                <div class="d-flex flex-wrap mb-3">
                                                    <span class="info-label">Contact:</span>
                                                    <span>0933-444-5555</span>
                                                </div>
                                                
                                                <div class="d-flex flex-wrap mb-3">
                                                    <span class="info-label">Details:</span>
                                                    <span>Requests weekly hygiene assistance. Family caregiver is temporarily unavailable.</span>
                                                </div>
                                                
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="bi bi-clock me-1"></i> 3 hours ago
                                                    </small>
                                                    <div>
                                                        <button class="btn btn-sm btn-success me-1">
                                                            <i class="bi bi-check-circle me-1"></i> Approve
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-x-circle me-1"></i> Reject
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="card notification-card request-card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-2">
                                                    <h5 class="card-title fw-bold mb-0 text-dark">Lola Feliza</h5>
                                                    <span class="badge bg-primary bg-opacity-10 text-primary">New</span>
                                                </div>
                                                
                                                <div class="d-flex flex-wrap mb-1">
                                                    <span class="info-label">Request Type:</span>
                                                    <span>Transportation</span>
                                                </div>
                                                
                                                <div class="d-flex flex-wrap mb-1">
                                                    <span class="info-label">Address:</span>
                                                    <span>Mondragon, Barangay 14</span>
                                                </div>
                                                
                                                <div class="d-flex flex-wrap mb-3">
                                                    <span class="info-label">Contact:</span>
                                                    <span>0944-555-6666</span>
                                                </div>
                                                
                                                <div class="d-flex flex-wrap mb-3">
                                                    <span class="info-label">Details:</span>
                                                    <span>Needs transport for monthly check-up on Friday.</span>
                                                </div>
                                                
                                                <div class="d-flex justify-content-between align-items-center">
                                                    <small class="text-muted">
                                                        <i class="bi bi-clock me-1"></i> 5 hours ago
                                                    </small>
                                                    <div>
                                                        <button class="btn btn-sm btn-success me-1">
                                                            <i class="bi bi-check-circle me-1"></i> Approve
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger">
                                                            <i class="bi bi-x-circle me-1"></i> Reject
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
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
                                <div class="card notification-card pending-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="fw-bold mb-0 text-dark">Lola Remedios - Emergency</h6>
                                            <span class="badge bg-info bg-opacity-10 text-info">In Progress</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">Responded:</span>
                                            <span>Nurse Juan Dela Cruz</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-3">
                                            <span class="info-label">Status:</span>
                                            <span>On route to location, ETA 10 minutes</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i> Started 25 min ago
                                            </small>
                                            <button class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil-square me-1"></i> Update
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card notification-card pending-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="fw-bold mb-0 text-dark">Lolo Carlos - Home Care</h6>
                                            <span class="badge bg-warning bg-opacity-10 text-warning">Pending</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">Approved:</span>
                                            <span>Admin User</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-3">
                                            <span class="info-label">Assigned To:</span>
                                            <span>Care Worker Ana (starts tomorrow)</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i> Requested 5 hours ago
                                            </small>
                                            <button class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil-square me-1"></i> Update
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="card notification-card pending-card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="fw-bold mb-0 text-dark">Tatay Juan - Emergency</h6>
                                            <span class="badge bg-info bg-opacity-10 text-info">In Progress</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">Responded:</span>
                                            <span>Dr. Santos</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-3">
                                            <span class="info-label">Status:</span>
                                            <span>Stabilized, monitoring vitals</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i> Started 2 hours ago
                                            </small>
                                            <button class="btn btn-sm btn-outline-primary">
                                                <i class="bi bi-pencil-square me-1"></i> Update
                                            </button>
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
        // Initialize Bootstrap tabs
        var tabEls = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabEls.forEach(function(tabEl) {
            new bootstrap.Tab(tabEl);
        });
    </script>
</body>
</html>