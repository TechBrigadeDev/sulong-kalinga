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
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp
    @include('components.adminNavbar')
    @include('components.adminSidebar')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <a href="{{ route('admin.families.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-bar-left"></i> {{ T::translate('Back', 'Bumalik') }}
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
                    <form action="{{ route('admin.families.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf <!-- Include CSRF token for security -->
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
                                    placeholder="{{ T::translate('Enter first name', 'Ilagay ang Pangalan') }}" 
                                    value="{{ old('first_name') }}"
                                    required >
                            </div>
                            <div class="col-md-3 relative">
                                <label for="lastName" class="form-label">{{ T::translate('Last Name', 'Apelyido') }}<label style="color:red;"> * </label></label>
                                <input type="text" class="form-control" id="lastName" name="last_name" 
                                        placeholder="{{ T::translate('Enter last name', 'Ilagay ang Apelyido') }}" 
                                        value="{{ old('last_name') }}"
                                        required >
                            </div>
                            <div class="col-md-3 relative">
                                <label for="gender" class="form-label">{{ T::translate('Gender', 'Kasarian') }}</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="" disabled {{ old('gender') ? '' : 'selected' }}>{{ T::translate('Select gender', 'Pumili ng Kasarian') }}</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>{{ T::translate('Male', 'Lalaki') }}</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>{{ T::translate('Female', 'Babae') }}</option>
                                    <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>{{ T::translate('Other', 'Iba pa') }}</option>
                                </select>
                            </div>
                            <div class="col-md-3 relative">
                                <label for="birthDate" class="form-label">{{ T::translate('Birthday', 'Kaarawan') }}<label style="color:red;"> * </label></label>
                                <input type="date" class="form-control" id="birthDate" name="birth_date" value="{{ old('birth_date') }}" required onkeydown="return true">
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-4 relative">
                                <label for="mobileNumber" class="form-label">Mobile Number<label style="color:red;"> * </label></label>
                                <div class="input-group">
                                    <span class="input-group-text">+63</span>
                                    <input type="text" class="form-control" id="mobileNumber" name="mobile_number" value="{{ old('mobile_number') }}" placeholder="{{ T::translate('Enter mobile number', 'Ilagay ang mobile number') }}" maxlength="11" required oninput="restrictToNumbers(this)" title="Must be 10 or 11digits.">
                                </div>
                            </div>
                            <div class="col-md-4 relative">
                                <label for="landlineNumber" class="form-label">Landline Number</label>
                                <input type="text" class="form-control" id="landlineNumber" name="landline_number" value="{{ old('landline_number') }}" placeholder="{{ T::translate('Enter Landline number', 'Enter ang Landline number') }}" maxlength="10" oninput="restrictToNumbers(this)" title="Must be between 7 and 10 digits.">
                            </div>
                            <div class="col-md-4 relative">
                                <label for="familyPhoto" class="form-label">{{ T::translate('Profile Picture', 'Litrato para sa Profile') }}</label>
                                <input type="file" class="form-control" id="familyPhoto" name="family_photo" accept="image/png, image/jpeg" capture="user">
                                <small class="text-danger">{{ T::translate('Maximum file size: 7MB', 'Maximum na laki ng file: 7MB')}}</small>
                                @if($errors->any())
                                <small class="text-danger">{{ T::translate('Note: You need to select the file again after a validation error.', 'Note: Pumili muli ng file pagkatapos nang validation error.')}}</small>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-1">
                            <!-- Change to dynamic -->
                            <div class="col-md-4 relative">
                            <label for="relatedBeneficiary" class="form-label">{{ T::translate('Related Beneficiary', 'Kaugnay na Benepisyaryo') }}<label style="color:red;"> * </label></label>
                            <select class="form-select" id="relatedBeneficiary" name="relatedBeneficiary" required>
                                <option value="" disabled {{ old('relatedBeneficiary') ? '' : 'selected' }}>{{ T::translate('Select a beneficiary', 'Pumili ng Benepisyaryo') }}</option>
                                @foreach ($beneficiaries as $beneficiary)
                                    <option value="{{ $beneficiary->beneficiary_id }}" {{ old('relatedBeneficiary') == $beneficiary->beneficiary_id ? 'selected' : '' }}>
                                        {{ $beneficiary->first_name }} {{ $beneficiary->last_name }}
                                    </option>
                                @endforeach
                            </select>
                            </div>
                            <div class="col-md-4 relative">
                                <label for="relationToBeneficiary" class="form-label">{{ T::translate('Relation to Beneficiary', 'Relasyon sa Benepisyaryo') }}<label style="color:red;"> * </label></label>
                                <select class="form-select" id="relationToBeneficiary" name="relation_to_beneficiary" required>
                                    <option value="" disabled {{ old('relation_to_beneficiary') ? '' : 'selected' }}>{{ T::translate('Select relation', 'Pumili ng Relasyon') }}</option>
                                    <option value="Son" {{ old('relation_to_beneficiary') == 'Son' ? 'selected' : '' }}>{{ T::translate('Son', 'Anak na Lalaki') }}</option>
                                    <option value="Daughter" {{ old('relation_to_beneficiary') == 'Daughter' ? 'selected' : '' }}>{{ T::translate('Daughter', 'Anak na Babae') }}</option>
                                    <option value="Spouse" {{ old('relation_to_beneficiary') == 'Spouse' ? 'selected' : '' }}>{{ T::translate('Spouse', 'Asawa') }}</option>
                                    <option value="Sibling" {{ old('relation_to_beneficiary') == 'Sibling' ? 'selected' : '' }}>{{ T::translate('Sibling', 'Kapatid') }}</option>
                                    <option value="Grandchild" {{ old('relation_to_beneficiary') == 'Grandchild' ? 'selected' : '' }}>{{ T::translate('Grandchild', 'Apo') }}</option>
                                    <option value="Other" {{ old('relation_to_beneficiary') == 'Other' ? 'selected' : '' }}>{{ T::translate('Other', 'Iba pa') }}</option>
                                </select>
                            </div>
                            
                            <div class="col-md-4 relative">
                                <label for="isPrimaryCaregiver" class="form-label">{{ T::translate('Is Primary Caregiver?', 'Pangunahing Tagapag-alaga?') }}<label style="color:red;"> * </label></label>
                                <select class="form-select" id="isPrimaryCaregiver" name="is_primary_caregiver" required>
                                    <option value="" disabled {{ old('is_primary_caregiver') !== null ? '' : 'selected' }}>{{ T::translate('Select an option', 'Pumili') }}</option>
                                    <option value="1" {{ old('is_primary_caregiver') == '1' ? 'selected' : '' }}>{{ T::translate('Yes', 'Oo') }}</option>
                                    <option value="0" {{ old('is_primary_caregiver') == '0' ? 'selected' : '' }}>{{ T::translate('No', 'Hindi') }}</option>
                                </select>
                            </div>
                        </div>
                        

                        <hr class="my-4">
                        <!-- Row 2: Address -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">{{ T::translate('Current Address', 'Kasalukuyang Tahanan') }}</h5> <!-- Row Title -->
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="addressDetails" class="form-label">{{ T::translate('House No., Street, Subdivision, Barangay, City, Province', 'Numero ng Bahay, Kalye, Subdivision, Barangay, Siyudad, Probinsya') }}<label style="color:red;"> * </label></label>
                                <textarea class="form-control" id="addressDetails" name="address_details" 
                                placeholder="{{ T::translate('Enter complete current address', 'Ilagay ang kumpletong address') }}" 
                                rows="2" 
                                required 
                                pattern="^[a-zA-Z0-9\s,.-]+$" 
                                title="Only alphanumeric characters, spaces, commas, periods, and hyphens are allowed."
                                oninput="validateAddress(this)">{{ old('address_details') }}</textarea>
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Login Access -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">{{ T::translate('Family Portal Login Access', 'Access para sa Family Portal') }}</h5>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <!-- Email Address -->
                            <div class="col-md-4">
                                <label for="personalEmail" class="form-label">Email Address<label style="color:red;"> * </label></label>
                                <input type="email" class="form-control" id="personalEmail" name="personal_email" 
                                    value="{{ old('personal_email') }}" 
                                    placeholder="{{ T::translate('Enter email address', 'Ilagay ang Email address') }}" 
                                    required 
                                    pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" 
                                    title="Please enter a valid email address.">
                            </div>
                            <!-- Password -->
                            <div class="col-md-4">
                                <label for="password" class="form-label">Password<label style="color:red;"> * </label></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="account[password]" 
                                        placeholder="{{ T::translate('Enter password', 'Ilagay ang password') }}" 
                                        minlength="8" 
                                        required >
                                    <span class="input-group-text password-toggle" data-target="password">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                </div>
                            </div>

                            <!-- Confirm Password -->
                            <div class="col-md-4">
                                <label for="confirmPassword" class="form-label">{{ T::translate('Confirm Password', 'Kumprimahin ang Password') }}<label style="color:red;"> * </label></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirmPassword" name="account[password_confirmation]" 
                                        placeholder="{{ T::translate('Confirm password', 'Kumprimahin ang Password') }}" 
                                        required 
                                        title="Passwords must match.">
                                    <span class="input-group-text password-toggle" data-target="confirmPassword">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-12">
                                <h6 class="text-start text-muted"><strong>Note:</strong>{{ T::translate('The family member will be able to log into the Family Portal using their email address and the password you set here. Their account will be connected to the selected beneficiary\'s portal account.', 'Ang miyembro ng pamilya ay makakapag-log in sa Family Portal gamit ang kanilang email address at ang password na iyong itinakda dito. Ikokonekta ang kanilang account sa portal account ng napiling benepisyaryo.') }}</h6>
                            </div>
                        </div>

                        
                        <div class="row mt-4">
                            <div class="col-12 d-flex justify-content-center align-items-center">
                                <button type="submit" class="btn btn-success btn-lg d-flex align-items-center" id="saveBeneficiaryButton">
                                    <i class="bi bi-floppy" style="padding-right: 10px;"></i>
                                    {{ T::translate('Save Family Member', 'I-save ang Miyembro ng Pamilya') }}
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
                    <p>{{ T::translate('Family Member has been successfully saved!', 'Ang miyembro ng pamilya ay matagumpay na nai-save!') }}</p>
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
                    <h5 class="modal-title" id="fileSizeErrorModalLabel">{{ T::translate('File Size Error', 'Erro sa File Size') }}</h5>
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
        // Parse the JSON data passed from the controller
        const beneficiaries = @json($beneficiaries);

        // Get the dropdown element
        const relatedBeneficiaryDropdown = document.getElementById('relatedBeneficiary');
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Select all password toggle elements
            const toggleButtons = document.querySelectorAll('.password-toggle');
            
            // Add click event listener to each toggle button
            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
                    // Get the target input element using the data-target attribute
                    const targetId = this.getAttribute('data-target');
                    const passwordInput = document.getElementById(targetId);
                    
                    // Toggle password visibility
                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        this.querySelector('i').classList.remove('bi-eye-slash');
                        this.querySelector('i').classList.add('bi-eye');
                    } else {
                        passwordInput.type = 'password';
                        this.querySelector('i').classList.remove('bi-eye');
                        this.querySelector('i').classList.add('bi-eye-slash');
                    }
                });
            });
        });
    </script>

</body>
</html>