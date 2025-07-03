<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Our Mobile Healthcare Service | COSE</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/aboutUs.css') }}">
    <link rel="stylesheet" href="{{ asset('css/events.css') }}">
</head>
<body>
    @include('components.navbar')

    <!-- Header Banner - Matches events page structure -->
    <div class="header-banner" style="background: linear-gradient(rgba(0,0,0,0.8), rgba(0,0,0,0.6)), url({{ asset('images/COSE.jpg') }}); background-size: cover;background-position: center;background-attachment: fixed;">
        <div class="header-content container">
            <h1 class="display-5 fw-bold mb-3 animate-on-scroll">About Mobile Healthcare Service</h1>
            <p class="lead mb-4 animate-on-scroll">Bringing comprehensive healthcare to elderly Filipinos in their communities</p>
        </div>
    </div>

    <!-- Hero Card - Similar to event cards container -->
    <section class="container">
        <div class="hero-card animate-on-scroll">
            <h2 class="section-title">Healthcare Where It's Needed Most</h2>
            <p class="fs-5">The Coalition of Services for the Elderly (COSE) operates our Mobile Healthcare Service (MHCS) to address the critical healthcare needs of older persons in remote and underserved communities across the Philippines, with special focus on Northern Samar.</p>
        </div>
    </section>

    <!-- Stats Section - Consistent grid layout -->
    <section class="stats-section container">
        <h2 class="section-title text-center animate-on-scroll">Our Reach</h2>
        <p class="text-center mb-5 fs-5 animate-on-scroll">Currently serving Northern Samar, our MHCS operates across 34 barangays, providing essential healthcare to those who need it most:</p>
        
        <div class="row g-4">
            <div class="col-md-3">
                <div class="stat-card animate-on-scroll">
                    <div class="stat-number">2</div>
                    <p class="fs-5">Project Municipalities</p>
                    <i class="bi bi-bank2 text-muted fs-4"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card animate-on-scroll" style="transition-delay: 0.1s">
                    <div class="stat-number">16</div>
                    <p class="fs-5">Total Barangays</p>
                    <i class="bi bi-geo-alt-fill text-muted fs-4"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card animate-on-scroll" style="transition-delay: 0.2s">
                    <div class="stat-number">800+</div>
                    <p class="fs-5">Elderly served</p>
                    <i class="bi bi-people-fill text-muted fs-4"></i>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card animate-on-scroll" style="transition-delay: 0.3s">
                    <div class="stat-number">10</div>
                    <p class="fs-5">Active Care Workers</p>
                    <i class="bi bi-person-hearts text-muted fs-4"></i>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section - Consistent card layout -->
    <section class="services-section">
        <div class="container py-5 position-relative">
            <h2 class="section-title text-white text-center animate-on-scroll">Our Comprehensive Services</h2>
            <p class="text-center text-white mb-5 fs-5 animate-on-scroll" style="transition-delay: 0.1s">We provide a full range of healthcare services tailored to the needs of elderly community members</p>
            
            <div class="row g-4">
                <div class="col-md-4 animate-on-scroll">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="bi bi-heart-pulse"></i>
                        </div>
                        <h3 class="text-white">Medical Care</h3>
                        <ul class="text-white ps-0" style="list-style-type: none;">
                            <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: var(--cose-primary-light);"></i> Regular health monitoring</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: var(--cose-primary-light);"></i> Disease or Therapy management</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: var(--cose-primary-light);"></i> Medication assistance</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: var(--cose-primary-light);"></i> Disability care and support</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 animate-on-scroll" style="transition-delay: 0.1s">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="bi bi-person-lines-fill"></i>
                        </div>
                        <h3 class="text-white">Daily Living Support</h3>
                        <ul class="text-white ps-0" style="list-style-type: none;">
                            <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: var(--cose-primary-light);"></i> Hygiene care assistance</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: var(--cose-primary-light);"></i> Mobility support</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: var(--cose-primary-light);"></i> Household keeping aid</li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-4 animate-on-scroll" style="transition-delay: 0.2s">
                    <div class="service-card">
                        <div class="service-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <h3 class="text-white">Community Services</h3>
                        <ul class="text-white ps-0" style="list-style-type: none;">
                            <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: var(--cose-primary-light);"></i> Social engagement</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: var(--cose-primary-light);"></i> Transportation assistance</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: var(--cose-primary-light);"></i> Caregiver education</li>
                            <li class="mb-2"><i class="bi bi-check-circle-fill me-2" style="color: var(--cose-primary-light);"></i> Community integration</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Approach Section - Consistent card layout -->
    <section class="container py-5">
        <h2 class="section-title text-center animate-on-scroll">Our Patient-Centered Approach</h2>
        <p class="text-center mb-5 fs-5 animate-on-scroll">We believe in providing care that respects the dignity and individuality of each senior we serve</p>
        
        <div class="row g-4 mt-4">
            <div class="col-md-6 col-lg-3 animate-on-scroll">
                <div class="approach-card">
                    <i class="bi bi-file-earmark-person-fill mb-3" style="font-size: 2rem; color: var(--cose-primary);"></i>
                    <h4>Personalized Care Plans</h4>
                    <p>Weekly care plans tailored to each individual's specific needs and health conditions.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 animate-on-scroll" style="transition-delay: 0.1s">
                <div class="approach-card">
                    <i class="bi bi-clipboard2-pulse-fill mb-3" style="font-size: 2rem; color: var(--cose-primary);"></i>
                    <h4>Comprehensive Tracking</h4>
                    <p>Detailed monitoring of health status and all interventions for continuous care improvement.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 animate-on-scroll" style="transition-delay: 0.2s">
                <div class="approach-card">
                    <i class="bi bi-heart-fill mb-3" style="font-size: 2rem; color: var(--cose-primary);"></i>
                    <h4>Holistic Support</h4>
                    <p>Addressing physical, mental, and social wellbeing for complete elder care.</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 animate-on-scroll" style="transition-delay: 0.3s">
                <div class="approach-card">
                    <i class="bi bi-house-heart-fill mb-3" style="font-size: 2rem; color: var(--cose-primary);"></i>
                    <h4>Community Integration</h4>
                    <p>Connecting seniors with local resources and building support networks.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Beyond Section - Consistent card layout -->
    <section class="container py-5">
        <h2 class="section-title text-center animate-on-scroll">Beyond Healthcare</h2>
        <p class="text-center mb-5 fs-5 animate-on-scroll">Our MHCS is part of COSE's broader strategy to improve quality of life for older Filipinos:</p>
        
        <div class="row g-4">
            <div class="col-md-6 col-lg-3 animate-on-scroll">
                <div class="beyond-card">
                    <i class="bi bi-house-door fs-1 mb-3" style="color: var(--cose-primary);"></i>
                    <h5 class="fw-bold">Senior Citizens Community Centers</h5>
                    <p class="text-muted">Providing spaces for social interaction and community activities</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 animate-on-scroll" style="transition-delay: 0.1s">
                <div class="beyond-card">
                    <i class="bi bi-people fs-1 mb-3" style="color: var(--cose-primary);"></i>
                    <h5 class="fw-bold">Older Persons Organizations</h5>
                    <p class="text-muted">Over 400 OPOs across 17 provinces advocating for senior rights</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 animate-on-scroll" style="transition-delay: 0.2s">
                <div class="beyond-card">
                    <i class="bi bi-capsule fs-1 mb-3" style="color: var(--cose-primary);"></i>
                    <h5 class="fw-bold">Botika Binhi Pharmacies</h5>
                    <p class="text-muted">Community-based pharmacies providing affordable medicines</p>
                </div>
            </div>
            <div class="col-md-6 col-lg-3 animate-on-scroll" style="transition-delay: 0.3s">
                <div class="beyond-card">
                    <i class="bi bi-heart-pulse fs-1 mb-3" style="color: var(--cose-primary);"></i>
                    <h5 class="fw-bold">Wellness Centers</h5>
                    <p class="text-muted">Facilities offering holistic health services for seniors</p>
                </div>
            </div>
        </div>
    </section>

    @include('components.footer')

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Shared Intersection Observer for scroll animations
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.1 });
        
        document.querySelectorAll('.animate-on-scroll').forEach(item => {
            observer.observe(item);
        });
    </script>
</body>
</html>