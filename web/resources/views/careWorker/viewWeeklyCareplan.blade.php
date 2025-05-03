<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/viewWeeklyCareplan.css') }}">
</head>
<body>

    @include('components.careWorkerNavbar')
    @include('components.careWorkerSidebar')
    @include('components.modals.confirmDeleteWeeklyCareplan')
    @include('components.modals.deleteWeeklyCareplan')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <!-- Original Back Button -->
                <a href="{{ route('care-worker.reports') }}" class="btn btn-secondary original-back-btn">
                    <i class="bx bx-arrow-back"></i> Back
                </a>

                <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">VIEW WEEKLY CAREPLAN DETAILS</div>

                <!-- Edit and Delete Buttons -->
                <div>
                    <!-- Edit Button with Routing -->
                    <a href="{{ route('care-worker.weeklycareplans.edit', $weeklyCareplan->weekly_care_plan_id) }}" title="Edit Weekly Care Plan" class="btn btn-primary">
                        <i class="bx bx-edit"></i> Edit
                    </a>
                    <button type="button" class="btn btn-danger" onclick="openDeleteModal('{{ $weeklyCareplan->weekly_care_plan_id }}')">
                        <i class="bi bi-trash"></i> Delete
                    </button>

                    <script>
                        // Function to open the modal for all roles
                        function openDeleteModal(id, name) {
                            // Set the values in the modal
                            const idField = document.getElementById('initialWeeklyCarePlanIdToDelete');
                            const nameField = document.getElementById('initialBeneficiaryNameToDelete');
                            
                            if (idField) idField.value = id;
                            if (nameField) nameField.textContent = name;
                            
                            // Show the modal
                            const modal = new bootstrap.Modal(document.getElementById('confirmDeleteWeeklyCarePlanModal'));
                            modal.show();
                        }
                    </script>

                </div>
            </div>
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong><i class="bx bx-check-circle me-1"></i> Success!</strong> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif
            <div class="row mb-3" id="weeklyCareplanDetails">
                <div class="col-12">
                    <div class="row personal-details">
                        <div class="col-12">
                            <div class="row mb-1 mt-2">
                            <div class="col-12">
                                <h5>Personal Details</h5>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-4 col-sm-9 position-relative">
                                <label for="benficiary" class="form-label">Beneficiary Name</label>
                                <input type="text" class="form-control" id="beneficiary" value="{{ $weeklyCareplan->beneficiary->first_name }} {{ $weeklyCareplan->beneficiary->last_name }}" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan" readonly>    
                            </div>
                            <div class="col-md-2 col-sm-3">
                                <label for="age" class="form-label">Age</label>
                                <input type="text" class="form-control" id="age" value="{{ \Carbon\Carbon::parse($weeklyCareplan->beneficiary->birthday)->age }} years old" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan"  readonly>                                            
                            </div>
                            <div class="col-md-3 col-sm-6">
                                <label for="birthDate" class="form-label">Birthdate</label>
                                <input type="text" class="form-control" id="birthDate" value="{{ $weeklyCareplan->beneficiary->birthday }}" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan" readonly>                                            
                            </div>
                            <div class="col-md-3 col-sm-6 position-relative">
                                <label for="gender" class="form-label">Gender</label>
                                <input type="text" class="form-control" id="gender" value="{{ $weeklyCareplan->beneficiary->gender }}" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan" readonly>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 col-sm-4 position-relative">
                                <label for="civilStatus" class="form-label">Civil Status</label>
                                <input type="text" class="form-control" id="civilStatus" value="{{ $weeklyCareplan->beneficiary->civil_status }}" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan" readonly>
                                </div>
                            <div class="col-md-6 col-sm-8">
                                <label for="address" class="form-label">Address</label>
                                <input type="text" class="form-control" id="address" value="{{ $weeklyCareplan->beneficiary->street_address }}" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan" readonly>
                                </div>
                            <div class="col-md-3 col-sm-12">
                                <label for="condition" class="form-label">Medical Conditions</label>
                                <input type="text" class="form-control" id="medicalConditions" 
                                value="{{ $weeklyCareplan->beneficiary->generalCarePlan->healthHistory->medical_conditions ?? 'No medical conditions recorded' }}" 
                                readonly data-bs-toggle="tooltip" title="Edit in General Care Plan">
                                </div>
                        </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <!-- ASSESSMENT AND EVALIATIONS ROW -->
                    <div class="row">
                        <div class="col-md-6 col-sm-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <th>Assessment</th>
                                        <th>Vital Signs</th>                                       
                                    </thead>
                                    <tbody>
                                        <tr>
                                        <td rowspan="4">
                                            {{ $weeklyCareplan->assessment ?? 'N/A' }}
                                        </td>
                                            <td>
                                            Blood Pressure: <p class="text-primary"> {{ $weeklyCareplan->vitalSigns->blood_pressure ?? 'N/A' }}
                                            </td> 
                                        </tr>
                                        <tr>
                                            <td>
                                            Body Temperature: <p class="text-primary"> {{ $weeklyCareplan->vitalSigns->body_temperature ?? 'N/A' }}
                                            </td>                                            
                                        </tr>
                                        <tr>
                                            <td>
                                            Pulse Rate: <p class="text-primary"> {{ $weeklyCareplan->vitalSigns->pulse_rate ?? 'N/A' }}
                                            </td>                                           
                                        </tr>
                                        <tr>
                                            <td>
                                            Respiratory Rate: <p class="text-primary"> {{ $weeklyCareplan->vitalSigns->respiratory_rate ?? 'N/A' }}
                                            </td>                                            
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <div class="row">
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <div class="table-responsive">
                                            <table class="table table-bordered">
                                                <thead><th>Evaluation and Recommendation</th></thead>
                                                <tbody>
                                                    <tr>
                                                        <td>
                                                            {{ $weeklyCareplan->evaluation_recommendations ?? 'N/A' }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-lg-12 col-md-12 col-sm-12">
                                        <label class="form-label">Picture Preview</label>
                                        <div class="border p-2 d-flex justify-content-center align-items-center" style="height: 200px;">
                                            <img id="picture_preview" src="#" alt="Preview" class="img-fluid" style="max-height: 100%; display: none;">
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>

                    <hr class="my-4">
                    <!-- CARE NEEDS AND INTERVENTIONS IMPLEMENTED ROW -->
                    <div class="row">
                        <div class="col-12">
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <th class="care-needs-column">Care Needs</th>
                                        <th class="interventions-column">Interventions Impelemented</th>
                                        <th class="minute-column minutes-header"></th>
                                    </thead>
                                    <tbody>
                                    @foreach($categories as $category)
                                        @php
                                            $categoryInterventions = $interventionsByCategory->get($category->care_category_id, collect());
                                            $categoryCustInterventions = $customInterventions->where('care_category_id', $category->care_category_id);
                                            $totalInterventions = $categoryInterventions->count() + $categoryCustInterventions->count();
                                        @endphp

                                        @if($totalInterventions > 0)
                                            <tr>
                                                <td rowspan="{{ $totalInterventions + 1 }}" class="care-needs-column">
                                                    <h6>{{ strtoupper($category->care_category_name) }}</h6>
                                                </td>
                                            </tr>
                                            
                                            {{-- Standard interventions (from interventions table) --}}
                                            @foreach($categoryInterventions as $intervention)
                                                <tr>
                                                    <td class="interventions-column">{{ $intervention->intervention_description }}</td>
                                                    <td class="minute-column">{{ $intervention->duration_minutes }} min</td>
                                                </tr>
                                            @endforeach
                                            
                                            {{-- Custom interventions (description in weekly_care_plan_interventions) --}}
                                            @foreach($categoryCustInterventions as $custom)
                                                <tr>
                                                    <td class="interventions-column">{{ $custom->intervention_description }} <span class="badge bg-info">Custom</span></td>
                                                    <td class="minute-column">{{ $custom->duration_minutes }} min</td>
                                                </tr>
                                            @endforeach
                                        @endif
                                    @endforeach
                                    </tbody>
                                    <tfoot>
                                    <tr>
                                        <th colspan="2" class="text-end">Total Time:</th>
                                        <th>
                                        @php
                                            $standardMinutes = (float)$interventionsByCategory->flatten()->sum('duration_minutes') ?? 0;
                                            $customMinutes = (float)$customInterventions->sum('duration_minutes') ?? 0;
                                            $totalMinutes = $standardMinutes + $customMinutes;

                                            // Format to exactly one decimal place
                                            $formattedTotal = number_format($totalMinutes, 2);
                                        @endphp
                                        {{ $formattedTotal }} min
                                        </th>
                                    </tr>
                                    <tr>
                                        <th colspan="2" class="text-end">Acknowledgement:</th>
                                        <th>
                                        @if($weeklyCareplan->acknowledged_by_beneficiary && $weeklyCareplan->acknowledgedByBeneficiary)
                                            <span class="text-success">
                                                <i class="fa fa-check-circle"></i> Acknowledged by: {{ $weeklyCareplan->acknowledgedByBeneficiary->first_name }} {{ $weeklyCareplan->acknowledgedByBeneficiary->last_name }} (Beneficiary)
                                            </span>
                                        @elseif($weeklyCareplan->acknowledged_by_family && $weeklyCareplan->acknowledgedByFamily)
                                            <span class="text-success">
                                                <i class="fa fa-check-circle"></i> Acknowledged by: {{ $weeklyCareplan->acknowledgedByFamily->first_name }} {{ $weeklyCareplan->acknowledgedByFamily->last_name }} (Family Member)
                                            </span>
                                        @elseif($weeklyCareplan->acknowledgement_signature)
                                            <span class="text-success">
                                                <i class="fa fa-check-circle"></i> Acknowledged with signature
                                                <a href="#" data-bs-toggle="modal" data-bs-target="#signatureModal">(View)</a>
                                            </span>
                                        @else
                                            <span class="text-secondary">Not Acknowledged</span>
                                        @endif
                                        </th>
                                    </tr>
                                </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src=" {{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    <script>
    // Function to open the initial confirmation modal
    function openInitialDeleteModal(id, name) {
        // Set values for the initial modal
        document.getElementById('initialWeeklyCarePlanIdToDelete').value = id;
        document.getElementById('initialBeneficiaryNameToDelete').textContent = name;
        
        // Show the initial confirmation modal
        const initialModal = new bootstrap.Modal(document.getElementById('confirmDeleteWeeklyCarePlanModal'));
        initialModal.show();
    }
</script>
   
</body>
</html>