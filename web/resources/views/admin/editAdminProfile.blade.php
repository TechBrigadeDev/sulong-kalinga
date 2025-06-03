<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Administrator Profile</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/addUsers.css') }}">
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')
    
    <div class="home-section">
        <div class="container-fluid">            
            <!-- Back Button Logic -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <form action="{{ route('admin.administrators.view') }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="administrator_id" value="{{ $administrator->id }}">
                    <button type="submit" class="btn btn-secondary original-back-btn">
                        <i class="bx bx-arrow-back"></i> Back
                    </button>
                </form>
                <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">EDIT ADMINISTRATOR PROFILE</div>
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
                    <form action="{{ route('admin.administrators.update', $administrator->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT') <!-- Use PUT method for updating -->
                        
                        <!-- Personal Details -->
                        <div class="row mb-1 mt-3">
                            <div class="col-12">
                                <h5 class="text-start">Personal Details</h5>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-3">
                                <label for="firstName" class="form-label">First Name<label style="color:red;"> * </label></label>
                                <input type="text" class="form-control" id="firstName" name="first_name" 
                                    placeholder="Enter first name" 
                                    value="{{ old('first_name', $administrator->first_name) }}" 
                                    required>
                                    
                            </div>
                            <div class="col-md-3">
                                <label for="lastName" class="form-label">Last Name<label style="color:red;"> * </label></label>
                                <input type="text" class="form-control" id="lastName" name="last_name" 
                                    placeholder="Enter last name" 
                                    value="{{ old('last_name', $administrator->last_name) }}" 
                                    required>
                                    
                            </div>
                            <!-- <div class="col-md-3">
                                <label for="birthDate" class="form-label">Birthday</label>
                                <input type="date" class="form-control" id="birthDate" name="birth_date" value="{{ $administrator->birth_date }}" required>
                            </div> -->
                            <div class="col-md-3">
                                <label for="birthDate" class="form-label">Birthday<label style="color:red;"> * </label></label>
                                <input type="date" class="form-control" id="birthDate" name="birth_date" 
                                    value="{{ old('birth_date', $birth_date) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-control" id="gender" name="gender">
                                    <option value="Male" {{ old('gender', $administrator->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $administrator->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender', $administrator->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="civilStatus" class="form-label">Civil Status</label>
                                <select class="form-select" id="civilStatus" name="civil_status">
                                    <option value="" disabled>Select civil status</option>
                                    <option value="Single" {{ old('civil_status', $administrator->civil_status) == 'Single' ? 'selected' : '' }}>Single</option>
                                    <option value="Married" {{ old('civil_status', $administrator->civil_status) == 'Married' ? 'selected' : '' }}>Married</option>
                                    <option value="Widowed" {{ old('civil_status', $administrator->civil_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                    <option value="Divorced" {{ old('civil_status', $administrator->civil_status) == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="religion" class="form-label">Religion</label>
                                <input type="text" class="form-control" id="religion" name="religion" placeholder="Enter religion" value="{{ old('religion', $administrator->religion) }}" pattern="^[a-zA-Z\s]*$" title="Only alphabets and spaces are allowed.">
                            </div>
                            <div class="col-md-3">
                                <label for="nationality" class="form-label">Nationality</label>
                                <input type="text" class="form-control" id="nationality" name="nationality" placeholder="Enter nationality" value="{{ old('nationality', $administrator->nationality) }}" pattern="^[a-zA-Z\s]*$" title="Only alphabets and spaces are allowed.">
                            </div>
                            <div class="col-md-3">
                                <label for="educationalBackground" class="form-label">Educational Background</label>
                                <select class="form-select" id="educationalBackground" name="educational_background" required>
                                    <option value="" disabled {{ old('educational_background', $administrator->educational_background ?? '') ? '' : 'selected' }}>Select educational background</option>
                                    <option value="Elementary Graduate" {{ old('educational_background', $administrator->educational_background ?? '') == 'Elementary Graduate' ? 'selected' : '' }}>Elementary Graduate</option>
                                    <option value="High School Undergraduate" {{ old('educational_background', $administrator->educational_background ?? '') == 'High School Undergraduate' ? 'selected' : '' }}>High School Undergraduate</option>
                                    <option value="High School Graduate" {{ old('educational_background', $administrator->educational_background ?? '') == 'High School Graduate' ? 'selected' : '' }}>High School Graduate</option>
                                    <option value="Vocational/Technical Course" {{ old('educational_background', $administrator->educational_background ?? '') == 'Vocational/Technical Course' ? 'selected' : '' }}>Vocational/Technical Course</option>
                                    <option value="College Undergraduate" {{ old('educational_background', $administrator->educational_background ?? '') == 'College Undergraduate' ? 'selected' : '' }}>College Undergraduate</option>
                                    <option value="Bachelor's Degree" {{ old('educational_background', $administrator->educational_background ?? '') == "Bachelor's Degree" ? 'selected' : '' }}>Bachelor's Degree</option>
                                    <option value="Master's Degree" {{ old('educational_background', $administrator->educational_background ?? '') == "Master's Degree" ? 'selected' : '' }}>Master's Degree</option>
                                    <option value="Doctorate Degree" {{ old('educational_background', $administrator->educational_background ?? '') == 'Doctorate Degree' ? 'selected' : '' }}>Doctorate Degree</option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">

                        <!-- Current Address -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Current Address</h5>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="addressDetails" class="form-label">House No., Street, Subdivision, Barangay, City, Province<label style="color:red;"> * </label></label>
                                <textarea class="form-control" id="addressDetails" name="address_details" rows="2" required>{{ old('address_details', $administrator->address) }}</textarea>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Contact Information</h5>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="personalEmail" class="form-label">Personal Email Address<label style="color:red;"> * </label></label>
                                <input type="email" class="form-control" id="personalEmail" name="personal_email" value="{{ old('personal_email', $administrator->personal_email) }}" required>
                            </div>
                            <div class="col-md-4">
                                <label for="mobileNumber" class="form-label">Mobile Number<label style="color:red;"> * </label></label>
                                <input type="text" class="form-control" id="mobileNumber" name="mobile_number" value="{{ old('mobile_number', ltrim($administrator->mobile, '+63')) }}" required oninput="restrictToNumbers(this)" maxlength="10" placeholder="Enter mobile number">
                            </div>
                            <div class="col-md-4">
                                <label for="landlineNumber" class="form-label">Landline Number</label>
                                <input type="text" class="form-control" id="landlineNumber" name="landline_number" value="{{ old('landline_number', $administrator->landline) }}" oninput="restrictToNumbers(this)" maxlength="10" placeholder="Enter landline number">
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
                                <label for="administratorPhoto" class="form-label">Administrator Photo</label>
                                <input type="file" class="form-control" id="administratorPhoto" name="administrator_photo" accept="image/png, image/jpeg">
                                 <small class="text-danger">Maximum file size: 7MB</small>
                                @if($administrator->photo)
                                    <small class="text-muted" title="{{ basename($administrator->photo) }}">
                                        Current file: {{ strlen(basename($administrator->photo)) > 30 ? substr(basename($administrator->photo), 0, 30) . '...' : basename($administrator->photo) }}
                                    </small>
                                @else
                                    <small class="text-muted">No file uploaded</small>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="governmentID" class="form-label">Government Issued ID</label>
                                <input type="file" class="form-control" id="governmentID" name="government_ID" accept=".jpg,.png">
                                 <small class="text-danger">Maximum file size: 7MB</small>
                                @if($administrator->government_issued_id)
                                    <small class="text-muted" title="{{ basename($administrator->government_issued_id) }}">
                                        Current file: {{ strlen(basename($administrator->government_issued_id)) > 30 ? substr(basename($administrator->government_issued_id), 0, 30) . '...' : basename($administrator->government_issued_id) }}
                                    </small>
                                @else
                                    <small class="text-muted">No file uploaded</small>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="resume" class="form-label">Resume / CV</label>
                                <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx">
                                <small class="text-danger">Maximum file size: 5MB</small>
                                @if($administrator->cv_resume)
                                    <small class="text-muted" title="{{ basename($administrator->cv_resume) }}">
                                        Current file: {{ strlen(basename($administrator->cv_resume)) > 30 ? substr(basename($administrator->cv_resume), 0, 30) . '...' : basename($administrator->cv_resume) }}
                                    </small>
                                @else
                                    <small class="text-muted">No file uploaded</small>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <label for="sssID" class="form-label">SSS ID</label>
                                <input type="text" class="form-control" id="sssID" name="sss_ID" placeholder="Enter SSS ID" maxlength="10" value="{{ old('sss_ID', $administrator->sss_id_number) }}" oninput="restrictToNumbers(this)" title="Must be 10 digits.">
                            </div>
                            <div class="col-md-4">
                                <label for="philhealthID" class="form-label">PhilHealth ID</label>
                                <input type="text" class="form-control" id="philhealthID" name="philhealth_ID" placeholder="Enter PhilHealth ID" maxlength="12" value="{{ old('philhealth_ID', $administrator->philhealth_id_number) }}" oninput="restrictToNumbers(this)" title="Must be 12 digits.">
                            </div>
                            <div class="col-md-4">
                                <label for="pagibigID" class="form-label">Pag-Ibig ID</label>
                                <input type="text" class="form-control" id="pagibigID" name="pagibig_ID" placeholder="Enter Pag-Ibig ID" maxlength="12" value="{{ old('pagibig_ID', $administrator->pagibig_id_number) }}" oninput="restrictToNumbers(this)" title="Must be 12 digits.">
                            </div>
                        </div>

                        <hr class="my-4">
                        
                        <!-- Account Registration -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Administrator Account Registration</h5>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <label for="email" class="form-label">Work Email Address</label>
                                <input type="email" class="form-control" id="email" name="account[email]" value="{{ old('account.email', $administrator->email) }}" required placeholder="Enter work email">
                            </div>
                            <div class="col-md-4">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="account[password]" placeholder="Enter new password (leave blank to keep current)">
                            </div>
                            <div class="col-md-4">
                                <label for="confirmPassword" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirmPassword" name="account[password_confirmation]" placeholder="Confirm new password">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 position-relative">
                                <label for="organization_Roles" class="form-label">Organization Roles</label>
                                <select class="form-select" id="Organization_RolesDropdown" name="Organization_Roles" required>
                                    <option value="" disabled {{ !isset($administrator->organization_role_id) ? 'selected' : '' }}>Select organization role</option>
                                    <option value="2" {{ old('Organization_Roles', $administrator->organization_role_id) == 2 ? 'selected' : '' }}>Project Coordinator</option>
                                    <option value="3" {{ old('Organization_Roles', $administrator->organization_role_id) == 3 ? 'selected' : '' }}>MEAL Coordinator</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mt-4">
                            <div class="col-12 d-flex justify-content-center align-items-center">
                                <button type="submit" class="btn btn-success btn-lg d-flex align-items-center">
                                    <i class='bx bx-save me-2' style="font-size: 24px;"></i>
                                    Save Changes
                                </button>
                            </div>
                        </div>
                    </form>
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
    document.addEventListener('DOMContentLoaded', function() {
        // Max sizes in bytes
        const MAX_SIZES = {
            'administratorPhoto': 7 * 1024 * 1024, // 7MB
            'governmentID': 7 * 1024 * 1024,     // 7MB
            'resume': 5 * 1024 * 1024           // 5MB
        };
        
        // Initialize modal
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
        
        // Form submission check for file sizes (will be combined with existing handler)
        function validateFileSize(form) {
            let isValid = true;
            
            document.querySelectorAll('input[type="file"]').forEach(input => {
                if (input.files.length > 0) {
                    const file = input.files[0];
                    const maxSize = MAX_SIZES[input.id] || 5 * 1024 * 1024;
                    
                    if (file.size > maxSize) {
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
        }
        
        // Store the validation function globally
        window.validateFileSize = validateFileSize;
    });
    </script>
    <script>
    document.querySelector('form').addEventListener('submit', function (e) {
        // Always prevent the default form submission first
        e.preventDefault();
        
        // Check file sizes first
        if (!window.validateFileSize(this)) {
            return false;
        }
        
        // Check if there are validation errors
        if (!document.querySelector('.alert-danger')) {
            // No validation errors, show success modal
            const successModal = new bootstrap.Modal(document.getElementById('saveSuccessModal'));
            const form = this;
            
            // Mark form as validated to prevent duplicate checks
            form.dataset.validated = 'true';
            
            // Show modal
            successModal.show();
            
            // Listen for modal hidden event
            document.getElementById('saveSuccessModal').addEventListener('hidden.bs.modal', function onModalHidden() {
                // Remove this event listener to prevent multiple submissions
                document.getElementById('saveSuccessModal').removeEventListener('hidden.bs.modal', onModalHidden);
                
                // Submit the form
                form.submit();
            });
            
            // Add a button click handler for the OK button
            document.querySelector('#saveSuccessModal .btn-primary').addEventListener('click', function() {
                // Submit the form when OK is clicked
                form.submit();
            });
        } else {
            // There are validation errors, allow normal form submission
            this.submit();
        }
    });
    </script>
    <script>
        // document.addEventListener('DOMContentLoaded', function () {
        //     // Function to filter dropdown items
        //     function filterDropdown(inputId, dropdownId) {
        //         const input = document.getElementById(inputId);
        //         const dropdown = document.getElementById(dropdownId);
        //         const items = dropdown.querySelectorAll('.dropdown-item');

        //         input.addEventListener('input', function () {
        //             const filter = input.value.toLowerCase();
        //             let hasVisibleItems = false;

        //             items.forEach(item => {
        //                 if (item.textContent.toLowerCase().includes(filter)) {
        //                     item.style.display = 'block';
        //                     hasVisibleItems = true;
        //                 } else {
        //                     item.style.display = 'none';
        //                 }
        //             });
        //             dropdown.style.display = hasVisibleItems ? 'block' : 'none';
        //         });
        //         input.addEventListener('blur', function () {
        //             setTimeout(() => dropdown.style.display = 'none', 200);
        //         });
        //         input.addEventListener('focus', function () {
        //             dropdown.style.display = 'block';
        //         });

        //         // Handle item selection
        //         items.forEach(item => {
        //             item.addEventListener('click', function (e) {
        //                 e.preventDefault();
        //                 input.value = item.textContent;
        //                 document.getElementById(inputId.replace('Input', '')).value = item.getAttribute('data-value');
        //                 dropdown.style.display = 'none';
        //             });
        //         });
        //     }

        //     // Initialize filtering for each dropdown
        //     filterDropdown('civilStatusInput', 'civilStatusDropdown');
        //     filterDropdown('genderInput', 'genderDropdown');
        //     filterDropdown('educationalBackgroundInput', 'educationalBackgroundDropdown');
        //     filterDropdown('Organization_RolesInput', 'Organization_RolesDropdown');
        // });

        function validateMobileNumber(input) {
            // Remove any non-numeric characters
            input.value = input.value.replace(/[^0-9]/g, '');

            // Limit the input to 11 digits
            if (input.value.length > 11) {
                input.value = input.value.slice(0, 11);
            }
        }

        // document.querySelectorAll('.dropdown-item').forEach(item => {
        //     item.addEventListener('click', function (e) {
        //         e.preventDefault();
        //         const input = this.closest('.position-relative').querySelector('input[type="text"]');
        //         const hiddenInput = this.closest('.position-relative').querySelector('input[type="hidden"]');
        //         input.value = this.textContent;
        //         hiddenInput.value = this.getAttribute('data-value');
        //     });
        // });

        document.querySelectorAll('input[type="text"]').forEach(input => {
            input.addEventListener('input', function () {
                // Special handling for name fields to allow proper Filipino names
                if (input.id === 'firstName' || input.id === 'lastName' || input.id === 'religion' || input.id === 'nationality') {
                    // Allow letters, spaces, hyphens, apostrophes, periods, and ñ/Ñ for name fields
                    this.value = this.value.replace(/[^a-zA-ZÑñ\s\'\.-]/g, '');
                } else {
                    // For other text fields, maintain the original restriction
                    this.value = this.value.replace(/[^a-zA-Z0-9\s]/g, '');
                }
            });
        });

        document.querySelectorAll('input[type="email"]').forEach(input => {
            input.addEventListener('input', function () {
                // Allow only characters that match the regex
                this.value = this.value.replace(/[^a-zA-Z0-9._%+-@]/g, '');
            });
        });

        function restrictToNumbers(input) {
            input.value = input.value.replace(/[^0-9]/g, '');
        }

        // Restrict input to numbers only
        function restrictToNumbers(input) {
            input.value = input.value.replace(/[^0-9]/g, ''); // Remove non-numeric characters
        }

        // Prevent spaces in email fields
        function preventSpaces(input) {
            input.value = input.value.replace(/\s/g, ''); // Remove spaces
        }

        // Validate Current Address to allow only alphanumeric characters, spaces, commas, periods, and hyphens
        function validateAddress(input) {
            input.value = input.value.replace(/[^a-zA-Z0-9\s,.-]/g, ''); // Remove invalid characters
        }

        // Validate Email to allow only characters matching the regex
        function validateEmail(input) {
            input.value = input.value.replace(/[^a-zA-Z0-9._%+-@]/g, ''); // Remove invalid characters
        }
    </script>
    <?php
    // if ($request->hasFile('administrator_photo')) {
    //     dd($request->file('administrator_photo')->getClientMimeType());
    // }
    ?>
</body>
</html>