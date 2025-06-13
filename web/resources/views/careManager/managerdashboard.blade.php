<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Care Manager Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard2.css') }}">
</head>
<body>

    @include('components.careManagerNavbar')
    @include('components.careManagerSidebar')

    <!-- Welcome Back Modal -->
    <div class="modal fade" id="welcomeBackModal" tabindex="-1" aria-labelledby="welcomeBackModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="welcomeBackModalLabel">Welcome Back!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class='bi bi-gem display-4 text-primary mb-3'></i>
                    <h4>Hello, {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}!</h4>
                    <p>Welcome back to your Care Manager Dashboard.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Get Started</button>
                </div>
            </div>
        </div>
    </div>

    <div class="home-section">
        <div class="text-left">CARE MANAGER DASHBOARD</div>
        <div class="container-fluid">
            <div class="row g-3" id="home-content">
                <!-- First Row -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card stat-card stat-card-beneficiaries">
                        <div class="card-body">
                            <div class="label">Total Beneficiaries</div>
                            <div class="value">{{ number_format($beneficiaryStats['total']) }}</div>
                            <div class="sub-stats">
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--teal-600)">{{ number_format($beneficiaryStats['active']) }}</div>
                                    <div class="sub-label">Active</div>
                                </div>
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--slate-500)">{{ number_format($beneficiaryStats['inactive']) }}</div>
                                    <div class="sub-label">Inactive</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card stat-card stat-card-workers">
                        <div class="card-body">
                            <div class="label">Total Care Workers</div>
                            <div class="value">{{ number_format($careWorkerStats['total']) }}</div>
                            <div class="sub-stats">
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--indigo-600)">{{ number_format($careWorkerStats['active']) }}</div>
                                    <div class="sub-label">Active</div>
                                </div>
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--slate-500)">{{ number_format($careWorkerStats['inactive']) }}</div>
                                    <div class="sub-label">Inactive</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card stat-card stat-card-requests">
                        <div class="card-body">
                            <div class="label">Requests Today</div>
                            <div class="value">{{ number_format($requestStats['total']) }}</div>
                            <div class="sub-stats">
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--rose-600)">{{ number_format($requestStats['emergency']) }}</div>
                                    <div class="sub-label">Emergency</div>
                                </div>
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--amber-600)">{{ number_format($requestStats['service']) }}</div>
                                    <div class="sub-label">Service</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Second Row -->
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <span>Emergency & Service Requests</span>
                            <a href="{{ route('care-manager.emergency.request.index') }}" class="see-all">See All <i class="bi bi-chevron-right"></i></a>
                        </div>
                        <div class="card-body p-0">
                            <div class="emergency-request-container">
                                <!-- Emergency Requests -->
                                <div class="notification-card emergency-card p-3 mb-3" style="background-color: var(--rose-50)">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0 d-flex align-items-center">
                                            <span class="badge bg-danger me-2">Emergency</span>
                                            Manuel Padilla
                                            <span class="badge bg-warning ms-2 status-badge">New</span>
                                        </h6>
                                        <small class="notification-time">2 days ago</small>
                                    </div>
                                    <p class="mb-2">Beneficiary Manuel Padilla left the group</p>
                                    <div class="text-end">
                                    </div>
                                </div>
                                
                                <!-- Service Request -->
                                <div class="notification-card request-card p-3 mb-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0 d-flex align-items-center">
                                            <span class="badge bg-primary me-2">Service</span>
                                            Felix Torres
                                            <span class="badge bg-warning ms-2 status-badge">New</span>
                                        </h6>
                                        <small class="notification-time">4 days ago</small>
                                    </div>
                                    <p class="mb-2">I noticed Felix has been sleeping better lately</p>
                                    <div class="text-end">
                                    </div>
                                </div>
                                
                                <!-- COSE Support Updates -->
                                <div class="notification-card request-card p-3">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0 d-flex align-items-center">
                                            <span class="badge bg-info me-2">Update</span>
                                            COSE Support
                                            <!-- This one doesn't have a New badge - showing how it looks without -->
                                        </h6>
                                        <small class="notification-time">1 week ago</small>
                                    </div>
                                    <p class="mb-2">Quisdam et accusamus velit unde sumo percipal</p>
                                    <div class="text-end">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <span>Upcoming Visitations</span>
                            <a href="{{ route('care-manager.careworker.appointments.index') }}" class="see-all">See All <i class="bi bi-chevron-right"></i></a>
                        </div>
                        <div class="card-body">
                            <div class="schedule-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="schedule-time">Today, 2:00 PM</div>
                                    <span class="badge bg-info">Health Check</span>
                                </div>
                                <div class="schedule-details">Home visit for beneficiary #B-02415 (Mrs. Anderson)</div>
                                <div class="schedule-details">Assigned to: Sarah Johnson</div>
                            </div>
                            
                            <div class="schedule-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="schedule-time">Today, 4:30 PM</div>
                                    <span class="badge bg-success">Medical Support</span>
                                </div>
                                <div class="schedule-details">Medical appointment for beneficiary #B-01822 (Mr. Thompson)</div>
                                <div class="schedule-details">Assigned to: Michael Chen</div>
                            </div>
                            
                            <div class="schedule-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="schedule-time">Tomorrow, 9:00 AM</div>
                                    <span class="badge bg-primary">Check-up</span>
                                </div>
                                <div class="schedule-details">Weekly checkup for beneficiary #B-01567 (Mrs. Rodriguez)</div>
                                <div class="schedule-details">Assigned to: Emma Williams</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Third Row -->
                <div class="col-12 col-lg-5">
                    <div class="card">
                        <div class="card-header">
                            <span>Care Worker Performance</span>
                            <a href="#" class="see-all">See All <i class="bi bi-chevron-right"></i></a>
                        </div>
                        <div class="card-body">
                            <div class="user-item">
                                <img src="https://randomuser.me/api/portraits/women/44.jpg" alt="Avatar" class="avatar">
                                <div class="user-info">
                                    <div class="user-name">Sarah Johnson</div>
                                    <div class="user-title">Senior Care Worker</div>
                                </div>
                                <div class="text-end">
                                    <div class="work-hours">142 hrs</div>
                                    <div class="work-hours-label">This month</div>
                                </div>
                            </div>
                            
                            <div class="user-item">
                                <img src="https://randomuser.me/api/portraits/men/32.jpg" alt="Avatar" class="avatar">
                                <div class="user-info">
                                    <div class="user-name">Michael Chen</div>
                                    <div class="user-title">Care Worker</div>
                                </div>
                                <div class="text-end">
                                    <div class="work-hours">138 hrs</div>
                                    <div class="work-hours-label">This month</div>
                                </div>
                            </div>
                            
                            <div class="user-item">
                                <img src="https://randomuser.me/api/portraits/women/68.jpg" alt="Avatar" class="avatar">
                                <div class="user-info">
                                    <div class="user-name">Emma Williams</div>
                                    <div class="user-title">Care Worker</div>
                                </div>
                                <div class="text-end">
                                    <div class="work-hours">127 hrs</div>
                                    <div class="work-hours-label">This month</div>
                                </div>
                            </div>
                            
                            <div class="user-item">
                                <img src="https://randomuser.me/api/portraits/men/75.jpg" alt="Avatar" class="avatar">
                                <div class="user-info">
                                    <div class="user-name">David Brown</div>
                                    <div class="user-title">Junior Care Worker</div>
                                </div>
                                <div class="text-end">
                                    <div class="work-hours">118 hrs</div>
                                    <div class="work-hours-label">This month</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-lg-7">
                    <div class="card">
                        <div class="card-header">
                            <span>Recent Reports</span>
                            <a href="{{ route('care-manager.reports') }}" class="see-all">See All <i class="bi bi-chevron-right"></i></a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Submitted By</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Monthly Report</td>
                                            <td>Sarah Johnson</td>
                                            <td>May 28, 2023</td>
                                        </tr>
                                        <tr>
                                            <td>Incident Report</td>
                                            <td>Michael Chen</td>
                                            <td>May 27, 2023</td>
                                        </tr>
                                        <tr>
                                            <td>Weekly Report</td>
                                            <td>Emma Williams</td>
                                            <td>May 26, 2023</td>
                                        </tr>
                                        <tr>
                                            <td>Monthly Report</td>
                                            <td>David Brown</td>
                                            <td>May 25, 2023</td>
                                        </tr>
                                    </tbody>
                                </table>
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
        document.addEventListener('DOMContentLoaded', function() {
            @if($showWelcome)
                var welcomeModal = new bootstrap.Modal(document.getElementById('welcomeBackModal'));
                welcomeModal.show();
            @endif
        });
    </script>
</body>
</html>