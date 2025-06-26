<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/forgotPass.css') }}">
</head>
<body>
    @include('components.navbar')

    <div class="container d-flex justify-content-center align-items-center min-vh-100" id="reset-password">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="text-center">Reset Password</h2>

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif
                        
                        <form action="{{ route('password.update') }}" method="POST">
                            @csrf
                            <input type="hidden" name="token" value="{{ $token }}">
                            <input type="hidden" name="email" value="{{ $email }}">
                            <input type="hidden" name="type" value="{{ old('type', $type) }}">
                            
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" value="{{ $email }}" readonly>
                            </div>
                            
                            <div class="mb-3">
                                <label class="form-label">New Password</label>
                                <div class="input-group">
                                    <input type="password" name="password" class="form-control" id="password" required>
                                    <span class="input-group-text password-toggle" data-target="password" style="cursor:pointer;">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                </div>
                                @error('password')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-2">
                                <div id="password-strength-meter" class="progress hide-until-typing" style="height: 6px;">
                                    <div id="password-strength-bar" class="progress-bar" role="progressbar" style="width: 0%;"></div>
                                </div>
                                <small id="password-strength-text" class="form-text hide-until-typing"></small>
                            </div>

                            <div class="mb-2">
                                <ul id="password-checklist" class="list-unstyled small mb-4 hide-until-typing">
                                    <li id="check-length" class="text-danger"><i class="bi bi-x-circle"></i> At least 8 characters</li>
                                    <li id="check-upper-lower" class="text-danger"><i class="bi bi-x-circle"></i> Upper &amp; lower case</li>
                                    <li id="check-number" class="text-danger"><i class="bi bi-x-circle"></i> At least one number</li>
                                    <li id="check-symbol" class="text-danger"><i class="bi bi-x-circle"></i> At least one symbol</li>
                                </ul>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Confirm New Password</label>
                                <div class="input-group">
                                    <input type="password" name="password_confirmation" class="form-control" id="password_confirmation" required>
                                    <span class="input-group-text password-toggle" data-target="password_confirmation" style="cursor:pointer;">
                                        <i class="bi bi-eye-slash"></i>
                                    </span>
                                </div>
                            </div>

                            <div class="mb-6">
                                <small id="password-match-text" class="form-text"></small>
                            </div>
                            
                            <button type="submit" class="btn btn-primary w-100">Reset Password</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <!-- Add Bootstrap Icons CDN in your <head> if not already present -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <script>
        function checkPasswordStrength(password) {
            let score = 0;
            if (password.length >= 8) score++;
            if (/[a-z]/.test(password) && /[A-Z]/.test(password)) score++;
            if (/\d/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;

            return score;
        }

        function getStrengthLabel(score, length) {
            if (length === 0) return {text: '', class: ''};
            if (length < 8) return {text: 'At least 8 characters required', class: 'weak'};
            switch (score) {
                case 1: return {text: 'Weak', class: 'weak'};
                case 2: return {text: 'Fair', class: 'fair'};
                case 3: return {text: 'Good', class: 'good'};
                case 4: return {text: 'Strong', class: 'strong'};
                default: return {text: 'Weak', class: 'weak'};
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            const toggleButtons = document.querySelectorAll('.password-toggle');
            const meter = document.getElementById('password-strength-meter');
            const meterText = document.getElementById('password-strength-text');
            const checklistEl = document.getElementById('password-checklist');

            toggleButtons.forEach(button => {
                button.addEventListener('click', function() {
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

            // Password strength meter
            const passwordInput = document.getElementById('password');
            const passwordStrengthBar = document.getElementById('password-strength-bar');
            const passwordStrengthText = document.getElementById('password-strength-text');
            const passwordConfirmation = document.getElementById('password_confirmation');
            const passwordMatchText = document.getElementById('password-match-text');

            const checklist = {
                length: document.getElementById('check-length'),
                upperLower: document.getElementById('check-upper-lower'),
                number: document.getElementById('check-number'),
                symbol: document.getElementById('check-symbol')
            };

            function updateChecklist(password) {
                // Length
                if (password.length >= 8) {
                    checklist.length.className = 'text-success';
                    checklist.length.querySelector('i').className = 'bi bi-check-circle';
                } else {
                    checklist.length.className = 'text-danger';
                    checklist.length.querySelector('i').className = 'bi bi-x-circle';
                }
                // Upper & lower
                if (/[a-z]/.test(password) && /[A-Z]/.test(password)) {
                    checklist.upperLower.className = 'text-success';
                    checklist.upperLower.querySelector('i').className = 'bi bi-check-circle';
                } else {
                    checklist.upperLower.className = 'text-danger';
                    checklist.upperLower.querySelector('i').className = 'bi bi-x-circle';
                }
                // Number
                if (/\d/.test(password)) {
                    checklist.number.className = 'text-success';
                    checklist.number.querySelector('i').className = 'bi bi-check-circle';
                } else {
                    checklist.number.className = 'text-danger';
                    checklist.number.querySelector('i').className = 'bi bi-x-circle';
                }
                // Symbol
                if (/[^A-Za-z0-9]/.test(password)) {
                    checklist.symbol.className = 'text-success';
                    checklist.symbol.querySelector('i').className = 'bi bi-check-circle';
                } else {
                    checklist.symbol.className = 'text-danger';
                    checklist.symbol.querySelector('i').className = 'bi bi-x-circle';
                }
            }

            function updateStrength() {
                const password = passwordInput.value;
                const score = checkPasswordStrength(password);
                const label = getStrengthLabel(score, password.length);

                passwordStrengthBar.style.width = (score / 4 * 100) + '%';
                passwordStrengthBar.className = 'progress-bar ' + label.class;
                passwordStrengthText.textContent = label.text;
                passwordStrengthText.className = 'form-text text-' + (label.class === 'weak' ? 'danger' : label.class === 'fair' ? 'warning' : label.class === 'good' ? 'info' : 'success');
            }

            function updateMatch() {
                if (!passwordConfirmation.value) {
                    passwordMatchText.textContent = '';
                    return;
                }
                if (passwordInput.value === passwordConfirmation.value) {
                    passwordMatchText.textContent = 'Passwords match';
                    passwordMatchText.className = 'form-text text-success';
                } else {
                    passwordMatchText.textContent = 'Passwords do not match';
                    passwordMatchText.className = 'form-text text-danger';
                }
            }

            // In your passwordInput event listener:
            passwordInput.addEventListener('input', function() {
                // Show meter and checklist when user starts typing
                if (passwordInput.value.length > 0) {
                    meter.classList.remove('hide-until-typing');
                    meterText.classList.remove('hide-until-typing');
                    checklistEl.classList.remove('hide-until-typing');
                } else {
                    meter.classList.add('hide-until-typing');
                    meterText.classList.add('hide-until-typing');
                    checklistEl.classList.add('hide-until-typing');
                }
                updateStrength();
                updateMatch();
                updateChecklist(passwordInput.value);
            });

            // Optionally, hide on page load if field is empty
            if (!passwordInput.value) {
                meter.classList.add('hide-until-typing');
                meterText.classList.add('hide-until-typing');
                checklistEl.classList.add('hide-until-typing');
            }

            // Also call once on page load (in case of autofill)
            updateChecklist(passwordInput.value);

            passwordConfirmation.addEventListener('input', updateMatch);
        });

    </script>

</body>
</html>