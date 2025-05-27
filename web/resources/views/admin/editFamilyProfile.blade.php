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

    @include('components.adminNavbar')
    @include('components.adminSidebar')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('admin.families.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-bar-left"></i> Back
                </a>
                <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">EDIT FAMILY MEMBER</div>
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
                <form action="{{ route('admin.families.update', $familyMember->family_member_id) }}" method="POST" enctype="multipart/form-data">
                        @csrf <!-- Include CSRF token for security -->
                        @method('PUT')
                        <!-- Row 1: Personal Details -->
                        <div class="row mb-1 mt-3">
                            <div class="col-12">
                                <h5 class="text-start">Personal Details</h5> <!-- Row Title -->
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-3 relative">
                                <label for="firstName" class="form-label">First Name<label style="color:red;"> * </label></label>
                                <input type="text" class="form-control" id="firstName" name="first_name" 
                                    placeholder="Enter first name" 
                                    value="{{ old('first_name', $familyMember->first_name) }}"
                                    required >
                                    
                            </div>
                            <div class="col-md-3 relative">
                                <label for="lastName" class="form-label">Last Name<label style="color:red;"> * </label></label>
                                <input type="text" class="form-control" id="lastName" name="last_name" 
                                    placeholder="Enter last name" 
                                    value="{{ old('last_name', $familyMember->last_name) }}"
                                    required >
                                   
                            </div>
                            <div class="col-md-3 relative">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="" disabled>Select gender</option>
                                    <option value="Male" {{ old('gender', $familyMember->gender) == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender', $familyMember->gender) == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender', $familyMember->gender) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-3 relative">
                                <label for="birthDate" class="form-label">Birthday<label style="color:red;"> * </label></label>
                                <input type="date" class="form-control" id="birthDate" name="birth_date" 
                                    value="{{ old('birth_date', $familyMember->birthday) }}" required onkeydown="return true">
                            </div>
                        </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-3 relative">
                                <label for="mobileNumber" class="form-label">Mobile Number<label style="color:red;"> * </label></label>
                                <div class="input-group">
                                    <span class="input-group-text">+63</span>
                                    <input type="text" class="form-control" id="mobileNumber" name="mobile_number" 
                                        value="{{ old('mobile_number', substr($familyMember->mobile, 3)) }}" 
                                        placeholder="Enter mobile number" maxlength="11" required oninput="restrictToNumbers(this)" 
                                        title="Must be 10 or 11digits.">
                                </div>
                            </div>
                            <div class="col-md-3 relative">
                                <label for="landlineNumber" class="form-label">Landline Number</label>
                                <input type="text" class="form-control" id="landlineNumber" name="landline_number" 
                                    value="{{ old('landline_number', $familyMember->landline) }}" 
                                    placeholder="Enter Landline number" maxlength="10" oninput="restrictToNumbers(this)" 
                                    title="Must be between 7 and 10 digits.">
                            </div>
                            <div class="col-md-3 relative">
                                <label for="personalEmail" class="form-label">Personal Email Address<label style="color:red;"> * </label></label>
                                <input type="email" class="form-control" id="personalEmail" name="personal_email" 
                                    value="{{ old('personal_email', $familyMember->email) }}"
                                    placeholder="Enter personal email" required pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" 
                                    title="Enter a valid email address" oninput="validateEmail(this)">
                            </div>
                            <div class="col-md-3 relative">
                                <label for="familyPhoto" class="form-label">Profile Picture</label>
                                <input type="file" class="form-control" id="familyPhoto" name="family_photo" accept="image/png, image/jpeg">
                                <small class="text-danger">Maximum file size: 7MB</small>   
                                @if($familyMember->photo)
                                        <div class="mt-1">
                                            <small class="text-muted" title="{{ basename($familyMember->photo) }}">
                                                Current file: {{ strlen(basename($familyMember->photo)) > 30 ? substr(basename($familyMember->photo), 0, 30) . '...' : basename($familyMember->photo) }}
                                            </small>
                                            <img src="{{ asset('storage/' . $familyMember->photo) }}" class="img-thumbnail mt-1" style="max-height: 100px;" alt="Current photo">
                                        </div>
                                    @else
                                        <small class="text-muted">No file uploaded</small>
                                    @endif
                            </div>
                        </div>
                        <div class="row mb-1">
                            <!-- Change to dynamic -->
                            <div class="col-md-3 relative">
                            <label for="relatedBeneficiary" class="form-label">Related Beneficiary<label style="color:red;"> * </label></label>
                            <select class="form-select" id="relatedBeneficiary" name="relatedBeneficiary" required>
                                <option value="" disabled>Select a beneficiary</option>
                                @foreach ($beneficiaries as $beneficiary)
                                    <option value="{{ $beneficiary->beneficiary_id }}" 
                                        {{ old('relatedBeneficiary', $familyMember->related_beneficiary_id) == $beneficiary->beneficiary_id ? 'selected' : '' }}>
                                        {{ $beneficiary->first_name }} {{ $beneficiary->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            </div>
                            <div class="col-md-3 relative">
                                <label for="relationToBeneficiary" class="form-label">Relation to Beneficiary<label style="color:red;"> * </label></label>
                                <select class="form-select" id="relationToBeneficiary" name="relation_to_beneficiary" required>
                                    <option value="" disabled>Select relation</option>
                                    <option value="Son" {{ old('relation_to_beneficiary', $familyMember->relation_to_beneficiary) == 'Son' ? 'selected' : '' }}>Son</option>
                                    <option value="Daughter" {{ old('relation_to_beneficiary', $familyMember->relation_to_beneficiary) == 'Daughter' ? 'selected' : '' }}>Daughter</option>
                                    <option value="Spouse" {{ old('relation_to_beneficiary', $familyMember->relation_to_beneficiary) == 'Spouse' ? 'selected' : '' }}>Spouse</option>
                                    <option value="Sibling" {{ old('relation_to_beneficiary', $familyMember->relation_to_beneficiary) == 'Sibling' ? 'selected' : '' }}>Sibling</option>
                                    <option value="Grandchild" {{ old('relation_to_beneficiary', $familyMember->relation_to_beneficiary) == 'Grandchild' ? 'selected' : '' }}>Grandchild</option>
                                    <option value="Other" {{ old('relation_to_beneficiary', $familyMember->relation_to_beneficiary) == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3 relative">
                                <label for="isPrimaryCaregiver" class="form-label">Is Primary Caregiver?<label style="color:red;"> * </label></label>
                                <select class="form-select" id="isPrimaryCaregiver" name="is_primary_caregiver" required>
                                    <option value="" disabled>Select an option</option>
                                    <option value="1" {{ old('is_primary_caregiver', $familyMember->is_primary_caregiver ? '1' : '0') == '1' ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ old('is_primary_caregiver', $familyMember->is_primary_caregiver ? '1' : '0') == '0' ? 'selected' : '' }}>No</option>
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
                                <label for="addressDetails" class="form-label">House No., Street, Subdivision, Barangay, City, Province<label style="color:red;"> * </label></label>
                                <textarea class="form-control" id="addressDetails" name="address_details" 
                                    placeholder="Enter complete current address" 
                                    rows="2" required pattern="^[a-zA-Z0-9\s,.-]+$" 
                                    title="Only alphanumeric characters, spaces, commas, periods, and hyphens are allowed."
                                    oninput="validateAddress(this)">{{ old('address_details', $familyMember->street_address) }}</textarea>
                            </div>
                        </div>
                        <!-- Account Registration -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Family Portal Account Registration</h5> <!-- Row Title -->
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <h5 class="text-start" styl="font-weight: normal;"><strong>Note:* </strong>Your Family Portal account will be connected to your <strong>Related Beneficiary's account</strong></h5> <!-- Row Title -->
                            </div>
                        </div>

                        
                        <div class="row mt-4">
                            <div class="col-12 d-flex justify-content-center align-items-center">
                                <button type="submit" class="btn btn-success btn-lg d-flex align-items-center" id="saveBeneficiaryButton">
                                    <i class='bi bi-floppy me-2' style="font-size: 24px;"></i>
                                    Update Family Member
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
                    <p>Family Member has been successfully saved!</p>
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
    document.querySelector('form').addEventListener('submit', function (e) {
        // Always prevent the default form submission first
        e.preventDefault();
        
        // Check for file size validation errors first
        const familyPhotoInput = document.getElementById('familyPhoto');
        if (familyPhotoInput && familyPhotoInput.files.length > 0) {
            const MAX_FILE_SIZE = 7 * 1024 * 1024; // 7MB in bytes
            const file = familyPhotoInput.files[0];
            
            if (file.size > MAX_FILE_SIZE) {
                const fileSizeMB = (file.size / (1024 * 1024)).toFixed(1);
                
                // Show file size error
                const fileSizeErrorModal = new bootstrap.Modal(document.getElementById('fileSizeErrorModal'));
                const fileSizeErrorMessage = document.getElementById('fileSizeErrorMessage');
                
                fileSizeErrorMessage.innerHTML = `
                    <strong>Form submission failed</strong><br>
                    Profile Picture (${fileSizeMB}MB) exceeds the maximum size of 7MB.<br>
                    Please select a smaller file or compress your existing file.
                `;
                fileSizeErrorModal.show();
                return false;
            }
        }
        
        // Continue with your existing validation and success modal
        if (!document.querySelector('.alert-danger')) {
            // No validation errors, show success modal
            const successModal = new bootstrap.Modal(document.getElementById('saveSuccessModal'));
            const form = this;
            
            // Show modal
            successModal.show();
            
            // Listen for modal hidden event
            document.getElementById('saveSuccessModal').addEventListener('hidden.bs.modal', function onModalHidden() {
                document.getElementById('saveSuccessModal').removeEventListener('hidden.bs.modal', onModalHidden);
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
    document.addEventListener('DOMContentLoaded', function () {
        // Function to filter dropdown items
        function filterDropdown(inputId, dropdownId) {
            const input = document.getElementById(inputId);
            const dropdown = document.getElementById(dropdownId);
            const items = dropdown.querySelectorAll('.dropdown-item');

            // Filter dropdown items based on user input
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

            // Hide dropdown when input loses focus
            input.addEventListener('blur', function () {
                setTimeout(() => dropdown.style.display = 'none', 200);
            });

            // Show dropdown when input gains focus
            input.addEventListener('focus', function () {
                dropdown.style.display = 'block';
            });

            // Handle item selection
            items.forEach(item => {
                item.addEventListener('click', function (e) {
                    e.preventDefault();
                    input.value = item.textContent; // Update the input field with the selected item's text
                    const hiddenInputId = inputId.replace('Input', ''); // Derive the hidden input ID
                    const hiddenInput = document.getElementById(hiddenInputId);
                    if (hiddenInput) {
                        hiddenInput.value = item.getAttribute('data-value'); // Update the hidden input with the selected item's value
                    }
                    dropdown.style.display = 'none'; // Hide the dropdown
                });
            });
        }

        // Initialize filtering for each dropdown
        // filterDropdown('genderInput', 'genderDropdown');
        filterDropdown('relatedBeneficiaryInput', 'relatedBeneficiaryDropdown');

        // Parse the JSON data passed from the controller
            const beneficiaries = JSON.parse(@json($beneficiaries));

        // Get the dropdown element
        const relatedBeneficiaryDropdown = document.getElementById('relatedBeneficiary');

        // Populate the dropdown with beneficiaries
        beneficiaries.forEach(beneficiary => {
            const option = document.createElement('option');
            option.value = beneficiary.beneficiary_id; // Set the value to the beneficiary ID
            option.textContent = `${beneficiary.first_name} ${beneficiary.last_name}`; // Display full name
            relatedBeneficiaryDropdown.appendChild(option);
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
    // document.addEventListener('DOMContentLoaded', function () {
    //     // Parse the JSON data passed from the controller
    //     const beneficiaries = {!! $beneficiaries !!};

    //     // Get the dropdown element
    //     const relatedBeneficiaryDropdown = document.getElementById('relatedBeneficiary');

    //     // Populate the dropdown with beneficiaries
    //     beneficiaries.forEach(beneficiary => {
    //         const option = document.createElement('option');
    //         option.value = beneficiary.beneficiary_id; // Set the value to the beneficiary ID
    //         option.textContent = `${beneficiary.first_name} ${beneficiary.last_name}`; // Display full name
    //         relatedBeneficiaryDropdown.appendChild(option);
    //     });
    // });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Maximum file size - 7MB for profile pictures
    const MAX_FILE_SIZE = 7 * 1024 * 1024; // 7MB in bytes
    
    // Initialize the modal
    const fileSizeErrorModal = new bootstrap.Modal(document.getElementById('fileSizeErrorModal'));
    const fileSizeErrorMessage = document.getElementById('fileSizeErrorMessage');
    
    // Add file size validation to profile picture input
    const familyPhotoInput = document.getElementById('familyPhoto');
    if (familyPhotoInput) {
        familyPhotoInput.addEventListener('change', function() {
            if (this.files.length > 0) {
                const file = this.files[0];
                
                if (file.size > MAX_FILE_SIZE) {
                    const fileSizeMB = (file.size / (1024 * 1024)).toFixed(1);
                    
                    // Set error message and show modal
                    fileSizeErrorMessage.innerHTML = `
                        <strong>Profile Picture</strong> file is too large (${fileSizeMB}MB).<br>
                        Maximum allowed size is 7MB.<br>
                        Please select a smaller file or compress your existing file.
                    `;
                    fileSizeErrorModal.show();
                    
                    // Reset the file input
                    this.value = '';
                }
            }
        });
    }
    
    // Add form submission check to prevent large file uploads
    document.querySelector('form').addEventListener('submit', function(e) {
        // Don't interfere if there's already a submission handler for the success modal
        if (this.dataset.validated === 'true') {
            return true;
        }
        
        // Check file size before submission
        if (familyPhotoInput && familyPhotoInput.files.length > 0) {
            const file = familyPhotoInput.files[0];
            
            if (file.size > MAX_FILE_SIZE) {
                e.preventDefault();
                
                const fileSizeMB = (file.size / (1024 * 1024)).toFixed(1);
                
                // Set error message and show modal
                fileSizeErrorMessage.innerHTML = `
                    <strong>Form submission failed</strong><br>
                    Profile Picture (${fileSizeMB}MB) exceeds the maximum size of 7MB.<br>
                    Please select a smaller file or compress your existing file.
                `;
                fileSizeErrorModal.show();
                return false;
            }
        }
        
        // Mark as validated so we don't check again
        this.dataset.validated = 'true';
        return true;
    });
});
</script>

</body>
</html>