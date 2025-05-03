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
            <!-- Back Button Logic -->
            <div class="d-flex justify-content-between align-items-center mb-3">
                <form action="{{ route('admin.caremanagers.view') }}" method="POST" style="display:inline;">
                    @csrf
                    <input type="hidden" name="caremanager_id" value="{{ $caremanager->id }}">
                    <button type="submit" class="btn btn-secondary original-back-btn">
                    <i class="bx bx-arrow-back"></i> Back
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
                                       placeholder="Enter first name" required 
                                       pattern="^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$"
                                       title="First letter must be capital">
                            </div>
                            <div class="col-md-3">
                                <label for="lastName" class="form-label">Last Name<label style="color:red;"> * </label></label>
                                <input type="text" class="form-control" id="lastName" name="last_name" 
                                       value="{{ old('last_name', $caremanager->last_name) }}" 
                                       placeholder="Enter last name" required
                                       pattern="^[A-Z][a-zA-Z]{1,}(?:-[a-zA-Z]{1,})?(?: [a-zA-Z]{2,}(?:-[a-zA-Z]{1,})?)*$"
                                       title="First letter must be capital">
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
                                    <i class='bx bx-save me-2' style="font-size: 24px;"></i>
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
                    <p>Care Worker has been successfully saved!</p>
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
            e.preventDefault(); // Prevent the default form submission

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
    </script>

</body>
</html>