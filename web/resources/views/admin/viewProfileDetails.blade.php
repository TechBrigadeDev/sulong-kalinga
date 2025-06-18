<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $beneficiary->first_name }} {{ $beneficiary->last_name }} | Profile Details</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/profileDetails.css') }}">
    <link rel="stylesheet" href="{{ asset('css/viewProfileDetails.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
</head>
<body>
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp
    @include('components.adminNavbar')
    @include('components.adminSidebar')
    @include('components.modals.statusChangeBeneficiary')
    @include('components.modals.deleteBeneficiary')
    
    <div class="home-section">
        <div class="container-fluid">
            <!-- Header Section -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-2 gap-2">
                <a href="{{ route('admin.beneficiaries.index') }}" class="btn btn-secondary desktop-back-btn align-self-start align-self-md-center">
                    <i class="bi bi-arrow-left"></i> {{ T::translate('Back', 'Bumalik')}}
                </a>
                <h4 class="mb-0 text-center" style="font-weight: bold;">
                    {{ T::translate('BENEFICIARY PROFILE DETAILS', 'DETALYE SA PROFILE NG BENEPISSYARYO')}}
                </h4>
                <div class="d-flex gap-2 align-self-end align-self-md-center header-buttons">
                    <a href="{{ route('admin.beneficiaries.index') }}" class="btn btn-secondary mobile-back-btn" style="height: 33px;">
                        <i class="bi bi-arrow-left"></i> {{ T::translate('Back', 'Bumalik')}}
                    </a>
                    <a href="{{ route('admin.beneficiaries.edit', $beneficiary->beneficiary_id) }}" class="btn btn-primary">
                        <i class="bi bi-pencil-square me-1"></i> {{ T::translate('Edit', 'I-Edit')}}
                    </a>
                    <button type="button" class="btn btn-danger" onclick="openDeleteBeneficiaryModal('{{ $beneficiary->beneficiary_id }}', '{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}')">
                        <i class="bi bi-trash me-1"></i> {{ T::translate('Delete', 'Tanggalin')}}
                    </button>
                </div>
            </div>
            <div class="row" id="home-content">
            <!-- Profile Header Card -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center mb-4 mb-md-0">
                            <img src="{{ $photoUrl ?? asset('images/defaultProfile.png') }}"
                                alt="Profile Picture" 
                                class="img-fluid rounded-circle profile-img">
                        </div>
                        <div class="col-md-9">
                            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center align-items-md-start profile-header-content">
                                <div class="text-center text-md-start mb-3 mb-md-0">
                                    <h3 class="mb-1" style="color: var(--secondary-color);">
                                        {{ $beneficiary->first_name }} {{ $beneficiary->last_name }}
                                    </h3>
                                    <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2">
                                        <span class="badge rounded-pill bg-light text-dark">
                                            <i class="bi bi-tag me-1"></i> {{ $beneficiary->category->category_name }}
                                        </span>
                                        <span class="badge rounded-pill {{ $beneficiary->status->status_name == 'Active' ? 'badge-active' : 'badge-inactive' }}">
                                            {{ $beneficiary->status->status_name }}
                                        </span>
                                    </div>
                                    <p class="text-muted mt-2 mb-0">
                                        <i class="bi bi-calendar3 me-1"></i> {{ T::translate('Beneficiary since', 'Benepisyaryo magmula')}} {{ $beneficiary->created_at->format('F j, Y') }}
                                    </p>
                                </div>
                                <div class="mt-2 mt-md-0 status-select-container">
                                    <select class="form-select status-select px-5 text-center" 
                                            id="statusSelect{{ $beneficiary->beneficiary_id }}" 
                                            name="status" 
                                            onchange="openStatusChangeModal(this, 'Beneficiary', {{ $beneficiary->beneficiary_id }}, '{{ $beneficiary->status->status_name }}')">
                                        <option value="Active" {{ $beneficiary->status->status_name == 'Active' ? 'selected' : '' }}>{{ T::translate('Active', 'Aktibo')}}</option>
                                        <option value="Inactive" {{ $beneficiary->status->status_name == 'Inactive' ? 'selected' : '' }}>{{ T::translate('Inactive', 'Di-Aktibo')}}</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Content Tabs -->
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <ul class="nav nav-tabs px-3 pt-2" id="profileTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="personal-tab" data-bs-toggle="tab" data-bs-target="#personal" type="button" role="tab">
                                <i class="bi bi-person-lines-fill me-1"></i> {{ T::translate('Persona Details', 'Personal na Detalye')}}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="medical-tab" data-bs-toggle="tab" data-bs-target="#medical" type="button" role="tab">
                                <i class="bi bi-heart-pulse me-1"></i> {{ T::translate('Medical Details', 'Medikal na Detalye')}}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="care-tab" data-bs-toggle="tab" data-bs-target="#care" type="button" role="tab">
                                <i class="bi bi-clipboard2-pulse me-1"></i> Care Plan
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="documents-tab" data-bs-toggle="tab" data-bs-target="#documents" type="button" role="tab">
                                <i class="bi bi-file-earmark-text me-1"></i> {{ T::translate('Documents', 'Mga Dokumento')}}
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content p-4" id="profileTabsContent">
                        <!-- Personal Details Tab -->
                        <div class="tab-pane fade show active" id="personal" role="tabpanel">
                            <div class="row g-4">
                                <!-- Basic Information -->
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0"><i class="bi bi-info-circle me-2"></i>{{ T::translate('Basic Information', 'Pangunahing Impormasyon')}}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold text-muted">{{ T::translate('Age', 'Edad')}}:</div>
                                                <div class="col-sm-8">{{ \Carbon\Carbon::parse($beneficiary->birthday)->age }} years old</div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold text-muted">{{ T::translate('Birthday', 'Kaarawan')}}:</div>
                                                <div class="col-sm-8">{{ \Carbon\Carbon::parse($beneficiary->birthday)->format('F j, Y') }}</div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold text-muted">{{ T::translate('Gender', 'Kasarian')}}:</div>
                                                <div class="col-sm-8">{{ $beneficiary->gender }}</div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold text-muted">{{ T::translate('Civil Status', 'Katayuan sa Pag-aasawa')}}:</div>
                                                <div class="col-sm-8">{{ $beneficiary->civil_status }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Contact Information -->
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0"><i class="bi bi-telephone me-2"></i>Contact Information</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold text-muted">Username:</div>
                                                <div class="col-sm-8">{{ $beneficiary->username }}</div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold text-muted">Mobile:</div>
                                                <div class="col-sm-8">{{ $beneficiary->mobile }}</div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold text-muted">Landline:</div>
                                                <div class="col-sm-8">{{ $beneficiary->landline ?? 'N/A' }}</div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-sm-4 fw-bold text-muted">{{ T::translate('Address', 'Tirahan')}}:</div>
                                                <div class="col-sm-8">{{ $beneficiary->street_address }}</div>
                                            </div>
                                            <div class="row">
                                                <div class="col-sm-4 fw-bold text-muted">{{ T::translate('Caregiver', 'Tagapag-alaga')}}:</div>
                                                <div class="col-sm-8">{{ $beneficiary->primary_caregiver ?? 'N/A' }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Emergency Contacts -->
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header" style="background: linear-gradient(135deg, #e74c3c, #c0392b);">
                                            <h5 class="mb-0 text-white"><i class="bi bi-exclamation-triangle me-2"></i>Emergency Contacts</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <div class="fw-bold text-muted">Contact Person:</div>
                                                    <div>{{ $beneficiary->emergency_contact_name }}</div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="fw-bold text-muted">{{ T::translate('Relationship', 'Relasyon')}}:</div>
                                                    <div>{{ $beneficiary->emergency_contact_relation ?? 'Not Specified' }}</div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="fw-bold text-muted">Mobile:</div>
                                                    <div>{{ $beneficiary->emergency_contact_mobile }}</div>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <div class="fw-bold text-muted">Email:</div>
                                                    <div>{{ $beneficiary->emergency_contact_email ?? 'N/A'}}</div>
                                                </div>
                                                <div class="col-12">
                                                    <div class="fw-bold text-muted">{{ T::translate('Emergency Procedure', 'Pamamaraan sa Emergency')}}:</div>
                                                    <div>{{ $beneficiary->emergency_procedure}}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Medical Tab -->
                        <div class="tab-pane fade" id="medical" role="tabpanel">
                            <div class="row g-4">
                                <!-- Health History -->
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0"><i class="bi bi-clipboard2-pulse me-2"></i>{{ T::translate('Health History', 'Kasaysayan ng Kalusugan')}}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <div class="fw-bold text-muted">{{ T::translate('Medical Conditions', 'Mga Medikal na Kondisyon')}}:</div>
                                                <div class="p-3 bg-light rounded mt-2">
                                                    @php
                                                        $medicalConditions = is_string($beneficiary->generalCarePlan->healthHistory->medical_conditions) 
                                                            ? json_decode($beneficiary->generalCarePlan->healthHistory->medical_conditions, true) 
                                                            : $beneficiary->generalCarePlan->healthHistory->medical_conditions;
                                                            
                                                        echo is_array($medicalConditions) ? implode(', ', $medicalConditions) : ($medicalConditions ?? 'N/A');
                                                    @endphp
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="fw-bold text-muted">{{ T::translate('Allergies', 'Mga Alerhiya')}}:</div>
                                                <div class="p-3 bg-light rounded mt-2">
                                                    @php
                                                        $allergies = is_string($beneficiary->generalCarePlan->healthHistory->allergies) 
                                                            ? json_decode($beneficiary->generalCarePlan->healthHistory->allergies, true) 
                                                            : $beneficiary->generalCarePlan->healthHistory->allergies;
                                                            
                                                        echo is_array($allergies) ? implode(', ', $allergies) : ($allergies ?? 'N/A');
                                                    @endphp
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <div class="fw-bold text-muted">{{ T::translate('Immunizations', 'Mga Bakuna')}}:</div>
                                                <div class="p-3 bg-light rounded mt-2">
                                                    @php
                                                        $immunizations = is_string($beneficiary->generalCarePlan->healthHistory->immunizations) 
                                                            ? json_decode($beneficiary->generalCarePlan->healthHistory->immunizations, true) 
                                                            : $beneficiary->generalCarePlan->healthHistory->immunizations;
                                                            
                                                        echo is_array($immunizations) ? implode(', ', $immunizations) : ($immunizations ?? 'N/A');
                                                    @endphp
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Medications -->
                                <div class="col-lg-6">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0"><i class="bi bi-capsule me-2"></i>{{ T::translate('Current Medications', 'Kasalukuyang mga Gamot')}}</h5>
                                        </div>
                                        <div class="card-body">
                                            @if(count($beneficiary->generalCarePlan->medications) > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>{{ T::translate('Medication', 'Gamot')}}</th>
                                                                <th>{{ T::translate('Dosage', 'Dosis')}}</th>
                                                                <th>{{ T::translate('Frequency', 'Dalas')}}</th>
                                                                <th>{{ T::translate('Instructions', 'Tagubilin')}}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($beneficiary->generalCarePlan->medications as $medication)
                                                            <tr>
                                                                <td>{{ $medication->medication }}</td>
                                                                <td>{{ $medication->dosage }}</td>
                                                                <td>{{ $medication->frequency }}</td>
                                                                <td>{{ $medication->administration_instructions }}</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="text-center text-muted py-4">
                                                    <i class="bi bi-info-circle fs-4"></i>
                                                    <p class="mt-2">{{ T::translate('No medications recorded', 'Walang mga gamot ang nai-tala..')}}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Care Plan Tab -->
                        <div class="tab-pane fade" id="care" role="tabpanel">
                            <div class="row g-4">
                                <!-- Care Needs -->
                                <div class="col-lg-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0"><i class="bi bi-clipboard2-check me-2"></i>{{ T::translate('Care Needs', 'Mga Pangangailangan sa Pangangalaga')}}</h5>
                                        </div>
                                        <div class="card-body">
                                            @if(count($careNeeds1) > 0 || count($careNeeds2) > 0 || count($careNeeds3) > 0 || count($careNeeds4) > 0 || count($careNeeds5) > 0 || count($careNeeds6) > 0 || count($careNeeds7) > 0)
                                                <div class="table-responsive">
                                                    <table class="table table-hover">
                                                        <thead>
                                                            <tr>
                                                                <th>{{ T::translate('Category', 'Kategorya')}}</th>
                                                                <th>{{ T::translate('Frequency', 'Dalas')}}</th>
                                                                <th>{{ T::translate('Assistance Required', 'Kailangan na Tulong')}}</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($careNeeds1 as $careNeed)
                                                            <tr>
                                                                <td>Mobility</td>
                                                                <td>{{ $careNeed->frequency }}</td>
                                                                <td>{{ $careNeed->assistance_required }}</td>
                                                            </tr>
                                                            @endforeach
                                                            
                                                            @foreach ($careNeeds2 as $careNeed)
                                                            <tr>
                                                                <td>Cognitive/Communication</td>
                                                                <td>{{ $careNeed->frequency }}</td>
                                                                <td>{{ $careNeed->assistance_required }}</td>
                                                            </tr>
                                                            @endforeach
                                                            
                                                            @foreach ($careNeeds3 as $careNeed)
                                                            <tr>
                                                                <td>Self-sustainability</td>
                                                                <td>{{ $careNeed->frequency }}</td>
                                                                <td>{{ $careNeed->assistance_required }}</td>
                                                            </tr>
                                                            @endforeach
                                                            
                                                            @foreach ($careNeeds4 as $careNeed)
                                                            <tr>
                                                                <td>Disease/Therapy Handling</td>
                                                                <td>{{ $careNeed->frequency }}</td>
                                                                <td>{{ $careNeed->assistance_required }}</td>
                                                            </tr>
                                                            @endforeach
                                                            
                                                            @foreach ($careNeeds5 as $careNeed)
                                                            <tr>
                                                                <td>Daily Life/Social Contact</td>
                                                                <td>{{ $careNeed->frequency }}</td>
                                                                <td>{{ $careNeed->assistance_required }}</td>
                                                            </tr>
                                                            @endforeach
                                                            
                                                            @foreach ($careNeeds6 as $careNeed)
                                                            <tr>
                                                                <td>Outdoor Activities</td>
                                                                <td>{{ $careNeed->frequency }}</td>
                                                                <td>{{ $careNeed->assistance_required }}</td>
                                                            </tr>
                                                            @endforeach
                                                            
                                                            @foreach ($careNeeds7 as $careNeed)
                                                            <tr>
                                                                <td>Household Keeping</td>
                                                                <td>{{ $careNeed->frequency }}</td>
                                                                <td>{{ $careNeed->assistance_required }}</td>
                                                            </tr>
                                                            @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @else
                                                <div class="text-center text-muted py-4">
                                                    <i class="bi bi-info-circle fs-4"></i>
                                                    <p class="mt-2">{{ T::translate('No care needs recorded', 'Walang pangangailangan sa pangangalaga ang nai-tala')}}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Mobility & Cognitive Function -->
                                <div class="row">
                                    <div class="col-lg-12">
                                        <div class="card mt-4">
                                            <div class="card-header">
                                                <h5 class="mb-0"><i class="bi bi-person-walking me-2"></i>{{ T::translate('Mobility Details', 'Mga Detalye sa Mobility')}}</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <div class="fw-bold text-muted">{{ T::translate('Walking Ability', 'Kakayahan sa Paglalakad')}}:</div>
                                                    <div>{{ $beneficiary->generalCarePlan->mobility->walking_ability ?? 'N/A' }}</div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="fw-bold text-muted">{{ T::translate('Assistive Devices', 'Mga Kagamitang Pantulong')}}:</div>
                                                    <div>{{ $beneficiary->generalCarePlan->mobility->assistive_devices ?? 'N/A' }}</div>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-muted">{{ T::translate('Transportation Needs', 'Pangangailangan sa Transportasyon')}}:</div>
                                                    <div>{{ $beneficiary->generalCarePlan->mobility->transportation_needs ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="card mt-4">
                                            <div class="card-header">
                                                <h5 class="mb-0"><i class="bi bi-brain me-2"></i>Cognitive Function</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <div class="fw-bold text-muted">{{ T::translate('Memory', 'Memorya')}}:</div>
                                                    <div>{{ $beneficiary->generalCarePlan->cognitiveFunction->memory ?? 'N/A' }}</div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="fw-bold text-muted">{{ T::translate('Thinking Skills', 'Kasanayan sa Pag-iisip')}}:</div>
                                                    <div>{{ $beneficiary->generalCarePlan->cognitiveFunction->thinking_skills ?? 'N/A' }}</div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="fw-bold text-muted">{{ T::translate('Orientation', 'Oryentasyon')}}:</div>
                                                    <div>{{ $beneficiary->generalCarePlan->cognitiveFunction->orientation ?? 'N/A' }}</div>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-muted">{{ T::translate('Behavior', 'Pag-uugali')}}:</div>
                                                    <div>{{ $beneficiary->generalCarePlan->cognitiveFunction->behavior ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="card mt-4">
                                            <div class="card-header">
                                                <h5 class="mb-0"><i class="bi bi-emoji-smile me-2"></i>Emotional Well-being</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <div class="fw-bold text-muted">{{ T::translate('Mood', 'Kalooban')}}:</div>
                                                    <div>{{ $beneficiary->generalCarePlan->emotionalWellbeing->mood ?? 'N/A' }}</div>
                                                </div>
                                                <div class="mb-3">
                                                    <div class="fw-bold text-muted">{{ T::translate('Social Interactions', 'Pakikipag-ugnayan sa Lipunan')}}:</div>
                                                    <div>{{ $beneficiary->generalCarePlan->emotionalWellbeing->social_interactions ?? 'N/A' }}</div>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-muted">{{ T::translate('Support Needs', 'Pangangailangang Suporta')}}:</div>
                                                    <div>{{ $beneficiary->generalCarePlan->emotionalWellbeing->emotional_support_needs ?? 'N/A' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="card mt-4">
                                            <div class="card-header">
                                                <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>{{ T::translate('Assigned Care Worker', 'Naitalagang Tagapag-alaga')}}</h5>
                                            </div>
                                            <div class="card-body">
                                                <div class="mb-3">
                                                    <div class="fw-bold text-muted">{{ T::translate('Name', 'Pangalan')}}:</div>
                                                    <div>{{ $careWorker->first_name ?? 'N/A' }} {{ $careWorker->last_name ?? 'N/A' }}</div>
                                                </div>
                                                <div>
                                                    <div class="fw-bold text-muted">{{ T::translate('Responsibilities', 'Mga Responsibilidad')}}:</div>
                                                    @if(count($beneficiary->generalCarePlan->careWorkerResponsibility) > 0)
                                                        <ul class="list-group list-group-flush mt-2">
                                                            @foreach ($beneficiary->generalCarePlan->careWorkerResponsibility as $responsibility)
                                                            <li class="list-group-item border-0 px-0 py-2">
                                                                <i class="bi bi-check-circle-fill text-primary me-2"></i>
                                                                {{ $responsibility->task_description }}
                                                            </li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <div class="text-muted">{{ T::translate('No responsibilities assigned', 'Walang mga responsibilidad ang naitalaga')}}</div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Documents Tab -->
                        <div class="tab-pane fade" id="documents" role="tabpanel">
                            <div class="row">
                                <div class="col-12">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="mb-0"><i class="bi bi-file-earmark-text me-2"></i>{{ T::translate('Documents', 'Mga Dokumento')}}</h5>
                                        </div>
                                        <div class="card-body">
                                            <div class="row">
                                                <div class="col-md-6 mb-4">
                                                    <div class="card border">
                                                        <div class="card-body text-center">
                                                            <i class="bi bi-file-text fs-1 text-primary mb-3"></i>
                                                            <h5 class="card-title">Care Service Agreement</h5>
                                                            @if($careServiceAgreementUrl)
                                                                <div class="d-flex justify-content-center gap-2 mt-3">
                                                                    <a href="{{ $careServiceAgreementUrl }}" class="btn btn-primary" download>
                                                                        <i class="bi bi-download me-1"></i> {{ T::translate('Download', 'I-Download')}}
                                                                    </a>
                                                                    @if($careServiceAgreementExtension === 'pdf' && $careServiceAgreementViewUrl)
                                                                        <a href="{{ $careServiceAgreementViewUrl }}" class="btn btn-outline-primary" target="_blank">
                                                                            <i class="bi bi-eye me-1"></i> {{ T::translate('View', 'Tingnan')}}
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                                <div class="mt-2 text-muted small">
                                                                    Last updated: {{ \Carbon\Carbon::parse($beneficiary->updated_at)->format('M j, Y') }}
                                                                </div>
                                                            @else
                                                                <p class="text-muted mt-3">{{ T::translate('No document uploaded', 'Walang dokumento ang na-upload')}}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-4">
                                                    <div class="card border">
                                                        <div class="card-body text-center">
                                                            <i class="bi bi-file-medical fs-1 text-primary mb-3"></i>
                                                            <h5 class="card-title">General Care Plan</h5>
                                                            @if($generalCarePlanUrl)
                                                                <div class="d-flex justify-content-center gap-2 mt-3">
                                                                    <a href="{{ $generalCarePlanUrl }}" class="btn btn-primary" download>
                                                                        <i class="bi bi-download me-1"></i> {{ T::translate('Download', 'I-Download')}}
                                                                    </a>
                                                                    @if($generalCarePlanExtension === 'pdf' && $generalCarePlanViewUrl)
                                                                        <a href="{{ $generalCarePlanViewUrl }}" class="btn btn-outline-primary" target="_blank">
                                                                            <i class="bi bi-eye me-1"></i> {{ T::translate('View', 'Tingnan')}}
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                                <div class="mt-2 text-muted small">
                                                                    Last updated: {{ \Carbon\Carbon::parse($beneficiary->updated_at)->format('M j, Y') }}
                                                                </div>
                                                            @else
                                                                <p class="text-muted mt-3">{{ T::translate('No document uploaded', 'Walang dokumento ang na-upload')}}</p>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
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
        // Initialize tooltips
        document.addEventListener('DOMContentLoaded', function() {
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });
            
            // Activate the first accordion item by default
            var firstAccordion = document.querySelector('.accordion-button');
            if (firstAccordion) {
                firstAccordion.classList.remove('collapsed');
                var firstCollapse = document.querySelector(firstAccordion.getAttribute('data-bs-target'));
                if (firstCollapse) {
                    firstCollapse.classList.add('show');
                }
            }
        });
    </script>
</body>
</html>