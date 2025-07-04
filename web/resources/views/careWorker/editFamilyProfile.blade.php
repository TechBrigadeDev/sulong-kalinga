<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Family | Care Worker</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon-white-16x16.png')}}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/addUsers.css') }}">
</head>
<body>
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp
    @include('components.careWorkerNavbar')
    @include('components.careWorkerSidebar')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <a href="{{ route('care-worker.families.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> {{ T::translate('Back', 'Bumalik') }}
                </a>
                <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">{{ T::translate('EDIT FAMILY MEMBER', 'I-EDIT ANG MIYEMBRO NG PAMILYA') }}</div>
            </div>
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if ($errors->any()))
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
                <form action="{{ route('care-worker.families.update', $familyMember->family_member_id) }}" method="POST" enctype="multipart/form-data">
                        @csrf <!-- Include CSRF token for security -->
                        @method('PUT')
                        <!-- Row 1: Personal Details -->
                        <div class="row mb-1 mt-3">
                            <div class="col-12">
                                <h5 class="text-start">{{ T::translate('Personal Details', 'Personal na Detalye') }}</h5> <!-- Row Title -->
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-3 relative">
                                <label for="firstName" class="form-label">{{ T::translate('First Name', 'Pangalan') }}<label style="color:red;"> * </label></label>
                                <input type="text" class="form-control" id="firstName" name="first_name" 
                                    placeholder="{{ T::translate('Enter first name', 'Ilagay ang pangalan') }}" 
                                    value="{{ old('first_name', $familyMember->first_name) }}"
                                    required >
                                    
                            </div>
                            <div class="col-md-3 relative">
                                <label for="lastName" class="form-label">{{ T::translate('Last Name', 'Apelyido') }}<label style="color:red;"> * </label></label>
                                <input type="text" class="form-control" id="lastName" name="last_name" 
                                    placeholder="{{ T::translate('Enter last name', 'Ilagay ang apelyido') }}" 
                                    value="{{ old('last_name', $familyMember->last_name) }}"
                                    required >
                                   
                            </div>
                            <div class="col-md-3 relative">
                                <label for="gender" class="form-label">{{ T::translate('Gender', 'Kasarian') }}</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="" disabled>{{ T::translate('Select gender', 'Pumili ng Kasarian') }}</option>
                                    <option value="Male" {{ old('gender', $familyMember->gender) == 'Male' ? 'selected' : '' }}>{{ T::translate('Male', 'Lalaki') }}</option>
                                    <option value="Female" {{ old('gender', $familyMember->gender) == 'Female' ? 'selected' : '' }}>{{ T::translate('Female', 'Babae') }}</option>
                                    <option value="Other" {{ old('gender', $familyMember->gender) == 'Other' ? 'selected' : '' }}>{{ T::translate('Other', 'Iba pa') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3 relative">
                                <label for="birthDate" class="form-label">{{ T::translate('Birthday', 'Kaarawan') }}<label style="color:red;"> * </label></label>
                                <input type="date" class="form-control" id="birthDate" name="birth_date" 
                                    value="{{ old('birth_date', $familyMember->birthday) }}" required onkeydown="return true">
                            </div>
                        </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-3 relative">
                                <label for="mobileNumber" class="form-label">{{ T::translate('Mobile Number', 'Numero sa Mobile') }}<label style="color:red;"> * </label></label>
                                <div class="input-group">
                                    <span class="input-group-text">+63</span>
                                    <input type="text" class="form-control" id="mobileNumber" name="mobile_number" 
                                        value="{{ old('mobile_number', substr($familyMember->mobile, 3)) }}" 
                                        placeholder="{{ T::translate('Enter mobile number', 'Ilagay ang numero ng mobile') }}" maxlength="11" required oninput="restrictToNumbers(this)" 
                                        title="{{ T::translate('Must be 10 or 11 digits.', 'Dapat ay 10 o 11 digits.') }}">
                                </div>
                            </div>
                            
                            <div class="col-md-6 relative">
                                <label for="familyPhoto" class="form-label">{{ T::translate('Profile Picture', 'Litrato sa Profile') }}</label>
                                <input type="file" class="form-control" id="familyPhoto" name="family_photo" accept="image/png, image/jpeg">
                                <small class="text-danger">{{ T::translate('Maximum file size: 7MB', 'Maximum na laki ng file: 7MB') }}</small>    
                                @if($familyMember->photo)
                                        <div class="mt-1">
                                            <small class="text-muted" title="{{ basename($familyMember->photo) }}">
                                                {{ T::translate('Current file', 'Kasalukuyang file') }}: {{ strlen(basename($familyMember->photo)) > 30 ? substr(basename($familyMember->photo), 0, 30) . '...' : basename($familyMember->photo) }}
                                            </small>
                                            <img src="{{ asset('storage/' . $familyMember->photo) }}" class="img-thumbnail mt-1" style="max-height: 100px;" alt="Current photo">
                                        </div>
                                    @else
                                        <small class="text-muted">{{ T::translate('No file uploaded', 'Walang file ang na-upload') }}</small>
                                    @endif
                            </div>
                            <div class="col-md-3 relative">
                                <label for="relatedBeneficiary" class="form-label">{{ T::translate('Related Beneficiary', 'Kaugnay na Benepisyaryo') }}<label style="color:red;"> * </label></label>
                                <select class="form-select" id="relatedBeneficiary" name="relatedBeneficiary" required>
                                    <option value="" disabled>{{ T::translate('Select a beneficiary', 'Pumili ng Benepisyaryo') }}</option>
                                    @foreach ($beneficiaries as $beneficiary)
                                        <option value="{{ $beneficiary->beneficiary_id }}" 
                                            {{ old('relatedBeneficiary', $familyMember->related_beneficiary_id) == $beneficiary->beneficiary_id ? 'selected' : '' }}>
                                            {{ $beneficiary->first_name }} {{ $beneficiary->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-1 me-0">
                            <!-- Change to dynamic -->
                            
                            <div class="col-md-5 relative">
                                <label for="relationToBeneficiary" class="form-label">{{ T::translate('Relation to Beneficiary', 'Relasyon sa Benepisyaryo') }}<label style="color:red;"> * </label></label>
                                <select class="form-select" id="relationToBeneficiary" name="relation_to_beneficiary" required>
                                    <option value="" disabled>{{ T::translate('Select relation', 'Pumili ng Relasyon') }}</option>
                                    <option value="Son" {{ old('relation_to_beneficiary', $familyMember->relation_to_beneficiary) == 'Son' ? 'selected' : '' }}>{{ T::translate('Son', 'Anak na Lalaki') }}</option>
                                    <option value="Daughter" {{ old('relation_to_beneficiary', $familyMember->relation_to_beneficiary) == 'Daughter' ? 'selected' : '' }}>{{ T::translate('Daughter', 'Anak na Babae') }}</option>
                                    <option value="Spouse" {{ old('relation_to_beneficiary', $familyMember->relation_to_beneficiary) == 'Spouse' ? 'selected' : '' }}>{{ T::translate('Spouse', 'Asawa') }}</option>
                                    <option value="Sibling" {{ old('relation_to_beneficiary', $familyMember->relation_to_beneficiary) == 'Sibling' ? 'selected' : '' }}>{{ T::translate('Sibling', 'Kapatid') }}</option>
                                    <option value="Grandchild" {{ old('relation_to_beneficiary', $familyMember->relation_to_beneficiary) == 'Grandchild' ? 'selected' : '' }}>{{ T::translate('Grandchild', 'Apo') }}</option>
                                    <option value="Other" {{ old('relation_to_beneficiary', $familyMember->relation_to_beneficiary) == 'Other' ? 'selected' : '' }}>{{ T::translate('Other', 'Iba pa') }}</option>
                                </select>
                            </div>
                            
                            <div class="col-md-7 relative">
                                <label for="isPrimaryCaregiver" class="form-label">{{ T::translate('Is Primary Caregiver?', 'Ay Pangunahing Tagapangalaga?') }}<label style="color:red;"> * </label></label>
                                <select class="form-select" id="isPrimaryCaregiver" name="is_primary_caregiver" required>
                                    <option value="" disabled>{{ T::translate('Select an option', 'Pumili ng Opsyon') }}</option>
                                    <option value="1" {{ old('is_primary_caregiver', $familyMember->is_primary_caregiver ? '1' : '0') == '1' ? 'selected' : '' }}>{{ T::translate('Yes', 'Oo') }}</option>
                                    <option value="0" {{ old('is_primary_caregiver', $familyMember->is_primary_caregiver ? '1' : '0') == '0' ? 'selected' : '' }}>{{ T::translate('No', 'Hindi') }}</option>
                                </select>
                            </div>
                        </div>
                        <hr class="my-4">
                        <!-- Row 2: Address -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">{{ T::translate('Current Address', 'Kasalukuyang Address') }}</h5> <!-- Row Title -->
                            </div>
                        </div>
                        <div class="row mb-3 me-0">
                            <div class="col-md-12">
                                <label for="addressDetails" class="form-label">{{ T::translate('House No., Street, Subdivision, Barangay, City, Province', 'Numero ng Bahay, Kalye, Subdivision, Barangay, Siyudad, Probinsya') }}<label style="color:red;"> * </label></label>
                                <textarea class="form-control" id="addressDetails" name="address_details" 
                                    placeholder="{{ T::translate('Enter complete current address', 'Ilagay ang kumpletong kasalukuyang address') }}" 
                                    rows="2" required pattern="^[a-zA-Z0-9\s,.-]+$" 
                                    title="{{ T::translate('Only alphanumeric characters, spaces, commas, periods, and hyphens are allowed.', 'Tanging alphanumeric characters, spaces, commas, periods, at hyphens lamang ang pinapayagan.') }}"
                                    oninput="validateAddress(this)">{{ old('address_details', $familyMember->street_address) }}</textarea>
                            </div>
                        </div>
                        
                        <hr class="my-4">
                        <!-- Login Access -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">{{ T::translate('Family Portal Login Access', 'Login Access sa Family Portal') }}</h5>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <!-- Email Address -->
                            <div class="col-md-4">
                                <label for="personalEmail" class="form-label">{{ T::translate('Email Address', 'Email Address') }}<label style="color:red;"> * </label></label>
                                <input type="email" class="form-control" id="personalEmail" name="personal_email" 
                                    value="{{ old('personal_email', $familyMember->email) }}"
                                    placeholder="{{ T::translate('Enter email address', 'Ilagay ang email address') }}" 
                                    required 
                                    pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" 
                                    title="{{ T::translate('Please enter a valid email address.', 'Mangyaring maglagay ng wastong email address.') }}">
                                <small class="text-muted">{{ T::translate('Current Login', 'Kasalukuyang Login') }}:</strong> {{ $familyMember->email }}</small>
                            </div>

                            <!-- Password -->
                            <div class="col-md-4">
                                <label for="password" class="form-label">{{ T::translate('Password', 'Password') }}</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="account[password]" 
                                        placeholder="{{ T::translate('Leave blank to keep current password', 'Iwanang blangko upang panatilihin ang kasalukuyang password') }}" 
                                        minlength="8" 
                                        title="{{ T::translate('Password must be at least 8 characters long.', 'Ang password ay dapat hindi bababa sa 8 characters ang haba.') }}">
                                    <span class="input-group-text password-toggle" data-target="password">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-4">
                                <label for="confirmPassword" class="form-label">{{ T::translate('Confirm Password', 'Kumpirmahin ang Password') }}</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirmPassword" name="account[password_confirmation]" 
                                        placeholder="{{ T::translate('Confirm new password', 'Kumpirmahin ang bagong password') }}" 
                                        title="{{ T::translate('Passwords must match.', 'Dapat mag-match ang mga password.') }}">
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
                                    {{ T::translate('Update Family Member', 'I-Update ang Miyembro ng Pamilya') }}
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
                    <h5 class="modal-title" id="saveSuccessModalLabel">{{ T::translate('Success', 'Tagumpay') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <p>{{ T::translate('Family Member has been successfully saved!', 'Ang Miyembro ng Pamilya ay matagumpay na nai-save!') }}</p>
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
                    <h5 class="modal-title" id="fileSizeErrorModalLabel">{{ T::translate('File Size Error', 'Error sa File Size') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-exclamation-triangle-fill text-danger me-3" style="font-size: 2rem;"></i>
                        <p id="fileSizeErrorMessage" class="mb-0"></p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ T::translate('Close', 'Isara') }}</button>
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
                    <strong>{{ T::translate('Form submission failed', 'Nabigo ang pag-sumite ng form') }}</strong><br>
                    {{ T::translate('Profile Picture', 'Litrato ng Profile') }} (${fileSizeMB}MB) {{ T::translate('exceeds the maximum size of', 'lumampas sa maximum na laki na') }} 7MB.<br>
                    {{ T::translate('Please select a smaller file or compress your existing file.', 'Mangyaring pumili ng mas maliit na file o i-compress ang iyong umiiral na file.') }}
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
                        <strong>{{ T::translate('Profile Picture', 'Litrato ng Profile') }}</strong> {{ T::translate('file is too large', 'masyadong malaki ang file') }} (${fileSizeMB}MB).<br>
                        {{ T::translate('Maximum allowed size is 7MB', 'Ang maximum na pinapayagang laki ay 7MB') }}.<br>
                        {{ T::translate('Please select a smaller file or compress your existing file.', 'Mangyaring pumili ng mas maliit na file o i-compress ang iyong umiiral na file.') }}
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
                    <strong>{{ T::translate('Form submission failed', 'Nabigo ang pag-sumite ng form') }}</strong><br>
                    {{ T::translate('Profile Picture', 'Litrato ng Profile') }} (${fileSizeMB}MB) {{ T::translate('exceeds the maximum size of', 'lumampas sa maximum na laki na') }} 7MB.<br>
                    {{ T::translate('Please select a smaller file or compress your existing file.', 'Mangyaring pumili ng mas maliit na file o i-compress ang iyong umiiral na file.') }}
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