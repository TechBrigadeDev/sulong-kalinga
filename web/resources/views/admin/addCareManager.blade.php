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
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('admin.caremanagers.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-bar-left"></i> Back
                </a>
                <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">ADD CARE MANAGER</div>
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
                    <form action="{{ route('admin.caremanagers.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf <!-- Include CSRF token for security -->
                        <!-- Row 1: Personal Details -->
                        <div class="row mb-1 mt-3">
                            <div class="col-12">
                                <h5 class="text-start">Personal Details</h5> <!-- Row Title -->
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-3">
                                <label for="firstName" class="form-label">First Name<label style="color:red;"> * </label></label>
                                <input type="text" class="form-control" id="firstName" name="first_name" 
                                        value="{{ old('first_name') }}"
                                        placeholder="Enter first name" 
                                        required 
                                        oninput="validateName(this)" 
                                        pattern="^[A-Z][a-zA-Z]*(?:-[a-zA-Z]+)?(?: [a-zA-Z]+(?:-[a-zA-Z]+)*)*$" 
                                        title="First letter must be uppercase. Only alphabets, single spaces, and hyphens are allowed. Single-letter words are not allowed.">
                            </div>
                            <div class="col-md-3">
                                <label for="lastName" class="form-label">Last Name<label style="color:red;"> * </label></label>
                                <input type="text" class="form-control" id="lastName" name="last_name" 
                                        value="{{ old('last_name') }}"
                                        placeholder="Enter last name" 
                                        required 
                                        oninput="validateName(this)" 
                                        pattern="^[A-Z][a-zA-Z]*(?:-[a-zA-Z]+)?(?: [a-zA-Z]+(?:-[a-zA-Z]+)*)*$" 
                                        title="First letter must be uppercase. Only alphabets, single spaces, and hyphens are allowed. Single-letter words are not allowed.">
                            </div>
                            <div class="col-md-3">
                                <label for="birthDate" class="form-label">Birthday<label style="color:red;"> * </label></label>
                                <input type="date" class="form-control" id="birthDate" name="birth_date" value="{{ old('birth_date') }}" required onkeydown="return true">
                            </div>
                            <div class="col-md-3">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select gender</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="civilStatus" class="form-label">Civil Status</label>
                                <select class="form-select" id="civilStatus" name="civil_status">
                                    <option value="" disabled {{ old('civil_status') ? '' : 'selected' }}>Select civil status</option>
                                    <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>Single</option>
                                    <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>Married</option>
                                    <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                    <option value="Divorced" {{ old('civil_status') == 'Divorced' ? 'selected' : '' }}>Divorced</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="religion" class="form-label">Religion</label>
                                <input type="text" class="form-control" id="religion" name="religion" value="{{ old('religion') }}" placeholder="Enter religion" pattern="^[a-zA-Z\s]*$" title="Only alphabets and spaces are allowed.">
                            </div>
                            <div class="col-md-3">
                                <label for="nationality" class="form-label">Nationality</label>
                                <input type="text" class="form-control" id="nationality" name="nationality" value="{{ old('nationality') }}" placeholder="Enter nationality" pattern="^[a-zA-Z\s]*$" title="Only alphabets and spaces are allowed.">
                            </div>
                            <div class="col-md-3">
                                <label for="educationalBackground" class="form-label">Educational Background</label>
                                <select class="form-select" id="educationalBackground" name="educational_background">
                                    <option value="" disabled {{ old('educational_background') ? '' : 'selected' }}>Select educational background</option>
                                    <option value="College" {{ old('educational_background') == 'College' ? 'selected' : '' }}>College</option>
                                    <option value="Highschool" {{ old('educational_background') == 'Highschool' ? 'selected' : '' }}>High School</option>
                                    <option value="Doctorate" {{ old('educational_background') == 'Doctorate' ? 'selected' : '' }}>Doctorate</option>
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
                                rows="2" 
                                required 
                                pattern="^[a-zA-Z0-9\s,.-]+$" 
                                title="Only alphanumeric characters, spaces, commas, periods, and hyphens are allowed."
                                oninput="validateAddress(this)">{{ old('address_details') }}</textarea>
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
                                <label for="personalEmail" class="form-label">Personal Email Address<label style="color:red;"> * </label></label>
                                <input type="email" class="form-control" id="personalEmail" name="personal_email" 
                                       placeholder="Enter personal email" 
                                       value="{{ old('personal_email') }}"
                                       required 
                                       pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" 
                                       title="Enter a valid email address (e.g., example@domain.com)" 
                                       oninput="validateEmail(this)">
                            </div>
                            <div class="col-md-4">
                                <label for="mobileNumber" class="form-label">Mobile Number<label style="color:red;"> * </label></label>
                                <div class="input-group">
                                    <span class="input-group-text">+63</span>
                                    <input type="text" class="form-control" id="mobileNumber" name="mobile_number" value="{{ old('mobile_number') }}" placeholder="Enter mobile number" maxlength="11" required oninput="restrictToNumbers(this)" title="Must be 10 or 11digits.">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="landlineNumber" class="form-label">Landline Number</label>
                                <input type="text" class="form-control" id="landlineNumber" name="landline_number" value="{{ old('landline_number') }}" placeholder="Enter Landline number" maxlength="10" oninput="restrictToNumbers(this)" title="Must be between 7 and 10 digits.">
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
                                <label for="caremanagerPhoto" class="form-label">Care Manager Photo</label>
                                <input type="file" class="form-control" id="caremanagerPhoto" name="caremanager_photo" accept="image/png, image/jpeg" capture="user">
                                @if($errors->any())
                                <small class="text-danger">Note: You need to select the file again after a validation error.</small>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="governmentID" class="form-label">Government Issued ID</label>
                                <input type="file" class="form-control" id="governmentID" name="government_ID" accept=".jpg,.png">
                                @if($errors->any())
                                <small class="text-danger">Note: You need to select the file again after a validation error.</small>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="resume" class="form-label">Resume / CV</label>
                                <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx">
                                @if($errors->any())
                                <small class="text-danger">Note: You need to select the file again after a validation error.</small>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <label for="sssID" class="form-label">SSS ID</label>
                                <input type="text" class="form-control" id="sssID" name="sss_ID" value="{{ old('sss_ID') }}" placeholder="Enter SSS ID" maxlength="10" oninput="restrictToNumbers(this)" title="Must be 10 digits.">
                            </div>
                            <div class="col-md-4">
                                <label for="philhealthID" class="form-label">PhilHealth ID</label>
                                <input type="text" class="form-control" id="philhealthID" name="philhealth_ID" value="{{ old('philhealth_ID') }}" placeholder="Enter PhilHealth ID" maxlength="12" oninput="restrictToNumbers(this)" title="Must be 12 digits.">
                            </div>
                            <div class="col-md-4">
                                <label for="pagibigID" class="form-label">Pag-Ibig ID</label>
                                <input type="text" class="form-control" id="pagibigID" name="pagibig_ID" value="{{ old('pagibig_ID') }}" placeholder="Enter Pag-Ibig ID" maxlength="12" oninput="restrictToNumbers(this)" title="Must be 12 digits.">
                            </div>
                        </div>


                        <hr class="my-4">
                        <!-- Account Registration -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">Care Manager Account Registration</h5> <!-- Row Title -->
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <label for="email" class="form-label">Work Email Address<label style="color:red;"> * </label></label>
                                <input type="email" class="form-control" id="email" name="account[email]" 
                                       value="{{ old('account.email') }}"
                                       placeholder="Enter work email" 
                                       required 
                                       pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" 
                                       title="Enter a valid email address (e.g., example@domain.com)" 
                                       oninput="validateEmail(this)">
                            </div>
                            <div class="col-md-4">
                                <label for="password" class="form-label">Password<label style="color:red;"> * </label></label>
                                <input type="password" class="form-control" id="password" name="account[password]" placeholder="Enter password" required>
                            </div>
                            <div class="col-md-4">
                                <label for="confirmPassword" class="form-label">Confirm Password<label style="color:red;"> * </label></label>
                                <input type="password" class="form-control" id="confirmPassword" name="account[password_confirmation]" placeholder="Confirm password" required>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4">
                            <label for="municipality" class="form-label">Municipality<label style="color:red;"> * </label></label>
                            <select class="form-select" id="municipality" name="municipality" required>
                                <option value="" disabled {{ old('municipality') ? '' : 'selected' }}>Select municipality</option>
                                @foreach ($municipalities as $municipality)
                                    <option value="{{ $municipality->municipality_id }}" {{ old('municipality') == $municipality->municipality_id ? 'selected' : '' }}>{{ $municipality->municipality_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        </div>                        
                        <div class="row mt-4">
                            <div class="col-12 d-flex justify-content-center align-items-center">
                                <button type="submit" class="btn btn-success btn-lg d-flex align-items-center">
                                    <i class="bi bi-floppy" style="padding-right: 10px;"></i>
                                    Save Care Manager
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
    <script src=" {{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            // Always prevent the default form submission first
            e.preventDefault();
            
            // Check if there are validation errors
            if (!document.querySelector('.alert-danger')) {
                // No validation errors, show success modal
                const successModal = new bootstrap.Modal(document.getElementById('saveSuccessModal'));
                const form = this;
                
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
<!-- <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent the default form submission

            // Show the success modal
            const successModal = new bootstrap.Modal(document.getElementById('saveSuccessModal'));
            successModal.show();
        });
    </script> -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Function to filter dropdown items
            // function filterDropdown(inputId, dropdownId) {
            //     const input = document.getElementById(inputId);
            //     const dropdown = document.getElementById(dropdownId);
            //     const items = dropdown.querySelectorAll('.dropdown-item');

            //     input.addEventListener('input', function () {
            //         const filter = input.value.toLowerCase();
            //         let hasVisibleItems = false;

            //         items.forEach(item => {
            //             if (item.textContent.toLowerCase().includes(filter)) {
            //                 item.style.display = 'block';
            //                 hasVisibleItems = true;
            //             } else {
            //                 item.style.display = 'none';
            //             }
            //         });
            //         dropdown.style.display = hasVisibleItems ? 'block' : 'none';
            //     });
            //     input.addEventListener('blur', function () {
            //         setTimeout(() => dropdown.style.display = 'none', 200);
            //     });
            //     input.addEventListener('focus', function () {
            //         dropdown.style.display = 'block';
            //     });

            //     // Handle item selection
            //     items.forEach(item => {
            //         item.addEventListener('click', function (e) {
            //             e.preventDefault();
            //             input.value = item.textContent;
            //             document.getElementById(inputId.replace('Input', '')).value = item.getAttribute('data-value');
            //             dropdown.style.display = 'none';
            //         });
            //     });
            // }

            // Initialize filtering for each dropdown
            // filterDropdown('civilStatusInput', 'civilStatusDropdown');
            // filterDropdown('genderInput', 'genderDropdown');
            // filterDropdown('educationalBackgroundInput', 'educationalBackgroundDropdown');
            // filterDropdown('Organization_RolesInput', 'Organization_RolesDropdown');
            // filterDropdown('municipalityInput', 'municipalityDropdown');
        });

        function validateMobileNumber(input) {
            // Remove any non-numeric characters
            input.value = input.value.replace(/[^0-9]/g, '');

            // Limit the input to 11 digits
            if (input.value.length > 11) {
                input.value = input.value.slice(0, 11);
            }
        }

        document.querySelectorAll('.dropdown-item').forEach(item => {
            item.addEventListener('click', function (e) {
                e.preventDefault();
                const input = this.closest('.position-relative').querySelector('input[type="text"]');
                const hiddenInput = this.closest('.position-relative').querySelector('input[type="hidden"]');
                input.value = this.textContent;
                hiddenInput.value = this.getAttribute('data-value');
            });
        });

        document.querySelectorAll('input[type="text"]').forEach(input => {
            input.addEventListener('input', function () {
                this.value = this.value.replace(/[^a-zA-Z0-9\s]/g, ''); // Remove special characters
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

        // Validate names to allow only one hyphen per word and not at the end
        function validateName(input) {
            // NOT WORKING AS INTENDED
            // input.value = input.value.replace(/[^a-zA-Z-]/g, ''); // Remove invalid characters
            // input.value = input.value.replace(/-{2,}/g, '-'); // Prevent multiple consecutive hyphens
            // input.value = input.value.replace(/^-|-$/g, ''); // Remove hyphen at the start or end
            // const words = input.value.split(' ');
            // input.value = words.map(word => word.replace(/-/g, (match, offset) => offset === word.indexOf('-') ? '-' : '')).join(' ');
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
        // document.addEventListener('DOMContentLoaded', function () {
        //     const municipalityInput = document.getElementById('municipalityInput');
        //     const municipalityHiddenInput = document.getElementById('municipality');
        //     const municipalityDropdown = document.getElementById('municipalityDropdown');

        //     // Add click event listeners to dropdown items
        //     municipalityDropdown.querySelectorAll('.dropdown-item').forEach(item => {
        //         item.addEventListener('click', function (e) {
        //             e.preventDefault();
        //             municipalityInput.value = this.textContent; // Set the visible input value
        //             municipalityHiddenInput.value = this.getAttribute('data-value'); // Set the hidden input value
        //         });
        //     });
        // }); FOR MUNICIPALITY DROPDOWN IF DYNAMIC WHEN FIXED
        document.addEventListener('DOMContentLoaded', function () {
            const municipalityInput = document.getElementById('municipalityInput');
            const municipalityHiddenInput = document.getElementById('municipality');
            const municipalityDropdown = document.getElementById('municipalityDropdown');

            // Add click event listeners to dropdown items
            municipalityDropdown.querySelectorAll('.dropdown-item').forEach(item => {
                item.addEventListener('click', function (e) {
                    e.preventDefault();
                    municipalityInput.value = this.textContent; // Set the visible input value
                    municipalityHiddenInput.value = this.getAttribute('data-value'); // Set the hidden input value
                });
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
    <?php
    // if ($request->hasFile('caremanager_photo')) {
    //     dd($request->file('caremanager_photo')->getClientMimeType());
    // }
    ?>
</body>
</html>