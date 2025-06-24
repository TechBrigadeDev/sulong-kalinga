<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | Beneficiary</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/viewProfile.css') }}">
    <link rel="stylesheet" href="{{ asset('css/viewProfile2.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>

    @php
    use App\Helpers\TranslationHelper as T;
    @endphp

    @include('components.beneficiaryPortalNavbar')
    @include('components.beneficiaryPortalSidebar')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="row justify-content-center" id="viewProfile">
                <div class="col-lg-12">
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
                                <h4 class="mb-0"><i class="bi bi-person-badge me-2" style="color: var(--complement-1);"></i>{{ T::translate('MY PROFILE', 'AKING PROFILE') }}</h4>
                            </div>
                        </div>
                        
                        <div class="breadcrumb-nav">
                            <nav aria-label="breadcrumb">
                                <ol class="breadcrumb mb-0">
                                    <li class="breadcrumb-item {{ session('activeTab') != 'contact' && session('activeTab') != 'settings' ? 'active' : '' }}" data-section="personal">
                                        <a href="javascript:void(0)"><i class="bi bi-person-vcard me-1"></i>{{ T::translate('Personal', 'Personal') }}</a>
                                    </li>
                                    <li class="breadcrumb-item {{ session('activeTab') == 'contact' ? 'active' : '' }}" data-section="contact">
                                        <a href="javascript:void(0)"><i class="bi bi-telephone me-1"></i>{{ T::translate('Contact', 'Contact') }}</a>
                                    </li>
                                    <li class="breadcrumb-item {{ session('activeTab') == 'settings' ? 'active' : '' }}" data-section="settings">
                                        <a href="javascript:void(0)"><i class="bi bi-gear me-1"></i>{{ T::translate('Settings', 'Mga Setting') }}</a>
                                    </li>
                                </ol>
                            </nav>
                        </div>
                        
                        <!-- Personal Information Section -->
                        <div class="profile-section {{ session('activeTab') != 'contact' && session('activeTab') != 'settings' ? 'active' : '' }}" id="personal-section">
                            <div class="row">
                                <div class="col-md-4 text-center mb-4 mb-md-0">
                                    @if($beneficiary->photo)
                                        <img src="{{ asset('storage/' . $beneficiary->photo) }}" alt="Profile Photo" class="profile-photo">
                                    @else
                                        <img src="{{ asset('images/defaultProfile.png') }}" alt="Profile Photo" class="profile-photo">
                                    @endif
                                    <h5 class="user-name">{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</h5>
                                    <p class="member-since"><i class="bi bi-calendar-event me-1"></i>{{ T::translate('Member since:', 'Miyembro magmula:') }} {{ $memberSince }}</p>
                                    <p class="mb-2">
                                        <span class="badge badge-status badge-active">
                                            <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>{{ T::translate('Active', 'Aktibo') }}
                                        </span>
                                    </p>
                                    <p class="user-role"><i class="bi bi-person-heart me-1"></i>{{ $beneficiary->category->category_name ?? T::translate('Beneficiary', 'Benepisyaryo') }}</p>
                                </div>
                                
                                <div class="col-md-8">
                                    <div class="profile-header">
                                        <i class="bi bi-person-lines-fill"></i>
                                        <h5>{{ T::translate('Personal Information', 'Personal na Impormasyon') }}</h5>
                                    </div>
                                    
                                    <div class="row">
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <div class="info-row">
                                                    <div class="info-label"><i class="bi bi-person"></i>{{ T::translate('Full Name:', 'Buong Pangalan:') }}</div>
                                                    <div class="info-value">{{ $beneficiary->first_name }} {{ $beneficiary->middle_name ?? '' }} {{ $beneficiary->last_name }}</div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label"><i class="bi bi-calendar"></i>{{ T::translate('Date of Birth:', 'Petsa ng Kapanganakan:') }}</div>
                                                    <div class="info-value">{{ $formattedBirthday ?? T::translate('Not specified', 'Hindi tinukoy') }}</div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label"><i class="bi bi-gender-ambiguous"></i>{{ T::translate('Gender:', 'Kasarian:') }}</div>
                                                    <div class="info-value">{{ $beneficiary->gender ?? T::translate('Not specified', 'Hindi tinukoy') }}</div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label"><i class="bi bi-heart"></i>{{ T::translate('Civil Status:', 'Katayuang Sibil:') }}</div>
                                                    <div class="info-value">{{ $beneficiary->civil_status ?? T::translate('Not specified', 'Hindi tinukoy') }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="col-md-6">
                                            <div class="info-card">
                                                <div class="info-row">
                                                    <div class="info-label"><i class="bi bi-person-heart"></i>{{ T::translate('Age:', 'Edad:') }}</div>
                                                    <div class="info-value">{{ $age ?? T::translate('Not specified', 'Hindi tinukoy') }} {{ T::translate('years old', 'taong gulang') }}</div>
                                                </div>
                                                <div class="info-row">
                                                    <div class="info-label"><i class="bi bi-person-plus"></i>{{ T::translate('Primary Caregiver:', 'Pangunahing Tagapag-alaga:') }}</div>
                                                    <div class="info-value">{{ $beneficiary->primary_caregiver ?? T::translate('Not specified', 'Hindi tinukoy') }}</div>
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
                                <h5>{{ T::translate('Contact Information', 'Impormasyon ng Contact') }}</h5>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-row">
                                            <div class="info-label"><i class="bi bi-person-badge"></i>{{ T::translate('Username:', 'Username:') }}</div>
                                            <div class="info-value">{{ $beneficiary->username }}</div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label"><i class="bi bi-phone"></i>{{ T::translate('Mobile Phone:', 'Mobile Phone:') }}</div>
                                            <div class="info-value">{{ $beneficiary->mobile ?? T::translate('Not specified', 'Hindi tinukoy') }}</div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label"><i class="bi bi-telephone"></i>{{ T::translate('Landline:', 'Landline:') }}</div>
                                            <div class="info-value">{{ $beneficiary->landline ?? T::translate('Not specified', 'Hindi tinukoy') }}</div>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-row">
                                            <div class="info-label"><i class="bi bi-geo-alt"></i>{{ T::translate('Address:', 'Tirahan:') }}</div>
                                            <div class="info-value">
                                                {{ $beneficiary->street_address }}, 
                                                {{ $beneficiary->barangay->barangay_name ?? '' }}, 
                                                {{ $beneficiary->municipality->municipality_name ?? '' }}
                                            </div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label"><i class="bi bi-exclamation-triangle"></i>{{ T::translate('Emergency Contact:', 'Emergency Contact:') }}</div>
                                            <div class="info-value">{{ $beneficiary->emergency_contact_name }} ({{ $beneficiary->emergency_contact_relation }})</div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label"><i class="bi bi-telephone-plus"></i>{{ T::translate('Emergency Mobile:', 'Emergency Mobile:') }}</div>
                                            <div class="info-value">{{ $beneficiary->emergency_contact_mobile }}</div>
                                        </div>
                                        @if($beneficiary->emergency_contact_email)
                                        <div class="info-row">
                                            <div class="info-label"><i class="bi bi-envelope-plus"></i>{{ T::translate('Emergency Email:', 'Emergency Email:') }}</div>
                                            <div class="info-value">{{ $beneficiary->emergency_contact_email }}</div>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Account Settings Section -->
                        <div class="profile-section {{ session('activeTab') == 'settings' ? 'active' : '' }}" id="settings-section" style="display: none;">
                            <div class="profile-header">
                                <i class="bi bi-shield-lock"></i>
                                <h5>{{ T::translate('Account Settings', 'Mga Setting ng Account') }}</h5>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="info-card">
                                        <div class="info-row">
                                            <div class="info-label"><i class="bi bi-person-badge"></i>{{ T::translate('Username:', 'Username:') }}</div>
                                            <div class="info-value">{{ $beneficiary->username }}</div>
                                        </div>
                                        <div class="info-row">
                                            <div class="info-label"><i class="bi bi-shield-check"></i>{{ T::translate('Account Status:', 'Status ng Account:') }}</div>
                                            <div class="info-value">
                                                <span class="badge badge-status badge-active">
                                                    <i class="bi bi-circle-fill me-1" style="font-size: 0.5rem;"></i>{{ T::translate('Active', 'Aktibo') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="d-flex justify-content-end mt-4">
                                <button class="btn btn-primary" id="updatePasswordBtn">
                                    <i class="bi bi-key me-2"></i>{{ T::translate('Update Password', 'I-update ang Password') }}
                                </button>
                            </div>
                            
                            <!-- Update Password Form -->
                            <div class="form-section" id="updatePasswordForm" style="display: none;">
                                <h6><i class="bi bi-key"></i>{{ T::translate('Update Password', 'I-update ang Password') }}</h6>
                                <form action="{{ route('beneficiary.profile.update-password') }}" method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <label for="current_password" class="form-label"><i class="bi bi-lock me-1"></i>{{ T::translate('Current Password', 'Kasalukuyang Password') }}</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="current_password" name="current_password" placeholder="{{ T::translate('Enter current password', 'Ilagay ang kasalukuyang password') }}" required>
                                            <span class="input-group-text password-toggle" data-target="current_password">
                                                <i class="bi bi-eye-slash"></i>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="account_password" class="form-label"><i class="bi bi-key me-1"></i>{{ T::translate('New Password', 'Bagong Password') }}</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="account_password" name="account_password" placeholder="{{ T::translate('Enter new password', 'Ilagay ang bagong password') }}" required>
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
                                        <label for="account_password_confirmation" class="form-label"><i class="bi bi-key-fill me-1"></i>{{ T::translate('Confirm Password', 'Kumpirmahin ang Password') }}</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="account_password_confirmation" name="account_password_confirmation" placeholder="{{ T::translate('Confirm new password', 'Kumpirmahin ang bagong password') }}" required>
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
                                            <i class="bi bi-x-circle me-1"></i>{{ T::translate('Cancel', 'I-Kansela') }}
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-check-circle me-1"></i>{{ T::translate('Save Changes', 'I-save ang mga Pagbabago') }}
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
        });

        // Cancel the Update Password Form
        document.getElementById('cancelPasswordUpdateBtn').addEventListener('click', function () {
            document.getElementById('updatePasswordForm').style.display = 'none';
            document.getElementById('updatePasswordBtn').style.display = 'inline-block';
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