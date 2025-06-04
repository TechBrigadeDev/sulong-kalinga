<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Beneficiary Portal - Family Members</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/familyPortalHomePage.css') }}">
    <link rel="stylesheet" href="{{ asset('css/familyMember.css') }}">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body>
    @include('components.beneficiaryPortalNavbar')
    @include('components.beneficiaryPortalSidebar')

    <div class="home-section">
        <div class="text-left">FAMILY MEMBERS</div>
        <div class="container-fluid">
            <div class="row" id="home-content">
                <!-- Beneficiary -->
                <div class="col-12">
                    <div class="member-card">
                        <div class="card-body text-center">
                            <div class="member-avatar-container">
                                @if($beneficiary->photo)
                                    <img src="{{ asset('storage/' . $beneficiary->photo) }}" alt="{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}" class="member-avatar">
                                @else
                                    <img src="{{ asset('images/defaultProfile.png') }}" alt="{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}" class="member-avatar">
                                @endif
                            </div>
                            <h5 class="member-name">{{ $beneficiary->first_name }} {{ $beneficiary->last_name }}</h5>
                            <span class="member-relationship">Beneficiary</span>
                            
                            <div class="member-details text-start">
                                <div class="detail-item">
                                    <i class="bi bi-person"></i>
                                    <span class="detail-item-content">{{ $beneficiary->username }}</span>
                                </div>
                                @if($beneficiary->mobile)
                                <div class="detail-item">
                                    <i class="bi bi-telephone"></i>
                                    <span class="detail-item-content">{{ $beneficiary->mobile }}</span>
                                </div>
                                @endif
                                <div class="detail-item">
                                    <i class="bi bi-house"></i>
                                    <span class="detail-item-content">
                                        {{ $beneficiary->street_address }}, 
                                        {{ $beneficiary->barangay->barangay_name ?? '' }}, 
                                        {{ $beneficiary->municipality->municipality_name ?? '' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Family Members -->
                @if(isset($familyMembers) && $familyMembers->count() > 0)
                    @foreach($familyMembers as $member)
                    <div class="col-12">
                        <div class="member-card">
                            <div class="card-body text-center">
                                <div class="member-avatar-container">
                                    @if($member->photo)
                                        <img src="{{ asset('storage/' . $member->photo) }}" alt="{{ $member->first_name }} {{ $member->last_name }}" class="member-avatar">
                                    @else
                                        <img src="{{ asset('images/defaultProfile.png') }}" alt="{{ $member->first_name }} {{ $member->last_name }}" class="member-avatar">
                                    @endif
                                </div>
                                <h5 class="member-name">{{ $member->first_name }} {{ $member->last_name }}</h5>
                                <span class="member-relationship">
                                    {{ $member->relation_to_beneficiary }}
                                    @if($member->is_primary_caregiver)
                                        <span class="badge bg-primary ms-2">Primary Caregiver</span>
                                    @endif
                                </span>
                                
                                <div class="member-details text-start">
                                    <div class="detail-item">
                                        <i class="bi bi-envelope"></i>
                                        <span class="detail-item-content">{{ $member->email }}</span>
                                    </div>
                                    @if($member->mobile)
                                    <div class="detail-item">
                                        <i class="bi bi-telephone"></i>
                                        <span class="detail-item-content">{{ $member->mobile }}</span>
                                    </div>
                                    @endif
                                    <div class="detail-item">
                                        <i class="bi bi-house"></i>
                                        <span class="detail-item-content">{{ $member->street_address }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i> You don't have any family members registered in the system yet.
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </div>
   
    <script src="{{ asset('js/jquery-3.6.0.min.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
</body>
</html>