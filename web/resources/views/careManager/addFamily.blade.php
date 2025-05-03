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
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('care-manager.families.index') }}" class="btn btn-secondary">
                    <i class="bx bx-arrow-back"></i> Back
                </a>
                <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">ADD FAMILY MEMBER</div>
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
                    <form action="{{ route('care-manager.families.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf <!-- Include CSRF token for security -->
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
                                        value="{{ old('first_name') }}"
                                        required 
                                        oninput="validateName(this)" 
                                        pattern="^[A-Z][a-zA-Z]*(?:-[a-zA-Z]+)?(?: [a-zA-Z]+(?:-[a-zA-Z]+)*)*$" 
                                        title="First letter must be uppercase. Only alphabets, single spaces, and hyphens are allowed. Single-letter words are not allowed.">
                            </div>
                            <div class="col-md-3 relative">
                                <label for="lastName" class="form-label">Last Name<label style="color:red;"> * </label></label>
                                <input type="text" class="form-control" id="lastName" name="last_name" 
                                        placeholder="Enter last name" 
                                        value="{{ old('last_name') }}"
                                        required 
                                        oninput="validateName(this)" 
                                        pattern="^[A-Z][a-zA-Z]*(?:-[a-zA-Z]+)?(?: [a-zA-Z]+(?:-[a-zA-Z]+)*)*$" 
                                        title="First letter must be uppercase. Only alphabets, single spaces, and hyphens are allowed. Single-letter words are not allowed.">
                            </div>
                            <div class="col-md-3 relative">
                                <label for="gender" class="form-label">Gender</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select gender</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>Female</option>
                                    <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            <div class="col-md-3 relative">
                                <label for="birthDate" class="form-label">Birthday<label style="color:red;"> * </label></label>
                                <input type="date" class="form-control" id="birthDate" name="birth_date" value="{{ old('birth_date') }}" required onkeydown="return true">
                            </div>
                        </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-3 relative">
                                <label for="mobileNumber" class="form-label">Mobile Number<label style="color:red;"> * </label></label>
                                <div class="input-group">
                                    <span class="input-group-text">+63</span>
                                    <input type="text" class="form-control" id="mobileNumber" name="mobile_number" value="{{ old('mobile_number') }}" placeholder="Enter mobile number" maxlength="11" required oninput="restrictToNumbers(this)" title="Must be 10 or 11digits.">
                                </div>
                            </div>
                            <div class="col-md-3 relative">
                                <label for="landlineNumber" class="form-label">Landline Number</label>
                                <input type="text" class="form-control" id="landlineNumber" name="landline_number" value="{{ old('landline_number') }}" placeholder="Enter Landline number" maxlength="10" oninput="restrictToNumbers(this)" title="Must be between 7 and 10 digits.">
                            </div>
                            <div class="col-md-3 relative">
                                <label for="personalEmail" class="form-label">Personal Email Address<label style="color:red;"> * </label></label>
                                <input type="email" class="form-control" id="personalEmail" name="personal_email" 
                                       value="{{ old('personal_email') }}"
                                       placeholder="Enter personal email" 
                                       required 
                                       pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" 
                                       title="Enter a valid email address (e.g., example@domain.com)" 
                                       oninput="validateEmail(this)">
                            </div>
                            <div class="col-md-3 relative">
                                <label for="familyPhoto" class="form-label">Profile Picture</label>
                                <input type="file" class="form-control" id="familyPhoto" name="family_photo" accept="image/png, image/jpeg" capture="user">
                                @if($errors->any())
                                <small class="text-danger">Note: You need to select the file again after a validation error.</small>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-1">
                            <!-- Change to dynamic -->
                            <div class="col-md-3 relative">
                            <label for="relatedBeneficiary" class="form-label">Related Beneficiary<label style="color:red;"> * </label></label>
                            <select class="form-select" id="relatedBeneficiary" name="relatedBeneficiary" required>
                                <option value="" disabled {{ old('relatedBeneficiary') ? '' : 'selected' }}>Select a beneficiary</option>
                                @foreach ($beneficiaries as $beneficiary)
                                    <option value="{{ $beneficiary->beneficiary_id }}" {{ old('relatedBeneficiary') == $beneficiary->beneficiary_id ? 'selected' : '' }}>
                                        {{ $beneficiary->first_name }} {{ $beneficiary->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            </div>
                            <div class="col-md-3 relative">
                                <label for="relationToBeneficiary" class="form-label">Relation to Beneficiary<label style="color:red;"> * </label></label>
                                <select class="form-select" id="relationToBeneficiary" name="relation_to_beneficiary" required>
                                    <option value="" disabled {{ old('relation_to_beneficiary') ? '' : 'selected' }}>Select relation</option>
                                    <option value="Son" {{ old('relation_to_beneficiary') == 'Son' ? 'selected' : '' }}>Son</option>
                                    <option value="Daughter" {{ old('relation_to_beneficiary') == 'Daughter' ? 'selected' : '' }}>Daughter</option>
                                    <option value="Spouse" {{ old('relation_to_beneficiary') == 'Spouse' ? 'selected' : '' }}>Spouse</option>
                                    <option value="Sibling" {{ old('relation_to_beneficiary') == 'Sibling' ? 'selected' : '' }}>Sibling</option>
                                    <option value="Grandchild" {{ old('relation_to_beneficiary') == 'Grandchild' ? 'selected' : '' }}>Grandchild</option>
                                    <option value="Other" {{ old('relation_to_beneficiary') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                            
                            <div class="col-md-3 relative">
                                <label for="isPrimaryCaregiver" class="form-label">Is Primary Caregiver?<label style="color:red;"> * </label></label>
                                <select class="form-select" id="isPrimaryCaregiver" name="is_primary_caregiver" required>
                                    <option value="" disabled {{ old('is_primary_caregiver') !== null ? '' : 'selected' }}>Select an option</option>
                                    <option value="1" {{ old('is_primary_caregiver') == '1' ? 'selected' : '' }}>Yes</option>
                                    <option value="0" {{ old('is_primary_caregiver') == '0' ? 'selected' : '' }}>No</option>
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
                                    <i class='bx bx-save me-2' style="font-size: 24px;"></i>
                                    Save Family Member
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
    <script src=" {{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
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

</body>
</html>