<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sulong Kalinga - Announcements</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/updates.css') }}">
</head>
<body>

    @include('components.navbar')

    <header class="announcement-header">
        <div class="container header-content">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-center text-center text-md-start">
                <i class="bi bi-megaphone-fill mb-3 mb-md-0 me-0 me-md-5" style="color: var(--cose-white); font-size: 5rem;"></i>
                <div>
                    <h1 class="display-4 mb-1" style="font-weight: bold;">COSE Announcements</h1>
                    <p class="lead mb-0" style="opacity: 0.95;">Latest updates from our Mobile Healthcare Service</p>
                </div>
            </div>
        </div>
    </header>

    <main class="container">
        
        <div class="updates-container">
            <!-- Update 1 -->
            <article class="update-card">
                <div class="card-header">
                    <span class="update-type mhcs-type">MHCS</span>
                    <span class="update-date"><i class="bi bi-calendar-event me-1"></i> July 15, 2023</span>
                </div>
                <div class="card-body">
                    <h2 class="update-title">
                        <i class="bi bi-ambulance me-2" style="color: var(--cose-accent);"></i>
                        MHCS Expands to 5 New Barangays
                    </h2>
                    <p>Our Mobile Healthcare Service has extended its reach to 5 additional barangays in San Roque, Northern Samar, serving approximately 120 more elderly individuals in remote communities.</p>
                    <div class="update-meta">
                        <span class="meta-item"><i class="bi bi-geo-alt me-1"></i> San Roque</span>
                        <span class="meta-item"><i class="bi bi-people me-1"></i> 120 beneficiaries</span>
                    </div>
                </div>
            </article>
            
            <!-- Update 2 -->
            <article class="update-card">
                <div class="card-header">
                    <span class="update-type org-type">Organization</span>
                    <span class="update-date"><i class="bi bi-calendar-event me-1"></i> July 10, 2023</span>
                </div>
                <div class="card-body">
                    <h2 class="update-title">
                        <i class="bi bi-heart-pulse me-2" style="color: var(--cose-primary);"></i>
                        PhilHealth Partnership Established
                    </h2>
                    <p>New partnership with PhilHealth ensures 100% of MHCS beneficiaries have access to mandatory coverage as mandated by the Expanded Senior Citizens Act of 2010.</p>
                    <div class="update-meta">
                        <span class="meta-item"><i class="bi bi-shield-check me-1"></i> Policy</span>
                    </div>
                </div>
            </article>
            
            <!-- Update 3 -->
            <article class="update-card">
                <div class="card-header">
                    <span class="update-type mhcs-type">MHCS</span>
                    <span class="update-date"><i class="bi bi-calendar-event me-1"></i> July 5, 2023</span>
                </div>
                <div class="card-body">
                    <h2 class="update-title">
                        <i class="bi bi-clipboard2-pulse me-2" style="color: var(--cose-accent);"></i>
                        New Health Monitoring Kits
                    </h2>
                    <p>All MHCS teams now equipped with digital health monitoring kits to better track vital signs and chronic conditions during home visits.</p>
                    <div class="update-meta">
                        <span class="meta-item"><i class="bi bi-tools me-1"></i> Equipment</span>
                    </div>
                </div>
            </article>
            
            <!-- Update 4 -->
            <article class="update-card">
                <div class="card-header">
                    <span class="update-type org-type">Organization</span>
                    <span class="update-date"><i class="bi bi-calendar-event me-1"></i> June 28, 2023</span>
                </div>
                <div class="card-body">
                    <h2 class="update-title">
                        <i class="bi bi-house-heart me-2" style="color: var(--cose-primary);"></i>
                        Bulacan Group Home Renovation
                    </h2>
                    <p>Group Home in Bulacan for abandoned older women will undergo renovations with funding from HelpAge International, including better accessibility features.</p>
                    <div class="update-meta">
                        <span class="meta-item"><i class="bi bi-building me-1"></i> Facility</span>
                    </div>
                </div>
            </article>
            
            <!-- Update 5 -->
            <article class="update-card">
                <div class="card-header">
                    <span class="update-type mhcs-type">MHCS</span>
                    <span class="update-date"><i class="bi bi-calendar-event me-1"></i> June 20, 2023</span>
                </div>
                <div class="card-body">
                    <h2 class="update-title">
                        <i class="bi bi-person-plus me-2" style="color: var(--cose-accent);"></i>
                        25 New Care Workers Trained
                    </h2>
                    <p>New care workers completed training in home care, massage therapy, and herbal medicine to support weekly care plans across 34 barangays.</p>
                    <div class="update-meta">
                        <span class="meta-item"><i class="bi bi-mortarboard me-1"></i> Training</span>
                    </div>
                </div>
            </article>
        </div>
        
        <nav aria-label="Page navigation" class="d-flex justify-content-center">
            <ul class="pagination">
                <li class="page-item disabled">
                    <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Previous</a>
                </li>
                <li class="page-item active"><a class="page-link" href="#">1</a></li>
                <li class="page-item"><a class="page-link" href="#">2</a></li>
                <li class="page-item"><a class="page-link" href="#">3</a></li>
                <li class="page-item">
                    <a class="page-link" href="#">Next</a>
                </li>
            </ul>
        </nav>
    </main>

    

    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    @include('components.footer')
</body>
</html>