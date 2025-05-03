<!-- 
    This is a Laravel Blade template for a "Forgot Password" page.
    It includes a form for users to enter their email address to receive a password reset link.
    The page has a responsive design and uses Bootstrap for styling.
    It is a temporary form that will be replaced with a modal in the future when implementation is ready with a mailing host. -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <style>
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
            min-height: calc(100vh - 60px);
            top: 56px;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .card {
            width: 400px;
            border-radius: 10px;
        }
        
        .btn-primary {
            background-color: #2c7873;
            border-color: #2c7873;
        }
        
        .btn-primary:hover {
            background-color: #235f5b;
            border-color: #235f5b;
        }
    </style>
</head>
<body>
    @include('components.navbar')

    <div class="overlay">
        <div class="card shadow">
            <div class="card-body p-4">
                <h2 class="text-center mb-4">Forgot Password</h2>
                
                @if (session('status'))
                    <div class="alert alert-success mb-4">
                        {{ session('status') }}
                    </div>
                @endif
                
                <form action="{{ route('password.email') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="email" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" 
                               placeholder="Enter your email" required>
                        @error('email')
                            <div class="text-danger mt-1">
                                {{ $message }}
                            </div>
                        @enderror
                    </div>
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary">Send Password Reset Link</button>
                        <a href="{{ route('login') }}" class="btn btn-outline-secondary">Back to Login</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>

<!-- <!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/forgotPass.css') }}">
</head>
<body>
    @include('components.navbar')

    <div class="container text-center" id="forgot-password">
        <div class="row justify-content-center">
            <div class="col">
            
            </div>
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body">
                        <h2 class="text-center">Forgot Password</h2>
                        <form action="{{ route('password.email') }}" method="POST">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email') }}" 
                                    placeholder="lastnameFirstname@example.com" required>
                                @error('email')
                                    <div class="text-danger mt-1">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            @if(session('status'))
                                <div class="alert alert-success mb-3">
                                    {{ session('status') }}
                                </div>
                            @endif
                            <button type="submit" class="btn btn-primary w-100">Send Reset Link</button>
                        </form>
                        <div class="backtoLogin">
                        <a href="/login">Back to Login</a>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col">

            </div>
        </div>
    </div>

    @include('components.modals.resetRequestSuccess')

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        document.getElementById('forgotPasswordForm').addEventListener('submit', function(event) {
            event.preventDefault();
            var myModal = new bootstrap.Modal(document.getElementById('exampleModal'));
            myModal.show();
            this.reset();
        });
    </script>

</body>
</html> 
-->