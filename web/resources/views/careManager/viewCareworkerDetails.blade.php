<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/careworkerProfilesDetails.css') }}">
</head>
<body>

    @include('components.careManagerNavbar')
    @include('components.careManagerSidebar')
    @include('components.modals.statusChangeCareworker')
    @include('components.modals.deleteCareworker')

    <div class="home-section">
        <div class="container-fluid">
            <!-- Header with Action Buttons -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-2">
                <a href="{{ route('care-manager.careworkers.index') }}" class="btn btn-secondary desktop-back-btn align-self-start align-self-md-center">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
                <h4 class="mb-0 text-center" style="font-size: 20px; font-weight: bold; padding: 10px;">
                    Care Worker Profile Details
                </h4>
                <div class="d-flex justify-content-center w-100 justify-content-md-end gap-2 header-buttons">
                    <a href="{{ route('care-manager.careworkers.index') }}" class="btn btn-secondary mobile-back-btn" style="height: 33px;">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                    <a href="{{ route('care-manager.careworkers.edit', $careworker->id) }}" class="btn btn-primary">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </a>
                    <button type="button" class="btn btn-danger" onclick="openDeleteCareworkerModal('{{ $careworker->id }}', '{{ $careworker->first_name }} {{ $careworker->last_name }}')">
                        <i class="bi bi-trash me-1"></i> Delete
                    </button>
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
                                        <img src="{{ $photoUrl ?? asset('images/defaultProfile.png') }}" alt="Profile Photo">
                                            alt="Profile Picture" 
                                            class="img-fluid rounded-circle profile-img">
                                    </div>
                                    <div class="col-md-9">
                                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center align-items-md-start">
                                            <div class="text-center text-md-start mb-3 mb-md-0">
                                                <h3 class="mb-1" style="color: var(--secondary-color);">
                                                    {{ $careworker->first_name }} {{ $careworker->last_name }}
                                                </h3>
                                                <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2">
                                                    <span class="badge rounded-pill bg-light text-dark">
                                                        <i class="bi bi-person-badge me-1"></i> Care Worker
                                                    </span>
                                                    <span class="badge rounded-pill {{ $careworker->status == 'Active' ? 'badge-active' : 'badge-inactive' }}">
                                                        {{ $careworker->status }}
                                                    </span>
                                                </div>
                                                <p class="text-muted mt-2 mb-0">
                                                    <i class="bi bi-calendar3 me-1"></i> Member since {{ $careworker->status_start_date->format('F j, Y') }}
                                                </p>
                                            </div>
                                            <div class="mt-2 mt-md-0">
                                                <select class="status-select px-4 text-center" 
                                                        name="status" 
                                                        id="statusSelect{{ $careworker->id }}" 
                                                        onchange="openStatusChangeCareworkerModal(this, 'Care Worker', {{ $careworker->id }}, '{{ $careworker->status }}')">
                                                    <option value="Active" {{ $careworker->status == 'Active' ? 'selected' : '' }}>Active</option>
                                                    <option value="Inactive" {{ $careworker->status == 'Inactive' ? 'selected' : '' }}>Inactive</option>
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
                                <i class="bi bi-person-circle me-2"></i>Personal Information
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Educational Background</div>
                                            <div class="detail-value">{{$careworker->educational_background ?? 'N/A'}}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Birthday</div>
                                            <div class="detail-value">{{$careworker->birthday->format('F j, Y')}}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Gender</div>
                                            <div class="detail-value">{{$careworker->gender ?? 'N/A'}}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Civil Status</div>
                                            <div class="detail-value">{{$careworker->civil_status ?? 'N/A'}}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Religion</div>
                                            <div class="detail-value">{{$careworker->religion ?? 'N/A'}}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Nationality</div>
                                            <div class="detail-value">{{$careworker->nationality ?? 'N/A'}}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Assigned Municipality</div>
                                            <div class="detail-value">{{$careworker->municipality->municipality_name}}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Assigned Care Manager</div>
                                            <div class="detail-value">
                                                @if($careworker->assignedCareManager)
                                                    {{ $careworker->assignedCareManager->first_name }} {{ $careworker->assignedCareManager->last_name }}
                                                @else
                                                    <span class="text-muted">Unassigned</span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-card card mb-4">
                            <div class="card-header detail-card-header">
                                <i class="bi bi-envelope me-2"></i>Contact Information
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Email Address</div>
                                            <div class="detail-value">{{$careworker->email}}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Mobile Number</div>
                                            <div class="detail-value">{{$careworker->mobile}}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">Landline Number</div>
                                            <div class="detail-value">{{$careworker->landline ?? 'N/A'}}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">Current Address</div>
                                            <div class="detail-value">{{$careworker->address}}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Documents and IDs Column -->
                    <div class="col-lg-4">
                        <div class="detail-card card mb-4">
                            <div class="card-header detail-card-header">
                                <i class="bi bi-file-earmark-text me-2"></i>Documents
                            </div>
                            <div class="card-body">
                                <div class="detail-item">
                                    <div class="detail-label">Government Issued ID</div>
                                    <div class="detail-value">
                                        @if($governmentIdUrl)
                                            <a href="{{ $governmentIdUrl }}" target="_blank">Download Government ID</a>
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Resume / CV</div>
                                    <div class="detail-value">
                                        @if($resumeUrl)
                                            <a href="{{ $resumeUrl }}" target="_blank">Download Resume</a>
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-card card mb-4">
                            <div class="card-header detail-card-header">
                                <i class="bi bi-card-checklist me-2"></i>Government ID Numbers
                            </div>
                            <div class="card-body">
                                <div class="detail-item">
                                    <div class="detail-label">SSS ID Number</div>
                                    <div class="detail-value">{{$careworker->sss_id_number ?? 'N/A'}}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">PhilHealth ID Number</div>
                                    <div class="detail-value">{{$careworker->philhealth_id_number ?? 'N/A'}}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Pag-Ibig ID Number</div>
                                    <div class="detail-value">{{$careworker->pagibig_id_number ?? 'N/A'}}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Managed Beneficiaries Section -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h4 class="section-title"><i class="bi bi-people me-2"></i>Managed Beneficiaries</h4>
                        
                        @if($beneficiaries->isEmpty())
                            <div class="empty-state">
                                <i class="bi bi-person-x fa-3x mb-3" style="color: var(--medium-gray);"></i>
                                <h5>No Beneficiaries Assigned</h5>
                                <p class="mb-0">This care worker is not currently managing any beneficiaries.</p>
                            </div>
                        @else
                            <div class="row">
                                @foreach ($beneficiaries as $beneficiary)
                                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mb-4">
                                        <div class="beneficiary-card card h-100">
                                            <div class="d-flex justify-content-center align-items-center p-3" style="height: 120px;">
                                                <img 
                                                    src="{{ $photoUrl ?? asset('images/defaultProfile.png') }}" 
                                                    class="beneficiary-img img-fluid" 
                                                    alt="{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}"
                                                >
                                            </div>
                                            <div class="card-body text-center p-3">
                                                <h6 class="card-title mb-0">{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</h6>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
   
</body>
</html>