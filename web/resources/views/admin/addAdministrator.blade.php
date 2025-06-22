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
                <a href="{{ route('admin.administrators.index') }}" class="btn btn-secondary">
                    <i class="bi bi-arrow-bar-left"></i>
                    {{ T::translate('Back', 'Bumalik') }}
                </a>
                <div class="mx-auto text-center" style="flex-grow: 1; font-weight: bold; font-size: 20px;">{{ T::translate('ADD ADMINISTRATOR', 'MAGDAGDAG NG ADMINISTRATOR') }}</div>
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
                <form action="{{ route('admin.administrators.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf <!-- Include CSRF token for security -->
                        <!-- Row 1: Personal Details -->
                        <div class="row mb-1 mt-3">
                            <div class="col-12">
                                <h5 class="text-start">{{ T::translate('Personal Details', 'Personal na Detalye')}}</h5> <!-- Row Title -->
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-3">
                                <label for="firstName" class="form-label">{{ T::translate('First Name', 'Pangalan')}}<label style="color:red;"> * </label></label>
                                <input type="text" class="form-control" id="firstName" name="first_name" 
                                    value="{{ old('first_name') }}"
                                    placeholder="{{ T::translate('Enter first name', 'Ilagay ang pangalan')}}" 
                                    required >
                                    
                            </div>
                            <div class="col-md-3">
                                <label for="lastName" class="form-label">{{ T::translate('Last Name', 'Apelyido')}}<label style="color:red;"> * </label></label>
                                <input type="text" class="form-control" id="lastName" name="last_name" 
                                    value="{{ old('last_name') }}"
                                    placeholder="{{ T::translate('Enter last name', 'Ilagay ang apelyido')}}" 
                                    required >
                                   
                            </div>
                            <div class="col-md-3">
                                <label for="birthDate" class="form-label">{{ T::translate('Birthday', 'Kaarawan')}}<label style="color:red;"> * </label></label>
                                <input type="date" class="form-control" id="birthDate" name="birth_date" value="{{ old('birth_date') }}" required onkeydown="return true">
                            </div>
                            <div class="col-md-3">
                                <label for="gender" class="form-label">{{ T::translate('Gender', 'Kasarian')}}</label>
                                <select class="form-select" id="gender" name="gender">
                                    <option value="" disabled {{ old('gender') ? '' : 'selected' }}>{{ T::translate('Select gender', 'Pumili ng Kasarian')}}</option>
                                    <option value="Male" {{ old('gender') == 'Male' ? 'selected' : '' }}>{{ T::translate('Male', 'Lalaki')}}</option>
                                    <option value="Female" {{ old('gender') == 'Female' ? 'selected' : '' }}>{{ T::translate('Female', 'Babae')}}</option>
                                    <option value="Other" {{ old('gender') == 'Other' ? 'selected' : '' }}>{{ T::translate('Other', 'Iba pa')}}</option>
                                </select>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-3">
                                <label for="civilStatus" class="form-label">{{ T::translate('Civil Status', 'Katayuan sa Pag-aasawa')}}</label>
                                <select class="form-select" id="civilStatus" name="civil_status">
                                    <option value="" disabled {{ old('civil_status') ? '' : 'selected' }}>{{ T::translate('Select civil status', 'Pumili ng Katayuan')}}</option>
                                    <option value="Single" {{ old('civil_status') == 'Single' ? 'selected' : '' }}>{{ T::translate('Single', 'Walang Asawa')}}</option>
                                    <option value="Married" {{ old('civil_status') == 'Married' ? 'selected' : '' }}>{{ T::translate('Married', 'Mayroong Asawa')}}</option>
                                    <option value="Widowed" {{ old('civil_status') == 'Widowed' ? 'selected' : '' }}>{{ T::translate('Widowed', 'Balo')}}</option>
                                    <option value="Divorced" {{ old('civil_status') == 'Divorced' ? 'selected' : '' }}>{{ T::translate('Divorced', 'Diborsiyado')}}</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label for="religion" class="form-label">{{ T::translate('Religion', 'Relihiyon')}}</label>
                                <input type="text" class="form-control" id="religion" name="religion" value="{{ old('religion') }}" placeholder="{{ T::translate('Enter religion', 'Ilagay ang Relihiyon')}}" pattern="^[a-zA-Z\s]*$" title="Only alphabets and spaces are allowed.">
                            </div>
                            <div class="col-md-3">
                                <label for="nationality" class="form-label">{{ T::translate('Nationality', 'Nasyonalidad')}}</label>
                                <input type="text" class="form-control" id="nationality" name="nationality" value="{{ old('nationality') }}" placeholder="{{ T::translate('Enter nationality', 'Ilagay ang Nasyonalidad')}}" pattern="^[a-zA-Z\s]*$" title="Only alphabets and spaces are allowed.">
                            </div>
                            <div class="col-md-3">
                                <label for="educationalBackground" class="form-label">{{ T::translate('Educational Background', 'Background na Pang-Edukasyon')}}</label>
                                <select class="form-select" id="educationalBackground" name="educational_background">
                                    <option value="" disabled {{ old('educational_background') ? '' : 'selected' }}>{{ T::translate('Select educational background', 'Pumili ng Background')}}</option>
                                    <option value="Elementary Graduate" {{ old('educational_background') == 'Elementary Graduate' ? 'selected' : '' }}>{{ T::translate('Elementary Graduate', 'Nagtapos ng Elementarya')}}</option>
                                    <option value="High School Undergraduate" {{ old('educational_background') == 'High School Undergraduate' ? 'selected' : '' }}>{{ T::translate('High School Undergraduate', 'Hindi nagtapos ng Hayskul')}}</option>
                                    <option value="High School Graduate" {{ old('educational_background') == 'High School Graduate' ? 'selected' : '' }}>{{ T::translate('High School Graduate', 'Nagtapos ng Hayskul')}}</option>
                                    <option value="Vocational/Technical Course" {{ old('educational_background') == 'Vocational/Technical Course' ? 'selected' : '' }}>{{ T::translate('Vocational/Technical Course', 'Bokasyonal/Teknikal na Kurso')}}</option>
                                    <option value="College Undergraduate" {{ old('educational_background') == 'College Undergraduate' ? 'selected' : '' }}>{{ T::translate('College Undergraduate', 'Hindi nagtapos ng Kolehiyo')}}</option>
                                    <option value="Bachelor's Degree" {{ old('educational_background') == 'Bachelor\'s Degree' ? 'selected' : '' }}>Bachelor's Degree</option>
                                    <option value="Master's Degree" {{ old('educational_background') == 'Master\'s Degree' ? 'selected' : '' }}>Master's Degree</option>
                                     <option value="Doctorate Degree" {{ old('educational_background') == 'Doctorate Degree' ? 'selected' : '' }}>Doctorate Degree</option>
                                </select>
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Row 2: Address -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">{{ T::translate('Current Address', 'Kasalukuyang Tahanan')}}</h5> <!-- Row Title -->
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label for="addressDetails" class="form-label">{{ T::translate('House No., Street, Subdivision, Barangay, City, Province', 'Numero ng Bahay, Kalye, Subdivision, Barangay, Siyudad, Probinsya')}}<label style="color:red;"> * </label></label>
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
                        <!-- Contact Information -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">{{ T::translate('Contact Information', 'Impormasyon sa Pakikipag-ugnayan') }}</h5> <!-- Row Title -->
                            </div>
                        </div> 
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label for="personalEmail" class="form-label">{{ T::translate('Personal Email Address', 'Personal na Email Address')}}<label style="color:red;"> * </label></label>
                                <input type="email" class="form-control" id="personalEmail" name="personal_email" 
                                       value="{{ old('personal_email') }}"
                                       placeholder="Enter personal email" 
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
                                <h5 class="text-start">{{ T::translate('Documents Upload', 'Mag-upload ng Dokumento') }}</h5> <!-- Row Title -->
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <label for="administratorPhoto" class="form-label">{{ T::translate('Administrator Photo', 'Litrato ng Administrator')}}</label>
                                <input type="file" class="form-control" id="administratorPhoto" name="administrator_photo" accept="image/png, image/jpeg" capture="user">
                                 <small class="text-danger">{{ T::translate('Maximum file size: 7MB', 'Maximum na laki ng file: 7MB')}}</small>
                                @if($errors->any())
                                <small class="text-danger">{{ T::translate('Note: You need to select the file again after a validation error.', 'Note: Pumili muli ng file pagkatapos nang validation error.')}}</small>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="governmentID" class="form-label">{{ T::translate('Government Issued ID', 'ID Galing sa Gobyerno')}}</label>
                                <input type="file" class="form-control" id="governmentID" name="government_ID" accept=".jpg,.png">
                                 <small class="text-danger">{{ T::translate('Maximum file size: 7MB', 'Maximum na laki ng file: 7MB')}}</small>
                                @if($errors->any())
                                <small class="text-danger">{{ T::translate('Note: You need to select the file again after a validation error.', 'Note: Pumili muli ng file pagkatapos nang validation error.')}}</small>
                                @endif
                            </div>
                            <div class="col-md-4">
                                <label for="resume" class="form-label">Resume / CV</label>
                                <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx">
                                <small class="text-danger">{{ T::translate('Maximum file size: 5MB', 'Maximum na laki ng file: 5MB')}}</small>
                                @if($errors->any())
                                <small class="text-danger">{{ T::translate('Note: You need to select the file again after a validation error.', 'Note: Pumili muli ng file pagkatapos nang validation error.')}}</small>
                                @endif
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <label for="sssID" class="form-label">SSS ID</label>
                                <input type="text" class="form-control" id="sssID" name="sss_ID" value="{{ old('sss_ID') }}" placeholder="{{ T::translate('Enter SSS ID', 'Ilagay ang SSS ID') }}" maxlength="10" oninput="restrictToNumbers(this)" title="Must be 10 digits.">
                            </div>
                            <div class="col-md-4">
                                <label for="philhealthID" class="form-label">PhilHealth ID</label>
                                <input type="text" class="form-control" id="philhealthID" name="philhealth_ID" value="{{ old('philhealth_ID') }}" placeholder="{{ T::translate('Enter PhilHealth ID', 'Ilagay ang PhilHealth ID') }}" maxlength="12" oninput="restrictToNumbers(this)" title="Must be 12 digits.">
                            </div>
                            <div class="col-md-4">
                                <label for="pagibigID" class="form-label">Pag-Ibig ID</label>
                                <input type="text" class="form-control" id="pagibigID" name="pagibig_ID" value="{{ old('pagibig_ID') }}" placeholder="{{ T::translate('Enter Pag-Ibig ID', 'Ilagay ang Pag-Ibig ID') }}" maxlength="12" oninput="restrictToNumbers(this)" title="Must be 12 digits.">
                            </div>
                        </div>

                        <hr class="my-4">
                        <!-- Account Registration -->
                        <div class="row mb-1">
                            <div class="col-12">
                                <h5 class="text-start">{{ T::translate('Administrator Account Registration', 'Pagrerehistro sa Account ng Administrator') }}</h5> <!-- Row Title -->
                            </div>
                        </div>
                        <div class="row mb-1">
                            <div class="col-md-4">
                                <label for="email" class="form-label">{{ T::translate('Work Email Address', 'Email Address sa Trabaho') }}<label style="color:red;"> * </label></label>
                                <input type="email" class="form-control" id="email" name="account[email]" 
                                       value="{{ old('account.email') }}"
                                       placeholder="{{ T::translate('Enter work email', 'Ilagay ang Email address') }}" 
                                       required 
                                       pattern="^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" 
                                       title="Enter a valid email address (e.g., example@domain.com)" 
                                       oninput="validateEmail(this)">
                            </div>
                            <div class="col-md-4">
                                <label for="password" class="form-label">Password<label style="color:red;"> * </label></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="account[password]" placeholder="{{ T::translate('Enter password', 'Ilagay ang Password') }}" required>
                                    <span class="input-group-text password-toggle" data-target="password">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label for="confirmPassword" class="form-label">{{ T::translate('Confirm Password', 'Kumpirmahin ang Password') }}<label style="color:red;"> * </label></label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="confirmPassword" name="account[password_confirmation]" placeholder="{{ T::translate('Confirm password', 'Kumpirmahin ang Password') }}" required>
                                    <span class="input-group-text password-toggle" data-target="confirmPassword">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-4 position-relative">
                                <label for="organization_Roles" class="form-label">Organization Roles<label style="color:red;"> * </label></label>
                                <select class="form-select" id="Organization_RolesDropdown" name="Organization_Roles" required>
                                    <option value="" disabled {{ old('Organization_Roles') ? '' : 'selected' }}>{{ T::translate('Select organization role', 'Pumili ng Organization Role') }}</option>
                                    <option value="2" {{ old('Organization_Roles') == '2' ? 'selected' : '' }}>Project Coordinator</option>
                                    <option value="3" {{ old('Organization_Roles') == '3' ? 'selected' : '' }}>MEAL Coordinator</option>
                                </select>
                            </div>
                        </div>                        
                        <div class="row mt-4">
                            <div class="col-12 d-flex justify-content-center align-items-center">
                                <button type="submit" class="btn btn-success btn-lg d-flex align-items-center">
                                    <i class="bi bi-floppy" style="padding-right: 10px;"></i>
                                    {{ T::translate('Save Administrator', 'I-Save ang Administrator')}}
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
                    <p>{{ T::translate('Administrator has been successfully saved!', 'Ang Administrato ay matagumpay na nai-Save!') }}</p>
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
                            <strong>${fieldLabel}</strong> {{ T::translate('file is too large', 'masyadong malaki ang file')}} (${fileSizeMB}MB).<br>
                            {{ T::translate('Maximum allowed size is', 'Ang maximum na pinapayagang laki ay')}} ${maxSizeMB}MB.<br>
                            {{ T::translate('Please select a smaller file or compress your existing file.', 'Mangyaring pumili ng mas maliit na file o i-compress ang iyong umiiral na file.')}}
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
                            <strong>{{ T::translate('Form submission failed', 'Nabigo ang pag-sumite ng form')}}</strong><br>
                            ${fieldLabel} (${fileSizeMB}MB) {{ T::translate('exceeds the maximum size of', 'lumampas sa maximum na laki na')}} ${maxSizeMB}MB.<br>
                            {{ T::translate('Please select a smaller file or compress your existing file.', 'Mangyaring pumili ng mas maliit na file o i-compress ang iyong umiiral na file.')}}
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
        document.querySelector('form[action="{{ route("admin.administrators.store") }}"]').addEventListener('submit', function (e) {
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
<!-- <script>
        document.querySelector('form').addEventListener('submit', function (e) {
            e.preventDefault(); // Prevent the default form submission

            // Show the success modal
            const successModal = new bootstrap.Modal(document.getElementById('saveSuccessModal'));
            successModal.show();
        });
    </script> -->
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

            // Initialize filtering for each dropdown
            // filterDropdown('civilStatusInput', 'civilStatusDropdown');
            // filterDropdown('genderInput', 'genderDropdown');
            // filterDropdown('educationalBackgroundInput', 'educationalBackgroundDropdown');
            // filterDropdown('Organization_RolesInput', 'Organization_RolesDropdown');
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
            if (input.id !== 'firstName' && input.id !== 'lastName') {
                input.addEventListener('input', function () {
                    this.value = this.value.replace(/[^a-zA-Z0-9\s]/g, ''); // Remove special characters
                });
            }
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
            
            // // Remove invalid characters (anything other than letters, spaces, and hyphens)
            // input.value = input.value.replace(/[^a-zA-Z\s-]/g, '');

            // // Prevent multiple consecutive spaces
            // input.value = input.value.replace(/\s{2,}/g, ' ');

            // // Prevent multiple consecutive hyphens
            // input.value = input.value.replace(/-{2,}/g, '-');

            // // Prevent spaces or hyphens at the start or end of the input
            // input.value = input.value.trim().replace(/^-|-$/g, '');

            // // Prevent single-letter words
            // const words = input.value.split(' ');
            // input.value = words
            //     .filter(word => word.length > 1 || /^[a-zA-Z]+$/.test(word)) // Remove single-letter words
            //     .map(word => word.replace(/^-|-$/g, '')) // Remove hyphens at the start or end of each word
            //     .join(' ');
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
    <script>
        // Password toggle functionality
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
    <?php
    // if ($request->hasFile('administrator_photo')) {
    //     dd($request->file('administrator_photo')->getClientMimeType());
    // }
    ?>
</body>
</html>