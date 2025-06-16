<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Beneficiary</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/addUsers.css') }}">
</head>
<body>
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp

    @include('components.adminNavbar')
    @include('components.adminSidebar')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('admin.beneficiaries.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-bar-left"></i> {{ T::translate('Back', 'Bumalik')}}
                </a>
                <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">{{ T::translate('ADD BENEFICIARY', 'MAGDAGDAG NG BENEPISYARYO')}}</div>
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
                    <form action="{{ route('admin.beneficiaries.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf
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
                                        value="{{ old('first_name') }}"
                                        placeholder="{{ T::translate('Enter first name', 'Ilagay ang Pangalan')}}" 
                                        required>
                            </div>
                            <div class="col-md-3 relative">
                                <label for="middleName" class="form-label">{{ T::translate('Middle Name', 'Gitnang Pangalan')}}</label>
                                <input type="text" class="form-control" id="middleName" name="middle_name" 
                                        value="{{ old('middle_name') }}"
                                        placeholder="{{ T::translate('Enter middle name', 'Ilagay ang Gitnang Pangalan')}}">
                            </div>
                            <div class="col-md-3 relative">
                                <label for="lastName" class="form-label">{{ T::translate('Last Name', 'Apelyido')}}<label style="color:red;"> * </label></label>
                                <input type="text" class="form-control" id="lastName" name="last_name" 
                                        value="{{ old('last_name') }}"
                                        placeholder="{{ T::translate('Enter last name', 'Ilagay ang Apelyido')}}" 
                                        required >
                            </div>
                            <div class="col-md-3 relative">
                                <label for="civilStatus" class="form-label">{{ T::translate('Civil Status', 'Katayuan sa Pag-aasawa')}}<label style="color:red;"> * </label></label>
                                <select class="form-select" id="civilStatus" name="civil_status" required>
                                    <option value="" disabled {{ old('civil_status') ? '' : 'selected' }}>{{ T::translate('Select civil status', 'Pumili ng Katayuan')}}</option>
                                    <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>{{ T::translate('Single', 'Walang Asawa')}}</option>
                                    <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>{{ T::translate('Married', 'May Asawa')}}</option>
                                    <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>{{ T::translate('Widowed', 'Balo')}}</option>
                                    <option value="Divorced" {{ old('civil_status') == 'Divorced' ? 'selected' : '' }}>{{ T::translate('Divorced', 'Diborsyado')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 relative">
                                <label for="gender" class="form-label">{{ T::translate('Gender', 'Kasarian')}}<label style="color:red;"> * </label></label>
                                <select class="form-select" id="gender" name="gender" required>
                                    <option value="" disabled {{ old('gender') ? '' : 'selected' }}>{{ T::translate('Select gender', 'Pumili ng Kasarian')}}</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>{{ T::translate('Male', 'Lalaki')}}</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>{{ T::translate('Female', 'Babae')}}</option>
                                    <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>{{ T::translate('Other', 'Iba pa')}}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="birthDate" class="form-label">{{ T::translate('Birthday', 'Kaarawan')}}<label style="color:red;"> * </label></label>
                                <input type="date" class="form-control" id="birthDate" name="birth_date" value="{{ old('birth_date') }}" required onkeydown="return true">
                            </div>
                            <div class="col-md-3">
                                <label for="mobileNumber" class="form-label">Mobile Number</label>
                                <div class="input-group">
                                    <span class="input-group-text">+63</span>
                                    <input type="text" class="form-control" id="mobileNumber"  name="mobile_number" value="{{ old('mobile_number') }}" placeholder="{{ T::translate('Enter mobile number', 'Ilagay ang mobile number')}}" maxlength="11" required oninput="restrictToNumbers(this)" title="Must be 10 or 11digits.">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label for="landlineNumber" class="form-label">Landline Number</label>
                                <input type="text" class="form-control" id="landlineNumber" name="landline_number" value="{{ old('landline_number') }}" placeholder="{{ T::translate('Enter Landline number', 'Ilagay ang Landline number')}}" maxlength="10" oninput="restrictToNumbers(this)" title="Must be between 7 and 10 digits.">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 position-relative">
                                <label for="primaryCaregiver" class="form-label">{{ T::translate('Primary Caregiver', 'Pangunahing Tagapag-alaga')}}</label>
                                <input type="text" class="form-control" id="primaryCaregiver" name="primary_caregiver" value="{{ old('primary_caregiver') }}" placeholder="{{ T::translate('Enter Primary Caregiver name', 'Ilagay ang Pangunahing Tagapag-alaga')}}">                
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Row 2: Address -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">{{ T::translate('Current Address', 'Kasalukayang Tahanan')}}<label style="color:red;"> * </label></h5> 
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="addressDetails" class="form-label">{{ T::translate('House No., Street, Subdivision, Barangay, City, Province', 'Numero ng Bahay, Kalye, Subdivision, Barangay, Siyudad, Probinsya')}}<label style="color:red;"> * </label></label>
                                <textarea class="form-control" id="addressDetails" name="address_details" 
                                placeholder="{{ T::translate('Enter complete current address', 'Ilagay ang kumpletong kasalukayang tahanan')}}" 
                                rows="2" 
                                required 
                                pattern="^[a-zA-Z0-9\s,.-]+$" 
                                title="Only alphanumeric characters, spaces, commas, periods, and hyphens are allowed."
                                oninput="validateAddress(this)">{{ old('address_details') }}</textarea>
                            </div>
                            <div class="col-md-3">
                                <label for="municipality" class="form-label">{{ T::translate('Municipality', 'Munisipalidad')}}<label style="color:red;"> * </label></label>
                                <select class="form-select" id="municipality" name="municipality" required>
                                    <option value="" disabled {{ old('municipality') ? '' : 'selected' }}>{{ T::translate('Select municipality', 'Pumili ng Munisipalidad')}}</option>
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
                                    <option value="" disabled {{ old('barangay') ? '' : 'selected' }}>{{ T::translate('Select barangay', 'Pumili ng Barangay')}}</option>
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
                                <input type="hidden" id="latitude" name="latitude" value="{{ old('latitude') }}">
                                <input type="hidden" id="longitude" name="longitude" value="{{ old('longitude') }}">
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
                                <h5 class="text-start">{{ T::translate('Medical History', 'Medikal na Kasaysayan')}}</h5> 
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-3">
                                <label for="medicalConditions" class="form-label">{{ T::translate('Medical Conditions', 'Medikal na Kondiyon')}}</label>
                                <textarea class="form-control medical-history-field" id="medicalConditions" name="medical_conditions" placeholder="List all medical conditions" rows="3">{{ old('medical_conditions') }}</textarea>
                                <small class="text-muted">{{ T::translate('Separate multiple conditions with commas.', 'Paghiwalayin ang maraming kundisyon gamit ang mga kuwit.')}}</small>
                            </div>
                            <div class="col-md-3">
                                <label for="medications" class="form-label">{{ T::translate('Medications', 'Mga Gamot')}}</label>
                                <textarea class="form-control medical-history-field" id="medications" name="medications" placeholder="List all medications" rows="3">{{ old('medications') }}</textarea>
                                <small class="text-muted">{{ T::translate('Separate multiple medications with commas', 'Paghiwalayin ang maraming gamot gamit ang mga kuwit.')}}</small>
                            </div>
                            <div class="col-md-3">
                                <label for="allergies" class="form-label">{{ T::translate('Allergies', 'Alerhiya')}}</label>
                                <textarea class="form-control medical-history-field" id="allergies" name="allergies" placeholder="List all allergies" rows="3">{{ old('allergies') }}</textarea>
                                <small class="text-muted">{{ T::translate('Separate multiple allergies with commas', 'Paghiwalayin ang maraming alerhiya gamit ang mga kuwit.')}}</small>
                            </div>
                            <div class="col-md-3">
                                <label for="immunizations" class="form-label">{{ T::translate('Immunizations', 'Mga Bakuna')}}</label>
                                <textarea class="form-control medical-history-field" id="immunizations" name="immunizations" placeholder="List all immunizations" rows="3">{{ old('immunizations') }}</textarea>
                                <small class="text-muted">{{ T::translate('Separate multiple immunizations with commas', 'Paghiwalayin ang mga bakuna gamit ang mga kuwit.')}}</small>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <!-- Category Dropdown -->
                            <div class="col-md-3 position-relative">
                                <label for="category" class="form-label">{{ T::translate('Category', 'Kategorya')}}<label style="color:red;"> * </label></label>
                                <select class="form-select" id="category" name="category" required>
                                    <option value="" disabled {{old('catogory') ? '' : 'selected' }}>{{ T::translate('Select category', 'Pumili ng Kategorya')}}</option>
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
                                <h5 class="text-start">{{ T::translate('Care Needs', 'Mga Pangangailangan sa Pag-aalaga')}}<label style="color:red;"> * </label></h5>
                            </div>
                        </div>

                        <!-- Care Needs Rows -->
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">{{ T::translate('Mobility', 'Mobilidad')}}</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="mobilityFrequency" name="frequency[mobility]" placeholder="{{ T::translate('Frequency', 'Dalas')}}" rows="2">{{ old('frequency.mobility') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="mobilityAssistance" name="assistance[mobility]" placeholder="{{ T::translate('Assistance Required', 'Kinakailangang Tulong')}}" rows="2">{{ old('assistance.mobility') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">{{ T::translate('Cognitive / Communication', 'Kognitibo / Komunikasyon')}}</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="cognitiveFrequency" name="frequency[cognitive]" placeholder="{{ T::translate('Frequency', 'Dalas')}}" rows="2">{{ old('frequency.cognitive') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="cognitiveAssistance" name="assistance[cognitive]" placeholder="{{ T::translate('Assistance Required', 'Kinakailangang Tulong')}}" rows="2">{{ old('assistance.cognitive') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">{{ T::translate('Self-sustainability', 'Pagpapanatili sa Sarili')}}</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="selfSustainabilityFrequency" name="frequency[self_sustainability]" placeholder="{{ T::translate('Frequency', 'Dalas')}}" rows="2">{{ old('frequency.self_sustainability') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="selfSustainabilityAssistance" name="assistance[self_sustainability]" placeholder="{{ T::translate('Assistance Required', 'Kinakailangang Tulong')}}" rows="2">{{ old('assistance.self_sustainability') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">{{ T::translate('Disease / Therapy Handling', 'Sakit / Therapy Handling')}}</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="diseaseFrequency" name="frequency[disease]" placeholder="{{ T::translate('Frequency', 'Dalas')}}" rows="2">{{ old('frequency.disease') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="diseaseAssistance" name="assistance[disease]" placeholder="{{ T::translate('Assistance Required', 'Kinakailangang Tulong')}}" rows="2">{{ old('assistance.disease') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">{{ T::translate('Daily Life / Social Contact', 'Pang-araw-araw na Buhay / Pakikipag-ugnayan sa Lipunan')}}</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="dailyLifeFrequency" name="frequency[daily_life]" placeholder="{{ T::translate('Frequency', 'Dalas')}}" rows="2">{{ old('frequency.daily_life') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="dailyLifeAssistance" name="assistance[daily_life]" placeholder="{{ T::translate('Assistance Required', 'Kinakailangang Tulong')}}" rows="2">{{ old('assistance.daily_life') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-2 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">{{ T::translate('Outdoor Activities', 'Mga Aktibidad sa Labas')}}</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="outdoorFrequency" name="frequency[outdoor]" placeholder="{{ T::translate('Frequency', 'Dalas')}}" rows="2">{{ old('frequency.outdoor') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="outdoorAssistance" name="assistance[outdoor]" placeholder="{{ T::translate('Assistance Required', 'Kinakailangang Tulong')}}" rows="2">{{ old('assistance.outdoor') }}</textarea>
                            </div>
                        </div>
                        <div class="row mb-3 align-items-center">
                            <div class="col-md-4">
                                <label class="form-label">{{ T::translate('Household Keeping', 'Pangangalaga sa Bahay')}}</label>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="householdFrequency" name="frequency[household]" placeholder="{{ T::translate('Frequency', 'Dalas')}}" rows="2">{{ old('frequency.household') }}</textarea>
                            </div>
                            <div class="col-md-4">
                                <textarea class="form-control" id="householdAssistance" name="assistance[household]" placeholder="{{ T::translate('Assistance Required', 'Kinakailangang Tulong')}}" rows="2">{{ old('assistance.household') }}</textarea>
                            </div>
                        </div>

                        <hr class="my-4">
                       <!-- Medication Management -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">{{ T::translate('Medication Management', 'Pamamahala sa Gamot')}}</h5> 
                            </div>
                        </div>
                        <div id="medicationManagement">
                            <div class="row mb-1 align-items-center medication-row">
                                <div class="col-md-3">
                                    <input type="text" class="form-control" name="medication_name[]" placeholder="{{ T::translate('Enter Medication name', 'Ilagay ang Pangalan ng Gamot')}}" >
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="dosage[]" placeholder="{{ T::translate('Enter Dosage', 'Ilagay ang Dosis')}}" >
                                </div>
                                <div class="col-md-2">
                                    <input type="text" class="form-control" name="frequency[]" placeholder="{{ T::translate('Enter Frequency', 'Ilagay ang Dalas')}}" >
                                </div>
                                <div class="col-md-3">
                                    <textarea class="form-control" name="administration_instructions[]" placeholder="{{ T::translate('Enter Administration Instructions', 'Ilagay ang mga tagaubilin sa pangangasiwa')}}" rows="1" ></textarea>
                                </div>
                                <div class="col-md-2 d-flex text-start">
                                    <button type="button" class="btn btn-danger w-100" onclick="removeMedicationRow(this)">{{ T::translate('Delete', 'Tanggalin')}}</button>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12 text-start">
                                <button type="button" class="btn btn-primary" onclick="addMedicationRow()">{{ T::translate('Add Medication', 'Magdagdag ng Gamot')}}</button>
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Mobility, Cognitive Function, Emotional Well-being -->
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <h5 class="text-start">{{ T::translate('Mobility', '')}}</h5>
                                <div class="mb-1">
                                    <label for="walkingAbility" class="form-label">{{ T::translate('Walking Ability', 'Kakayahan sa Paglalakad')}}</label>
                                    <textarea class="form-control" id="walkingAbility" name="mobility[walking_ability]" 
                                            placeholder="{{ T::translate('Enter details about walking ability', 'Ilagay ang mga detalye sa kakayahan sa paglalakad')}}" rows="2" 
                                            pattern="^[A-Za-z0-9\s.,\-()'\"+’!?]+$"!?]+$" 
                                            title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('mobility.walking_ability') }}</textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="assistiveDevices" class="form-label">{{ T::translate('Assistive Devices', 'Kagamitang Pantulong')}}</label>
                                    <textarea class="form-control" id="assistiveDevices" name="mobility[assistive_devices]" 
                                            placeholder="{{ T::translate('Enter details about assistive devices', 'Ilagay ang mga detalye sa kagamitan pangtulong')}}" rows="2" 
                                            pattern="^[A-Za-z0-9\s.,\-()'\"+’!?]+$"!?]+$" 
                                            title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('mobility.assistive_devices') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="transportationNeeds" class="form-label">{{ T::translate('Transportation Needs', 'Pangangailangan sa Transportasyon')}}</label>
                                    <textarea class="form-control" id="transportationNeeds" name="mobility[transportation_needs]" 
                                            placeholder="{{ T::translate('Enter details about transportation needs', 'Ilagay ang mga detalye sa pangangailangan sa transportasyon')}}" rows="2" 
                                            pattern="^[A-Za-z0-9\s.,\-()'\"+’!?]+$"!?]+$" 
                                            title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('mobility.transportation_needs') }}</textarea>
                                </div>
                            </div>

                            <!-- Cognitive Function Section -->
                            <div class="col-md-4">
                                <h5 class="text-start">Cognitive Function</h5>
                                <div class="mb-1">
                                    <label for="memory" class="form-label">{{ T::translate('Memory', 'Memorya')}}</label>
                                    <textarea class="form-control" id="memory" name="cognitive[memory]" 
                                            placeholder="{{ T::translate('Enter details about memory', 'Ilagay ang detalye sa Memorya')}}" rows="2" 
                                            pattern="^[A-Za-z0-9\s.,\-()'\"+’!?]+$"!?]+$" 
                                            title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('cognitive.memory') }}</textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="thinkingSkills" class="form-label">{{ T::translate('Thinking Skills', 'Kasanayan sa Pag-iisip')}}</label>
                                    <textarea class="form-control" id="thinkingSkills" name="cognitive[thinking_skills]" 
                                            placeholder="{{ T::translate('Enter details about thinking skills', 'Ilagay ang detalye sa Kasanayan sa Pag-iisip')}}" rows="2" 
                                            pattern="^[A-Za-z0-9\s.,\-()'\"+’!?]+$"!?]+$" 
                                            title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('cognitive.thinking_skills') }}</textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="orientation" class="form-label">{{ T::translate('Orientation', 'Oryentasyon')}}</label>
                                    <textarea class="form-control" id="orientation" name="cognitive[orientation]" 
                                            placeholder="{{ T::translate('Enter details about orientation', 'Ilagay ang detalye sa Oryentasyon')}}" rows="2" 
                                            pattern="^[A-Za-z0-9\s.,\-()'\"+’!?]+$"!?]+$" 
                                            title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('cognitive.orientation') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="behavior" class="form-label">{{ T::translate('Behavior', 'Pag-uugali')}}</label>
                                    <textarea class="form-control" id="behavior" name="cognitive[behavior]" 
                                            placeholder="{{ T::translate('Enter details about behavior', 'Ilagay ang detalye sa Pag-uugali')}}" rows="2" 
                                            pattern="^[A-Za-z0-9\s.,\-()'\"+’!?]+$"!?]+$" 
                                            title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('cognitive.behavior') }}</textarea>
                                </div>
                            </div>

                            <!-- Emotional Well-being Section -->
                            <div class="col-md-4">
                                <h5 class="text-start">{{ T::translate('Emotional Well-being', 'Emosyonal na Kagalingan.')}}</h5>
                                <div class="mb-1">
                                    <label for="mood" class="form-label">{{ T::translate('Mood', 'Kalooban')}}</label>
                                    <textarea class="form-control" id="mood" name="emotional[mood]" 
                                            placeholder="Enter details about mood" rows="2" 
                                            pattern="^[A-Za-z0-9\s.,\-()'\"+’!?]+$"!?]+$" 
                                            title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('emotional.mood') }}</textarea>
                                </div>
                                <div class="mb-1">
                                    <label for="socialInteractions" class="form-label">{{ T::translate('Social Interactions', 'Pakikipag-ugnayan sa Lipunan')}}</label>
                                    <textarea class="form-control" id="socialInteractions" name="emotional[social_interactions]" 
                                            placeholder="Enter details about social interactions" rows="2" 
                                            pattern="^[A-Za-z0-9\s.,\-()'\"+’!?]+$"!?]+$" 
                                            title="Only letters, numbers, spaces, commas, periods, hyphens, and parentheses are allowed.">{{ old('emotional.social_interactions') }}</textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="emotionalSupport" class="form-label">{{ T::translate('Emotional Support Need', 'Pangangailangan ng Emosyonal na Suporta')}}</label>
                                    <textarea class="form-control" id="emotionalSupport" name="emotional[emotional_support]" 
                                            placeholder="{{ T::translate('Enter details about emotional support need', 'Ilagay aang detalye sa pangangailangan ng emosyonal na suporta')}}" rows="2" 
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
                                <label for="contactName" class="form-label">{{ T::translate('Contact Name', 'Pangalan ng Kontak')}}<label style="color:red;"> * </label></label>
                                <input type="text" class="form-control" id="contactName" name="emergency_contact[name]" 
                                    value="{{ old('emergency_contact.name') }}"
                                    placeholder="Enter contact name" 
                                    required >
                            </div>

                            <!-- Relation -->
                            <div class="col-md-3">
                                <label for="relation" class="form-label">{{ T::translate('Relation', 'Relasyon')}}<label style="color:red;"> * </label></label>
                                <select class="form-select" id="relation" name="emergency_contact[relation]" required>
                                    <option value="" disabled {{ old('emergency_contact.relation') ? '' : 'selected' }}>{{ T::translate('Select relation', 'Pumili ng Relasyon')}}</option>
                                    <option value="Parent" {{old('emergency_contact.relation') == 'Parent' ? 'selected' : '' }}>{{ T::translate('Parent', 'Magulang')}}</option>
                                    <option value="Sibling" {{old('emergency_contact.relation') == 'Sibling' ? 'selected' : '' }}>{{ T::translate('Sibling', 'Kapatid')}}</option>
                                    <option value="Spouse" {{old('emergency_contact.relation') == 'Spouse' ? 'selected' : '' }}>{{ T::translate('Spouse', 'Asawa')}}</option>
                                    <option value="Child" {{old('emergency_contact.relation') == 'Child' ? 'selected' : '' }}>{{ T::translate('Child', 'Anak')}}</option>
                                    <option value="Relative" {{old('emergency_contact.relation') == 'Relative' ? 'selected' : '' }}>{{ T::translate('Relative', 'Kamag-anak')}}</option>
                                    <option value="Friend" {{old('emergency_contact.relation') == 'Friend' ? 'selected' : '' }}>{{ T::translate('Friend', 'Kaibigan')}}</option>
                                    <option value="Other" {{old('emergency_contact.relation') == 'Other' ? 'selected' : '' }}>{{ T::translate('Other', 'Iba pa')}}</option>
                                </select>
                            </div>

                            <!-- Mobile Number -->
                            <div class="col-md-3">
                                <label for="emergencyMobileNumber" class="form-label">Mobile Number<label style="color:red;"> * </label></label>
                                <div class="input-group">
                                    <span class="input-group-text">+63</span>
                                    <input type="text" class="form-control" id="emergencyMobileNumber" name="emergency_contact[mobile]" 
                                        value="{{ old('emergency_contact.mobile') }}"
                                        placeholder="{{ T::translate('Enter mobile number', 'Ilagay ang mobile number')}}" 
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
                                    placeholder="{{ T::translate('Enter email address', 'Ilagay ang email address')}}" 
                                    >
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
                                <label for="emergencyProcedures" class="form-label">{{ T::translate('Emergency Procedures', 'Estratihiya sa Emergency')}}<label style="color:red;"> * </label></label>
                                <textarea class="form-control" id="emergencyProcedures" name="emergency_plan[procedures]" 
                                        placeholder="{{ T::translate('Enter emergency procedures', 'Ilagay ang mga estratihiya sa emergency')}}" 
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
                                <h5 class="text-start">{{ T::translate('Care Worker\'s Responsibilities', 'Mga Responsibilidad ng Tagapag-alaga')}}<label style="color:red;"> * </label></h5> 
                            </div>
                        </div>
                        <div class="row mb-1">
                            <!-- Select Care Worker -->
                            <div class="col-md-3">
                                <label for="careworkerName" class="form-label">Select Care Worker<label style="color:red;"> * </label></label>
                                <select class="form-select" id="careworkerName" name="care_worker[careworker_id]" required>
                                    <option value="" disabled {{ old('care_worker.careworker_id') ? '' : 'selected' }}>{{ T::translate('Select Care Worker', 'Pumilii ng Tagapag-alaga')}}<label style="color:red;"> * </label></option>
                                    @foreach ($careWorkers as $careWorker)
                                        <option value="{{ $careWorker->id }}" {{ old('care_worker.careworker_id') == $careWorker->id ? 'selected' : '' }}>
                                            {{ $careWorker->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Tasks and Responsibilities -->
                            <div class="col-md-5">
                                <label class="form-label">{{ T::translate('Tasks and Responsibilities', 'Mga Gawain at Responsibilidad')}}<label style="color:red;"> * </label></label>
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
                            <div class="col-md-4 d-flex flex-column align-items-start">
                                <label class="form-label">{{ T::translate('Add or Delete Task', 'Magdagdag o Mag-tanggal ng Gawain')}}</label>
                                <button type="button" class="btn btn-primary btn-sm mb-2 w-100" onclick="addTask()">{{ T::translate('Add Task', 'Magdagdag ng Gawain')}}</button>
                                <button type="button" class="btn btn-danger btn-sm w-100" onclick="removeTask()">{{ T::translate('Delete Task', 'Magtanggal ng Gawain')}}</button>
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- General Care Plan and Care Service Agreement File Upload -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">{{ T::translate('Documents and Signatures', 'Dokumento at Pirma')}}</h5> 
                            </div>
                        </div>
                        <div class="row mb-1">
                        <!-- Beneficiary Picture -->
                            <div class="col-md-3">
                                <label for="beneficiaryProfilePic" class="form-label">{{ T::translate('Upload Beneficiary Picture', 'Mag-upload ng Litrato ng Benepisyaryo')}}</label>
                                <input type="file" class="form-control" id="beneficiaryProfilePic" name="beneficiaryProfilePic" 
                                    accept="image/png, image/jpeg" 
                                    title="Only PNG and JPEG images are allowed.">
                                @if($errors->any())
                                <small class="text-danger">{{ T::translate('Note: You need to select the file again after a validation error.', 'Note: Pumili muli ng file pagkatapos nang validation error.')}}</small>
                                @endif
                                <small class="text-danger">{{ T::translate('Maximum file size: 7MB', 'Maximum na laki ng file: 7MB')}}</small>
                            </div>

                            <!-- Review Date -->
                            <div class="col-md-3">
                                <label for="datePicker" class="form-label">{{ T::translate('Review Date', 'Petsa ng Pagsusuri')}}</label>
                                <input type="date" class="form-control" id="datePicker" name="date" 
                                    value="{{ date('Y-m-d') }}" 
                                    required 
                                    max="{{ date('Y-m-d', strtotime('+1 year')) }}" 
                                    min="{{ date('Y-m-d') }}" 
                                    title="The date must be within 1 year from today.">
                            </div>

                            <!-- Care Service Agreement -->
                            <div class="col-md-3">
                                <label for="careServiceAgreement" class="form-label">{{ T::translate('Care Service Agreement', 'Kasunduan sa Serbisyo ng Pangangalaga')}}</label>
                                <input type="file" class="form-control" id="careServiceAgreement" name="care_service_agreement" 
                                    accept=".pdf,.doc,.docx" 
                                    required 
                                    title="Only PDF, DOC, and DOCX files are allowed.">
                                @if($errors->any())
                                <small class="text-danger">{{ T::translate('Note: You need to select the file again after a validation error.', 'Note: Pumili muli ng file pagkatapos nang validation error.')}}</small>
                                @endif
                                <small class="text-danger">{{ T::translate('Maximum file size: 5MB', 'Maximum na laki ng file: 5MB')}}</small>
                            </div>

                            <!-- General Careplan -->
                            <div class="col-md-3">
                                <label for="generalCareplan" class="form-label">{{ T::translate('General Careplan', 'Pangkalahatang Plano sa Pag-aalaga')}}</label>
                                <input type="file" class="form-control" id="generalCareplan" name="general_careplan" 
                                    accept=".pdf,.doc,.docx" 
                                    title="Only PDF, DOC, and DOCX files are allowed.">
                                @if($errors->any())
                                <small class="text-danger">{{ T::translate('Note: You need to select the file again after a validation error.', 'Note: Pumili muli ng file pagkatapos nang validation error.')}}</small>
                                @endif
                                <small class="text-danger">{{ T::translate('Maximum file size: 5MB', 'Maximum na laki ng file: 5MB')}}</small>
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
                                                <label>{{ T::translate('Beneficiary Signature', 'Pirma ng Benepisyaryo')}}</label>
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
                                <small class="text-danger">{{ T::translate('Note: You need to upload/sign again after a validation error.', 'Mag-upload/Pumirma muli pagkatapos ng validation error')}}</small>
                                @endif
                                </div>
                                <div class="row mb-1">
                                    <div class="col-md-12">
                                        <label for="beneficiarySignatureUpload" class="form-label">{{ T::translate('Upload Beneficiary Signature', 'Mag-upload ng Pirma nang Benepisyaryo')}}</label>
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
                                                <label>{{ T::translate('Care Worker Signature', 'Pirma ng Tagapag-alaga')}}</label>
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
                                <small class="text-danger">{{ T::translate('Note: You need to upload/sign again after a validation error.', 'Mag-upload/Pumirma muli pagkatapos ng validation error')}}</small>
                                @endif
                                </div>
                                <div class="row mb-1">
                                    <div class="col-md-12">
                                        <label for="careWorkerSignatureUpload" class="form-label">{{ T::translate('Upload Care Worker Signature', 'Mag-upload ng Pirma nang Tagapag-alaga')}}</label>
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
                                <h5 class="text-start">Login Access</h5> 
                            </div>
                        </div>
                        <div class="row mb-3">
                            <!-- Email -->
                            <div class="col-md-6">
                                <label for="generatedUsername" class="form-label">Username (Auto-generated)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" id="generatedUsername" readonly 
                                        value="{{ T::translate('Username will be generated from name fields', 'Mabubuo ang Username mula sa mga field ng Pangalan')}}" disabled>
                                    <span class="input-group-text"><i class="bi bi-info-circle" title="Username is automatically generated from your name: first initial + middle initial + last name"></i></span>
                                </div>
                                <small class="text-muted">{{ T::translate('The system will create a username based on the beneficiary\'s name.', 'Ang system ang gagawa ng username batay sa pangalan ng Benepisyaryo.')}}</small>
                            </div>

                            <!-- Password -->
                            <div class="col-md-3">
                                <label for="password" class="form-label">Password<label style="color:red;"> * </label></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="account[password]" placeholder="Enter password" required minlength="8" 
                                        title="{{ T::translate('Password must be at least 8 characters long.', 'Ang password ay dapat hindi bababa sa 8 karakter.')}}">
                                    <span class="input-group-text password-toggle" data-target="password">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-3">
                                <label for="confirmPassword" class="form-label">{{ T::translate('Confirm Password', 'Kumprimahin ang Password')}}<label style="color:red;"> * </label></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirmPassword" name="account[password_confirmation]" placeholder="{{ T::translate('Confirm password', 'Kumpirmahin ang Password')}}" required title="Passwords must match.">
                                    <span class="input-group-text password-toggle" data-target="confirmPassword">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        
                        <div class="row mt-4">
                            <div class="col-12 d-flex justify-content-center align-items-center">
                                <button type="submit" class="btn btn-success btn-lg d-flex align-items-center" id="saveBeneficiaryButton">
                                    <i class="bi bi-floppy" style="padding-right: 10px;"></i>
                                    {{ T::translate('Save Beneficiary', 'I-save ang Benepisaryo')}}
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
                    <p>{{ T::translate('Beneficiary has been successfully saved!', 'Ang Benepisyaryo ay matagumpay na nai-save!')}}</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- File Size Error Modal -->
    <div class="modal fade" id="fileSizeErrorModal" tabindex="-1" aria-labelledby="fileSizeErrorModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="fileSizeErrorModalLabel">{{ T::translate('File Size Error', 'Error sa File Size')}}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill text-danger me-3" style="font-size: 2rem;"></i>
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
                <input type="text" class="form-control" name="medication_name[]" value="${name}" placeholder="{{ T::translate('Enter medication name', 'Ilagay ang pangalan ng gamot')}}">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="dosage[]" value="${dosage}" placeholder="{{ T::translate('Enter dosage', 'Ilagay ang dosis')}}">
            </div>
            <div class="col-md-2">
                <input type="text" class="form-control" name="frequency[]" value="${freq}" placeholder="{{ T::translate('Enter frequency', 'Ilagay ang dalas')}}">
            </div>
            <div class="col-md-4">
                <textarea class="form-control" name="administration_instructions[]" placeholder="{{ T::translate('Enter administration instructions', 'Ilagay ang mga tagaubilin sa pangangasiwa')}}" rows="1">${instructions}</textarea>
            </div>
            <div class="col-md-1 d-flex text-start">
                <button type="button" class="btn btn-danger" onclick="removeMedicationRow(this)">{{ T::translate('Delete', 'Tanggalin')}}</button>
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
        // Before showing the success modal, check file sizes
        let fileSizeValid = true;
        
        document.querySelectorAll('input[type="file"]').forEach(input => {
            if (input.files.length > 0) {
                const file = input.files[0];
                const maxSize = MAX_SIZES[input.id] || 5 * 1024 * 1024;
                
                if (file.size > maxSize) {
                    e.preventDefault();
                    fileSizeValid = false;
                    
                    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(1);
                    const maxSizeMB = (maxSize / (1024 * 1024)).toFixed(1);
                    
                    // Set error message and show modal
                    fileSizeErrorMessage.innerHTML = `
                        <strong>Form submission failed</strong><br>
                        ${input.previousElementSibling.textContent} (${fileSizeMB}MB) exceeds the maximum size of ${maxSizeMB}MB.<br>
                        Please select a smaller file or compress your existing file.
                    `;
                    fileSizeErrorModal.show();
                    return;
                }
            }
        });
        
        if (fileSizeValid) {
            e.preventDefault();
            const successModal = new bootstrap.Modal(document.getElementById('saveSuccessModal'));
            successModal.show();
        }
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

    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Max sizes in bytes
        const MAX_SIZES = {
            'beneficiaryProfilePic': 7 * 1024 * 1024, // 7MB
            'careServiceAgreement': 5 * 1024 * 1024, // 5MB
            'generalCareplan': 5 * 1024 * 1024, // 5MB
            'beneficiarySignatureUpload': 2 * 1024 * 1024, // 2MB
            'careWorkerSignatureUpload': 2 * 1024 * 1024 // 2MB
        };
        
        // Initialize the modal
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
        document.querySelector('form').addEventListener('submit', function(e) {
            // Don't interfere with the success modal submission handler
            if (document.getElementById('saveSuccessModal')) {
                return;
            }
            
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