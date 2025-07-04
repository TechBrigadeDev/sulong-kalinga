<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sulong Kalinga</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon-white-16x16.png')}}">
    <style>
        :root {
            --cose-primary: #1a6e46;
            --cose-primary-dark: #0d4b2a;
            --cose-primary-light: #4c9d6f;
            --cose-secondary: #f5f0e6;
            --cose-accent: #e63946;
            --cose-text: #333333;
            --cose-text-light: #6c757d;
            --cose-bg-light: #f8f9fa;
            --cose-white: #ffffff;
            --cose-black: #212529;
            --navbar-height: 56px; /* Default Bootstrap navbar height */
        }

        .hero-section {
            min-height: calc(100vh - var(--navbar-height)); /* Account for navbar */
            display: flex;
            align-items: center;
            background: linear-gradient(135deg, var(--cose-secondary) 0%, var(--cose-white) 100%);
        }

        h1 {
            color: var(--cose-primary-dark);
            font-weight: 800;
            margin-bottom: 1.5rem;
            font-size: clamp(2rem, 5vw, 3rem);
            line-height: 1.2;
            letter-spacing: -0.05em;
        }

        .lead {
            color: var(--cose-text);
            font-size: 1.25rem;
            margin-bottom: 2.5rem;
            font-weight: 400;
        }

        .btn-group {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .btn-primary {
            background-color: var(--cose-primary);
            border-color: var(--cose-primary);
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .btn-primary:hover {
            background-color: var(--cose-primary-dark);
            border-color: var(--cose-primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .btn-outline {
            border: 2px solid var(--cose-primary);
            color: var(--cose-primary);
            background-color: transparent;
            padding: 0.75rem 2rem;
            font-weight: 600;
            border-radius: 50px;
            transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        .btn-outline:hover {
            background-color: var(--cose-primary);
            color: var(--cose-white);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .cose-logo-card {
            padding: 2rem 1.5rem !important;
            border-radius: 2.5rem !important;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .cose-logo {
            max-width: 380px !important;
            border-radius: 2rem !important;
        }
        .bg-pattern {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            opacity: 0.03;
            background-image: radial-gradient(var(--cose-primary) 1px, transparent 1px);
            background-size: 30px 30px;
            z-index: 0;
        }

        @media (max-width: 992px) {
            .hero-section {
                text-align: center;
                padding: 2rem 0;
            }
            
            .lead {
                max-width: 100%;
                margin-left: auto;
                margin-right: auto;
            }
            
            .btn-group {
                justify-content: center;
            }

            .cose-logo {
                margin-top: 2rem;
                max-width: 250px;
            }
        }

        @media (max-width: 576px) {
            .btn-group {
                flex-direction: column;
                gap: 0.75rem;
            }
            
            .btn-primary, .btn-outline {
                width: 100%;
            }
            
            .cose-logo {
                max-width: 200px;
            }
        }
    </style>
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