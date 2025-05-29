<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
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
                <form action="{{ route('admin.caremanagers.view') }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="caremanager_id" value="{{ $caremanager->id }}">
                    <button type="submit" class="btn btn-secondary original-back-btn">
                    <i class="bi bi-arrow-bar-left"></i> Back
                    </button>
                </form>
                <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">EDIT CARE MANAGER PROFILE</div>
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
                <form method="POST" action="{{ route('admin.caremanagers.update', $caremanager->id) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
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
                                    value="{{ old('first_name', $caremanager->first_name) }}" 
                                    placeholder="Enter first name" 
                                    required >
                                    
                            </div>
                            <div class="col-md-3">
                                <label for="lastName" class="form-label">Last Name<label style="color:red;"> * </label></label>
                                <input type="text" class="form-control" id="lastName" name="last_name" 
                                    value="{{ old('last_name', $caremanager->last_name) }}" 
                                    placeholder="Enter last name" 
                                    required>
                                   
                            </div>
                            <div class="col-md-3">
                                <label for="birthDate" class="form-label">Birthday<label style="color:red;"> * </label></label>
                                <input type="date" class="form-control" id="birthDate" name="birth_date" 
                                       value="{{ old('birth_date', $birth_date) }}" required>
                            </div>
                            <div class="col-md-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="" disabled>Select gender</option>
                                    <option value="Male" {{ old('gender', $caremanager->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $caremanager->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender', $caremanager->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-3">
                                <label for="civilStatus" class="form-label">Civil Status</label>
                                <select class="form-select" id="civilStatus" name="civil_status">
                                    <option value="" disabled>Select civil status</option>
                                    <option value="Single" {{ old('civil_status', $caremanager->civil_status) == 'Single' ? 'selected' : '' }}>Single</option>
                                    <option value="Married" {{ old('civil_status', $caremanager->civil_status) == 'Married' ? 'selected' : '' }}>Married</option>
                                    <option value="Widowed" {{ old('civil_status', $caremanager->civil_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                    <option value="Divorced" {{ old('civil_status', $caremanager->civil_status) == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="religion" class="form-label">Religion</label>
                                <input type="text" class="form-control" id="religion" name="religion" 
                                       value="{{ old('religion', $caremanager->religion) }}" placeholder="Enter religion">
                            </div>
                            <div class="col-md-3">
                                <label for="nationality" class="form-label">Nationality</label>
                                <input type="text" class="form-control" id="nationality" name="nationality" 
                                       value="{{ old('nationality', $caremanager->nationality) }}" placeholder="Enter nationality">
                            </div>
                            <div class="col-md-3">
                                <label for="municipality" class="form-label">Municipality<label style="color:red;"> * </label></label>
                                <select class="form-select" id="municipality" name="municipality" required>
                                    <option value="" disabled>Select municipality</option>
                                    @foreach ($municipalities as $municipality)
                                        <option value="{{ $municipality->municipality_id }}" {{ old('municipality', $caremanager->assigned_municipality_id) == $municipality->municipality_id ? 'selected' : '' }}>
                                            {{ $municipality->municipality_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="educationalBackground" class="form-label">Educational Background</label>
                                <select class="form-select" id="educationalBackground" name="educational_background" required>
                                    <option value="" disabled>Select educational background</option>
                                    <option value="College" {{ old('educational_background', $caremanager->educational_background) == 'College' ? 'selected' : '' }}>College</option>
                                    <option value="Highschool" {{ old('educational_background', $caremanager->educational_background) == 'Highschool' ? 'selected' : '' }}>High School</option>
                                    <option value="Doctorate" {{ old('educational_background', $caremanager->educational_background) == 'Doctorate' ? 'selected' : '' }}>Doctorate</option>
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
                                <textarea class="form-control" id="addressDetails" name="address_details" 
                                    placeholder="Enter complete current address" rows="2" required>{{ old('address_details', $caremanager->address) }}</textarea>
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Contact Information -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Contact Information</h5>
                            </div>
                        </div> 
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="personalEmail" class="form-label">Personal Email Address<label style="color:red;"> * </label></label>
                                <input type="email" class="form-control" id="personalEmail" name="personal_email" 
                                    value="{{ old('personal_email', $caremanager->personal_email) }}" placeholder="Enter personal email" required>
                            </div>
                            <div class="col-md-4">
                                <label for="mobileNumber" class="form-label">Mobile Number<label style="color:red;"> * </label></label>
                                <div class="input-group">
                                    <span class="input-group-text">+63</span>
                                    <input type="text" class="form-control" id="mobileNumber" name="mobile_number" 
                                        value="{{ old('mobile_number', substr($caremanager->mobile, 0, 3) === '+63' ? substr($caremanager->mobile, 3) : $caremanager->mobile) }}" 
                                        placeholder="9XXXXXXXXX" required
                                        pattern="[0-9]{10-11}"
                                        title="Please enter a valid 10-digit mobile number">
                                </div>  
                            </div>
                            <div class="col-md-4">
                                <label for="landlineNumber" class="form-label">Landline Number</label>
                                <input type="text" class="form-control" id="landlineNumber" name="landline_number" 
                                    value="{{ old('landline_number', $caremanager->landline) }}" placeholder="Enter landline number" maxlength="10">
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Documents Upload -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Documents Upload</h5>
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <label for="caremanagerPhoto" class="form-label">Care Manager Photo</label>
                                <input type="file" class="form-control" id="caremanagerPhoto" name="caremanager_photo" accept="image/png, image/jpeg">
                                <small class="text-danger">Maximum file size: 7MB</small>
                                @if($caremanager->photo)
                                    <small class="text-muted" title="{{ basename($caremanager->photo) }}">
                                        Current file: {{ strlen(basename($caremanager->photo)) > 30 ? substr(basename($caremanager->photo), 0, 30) . '...' : basename($caremanager->photo) }}
                                    </small>
                                @else
                                    <small class="text-muted">No file uploaded</small>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="governmentID" class="form-label">Government Issued ID</label>
                                <input type="file" class="form-control" id="governmentID" name="government_ID" accept=".jpg,.png">
                                <small class="text-danger">Maximum file size: 7MB</small>
                                @if($caremanager->government_issued_id)
                                    <small class="text-muted" title="{{ basename($caremanager->government_issued_id) }}">
                                        Current file: {{ strlen(basename($caremanager->government_issued_id)) > 30 ? substr(basename($caremanager->government_issued_id), 0, 30) . '...' : basename($caremanager->government_issued_id) }}
                                    </small>
                                @else
                                    <small class="text-muted">No file uploaded</small>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="resume" class="form-label">Resume / CV</label>
                                <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx">
                                <small class="text-danger">Maximum file size: 5MB</small>
                                @if($caremanager->cv_resume)
                                    <small class="text-muted" title="{{ basename($caremanager->cv_resume) }}">
                                        Current file: {{ strlen(basename($caremanager->cv_resume)) > 30 ? substr(basename($caremanager->cv_resume), 0, 30) . '...' : basename($caremanager->cv_resume) }}
                                    </small>
                                @else
                                    <small class="text-muted">No file uploaded</small>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <label for="sssID" class="form-label">SSS ID</label>
                                <input type="text" class="form-control" id="sssID" name="sss_ID" 
                                       value="{{ old('sss_ID', $caremanager->sss_id_number) }}" placeholder="Enter SSS ID number">
                            </div>
                            <div class="col-md-4">
                                <label for="philhealthID" class="form-label">PhilHealth ID</label>
                                <input type="text" class="form-control" id="philhealthID" name="philhealth_ID" 
                                       value="{{ old('philhealth_ID', $caremanager->philhealth_id_number) }}" placeholder="Enter PhilHealth ID number">
                            </div>
                            <div class="col-md-4">
                                <label for="pagibigID" class="form-label">Pag-Ibig ID</label>
                                <input type="text" class="form-control" id="pagibigID" name="pagibig_ID" 
                                       value="{{ old('pagibig_ID', $caremanager->pagibig_id_number) }}" placeholder="Enter Pag-Ibig ID number">
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Account Registration -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Care Manager Account Registration</h5>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="accountEmail" class="form-label">Email</label>
                                <input type="email" class="form-control" id="accountEmail" name="account[email]" 
                                       value="{{ old('account.email', $caremanager->email) }}" placeholder="Enter email" required>
                            </div>
                            <div class="col-md-4">
                                <label for="password" class="form-label">Password (leave blank to keep current)</label>
                                <input type="password" class="form-control" id="password" name="account[password]" 
                                       placeholder="Enter new password">
                            </div>
                            <div class="col-md-4">
                                <label for="confirmPassword" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="confirmPassword" name="account[password_confirmation]" 
                                       placeholder="Confirm new password">
                            </div>
                        </div>
                        
                        <div class="row mt-4">
                            <div class="col-12 d-flex justify-content-center align-items-center">
                                <button type="submit" class="btn btn-success btn-lg d-flex align-items-center">
                                    <i class='bi bi-floppy me-2' style="font-size: 24px;"></i>
                                    Update Care Manager
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
                    <p>Care Manager has been successfully saved!</p>
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
        document.querySelector('form[action="{{ route('admin.caremanagers.update', $caremanager->id) }}"]')
        .addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent only the main form submission
            
            // Show the success modal
            const successModal = new bootstrap.Modal(document.getElementById('saveSuccessModal'));
            successModal.show();
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
        //     filterDropdown('municipalityInput', 'municipalityDropdown');
        // });
        function restrictToNumbers(input) {
            input.value = input.value.replace(/[^0-9]/g, '');
        }

        function validateName(input) {
            const pattern = /^[A-ZÑ][a-zA-ZÑñ\'\.]*(?:-[a-zA-ZÑñ\'\.]+)?(?:(?: (?:[A-ZÑ][a-zA-ZÑñ\'\.]*|(?:de|la|del|los|las|von|van|der|den|di|le|da|do|dos|el|al|bin|binti|ibn|[a-z]))(?:-[a-zA-ZÑñ\'\.]+)?)+)?$/;
            
            if (!pattern.test(input.value)) {
                input.setCustomValidity("Please enter a valid name format. Names must start with an uppercase letter. Compound names like 'de la Cruz', names with enye (Ñ), apostrophes, and periods are allowed.");
            } else {
                input.setCustomValidity("");
            }
        }
    </script>

    <script>
    document.querySelector('form').addEventListener('submit', function (e) {
        // Always prevent the default form submission first
        e.preventDefault();
        
        // Check for file size issues first
        let fileSizeValid = true;
        const MAX_SIZES = {
            'caremanagerPhoto': 10 * 1024 * 1024, // 10MB
            'governmentID': 10 * 1024 * 1024,     // 10MB
            'resume': 5 * 1024 * 1024            // 5MB
        };
        
        document.querySelectorAll('input[type="file"]').forEach(input => {
            if (input.files.length > 0) {
                const file = input.files[0];
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
                    return;
                }
            }
        });
        
        if (!fileSizeValid) {
            return;
        }
        
        // Continue with existing validation logic
        if (!document.querySelector('.alert-danger')) {
            // No validation errors, show success modal
            const successModal = new bootstrap.Modal(document.getElementById('saveSuccessModal'));
            const form = this;
            
            // Mark form as validated to prevent double-checking file sizes
            form.dataset.validated = 'true';
            
            // Show modal
            successModal.show();
            
            // Listen for modal hidden event
            document.getElementById('saveSuccessModal').addEventListener('hidden.bs.modal', function onModalHidden() {
                document.getElementById('saveSuccessModal').removeEventListener('hidden.bs.modal', onModalHidden);
                form.submit();
            });
            
            // Add a button click handler for the OK button
            document.querySelector('#saveSuccessModal .btn-primary').addEventListener('click', function() {
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
            'caremanagerPhoto': 10 * 1024 * 1024, // 10MB
            'governmentID': 10 * 1024 * 1024,     // 10MB
            'resume': 5 * 1024 * 1024            // 5MB
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
            
            // If no validation errors, let the original form handler continue
            return true;
        }, true); // Use capturing phase to run before other handlers
    });
    </script>
</body>
</html>