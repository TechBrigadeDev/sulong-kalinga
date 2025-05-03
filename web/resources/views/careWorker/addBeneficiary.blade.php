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

    @include('components.careWorkerNavbar')
    @include('components.careWorkerSidebar')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('care-worker.beneficiaries.index') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back"></i> Back
                </a>
                <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">ADD BENEFICIARY</div>
            </div>
            <div class="row" id="addUserForm">
                <div class="col-12">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    <form action="{{ route('care-worker.beneficiaries.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <!-- Row 1: Personal Details -->
                        <div class="row mb-1 mt-3">
                            <div class="col-12">
                                <h5 class="text-start">Personal Details</h5>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-3 relative">
                                <label for="firstName" class="form-label">First Name<label style="color:red;"> * </label></label>
                                <input type="text" class="form-control" id="firstName" name="first_name" 
                                        value="{{ old('first_name') }}"
                                        placeholder="Enter first name" 
                                        required 
                                        oninput="validateName(this)" 
                                        pattern="^[A-Z][a-zA-Z]*(?:-[a-zA-Z]+)?(?: [a-zA-Z]+(?:-[a-zA-Z]+)*)*$" 
                                        title="First letter must be uppercase. Only alphabets, single spaces, and hyphens are allowed. Single-letter words are not allowed.">
                            </div>
                            <div class="col-md-3 relative">
                                <label for="lastName" class="form-label">Last Name<label style="color:red;"> * </label></label>
                                <input type="text" class="form-control" id="lastName" name="last_name" 
                                        value="{{ old('last_name') }}"
                                        placeholder="Enter last name" 
                                        required 
                                        oninput="validateName(this)" 
                                        pattern="^[A-Z][a-zA-Z]*(?:-[a-zA-Z]+)?(?: [a-zA-Z]+(?:-[a-zA-Z]+)*)*$" 
                                        title="First letter must be uppercase. Only alphabets, single spaces, and hyphens are allowed. Single-letter words are not allowed.">
                            </div>
                            <div class="col-md-3 relative">
                                <label for="civilStatus" class="form-label">Civil Status<label style="color:red;"> * </label></label>
                                <select class="form-select" id="civilStatus" name="civil_status" required>
                                    <option value="" disabled {{ old('civil_status') ? '' : 'selected' }}>Select civil status</option>
                                    <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>Single</option>
                                    <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>Married</option>
                                    <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                    <option value="Divorced" {{ old('civil_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                </select>
                            </div>
                            <div class="col-md-3 relative">
                                <label for="gender" class="form-label">Gender<label style="color:red;"> * </label></label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select gender</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                        <div class="col-md-3">
                                <label for="birthDate" class="form-label">Birthday<label style="color:red;"> * </label></label>
                                <input type="date" class="form-control" id="birthDate" name="birth_date" value="{{ old('birth_date') }}" required onkeydown="return true">
                            </div>
                            <div class="col-md-3 position-relative">
                                <label for="primaryCaregiver" class="form-label">Primary Caregiver</label>
                                <input type="text" class="form-control" id="primaryCaregiver" name="primary_caregiver" value="{{ old('primary_caregiver') }}" placeholder="Enter Primary Caregiver name">                            
                            </div>
                            <div class="col-md-3">
                                <label for="mobileNumber" class="form-label">Mobile Number</label>
                                <div class="input-group">
                                    <span class="input-group-text">+63</span>
                                    <input type="text" class="form-control" id="mobileNumber"  name="mobile_number" value="{{ old('mobile_number') }}" placeholder="Enter mobile number" maxlength="11" required oninput="restrictToNumbers(this)" title="Must be 10 or 11digits.">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="landlineNumber" class="form-label">Landline Number</label>
                                <input type="text" class="form-control" id="landlineNumber" name="landline_number" value="{{ old('landline_number') }}" placeholder="Enter Landline number" maxlength="10" oninput="restrictToNumbers(this)" title="Must be between 7 and 10 digits.">
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Row 2: Address -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Current Address<label style="color:red;"> * </label></h5> 
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="addressDetails" class="form-label">House No., Street, Subdivision, Barangay, City, Province<label style="color:red;"> * </label></label>
                                <textarea class="form-control" id="addressDetails" name="address_details" 
                                placeholder="Enter complete current address" 
                                rows="2" 
                                required 
                                pattern="^[a-zA-Z0-9\s,.-]+$" 
                                title="Only alphanumeric characters, spaces, commas, periods, and hyphens are allowed."
                                oninput="validateAddress(this)">{{ old('address_details') }}</textarea>
                            </div>
                            <div class="col-md-3">
                                <label for="municipality" class="form-label">Municipality<label style="color:red;"> * </label></label>
                                <select class="form-select" id="municipality" name="municipality" required>
                                    <option value="" disabled {{ old('municipality') ? '' : 'selected' }}>Select municipality</option>
                                    @foreach ($municipalities as $municipality)
                                    <option value="{{ $municipality->municipality_id }}" {{ old('municipality') == $municipality->municipality_id ? 'selected' : '' }}>
                                        {{ $municipality->municipality_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div> 
                            <div class="col-md-3">
                                <label for="barangay" class="form-label">Barangay<label style="color:red;"> * </label></label>
                                <select class="form-select" id="barangay" name="barangay" required>
                                    <option value="" disabled {{ old('barangay') ? '' : 'selected' }}>Select barangay</option>
                                    @foreach ($barangays as $b)
                                    <option value="{{ $b->barangay_id }}" 
                                            data-municipality-id="{{ $b->municipality_id }}"
                                            {{ old('barangay') == $b->barangay_id ? 'selected' : '' }}
                                            style="{{ old('municipality') != $b->municipality_id ? 'display:none' : '' }}">
                                        {{ $b->barangay_name }}
                                    </option>
                                    @endforeach
                                </select>
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
                                <textarea class="form-control" id="medicalConditions" name="medical_conditions" placeholder="List all medical conditions" rows="3">{{ old('medical_conditions') }}</textarea>
                            </div>
                            <div class="col-md-3">
                                <label for="medications" class="form-label">Medications</label>
                                <textarea class="form-control" id="medications" name="medications" placeholder="List all medications" rows="3">{{ old('medications') }}</textarea>
                            </div>
                            <div class="col-md-3">
                                <label for="allergies" class="form-label">Allergies</label>
                                <textarea class="form-control" id="allergies" name="allergies" placeholder="List all allergies" rows="3">{{ old('allergies') }}</textarea>
                            </div>
                            <div class="col-md-3">
                                <label for="immunizations" class="form-label">Immunizations</label>
                                <textarea class="form-control" id="immunizations" name="immunizations" placeholder="List all immunizations" rows="3">{{ old('immunizations') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <!-- Category Dropdown -->
                            <div class="col-md-3 position-relative">
                                <label for="category" class="form-label">Category<label style="color:red;"> * </label></label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="" disabled {{old('catogory') ? '' : 'selected' }}>Select category</option>
                                    @foreach ($categories as $category)
                                        <option value="{{ $category->category_id }}" {{ old('category') == $category->category_id ? 'selected' : '' }}>
                                            {{ $category->category_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Care Needs -->
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <h5 class="text-start">Care Needs<label style="color:red;"> * </label></h5>
                            </div>
                        </div>

                        <!-- Care Needs Rows -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Mobility</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="mobilityFrequency" name="frequency[mobility]" placeholder="Frequency" rows="2">{{ old('frequency.mobility') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="mobilityAssistance" name="assistance[mobility]" placeholder="Assistance Required" rows="2">{{ old('assistance.mobility') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Cognitive / Communication</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="cognitiveFrequency" name="frequency[cognitive]" placeholder="Frequency" rows="2">{{ old('frequency.cognitive') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="cognitiveAssistance" name="assistance[cognitive]" placeholder="Assistance Required" rows="2">{{ old('assistance.cognitive') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Self-sustainability</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="selfSustainabilityFrequency" name="frequency[self_sustainability]" placeholder="Frequency" rows="2">{{ old('frequency.self_sustainability') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="selfSustainabilityAssistance" name="assistance[self_sustainability]" placeholder="Assistance Required" rows="2">{{ old('assistance.self_sustainability') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Disease / Therapy Handling</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="diseaseFrequency" name="frequency[disease]" placeholder="Frequency" rows="2">{{ old('frequency.disease') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="diseaseAssistance" name="assistance[disease]" placeholder="Assistance Required" rows="2">{{ old('assistance.disease') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Daily Life / Social Contact</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="dailyLifeFrequency" name="frequency[daily_life]" placeholder="Frequency" rows="2">{{ old('frequency.daily_life') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="dailyLifeAssistance" name="assistance[daily_life]" placeholder="Assistance Required" rows="2">{{ old('assistance.daily_life') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Outdoor Activities</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="outdoorFrequency" name="frequency[outdoor]" placeholder="Frequency" rows="2">{{ old('frequency.outdoor') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="outdoorAssistance" name="assistance[outdoor]" placeholder="Assistance Required" rows="2">{{ old('assistance.outdoor') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Household Keeping</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="householdFrequency" name="frequency[household]" placeholder="Frequency" rows="2">{{ old('frequency.household') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="householdAssistance" name="assistance[household]" placeholder="Assistance Required" rows="2">{{ old('assistance.household') }}</textarea>
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
                                    <input type="text" class="form-control" name="medication_name[]" placeholder="Enter Medication name" >
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="dosage[]" placeholder="Enter Dosage" >
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="frequency[]" placeholder="Enter Frequency" >
                                </div>
                                <div class="col-md-4">
                                    <textarea class="form-control" name="administration_instructions[]" placeholder="Enter Administration Instructions" rows="1" ></textarea>
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
                                    <textarea class="form-control" id="walkingAbility" name="mobility[walking_ability]" 
                                            placeholder="Enter details about walking ability" rows="2" 
                                            pattern="^[A-Za-z0-9\s.,\-()'\"+’!?]+$"!?]+$" 
                                            title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('mobility.walking_ability') }}</textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="assistiveDevices" class="form-label">Assistive Devices</label>
                                    <textarea class="form-control" id="assistiveDevices" name="mobility[assistive_devices]" 
                                            placeholder="Enter details about assistive devices" rows="2" 
                                            pattern="^[A-Za-z0-9\s.,\-()'\"+’!?]+$"!?]+$" 
                                            title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('mobility.assistive_devices') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="transportationNeeds" class="form-label">Transportation Needs</label>
                                    <textarea class="form-control" id="transportationNeeds" name="mobility[transportation_needs]" 
                                            placeholder="Enter details about transportation needs" rows="2" 
                                            pattern="^[A-Za-z0-9\s.,\-()'\"+’!?]+$"!?]+$" 
                                            title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('mobility.transportation_needs') }}</textarea>
                                </div>
                            </div>

                            <!-- Cognitive Function Section -->
                            <div class="col-md-4">
                                <h5 class="text-start">Cognitive Function</h5>
                                <div class="mb-1">
                                    <label for="memory" class="form-label">Memory</label>
                                    <textarea class="form-control" id="memory" name="cognitive[memory]" 
                                            placeholder="Enter details about memory" rows="2" 
                                            pattern="^[A-Za-z0-9\s.,\-()'\"+’!?]+$"!?]+$" 
                                            title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('cognitive.memory') }}</textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="thinkingSkills" class="form-label">Thinking Skills</label>
                                    <textarea class="form-control" id="thinkingSkills" name="cognitive[thinking_skills]" 
                                            placeholder="Enter details about thinking skills" rows="2" 
                                            pattern="^[A-Za-z0-9\s.,\-()'\"+’!?]+$"!?]+$" 
                                            title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('cognitive.thinking_skills') }}</textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="orientation" class="form-label">Orientation</label>
                                    <textarea class="form-control" id="orientation" name="cognitive[orientation]" 
                                            placeholder="Enter details about orientation" rows="2" 
                                            pattern="^[A-Za-z0-9\s.,\-()'\"+’!?]+$"!?]+$" 
                                            title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('cognitive.orientation') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="behavior" class="form-label">Behavior</label>
                                    <textarea class="form-control" id="behavior" name="cognitive[behavior]" 
                                            placeholder="Enter details about behavior" rows="2" 
                                            pattern="^[A-Za-z0-9\s.,\-()'\"+’!?]+$"!?]+$" 
                                            title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('cognitive.behavior') }}</textarea>
                                </div>
                            </div>

                            <!-- Emotional Well-being Section -->
                            <div class="col-md-4">
                                <h5 class="text-start">Emotional Well-being</h5>
                                <div class="mb-1">
                                    <label for="mood" class="form-label">Mood</label>
                                    <textarea class="form-control" id="mood" name="emotional[mood]" 
                                            placeholder="Enter details about mood" rows="2" 
                                            pattern="^[A-Za-z0-9\s.,\-()'\"+’!?]+$"!?]+$" 
                                            title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('emotional.mood') }}</textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="socialInteractions" class="form-label">Social Interactions</label>
                                    <textarea class="form-control" id="socialInteractions" name="emotional[social_interactions]" 
                                            placeholder="Enter details about social interactions" rows="2" 
                                            pattern="^[A-Za-z0-9\s.,\-()'\"+’!?]+$"!?]+$" 
                                            title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('emotional.social_interactions') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="emotionalSupport" class="form-label">Emotional Support Need</label>
                                    <textarea class="form-control" id="emotionalSupport" name="emotional[emotional_support]" 
                                            placeholder="Enter details about emotional support need" rows="2" 
                                            pattern="^[A-Za-z0-9\s.,\-()'\"+’!?]+$"!?]+$" 
                                            title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('emotional.emotional_support') }}</textarea>
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
                            <!-- Contact Name -->
                            <div class="col-md-3">
                                <label for="contactName" class="form-label">Contact Name<label style="color:red;"> * </label></label>
                                <input type="text" class="form-control" id="contactName" name="emergency_contact[name]" 
                                    value="{{ old('emergency_contact.name') }}"
                                    placeholder="Enter contact name" 
                                    required 
                                    pattern="^[A-Z][a-zA-Z]*(?: [A-Z][a-zA-Z]*)+$" 
                                    title="Must be a valid full name with each word starting with an uppercase letter.">
                            </div>

                            <!-- Relation -->
                            <div class="col-md-3">
                                <label for="relation" class="form-label">Relation<label style="color:red;"> * </label></label>
                                <select class="form-select" id="relation" name="emergency_contact[relation]" required>
                                    <option value="" disabled {{ old('emergency_contact.relation') ? '' : 'selected' }}>Select relation</option>
                                    <option value="Parent" {{old('emergency_contact.relation') == 'Parent' ? 'selected' : '' }}>Parent</option>
                                    <option value="Sibling" {{old('emergency_contact.relation') == 'Sibling' ? 'selected' : '' }}>Sibling</option>
                                    <option value="Spouse" {{old('emergency_contact.relation') == 'Spouse' ? 'selected' : '' }}>Spouse</option>
                                    <option value="Child" {{old('emergency_contact.relation') == 'Child' ? 'selected' : '' }}>Child</option>
                                    <option value="Relative" {{old('emergency_contact.relation') == 'Relative' ? 'selected' : '' }}>Relative</option>
                                    <option value="Friend" {{old('emergency_contact.relation') == 'Friend' ? 'selected' : '' }}>Friend</option>
                                    <option value="Other" {{old('emergency_contact.relation') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <!-- Mobile Number -->
                            <div class="col-md-3">
                                <label for="emergencyMobileNumber" class="form-label">Mobile Number<label style="color:red;"> * </label></label>
                                <div class="input-group">
                                    <span class="input-group-text">+63</span>
                                    <input type="text" class="form-control" id="emergencyMobileNumber" name="emergency_contact[mobile]" 
                                        value="{{ old('emergency_contact.mobile') }}"
                                        placeholder="Enter mobile number" 
                                        maxlength="10" 
                                        required 
                                        oninput="restrictToNumbers(this)" 
                                        title="Must be 10 digits.">
                                </div>
                            </div>

                            <!-- Email Address -->
                            <div class="col-md-3">
                                <label for="emailAddress" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="emailAddress" name="emergency_contact[email]" 
                                    value="{{ old('emergency_contact.email') }}"
                                    placeholder="Enter email address">
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
                                <label for="emergencyProcedures" class="form-label">Emergency Procedures<label style="color:red;"> * </label></label>
                                <textarea class="form-control" id="emergencyProcedures" name="emergency_plan[procedures]" 
                                        placeholder="Enter emergency procedures" 
                                        rows="3" 
                                        required 
                                        pattern="^[A-Za-z0-9\s.,\-()'\"+’!?]+$"!?]+$" 
                                        title="Only letters, numbers, spaces, commas, periods, hyphens, parentheses, single quotes, double quotes, apostrophes, and exclamation/question marks are allowed.">{{ old('emergency_plan.procedures') }}</textarea>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        <!-- Care Worker's Responsibilities -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Care Worker's Responsibilities<label style="color:red;"> * </label></h5> 
                            </div>
                        </div>
                        <div class="row mb-1">
                            <!-- Select Care Worker -->
                            <div class="col-md-3">
                                <label for="careworkerName" class="form-label">Select Care Worker<label style="color:red;"> * </label></label>
                                <select class="form-select" id="careworkerName" name="care_worker[careworker_id]" required>
                                    <option value="" disabled {{ old('care_worker.careworker_id') ? '' : 'selected' }}>Select Care Worker<label style="color:red;"> * </label></option>
                                    @foreach ($careWorkers as $careWorker)
                                        <option value="{{ $careWorker->id }}" {{ old('care_worker.careworker_id') == $careWorker->id ? 'selected' : '' }}>
                                            {{ $careWorker->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tasks and Responsibilities -->
                            <div class="col-md-5">
                                <label class="form-label">Tasks and Responsibilities<label style="color:red;"> * </label></label>
                                <div id="tasksContainer">
                                    <div class="input-group mb-2">
                                        <input type="text" class="form-control" name="care_worker[tasks][]"
                                            value="{{ old('care_worker.tasks.0') ?? '' }}" 
                                            placeholder="Enter task or responsibility" 
                                            required 
                                            pattern="^[A-Za-z0-9\s.,\-()'\"+’!?]+$"!?]+$" 
                                            title="Only letters, numbers, spaces, commas, periods, and hyphens are allowed.">
                                    </div>
                                </div>
                            </div>

                            <!-- Add/Delete Task Buttons -->
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
                        <!-- Beneficiary Picture -->
                            <div class="col-md-3">
                                <label for="beneficiaryProfilePic" class="form-label">Upload Beneficiary Picture</label>
                                <input type="file" class="form-control" id="beneficiaryProfilePic" name="beneficiaryProfilePic" 
                                    accept="image/png, image/jpeg" 
                                    title="Only PNG and JPEG images are allowed.">
                                @if($errors->any())
                                <small class="text-danger">Note: You need to select the file again after a validation error.</small>
                                @endif
                            </div>

                            <!-- Review Date -->
                            <div class="col-md-3">
                                <label for="datePicker" class="form-label">Review Date</label>
                                <input type="date" class="form-control" id="datePicker" name="date" 
                                    value="{{ date('Y-m-d') }}" 
                                    required 
                                    max="{{ date('Y-m-d', strtotime('+1 year')) }}" 
                                    min="{{ date('Y-m-d') }}" 
                                    title="The date must be within 1 year from today.">
                            </div>

                            <!-- Care Service Agreement -->
                            <div class="col-md-3">
                                <label for="careServiceAgreement" class="form-label">Care Service Agreement</label>
                                <input type="file" class="form-control" id="careServiceAgreement" name="care_service_agreement" 
                                    accept=".pdf,.doc,.docx" 
                                    required 
                                    title="Only PDF, DOC, and DOCX files are allowed.">
                                @if($errors->any())
                                <small class="text-danger">Note: You need to select the file again after a validation error.</small>
                                @endif
                            </div>

                            <!-- General Careplan -->
                            <div class="col-md-3">
                                <label for="generalCareplan" class="form-label">General Careplan</label>
                                <input type="file" class="form-control" id="generalCareplan" name="general_careplan" 
                                    accept=".pdf,.doc,.docx" 
                                    title="Only PDF, DOC, and DOCX files are allowed.">
                                @if($errors->any())
                                <small class="text-danger">Note: You need to select the file again after a validation error.</small>
                                @endif
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
                                @if($errors->any())
                                <small class="text-danger">Note: You need to upload/sign again after a validation error.</small>
                                @endif
                                </div>
                                <div class="row mb-1">
                                    <div class="col-md-12">
                                        <label for="beneficiarySignatureUpload" class="form-label">Upload Beneficiary Signature</label>
                                        <input type="file" class="form-control" id="beneficiarySignatureUpload" name="beneficiary_signature_upload" accept="image/png, image/jpeg">
                                    </div>
                                </div>
                                <!-- Hidden input to store the canvas signature as base64 -->
                                <input type="hidden" id="beneficiarySignatureCanvas" name="beneficiary_signature_canvas">
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
                                @if($errors->any())
                                <small class="text-danger">Note: You need to upload/sign again after a validation error.</small>
                                @endif
                                </div>
                                <div class="row mb-1">
                                    <div class="col-md-12">
                                        <label for="careWorkerSignatureUpload" class="form-label">Upload Care Worker Signature</label>
                                        <input type="file" class="form-control" id="careWorkerSignatureUpload" name="care_worker_signature_upload" accept="image/png, image/jpeg">
                                    </div>
                                </div>
                                <!-- Hidden input to store the canvas signature as base64 -->
                                <input type="hidden" id="careWorkerSignatureCanvas" name="care_worker_signature_canvas">
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Account Registration -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Family Portal Account Registration</h5> 
                            </div>
                        </div>
                        <div class="row mb-3">
                            <!-- Email -->
                            <div class="col-md-4">
                                <label for="accountEmail" class="form-label">Email<label style="color:red;"> * </label></label>
                                <input type="email" class="form-control" id="accountEmail" name="account[email]" 
                                    placeholder="Enter email"
                                    value="{{ old('account.email') }}" 
                                    required 
                                    pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" 
                                    title="Enter a valid email address (e.g., example@domain.com)" 
                                    oninput="validateEmail(this)">
                            </div>

                            <!-- Password -->
                            <div class="col-md-4">
                                <label for="password" class="form-label">Password<label style="color:red;"> * </label></label>
                                <input type="password" class="form-control" id="password" name="account[password]" 
                                    placeholder="Enter password" 
                                    required 
                                    minlength="8" 
                                    title="Password must be at least 8 characters long.">
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-4">
                                <label for="confirmPassword" class="form-label">Confirm Password<label style="color:red;"> * </label></label>
                                <input type="password" class="form-control" id="confirmPassword" name="account[password_confirmation]" 
                                    placeholder="Confirm password" 
                                    required 
                                    title="Passwords must match.">
                            </div>
                        </div>

                        
                        <div class="row mt-4">
                            <div class="col-12 d-flex justify-content-center align-items-center">
                                <button type="submit" class="btn btn-success btn-lg d-flex align-items-center" id="saveBeneficiaryButton">
                                    <i class='bx bx-save me-2' style="font-size: 24px;"></i>
                                    Save Beneficiary
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Save Beneficiary Success Modal -->
    <div class="modal fade" id="saveSuccessModal" tabindex="-1" aria-labelledby="saveSuccessModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="saveSuccessModalLabel">Success</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p>Beneficiary has been successfully saved!</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/signature_pad/1.5.3/signature_pad.min.js"></script>


    <script src=" {{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        function addMedicationRow(name = '', dosage = '', freq = '', instructions = '') {
        const container = document.getElementById('medicationManagement');
        const newRow = document.createElement('div');
        newRow.className = 'row mb-2 align-items-center medication-row';
        newRow.innerHTML = `
            <div class="col-md-3">
                <input type="text" class="form-control" name="medication_name[]" value="${name}" placeholder="Enter medication name">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="dosage[]" value="${dosage}" placeholder="Enter dosage">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="frequency[]" value="${freq}" placeholder="Enter frequency">
            </div>
            <div class="col-md-4">
                <textarea class="form-control" name="administration_instructions[]" placeholder="Enter administration instructions" rows="1">${instructions}</textarea>
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
        function addTask(taskValue = '') {
            const tasksContainer = document.getElementById('tasksContainer');
            const inputGroup = document.createElement('div');
            inputGroup.className = 'input-group mb-2';

            const input = document.createElement('input');
            input.type = 'text';
            input.className = 'form-control';
            input.name = 'care_worker[tasks][]';
            input.value = taskValue; 
            input.placeholder = 'Enter task or responsibility';
            input.required = true;
            input.pattern = "^[A-Za-z0-9\\s.,\\-()]+$";
            input.title = "Only letters, numbers, spaces, commas, periods, and hyphens are allowed.";

            inputGroup.appendChild(input);
            tasksContainer.appendChild(inputGroup);
        }

        function removeTask() {
            const tasksContainer = document.getElementById('tasksContainer');
            if (tasksContainer.children.length > 1) {
                tasksContainer.lastChild.remove();
            } else {
                alert('At least one task is required.');
            }
        }

    </script>
    <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            e.preventDefault();
            const successModal = new bootstrap.Modal(document.getElementById('saveSuccessModal'));
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

            // For medication fields
            @if(old('medication_name'))
                // Remove the default row
                document.querySelector('.medication-row').remove();
                
                // Re-create rows with old data
                @foreach(old('medication_name') as $index => $name)
                    addMedicationRow(
                        '{{ $name }}', 
                        '{{ old('dosage.'.$index) }}', 
                        '{{ old('frequency.'.$index) }}', 
                        '{{ old('administration_instructions.'.$index) }}'
                    );
                @endforeach
            @endif

            // For tasks list
            // Check if we have old tasks data from validation errors
            @if(old('care_worker.tasks'))
                // Clear the default first task input to avoid duplicates
                tasksContainer.innerHTML = '';
                
                // Loop through all old task values and create inputs for them
                @foreach(old('care_worker.tasks') as $task)
                    addTask('{{ $task }}');
                @endforeach
            @endif

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

            // Clear Beneficiary Signature
            document.getElementById("clear-signature-1").addEventListener("click", function () {
                signaturePad1.clear();
                document.getElementById("beneficiarySignatureCanvas").value = ""; // Clear the hidden input
            });

            // Clear Care Worker Signature
            document.getElementById("clear-signature-2").addEventListener("click", function () {
                signaturePad2.clear();
                document.getElementById("careWorkerSignatureCanvas").value = ""; // Clear the hidden input
            });

            // Save Beneficiary Signature as base64 when a drawing is detected
            canvas1.addEventListener("mouseup", function () {
                if (!signaturePad1.isEmpty()) {
                    const signatureDataURL = signaturePad1.toDataURL("image/png");
                    document.getElementById("beneficiarySignatureCanvas").value = signatureDataURL;
                }
            });

            canvas1.addEventListener("touchend", function () {
                if (!signaturePad1.isEmpty()) {
                    const signatureDataURL = signaturePad1.toDataURL("image/png");
                    document.getElementById("beneficiarySignatureCanvas").value = signatureDataURL;
                }
            });

            // Save Care Worker Signature as base64 when a drawing is detected
            canvas2.addEventListener("mouseup", function () {
                if (!signaturePad2.isEmpty()) {
                    const signatureDataURL = signaturePad2.toDataURL("image/png");
                    document.getElementById("careWorkerSignatureCanvas").value = signatureDataURL;
                }
            });

            canvas2.addEventListener("touchend", function () {
                if (!signaturePad2.isEmpty()) {
                    const signatureDataURL = signaturePad2.toDataURL("image/png");
                    document.getElementById("careWorkerSignatureCanvas").value = signatureDataURL;
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const birthDateInput = document.getElementById('birthDate');

            // Calculate the maximum allowable date (14 years ago from today)
            const today = new Date();
            const maxDate = new Date(today.getFullYear() - 14, today.getMonth(), today.getDate());
            const formattedMaxDate = maxDate.toISOString().split('T')[0]; // Format as YYYY-MM-DD

            // Set the max attribute for the birth_date input
            birthDateInput.setAttribute('max', formattedMaxDate);
        });
    </script>
    <script>
        // for connecting the municipality dropdown to the barangay dropdown
        document.addEventListener('DOMContentLoaded', function () {
    const municipalityDropdown = document.getElementById('municipality');
    const barangayDropdown = document.getElementById('barangay');

    // Set saved values from old input if they exist
    @if(old('municipality'))
        municipalityDropdown.value = "{{ old('municipality') }}";
        barangayDropdown.disabled = false;
    @endif

    // Function to filter barangays
    function updateBarangays() {
        const selectedMunicipalityId = municipalityDropdown.value;
        console.log("Selected Municipality ID:", selectedMunicipalityId);
        
        // Always reset the dropdown first
        barangayDropdown.innerHTML = '<option value="" disabled selected>Select barangay</option>';
        
        // If no municipality selected, disable barangay dropdown and return
        if (!selectedMunicipalityId) {
            barangayDropdown.disabled = true;
            return;
        }
        
        // Enable the barangay dropdown
        barangayDropdown.disabled = false;
        
        // Find and append matching barangay options
        let found = false;
        @foreach ($barangays as $b)
            if (String("{{ $b->municipality_id }}") === String(selectedMunicipalityId)) {
                const option = document.createElement('option');
                option.value = "{{ $b->barangay_id }}";
                option.textContent = "{{ $b->barangay_name }}";
                
                // If this matches the old selected value, select it
                @if(old('barangay'))
                    if ("{{ old('barangay') }}" === "{{ $b->barangay_id }}") {
                        option.selected = true;
                    }
                @endif
                
                barangayDropdown.appendChild(option);
                found = true;
            }
        @endforeach
        
        console.log("Found barangays for municipality:", found);
        
        if (!found) {
            const option = document.createElement('option');
            option.value = "";
            option.textContent = "No barangays available for this municipality";
            option.disabled = true;
            barangayDropdown.appendChild(option);
        }
    }
                // Call the function on page load to set up initial state
            updateBarangays();

            // And add the change event listener
            municipalityDropdown.addEventListener('change', updateBarangays);
        });

        document.addEventListener("DOMContentLoaded", function () {
            const password = document.getElementById("password");
            const confirmPassword = document.getElementById("confirmPassword");

            confirmPassword.addEventListener("input", function () {
                if (confirmPassword.value !== password.value) {
                    confirmPassword.setCustomValidity("Passwords do not match.");
                } else {
                    confirmPassword.setCustomValidity("");
                }
            });
        });
    </script>

    <script>
    // This script specifically handles task restoration
    document.addEventListener('DOMContentLoaded', function() {
        // Important: This needs to run AFTER the tasksContainer might have been referenced elsewhere
        setTimeout(function() {
            // Get the tasks container element
            const tasksContainer = document.getElementById('tasksContainer');
            
            // Check if we have old tasks data from validation errors
            @if(old('care_worker.tasks'))
                // Clear existing tasks first
                tasksContainer.innerHTML = '';
                
                // Loop through all old task values and create inputs for them
                @foreach(old('care_worker.tasks') as $task)
                    addTask('{{ $task }}');
                @endforeach
            @endif
        }, 100); // Small delay to ensure DOM is fully processed
    });
    </script>

</body>
</html>