<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Family Portal - Care Plan</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/familyPortalHomePage.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <style>
        :root {
            /* Vibrant yet professional color palette */
            --primary-1: #4361ee;  /* Royal blue */
            --primary-2: #3a0ca3;  /* Dark blue */
            --secondary-1: #4cc9f0; /* Sky blue */
            --secondary-2: #4895ef; /* Light blue */
            --accent-1: #f72585;   /* Pink */
            --accent-2: #7209b7;   /* Purple */
            --neutral-1: #f8f9fa;  /* Light gray */
            --neutral-2: #e9ecef;  /* Medium gray */
            --neutral-3: #495057;  /* Dark gray */
            --success: #38b000;    /* Green */
            --warning: #ffaa00;    /* Yellow */
            --danger: #ef233c;     /* Red */
        }
        
        body {
            color: var(--neutral-3);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.5;
        }
        
        .beneficiary-name {
            font-size: clamp(0.7rem, 1.8vw, 1.2rem);
        }
        
        .beneficiary-meta-item, .form-label {
            font-size: clamp(0.75rem, 1.1vw, 0.9rem);
        }
        
        .summary-card .value {
            font-size: clamp(1.5rem, 2.5vw, 2rem);
        }
        
        .summary-card .label {
            font-size: clamp(0.7rem, 1vw, 0.85rem);
        }
        
        /* Card styling */
        .card {
            border-radius: 12px;
            border: none;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            margin-bottom: clamp(0.75rem, 1.5vw, 1.25rem);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.12);
        }
        
        .card-header {
            background: linear-gradient(135deg, var(--primary-1), var(--primary-2));
            color: white;
            border-radius: 12px 12px 0 0 !important;
            padding: clamp(0.75rem, 1.25vw, 1rem);
        }
        
        /* Beneficiary section */
        .beneficiary-info {
            background-color: white;
            border-radius: 12px;
            padding: clamp(0.75rem, 1.5vw, 1.25rem);
            margin-bottom: clamp(0.75rem, 1.5vw, 1.25rem);
            display: flex;
            align-items: center;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            border-left: 5px solid var(--accent-1);
        }
        
        .beneficiary-avatar {
            width: clamp(50px, 8vw, 70px);
            height: clamp(50px, 8vw, 70px);
            border-radius: 50%;
            object-fit: cover;
            margin-right: clamp(1rem, 2vw, 1.5rem);
            border: 3px solid var(--neutral-1);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }
        
        .beneficiary-name {
            color: var(--primary-2);
            margin-bottom: 0.25rem;
        }
        
        .beneficiary-meta-label {
            color: var(--neutral-3);
            font-weight: 500;
        }
        
        /* Filters */
        .filter-card .btn {
            font-size: clamp(0.7rem, 1vw, 0.85rem);
            padding: clamp(0.2rem, 0.75vw, 0.4rem) clamp(0.3rem, 1vw, 0.6rem);
        }
        
        /* Summary cards */
        .summary-card {
            text-align: center;
            padding: clamp(0.75rem, 1.5vw, 1.25rem);
            height: 100%;
            border-left: 4px solid;
        }
        
        .summary-card:nth-child(1) {
            border-left-color: var(--secondary-1);
        }
        
        .summary-card:nth-child(2) {
            border-left-color: var(--accent-2);
        }
        
        .summary-card .value {
            color: var(--primary-2);
            margin: clamp(0.25rem, 0.75vw, 0.5rem) 0;
        }
        
        /* Care services table */
        .care-services-table {
            font-size: clamp(0.7rem, 1vw, 0.85rem);
        }
        
        .care-services-table th {
            background-color: var(--neutral-1);
            color: var(--primary-2);
        }
        
        .care-services-table td {
            vertical-align: middle;
        }
        
        /* Chart containers */
        .chart-container {
            height: clamp(180px, 25vw, 220px);
            margin-bottom: 0.5rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .beneficiary-meta {
                flex-direction: column;
                gap: 0.25rem;
            }
            
            .filter-card .row > div {
                margin-bottom: 0.5rem;
            }
            
            .summary-card {
                margin-bottom: 0.75rem;
            }
        }
        
        /* Button styling */
        .btn-outline-secondary {
            border-color: var(--neutral-2);
            color: var(--neutral-3);
        }
        
        .btn-outline-secondary:hover {
            background-color: var(--neutral-1);
        }
        
        /* Carousel controls */
        .position-relative .btn-outline-secondary {
            margin-top: 0;
            padding: 0.1rem 0.4rem;
        }
    </style>
