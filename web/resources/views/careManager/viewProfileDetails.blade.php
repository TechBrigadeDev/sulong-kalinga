<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/viewProfileDetails.css') }}">
</head>
<body>

    @include('components.careManagerNavbar')
    @include('components.careManagerSidebar')
    @include('components.modals.statusChangeBeneficiary')
    @include('components.modals.deleteBeneficiary')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                    <!-- Original Back Button -->
                <a href="{{ route('care-manager.beneficiaries.index') }}" class="btn btn-secondary original-back-btn">
                    <i class="bx bx-arrow-back"></i> Back
                </a>
                <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">VIEW BENEFICIARY PROFILE DETAILS</div>

                <!-- Edit and Delete Buttons -->
                <div>
                    <!-- Hidden Back Button 
                    <a href="{{ route('care-manager.beneficiaries.index') }} class="btn btn-secondary hidden-back-btn">
                        <i class="bx bx-arrow-back"></i> Back
                    </a>-->
                    <!-- Edit Button with Routing -->
                    <a href="{{ route('care-manager.beneficiaries.edit', $beneficiary->beneficiary_id) }}" class="btn btn-primary">
                        <i class="bx bxs-edit"></i> Edit
                    </a>
                    <button type="button" class="btn btn-danger" onclick="openDeleteBeneficiaryModal('{{ $beneficiary->beneficiary_id }}', '{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}')">
                        <i class="bx bxs-trash"></i> Delete
                    </button>
                </div>
            </div>
            <div class="row justify-content-center" id="profileDetails">
                <div class="row mb-3 mt-3 justify-content-center">
                    <div class="col-lg-8 col-md-12 col-sm-12" id="profilePic">
                        <div class="row justify-content-center align-items-center text-center text-md-start">
                            <!-- Profile Picture Column -->
                            <div class="col-lg-3 col-md-4 col-sm-12 mb-3 mb-md-0">
                                <img src="{{ $beneficiary->photo ? asset('storage/' . $beneficiary->photo) : asset('images/defaultProfile.png') }}" 
                                    alt="Profile Picture" 
                                    class="img-fluid rounded-circle mx-auto d-block d-md-inline" 
                                    style="width: 150px; height: 150px; border: 1px solid #ced4da;">
                            </div>
                            <!-- Name and Details Column -->
                            <div class="col-lg-9 col-md-8 col-sm-12">
                                <div class="d-flex flex-column flex-md-row align-items-center align-items-md-start">
                                    <!-- Complete Name -->
                                    <h4 class="me-md-3 mb-2 mb-md-0 mt-2">{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</h4>
                                    <!-- Dropdown for Status -->
                                    <div class="form-group mb-0 ms-md-auto">
                                        <select class="form-select d-inline-block w-auto" id="statusSelect{{ $beneficiary->beneficiary_id }}" 
                                                name="status" 
                                                onchange="openStatusChangeModal(this, 'Beneficiary', {{ $beneficiary->beneficiary_id }}, '{{ $beneficiary->status->status_name }}')">
                                            <option value="Active" {{ $beneficiary->status->status_name == 'Active' ? 'selected' : '' }}>Active Beneficiary</option>
                                            <option value="Inactive" {{ $beneficiary->status->status_name == 'Inactive' ? 'selected' : '' }}>Inactive Beneficiary</option>
                                        </select>
                                    </div>
                                </div>
                                <p class="text-muted mt-2 text-center text-md-start">A Beneficiary since {{ $beneficiary->created_at->format('F j, Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Personal Details Column -->
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <h5 class="text-center">Personal Details</h5>
                        <table class="table table-striped personal-details">                            
                            <tbody>
                                <tr>
                                    <td style="width:30%;"><strong>Age:</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($beneficiary->birthday)->age }} years old</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Birthday:</strong></td>
                                    <td>{{ \Carbon\Carbon::parse($beneficiary->birthday)->format('F j, Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Gender:</strong></td>
                                    <td>{{ $beneficiary->gender }}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Civil Status:</strong></td>
                                    <td>{{ $beneficiary->civil_status }}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Mobile Number:</strong></td>
                                    <td>{{ $beneficiary->mobile }}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Landline Number:</strong></td>
                                    <td>{{ $beneficiary->landline ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Current Address:</strong></td>
                                    <td><p>{{ $beneficiary->street_address }}</p></td>
                                </tr>
                                <tr>
                                    <td style="width:30%;"><strong>Primary Caregiver:</strong></td>
                                    <td><p>{{ $beneficiary->primary_caregiver ?? 'N/A' }}</p></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Medical History Column -->
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <h5 class="text-center">Medical History</h5>
                        <table class="table table-striped medical-history">
                            <tbody>
                                <tr>
                                    <td><strong>Medical Conditions:</strong></td>
                                    <td><p>{{ $beneficiary->generalCarePlan->healthHistory->medical_conditions ?? 'N/A' }}</p></td>   
                                </tr>
                                <tr>
                                    <td><strong>Medications:</strong></td>
                                    <td><p>{{ $beneficiary->generalCarePlan->healthHistory->medications ?? 'N/A' }}</p></td>   
                                </tr>
                                <tr>
                                    <td><strong>Allergies:</strong></td>
                                    <td><p>{{ $beneficiary->generalCarePlan->healthHistory->allergies ?? 'N/A' }}</p></td>   
                                </tr>
                                <tr>
                                    <td><strong>Immunizations:</strong></td>
                                    <td><p>{{ $beneficiary->generalCarePlan->healthHistory->immunizations ?? 'N/A' }}</p></td>   
                                </tr>
                                <tr>
                                    <td><strong>Category:</strong></td>
                                    <td><p>{{ $beneficiary->category->category_name }}</p></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <!-- Emergency Details Column -->
                    <div class="col-lg-5 col-md-5 col-sm-12">
                        <h5 class="text-center">Emergency Details</h5>
                        <table class="table table-striped emergency-details">
                            <tbody>
                                <tr>
                                    <td><strong>Emergency Contact:</strong></td>
                                    <td>{{ $beneficiary->emergency_contact_name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Relation:</strong></td>
                                    <td>{{ $beneficiary->emergency_contact_relation ?? 'Not Specified' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Mobile Number:</strong></td>
                                    <td>{{ $beneficiary->emergency_contact_mobile }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Email Address:</strong></td>
                                    <td>{{ $beneficiary->emergency_contact_email ?? 'N/A'}}</td>                                </tr>
                                <tr>
                                    <td><strong>Emergency Procedure:</strong></td>
                                    <td>{{ $beneficiary->emergency_procedure}}</td>  
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Medication Management Column -->
                    <div class="col-lg-7 col-md-7 col-sm-12">
                        <h5 class="text-center">Medication Management</h5>
                        <table class="table table-striped medication-management">
                            <thead>
                                <th style="width:30%;">Medication Name</th>
                                <th style="width:20%;">Dosage</th>
                                <th style="width:20%;">Frequency</th>
                                <th style="width:30%;">Instructions</th>
                            </thead>
                            <tbody>
                                @foreach ($beneficiary->generalCarePlan->medications as $medication)
                                <tr>
                                    <td style="width:30%;"><p>{{ $medication->medication }}</p></td>
                                    <td style="width:20%;"><p>{{ $medication->dosage }}</p></td>
                                    <td style="width:20%;"><p>{{ $medication->frequency }}</p></td>
                                    <td style="width:30%;"><p>{{ $medication->administration_instructions }}</p></td>                                
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <!-- Care Needs Column -->
                    <div class="col-lg-8 col-md-8 col-sm-12">
                        <h5 class="text-center">Care Needs</h5>
                        <table class="table table-striped care-needs">                            
                            <thead>
                                <th style="width:30%;"></th>
                                <th style="width:20%;">Frequency</th>
                                <th style="width:50%;">Assistance Required</th>
                            </thead>
                            <tbody>
                                @php $firstRow = true; @endphp
                                @foreach ($careNeeds1 as $careNeed)
                                <tr>
                                    @if ($firstRow)
                                        <td style="width:30%;"><strong>Mobility</strong></td>
                                        @php $firstRow = false; @endphp
                                    @else
                                        <td style="width:30%;"></td>
                                    @endif
                                    <td style="width:20%;"><p>{{ $careNeed->frequency }}</p></td>
                                    <td style="width:50%;"><p>{{ $careNeed->assistance_required }}</p></td>
                                </tr>
                                @endforeach

                                @php $firstRow = true; @endphp
                                @foreach ($careNeeds2 as $careNeed)
                                <tr>
                                    @if ($firstRow)
                                        <td style="width:30%;"><strong>Cognitive / Communication</strong></td>
                                        @php $firstRow = false; @endphp
                                    @else
                                        <td style="width:30%;"></td>
                                    @endif
                                    <td style="width:20%;"><p>{{ $careNeed->frequency }}</p></td>
                                    <td style="width:50%;"><p>{{ $careNeed->assistance_required }}</p></td>
                                </tr>
                                @endforeach

                                @php $firstRow = true; @endphp
                                @foreach ($careNeeds3 as $careNeed)
                                <tr>
                                    @if ($firstRow)
                                        <td style="width:30%;"><strong>Self-sustainability</strong></td>
                                        @php $firstRow = false; @endphp
                                    @else
                                        <td style="width:30%;"></td>
                                    @endif
                                    <td style="width:20%;"><p>{{ $careNeed->frequency }}</p></td>
                                    <td style="width:50%;"><p>{{ $careNeed->assistance_required }}</p></td>
                                </tr>
                                @endforeach

                                @php $firstRow = true; @endphp
                                @foreach ($careNeeds4 as $careNeed)
                                <tr>
                                    @if ($firstRow)
                                        <td style="width:30%;"><strong>Disease / Therapy Handling</strong></td>
                                        @php $firstRow = false; @endphp
                                    @else
                                        <td style="width:30%;"></td>
                                    @endif
                                    <td style="width:20%;"><p>{{ $careNeed->frequency }}</p></td>
                                    <td style="width:50%;"><p>{{ $careNeed->assistance_required }}</p></td>
                                </tr>
                                @endforeach

                                @php $firstRow = true; @endphp
                                @foreach ($careNeeds5 as $careNeed)
                                <tr>
                                    @if ($firstRow)
                                        <td style="width:30%;"><strong>Daily Life / Social Contact</strong></td>
                                        @php $firstRow = false; @endphp
                                    @else
                                        <td style="width:30%;"></td>
                                    @endif
                                    <td style="width:20%;"><p>{{ $careNeed->frequency }}</p></td>
                                    <td style="width:50%;"><p>{{ $careNeed->assistance_required }}</p></td>
                                </tr>
                                @endforeach

                                @php $firstRow = true; @endphp
                                @foreach ($careNeeds6 as $careNeed)
                                <tr>
                                    @if ($firstRow)
                                        <td style="width:30%;"><strong>Outdoor Activities</strong></td>
                                        @php $firstRow = false; @endphp
                                    @else
                                        <td style="width:30%;"></td>
                                    @endif
                                    <td style="width:20%;"><p>{{ $careNeed->frequency }}</p></td>
                                    <td style="width:50%;"><p>{{ $careNeed->assistance_required }}</p></td>
                                </tr>
                                @endforeach

                                @php $firstRow = true; @endphp
                                @foreach ($careNeeds7 as $careNeed)
                                <tr>
                                    @if ($firstRow)
                                        <td style="width:30%;"><strong>Household Keeping</strong></td>
                                        @php $firstRow = false; @endphp
                                    @else
                                        <td style="width:30%;"></td>
                                    @endif
                                    <td style="width:20%;"><p>{{ $careNeed->frequency }}</p></td>
                                    <td style="width:50%;"><p>{{ $careNeed->assistance_required }}</p></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <!-- Mobility Column -->
                    <div class="col-lg-4 col-md-4 col-sm-12">
                        <h5 class="text-center">Mobility</h5>
                        <table class="table table-striped mobility">
                            <tbody>
                                <tr>
                                    <td><strong>Walking Ability:</strong></td>
                                    <td><p>{{ $beneficiary->generalCarePlan->mobility->walking_ability ?? 'N/A' }}</p></td>                                
                                </tr>
                                <tr>
                                    <td><strong>Assistive Devices:</strong></td>
                                    <td><p>{{ $beneficiary->generalCarePlan->mobility->assistive_devices ?? 'N/A' }}</p></td>                                
                                </tr>
                                <tr>
                                    <td><strong>Transportation Needs:</strong></td>
                                    <td><p>{{ $beneficiary->generalCarePlan->mobility->transportation_needs ?? 'N/A' }}</p></td>                                
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <!-- Cognitive Function Column -->
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <h5 class="text-center">Cognitive Function</h5>
                        <table class="table table-striped cognitive-function">
                            <tbody>
                                <tr>
                                    <td><strong>Memory:</strong></td>
                                    <td><p>{{ $beneficiary->generalCarePlan->cognitiveFunction->memory ?? 'N/A' }}</p></td>                                
                                </tr>
                                <tr>
                                    <td><strong>Thinking Skills:</strong></td>
                                    <td><p>{{ $beneficiary->generalCarePlan->cognitiveFunction->thinking_skills ?? 'N/A' }}</p></td>                                
                                </tr>
                                <tr>
                                    <td><strong>Orientation:</strong></td>
                                    <td><p>{{ $beneficiary->generalCarePlan->cognitiveFunction->orientation ?? 'N/A' }}</p></td>                                
                                </tr>
                                <tr>
                                    <td><strong>Behavior:</strong></td>
                                    <td><p>{{ $beneficiary->generalCarePlan->cognitiveFunction->behavior ?? 'N/A' }}</p></td>                                
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- Emotional Well-being Column -->
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <h5 class="text-center">Emotional Well-being</h5>
                        <table class="table table-striped emotional-wellbeing">
                            <tbody>
                                <tr>
                                    <td><strong>Mood:</strong></td>
                                    <td><p>{{ $beneficiary->generalCarePlan->emotionalWellbeing->mood ?? 'N/A' }}</p></td>                                
                                </tr>
                                <tr>
                                    <td><strong>Social Interactions:</strong></td>
                                    <td><p>{{ $beneficiary->generalCarePlan->emotionalWellbeing->social_interactions ?? 'N/A' }}</p></td>                                
                                </tr>
                                <tr>
                                    <td><strong>Emotional Support Need:</strong></td>
                                    <td><p>{{ $beneficiary->generalCarePlan->emotionalWellbeing->emotional_support_needs ?? 'N/A' }}</p></td>                                
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="row">
                    <!-- Assigned Care Worker Column -->
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <h5 class="text-center">Assigned Care Worker</h5>
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td><strong>Name:</strong>   {{ $careWorker->first_name ?? 'N/A' }} {{ $careWorker->last_name ?? 'N/A' }}</td>                             
                                </tr>
                                <tr> 
                                    <td style="width: 100%; text-align:center;"><strong>Tasks and Responsibilities</strong></td>                           
                                </tr>
                                <!-- LOOP -->
                                @foreach ($beneficiary->generalCarePlan->careWorkerResponsibility as $responsibility)
                                <tr>
                                    <td style="width:100%;"><p>{{ $responsibility->task_description }}</p></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    <div class="col-lg-6 col-md-6 col-sm-12">
                        <h5 class="text-center">Documents</h5>
                        <table class="table table-striped">
                            <tbody>
                                <tr>
                                    <td style="width: 40%;"><strong>Care Service Agreement:</strong></td>
                                    <td style="width: 60%;">
                                        @if($beneficiary->care_service_agreement_doc)
                                            <a href="{{ asset('storage/' . $beneficiary->care_service_agreement_doc) }}" download>Download</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>                                
                                </tr>
                                <tr>
                                    <td style="width: 40%;"><strong>General Careplan:</strong></td>
                                    <td style="width: 60%;">
                                        @if($beneficiary->general_care_plan_doc)
                                            <a href="{{ asset('storage/' . $beneficiary->general_care_plan_doc) }}" download>Download</a>
                                        @else
                                            N/A
                                        @endif
                                    </td>                                
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src=" {{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
   
</body>
</html>