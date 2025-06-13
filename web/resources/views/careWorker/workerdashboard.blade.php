<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Care Worker Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/dashboard2.css') }}">
</head>
<body>

    @include('components.careWorkerNavbar')
    @include('components.careWorkerSidebar')

    @php
    use App\Helpers\TranslationHelper as T;
    @endphp

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
                    <p>Welcome back to your Care Worker Dashboard.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Get Started</button>
                </div>
            </div>
        </div>
    </div>

    <div class="home-section">
        <div class="text-left">{{ T::translate('CARE WORKER DASHBOARD', 'DASHBOARD PARA SA CARE WORKER') }}</div>
        <div class="container-fluid">
            <div class="row g-3" id="home-content">
                <!-- First Row -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card stat-card stat-card-beneficiaries">
                        <div class="card-body">
                            <div class="label">Managed Beneficiaries</div>
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
                            <div class="label">Total Care Hours</div>
                            <div class="value">{{ $careHoursStats['total_formatted'] }}</div>
                            <div class="sub-stats">
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--indigo-600)">{{ $careHoursStats['week_formatted'] }}</div>
                                    <div class="sub-label">This Week</div>
                                </div>
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--amber-600)">{{ $careHoursStats['month_formatted'] }}</div>
                                    <div class="sub-label">This Month</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card stat-card stat-card-requests">
                        <div class="card-body">
                            <div class="label">Submitted Care Plans</div>
                            <div class="value">{{ number_format($reportStats['total']) }}</div>
                            <div class="sub-stats">
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--rose-600)">{{ number_format($reportStats['pending']) }}</div>
                                    <div class="sub-label">Pending</div>
                                </div>
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--emerald-600)">{{ number_format($reportStats['approved']) }}</div>
                                    <div class="sub-label">Approved</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Second Row -->
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <span>Your Recent Reports</span>
                            <a href="#" class="see-all">See All <i class="bi bi-chevron-right"></i></a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Beneficiary</th>
                                            <th>Status</th>
                                            <th>Date</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td>Visit Report</td>
                                            <td><span class="badge badge-reviewed">Approved</span></td>
                                            <td>May 28, 2023</td>
                                        </tr>
                                        <tr>
                                            <td>Incident Report</td>
                                            <td><span class="badge badge-reviewed">Approved</span></td>
                                            <td>May 27, 2023</td>
                                        </tr>
                                        <tr>
                                            <td>Weekly Report</td>
                                            <td><span class="badge badge-emergency">Pending</span></td>
                                            <td>May 26, 2023</td>
                                        </tr>
                                        <tr>
                                            <td>Medication Report</td>
                                            <td><span class="badge badge-service">Pending</span></td>
                                            <td>May 25, 2023</td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <span>Your Upcoming Schedules</span>
                            <a href="#" class="see-all">See All <i class="bi bi-chevron-right"></i></a>
                        </div>
                        <div class="card-body">
                            <div class="schedule-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="schedule-time">Today, 2:00 PM</div>
                                    <span class="badge badge-status badge-active">Confirmed</span>
                                </div>
                                <div class="schedule-details">Home visit for beneficiary #B-02415 (Mrs. Anderson)</div>
                                <div class="schedule-details">Location: 123 Main St, Apt 4B</div>
                            </div>
                            
                            <div class="schedule-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="schedule-time">Today, 4:30 PM</div>
                                    <span class="badge badge-status badge-active">Confirmed</span>
                                </div>
                                <div class="schedule-details">Medical appointment for beneficiary #B-01822 (Mr. Thompson)</div>
                                <div class="schedule-details">Location: City General Hospital</div>
                            </div>
                            
                            <div class="schedule-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="schedule-time">Tomorrow, 9:00 AM</div>
                                    <span class="badge badge-status badge-inactive">Pending</span>
                                </div>
                                <div class="schedule-details">Weekly checkup for beneficiary #B-01567 (Mrs. Rodriguez)</div>
                                <div class="schedule-details">Location: 456 Oak Ave</div>
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