</head>
<body>
    @include('components.familyPortalNavbar')
    @include('components.familyPortalSidebar')

    <div class="home-section">
            <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="text-left">CARE PLAN OVERVIEW</div>
            <a href="{{ route(Auth::guard('beneficiary')->check() ? 'beneficiary.care.plan.index' : 'family.care.plan.index') }}" class="btn btn-primary">
                <i class="bi bi-list-check"></i> View Care Plans
            </a>
        </div>
        <div class="container-fluid">
            <div class="row p-1" id="home-content">
                <!-- Beneficiary Info Card -->
                <div class="col-12 mb-3">
                    <div class="beneficiary-info">
                        @if($beneficiary->photo)
                            <img src="{{ asset('storage/' . $beneficiary->photo) }}" alt="Beneficiary Photo" class="beneficiary-avatar">
                        @else
                            <div class="beneficiary-avatar d-flex align-items-center justify-content-center bg-light">
                                <i class="bi bi-person-circle" style="font-size: 2rem; color: #6c757d;"></i>
                            </div>
                        @endif
                        <div class="beneficiary-meta">
                            <h4 class="beneficiary-name">{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</h4>
                            <div class="d-flex flex-wrap">
                                <div class="beneficiary-meta-item me-3">
                                    <span class="beneficiary-meta-label">Age:</span>
                                    {{ \Carbon\Carbon::parse($beneficiary->birthday)->age }} years
                                </div>
                                <div class="beneficiary-meta-item me-3">
                                    <span class="beneficiary-meta-label">Gender:</span>
                                    {{ $beneficiary->gender }}
                                </div>
                                <div class="beneficiary-meta-item">
                                    <span class="beneficiary-meta-label">Category:</span>
                                    {{ $beneficiary->category->category_name ?? 'N/A' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Filter Card -->
                <div class="col-12 mb-3">
                    <div class="card filter-card">
                        <div class="card-header d-flex justify-content-between align-items-center p-3">
                            <strong>Filters</strong>
                            <button type="submit" class="btn btn-sm btn-primary" form="filterForm">
                                <i class="bi bi-funnel-fill"></i> Apply Filters
                            </button>
                        </div>
                        <div class="card-body p-2">
                            <form id="filterForm" action="{{ route(Auth::guard('beneficiary')->check() ? 'beneficiary.care.plan.allCarePlans' : 'family.care.plan.allCarePlans') }}" method="GET">
                                <div class="row g-2 align-items-center">
                                    <div class="col-md-4">
                                        <label for="timeRange" class="form-label">Time Range:</label>
                                        <select class="form-select" id="timeRange" name="time_range">
                                            <option value="weeks" {{ $selectedTimeRange == 'weeks' ? 'selected' : '' }}>Monthly</option>
                                            <option value="months" {{ $selectedTimeRange == 'months' ? 'selected' : '' }}>Range of Months</option>
                                            <option value="year" {{ $selectedTimeRange == 'year' ? 'selected' : '' }}>Yearly</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Week Filter (visible by default if selected) -->
                                    <div class="col-md-8" id="weekFilterContainer" style="{{ $selectedTimeRange != 'weeks' ? 'display: none;' : '' }}">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <label for="monthSelect" class="form-label">Select Month:</label>
                                                <select class="form-select" id="monthSelect" name="month">
                                                    @for($m = 1; $m <= 12; $m++)
                                                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-md-6">
                                                <label for="yearSelect" class="form-label">Year:</label>
                                                <select class="form-select" id="yearSelect" name="year">
                                                    @foreach($availableYears as $year)
                                                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                                            {{ $year }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Month Range Filter (hidden by default unless selected) -->
                                    <div class="col-md-8" id="monthRangeFilterContainer" style="{{ $selectedTimeRange != 'months' ? 'display: none;' : '' }}">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="startMonthSelect" class="form-label">Start Month:</label>
                                                <select class="form-select" id="startMonthSelect" name="start_month">
                                                    @for($m = 1; $m <= 12; $m++)
                                                        <option value="{{ $m }}" {{ $selectedStartMonth == $m ? 'selected' : '' }}>
                                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="endMonthSelect" class="form-label">End Month:</label>
                                                <select class="form-select" id="endMonthSelect" name="end_month">
                                                    @for($m = 1; $m <= 12; $m++)
                                                        <option value="{{ $m }}" {{ $selectedEndMonth == $m ? 'selected' : '' }}>
                                                            {{ date('F', mktime(0, 0, 0, $m, 1)) }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-md-4">
                                                <label for="rangeYearSelect" class="form-label">Year:</label>
                                                <select class="form-select" id="rangeYearSelect" name="year">
                                                    @foreach($availableYears as $year)
                                                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                                            {{ $year }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Year Filter (hidden by default unless selected) -->
                                    <div class="col-md-4" id="yearFilterContainer" style="{{ $selectedTimeRange != 'year' ? 'display: none;' : '' }}">
                                        <label for="yearOnlySelect" class="form-label">Year:</label>
                                        <select class="form-select" id="yearOnlySelect" name="year">
                                            @foreach($availableYears as $year)
                                                <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                                    {{ $year }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="col-12 mb-3">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body summary-card">
                                    <i class="bi bi-clock-history text-primary mb-2" style="font-size: 2rem;"></i>
                                    <div class="value">{{ $totalCareTime }}</div>
                                    <div class="label text-muted">Total Care Time ({{ $dateRangeLabel }})</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-body summary-card">
                                    <i class="bi bi-file-earmark-medical text-primary mb-2" style="font-size: 2rem;"></i>
                                    <div class="value">{{ $totalCarePlans }}</div>
                                    <div class="label text-muted">Care Plans Created ({{ $dateRangeLabel }})</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Care Services Section -->
                <div class="col-12 mb-3">
                    <div class="card">
                        <div class="card-header position-relative py-4">
                        <!-- Left arrow at edge -->
                        <div class="position-absolute start-0 top-50 translate-middle-y ms-3">
                            <button class="btn btn-sm btn-outline-light" id="prevCategory">
                                <i class="bi bi-chevron-left"></i>
                            </button>
                        </div>
                        
                        <!-- Absolutely centered title with more breathing room -->
                        <div class="position-absolute start-0 end-0 top-50 translate-middle-y px-5">
                            <h5 class="mb-0 text-center">Care Services Summary</h5>
                        </div>
                        
                        <!-- Right arrow at edge -->
                        <div class="position-absolute end-0 top-50 translate-middle-y me-3">
                            <button class="btn btn-sm btn-outline-light" id="nextCategory">
                                <i class="bi bi-chevron-right"></i>
                            </button>
                        </div>
                    </div>
                        <div class="card-body">
                            @if(count($careServicesSummary) > 0)
                                <div id="careServicesCarousel" class="carousel slide" data-bs-ride="false" data-bs-interval="false">
                                    <div class="carousel-inner">
                                        @foreach($careServicesSummary as $categoryId => $categoryData)
                                            <div class="carousel-item {{ $loop->first ? 'active' : '' }}" data-category-id="{{ $categoryId }}">
                                                <div class="table-responsive">
                                                    <table class="table care-services-table">
                                                        <thead>
                                                            <tr>
                                                                <th colspan="3" class="text-center bg-light">
                                                                    <div class="d-flex justify-content-center">
                                                                        <h6 class="mb-0">{{ isset($careCategories->firstWhere('care_category_id', $categoryId)->care_category_name) ? 
                                                                            $careCategories->firstWhere('care_category_id', $categoryId)->care_category_name : 'Unknown Category' }}</h6>
                                                                    </div>
                                                                </th>
                                                            </tr>
                                                            <tr>
                                                                <th>Intervention</th>
                                                                <th>Duration</th>
                                                                <th>% of Category</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach($categoryData['interventions'] as $intervention)
                                                                <tr>
                                                                    <td>
                                                                        @if(!$intervention['is_custom'])
                                                                            {{ $intervention['name'] }}
                                                                        @else
                                                                            {{ $intervention['name'] }}
                                                                        @endif
                                                                    </td>
                                                                    <td>{{ $intervention['duration_display'] }}</td>
                                                                    <td>
                                                                        <div class="progress" style="height: 8px;">
                                                                            <div class="progress-bar" role="progressbar" style="width: {{ $intervention['percentage'] }}%;" 
                                                                                aria-valuenow="{{ $intervention['percentage'] }}" aria-valuemin="0" aria-valuemax="100"></div>
                                                                        </div>
                                                                        <small>{{ number_format($intervention['percentage'], 1) }}%</small>
                                                                    </td>
                                                                </tr>
                                                            @endforeach
                                                            @if(empty($categoryData['interventions']))
                                                                <tr>
                                                                    <td colspan="3" class="text-center">No interventions recorded</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                        <tfoot>
                                                            <tr class="table-light">
                                                                <th colspan="2" class="text-end">Category Total:</th>
                                                                <th>{{ $categoryData['total_duration_display'] ?? '0 min' }}</th>
                                                            </tr>
                                                        </tfoot>
                                                    </table>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <div class="text-center mt-3">
                                    <span class="badge rounded-pill bg-primary me-1" id="categoryIndicator">1/{{ count($careServicesSummary) }}</span>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    No care services recorded for this period.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Medical Information -->
                <div class="col-12 mb-3">
                    <div class="row">
                        <!-- Medical Conditions -->
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">Medical Conditions</h5>
                                </div>
                                <div class="card-body">
                                    @if(count($medicalConditions) > 0)
                                        <ul class="list-group">
                                            @foreach($medicalConditions as $condition)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    {{ $condition }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="alert alert-info">
                                            No medical conditions recorded.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <!-- Illnesses -->
                        <div class="col-md-6 mb-3">
                            <div class="card h-100">
                                <div class="card-header">
                                    <h5 class="mb-0">Recent Illnesses</h5>
                                </div>
                                <div class="card-body">
                                    @if(count($illnesses) > 0)
                                        <ul class="list-group">
                                            @foreach($illnesses as $illness)
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    {{ $illness }}
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <div class="alert alert-info">
                                            No illnesses recorded for this period.
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Vital Signs Charts -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Vital Signs History</h5>
                        </div>
                        <div class="card-body">
                            @if(count($chartLabels ?? []) > 0)
                                <div class="row">
                                    <!-- Blood Pressure Chart -->
                                    <div class="col-md-6 mb-4">
                                        <h6 class="text-center">Blood Pressure</h6>
                                        <div class="chart-container">
                                            <canvas id="bloodPressureChart"></canvas>
                                        </div>
                                    </div>
                                    
                                    <!-- Heart Rate Chart -->
                                    <div class="col-md-6 mb-4">
                                        <h6 class="text-center">Heart Rate (bpm)</h6>
                                        <div class="chart-container">
                                            <canvas id="heartRateChart"></canvas>
                                        </div>
                                    </div>
                                    
                                    <!-- Respiratory Rate Chart -->
                                    <div class="col-md-6 mb-4">
                                        <h6 class="text-center">Respiratory Rate (breaths/min)</h6>
                                        <div class="chart-container">
                                            <canvas id="respiratoryRateChart"></canvas>
                                        </div>
                                    </div>
                                    
                                    <!-- Temperature Chart -->
                                    <div class="col-md-6 mb-4">
                                        <h6 class="text-center">Body Temperature (°C)</h6>
                                        <div class="chart-container">
                                            <canvas id="temperatureChart"></canvas>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="alert alert-info">
                                    No vital signs data available for this period.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize charts if data is available
        @if(count($chartLabels) > 0)
            initializeCharts();
        @endif
        
        // Time range change handler
        document.getElementById('timeRange').addEventListener('change', function() {
            const selectedRange = this.value;
            
            // Hide all time range filters
            document.getElementById('weekFilterContainer').classList.add('d-none');
            document.getElementById('monthRangeFilterContainer').classList.add('d-none');
            document.getElementById('yearFilterContainer').classList.add('d-none');
            
            // Show the appropriate filter based on selection
            if (selectedRange === 'weeks') {
                document.getElementById('weekFilterContainer').classList.remove('d-none');
            } else if (selectedRange === 'months') {
                document.getElementById('monthRangeFilterContainer').classList.remove('d-none');
            } else if (selectedRange === 'year') {
                document.getElementById('yearFilterContainer').classList.remove('d-none');
            }
        });
        
        // Initialize charts
        function initializeCharts() {
            // Chart.js configuration
            const chartLabels = @json($chartLabels);
            
            // Blood Pressure Chart
            const bpCtx = document.getElementById('bloodPressureChart').getContext('2d');
            new Chart(bpCtx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Systolic',
                            data: @json($bloodPressureData['systolic'] ?? []),
                            borderColor: 'rgb(255, 99, 132)',
                            backgroundColor: 'rgba(255, 99, 132, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: false
                        },
                        {
                            label: 'Diastolic',
                            data: @json($bloodPressureData['diastolic'] ?? []),
                            borderColor: 'rgb(54, 162, 235)',
                            backgroundColor: 'rgba(54, 162, 235, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: false
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            min: 40,
                            title: {
                                display: true,
                                text: 'mmHg'
                            }
                        }
                    }
                }
            });
            
            // Heart Rate Chart
            const hrCtx = document.getElementById('heartRateChart').getContext('2d');
            new Chart(hrCtx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Heart Rate',
                            data: @json($heartRateData ?? []),
                            borderColor: 'rgb(255, 159, 64)',
                            backgroundColor: 'rgba(255, 159, 64, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            title: {
                                display: true,
                                text: 'bpm'
                            }
                        }
                    }
                }
            });
            
            // Respiratory Rate Chart
            const rrCtx = document.getElementById('respiratoryRateChart').getContext('2d');
            new Chart(rrCtx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Respiratory Rate',
                            data: @json($respiratoryRateData ?? []),
                            borderColor: 'rgb(75, 192, 192)',
                            backgroundColor: 'rgba(75, 192, 192, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            title: {
                                display: true,
                                text: 'breaths/min'
                            }
                        }
                    }
                }
            });
            
            // Temperature Chart
            const tempCtx = document.getElementById('temperatureChart').getContext('2d');
            new Chart(tempCtx, {
                type: 'line',
                data: {
                    labels: chartLabels,
                    datasets: [
                        {
                            label: 'Temperature',
                            data: @json($temperatureData ?? []),
                            borderColor: 'rgb(153, 102, 255)',
                            backgroundColor: 'rgba(153, 102, 255, 0.1)',
                            borderWidth: 2,
                            tension: 0.3,
                            fill: true
                        }
                    ]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            title: {
                                display: true,
                                text: '°C'
                            }
                        }
                    }
                }
            });
        }
    });
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize carousel navigation
    const carousel = document.getElementById('careServicesCarousel');
    const prevButton = document.getElementById('prevCategory');
    const nextButton = document.getElementById('nextCategory');
    const categoryIndicator = document.getElementById('categoryIndicator');
    
    if (carousel && prevButton && nextButton) {
        const carouselInstance = new bootstrap.Carousel(carousel, {
            interval: false,  // Don't auto-rotate
            wrap: true       // Loop around
        });
        
        // Navigate carousel with buttons
        prevButton.addEventListener('click', function() {
            carouselInstance.prev();
            updateCategoryIndicator();
        });
        
        nextButton.addEventListener('click', function() {
            carouselInstance.next();
            updateCategoryIndicator();
        });
        
        // Update category indicator
        function updateCategoryIndicator() {
            const items = document.querySelectorAll('#careServicesCarousel .carousel-item');
            const currentItem = document.querySelector('#careServicesCarousel .carousel-item.active');
            const currentIndex = Array.from(items).indexOf(currentItem) + 1;
            if (categoryIndicator) {
                categoryIndicator.textContent = currentIndex + '/' + items.length;
            }
        }
        
        // Initialize indicator
        updateCategoryIndicator();
        
        // Listen for carousel events
        carousel.addEventListener('slid.bs.carousel', updateCategoryIndicator);
    }
    
    // Time range change handler
    const timeRangeSelect = document.getElementById('timeRange');
    if (timeRangeSelect) {
        timeRangeSelect.addEventListener('change', function() {
            const selectedRange = this.value;
            
            // Hide all time range filters first
            document.getElementById('weekFilterContainer').style.display = 'none';
            document.getElementById('monthRangeFilterContainer').style.display = 'none';
            document.getElementById('yearFilterContainer').style.display = 'none';
            
            // Show the appropriate filter based on selection
            if (selectedRange === 'weeks') {
                document.getElementById('weekFilterContainer').style.display = '';
            } else if (selectedRange === 'months') {
                document.getElementById('monthRangeFilterContainer').style.display = '';
            } else if (selectedRange === 'year') {
                document.getElementById('yearFilterContainer').style.display = '';
            }
        });
    }
    
    // Initialize charts if data is available
    if (document.getElementById('bloodPressureChart')) {
        initializeCharts();
    }
    
    // Function to initialize all charts
    function initializeCharts() {
        // Chart data from controller - use empty arrays as fallbacks if undefined
        const chartLabels = @json($chartLabels ?? []);
        const bloodPressureData = @json($bloodPressureData ?? ['systolic' => [], 'diastolic' => []]);
        const heartRateData = @json($heartRateData ?? []);
        const respiratoryRateData = @json($respiratoryRateData ?? []);
        const temperatureData = @json($temperatureData ?? []);
        
        // Blood Pressure Chart
        const bpCtx = document.getElementById('bloodPressureChart').getContext('2d');
        new Chart(bpCtx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Systolic',
                        data: bloodPressureData.systolic,
                        borderColor: 'rgb(255, 99, 132)',
                        backgroundColor: 'rgba(255, 99, 132, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: false
                    },
                    {
                        label: 'Diastolic',
                        data: bloodPressureData.diastolic,
                        borderColor: 'rgb(54, 162, 235)',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        min: 40,
                        title: {
                            display: true,
                            text: 'mmHg'
                        }
                    }
                }
            }
        });
        
        // Heart Rate Chart
        const hrCtx = document.getElementById('heartRateChart').getContext('2d');
        new Chart(hrCtx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Heart Rate',
                        data: heartRateData,
                        borderColor: 'rgb(255, 159, 64)',
                        backgroundColor: 'rgba(255, 159, 64, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'bpm'
                        }
                    }
                }
            }
        });
        
        // Respiratory Rate Chart
        const rrCtx = document.getElementById('respiratoryRateChart').getContext('2d');
        new Chart(rrCtx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Respiratory Rate',
                        data: respiratoryRateData,
                        borderColor: 'rgb(75, 192, 192)',
                        backgroundColor: 'rgba(75, 192, 192, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: 'breaths/min'
                        }
                    }
                }
            }
        });
        
        // Temperature Chart
        const tempCtx = document.getElementById('temperatureChart').getContext('2d');
        new Chart(tempCtx, {
            type: 'line',
            data: {
                labels: chartLabels,
                datasets: [
                    {
                        label: 'Temperature',
                        data: temperatureData,
                        borderColor: 'rgb(153, 102, 255)',
                        backgroundColor: 'rgba(153, 102, 255, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: false,
                        title: {
                            display: true,
                            text: '°C'
                        }
                    }
                }
            }
        });
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Time range change handler with consistent display toggling
    const timeRangeSelect = document.getElementById('timeRange');
    const weekFilterContainer = document.getElementById('weekFilterContainer');
    const monthRangeFilterContainer = document.getElementById('monthRangeFilterContainer');
    const yearFilterContainer = document.getElementById('yearFilterContainer');
    
    if (timeRangeSelect) {
        timeRangeSelect.addEventListener('change', function() {
            const selectedRange = this.value;
            
            // First hide all containers consistently
            weekFilterContainer.style.display = 'none';
            monthRangeFilterContainer.style.display = 'none';
            yearFilterContainer.style.display = 'none';
            
            // Remove d-none classes that might be added
            weekFilterContainer.classList.remove('d-none');
            monthRangeFilterContainer.classList.remove('d-none');
            yearFilterContainer.classList.remove('d-none');
            
            // Show the appropriate filter based on selection
            if (selectedRange === 'weeks') {
                weekFilterContainer.style.display = '';
            } else if (selectedRange === 'months') {
                monthRangeFilterContainer.style.display = '';
            } else if (selectedRange === 'year') {
                yearFilterContainer.style.display = '';
            }
        });
    }
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Wait for Bootstrap to be loaded
    setTimeout(function() {
        // Initialize carousel navigation
        const carousel = document.getElementById('careServicesCarousel');
        const prevButton = document.getElementById('prevCategory');
        const nextButton = document.getElementById('nextCategory');
        const categoryIndicator = document.getElementById('categoryIndicator');
        
        if (carousel && prevButton && nextButton) {
            try {
                // Create carousel instance with Bootstrap
                const carouselInstance = new bootstrap.Carousel(carousel, {
                    interval: false,  // Don't auto-rotate
                    wrap: true        // Loop around
                });
                
                // Navigate carousel with buttons
                prevButton.addEventListener('click', function() {
                    carouselInstance.prev();
                    updateCategoryIndicator();
                });
                
                nextButton.addEventListener('click', function() {
                    carouselInstance.next();
                    updateCategoryIndicator();
                });
                
                // Update category indicator
                function updateCategoryIndicator() {
                    const items = document.querySelectorAll('#careServicesCarousel .carousel-item');
                    const currentItem = document.querySelector('#careServicesCarousel .carousel-item.active');
                    const currentIndex = Array.from(items).indexOf(currentItem) + 1;
                    if (categoryIndicator) {
                        categoryIndicator.textContent = currentIndex + '/' + items.length;
                    }
                }
                
                // Initialize indicator
                updateCategoryIndicator();
                
                // Listen for carousel events
                carousel.addEventListener('slid.bs.carousel', updateCategoryIndicator);
            } catch (error) {
                console.error("Error initializing carousel:", error);
            }
        }
    }, 500); // Give a small delay to ensure Bootstrap is loaded
});
</script>
<script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>