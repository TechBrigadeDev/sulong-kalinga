<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Beneficiary | Manager</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/addUsers.css') }}">
</head>
<body>
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp

    @include('components.careManagerNavbar')
    @include('components.careManagerSidebar')
    
    <div class="home-section">
        <div class="container-fluid pt-0">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <a href="{{ route('care-manager.beneficiaries.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> {{ T::translate('Back', 'Bumalik')}}
                </a>
                <div class="mx-auto text-center" style="padding: 10px; font-weight: bold; font-size: 20px;">{{ T::translate('EDIT BENEFICIARY', 'I-EDIT ANG BENEPISYARYO')}}</div>
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
                                <h5 class="text-start">{{ T::translate('Personal Details', 'Personal na Detalye')}}</h5>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-3 relative">
                                <label for="firstName" class="form-label">{{ T::translate('First Name', 'Pangalan')}}<label style="color:red;"> * </label></label>
                                <input type="text" class="form-control" id="firstName" name="first_name" 
                                        value="{{ old('first_name', $beneficiary->first_name) }}"
                                        placeholder="{{ T::translate('Enter first name', 'Ilagay ang pangalan')}}" 
                                        required >
                            </div>
                            <div class="col-md-3 relative">
                                <label for="middleName" class="form-label">{{ T::translate('Middle Name', 'Gitnang Pangalan')}}</label>
                                <input type="text" class="form-control" id="middleName" name="middle_name" 
                                        value="{{ old('middle_name', $beneficiary->middle_name) }}"
                                        placeholder="{{ T::translate('Enter middle name', 'Ilagay ang gitnang pangalan')}}">
                            </div>
                            <div class="col-md-3 relative">
                                <label for="lastName" class="form-label">{{ T::translate('Last Name', 'Apelyido')}}<label style="color:red;"> * </label></label>
                                <input type="text" class="form-control" id="lastName" name="last_name" 
                                        value="{{ old('last_name', $beneficiary->last_name) }}"
                                        placeholder="{{ T::translate('Enter last name', 'Ilagay ang apelyido')}}" 
                                        required >
                            </div>
                            <div class="col-md-3 relative">
                                <label for="civilStatus" class="form-label">{{ T::translate('Civil Status', 'Katayuan sa Pag-aasawa')}}<label style="color:red;"> * </label></label>
                                <select class="form-select" id="civilStatus" name="civil_status" required>
                                    <option value="" disabled>{{ T::translate('Select civil status', 'Pumili ng Katayuan')}}</option>
                                    <option value="Single" {{ old('civil_status', $beneficiary->civil_status) == 'Single' ? 'selected' : '' }}>{{ T::translate('Single', 'Walang Asawa')}}</option>
                                    <option value="Married" {{ old('civil_status', $beneficiary->civil_status) == 'Married' ? 'selected' : '' }}>{{ T::translate('Married', 'May Asawa')}}</option>
                                    <option value="Widowed" {{ old('civil_status', $beneficiary->civil_status) == 'Widowed' ? 'selected' : '' }}>{{ T::translate('Widowed', 'Balo')}}</option>
                                    <option value="Divorced" {{ old('civil_status', $beneficiary->civil_status) == 'Divorced' ? 'selected' : '' }}>{{ T::translate('Divorced', 'Diborsyado')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 relative">
                                <label for="gender" class="form-label">{{ T::translate('Gender', 'Kasarian')}}<label style="color:red;"> * </label></label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="" disabled>{{ T::translate('Select gender', 'Pumili ng Kasarian')}}</option>
                                    <option value="Male" {{ old('gender', $beneficiary->gender) == 'Male' ? 'selected' : '' }}>{{ T::translate('Male', 'Lalaki')}}</option>
                                    <option value="Female" {{ old('gender', $beneficiary->gender) == 'Female' ? 'selected' : '' }}>{{ T::translate('Female', 'Babae')}}</option>
                                    <option value="Other" {{ old('gender', $beneficiary->gender) == 'Other' ? 'selected' : '' }}>{{ T::translate('Other', 'Iba pa')}}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="birthDate" class="form-label">{{ T::translate('Birthday', 'Kaarawan')}}<label style="color:red;"> * </label></label>
                                <input type="date" class="form-control" id="birthDate" name="birth_date" value="{{ old('birth_date', $birth_date) }}" required onkeydown="return true">
                            </div>
                            <div class="col-md-3">
                                <label for="mobileNumber" class="form-label">{{ T::translate('Mobile Number', 'Numero sa Mobile')}}</label>
                                <div class="input-group">
                                    <span class="input-group-text">+63</span>
                                    <input type="text" class="form-control" id="mobileNumber"  name="mobile_number" value="{{ old('mobile_number', ltrim($beneficiary->mobile, '+63')) }}" placeholder="{{ T::translate('Enter mobile number', 'Ilagay ang numero ng mobile')}}" maxlength="11" required oninput="restrictToNumbers(this)" title="{{ T::translate('Must be 10 or 11 digits.', 'Dapat ay 10 o 11 digit.')}}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="landlineNumber" class="form-label">{{ T::translate('Landline Number', 'Numero sa Landline')}}</label>
                                <input type="text" class="form-control" id="landlineNumber" name="landline_number" value="{{ old('landline_number', $beneficiary->landline) }}" placeholder="{{ T::translate('Enter Landline number', 'Ilagay ang numero ng landline')}}" maxlength="10" oninput="restrictToNumbers(this)" title="{{ T::translate('Must be between 7 and 10 digits.', 'Dapat ay nasa pagitan ng 7 at 10 digit.')}}">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 position-relative">
                                <label for="primaryCaregiver" class="form-label">{{ T::translate('Primary Caregiver', 'Pangunahing Tagapag-alaga')}}</label>
                                <input type="text" class="form-control" id="primaryCaregiver" name="primary_caregiver" value="{{ old('primary_caregiver', $beneficiary->primary_caregiver) }}" placeholder="{{ T::translate('Enter Primary Caregiver name', 'Ilagay ang pangalan ng pangunahing tagapag-alaga')}}">                            
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Row 2: Address -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">{{ T::translate('Current Address', 'Kasalukuyang Tirahan')}}<label style="color:red;"> * </label></h5> 
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="addressDetails" class="form-label">{{ T::translate('House No., Street, Subdivision, Barangay, City, Province', 'Numero ng Bahay, Kalye, Subdivision, Barangay, Probinsya.')}}<label style="color:red;"> * </label></label>
                                <textarea class="form-control" id="addressDetails" name="address_details" 
                                placeholder="{{ T::translate('Enter complete current address', 'Ilagay ang kumpletong kasalukuyang tirahan')}}" 
                                rows="2" 
                                required 
                                pattern="^[a-zA-Z0-9\s,.-]+$" 
                                title="{{ T::translate('Only alphanumeric characters, spaces, commas, periods, and hyphens are allowed.', 'Tanging mga alphanumeric na karakter, espasyo, kuwit, tuldok, at gitling lamang ang pinapayagan.')}}"
                                oninput="validateAddress(this)">{{ old('address_details', $beneficiary->street_address) }}</textarea>
                            </div>
                            <div class="col-md-3">
                                <label for="municipality" class="form-label">{{ T::translate('Municipality', 'Munisipalidad')}}<label style="color:red;"> * </label></label>
                                <select class="form-select" id="municipality" name="municipality" required>
                                    <option value="" disabled>{{ T::translate('Select municipality', 'Pumili ng Munisipalidad')}}</option>
                                    @foreach ($municipalities as $municipality)
                                    <option value="{{ $municipality->municipality_id }}" 
                                            {{ old('municipality', $beneficiary->municipality_id) == $municipality->municipality_id ? 'selected' : '' }}>
                                        {{ $municipality->municipality_name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div> 
                            <div class="col-md-3">
                                <label for="barangay" class="form-label">{{ T::translate('Barangay', 'Barangay')}}<label style="color:red;"> * </label></label>
                                <select class="form-select" id="barangay" name="barangay" required>
                                    <option value="" disabled>{{ T::translate('Select barangay', 'Pumili ng Barangay')}}</option>
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
                                <h5 class="text-start">{{ T::translate('Map Location', 'Lokasyon sa Mapa')}}<label style="color:red;"> * </label></h5> 
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="mapLocation" class="form-label">{{ T::translate('Pinpoint in Google Maps', 'Tukuyin sa Google Maps')}}<label style="color:red;"> * </label></label>
                                <div id="googleMap" style="width:100%;height:400px;border:1px solid #ccc;"></div>
                                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude', $latitude) }}">
                                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude', $longitude) }}">
                                <small class="text-muted">{{ T::translate('Drag the marker to set the beneficiary\'s location.', 'I-Drag ang marker upang itakda ang lokasyon ng Benepisyaryo.')}}</small>
                            </div>
                            <div class="col-md-6">
                                <label for="searchAddress" class="form-label">{{ T::translate('Or search address', 'O hanapin ang address')}}</label>
                                <input type="text" id="searchAddress" class="form-control" placeholder="{{ T::translate('Enter address', 'Ilagay ang address')}}">
                                <button type="button" id="searchAddressBtn" class="btn btn-primary mt-2">{{ T::translate('Find on Map', 'Hanapin sa Mapa')}}</button>
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Row 3: Medical History -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">{{ T::translate('Medical History', 'Kasaysayang Medikal')}}</h5> 
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-3">
                                <label for="medicalConditions" class="form-label">{{ T::translate('Medical Conditions', 'Medikal na Kondisyon')}}</label>
                                <textarea class="form-control medical-history-field" id="medicalConditions" name="medical_conditions" placeholder="{{ T::translate('List all medical conditions', 'Ilagay ang lahat ng medikal na kondisyon')}}" rows="3">{{ old('medical_conditions', is_array($beneficiary->generalCarePlan->healthHistory->medical_conditions ?? '') ? implode(", ", json_decode($beneficiary->generalCarePlan->healthHistory->medical_conditions)) : ($beneficiary->generalCarePlan->healthHistory->medical_conditions ?? '')) }}</textarea>
                                <small class="text-muted">{{ T::translate('Separate multiple conditions with commas', 'Paghiwalayin ang maraming kundisyon gamit ang mga kuwit')}}</small>
                            </div>
                            <div class="col-md-3">
                                <label for="medications" class="form-label">{{ T::translate('Medications', 'Mga Gamot')}}</label>
                                <textarea class="form-control medical-history-field" id="medications" name="medications" placeholder="{{ T::translate('List all medications', 'Ilagay ang lahat ng gamot')}}" rows="3">{{ old('medications', is_array($beneficiary->generalCarePlan->healthHistory->medications ?? '') ? implode(", ", json_decode($beneficiary->generalCarePlan->healthHistory->medications)) : ($beneficiary->generalCarePlan->healthHistory->medications ?? '')) }}</textarea>
                                <small class="text-muted">{{ T::translate('Separate multiple medications with commas', 'Paghiwalayin ang maraming gamot gamit ang mga kuwit')}}</small>
                            </div>
                            <div class="col-md-3">
                                <label for="allergies" class="form-label">{{ T::translate('Allergies', 'Mga Alerhiya')}}</label>
                                <textarea class="form-control medical-history-field" id="allergies" name="allergies" placeholder="{{ T::translate('List all allergies', 'Ilagay ang lahat ng alerhiya')}}" rows="3">{{ old('allergies', is_array($beneficiary->generalCarePlan->healthHistory->allergies ?? '') ? implode(", ", json_decode($beneficiary->generalCarePlan->healthHistory->allergies)) : ($beneficiary->generalCarePlan->healthHistory->allergies ?? '')) }}</textarea>
                                <small class="text-muted">{{ T::translate('Separate multiple allergies with commas', 'Paghiwalayin ang maraming alerhiya gamit ang mga kuwit')}}</small>
                            </div>
                            <div class="col-md-3">
                                <label for="immunizations" class="form-label">{{ T::translate('Immunizations', 'Mga Bakuna')}}</label>
                                <textarea class="form-control medical-history-field" id="immunizations" name="immunizations" placeholder="{{ T::translate('List all immunizations', 'Ilagay ang lahat ng bakuna')}}" rows="3">{{ old('immunizations', is_array($beneficiary->generalCarePlan->healthHistory->immunizations ?? '') ? implode(", ", json_decode($beneficiary->generalCarePlan->healthHistory->immunizations)) : ($beneficiary->generalCarePlan->healthHistory->immunizations ?? '')) }}</textarea>
                                <small class="text-muted">{{ T::translate('Separate multiple immunizations with commas', 'Paghiwalayin ang maraming bakuna gamit ang mga kuwit')}}</small>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <!-- Category Dropdown -->
                            <div class="col-md-3 position-relative">
                                <label for="category" class="form-label">{{ T::translate('Category', 'Kategorya')}}<label style="color:red;"> * </label></label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="" disabled>{{ T::translate('Select category', 'Pumili ng Kategorya')}}</option>
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
                                <h5 class="text-start">{{ T::translate('Care Needs', 'Pangangaialangan sa Pangangalaga')}}<label style="color:red;"> * </label></h5>
                            </div>
                        </div>

                        <!-- Care Needs Rows -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Mobility</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="mobilityFrequency" name="frequency[mobility]" placeholder="{{ T::translate('Frequency', 'Dalas')}}" rows="2">{{ old('frequency.mobility', $careNeeds[1]['frequency'] ?? '') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="mobilityAssistance" name="assistance[mobility]" placeholder="{{ T::translate('Assistance Required', 'Kailangang Tulong')}}" rows="2">{{ old('assistance.mobility', $careNeeds[1]['assistance'] ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Cognitive / Communication</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="cognitiveFrequency" name="frequency[cognitive]" placeholder="{{ T::translate('Frequency', 'Dalas')}}" rows="2">{{ old('frequency.cognitive', $careNeeds[2]['frequency'] ?? '') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="cognitiveAssistance" name="assistance[cognitive]" placeholder="{{ T::translate('Assistance Required', 'Kailangang Tulong')}}" rows="2">{{ old('assistance.cognitive', $careNeeds[2]['assistance'] ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Self-sustainability</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="selfSustainabilityFrequency" name="frequency[self_sustainability]" placeholder="{{ T::translate('Frequency', 'Dalas')}}" rows="2">{{ old('frequency.self_sustainability', $careNeeds[3]['frequency'] ?? '') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="selfSustainabilityAssistance" name="assistance[self_sustainability]" placeholder="{{ T::translate('Assistance Required', 'Kailangang Tulong')}}" rows="2">{{ old('assistance.self_sustainability', $careNeeds[3]['assistance'] ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Disease / Therapy Handling</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="diseaseFrequency" name="frequency[disease]" placeholder="{{ T::translate('Frequency', 'Dalas')}}" rows="2">{{ old('frequency.disease', $careNeeds[4]['frequency'] ?? '') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="diseaseAssistance" name="assistance[disease]" placeholder="{{ T::translate('Assistance Required', 'Kailangang Tulong')}}" rows="2">{{ old('assistance.disease', $careNeeds[4]['assistance'] ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Daily Life / Social Contact</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="dailyLifeFrequency" name="frequency[daily_life]" placeholder="{{ T::translate('Frequency', 'Dalas')}}" rows="2">{{ old('frequency.daily_life', $careNeeds[5]['frequency'] ?? '') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="dailyLifeAssistance" name="assistance[daily_life]" placeholder="{{ T::translate('Assistance Required', 'Kailangang Tulong')}}" rows="2">{{ old('assistance.daily_life', $careNeeds[5]['assistance'] ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Outdoor Activities</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="outdoorFrequency" name="frequency[outdoor]" placeholder="{{ T::translate('Frequency', 'Dalas')}}" rows="2">{{ old('frequency.outdoor', $careNeeds[6]['frequency'] ?? '') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="outdoorAssistance" name="assistance[outdoor]" placeholder="{{ T::translate('Assistance Required', 'Kailangang Tulong')}}" rows="2">{{ old('assistance.outdoor', $careNeeds[6]['assistance'] ?? '') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">Household Keeping</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="householdFrequency" name="frequency[household]" placeholder="{{ T::translate('Frequency', 'Dalas')}}" rows="2">{{ old('frequency.household', $careNeeds[7]['frequency'] ?? '') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="householdAssistance" name="assistance[household]" placeholder="{{ T::translate('Assistance Required', 'Kailangang Tulong')}}" rows="2">{{ old('assistance.household', $careNeeds[7]['assistance'] ?? '') }}</textarea>                            </div>
                        </div>

                        <hr class="my-4">
                       <!-- Medication Management -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">{{ T::translate('Medication Management', 'Pamamahala sa Gamot')}}</h5> 
                            </div>
                        </div>
                        <div id="medicationManagement">
                            @if(old('medication_name'))
                                @foreach(old('medication_name') as $index => $name)
                                    <div class="row mb-1 align-items-center medication-row">
                                        <div class="col-md-2">
                                            <input type="text" class="form-control" name="medication_name[]" 
                                                value="{{ $name }}" placeholder="{{ T::translate('Enter Medication name', 'Ilagay ang pangalan ng gamot')}}">
                                        </div>
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" name="dosage[]" 
                                                value="{{ old('dosage.'.$index) }}" placeholder="{{ T::translate('Enter Dosage', 'Ilagay ang dosage')}}">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" class="form-control" name="frequency[]" 
                                                value="{{ old('frequency.'.$index) }}" placeholder="{{ T::translate('Enter Frequency', 'Ilagay ang dalas')}}">
                                        </div>
                                        <div class="col-md-4">
                                            <textarea class="form-control" name="administration_instructions[]" 
                                                placeholder="{{ T::translate('Enter Administration Instructions', 'Ilagay ang mga tagubilin sa pag-administra')}}" rows="1">{{ old('administration_instructions.'.$index) }}</textarea>
                                        </div>
                                        <div class="col-md-1">
                                            <button type="button" class="btn btn-danger" onclick="removeMedicationRow(this)">Delete</button>
                                        </div>
                                    </div>
                                @endforeach
                            @elseif($beneficiary->generalCarePlan && $beneficiary->generalCarePlan->medications->count() > 0)
                                @foreach($beneficiary->generalCarePlan->medications as $medication)
                                    <div class="row mb-1 align-items-center medication-row">
                                        <div class="col-md-3">
                                            <input type="text" class="form-control" name="medication_name[]" 
                                                value="{{ $medication->medication }}" placeholder="{{ T::translate('Enter Medication name', 'Ilagay ang pangalan ng gamot')}}">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" class="form-control" name="dosage[]" 
                                                value="{{ $medication->dosage }}" placeholder="{{ T::translate('Enter Dosage', 'Ilagay ang dosage')}}">
                                        </div>
                                        <div class="col-md-2">
                                            <input type="text" class="form-control" name="frequency[]" 
                                                value="{{ $medication->frequency }}" placeholder="{{ T::translate('Enter Frequency', 'Ilagay ang dalas')}}">
                                        </div>
                                        <div class="col-md-4">
                                            <textarea class="form-control" name="administration_instructions[]" 
                                                placeholder="{{ T::translate('Enter Administration Instructions', 'Ilagay ang mga tagubilin sa pag-administra')}}" rows="1">{{ $medication->administration_instructions }}</textarea>
                                        </div>
                                        <div class="col-md-1 d-flex text-start">
                                            <button type="button" class="btn btn-danger" onclick="removeMedicationRow(this)">Delete</button>
                                        </div>
                                    </div>
                                @endforeach
                            @else
                                <div class="row mb-1 align-items-center medication-row">
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="medication_name[]" placeholder="{{ T::translate('Enter Medication name', 'Ilagay ang pangalan ng gamot')}}">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control" name="dosage[]" placeholder="{{ T::translate('Enter Dosage', 'Ilagay ang dosage')}}">
                                    </div>
                                    <div class="col-md-2">
                                        <input type="text" class="form-control" name="frequency[]" placeholder="{{ T::translate('Enter Frequency', 'Ilagay ang dalas')}}">
                                    </div>
                                    <div class="col-md-4">
                                        <textarea class="form-control" name="administration_instructions[]" placeholder="{{ T::translate('Enter Administration Instructions', 'Ilagay ang mga tagubilin sa pag-administra')}}" rows="1"></textarea>
                                    </div>
                                    <div class="col-md-1 d-flex text-start">
                                        <button type="button" class="btn btn-danger" onclick="removeMedicationRow(this)">Delete</button>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 text-start">
                                <button type="button" class="btn btn-primary" onclick="addMedicationRow()"><i class="bi bi-plus-circle"></i> {{ T::translate('Add Medication', 'Magdagdag ng Gamot')}}</button>
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Mobility, Cognitive Function, Emotional Well-being -->
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <h5 class="text-start">Mobility</h5>
                                <div class="mb-1">
                                    <label for="walkingAbility" class="form-label">{{ T::translate('Walking Ability', 'Kakayahan sa Paglalakad')}}</label>
                                    <textarea class="form-control" id="walkingAbility" name="mobility[walking_ability]" 
                                        placeholder="{{ T::translate('Enter details about walking ability', 'Ilagay ang mga detalye tungkol sa kakayahan sa paglalakad')}}" rows="2" 
                                        pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$"!?]+$" 
                                        title="{{ T::translate('Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.', 'Tanging mga titik, numero, espasyo, kuwit, tuldok, gitling, at panaklong lamang ang pinapayagan.')}}">{{ old('mobility.walking_ability', $beneficiary->generalCarePlan->mobility->walking_ability ?? '') }}</textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="assistiveDevices" class="form-label">{{ T::translate('Assistive Devices', 'Kagamitang Pantulong')}}</label>
                                    <textarea class="form-control" id="assistiveDevices" name="mobility[assistive_devices]" 
                                        placeholder="{{ T::translate('Enter details about assistive devices', 'Ilagay ang mga detalye tungkol sa kagamitang pantulong')}}" rows="2" 
                                        pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$"!?]+$" 
                                        title="{{ T::translate('Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.', 'Tanging mga titik, numero, espasyo, kuwit, tuldok, gitling, at panaklong lamang ang pinapayagan.')}}">{{ old('mobility.assistive_devices', $beneficiary->generalCarePlan->mobility->assistive_devices ?? '') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="transportationNeeds" class="form-label">{{ T::translate('Transportation Needs', 'Pangangailangan sa Transportasyon')}}</label>
                                    <textarea class="form-control" id="transportationNeeds" name="mobility[transportation_needs]" 
                                        placeholder="{{ T::translate('Enter details about transportation needs', 'Ilagay ang mga detalye tungkol sa pangangailangan sa transportasyon')}}" rows="2" 
                                        pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$"!?]+$" 
                                        title="{{ T::translate('Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.', 'Tanging mga titik, numero, espasyo, kuwit, tuldok, gitling, at panaklong lamang ang pinapayagan.')}}">{{ old('mobility.transportation_needs', $beneficiary->generalCarePlan->mobility->transportation_needs ?? '') }}</textarea>
                                </div>
                            </div>

                            <!-- Cognitive Function Section -->
                            <div class="col-md-4">
                                <h5 class="text-start">Cognitive Function</h5>
                                <div class="mb-1">
                                    <label for="memory" class="form-label">{{ T::translate('Memory', 'Memorya')}}</label>
                                    <textarea class="form-control" id="memory" name="cognitive[memory]" 
                                        placeholder="{{ T::translate('Enter details about memory', 'Ilagay ang mga detalye tungkol sa memorya')}}" rows="2" 
                                        pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$"!?]+$" 
                                        title="{{ T::translate('Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.', 'Tanging mga titik, numero, espasyo, kuwit, tuldok, gitling, at panaklong lamang ang pinapayagan.')}}">{{ old('cognitive.memory', $beneficiary->generalCarePlan->cognitiveFunction->memory ?? '') }}</textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="thinkingSkills" class="form-label">{{ T::translate('Thinking Skills', 'Kasanayan sa Pag-iisip')}}</label>
                                    <textarea class="form-control" id="thinkingSkills" name="cognitive[thinking_skills]" 
                                        placeholder="{{ T::translate('Enter details about thinking skills', 'Ilagay ang mga detalye tungkol sa kasanayan sa pag-iisip')}}" rows="2" 
                                        pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$"!?]+$" 
                                        title="{{ T::translate('Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.', 'Tanging mga titik, numero, espasyo, kuwit, tuldok, gitling, at panaklong lamang ang pinapayagan.')}}">{{ old('cognitive.thinking_skills', $beneficiary->generalCarePlan->cognitiveFunction->thinking_skills ?? '') }}</textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="orientation" class="form-label">{{ T::translate('Orientation', 'Oryentasyon')}}</label>
                                    <textarea class="form-control" id="orientation" name="cognitive[orientation]" 
                                        placeholder="{{ T::translate('Enter details about orientation', 'Ilagay ang mga detalye tungkol sa oryentasyon')}}" rows="2" 
                                        pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$"!?]+$" 
                                        title="{{ T::translate('Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.', 'Tanging mga titik, numero, espasyo, kuwit, tuldok, gitling, at panaklong lamang ang pinapayagan.')}}">{{ old('cognitive.orientation', $beneficiary->generalCarePlan->cognitiveFunction->orientation ?? '') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="behavior" class="form-label">{{ T::translate('Behavior', 'Pag-uugali')}}</label>
                                    <textarea class="form-control" id="behavior" name="cognitive[behavior]" 
                                        placeholder="{{ T::translate('Enter details about behavior', 'Ilagay ang mga detalye tungkol sa pag-uugali')}}" rows="2" 
                                        pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$"!?]+$" 
                                        title="{{ T::translate('Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.', 'Tanging mga titik, numero, espasyo, kuwit, tuldok, gitling, at panaklong lamang ang pinapayagan.')}}">{{ old('cognitive.behavior', $beneficiary->generalCarePlan->cognitiveFunction->behavior ?? '') }}</textarea>
                                </div>
                            </div>

                            <!-- Emotional Well-being Section -->
                            <div class="col-md-4">
                                <h5 class="text-start">Emotional Well-being</h5>
                                <div class="mb-1">
                                    <label for="mood" class="form-label">{{ T::translate('Mood', 'Kalooban')}}</label>
                                    <textarea class="form-control" id="mood" name="emotional[mood]" 
                                        placeholder="{{ T::translate('Enter details about mood', 'Ilagay ang mga detalye tungkol sa kalooban')}}" rows="2" 
                                        pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$"!?]+$" 
                                        title="{{ T::translate('Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.', 'Tanging mga titik, numero, espasyo, kuwit, tuldok, gitling, at panaklong lamang ang pinapayagan.')}}">{{ old('emotional.mood', $beneficiary->generalCarePlan->emotionalWellbeing->mood ?? '') }}</textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="socialInteractions" class="form-label">{{ T::translate('Social Interactions', 'Pakikipag-ugnayan sa Lipunan')}}</label>
                                    <textarea class="form-control" id="socialInteractions" name="emotional[social_interactions]" 
                                        placeholder="{{ T::translate('Enter details about social interactions', 'Ilagay ang mga detalye tungkol sa pakikipag-ugnayan sa lipunan')}}" rows="2" 
                                        pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$"!?]+$" 
                                        title="{{ T::translate('Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.', 'Tanging mga titik, numero, espasyo, kuwit, tuldok, gitling, at panaklong lamang ang pinapayagan.')}}">{{ old('emotional.social_interactions', $beneficiary->generalCarePlan->emotionalWellbeing->social_interactions ?? '') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="emotionalSupport" class="form-label">{{ T::translate('Emotional Support Need', 'Pangangailangang Emosyonal na Suporta')}}</label>
                                    <textarea class="form-control" id="emotionalSupport" name="emotional[emotional_support]" 
                                        placeholder="{{ T::translate('Enter details about emotional support need', 'Ilagay ang mga detalye tungkol sa pangangailangan ng emosyonal na suporta')}}" rows="2" 
                                        pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$"!?]+$" 
                                        title="{{ T::translate('Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.', 'Tanging mga titik, numero, espasyo, kuwit, tuldok, gitling, at panaklong lamang ang pinapayagan.')}}">{{ old('emotional.emotional_support', $beneficiary->generalCarePlan->emotionalWellbeing->emotional_support_needs ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Emergency Contact -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">{{ T::translate('Emergency Contact', 'Emergency Contact')}}</h5>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <!-- Contact Name -->
                            <div class="col-md-3">
                                <label for="contactName" class="form-label">{{ T::translate('Contact Name', 'Pangalan ng Contact')}}<label style="color:red;"> * </label></label>
                                <input type="text" class="form-control" id="contactName" name="emergency_contact[name]" 
                                    value="{{ old('emergency_contact.name', $beneficiary->emergency_contact_name) }}"
                                    placeholder="{{ T::translate('Enter contact name', 'Ilagay ang pangalan ng contact')}}" 
                                    required >
                            </div>

                            <!-- Relation -->
                            <div class="col-md-3">
                                <label for="relation" class="form-label">{{ T::translate('Relation', 'Relasyon')}}<label style="color:red;"> * </label></label>
                                <select class="form-select" id="relation" name="emergency_contact[relation]" required>
                                    <option value="" disabled>{{ T::translate('Select relation', 'Pumili ng relasyon')}}</option>
                                    <option value="Parent" {{ old('emergency_contact.relation', $beneficiary->emergency_contact_relation) == 'Parent' ? 'selected' : '' }}>{{ T::translate('Parent', 'Magulang')}}</option>
                                    <option value="Sibling" {{ old('emergency_contact.relation', $beneficiary->emergency_contact_relation) == 'Sibling' ? 'selected' : '' }}>{{ T::translate('Sibling', 'Kapatid')}}</option>
                                    <option value="Spouse" {{ old('emergency_contact.relation', $beneficiary->emergency_contact_relation) == 'Spouse' ? 'selected' : '' }}>{{ T::translate('Spouse', 'Asawa')}}</option>
                                    <option value="Child" {{ old('emergency_contact.relation', $beneficiary->emergency_contact_relation) == 'Child' ? 'selected' : '' }}>{{ T::translate('Child', 'Anak')}}</option>
                                    <option value="Relative" {{ old('emergency_contact.relation', $beneficiary->emergency_contact_relation) == 'Relative' ? 'selected' : '' }}>{{ T::translate('Relative', 'Kamag-anak')}}</option>
                                    <option value="Friend" {{ old('emergency_contact.relation', $beneficiary->emergency_contact_relation) == 'Friend' ? 'selected' : '' }}>{{ T::translate('Friend', 'Kaibigan')}}</option>
                                    <option value="Other" {{ old('emergency_contact.relation', $beneficiary->emergency_contact_relation) == 'Other' ? 'selected' : '' }}>{{ T::translate('Other', 'Iba pa')}}</option>
                                </select>
                            </div>

                            <!-- Mobile Number -->
                            <div class="col-md-3">
                                <label for="emergencyMobileNumber" class="form-label">{{ T::translate('Mobile Number', 'Numero sa Mobile')}}<label style="color:red;"> * </label></label>
                                <div class="input-group">
                                    <span class="input-group-text">+63</span>
                                    <input type="text" class="form-control" id="emergencyMobileNumber" name="emergency_contact[mobile]" 
                                        value="{{ old('emergency_contact.mobile', str_replace('+63', '', $beneficiary->emergency_contact_mobile)) }}"
                                        placeholder="{{ T::translate('Enter mobile number', 'Ilagay ang numero ng mobile')}}" 
                                        maxlength="10" 
                                        required 
                                        oninput="restrictToNumbers(this)" 
                                        title="{{ T::translate('Must be 10 digits.', 'Dapat ay 10 digit.')}}">
                                </div>
                            </div>

                            <!-- Email Address -->
                            <div class="col-md-3">
                                <label for="emailAddress" class="form-label">{{ T::translate('Email Address', 'Email Address')}}</label>
                                <input type="email" class="form-control" id="emailAddress" name="emergency_contact[email]" 
                                    value="{{ old('emergency_contact.email', $beneficiary->emergency_contact_email) }}"
                                    placeholder="{{ T::translate('Enter email address', 'Ilagay ang email address')}}" 
                                    required>
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Emergency Plan -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">{{ T::translate('Emergency Plan', 'Plano sa Emergency')}}</h5> 
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <label for="emergencyProcedures" class="form-label">{{ T::translate('Emergency Procedures', 'Mga Pamamaraan sa Emergency')}}<label style="color:red;"> * </label></label>
                                <textarea class="form-control" id="emergencyProcedures" name="emergency_plan[procedures]" 
                                    placeholder="{{ T::translate('Enter emergency procedures', 'Ilagay ang mga pamamaraan sa emergency')}}" 
                                    rows="3" 
                                    required 
                                    pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$"!?]+$" 
                                    title="{{ T::translate('Only letters, numbers, spaces, commas, periods, hyphens, parentheses, single quotes, double quotes, apostrophes, and exclamation/question marks are allowed.', 'Tanging mga titik, numero, espasyo, kuwit, tuldok, gitling, panaklong, single quote, double quote, apostrophe, at tandang pananong/padamdam lamang ang pinapayagan.')}}">{{ old('emergency_plan.procedures', $beneficiary->emergency_procedure) }}</textarea>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        <!-- Care Worker's Responsibilities -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">{{ T::translate('Care Worker\'s Responsibilities', 'Mga Responsibilidad ng Tagapag-alaga')}}<label style="color:red;"> * </label></h5> 
                            </div>
                        </div>
                        <div class="row mb-1">
                            <!-- Select Care Worker -->
                            <div class="col-md-3">
                                <label for="careworkerName" class="form-label">{{ T::translate('Select Care Worker', 'Pumili ng Tagapag-alaga')}}<label style="color:red;"> * </label></label>
                                <select class="form-select" id="careworkerName" name="care_worker[careworker_id]" required>
                                    <option value="" disabled>{{ T::translate('Select Care Worker', 'Pumili ng Tagapag-alaga')}}</option>
                                    @foreach ($careWorkers as $careWorker)
                                        <option value="{{ $careWorker->id }}" {{ old('care_worker.careworker_id', $currentCareWorker) == $careWorker->id ? 'selected' : '' }}>
                                            {{ $careWorker->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tasks and Responsibilities -->
                            <div class="col-md-5">
                                <label class="form-label">{{ T::translate('Tasks and Responsibilities', 'Mga Gawain at Responsibilidad')}}<label style="color:red;"> * </label></label>
                                <div id="tasksContainer">
                                    @if(old('care_worker.tasks'))
                                        @foreach(old('care_worker.tasks') as $task)
                                            <div class="input-group mb-2">
                                                <input type="text" class="form-control" name="care_worker[tasks][]"
                                                    value="{{ $task }}" 
                                                    placeholder="{{ T::translate('Enter task or responsibility', 'Ilagay ang gawain o responsibilidad')}}" 
                                                    required 
                                                    pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$" 
                                                    title="{{ T::translate('Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.', 'Tanging mga titik, numero, espasyo, kuwit, tuldok, gitling, at panaklong lamang ang pinapayagan.')}}">
                                            </div>
                                        @endforeach
                                    @elseif(isset($currentCareWorkerTasks) && count($currentCareWorkerTasks) > 0)
                                        @foreach($currentCareWorkerTasks as $task)
                                            <div class="input-group mb-2">
                                                <input type="text" class="form-control" name="care_worker[tasks][]"
                                                    value="{{ $task }}" 
                                                    placeholder="{{ T::translate('Enter task or responsibility', 'Ilagay ang gawain o responsibilidad')}}" 
                                                    required 
                                                    pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$" 
                                                    title="{{ T::translate('Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.', 'Tanging mga titik, numero, espasyo, kuwit, tuldok, gitling, at panaklong lamang ang pinapayagan.')}}">
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="input-group mb-2">
                                            <input type="text" class="form-control" name="care_worker[tasks][]"
                                                placeholder="{{ T::translate('Enter task or responsibility', 'Ilagay ang gawain o responsibilidad')}}" 
                                                required 
                                                pattern="^[A-Za-z0-9\s.,\-()'\"+'!?]+$" 
                                                title="{{ T::translate('Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.', 'Tanging mga titik, numero, espasyo, kuwit, tuldok, gitling, at panaklong lamang ang pinapayagan.')}}">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Add/Delete Task Buttons -->
                            <div class="col-md-3 d-flex flex-column align-items-start">
                                <label class="form-label">{{ T::translate('Add or Delete Task', 'Magdagdag o Magtanggal ng Gawain')}}</label>
                                <button type="button" class="btn btn-primary btn-sm mb-2 w-100" onclick="addTask()">{{ T::translate('Add Task', 'Magdagdag ng Gawain')}}</button>
                                <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeTask()">{{ T::translate('Delete Task', 'Magtanggal ng Gawain')}}</button>
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- General Care Plan and Care Service Agreement File Upload -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">{{ T::translate('Documents and Signatures', 'Mga Dokumento at Lagda')}}</h5> 
                            </div>
                        </div>
                        <div class="row mb-1">
                        <!-- Beneficiary Picture -->
                        <div class="col-md-6">
                            <label for="beneficiaryProfilePic" class="form-label">{{ T::translate('Beneficiary Picture', 'Litrato ng Benepisyaryo')}}</label>
                            <input type="file" class="form-control" id="beneficiaryProfilePic" name="beneficiaryProfilePic" 
                                accept="image/png, image/jpeg" 
                                title="{{ T::translate('Only PNG and JPEG images are allowed.', 'Tanging PNG at JPEG na mga larawan lamang ang pinapayagan.')}}">
                                @if($beneficiary->photo)
                                    <div class="d-flex align-items-center mt-1">
                                        <i class="bi bi-file-earmark text-primary"></i>
                                        <small class="text-muted ms-1 file-name-container" title="{{ basename($beneficiary->photo) }}">
                                            {{ T::translate('Current file:', 'Kasalukuyang file:')}} {{ substr(basename($beneficiary->photo), 0, 30) }}{{ strlen(basename($beneficiary->photo)) > 30 ? '...' : '' }}
                                        </small>
                                    </div>
                                @else
                                    <small class="text-muted">{{ T::translate('No file uploaded', 'Walang file ang na-upload')}}</small>
                                @endif
                                <small class="text-danger">{{ T::translate('Maximum file size: 7MB. Please compress or split larger files.', 'Pinakamataas na laki ng file: 7MB. Mangyaring i-compress o hatiin ang mas malalaking file.')}}</small>
                        </div>

                            <!-- Review Date -->
                            <div class="col-md-6">
                                <label for="datePicker" class="form-label">{{ T::translate('Review Date', 'Petsa ng Pagsusuri')}}</label>
                                <input type="date" class="form-control" id="datePicker" name="date" 
                                    value="{{ old('date', $review_date ?? date('Y-m-d')) }}" 
                                    required 
                                    max="{{ date('Y-m-d', strtotime('+1 year')) }}" 
                                    min="{{ date('Y-m-d') }}" 
                                    title="{{ T::translate('The date must be within 1 year from today.', 'Ang petsa ay dapat nasa loob ng 1 taon mula ngayon.')}}">
                            </div>

                            <!-- Care Service Agreement -->
                            <div class="col-md-6">
                                <label for="careServiceAgreement" class="form-label">Care Service Agreement</label>
                                <input type="file" class="form-control" id="careServiceAgreement" name="care_service_agreement" 
                                    accept=".pdf,.doc,.docx" 
                                    title="{{ T::translate('Only PDF, DOC, and DOCX files are allowed.', 'Tanging PDF, DOC, at DOCX na mga file lamang ang pinapayagan.')}}">
                                    @if($beneficiary->care_service_agreement_doc)
                                        <div class="d-flex align-items-center mt-1">
                                            <i class="bi bi-file-earmark text-primary"></i>
                                            <small class="text-muted ms-1 file-name-container" title="{{ basename($beneficiary->care_service_agreement_doc) }}">
                                                {{ T::translate('Current file:', 'Kasalukuyang file:')}} {{ substr(basename($beneficiary->care_service_agreement_doc), 0, 30) }}{{ strlen(basename($beneficiary->care_service_agreement_doc)) > 30 ? '...' : '' }}
                                            </small>
                                        </div>
                                    @else
                                    <small class="text-muted">{{ T::translate('No file uploaded', 'Walang file ang na-upload')}}</small>
                                    @endif
                                    <small class="text-danger">{{ T::translate('Maximum file size: 5MB. Please compress or split larger files.', 'Pinakamataas na laki ng file: 5MB. Mangyaring i-compress o hatiin ang mas malalaking file.')}}</small>
                            </div>

                            <!-- General Careplan -->
                            <div class="col-md-6">
                                <label for="generalCareplan" class="form-label">{{ T::translate('General Careplan', 'General Careplan')}}</label>
                                <input type="file" class="form-control" id="generalCareplan" name="general_careplan" 
                                    accept=".pdf,.doc,.docx" 
                                    title="{{ T::translate('Only PDF, DOC, and DOCX files are allowed.', 'Tanging PDF, DOC, at DOCX na mga file lamang ang pinapayagan.')}}">
                                    @if($beneficiary->general_care_plan_doc)
                                        <div class="d-flex align-items-center mt-1">
                                            <i class="bi bi-file-earmark text-primary"></i>
                                            <small class="text-muted ms-1 file-name-container" title="{{ basename($beneficiary->general_care_plan_doc) }}">
                                                {{ T::translate('Current file:', 'Kasalukuyang file:')}} {{ substr(basename($beneficiary->general_care_plan_doc), 0, 30) }}{{ strlen(basename($beneficiary->general_care_plan_doc)) > 30 ? '...' : '' }}
                                            </small>
                                        </div>
                                    @else
                                    <small class="text-muted">{{ T::translate('No file uploaded', 'Walang file ang na-upload')}}</small>
                                    @endif
                                    <small class="text-danger">{{ T::translate('Maximum file size: 5MB. Please compress or split larger files.', 'Pinakamataas na laki ng file: 5MB. Mangyaring i-compress o hatiin ang mas malalaking file.')}}</small>
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
                                            <label for="beneficiarySignatureUpload" class="form-label">{{ T::translate('Upload Beneficiary Signature', 'Mag-upload ng Lagda ng Benepisyaryo')}}</label>
                                                <button type="button" id="clear-signature-1" class="btn btn-danger btn-sm">{{ T::translate('Clear', 'Burahin')}}</button>
                                            </div>
                                            <div id="signature-pad-1" class="signature-pad">
                                                <div class="signature-pad-body">
                                                    <canvas id="canvas1" style="border: 1px solid #ced4da; width: 100%; height: 200px;"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @if($errors->any())
                                <small class="text-danger">{{ T::translate('Note: The old signature is saved after a validation error. You need to enter the new one again.', 'Tandaan: Ang lumang lagda ay nai-save pagkatapos ng isang error sa validation. Kailangan mong ilagay muli ang bago.')}}</small>
                                @endif
                                </div>
                                <div class="row mb-1">
                                    <div class="col-md-12">
                                        <label for="beneficiarySignatureUpload" class="form-label">{{ T::translate('Upload Beneficiary Signature', 'Mag-Upload ng Lagda ng Benepisyaryo')}}</label>
                                        <input type="file" class="form-control" id="beneficiarySignatureUpload" name="beneficiary_signature_upload" accept="image/png, image/jpeg">
                                                @if($beneficiary->beneficiary_signature)
                                                    <div class="d-flex align-items-center mt-1">
                                                        <i class="bi bi-file-earmark text-primary"></i>
                                                        <small class="text-muted ms-1 file-name-container" title="{{ basename($beneficiary->beneficiary_signature) }}">
                                                            {{ T::translate('Current signature:', 'Kasalukuyang Lagda')}} {{ substr(basename($beneficiary->beneficiary_signature), 0, 50) }}{{ strlen(basename($beneficiary->beneficiary_signature)) > 50 ? '...' : '' }}
                                                        </small>
                                                        <small class="text-muted ms-1 file-name-container" title="{{ basename($beneficiary->beneficiary_signature) }}">
                                                            {{ T::translate('Leave blank to keep current signature', 'Iwanang blank upang panatilihin ang kasalukuyang lagda')}}
                                                        </small>
                                                    </div>
                                                    <img src="{{ asset('storage/' . $beneficiary->beneficiary_signature) }}" class="img-thumbnail signature-preview" style="max-height: 100px;" alt="Current beneficiary signature">
                                                @else
                                                    <small class="text-muted">{{ T::translate('No signature uploaded', 'Walang lagda ang na-upload')}}</small>
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
                                                <label>{{ T::translate('Care Worker Signature', 'Lagda ng Tagapag-alaga')}}</label>
                                                <button type="button" id="clear-signature-2" class="btn btn-danger btn-sm">{{ T::translate('Clear', 'Burahin')}}</button>
                                            </div>
                                            <div id="signature-pad-2" class="signature-pad">
                                                <div class="signature-pad-body">
                                                    <canvas id="canvas2" style="border: 1px solid #ced4da; width: 100%; height: 200px;"></canvas>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @if($errors->any())
                                <small class="text-danger">{{ T::translate('Note: The old signature is saved after a validation error. You need to enter the new one again.', 'Tandaan: Ang lumang lagda ay nai-save pagkatapos ng isang error sa validation. Kailangan mong ilagay muli ang bago.')}}</small>
                                @endif
                                </div>
                                <div class="row mb-1">
                                    <div class="col-md-12">
                                        <label for="careWorkerSignatureUpload" class="form-label">{{ T::translate('Upload Care Worker Signature', 'I-Upload ang Lagda ng Tagapag-alaga')}}</label>
                                        <input type="file" class="form-control" id="careWorkerSignatureUpload" name="care_worker_signature_upload" accept="image/png, image/jpeg">
                                                @if($beneficiary->care_worker_signature)
                                                    <div class="d-flex align-items-center mt-1">
                                                        <i class="bi bi-file-earmark text-primary"></i>
                                                        <small class="text-muted ms-1 file-name-container" title="{{ basename($beneficiary->care_worker_signature) }}">
                                                            {{ T::translate('Current signature:', 'Kasalukuyang Lagda')}} {{ substr(basename($beneficiary->care_worker_signature), 0, 60) }}{{ strlen(basename($beneficiary->care_worker_signature)) > 60 ? '...' : '' }}
                                                        </small>
                                                    </div>
                                                    <img src="{{ asset('storage/' . $beneficiary->care_worker_signature) }}" class="img-thumbnail signature-preview" style="max-height: 100px;" alt="Current beneficiary signature">
                                                @else
                                                    <small class="text-muted">{{ T::translate('No signature uploaded', 'Walang lagda ang na-upload')}}</small>
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
                                <h5 class="text-start">{{ T::translate('Login Access', 'Access sa Login')}}</h5> 
                            </div>
                        </div>
                        <div class="row mb-3">
                            <!-- Generated Username -->
                            <div class="col-md-4">
                                <label for="generatedUsername" class="form-label">{{ T::translate('Username', 'Username')}}</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="generatedUsername" readonly 
                                        value="{{ $beneficiary->username }}" disabled>
                                    <span class="input-group-text"><i class="bi bi-info-circle" title="{{ T::translate('Username is automatically generated from name: first initial + middle initial + last name', 'Ang username ay awtomatikong nabubuo mula sa pangalan: unang inisyal + gitnang inisyal + apelyido')}}"></i></span>
                                </div>
                                <small class="text-muted">{{ T::translate('Username will update automatically based on name changes', 'Awtomatikong mag-a-update ang username ayon sa mga pagbabago sa pangalan. ')}}</small>
                                <!-- Hidden field to pass the new username value if name fields are changed -->
                                <input type="hidden" id="updatedUsername" name="updated_username" value="{{ $beneficiary->username }}">
                            </div>

                            <!-- Password -->
                            <div class="col-md-4">
                                <label for="password" class="form-label">{{ T::translate('Password', 'Password')}}<label style="color:red;"> * </label></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="account[password]" placeholder="{{ T::translate('Leave blank to keep old password', 'Iwanang blangko upang panatilihin ang lumang password')}}" minlength="8" 
                                        title="{{ T::translate('Password must be at least 8 characters long.', 'Ang password ay dapat hindi bababa sa 8 karakter ang haba.')}}">
                                    <span class="input-group-text password-toggle" data-target="password">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-4">
                                <label for="confirmPassword" class="form-label">{{ T::translate('Confirm Password', 'Kumpirmahin ang Password')}}<label style="color:red;"> * </label></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirmPassword" name="account[password_confirmation]" placeholder="{{ T::translate('Leave blank to keep old password', 'Iwanang blangko upang panatilihin ang lumang password')}}" title="{{ T::translate('Passwords must match.', 'Dapat magtugma ang mga password.')}}">
                                    <span class="input-group-text password-toggle" data-target="confirmPassword">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        
                        <div class="row mt-4">
                            <div class="col-12 d-flex justify-content-center align-items-center">
                                <button type="submit" class="btn btn-success btn-lg d-flex align-items-center" id="saveBeneficiaryButton">
                                    <i class='bi bi-floppy me-2' style="font-size: 24px;"></i>
                                    {{ T::translate('Save Beneficiary', 'I-Save ang Benepisyaryo')}}
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
                    <h5 class="modal-title" id="saveSuccessModalLabel">{{ T::translate('Success', 'Tagumpay')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p>{{ T::translate('Beneficiary has been successfully saved!', 'Ang Benepisyaryo ay tagumpay na na-save!')}}</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">{{ T::translate('OK', 'OK')}}</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="fileSizeErrorModal" tabindex="-1" aria-labelledby="fileSizeErrorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="fileSizeErrorModalLabel">{{ T::translate('File Size Error', 'Error sa File Size')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-circle text-danger me-3" style="font-size: 2rem;"></i>
                        <p id="fileSizeErrorMessage" class="mb-0"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ T::translate('Close', 'Isara')}}</button>
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
                <input type="text" class="form-control" name="medication_name[]" value="${name}" placeholder="{{ T::translate('Enter medication name', 'Ilagay ang pangalan ng gamot')}}" required>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="dosage[]" value="${dosage}" placeholder="{{ T::translate('Enter dosage', 'Ilagay ang dosage')}}" required>
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="frequency[]" value="${freq}" placeholder="{{ T::translate('Enter frequency', 'Ilagay ang dalas')}}" required>
            </div>
            <div class="col-md-4">
                <textarea class="form-control" name="administration_instructions[]" placeholder="{{ T::translate('Enter administration instructions', 'Ilagay ang mga tagubilin sa pag-administra')}}" rows="1" required>${instructions}</textarea>
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
            input.placeholder = '{{ T::translate('Enter task or responsibility', 'Ilagay ang gawain o responsibilidad')}}';
            input.required = true;
            input.pattern = "^[A-Za-z0-9\\s.,\\-()]+$";
            input.title = "{{ T::translate('Only letters, numbers, spaces, commas, periods, and hyphens are allowed.', 'Tanging mga titik, numero, espasyo, kuwit, tuldok, at gitling lamang ang pinapayagan.')}}";

            inputGroup.appendChild(input);
            tasksContainer.appendChild(inputGroup);
        }

        function removeTask() {
            const tasksContainer = document.getElementById('tasksContainer');
            if (tasksContainer.children.length > 1) {
                tasksContainer.lastChild.remove();
            } else {
                alert('{{ T::translate('At least one task is required.', 'Hindi bababa sa isang gawain ang kinakailangan.')}}');
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
            defaultOption.textContent = "{{ T::translate('Select barangay', 'Pumili ng Barangay')}}";
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
                option.textContent = "{{ T::translate('No barangays available for this municipality', 'Walang mga barangay na available para sa munisipalidad na ito')}}";
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
                    confirmPassword.setCustomValidity("{{ T::translate('Passwords do not match.', 'Hindi nagtutugma ang mga password.')}}");
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
                            <strong>${fieldLabel}</strong> {{ T::translate('file is too large', 'masyadong malaki ang file')}} (${fileSizeMB}MB).<br>
                            {{ T::translate('Maximum allowed size is', 'Ang maximum na pinapayagang laki ay')}} ${maxSizeMB}MB.<br>
                            {{ T::translate('Please select a smaller file or compress your existing file.', 'Mangyaring pumili ng mas maliit na file o i-compress ang iyong umiiral na file.')}}
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
                            <strong>{{ T::translate('Form submission failed', 'Nabigo ang pag-sumite ng form')}}</strong><br>
                            ${fieldLabel} (${fileSizeMB}MB) {{ T::translate('exceeds the maximum size of', 'lumampas sa maximum na laki na')}} ${maxSizeMB}MB.<br>
                            {{ T::translate('Please select a smaller file or compress your existing file.', 'Mangyaring pumili ng mas maliit na file o i-compress ang iyong umiiral na file.')}}
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
                    usernamePreview.value = "{{ T::translate('Username will be generated from name fields', 'Mabubuo ang username mula sa mga field ng pangalan')}}";
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
                    confirmPassword.setCustomValidity("{{ T::translate('Passwords do not match.', 'Hindi nagtutugma ang mga password.')}}");
                } else {
                    confirmPassword.setCustomValidity("");
                }
            });
            
            // Also update when password changes
            password.addEventListener("input", function() {
                if (confirmPassword.value && confirmPassword.value !== password.value) {
                    confirmPassword.setCustomValidity("{{ T::translate('Passwords do not match.', 'Hindi nagtutugma ang mga password.')}}");
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
            alert('{{ T::translate('Geocode was not successful for the following reason:', 'Hindi matagumpay ang geocode sa sumusunod na dahilan:')}} ' + status);
        }
    });
}
</script>