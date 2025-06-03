<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | {{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/viewProfile.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

    @include('components.beneficiaryPortalNavbar')
    @include('components.beneficiaryPortalSidebar')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="row">
                <div class="col-12">
                    <div class="text-left">MY PROFILE</div>
                </div>
            </div>
            
            <div class="row" id="viewProfile">
                <div class="col-12">
                @if(session('success'))
                    <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
                        {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                @if($errors->any())
                    <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                @endif

                    <div class="d-flex justify-content-center">
                        <div class="breadcrumb-container">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb">
                                    <li class="breadcrumb-item {{ session('activeTab') != 'contact' && session('activeTab') != 'settings' ? 'active' : '' }}" data-section="personal"><a href="javascript:void(0)">Personal</a></li>
                                    <li class="breadcrumb-item {{ session('activeTab') == 'contact' ? 'active' : '' }}" data-section="contact"><a href="javascript:void(0)">Contact</a></li>
                                    <li class="breadcrumb-item {{ session('activeTab') == 'settings' ? 'active' : '' }}" data-section="settings"><a href="javascript:void(0)">Settings</a></li>
                                </ol>
                            </nav>
                        </div>
                    </div>
                    
                    <!-- Profile Sections -->
                    <div class="profile-sections">
                        <!-- Personal Information Section -->
                        <div class="profile-section {{ session('activeTab') != 'contact' && session('activeTab') != 'settings' ? 'active' : '' }}" id="personal-section">
                            <div class="profile-header">
                                <h5>Personal Information</h5>
                            </div>
                            <div class="row">
                                <div class="col-md-4 text-center">
                                    @if($beneficiary->photo)
                                        <img src="{{ asset('storage/' . $beneficiary->photo) }}" alt="Profile Photo" class="profile-photo">
                                    @else
                                        <img src="{{ asset('images/defaultProfile.png') }}" alt="Profile Photo" class="profile-photo">
                                    @endif
                                    <h5>{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</h5>
                                    <p class="text-muted">Member since: {{ $memberSince }}</p>
                                    <p class="mb-1"><span class="badge bg-success">Beneficiary</span></p>
                                    <p class="text-muted">{{ $beneficiary->category->category_name ?? 'Beneficiary' }}</p>
                                </div>
                                <div class="col-md-8">
                                    <div class="row info-row">
                                        <div class="col-md-3 info-label">Full Name:</div>
                                        <div class="col-md-9">{{ $beneficiary->first_name }} {{ $beneficiary->middle_name ?? '' }} {{ $beneficiary->last_name }}</div>
                                    </div>
                                    <div class="row info-row">
                                        <div class="col-md-3 info-label">Date of Birth:</div>
                                        <div class="col-md-9">{{ $formattedBirthday ?? 'Not specified' }}</div>
                                    </div>
                                    <div class="row info-row">
                                        <div class="col-md-3 info-label">Age:</div>
                                        <div class="col-md-9">{{ $age ?? 'Not specified' }} years old</div>
                                    </div>
                                    <div class="row info-row">
                                        <div class="col-md-3 info-label">Gender:</div>
                                        <div class="col-md-9">{{ $beneficiary->gender ?? 'Not specified' }}</div>
                                    </div>
                                    <div class="row info-row">
                                        <div class="col-md-3 info-label">Civil Status:</div>
                                        <div class="col-md-9">{{ $beneficiary->civil_status ?? 'Not specified' }}</div>
                                    </div>
                                    <div class="row info-row">
                                        <div class="col-md-3 info-label">Primary Caregiver:</div>
                                        <div class="col-md-9">{{ $beneficiary->primary_caregiver ?? 'Not specified' }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact Information Section -->
                        <div class="profile-section {{ session('activeTab') == 'contact' ? 'active' : '' }}" id="contact-section">
                            <div class="profile-header">
                                <h5>Contact Information</h5>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Username:</div>
                                <div class="col-md-9">{{ $beneficiary->username }}</div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Mobile Phone:</div>
                                <div class="col-md-9">{{ $beneficiary->mobile ?? 'Not specified' }}</div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Landline:</div>
                                <div class="col-md-9">{{ $beneficiary->landline ?? 'Not specified' }}</div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Address:</div>
                                <div class="col-md-9">
                                    {{ $beneficiary->street_address }}, 
                                    {{ $beneficiary->barangay->barangay_name ?? '' }}, 
                                    {{ $beneficiary->municipality->municipality_name ?? '' }}
                                </div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Emergency Contact:</div>
                                <div class="col-md-9">{{ $beneficiary->emergency_contact_name }} ({{ $beneficiary->emergency_contact_relation }})</div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Emergency Mobile:</div>
                                <div class="col-md-9">{{ $beneficiary->emergency_contact_mobile }}</div>
                            </div>
                            @if($beneficiary->emergency_contact_email)
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Emergency Email:</div>
                                <div class="col-md-9">{{ $beneficiary->emergency_contact_email }}</div>
                            </div>
                            @endif
                        </div>
                        
                        <!-- Account Settings Section -->
                        <div class="profile-section {{ session('activeTab') == 'settings' ? 'active' : '' }}" id="settings-section">
                            <div class="profile-header">
                                <h5>Account Settings</h5>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Username:</div>
                                <div class="col-md-9">{{ $beneficiary->username }}</div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-3 info-label">Account Status:</div>
                                <div class="col-md-9">
                                    <span class="badge bg-success">Active</span>
                                </div>
                            </div>
                            <div class="row info-row">
                                <div class="col-md-12 text-end">
                                    <button class="btn btn-primary" id="updatePasswordBtn">Update Password</button>
                                </div>
                            </div>

                            <!-- Hidden Update Password Form -->
                            <div class="row mt-3" id="updatePasswordForm" style="display: none;">
                                <div class="col-md-12">
                                    <form action="{{ route('beneficiary.profile.update-password') }}" method="POST">
                                        @csrf
                                        <div class="mb-3">
                                            <label for="current_password" class="form-label">Current Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Enter current password" required>
                                                <span class="input-group-text password-toggle" data-target="current_password">
                                                    <i class="bi bi-eye-slash"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="account_password" class="form-label">New Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="account_password" name="account_password" placeholder="Enter new password" required>
                                                <span class="input-group-text password-toggle" data-target="account_password">
                                                    <i class="bi bi-eye-slash"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label for="account_password_confirmation" class="form-label">Confirm Password</label>
                                            <div class="input-group">
                                                <input type="password" class="form-control" id="account_password_confirmation" name="account_password_confirmation" placeholder="Confirm new password" required>
                                                <span class="input-group-text password-toggle" data-target="account_password_confirmation">
                                                    <i class="bi bi-eye-slash"></i>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-success">Save Password</button>
                                            <button type="button" class="btn btn-secondary" id="cancelPasswordUpdateBtn">Cancel</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script>
        // Check if there's an activeTab to display
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('activeTab'))
                showSection('{{ session('activeTab') }}');
            @endif
        });
        
        // Show the Update Password Form
        document.getElementById('updatePasswordBtn').addEventListener('click', function () {
            document.getElementById('updatePasswordForm').style.display = 'block';
            this.style.display = 'none'; // Hide the button
        });

        // Cancel the Update Password Form
        document.getElementById('cancelPasswordUpdateBtn').addEventListener('click', function () {
            document.getElementById('updatePasswordForm').style.display = 'none';
            document.getElementById('updatePasswordBtn').style.display = 'inline-block'; // Show the button
        });
    </script>
    <script>
        // Profile section navigation
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.profile-section').forEach(section => {
                section.classList.remove('active');
            });
            
            // Show the selected section
            document.getElementById(sectionId + '-section').classList.add('active');
            
            // Update breadcrumb active state
            document.querySelectorAll('.breadcrumb-item').forEach(item => {
                item.classList.remove('active');
                if (item.dataset.section === sectionId) {
                    item.classList.add('active');
                }
            });
            
            // Scroll to top of content
            window.scrollTo(0, 0);
        }
        
        // Breadcrumb navigation
        document.querySelectorAll('.breadcrumb-item').forEach(item => {
            item.addEventListener('click', function() {
                showSection(this.dataset.section);
            });
        });
    </script>

    <script>
        // Auto-dismiss alerts after 5 seconds
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(function(alert) {
                    const bsAlert = new bootstrap.Alert(alert);
                    bsAlert.close();
                });
            }, 5000);
        });
    </script>

    <script>
        // Password visibility toggle
        document.addEventListener('DOMContentLoaded', function() {
            // Add click event to all password toggle icons
            document.querySelectorAll('.password-toggle').forEach(function(toggle) {
                toggle.addEventListener('click', function() {
                    const targetId = this.getAttribute('data-target');
                    const passwordInput = document.getElementById(targetId);
                    const icon = this.querySelector('i');
                    
                    // Toggle password visibility
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