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
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp
    @include('components.adminNavbar')
    @include('components.adminSidebar')
    @include('components.modals.statusChangeCareworker')
    @include('components.modals.deleteCareworker')

    <div class="home-section">
        <div class="container-fluid">
            <!-- Header with Action Buttons -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-2">
                <a href="{{ route('admin.careworkers.index') }}" class="btn btn-secondary desktop-back-btn align-self-start align-self-md-center">
                    <i class="bi bi-arrow-left"></i> {{ T::translate('Back', 'Bumalik')}}
                </a>
                <h4 class="mb-0 text-center" style="font-size: 20px; font-weight: bold; padding: 10px;">
                    {{ T::translate('CARE WORKER PROFILE DETAILS', 'MGA DETALYE SA PROFILE NG TAGAPAG-ALAGA')}}
                </h4>
                <div class="d-flex justify-content-center w-100 justify-content-md-end gap-2 header-buttons">
                    <a href="{{ route('admin.careworkers.index') }}" class="btn btn-secondary mobile-back-btn" style="height: 33px;">
                        <i class="bi bi-arrow-left"></i> {{ T::translate('Back', 'Bumalik')}}
                    </a>
                    <a href="{{ route('admin.careworkers.edit', $careworker->id) }}" class="btn btn-primary">
                        <i class="bi bi-pencil-square me-1"></i> {{ T::translate('Edit', 'I-Edit')}}
                    </a>
                    <button type="button" class="btn btn-danger" onclick="openDeleteCareworkerModal('{{ $careworker->id }}', '{{ $careworker->first_name }} {{ $careworker->last_name }}')">
                        <i class="bi bi-trash me-1"></i> {{ T::translate('Delete', 'Tanggalin')}}
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
                                        <img src="{{ $careworker->photo ? asset('storage/' . $careworker->photo) : asset('images/defaultProfile.png') }}" 
                                            alt="Profile Picture" 
                                            class="img-fluid rounded-circle profile-img">
                                    </div>
                                    <div class="col-md-9">
                                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-center align-items-md-start profile-header-content">
                                            <div class="text-center text-md-start mb-3 mb-md-0">
                                                <h3 class="mb-1" style="color: var(--secondary-color);">
                                                    {{ $careworker->first_name }} {{ $careworker->last_name }}
                                                </h3>
                                                <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2">
                                                    <span class="badge rounded-pill bg-light text-dark">
                                                        <i class="bi bi-person-badge me-1"></i> {{ T::translate('Care Worker', 'Tagapag-alaga')}}
                                                    </span>
                                                    <span class="badge rounded-pill {{ $careworker->status == 'Active' ? 'badge-active' : 'badge-inactive' }}">
                                                        {{ $careworker->status }}
                                                    </span>
                                                </div>
                                                <p class="text-muted mt-2 mb-0">
                                                    <i class="bi bi-calendar3 me-1"></i> {{ T::translate('Member since', 'Miyembro magmula')}} {{ $careworker->status_start_date->format('F j, Y') }}
                                                </p>
                                            </div>
                                            <div class="mt-2 mt-md-0 status-select-container">
                                                <select class="form-select status-select px-5 text-center" 
                                                        name="status" 
                                                        id="statusSelect{{ $careworker->id }}" 
                                                        onchange="openStatusChangeCareworkerModal(this, 'Care Worker', {{ $careworker->id }}, '{{ $careworker->status }}')">
                                                    <option value="Active" {{ $careworker->status == 'Active' ? 'selected' : '' }}>{{ T::translate('Active', 'Aktibo')}}</option>
                                                    <option value="Inactive" {{ $careworker->status == 'Inactive' ? 'selected' : '' }}>{{ T::translate('Inactive', 'Di-Aktibo')}}</option>
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
                                <i class="bi bi-person-circle me-2"></i>{{ T::translate('Personal Information', 'Personal na Impormasyon')}}
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Educational Background', 'Background Pang-Edukasyon')}}</div>
                                            <div class="detail-value">{{$careworker->educational_background ?? 'N/A'}}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Birthday', 'Kaarawan')}}</div>
                                            <div class="detail-value">{{$careworker->birthday->format('F j, Y')}}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Gender', 'Kasarian')}}</div>
                                            <div class="detail-value">{{$careworker->gender ?? 'N/A'}}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Civil Status', 'Katayuan sa Pag-aasawa')}}</div>
                                            <div class="detail-value">{{$careworker->civil_status ?? 'N/A'}}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Religion', 'Relihiyon')}}</div>
                                            <div class="detail-value">{{$careworker->religion ?? 'N/A'}}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Nationality', 'Nasyonalidad')}}</div>
                                            <div class="detail-value">{{$careworker->nationality ?? 'N/A'}}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Assigned Municipality', 'Nakatalagang Munisipalidad')}}</div>
                                            <div class="detail-value">{{$careworker->municipality->municipality_name}}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Assigned Care Manager', 'Nakatalagang Care Manager')}}</div>
                                            <div class="detail-value">
                                                @if($careworker->assignedCareManager)
                                                    {{ $careworker->assignedCareManager->first_name }} {{ $careworker->assignedCareManager->last_name }}
                                                @else
                                                    <span class="text-muted">{{ T::translate('Unassigned', 'Di-nakatalaga')}}</span>
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
                                            <div class="detail-label">{{ T::translate('Mobile Number', 'Numero sa Mobile')}}</div>
                                            <div class="detail-value">{{$careworker->mobile}}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Landline Number', 'Numero sa Landline')}}</div>
                                            <div class="detail-value">{{$careworker->landline ?? 'N/A'}}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Current Address', 'Kasalukuyang Tirahan')}}</div>
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
                                <i class="bi bi-file-earmark-text me-2"></i>{{ T::translate('Documents', 'Mga Dokumento')}}
                            </div>
                            <div class="card-body">
                                <div class="detail-item">
                                    <div class="detail-label">{{ T::translate('Government Issued ID', 'ID mula sa Gobyerno')}}</div>
                                    <div class="detail-value">
                                        @if($governmentIdUrl)
                                            <a href="{{ $governmentIdUrl }}" target="_blank">{{ T::translate('Download', 'I-Download')}}</a>
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Resume / CV</div>
                                    <div class="detail-value">
                                        @if($resumeUrl)
                                        <a href="{{ $resumeUrl }}" target="_blank">{{ T::translate('Download', 'I-Download')}}</a>
                                    @else
                                        N/A
                                    @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-card card mb-4">
                            <div class="card-header detail-card-header">
                                <i class="bi bi-person-vcard-fill me-2"></i>{{ T::translate('Government ID Numbers', 'Mga Numero ng ID mula sa Gobyerno')}}
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
                        <h4 class="section-title"><i class="bi bi-people me-2"></i>{{ T::translate('Managed Beneficiaries', 'Mga Pinangasiwaang Benepisyaryo')}}</h4>
                        
                        @if($beneficiaries->isEmpty())
                            <div class="empty-state">
                                <i class="bi bi-person-slash fa-3x mb-3" style="color: var(--medium-gray);"></i>
                                <h5>{{ T::translate('No Beneficiaries Assigned', 'Walang mga Nakatalagang Benepisyaryo')}}</h5>
                                <p class="mb-0">{{ T::translate('This care worker is not currently managing any beneficiaries.', 'Ang Tagapag-alaga na ito ay hindi nangangasiwa sa kahit anong benepisyaryo sa kasalukuyan.')}}</p>
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