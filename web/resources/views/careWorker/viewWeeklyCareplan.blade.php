<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Care Plan | Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/viewWeeklyCareplan.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
</head>
<body>

    @include('components.careWorkerNavbar')
    @include('components.careWorkerSidebar')
    @include('components.modals.confirmDeleteWeeklyCareplan')
    @include('components.modals.deleteWeeklyCareplan')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="d-flex align-items-center flex-wrap mb-2">
                <!-- Back button -->
                <a href="{{ route('care-worker.reports') }}" class="btn btn-secondary btn-sm btn-action">
                    <i class="bi bi-arrow-bar-left"></i> Back
                </a>
                
                <div class="text-center flex-grow-1" style="font-weight: bold; font-size: 20px;">
                    Weekly Care Plan Details
                </div>
                
                <!-- Action buttons -->
                <a href="{{ route('care-worker.reports') }}" class="btn btn-secondary btn-sm btn-action d-inline-flex d-md-none">
                    <i class="bi bi-arrow-bar-left"></i> Back
                </a>
                <div class="d-flex gap-2 action-buttons">
                    <a href="{{ route('care-worker.weeklycareplans.edit', $weeklyCareplan->weekly_care_plan_id) }}" class="btn btn-primary btn-sm btn-action">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                    <button type="button" class="btn btn-danger btn-sm btn-action" onclick="openDeleteModal('{{ $weeklyCareplan->weekly_care_plan_id }}', '{{ $weeklyCareplan->beneficiary->first_name }} {{ $weeklyCareplan->beneficiary->last_name }}')">
                        <i class="bi bi-trash"></i> Delete
                    </button>
                </div>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2" style="font-size: 1.25rem;"></i>
                        <strong>Success!</strong> {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <div class="row" id="home-content">
                <!-- Personal Details Card -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-person-vcard-fill icon-secondary me-2"></i>Personal Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="detail-group">
                                    <div class="detail-label">
                                        Beneficiary Name
                                    </div>
                                    <div class="detail-value">{{ $weeklyCareplan->beneficiary->first_name }} {{ $weeklyCareplan->beneficiary->last_name }}</div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="detail-group">
                                    <div class="detail-label">
                                        Age
                                    </div>
                                    <div class="detail-value">{{ \Carbon\Carbon::parse($weeklyCareplan->beneficiary->birthday)->age }} years old</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="detail-group">
                                    <div class="detail-label">
                                        Birthdate
                                    </div>
                                    <div class="detail-value">{{ $weeklyCareplan->beneficiary->birthday }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="detail-group">
                                    <div class="detail-label">
                                        Gender
                                    </div>
                                    <div class="detail-value">{{ $weeklyCareplan->beneficiary->gender }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="detail-group">
                                    <div class="detail-label">
                                        Civil Status
                                    </div>
                                    <div class="detail-value">{{ $weeklyCareplan->beneficiary->civil_status }}</div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="detail-group">
                                    <div class="detail-label">
                                        Address
                                    </div>
                                    <div class="detail-value">{{ $weeklyCareplan->beneficiary->street_address }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-group">
                                    <div class="detail-label">
                                        Medical Conditions
                                    </div>
                                    <div class="detail-value">
                                        @php
                                            $medicalConditionsText = $weeklyCareplan->beneficiary->generalCarePlan->healthHistory->medical_conditions ?? '';
                                            if ($medicalConditionsText) {
                                                try {
                                                    $conditionsArray = json_decode($medicalConditionsText, true);
                                                    if (is_array($conditionsArray)) {
                                                        echo implode(', ', $conditionsArray);
                                                    } else {
                                                        echo $medicalConditionsText;
                                                    }
                                                } catch (\Exception $e) {
                                                    echo $medicalConditionsText;
                                                }
                                            }
                                        @endphp
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="detail-group">
                                    <div class="detail-label">
                                        Illness
                                    </div>
                                    <div class="detail-value">
                                        @php
                                            if ($weeklyCareplan->illnesses) {
                                                $illnesses = is_string($weeklyCareplan->illnesses) ? json_decode($weeklyCareplan->illnesses, true) : $weeklyCareplan->illnesses;
                                                
                                                if (is_array($illnesses)) {
                                                    echo implode(', ', $illnesses);
                                                } else {
                                                    echo $weeklyCareplan->illnesses;
                                                }
                                            }
                                        @endphp
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Assessment Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-clipboard2-check-fill icon-secondary me-2"></i>Assessment</h5>
                    </div>
                    <div class="card-body">
                        <div class="detail-group">
                            <div class="detail-label">
                                <i class="bi bi-journal-text icon-primary"></i>
                                Assessment Notes
                            </div>
                            <div class="detail-value" style="min-height: 80px;">
                                {{ $weeklyCareplan->assessment ?? 'No assessment recorded' }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Vitals and Photo Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-heart-pulse-fill icon-secondary me-2"></i>Vital Signs & Photo Documentation</h5>
                    </div>
                    <div class="card-body vitals-photo-container">
                        <div class="vitals-col">
                            <div class="detail-group">
                                <div class="detail-label">
                                    <i class="bi bi-activity icon-primary"></i>
                                    Vital Signs
                                </div>
                                <div class="vital-signs-container">
                                    <div class="vital-sign">
                                        <span class="vital-label">
                                            <i class="bi bi-speedometer2"></i>
                                            Blood Pressure
                                        </span>
                                        <span class="vital-value">{{ $weeklyCareplan->vitalSigns->blood_pressure ?? 'N/A' }}</span>
                                    </div>
                                    <div class="vital-sign">
                                        <span class="vital-label">
                                            <i class="bi bi-thermometer-half"></i>
                                            Body Temperature
                                        </span>
                                        <span class="vital-value">{{ $weeklyCareplan->vitalSigns->body_temperature ?? 'N/A' }}</span>
                                    </div>
                                    <div class="vital-sign">
                                        <span class="vital-label">
                                            <i class="bi bi-heart-pulse"></i>
                                            Pulse Rate
                                        </span>
                                        <span class="vital-value">{{ $weeklyCareplan->vitalSigns->pulse_rate ?? 'N/A' }}</span>
                                    </div>
                                    <div class="vital-sign">
                                        <span class="vital-label">
                                            <i class="bi bi-lungs-fill"></i>
                                            Respiratory Rate
                                        </span>
                                        <span class="vital-value">{{ $weeklyCareplan->vitalSigns->respiratory_rate ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="photo-col">
                            <div class="detail-group">
                                <div class="detail-label">
                                    <i class="bi bi-camera-fill icon-primary"></i>
                                    Photo Documentation
                                </div>
                                <div class="photo-container">
                                    @if($weeklyCareplan->photo_path)
                                        <img src="{{ asset('storage/' . $weeklyCareplan->photo_path) }}" alt="Weekly Care Plan Photo">
                                    @else
                                        <div class="text-center text-muted">
                                            <i class="bi bi-image" style="font-size: 2rem; opacity: 0.5;"></i>
                                            <p class="mt-2">No image available</p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Evaluation Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-clipboard2-data-fill icon-secondary me-2"></i>Evaluation and Recommendations</h5>
                    </div>
                    <div class="card-body">
                        <div class="detail-group">
                            <div class="detail-value">
                                @if($weeklyCareplan->evaluation_recommendations)
                                    <i class="bi bi-chat-square-text-fill icon-secondary me-2"></i>
                                    {{ $weeklyCareplan->evaluation_recommendations }}
                                @else
                                    No evaluation recorded
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Care Needs and Interventions Section -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-clipboard2-plus-fill icon-secondary me-2"></i>Care Plan Interventions</h5>
                    </div>
                    <div class="card-body">
                        @foreach($categories as $category)
                            @php
                                $categoryInterventions = $interventionsByCategory->get($category->care_category_id, collect());
                                $categoryCustInterventions = $customInterventions->where('care_category_id', $category->care_category_id);
                                $totalInterventions = $categoryInterventions->count() + $categoryCustInterventions->count();
                            @endphp

                            @if($totalInterventions > 0)
                                <div class="intervention-card">
                                    <div class="intervention-category">
                                        <i class="bi bi-list-check"></i>
                                        {{ $category->care_category_name }}
                                    </div>
                                    
                                    {{-- Standard interventions (from interventions table) --}}
                                    @foreach($categoryInterventions as $intervention)
                                        <div class="intervention-item">
                                            <span>
                                                <i class="bi bi-check-circle-fill icon-secondary" style="font-size: 0.8rem;"></i>
                                                {{ $intervention->intervention_description }}
                                            </span>
                                            <span class="time-badge">
                                                <i class="bi bi-clock-history"></i>
                                                {{ $intervention->duration_minutes }} min
                                            </span>
                                        </div>
                                    @endforeach
                                    
                                    {{-- Custom interventions (description in weekly_care_plan_interventions) --}}
                                    @foreach($categoryCustInterventions as $custom)
                                        <div class="intervention-item">
                                            <span>
                                                <i class="bi bi-stars icon-accent" style="font-size: 0.8rem;"></i>
                                                {{ $custom->intervention_description }}
                                                <span class="custom-badge">Custom</span>
                                            </span>
                                            <span class="time-badge">
                                                <i class="bi bi-clock-history"></i>
                                                {{ $custom->duration_minutes }} min
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                        
                        <div class="total-time">
                            <i class="bi bi-stopwatch-fill icon-primary"></i>
                            Total Care Time: 
                            @php
                                $standardMinutes = (float)$interventionsByCategory->flatten()->sum('duration_minutes') ?? 0;
                                $customMinutes = (float)$customInterventions->sum('duration_minutes') ?? 0;
                                $totalMinutes = $standardMinutes + $customMinutes;
                                $formattedTotal = number_format($totalMinutes, 2);
                            @endphp
                            <strong>{{ $formattedTotal }} minutes</strong>
                        </div>
                        
                        <div class="acknowledgement">
                            @if($weeklyCareplan->acknowledged_by_beneficiary && $weeklyCareplan->acknowledgedByBeneficiary)
                                <i class="bi bi-check-circle-fill icon-secondary"></i> 
                                <strong>Acknowledged by:</strong> {{ $weeklyCareplan->acknowledgedByBeneficiary->first_name }} {{ $weeklyCareplan->acknowledgedByBeneficiary->last_name }} (Beneficiary)
                            @elseif($weeklyCareplan->acknowledged_by_family && $weeklyCareplan->acknowledgedByFamily)
                                <i class="bi bi-check-circle-fill icon-secondary"></i> 
                                <strong>Acknowledged by:</strong> {{ $weeklyCareplan->acknowledgedByFamily->first_name }} {{ $weeklyCareplan->acknowledgedByFamily->last_name }} (Family Member)
                            @elseif($weeklyCareplan->acknowledgement_signature)
                                <i class="bi bi-check-circle-fill icon-secondary"></i> 
                                <strong>Acknowledged with signature</strong>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#signatureModal">(View)</a>
                            @else
                                <i class="bi bi-exclamation-circle-fill icon-accent"></i> 
                                <strong>Not Acknowledged</strong>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    <script>
        // Function to open the delete confirmation modal
        function openDeleteModal(id, name) {
            // Set values for the modal
            document.getElementById('initialWeeklyCarePlanIdToDelete').value = id;
            document.getElementById('initialBeneficiaryNameToDelete').textContent = name;
            
            // Show the confirmation modal
            const modal = new bootstrap.Modal(document.getElementById('confirmDeleteWeeklyCarePlanModal'));
            modal.show();
        }
    </script>
   
</body>
</html>