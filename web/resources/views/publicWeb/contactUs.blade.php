<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us | Sulong Kalinga</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon-white-16x16.png')}}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <style>
        :root {
            --cose-primary: #1a6e46;       /* Primary brand green */
            --cose-primary-dark: #0d4b2a;  /* Darker green for contrast */
            --cose-primary-light: #4c9d6f; /* Lighter green */
            --cose-secondary: #f5f0e6;     /* Beige for backgrounds */
            --cose-accent: #e63946;        /* Red for important actions */
            --cose-text: #333333;          /* High contrast text */
            --cose-text-light: #6c757d;    /* Secondary text */
            --cose-bg-light: #f8f9fa;      /* Light background */
            --cose-white: #ffffff;         /* Pure white */
            --cose-black: #212529;         /* Almost black */
        }
        
        body {
            background-color: var(--cose-secondary);
            color: var(--cose-text);
        }
        
        .contact-container {
            min-height: calc(100vh - 120px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }
        
        .contact-card {
            background-color: var(--cose-white);
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        
        .contact-card img {
            max-width: 150px;
            margin-bottom: 1.5rem;
        }
        
        .contact-card h1 {
            color: var(--cose-primary);
            margin-bottom: 1.5rem;
        }
        
        .contact-info {
            margin: 1.5rem 0;
        }
        
        .contact-info p {
            margin-bottom: 0.5rem;
        }
        
        .contact-info i {
            color: var(--cose-primary-light);
            margin-right: 0.5rem;
        }
    </style>
</head>
<body>

    @include('components.navbar')

    <div class="contact-container">
        <div class="contact-card">
            <img src="{{ asset('images/cose-logo.png')}}" alt="Sulong Kalinga Logo">
            <h1>CONTACT US</h1>
            <div class="contact-info">
                <p><i class="bi bi-envelope-fill"></i> hello@sulongkalinga.com</p>
                <p><i class="bi bi-telephone-fill"></i> +63 123 456 7890</p>
                <p><i class="bi bi-geo-alt-fill"></i> 123 Main Street, City, Philippines</p>
            </div>
            <p>We'd love to hear from you! Whether you have questions about our services or want to get involved, our team is ready to help.</p>
        </div>
    </div>
    
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
</body>
</html>