<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Weekly Care Plan | Care Worker</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/viewWeeklyCareplan.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

    @php
    use App\Helpers\TranslationHelper as T;
    @endphp

    @include('components.careWorkerNavbar')
    @include('components.careWorkerSidebar')
    @include('components.modals.confirmDeleteWeeklyCareplan')
    @include('components.modals.deleteWeeklyCareplan')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="d-flex align-items-center flex-wrap mb-2">
                <!-- Back button -->
                <a href="{{ route('care-worker.reports') }}" class="btn btn-secondary btn-sm btn-action">
                    <i class="bi bi-arrow-bar-left"></i> {{ T::translate('Back', 'Bumalik') }}
                </a>
                
                <div class="text-center flex-grow-1" style="font-weight: bold; font-size: 20px;">
                    {{ T::translate('WEEKLY CARE PLAN DETAILS', 'MGA DETALYE NG WEEKLLY CARE PLAN') }}
                </div>
                
                <!-- Action buttons -->
                <a href="{{ route('care-worker.reports') }}" class="btn btn-secondary btn-sm btn-action d-inline-flex d-md-none">
                    <i class="bi bi-arrow-bar-left"></i> {{ T::translate('Back', 'Bumalik') }}
                </a>
                <div class="d-flex gap-2 action-buttons">
                    <a href="{{ route('care-worker.weeklycareplans.edit', $weeklyCareplan->weekly_care_plan_id) }}" class="btn btn-primary btn-sm btn-action">
                        <i class="bi bi-pencil-square"></i> {{ T::translate('Edit', 'I-edit') }}
                    </a>
                    <button type="button" class="btn btn-danger btn-sm btn-action" onclick="openDeleteModal('{{ $weeklyCareplan->weekly_care_plan_id }}', '{{ $weeklyCareplan->beneficiary->first_name }} {{ $weeklyCareplan->beneficiary->last_name }}')">
                        <i class="bi bi-trash"></i> {{ T::translate('Delete', 'Tanggalin') }}
                    </button>
                </div>
            </div>
            
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-check-circle-fill me-2" style="font-size: 1.25rem;"></i>
                        <strong>{{ T::translate('Success!', 'Tagumpay!') }}</strong> {{ session('success') }}
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            
            <div class="row" id="home-content">
                <!-- Personal Details Card -->
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="bi bi-person-vcard-fill icon-secondary me-2"></i>{{ T::translate('Personal Details', 'Personal na Detalye') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="detail-group">
                                    <div class="detail-label">
                                        {{ T::translate('Beneficiary Name', 'Pangalan ng Benepisyaryo') }}
                                    </div>
                                    <div class="detail-value">{{ $weeklyCareplan->beneficiary->first_name }} {{ $weeklyCareplan->beneficiary->last_name }}</div>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="detail-group">
                                    <div class="detail-label">
                                        {{ T::translate('Age', 'Edad') }}
                                    </div>
                                    <div class="detail-value">{{ \Carbon\Carbon::parse($weeklyCareplan->beneficiary->birthday)->age }} {{ T::translate('years old', 'taong gulang') }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="detail-group">
                                    <div class="detail-label">
                                        {{ T::translate('Birthdate', 'Petsa ng Kapanganakan') }}
                                    </div>
                                    <div class="detail-value">{{ $weeklyCareplan->beneficiary->birthday }}</div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="detail-group">
                                    <div class="detail-label">
                                        {{ T::translate('Gender', 'Kasarian') }}
                                    </div>
                                    <div class="detail-value">{{ $weeklyCareplan->beneficiary->gender }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <div class="detail-group">
                                    <div class="detail-label">
                                        {{ T::translate('Civil Status', 'Katayuan sa Pag-aasawa') }}
                                    </div>
                                    <div class="detail-value">{{ $weeklyCareplan->beneficiary->civil_status }}</div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="detail-group">
                                    <div class="detail-label">
                                        {{ T::translate('Address', 'Address') }}
                                    </div>
                                    <div class="detail-value">{{ $weeklyCareplan->beneficiary->street_address }}</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="detail-group">
                                    <div class="detail-label">
                                        {{ T::translate('Medical Conditions', 'Kondisyong Medikal') }}
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
                                        {{ T::translate('Illness', 'Sakit') }}
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
                        <h5 class="mb-0"><i class="bi bi-clipboard2-check-fill icon-secondary me-2"></i>{{ T::translate('Assessment', 'Pagtatasa') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="detail-group">
                            <div class="detail-label">
                                <i class="bi bi-journal-text icon-primary"></i>
                                {{ T::translate('Assessment Notes', 'Mga Tala ng Pagtatasa') }}
                            </div>
                            <div class="detail-value" style="min-height: 80px;">
                                {{ $weeklyCareplan->assessment ?? T::translate('No assessment recorded', 'Walang assessment na naitala') }}
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Vitals and Photo Section -->
                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-heart-pulse-fill icon-secondary me-2"></i>{{ T::translate('Vital Signs & Photo Documentation', 'Vital Signs at Photo Documentation') }}</h5>
                    </div>
                    <div class="card-body vitals-photo-container">
                        <div class="vitals-col">
                            <div class="detail-group">
                                <div class="detail-label">
                                    <i class="bi bi-activity icon-primary"></i>
                                    {{ T::translate('Vital Signs', 'Vital Signs') }}
                                </div>
                                <div class="vital-signs-container">
                                    <div class="vital-sign">
                                        <span class="vital-label">
                                            <i class="bi bi-speedometer2"></i>
                                            {{ T::translate('Blood Pressure', 'Presyon ng Dugo') }}
                                        </span>
                                        <span class="vital-value">{{ $weeklyCareplan->vitalSigns->blood_pressure ?? 'N/A' }}</span>
                                    </div>
                                    <div class="vital-sign">
                                        <span class="vital-label">
                                            <i class="bi bi-thermometer-half"></i>
                                            {{ T::translate('Body Temperature', 'Temperatura ng Katawan') }}
                                        </span>
                                        <span class="vital-value">{{ $weeklyCareplan->vitalSigns->body_temperature ?? 'N/A' }}</span>
                                    </div>
                                    <div class="vital-sign">
                                        <span class="vital-label">
                                            <i class="bi bi-heart-pulse"></i>
                                            {{ T::translate('Pulse Rate', 'Bilis ng Puso') }}
                                        </span>
                                        <span class="vital-value">{{ $weeklyCareplan->vitalSigns->pulse_rate ?? 'N/A' }}</span>
                                    </div>
                                    <div class="vital-sign">
                                        <span class="vital-label">
                                            <i class="bi bi-lungs-fill"></i>
                                            {{ T::translate('Respiratory Rate', 'Bilis ng Paghinga') }}
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
                                    {{ T::translate('Photo Documentation', 'Photo Documentation') }}
                                </div>
                                <div class="photo-container">
                                    @if($weeklyCareplan->photo_path)
                                        <img src="{{ asset('storage/' . $weeklyCareplan->photo_path) }}" alt="Weekly Care Plan Photo">
                                    @else
                                        <div class="text-center text-muted">
                                            <i class="bi bi-image" style="font-size: 2rem; opacity: 0.5;"></i>
                                            <p class="mt-2">{{ T::translate('No image available', 'Walang larawan na available') }}</p>
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
                        <h5 class="mb-0"><i class="bi bi-clipboard2-data-fill icon-secondary me-2"></i>{{ T::translate('Evaluation and Recommendations', 'Ebalwasyon at Rekomendasyon') }}</h5>
                    </div>
                    <div class="card-body">
                        <div class="detail-group">
                            <div class="detail-value">
                                @if($weeklyCareplan->evaluation_recommendations)
                                    <i class="bi bi-chat-square-text-fill icon-secondary me-2"></i>
                                    {{ $weeklyCareplan->evaluation_recommendations }}
                                @else
                                    {{ T::translate('No evaluation recorded', 'Walang ebalwasyon na naitala') }}
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Care Needs and Interventions Section -->
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0"><i class="bi bi-clipboard2-plus-fill icon-secondary me-2"></i>{{ T::translate('Care Plan Interventions', 'Mga Interbensyong Isinagawa') }}</h5>
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
                                                @if(!empty($useTagalog) && $useTagalog && isset($tagalogInterventions[$intervention->intervention_id]))
                                                    {{ $tagalogInterventions[$intervention->intervention_id]->t_intervention_description }}
                                                @else
                                                    {{ $intervention->intervention_description }}
                                                @endif
                                            </span>
                                            <span class="time-badge">
                                                <i class="bi bi-clock-history"></i>
                                                {{ $intervention->duration_minutes }} {{ T::translate('min', 'min') }}
                                            </span>
                                        </div>
                                    @endforeach
                                    
                                    {{-- Custom interventions (description in weekly_care_plan_interventions) --}}
                                    @foreach($categoryCustInterventions as $custom)
                                        <div class="intervention-item">
                                            <span>
                                                <i class="bi bi-stars icon-accent" style="font-size: 0.8rem;"></i>
                                                {{ $custom->intervention_description }}
                                                <span class="custom-badge">{{ T::translate('Custom', 'Custom') }}</span>
                                            </span>
                                            <span class="time-badge">
                                                <i class="bi bi-clock-history"></i>
                                                {{ $custom->duration_minutes }} {{ T::translate('min', 'min') }}
                                            </span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @endforeach
                        
                        <div class="total-time">
                            <i class="bi bi-stopwatch-fill icon-primary"></i>
                            {{ T::translate('Total Care Time:', 'Kabuuang Oras ng Pangangalaga:') }} 
                            @php
                                $standardMinutes = (float)$interventionsByCategory->flatten()->sum('duration_minutes') ?? 0;
                                $customMinutes = (float)$customInterventions->sum('duration_minutes') ?? 0;
                                $totalMinutes = $standardMinutes + $customMinutes;
                                $formattedTotal = number_format($totalMinutes, 2);
                            @endphp
                            <strong>{{ $formattedTotal }} {{ T::translate('minutes', 'minuto') }}</strong>
                        </div>
                        
                        <div class="acknowledgement">
                            @if($weeklyCareplan->acknowledged_by_beneficiary && $weeklyCareplan->acknowledgedByBeneficiary)
                                <i class="bi bi-check-circle-fill icon-secondary"></i> 
                                <strong>{{ T::translate('Acknowledged by:', 'Kinilala ni:') }}</strong> {{ $weeklyCareplan->acknowledgedByBeneficiary->first_name }} {{ $weeklyCareplan->acknowledgedByBeneficiary->last_name }} ({{ T::translate('Beneficiary', 'Benepisyaryo') }})
                            @elseif($weeklyCareplan->acknowledged_by_family && $weeklyCareplan->acknowledgedByFamily)
                                <i class="bi bi-check-circle-fill icon-secondary"></i> 
                                <strong>{{ T::translate('Acknowledged by:', 'Kinilala ni:') }}</strong> {{ $weeklyCareplan->acknowledgedByFamily->first_name }} {{ $weeklyCareplan->acknowledgedByFamily->last_name }} ({{ T::translate('Family Member', 'Miyembro ng Pamilya') }})
                            @elseif($weeklyCareplan->acknowledgement_signature)
                                <i class="bi bi-check-circle-fill icon-secondary"></i> 
                                <strong>{{ T::translate('Acknowledged with signature', 'Kinilala ng may lagda') }}</strong>
                                <a href="#" data-bs-toggle="modal" data-bs-target="#signatureModal">({{ T::translate('View', 'Tingnan') }})</a>
                            @else
                                <i class="bi bi-exclamation-circle-fill icon-accent"></i> 
                                <strong>{{ T::translate('Not Acknowledged', 'Di-Kinilala') }}</strong>
                            @endif
                        </div>
                    </div>

                    <hr class="my-4">

                    @if($weeklyCareplan->acknowledged_by_beneficiary || $weeklyCareplan->acknowledged_by_family || $weeklyCareplan->acknowledgement_signature)
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-success text-white">
                                        <h5 class="mb-0">{{ T::translate('Acknowledgement Details', 'Detalye ng Pagkilala') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="row">
                                            @if($weeklyCareplan->acknowledged_by_beneficiary && $weeklyCareplan->acknowledgedByBeneficiary)
                                                <div class="col-md-4">
                                                    <p class="mb-1"><strong>{{ T::translate('Acknowledged By:', 'Kinilala Ni:') }}</strong></p>
                                                    <p>{{ $weeklyCareplan->acknowledgedByBeneficiary->first_name }} {{ $weeklyCareplan->acknowledgedByBeneficiary->last_name }}</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p class="mb-1"><strong>{{ T::translate('Role:', 'Papel:') }}</strong></p>
                                                    <p>{{ T::translate('Beneficiary', 'Benepisyaryo') }}</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p class="mb-1"><strong>{{ T::translate('Date:', 'Petsa:') }}</strong></p>
                                                    <p>{{ \Carbon\Carbon::parse($weeklyCareplan->beneficiary_acknowledged_at)->format('M d, Y g:i A') }}</p>
                                                </div>
                                            @elseif($weeklyCareplan->acknowledged_by_family && $weeklyCareplan->acknowledgedByFamily)
                                                <div class="col-md-4">
                                                    <p class="mb-1"><strong>{{ T::translate('Acknowledged By:', 'Kinilala Ni:') }}</strong></p>
                                                    <p>{{ $weeklyCareplan->acknowledgedByFamily->first_name }} {{ $weeklyCareplan->acknowledgedByFamily->last_name }}</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p class="mb-1"><strong>{{ T::translate('Role:', 'Papel:') }}</strong></p>
                                                    <p>{{ T::translate('Family Member', 'Miyembro ng Pamilya') }}</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p class="mb-1"><strong>{{ T::translate('Date:', 'Petsa:') }}</strong></p>
                                                    <p>{{ \Carbon\Carbon::parse($weeklyCareplan->family_acknowledged_at)->format('M d, Y g:i A') }}</p>
                                                </div>
                                            @elseif($weeklyCareplan->acknowledgement_signature)
                                                @php
                                                    $signatureData = json_decode($weeklyCareplan->acknowledgement_signature, true);
                                                @endphp
                                                <div class="col-md-4">
                                                    <p class="mb-1"><strong>{{ T::translate('Acknowledged By:', 'Kinilala Ni:') }}</strong></p>
                                                    <p>{{ $signatureData['name'] ?? T::translate('Unknown', 'Hindi Kilala') }}</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p class="mb-1"><strong>{{ T::translate('Role:', 'Papel:') }}</strong></p>
                                                    <p>{{ $signatureData['acknowledged_by'] ?? T::translate('Unknown', 'Hindi Kilala') }}</p>
                                                </div>
                                                <div class="col-md-4">
                                                    <p class="mb-1"><strong>{{ T::translate('Date:', 'Petsa:') }}</strong></p>
                                                    <p>{{ isset($signatureData['date']) ? \Carbon\Carbon::parse($signatureData['date'])->format('M d, Y g:i A') : T::translate('Unknown', 'Hindi Kilala') }}</p>
                                                </div>
                                                
                                                @if(isset($signatureData['signature']))
                                                    <div class="col-12 mt-3">
                                                        <p class="mb-1"><strong>{{ T::translate('Signature:', 'Lagda:') }}</strong></p>
                                                        <div class="border p-3">
                                                            <img src="{{ $signatureData['signature'] }}" alt="Signature" class="img-fluid" style="max-height: 100px;">
                                                        </div>
                                                    </div>
                                                @endif
                                            @endif
                                            
                                            <div class="col-12 mt-3">
                                                <p class="mb-1"><strong>{{ T::translate('Acknowledgement Statement:', 'Pahayag sa Pagkilala:') }}</strong></p>
                                                <p class="fst-italic">
                                                    "{{ T::translate('By acknowledging this care plan, the person named above confirms they have thoroughly reviewed all of the information in this care plan, understand the assessment, care needs, and interventions outlined for the beneficiary, and agree with the care plan as documented.', 'Sa pamamagitan ng pagkilala sa care plan na ito, ang taong pinangalanan sa itaas ay kumpirmadong lubusang nirepaso ang lahat ng impormasyon sa care plan na ito, nauunawaan ang assessment, pangangailangan ng pangangalaga, at mga interbensyon na nakabalangkas para sa benepisyaryo, at sumasang-ayon sa care plan gaya ng nakadokumento.') }}"
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-warning text-white">
                                        <h5 class="mb-0">{{ T::translate('Acknowledgement Pending', 'Nakabinbin ang Pagkilala') }}</h5>
                                    </div>
                                    <div class="card-body">
                                        <p>{{ T::translate('This care plan has not been acknowledged yet by the beneficiary or family member.', 'Ang care plan na ito ay hindi pa kinikilala ng benepisyaryo o miyembro ng pamilya.') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                    
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