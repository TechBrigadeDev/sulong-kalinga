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
    @include('components.beneficiaryPortalNavbar')
    @include('components.beneficiaryPortalSidebar')

    <div class="home-section">
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="text-left">CARE PLAN OVERVIEW</div>
            <a href="{{ route('beneficiary.care.plan.index') }}" class="btn btn-primary">
                <i class="bi bi-file-earmark-text"></i> View All Care Plans
            </a>
        </div>
        <div class="container-fluid">
            <div class="row p-1" id="home-content">
                <!-- Beneficiary Information with Avatar -->
                <div class="col-12 pt-3">
                    <div class="beneficiary-info">
                        <img src="https://ui-avatars.com/api/?name=Maria+Dela+Cruz&background=4361ee&color=fff&size=100" 
                             alt="Beneficiary Avatar" class="beneficiary-avatar">
                        <div class="beneficiary-details">
                            <div class="beneficiary-name">Maria Dela Cruz</div>
                            <div class="beneficiary-meta d-flex flex-wrap gap-3">
                                <div class="beneficiary-meta-item">
                                    <span class="beneficiary-meta-label">Age:</span>
                                    <span>72</span>
                                </div>
                                <div class="beneficiary-meta-item">
                                    <span class="beneficiary-meta-label">Gender:</span>
                                    <span>Female</span>
                                </div>
                                <div class="beneficiary-meta-item">
                                    <span class="beneficiary-meta-label">Category:</span>
                                    <span>Frail Elderly</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Filters -->
                <div class="col-12">
                    <div class="card filter-card">
                        <div class="card-header d-flex justify-content-between align-items-center p-3">
                            <strong>Filters</strong>
                            <button type="submit" class="btn btn-sm btn-light" form="filterForm">
                                <i class="bi bi-funnel-fill"></i> Apply Filters
                            </button>
                        </div>
                        <div class="card-body p-2">
                            <form id="filterForm">
                                <div class="row g-2 align-items-center">
                                    <div class="col-md-4">
                                        <label for="timeRange" class="form-label">Time Range:</label>
                                        <select class="form-select" id="timeRange" name="time_range">
                                            <option value="weeks" selected>Monthly</option>
                                            <option value="months">Range of Months</option>
                                            <option value="year">Yearly</option>
                                        </select>
                                    </div>
                                    
                                    <!-- Week Filter (visible by default) -->
                                    <div class="col-md-4" id="weekFilterContainer">
                                        <label for="monthSelect" class="form-label">Select Month:</label>
                                        <div class="d-flex">
                                            <select class="form-select" id="monthSelect" name="month" style="width: 60%;">
                                                @for($i = 1; $i <= 12; $i++)
                                                    <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>
                                                        {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                                    </option>
                                                @endfor
                                            </select>
                                            <select class="form-select ms-2" id="yearSelect" name="monthly_year" style="width: 40%;">
                                                @for($year = date('Y'); $year >= 2020; $year--)
                                                    <option value="{{ $year }}" {{ date('Y') == $year ? 'selected' : '' }}>
                                                        {{ $year }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Month Range Filter (hidden by default) -->
                                    <div class="col-md-8 d-none" id="monthRangeFilterContainer">
                                        <div class="row g-2">
                                            <div class="col-12 col-sm-6 col-md-5">
                                                <label for="startMonth" class="form-label">Start Month:</label>
                                                <select class="form-select" id="startMonth" name="start_month">
                                                    @for($i = 1; $i <= 12; $i++)
                                                        <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>
                                                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-12 col-sm-6 col-md-5">
                                                <label for="endMonth" class="form-label">End Month:</label>
                                                <select class="form-select" id="endMonth" name="end_month">
                                                    @for($i = 1; $i <= 12; $i++)
                                                        <option value="{{ $i }}" {{ date('n') == $i ? 'selected' : '' }}>
                                                            {{ date('F', mktime(0, 0, 0, $i, 1)) }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                            <div class="col-12 col-sm-12 col-md-2">
                                                <label for="rangeYearSelect" class="form-label">Year:</label>
                                                <select class="form-select" id="rangeYearSelect" name="range_year">
                                                    @for($year = date('Y'); $year >= 2020; $year--)
                                                        <option value="{{ $year }}" {{ date('Y') == $year ? 'selected' : '' }}>
                                                            {{ $year }}
                                                        </option>
                                                    @endfor
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Year Filter (hidden by default) -->
                                    <div class="col-md-4 d-none" id="yearFilterContainer">
                                        <label for="yearOnlySelect" class="form-label">Select Year:</label>
                                        <select class="form-select" id="yearOnlySelect" name="year">
                                            @for($year = date('Y'); $year >= 2020; $year--)
                                                <option value="{{ $year }}" {{ date('Y') == $year ? 'selected' : '' }}>
                                                    {{ $year }}
                                                </option>
                                            @endfor
                                        </select>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Summary Cards -->
                <div class="col-12">
                    <div class="row g-3">
                        <div class="col-6 mb-3">
                            <div class="card summary-card h-100">
                                <div class="value">24.5 hrs</div>
                                <div class="label">Total Care Hours</div>
                                <div class="text-muted small">This month</div>
                            </div>
                        </div>
                        <div class="col-6 mb-3">
                            <div class="card summary-card h-100">
                                <div class="value">14</div>
                                <div class="label">Completed Interventions</div>
                                <div class="text-muted small">This month</div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Care Services Summary -->
                <div class="col-12">
                    <div class="card">
                        <div class="card-header text-center">
                            <strong>Care Services Summary</strong>
                        </div>
                        <div class="card-body p-1">
                            <div id="careServicesCarousel" class="carousel slide" data-bs-interval="false">
                                <div class="carousel-inner">
                                    <div class="carousel-item active">
                                        <table class="table table-bordered table-sm care-services-table">
                                            <thead>
                                                <tr>
                                                    <th colspan="3" class="text-center bg-light position-relative">
                                                        <button class="btn btn-sm btn-outline-secondary position-absolute start-0" style="transform: translateY(-3px);" data-bs-target="#careServicesCarousel" data-bs-slide="prev">
                                                            <i class="bi bi-chevron-left"></i>
                                                        </button>
                                                        Medical Monitoring
                                                        <button class="btn btn-sm btn-outline-secondary position-absolute end-0"style="transform: translateY(-3px);"  data-bs-target="#careServicesCarousel" data-bs-slide="next">
                                                            <i class="bi bi-chevron-right"></i>
                                                        </button>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>Intervention Implemented</th>
                                                    <th class="text-center">Times Implemented</th>
                                                    <th class="text-center">Total Hours</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="align-top">Blood Pressure Monitoring</td>
                                                    <td class="text-center align-middle">8</td>
                                                    <td class="text-center align-middle">4.5 hrs</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Medication Administration</td>
                                                    <td class="text-center align-middle">12</td>
                                                    <td class="text-center align-middle">6.0 hrs</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Glucose Level Check</td>
                                                    <td class="text-center align-middle">4</td>
                                                    <td class="text-center align-middle">2.0 hrs</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="carousel-item">
                                        <table class="table table-bordered table-sm care-services-table">
                                            <thead>
                                                <tr>
                                                    <th colspan="3" class="text-center bg-light position-relative">
                                                        <button class="btn btn-sm btn-outline-secondary position-absolute start-0" data-bs-target="#careServicesCarousel" data-bs-slide="prev">
                                                            <i class="bi bi-chevron-left"></i>
                                                        </button>
                                                        Physical Therapy
                                                        <button class="btn btn-sm btn-outline-secondary position-absolute end-0" data-bs-target="#careServicesCarousel" data-bs-slide="next">
                                                            <i class="bi bi-chevron-right"></i>
                                                        </button>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>Intervention Implemented</th>
                                                    <th class="text-center">Times Implemented</th>
                                                    <th class="text-center">Total Hours</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="align-top">Lower Body Exercises</td>
                                                    <td class="text-center align-middle">6</td>
                                                    <td class="text-center align-middle">8.5 hrs</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Balance Training</td>
                                                    <td class="text-center align-middle">4</td>
                                                    <td class="text-center align-middle">5.0 hrs</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="carousel-item">
                                        <table class="table table-bordered table-sm care-services-table">
                                            <thead>
                                                <tr>
                                                    <th colspan="3" class="text-center bg-light position-relative">
                                                        <button class="btn btn-sm btn-outline-secondary position-absolute start-0" data-bs-target="#careServicesCarousel" data-bs-slide="prev">
                                                            <i class="bi bi-chevron-left"></i>
                                                        </button>
                                                        Nutritional Support
                                                        <button class="btn btn-sm btn-outline-secondary position-absolute end-0" data-bs-target="#careServicesCarousel" data-bs-slide="next">
                                                            <i class="bi bi-chevron-right"></i>
                                                        </button>
                                                    </th>
                                                </tr>
                                                <tr>
                                                    <th>Intervention Implemented</th>
                                                    <th class="text-center">Times Implemented</th>
                                                    <th class="text-center">Total Hours</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr>
                                                    <td class="align-top">Dietary Assessment</td>
                                                    <td class="text-center align-middle">2</td>
                                                    <td class="text-center align-middle">3.0 hrs</td>
                                                </tr>
                                                <tr>
                                                    <td class="align-top">Meal Planning</td>
                                                    <td class="text-center align-middle">3</td>
                                                    <td class="text-center align-middle">4.5 hrs</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Vital Signs Charts -->
                <div class="col-12">
                    <div class="row">
                        <!-- Blood Pressure Chart -->
                        <div class="col-lg-6 col-md-12">
                            <div class="card">
                                <div class="card-header" style="background: linear-gradient(135deg, var(--accent-2), var(--accent-1));">
                                    <strong>Blood Pressure</strong>
                                </div>
                                <div class="card-body p-2">
                                    <div class="chart-container">
                                        <canvas id="bloodPressureChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Heart Rate Chart -->
                        <div class="col-lg-6 col-md-12">
                            <div class="card">
                                <div class="card-header" style="background: linear-gradient(135deg, var(--secondary-2), var(--secondary-1));">
                                    <strong>Heart Rate</strong>
                                </div>
                                <div class="card-body p-2">
                                    <div class="chart-container">
                                        <canvas id="heartRateChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Respiratory Rate Chart -->
                        <div class="col-lg-6 col-md-12">
                            <div class="card">
                                <div class="card-header" style="background: linear-gradient(135deg, #38b000, #70e000);">
                                    <strong>Respiratory Rate</strong>
                                </div>
                                <div class="card-body p-2">
                                    <div class="chart-container">
                                        <canvas id="respiratoryRateChart"></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Temperature Chart -->
                        <div class="col-lg-6 col-md-12">
                            <div class="card">
                                <div class="card-header" style="background: linear-gradient(135deg, #ffaa00, #ffbe0b);">
                                    <strong>Temperature</strong>
                                </div>
                                <div class="card-body p-2">
                                    <div class="chart-container">
                                        <canvas id="temperatureChart"></canvas>
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
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialize charts
        let bloodPressureChart, heartRateChart, respiratoryRateChart, temperatureChart;
        initializeCharts();
        
        // Initialize carousel
        const careServicesCarousel = new bootstrap.Carousel(document.getElementById('careServicesCarousel'), {
            interval: false // Don't auto-rotate
        });
        
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
        
        // Form submission handler
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault();
            applyFilters();
        });
        
        // Function to apply filters and update charts
        function applyFilters() {
            const timeRange = document.getElementById('timeRange').value;
            let month, year, startMonth, endMonth;
            
            // Get filter values based on selected time range
            if (timeRange === 'weeks') {
                month = parseInt(document.getElementById('monthSelect').value);
                year = parseInt(document.getElementById('yearSelect').value);
                updateChartsWithMonthlyData(month, year);
            } else if (timeRange === 'months') {
                startMonth = parseInt(document.getElementById('startMonth').value);
                endMonth = parseInt(document.getElementById('endMonth').value);
                year = parseInt(document.getElementById('rangeYearSelect').value);
                updateChartsWithRangeData(startMonth, endMonth, year);
            } else if (timeRange === 'year') {
                year = parseInt(document.getElementById('yearOnlySelect').value);
                updateChartsWithYearlyData(year);
            }
            
            // Update summary cards (dummy data)
            document.querySelector('.summary-card .value').textContent = '24.5 hrs';
            document.querySelectorAll('.summary-card .value')[1].textContent = '14';
        }
        
        // Function to generate monthly data
        function generateMonthlyData(month, year) {
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                               'July', 'August', 'September', 'October', 'November', 'December'];
            const daysInMonth = new Date(year, month, 0).getDate();
            const labels = [];
            const bpSystolic = [];
            const bpDiastolic = [];
            const hrData = [];
            const rrData = [];
            const tempData = [];
            
            // Generate weekly data points (4-5 per month)
            const weeks = Math.ceil(daysInMonth / 7);
            for (let i = 1; i <= weeks; i++) {
                const day = i * 7 > daysInMonth ? daysInMonth : i * 7;
                labels.push(`${monthNames[month-1]} ${day}`);
                
                // Generate realistic dummy data with some variation
                bpSystolic.push(Math.floor(Math.random() * 20) + 120);
                bpDiastolic.push(Math.floor(Math.random() * 15) + 70);
                hrData.push(Math.floor(Math.random() * 15) + 65);
                rrData.push(Math.floor(Math.random() * 5) + 15);
                tempData.push(36.5 + (Math.random() * 0.5));
            }
            
            return {
                labels,
                bpSystolic,
                bpDiastolic,
                hrData,
                rrData,
                tempData
            };
        }
        
        // Function to update charts with monthly data
        function updateChartsWithMonthlyData(month, year) {
            const data = generateMonthlyData(month, year);
            
            // Update Blood Pressure Chart
            bloodPressureChart.data.labels = data.labels;
            bloodPressureChart.data.datasets[0].data = data.bpSystolic;
            bloodPressureChart.data.datasets[1].data = data.bpDiastolic;
            bloodPressureChart.update();
            
            // Update Heart Rate Chart
            heartRateChart.data.labels = data.labels;
            heartRateChart.data.datasets[0].data = data.hrData;
            heartRateChart.update();
            
            // Update Respiratory Rate Chart
            respiratoryRateChart.data.labels = data.labels;
            respiratoryRateChart.data.datasets[0].data = data.rrData;
            respiratoryRateChart.update();
            
            // Update Temperature Chart
            temperatureChart.data.labels = data.labels;
            temperatureChart.data.datasets[0].data = data.tempData;
            temperatureChart.update();
        }
        
        // Function to update charts with range data
        function updateChartsWithRangeData(startMonth, endMonth, year) {
            const monthNames = ['January', 'February', 'March', 'April', 'May', 'June', 
                               'July', 'August', 'September', 'October', 'November', 'December'];
            const labels = [];
            const bpSystolic = [];
            const bpDiastolic = [];
            const hrData = [];
            const rrData = [];
            const tempData = [];
            
            // Generate data for each month in range
            for (let m = startMonth; m <= endMonth; m++) {
                labels.push(monthNames[m-1]);
                
                // Generate realistic dummy data with some variation
                bpSystolic.push(Math.floor(Math.random() * 20) + 120);
                bpDiastolic.push(Math.floor(Math.random() * 15) + 70);
                hrData.push(Math.floor(Math.random() * 15) + 65);
                rrData.push(Math.floor(Math.random() * 5) + 15);
                tempData.push(36.5 + (Math.random() * 0.5));
            }
            
            // Update charts
            bloodPressureChart.data.labels = labels;
            bloodPressureChart.data.datasets[0].data = bpSystolic;
            bloodPressureChart.data.datasets[1].data = bpDiastolic;
            bloodPressureChart.update();
            
            heartRateChart.data.labels = labels;
            heartRateChart.data.datasets[0].data = hrData;
            heartRateChart.update();
            
            respiratoryRateChart.data.labels = labels;
            respiratoryRateChart.data.datasets[0].data = rrData;
            respiratoryRateChart.update();
            
            temperatureChart.data.labels = labels;
            temperatureChart.data.datasets[0].data = tempData;
            temperatureChart.update();
        }
        
        // Function to update charts with yearly data
        function updateChartsWithYearlyData(year) {
            const labels = [];
            const bpSystolic = [];
            const bpDiastolic = [];
            const hrData = [];
            const rrData = [];
            const tempData = [];
            
            // Generate data for each quarter
            for (let q = 1; q <= 4; q++) {
                labels.push(`Q${q} ${year}`);
                
                // Generate realistic dummy data with some variation
                bpSystolic.push(Math.floor(Math.random() * 20) + 120);
                bpDiastolic.push(Math.floor(Math.random() * 15) + 70);
                hrData.push(Math.floor(Math.random() * 15) + 65);
                rrData.push(Math.floor(Math.random() * 5) + 15);
                tempData.push(36.5 + (Math.random() * 0.5));
            }
            
            // Update charts
            bloodPressureChart.data.labels = labels;
            bloodPressureChart.data.datasets[0].data = bpSystolic;
            bloodPressureChart.data.datasets[1].data = bpDiastolic;
            bloodPressureChart.update();
            
            heartRateChart.data.labels = labels;
            heartRateChart.data.datasets[0].data = hrData;
            heartRateChart.update();
            
            respiratoryRateChart.data.labels = labels;
            respiratoryRateChart.data.datasets[0].data = rrData;
            respiratoryRateChart.update();
            
            temperatureChart.data.labels = labels;
            temperatureChart.data.datasets[0].data = tempData;
            temperatureChart.update();
        }
        
        function initializeCharts() {
            // Common chart options
            const chartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                            font: {
                                size: 10
                            }
                        }
                    },
                    tooltip: {
                        bodyFont: {
                            size: 10
                        },
                        titleFont: {
                            size: 12
                        }
                    }
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    },
                    y: {
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    }
                },
                elements: {
                    line: {
                        tension: 0.3,
                        borderWidth: 2
                    },
                    point: {
                        radius: 3,
                        hoverRadius: 5,
                        backgroundColor: 'white',
                        borderWidth: 2
                    }
                }
            };
            
            // Blood Pressure Chart
            const bpCtx = document.getElementById('bloodPressureChart').getContext('2d');
            bloodPressureChart = new Chart(bpCtx, {
                type: 'line',
                data: {
                    labels: ['May 1', 'May 8', 'May 15', 'May 22', 'May 29', 'Jun 5', 'Jun 12'],
                    datasets: [{
                        label: 'Systolic',
                        data: [132, 128, 130, 135, 125, 128, 126],
                        borderColor: '#f72585',
                        backgroundColor: 'rgba(247, 37, 133, 0.1)',
                        fill: true
                    }, {
                        label: 'Diastolic',
                        data: [85, 82, 80, 88, 78, 82, 80],
                        borderColor: '#7209b7',
                        backgroundColor: 'rgba(114, 9, 183, 0.1)',
                        fill: true
                    }]
                },
                options: chartOptions
            });
            
            // Heart Rate Chart
            const hrCtx = document.getElementById('heartRateChart').getContext('2d');
            heartRateChart = new Chart(hrCtx, {
                type: 'line',
                data: {
                    labels: ['May 1', 'May 8', 'May 15', 'May 22', 'May 29', 'Jun 5', 'Jun 12'],
                    datasets: [{
                        label: 'Heart Rate',
                        data: [72, 75, 74, 78, 76, 72, 70],
                        borderColor: '#4895ef',
                        backgroundColor: 'rgba(72, 149, 239, 0.1)',
                        fill: true
                    }]
                },
                options: chartOptions
            });
            
            // Respiratory Rate Chart
            const rrCtx = document.getElementById('respiratoryRateChart').getContext('2d');
            respiratoryRateChart = new Chart(rrCtx, {
                type: 'line',
                data: {
                    labels: ['May 1', 'May 8', 'May 15', 'May 22', 'May 29', 'Jun 5', 'Jun 12'],
                    datasets: [{
                        label: 'Respiratory Rate',
                        data: [16, 17, 16, 18, 17, 16, 15],
                        borderColor: '#38b000',
                        backgroundColor: 'rgba(56, 176, 0, 0.1)',
                        fill: true
                    }]
                },
                options: chartOptions
            });
            
            // Temperature Chart
            const tempCtx = document.getElementById('temperatureChart').getContext('2d');
            temperatureChart = new Chart(tempCtx, {
                type: 'line',
                data: {
                    labels: ['May 1', 'May 8', 'May 15', 'May 22', 'May 29', 'Jun 5', 'Jun 12'],
                    datasets: [{
                        label: 'Temperature (°C)',
                        data: [36.5, 36.6, 36.7, 36.8, 36.5, 36.6, 36.4],
                        borderColor: '#ffaa00',
                        backgroundColor: 'rgba(255, 170, 0, 0.1)',
                        fill: true
                    }]
                },
                options: chartOptions
            });
        }
    });
    </script>
</body>
</html>