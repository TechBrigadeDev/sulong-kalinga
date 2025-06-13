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
                                    <div class="sub-label">Pending Review</div>
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
                            <span>{{ T::translate('Your Recent Care Plans', 'Mga Kamakailang Plano ng Pangangalaga') }}</span>
                            <a href="{{ route('care-worker.reports') }}" class="see-all">{{ T::translate('See All', 'Tingnan Lahat') }} <i class="bi bi-chevron-right"></i></a>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>{{ T::translate('Beneficiary', 'Benepisyaryo') }}</th>
                                            <th>{{ T::translate('Status', 'Katayuan') }}</th>
                                            <th>{{ T::translate('Date', 'Petsa') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($recentCarePlans as $plan)
                                        <tr>
                                            <td>{{ $plan['beneficiary_name'] }}</td>
                                            <td><span class="badge {{ $plan['status_class'] }}">{{ $plan['status'] }}</span></td>
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
                
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <span>{{ T::translate('Your Upcoming Visitations', 'Mga Papalapit na Pagbisita') }}</span>
                            <a href="{{ route('care-worker.careworker.appointments.index') }}" class="see-all">{{ T::translate('See All', 'Tingnan Lahat') }} <i class="bi bi-chevron-right"></i></a>
                        </div>
                        <div class="card-body">
                            @forelse($upcomingVisitations as $visit)
                            <div class="schedule-item">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <div class="schedule-time">{{ $visit['date_display'] }}, {{ $visit['time'] }}</div>
                                </div>
                                <div class="schedule-details">{{ $visit['visit_type'] }} for Beneficiary {{ $visit['beneficiary_name'] }}</div>
                                <div class="schedule-details">{{ T::translate('Location', 'Lokasyon') }}: {{ $visit['location'] }}</div>
                            </div>
                            @empty
                            <div class="text-center py-3">
                                {{ T::translate('No upcoming visitations scheduled', 'Walang nakaiskedyul na mga papalapit na pagbisita') }}
                            </div>
                            @endforelse
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