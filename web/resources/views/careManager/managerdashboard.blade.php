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

    <style>
        /* Emergency & Service Request Card Styles - Updated for consistency */
        .emergency-card,
        .request-card {
            border-left: 3px solid; /* Reduced from 4px */
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05); /* Reduced shadow */
            transition: transform 0.2s, box-shadow 0.2s;
            margin-bottom: 0.75rem; /* Consistent spacing */
        }
        
        .emergency-card {
            border-color: var(--rose-500);
            background: linear-gradient(to right, rgba(254, 242, 242, 0.5), white) !important; /* More subtle gradient */
        }
        
        .request-card {
            border-color: var(--teal-500);
            background: linear-gradient(to right, rgba(239, 246, 255, 0.5), white) !important; /* More subtle gradient */
        }
        
        .notification-card:hover {
            transform: translateY(-1px); /* Reduced movement */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.08);
        }
        
        /* Badge Styles - Match other dashboard badges */
        .notification-card .badge {
            font-size: 0.75rem; /* 12px */
            font-weight: 500;
            padding: 0.25rem 0.5rem;
        }
        
        /* Typography normalization */
        .notification-card p {
            font-size: 0.75rem; /* 14px - standard text size */
            line-height: 1.4;
            margin-bottom: 0.5rem;
            border-left-width: 2px !important; /* Thinner accent border */
        }
        
        .notification-card .fw-semibold {
            font-weight: 500 !important; /* Less bold */
        }
        
        /* Make rounded pill badges match other badges */
        .notification-card .badge.rounded-pill {
            font-size: 0.75rem;
            padding: 0.25rem 0.75rem !important;
        }
        
        /* Reduce icon size */
        .notification-card i {
            font-size: 0.75rem;
        }

        .notification-card .beneficiary-name {
            font-size: 0.8rem;  /* Match other text */
        }
        
        /* Also ensure consistent font sizes for all elements */
        .notification-card span:not(.badge),
        .notification-card small {
            font-size: 0.8rem;
        }
    </style>

