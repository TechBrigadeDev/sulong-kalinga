<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard | Care Worker</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon-white-16x16.png')}}">
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
                    <h5 class="modal-title" id="welcomeBackModalLabel">{{ T::translate('Welcome Back!', 'Maligayang Pagbabalik!') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="{{ T::translate('Close', 'Isara') }}"></button>
                </div>
                <div class="modal-body text-center">
                    <i class='bi bi-gem display-4 text-primary mb-3'></i>
                    <h4>{{ T::translate('Hello,', 'Kamusta,') }} {{ auth()->user()->first_name }} {{ auth()->user()->last_name }}!</h4>
                    <p>{{ T::translate('Welcome back to your Care Worker Dashboard.', 'Maligayang pagbabalik sa iyong Care Worker Dashboard.') }}</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">{{ T::translate('Get Started', 'Magsimula') }}</button>
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
                            <div class="label">{{ T::translate('Managed Beneficiaries', 'Mga Benepisyaryong Namamahalaan') }}</div>
                            <div class="value">{{ number_format($beneficiaryStats['total']) }}</div>
                            <div class="sub-stats">
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--teal-600)">{{ number_format($beneficiaryStats['active']) }}</div>
                                    <div class="sub-label">{{ T::translate('Active', 'Aktibo') }}</div>
                                </div>
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--slate-500)">{{ number_format($beneficiaryStats['inactive']) }}</div>
                                    <div class="sub-label">{{ T::translate('Inactive', 'Hindi Aktibo') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card stat-card stat-card-workers">
                        <div class="card-body">
                            <div class="label">{{ T::translate('Total Care Hours', 'Kabuuang Oras ng Pangangalaga') }}</div>
                            <div class="value">{{ $careHoursStats['total_formatted'] }}</div>
                            <div class="sub-stats">
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--indigo-600)">{{ $careHoursStats['week_formatted'] }}</div>
                                    <div class="sub-label">{{ T::translate('This Week', 'Ngayong Linggo') }}</div>
                                </div>
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--amber-600)">{{ $careHoursStats['month_formatted'] }}</div>
                                    <div class="sub-label">{{ T::translate('This Month', 'Ngayong Buwan') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-4">
                    <div class="card stat-card stat-card-requests">
                        <div class="card-body">
                            <div class="label">{{ T::translate('Submitted Care Plans', 'Mga Isinumiteng Plano ng Pangangalaga') }}</div>
                            <div class="value">{{ number_format($reportStats['total']) }}</div>
                            <div class="sub-stats">
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--rose-600)">{{ number_format($reportStats['pending']) }}</div>
                                    <div class="sub-label">{{ T::translate('Pending Review', 'Naghihintay ng Pagsusuri') }}</div>
                                </div>
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--emerald-600)">{{ number_format($reportStats['approved']) }}</div>
                                    <div class="sub-label">{{ T::translate('Approved', 'Aprubado') }}</div>
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
                                            <td><span class="badge {{ $plan['status_class'] }}">{{ T::translate($plan['status'], $plan['status']) }}</span></td>
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
                                <div class="schedule-details">{{ $visit['visit_type'] }} {{ T::translate('for', 'para kay') }} {{ $visit['beneficiary_name'] }}</div>
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