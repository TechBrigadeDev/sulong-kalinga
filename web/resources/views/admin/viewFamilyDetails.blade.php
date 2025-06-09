<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $family_member->first_name }} {{ $family_member->last_name }} | Family Profile</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <link rel="stylesheet" href="{{ asset('css/viewProfileDetails.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/viewFamilyDetails.css') }}">
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')
    @include('components.modals.deleteFamilyMember')

    <div class="home-section">
        <div class="container-fluid">
            <!-- Header with buttons -->
            <div class="d-flex justify-content-between align-items-center mb-2">
                <!-- Back button for large screens -->
                <a href="{{ route('admin.families.index') }}" class="btn btn-secondary d-none d-md-inline-flex">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
                
                <h4 class="text-center mb-0" style="font-size: 20px; font-weight: bold; padding: 10px;">Family Member Profile</h4>
                
                <div class="d-flex gap-2 header-buttons">
                    <!-- Back button for small screens -->
                    <a href="{{ route('admin.families.index') }}" class="btn btn-secondary d-inline-flex d-md-none" style="margin-bottom: 8px;">
                        <i class="bi bi-arrow-left"></i> Back
                    </a>
                    <a href="{{ route('admin.families.edit', $family_member->family_member_id) }}" class="btn btn-primary">
                        <i class="bi bi-pencil-square me-1"></i> Edit
                    </a>
                    <button type="button" class="btn btn-danger" onclick="openDeleteFamilyMemberModal('{{ $family_member->family_member_id }}', '{{ $family_member->first_name }} {{ $family_member->last_name }}')">
                        <i class="bi bi-trash-fill me-1"></i> Delete
                    </button>
                </div>
            </div>
            
            <div class="row" id="home-content">
                <!-- Profile Header -->
                <div class="profile-header">
                    <div class="row align-items-center">
                        <div class="col-md-3 text-center mb-4 mb-md-0">
                            <img src="{{ $family_member->photo ? asset('storage/' . $family_member->photo) : asset('images/defaultProfile.png') }}" 
                                alt="Profile Picture" 
                                class="img-fluid rounded-circle profile-img">
                        </div>
                        <div class="col-md-9">
                            <h2 class="mb-2" style="color: var(--secondary-color);">
                                {{ $family_member->first_name }} {{ $family_member->last_name }}
                            </h2>
                            <div class="d-flex flex-wrap gap-2 mb-3 justify-content-center justify-content-md-start">
                                <span class="badge rounded-pill" style="background-color: var(--primary-light); color: var(--secondary-color);">
                                    <i class="bi bi-person-badge me-1"></i> Family Member
                                </span>
                            </div>
                            <div class="d-flex flex-wrap gap-4 justify-content-center justify-content-md-start">
                                <div>
                                    <span class="text-muted"><i class="bi bi-telephone me-1"></i></span>
                                    <span>{{ $family_member->mobile ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-muted"><i class="bi bi-envelope me-1"></i></span>
                                    <span>{{ $family_member->email ?? 'N/A' }}</span>
                                </div>
                                <div>
                                    <span class="text-muted"><i class="bi bi-house me-1"></i></span>
                                    <span>{{ $family_member->street_address }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Personal Details Section -->
                <div class="row">
                    <div class="col-lg-8">
                        <div class="detail-card">
                            <div class="card-header detail-card-header">
                                <h5 class="mb-0"><i class="bi bi-person-lines-fill me-2"></i>Personal Details</h5>
                            </div>
                            <div class="card-body p-0">
                                <div class="detail-item">
                                    <span class="detail-label">Gender</span>
                                    <span>{{ $family_member->gender }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Birthday</span>
                                    <span>{{ \Carbon\Carbon::parse($family_member->birthday)->format('F j, Y') }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Mobile Number</span>
                                    <span>{{ $family_member->mobile ?? 'N/A' }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Landline Number</span>
                                    <span>{{ $family_member->landline ?? 'N/A' }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Email Address</span>
                                    <span>{{ $family_member->email ?? 'N/A' }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Current Address</span>
                                    <span>{{ $family_member->street_address }}</span>
                                </div>
                                <div class="detail-item">
                                    <span class="detail-label">Relation to Beneficiary</span>
                                    <span>{{ $family_member->relation_to_beneficiary }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Related Beneficiary Section -->
                    <div class="col-lg-4">
                        <div class="detail-card beneficiary-card">
                            <div class="card-header detail-card-header">
                                <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>Related Beneficiary</h5>
                            </div>
                            <div class="card-body text-center p-3">
                                <div class="d-flex flex-column align-items-center">
                                    <img src="{{ $family_member->beneficiary->photo ? asset('storage/' . $family_member->beneficiary->photo) : asset('images/defaultProfile.png') }}" 
                                        class="rounded-circle beneficiary-img mb-3" 
                                        alt="Beneficiary Photo">
                                    <h5 class="mb-2">{{ $family_member->beneficiary->first_name }} {{ $family_member->beneficiary->last_name }}</h5>
                                    <div class="d-flex flex-wrap justify-content-center gap-2 mb-3">
                                        <span class="badge rounded-pill" style="background-color: var(--primary-light); color: var(--secondary-color);">
                                            <i class="bi bi-heart-pulse me-1"></i> Beneficiary
                                        </span>
                                    </div>
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