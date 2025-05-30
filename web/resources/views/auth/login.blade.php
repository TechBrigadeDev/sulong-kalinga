<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- <link rel="stylesheet" href="{{ asset('css/login.css') }}"> -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    
    <style>
        .password-input-group {
            position: relative;
        }
        
        .password-toggle {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            z-index: 10;
            color: #6c757d;
        }
        
        .password-toggle:hover {
            color: #2c7873;
        }
        
        /* Adjust padding for password input to prevent text from going under the icon */
        .password-input-group input[type="password"],
        .password-input-group input[type="text"] {
            padding-right: 40px;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(261deg,#2d7bfa,#88f1fb,#3ae07d,#3bd09c);
            background-size: 240% 240%;
            animation: gradient-animation 28s ease infinite;
            margin: 0;
            padding: 0;
            color: #333;
        }

        @keyframes gradient-animation {
            0% {
                background-position: 0% 50%;
            }
            50% {
                background-position: 100% 50%;
            }
            100% {
                background-position: 0% 50%;
            }
        }
        
        .overlay {
            /* background-color: rgba(255, 255, 255, 0.9); */
            min-height: calc(100vh - 60px);
            top: 56px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .login-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            width: 400px;
            padding: 10px 30px;
            text-align: center;
        }
        
        .logo {
            width: 90px;
            margin-bottom: 15px;
            margin-top: 15px;
        }
        
        h1 {
            color: #2c7873;
            margin-bottom: 10px;
            font-size: 20px;
        }
        
        .tagline {
            color: #6fb3b8;
            margin-bottom: 20px;
            font-style: italic;
        }
        
        .form-group {
            margin-bottom: 20px;
            text-align: left;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            color: #2c7873;
            font-weight: 600;
        }
        
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            box-sizing: border-box;
        }
        
        .btn {
            background-color: #2c7873;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            font-weight: 600;
            width: 100%;
            transition: background-color 0.3s;
        }
        
        .btn:hover {
            background-color: #235f5b;
        }
        
        .links {
            margin-top: 20px;
            font-size: 14px;
        }
        
        .links a {
            color: #6fb3b8;
            text-decoration: none;
        }
        
        .links a:hover {
            text-decoration: underline;
        }
        
        .mission-statement {
            margin-top: 10px;
            font-size: 14px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 20px;
        }
        
        
    </style>
</head>
<body>

    @include('components.navbar')

    <div class="overlay">
        <div class="login-container">
            <img src="{{ asset('images/cose-logo.png') }}" alt="" class="logo">
            <h1>Coalition of Services for the Elderly</h1>
            <p class="tagline">Empowering Senior Citizens Since 1989</p>
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
            <form action="{{ route('login') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email" placeholder="user@example.com" value = "{{ old('email') }}" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="password-input-group">
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                        <span class="password-toggle" data-target="password">
                            <i class="bi bi-eye-slash"></i>
                        </span>
                    </div>
                </div>

                <button type="submit" class="btn">Login</button>

                <div class="links">
                    <a href="{{ route('password.request') }}">Forgot password?</a>
                </div>
            </form>

            <div class="mission-statement">
                <p>COSE envisions a society where elderly individuals lead lives of dignity, maintain good health, are independent, and play active roles in community development.</p>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    <script>
        // Password visibility toggle
        document.addEventListener('DOMContentLoaded', function() {
            // Add click event to password toggle icon
            document.querySelector('.password-toggle').addEventListener('click', function() {
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
    </script>
</body>
</html>
