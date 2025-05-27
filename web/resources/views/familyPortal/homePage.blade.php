<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Family Portal - Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/familyPortalHomePage.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
    @include('components.familyPortalNavbar')
    @include('components.familyPortalSidebar')

    <!-- Welcome Back Modal -->
    <div class="modal fade" id="welcomeBackModal" tabindex="-1" aria-labelledby="welcomeBackModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="welcomeBackModalLabel">Welcome to Family Portal!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class='bi bi-people-fill display-4 text-primary mb-3'></i>
                    <h4>Hello, [FAMILY MEMBER]!</h4>
                    <p>Welcome to your Family Access Portal where you can monitor care services.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Get Started</button>
                </div>
            </div>
        </div>
    </div>

    <div class="home-section">
        <div class="container-fluid">
            <div class="row p-3" id="home-content">
                <!-- Combined Welcome and Alert Banners -->
                <div class="banner-grid">
                    <div class="welcome-banner">
                        <h1 class="welcome-title">Welcome, [FAMILY MEMBER]!</h1>
                        <p class="welcome-subtitle">You're viewing the profile of [Beneficiary Name]</p>
                        <p>Last care worker visit: [Last visit details]</p>
                    </div>
                    
                    <div class="alert-banner">
                        <span class="alert-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
                        <div class="alert-content">
                            <strong>Upcoming Visit:</strong> Care worker scheduled for [next visit details]
                        </div>
                    </div>
                </div>
                
                <!-- Dashboard Cards -->
                <div class="dashboard-cards">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-title">Visitation Schedule</div>
                            <div class="card-icon"><i class="bi bi-calendar-check"></i></div>
                        </div>
                        <div class="card-content">
                            Next visit scheduled for [date] at [time] with [care worker]
                        </div>
                        <div class="card-action">
                            <span>View Full Schedule</span>
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </div>
                    
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-title">Medication Schedule</div>
                            <div class="card-icon"><i class="bi bi-capsule-pill"></i></div>
                        </div>
                        <div class="card-content">
                            Next medication: [Medication name] at [time] on [date]
                        </div>
                        <div class="card-action">
                            <span>View All Medications</span>
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </div>
                    
                    <div class="dashboard-card emergency-card">
                        <div class="card-header">
                            <div class="card-title">Emergency Request</div>
                            <div class="card-icon emergency-icon"><i class="bi bi-exclamation-octagon"></i></div>
                        </div>
                        <div class="card-content">
                            In case of emergency, request immediate assistance
                        </div>
                        <div class="card-action emergency-action">
                            <span>Request Emergency Care</span>
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </div>
                    
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-title">Service Request</div>
                            <div class="card-icon"><i class="bi bi-clipboard2-pulse"></i></div>
                        </div>
                        <div class="card-content">
                            Request additional services or report issues
                        </div>
                        <div class="card-action">
                            <span>Create New Request</span>
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </div>
                    
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-title">Weekly Care Plan</div>
                            <div class="card-icon"><i class="bi bi-clipboard2-check"></i></div>
                        </div>
                        <div class="card-content">
                            View this week's care interventions and progress
                        </div>
                        <div class="card-action">
                            <span>View Care Plan</span>
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </div>
                    
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-title">Family Members</div>
                            <div class="card-icon"><i class="bi bi-people-fill"></i></div>
                        </div>
                        <div class="card-content">
                            [Number] family members have access to this account
                        </div>
                        <div class="card-action">
                            <span>Manage Family Access</span>
                            <i class="bi bi-chevron-right"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            @if($showWelcome)
                var welcomeModal = new bootstrap.Modal(document.getElementById('welcomeBackModal'));
                welcomeModal.show();
            @endif
        });
    </script>
</body>
</html>