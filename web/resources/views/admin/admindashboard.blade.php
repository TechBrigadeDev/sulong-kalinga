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
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp

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
                    <p>{{ T::translate('Welcome back to your Administrator Dashboard.', 'Maligayang Pagbabalik sa Iyong Administrator Dashboard')}}</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">{{ T::translate('Get Started', 'Magpatuloy')}}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="home-section">
        <div class="text-left">{{ T::translate('ADMIN DASHBOARD', 'DASHBOARD PARA SA ADMIN') }}</div>
        <div class="container-fluid">
            <div class="row g-3" id="home-content">
                <!-- First Row -->
                <div class="col-sm-6 col-md-3">
                    <div class="card stat-card stat-card-beneficiaries">
                        <div class="card-body">
                            <div class="label">{{ T::translate('Total Beneficiaries', 'Mga Benepisyaryo')}}</div>
                            <div class="value">{{ number_format($beneficiaryStats['total']) }}</div>
                            <div class="sub-stats">
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--teal-600)">{{ number_format($beneficiaryStats['active']) }}</div>
                                    <div class="sub-label">{{ T::translate('Active', 'Aktibo')}}</div>
                                </div>
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--slate-500)">{{ number_format($beneficiaryStats['inactive']) }}</div>
                                    <div class="sub-label">{{ T::translate('Inactive', 'Di-Aktibo')}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <div class="card stat-card stat-card-workers">
                        <div class="card-body">
                            <div class="label">{{ T::translate('Total Care Workers', 'Mga Tagapag-alaga')}}</div>
                            <div class="value">{{ number_format($careWorkerStats['total']) }}</div>
                            <div class="sub-stats">
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--indigo-600)">{{ number_format($careWorkerStats['active']) }}</div>
                                    <div class="sub-label">{{ T::translate('Active', 'Aktibo')}}</div>
                                </div>
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--slate-500)">{{ number_format($careWorkerStats['inactive']) }}</div>
                                    <div class="sub-label">{{ T::translate('Inactive', 'Di-Aktibo')}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <div class="card stat-card stat-card-municipalities">
                        <div class="card-body">
                            <div class="label">{{ T::translate('Municipalities', 'Mga Munisipalidad')}}</div>
                            <div class="value">{{ number_format($locationStats['municipalities']) }}</div>
                            <div class="sub-stats">
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--blue-600)">{{ number_format($locationStats['barangays']) }}</div>
                                    <div class="sub-label">{{ T::translate('Total Barangays', 'Mga Barangay')}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-sm-6 col-md-3">
                    <div class="card stat-card stat-card-requests">
                        <div class="card-body">
                            <div class="label">{{ T::translate('Requests Today', 'Mga Pakiusap')}}</div>
                            <div class="value">{{ number_format($requestStats['total']) }}</div>
                            <div class="sub-stats">
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--rose-600)">{{ number_format($requestStats['emergency']) }}</div>
                                    <div class="sub-label">{{ T::translate('Emergency', 'Emergency')}}</div>
                                </div>
                                <div class="sub-stat">
                                    <div class="sub-value" style="color: var(--amber-600)">{{ number_format($requestStats['service']) }}</div>
                                    <div class="sub-label">{{ T::translate('Service', 'Paglilingkod')}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Second Row -->
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <span>{{ T::translate('Expenses This Month', 'Mga Gastos Ngayong Buwan') }}</span>
                            <a href="{{ route('admin.expense.index') }}" class="see-all">{{ T::translate('See All', 'Tingnan Lahat') }} <i class="bi bi-chevron-right"></i></a>
                        </div>
                        <div class="card-body">
                            <!-- This Month's Expenses -->
                            @if(count($expenseData['recent_expenses']) > 0)
                                <div class="d-flex justify-content-between mb-3 align-items-center">
                                    <h6 class="mb-0">{{ T::translate('Total', 'Kabuuan') }}: ₱{{ number_format($expenseData['total_spent'], 2) }}</h6>
                                    <span class="text-muted small">{{ $expenseData['month'] }}</span>
                                </div>
                                
                                @foreach($expenseData['recent_expenses'] as $expense)
                                    <div class="schedule-item d-flex justify-content-between align-items-center" style="border-left: 4px solid {{ $expense['color'] }}; padding-left: 10px;">
                                        <div>
                                            <div class="schedule-time">{{ $expense['title'] }}</div>
                                            <div class="schedule-details">{{ date('M j', strtotime($expense['date'])) }}</div>
                                        </div>
                                        <div class="text-end">
                                            <span class="schedule-time fw-bold">₱{{ number_format($expense['amount'], 2) }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="text-center py-4">
                                    <i class="bi bi-receipt text-secondary" style="font-size: 2rem;"></i>
                                    <p class="mt-2 mb-0">{{ T::translate('No expenses recorded this month', 'Walang naitalang gastos ngayong buwan') }}</p>
                                </div>
                            @endif
                            @if(count($expenseData['category_breakdown']) > 0)
                                <hr class="my-3">
                                <div class="category-breakdown">
                                    <h6 class="mb-3">{{ T::translate('Expense Breakdown', 'Breakdown ng Gastos') }}</h6>
                                    @foreach($expenseData['category_breakdown'] as $category)
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <div class="d-flex align-items-center">
                                                <div class="category-color me-2" style="width: 12px; height: 12px; border-radius: 50%; background-color: {{ $category['color'] }};"></div>
                                                <span class="small">{{ $category['category'] }}</span>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <span class="small me-2">₱{{ number_format($category['amount'], 2) }}</span>
                                                <span class="badge bg-secondary">{{ $category['percentage'] }}%</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                <div class="col-12 col-lg-6">
                    <div class="card">
                        <div class="card-header">
                            <span>{{ T::translate('Upcoming Visitations', 'Nalalapit na Pagdalaw')}}</span>
                            <a href="{{ route('admin.careworker.appointments.index') }}" class="see-all">{{ T::translate('See All', 'Tignan Lahat')}} <i class="bi bi-chevron-right"></i></a>
                        </div>
                        <div class="card-body">
                            @forelse($upcomingVisitations as $visit)
                                <div class="schedule-item">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <div class="schedule-time">{{ $visit['date_display'] }}, {{ $visit['time'] }}</div>
                                        <span class="badge {{ $visit['status_class'] }}">{{ $visit['visit_type'] }}</span>
                                    </div>
                                    <div class="schedule-details">{{ $visit['visit_type'] }} for {{ $visit['beneficiary_name'] }}</div>
                                    <div class="schedule-details">Assigned to: {{ $visit['assigned_to'] }}</div>
                                </div>
                            @empty
                                <div class="schedule-item text-center">
                                    <div class="schedule-details">{{ T::translate('No upcoming visitations scheduled', 'Walang Nalalapit na Pagdalaw')}}</div>
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
                            <a href="{{ route('admin.careworker.performance.index') }}" class="see-all">{{ T::translate('See All', 'Tingnan Lahat')}}<i class="bi bi-chevron-right"></i></a>
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
                            <span>{{ T::translate('Recent Weekly Care Plans', 'Mga Katatapos na Weekly Care Plan')}}</span>
                            <a href="{{ route('admin.reports') }}" class="see-all">{{ T::translate('See All', 'Tingnan Lahat')}} <i class="bi bi-chevron-right"></i></a>
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