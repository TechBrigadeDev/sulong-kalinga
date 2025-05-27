<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Family Portal - Emergency & Service</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/emergencyAndService.css') }}">
</head>
<body>
    @include('components.familyPortalNavbar')
    @include('components.familyPortalSidebar')

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
                                        <div class="mb-3">
                                            <label for="requestType" class="form-label">Service Type</label>
                                            <select class="form-select" id="requestType" required>
                                                <option value="" selected disabled>Select service type</option>
                                                <option value="home_care">Home Care Visit</option>
                                                <option value="transportation">Transportation Assistance</option>
                                                <option value="meal_delivery">Meal Delivery</option>
                                                <option value="medication">Medication Pickup</option>
                                                <option value="other">Other Service</option>
                                            </select>
                                        </div>
                                        
                                        <div class="row g-3 mb-3">
                                            <div class="col-md-6">
                                                <label for="preferredDate" class="form-label">Preferred Date</label>
                                                <input type="date" class="form-control" id="preferredDate" required>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="preferredTime" class="form-label">Preferred Time</label>
                                                <input type="time" class="form-control" id="preferredTime" required>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label for="details" class="form-label">Service Details</label>
                                            <textarea class="form-control" id="details" rows="2" placeholder="Please describe your needs..." required></textarea>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center mt-auto">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox" id="recurring" checked>
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
                                    <p class="text-white-80 mb-4">Immediate help when you need it most. Our team will respond within minutes.</p>
                                    <button class="btn emergency-btn" id="emergencyButton">
                                        <i class="bi bi-exclamation-triangle-fill"></i>
                                        <span>Request Emergency Help</span>
                                    </button>
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
                                <table class="table table-hover mb-0">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Date Submitted</th>
                                            <th>Status</th>
                                            <th>Assigned To</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><span class="badge badge-service">Service</span></td>
                                            <td>Today, 10:30 AM</td>
                                            <td><span class="badge badge-pending">Pending Approval</span></td>
                                            <td>-</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-secondary">
                                                    <i class="bi bi-eye"></i> View
                                                </button>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><span class="badge badge-emergency">Emergency</span></td>
                                            <td>Today, 09:15 AM</td>
                                            <td><span class="badge badge-service">In Progress</span></td>
                                            <td>Nurse Juan Dela Cruz</td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-geo-alt"></i> Track
                                                </button>
                                            </td>
                                        </tr>
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
                            <button class="btn btn-md btn-outline-secondary">
                                <i class="bi bi-filter"></i> Filter
                            </button>
                        </div>
                        <div class="card-body mt-0 pt-0">
                            <!-- Emergency History Item -->
                            <div class="status-card status-emergency p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-1 d-flex align-items-center">
                                            <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i>
                                            Emergency Assistance
                                        </h6>
                                    </div>
                                    <span class="badge badge-completed">Completed</span>
                                </div>
                                <p class="mb-2">Medical emergency - chest pain</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar me-1"></i> November 15, 2023
                                        <i class="bi bi-clock ms-3 me-1"></i> 2:30 PM
                                    </small>
                                    <button class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Service History Item -->
                            <div class="status-card status-service p-3 mb-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-1 d-flex align-items-center">
                                            <i class="bi bi-hand-thumbs-up-fill text-primary me-2"></i>
                                            Transportation Assistance
                                        </h6>
                                    </div>
                                    <span class="badge badge-completed">Completed</span>
                                </div>
                                <p class="mb-2">Doctor's appointment at General Hospital</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar me-1"></i> November 10, 2023
                                        <i class="bi bi-clock ms-3 me-1"></i> 9:00 AM
                                    </small>
                                    <button class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Rejected Service Item -->
                            <div class="status-card status-pending p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-1 d-flex align-items-center">
                                            <i class="bi bi-hand-thumbs-up-fill text-warning me-2"></i>
                                            Meal Delivery
                                        </h6>
                                    </div>
                                    <span class="badge badge-emergency">Rejected</span>
                                </div>
                                <p class="mb-2">Special dietary requirements</p>
                                <div class="d-flex justify-content-between align-items-center">
                                    <small class="text-muted">
                                        <i class="bi bi-calendar me-1"></i> November 5, 2023
                                        <i class="bi bi-clock ms-3 me-1"></i> 11:45 AM
                                    </small>
                                    <button class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-three-dots"></i>
                                    </button>
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
        // Emergency button functionality
        document.getElementById('emergencyButton').addEventListener('click', function() {
            const alertDiv = document.getElementById('emergencyAlert');
            alertDiv.classList.remove('d-none');
            
            // Simulate API call
            setTimeout(() => {
                alertDiv.classList.add('d-none');
            }, 5000);
            
            // In real implementation, would use fetch() to send request
            console.log('Emergency request sent to server');
        });

        // Service request form submission
        document.getElementById('serviceRequestForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const alertDiv = document.getElementById('serviceRequestAlert');
            alertDiv.classList.remove('d-none');
            
            // Simulate form processing
            setTimeout(() => {
                alertDiv.classList.add('d-none');
                this.reset();
            }, 5000);
            
            console.log('Service request submitted');
        });
    </script>
</body>
</html>