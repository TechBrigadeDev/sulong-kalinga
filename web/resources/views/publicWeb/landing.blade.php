<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sulong Kalinga</title>
    <link rel="stylesheet" href="{{ asset('css/landing.css')}}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Brygada+1918:ital,wght@0,400..700;1,400..700&family=Cormorant+SC:wght@300;400;500;600;700&family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
    <style>
    
    img.logo{
        padding: 10px;
        border-radius: 23px;
        box-shadow: 0 5px 15px 0px rgba(0,0,0,0.6);
        transform: translatey(0px);
        animation: float 6s ease-in-out infinite;
        img { width: 80%; height: auto; }
    }
    @keyframes float {
	0% {
		box-shadow: 0 5px 15px 0px rgba(0,0,0,0.6);
		transform: translatey(0px);
	}
	50% {
		box-shadow: 0 25px 15px 0px rgba(0,0,0,0.2);
		transform: translatey(-20px);
	}
	100% {
		box-shadow: 0 5px 15px 0px rgba(0,0,0,0.6);
		transform: translatey(0px);
	}
}
    </style>
</head>
<body>

    @include('components.navbar')

    <div class="container text-center" id="landing">
        
        <div class="mobile-logo-container d-md-none text-center mb-5">
            <img src="{{ asset('images/cose-logo.png') }}" class="mobile-logo" alt="COSE Logo">
        </div>
        
        <div class="row align-items-center justify-content-center mt-0" >
            <div class="col-lg-7 col-md-7 col-sm-12" style="text-align: left;">
            <h1>COSE: MOBILE HEALTHCARE SERVICE</h1> 
                <p class="lead">One of COSE's flagship initiatives operating in Northern Samar. Delivering home-based healthcare service for older persons, ensuring they receive consistent medical attention.</p>
                <a href="#" class="btn btn-outline-dark">Learn More</a>
                <a href="{{ route('login') }}" class="btn btn-primary">Login</a>
            </div>
            <div class="col-1">

            </div>
            <div class="col-4" >
                <div class="logo">
                    <img src="{{ asset('images/cose-logo.png') }}" alt="" class="logo">
                </div>
            </div>
        </div>
        <div class="techBrigade">
            <img src="{{ asset('images/collage2.png') }}" class="half-transparent-img">
        </div>
        
        <img src="{{ asset('images/collage2.png') }}" class="half-transparent-img-mobile d-md-none">
    </div>
    
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>