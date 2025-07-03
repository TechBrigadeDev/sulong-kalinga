<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sulong Kalinga - Announcements</title>
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
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
            color: var(--cose-text);
            line-height: 1.6;
        }
        
        /* Header Section */
        .announcement-header {
            background: linear-gradient(135deg, var(--cose-primary) 0%, var(--cose-primary-dark) 100%);
            color: var(--cose-white);
            padding: 3rem 0 2rem;
            margin-bottom: 2rem;
            position: relative;
        }
        
        .header-content {
            position: relative;
            z-index: 2;
        }
        
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }
        
        .stat-item {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(5px);
            border-radius: 8px;
            padding: 1rem;
            text-align: center;
        }
        
        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.25rem;
            color: var(--cose-white);
        }
        
        .stat-label {
            font-size: 0.8rem;
            opacity: 0.9;
        }
        
        /* Filter Controls */
        .filter-controls {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: center;
            margin: 1.5rem 0;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.08);
        }
        
        .filter-group {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .filter-label {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--cose-text-light);
        }
        
        .filter-select {
            border: 1px solid #dee2e6;
            border-radius: 6px;
            padding: 0.375rem 1.75rem 0.375rem 0.75rem;
            font-size: 0.9rem;
            background-color: var(--cose-white);
            transition: border-color 0.15s ease-in-out;
        }
        
        .filter-select:focus {
            border-color: var(--cose-primary-light);
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(26, 110, 70, 0.25);
        }
        
        /* Update Cards */
        .updates-container {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.25rem;
            margin: 2rem 0;
        }
        
        .update-card {
            background: var(--cose-white);
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
            border: none;
            overflow: hidden;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .update-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.12);
        }
        
        .card-header {
            padding: 1rem 1.25rem;
            border-bottom: 1px solid rgba(0, 0, 0, 0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background-color: var(--cose-bg-light);
        }
        
        .card-body {
            padding: 1.25rem;
        }
        
        .update-type {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 0.3rem 0.7rem;
            border-radius: 20px;
        }
        
        .mhcs-type {
            background-color: var(--cose-accent);
            color: var(--cose-white);
        }
        
        .org-type {
            background-color: var(--cose-primary);
            color: var(--cose-white);
        }
        
        .update-date {
            font-size: 0.8rem;
            color: var(--cose-text-light);
            display: flex;
            align-items: center;
        }
        
        .update-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--cose-primary-dark);
        }
        
        .update-meta {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }
        
        .meta-item {
            display: flex;
            align-items: center;
            font-size: 0.8rem;
            color: var(--cose-text-light);
        }
        
        /* Pagination */
        .pagination {
            margin-top: 2rem;
        }
        
        .page-item.active .page-link {
            background-color: var(--cose-primary);
            border-color: var(--cose-primary);
        }
        
        .page-link {
            color: var(--cose-primary);
        }
        
        /* Responsive Adjustments */
        @media (max-width: 768px) {
            .announcement-header {
                padding: 2rem 0 1.5rem;
            }
            
            .stats-grid {
                grid-template-columns: 1fr 1fr;
            }
            
            .filter-controls {
                flex-direction: column;
                align-items: flex-start;
                gap: 0.75rem;
            }
        }
    </style>
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