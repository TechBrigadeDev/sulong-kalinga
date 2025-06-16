<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
            <!-- Back Button Logic -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <form action="{{ route('care-manager.careworkers.view') }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="careworker_id" value="{{ $careworker->id }}">
                    <button type="submit" class="btn btn-secondary original-back-btn">
                    <i class="bx bx-arrow-back"></i> Back
                    </button>
                </form>
                <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">EDIT CARE WORKER PROFILE</div>
            </div>

            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="row" id="addUserForm">
                <div class="col-12">
                <form action="{{ route('care-manager.careworkers.update', $careworker->id) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                        <div class="row mb-1 mt-3">
                            <div class="col-12">
                                <h5 class="text-start">Personal Details</h5> <!-- Row Title -->
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-3">
                                <label for="firstName" class="form-label">First Name</label>
                                <input type="text" class="form-control" id="firstName" name="first_name" 
                                    placeholder="Enter first name" 
                                    value="{{ old('first_name', $careworker->first_name) }}"
                                    required>
                                    
                            </div>
                            <div class="col-md-3">
                                <label for="lastName" class="form-label">Last Name</label>
                                <input type="text" class="form-control" id="lastName" name="last_name" 
                                    placeholder="Enter last name" 
                                    value="{{ old('last_name', $careworker->last_name) }}"
                                    required>
                                    
                            </div>
                            <div class="col-md-3">
                                <label for="birthDate" class="form-label">Birthday</label>
                                <input type="date" class="form-control" id="birthDate" name="birth_date" value="{{ old('birth_date', $birth_date) }}">
                            </div>
                            <div class="col-md-3 position-relative">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="" disabled>Select gender</option>
                                    <option value="Male" {{ old('gender', $careworker->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $careworker->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender', $careworker->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-3 position-relative">
                                <label for="civilStatus" class="form-label">Civil Status</label>
                                <select class="form-select" id="civilStatus" name="civil_status">
                                    <option value="" disabled>Select civil status</option>
                                    <option value="Single" {{ old('civil_status', $careworker->civil_status) == 'Single' ? 'selected' : '' }}>Single</option>
                                    <option value="Married" {{ old('civil_status', $careworker->civil_status) == 'Married' ? 'selected' : '' }}>Married</option>
                                    <option value="Widowed" {{ old('civil_status', $careworker->civil_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                    <option value="Divorced" {{ old('civil_status', $careworker->civil_status) == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="religion" class="form-label">Religion</label>
                                <input type="text" class="form-control" id="religion" name="religion" placeholder="Enter religion" value="{{ old('religion', $careworker->religion) }}"></div>
                            <div class="col-md-3">
                                <label for="nationality" class="form-label">Nationality</label>
                                <input type="text" class="form-control" id="nationality" name="nationality" placeholder="Enter nationality" value="{{ old('nationality', $careworker->nationality) }}"></div>
                            <div class="col-md-3 position-relative">
                                <label for="educationalBackground" class="form-label">Educational Background</label>
                                <select class="form-select" id="educationalBackground" name="educational_background">
                                    <option value="" disabled {{ old('educational_background', $careworker->educational_background ?? '') ? '' : 'selected' }}>Select educational background</option>
                                    <option value="Elementary Graduate" {{ old('educational_background', $careworker->educational_background ?? '') == 'Elementary Graduate' ? 'selected' : '' }}>Elementary Graduate</option>
                                    <option value="High School Undergraduate" {{ old('educational_background', $careworker->educational_background ?? '') == 'High School Undergraduate' ? 'selected' : '' }}>High School Undergraduate</option>
                                    <option value="High School Graduate" {{ old('educational_background', $careworker->educational_background ?? '') == 'High School Graduate' ? 'selected' : '' }}>High School Graduate</option>
                                    <option value="Vocational/Technical Course" {{ old('educational_background', $careworker->educational_background ?? '') == 'Vocational/Technical Course' ? 'selected' : '' }}>Vocational/Technical Course</option>
                                    <option value="College Undergraduate" {{ old('educational_background', $careworker->educational_background ?? '') == 'College Undergraduate' ? 'selected' : '' }}>College Undergraduate</option>
                                    <option value="Bachelor's Degree" {{ old('educational_background', $careworker->educational_background ?? '') == "Bachelor's Degree" ? 'selected' : '' }}>Bachelor's Degree</option>
                                    <option value="Master's Degree" {{ old('educational_background', $careworker->educational_background ?? '') == "Master's Degree" ? 'selected' : '' }}>Master's Degree</option>
                                    <option value="Doctorate Degree" {{ old('educational_background', $careworker->educational_background ?? '') == 'Doctorate Degree' ? 'selected' : '' }}>Doctorate Degree</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3 position-relative">
                                <label for="municipality" class="form-label">Municipality</label>
                                <select class="form-select" id="municipality" name="municipality">
                                    <option value="" disabled>Select municipality</option>
                                    @foreach($municipalities as $municipality)
                                        <option value="{{ $municipality->municipality_id }}" 
                                            {{ old('municipality', $careworker->assigned_municipality_id) == $municipality->municipality_id ? 'selected' : '' }}>
                                            {{ $municipality->municipality_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3 position-relative">
                                <label for="assigned_care_manager" class="form-label">Assigned Care Manager</label>
                                <select class="form-select" id="assigned_care_manager" name="assigned_care_manager">
                                    <option value="">None (Unassigned)</option>
                                    @foreach($careManagers as $careManager)
                                        <option value="{{ $careManager->id }}" 
                                            {{ old('assigned_care_manager', $careworker->assigned_care_manager_id) == $careManager->id ? 'selected' : '' }}>
                                            {{ $careManager->first_name }} {{ $careManager->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Row 2: Address -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Current Address</h5> <!-- Row Title -->
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="addressDetails" class="form-label">House No., Street, Subdivision, Barangay, City, Province</label>
                                <textarea class="form-control" id="addressDetails" name="address_details" placeholder="Enter complete current address" rows="2">{{ old('address_details', $careworker->address) }}</textarea>
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Contact Information -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Contact Information</h5> <!-- Row Title -->
                            </div>
                        </div> 
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="emailAddress" class="form-label">Email Address</label>
                                <input type="email" class="form-control" id="emailAddress" name="personal_email" placeholder="Enter personal email" value="{{ old('personal_email', $careworker->personal_email) }}">                            </div>
                            <div class="col-md-4">
                                <label for="mobileNumber" class="form-label">Mobile Number</label>
                                <input type="text" class="form-control" id="mobileNumber" name="mobile_number" placeholder="Enter mobile number" value="{{ old('mobile_number', ltrim($careworker->mobile, '+63')) }}">
                            </div>
                            <div class="col-md-4">
                                <label for="landlineNumber" class="form-label">Landline Number</label>
                                <input type="text" class="form-control" id="landlineNumber" name="landline_number" placeholder="Enter Landline number" value="{{ old('landline_number', $careworker->landline) }}">
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Documents -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Documents Upload</h5> <!-- Row Title -->
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <label for="careWorkerPhoto" class="form-label">Care Worker Photo</label>
                                <input type="file" class="form-control" id="careWorkerPhoto" name="careworker_photo" accept="image/png, image/jpeg" capture="user">
                                <small class="text-danger">Maximum file size: 7MB</small>
                                @if($careworker->photo)
                                    <small class="text-muted" title="{{ basename($careworker->photo) }}">
                                        Current file: {{ strlen(basename($careworker->photo)) > 30 ? substr(basename($careworker->photo), 0, 30) . '...' : basename($careworker->photo) }}
                                    </small>
                                @else
                                    <small class="text-muted">No file uploaded</small>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="governmentID" class="form-label">Government Issued ID</label>
                                <input type="file" class="form-control" id="governmentID" name="government_ID" accept=".jpg,.png">
                                <small class="text-danger">Maximum file size: 7MB</small>
                                @if($careworker->government_issued_id)
                                    <small class="text-muted" title="{{ basename($careworker->government_issued_id) }}">
                                        Current file: {{ strlen(basename($careworker->government_issued_id)) > 30 ? substr(basename($careworker->government_issued_id), 0, 30) . '...' : basename($careworker->government_issued_id) }}
                                    </small>
                                @else
                                    <small class="text-muted">No file uploaded</small>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="resume" class="form-label">Resume / CV</label>
                                <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx">
                                <small class="text-danger">Maximum file size: 5MB</small>
                                @if($careworker->cv_resume)
                                    <small class="text-muted" title="{{ basename($careworker->cv_resume) }}">
                                        Current file: {{ strlen(basename($careworker->cv_resume)) > 30 ? substr(basename($careworker->cv_resume), 0, 30) . '...' : basename($careworker->cv_resume) }}
                                    </small>
                                @else
                                    <small class="text-muted">No file uploaded</small>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <label for="generalCarePlan" class="form-label">SSS ID</label>
                                <input type="text" class="form-control" id="sssID" name="sss_ID" value="{{ old('sss_ID', $careworker->sss_id_number) }}" placeholder="Enter SSS ID number">
                            </div>
                            <div class="col-md-4">
                                <label for="philhealthID" class="form-label">PhilHealth ID</label>
                                <input type="text" class="form-control" id="philhealthID" name="philhealth_ID" value="{{ old('philhealth_ID', $careworker->philhealth_id_number) }}" placeholder="Enter PhilHealth ID number">                            
                            </div>
                            <div class="col-md-4">
                                <label for="pagibigID" class="form-label">Pag-Ibig ID</label>
                                <input type="text" class="form-control" id="pagibigID" name="pagibig_ID" value="{{ old('pagibig_ID', $careworker->pagibig_id_number) }}" placeholder="Enter Pag-IBIG ID number">
                            </div>
                        </div>


                        <hr class="my-4">
                        <!-- Account Registration -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Care Worker Account Registration</h5> <!-- Row Title -->
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="accountEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="accountEmail" name="account[email]" placeholder="Enter email" value="{{ old('account.email', $careworker->email) }}">                            </div>
                            <div class="col-md-4">
                                <label for="password" class="form-label">Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="account[password]" placeholder="Leave blank to keep current password">
                                    <span class="input-group-text password-toggle" data-target="password">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="confirmPassword" class="form-label">Confirm Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirmPassword" name="account[confirm_password]" placeholder="Confirm password">
                                    <span class="input-group-text password-toggle" data-target="confirmPassword">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                </div>
                            </div>
                        </div>

                        
                        <div class="row mt-4">
                            <div class="col-12 d-flex justify-content-center align-items-center">
                                <button type="submit" class="btn btn-success btn-lg d-flex align-items-center">
                                    <i class='bx bx-save me-2' style="font-size: 24px;"></i>
                                    Update Care Worker
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
                    <p>Care Worker has been successfully saved!</p>
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
                    <h5 class="modal-title" id="fileSizeErrorModalLabel">File Size Error</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill text-danger me-3" style="font-size: 2rem;"></i>
                        <p id="fileSizeErrorMessage" class="mb-0"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src=" {{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

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
            filterDropdown('educationalBackgroundInput', 'educationalBackgroundDropdown');
            filterDropdown('municipalityInput', 'municipalityDropdown');
        });
    </script>
    <script>
    document.querySelector('form').addEventListener('submit', function (e) {
        // Always prevent the default form submission first
        e.preventDefault();
        
        // Check for file size issues first (prevents submission entirely if files are too large)
        let fileSizeValid = true;
        
        document.querySelectorAll('input[type="file"]').forEach(input => {
            if (input.files.length > 0) {
                const file = input.files[0];
                const MAX_SIZES = {
                    'careWorkerPhoto': 7 * 1024 * 1024, // 7MB
                    'governmentID': 7 * 1024 * 1024, // 7MB
                    'resume': 5 * 1024 * 1024 // 5MB
                };
                const maxSize = MAX_SIZES[input.id] || 5 * 1024 * 1024;
                
                if (file.size > maxSize) {
                    fileSizeValid = false;
                    
                    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(1);
                    const maxSizeMB = (maxSize / (1024 * 1024)).toFixed(1);
                    const fieldLabel = input.previousElementSibling ? input.previousElementSibling.textContent : input.id;
                    
                    // Show error modal
                    const fileSizeErrorModal = new bootstrap.Modal(document.getElementById('fileSizeErrorModal'));
                    const fileSizeErrorMessage = document.getElementById('fileSizeErrorMessage');
                    
                    fileSizeErrorMessage.innerHTML = `
                        <strong>Form submission failed</strong><br>
                        ${fieldLabel} (${fileSizeMB}MB) exceeds the maximum size of ${maxSizeMB}MB.<br>
                        Please select a smaller file or compress your existing file.
                    `;
                    fileSizeErrorModal.show();
                    return false;
                }
            }
        });
        
        if (!fileSizeValid) {
            return;
        }
        
        // Proceed with your existing validation and success modal logic
        if (!document.querySelector('.alert-danger')) {
            // No validation errors, show success modal
            const successModal = new bootstrap.Modal(document.getElementById('saveSuccessModal'));
            const form = this;
            
            // Show modal
            successModal.show();
            
            // Listen for modal hidden event
            document.getElementById('saveSuccessModal').addEventListener('hidden.bs.modal', function onModalHidden() {
                document.getElementById('saveSuccessModal').removeEventListener('hidden.bs.modal', onModalHidden);
                form.dataset.validated = 'true'; // Mark as validated to prevent double-checking file sizes
                form.submit();
            });
            
            // Add a button click handler for the OK button
            document.querySelector('#saveSuccessModal .btn-primary').addEventListener('click', function() {
                form.dataset.validated = 'true'; // Mark as validated to prevent double-checking file sizes
                form.submit();
            });
        } else {
            // There are validation errors, allow normal form submission
            this.submit();
        }
    });
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Max sizes in bytes
        const MAX_SIZES = {
            'careWorkerPhoto': 7 * 1024 * 1024, // 7MB
            'governmentID': 7 * 1024 * 1024, // 7MB
            'resume': 5 * 1024 * 1024 // 5MB
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
        document.querySelector('form').addEventListener('submit', function(e) {
            // Skip this check if the form is already being submitted after validation
            if (this.dataset.validated === 'true') {
                return true;
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
                        return false;
                    }
                }
            });
            
            if (!isValid) {
                return false;
            }
            
            // Mark as validated so we don't check again on the actual submission
            this.dataset.validated = 'true';
            return true;
        });
    });
    </script>
    <script>
        // Password toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
</body>
</html>