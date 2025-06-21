<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Medication Schedule | Manager</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/medicationSchedule.css') }}">
</head>
<body>
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp

    @include('components.careManagerNavbar')
    @include('components.careManagerSidebar')

    <div class="home-section">
        <div class="text-left">{{ T::translate('MEDICATION SCHEDULE', 'ISKEDYUL NG GAMOT')}}</div>
        <div class="container-fluid">
            <div class="row p-3" id="home-content">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="section-heading">
                                <i class="bi bi-capsule"></i> {{ T::translate('Medication Schedule Management', 'Pamamahala ng Iskedyul ng Gamot') }}
                            </h5>
                            <button class="btn btn-primary d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#addScheduleModal">
                                <i class="bi bi-plus-lg me-2"></i> {{ T::translate('New Schedule', 'Bagong Iskedyul')}}
                            </button>
                        </div>
                        <div class="card-body">
                            <!-- Search and Filter Controls -->
                            <div class="row mb-3">
                                <div class="col-md-6 mb-2 mb-md-0">
                                    <form action="{{ route('care-manager.medication.schedule.index') }}" method="GET" id="searchForm">
                                        <div class="search-container">
                                            <input type="text" class="form-control search-input" id="scheduleSearch" name="search" 
                                                placeholder="{{ T::translate('Enter beneficiary name or medication name...', 'Ilagay ang pangalan nang benepisyaro o gamot...')}}" value="{{ $search }}">
                                            <i class="bi bi-search"></i>
                                            <button type="submit" class="search-btn">
                                                <i class="bi bi-search"></i>{{ T::translate('Search', 'Maghanap')}}
                                            </button>
                                        </div>
                                        <input type="hidden" name="status" value="{{ $statusFilter }}">
                                        <input type="hidden" name="period" value="{{ $periodFilter }}">
                                    </form>
                                </div>
                                <div class="col-md-6">
                                    <div class="filter-bar">
                                        <div class="filter-group">
                                            <label class="filter-label">{{ T::translate('Status:', 'Status:')}}</label>
                                            <div class="select-container">
                                                <select class="form-select" id="statusFilter" name="status" onchange="applyFilters()">
                                                    <option value="all" {{ $statusFilter == 'all' ? 'selected' : '' }}>{{ T::translate('All', 'Lahat')}}</option>
                                                    <option value="active" {{ $statusFilter == 'active' ? 'selected' : '' }}>{{ T::translate('Active', 'Aktibo')}}</option>
                                                    <option value="paused" {{ $statusFilter == 'paused' ? 'selected' : '' }}>{{ T::translate('Paused', 'Nakahinto')}}</option>
                                                    <option value="completed" {{ $statusFilter == 'completed' ? 'selected' : '' }}>{{ T::translate('Completed', 'Natapos')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="filter-group">
                                            <label class="filter-label">{{ T::translate('Period:', 'Panahon:')}}</label>
                                            <div class="select-container">
                                                <select class="form-select" id="timeFilter" name="period" onchange="applyFilters()">
                                                    <option value="all" {{ $periodFilter == 'all' ? 'selected' : '' }}>{{ T::translate('All', 'Lahat')}}</option>
                                                    <option value="morning" {{ $periodFilter == 'morning' ? 'selected' : '' }}>{{ T::translate('Morning', 'Umaga')}}</option>
                                                    <option value="afternoon" {{ $periodFilter == 'afternoon' ? 'selected' : '' }}>{{ T::translate('Afternoon', 'Hapon')}}</option>
                                                    <option value="evening" {{ $periodFilter == 'evening' ? 'selected' : '' }}>{{ T::translate('Evening', 'Gabi')}}</option>
                                                    <option value="night" {{ $periodFilter == 'night' ? 'selected' : '' }}>Night</option>
                                                    <option value="as_needed" {{ $periodFilter == 'as_needed' ? 'selected' : '' }}>{{ T::translate('As Needed', 'Kung Kinakailangan')}}</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Success Message -->
                            @if (session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                            @endif

                            <!-- Medication Schedule Table -->
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th class="col-beneficiary">{{ T::translate('Beneficiary', 'Benepisyaryo')}}</th>
                                            <th class="col-medication">{{ T::translate('Medication', 'Gamot')}}</th>
                                            <th class="col-schedule">{{ T::translate('Schedule', 'Iskedyul')}}</th>
                                            <th class="col-instructions">{{ T::translate('Special Instructions', 'Espesyal na Tagubilin')}}</th>
                                            <th class="col-status">{{ T::translate('Status', 'Status')}}</th>
                                            <th class="col-actions">{{ T::translate('Actions', 'Aksyon')}}</th>
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
                                                        ($schedule->with_food_morning ? ' ('.T::translate('with food', 'may pagkain').')' : '') . '</span>';
                                                }
                                                if ($schedule->noon_time) {
                                                    $times[] = '<span class="badge-time"><i class="bi bi-sun"></i> ' . 
                                                        \Carbon\Carbon::parse($schedule->noon_time)->format('h:i A') . 
                                                        ($schedule->with_food_noon ? ' ('.T::translate('with food', 'may pagkain').')' : '') . '</span>';
                                                }
                                                if ($schedule->evening_time) {
                                                    $times[] = '<span class="badge-time"><i class="bi bi-sunset"></i> ' . 
                                                        \Carbon\Carbon::parse($schedule->evening_time)->format('h:i A') . 
                                                        ($schedule->with_food_evening ? ' ('.T::translate('with food', 'may pagkain').')' : '') . '</span>';
                                                }
                                                if ($schedule->night_time) {
                                                    $times[] = '<span class="badge-time"><i class="bi bi-moon"></i> ' . 
                                                        \Carbon\Carbon::parse($schedule->night_time)->format('h:i A') . 
                                                        ($schedule->with_food_night ? ' ('.T::translate('with food', 'may pagkain').')' : '') . '</span>';
                                                }
                                                if ($schedule->as_needed) {
                                                    $times[] = '<span class="badge-time"><i class="bi bi-alarm"></i> '.T::translate('As needed', 'Kung Kinakailangan').'</span>';
                                                }
                                            @endphp
                                            
                                            <tr class="{{ $isNewBeneficiary ? 'beneficiary-start' : '' }}">
                                                <!-- Beneficiary column -->
                                                <td>
                                                    @if($isNewBeneficiary)
                                                        <div class="beneficiary-name">
                                                            <i class="bi bi-person-fill beneficiary-icon"></i>
                                                            {{ $schedule->beneficiary ? $schedule->beneficiary->first_name . ' ' . $schedule->beneficiary->last_name : T::translate('Unknown Beneficiary', 'Hindi Kilalang Benepisyaryo') }}
                                                        </div>
                                                        
                                                        @if($schedule->beneficiary && $schedule->beneficiary->generalCarePlan && $schedule->beneficiary->generalCarePlan->healthHistory)
                                                            <div class="medical-info">
                                                                <div class="medical-info-item">
                                                                    <strong>{{ T::translate('Condition:', 'Kondisyon:')}}</strong> 
                                                                    {{ $schedule->beneficiary->generalCarePlan->healthHistory->formatted_conditions ?? T::translate('No data', 'Walang data') }}
                                                                </div>
                                                                <div class="medical-info-item">
                                                                    <strong>{{ T::translate('Immunizations:', 'Bakuna:')}}</strong> 
                                                                    {{ $schedule->beneficiary->generalCarePlan->healthHistory->formatted_immunizations ?? T::translate('No data', 'Walang data') }}
                                                                </div>
                                                                <div class="medical-info-item">
                                                                    <strong>{{ T::translate('Allergies:', 'Alerhiya:')}}</strong> 
                                                                    {{ $schedule->beneficiary->generalCarePlan->healthHistory->formatted_allergies ?? T::translate('No data', 'Walang data') }}
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
                                                        <span class="text-muted">{{ T::translate('No special instructions', 'Walang espesyal na tagubilin')}}</span>
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
                                                        <button class="btn btn-sm btn-primary action-btn me-1 edit-btn" 
                                                            title="{{ T::translate('Edit', 'I-edit')}}" 
                                                            data-id="{{ $schedule->medication_schedule_id }}">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-danger action-btn delete-btn" 
                                                            data-id="{{ $schedule->medication_schedule_id }}" 
                                                            data-beneficiary="{{ $schedule->beneficiary->first_name }} {{ $schedule->beneficiary->last_name }}"
                                                            data-medication="{{ $schedule->medication_name }}"
                                                            data-dosage="{{ $schedule->dosage }}"
                                                            title="{{ T::translate('Delete schedule', 'Tanggalin ang iskedyul')}}">
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
                                                        <p class="mt-2">{{ T::translate('No medication schedules found.', 'Walang nakitang iskedyul ng gamot.')}}</p>
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
                        <i class="bi bi-calendar-plus"></i> {{ T::translate('Add New Medication Schedule', 'Magdagdag ng Bagong Iskedyul ng Gamot')}}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    
                    <!-- Error Alert for Validation -->
                    <div id="modalErrors" class="alert alert-danger" style="{{ $errors->any() ? '' : 'display: none;' }}">
                        <strong>{{ T::translate('Please correct the following:', 'Maaring itama ang sumusunod:')}}</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <form action="{{ route('care-manager.medication.schedule.store') }}" method="POST" id="addScheduleForm">
                        @csrf
                        <!-- Beneficiary Selection -->
                        <div class="form-group">
                            <label for="beneficiarySelect" class="form-label">
                                <i class="bi bi-person-fill"></i> {{ T::translate('Beneficiary', 'Benepisyaryo')}}
                            </label>
                            <div class="beneficiary-select-container">
                                <div class="select-container">
                                    <select class="form-select" id="beneficiarySelect" name="beneficiary_id" required>
                                        <option value="" disabled {{ old('beneficiary_id') ? '' : 'selected' }}>{{ T::translate('Select a beneficiary', 'Pumili ng Benepisyaryo')}}</option>
                                        @foreach($beneficiaries as $beneficiary)
                                            <option value="{{ $beneficiary['id'] }}" 
                                                data-allergies="{{ $beneficiary['allergies'] }}"
                                                data-conditions="{{ $beneficiary['medical_conditions'] }}"
                                                data-immunizations="{{ $beneficiary['immunizations'] }}"
                                                {{ old('beneficiary_id') == $beneficiary['id'] ? 'selected' : '' }}>
                                                {{ $beneficiary['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Alert for allergies -->
                        <div class="alert alert-warning d-flex align-items-center" role="alert" id="allergiesAlert" style="display: none !important;">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <div>
                                <strong>{{ T::translate('Allergies:', 'Alerhiya:')}}</strong> <span id="allergiesContent">{{ T::translate('None on record', 'Wala sa record')}}</span>
                            </div>
                        </div>
                        
                        <!-- Medication Details -->
                        <hr>
                        <h6 class="fw-bold mb-3">{{ T::translate('Medication Details', 'Mga Detalye ng Gamot')}}</h6>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="medicationName" class="form-label">
                                        <i class="bi bi-capsule"></i> {{ T::translate('Medication Name', 'Pangalan ng Gamot')}}
                                    </label>
                                    <input type="text" class="form-control" id="medicationName" name="medication_name" 
                                        placeholder="{{ T::translate('Enter medication name', 'Ilagay ang pangalan ng gamot')}}" required value="{{ old('medication_name') }}">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="dosage" class="form-label">
                                        <i class="bi bi-diagram-3"></i>{{ T::translate('Dosage', 'Dosis')}} 
                                    </label>
                                    <input type="text" class="form-control" id="dosage" name="dosage" 
                                        placeholder="{{ T::translate('e.g., 500mg', 'hal., 500mg')}}" required value="{{ old('dosage') }}">
                                </div>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="medicationType" class="form-label">
                                <i class="bi bi-archive"></i>{{ T::translate('Medication Type', 'Uri ng Gamot')}} 
                            </label>
                            <div class="select-container">
                                <select class="form-select" id="medicationType" name="medication_type" required>
                                    <option value="" disabled {{ old('medication_type') ? '' : 'selected' }}>{{ T::translate('Select medication type', 'Pumili ng Uri ng Gamot')}}</option>
                                    <option value="tablet" {{ old('medication_type') == 'tablet' ? 'selected' : '' }}>{{ T::translate('Tablet', 'Tableta')}}</option>
                                    <option value="capsule" {{ old('medication_type') == 'capsule' ? 'selected' : '' }}>{{ T::translate('Capsule', 'Kapsula')}}</option>
                                    <option value="liquid" {{ old('medication_type') == 'liquid' ? 'selected' : '' }}>{{ T::translate('Liquid', 'Likido')}}</option>
                                    <option value="injection" {{ old('medication_type') == 'injection' ? 'selected' : '' }}>Injection</option>
                                    <option value="inhaler" {{ old('medication_type') == 'inhaler' ? 'selected' : '' }}>Inhaler</option>
                                    <option value="topical" {{ old('medication_type') == 'topical' ? 'selected' : '' }}>Topical</option>
                                    <option value="drops" {{ old('medication_type') == 'drops' ? 'selected' : '' }}>Drops</option>
                                    <option value="other" {{ old('medication_type') == 'other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Schedule Times -->
                        <hr>
                        <h6 class="fw-bold mb-3">{{ T::translate('Schedule Times', 'Oras ng Iskedyul')}}</h6>
                        
                        <!-- Morning -->
                        <div class="time-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="morningSwitch" 
                                    name="morning_time" {{ old('morning_time') ? 'checked' : '' }}>
                                <label class="form-check-label" for="morningSwitch">{{ T::translate('Morning', 'Umaga')}}</label>
                            </div>
                            <input type="time" class="form-control time-input" id="morningTime" 
                                name="morning_time_value" value="{{ old('morning_time_value', '08:00') }}">
                            <div class="form-check ms-2">
                                <input class="form-check-input" type="checkbox" value="1" id="morningWithFood" 
                                    name="with_food_morning" {{ old('with_food_morning') ? 'checked' : '' }}>
                                <label class="form-check-label" for="morningWithFood">{{ T::translate('With food', 'May Pagkain')}}</label>
                            </div>
                        </div>
        
                        <!-- Afternoon -->
                        <div class="time-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="noonSwitch" 
                                    name="noon_time" {{ old('noon_time') ? 'checked' : '' }}>
                                <label class="form-check-label" for="noonSwitch">{{ T::translate('Afternoon', 'Hapon')}}</label>
                            </div>
                            <input type="time" class="form-control time-input" id="noonTime" 
                                name="noon_time_value" value="{{ old('noon_time_value', '13:00') }}">
                            <div class="form-check ms-2">
                                <input class="form-check-input" type="checkbox" value="1" id="noonWithFood" 
                                    name="with_food_noon" {{ old('with_food_noon') ? 'checked' : '' }}>
                                <label class="form-check-label" for="noonWithFood">{{ T::translate('With food', 'May Pagkain')}}</label>
                            </div>
                        </div>
                        
                        <!-- Evening -->
                        <div class="time-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="eveningSwitch" 
                                    name="evening_time" {{ old('evening_time') ? 'checked' : '' }}>
                                <label class="form-check-label" for="eveningSwitch">{{ T::translate('Evening', 'Gabi')}}</label>
                            </div>
                            <input type="time" class="form-control time-input" id="eveningTime" 
                                name="evening_time_value" value="{{ old('evening_time_value', '18:00') }}">
                            <div class="form-check ms-2">
                                <input class="form-check-input" type="checkbox" value="1" id="eveningWithFood" 
                                    name="with_food_evening" {{ old('with_food_evening') ? 'checked' : '' }}>
                                <label class="form-check-label" for="eveningWithFood">{{ T::translate('With food', 'May Pagkain')}}</label>
                            </div>
                        </div>
                        
                        <!-- Night -->
                        <div class="time-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="nightSwitch" 
                                    name="night_time" {{ old('night_time') ? 'checked' : '' }}>
                                <label class="form-check-label" for="nightSwitch">Night</label>
                            </div>
                            <input type="time" class="form-control time-input" id="nightTime" 
                                name="night_time_value" value="{{ old('night_time_value', '21:00') }}">
                            <div class="form-check ms-2">
                                <input class="form-check-input" type="checkbox" value="1" id="nightWithFood" 
                                    name="with_food_night" {{ old('with_food_night') ? 'checked' : '' }}>
                                <label class="form-check-label" for="nightWithFood">{{ T::translate('With food', 'May Pagkain')}}</label>
                            </div>
                        </div>
                        
                        <!-- As Needed -->
                        <div class="time-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="asNeededSwitch" 
                                    name="as_needed" value="1" {{ old('as_needed') ? 'checked' : '' }}>
                                <label class="form-check-label" for="asNeededSwitch">{{ T::translate('As needed (PRN)', 'Kung Kinakailangan (PRN)')}}</label>
                            </div>
                            <div class="text-muted ms-auto" style="font-size: 0.85rem;">
                                <i class="bi bi-info-circle"></i> {{ T::translate('No fixed time', 'Walang takdang oras')}}
                            </div>
                        </div>
                        
                        <!-- Additional Instructions -->
                        <div class="form-group mt-3">
                            <label for="instructions" class="form-label">
                                <i class="bi bi-journal-text"></i> {{ T::translate('Special Instructions', 'Espesyal na Tagubilin')}}
                            </label>
                            <textarea class="form-control" id="instructions" name="special_instructions" rows="3" 
                                placeholder="{{ T::translate('Enter any special instructions for administration...', 'Maglagay ng anumang espesyal na tagubilin para sa pangangasiwa...')}}">{{ old('special_instructions') }}</textarea>
                        </div>
                        
                        <!-- Duration -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="startDate" class="form-label">
                                        <i class="bi bi-calendar-check"></i> {{ T::translate('Start Date', 'Petsa ng Simula')}}
                                    </label>
                                    <input type="date" class="form-control" id="startDate" name="start_date" 
                                        required value="{{ old('start_date') }}">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="endDate" class="form-label">
                                        <i class="bi bi-calendar-x"></i> {{ T::translate('End Date (Optional)', 'Petsa ng Pagtatapos (Opsyonal)')}}
                                    </label>
                                    <input type="date" class="form-control" id="endDate" name="end_date" 
                                        value="{{ old('end_date') }}">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ T::translate('Cancel', 'Kanselahin')}}</button>
                    <button type="submit" form="addScheduleForm" class="btn btn-primary" id="saveScheduleBtn">
                        <i class="bi bi-check-lg me-1"></i> {{ T::translate('Save Medication Schedule', 'I-Save ang Iskedyul ng Gamot')}}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Medication Schedule Modal -->
    <div class="modal fade" id="editScheduleModal" tabindex="-1" aria-labelledby="editScheduleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editScheduleModalLabel">
                        <i class="bi bi-calendar-check"></i> {{ T::translate('Edit Medication Schedule', 'I-Edit ang Iskedyul ng Gamot')}}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Error Alert for Validation -->
                    <div id="editModalErrors" class="alert alert-danger" style="{{ $errors->any() && session('show_edit_modal') ? '' : 'display: none;' }}">
                        <strong>{{ T::translate('Please correct the following:', 'Maaring itama ang sumusunod:')}}</strong>
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>

                    <form action="" method="POST" id="editScheduleForm">
                        @csrf
                        @method('PUT')
                        <input type="hidden" id="edit_medication_id" name="medication_id">
                        
                        <!-- Beneficiary Selection -->
                        <div class="form-group">
                            <label for="editBeneficiarySelect" class="form-label">
                                <i class="bi bi-person-fill"></i>{{ T::translate('Beneficiary', 'Benepisyaryo')}}
                            </label>
                            <div class="beneficiary-select-container">
                                <div class="select-container">
                                    <select class="form-select" id="editBeneficiarySelect" name="beneficiary_id" required>
                                        <option value="" disabled>{{ T::translate('Select a beneficiary', 'Pumili ng Benepisyaryo')}}</option>
                                        @foreach($beneficiaries as $beneficiary)
                                            <option value="{{ $beneficiary['id'] }}" 
                                                data-allergies="{{ $beneficiary['allergies'] }}"
                                                data-conditions="{{ $beneficiary['medical_conditions'] }}"
                                                data-immunizations="{{ $beneficiary['immunizations'] }}">
                                                {{ $beneficiary['name'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Alert for allergies -->
                        <div class="alert alert-warning d-flex align-items-center" role="alert" id="editAllergiesAlert" style="display: none !important;">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <div>
                                <strong>{{ T::translate('Allergies:', 'Alerhiya:')}}</strong> <span id="editAllergiesContent">{{ T::translate('None on record', 'Wala sa record')}}</span>
                            </div>
                        </div>
                        
                        <!-- Medication Details -->
                        <hr>
                        <h6 class="fw-bold mb-3">{{ T::translate('Medication Details', 'Detalye ng Gamot')}}</h6>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label for="editMedicationName" class="form-label">
                                        <i class="bi bi-capsule"></i> {{ T::translate('Medication Name', 'Pangalan ng Gamot')}}
                                    </label>
                                    <input type="text" class="form-control" id="editMedicationName" name="medication_name" placeholder="{{ T::translate('Enter medication name', 'Ilagay ang pangalan ng gamot')}}" required>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="editDosage" class="form-label">
                                        <i class="bi bi-diagram-3"></i> {{ T::translate('Dosage', 'Dosis')}}
                                    </label>
                                    <input type="text" class="form-control" id="editDosage" name="dosage" placeholder="{{ T::translate('e.g., 500mg', 'hal., 500mg')}}" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="editMedicationType" class="form-label">
                                        <i class="bi bi-archive"></i> {{ T::translate('Medication Type', 'Uri ng Gamot')}}
                                    </label>
                                    <div class="select-container">
                                        <select class="form-select" id="editMedicationType" name="medication_type" required>
                                            <option value="" disabled>{{ T::translate('Select medication type', 'Pumili ng Uri ng Gamot')}}</option>
                                            <option value="tablet">{{ T::translate('Tablet', 'Tableta')}}</option>
                                            <option value="capsule">{{ T::translate('Capsule', 'Kapsula')}}</option>
                                            <option value="liquid">{{ T::translate('Liquid', 'Likido')}}</option>
                                            <option value="injection">Injection</option>
                                            <option value="inhaler">Inhaler</option>
                                            <option value="topical">Topical</option>
                                            <option value="drops">Drops</option>
                                            <option value="other">Other</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="editStatus" class="form-label">
                                        <i class="bi bi-toggle-on"></i> {{ T::translate('Status', 'Status')}}
                                    </label>
                                    <div class="select-container">
                                        <select class="form-select" id="editStatus" name="status" required>
                                            <option value="active">{{ T::translate('Active', 'Aktibo')}}</option>
                                            <option value="paused">{{ T::translate('Paused', 'Nakahinto')}}</option>
                                            <option value="completed">{{ T::translate('Completed', 'Natapos')}}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Schedule Times -->
                        <hr>
                        <h6 class="fw-bold mb-3">{{ T::translate('Schedule Times', 'Mga Oras ng Iskedyul')}}</h6>
                        
                        <div class="time-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="editMorningSwitch" name="morning_time">
                                <label class="form-check-label" for="editMorningSwitch">{{ T::translate('Morning', 'Umaga')}}</label>
                            </div>
                            <input type="time" class="form-control time-input" id="editMorningTime" name="morning_time_value" value="08:00">
                            <div class="form-check ms-2">
                                <input class="form-check-input" type="checkbox" value="1" id="editMorningWithFood" name="with_food_morning">
                                <label class="form-check-label" for="editMorningWithFood">
                                    {{ T::translate('With food', 'May Pagkain')}}
                                </label>
                            </div>
                        </div>
                        
                        <div class="time-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="editNoonSwitch" name="noon_time">
                                <label class="form-check-label" for="editNoonSwitch">{{ T::translate('Afternoon', 'Hapon')}}</label>
                            </div>
                            <input type="time" class="form-control time-input" id="editNoonTime" name="noon_time_value" value="13:00">
                            <div class="form-check ms-2">
                                <input class="form-check-input" type="checkbox" value="1" id="editNoonWithFood" name="with_food_noon">
                                <label class="form-check-label" for="editNoonWithFood">
                                    {{ T::translate('With food', 'May Pagkain')}}
                                </label>
                            </div>
                        </div>
                        
                        <div class="time-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="editEveningSwitch" name="evening_time">
                                <label class="form-check-label" for="editEveningSwitch">{{ T::translate('Evening', 'Gabi')}}</label>
                            </div>
                            <input type="time" class="form-control time-input" id="editEveningTime" name="evening_time_value" value="18:00">
                            <div class="form-check ms-2">
                                <input class="form-check-input" type="checkbox" value="1" id="editEveningWithFood" name="with_food_evening">
                                <label class="form-check-label" for="editEveningWithFood">
                                    {{ T::translate('With food', 'May Pagkain')}}
                                </label>
                            </div>
                        </div>
                        
                        <div class="time-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="editNightSwitch" name="night_time">
                                <label class="form-check-label" for="editNightSwitch">Night</label>
                            </div>
                            <input type="time" class="form-control time-input" id="editNightTime" name="night_time_value" value="21:00">
                            <div class="form-check ms-2">
                                <input class="form-check-input" type="checkbox" value="1" id="editNightWithFood" name="with_food_night">
                                <label class="form-check-label" for="editNightWithFood">
                                    {{ T::translate('With food', 'May Pagkain')}}
                                </label>
                            </div>
                        </div>
                        
                        <div class="time-group">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" role="switch" id="editAsNeededSwitch" name="as_needed" value="1">
                                <label class="form-check-label" for="editAsNeededSwitch">{{ T::translate('As needed (PRN)', 'Kung Kinakailangan (PRN)')}}</label>
                            </div>
                            <div class="text-muted ms-auto" style="font-size: 0.85rem;">
                                <i class="bi bi-info-circle"></i> {{ T::translate('No fixed time', 'Walang takdang oras')}}
                            </div>
                        </div>
                        
                        <!-- Additional Instructions -->
                        <div class="form-group mt-3">
                            <label for="editInstructions" class="form-label">
                                <i class="bi bi-journal-text"></i> {{ T::translate('Special Instructions', 'Espesyal na Tagubilin')}}
                            </label>
                            <textarea class="form-control" id="editInstructions" name="special_instructions" rows="3" 
                                placeholder="{{ T::translate('Enter any special instructions for administration...', 'Maglagay ng anumang espesyal na tagubilin para sa pangangasiwa...')}}"></textarea>
                        </div>
                        
                        <!-- Duration -->
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="editStartDate" class="form-label">
                                        <i class="bi bi-calendar-check"></i> {{ T::translate('Start Date', 'Petsa ng Simula')}}
                                    </label>
                                    <input type="date" class="form-control" id="editStartDate" name="start_date" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="editEndDate" class="form-label">
                                        <i class="bi bi-calendar-x"></i> {{ T::translate('End Date (Optional)', 'Petsa ng Pagtatapos (Opsyonal)')}}
                                    </label>
                                    <input type="date" class="form-control" id="editEndDate" name="end_date">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ T::translate('Cancel', 'Kanselahin')}}</button>
                    <button type="submit" form="editScheduleForm" class="btn btn-primary" id="updateScheduleBtn">
                        <i class="bi bi-check-lg me-1"></i> {{ T::translate('Update Medication Schedule', 'I-update ang Iskedyul ng Gamot')}}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Medication Schedule Modal -->
    <div class="modal fade" id="deleteMedicationModal" tabindex="-1" aria-labelledby="deleteMedicationModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteMedicationModalLabel">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i> {{ T::translate('Confirm Deletion', 'Kumpirmahin ang Pagtanggal')}}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="deleteModalErrors" class="alert alert-danger" style="display: none;">
                        <strong>{{ T::translate('Please correct the following:', 'Maaring itama ang sumusunod:')}}</strong>
                        <ul></ul>
                    </div>
                    
                    <form id="deleteMedicationForm" action="/care-manager/medication-schedule/delete" method="POST">
                        @csrf
                        <input type="hidden" name="medication_id" id="delete_medication_id">
                        
                        <div class="medication-info-display mb-3 p-3 border rounded bg-light">
                            <h6 class="mb-2">{{ T::translate('Medication details:', 'Detalye ng gamot:')}}</h6>
                            <div><strong>{{ T::translate('Beneficiary:', 'Benepisyaryo:')}}</strong> <span id="delete_beneficiary_name"></span></div>
                            <div><strong>{{ T::translate('Medication:', 'Gamot:')}}</strong> <span id="delete_medication_name"></span></div>
                            <div><strong>{{ T::translate('Dosage:', 'Dosis:')}}</strong> <span id="delete_medication_dosage"></span></div>
                        </div>
                        
                        <div class="alert alert-warning">
                            <i class="bi bi-lightbulb-fill me-2"></i>
                            <strong>{{ T::translate('Consider an alternative:', 'Isipin ang alternatibo:')}}</strong>{{ T::translate('If the medication course is simply complete, you can ', 'Kung kumpleto lang ang kurso ng gamot, magagawa mo')}} 
                            <a href="#" id="editInsteadLink" class="alert-link">{{ T::translate('edit the schedule', 'i-edit ang iskedyul')}} </a>{{ T::translate('and change its status to "Completed" instead of deleting it.', 'at baguhin ang status nito sa \"Natapos\" sa halip na tanggalin ito.')}} 
                        </div>
                        
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <strong>{{ T::translate('Warning:', 'Babala:')}}</strong> {{ T::translate('This action cannot be undone. Deleting this medication schedule will permanently remove it from the system\'s records and from the beneficiary\'s view.', 'Ang aksyon na ito ay hindi maaaring i-undo. Ang pagtanggal sa iskedyul ng gamot na ito ay permanenteng mag-aalis nito sa mga talaan ng system at mula sa pananaw ng benepisyaryo.')}}
                        </div>
                        
                        <p>{{ T::translate('Please enter your password to confirm deletion:', 'Maaring ilagay ang iyo\'ng password upang kumpirmahin ang pagtanggal:')}}</p>
                        
                        <div class="mb-3">
                            <label for="confirmation_password" class="form-label">{{ T::translate('Your Password', 'Ang Iyo\'ng Password')}}</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-key-fill"></i></span>
                                <input type="password" class="form-control" id="confirmation_password" name="password" required>
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                            <div class="form-text">{{ T::translate('Enter your password to confirm this action', 'Ilagay ang iyo\'ng password upang kumpirmahin ang aksyon')}}</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="delete_reason" class="form-label">{{ T::translate('Reason for deletion', 'Dahilan sa Pagtanggal')}} <span class="text-danger">*</span></label>
                            <textarea class="form-control" id="delete_reason" name="reason" rows="2" placeholder="{{ T::translate('Please provide a reason for deleting this medication schedule', 'Maaring magbigay nang dahilan sa pagtanggal ng iskedyul ng gamot na ito')}}" required></textarea>
                            <div class="form-text">{{ T::translate('This will be included in notifications sent to the beneficiary and family members', 'Ito ay isasama sa mga abiso na ipinadala sa benepisyaryo at mga miyembro ng pamilya')}}</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">{{ T::translate('Cancel', 'Kanselahin')}}</button>
                    <button type="button" id="confirmDeleteBtn" class="btn btn-danger">
                        <i class="bi bi-trash me-1"></i> {{ T::translate('Delete Medication Schedule', 'Tanggalin ang Iskedyul ng Gamot')}}
                    </button>
                </div>
            </div>
        </div>
    </div>
   
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize modal references
            var addScheduleModal = new bootstrap.Modal(document.getElementById('addScheduleModal'));
            var editScheduleModal = new bootstrap.Modal(document.getElementById('editScheduleModal'));
            
            // Only show add modal if there are errors but NOT from edit form
            @if(($errors->any() && !session('show_edit_modal')) || session('show_modal'))
                addScheduleModal.show();
            @endif
            
            // Only show edit modal if errors are from edit form
            @if($errors->any() && session('show_edit_modal'))
                editScheduleModal.show();
                
                // If we have an edit ID, load the data again
                @if(session('edit_id'))
                    fetchMedicationSchedule({{ session('edit_id') }});
                @endif
            @endif

            // Add this function to handle proper modal cleanup
            function clearModalBackdrops() {
                // Remove any modal backdrops that may be stuck
                const backdrops = document.querySelectorAll('.modal-backdrop');
                backdrops.forEach(backdrop => {
                    backdrop.remove();
                });
                
                // Also remove modal-open class from body
                document.body.classList.remove('modal-open');
                document.body.style.removeProperty('overflow');
                document.body.style.removeProperty('padding-right');
            }
            
            // Update the hidden event handlers for both modals
            document.getElementById('editScheduleModal').addEventListener('hidden.bs.modal', function() {
                document.getElementById('editModalErrors').style.display = 'none';
                clearModalBackdrops();
            });
            
            document.getElementById('addScheduleModal').addEventListener('hidden.bs.modal', function() {
                document.getElementById('modalErrors').style.display = 'none';
                clearModalBackdrops();
            });
            
            // Cleanup when clicking close buttons
            const closeButtons = document.querySelectorAll('[data-bs-dismiss="modal"]');
            closeButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Short timeout to allow Bootstrap to process the click first
                    setTimeout(clearModalBackdrops, 200);
                });
            });

            // Add a click handler for the "Add New Schedule" button
            document.querySelector('[data-bs-target="#addScheduleModal"]').addEventListener('click', function() {
                // First, clear any errors
                document.getElementById('modalErrors').style.display = 'none';
                document.getElementById('modalErrors').innerHTML = '<strong>{{ T::translate('Please correct the following:', 'Maaring itama ang sumusunod:')}}</strong>';
                
                // Reset form to clear any values
                const form = document.getElementById('addScheduleForm');
                form.reset();
                
                // Clear any hidden inputs that might have been added dynamically
                const hiddenInputs = form.querySelectorAll('input[type="hidden"]:not([name="_token"])');
                hiddenInputs.forEach(input => input.remove());
                
                // Reset dropdown selections properly
                document.getElementById('beneficiarySelect').selectedIndex = 0;
                document.getElementById('medicationType').selectedIndex = 0;
                
                // Force hide allergies alert (important to use both methods)
                const allergiesAlert = document.getElementById('allergiesAlert');
                allergiesAlert.style.display = 'none';
                allergiesAlert.setAttribute('style', 'display: none !important');
                
                // Reset all time fields to unchecked state
                document.getElementById('morningSwitch').checked = false;
                document.getElementById('noonSwitch').checked = false;
                document.getElementById('eveningSwitch').checked = false;
                document.getElementById('nightSwitch').checked = false;
                document.getElementById('asNeededSwitch').checked = false;
                
                // Reset all "with food" checkboxes
                document.getElementById('morningWithFood').checked = false;
                document.getElementById('noonWithFood').checked = false;
                document.getElementById('eveningWithFood').checked = false;
                document.getElementById('nightWithFood').checked = false;
                
                // Reset all time values to defaults
                document.getElementById('morningTime').value = '08:00';
                document.getElementById('noonTime').value = '13:00';
                document.getElementById('eveningTime').value = '18:00';
                document.getElementById('nightTime').value = '21:00';
                
                // Update time field enabled/disabled states
                updateTimeFields('morning');
                updateTimeFields('noon');
                updateTimeFields('evening');
                updateTimeFields('night');
                
                // Set current date as default for start date
                document.getElementById('startDate').valueAsDate = new Date();
                document.getElementById('endDate').value = '';
                
                // Clear instructions
                document.getElementById('instructions').value = '';
                
                // Ensure "As Needed" state is consistent
                if (document.getElementById('asNeededSwitch').checked) {
                    document.getElementById('asNeededSwitch').checked = false;
                    // Make sure time switches are enabled
                    document.getElementById('morningSwitch').disabled = false;
                    document.getElementById('noonSwitch').disabled = false;
                    document.getElementById('eveningSwitch').disabled = false;
                    document.getElementById('nightSwitch').disabled = false;
                }
            });
            
            // Client-side validation for medication name and dosage
            document.getElementById('medicationName').addEventListener('input', function() {
                if (this.value && !isNaN(this.value) && this.value.trim() !== '') {
                    this.setCustomValidity('{{ T::translate('Medication name cannot be purely numeric', 'Ang pangalan ng gamot ay hindi maaaring puro numero')}}');
                } else {
                    this.setCustomValidity('');
                }
            });

            document.getElementById('dosage').addEventListener('input', function() {
                if (this.value && !isNaN(this.value) && this.value.trim() !== '') {
                    this.setCustomValidity('{{ T::translate('Dosage must include units (e.g., 500mg, 10ml)', 'Ang dosis ay dapat maglaman ng mga unit (hal., 500mg, 10ml)')}}');
                } else {
                    this.setCustomValidity('');
                }
            });
            
            // Initialize modal reference for use in showing/hiding
            var addScheduleModal = new bootstrap.Modal(document.getElementById('addScheduleModal'));

            // When modal is hidden, clear errors
            document.getElementById('addScheduleModal').addEventListener('hidden.bs.modal', function () {
                document.getElementById('modalErrors').style.display = 'none';
            });

            // Reset errors when form is reset
            document.getElementById('addScheduleForm').addEventListener('reset', function() {
                document.getElementById('modalErrors').style.display = 'none';
            });

            // Set current date as default for start date
            document.getElementById('startDate').valueAsDate = new Date();

            // When the page loads, check if we need to display allergies for a previously selected beneficiary
            const beneficiarySelect = document.getElementById('beneficiarySelect');
            if (beneficiarySelect.value) {
                const selectedOption = beneficiarySelect.options[beneficiarySelect.selectedIndex];
                const allergiesData = selectedOption.dataset.allergies;
                
                // Handle allergies alert
                const allergiesAlert = document.getElementById('allergiesAlert');
                const allergiesContent = document.getElementById('allergiesContent');
                
                if (allergiesData && allergiesData !== 'null') {
                    allergiesContent.textContent = allergiesData;
                    allergiesAlert.style.display = 'flex !important';
                    allergiesAlert.removeAttribute('style');
                }
            }
            
            // Make sure time fields are properly enabled/disabled based on saved state
            const asNeeded = document.getElementById('asNeededSwitch').checked;
            if (asNeeded) {
                // Disable time switches if "as needed" was checked
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
                // Make sure time fields are enabled/disabled based on their checkbox state
                updateTimeFields('morning');
                updateTimeFields('noon');
                updateTimeFields('evening');
                updateTimeFields('night');
            }
            
            // Show/hide allergies alert based on beneficiary selection
            document.getElementById('beneficiarySelect').addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const allergiesData = selectedOption.dataset.allergies;
                
                // Handle allergies alert
                const allergiesAlert = document.getElementById('allergiesAlert');
                const allergiesContent = document.getElementById('allergiesContent');
                
                if (allergiesData && allergiesData !== 'null') {
                    allergiesContent.textContent = allergiesData;
                    allergiesAlert.style.display = 'flex !important';
                    allergiesAlert.removeAttribute('style');
                } else {
                    allergiesAlert.style.display = 'none !important';
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
            
            // Initialize time fields
            function updateTimeFields(timePeriod) {
                const isChecked = document.getElementById(timePeriod + 'Switch').checked;
                document.getElementById(timePeriod + 'Time').disabled = !isChecked;
                document.getElementById(timePeriod + 'WithFood').disabled = !isChecked;
            }
            
            // Initialize all time fields
            updateTimeFields('morning');
            updateTimeFields('noon');
            updateTimeFields('evening');
            updateTimeFields('night');
            
            // Form submission handler
            document.getElementById('addScheduleForm').addEventListener('submit', function(event) {
                // Check if at least one time or "as needed" is selected
                const asNeeded = document.getElementById('asNeededSwitch').checked;
                const morningChecked = document.getElementById('morningSwitch').checked;
                const noonChecked = document.getElementById('noonSwitch').checked;
                const eveningChecked = document.getElementById('eveningSwitch').checked;
                const nightChecked = document.getElementById('nightSwitch').checked;
                
                if (!asNeeded && !morningChecked && !noonChecked && !eveningChecked && !nightChecked) {
                    event.preventDefault();
                    
                    // Show the error in the modal errors section instead of an alert
                    const modalErrors = document.getElementById('modalErrors');
                    modalErrors.style.display = '';
                    
                    // Check if there's already a list
                    let errorList = modalErrors.querySelector('ul');
                    if (!errorList) {
                        errorList = document.createElement('ul');
                        modalErrors.appendChild(errorList);
                    }
                    
                    // Check if this specific error is already in the list
                    let errorExists = false;
                    const existingErrors = errorList.querySelectorAll('li');
                    existingErrors.forEach(item => {
                        if (item.textContent.includes('{{ T::translate('Please select at least one schedule time', 'Maaring pumimli ng kahit isang oras ng iskedyul')}}')) {
                            errorExists = true;
                        }
                    });
                    
                    // Add the error if it doesn't exist
                    if (!errorExists) {
                        const errorItem = document.createElement('li');
                        errorItem.textContent = '{{ T::translate('Please select at least one schedule time or "As Needed".', 'Maaring pumili ng kahit isang oras ng iskedyul o "Kung Kinakailangan"')}}';
                        errorList.appendChild(errorItem);
                    }
                    
                    return false;
                }
                
                // If a time checkbox is checked, make sure we send the corresponding time value
                if (morningChecked) {
                    const hiddenField = document.createElement('input');
                    hiddenField.type = 'hidden';
                    hiddenField.name = 'morning_time';
                    hiddenField.value = document.getElementById('morningTime').value;
                    this.appendChild(hiddenField);
                }
                
                if (noonChecked) {
                    const hiddenField = document.createElement('input');
                    hiddenField.type = 'hidden';
                    hiddenField.name = 'noon_time';
                    hiddenField.value = document.getElementById('noonTime').value;
                    this.appendChild(hiddenField);
                }
                
                if (eveningChecked) {
                    const hiddenField = document.createElement('input');
                    hiddenField.type = 'hidden';
                    hiddenField.name = 'evening_time';
                    hiddenField.value = document.getElementById('eveningTime').value;
                    this.appendChild(hiddenField);
                }
                
                if (nightChecked) {
                    const hiddenField = document.createElement('input');
                    hiddenField.type = 'hidden';
                    hiddenField.name = 'night_time';
                    hiddenField.value = document.getElementById('nightTime').value;
                    this.appendChild(hiddenField);
                }
            });
            
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

            // Initialize edit modal
            var editScheduleModal = new bootstrap.Modal(document.getElementById('editScheduleModal'));
            
            // Click handler for edit buttons
            document.querySelectorAll('.edit-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    fetchMedicationSchedule(id);
                });
            });
            
            // Function to fetch medication schedule data for editing
            function fetchMedicationSchedule(id) {
                fetch(`{{ route('care-manager.medication.schedule.edit', ':id') }}`.replace(':id', id))
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Network response was not ok');
                        }
                        return response.json();
                    })
                    .then(data => {
                        populateEditForm(data, id);
                        editScheduleModal.show();
                    })
                    .catch(error => {
                        console.error('Error fetching medication schedule:', error);
                        alert('{{ T::translate('Failed to load medication schedule data. Please try again.', 'Nabigong i-load ang datos ng iskedyul ng gamot. Pakisubukan muli.')}}');
                    });
            }
            
            // Function to populate the edit form with medication schedule data
            function populateEditForm(data, id) {
                // Clear previous form data
                document.getElementById('editScheduleForm').reset();
                
                // Set the form action URL
                document.getElementById('editScheduleForm').action = `{{ route('care-manager.medication.schedule.update', '') }}/${id}`;
                
                // Set the medication ID
                document.getElementById('edit_medication_id').value = id;

                // Store original values for comparison on submit
                const originalValues = {
                    beneficiaryId: data.beneficiary_id,
                    medicationName: data.medication_name,
                    dosage: data.dosage,
                    medicationType: data.medication_type.toLowerCase(),
                    status: data.status,
                    instructions: data.special_instructions || '',
                    startDate: data.formatted_start_date,
                    endDate: data.formatted_end_date || '',
                    asNeeded: data.as_needed,
                    morningTime: data.morning_time ? true : false,
                    withFoodMorning: data.with_food_morning,
                    noonTime: data.noon_time ? true : false,
                    withFoodNoon: data.with_food_noon,
                    eveningTime: data.evening_time ? true : false,
                    withFoodEvening: data.with_food_evening,
                    nightTime: data.night_time ? true : false,
                    withFoodNight: data.with_food_night
                };
                
                // Store original values as dataset attribute on the form
                const editForm = document.getElementById('editScheduleForm');
                editForm.dataset.originalValues = JSON.stringify(originalValues);
                
                // Set basic info
                document.getElementById('editBeneficiarySelect').value = data.beneficiary_id;
                document.getElementById('editMedicationName').value = data.medication_name;
                document.getElementById('editDosage').value = data.dosage;
                document.getElementById('editMedicationType').value = data.medication_type.toLowerCase();
                document.getElementById('editStatus').value = data.status;
                document.getElementById('editInstructions').value = data.special_instructions || '';
                
                // Use formatted dates for proper display in date inputs
                document.getElementById('editStartDate').value = data.formatted_start_date;
                document.getElementById('editEndDate').value = data.formatted_end_date || '';
                
                // Handle allergies alert
                const selectedOption = document.getElementById('editBeneficiarySelect').options[document.getElementById('editBeneficiarySelect').selectedIndex];
                const allergiesData = selectedOption ? selectedOption.dataset.allergies : null;
                const allergiesAlert = document.getElementById('editAllergiesAlert');
                const allergiesContent = document.getElementById('editAllergiesContent');
                
                if (allergiesData && allergiesData !== 'null') {
                    allergiesContent.textContent = allergiesData;
                    allergiesAlert.style.display = 'flex !important';
                    allergiesAlert.removeAttribute('style');
                } else {
                    allergiesAlert.style.display = 'none !important';
                }
                
                // Handle schedule times
                document.getElementById('editAsNeededSwitch').checked = data.as_needed;
                
                // Reset all time switches first
                document.getElementById('editMorningSwitch').checked = false;
                document.getElementById('editNoonSwitch').checked = false;
                document.getElementById('editEveningSwitch').checked = false;
                document.getElementById('editNightSwitch').checked = false;
                
                // Set time values if present
                if (data.morning_time) {
                    document.getElementById('editMorningSwitch').checked = true;
                    document.getElementById('editMorningTime').value = data.formatted_morning_time;
                    document.getElementById('editMorningWithFood').checked = data.with_food_morning;
                }
                
                if (data.noon_time) {
                    document.getElementById('editNoonSwitch').checked = true;
                    document.getElementById('editNoonTime').value = data.formatted_noon_time;
                    document.getElementById('editNoonWithFood').checked = data.with_food_noon;
                }
                
                if (data.evening_time) {
                    document.getElementById('editEveningSwitch').checked = true;
                    document.getElementById('editEveningTime').value = data.formatted_evening_time;
                    document.getElementById('editEveningWithFood').checked = data.with_food_evening;
                }
                
                if (data.night_time) {
                    document.getElementById('editNightSwitch').checked = true;
                    document.getElementById('editNightTime').value = data.formatted_night_time;
                    document.getElementById('editNightWithFood').checked = data.with_food_night;
                }
                
                // Handle "As needed" disabling of time fields
                handleEditAsNeeded();
                
                // Update time field states
                updateEditTimeFields('morning');
                updateEditTimeFields('noon');
                updateEditTimeFields('evening');
                updateEditTimeFields('night');
            }
            
            // Handle "As needed" switch in edit form
            document.getElementById('editAsNeededSwitch').addEventListener('change', handleEditAsNeeded);
            
            function handleEditAsNeeded() {
                const asNeeded = document.getElementById('editAsNeededSwitch').checked;
                if (asNeeded) {
                    // Disable other time switches
                    document.getElementById('editMorningSwitch').checked = false;
                    document.getElementById('editNoonSwitch').checked = false;
                    document.getElementById('editEveningSwitch').checked = false;
                    document.getElementById('editNightSwitch').checked = false;
                    
                    // Disable the time input fields
                    document.getElementById('editMorningSwitch').disabled = true;
                    document.getElementById('editNoonSwitch').disabled = true;
                    document.getElementById('editEveningSwitch').disabled = true;
                    document.getElementById('editNightSwitch').disabled = true;
                    
                    document.getElementById('editMorningTime').disabled = true;
                    document.getElementById('editNoonTime').disabled = true;
                    document.getElementById('editEveningTime').disabled = true;
                    document.getElementById('editNightTime').disabled = true;
                    
                    document.getElementById('editMorningWithFood').disabled = true;
                    document.getElementById('editNoonWithFood').disabled = true;
                    document.getElementById('editEveningWithFood').disabled = true;
                    document.getElementById('editNightWithFood').disabled = true;
                } else {
                    // Enable the time switches again
                    document.getElementById('editMorningSwitch').disabled = false;
                    document.getElementById('editNoonSwitch').disabled = false;
                    document.getElementById('editEveningSwitch').disabled = false;
                    document.getElementById('editNightSwitch').disabled = false;
                    
                    // Re-enable time fields based on their switch state
                    updateEditTimeFields('morning');
                    updateEditTimeFields('noon');
                    updateEditTimeFields('evening');
                    updateEditTimeFields('night');
                }
            }
            
            // Handle time switch controls in edit form
            ['morning', 'noon', 'evening', 'night'].forEach(function(time) {
                document.getElementById('edit' + time.charAt(0).toUpperCase() + time.slice(1) + 'Switch').addEventListener('change', function() {
                    updateEditTimeFields(time);
                });
            });
            
            // Update edit time fields based on switch state
            function updateEditTimeFields(timePeriod) {
                const capitalizedTime = timePeriod.charAt(0).toUpperCase() + timePeriod.slice(1);
                const isChecked = document.getElementById('edit' + capitalizedTime + 'Switch').checked;
                document.getElementById('edit' + capitalizedTime + 'Time').disabled = !isChecked;
                document.getElementById('edit' + capitalizedTime + 'WithFood').disabled = !isChecked;
            }
            
            // Form validation for edit
            document.getElementById('editScheduleForm').addEventListener('submit', function(event) {

                // Get the form reference and original values
                const form = this;
                const originalValues = JSON.parse(form.dataset.originalValues || '{}');
                
                // Get current values
                const currentValues = {
                    beneficiaryId: document.getElementById('editBeneficiarySelect').value,
                    medicationName: document.getElementById('editMedicationName').value,
                    dosage: document.getElementById('editDosage').value, 
                    medicationType: document.getElementById('editMedicationType').value,
                    status: document.getElementById('editStatus').value,
                    instructions: document.getElementById('editInstructions').value || '',
                    startDate: document.getElementById('editStartDate').value,
                    endDate: document.getElementById('editEndDate').value || '',
                    asNeeded: document.getElementById('editAsNeededSwitch').checked,
                    morningTime: document.getElementById('editMorningSwitch').checked,
                    withFoodMorning: document.getElementById('editMorningWithFood').checked,
                    noonTime: document.getElementById('editNoonSwitch').checked,
                    withFoodNoon: document.getElementById('editNoonWithFood').checked,
                    eveningTime: document.getElementById('editEveningSwitch').checked,
                    withFoodEvening: document.getElementById('editEveningWithFood').checked,
                    nightTime: document.getElementById('editNightSwitch').checked,
                    withFoodNight: document.getElementById('editNightWithFood').checked
                };
                
                // Check if anything has changed
                let hasChanges = false;
                for (const key in originalValues) {
                    if (originalValues[key] !== currentValues[key]) {
                        hasChanges = true;
                        break;
                    }
                }
                
                // If nothing has changed, show an error and prevent submission
                if (!hasChanges) {
                    event.preventDefault();
                    
                    const modalErrors = document.getElementById('editModalErrors');
                    modalErrors.style.display = '';
                    
                    let errorList = modalErrors.querySelector('ul');
                    if (!errorList) {
                        errorList = document.createElement('ul');
                        modalErrors.appendChild(errorList);
                    }
                    
                    // Clear previous errors
                    errorList.innerHTML = '';
                    
                    const errorItem = document.createElement('li');
                    errorItem.textContent = '{{ T::translate('No changes were made to the medication schedule.', 'Walang pagbabago ang ginawa sa iskedyul ng gamot.')}}';
                    errorList.appendChild(errorItem);
                    
                    return false;
                }
                
                // Client-side validation for medication name
                const medicationName = document.getElementById('editMedicationName').value;
                if (medicationName && !isNaN(medicationName) && medicationName.trim() !== '') {
                    event.preventDefault();
                    const modalErrors = document.getElementById('editModalErrors');
                    modalErrors.style.display = '';
                    
                    let errorList = modalErrors.querySelector('ul');
                    if (!errorList) {
                        errorList = document.createElement('ul');
                        modalErrors.appendChild(errorList);
                    }
                    
                    // Clear previous errors
                    errorList.innerHTML = '';
                    
                    const errorItem = document.createElement('li');
                    errorItem.textContent = '{{ T::translate('The medication name cannot be purely numeric.', 'Ang pangalan ng gamot ay hindi maaaring puro numero.')}}';
                    errorList.appendChild(errorItem);
                    return false;
                }
                
                // Client-side validation for dosage
                const dosage = document.getElementById('editDosage').value;
                if (dosage && !isNaN(dosage) && dosage.trim() !== '') {
                    event.preventDefault();
                    const modalErrors = document.getElementById('editModalErrors');
                    modalErrors.style.display = '';
                    
                    let errorList = modalErrors.querySelector('ul');
                    if (!errorList) {
                        errorList = document.createElement('ul');
                        modalErrors.appendChild(errorList);
                    }
                    
                    // Clear previous errors
                    errorList.innerHTML = '';
                    
                    const errorItem = document.createElement('li');
                    errorItem.textContent = '{{ T::translate('The dosage must include units (e.g., 500mg, 10ml).', 'Ang dosis ay dapat maglaman ng mga unit (hal., 500mg, 10ml).')}}';
                    errorList.appendChild(errorItem);
                    return false;
                }
                
                // Check if at least one time or "as needed" is selected
                const asNeeded = document.getElementById('editAsNeededSwitch').checked;
                const morningChecked = document.getElementById('editMorningSwitch').checked;
                const noonChecked = document.getElementById('editNoonSwitch').checked;
                const eveningChecked = document.getElementById('editEveningSwitch').checked;
                const nightChecked = document.getElementById('editNightSwitch').checked;
                
                if (!asNeeded && !morningChecked && !noonChecked && !eveningChecked && !nightChecked) {
                    event.preventDefault();
                    
                    const modalErrors = document.getElementById('editModalErrors');
                    modalErrors.style.display = '';
                    
                    let errorList = modalErrors.querySelector('ul');
                    if (!errorList) {
                        errorList = document.createElement('ul');
                        modalErrors.appendChild(errorList);
                    }
                    
                    // Clear previous errors
                    errorList.innerHTML = '';
                    
                    const errorItem = document.createElement('li');
                    errorItem.textContent = '{{ T::translate('Please select at least one schedule time or "As Needed".', 'Maaring pumili ng kahit isang oras ng iskedyul o "Kung Kinakailangan".')}}';
                    errorList.appendChild(errorItem);
                    
                    return false;
                }
                
                // If a time checkbox is checked, make sure we send the corresponding time value
                if (morningChecked) {
                    const hiddenField = document.createElement('input');
                    hiddenField.type = 'hidden';
                    hiddenField.name = 'morning_time';
                    hiddenField.value = document.getElementById('editMorningTime').value;
                    this.appendChild(hiddenField);
                }
                
                if (noonChecked) {
                    const hiddenField = document.createElement('input');
                    hiddenField.type = 'hidden';
                    hiddenField.name = 'noon_time';
                    hiddenField.value = document.getElementById('editNoonTime').value;
                    this.appendChild(hiddenField);
                }
                
                if (eveningChecked) {
                    const hiddenField = document.createElement('input');
                    hiddenField.type = 'hidden';
                    hiddenField.name = 'evening_time';
                    hiddenField.value = document.getElementById('editEveningTime').value;
                    this.appendChild(hiddenField);
                }
                
                if (nightChecked) {
                    const hiddenField = document.createElement('input');
                    hiddenField.type = 'hidden';
                    hiddenField.name = 'night_time';
                    hiddenField.value = document.getElementById('editNightTime').value;
                    this.appendChild(hiddenField);
                }
            });
            
            // Clear edit modal errors when modal is closed
            document.getElementById('editScheduleModal').addEventListener('hidden.bs.modal', function() {
                document.getElementById('editModalErrors').style.display = 'none';
            });

            // Initialize delete modal
            var deleteMedicationModal = new bootstrap.Modal(document.getElementById('deleteMedicationModal'));

            // Click handler for delete buttons
            document.querySelectorAll('.delete-btn').forEach(function(button) {
                button.addEventListener('click', function() {
                    // Get the medication data from data attributes
                    const id = this.dataset.id;
                    const beneficiaryName = this.dataset.beneficiary;
                    const medicationName = this.dataset.medication;
                    const dosage = this.dataset.dosage;
                    
                    // Set the values in the modal
                    document.getElementById('delete_medication_id').value = id;
                    document.getElementById('delete_beneficiary_name').textContent = beneficiaryName;
                    document.getElementById('delete_medication_name').textContent = medicationName;
                    document.getElementById('delete_medication_dosage').textContent = dosage;
                    
                    // Clear previous inputs
                    document.getElementById('confirmation_password').value = '';
                    document.getElementById('delete_reason').value = '';
                    document.getElementById('deleteModalErrors').style.display = 'none';
                    
                    // Set up the "edit instead" link to open edit modal
                    document.getElementById('editInsteadLink').onclick = function(e) {
                        e.preventDefault();
                        deleteMedicationModal.hide();
                        fetchMedicationSchedule(id);
                    };
                    
                    // Show modal
                    deleteMedicationModal.show();
                });
            });

            // Toggle password visibility
            document.getElementById('togglePassword').addEventListener('click', function() {
                const passwordField = document.getElementById('confirmation_password');
                const icon = this.querySelector('i');
                
                if (passwordField.type === 'password') {
                    passwordField.type = 'text';
                    icon.classList.remove('bi-eye');
                    icon.classList.add('bi-eye-slash');
                } else {
                    passwordField.type = 'password';
                    icon.classList.remove('bi-eye-slash');
                    icon.classList.add('bi-eye');
                }
            });

            // Handle delete form submission with password validation
            document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
                const password = document.getElementById('confirmation_password').value;
                const reason = document.getElementById('delete_reason').value.trim();
                const errorDisplay = document.getElementById('deleteModalErrors');

                // Clear previous errors
                errorDisplay.querySelector('ul').innerHTML = '';
                
                // Validation errors
                let hasErrors = false;
                
                // Basic validation
                if (!password) {
                    errorDisplay.style.display = '';
                    errorDisplay.querySelector('ul').innerHTML = '<li>{{ T::translate('Password is required to confirm deletion.', 'Kailangan ang password upang kumpirmahin ang pagtanggal.')}}</li>';
                    return;
                }

                // Reason validation
                if (!reason) {
                    hasErrors = true;
                    errorDisplay.style.display = '';
                    errorDisplay.querySelector('ul').innerHTML += '<li>{{ T::translate('Please provide a reason for deletion.', 'Maaring magbigay ng dahilan para sa pagtanggal.')}}</li>';
                }

                if (hasErrors) {
                    return;
                }
                            
                // Validate password via AJAX
                fetch('/care-manager/validate-password', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ password: password })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.valid) {
                        // Password is valid, submit the form
                        document.getElementById('deleteMedicationForm').submit();
                    } else {
                        // Password is invalid, show error
                        errorDisplay.style.display = '';
                        errorDisplay.querySelector('ul').innerHTML = '<li>{{ T::translate('The password you entered is incorrect.', 'Ang password na iyong inilagay ay hindi tama.')}}</li>';
                    }
                })
                .catch(error => {
                    console.error('Error validating password:', error);
                    errorDisplay.style.display = '';
                    errorDisplay.querySelector('ul').innerHTML = '<li>{{ T::translate('An error occurred while validating your password. Please try again.', 'May naganap na error habang pinapatunayan ang iyong password. Maaring subukan muli.')}}</li>';
                });
            });
        });
    </script>
</body>
</html>