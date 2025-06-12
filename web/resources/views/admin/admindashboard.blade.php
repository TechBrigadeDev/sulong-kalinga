<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard2.css') }}">
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')

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
                    <p>Welcome back to your Administrator Dashboard.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Get Started</button>
                </div>
            </div>
        </div>
    </div>

    <div class="home-section">
        <div class="text-left">ADMIN DASHBOARD</div>
        <div class="container-fluid">
            <div class="row g-3" id="home-content">
                <!-- First Row -->
                <div class="col-sm-6 col-md-3">
                    <div class="card stat-card stat-card-beneficiaries">
                        <div class="card-body">
                            <div class="label">Total Beneficiaries</div>
                            <div class="value">1,248</div>
                            <div class="sub-stats">
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--teal-600)">892</div>
                                    <div class="sub-label">Active</div>
                                </div>
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--slate-500)">356</div>
                                    <div class="sub-label">Inactive</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-6 col-md-3">
                    <div class="card stat-card stat-card-workers">
                        <div class="card-body">
                            <div class="label">Total Care Workers</div>
                            <div class="value">84</div>
                            <div class="sub-stats">
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--indigo-600)">72</div>
                                    <div class="sub-label">Active</div>
                                </div>
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--slate-500)">12</div>
                                    <div class="sub-label">Inactive</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-6 col-md-3">
                    <div class="card stat-card stat-card-municipalities">
                        <div class="card-body">
                            <div class="label">Municipalities</div>
                            <div class="value">12</div>
                            <div class="sub-stats">
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--blue-600)">142</div>
                                    <div class="sub-label">Total Barangays</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-sm-6 col-md-3">
                    <div class="card stat-card stat-card-requests">
                        <div class="card-body">
                            <div class="label">Requests Today</div>
                            <div class="value">24</div>
                            <div class="sub-stats">
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--rose-600)">5</div>
                                    <div class="sub-label">Emergency</div>
                                </div>
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--amber-600)">19</div>
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
                            <span>Expenses & Budgeting</span>
                            <a href="{{ route('admin.expense.index') }}" class="see-all">See All <i class="bi bi-chevron-right"></i></a>
                        </div>
                        <div class="card-body">
                            <!-- Recent Expenses List -->
                            <div class="schedule-item" style="border-left: 4px solid var(--rose-500); padding-left: 10px;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="schedule-time">Office Supplies</div>
                                    <span class="schedule-details">₱12,500</span>
                                </div>
                                <div class="schedule-details">June 10, 2025</div>
                            </div>
                            
                            <div class="schedule-item" style="border-left: 4px solid var(--indigo-500); padding-left: 10px;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="schedule-time">Transportation</div>
                                    <span class="schedule-details">₱8,750</span>
                                </div>
                                <div class="schedule-details">June 8, 2025</div>
                            </div>
                            
                            <div class="schedule-item" style="border-left: 4px solid var(--amber-500); padding-left: 10px;">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="schedule-time">Medical Supplies</div>
                                    <span class="schedule-details">₱21,100</span>
                                </div>
                                <div class="schedule-details">June 5, 2025</div>
                            </div>
                            
                            <!-- Budget Categories Breakdown -->
                            <div class="schedule-item">
                                <div class="schedule-time mb-2">Budget Categories</div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        <div style="width: 10px; height: 10px; background-color: var(--rose-500); border-radius: 50%; margin-right: 8px;"></div>
                                        <span class="schedule-details">Office Supplies</span>
                                    </div>
                                    <div class="schedule-details">₱12,500 (29%)</div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="d-flex align-items-center">
                                        <div style="width: 10px; height: 10px; background-color: var(--indigo-500); border-radius: 50%; margin-right: 8px;"></div>
                                        <span class="schedule-details">Transportation</span>
                                    </div>
                                    <div class="schedule-details">₱8,750 (21%)</div>
                                </div>
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <div style="width: 10px; height: 10px; background-color: var(--amber-500); border-radius: 50%; margin-right: 8px;"></div>
                                        <span class="schedule-details">Medical Supplies</span>
                                    </div>
                                    <div class="schedule-details">₱21,100 (50%)</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <span>Upcoming Schedules</span>
                            <a href="#" class="see-all">See All <i class="bi bi-chevron-right"></i></a>
                        </div>
                        <div class="card-body">
                            <div class="schedule-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="schedule-time">Today, 2:00 PM</div>
                                    <span class="badge badge-status badge-active">Confirmed</span>
                                </div>
                                <div class="schedule-details">Home visit for beneficiary #B-02415 (Mrs. Anderson)</div>
                                <div class="schedule-details">Assigned to: Sarah Johnson</div>
                            </div>
                            
                            <div class="schedule-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="schedule-time">Today, 4:30 PM</div>
                                    <span class="badge badge-status badge-active">Confirmed</span>
                                </div>
                                <div class="schedule-details">Medical appointment for beneficiary #B-01822 (Mr. Thompson)</div>
                                <div class="schedule-details">Assigned to: Michael Chen</div>
                            </div>
                            
                            <div class="schedule-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="schedule-time">Tomorrow, 9:00 AM</div>
                                    <span class="badge badge-status badge-inactive">Pending</span>
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
                            <a href="{{ route('admin.reports') }}" class="see-all">See All <i class="bi bi-chevron-right"></i></a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Type</th>
                                            <th>Submitted By</th>
                                            <th>Date</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Monthly Report</td>
                                            <td>Sarah Johnson</td>
                                            <td>June 11, 2025</td>
                                            <td><span class="badge bg-success">Approved</span></td>
                                        </tr>
                                        <tr>
                                            <td>Incident Report</td>
                                            <td>Michael Chen</td>
                                            <td>June 10, 2025</td>
                                            <td><span class="badge bg-warning">Pending</span></td>
                                        </tr>
                                        <tr>
                                            <td>Weekly Report</td>
                                            <td>Emma Williams</td>
                                            <td>June 9, 2025</td>
                                            <td><span class="badge bg-success">Approved</span></td>
                                        </tr>
                                        <tr>
                                            <td>Visitation Report</td>
                                            <td>David Brown</td>
                                            <td>June 8, 2025</td>
                                            <td><span class="badge bg-info">Reviewed</span></td>
                                        </tr>
                                        <tr>
                                            <td>Monthly Report</td>
                                            <td>Lisa Anderson</td>
                                            <td>June 7, 2025</td>
                                            <td><span class="badge bg-success">Approved</span></td>
                                        </tr>
                                        <tr>
                                            <td>Financial Report</td>
                                            <td>John Smith</td>
                                            <td>June 6, 2025</td>
                                            <td><span class="badge bg-success">Approved</span></td>
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