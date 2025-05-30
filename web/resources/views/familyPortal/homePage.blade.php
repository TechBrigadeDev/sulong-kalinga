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
                    <h4>Hello, {{ $familyMember->first_name }}!</h4>
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
                        <h1 class="welcome-title">Welcome, {{ $familyMember->first_name }}!</h1>
                        <p class="welcome-subtitle">You're viewing the profile of {{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</p>
                        <p>Last care worker visit: {{ \Carbon\Carbon::now()->subDays(3)->format('F d, Y') }}</p>
                    </div>
                    
                    <div class="alert-banner">
                        <span class="alert-icon"><i class="bi bi-exclamation-triangle-fill"></i></span>
                        <div class="alert-content">
                            <strong>Upcoming Visit:</strong> Care worker scheduled for {{ \Carbon\Carbon::now()->addDays(2)->format('F d, Y') }} at 2:00 PM
                        </div>
                    </div>
                </div>
                
                <!-- Dashboard Cards -->
                <div class="dashboard-cards">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="bi bi-calendar-check me-2"></i>
                                Visitation Schedule
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-content">
                                Next visit scheduled for {{ \Carbon\Carbon::now()->addDays(2)->format('F d, Y') }} at 2:00 PM
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('family.visitation.schedule.index') }}" class="card-link">
                                <span>View Full Schedule</span>
                                <i class="bi bi-chevron-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                    
                    <!-- Rest of the cards remain the same -->
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