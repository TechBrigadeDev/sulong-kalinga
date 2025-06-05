<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <style>
        :root {
            --primary-color: #3498db;
            --secondary-color: #2c3e50;
            --accent-color: #e74c3c;
            --light-gray: #f8f9fa;
            --medium-gray: #e9ecef;
            --dark-gray: #6c757d;
            --success-color: #27ae60;
            --warning-color: #f39c12;
        }
        
        body {
            background-color: #f5f7fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .profile-img {
            width: clamp(6.25rem, 15vw, 9.375rem);
            height: clamp(6.25rem, 15vw, 9.375rem);
            object-fit: cover;
            border: 0.1875rem solid white;
            box-shadow: 0 0.1875rem 0.625rem rgba(0, 0, 0, 0.1);
        }
        
        .profile-header-card {
            border: none;
            border-radius: 0.5rem;
            box-shadow: 0 0.25rem 0.75rem rgba(0, 0, 0, 0.08);
            margin-bottom: 1.5rem;
        }
        
        .badge-active {
            background-color: rgba(39, 174, 96, 0.1);
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }
        
        .badge-inactive {
            background-color: rgba(231, 76, 60, 0.1);
            color: var(--accent-color);
            border: 1px solid var(--accent-color);
        }
        
        .status-select {
            border-radius: 1.25rem;
            padding: 0.375rem 1rem;
            font-weight: 500;
            border: 1px solid var(--medium-gray);
            background-color: white;
            cursor: pointer;
            transition: all 0.3s;
            font-size: clamp(0.75rem, 2vw, 0.8125rem);
            min-width: 6rem;
            text-align: center;
        }
        
        .detail-card {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            border: none;
        }
        
        .detail-card-header {
            background-color: var(--secondary-color);
            color: white;
            border-radius: 8px 8px 0 0 !important;
            font-weight: 600;
        }
        
        .detail-item {
            border-bottom: 1px solid var(--medium-gray);
            padding: 15px 0;
        }
        
        .detail-item:last-child {
            border-bottom: none;
        }
        
        .detail-label {
            font-weight: 600;
            color: var(--secondary-color);
            margin-bottom: 5px;
        }
        
        .detail-value {
            color: var(--dark-gray);
        }
        
        .document-link {
            color: var(--primary-color);
            text-decoration: none;
            transition: all 0.3s;
        }
        
        .document-link:hover {
            color: var(--secondary-color);
            text-decoration: underline;
        }
        
        .section-title {
            color: var(--secondary-color);
            font-weight: 600;
            margin-bottom: 20px;
            position: relative;
            padding-bottom: 8px;
        }
        
        .section-title::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 50px;
            height: 3px;
            background-color: var(--primary-color);
        }
        
        .btn-action {
            border-radius: 6px;
            font-weight: 500;
            padding: 8px 15px;
            transition: all 0.3s;
        }
        
        .desktop-back-btn {
            display: none;
        }

        .header-buttons {
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        @media (max-width: 767.98px) {
            .desktop-back-btn {
                display: none;
            }
            
            .mobile-back-btn {
                display: inline-flex;
            }
            
            .header-buttons .btn {
                padding: 0.375rem 0.5rem;
            }
        }

        @media (min-width: 768px) {
            .desktop-back-btn {
                display: inline-flex;
            }
            
            .mobile-back-btn {
                display: none;
            }
            
            .header-buttons {
                width: auto !important;
            }
        }

        #home-content .row {
            margin-left: 0 !important;
            margin-right: 0 !important;
        }
        
        .role-badge {
            background-color: rgba(155, 89, 182, 0.1);
            color: #9b59b6;
            border: 1px solid #9b59b6;
        }
    </style>
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')
    @include('components.modals.statusChangeCaremanager')
    @include('components.modals.deleteCaremanager')

    <div class="home-section">
        <div class="container-fluid">
            <!-- Header with Action Buttons -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-2">
                <a href="{{ route('admin.caremanagers.index') }}" class="btn btn-secondary desktop-back-btn align-self-start align-self-md-center">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
                <h4 class="mb-0 text-center" style="font-size: 20px; font-weight: bold; padding: 10px;">
                    Care Manager Profile Details
                </h4>
                <div class="d-flex justify-content-center w-100 justify-content-md-end gap-2 header-buttons">
                    <a href="{{ route('admin.caremanagers.index') }}" class="btn btn-secondary mobile-back-btn" style="height: 33px;">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                    @if(Auth::user()->role_id == 1)
                        <a href="{{ route('admin.caremanagers.edit', $caremanager->id) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </a>
                        <button type="button" class="btn btn-danger" onclick="openDeleteCaremanagerModal('{{ $caremanager->id }}', '{{ $caremanager->first_name }} {{ $caremanager->last_name }}')">
                            <i class="bi bi-trash me-1"></i> Delete
                        </button>
                    @endif
                </div>
            </div>
            
            <div class="row" id="home-content">
                <!-- Profile Header Section -->
                <div class="row">
                    <div class="col-12">
                        <div class="card profile-header-card">
                            <div class="card-body p-4">
                                <div class="row align-items-center">
                                    <div class="col-md-3 text-center mb-4 mb-md-0">
                                        <img src="{{ $caremanager->photo ? asset('storage/' . $caremanager->photo) : asset('images/defaultProfile.png') }}" 
                                            alt="Profile Picture" 
                                            class="img-fluid rounded-circle profile-img">
                                    </div>
                                    <div class="col-md-9">
                                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center align-items-md-start">
                                            <div class="text-center text-md-start mb-3 mb-md-0">
                                                <h3 class="mb-1" style="color: var(--secondary-color);">
                                                    {{ $caremanager->first_name }} {{ $caremanager->last_name }}
                                                </h3>
                                                <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2">
                                                    <span class="badge rounded-pill role-badge">
                                                        <i class="bi bi-person-badge me-1"></i> Care Manager
                                                    </span>
                                                    <span class="badge rounded-pill {{ $caremanager->status == 'Active' ? 'badge-active' : 'badge-inactive' }}">
                                                        {{ $caremanager->status }}
                                                    </span>
                                                </div>
                                                <p class="text-muted mt-2 mb-0">
                                                    <i class="bi bi-calendar3 me-1"></i> Member since {{ $caremanager->status_start_date->format('F j, Y') }}
                                                </p>
                                            </div>
                                            <div class="mt-2 mt-md-0">
                                                <select class="status-select px-4 text-center" 
                                                        name="status" 
                                                        id="statusSelect{{ $caremanager->id }}" 
                                                        onchange="openStatusChangeCaremanagerModal(this, 'Care Manager', {{ $caremanager->id }}, '{{ $caremanager->status }}')">
                                                    <option value="Active" {{ $caremanager->status == 'Active' ? 'selected' : '' }}>Active</option>
                                                    <option value="Inactive" {{ $caremanager->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="row">
                    <!-- Personal Details Column -->
                    <div class="col-lg-8">
                        <div class="detail-card card mb-4">
                            <div class="card-header detail-card-header">
                                <i class="fas fa-user-circle me-2"></i>Personal Information
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Educational Background</div>
                                            <div class="detail-value">{{ $caremanager->educational_background ?? 'N/A' }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Birthday</div>
                                            <div class="detail-value">{{ $caremanager->birthday->format('F j, Y') }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Gender</div>
                                            <div class="detail-value">{{ $caremanager->gender ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Civil Status</div>
                                            <div class="detail-value">{{ $caremanager->civil_status ?? 'N/A' }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Religion</div>
                                            <div class="detail-value">{{ $caremanager->religion ?? 'N/A' }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Nationality</div>
                                            <div class="detail-value">{{ $caremanager->nationality ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-card card mb-4">
                            <div class="card-header detail-card-header">
                                <i class="fas fa-address-card me-2"></i>Contact Information
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Email Address</div>
                                            <div class="detail-value">{{ $caremanager->email }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Mobile Number</div>
                                            <div class="detail-value">{{ $caremanager->mobile }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Landline Number</div>
                                            <div class="detail-value">{{ $caremanager->landline ?? 'N/A' }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Current Address</div>
                                            <div class="detail-value">{{ $caremanager->address }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-card card mb-4">
                            <div class="card-header detail-card-header">
                                <i class="fas fa-map-marker-alt me-2"></i>Assignment Information
                            </div>
                            <div class="card-body">
                                <div class="detail-item">
                                    <div class="detail-label">Assigned Municipality</div>
                                    <div class="detail-value">{{ $caremanager->municipality->municipality_name }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Documents and IDs Column -->
                    <div class="col-lg-4">
                        <div class="detail-card card mb-4">
                            <div class="card-header detail-card-header">
                                <i class="fas fa-file-alt me-2"></i>Documents
                            </div>
                            <div class="card-body">
                                <div class="detail-item">
                                    <div class="detail-label">Government Issued ID</div>
                                    <div class="detail-value">
                                        @if($caremanager->government_issued_id)
                                            <a href="{{ asset('storage/' . $caremanager->government_issued_id) }}" download class="document-link">
                                                <i class="fas fa-download me-2"></i>Download
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Resume / CV</div>
                                    <div class="detail-value">
                                        @if($caremanager->cv_resume)
                                            <a href="{{ asset('storage/' . $caremanager->cv_resume) }}" download class="document-link">
                                                <i class="fas fa-download me-2"></i>Download
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-card card mb-4">
                            <div class="card-header detail-card-header">
                                <i class="fas fa-id-card me-2"></i>Government ID Numbers
                            </div>
                            <div class="card-body">
                                <div class="detail-item">
                                    <div class="detail-label">SSS ID Number</div>
                                    <div class="detail-value">{{ $caremanager->sss_id_number ?? 'N/A' }}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">PhilHealth ID Number</div>
                                    <div class="detail-value">{{ $caremanager->philhealth_id_number ?? 'N/A' }}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Pag-Ibig ID Number</div>
                                    <div class="detail-value">{{ $caremanager->pagibig_id_number ?? 'N/A' }}</div>
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