</head>
<body>

    @include('components.careManagerNavbar')
    @include('components.careManagerSidebar')

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
                    <p>Welcome back to your Care Manager Dashboard.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Get Started</button>
                </div>
            </div>
        </div>
    </div>

    <div class="home-section">
        <div class="text-left">{{ T::translate('CARE MANAGER DASHBOARD', 'DASHBOARD PARA SA CARE MANAGER') }}</div>
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
                                @forelse($emergencyAndServiceRequests as $request)
                                    <div class="notification-card {{ $request['type'] === 'emergency' ? 'emergency-card' : 'request-card' }} p-3">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div>
                                                <span class="badge px-2 me-1" style="background-color: {{ $request['color_code'] }}">
                                                    <i class="bi {{ $request['type'] === 'emergency' ? 'bi-exclamation-triangle-fill' : 'bi-tools' }} me-1"></i>
                                                    {{ $request['emergency_type'] }}
                                                </span>
                                                <span class="badge bg-warning text-white">{{ ucfirst($request['status']) }}</span>
                                            </div>
                                            <small class="notification-time text-muted"><i class="bi bi-clock me-1"></i>{{ $request['time_ago'] }}</small>
                                        </div>
                                        <p class="ps-1" style="border-color: {{ $request['color_code'] }} !important;">{{ $request['message'] }}</p>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <div>
                                                <i class="bi bi-person-fill me-1 text-primary"></i>
                                                <span class="beneficiary-name">{{ $request['beneficiary_name'] }}</span>
                                            </div>
                                            @if($request['assigned_to'] === 'Unassigned')
                                                <span class="badge rounded-pill bg-warning text-dark">
                                                    <i class="bi bi-exclamation-circle me-1"></i>{{ $request['assigned_to'] }}
                                                </span>
                                            @else
                                                <span class="badge rounded-pill bg-success text-white">
                                                    <i class="bi bi-person-check me-1"></i>{{ $request['assigned_to'] }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                @empty
                                    <div class="notification-card p-3 text-center">
                                        <i class="bi bi-inbox-fill text-secondary" style="font-size: 1.25rem;"></i>
                                        <p class="mt-2 mb-0">{{ T::translate('No requests found.', 'Walang nahanap na kahilingan.') }}</p>
                                    </div>
                                @endforelse
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
                            @forelse($upcomingVisitations as $visit)
                                <div class="schedule-item">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="schedule-time">{{ $visit['date_display'] }}, {{ $visit['time'] }}</div>
                                        <span class="badge {{ $visit['status_class'] }}">{{ $visit['visit_type'] }}</span>
                                    </div>
                                    <div class="schedule-details">{{ $visit['visit_type'] }} for {{ $visit['beneficiary_name'] }}</div>
                                    <div class="schedule-details">{{ T::translate('Assigned to', 'Itinalaga kay') }}: {{ $visit['assigned_to'] }}</div>
                                </div>
                            @empty
                                <div class="text-center py-3">
                                    {{ T::translate('No upcoming visitations scheduled', 'Walang nakaiskedyul na mga papalapit na pagbisita') }}
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                
                <!-- Third Row -->
                <div class="col-12 col-lg-5">
                    <div class="card">
                        <div class="card-header">
                            <span>Care Worker Performance</span>
                            <a href="{{ route('care-manager.careworker.performance.index') }}" class="see-all">See All <i class="bi bi-chevron-right"></i></a>
                        </div>
                        <div class="card-body">
                            @forelse($careWorkerPerformance as $worker)
                                <div class="user-item d-flex align-items-center p-2 mb-2 rounded" style="background-color: rgba(0,0,0,0.02); transition: all 0.2s;">
                                    @if(isset($worker['photo_path']) && $worker['photo_path'])
                                        <img src="{{ asset($worker['photo_path']) }}" alt="{{ $worker['name'] }}" class="avatar rounded-circle me-3" style="width: 48px; height: 48px; object-fit: cover;">
                                    @else
                                        <img src="{{ asset('images/defaultProfile.png') }}" alt="Default Profile" class="avatar rounded-circle me-3" style="width: 48px; height: 48px; object-fit: cover;">
                                    @endif
                                    <div class="user-info flex-grow-1">
                                        <div class="user-name fw-bold">{{ $worker['name'] }}</div>
                                        <div class="user-title text-muted small">{{ T::translate('Care Worker', 'Tagapag-alaga') }}</div>
                                    </div>
                                    <div class="text-end">
                                        <div class="work-hours fw-bold text-primary">{{ $worker['formatted_time'] }}</div>
                                        <div class="work-hours-label small">{{ T::translate('This month', 'Ngayong buwan') }}</div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-4">
                                    <i class="bi bi-graph-down text-secondary" style="font-size: 2rem;"></i>
                                    <p class="mt-2 mb-0">{{ T::translate('No care worker data available', 'Walang available na data ng tagapag-alaga') }}</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-lg-7">
                    <div class="card">
                        <div class="card-header">
                            <span>Recent Weekly Care Plans</span>
                            <a href="{{ route('care-manager.reports') }}" class="see-all">See All <i class="bi bi-chevron-right"></i></a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ T::translate('Beneficiary', 'Benepisyaryo') }}</th>
                                            <th>{{ T::translate('Submitted By', 'Isinumite Ni') }}</th>
                                            <th>{{ T::translate('Date', 'Petsa') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentCarePlans as $plan)
                                            <tr>
                                                <td>{{ $plan['beneficiary_name'] }}</td>
                                                <td>{{ $plan['submitted_by'] }}</td>
                                                <td>{{ $plan['date'] }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="3" class="text-center">{{ T::translate('No care plans found', 'Walang nahanap na mga plano sa pangangalaga') }}</td>
                                            </tr>
                                        @endforelse
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