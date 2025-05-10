<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports Management</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href='https://unpkg.com/boxicons@2.0.7/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="{{ asset('css/weeklyCareplan.css') }}">

    <style>
        .breadcrumb-container {
            overflow-x: auto;
            white-space: nowrap;
            scrollbar-width: thin;
            -ms-overflow-style: none;
            margin-bottom: 20px;
            padding-bottom: 5px;
        }
        
        .breadcrumb-container::-webkit-scrollbar {
            height: 5px;
        }
        
        .breadcrumb-container::-webkit-scrollbar-thumb {
            background-color: rgba(0,0,0,0.2);
            border-radius: 5px;
        }
        
        .breadcrumb {
            flex-wrap: nowrap;
            margin-bottom: 0;
        }
        
        .breadcrumb-item {
            float: none;
            display: inline-block;
        }
        
        .breadcrumb-item.active a {
            font-weight: bold;
            color: #0d6efd;
            padding: 5px 10px;
            background-color: rgba(13, 110, 253, 0.1);
            border-radius: 5px;
        }
        
        .form-page {
            display: none;
        }
        
        .form-page.active {
            display: block;
        }
    </style>
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')

    <div class="home-section">
        <h4 class="text-center mt-2">WEEKLY CARE PLAN FORM</h4>
        
            <div class="container-fluid">
            <div id="success-message" class="alert alert-success alert-dismissible fade show mx-3" style="display:none;">
                <strong>Success!</strong> Weekly care plan has been successfully edited.
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
                <div class="row mb-1" id="weeklyCareplanForm">
                    <div class="col-12">
                        <div class="row">
                            <div class="col-12">
                                <!-- Breadcrumb Navigation -->
                                <div class="breadcrumb-container">
                                    <nav aria-label="breadcrumb">
                                        <ol class="breadcrumb">
                                            <li class="breadcrumb-item active" data-page="1"><a href="javascript:void(0)">Personal Details</a></li>
                                            <li class="breadcrumb-item" data-page="2"><a href="javascript:void(0)">Mobility</a></li>
                                            <li class="breadcrumb-item" data-page="3"><a href="javascript:void(0)">Cognitive/Communication</a></li>
                                            <li class="breadcrumb-item" data-page="4"><a href="javascript:void(0)">Self-Sustainability</a></li>
                                            <li class="breadcrumb-item" data-page="5"><a href="javascript:void(0)">Disease/Therapy</a></li>
                                            <li class="breadcrumb-item" data-page="6"><a href="javascript:void(0)">Social Contact</a></li>
                                            <li class="breadcrumb-item" data-page="7"><a href="javascript:void(0)">Outdoor Activities</a></li>
                                            <li class="breadcrumb-item" data-page="8"><a href="javascript:void(0)">Household Keeping</a></li>
                                            <li class="breadcrumb-item" data-page="9"><a href="javascript:void(0)">Evaluation</a></li>
                                        </ol>
                                    </nav>
                                </div>
                            </div>
                        </div>
                        
                        <form id="weeklyCarePlanForm" action="{{ route('admin.weeklycareplans.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-12">
                                    <!-- Multi-Paged Form -->
                                    <div id="multiPageForm">
                                        <!-- Page 1: Personal Details and Assessment, Vital Signs -->
                                        <div class="form-page active" id="page1">
                                        <div class="validation-error-container alert alert-danger mb-3" style="display: none;"></div>
                                            <div class="row mb-1">
                                                <div class="col-12">
                                                    <h5>Personal Details</h5>
                                                </div>
                                            </div>
                                            <div class="row mb-1">
                                                <div class="col-md-4 col-sm-9 position-relative">
                                                    <label for="beneficiary_id" class="form-label">Select Beneficiary</label>
                                                    <select class="form-select" id="beneficiary_id" name="beneficiary_id">
                                                            <option value="">-- Select Beneficiary --</option>
                                                            @foreach($beneficiaries as $beneficiary)
                                                                <option value="{{ $beneficiary->beneficiary_id }}">
                                                                    {{ $beneficiary->last_name }}, {{ $beneficiary->first_name }} {{ $beneficiary->middle_name }}
                                                                </option>
                                                            @endforeach
                                                    </select>
                                                </div>
                                                <div class="col-md-2 col-sm-3">
                                                    <label for="age" class="form-label">Age</label>
                                                    <input type="text" class="form-control" id="age" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan">                                            </div>
                                                <div class="col-md-3 col-sm-6">
                                                    <label for="birthDate" class="form-label">Birthdate</label>
                                                    <input type="date" class="form-control" id="birthDate" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan">                                            </div>
                                                <div class="col-md-3 col-sm-6 position-relative">
                                                    <label for="gender" class="form-label">Gender</label>
                                                    <input type="text" class="form-control" id="gender" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan">
                                                </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-3 col-sm-4 position-relative">
                                                    <label for="civilStatus" class="form-label">Civil Status</label>
                                                    <input type="text" class="form-control" id="civilStatus" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan">
                                                    </div>
                                                <div class="col-md-9 col-sm-8">
                                                    <label for="address" class="form-label">Address</label>
                                                    <input type="text" class="form-control" id="address" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan">
                                                    </div>
                                            </div>
                                            <div class="row mb-3">
                                                <div class="col-md-6 col-sm-12">
                                                    <label for="condition" class="form-label">Medical Conditions</label>
                                                    <input type="text" class="form-control" id="medicalConditions" readonly data-bs-toggle="tooltip" title="Edit in General Care Plan">
                                                </div>
                                                <div class="col-md-6 col-sm-12">
                                                    <label for="illness" class="form-label">Illness</label>
                                                    <input type="text" class="form-control" id="illness" name="illness" 
                                                        placeholder="e.g. Cough, Fever (separate multiple illnesses with commas)" 
                                                        value="{{ old('illness') }}">
                                                    <small class="form-text text-muted">Leave blank if no illness recorded. Separate multiple illnesses with commas.</small>
                                                </div>
                                            </div>
                                            <hr my-4>
                                            <!-- Assessment, Vital Signs -->
                                            <div class="row mb-3 mt-2">
                                                <div class="col-lg-6 col-md-6 col-sm-12 text-center">
                                                    <label for="assessment" class="form-label"><h5>Assessment</h5></label>
                                                    <textarea class="form-control @error('assessment') is-invalid @enderror" 
                                                            id="assessment" name="assessment" rows="5">{{ old('assessment') }}</textarea>
                                                    @error('assessment')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12">
                                                    <h5>Vital Signs</h5>
                                                    <div class="row">
                                                        <div class="col-md-6 col-sm-6">
                                                            <label for="blood_pressure" class="form-label">Blood Pressure (mmHg)</label>
                                                            <input type="text" class="form-control @error('blood_pressure') is-invalid @enderror" 
                                                                id="blood_pressure" name="blood_pressure"
                                                                placeholder="e.g. 120/80" value="{{ old('blood_pressure') }}"
                                                                pattern="^\d{2,3}\/\d{2,3}$"
                                                                title="Format: systolic/diastolic (e.g. 120/80)">
                                                            <small class="form-text text-muted">Format: systolic/diastolic (e.g. 120/80)</small>
                                                            @error('blood_pressure')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6 col-sm-6 position-relative">
                                                            <label for="body_temperature" class="form-label">Body Temperature (°C)</label>
                                                            <input type="number" class="form-control @error('body_temperature') is-invalid @enderror" 
                                                                id="body_temperature" name="body_temperature" 
                                                                placeholder="e.g. 36.5" value="{{ old('body_temperature') }}"
                                                                min="35" max="42" step="0.1">
                                                            <small class="form-text text-muted">Enter a number between 35-42°C</small>
                                                            @error('body_temperature')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                            </div>
                                                    </div>
                                                    <div class="row">
                                                        <div class="col-md-6 col-sm-6">
                                                            <label for="pulse_rate" class="form-label">Pulse Rate (bpm)</label>
                                                            <input type="number" class="form-control @error('pulse_rate') is-invalid @enderror" 
                                                                id="pulse_rate" name="pulse_rate"
                                                                placeholder="e.g. 72" value="{{ old('pulse_rate') }}"
                                                                min="40" max="200" step="1">
                                                            <small class="form-text text-muted">Enter a whole number (beats per minute)</small>
                                                            @error('pulse_rate')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                        <div class="col-md-6 col-sm-6 position-relative">
                                                            <label for="respiratory_rate" class="form-label">Respiratory Rate (bpm)</label>
                                                            <input type="number" class="form-control @error('respiratory_rate') is-invalid @enderror" 
                                                                id="respiratory_rate" name="respiratory_rate"
                                                                placeholder="e.g. 16" value="{{ old('respiratory_rate') }}"
                                                                min="8" max="40" step="1">
                                                            <small class="form-text text-muted">Enter a whole number (breaths per minute)</small>
                                                            @error('respiratory_rate')
                                                                <div class="invalid-feedback">{{ $message }}</div>
                                                            @enderror
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="row mt-4">
                                                <div class="col-12 d-flex justify-content-end">
                                                <button type="button" class="btn btn-primary" onclick="goToPage(2)">Next <i class="fa fa-arrow-right"></i></button>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- PAGE 2-8: Interventions by Category -->
                                    @foreach($careCategories as $index => $category)
                                        <div class="form-page" id="page{{ $index + 2 }}" 
                                            style="{{ ($index + 2) > 8 ? 'display:none;' : '' }}">
                                            <div class="validation-error-container alert alert-danger mb-3" style="display: none;"></div>
                                            <div class="card mb-4">
                                                <div class="card-header">
                                                    <h5>{{ $category->care_category_name }}</h5>
                                                </div>
                                                <div class="card-body">
                                                    @foreach($category->interventions as $intervention)
                                                        <div class="row intervention-row">
                                                            <div class="col-md-8">
                                                                <div class="form-check">
                                                                    <input class="form-check-input intervention-checkbox" 
                                                                        type="checkbox" 
                                                                        name="selected_interventions[]" 
                                                                        id="intervention_{{ $intervention->intervention_id }}"
                                                                        value="{{ $intervention->intervention_id }}">
                                                                    <label class="form-check-label" for="intervention_{{ $intervention->intervention_id }}">
                                                                        {{ $intervention->intervention_description }}
                                                                    </label>
                                                                </div>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <div class="input-group duration-input">
                                                                    <input type="number" 
                                                                        class="form-control intervention-duration" 
                                                                        name="duration_minutes[{{ $intervention->intervention_id }}]" 
                                                                        placeholder="Minutes" 
                                                                        min="0.01" 
                                                                        max="999.99" 
                                                                        step="0.1"
                                                                        disabled>
                                                                    <span class="input-group-text">min</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                    
                                                    <!-- Custom intervention for this category -->
                                                    <div class="mt-4">
                                                        <h6>Add Custom {{ $category->care_category_name }} Intervention</h6>
                                                        <div class="custom-intervention-container" data-category="{{ $category->care_category_id }}">
                                                            <!-- Custom interventions will be added here -->
                                                        </div>
                                                        <button type="button" class="btn btn-sm btn-outline-primary add-custom-intervention"
                                                                data-category="{{ $category->care_category_id }}">
                                                            <i class="fa fa-plus"></i> Add Custom Intervention
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="d-flex justify-content-between">
                                                <button type="button" class="btn btn-secondary" onclick="goToPage({{ $index + 1 }})">
                                                    <i class="fa fa-arrow-left"></i> Previous
                                                </button>
                                                <button type="button" class="btn btn-primary" onclick="goToPage({{ $index + 3 }})">
                                                    Next <i class="fa fa-arrow-right"></i>
                                                </button>
                                            </div>
                                        </div>
                                    @endforeach                       

                                        <!-- Page 9: Evaluation and Submit -->
                                        <div class="form-page" id="page9">
                                        <div class="validation-error-container alert alert-danger mb-3" style="display: none;"></div>
                                            <div class="row mb-3 mt-2 justify-content-center">
                                                    <div class="col-lg-6 col-md-6 col-sm-12 text-center">
                                                        <div class="row mb-3">
                                                            <div class="col-md-12 col-sm-12">
                                                                <label for="upload_picture" class="form-label">Upload Picture <span class="text-danger">*</span></label>
                                                                <input type="file" class="form-control @error('upload_picture') is-invalid @enderror" 
                                                                    id="upload_picture" name="upload_picture" 
                                                                    accept="image/*" onchange="previewImage(event)" required>
                                                                <small class="form-text text-muted">Use your camera or upload an image for validation purposes. Required.</small>
                                                                @error('upload_picture')
                                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                                @enderror
                                                            </div>
                                                            <div class="col-md-12 col-sm-12 text-center mt-2">
                                                                <label class="form-label">Picture Preview</label>
                                                                <div class="border p-2 d-flex justify-content-center align-items-center" style="height: 200px;">
                                                                    <img id="picture_preview" src="#" alt="Preview" class="img-fluid" style="max-height: 100%; display: none;">
                                                                    <span id="no_preview_text">No image selected</span>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                <div class="col-lg-6 col-md-6 col-sm-12 text-center">
                                                    <label for="evaluation_recommendations" class="form-label"><h5>Recommendations and Evaluations</h5></label>
                                                    <textarea class="form-control @error('evaluation_recommendations') is-invalid @enderror" 
                                                            id="evaluation_recommendations" name="evaluation_recommendations" 
                                                            rows="6" >{{ old('evaluation_recommendations') }}</textarea>
                                                    @error('evaluation_recommendations')
                                                        <div class="invalid-feedback">{{ $message }}</div>
                                                    @enderror
                                                </div>
                                            </div>

                                            <div class="row mt-4">
                                                <div class="col-12 d-flex justify-content-between align-items-center">
                                                    <button type="button" class="btn btn-secondary" onclick="goToPage(8)">
                                                        <i class="fa fa-arrow-left"></i> Previous
                                                    </button>
                                                    <button type="submit" class="btn btn-success">
                                                        <i class="fa fa-save"></i> Save Weekly Care Plan
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Template for custom intervention row -->
    <template id="custom-intervention-template">
        <div class="row custom-intervention-row mb-2">
            <div class="col-md-8">
                <input type="text" class="form-control" name="custom_description[]" placeholder="Describe intervention">
                <input type="hidden" name="custom_category[]" value="">
            </div>
            <div class="col-md-3">
                <div class="input-group">
                    <input type="number" class="form-control" name="custom_duration[]" placeholder="Minutes" min="0.01" max="999.99" step="0.01">
                    <span class="input-group-text">min</span>
                </div>
            </div>
            <div class="col-md-1">
                <button type="button" class="btn btn-sm btn-danger remove-custom-row">
                    <i class="fa fa-times"></i>
                </button>
            </div>
        </div>
    </template>

    <!-- Client Side Validation -->

    <script>
    // Client-side validation for vital signs
    document.addEventListener('DOMContentLoaded', function() {
        // Blood pressure validation
        const bpInput = document.getElementById('blood_pressure');
        bpInput.addEventListener('input', function() {
            const bpRegex = /^\d{2,3}\/\d{2,3}$/;
            if (!bpRegex.test(this.value) && this.value !== '') {
                this.classList.add('is-invalid');
                if (!this.nextElementSibling.classList.contains('invalid-feedback')) {
                    const feedback = document.createElement('div');
                    feedback.classList.add('invalid-feedback');
                    feedback.textContent = 'Blood pressure must be in format 120/80';
                    this.parentNode.insertBefore(feedback, this.nextSibling);
                }
            } else {
                this.classList.remove('is-invalid');
                const feedback = this.nextElementSibling;
                if (feedback && feedback.classList.contains('invalid-feedback')) {
                    feedback.remove();
                }
            }
        });
        
        // Form submission validation for vital signs
        document.getElementById('weeklyCarePlanForm').addEventListener('submit', function(e) {
            // Existing validation code...
            
            // Additional validation for vital signs
            const bodyTemp = document.getElementById('body_temperature').value;
            const pulseRate = document.getElementById('pulse_rate').value;
            const respRate = document.getElementById('respiratory_rate').value;
            const bloodPressure = document.getElementById('blood_pressure').value;
            
            // Check if blood pressure is in the correct format
            const bpRegex = /^\d{2,3}\/\d{2,3}$/;
            if (!bpRegex.test(bloodPressure)) {
                e.preventDefault();
                showValidationError(1,'Blood pressure must be in format 120/80');
                goToPage(1); // Go back to the vital signs page
                return;
            }
            
            // Check if numeric values are within realistic ranges
            if (bodyTemp < 35 || bodyTemp > 42) {
                e.preventDefault();
                showValidationError(1,'Body temperature must be between 35°C and 42°C');
                goToPage(1);
                return;
            }
            
            if (pulseRate < 40 || pulseRate > 200) {
                e.preventDefault();
                showValidationError(1,'Pulse rate must be between 40 and 200 bpm');
                goToPage(1);
                return;
            }
            
            if (respRate < 8 || respRate > 40) {
                e.preventDefault();
                showValidationError(1,'Respiratory rate must be between 8 and 40 bpm');
                goToPage(1);
                return;
            }
        });
    });
    </script>

    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    
    <script>
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('picture_preview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();

                reader.onload = function (e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block'; // Show the preview
                };

                reader.readAsDataURL(input.files[0]); // Read the file as a data URL
            } else {
                preview.src = '#';
                preview.style.display = 'none'; // Hide the preview if no file is selected
            }
        }
    </script>
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
        
        // Form navigation
        function goToPage(pageNumber) {
            // Hide all pages
            document.querySelectorAll('.form-page').forEach(page => {
                page.classList.remove('active');
            });
            
            // Show the selected page
            document.getElementById('page' + pageNumber).classList.add('active');
            
            // Update breadcrumb active state
            document.querySelectorAll('.breadcrumb-item').forEach(item => {
                item.classList.remove('active');
                if (item.dataset.page == pageNumber) {
                    item.classList.add('active');
                    
                    // Scroll the active breadcrumb item into view
                    setTimeout(() => {
                        const breadcrumbContainer = document.querySelector('.breadcrumb-container');
                        const activeItem = item;
                        
                        // Calculate scroll position to center the active item
                        const containerWidth = breadcrumbContainer.offsetWidth;
                        const itemLeft = activeItem.offsetLeft;
                        const itemWidth = activeItem.offsetWidth;
                        const scrollPosition = itemLeft - (containerWidth / 2) + (itemWidth / 2);
                        
                        // Smooth scroll to the position
                        breadcrumbContainer.scrollTo({
                            left: Math.max(0, scrollPosition),
                            behavior: 'smooth'
                        });
                    }, 100);
                }
            });
            
            // Scroll to top of content
            window.scrollTo(0, 0);
        }
        
        // Breadcrumb navigation
        document.querySelectorAll('.breadcrumb-item').forEach(item => {
            item.addEventListener('click', function() {
                goToPage(this.dataset.page);
            });
        });
        
        document.getElementById('beneficiary_id').addEventListener('change', function() {
        const beneficiaryId = this.value;
        
        if (!beneficiaryId) {
            // Clear all fields if no beneficiary selected
            document.getElementById('age').value = '';
            document.getElementById('birthDate').value = '';
            document.getElementById('gender').value = '';
            document.getElementById('civilStatus').value = '';
            document.getElementById('address').value = '';
            document.getElementById('medicalConditions').value = '';
            return;
        }
        
        // Fetch beneficiary details via AJAX
        fetch(`{{ route('admin.weeklycareplans.beneficiaryDetails', ['id' => ':id']) }}`.replace(':id', beneficiaryId))
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    const beneficiary = data.data;
                    
                    // Calculate age
                    const birthDate = new Date(beneficiary.birthday);
                    const today = new Date();
                    let age = today.getFullYear() - birthDate.getFullYear();
                    const monthDiff = today.getMonth() - birthDate.getMonth();
                    if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
                        age--;
                    }
                    
                    // Populate form fields
                    document.getElementById('age').value = age;
                    document.getElementById('birthDate').value = beneficiary.birthday;
                    document.getElementById('gender').value = beneficiary.gender;
                    document.getElementById('civilStatus').value = beneficiary.civil_status;
                    document.getElementById('address').value = beneficiary.street_address;
                    
                    let medicalConditions = 'No medical conditions recorded';

                    if (beneficiary.general_care_plan && beneficiary.general_care_plan.health_history) {
                        const medicalConditionsData = beneficiary.general_care_plan.health_history.medical_conditions || 'None';
                        
                        // Format as comma-separated list if it's JSON
                        try {
                            const conditionsArray = JSON.parse(medicalConditionsData);
                            if (Array.isArray(conditionsArray)) {
                                medicalConditions = conditionsArray.join(', ');
                            } else {
                                medicalConditions = medicalConditionsData;
                            }
                        } catch (e) {
                            // If not valid JSON, use as is
                            medicalConditions = medicalConditionsData;
                        }
                    }

                    document.getElementById('medicalConditions').value = medicalConditions;
                } else {
                    alert('Failed to load beneficiary details');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while fetching beneficiary data');
            });
    });
        
        // Handle intervention checkboxes
        document.querySelectorAll('.intervention-checkbox').forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const durationInput = this.closest('.row').querySelector('.intervention-duration');
                
                if (this.checked) {
                    durationInput.disabled = false;
                    durationInput.required = true;
                } else {
                    durationInput.disabled = true;
                    durationInput.required = false;
                    durationInput.value = '';
                }
            });
        });
        
        // Add custom intervention
        document.querySelectorAll('.add-custom-intervention').forEach(button => {
            button.addEventListener('click', function() {
                const categoryId = this.dataset.category;
                const container = this.previousElementSibling;
                
                // Clone the template
                const template = document.getElementById('custom-intervention-template');
                const clone = document.importNode(template.content, true);
                
                // Set the category ID in the hidden input
                clone.querySelector('input[name="custom_category[]"]').value = categoryId;
                
                // Add remove button functionality
                clone.querySelector('.remove-custom-row').addEventListener('click', function() {
                    this.closest('.custom-intervention-row').remove();
                });
                
                // Add to container
                container.appendChild(clone);
            });
        });
        
        // Form validation before submission - COMPLETE REVISION
        document.getElementById('weeklyCarePlanForm').addEventListener('submit', function(e) {
            // Prevent default submission initially to run our validations
            e.preventDefault();
            
            // Fix hidden fields and disabled fields first
            document.querySelectorAll('.form-page:not(.active) [required]').forEach(field => {
                field.removeAttribute('required');
            });
            
            document.querySelectorAll('.intervention-checkbox:checked').forEach(checkbox => {
                const durationInput = checkbox.closest('.row').querySelector('.intervention-duration');
                if (durationInput) durationInput.disabled = false;
            });
            
            // Initialize validation variables
            let errors = [];
            let isValid = true;
            let pageToShow = 1;
            
            // 1. Check if beneficiary is selected
            if (!document.getElementById('beneficiary_id').value) {
                errors.push('Please select a beneficiary');
                isValid = false;
                pageToShow = 1;
            }
            
            // 2. Check assessment - minimum 20 characters with meaningful content
            const assessment = document.getElementById('assessment').value.trim();
            if (!assessment) {
                errors.push('Please provide an assessment');
                isValid = false;
                pageToShow = 1;
            } else if (assessment.length < 20) {
                errors.push('Assessment must be at least 20 characters');
                isValid = false;
                pageToShow = 1;
            } else if (!/[a-zA-Z]/.test(assessment)) {
                errors.push('Assessment must contain text and cannot consist of only numbers or symbols');
                isValid = false;
                pageToShow = 1;
            }
            
            // 3. Check vital signs
            const bodyTemp = document.getElementById('body_temperature').value;
            const pulseRate = document.getElementById('pulse_rate').value;
            const respRate = document.getElementById('respiratory_rate').value;
            const bloodPressure = document.getElementById('blood_pressure').value;
            
            // Validate blood pressure
            if (!bloodPressure) {
                errors.push('Blood pressure is required');
                isValid = false;
                pageToShow = 1;
            } else {
                const bpRegex = /^\d{2,3}\/\d{2,3}$/;
                if (!bpRegex.test(bloodPressure)) {
                    errors.push('Blood pressure must be in format 120/80');
                    isValid = false;
                    pageToShow = 1;
                }
            }
            
            // Validate body temperature
            if (!bodyTemp) {
                errors.push('Body temperature is required');
                isValid = false;
                pageToShow = 1;
            } else if (bodyTemp < 35 || bodyTemp > 42) {
                errors.push('Body temperature must be between 35°C and 42°C');
                isValid = false;
                pageToShow = 1;
            }
            
            // Validate pulse rate
            if (!pulseRate) {
                errors.push('Pulse rate is required');
                isValid = false;
                pageToShow = 1;
            } else if (pulseRate < 40 || pulseRate > 200) {
                errors.push('Pulse rate must be between 40 and 200 bpm');
                isValid = false;
                pageToShow = 1;
            }
            
            // Validate respiratory rate
            if (!respRate) {
                errors.push('Respiratory rate is required');
                isValid = false;
                pageToShow = 1;
            } else if (respRate < 8 || respRate > 40) {
                errors.push('Respiratory rate must be between 8 and 40 bpm');
                isValid = false;
                pageToShow = 1;
            }
            
            // 4. Check if at least one intervention is selected
            const checkedInterventions = document.querySelectorAll('.intervention-checkbox:checked');
            let hasCustomInterventions = false;
            
            // Check if custom interventions are filled
            document.querySelectorAll('input[name="custom_description[]"]').forEach(input => {
                if (input.value.trim() !== '') {
                    hasCustomInterventions = true;
                }
            });
            
            if (checkedInterventions.length === 0 && !hasCustomInterventions) {
                errors.push('Please select at least one intervention');
                isValid = false;
                pageToShow = 2;
            }
            
            // 5. Check if all selected interventions have valid durations
            checkedInterventions.forEach(checkbox => {
                const durationInput = checkbox.closest('.row').querySelector('.intervention-duration');
                if (!durationInput.value) {
                    errors.push('Please enter duration for all selected interventions');
                    isValid = false;
                    
                    // Find which page this intervention is on
                    const page = checkbox.closest('.form-page');
                    if (page && parseInt(page.id.replace('page', '')) > pageToShow) {
                        pageToShow = parseInt(page.id.replace('page', ''));
                    }
                } else if (parseFloat(durationInput.value) <= 0) {
                    errors.push('Duration must be greater than 0');
                    isValid = false;
                    
                    const page = checkbox.closest('.form-page');
                    if (page && parseInt(page.id.replace('page', '')) > pageToShow) {
                        pageToShow = parseInt(page.id.replace('page', ''));
                    }
                }
            });
            
            // 6. Validate custom interventions
            document.querySelectorAll('.custom-intervention-row').forEach(row => {
                const descriptionInput = row.querySelector('input[name="custom_description[]"]');
                const durationInput = row.querySelector('input[name="custom_duration[]"]');
                
                if (descriptionInput && descriptionInput.value.trim() !== '') {
                    // Description is provided, check if it meets requirements
                    if (descriptionInput.value.trim().length < 5) {
                        errors.push('Custom intervention description must be at least 5 characters');
                        isValid = false;
                        
                        const page = row.closest('.form-page');
                        if (page) {
                            const pageNum = parseInt(page.id.replace('page', ''));
                            if (pageNum > pageToShow) pageToShow = pageNum;
                        }
                    }
                    
                    if (!/[a-zA-Z]/.test(descriptionInput.value)) {
                        errors.push('Custom intervention description must contain text');
                        isValid = false;
                        
                        const page = row.closest('.form-page');
                        if (page) {
                            const pageNum = parseInt(page.id.replace('page', ''));
                            if (pageNum > pageToShow) pageToShow = pageNum;
                        }
                    }
                    
                    // Check duration
                    if (!durationInput.value) {
                        errors.push('Please enter duration for all custom interventions');
                        isValid = false;
                        
                        const page = row.closest('.form-page');
                        if (page) {
                            const pageNum = parseInt(page.id.replace('page', ''));
                            if (pageNum > pageToShow) pageToShow = pageNum;
                        }
                    } else if (parseFloat(durationInput.value) <= 0) {
                        errors.push('Duration for custom interventions must be greater than 0');
                        isValid = false;
                        
                        const page = row.closest('.form-page');
                        if (page) {
                            const pageNum = parseInt(page.id.replace('page', ''));
                            if (pageNum > pageToShow) pageToShow = pageNum;
                        }
                    }
                }
            });
            
            // 7. Check evaluation recommendations (if we're on or past page 9)
            const evaluationRecs = document.getElementById('evaluation_recommendations').value.trim();
            const page9 = document.getElementById('page9');
            
            if (page9 && page9.classList.contains('active')) {
                if (!evaluationRecs) {
                    errors.push('Please provide evaluation recommendations');
                    isValid = false;
                    pageToShow = 9;
                } else if (evaluationRecs.length < 20) {
                    errors.push('Evaluation recommendations must be at least 20 characters');
                    isValid = false;
                    pageToShow = 9;
                } else if (!/[a-zA-Z]/.test(evaluationRecs)) {
                    errors.push('Evaluation recommendations must contain text and cannot consist of only numbers or symbols');
                    isValid = false;
                    pageToShow = 9;
                }
            }
            
            // If there are errors, show them and prevent form submission
            if (!isValid) {
                showValidationError(pageToShow, errors);
                return false;
            }
            
            // If all validation passed, submit the form
            this.submit();
        });
    </script>

    <script>
    // Function to add a new "Others" input
    function addOthersInput(containerId) {
        const othersContainer = document.getElementById(containerId);
        const newOthersRow = document.createElement('div');
        newOthersRow.classList.add('row', 'mb-2', 'others-row');
        newOthersRow.innerHTML = `
            <div class="col-md-9">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="${containerId}_others${othersContainer.children.length}">
                    <label class="form-check-label" for="${containerId}_others${othersContainer.children.length}">Others</label>
                </div>
                <!-- Additional Input Field -->
                <input type="text" class="form-control mt-2" placeholder="Enter details">
            </div>
            <div class="col-md-3">
                <input type="number" class="form-control" placeholder="Mins">
            </div>
        `;
        othersContainer.appendChild(newOthersRow);
    }

    // Function to delete the last "Others" input
    function deleteOthersInput(containerId) {
        const othersContainer = document.getElementById(containerId);
        const othersRows = othersContainer.querySelectorAll('.others-row');
        if (othersRows.length > 1) { // Ensure at least one "Others" input remains
            othersContainer.removeChild(othersRows[othersRows.length - 1]);
        } else {
            // Find which page this container is on
            const page = othersContainer.closest('.form-page');
            const pageNum = page ? parseInt(page.id.replace('page', '')) : 1;
            showValidationError(pageNum, "At least one 'Others' input is required.");
        }
    }

    // Function to show validation errors properly with multiple messages
    function showValidationError(pageNumber, errorMessages) {
        // Go to the specified page
        goToPage(pageNumber);
        
        // Get the error container on that page
        const errorContainer = document.querySelector('#page' + pageNumber + ' .validation-error-container');
        
        if (errorContainer) {
            // Clear previous content
            errorContainer.innerHTML = '';
            
            // Add header
            const header = document.createElement('h5');
            header.textContent = 'Please fix the following errors:';
            errorContainer.appendChild(header);
            
            // Create list of errors
            const list = document.createElement('ul');
            
            // Handle both string and array formats
            if (Array.isArray(errorMessages)) {
                // Multiple errors as array
                errorMessages.forEach(msg => {
                    const item = document.createElement('li');
                    item.textContent = msg;
                    list.appendChild(item);
                });
            } else if (typeof errorMessages === 'string' && errorMessages.includes('<br>')) {
                // String with <br> separators
                errorMessages.split('<br>').forEach(msg => {
                    const item = document.createElement('li');
                    item.textContent = msg;
                    list.appendChild(item);
                });
            } else {
                // Single error message
                const item = document.createElement('li');
                item.textContent = errorMessages;
                list.appendChild(item);
            }
            
            errorContainer.appendChild(list);
            
            // Add close button
            const closeButton = document.createElement('button');
            closeButton.type = 'button';
            closeButton.className = 'btn-close';
            closeButton.setAttribute('aria-label', 'Close');
            closeButton.style.position = 'absolute';
            closeButton.style.right = '10px';
            closeButton.style.top = '10px';
            closeButton.addEventListener('click', function() {
                errorContainer.style.display = 'none';
            });
            
            errorContainer.style.position = 'relative';
            errorContainer.appendChild(closeButton);
            
            // Display the error container
            errorContainer.style.display = 'block';
            errorContainer.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
</script>

<script>
// **** ABSOLUTE FINAL FIX - ONE SCRIPT TO RULE THEM ALL ****
(function() {
    // Wait for page load
    window.addEventListener('load', function() {
        console.log('Applying absolute final form fix');
        
        // Get the form
        const form = document.getElementById('weeklyCarePlanForm');
        if (!form) return;
        
        // CRITICAL: Disable browser validation
        form.setAttribute('novalidate', 'novalidate');
        
        // CRITICAL: Intercept before any other handlers
        form.addEventListener('submit', function(event) {
            // Always stop default first
            event.preventDefault();
            event.stopPropagation();
            console.log('Form submission intercepted by absolute final fix');
            
            // 1. TEMPORARILY DISABLE HIDDEN FIELDS (for form data collection only)
            const disabledFields = [];

            document.querySelectorAll('.form-page:not(.active) input, .form-page:not(.active) select, .form-page:not(.active) textarea').forEach(field => {
                if (!field.disabled) {
                    disabledFields.push(field); // Track only fields we're disabling
                    field.disabled = true;
                }
                
                // Remove validation constraints
                ['required', 'min', 'max', 'pattern', 'step', 'minlength', 'maxlength'].forEach(attr => {
                    if (field.hasAttribute(attr)) {
                        field.removeAttribute(attr);
                    }
                });
            });

            // IMPORTANT: Function to re-enable all temporarily disabled fields
            const reEnableFields = () => {
                disabledFields.forEach(field => {
                    field.disabled = false;
                });
                disabledFields.length = 0; // Clear the array
            };
            
            // 2. BYPASS BROWSER VALIDATION BY USING AJAX SUBMISSION
            const formData = new FormData(form);
            
            // Re-enable fields that were disabled but needed for submission
            document.querySelectorAll('.intervention-checkbox:checked').forEach(checkbox => {
                const durationInput = checkbox.closest('.row').querySelector('.intervention-duration');
                if (durationInput) {
                    durationInput.disabled = false;
                    // Add it back to formData if it was disabled
                    formData.append(durationInput.name, durationInput.value);
                }
            });
            
            // Re-enable fields that were temporarily disabled
            document.querySelectorAll('.form-page:not(.active) input:disabled, .form-page:not(.active) select:disabled, .form-page:not(.active) textarea:disabled').forEach(field => {
                if (field.name && field.value) {
                    formData.append(field.name, field.value);
                }
            });
            
            // 3. HANDLE VALIDATION MANUALLY
            let isValid = true;
            let errors = [];
            let pageToShow = 1;
            
            // All validation logic (your existing validation code)
            // 1. Check if beneficiary is selected
            if (!document.getElementById('beneficiary_id').value) {
                errors.push('Please select a beneficiary');
                isValid = false;
                pageToShow = 1;
            }
            
            // 2. Check assessment - minimum 20 characters with meaningful content
            const assessment = document.getElementById('assessment').value.trim();
            if (!assessment) {
                errors.push('Please provide an assessment');
                isValid = false;
                pageToShow = 1;
            } else if (assessment.length < 20) {
                errors.push('Assessment must be at least 20 characters');
                isValid = false;
                pageToShow = 1;
            } else if (!/[a-zA-Z]/.test(assessment)) {
                errors.push('Assessment must contain text and cannot consist of only numbers or symbols');
                isValid = false;
                pageToShow = 1;
            }
            
            // Add all your other validations here
            
            // 4. SHOW ERRORS OR SUBMIT
            if (!isValid && errors.length > 0) {
                showValidationError(pageToShow, errors);
                console.log('Validation failed - form not submitted');
                return false;
            }
            
            // 5. IF ALL VALID, SUBMIT THE FORM USING FETCH API
            fetch(form.action, {
                method: form.method,
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                redirect: 'manual'
            })
            .then(response => {
                // First check if this is a redirect (success)
                if (response.type === 'opaqueredirect' || response.redirected) {
                    // Show success message without page refresh
                    const successMsg = document.getElementById('success-message');
                    if (successMsg) {
                        successMsg.style.display = 'block';
                        window.scrollTo({top: 0, behavior: 'smooth'});
                    }
                    
                    // Reset form and return to first page
                    setTimeout(() => {
                        form.reset();
                        goToPage(1);
                        document.querySelectorAll('.validation-error-container').forEach(el => {
                            el.style.display = 'none';
                        });
                    }, 500);
                    
                    // Return mock response to prevent redirect
                    return {
                        text: () => Promise.resolve('{"success":true}'),
                        json: () => Promise.resolve({"success":true}),
                        redirected: true,
                        status: 200,
                        type: 'basic',
                        ok: true,
                        clone: () => ({ json: () => Promise.resolve({"success":true}) })
                    };
                }
                // Try parsing as JSON first (Laravel typically returns JSON for validation errors)
                return response.text().then(text => {
                    try {
                        return { isJson: true, data: JSON.parse(text) };
                    } catch (e) {
                        return { isJson: false, data: text };
                    }
                });
            })
            .then(result => {
            // Always re-enable fields regardless of success/failure
            reEnableFields();
            
            if (!result) return; // Skip if redirecting
            
            if (result.isJson && result.data.errors) {
                // Handle Laravel validation errors (JSON format)
                const errorMessages = [];
                
                // Extract error messages from the Laravel JSON response
                for (const field in result.data.errors) {
                    result.data.errors[field].forEach(message => {
                        errorMessages.push(message);
                    });
                }
                
                console.log('Server validation errors:', errorMessages);
                showValidationError(1, errorMessages);
                } else if (!result.isJson) {
                    // Not JSON, try to extract errors from HTML
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(result.data, 'text/html');
                    
                    // Look for validation errors in the HTML
                    const errorElements = doc.querySelectorAll('.invalid-feedback, .alert-danger');
                    const errorMessages = [];
                    
                    errorElements.forEach(el => {
                        if (el.textContent.trim()) {
                            errorMessages.push(el.textContent.trim());
                        }
                    });
                    
                    if (errorMessages.length > 0) {
                        console.log('HTML validation errors:', errorMessages);
                        showValidationError(1, errorMessages);
                    } else {
                        // No specific errors found
                        showValidationError(1, 'Form submission failed. Please check your input.');
                    }
                } else {
                    // Unexpected response format
                    showValidationError(1, 'An unexpected error occurred during submission.');
                }
            })
            .catch(error => {
                // Re-enable fields on error
                reEnableFields();
                
                console.error('Submission error:', error);
                showValidationError(1, 'Connection error during form submission. Please try again.');
            });
            
        }, true); // true = capture phase, ensures this runs first
        
        console.log('Absolute final form fix applied successfully');
    });
})();
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Get your form
    const form = document.getElementById('weeklyCarePlanForm');
    if (!form) return;
    
    // Intercept the error display function to prevent flashing errors on success
    const originalShowError = window.showValidationError;
    window.showValidationError = function(pageNumber, errorMessages) {
        // Do nothing if the success message is already showing (this prevents the error flash)
        if (document.getElementById('success-message') && 
            document.getElementById('success-message').style.display === 'block') {
            return;
        }
        
        // Otherwise call the original function
        originalShowError(pageNumber, errorMessages);
    };
    
    // Also fix the AJAX response handling in the absolute final fix script
    const originalFetch = window.fetch;
    window.fetch = function(url, options) {
        if (options && options.body instanceof FormData && 
            options.body.has('beneficiary_id') && 
            url.includes('weeklycareplans')) {
            
            // This is our specific form submission
            return originalFetch(url, options).then(response => {
                if (response.type === 'opaqueredirect' || response.redirected || response.status === 200 || response.status === 302) {
                    // Show success message
                    const successMsg = document.getElementById('success-message');
                    if (successMsg) {
                        successMsg.style.display = 'block';
                        window.scrollTo({top: 0, behavior: 'smooth'});
                    }
                    
                    setTimeout(() => {
                        form.reset();
                        if (typeof goToPage === 'function') {
                            goToPage(1);
                        }
                        document.querySelectorAll('.validation-error-container').forEach(el => {
                            el.style.display = 'none';
                        });
                    }, 500);
                    
                    // Create a complete response that won't trigger error handling
                    return {
                        text: () => Promise.resolve('{"success":true}'),
                        json: () => Promise.resolve({"success":true}),
                        clone: () => ({ json: () => Promise.resolve({"success":true}) }),
                        redirected: true,
                        url: url,
                        status: 200,
                        ok: true,
                        type: 'basic'
                    };
                }
                
                return response;
            });
        }
        
        // For all other fetches, use the original
        return originalFetch(url, options);
    };
});
</script>

