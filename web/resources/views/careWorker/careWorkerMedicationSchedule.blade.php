<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Medication Schedule</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        /* Card Design */
        .card {
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
            border: 1px solid rgba(0,0,0,0.07);
            margin-bottom: 1.5rem;
        }
        
        .card-header {
            padding: 0.8rem 1.25rem;
            background-color: #f8f9fc;
            border-bottom: 1px solid rgba(0,0,0,0.07);
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }
        
        .section-heading {
            font-weight: 600;
            font-size: 1.1rem;
            color: #333;
            margin-bottom: 0;
        }
        
        /* Search Bar Enhancements */
        .search-container {
            position: relative;
            margin-bottom: 1rem;
            display: flex;
        }
        
        .search-container i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
            transition: opacity 0.2s ease;
        }
        
        .search-container .search-input:focus + i,
        .search-container .search-input:not(:placeholder-shown) + i {
            opacity: 0;
        }
        
        .search-input {
            padding-left: 35px;
            border-radius: 6px 0 0 6px;
            border: 1px solid #dee2e6;
            border-right: none;
            height: 38px;
            flex: 1;
        }
        
        .search-btn {
            border-radius: 0 6px 6px 0;
            background-color: #4e73df;
            color: white;
            border: none;
            padding: 0 15px;
        }
        
        /* Filter Bar */
        .filter-bar {
            display: flex;
            gap: 15px;
            margin-bottom: 15px;
            flex-wrap: wrap;
            justify-content: flex-end;
        }
        
        .filter-group {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .filter-label {
            font-size: 0.9rem;
            font-weight: 500;
            margin-bottom: 0;
            white-space: nowrap;
        }
        
        /* Select dropdown styling */
        .select-container {
            position: relative;
        }
        
        .select-container::after {
            content: "";
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            width: 0;
            height: 0;
            border-left: 5px solid transparent;
            border-right: 5px solid transparent;
            border-top: 5px solid #666;
            pointer-events: none;
        }
        
        .select-container select {
            padding-right: 30px;
            appearance: none;
            -webkit-appearance: none;
            -moz-appearance: none;
            min-width: 120px;
        }
        
        /* Table Styling */
        .table-responsive {
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.07);
        }
        
        .table {
            margin-bottom: 0;
        }
        
        .table thead {
            background-color: #4e73df;
            color: white;
        }
        
        .table thead th {
            font-weight: 500;
            border-bottom: none;
            padding: 12px 15px;
        }
        
        .table tbody tr:nth-of-type(odd) {
            background-color: #f8f9fc;
        }
        
        .table td {
            vertical-align: middle;
            padding: 12px 15px;
        }
        
        /* Medication time badges */
        .badge-time {
            background-color: #e8f4fd;
            color: #0d6efd;
            padding: 4px 10px;
            border-radius: 20px;
            font-weight: 500;
            font-size: 0.75rem;
            margin-right: 6px;
            margin-bottom: 4px;
            display: inline-block;
            border: 1px solid rgba(13, 110, 253, 0.2);
        }
        
        /* Beneficiary row styling */
        .beneficiary-name {
            font-weight: 700;
            font-size: 1.1rem;
            color: #2e59d9;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 6px 0;
            border-bottom: 2px solid #e0e4ff;
            margin-bottom: 8px;
            letter-spacing: 0.01em;
        }

        .beneficiary-icon {
            color: white;
            font-size: 1rem;
            background-color: #4e73df;
            padding: 5px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 28px;
            height: 28px;
        }

        /* Add a subtle highlight to the entire beneficiary row */
        .beneficiary-start {
            border-top: 3px solid #e6e9ff;
            box-shadow: 0 2px 4px rgba(78, 115, 223, 0.1);
        }
        
        .medication-info {
            font-weight: 500;
        }
        
        .medication-info .dosage {
            color: #6c757d;
            font-weight: normal;
            font-size: 0.85rem;
        }
        
        .medication-type {
            display: inline-block;
            background-color: #f1f3f9;
            color: #4e73df;
            border-radius: 4px;
            padding: 2px 8px;
            font-size: 0.75rem;
            margin-top: 4px;
        }
        
        .medical-info {
            color: #555;
            font-size: 0.85rem;
            line-height: 1.5;
            margin-top: 10px;
        }
        
        .medical-info strong {
            color: #4e73df;
        }
        
        .medical-info-item {
            margin-bottom: 5px;
        }
        
        /* Action buttons */
        .action-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 32px;
            height: 32px;
            border-radius: 6px;
            margin-right: 5px;
        }
        
        /* Pagination styling */
        .pagination {
            margin-top: 1rem;
            justify-content: center;
        }
        
        .page-item.active .page-link {
            background-color: #4e73df;
            border-color: #4e73df;
        }
        
        .page-link {
            color: #4e73df;
            padding: 0.4rem 0.75rem;
            font-size: 0.9rem;
        }
        
        /* Improved divider */
        .beneficiary-divider td {
            border-bottom: 2px solid #e0e0e0;
        }
        
        /* Status badge styling */
        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            display: inline-block;
            text-align: center;
            min-width: 80px;
        }
        
        .status-active {
            background-color: #e8f5e9;
            color: #2e7d32;
        }
        
        .status-paused {
            background-color: #fff8e1;
            color: #f57f17;
        }
        
        .status-completed {
            background-color: #e0e0e0;
            color: #616161;
        }
        
        /* Special instructions formatting */
        .special-instructions {
            font-style: italic;
            color: #555;
            font-size: 0.9rem;
        }

        /* Responsive styles */
        @media (max-width: 768px) {
            .filter-bar {
                flex-direction: column;
                align-items: flex-end;
                gap: 10px;
            }
            
            .filter-group {
                width: 100%;
            }
            
            .filter-group .form-select {
                width: 100%;
            }
            
            .table-responsive {
                font-size: 0.9rem;
            }
            
            .beneficiary-name {
                font-size: 0.9rem;
            }
            
            .badge-time {
                font-size: 0.7rem;
                padding: 3px 8px;
            }
        }
    </style>
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')

    <div class="home-section">
        <div class="text-left">MEDICATION SCHEDULE</div>
        <div class="container-fluid">
            <div class="row p-3" id="home-content">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="section-heading">
                                <i class="bi bi-capsule"></i> Medication Schedule Management
                            </h5>
                            <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                                <i class="bi bi-plus-lg me-2"></i> New Schedule
                            </button>
                        </div>
                        <div class="card-body">
                            <!-- Search and Filter Controls -->
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <form action="{{ route('care-worker.medication.schedule.index') }}" method="GET" id="searchForm">
                                        <div class="search-container">
                                            <input type="text" class="form-control search-input" id="scheduleSearch" name="search" 
                                                placeholder="Enter beneficiary name or medication name..." value="{{ $search }}">
                                            <i class="bi bi-search"></i>
                                            <button type="submit" class="search-btn">
                                                <i class="bi bi-search"></i>Search
                                            </button>
                                        </div>
                                        <input type="hidden" name="status" value="{{ $statusFilter }}">
                                        <input type="hidden" name="period" value="{{ $periodFilter }}">
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <div class="filter-bar">
                                        <div class="filter-group">
                                            <label class="filter-label">Status:</label>
                                            <div class="select-container">
                                                <select class="form-select" id="statusFilter" name="status" onchange="applyFilters()">
                                                    <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>All</option>
                                                    <option value="active" {{ $statusFilter == 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="paused" {{ $statusFilter == 'paused' ? 'selected' : '' }}>Paused</option>
                                                    <option value="completed" {{ $statusFilter == 'completed' ? 'selected' : '' }}>Completed</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="filter-group">
                                            <label class="filter-label">Period:</label>
                                            <div class="select-container">
                                                <select class="form-select" id="timeFilter" name="period" onchange="applyFilters()">
                                                    <option value="all" {{ $periodFilter == 'all' ? 'selected' : '' }}>All</option>
                                                    <option value="morning" {{ $periodFilter == 'morning' ? 'selected' : '' }}>Morning</option>
                                                    <option value="afternoon" {{ $periodFilter == 'afternoon' ? 'selected' : '' }}>Afternoon</option>
                                                    <option value="evening" {{ $periodFilter == 'evening' ? 'selected' : '' }}>Evening</option>
                                                    <option value="night" {{ $periodFilter == 'night' ? 'selected' : '' }}>Night</option>
                                                    <option value="as_needed" {{ $periodFilter == 'as_needed' ? 'selected' : '' }}>As Needed</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Medication Schedule Table -->
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Beneficiary</th>
                                            <th>Medication</th>
                                            <th>Schedule</th>
                                            <th>Special Instructions</th>
                                            <th>Status</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php $prevBeneficiary = null; @endphp
                                        
                                        @foreach($medicationSchedules as $schedule)
                                            @php 
                                                $isNewBeneficiary = !$prevBeneficiary || ($prevBeneficiary != $schedule->beneficiary_id);
                                                $prevBeneficiary = $schedule->beneficiary_id;
                                                
                                                // Format times
                                                $times = [];
                                                if ($schedule->morning_time) {
                                                    $times[] = '<span class="badge-time"><i class="bi bi-sunrise"></i> ' . 
                                                        \Carbon\Carbon::parse($schedule->morning_time)->format('h:i A') . 
                                                        ($schedule->with_food_morning ? ' (with food)' : '') . '</span>';
                                                }
                                                if ($schedule->noon_time) {
                                                    $times[] = '<span class="badge-time"><i class="bi bi-sun"></i> ' . 
                                                        \Carbon\Carbon::parse($schedule->noon_time)->format('h:i A') . 
                                                        ($schedule->with_food_noon ? ' (with food)' : '') . '</span>';
                                                }
                                                if ($schedule->evening_time) {
                                                    $times[] = '<span class="badge-time"><i class="bi bi-sunset"></i> ' . 
                                                        \Carbon\Carbon::parse($schedule->evening_time)->format('h:i A') . 
                                                        ($schedule->with_food_evening ? ' (with food)' : '') . '</span>';
                                                }
                                                if ($schedule->night_time) {
                                                    $times[] = '<span class="badge-time"><i class="bi bi-moon"></i> ' . 
                                                        \Carbon\Carbon::parse($schedule->night_time)->format('h:i A') . 
                                                        ($schedule->with_food_night ? ' (with food)' : '') . '</span>';
                                                }
                                                if ($schedule->as_needed) {
                                                    $times[] = '<span class="badge-time"><i class="bi bi-alarm"></i> As needed</span>';
                                                }
                                            @endphp
                                            
                                            <tr class="{{ $isNewBeneficiary ? 'beneficiary-start' : '' }}">
                                                <!-- Beneficiary column -->
                                                <td>
                                                    @if($isNewBeneficiary)
                                                        <div class="beneficiary-name">
                                                            <i class="bi bi-person-fill beneficiary-icon"></i>
                                                            {{ $schedule->beneficiary ? $schedule->beneficiary->first_name . ' ' . $schedule->beneficiary->last_name : 'Unknown Beneficiary' }}
                                                        </div>
                                                        
                                                        @if($schedule->beneficiary && $schedule->beneficiary->generalCarePlan && $schedule->beneficiary->generalCarePlan->healthHistory)
                                                            <div class="medical-info">
                                                                <div class="medical-info-item">
                                                                    <strong>Condition:</strong> 
                                                                    {{ $schedule->beneficiary->generalCarePlan->healthHistory->formatted_conditions ?? 'No data' }}
                                                                </div>
                                                                <div class="medical-info-item">
                                                                    <strong>Immunizations:</strong> 
                                                                    {{ $schedule->beneficiary->generalCarePlan->healthHistory->formatted_immunizations ?? 'No data' }}
                                                                </div>
                                                                <div class="medical-info-item">
                                                                    <strong>Allergies:</strong> 
                                                                    {{ $schedule->beneficiary->generalCarePlan->healthHistory->formatted_allergies ?? 'No data' }}
                                                                </div>
                                                            </div>
                                                        @endif
                                                    @endif
                                                </td>
                                                
                                                <!-- Medication column -->
                                                <td>
                                                    <div class="medication-info">
                                                        {{ $schedule->medication_name }}
                                                        <div class="dosage">{{ $schedule->dosage }}</div>
                                                        <div class="medication-type">{{ $schedule->medication_type }}</div>
                                                    </div>
                                                </td>
                                                
                                                <!-- Schedule column -->
                                                <td>
                                                    <div class="times-container">
                                                        {!! implode('', $times) !!}
                                                    </div>
                                                    <div class="date-range mt-2">
                                                        <small>
                                                            <i class="bi bi-calendar-range"></i> 
                                                            {{ \Carbon\Carbon::parse($schedule->start_date)->format('M j, Y') }}
                                                            @if($schedule->end_date)
                                                                - {{ \Carbon\Carbon::parse($schedule->end_date)->format('M j, Y') }}
                                                            @endif
                                                        </small>
                                                    </div>
                                                </td>
                                                
                                                <!-- Special Instructions column -->
                                                <td>
                                                    @if($schedule->special_instructions)
                                                        <div class="special-instructions">
                                                            {{ $schedule->special_instructions }}
                                                        </div>
                                                    @else
                                                        <span class="text-muted">No special instructions</span>
                                                    @endif
                                                </td>
                                                
                                                <!-- Status column -->
                                                <td>
                                                    <span class="status-badge status-{{ $schedule->status }}">
                                                        {{ ucfirst($schedule->status) }}
                                                    </span>
                                                </td>
                                                
                                                <!-- Actions column -->
                                                <td>
                                                    <div class="d-flex">
                                                        <button class="btn btn-sm btn-outline-primary action-btn me-1" title="Edit" data-id="{{ $schedule->medication_schedule_id }}">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger action-btn" title="Delete" data-id="{{ $schedule->medication_schedule_id }}">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                        
                                        @if($medicationSchedules->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-center py-4">
                                                    <div class="text-muted">
                                                        <i class="bi bi-calendar-x" style="font-size: 2rem;"></i>
                                                        <p class="mt-2">No medication schedules found.</p>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Pagination -->
                            @if($medicationSchedules->hasPages())
                                <div class="mt-3">
                                    {{ $medicationSchedules->withQueryString()->links('pagination::bootstrap-4') }}
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Medication Schedule Modal -->
    <div class="modal fade" id="addScheduleModal" tabindex="-1" aria-labelledby="addScheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addScheduleModalLabel">
                        <i class="bi bi-calendar-plus"></i> Add New Medication Schedule
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form>
                        <!-- Beneficiary Selection -->
                        <div class="form-group">
                            <label for="beneficiarySelect" class="form-label">
                                <i class="bi bi-person-fill"></i> Beneficiary
                            </label>
                            <div class="beneficiary-select-container">
                                <div class="select-container">
                                    <select class="form-select" id="beneficiarySelect" required>
                                        <option value="" selected disabled>Select a beneficiary</option>
                                        <option value="1">John Doe (B-001-24)</option>
                                        <option value="2">Mary Smith (B-002-24)</option>
                                        <option value="3">Robert Johnson (B-003-24)</option>
                                        <option value="4">Sarah Williams (B-004-24)</option>
                                        <option value="5">Michael Brown (B-005-24)</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Medical Information (Read-only) -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="medicalCondition" class="form-label">
                                        <i class="bi bi-heart-pulse"></i> Medical Condition
                                    </label>
                                    <input type="text" class="form-control" id="medicalCondition" placeholder="No condition on record" readonly>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="illnessInfo" class="form-label">
                                        <i class="bi bi-bandaid"></i> Illness Information
                                    </label>
                                    <input type="text" class="form-control" id="illnessInfo" placeholder="No illness on record" readonly>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Alert for allergies -->
                        <div class="alert alert-warning d-flex align-items-center" role="alert" id="allergiesAlert" style="display: none !important;">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <div>
                                Allergies: <span id="allergiesContent">None on record</span>
                            </div>
                        </div>
                        
                        <!-- Medication Details -->
                        <hr>
                        <h6 class="fw-bold mb-3">Medication Details</h6>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="medicationName" class="form-label">
                                        <i class="bi bi-capsule"></i> Medication Name
                                    </label>
                                    <input type="text" class="form-control" id="medicationName" placeholder="Enter medication name" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="dosage" class="form-label">
                                        <i class="bi bi-diagram-3"></i> Dosage
                                    </label>
                                    <input type="text" class="form-control" id="dosage" placeholder="e.g., 500mg" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="medicationType" class="form-label">
                                <i class="bi bi-archive"></i> Medication Type
                            </label>
                            <div class="select-container">
                                <select class="form-select" id="medicationType" required>
                                    <option value="" selected disabled>Select medication type</option>
                                    <option value="tablet">Tablet</option>
                                    <option value="capsule">Capsule</option>
                                    <option value="liquid">Liquid</option>
                                    <option value="injection">Injection</option>
                                    <option value="inhaler">Inhaler</option>
                                    <option value="topical">Topical</option>
                                    <option value="drops">Drops</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Schedule Times -->
                        <hr>
                        <h6 class="fw-bold mb-3">Schedule Times</h6>
                        
                        <div class="time-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="morningSwitch">
                                <label class="form-check-label" for="morningSwitch">Morning</label>
                            </div>
                            <input type="time" class="form-control time-input" id="morningTime" value="08:00">
                            <div class="form-check ms-2">
                                <input class="form-check-input" type="checkbox" value="" id="morningWithFood">
                                <label class="form-check-label" for="morningWithFood">
                                    With food
                                </label>
                            </div>
                        </div>
                        
                        <div class="time-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="noonSwitch">
                                <label class="form-check-label" for="noonSwitch">Afternoon</label>
                            </div>
                            <input type="time" class="form-control time-input" id="noonTime" value="13:00">
                            <div class="form-check ms-2">
                                <input class="form-check-input" type="checkbox" value="" id="noonWithFood">
                                <label class="form-check-label" for="noonWithFood">
                                    With food
                                </label>
                            </div>
                        </div>
                        
                        <div class="time-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="eveningSwitch">
                                <label class="form-check-label" for="eveningSwitch">Evening</label>
                            </div>
                            <input type="time" class="form-control time-input" id="eveningTime" value="18:00">
                            <div class="form-check ms-2">
                                <input class="form-check-input" type="checkbox" value="" id="eveningWithFood">
                                <label class="form-check-label" for="eveningWithFood">
                                    With food
                                </label>
                            </div>
                        </div>
                        
                        <div class="time-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="nightSwitch">
                                <label class="form-check-label" for="nightSwitch">Night</label>
                            </div>
                            <input type="time" class="form-control time-input" id="nightTime" value="21:00">
                            <div class="form-check ms-2">
                                <input class="form-check-input" type="checkbox" value="" id="nightWithFood">
                                <label class="form-check-label" for="nightWithFood">
                                    With food
                                </label>
                            </div>
                        </div>
                        
                        <div class="time-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="asNeededSwitch">
                                <label class="form-check-label" for="asNeededSwitch">As needed (PRN)</label>
                            </div>
                            <div class="text-muted ms-auto" style="font-size: 0.85rem;">
                                <i class="bi bi-info-circle"></i> No fixed time
                            </div>
                        </div>
                        
                        <!-- Additional Instructions -->
                        <div class="form-group mt-3">
                            <label for="instructions" class="form-label">
                                <i class="bi bi-journal-text"></i> Special Instructions
                            </label>
                            <textarea class="form-control" id="instructions" rows="3" placeholder="Enter any special instructions for administration..."></textarea>
                        </div>
                        
                        <!-- Duration -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="startDate" class="form-label">
                                        <i class="bi bi-calendar-check"></i> Start Date
                                    </label>
                                    <input type="date" class="form-control" id="startDate" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="endDate" class="form-label">
                                        <i class="bi bi-calendar-x"></i> End Date (Optional)
                                    </label>
                                    <input type="date" class="form-control" id="endDate">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveScheduleBtn">
                        <i class="bi bi-check-lg me-1"></i> Save Medication Schedule
                    </button>
                </div>
            </div>
        </div>
    </div>
   
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        /*
        document.addEventListener('DOMContentLoaded', function() {
            // Show/hide allergies alert based on beneficiary selection
            document.getElementById('beneficiarySelect').addEventListener('change', function() {
                // Sample data - would come from your database in reality
                const beneficiaryData = {
                    '1': { condition: 'Type 2 Diabetes', illness: 'Hypertension', allergies: 'Penicillin' },
                    '2': { condition: 'Hypothyroidism', illness: 'Asthma', allergies: '' },
                    '3': { condition: 'Heart Disease', illness: 'Edema', allergies: 'Sulfa drugs' },
                    '4': { condition: 'Type 1 Diabetes', illness: 'High Blood Pressure', allergies: 'Latex, Iodine' },
                    '5': { condition: 'COPD', illness: 'Arthritis', allergies: '' }
                };
                
                const selectedValue = this.value;
                if (selectedValue && beneficiaryData[selectedValue]) {
                    // Set medical condition and illness
                    document.getElementById('medicalCondition').value = beneficiaryData[selectedValue].condition || 'No condition on record';
                    document.getElementById('illnessInfo').value = beneficiaryData[selectedValue].illness || 'No illness on record';
                    
                    // Handle allergies alert
                    const allergiesAlert = document.getElementById('allergiesAlert');
                    const allergiesContent = document.getElementById('allergiesContent');
                    
                    if (beneficiaryData[selectedValue].allergies) {
                        allergiesContent.textContent = beneficiaryData[selectedValue].allergies;
                        allergiesAlert.style.display = 'flex';
                    } else {
                        allergiesAlert.style.display = 'none';
                    }
                } else {
                    // Reset fields if no beneficiary is selected
                    document.getElementById('medicalCondition').value = '';
                    document.getElementById('illnessInfo').value = '';
                    document.getElementById('allergiesAlert').style.display = 'none';
                }
            });
            
            // Handle "As needed" switch disabling time fields
            document.getElementById('asNeededSwitch').addEventListener('change', function() {
                if (this.checked) {
                    // Disable other time switches
                    document.getElementById('morningSwitch').checked = false;
                    document.getElementById('noonSwitch').checked = false;
                    document.getElementById('eveningSwitch').checked = false;
                    document.getElementById('nightSwitch').checked = false;
                    
                    // Disable the time input fields
                    document.getElementById('morningSwitch').disabled = true;
                    document.getElementById('noonSwitch').disabled = true;
                    document.getElementById('eveningSwitch').disabled = true;
                    document.getElementById('nightSwitch').disabled = true;
                    
                    document.getElementById('morningTime').disabled = true;
                    document.getElementById('noonTime').disabled = true;
                    document.getElementById('eveningTime').disabled = true;
                    document.getElementById('nightTime').disabled = true;
                    
                    document.getElementById('morningWithFood').disabled = true;
                    document.getElementById('noonWithFood').disabled = true;
                    document.getElementById('eveningWithFood').disabled = true;
                    document.getElementById('nightWithFood').disabled = true;
                } else {
                    // Enable the time switches again
                    document.getElementById('morningSwitch').disabled = false;
                    document.getElementById('noonSwitch').disabled = false;
                    document.getElementById('eveningSwitch').disabled = false;
                    document.getElementById('nightSwitch').disabled = false;
                    
                    // Re-enable time fields but don't automatically check them
                    updateTimeFields('morning');
                    updateTimeFields('noon');
                    updateTimeFields('evening');
                    updateTimeFields('night');
                }
            });
            
            // Handle time switch controls
            ['morning', 'noon', 'evening', 'night'].forEach(function(time) {
                document.getElementById(time + 'Switch').addEventListener('change', function() {
                    updateTimeFields(time);
                });
            });
            
            function updateTimeFields(timePeriod) {
                const isChecked = document.getElementById(timePeriod + 'Switch').checked;
                document.getElementById(timePeriod + 'Time').disabled = !isChecked;
                document.getElementById(timePeriod + 'WithFood').disabled = !isChecked;
            }
            
            // Search functionality
            document.getElementById('scheduleSearch').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                
                // Get all beneficiary groups (all rows with the same beneficiary)
                const rows = document.querySelectorAll('tbody tr');
                
                // Create a map to group rows by beneficiary
                const beneficiaryGroups = new Map();
                
                rows.forEach(row => {
                    // Check if this row has a beneficiary name (first cell with class beneficiary-name)
                    const beneficiaryCell = row.querySelector('.beneficiary-name');
                    
                    if (beneficiaryCell) {
                        // This is the first row of a beneficiary group
                        const beneficiaryName = beneficiaryCell.textContent.trim();
                        beneficiaryGroups.set(row, [row]);
                        
                        // Find all rows that belong to this beneficiary (until next beneficiary-name)
                        let nextRow = row.nextElementSibling;
                        while (nextRow && !nextRow.querySelector('.beneficiary-name')) {
                            beneficiaryGroups.get(row).push(nextRow);
                            nextRow = nextRow.nextElementSibling;
                        }
                    }
                });
                
                // Now search within each group
                beneficiaryGroups.forEach((groupRows, firstRow) => {
                    // Get searchable content from the group
                    let groupContent = '';
                    
                    // Get beneficiary name and info
                    const beneficiaryName = firstRow.querySelector('.beneficiary-name').textContent.trim();
                    const medicalInfo = firstRow.querySelector('.medical-info').textContent.trim();
                    
                    // Add to searchable content
                    groupContent += beneficiaryName + ' ' + medicalInfo;
                    
                    // Get all medication info from all rows in group
                    groupRows.forEach(row => {
                        const medicationInfo = row.querySelector('.medication-info');
                        if (medicationInfo) {
                            groupContent += ' ' + medicationInfo.textContent.trim();
                        }
                        
                        // Get schedule info
                        const scheduleCell = row.cells[3]; // Assuming schedule is the 4th cell
                        if (scheduleCell) {
                            groupContent += ' ' + scheduleCell.textContent.trim();
                        }
                    });
                    
                    // Check if the search term is found in the group's content
                    const isMatch = groupContent.toLowerCase().includes(searchTerm);
                    
                    // Show/hide all rows in the group
                    groupRows.forEach(row => {
                        row.style.display = isMatch || searchTerm.length < 2 ? '' : 'none';
                    });
                });
            });
            
            // Filter functionality
            document.getElementById('statusFilter').addEventListener('change', filterSchedules);
            document.getElementById('timeFilter').addEventListener('change', filterSchedules);
            
            function filterSchedules() {
                const statusValue = document.getElementById('statusFilter').value;
                const timeValue = document.getElementById('timeFilter').value;
                
                // Here you would implement filtering logic based on status and time
                // For this demo, we'll just log the filter values
                console.log('Filtering by:', {status: statusValue, time: timeValue});
                
                // In a real implementation, you would update the visible rows based on these filters
            }
            
            // Initialize time fields
            updateTimeFields('morning');
            updateTimeFields('noon');
            updateTimeFields('evening');
            updateTimeFields('night');
        });*/
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Filter handling
            function applyFilters() {
                const statusValue = document.getElementById('statusFilter').value;
                const timeValue = document.getElementById('timeFilter').value;
                const searchValue = document.getElementById('scheduleSearch').value;
                
                // Update hidden form fields
                document.querySelector('input[name="status"]').value = statusValue;
                document.querySelector('input[name="period"]').value = timeValue;
                
                // Submit the form
                document.getElementById('searchForm').submit();
            }
            
            // Attach filter function to global scope for the onchange attributes
            window.applyFilters = applyFilters;
        });
    </script>
</body>
</html>