<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Care Worker Performance</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <style>
        .compact-chart {
            height: 220px !important;
            width: 100% !important;
        }
        .card-body {
            padding: 1rem;
        }
        .card-header {
            padding: 0.75rem 1rem;
        }
        .card-title {
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        h5 {
            font-size: 1.1rem;
        }
        .table th, .table td {
            font-size: clamp(0.8rem, 1vw, 1rem);
        }

        .table th {
            font-weight: bold;
        }

        .table-responsive {
            overflow-x: auto;
        }
        
        tbody td{
            vertical-align: middle;
        }
        th button{
            transform: translateY(-3px);
        }

        tbody td {
        vertical-align: middle;
        }

        th button {
            transform: translateY(-3px);
        }

        #home-content {
            font-size: clamp(0.8rem, 1vw, 1rem);
        }

        #home-content th,
        #home-content td {
            font-size: clamp(0.7rem, 0.9vw, 0.9rem);
        }

        #home-content .card-header,
        #home-content .form-label {
            font-size: clamp(0.9rem, 1.1vw, 1.1rem);
        }

        #home-content .btn {
            font-size: 0.8rem;
        }

        #home-content .form-select,
        #home-content .form-control {
            font-size: 0.8rem;
            padding: 0.25rem 0.5rem;
        }

        #home-content .table {
            font-size: 0.8rem;
        }

        #beneficiaryDetailsRow .form-label {
            font-size: clamp(0.8rem, 1vw, 1rem);
        }

        #beneficiaryDetailsRow .form-control {
            font-size: clamp(0.8rem, 1vw, 1rem);
            padding: 0.3rem 0.5rem;
        }

        #beneficiaryDetailsRow .card-header {
            font-size: clamp(1rem, 1.2vw, 1.2rem);
        }

        .carousel-item table {
            margin-bottom: 0;
        }
        
        .carousel-control-prev,
        .carousel-control-next {
            display: none; /* Hide default carousel controls since we're using custom buttons */
        }
        
        .position-relative .btn-outline-secondary {
            margin-top: 0px;
            padding: 0.1rem 0.4rem;
        }
        
        /* Make intervention names bold in the table */
        .table tbody td:first-child {
            font-weight: 500;
        }
    </style>