<script>
// FINAL FIX FOR SUCCESS MESSAGE AND ERROR HANDLING
document.addEventListener('DOMContentLoaded', function() {
    // Improve the error display function to properly show all errors
    const originalShowError = window.showValidationError;
    if (originalShowError) {
        window.showValidationError = function(pageNumber, errorMessages) {
            // First re-enable any disabled fields
            document.querySelectorAll('input:disabled:not([readonly]), select:disabled:not([readonly]), textarea:disabled:not([readonly])')
                .forEach(field => {
                    field.disabled = false;
                });
            
            // Special handling for intervention durations
            document.querySelectorAll('.intervention-checkbox:checked').forEach(checkbox => {
                const durationInput = checkbox.closest('.row').querySelector('.intervention-duration');
                if (durationInput) {
                    durationInput.disabled = false;
                }
            });
            
            // Don't show errors if success is visible
            if (document.getElementById('success-message') && 
                document.getElementById('success-message').style.display === 'block') {
                return;
            }
            
            // Call original function to show errors
            originalShowError(pageNumber, errorMessages);
        };
    }
    
    // Override the original fetch hook to properly handle form responses
    const originalFetch = window.fetch;
    window.fetch = function(url, options) {
        if (options && options.body instanceof FormData && 
            options.body.has('beneficiary_id') && 
            url.includes('weeklycareplans')) {
            
            // This is our weekly care plan form submission
            return originalFetch(url, options)
                .then(response => {
                    // Success case
                    if (response.type === 'opaqueredirect' || response.redirected || response.status === 200 || response.status === 302) {
                        // Hide any validation errors first
                        document.querySelectorAll('.validation-error-container').forEach(el => {
                            el.style.display = 'none';
                        });
                        
                        // Show success message
                        const successMsg = document.getElementById('success-message');
                        if (successMsg) {
                            successMsg.style.display = 'block';
                            window.scrollTo({top: 0, behavior: 'smooth'});
                        }
                        
                        // Reset form after short delay
                        setTimeout(() => {
                            const form = document.getElementById('weeklyCarePlanForm');
                            if (form) {
                                form.reset();
                                if (typeof goToPage === 'function') {
                                    goToPage(1);
                                }
                            }
                        }, 500);
                        
                        // Return a complete mock response object
                        return {
                            text: () => Promise.resolve('{"success":true}'),
                            json: () => Promise.resolve({"success":true}),
                            clone: () => ({ json: () => Promise.resolve({"success":true}) }),
                            redirected: true,
                            url: url,
                            status: 200,
                            ok: true,
                            type: 'basic'
                        };
                    }
                    return response;
                })
                .catch(error => {
                    // Re-enable any disabled fields on error
                    document.querySelectorAll('input:disabled:not([readonly]), select:disabled:not([readonly]), textarea:disabled:not([readonly])')
                        .forEach(field => {
                            field.disabled = false;
                        });
                    
                    // Re-throw the error to maintain the error chain
                    throw error;
                });
        }
        
        // For all other fetch calls, use the original fetch
        return originalFetch(url, options);
    };
});
</script>

<script>
function previewImage(event) {
    const preview = document.getElementById('picture_preview');
    const noPreviewText = document.getElementById('no_preview_text');
    const file = event.target.files[0];
    
    if (file) {
        const reader = new FileReader();
        
        reader.onload = function() {
            preview.src = reader.result;
            preview.style.display = 'block';
            noPreviewText.style.display = 'none';
        }
        
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
        noPreviewText.style.display = 'block';
    }
}
</script>

</body>
</html>


