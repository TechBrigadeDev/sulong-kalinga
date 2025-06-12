<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Profile | {{ $user->first_name }} {{ $user->last_name }}</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/viewProfile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/viewProfile2.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="row justify-content-center" id="viewProfile">
                <div class="col-lg-10">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle-fill me-2"></i>
                            <ul class="mb-0">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="viewProfile">
                        <div class="row mb-2">
                            <div class="col-12 text-center">
                                <h4 class="mb-0"><i class="bi bi-person-badge me-2" style="color: var(--complement-1);"></i>USER PROFILE</h4>
                            </div>
                        </div>
                        
                        <div class="breadcrumb-nav">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item {{ session('activeTab') != 'contact' && session('activeTab') != 'settings' ? 'active' : '' }}" data-section="personal">
                                        <a href="javascript:void(0)"><i class="bi bi-person-vcard me-1"></i>Personal</a>
                                    </li>
                                    <li class="breadcrumb-item {{ session('activeTab') == 'contact' ? 'active' : '' }}" data-section="contact">
                                        <a href="javascript:void(0)"><i class="bi bi-telephone me-1"></i>Contact</a>
                                    </li>
                                    <li class="breadcrumb-item {{ session('activeTab') == 'settings' ? 'active' : '' }}" data-section="settings">
                                        <a href="javascript:void(0)"><i class="bi bi-gear me-1"></i>Settings</a>
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        
                        <!-- Personal Information Section -->
                        <div class="profile-section {{ session('activeTab') != 'contact' && session('activeTab') != 'settings' ? 'active' : '' }}" id="personal-section">
                            <div class="row">
                                <div class="col-md-4 text-center mb-4 mb-md-0">
                                    @if($user->photo))
                                        <img src="{{ asset('storage/' . $user->photo) }}" alt="Profile Photo" class="profile-photo">
                                    @else
                                        <img src="{{ asset('images/defaultProfile.png') }}" alt="Profile Photo" class="profile-photo">
                                    @endif
                                    <h5 class="user-name">{{ $user->first_name }} {{ $user->last_name }}</h5>
                                    <p class="member-since"><i class="bi bi-calendar-event me-1"></i>Member since: {{ $memberSince }}</p>
                                    <p class="mb-2">
                                        <span class="badge badge-status {{ $user->status == 'Active' ? 'badge-active' : 'badge-inactive' }}">
                                            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>{{ $user->status }}
                                        </span>
                                    </p>
                                    <p class="user-role"><i class="bi bi-person-workspace me-1"></i>{{ $user->organizationRole->name ?? 'Administrator' }}</p>
                                </div>
                                
                                <div class="col-md-8">
                                    <div class="profile-header">
                                        <i class="bi bi-person-lines-fill"></i>
                                        <h5>Personal Information</h5>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <div class="info-row">
                                                    <div class="info-label"><i class="bi bi-person"></i>Full Name:</div>
                                                    <div class="info-value">{{ $user->first_name }} {{ $user->last_name }}</div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label"><i class="bi bi-calendar"></i>Date of Birth:</div>
                                                    <div class="info-value">{{ $formattedBirthday ?? 'Not specified' }}</div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label"><i class="bi bi-gender-ambiguous"></i>Gender:</div>
                                                    <div class="info-value">{{ $user->gender ?? 'Not specified' }}</div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label"><i class="bi bi-globe"></i>Nationality:</div>
                                                    <div class="info-value">{{ $user->nationality ?? 'Not specified' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <div class="info-row">
                                                    <div class="info-label"><i class="bi bi-heart"></i>Marital Status:</div>
                                                    <div class="info-value">{{ $user->civil_status ?? 'Not specified' }}</div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label"><i class="bi bi-mortarboard"></i>Educational Background:</div>
                                                    <div class="info-value">{{ $user->educational_background ?? 'Not specified' }}</div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label"><i class="bi bi-house-door"></i>Religion:</div>
                                                    <div class="info-value">{{ $user->religion ?? 'Not specified' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Contact Information Section -->
                        <div class="profile-section {{ session('activeTab') == 'contact' ? 'active' : '' }}" id="contact-section" style="display: none;">
                            <div class="profile-header">
                                <i class="bi bi-mailbox"></i>
                                <h5>Contact Information</h5>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-row">
                                            <div class="info-label"><i class="bi bi-envelope-at"></i>Work Email:</div>
                                            <div class="info-value">{{ $user->email }}</div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label"><i class="bi bi-envelope"></i>Personal Email:</div>
                                            <div class="info-value">{{ $user->personal_email ?? 'Not specified' }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-row">
                                            <div class="info-label"><i class="bi bi-phone"></i>Mobile Phone:</div>
                                            <div class="info-value">{{ $user->mobile }}</div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label"><i class="bi bi-telephone"></i>Landline:</div>
                                            <div class="info-value">{{ $user->landline ?? 'Not specified' }}</div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label"><i class="bi bi-geo-alt"></i>Address:</div>
                                            <div class="info-value">{{ $user->address }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Account Settings Section -->
                        <div class="profile-section {{ session('activeTab') == 'settings' ? 'active' : '' }}" id="settings-section" style="display: none;">
                            <div class="profile-header">
                                <i class="bi bi-shield-lock"></i>
                                <h5>Account Settings</h5>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-row">
                                            <div class="info-label"><i class="bi bi-envelope-at"></i>Email:</div>
                                            <div class="info-value">{{ $user->email }}</div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label"><i class="bi bi-shield-check"></i>Account Status:</div>
                                            <div class="info-value">
                                                <span class="badge badge-status {{ $user->status == 'Active' ? 'badge-active' : 'badge-inactive' }}">
                                                    <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>{{ $user->status }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-row">
                                            <div class="info-label"><i class="bi bi-credit-card"></i>SSS ID Number:</div>
                                            <div class="info-value">{{ $user->sss_id_number ?? 'Not specified' }}</div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label"><i class="bi bi-heart-pulse"></i>PhilHealth ID Number:</div>
                                            <div class="info-value">{{ $user->philhealth_id_number ?? 'Not specified' }}</div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label"><i class="bi bi-house"></i>Pag-IBIG ID Number:</div>
                                            <div class="info-value">{{ $user->pagibig_id_number ?? 'Not specified' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end mt-4 gap-2">
                                <button class="btn btn-primary" id="updateEmailBtn">
                                    <i class="bi bi-envelope-arrow-up me-2"></i>Update Email
                                </button>
                                <button class="btn btn-primary" id="updatePasswordBtn">
                                    <i class="bi bi-key me-2"></i>Update Password
                                </button>
                            </div>
                            
                            <!-- Update Email Form -->
                            <div class="form-section" id="updateEmailForm" style="display: none;">
                                <h6><i class="bi bi-envelope-arrow-up"></i>Update Email Address</h6>
                                <form action="/admin/update-email" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="current_email" class="form-label"><i class="bi bi-envelope me-1"></i>Current Email</label>
                                        <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="account_email" class="form-label"><i class="bi bi-envelope-plus me-1"></i>New Email</label>
                                        <input type="email" class="form-control" id="account_email" name="account_email" placeholder="Enter new email address" value="{{ old('account_email') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="current_password_for_email" class="form-label"><i class="bi bi-lock me-1"></i>Current Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="current_password_for_email" name="current_password" placeholder="Enter your password to confirm" required>
                                            <span class="input-group-text password-toggle" data-target="current_password_for_email">
                                                <i class="bi bi-eye-slash"></i>
                                            </span>
                                        </div>
                                        <small class="form-text text-muted">For security, please enter your current password to confirm this change.</small>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-outline-secondary" id="cancelEmailUpdateBtn">
                                            <i class="bi bi-x-circle me-1"></i>Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle me-1"></i>Save Changes
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Update Password Form -->
                            <div class="form-section" id="updatePasswordForm" style="display: none;">
                                <h6><i class="bi bi-key"></i>Update Password</h6>
                                <form action="/admin/update-password" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label"><i class="bi bi-lock me-1"></i>Current Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="current_password" name="current_password" placeholder="Enter current password" required>
                                            <span class="input-group-text password-toggle" data-target="current_password">
                                                <i class="bi bi-eye-slash"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="account_password" class="form-label"><i class="bi bi-key me-1"></i>New Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="account_password" name="account_password" placeholder="Enter new password" required>
                                            <span class="input-group-text password-toggle" data-target="account_password">
                                                <i class="bi bi-eye-slash"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="account_password_confirmation" class="form-label"><i class="bi bi-key-fill me-1"></i>Confirm Password</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="account_password_confirmation" name="account_password_confirmation" placeholder="Confirm new password" required>
                                            <span class="input-group-text password-toggle" data-target="account_password_confirmation">
                                                <i class="bi bi-eye-slash"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-outline-secondary" id="cancelPasswordUpdateBtn">
                                            <i class="bi bi-x-circle me-1"></i>Cancel
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle me-1"></i>Save Changes
                                        </button>
                                    </div>
                                </form>
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
        // Profile section navigation
        function showSection(sectionId) {
            // Hide all sections
            document.querySelectorAll('.profile-section').forEach(section => {
                section.style.display = 'none';
            });
            
            // Show the selected section
            document.getElementById(sectionId + '-section').style.display = 'block';
            
            // Update breadcrumb active state
            document.querySelectorAll('.breadcrumb-item').forEach(item => {
                item.classList.remove('active');
                if (item.dataset.section === sectionId) {
                    item.classList.add('active');
                }
            });
            
            // Scroll to top of content
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
        
        // Breadcrumb navigation
        document.querySelectorAll('.breadcrumb-item').forEach(item => {
            item.addEventListener('click', function() {
                showSection(this.dataset.section);
            });
        });
        
        // Initialize with active tab if set
        document.addEventListener('DOMContentLoaded', function() {
            @if(session('activeTab'))
                showSection('{{ session('activeTab') }}');
            @endif
        });
        
        // Show the Update Password Form
        document.getElementById('updatePasswordBtn').addEventListener('click', function () {
            document.getElementById('updatePasswordForm').style.display = 'block';
            this.style.display = 'none';
            document.getElementById('updateEmailBtn').style.display = 'none';
        });

        // Cancel the Update Password Form
        document.getElementById('cancelPasswordUpdateBtn').addEventListener('click', function () {
            document.getElementById('updatePasswordForm').style.display = 'none';
            document.getElementById('updateEmailBtn').style.display = 'inline-block';
            document.getElementById('updatePasswordBtn').style.display = 'inline-block';
        });

        // Show the Update Email Form
        document.getElementById('updateEmailBtn').addEventListener('click', function () {
            document.getElementById('updateEmailForm').style.display = 'block';
            this.style.display = 'none';
            document.getElementById('updatePasswordBtn').style.display = 'none';
        });

        // Cancel the Update Email Form
        document.getElementById('cancelEmailUpdateBtn').addEventListener('click', function () {
            document.getElementById('updateEmailForm').style.display = 'none';
            document.getElementById('updatePasswordBtn').style.display = 'inline-block';
            document.getElementById('updateEmailBtn').style.display = 'inline-block';
        });

        // Add activeTab to form submissions
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!this.querySelector('input[name="activeTab"]')) {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'activeTab';
                    input.value = 'settings';
                    this.appendChild(input);
                }
            });
        });
        
        // Auto-dismiss alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
        
        // Password visibility toggle
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
    </script>
</body>
</html>