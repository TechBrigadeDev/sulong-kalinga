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
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp
    @include('components.careWorkerNavbar')
    @include('components.careWorkerSidebar')
    
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
                                <h4 class="mb-0"><i class="bi bi-person-badge me-2" style="color: var(--complement-1);"></i>{{ T::translate('USER PROFILE', 'PROFILE NG USER')}}</h4>
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
                                        <a href="javascript:void(0)"><i class="bi bi-gear me-1"></i>{{ T::translate('Settings', 'Mga Setting')}}</a>
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        
                        <!-- Personal Information Section -->
                        <div class="profile-section {{ session('activeTab') != 'contact' && session('activeTab') != 'settings' ? 'active' : '' }}" id="personal-section">
                            <div class="row">
                                <div class="col-md-4 text-center mb-4 mb-md-0">
                                    @if($photoUrl)
                                        <img src="{{ $photoUrl }}" alt="Profile Photo" class="profile-photo">
                                    @else
                                        <img src="{{ asset('images/defaultProfile.png') }}" alt="Profile Photo" class="profile-photo">
                                    @endif
                                    <h5 class="user-name">{{ $user->first_name }} {{ $user->last_name }}</h5>
                                    <p class="member-since"><i class="bi bi-calendar-event me-1"></i>{{ T::translate('Member since', 'Miyembro magmula')}}: {{ $memberSince }}</p>
                                    <p class="mb-2">
                                        <span class="badge badge-status {{ $user->status == 'Active' ? 'badge-active' : 'badge-inactive' }}">
                                            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>{{ $user->status }}
                                        </span>
                                    </p>
                                    <p class="user-role"><i class="bi bi-person-workspace me-1"></i>{{ T::translate('Care Worker', 'Tagapag-alaga')}}</p>
                                </div>
                                
                                <div class="col-md-8">
                                    <div class="profile-header">
                                        <i class="bi bi-person-lines-fill"></i>
                                        <h5>{{ T::translate('Personal Information', 'Personal na Impormasyon')}}</h5>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <div class="info-row">
                                                    <div class="info-label"><i class="bi bi-person"></i>{{ T::translate('Full Name', 'Buong Pangalan')}}:</div>
                                                    <div class="info-value">{{ $user->first_name }} {{ $user->last_name }}</div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label"><i class="bi bi-calendar"></i>{{ T::translate('Date of Birth', 'Petsa ng Kapanganakan')}}:</div>
                                                    <div class="info-value">{{ $formattedBirthday ?? 'Not specified' }}</div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label"><i class="bi bi-gender-ambiguous"></i>{{ T::translate('Gender', 'Kasarian')}}:</div>
                                                    <div class="info-value">{{ $user->gender ?? 'Not specified' }}</div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label"><i class="bi bi-globe"></i>{{ T::translate('Nationality', 'Nasyonalidad')}}:</div>
                                                    <div class="info-value">{{ $user->nationality ?? 'Not specified' }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <div class="info-row">
                                                    <div class="info-label"><i class="bi bi-heart"></i>{{ T::translate('Marital Status', 'Katayuan sa Pag-aasawa')}}:</div>
                                                    <div class="info-value">{{ $user->civil_status ?? 'Not specified' }}</div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label"><i class="bi bi-mortarboard"></i>{{ T::translate('Educational Background', 'Background Pang-Edukasyon')}}:</div>
                                                    <div class="info-value">{{ $user->educational_background ?? 'Not specified' }}</div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label"><i class="bi bi-house-door"></i>{{ T::translate('Religion', 'Relihiyon')}}:</div>
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
                                            <div class="info-label"><i class="bi bi-envelope-at"></i>{{ T::translate('Work Email', 'Email sa Trabaho')}}:</div>
                                            <div class="info-value">{{ $user->email }}</div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label"><i class="bi bi-envelope"></i>{{ T::translate('Personal Email', 'Email sa Personal')}}:</div>
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
                                            <div class="info-label"><i class="bi bi-geo-alt"></i>{{ T::translate('Address', 'Tirahan')}}:</div>
                                            <div class="info-value">{{ $user->address }}</div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label"><i class="bi bi-geo"></i>{{ T::translate('Assigned Municipality', 'Nakatalagang Munisipalidad')}}:</div>
                                            <div class="info-value">{{ $user->municipality->municipality_name ?? 'Not assigned' }}</div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label"><i class="bi bi-person-badge"></i>{{ T::translate('Assigned Care Manager', 'Nakatalagang Care Manager')}}:</div>
                                            <div class="info-value">
                                                @if($user->assignedCareManager)
                                                    {{ $user->assignedCareManager->first_name }} {{ $user->assignedCareManager->last_name }}
                                                @else
                                                    Not assigned
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Account Settings Section -->
                        <div class="profile-section {{ session('activeTab') == 'settings' ? 'active' : '' }}" id="settings-section" style="display: none;">
                            <div class="profile-header">
                                <i class="bi bi-shield-lock"></i>
                                <h5>{{ T::translate('Account Settings', 'Mga Setting sa Accoung')}}</h5>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-row">
                                            <div class="info-label"><i class="bi bi-envelope-at"></i>Email:</div>
                                            <div class="info-value">{{ $user->email }}</div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label"><i class="bi bi-shield-check"></i>{{ T::translate('Account Status', 'Status ng Account')}}:</div>
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
                                    <i class="bi bi-envelope-arrow-up me-2"></i>{{ T::translate('Update Email', 'I-Update ang Email')}}
                                </button>
                                <button class="btn btn-primary" id="updatePasswordBtn">
                                    <i class="bi bi-key me-2"></i>{{ T::translate('Update Password', 'I-Update ang Password')}}
                                </button>
                            </div>
                            
                            <!-- Update Email Form -->
                            <div class="form-section" id="updateEmailForm" style="display: none;">
                                <h6><i class="bi bi-envelope-arrow-up"></i>{{ T::translate('Update Email Address', 'I-Update ang Email Address')}}</h6>
                                <form action="/care-worker/update-email" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="current_email" class="form-label"><i class="bi bi-envelope me-1"></i>{{ T::translate('Current Email', 'Kasalukuyang Email')}}</label>
                                        <input type="email" class="form-control" value="{{ $user->email }}" disabled>
                                    </div>
                                    <div class="mb-3">
                                        <label for="account_email" class="form-label"><i class="bi bi-envelope-plus me-1"></i>{{ T::translate('New Email', 'Bagong Email')}}</label>
                                        <input type="email" class="form-control" id="account_email" name="account_email" placeholder="{{ T::translate('', '')}}Enter new email address" value="{{ old('account_email') }}" required>
                                    </div>
                                    <div class="mb-3">
                                        <label for="current_password_for_email" class="form-label"><i class="bi bi-lock me-1"></i>{{ T::translate('Current Password', 'Kasalukuyang Password')}}</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="current_password_for_email" name="current_password" placeholder="{{ T::translate('Enter your password to confirm', 'Ilagay ang iyong password upang kumpirmahin')}}" required>
                                            <span class="input-group-text password-toggle" data-target="current_password_for_email">
                                                <i class="bi bi-eye-slash"></i>
                                            </span>
                                        </div>
                                        <small class="form-text text-muted">{{ T::translate('For security, please enter your current password to confirm this change.', 'Para sa seguridad, mangyaring ilagay ang kasalukuyang passowrd upang kumpirmahin ang pagbabagong ito.')}}</small>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-outline-secondary" id="cancelEmailUpdateBtn">
                                            <i class="bi bi-x-circle me-1"></i>{{ T::translate('Cancel', 'I-Kansela')}}
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle me-1"></i>{{ T::translate('Save Changes', 'I-save ang mga pagbabago')}}
                                        </button>
                                    </div>
                                </form>
                            </div>
                            
                            <!-- Update Password Form -->
                            <div class="form-section" id="updatePasswordForm" style="display: none;">
                                <h6><i class="bi bi-key"></i>{{ T::translate('Update Password', 'I-Update ang Password')}}</h6>
                                <form action="/care-worker/update-password" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label"><i class="bi bi-lock me-1"></i>{{ T::translate('Current Password', 'Kasalukuyang Password')}}</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="current_password" name="current_password" placeholder="{{ T::translate('Enter current password', 'Ilagay ang kasalukuyang password')}}" required>
                                            <span class="input-group-text password-toggle" data-target="current_password">
                                                <i class="bi bi-eye-slash"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="account_password" class="form-label"><i class="bi bi-key me-1"></i>{{ T::translate('New Password', 'Bagong Password')}}</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="account_password" name="account_password" placeholder="{{ T::translate('Enter new password', 'Ilagay ang bagong password')}}" required>
                                            <span class="input-group-text password-toggle" data-target="account_password">
                                                <i class="bi bi-eye-slash"></i>
                                            </span>
                                        </div>
                                        <!-- Password Strength Meter & Checklist -->
                                        <div id="password-strength-container" style="display:none; margin-top:10px;">
                                            <div id="password-strength-label" style="font-size:13px; font-weight:bold; margin-bottom:4px;"></div>
                                            <div id="password-strength-meter" style="height:8px; border-radius:4px; background:#e9ecef; margin-bottom:8px;">
                                                <div id="password-strength-bar" style="height:100%; width:0%; background:#dc3545; border-radius:4px; transition:width 0.3s;"></div>
                                            </div>
                                            <ul id="password-checklist" style="list-style:none; padding-left:0; font-size:13px;">
                                                <li id="pw-length" style="color:#dc3545;">&#10007; At least 8 characters</li>
                                                <li id="pw-uppercase" style="color:#dc3545;">&#10007; At least one uppercase letter</li>
                                                <li id="pw-lowercase" style="color:#dc3545;">&#10007; At least one lowercase letter</li>
                                                <li id="pw-number" style="color:#dc3545;">&#10007; At least one number</li>
                                                <li id="pw-special" style="color:#dc3545;">&#10007; At least one special character</li>                                            </ul>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="account_password_confirmation" class="form-label"><i class="bi bi-key-fill me-1"></i>{{ T::translate('Confirm Password', 'Kumpirmahin ang Password')}}</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="account_password_confirmation" name="account_password_confirmation" placeholder="{{ T::translate('Confirm new password', 'Kumpirmahin ang bagong password')}}" required>
                                            <span class="input-group-text password-toggle" data-target="account_password_confirmation">
                                                <i class="bi bi-eye-slash"></i>
                                            </span>
                                        </div>
                                        <ul id="password-checklist" style="list-style:none; padding-left:0; font-size:13px; margin-top:8px;">
                                                <li id="pw-match" style="color:#dc3545;">&#10007; Passwords match</li>
                                        </ul>
                                    </div>
                                    <div class="d-flex justify-content-end gap-2">
                                        <button type="button" class="btn btn-outline-secondary" id="cancelPasswordUpdateBtn">
                                            <i class="bi bi-x-circle me-1"></i>{{ T::translate('Cancel', 'I-Kansela')}}
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle me-1"></i>{{ T::translate('Save Changes', 'I-save ang mga pagbabago')}}
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

        // Password Meter & Checklist for Update Password
        function checkPasswordStrength(password) {
            let score = 0;
            if (password.length >= 8) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[a-z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;
            return score;
        }

        function updatePasswordChecklist() {
            const pw = document.getElementById('account_password').value;
            const confirm = document.getElementById('account_password_confirmation').value;

            // Show/hide meter
            const meterContainer = document.getElementById('password-strength-container');
            if (pw.length > 0 || confirm.length > 0) {
                meterContainer.style.display = 'block';
            } else {
                meterContainer.style.display = 'none';
            }

            // Checklist
            document.getElementById('pw-length').style.color = pw.length >= 8 ? '#198754' : '#dc3545';
            document.getElementById('pw-length').innerHTML = (pw.length >= 8 ? '&#10003;' : '&#10007;') + ' At least 8 characters';

            document.getElementById('pw-uppercase').style.color = /[A-Z]/.test(pw) ? '#198754' : '#dc3545';
            document.getElementById('pw-uppercase').innerHTML = (/[A-Z]/.test(pw) ? '&#10003;' : '&#10007;') + ' At least one uppercase letter';

            document.getElementById('pw-lowercase').style.color = /[a-z]/.test(pw) ? '#198754' : '#dc3545';
            document.getElementById('pw-lowercase').innerHTML = (/[a-z]/.test(pw) ? '&#10003;' : '&#10007;') + ' At least one lowercase letter';

            document.getElementById('pw-number').style.color = /[0-9]/.test(pw) ? '#198754' : '#dc3545';
            document.getElementById('pw-number').innerHTML = (/[0-9]/.test(pw) ? '&#10003;' : '&#10007;') + ' At least one number';

            document.getElementById('pw-special').style.color = /[^A-Za-z0-9]/.test(pw) ? '#198754' : '#dc3545';
            document.getElementById('pw-special').innerHTML = (/[^A-Za-z0-9]/.test(pw) ? '&#10003;' : '&#10007;') + ' At least one special character';

            const match = pw.length > 0 && pw === confirm;
            document.getElementById('pw-match').style.color = match ? '#198754' : '#dc3545';
            document.getElementById('pw-match').innerHTML = (match ? '&#10003;' : '&#10007;') + ' Passwords match';

            // Meter
            const score = checkPasswordStrength(pw);
            const bar = document.getElementById('password-strength-bar');
            bar.style.width = (score * 20) + '%';
            if (score <= 2) {
                bar.style.background = '#dc3545';
            } else if (score === 3 || score === 4) {
                bar.style.background = '#ffc107';
            } else {
                bar.style.background = '#198754';
            }

            const label = document.getElementById('password-strength-label');
            if (score <= 2) {
                label.textContent = 'Weak';
                label.style.color = '#dc3545';
            } else if (score === 3 || score === 4) {
                label.textContent = 'Fair';
                label.style.color = '#ffc107';
            } else {
                label.textContent = 'Strong';
                label.style.color = '#198754';
            }
        }

        document.getElementById('account_password').addEventListener('input', updatePasswordChecklist);
        document.getElementById('account_password_confirmation').addEventListener('input', updatePasswordChecklist);

    </script>
</body>
</html>