</head>
<body>
    @include('components.adminNavbar')
    @include('components.adminSidebar')

    <div class="home-section">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="text-left">
                <strong>CARE WORKER PERFORMANCE</strong>
            </div>
            <button class="btn btn-danger btn-md" id="exportPdfBtn">
                <i class="bi bi-file-earmark-pdf"></i> Export to PDF
            </button>
        </div>
        <div class="container-fluid">
            <div class="row" id="home-content">
                <div class="col-12">
                    <div class="card shadow-sm mb-3">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <div class="d-flex align-items-center">
                                <strong class="me-3">Filters</strong>
                            </div>
                            <div>
                                <button class="btn btn-outline-secondary btn-sm me-2" id="resetFilterBtn" style="display: none;">
                                    <i class="bi bi-arrow-counterclockwise"></i> Reset
                                </button>
                                <button class="btn btn-primary btn-sm" id="applyFilterBtn">Apply Filters</button>
                            </div>
                        </div>
                        <div class="card-body p-2">
                            <form id="filterForm" action="{{ route('admin.careworker.performance.index') }}" method="GET">
                                <div class="row g-2">
                                    <!-- Care Worker Select -->
                                    <div class="col">
                                        <label for="careWorkerSelect" class="form-label">Select Care Worker:</label>
                                        <select class="form-select" id="careWorkerSelect" name="care_worker_id">
                                            <option value="">All Care Workers</option>
                                            @foreach($careWorkers as $worker)
                                                <option value="{{ $worker->id }}" {{ $selectedCareWorkerId == $worker->id ? 'selected' : '' }}>
                                                    {{ $worker->last_name }}, {{ $worker->first_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <!-- Municipalities Select -->
                                    <div class="col">
                                        <label for="municipalitySelect" class="form-label">Select Municipality:</label>
                                        <select class="form-select" id="municipalitySelect" name="municipality_id" {{ $selectedCareWorkerId ? 'disabled' : '' }}>
                                            <option value="">All Municipalities</option>
                                            @foreach($municipalities as $municipality)
                                                <option value="{{ $municipality->municipality_id }}" {{ $selectedMunicipalityId == $municipality->municipality_id ? 'selected' : '' }}>
                                                    {{ $municipality->municipality_name }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <!-- Time Range Select -->
                                    <div class="col">
                                        <label for="timeRange" class="form-label">Time Range:</label>
                                        <select class="form-select" id="timeRange" name="time_range">
                                            <option value="weeks" {{ $selectedTimeRange == 'weeks' ? 'selected' : '' }}>Monthly</option>
                                            <option value="months" {{ $selectedTimeRange == 'months' ? 'selected' : '' }}>Range of Months</option>
                                            <option value="year" {{ $selectedTimeRange == 'year' ? 'selected' : '' }}>Yearly</option>
                                        </select>
                                    </div>

                                    <!-- Week Filter (visible by default) -->
                                    <div class="col {{ $selectedTimeRange != 'weeks' ? 'd-none' : '' }}" id="weekFilterContainer">
                                        <label for="monthSelect" class="form-label">Select Month:</label>
                                        <div class="d-flex">
                                            <select class="form-select" id="monthSelect" name="month" style="width: 60%;">
                                                @for($i = 1; $i <= 12; $i++)
                                                    <option value="{{ $i }}" {{ $selectedMonth == $i ? 'selected' : '' }}>
                                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                                    </option>
                                                @endfor
                                            </select>
                                            <select class="form-select ms-2" id="yearSelect" name="monthly_year" style="width: 40%;">
                                                @foreach($availableYears as $year)
                                                    <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                                        {{ $year }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Month Range Filter (hidden by default) -->
                                    <div class="col {{ $selectedTimeRange != 'months' ? 'd-none' : '' }}" id="monthRangeFilterContainer">
                                        <div class="row g-2">
                                            <div class="col-5">
                                                <label for="startMonth" class="form-label">Start Month:</label>
                                                <select class="form-select" id="startMonth" name="start_month">
                                                    @for($i = 1; $i <= 12; $i++)
                                                        <option value="{{ $i }}" {{ $selectedStartMonth == $i ? 'selected' : '' }}>
                                                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-5">
                                                <label for="endMonth" class="form-label">End Month:</label>
                                                <select class="form-select" id="endMonth" name="end_month">
                                                    @for($i = 1; $i <= 12; $i++)
                                                        <option value="{{ $i }}" {{ $selectedEndMonth == $i ? 'selected' : '' }}>
                                                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-2">
                                                <label for="rangeYearSelect" class="form-label">Year:</label>
                                                <select class="form-select" id="rangeYearSelect" name="range_year">
                                                    @foreach($availableYears as $year)
                                                        <option value="{{ $year }}" {{ $selectedYear == $year ? 'selected' : '' }}>
                                                            {{ $year }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Year Filter (hidden by default) -->
                                    <div class="col {{ $selectedTimeRange != 'year' ? 'd-none' : '' }}" id="yearFilterContainer">
                                        <label for="yearOnlySelect" class="form-label">Select Year:</label>
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
                    
                    @if(!$hasRecords)
                        <div class="alert alert-info">
                            No records found for the selected filters. Please try different filters.
                        </div>
                    @endif
                    
                    <!-- Summary Cards -->
                    <div class="row mb-2">
                        <!-- Total Care Hours -->
                        <div class="col-md-4 mb-2">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title" style="font-size: clamp(1rem, 1.5vw, 1.2rem);">Total Care Hours</h5>
                                    <h2 class="text-primary" style="font-size: clamp(1.5rem, 2vw, 2rem);">
                                        {{ $formattedCareTime['hours'] }} hrs {{ $formattedCareTime['minutes'] > 0 ? $formattedCareTime['minutes'] . ' min' : '' }}
                                    </h2>
                                    <p class="text-muted" style="font-size: clamp(0.8rem, 1vw, 1rem);">{{ $dateRangeLabel }}</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Active Care Workers -->
                        <div class="col-md-4 mb-2">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title" style="font-size: clamp(1rem, 1.5vw, 1.2rem);">Active Care Workers</h5>
                                    <h2 class="text-success" style="font-size: clamp(1.5rem, 2vw, 2rem);">{{ $activeCareWorkersCount }}</h2>
                                    <p class="text-muted" style="font-size: clamp(0.8rem, 1vw, 1rem);">Currently active</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Total Care Services -->
                        <div class="col-md-4 mb-2">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title" style="font-size: clamp(1rem, 1.5vw, 1.2rem);">Total Care Services</h5>
                                    <h2 class="text-warning" style="font-size: clamp(1.5rem, 2vw, 2rem);">{{ $totalInterventions }}</h2>
                                    <p class="text-muted" style="font-size: clamp(0.8rem, 1vw, 1rem);">{{ $dateRangeLabel }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Care Services Summary Section -->
                    <div class="row mb-3" id="careServicesSummaryRow">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header text-center">
                                    <strong>Care Services Summary</strong>
                                </div>
                                <div class="card-body p-2">
                                    <div id="careServicesCarousel" class="carousel slide" data-bs-interval="false">
                                        <div class="carousel-inner">
                                            @foreach($careCategories as $index => $category)
                                                <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                                    <table class="table table-bordered table-sm">
                                                        <thead>
                                                            <tr>
                                                                <th colspan="3" class="text-center bg-light position-relative">
                                                                    <button class="btn btn-sm btn-outline-secondary position-absolute start-0" data-bs-target="#careServicesCarousel" data-bs-slide="prev">
                                                                        <i class="bi bi-chevron-left"></i>
                                                                    </button>
                                                                    {{ $category->care_category_name }}
                                                                    <button class="btn btn-sm btn-outline-secondary position-absolute end-0" data-bs-target="#careServicesCarousel" data-bs-slide="next">
                                                                        <i class="bi bi-chevron-right"></i>
                                                                    </button>
                                                                </th>
                                                            </tr>
                                                            <tr>
                                                                <th>Intervention Implemented</th>
                                                                <th class="text-center">Times Implemented</th>
                                                                <th>Total Hours</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @if(isset($categorySummaries[$category->care_category_id]) && 
                                                                !empty($categorySummaries[$category->care_category_id]['interventions']))
                                                                @foreach($categorySummaries[$category->care_category_id]['interventions'] as $intervention)
                                                                    <tr>
                                                                        <td>{{ $intervention['description'] }}</td>
                                                                        <td class="text-center">{{ $intervention['times_implemented'] }}</td>
                                                                        <td>{{ $intervention['total_hours'] }} hrs {{ $intervention['total_minutes'] > 0 ? $intervention['total_minutes'] . ' min' : '' }}</td>
                                                                    </tr>
                                                                @endforeach
                                                            @else
                                                                <tr>
                                                                    <td colspan="3" class="text-center">None</td>
                                                                </tr>
                                                            @endif
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Main Charts Row -->
                    <div class="row mb-2">
                        <!-- Most Implemented Interventions (half width, taller) -->
                        <div class="col-md-12 mb-2 mx-auto"> <!-- Using col-md-6 and mx-auto for centering -->
                            <div class="card h-100">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0" style="font-size: clamp(1rem, 1.5vw, 1.2rem);">Most Implemented Interventions</h5>
                                </div>
                                <div class="card-body">
                                    <!-- Increase the height to make the chart taller -->
                                    <div style="height: 300px;"> <!-- Taller height for better readability -->
                                        <canvas id="mostImplementedChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Second Charts Row -->
                    <div class="row mb-2">
                        <!-- Time/Care Need Category Chart -->
                        <div class="{{ $selectedCareWorkerId ? 'col-lg-6' : 'col-12' }} mb-2">
                            <div class="card h-100">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0" style="font-size: clamp(1rem, 1.5vw, 1.2rem);">Time Per Care Category</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="timePerCategoryChart" height="{{ $selectedCareWorkerId ? '250' : '300' }}"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Client Care Breakdown - Only shown when a care worker is selected -->
                        @if($selectedCareWorkerId)
                        <div class="col-lg-6 mb-2">
                            <div class="card h-100">
                                <div class="card-header bg-white">
                                    <h5 class="mb-0" style="font-size: clamp(1rem, 1.5vw, 1.2rem);">Client Care Breakdown</h5>
                                </div>
                                <div class="card-body">
                                    <canvas id="clientCareChart" height="250"></canvas>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Care Worker Performance Table -->
                    <div class="row mb-3">
                        <div class="col-12">
                            <div class="card shadow-sm">
                                <div class="card-header bg-white">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="mb-0" style="font-size: clamp(1rem, 1.5vw, 1.2rem);">Care Worker Performance</h5>
                                        <div>
                                            <button class="btn btn-sm btn-outline-secondary sort-btn" data-sort="asc">
                                                <i class="bi bi-sort-alpha-down"></i> A-Z
                                            </button>
                                            <button class="btn btn-sm btn-outline-secondary sort-btn" data-sort="desc">
                                                <i class="bi bi-sort-alpha-up"></i> Z-A
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body p-2">
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-sm">
                                            <thead class="table-light">
                                                <tr>
                                                    <th>Care Worker</th>
                                                    <th class="text-center">Hours Worked</th>
                                                    <th class="text-center">Beneficiary Visits</th>
                                                    <th class="text-center">Interventions Performed</th>
                                                </tr>
                                            </thead>
                                            <tbody id="performanceTableBody">
                                                @forelse($careWorkerPerformance as $worker)
                                                    <tr @if($worker['is_selected']) class="table-primary" @endif>
                                                        <td>{{ $worker['name'] }}</td>
                                                        <td class="text-center">{{ $worker['hours_worked']['formatted_time'] }}</td>
                                                        <td class="text-center">{{ $worker['beneficiary_visits'] }}</td>
                                                        <td class="text-center">{{ $worker['interventions_performed'] }}</td>
                                                    </tr>
                                                @empty
                                                    <tr>
                                                        <td colspan="4" class="text-center">No performance data available</td>
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
        </div>
    </div>
   
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Toggle visibility of filters based on the selected time range
        function updateTimeFilters() {
            const timeRange = document.getElementById('timeRange').value;

            // Hide all filters first
            document.getElementById('weekFilterContainer').classList.add('d-none');
            document.getElementById('monthRangeFilterContainer').classList.add('d-none');
            document.getElementById('yearFilterContainer').classList.add('d-none');

            // Show the appropriate filter
            if (timeRange === 'weeks') {
                document.getElementById('weekFilterContainer').classList.remove('d-none');
            } else if (timeRange === 'months') {
                document.getElementById('monthRangeFilterContainer').classList.remove('d-none');
            } else if (timeRange === 'year') {
                document.getElementById('yearFilterContainer').classList.remove('d-none');
            }
        }

        // Apply filters and update the page content
        function applyFilters() {
            const careWorkerId = document.getElementById('beneficiarySelect').value;
            const careWorkerName = careWorkerId
                ? document.getElementById('beneficiarySelect').options[document.getElementById('beneficiarySelect').selectedIndex].text
                : "All Care Workers";

            const careWorkerPerformanceTable = document.querySelector('.card:has(table)'); // Care Worker Performance table
            const chartHeaders = document.querySelectorAll('.card-header h5'); // Chart headers

            if (careWorkerId) {
                // Hide Care Worker Performance table
                careWorkerPerformanceTable.classList.add('d-none');

                // Update chart headers with the selected care worker's name
                chartHeaders.forEach(header => {
                    header.textContent = `${header.textContent.split('(')[0].trim()} (${careWorkerName})`;
                });
            } else {
                // Show Care Worker Performance table
                careWorkerPerformanceTable.classList.remove('d-none');

                // Reset chart headers to default
                chartHeaders.forEach(header => {
                    header.textContent = header.textContent.split('(')[0].trim();
                });
            }

            // Example logic for applying filters (you can replace this with actual data fetching logic)
            console.log('Filters applied:');
            console.log('Care Worker ID:', careWorkerId || 'All Care Workers');

        // Event listeners
        document.getElementById('timeRange').addEventListener('change', updateTimeFilters);
        document.getElementById('applyFilterBtn').addEventListener('click', applyFilters);

        // Initial setup
        updateTimeFilters();

        // Add reset button functionality
        const resetFilterBtn = document.getElementById('resetFilterBtn');
            resetFilterBtn.addEventListener('click', function() {
                // Reset all filters to default and submit form
                document.getElementById('careWorkerSelect').value = '';
                document.getElementById('municipalitySelect').value = '';
                document.getElementById('timeRange').value = 'weeks';
                document.getElementById('monthSelect').value = new Date().getMonth() + 1;
                document.getElementById('startMonth').value = 1;
                document.getElementById('endMonth').value = 12;
                document.getElementById('yearSelect').value = new Date().getFullYear();
                document.getElementById('rangeYearSelect').value = new Date().getFullYear(); // Add this line
                document.getElementById('yearOnlySelect').value = new Date().getFullYear();
                
                // Update visibility of time range filters
                updateTimeFilters();
                
                // Submit the form
                document.getElementById('filterForm').submit();
            });

        // Check if any filter is non-default and show/hide reset button accordingly
        function checkFiltersModified() {
            const careWorkerSelect = document.getElementById('careWorkerSelect');
            const municipalitySelect = document.getElementById('municipalitySelect');
            const timeRange = document.getElementById('timeRange');
            const monthSelect = document.getElementById('monthSelect');
            const startMonth = document.getElementById('startMonth');
            const endMonth = document.getElementById('endMonth');
            const yearSelect = document.getElementById('yearSelect');
            const rangeYearSelect = document.getElementById('rangeYearSelect'); // Add this line
            const yearOnlySelect = document.getElementById('yearOnlySelect');
            
            const currentMonth = new Date().getMonth() + 1;
            const currentYear = new Date().getFullYear();
            
            // Check if any filter is non-default
            const isModified = 
                careWorkerSelect.value !== '' ||
                municipalitySelect.value !== '' ||
                timeRange.value !== 'weeks' ||
                (timeRange.value === 'weeks' && (parseInt(monthSelect.value) !== currentMonth || parseInt(yearSelect.value) !== currentYear)) ||
                (timeRange.value === 'months' && (parseInt(startMonth.value) !== 1 || parseInt(endMonth.value) !== 12 || parseInt(rangeYearSelect.value) !== currentYear)) || // Updated this line
                (timeRange.value === 'year' && parseInt(yearOnlySelect.value) !== currentYear);
            
            // Show/hide reset button based on whether filters are modified
            resetFilterBtn.style.display = isModified ? 'inline-block' : 'none';
        }

        // Add event listeners to all filter controls to check if they've been modified
        const filterControls = document.querySelectorAll('#filterForm select');
        filterControls.forEach(control => {
            control.addEventListener('change', checkFiltersModified);
        });
        
        // Initial check for filter modifications
        checkFiltersModified();
    }});
</script>

    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>
        // CHART CONFIGURATION
        // Base chart configuration with responsive settings

        document.addEventListener('DOMContentLoaded', function() {
            // Base chart configuration
            const baseChartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 10,
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            };

            // Configuration for bar charts (with grids)
            const barChartOptions = {
                ...baseChartOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            display: true
                        },
                        ticks: {
                            font: {
                                size: 9
                            }
                        },
                        title: {
                            display: true,
                            text: 'Count',
                            font: {
                                size: 10
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 9
                            }
                        }
                    }
                }
            };

            // Configuration for doughnut/pie charts (no grids)
            const circularChartOptions = {
                ...baseChartOptions,
                scales: {
                    y: {
                        display: false,
                        grid: {
                            display: false
                        }
                    },
                    x: {
                        display: false,
                        grid: {
                            display: false
                        }
                    }
                }
            };

            // Most Implemented Interventions Chart
            const mostImplementedLabels = @json(array_column($mostImplementedInterventions, 'intervention_description') ?? []);
            const mostImplementedData = @json(array_column($mostImplementedInterventions, 'count') ?? []);

            new Chart(document.getElementById('mostImplementedChart').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: mostImplementedLabels.length > 0 ? mostImplementedLabels.slice(0, 7) : ['No data available'],
                    datasets: [{
                        label: 'Times Implemented',
                        data: mostImplementedData.length > 0 ? mostImplementedData.slice(0, 7) : [0],
                        backgroundColor: [
                            'rgba(54, 162, 235, 0.8)',
                            'rgba(75, 192, 192, 0.8)',
                            'rgba(255, 206, 86, 0.8)',
                            'rgba(255, 99, 132, 0.8)',
                            'rgba(153, 102, 255, 0.8)',
                            'rgba(255, 159, 64, 0.8)',
                            'rgba(201, 203, 207, 0.8)'
                        ],
                        borderColor: [
                            'rgb(54, 162, 235)',
                            'rgb(75, 192, 192)',
                            'rgb(255, 206, 86)',
                            'rgb(255, 99, 132)',
                            'rgb(153, 102, 255)',
                            'rgb(255, 159, 64)',
                            'rgb(201, 203, 207)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    indexAxis: 'y', // Horizontal bar chart
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            beginAtZero: true,
                            grid: { 
                                display: true,
                                color: 'rgba(0, 0, 0, 0.05)'
                            },
                            ticks: {
                                precision: 0, // Show only whole numbers
                                font: {
                                    size: 12 // Larger font for x axis
                                }
                            }
                        },
                        y: {
                            grid: {
                                display: false
                            },
                            ticks: {
                                font: {
                                    size: 15, // Larger font size for intervention names
                                },
                                callback: function(value, index, values) {
                                    // Truncate long labels
                                    const label = this.getLabelForValue(value);
                                    if (label.length > 50) {
                                        return label.substring(0, 50) + '...';
                                    }
                                    return label;
                                }
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: false
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 13
                            },
                            callbacks: {
                                title: function(tooltipItems) {
                                    return tooltipItems[0].label; // Show full intervention name
                                },
                                label: function(context) {
                                    return `${context.parsed.x} times implemented`;
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Toggle visibility of filters based on the selected time range
            function updateTimeFilters() {
                const timeRange = document.getElementById('timeRange').value;

                // Hide all filters first
                document.getElementById('weekFilterContainer').classList.add('d-none');
                document.getElementById('monthRangeFilterContainer').classList.add('d-none');
                document.getElementById('yearFilterContainer').classList.add('d-none');

                // Show the appropriate filter
                if (timeRange === 'weeks') {
                    document.getElementById('weekFilterContainer').classList.remove('d-none');
                } else if (timeRange === 'months') {
                    document.getElementById('monthRangeFilterContainer').classList.remove('d-none');
                } else if (timeRange === 'year') {
                    document.getElementById('yearFilterContainer').classList.remove('d-none');
                }
            }

            // Handle care worker selection
            function handleCareWorkerSelection() {
                const careWorkerSelect = document.getElementById('careWorkerSelect');
                const municipalitySelect = document.getElementById('municipalitySelect');
                
                if (careWorkerSelect.value) {
                    municipalitySelect.disabled = true;
                } else {
                    municipalitySelect.disabled = false;
                }
            }

            // Submit form when Apply button is clicked
            function applyFilters() {
                document.getElementById('filterForm').submit();
            }

            // Event listeners
            document.getElementById('timeRange').addEventListener('change', updateTimeFilters);
            document.getElementById('careWorkerSelect').addEventListener('change', handleCareWorkerSelection);
            document.getElementById('applyFilterBtn').addEventListener('click', applyFilters);
            
            // Initial setup
            updateTimeFilters();
            handleCareWorkerSelection();
            
            // CHART CONFIGURATION
            // Base chart configuration with responsive settings
            const baseChartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 10,
                            font: { size: 10 }
                        }
                    }
                }
            };

            // Configuration for bar charts
            const barChartOptions = {
                ...baseChartOptions,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { display: true },
                        ticks: { font: { size: 9 } },
                        title: {
                            display: true,
                            text: 'Count',
                            font: { size: 10 }
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: { font: { size: 9 } }
                    }
                }
            };

            // Configuration for doughnut/pie charts
            const circularChartOptions = {
                ...baseChartOptions,
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            boxWidth: 10,
                            font: { size: 10 }
                        }
                    }
                },
                cutout: '50%'
            };

            // Time Per Category Chart
            const categoryLabels = @json(array_column($categoryTimeBreakdown, 'category_name') ?? []);
            const categoryMinutes = @json(array_column($categoryTimeBreakdown, 'total_minutes') ?? []);
            const categoryFormattedTimes = @json(array_column($categoryTimeBreakdown, 'formatted_time') ?? []);

            new Chart(document.getElementById('timePerCategoryChart').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: categoryLabels.length > 0 ? categoryLabels : ['No data available'],
                    datasets: [{
                        data: categoryMinutes.length > 0 ? categoryMinutes : [0],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)',
                            'rgba(255, 159, 64, 0.7)',
                            'rgba(201, 203, 207, 0.7)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    ...circularChartOptions,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                font: { size: 11 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return categoryFormattedTimes[context.dataIndex];
                                }
                            }
                        }
                    }
                }
            });

            @if($selectedCareWorkerId)
            // Client Care Breakdown Chart
            const clientLabels = @json(array_column($clientCareBreakdown, 'beneficiary_name') ?? []);
            const clientMinutes = @json(array_column($clientCareBreakdown, 'total_minutes') ?? []);
            const clientFormattedTimes = @json(array_column($clientCareBreakdown, 'formatted_time') ?? []);

            new Chart(document.getElementById('clientCareChart').getContext('2d'), {
                type: 'pie',
                data: {
                    labels: clientLabels.length > 0 ? clientLabels : ['No data available'],
                    datasets: [{
                        data: clientMinutes.length > 0 ? clientMinutes : [0],
                        backgroundColor: [
                            'rgba(255, 99, 132, 0.7)',
                            'rgba(54, 162, 235, 0.7)',
                            'rgba(255, 206, 86, 0.7)',
                            'rgba(75, 192, 192, 0.7)',
                            'rgba(153, 102, 255, 0.7)',
                            'rgba(255, 159, 64, 0.7)',
                            'rgba(201, 203, 207, 0.7)'
                        ],
                        borderWidth: 1
                    }]
                },
                options: {
                    ...circularChartOptions,
                    plugins: {
                        legend: {
                            position: 'right',
                            labels: {
                                boxWidth: 12,
                                font: { size: 11 }
                            }
                        },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return clientFormattedTimes[context.dataIndex];
                                }
                            }
                        }
                    }
                }
            });
            @endif

            // Sorting functions for the performance table
            const sortButtons = document.querySelectorAll('.sort-btn');
            if (sortButtons) {
                sortButtons.forEach(button => {
                    button.addEventListener('click', function() {
                        const sortDirection = this.getAttribute('data-sort');
                        sortTable(sortDirection);
                    });
                });
            }
            
            function sortTable(direction) {
                const tableBody = document.getElementById('performanceTableBody');
                if (!tableBody) return; // Safety check
                
                const rows = Array.from(tableBody.querySelectorAll('tr'));
                if (rows.length <= 1) return; // Don't sort if only one or zero rows
                
                // Sort the rows
                rows.sort((a, b) => {
                    // Skip empty rows or rows with less than 1 cell
                    if (!a.cells[0] || !b.cells[0]) return 0;
                    
                    const nameA = a.cells[0].textContent.trim().toLowerCase();
                    const nameB = b.cells[0].textContent.trim().toLowerCase();
                    
                    if (direction === 'asc') {
                        return nameA.localeCompare(nameB);
                    } else {
                        return nameB.localeCompare(nameA);
                    }
                });
                
                // Remove existing rows
                while (tableBody.firstChild) {
                    tableBody.removeChild(tableBody.firstChild);
                }
                
                // Append sorted rows
                rows.forEach(row => tableBody.appendChild(row));
            }
            
            // Sort table alphabetically (A-Z) by default on page load
            sortTable('asc');
                    
        });

        // Handle PDF export button - replace the existing event listener
        document.getElementById('exportPdfBtn').addEventListener('click', function() {
            // Create a form to submit the current filter values
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = '{{ route("admin.export.careworker.performance.pdf") }}';
            form.style.display = 'none';
            
            // Add CSRF token
            const csrfToken = document.createElement('input');
            csrfToken.type = 'hidden';
            csrfToken.name = '_token';
            csrfToken.value = '{{ csrf_token() }}';
            form.appendChild(csrfToken);
            
            // Copy all filter values from the filter form
            const filterForm = document.getElementById('filterForm');
            const filterInputs = filterForm.querySelectorAll('select, input');
            
            filterInputs.forEach(input => {
                const hiddenField = document.createElement('input');
                hiddenField.type = 'hidden';
                hiddenField.name = input.name;
                hiddenField.value = input.value;
                form.appendChild(hiddenField);
            });
            
            // Append form to body and submit
            document.body.appendChild(form);
            form.submit();
        });

        </script>
</body>
</html>