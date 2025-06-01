<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Beneficiary Portal - Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/familyPortalHomePage.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
    @include('components.beneficiaryPortalNavbar')
    @include('components.beneficiaryPortalSidebar')

    <!-- Welcome Back Modal -->
    <div class="modal fade" id="welcomeBackModal" tabindex="-1" aria-labelledby="welcomeBackModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="welcomeBackModalLabel">Welcome to Your Portal!</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <i class='bi bi-person-circle display-4 text-primary mb-3'></i>
                    <h4>Welcome, {{ Auth::guard('beneficiary')->user()->first_name }}!</h4>
                    <p>This is your personal Sulong Kalinga portal where you can access care services information.</p>
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
                        <h1 class="welcome-title">Welcome, {{ Auth::guard('beneficiary')->user()->first_name }}!</h1>
                        <p class="welcome-subtitle">Your care worker is monitoring your progress</p>
                        <p>Last care worker visit: <span class="fw-bold">{{ \Carbon\Carbon::now()->subDays(3)->format('F d, Y') }}</span></p>
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
                                My Schedule
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-content">
                                Next visit scheduled for {{ \Carbon\Carbon::now()->addDays(2)->format('F d, Y') }} at 2:00 PM
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('beneficiary.schedule.index') }}" class="card-link">
                                <span>View Full Schedule</span>
                                <i class="bi bi-chevron-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="bi bi-capsule-pill me-2"></i>
                                My Medications
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-content">
                                Next medication: Blood pressure medicine at 8:00 PM today
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('beneficiary.schedule.index') }}" class="card-link">
                                <span>View All Medications</span>
                                <i class="bi bi-chevron-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="dashboard-card emergency-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="bi bi-exclamation-octagon me-2"></i>
                                Emergency Contact
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-content">
                                In case of emergency, contact your care worker or emergency services
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('beneficiary.emergency.index') }}" class="card-link">
                                <span>Emergency Information</span>
                                <i class="bi bi-chevron-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="bi bi-chat-dots me-2"></i>
                                Messages
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-content">
                                You have 2 unread messages from your care worker
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('beneficiary.messages.index') }}" class="card-link">
                                <span>View Messages</span>
                                <i class="bi bi-chevron-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="bi bi-clipboard2-check me-2"></i>
                                My Care Plan
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-content">
                                View your personalized care plan and weekly goals
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('beneficiary.care.plan.index') }}" class="card-link">
                                <span>View Care Plan</span>
                                <i class="bi bi-chevron-right ms-2"></i>
                            </a>
                        </div>
                    </div>
                    
                    <div class="dashboard-card">
                        <div class="card-header">
                            <div class="card-title">
                                <i class="bi bi-person-circle me-2"></i>
                                My Profile
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="card-content">
                                Update your personal information and preferences
                            </div>
                        </div>
                        <div class="card-footer">
                            <a href="{{ route('beneficiary.profile.index') }}" class="card-link">
                                <span>View Profile</span>
                                <i class="bi bi-chevron-right ms-2"></i>
                            </a>
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