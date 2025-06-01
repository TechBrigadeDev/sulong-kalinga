<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Family Portal - Family Members</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/familyPortalHomePage.css') }}">
    <link rel="stylesheet" href="{{ asset('css/familyMember.css') }}">
</head>
<body>
    @include('components.beneficiaryPortalNavbar')
    @include('components.beneficiaryPortalSidebar')

    <div class="home-section">
        <div class="text-left">FAMILY MEMBERS MANAGEMENT</div>
        <div class="container-fluid">
            <div class="row" id="home-content">
                <!-- Beneficiary -->
                <div class="col-12">
                    <div class="member-card">
                        <div class="card-body text-center">
                            <div class="member-avatar-container">
                                <img src="https://randomuser.me/api/portraits/women/65.jpg" alt="Maria Santos" class="member-avatar">
                            </div>
                            <h5 class="member-name">Maria Santos</h5>
                            <span class="member-relationship">Beneficiary</span>
                            
                            <div class="member-details text-start">
                                <div class="detail-item">
                                    <i class="bi bi-envelope"></i>
                                    <span class="detail-item-content">maria.santos@example.com</span>
                                </div>
                                <div class="detail-item">
                                    <i class="bi bi-telephone"></i>
                                    <span class="detail-item-content">(555) 123-4567</span>
                                </div>
                                <div class="detail-item">
                                    <i class="bi bi-house"></i>
                                    <span class="detail-item-content">123 Main Street, Manila, Philippines</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Spouse -->
                <div class="col-12">
                    <div class="member-card">
                        <div class="card-body text-center">
                            <div class="member-avatar-container">
                                <img src="https://randomuser.me/api/portraits/men/72.jpg" alt="Juan Santos" class="member-avatar">
                            </div>
                            <h5 class="member-name">Juan Santos</h5>
                            <span class="member-relationship">Spouse</span>
                            
                            <div class="member-details text-start">
                                <div class="detail-item">
                                    <i class="bi bi-envelope"></i>
                                    <span class="detail-item-content">juan.santos@example.com</span>
                                </div>
                                <div class="detail-item">
                                    <i class="bi bi-telephone"></i>
                                    <span class="detail-item-content">(555) 987-6543</span>
                                </div>
                                <div class="detail-item">
                                    <i class="bi bi-house"></i>
                                    <span class="detail-item-content">Same as Beneficiary</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Daughter -->
                <div class="col-12">
                    <div class="member-card">
                        <div class="card-body text-center">
                            <div class="member-avatar-container">
                                <img src="https://randomuser.me/api/portraits/women/22.jpg" alt="Ana Santos" class="member-avatar">
                            </div>
                            <h5 class="member-name">Ana Santos</h5>
                            <span class="member-relationship">Daughter</span>
                            
                            <div class="member-details text-start">
                                <div class="detail-item">
                                    <i class="bi bi-envelope"></i>
                                    <span class="detail-item-content">ana.santos@example.com</span>
                                </div>
                                <div class="detail-item">
                                    <i class="bi bi-telephone"></i>
                                    <span class="detail-item-content">(555) 456-7890</span>
                                </div>
                                <div class="detail-item">
                                    <i class="bi bi-house"></i>
                                    <span class="detail-item-content">Same as Beneficiary</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Son -->
                <div class="col-12">
                    <div class="member-card">
                        <div class="card-body text-center">
                            <div class="member-avatar-container">
                                <img src="https://randomuser.me/api/portraits/men/18.jpg" alt="Carlos Santos" class="member-avatar">
                            </div>
                            <h5 class="member-name">Carlos Santos</h5>
                            <span class="member-relationship">Son</span>
                            
                            <div class="member-details text-start">
                                <div class="detail-item">
                                    <i class="bi bi-envelope"></i>
                                    <span class="detail-item-content">carlos.santos@example.com</span>
                                </div>
                                <div class="detail-item">
                                    <i class="bi bi-telephone"></i>
                                    <span class="detail-item-content">(555) 789-0123</span>
                                </div>
                                <div class="detail-item">
                                    <i class="bi bi-house"></i>
                                    <span class="detail-item-content">University Dorm, Quezon City</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Mother -->
                <div class="col-12">
                    <div class="member-card">
                        <div class="card-body text-center">
                            <div class="member-avatar-container">
                                <img src="https://randomuser.me/api/portraits/women/75.jpg" alt="Rosa Dela Cruz" class="member-avatar">
                            </div>
                            <h5 class="member-name">Rosa Dela Cruz</h5>
                            <span class="member-relationship">Mother</span>
                            
                            <div class="member-details text-start">
                                <div class="detail-item">
                                    <i class="bi bi-envelope"></i>
                                    <span class="detail-item-content">rosa.delacruz@example.com</span>
                                </div>
                                <div class="detail-item">
                                    <i class="bi bi-telephone"></i>
                                    <span class="detail-item-content">(555) 234-5678</span>
                                </div>
                                <div class="detail-item">
                                    <i class="bi bi-house"></i>
                                    <span class="detail-item-content">456 Elder Street, Manila</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
</body>
</html>