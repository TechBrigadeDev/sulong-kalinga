<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sulong Kalinga</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon-white-16x16.png')}}">
</head>
<body>

    @include('components.navbar')

    <section class="hero-section">
        <div class="bg-pattern"></div>
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7 col-md-6">
                    <h1>COSE: MOBILE HEALTHCARE SERVICE</h1> 
                    <p class="lead">One of COSE's flagship initiatives operating in Northern Samar. Delivering home-based healthcare service for older persons, ensuring they receive consistent medical attention.</p>
                    <div class="btn-group">
                        <a href="#" class="btn btn-outline">Learn More</a>
                        <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
                    </div>
                </div>
                <div class="col-lg-5 col-md-6 d-flex justify-content-center align-items-center">
                    <div class="card cose-logo-card shadow-lg border-0 p-4" style="background: var(--cose-primary); border-radius: 2rem;">
                        <img src="{{ asset('images/cose-logo-white.png') }}" alt="COSE Logo" class="cose-logo mb-3" style="background: var(--cose-primary); border-radius: 1.5rem;">
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    @include('components.footer')
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>