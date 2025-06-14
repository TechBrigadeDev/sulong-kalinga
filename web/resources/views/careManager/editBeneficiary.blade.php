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

    @include('components.careManagerNavbar')
    @include('components.careManagerSidebar')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('care-manager.beneficiaries.index') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back"></i> Back
                </a>
                <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">EDIT BENEFICIARY</div>
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

                    <form action="{{ route('care-manager.beneficiaries.update', $beneficiary->beneficiary_id) }}" method="POST" enctype="multipart/form-data" id="beneficiaryForm">
                        @csrf
                        @method('PUT')
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
                                        value="{{ old('first_name', $beneficiary->first_name) }}"
                                        placeholder="Enter first name" 
                                        required >
                            </div>
                            <div class="col-md-3 relative">
                                <label for="middleName" class="form-label">Middle Name</label>
                                <input type="text" class="form-control" id="middleName" name="middle_name" 
                                        value="{{ old('middle_name', $beneficiary->middle_name) }}"
                                        placeholder="Enter middle name">
                            </div>
                            <div class="col-md-3 relative">
                                <label for="lastName" class="form-label">Last Name<label style="color:red;"> * </label></label>
                                <input type="text" class="form-control" id="lastName" name="last_name" 
                                        value="{{ old('last_name', $beneficiary->last_name) }}"
                                        placeholder="Enter last name" 
                                        required >
                            </div>
                            <div class="col-md-3 relative">
                                <label for="civilStatus" class="form-label">Civil Status<label style="color:red;"> * </label></label>
                                <select class="form-select" id="civilStatus" name="civil_status" required>
                                    <option value="" disabled>Select civil status</option>
                                    <option value="Single" {{ old('civil_status', $beneficiary->civil_status) == 'Single' ? 'selected' : '' }}>Single</option>
                                    <option value="Married" {{ old('civil_status', $beneficiary->civil_status) == 'Married' ? 'selected' : '' }}>Married</option>
                                    <option value="Widowed" {{ old('civil_status', $beneficiary->civil_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                    <option value="Divorced" {{ old('civil_status', $beneficiary->civil_status) == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 relative">
                                <label for="gender" class="form-label">Gender<label style="color:red;"> * </label></label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="" disabled>Select gender</option>
                                    <option value="Male" {{ old('gender', $beneficiary->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $beneficiary->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender', $beneficiary->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="birthDate" class="form-label">Birthday<label style="color:red;"> * </label></label>
                                <input type="date" class="form-control" id="birthDate" name="birth_date" value="{{ old('birth_date', $birth_date) }}" required onkeydown="return true">
                            </div>
                            <div class="col-md-3">
                                <label for="mobileNumber" class="form-label">Mobile Number</label>
                                <div class="input-group">
                                    <span class="input-group-text">+63</span>
                                    <input type="text" class="form-control" id="mobileNumber"  name="mobile_number" value="{{ old('mobile_number', ltrim($beneficiary->mobile, '+63')) }}" placeholder="Enter mobile number" maxlength="11" required oninput="restrictToNumbers(this)" title="Must be 10 or 11digits.">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="landlineNumber" class="form-label">Landline Number</label>
                                <input type="text" class="form-control" id="landlineNumber" name="landline_number" value="{{ old('landline_number', $beneficiary->landline) }}" placeholder="Enter Landline number" maxlength="10" oninput="restrictToNumbers(this)" title="Must be between 7 and 10 digits.">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 position-relative">
                                <label for="primaryCaregiver" class="form-label">Primary Caregiver</label>
                                <input type="text" class="form-control" id="primaryCaregiver" name="primary_caregiver" value="{{ old('primary_caregiver', $beneficiary->primary_caregiver) }}" placeholder="Enter Primary Caregiver name">                            
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
                                oninput="validateAddress(this)">{{ old('address_details', $beneficiary->street_address) }}</textarea>
                            </div>
                            <div class="col-md-3">
                                <label for="municipality" class="form-label">Municipality<label style="color:red;"> * </label></label>
                                <select class="form-select" id="municipality" name="municipality" required>
                                    <option value="" disabled>Select municipality</option>
                                    @foreach ($municipalities as $municipality)
                                    <option value="{{ $municipality->municipality_id }}" 
                                            {{ old('municipality', $beneficiary->municipality_id) == $municipality->municipality_id ? 'selected' : '' }}>
                                        {{ $municipality->municipality_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div> 
                            <div class="col-md-3">
                                <label for="barangay" class="form-label">Barangay<label style="color:red;"> * </label></label>
                                <select class="form-select" id="barangay" name="barangay" required>
                                    <option value="" disabled>Select barangay</option>
                                    @foreach ($barangays as $b)
                                    <option value="{{ $b->barangay_id }}" 
                                            data-municipality-id="{{ $b->municipality_id }}"
                                            {{ old('barangay', $beneficiary->barangay_id) == $b->barangay_id ? 'selected' : '' }}
                                            style="{{ $beneficiary->municipality_id != $b->municipality_id ? 'display:none' : '' }}">
                                        {{ $b->barangay_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Google Maps -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Map Location<label style="color:red;"> * </label></h5> 
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="mapLocation" class="form-label">Pinpoint in Google Maps<label style="color:red;"> * </label></label>
                                <div id="googleMap" style="width:100%;height:400px;border:1px solid #ccc;"></div>
                                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $latitude) }}">
                                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $longitude) }}">
                                <small class="text-muted">Drag the marker to set the beneficiary's location.</small>
                            </div>
                            <div class="col-md-6">
                                <label for="searchAddress" class="form-label">Or search address</label>
                                <input type="text" id="searchAddress" class="form-control" placeholder="Enter address">
                                <button type="button" id="searchAddressBtn" class="btn btn-primary mt-2">Find on Map</button>
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
                                <textarea class="form-control medical-history-field" id="medicalConditions" name="medical_conditions" placeholder="List all medical conditions" rows="3">{{ old('medical_conditions', is_array($beneficiary->generalCarePlan->healthHistory->medical_conditions ?? '') ? implode(", ", json_decode($beneficiary->generalCarePlan->healthHistory->medical_conditions)) : ($beneficiary->generalCarePlan->healthHistory->medical_conditions ?? '')) }}</textarea>
                                <small class="text-muted">Separate multiple conditions with commas</small>
                            </div>
                            <div class="col-md-3">
                                <label for="medications" class="form-label">Medications</label>
                                <textarea class="form-control medical-history-field" id="medications" name="medications" placeholder="List all medications" rows="3">{{ old('medications', is_array($beneficiary->generalCarePlan->healthHistory->medications ?? '') ? implode(", ", json_decode($beneficiary->generalCarePlan->healthHistory->medications)) : ($beneficiary->generalCarePlan->healthHistory->medications ?? '')) }}</textarea>
                                <small class="text-muted">Separate multiple medications with commas</small>
                            </div>
                            <div class="col-md-3">
                                <label for="allergies" class="form-label">Allergies</label>
                                <textarea class="form-control medical-history-field" id="allergies" name="allergies" placeholder="List all allergies" rows="3">{{ old('allergies', is_array($beneficiary->generalCarePlan->healthHistory->allergies ?? '') ? implode(", ", json_decode($beneficiary->generalCarePlan->healthHistory->allergies)) : ($beneficiary->generalCarePlan->healthHistory->allergies ?? '')) }}</textarea>
                                <small class="text-muted">Separate multiple allergies with commas</small>
                            </div>
                            <div class="col-md-3">
                                <label for="immunizations" class="form-label">Immunizations</label>
                                <textarea class="form-control medical-history-field" id="immunizations" name="immunizations" placeholder="List all immunizations" rows="3">{{ old('immunizations', is_array($beneficiary->generalCarePlan->healthHistory->immunizations ?? '') ? implode(", ", json_decode($beneficiary->generalCarePlan->healthHistory->immunizations)) : ($beneficiary->generalCarePlan->healthHistory->immunizations ?? '')) }}</textarea>
                                <small class="text-muted">Separate multiple immunizations with commas</small>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <!-- Category Dropdown -->
                            <div class="col-md-3 position-relative">
                                <label for="category" class="form-label">Category<label style="color:red;"> * </label></label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="" disabled>Select category</option>
                                    @foreach ($categories as $category)
                                    <option value="{{ $category->category_id }}" {{ old('category', $beneficiary->category_id) == $category->category_id ? 'selected' : '' }}>
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
                                <textarea class="form-control" id="mobilityFrequency" name="frequency[mobility]" placeholder="Frequency" rows="2">{{ old('frequency.mobility', $careNeeds[1]['frequency'] ?? '') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="mobilityAssistance" name="assistance[mobility]" placeholder="Assistance Required" rows="2">{{ old('assistance.mobility', $careNeeds[1]['assistance'] ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Cognitive / Communication</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="cognitiveFrequency" name="frequency[cognitive]" placeholder="Frequency" rows="2">{{ old('frequency.cognitive', $careNeeds[2]['frequency'] ?? '') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="cognitiveAssistance" name="assistance[cognitive]" placeholder="Assistance Required" rows="2">{{ old('assistance.cognitive', $careNeeds[2]['assistance'] ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Self-sustainability</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="selfSustainabilityFrequency" name="frequency[self_sustainability]" placeholder="Frequency" rows="2">{{ old('frequency.self_sustainability', $careNeeds[3]['frequency'] ?? '') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="selfSustainabilityAssistance" name="assistance[self_sustainability]" placeholder="Assistance Required" rows="2">{{ old('assistance.self_sustainability', $careNeeds[3]['assistance'] ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Disease / Therapy Handling</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="diseaseFrequency" name="frequency[disease]" placeholder="Frequency" rows="2">{{ old('frequency.disease', $careNeeds[4]['frequency'] ?? '') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="diseaseAssistance" name="assistance[disease]" placeholder="Assistance Required" rows="2">{{ old('assistance.disease', $careNeeds[4]['assistance'] ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Daily Life / Social Contact</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="dailyLifeFrequency" name="frequency[daily_life]" placeholder="Frequency" rows="2">{{ old('frequency.daily_life', $careNeeds[5]['frequency'] ?? '') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="dailyLifeAssistance" name="assistance[daily_life]" placeholder="Assistance Required" rows="2">{{ old('assistance.daily_life', $careNeeds[5]['assistance'] ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Outdoor Activities</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="outdoorFrequency" name="frequency[outdoor]" placeholder="Frequency" rows="2">{{ old('frequency.outdoor', $careNeeds[6]['frequency'] ?? '') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="outdoorAssistance" name="assistance[outdoor]" placeholder="Assistance Required" rows="2">{{ old('assistance.outdoor', $careNeeds[6]['assistance'] ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Household Keeping</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="householdFrequency" name="frequency[household]" placeholder="Frequency" rows="2">{{ old('frequency.household', $careNeeds[7]['frequency'] ?? '') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="householdAssistance" name="assistance[household]" placeholder="Assistance Required" rows="2">{{ old('assistance.household', $careNeeds[7]['assistance'] ?? '') }}</textarea>                            </div>
                        </div>

                        <hr class="my-4">
                       <!-- Medication Management -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Medication Management</h5> 
                            </div>
                        </div>
                        <div id="medicationManagement">
                            @if(old('medication_name'))
                                @foreach(old('medication_name') as $index => $name)
                                    <div class="row mb-1 align-items-center medication-row">
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" name="medication_name[]" 
                                                value="{{ $name }}" placeholder="Enter Medication name">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" class="form-control" name="dosage[]" 
                                                value="{{ old('dosage.'.$index) }}" placeholder="Enter Dosage">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" class="form-control" name="frequency[]" 
                                                value="{{ old('frequency.'.$index) }}" placeholder="Enter Frequency">
                                        </div>
                                        <div class="col-md-4">
                                            <textarea class="form-control" name="administration_instructions[]" 
                                                placeholder="Enter Administration Instructions" rows="1">{{ old('administration_instructions.'.$index) }}</textarea>
                                        </div>
                                        <div class="col-md-1 d-flex text-start">
                                            <button type="button" class="btn btn-danger" onclick="removeMedicationRow(this)">Delete</button>
                                        </div>
                                    </div>
                                @endforeach
                            @elseif($beneficiary->generalCarePlan && $beneficiary->generalCarePlan->medications->count() > 0)
                                @foreach($beneficiary->generalCarePlan->medications as $medication)
                                    <div class="row mb-1 align-items-center medication-row">
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" name="medication_name[]" 
                                                value="{{ $medication->medication }}" placeholder="Enter Medication name">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" class="form-control" name="dosage[]" 
                                                value="{{ $medication->dosage }}" placeholder="Enter Dosage">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" class="form-control" name="frequency[]" 
                                                value="{{ $medication->frequency }}" placeholder="Enter Frequency">
                                        </div>
                                        <div class="col-md-4">
                                            <textarea class="form-control" name="administration_instructions[]" 
                                                placeholder="Enter Administration Instructions" rows="1">{{ $medication->administration_instructions }}</textarea>
                                        </div>
                                        <div class="col-md-1 d-flex text-start">
                                            <button type="button" class="btn btn-danger" onclick="removeMedicationRow(this)">Delete</button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="row mb-1 align-items-center medication-row">
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="medication_name[]" placeholder="Enter Medication name">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control" name="dosage[]" placeholder="Enter Dosage">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control" name="frequency[]" placeholder="Enter Frequency">
                                    </div>
                                    <div class="col-md-4">
                                        <textarea class="form-control" name="administration_instructions[]" placeholder="Enter Administration Instructions" rows="1"></textarea>
                                    </div>
                                    <div class="col-md-1 d-flex text-start">
                                        <button type="button" class="btn btn-danger" onclick="removeMedicationRow(this)">Delete</button>
                                    </div>
                                </div>
                            @endif
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
                                        pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$"!?]+$" 
                                        title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('mobility.walking_ability', $beneficiary->generalCarePlan->mobility->walking_ability ?? '') }}</textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="assistiveDevices" class="form-label">Assistive Devices</label>
                                    <textarea class="form-control" id="assistiveDevices" name="mobility[assistive_devices]" 
                                        placeholder="Enter details about assistive devices" rows="2" 
                                        pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$"!?]+$" 
                                        title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('mobility.assistive_devices', $beneficiary->generalCarePlan->mobility->assistive_devices ?? '') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="transportationNeeds" class="form-label">Transportation Needs</label>
                                    <textarea class="form-control" id="transportationNeeds" name="mobility[transportation_needs]" 
                                        placeholder="Enter details about transportation needs" rows="2" 
                                        pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$"!?]+$" 
                                        title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('mobility.transportation_needs', $beneficiary->generalCarePlan->mobility->transportation_needs ?? '') }}</textarea>
                                </div>
                            </div>

                            <!-- Cognitive Function Section -->
                            <div class="col-md-4">
                                <h5 class="text-start">Cognitive Function</h5>
                                <div class="mb-1">
                                    <label for="memory" class="form-label">Memory</label>
                                    <textarea class="form-control" id="memory" name="cognitive[memory]" 
                                        placeholder="Enter details about memory" rows="2" 
                                        pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$"!?]+$" 
                                        title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('cognitive.memory', $beneficiary->generalCarePlan->cognitiveFunction->memory ?? '') }}</textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="thinkingSkills" class="form-label">Thinking Skills</label>
                                    <textarea class="form-control" id="thinkingSkills" name="cognitive[thinking_skills]" 
                                        placeholder="Enter details about thinking skills" rows="2" 
                                        pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$"!?]+$" 
                                        title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('cognitive.thinking_skills', $beneficiary->generalCarePlan->cognitiveFunction->thinking_skills ?? '') }}</textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="orientation" class="form-label">Orientation</label>
                                    <textarea class="form-control" id="orientation" name="cognitive[orientation]" 
                                        placeholder="Enter details about orientation" rows="2" 
                                        pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$"!?]+$" 
                                        title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('cognitive.orientation', $beneficiary->generalCarePlan->cognitiveFunction->orientation ?? '') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="behavior" class="form-label">Behavior</label>
                                    <textarea class="form-control" id="behavior" name="cognitive[behavior]" 
                                        placeholder="Enter details about behavior" rows="2" 
                                        pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$"!?]+$" 
                                        title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('cognitive.behavior', $beneficiary->generalCarePlan->cognitiveFunction->behavior ?? '') }}</textarea>
                                </div>
                            </div>

                            <!-- Emotional Well-being Section -->
                            <div class="col-md-4">
                                <h5 class="text-start">Emotional Well-being</h5>
                                <div class="mb-1">
                                    <label for="mood" class="form-label">Mood</label>
                                    <textarea class="form-control" id="mood" name="emotional[mood]" 
                                        placeholder="Enter details about mood" rows="2" 
                                        pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$"!?]+$" 
                                        title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('emotional.mood', $beneficiary->generalCarePlan->emotionalWellbeing->mood ?? '') }}</textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="socialInteractions" class="form-label">Social Interactions</label>
                                    <textarea class="form-control" id="socialInteractions" name="emotional[social_interactions]" 
                                        placeholder="Enter details about social interactions" rows="2" 
                                        pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$"!?]+$" 
                                        title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('emotional.social_interactions', $beneficiary->generalCarePlan->emotionalWellbeing->social_interactions ?? '') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="emotionalSupport" class="form-label">Emotional Support Need</label>
                                    <textarea class="form-control" id="emotionalSupport" name="emotional[emotional_support]" 
                                        placeholder="Enter details about emotional support need" rows="2" 
                                        pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$"!?]+$" 
                                        title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('emotional.emotional_support', $beneficiary->generalCarePlan->emotionalWellbeing->emotional_support_needs ?? '') }}</textarea>
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
                                    value="{{ old('emergency_contact.name', $beneficiary->emergency_contact_name) }}"
                                    placeholder="Enter contact name" 
                                    required >
                            </div>

                            <!-- Relation -->
                            <div class="col-md-3">
                                <label for="relation" class="form-label">Relation<label style="color:red;"> * </label></label>
                                <select class="form-select" id="relation" name="emergency_contact[relation]" required>
                                    <option value="" disabled>Select relation</option>
                                    <option value="Parent" {{ old('emergency_contact.relation', $beneficiary->emergency_contact_relation) == 'Parent' ? 'selected' : '' }}>Parent</option>
                                    <option value="Sibling" {{ old('emergency_contact.relation', $beneficiary->emergency_contact_relation) == 'Sibling' ? 'selected' : '' }}>Sibling</option>
                                    <option value="Spouse" {{ old('emergency_contact.relation', $beneficiary->emergency_contact_relation) == 'Spouse' ? 'selected' : '' }}>Spouse</option>
                                    <option value="Child" {{ old('emergency_contact.relation', $beneficiary->emergency_contact_relation) == 'Child' ? 'selected' : '' }}>Child</option>
                                    <option value="Relative" {{ old('emergency_contact.relation', $beneficiary->emergency_contact_relation) == 'Relative' ? 'selected' : '' }}>Relative</option>
                                    <option value="Friend" {{ old('emergency_contact.relation', $beneficiary->emergency_contact_relation) == 'Friend' ? 'selected' : '' }}>Friend</option>
                                    <option value="Other" {{ old('emergency_contact.relation', $beneficiary->emergency_contact_relation) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>

                            <!-- Mobile Number -->
                            <div class="col-md-3">
                                <label for="emergencyMobileNumber" class="form-label">Mobile Number<label style="color:red;"> * </label></label>
                                <div class="input-group">
                                    <span class="input-group-text">+63</span>
                                    <input type="text" class="form-control" id="emergencyMobileNumber" name="emergency_contact[mobile]" 
                                        value="{{ old('emergency_contact.mobile', str_replace('+63', '', $beneficiary->emergency_contact_mobile)) }}"
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
                                    value="{{ old('emergency_contact.email', $beneficiary->emergency_contact_email) }}"
                                    placeholder="Enter email address" 
                                    required>
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
                                    pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$"!?]+$" 
                                    title="Only letters, numbers, spaces, commas, periods, hyphens, parentheses, single quotes, double quotes, apostrophes, and exclamation/question marks are allowed.">{{ old('emergency_plan.procedures', $beneficiary->emergency_procedure) }}</textarea>
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
                                    <option value="" disabled>Select Care Worker</option>
                                    @foreach ($careWorkers as $careWorker)
                                        <option value="{{ $careWorker->id }}" {{ old('care_worker.careworker_id', $currentCareWorker) == $careWorker->id ? 'selected' : '' }}>
                                            {{ $careWorker->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tasks and Responsibilities -->
                            <div class="col-md-5">
                                <label class="form-label">Tasks and Responsibilities<label style="color:red;"> * </label></label>
                                <div id="tasksContainer">
                                    @if(old('care_worker.tasks'))
                                        @foreach(old('care_worker.tasks') as $task)
                                            <div class="input-group mb-2">
                                                <input type="text" class="form-control" name="care_worker[tasks][]"
                                                    value="{{ $task }}" 
                                                    placeholder="Enter task or responsibility" 
                                                    required 
                                                    pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$" 
                                                    title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">
                                            </div>
                                        @endforeach
                                    @elseif(isset($currentCareWorkerTasks) && count($currentCareWorkerTasks) > 0)
                                        @foreach($currentCareWorkerTasks as $task)
                                            <div class="input-group mb-2">
                                                <input type="text" class="form-control" name="care_worker[tasks][]"
                                                    value="{{ $task }}" 
                                                    placeholder="Enter task or responsibility" 
                                                    required 
                                                    pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$" 
                                                    title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" name="care_worker[tasks][]"
                                                placeholder="Enter task or responsibility" 
                                                required 
                                                pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$" 
                                                title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">
                                        </div>
                                    @endif
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
                            <label for="beneficiaryProfilePic" class="form-label">Beneficiary Picture</label>
                            <input type="file" class="form-control" id="beneficiaryProfilePic" name="beneficiaryProfilePic" 
                                accept="image/png, image/jpeg" 
                                title="Only PNG and JPEG images are allowed.">
                                @if($beneficiary->photo)
                                    <div class="d-flex align-items-center mt-1">
                                        <i class="bx bx-file text-primary"></i>
                                        <small class="text-muted ms-1 file-name-container" title="{{ basename($beneficiary->photo) }}">
                                            Current file: {{ substr(basename($beneficiary->photo), 0, 30) }}{{ strlen(basename($beneficiary->photo)) > 30 ? '...' : '' }}
                                        </small>
                                    </div>
                                @else
                                    <small class="text-muted">No file uploaded</small>
                                @endif
                                <small class="text-danger">Maximum file size: 7MB. Please compress or split larger files.</small>
                        </div>

                            <!-- Review Date -->
                            <div class="col-md-3">
                                <label for="datePicker" class="form-label">Review Date</label>
                                <input type="date" class="form-control" id="datePicker" name="date" 
                                    value="{{ old('date', $review_date ?? date('Y-m-d')) }}" 
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
                                    title="Only PDF, DOC, and DOCX files are allowed.">
                                    @if($beneficiary->care_service_agreement_doc)
                                        <div class="d-flex align-items-center mt-1">
                                            <i class="bx bx-file text-primary"></i>
                                            <small class="text-muted ms-1 file-name-container" title="{{ basename($beneficiary->care_service_agreement_doc) }}">
                                                Current file: {{ substr(basename($beneficiary->care_service_agreement_doc), 0, 30) }}{{ strlen(basename($beneficiary->care_service_agreement_doc)) > 30 ? '...' : '' }}
                                            </small>
                                        </div>
                                    @else
                                    <small class="text-muted">No file uploaded</small>
                                    @endif
                                    <small class="text-danger">Maximum file size: 5MB. Please compress or split larger files.</small>
                            </div>

                            <!-- General Careplan -->
                            <div class="col-md-3">
                                <label for="generalCareplan" class="form-label">General Careplan</label>
                                <input type="file" class="form-control" id="generalCareplan" name="general_careplan" 
                                    accept=".pdf,.doc,.docx" 
                                    title="Only PDF, DOC, and DOCX files are allowed.">
                                    @if($beneficiary->general_care_plan_doc)
                                        <div class="d-flex align-items-center mt-1">
                                            <i class="bx bx-file text-primary"></i>
                                            <small class="text-muted ms-1 file-name-container" title="{{ basename($beneficiary->general_care_plan_doc) }}">
                                                Current file: {{ substr(basename($beneficiary->general_care_plan_doc), 0, 30) }}{{ strlen(basename($beneficiary->general_care_plan_doc)) > 30 ? '...' : '' }}
                                            </small>
                                        </div>
                                    @else
                                    <small class="text-muted">No file uploaded</small>
                                    @endif
                                    <small class="text-danger">Maximum file size: 5MB. Please compress or split larger files.</small>
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
                                            <label for="beneficiarySignatureUpload" class="form-label">Upload Beneficiary Signature</label>
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
                                <small class="text-danger">Note: The old signature is saved after a validation error. You need to enter the new one again.</small>
                                @endif
                                </div>
                                <div class="row mb-1">
                                    <div class="col-md-12">
                                        <label for="beneficiarySignatureUpload" class="form-label">Upload Beneficiary Signature</label>
                                        <input type="file" class="form-control" id="beneficiarySignatureUpload" name="beneficiary_signature_upload" accept="image/png, image/jpeg">
                                                @if($beneficiary->beneficiary_signature)
                                                    <div class="d-flex align-items-center mt-1">
                                                        <i class="bx bx-file text-primary"></i>
                                                        <small class="text-muted ms-1 file-name-container" title="{{ basename($beneficiary->beneficiary_signature) }}">
                                                            Current signature: {{ substr(basename($beneficiary->beneficiary_signature), 0, 50) }}{{ strlen(basename($beneficiary->beneficiary_signature)) > 50 ? '...' : '' }}
                                                        </small>
                                                    </div>
                                                    <img src="{{ asset('storage/' . $beneficiary->beneficiary_signature) }}" class="img-thumbnail signature-preview" style="max-height: 100px;" alt="Current beneficiary signature">
                                                @else
                                                    <small class="text-muted">No signature uploaded</small>
                                                @endif
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
                                <small class="text-danger">Note: The old signature is saved after a validation error. You need to enter the new one again.</small>
                                @endif
                                </div>
                                <div class="row mb-1">
                                    <div class="col-md-12">
                                        <label for="careWorkerSignatureUpload" class="form-label">Upload Care Worker Signature</label>
                                        <input type="file" class="form-control" id="careWorkerSignatureUpload" name="care_worker_signature_upload" accept="image/png, image/jpeg">
                                                @if($beneficiary->care_worker_signature)
                                                    <div class="d-flex align-items-center mt-1">
                                                        <i class="bx bx-file text-primary"></i>
                                                        <small class="text-muted ms-1 file-name-container" title="{{ basename($beneficiary->care_worker_signature) }}">
                                                            Current signature: {{ substr(basename($beneficiary->care_worker_signature), 0, 60) }}{{ strlen(basename($beneficiary->care_worker_signature)) > 60 ? '...' : '' }}
                                                        </small>
                                                    </div>
                                                    <img src="{{ asset('storage/' . $beneficiary->care_worker_signature) }}" class="img-thumbnail signature-preview" style="max-height: 100px;" alt="Current beneficiary signature">
                                                @else
                                                    <small class="text-muted">No signature uploaded</small>
                                                @endif
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
                                <h5 class="text-start">Login Access</h5> 
                            </div>
                        </div>
                        <div class="row mb-3">
                            <!-- Generated Username -->
                            <div class="col-md-4">
                                <label for="generatedUsername" class="form-label">Username</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="generatedUsername" readonly 
                                        value="{{ $beneficiary->username }}" disabled>
                                    <span class="input-group-text"><i class="bi bi-info-circle" title="Username is automatically generated from name: first initial + middle initial + last name"></i></span>
                                </div>
                                <small class="text-muted">Username will update automatically based on name changes</small>
                                <!-- Hidden field to pass the new username value if name fields are changed -->
                                <input type="hidden" id="updatedUsername" name="updated_username" value="{{ $beneficiary->username }}">
                            </div>

                            <!-- Password -->
                            <div class="col-md-4">
                                <label for="password" class="form-label">Password<label style="color:red;"> * </label></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="account[password]" placeholder="Leave blank to keep old password" minlength="8" 
                                        title="Password must be at least 8 characters long.">
                                    <span class="input-group-text password-toggle" data-target="password">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-4">
                                <label for="confirmPassword" class="form-label">Confirm Password<label style="color:red;"> * </label></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirmPassword" name="account[password_confirmation]" placeholder="Leave blank to keep old password" title="Passwords must match.">
                                    <span class="input-group-text password-toggle" data-target="confirmPassword">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                </div>
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

    <div class="modal fade" id="fileSizeErrorModal" tabindex="-1" aria-labelledby="fileSizeErrorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="fileSizeErrorModalLabel">File Size Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center">
                        <i class="bx bx-error-circle text-danger me-3" style="font-size: 2rem;"></i>
                        <p id="fileSizeErrorMessage" class="mb-0"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
                <input type="text" class="form-control" name="medication_name[]" value="${name}" placeholder="Enter medication name" required>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="dosage[]" value="${dosage}" placeholder="Enter dosage" required>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="frequency[]" value="${freq}" placeholder="Enter frequency" required>
            </div>
            <div class="col-md-4">
                <textarea class="form-control" name="administration_instructions[]" placeholder="Enter administration instructions" rows="1" required>${instructions}</textarea>
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
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('success'))
                const successModal = new bootstrap.Modal(document.getElementById('saveSuccessModal'));
                successModal.show();
            @endif

            const fileSizeErrorModal = new bootstrap.Modal(document.getElementById('fileSizeErrorModal'));
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

            /*
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
            */

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
        document.addEventListener('DOMContentLoaded', function() {
        const municipalityDropdown = document.getElementById('municipality');
        const barangayDropdown = document.getElementById('barangay');
        
        // Function to update barangays based on selected municipality
        function updateBarangays() {
            const selectedMunicipalityId = municipalityDropdown.value;
            console.log("Selected Municipality ID:", selectedMunicipalityId);
            
            // Always reset the dropdown first
            barangayDropdown.innerHTML = '';
            
            // Add default prompt option
            const defaultOption = document.createElement('option');
            defaultOption.value = "";
            defaultOption.disabled = true;
            defaultOption.textContent = "Select barangay";
            barangayDropdown.appendChild(defaultOption);
            
            // If no municipality selected, disable barangay dropdown and return
            if (!selectedMunicipalityId) {
                barangayDropdown.disabled = true;
                return;
            }
            
            // Enable the barangay dropdown
            barangayDropdown.disabled = false;
            
            // Get the old input value if it exists
            const oldBarangayValue = "{{ old('barangay', $beneficiary->barangay_id) }}";
            
            // Find and append matching barangay options
            let found = false;
            @foreach ($barangays as $b)
                if ({{ $b->municipality_id }} == selectedMunicipalityId) {
                    const option = document.createElement('option');
                    option.value = "{{ $b->barangay_id }}";
                    option.textContent = "{{ $b->barangay_name }}";
                    
                    // Select this option if it matches the old input or the beneficiary's barangay
                    if ("{{ $b->barangay_id }}" == oldBarangayValue) {
                        option.selected = true;
                    }
                    
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
                option.selected = true;
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
        document.addEventListener('DOMContentLoaded', function() {
            // Format medical history fields that might contain JSON arrays
            document.querySelectorAll('.medical-history-field').forEach(function(field) {
                const value = field.value;
                
                // Check if the value looks like a JSON array
                if (value.trim().startsWith('[') && value.trim().endsWith(']')) {
                    try {
                        // Parse the JSON and display as comma-separated list
                        const parsedValue = JSON.parse(value);
                        if (Array.isArray(parsedValue)) {
                            field.value = parsedValue.join(', ');
                        }
                    } catch (e) {
                        // If parsing fails, keep the original value
                        console.log('Error parsing JSON field:', e);
                    }
                }
            });
            
            // Add form submission handler
            document.getElementById('beneficiaryForm').addEventListener('submit', function() {
                // No need for special processing - the backend will handle comma-separated values
            });
        });
    </script>

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Max sizes in bytes
        const MAX_SIZES = {
            'beneficiaryProfilePic': 7 * 1024 * 1024, // 7MB
            'careServiceAgreement': 5 * 1024 * 1024, // 5MB
            'generalCareplan': 5 * 1024 * 1024 // 5MB
        };
        
        // Get the modal elements
        const fileSizeErrorModal = new bootstrap.Modal(document.getElementById('fileSizeErrorModal'));
        const fileSizeErrorMessage = document.getElementById('fileSizeErrorMessage');
        
        // Add file size validation to all file inputs
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function() {
                if (this.files.length > 0) {
                    const file = this.files[0];
                    const maxSize = MAX_SIZES[this.id] || 5 * 1024 * 1024; // Default to 5MB
                    
                    if (file.size > maxSize) {
                        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(1);
                        const maxSizeMB = (maxSize / (1024 * 1024)).toFixed(1);
                        const fieldLabel = this.previousElementSibling ? this.previousElementSibling.textContent : this.id;
                        
                        // Set error message and show modal
                        fileSizeErrorMessage.innerHTML = `
                            <strong>${fieldLabel}</strong> file is too large (${fileSizeMB}MB).<br>
                            Maximum allowed size is ${maxSizeMB}MB.<br>
                            Please select a smaller file or compress your existing file.
                        `;
                        fileSizeErrorModal.show();
                        
                        // Reset the file input
                        this.value = '';
                    }
                }
            });
        });
        
        // Add form submission check to prevent large file uploads
        document.getElementById('beneficiaryForm').addEventListener('submit', function(e) {
            // Validate all file inputs before submission
            let isValid = true;
            
            document.querySelectorAll('input[type="file"]').forEach(input => {
                if (input.files.length > 0) {
                    const file = input.files[0];
                    const maxSize = MAX_SIZES[input.id] || 5 * 1024 * 1024;
                    
                    if (file.size > maxSize) {
                        e.preventDefault();
                        isValid = false;
                        
                        const fileSizeMB = (file.size / (1024 * 1024)).toFixed(1);
                        const maxSizeMB = (maxSize / (1024 * 1024)).toFixed(1);
                        const fieldLabel = input.previousElementSibling ? input.previousElementSibling.textContent : input.id;
                        
                        // Set error message and show modal
                        fileSizeErrorMessage.innerHTML = `
                            <strong>Form submission failed</strong><br>
                            ${fieldLabel} (${fileSizeMB}MB) exceeds the maximum size of ${maxSizeMB}MB.<br>
                            Please select a smaller file or compress your existing file.
                        `;
                        fileSizeErrorModal.show();
                    }
                }
            });
            
            return isValid;
        });
    });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get elements
            const firstNameInput = document.getElementById('firstName');
            const middleNameInput = document.getElementById('middleName');
            const lastNameInput = document.getElementById('lastName');
            const usernamePreview = document.getElementById('generatedUsername');
            
            // Password toggle functionality
            document.querySelectorAll('.password-toggle').forEach(function(toggle) {
                toggle.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const passwordInput = document.getElementById(targetId);
                    const icon = this.querySelector('i');
                    
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    } else {
                        passwordInput.type = 'password';
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    }
                });
            });
            
            // Function to update username preview
            function updateUsernamePreview() {
                const firstName = firstNameInput.value.trim();
                const middleName = middleNameInput.value.trim();
                const lastName = lastNameInput.value.trim();
                
                if (!firstName || !lastName) {
                    usernamePreview.value = "Username will be generated from name fields";
                    return;
                }
                
                // Create preview username
                const firstInitial = firstName.charAt(0).toLowerCase();
                const middleInitial = middleName ? middleName.charAt(0).toLowerCase() : '';
                const cleanLastName = lastName.toLowerCase().replace(/[^a-z0-9]/g, '');
                
                // Show the preview
                usernamePreview.value = firstInitial + middleInitial + cleanLastName;
            }
            
            // Add event listeners
            firstNameInput.addEventListener('input', updateUsernamePreview);
            middleNameInput.addEventListener('input', updateUsernamePreview);
            lastNameInput.addEventListener('input', updateUsernamePreview);
            
            // Password confirmation validation
            const password = document.getElementById("password");
            const confirmPassword = document.getElementById("confirmPassword");
            
            confirmPassword.addEventListener("input", function() {
                if (confirmPassword.value !== password.value) {
                    confirmPassword.setCustomValidity("Passwords do not match.");
                } else {
                    confirmPassword.setCustomValidity("");
                }
            });
            
            // Also update when password changes
            password.addEventListener("input", function() {
                if (confirmPassword.value && confirmPassword.value !== password.value) {
                    confirmPassword.setCustomValidity("Passwords do not match.");
                } else {
                    confirmPassword.setCustomValidity("");
                }
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get elements for name-based username generation
            const firstNameInput = document.getElementById('firstName');
            const middleNameInput = document.getElementById('middleName');
            const lastNameInput = document.getElementById('lastName');
            const usernameDisplay = document.getElementById('generatedUsername');
            const hiddenUsernameField = document.getElementById('updatedUsername');
            
            // Original name values for comparison
            const originalFirstName = '{{ $beneficiary->first_name }}';
            const originalMiddleName = '{{ $beneficiary->middle_name }}';
            const originalLastName = '{{ $beneficiary->last_name }}';
            const originalUsername = '{{ $beneficiary->username }}';
            
            // Function to generate username from name components
            function generateUsername(firstName, middleName, lastName) {
                if (!firstName || !lastName) return originalUsername;
                
                // Create username
                const firstInitial = firstName.charAt(0).toLowerCase();
                const middleInitial = middleName ? middleName.charAt(0).toLowerCase() : '';
                const cleanLastName = lastName.toLowerCase().replace(/[^a-z0-9]/g, '');
                
                return firstInitial + middleInitial + cleanLastName;
            }
            
            // Function to check if name has changed and update username preview
            function updateUsernameIfNameChanged() {
                const currentFirstName = firstNameInput.value.trim();
                const currentMiddleName = middleNameInput.value.trim();
                const currentLastName = lastNameInput.value.trim();
                
                // Check if any name component changed
                if (currentFirstName !== originalFirstName || 
                    currentMiddleName !== originalMiddleName || 
                    currentLastName !== originalLastName) {
                    
                    // Generate new username
                    const newUsername = generateUsername(currentFirstName, currentMiddleName, currentLastName);
                    
                    // Update the visible username field (for display only)
                    usernameDisplay.value = newUsername;
                    
                    // Set the hidden field value that will be submitted with the form
                    hiddenUsernameField.value = newUsername;
                    
                    // Visual indicator that username will change
                    usernameDisplay.style.fontWeight = 'bold';
                    usernameDisplay.style.color = '#007bff';
                } else {
                    // Reset to original if changed back
                    usernameDisplay.value = originalUsername;
                    hiddenUsernameField.value = originalUsername;
                    
                    // Reset styling
                    usernameDisplay.style.fontWeight = 'normal';
                    usernameDisplay.style.color = '';
                }
            }
            
            // Add event listeners to name fields
            firstNameInput.addEventListener('input', updateUsernameIfNameChanged);
            middleNameInput.addEventListener('input', updateUsernameIfNameChanged);
            lastNameInput.addEventListener('input', updateUsernameIfNameChanged);
        });
    </script>

</body>
</html>
    <script src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&callback=initMap" async defer></script>
    <script>
let map, marker, geocoder;

function initMap() {
    var lat = parseFloat(document.getElementById('latitude').value) || 13.41;
    var lng = parseFloat(document.getElementById('longitude').value) || 122.56;
    var initialPosition = {lat: lat, lng: lng};

    map = new google.maps.Map(document.getElementById('googleMap'), {
        center: initialPosition,
        zoom: 12
    });

    marker = new google.maps.Marker({
        position: initialPosition,
        map: map,
        draggable: true
    });

    geocoder = new google.maps.Geocoder();

    marker.addListener('dragend', function(e) {
        document.getElementById('latitude').value = e.latLng.lat();
        document.getElementById('longitude').value = e.latLng.lng();
    });

    document.getElementById('searchAddressBtn').addEventListener('click', function() {
        geocodeAddress();
    });

    document.getElementById('searchAddress').addEventListener('keyup', function(event) {
        if (event.key === 'Enter') {
            geocodeAddress();
        }
    });
}

function geocodeAddress() {
    var address = document.getElementById('searchAddress').value;
    if (!address) return;
    geocoder.geocode({ 'address': address }, function(results, status) {
        if (status === 'OK') {
            var location = results[0].geometry.location;
            map.setCenter(location);
            marker.setPosition(location);
            document.getElementById('latitude').value = location.lat();
            document.getElementById('longitude').value = location.lng();
        } else {
            alert('Geocode was not successful for the following reason: ' + status);
        }
    });
}
</script>