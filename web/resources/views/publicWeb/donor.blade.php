<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To Our Donors | Sulong Kalinga</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon-white-16x16.png')}}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
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
        }
        
        body {
            color: var(--cose-text);
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            line-height: 1.6;
        }
        
        .hero-gradient {
            background: linear-gradient(135deg, var(--cose-primary-dark) 0%, var(--cose-primary) 100%);
        }
        
        .gold-card {
            border-left: 4px solid #FFD700;
            background: linear-gradient(to right, rgba(255, 215, 0, 0.05), var(--cose-white));
            transition: all 0.3s ease;
        }
        
        .silver-card {
            border-left: 4px solid #C0C0C0;
            background: linear-gradient(to right, rgba(192, 192, 192, 0.05), var(--cose-white));
            transition: all 0.3s ease;
        }
        
        .bronze-card {
            border-left: 4px solid #CD7F32;
            background: linear-gradient(to right, rgba(205, 127, 50, 0.05), var(--cose-white));
            transition: all 0.3s ease;
        }
        
        .donor-avatar {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border: 3px solid var(--cose-white);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        
        .tier-icon {
            font-size: 2rem;
            margin-bottom: 1rem;
        }
        
        .appreciation-card {
            border: none;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            transition: transform 0.3s, box-shadow 0.3s;
            background-color: var(--cose-white);
            border-radius: 12px;
        }
        
        .appreciation-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.12);
        }
        
        .card {
            border-radius: 10px;
            overflow: hidden;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            transition: all 0.3s ease;
        }
        
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.1);
        }
        
        .btn-donate {
            background-color: var(--cose-accent);
            color: white;
            font-weight: 600;
            padding: 0.75rem 2rem;
            border-radius: 50px;
            transition: all 0.3s ease;
        }
        
        .btn-donate:hover {
            background-color: #c1121f;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(230, 57, 70, 0.3);
        }
        
        .section-title {
            position: relative;
            display: inline-block;
            margin-bottom: 2rem;
        }
        
        .section-title:after {
            content: '';
            position: absolute;
            bottom: -10px;
            left: 0;
            width: 50px;
            height: 4px;
            background-color: var(--cose-primary);
            border-radius: 2px;
        }
        
        .bg-light-custom {
            background-color: var(--cose-secondary);
        }
        
        .text-primary-cose {
            color: var(--cose-primary);
        }
        
        .lead-custom {
            font-size: 1.15rem;
            color: var(--cose-text-light);
        }
        
        .quote-icon {
            color: var(--cose-primary-light);
            opacity: 0.3;
        }
    </style>
