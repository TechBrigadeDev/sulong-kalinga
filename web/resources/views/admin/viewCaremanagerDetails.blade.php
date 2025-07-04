<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manager Profile Details | Admin</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon-white-16x16.png')}}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/viewManagerDetails.css') }}">
</head>
<body>
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp
    @include('components.adminNavbar')
    @include('components.adminSidebar')
    @include('components.modals.statusChangeCaremanager')
    @include('components.modals.deleteCaremanager')

    <div class="home-section">
        <div class="container-fluid">
            <!-- Header with Action Buttons -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-2">
                <a href="{{ route('admin.caremanagers.index') }}" class="btn btn-secondary desktop-back-btn align-self-start align-self-md-center">
                    <i class="bi bi-arrow-left"></i> {{ T::translate('Back', 'Bumalik')}}
                </a>
                <h4 class="mb-0 text-center" style="font-size: 20px; font-weight: bold; padding: 10px;">
                    {{ T::translate('Care Manager Profile Details', 'MGA DETALYE SA PROFILE NG CARE MANAGER') }}
                </h4>
                <div class="d-flex justify-content-center w-100 justify-content-md-end gap-2 header-buttons">
                    <a href="{{ route('admin.caremanagers.index') }}" class="btn btn-secondary mobile-back-btn" style="height: 33px;">
                        <i class="bi bi-arrow-left"></i> {{ T::translate('Back', 'Bumalik')}}
                    </a>
                    @if(Auth::user()->role_id == 1)
                        <a href="{{ route('admin.caremanagers.edit', $caremanager->id) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-1"></i> {{ T::translate('Edit', 'I-Edit')}}
                        </a>
                        <button type="button" class="btn btn-danger" onclick="openDeleteCaremanagerModal('{{ $caremanager->id }}', '{{ $caremanager->first_name }} {{ $caremanager->last_name }}')">
                            <i class="bi bi-trash me-1"></i> {{ T::translate('Delete', 'Tanggalin')}}
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
                                        <img src="{{ $photoUrl ?? asset('images/defaultProfile.png') }}" 
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
                                                    <i class="bi bi-calendar3 me-1"></i> {{ T::translate('Member since', 'Miyembro magmula')}} {{ $caremanager->status_start_date->format('F j, Y') }}
                                                </p>
                                            </div>
                                            <div class="mt-2 mt-md-0">
                                                <select class="form-select status-select px-5 text-center" 
                                                        name="status" 
                                                        id="statusSelect{{ $caremanager->id }}" 
                                                        onchange="openStatusChangeCaremanagerModal(this, 'Care Manager', {{ $caremanager->id }}, '{{ $caremanager->status }}')">
                                                    <option value="Active" {{ $caremanager->status == 'Active' ? 'selected' : '' }}>{{ T::translate('Active', 'Aktibo')}}</option>
                                                    <option value="Inactive" {{ $caremanager->status == 'Inactive' ? 'selected' : '' }}>{{ T::translate('Inactive', 'Di-Aktibo')}}</option>
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
                                <i class="fas fa-user-circle me-2"></i>{{ T::translate('Personal Information', 'Personal na Impormasyon')}}
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Educational Background', 'Background Pang-Edukasyon')}}</div>
                                            <div class="detail-value">{{ $caremanager->educational_background ?? 'N/A' }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Birthday', 'Kaarawan')}}</div>
                                            <div class="detail-value">{{ $caremanager->birthday->format('F j, Y') }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Gender', 'Kasarian')}}</div>
                                            <div class="detail-value">{{ $caremanager->gender ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Civil Status', 'Katayuan sa Pag-aasawa')}}</div>
                                            <div class="detail-value">{{ $caremanager->civil_status ?? 'N/A' }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Religion', 'Relihiyon')}}</div>
                                            <div class="detail-value">{{ $caremanager->religion ?? 'N/A' }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Nationality', 'Nasyonalidad')}}</div>
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
                                            <div class="detail-label">{{ T::translate('Mobile Number', 'Numero sa Mobile')}}</div>
                                            <div class="detail-value">{{ $caremanager->mobile }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Landline Number', 'Numero sa Landline')}}</div>
                                            <div class="detail-value">{{ $caremanager->landline ?? 'N/A' }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Current Address', 'Kasalukuyang Tirahan')}}</div>
                                            <div class="detail-value">{{ $caremanager->address }}</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="detail-card card mb-4">
                            <div class="card-header detail-card-header">
                                <i class="fas fa-map-marker-alt me-2"></i>{{ T::translate('Assignment Information', 'Impormasyon sa Pagtatalaga')}}
                            </div>
                            <div class="card-body">
                                <div class="detail-item">
                                    <div class="detail-label">{{ T::translate('Assigned Municipality', 'Nai-talagang Munisipalidad')}}</div>
                                    <div class="detail-value">{{ $caremanager->municipality->municipality_name }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Documents and IDs Column -->
                    <div class="col-lg-4">
                        <div class="detail-card card mb-4">
                            <div class="card-header detail-card-header">
                                <i class="fas fa-file-alt me-2"></i>{{ T::translate('Documents', 'Mga Dokumento')}}
                            </div>
                            <div class="card-body">
                                <div class="detail-item">
                                    <div class="detail-label">{{ T::translate('Government Issued ID', 'ID mula sa Gobyerno')}}</div>
                                    <div class="detail-value">
                                        @if($governmentIdUrl)
                                            <a href="{{ $governmentIdUrl }}" target="_blank" class="document-link">
                                                <i class="fas fa-download me-2"></i>{{ T::translate('Download', 'I-Download')}}
                                            </a>
                                        @else
                                            N/A
                                        @endif
                                    </div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Resume / CV</div>
                                    <div class="detail-value">
                                        @if($resumeUrl)
                                            <a href="{{ $resumeUrl }}" target="_blank" class="document-link">
                                                <i class="fas fa-download me-2"></i>{{ T::translate('Download', 'I-Download')}}
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
                                <i class="fas fa-id-card me-2"></i>{{ T::translate('Government ID Numbers', 'Mga Numero ng ID mula sa Gobyerno')}}
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