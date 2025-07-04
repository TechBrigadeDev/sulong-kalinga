<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Profile Details | Admin</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon-white-16x16.png')}}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/viewAdminDetails.css') }}">
</head>
<body>
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp
    @include('components.adminNavbar')
    @include('components.adminSidebar')
    @include('components.modals.statusChangeAdmin')
    @include('components.modals.deleteAdmin')
    @php use App\Helpers\StringHelper;
    use Illuminate\Support\Facades\Auth;
    @endphp

    <div class="home-section">
        <div class="container-fluid">
            <!-- Header with Action Buttons -->
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-2">
                <a href="{{ route('admin.administrators.index') }}" class="btn btn-secondary desktop-back-btn align-self-start align-self-md-center">
                    <i class="bi bi-arrow-left"></i> {{ T::translate('Back', 'Bumalik')}}
                </a>
                <h4 class="mb-0 text-center" style="font-size: 20px; font-weight: bold; padding: 10px;">
                    {{ T::translate('ADMINISTRATOR PROFILE DETAILS', 'MGA DETALYE SA PROFILE NG ADMINISTRATOR') }}
                </h4>
                <div class="d-flex justify-content-center w-100 justify-content-md-end gap-2 header-buttons">
                    <a href="{{ route('admin.administrators.index') }}" class="btn btn-secondary mobile-back-btn" style="height: 33px;">
                        <i class="bi bi-arrow-left"></i> {{ T::translate('Back', '')}}
                    </a>
                    @if(Auth::user()->organization_role_id == 1)
                        <a href="{{ route('admin.administrators.edit', $administrator->id) }}" class="btn btn-primary">
                            <i class="bi bi-pencil-square me-1"></i> {{ T::translate('Edit', 'I-Edit')}}
                        </a>
                        <button class="btn btn-danger" onclick="openDeleteAdminModal('{{ $administrator->id }}', '{{ $administrator->first_name }} {{ $administrator->last_name }}')">
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
                                                    {{ $administrator->first_name }} {{ $administrator->last_name }}
                                                </h3>
                                                <div class="d-flex flex-wrap justify-content-center justify-content-md-start gap-2">
                                                    <span class="badge rounded-pill role-badge">
                                                        <i class="bi bi-person-badge me-1"></i> 
                                                        {{ StringHelper::formatArea($administrator->organizationRole->area ?? 'N/A') }}
                                                    </span>
                                                    <span class="badge rounded-pill {{ $administrator->status == 'Active' ? 'badge-active' : 'badge-inactive' }}">
                                                        {{ $administrator->status }}
                                                    </span>
                                                </div>
                                                <p class="text-muted mt-2 mb-0">
                                                    <i class="bi bi-calendar3 me-1"></i> {{ T::translate('Member since', 'Miyembro magmula:')}} {{ $administrator->status_start_date->format('F j, Y') }}
                                                </p>
                                            </div>
                                            <div class="mt-2 mt-md-0">
                                                @if(isset($administrator->organizationRole) && $administrator->organizationRole->role_name == 'executive_director')
                                                    <span class="badge rounded-pill bg-primary px-4 py-2">
                                                        Executive Director
                                                    </span>
                                                @else
                                                    <select class="form-select status-select px-5 text-center" 
                                                            name="status" 
                                                            id="statusSelect{{ $administrator->id }}" 
                                                            onchange="openStatusChangeAdminModal(this, 'Administrator', {{ $administrator->id }}, '{{ $administrator->status ?? 'Active' }}')">
                                                        <option value="Active" {{ ($administrator->status ?? 'Active') == 'Active' ? 'selected' : '' }}>{{ T::translate('Active', 'Aktibo')}}</option>
                                                        <option value="Inactive" {{ ($administrator->status ?? 'Active') == 'Inactive' ? 'selected' : '' }}>{{ T::translate('Inactive', 'Di-Aktibo')}}</option>
                                                    </select>
                                                @endif
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
                                            <div class="detail-value">{{ $administrator->educational_background ?? 'N/A' }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Birthday', 'Kaarawan')}}</div>
                                            <div class="detail-value">{{ $administrator->birthday->format('F j, Y') }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Gender', 'Kasarian')}}</div>
                                            <div class="detail-value">{{ $administrator->gender ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Civil Status', 'Katayuan sa Pag-aasawa')}}</div>
                                            <div class="detail-value">{{ $administrator->civil_status ?? 'N/A' }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Religion', 'Relihiyon')}}</div>
                                            <div class="detail-value">{{ $administrator->religion ?? 'N/A' }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Nationality', 'Nasyonalidad')}}</div>
                                            <div class="detail-value">{{ $administrator->nationality ?? 'N/A' }}</div>
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
                                            <div class="detail-value">{{ $administrator->email }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Mobile Number', 'Numero sa Mobile')}}</div>
                                            <div class="detail-value">{{ $administrator->mobile }}</div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Landline Number', 'Numero sa Landline')}}</div>
                                            <div class="detail-value">{{ $administrator->landline ?? 'N/A' }}</div>
                                        </div>
                                        <div class="detail-item">
                                            <div class="detail-label">{{ T::translate('Current Address', 'Kasalukuyang Tirahan')}}</div>
                                            <div class="detail-value">{{ $administrator->address }}</div>
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
                                <i class="fas fa-id-card me-2"></i>{{ T::translate('Government ID Numbers', 'Mga Numero sa ID mula sa Gobyerno')}}
                            </div>
                            <div class="card-body">
                                <div class="detail-item">
                                    <div class="detail-label">SSS ID Number</div>
                                    <div class="detail-value">{{ $administrator->sss_id_number ?? 'N/A' }}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">PhilHealth ID Number</div>
                                    <div class="detail-value">{{ $administrator->philhealth_id_number ?? 'N/A' }}</div>
                                </div>
                                <div class="detail-item">
                                    <div class="detail-label">Pag-Ibig ID Number</div>
                                    <div class="detail-value">{{ $administrator->pagibig_id_number ?? 'N/A' }}</div>
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