</head>
<body>
    @include('components.navbar')

    <!-- Hero Section -->
    <header class="hero-gradient text-white py-5">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h1 class="display-4 fw-bold mb-4">With Deepest Gratitude</h1>
                    <p class="lead mb-4">To our compassionate donors who make our Mobile Health Care Service possible - your generosity brings healthcare, dignity, and hope to Filipino elders every day.</p>
                    <p>Because of you, we've been able to provide essential medical care to over 800 senior citizens across 34 barangays in Northern Samar. Each donation represents a life touched, a story changed, and a community strengthened.</p>
                </div>
                <div class="col-lg-4 text-center">
                    <i class="bi bi-heart-fill display-1 text-white opacity-25"></i>
                </div>
            </div>
        </div>
    </header>

    <!-- Appreciation Message -->
    <section class="py-5 bg-light-custom">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-10 text-center">
                    <div class="appreciation-card p-4 p-md-5">
                        <i class="bi bi-quote display-4 quote-icon mb-4"></i>
                        <h2 class="mb-4">Your Compassion Changes Lives</h2>
                        <p>At COSE, we witness daily how your support transforms the lives of vulnerable elders. From the grandmother who can now receive her medications at home to the grandfather who finally gets his blood pressure monitored regularly - these moments of care and dignity are made possible by you.</p>
                        <p class="lead">Your belief in our mission fuels our Mobile Health Care Service, allowing us to reach remote communities where healthcare access is limited. Together, we're building a Philippines where no elder is left without the care they deserve.</p>
                        <p class="fw-bold mt-4 text-primary-cose">From all of us at COSE - maraming salamat for your compassionate support.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Donor Tiers -->
    <section class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold section-title">Our Generous Supporters</h2>
                <p class="lead-custom">Recognizing the organizations and individuals who make our work possible</p>
            </div>

            <!-- Gold Tier -->
            <div class="row mb-5">
                <div class="col-12 mb-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-trophy-fill tier-icon text-warning me-3"></i>
                        <div>
                            <h3 class="mb-1">Visionary Partners</h3>
                            <p class="text-muted">Our most dedicated supporters who provide transformative gifts</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100 gold-card">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <img src="https://via.placeholder.com/150" alt="HelpAge" class="donor-avatar rounded-circle">
                            </div>
                            <h4 class="text-center">HelpAge International</h4>
                            <p class="text-center text-muted">Founding Partner</p>
                            <p class="text-center mt-3">"Your pioneering support helped establish COSE's foundation. We're forever grateful for your vision in elder care."</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100 gold-card">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <img src="https://via.placeholder.com/150" alt="PhilHealth" class="donor-avatar rounded-circle">
                            </div>
                            <h4 class="text-center">PhilHealth</h4>
                            <p class="text-center text-muted">Healthcare Champion</p>
                            <p class="text-center mt-3">"Your commitment to health coverage ensures our elders receive consistent medical attention."</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100 gold-card">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <img src="https://via.placeholder.com/150" alt="Northern Samar" class="donor-avatar rounded-circle">
                            </div>
                            <h4 class="text-center">Northern Samar LGU</h4>
                            <p class="text-center text-muted">Regional Leader</p>
                            <p class="text-center mt-3">"Your partnership allows us to reach elders across 34 barangays with vital healthcare services."</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Silver Tier -->
            <div class="row mb-5">
                <div class="col-12 mb-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-award-fill tier-icon text-secondary me-3"></i>
                        <div>
                            <h3 class="mb-1">Sustaining Partners</h3>
                            <p class="text-muted">Our steadfast supporters who provide consistent, meaningful support</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100 silver-card">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <img src="https://via.placeholder.com/150" alt="Mondragon" class="donor-avatar rounded-circle">
                            </div>
                            <h4 class="text-center">Mondragon LGU</h4>
                            <p class="text-center text-muted">Community Builder</p>
                            <p class="text-center mt-3">"Your local support helps us maintain services in 18 barangays, reaching elders where they live."</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100 silver-card">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <img src="https://via.placeholder.com/150" alt="San Roque" class="donor-avatar rounded-circle">
                            </div>
                            <h4 class="text-center">San Roque LGU</h4>
                            <p class="text-center text-muted">Community Builder</p>
                            <p class="text-center mt-3">"Your dedication ensures 16 barangays receive regular health visits for their elderly residents."</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100 silver-card">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <img src="https://via.placeholder.com/150" alt="Botika Binhi" class="donor-avatar rounded-circle">
                            </div>
                            <h4 class="text-center">Botika Binhi</h4>
                            <p class="text-center text-muted">Medicine Access</p>
                            <p class="text-center mt-3">"Your network provides affordable medicines to elders who need them most."</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bronze Tier -->
            <div class="row mb-5">
                <div class="col-12 mb-4">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-heart-fill tier-icon text-danger me-3"></i>
                        <div>
                            <h3 class="mb-1">Community Champions</h3>
                            <p class="text-muted">Our local heroes who make a difference in their communities</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100 bronze-card">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <img src="https://via.placeholder.com/150" alt="Bulacan" class="donor-avatar rounded-circle">
                            </div>
                            <h4 class="text-center">Bulacan Senior Citizens</h4>
                            <p class="text-center text-muted">Group Home Advocates</p>
                            <p class="text-center mt-3">"Your care for abandoned elders provides safety and dignity to those who need it most."</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100 bronze-card">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <img src="https://via.placeholder.com/150" alt="Women's Club" class="donor-avatar rounded-circle">
                            </div>
                            <h4 class="text-center">Mondragon Women's Club</h4>
                            <p class="text-center text-muted">Elder Advocates</p>
                            <p class="text-center mt-3">"Your support empowers elderly women to live with independence and pride."</p>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4 mb-4">
                    <div class="card h-100 bronze-card">
                        <div class="card-body">
                            <div class="text-center mb-3">
                                <img src="https://via.placeholder.com/150" alt="Volunteers" class="donor-avatar rounded-circle">
                            </div>
                            <h4 class="text-center">San Roque Volunteers</h4>
                            <p class="text-center text-muted">Local Caregivers</p>
                            <p class="text-center mt-3">"Your hands-on care brings comfort to elders in your community every day."</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Individual Donors -->
    <section class="py-5 bg-light-custom">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold section-title">Individual Donors</h2>
                <p class="lead-custom">Every gift matters - thank you to our compassionate individual supporters</p>
            </div>
            
            <div class="row row-cols-2 row-cols-md-4 g-4">
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <img src="https://via.placeholder.com/100" alt="Dr. Maria" class="rounded-circle mb-3" width="60">
                            <h5 class="mb-1">Dr. Maria Santos</h5>
                            <p class="small text-muted">Monthly Donor</p>
                            <p class="small">"Your consistent support provides regular care for 2 elders every month."</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <img src="https://via.placeholder.com/100" alt="Juan" class="rounded-circle mb-3" width="60">
                            <h5 class="mb-1">Mr. Juan Dela Cruz</h5>
                            <p class="small text-muted">Memorial Gift</p>
                            <p class="small">"Your tribute to Lola Nena continues her legacy of care."</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <img src="https://via.placeholder.com/100" alt="Reyes" class="rounded-circle mb-3" width="60">
                            <h5 class="mb-1">The Reyes Family</h5>
                            <p class="small text-muted">Legacy Donors</p>
                            <p class="small">"Your 8 years of support have touched countless lives."</p>
                        </div>
                    </div>
                </div>
                <div class="col">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center">
                            <img src="https://via.placeholder.com/100" alt="Anonymous" class="rounded-circle mb-3" width="60">
                            <h5 class="mb-1">Anonymous</h5>
                            <p class="small text-muted">Quiet Benefactor</p>
                            <p class="small">"Your generosity supports 5 elders with all their healthcare needs."</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Final Call to Action -->
    <section class="py-5 text-white hero-gradient">
        <div class="container py-4">
            <div class="row align-items-center">
                <div class="col-lg-8">
                    <h2 class="fw-bold mb-3">Join Our Circle of Compassion</h2>
                    <p class="lead mb-4">Your support directly impacts the lives of Filipino elders through our Mobile Health Care Service. Together, we can ensure no senior is left without care.</p>
                    <p>Every donation, no matter the size, helps provide:</p>
                    <ul class="mb-4">
                        <li>Home health visits for immobile elders</li>
                        <li>Medications for chronic conditions</li>
                        <li>Regular health monitoring</li>
                        <li>Dignity and care in their golden years</li>
                    </ul>
                </div>
                <div class="col-lg-4 text-center">
                    <a href="#" class="btn btn-donate btn-lg px-4 py-3 fw-bold">
                        <i class="bi bi-heart-fill me-2"></i>Donate Now
                    </a>
                    <p class="small mt-2 text-white-50">100% of donations support elder care services</p>
                </div>
            </div>
        </div>
    </section>

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>