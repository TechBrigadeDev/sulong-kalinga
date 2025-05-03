<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Beneficiary</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/addUsers.css') }}">
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')
    
    <div class="home-section">
        <div class="container-fluid">
            <!-- Back Button Logic -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <form action="{{ route('admin.beneficiaries.view') }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="beneficiary_id" value="{{ $beneficiary->beneficiary_id }}">
                    <button type="submit" class="btn btn-secondary original-back-btn">
                    <i class="bx bx-arrow-back"></i> Back
                    </button>
                </form>

                <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">EDIT BENEFICIARY PROFILE</div>
            </div>
            <div class="row" id="addUserForm">
                <div class="col-12">
                <form action="{{ route('editProfile') }}" method="POST">
                    <form id="addBeneficiaryForm">
                        @csrf
                        @method('PUT')
                        <!-- Row 1: Personal Details -->
                        <div class="row mb-1 mt-3">
                            <div class="col-12">
                                <h5 class="text-start">Personal Details</h5>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-3">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="first_name" placeholder="Enter first name" value="{{ $beneficiary->first_name }}" required>
                            </div>
                            <div class="col-md-3">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="last_name" placeholder="Enter last name" required>
                            </div>
                            <div class="col-md-3 position-relative">
                                <label for="civilStatus" class="form-label">Civil Status</label>
                                <input type="text" class="form-control" id="civilStatusInput" placeholder="Select civil status" autocomplete="off" value="Single"><!--set the value from DB-->
                                <ul class="dropdown-menu w-100" id="civilStatusDropdown">
                                    <li><a class="dropdown-item" data-value="single">Single</a></li>
                                    <li><a class="dropdown-item" data-value="married">Married</a></li>
                                    <li><a class="dropdown-item" data-value="widowed">Widowed</a></li>
                                    <li><a class="dropdown-item" data-value="divorced">Divorced</a></li>
                                </ul>
                                <input type="hidden" id="civilStatus" name="civil_status" value="single"><!--set the value from DB-->
                            </div>
                            <div class="col-md-3 position-relative">
                                <label for="gender" class="form-label">Gender</label>
                                <input type="text" class="form-control" id="genderInput" placeholder="Select gender" autocomplete="off" value="Male"><!--set the value from DB-->
                                <ul class="dropdown-menu w-100" id="genderDropdown">
                                    <li><a class="dropdown-item" data-value="male">Male</a></li>
                                    <li><a class="dropdown-item" data-value="female">Female</a></li>
                                    <li><a class="dropdown-item" data-value="other">Other</a></li>
                                </ul>
                                <input type="hidden" id="gender" name="gender" value="male"><!--set the value from DB-->
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="birthDate" class="form-label">Birthday</label>
                                <input type="date" class="form-control" id="birthDate" name="birth_date" required onkeydown="return true">
                            </div>
                            <div class="col-md-3 position-relative">
                                <label for="primaryCaregiver" class="form-label">Primary Caregiver</label>
                                <input type="text" class="form-control" id="primaryCaregiver" name="primary_caregiver" placeholder="Enter Primary Caregiver name" required>                            
                            </div>
                            <div class="col-md-3">
                                <label for="mobileNumber" class="form-label">Mobile Number</label>
                                <input type="text" class="form-control" id="mobileNumber" name="mobile_number" placeholder="Enter mobile number" required>
                            </div>
                            <div class="col-md-3">
                                <label for="landlineNumber" class="form-label">Landline Number</label>
                                <input type="text" class="form-control" id="landlineNumber" name="landline_number" placeholder="Enter landline number">
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Row 2: Address -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Current Address</h5> 
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="addressDetails" class="form-label">House No., Street, Subdivision</label>
                                <input type="text" class="form-control" id="addressDetails" name="address_details" placeholder="Enter house no., street, subdivision" required>
                            </div>
                            <div class="col-md-3 position-relative">
                                <label for="barangay" class="form-label">Barangay</label>
                                <input type="text" class="form-control" id="barangayInput" placeholder="Select barangay" autocomplete="off">
                                <ul class="dropdown-menu w-100" id="barangayDropdown">
                                    <li><a class="dropdown-item" data-value="barangay1">Barangay 1</a></li>
                                    <li><a class="dropdown-item" data-value="barangay2">Barangay 2</a></li>
                                    <li><a class="dropdown-item" data-value="barangay3">Barangay 3</a></li>
                                </ul>
                                <input type="hidden" id="barangay" name="barangay">
                            </div>
                            <div class="col-md-3 position-relative">
                                <label for="municipality" class="form-label">Municipality</label>
                                <input type="text" class="form-control" id="municipalityInput" placeholder="Select municipality" autocomplete="off">
                                <ul class="dropdown-menu w-100" id="municipalityDropdown">
                                    <li><a class="dropdown-item" data-value="municipality1">Municipality 1</a></li>
                                    <li><a class="dropdown-item" data-value="municipality2">Municipality 2</a></li>
                                    <li><a class="dropdown-item" data-value="municipality3">Municipality 3</a></li>
                                </ul>
                                <input type="hidden" id="municipality" name="municipality">
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Row 3: Medical History -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Medical History</h5> 
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-3">
                                <label for="medicalConditions" class="form-label">Medical Conditions</label>
                                <textarea class="form-control" id="medicalConditions" name="medical_conditions" placeholder="List all medical conditions" rows="3"></textarea>
                            </div>
                            <div class="col-md-3">
                                <label for="medications" class="form-label">Medications</label>
                                <textarea class="form-control" id="medications" name="medications" placeholder="List all medications" rows="3"></textarea>
                            </div>
                            <div class="col-md-3">
                                <label for="allergies" class="form-label">Allergies</label>
                                <textarea class="form-control" id="allergies" name="allergies" placeholder="List all allergies" rows="3"></textarea>
                            </div>
                            <div class="col-md-3">
                                <label for="immunizations" class="form-label">Immunizations</label>
                                <textarea class="form-control" id="immunizations" name="immunizations" placeholder="List all immunizations" rows="3"></textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                        <div class="col-md-3 position-relative">
                            <label for="category" class="form-label">Category</label>
                            <input type="text" class="form-control" id="categoryInput" placeholder="Select category" autocomplete="off" value="Frail"> <!--set the value from DB-->
                            <ul class="dropdown-menu w-100" id="categoryDropdown">
                                <li><a class="dropdown-item" data-value="frail">Frail</a></li>
                                <li><a class="dropdown-item" data-value="bedridden">Bedridden</a></li>
                                <li><a class="dropdown-item" data-value="dementia">Dementia</a></li>
                            </ul>
                            <input type="hidden" id="category" name="category" value="frail"> <!--set the value from DB-->
                        </div>
                        </div>

                        <hr class="my-4">
                        <!-- Care Needs -->
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <h5 class="text-start">Care Needs</h5>
                            </div>
                        </div>

                        <!-- Care Needs Rows -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Mobility</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="mobilityFrequency" name="frequency[mobility]" placeholder="Frequency" rows="2"></textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="mobilityAssistance" name="assistance[mobility]" placeholder="Assistance Required" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Cognitive / Communication</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="cognitiveFrequency" name="frequency[cognitive]" placeholder="Frequency" rows="2"></textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="cognitiveAssistance" name="assistance[cognitive]" placeholder="Assistance Required" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Self-sustainability</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="selfSustainabilityFrequency" name="frequency[self_sustainability]" placeholder="Frequency" rows="2"></textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="selfSustainabilityAssistance" name="assistance[self_sustainability]" placeholder="Assistance Required" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Disease / Therapy Handling</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="diseaseFrequency" name="frequency[disease]" placeholder="Frequency" rows="2"></textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="diseaseAssistance" name="assistance[disease]" placeholder="Assistance Required" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Daily Life / Social Contact</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="dailyLifeFrequency" name="frequency[daily_life]" placeholder="Frequency" rows="2"></textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="dailyLifeAssistance" name="assistance[daily_life]" placeholder="Assistance Required" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Outdoor Activities</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="outdoorFrequency" name="frequency[outdoor]" placeholder="Frequency" rows="2"></textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="outdoorAssistance" name="assistance[outdoor]" placeholder="Assistance Required" rows="2"></textarea>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Household Keeping</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="householdFrequency" name="frequency[household]" placeholder="Frequency" rows="2"></textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="householdAssistance" name="assistance[household]" placeholder="Assistance Required" rows="2"></textarea>
                            </div>
                        </div>

                        <hr class="my-4">
                       <!-- Medication Management -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Medication Management</h5> 
                            </div>
                        </div>
                        <div id="medicationManagement">
                            <div class="row mb-1 align-items-center medication-row">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="medication_name[]" placeholder="Medication name" >
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="dosage[]" placeholder="Dosage" >
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="frequency[]" placeholder="Frequency" >
                                </div>
                                <div class="col-md-4">
                                    <textarea class="form-control" name="administration_instructions[]" placeholder="Administration Instructions" rows="1" ></textarea>
                                </div>
                                <div class="col-md-1 d-flex text-start">
                                    <button type="button" class="btn btn-danger" onclick="removeMedicationRow(this)">Delete</button>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 text-start">
                                <button type="button" class="btn btn-primary" onclick="addMedicationRow()">Add Medication</button>
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Mobility, Cognitive Function, Emotional Well-being -->
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <h5 class="text-start">Mobility</h5>
                                <div class="mb-1">
                                    <label for="walkingAbility" class="form-label">Walking Ability</label>
                                    <textarea class="form-control" id="walkingAbility" name="mobility[walking_ability]" placeholder="Enter details about walking ability" rows="2"></textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="assistiveDevices" class="form-label">Assistive Devices</label>
                                    <textarea class="form-control" id="assistiveDevices" name="mobility[assistive_devices]" placeholder="Enter details about assistive devices" rows="2"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="transportationNeeds" class="form-label">Transportation Needs</label>
                                    <textarea class="form-control" id="transportationNeeds" name="mobility[transportation_needs]" placeholder="Enter details about transportation needs" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h5 class="text-start">Cognitive Function</h5>
                                <div class="mb-1">
                                    <label for="memory" class="form-label">Memory</label>
                                    <textarea class="form-control" id="memory" name="cognitive[memory]" placeholder="Enter details about memory" rows="2"></textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="thinkingSkills" class="form-label">Thinking Skills</label>
                                    <textarea class="form-control" id="thinkingSkills" name="cognitive[thinking_skills]" placeholder="Enter details about thinking skills" rows="2"></textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="orientation" class="form-label">Orientation</label>
                                    <textarea class="form-control" id="orientation" name="cognitive[orientation]" placeholder="Enter details about orientation" rows="2"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="behavior" class="form-label">Behavior</label>
                                    <textarea class="form-control" id="behavior" name="cognitive[behavior]" placeholder="Enter details about behavior" rows="2"></textarea>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <h5 class="text-start">Emotional Well-being</h5>
                                <div class="mb-1">
                                    <label for="mood" class="form-label">Mood</label>
                                    <textarea class="form-control" id="mood" name="emotional[mood]" placeholder="Enter details about mood" rows="2"></textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="socialInteractions" class="form-label">Social Interactions</label>
                                    <textarea class="form-control" id="socialInteractions" name="emotional[social_interactions]" placeholder="Enter details about social interactions" rows="2"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="emotionalSupport" class="form-label">Emotional Support Need</label>
                                    <textarea class="form-control" id="emotionalSupport" name="emotional[emotional_support]" placeholder="Enter details about emotional support need" rows="2"></textarea>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Emergency Contact -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Emergency Contact</h5>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="contactName" class="form-label">Contact Name</label>
                                <input type="text" class="form-control" id="contactName" name="emergency_contact[name]" placeholder="Enter contact name" required>
                            </div>
                            <div class="col-md-3 position-relative">
                                <label for="relation" class="form-label">Relation</label>
                                <input type="text" class="form-control" id="relationInput" placeholder="Select relation" autocomplete="off">
                                <ul class="dropdown-menu w-100" id="relationDropdown">
                                    <li><a class="dropdown-item" data-value="son">Son</a></li>
                                    <li><a class="dropdown-item" data-value="daughter">Daughter</a></li>
                                    <li><a class="dropdown-item" data-value="grandchild">Grandchild</a></li>
                                </ul>
                                <input type="hidden" id="relation" name="relation">
                            </div>
                            <div class="col-md-3">
                                <label for="mobileNumber" class="form-label">Mobile Number</label>
                                <input type="text" class="form-control" id="mobileNumber" name="emergency_contact[mobile]" placeholder="Enter mobile number" required>
                            </div>
                            <div class="col-md-3">
                                <label for="emailAddress" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="emailAddress" name="emergency_contact[email]" placeholder="Enter email address" >
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Emergency Plan -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Emergency Plan</h5> 
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="emergencyProcedures" class="form-label">Emergency Procedures</label>
                                <textarea class="form-control" id="emergencyProcedures" name="emergency_plan[procedures]" placeholder="Enter emergency procedures" rows="3" required></textarea>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        <!-- Care Worker's Responsibilities -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Care Worker's Responsibilities</h5> 
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-3 position-relative">
                                <label for="careworkerName" class="form-label">Select Care Worker</label>
                                <input type="text" class="form-control" id="careworkerNameInput" placeholder="Select Care Worker" autocomplete="off" value="Careworker 1"> <!--set the value from DB-->
                                <ul class="dropdown-menu w-100" id="careworkerNameDropdown">
                                    <li><a class="dropdown-item" data-value="careworker1">Careworker 1</a></li>
                                    <li><a class="dropdown-item" data-value="careworker2">Careworker 2</a></li>
                                    <li><a class="dropdown-item" data-value="careworker3">Careworker 3</a></li>
                                </ul>
                                <input type="hidden" id="careworkerName" name="careworkerName" value="careworker1"> <!--set the value from DB-->
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">Tasks and Responsibilities</label>
                                <div id="tasksContainer">
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="care_worker[tasks][]" placeholder="Enter task or responsibility" required>
                                    </div>
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="care_worker[tasks][]" placeholder="Enter task or responsibility" required>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3 d-flex flex-column align-items-start">
                                <label class="form-label">Add or Delete Task</label>
                                <button type="button" class="btn btn-primary btn-sm mb-2 w-100" onclick="addTask()">Add Task</button>
                                <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeTask()">Delete Task</button>
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- General Care Plan and Care Service Agreement File Upload -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Documents and Signatures</h5> 
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <label for="beneficiaryProfilePic" class="form-label">Upload Beneficiary Picture</label>
                                <input type="file" class="form-control" id="beneficiaryProfilePic" name="beneficiaryProfilePic" accept="image/png, image/jpeg">
                            </div>
                            <div class="col-md-4">
                                <label for="careServiceAgreement" class="form-label">Care Service Agreement</label>
                                <input type="file" class="form-control" id="careServiceAgreement" name="care_service_agreement" accept=".pdf,.doc,.docx" required>
                            </div>
                            <div class="col-md-4">
                                <label for="generalCareplan" class="form-label">General Careplan</label>
                                <input type="file" class="form-control" id="generalCareplan" name="general_careplan" accept=".pdf,.doc,.docx">
                            </div>
                        </div>

                        <!-- Beneficiary and Care Worker Signatures -->
                        <div class="row mb-3">
                            <!-- Beneficiary Signature Column -->
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <div class="form-group mt-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label>Beneficiary Signature</label>
                                                <button type="button" id="clear-signature-1" class="btn btn-danger btn-sm">Clear</button>
                                            </div>
                                            <div id="signature-pad-1" class="signature-pad">
                                                <div class="signature-pad-body">
                                                    <canvas id="canvas1" style="border: 1px solid #ced4da; width: 100%; height: 200px;"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-md-12">
                                        <label for="beneficiarySignatureUpload" class="form-label">Upload Beneficiary Signature</label>
                                        <input type="file" class="form-control" id="beneficiarySignatureUpload" name="beneficiary_signature_upload" accept="image/png, image/jpeg">
                                    </div>
                                </div>
                            </div>

                            <!-- Care Worker Signature Column -->
                            <div class="col-md-6">
                                <div class="row mb-2">
                                    <div class="col-md-12">
                                        <div class="form-group mt-3">
                                            <div class="d-flex justify-content-between align-items-center mb-2">
                                                <label>Care Worker Signature</label>
                                                <button type="button" id="clear-signature-2" class="btn btn-danger btn-sm">Clear</button>
                                            </div>
                                            <div id="signature-pad-2" class="signature-pad">
                                                <div class="signature-pad-body">
                                                    <canvas id="canvas2" style="border: 1px solid #ced4da; width: 100%; height: 200px;"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="row mb-1">
                                    <div class="col-md-12">
                                        <label for="beneficiarySignatureUpload" class="form-label">Upload Care Worker Signature</label>
                                        <input type="file" class="form-control" id="beneficiarySignatureUpload" name="beneficiary_signature_upload" accept="image/png, image/jpeg">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12 d-flex justify-content-center align-items-center">
                                <button type="submit" class="btn btn-success btn-lg d-flex align-items-center" id="saveBeneficiaryButton">
                                    <i class='bx bx-save me-2' style="font-size: 24px;"></i>
                                    Update Profile
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Update Beneficiary Success Modal -->
    <div class="modal fade" id="saveSuccessModal" tabindex="-1" aria-labelledby="saveSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="saveSuccessModalLabel">Update Success</h5> <!-- Changed title -->
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p>Beneficiary profile has been successfully updated!</p> <!-- Changed body text -->
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Unsaved Changes Modal -->
    <div class="modal fade" id="unsavedChangesModal" tabindex="-1" aria-labelledby="unsavedChangesModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="unsavedChangesModalLabel">Unsaved Changes</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    You have unsaved changes. Are you sure you want to leave this page without saving?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Stay on Page</button>
                    <a href="#" id="leavePageButton" class="btn btn-danger">Leave Page</a>
                </div>
            </div>
        </div>
    </div>



    <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>


    <script src=" {{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        function addMedicationRow() {
        const container = document.getElementById('medicationManagement');
        const newRow = document.createElement('div');
        newRow.className = 'row mb-2 align-items-center medication-row';
        newRow.innerHTML = `
            <div class="col-md-3">
                <input type="text" class="form-control" name="medication_name[]" placeholder="Enter medication name" required>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="dosage[]" placeholder="Enter dosage" required>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="frequency[]" placeholder="Enter frequency" required>
            </div>
            <div class="col-md-4">
                <textarea class="form-control" name="administration_instructions[]" placeholder="Enter administration instructions" rows="1" required></textarea>
            </div>
            <div class="col-md-1 d-flex text-start">
                <button type="button" class="btn btn-danger" onclick="removeMedicationRow(this)">Delete</button>
            </div>
        `;
        container.appendChild(newRow);

        inputGroup.scrollIntoView({ behavior: 'smooth', block: 'center' });

        }

        // Function to remove a medication row
        function removeMedicationRow(button) {
            const row = button.closest('.medication-row');
            row.remove();
        }

    </script>
    <script>
        // Function to add a new task input field
        function addTask() {
            const tasksContainer = document.getElementById('tasksContainer');
            const inputGroup = document.createElement('div');
            inputGroup.className = 'input-group mb-2';

            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'form-control';
            input.name = 'care_worker[tasks][]';
            input.placeholder = 'Enter task or responsibility';
            input.required = true;

            tasksContainer.appendChild(inputGroup);
            inputGroup.appendChild(input);

            inputGroup.scrollIntoView({ behavior: 'smooth', block: 'center' });

        }

        // Function to remove a task input field
        function removeTask(button) {
            const tasksContainer = document.getElementById('tasksContainer');
            if (tasksContainer.children.length > 0) {
                tasksContainer.lastChild.remove();
            }
        }

    </script>
    <!--SCRIPT FOR SAVE SUCCESS MODAL -->
    <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            e.preventDefault();
            const successModal = new bootstrap.Modal(document.getElementById('saveSuccessModal')); // Ensure the modal ID matches
            successModal.show();
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Function to filter dropdown items
            function filterDropdown(inputId, dropdownId) {
                const input = document.getElementById(inputId);
                const dropdown = document.getElementById(dropdownId);
                const items = dropdown.querySelectorAll('.dropdown-item');

                input.addEventListener('input', function () {
                    const filter = input.value.toLowerCase();
                    let hasVisibleItems = false;

                    items.forEach(item => {
                        if (item.textContent.toLowerCase().includes(filter)) {
                            item.style.display = 'block';
                            hasVisibleItems = true;
                        } else {
                            item.style.display = 'none';
                        }
                    });
                    dropdown.style.display = hasVisibleItems ? 'block' : 'none';
                });
                input.addEventListener('blur', function () {
                    setTimeout(() => dropdown.style.display = 'none', 200);
                });
                input.addEventListener('focus', function () {
                    dropdown.style.display = 'block';
                });

                // Handle item selection
                items.forEach(item => {
                    item.addEventListener('click', function (e) {
                        e.preventDefault();
                        input.value = item.textContent;
                        document.getElementById(inputId.replace('Input', '')).value = item.getAttribute('data-value');
                        dropdown.style.display = 'none';
                    });
                });
            }

            // Initialize filtering for each dropdown
            filterDropdown('civilStatusInput', 'civilStatusDropdown');
            filterDropdown('genderInput', 'genderDropdown');
            filterDropdown('barangayInput', 'barangayDropdown');
            filterDropdown('municipalityInput', 'municipalityDropdown');
            filterDropdown('categoryInput', 'categoryDropdown');
            filterDropdown('relationInput', 'relationDropdown');
            filterDropdown('careworkerNameInput', 'careworkerNameDropdown');
        });
    </script>

   <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Initialize Signature Pads
            const canvas1 = document.getElementById("canvas1");
            const canvas2 = document.getElementById("canvas2");

            const signaturePad1 = new SignaturePad(canvas1);
            const signaturePad2 = new SignaturePad(canvas2);

            // Resize canvas to fit the container
            function resizeCanvas(canvas, signaturePad) {
                const ratio = Math.max(window.devicePixelRatio || 1, 1);
                canvas.width = canvas.offsetWidth * ratio;
                canvas.height = canvas.offsetHeight * ratio;
                canvas.getContext("2d").scale(ratio, ratio);
                signaturePad.clear(); // Clear the canvas after resizing
            }

            // Resize both canvases on page load and window resize
            function initializeCanvas() {
                resizeCanvas(canvas1, signaturePad1);
                resizeCanvas(canvas2, signaturePad2);
            }

            window.addEventListener("resize", initializeCanvas);
            initializeCanvas();

            // Clear First Signature
            document.getElementById("clear-signature-1").addEventListener("click", function () {
                signaturePad1.clear();
            });

            // Clear Second Signature
            document.getElementById("clear-signature-2").addEventListener("click", function () {
                signaturePad2.clear();
            });

            // Handle Form Submission
            document.getElementById("addBeneficiaryForm").addEventListener("submit", function (e) {
                e.preventDefault();

                if (signaturePad1.isEmpty()) {
                    alert("Please draw the first signature.");
                    return;
                }

                if (signaturePad2.isEmpty()) {
                    alert("Please draw the second signature.");
                    return;
                }

                // Save signatures as base64 images
                const signatureDataURL1 = signaturePad1.toDataURL();
                const signatureDataURL2 = signaturePad2.toDataURL();

                // Log the signatures (you can send these to the backend)
                console.log("First Signature (Base64):", signatureDataURL1);
                console.log("Second Signature (Base64):", signatureDataURL2);

                alert("Both signatures submitted successfully!");
            });
        });
    </script>
    
    <!--SCRIPT FOR UNSAVED CHANGES MODAL -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let isFormDirty = false;

            // Mark the form as dirty when any input changes
            const form = document.getElementById("addBeneficiaryForm");
            const inputs = form.querySelectorAll("input, textarea, select");

            inputs.forEach(input => {
                input.addEventListener("change", () => {
                    isFormDirty = true;
                });
            });

            // Handle navigation
            const links = document.querySelectorAll("a");
            links.forEach(link => {
                link.addEventListener("click", function (e) {
                    if (isFormDirty) {
                        e.preventDefault();
                        const leavePageButton = document.getElementById("leavePageButton");
                        leavePageButton.href = this.href; // Set the link to the modal's "Leave Page" button
                        const unsavedChangesModal = new bootstrap.Modal(document.getElementById("unsavedChangesModal"));
                        unsavedChangesModal.show();
                    }
                });
            });

            // Reset the dirty state when the form is submitted
            form.addEventListener("submit", function () {
                isFormDirty = false;
            });
        });
    </script>
    

</body>
</html>