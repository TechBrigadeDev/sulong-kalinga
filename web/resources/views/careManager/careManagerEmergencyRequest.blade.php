<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Emergency Notices & Service Requests</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/emergencyAndService.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
</head>
<body>
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp

    @include('components.careManagerNavbar')
    @include('components.careManagerSidebar')

    <div class="home-section">
        <div class="page-header">
            <div class="text-left">{{ T::translate('EMERGENCY AND SERVICE REQUEST', 'MGA EMERGENCY AT PAKIUSAP NA SERBISYO')}}</div>
            <a href="{{ route('care-manager.emergency.request.viewHistory') }}"></a>
                <button class="history-btn" id="historyToggle" onclick="window.location.href='/care-manager/emergency-request/view-history'">
                    <i class="bi bi-clock-history me-1"></i> {{ T::translate('View History', 'Tingnan ang Kasaysayan')}}
                </button>
            </a>
        </div>

        <!-- Add this success alert container -->
        <div id="successAlert" class="alert alert-success alert-dismissible fade show d-none" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i>
            <span id="successAlertMessage">{{ T::translate('Action completed successfully!', 'Matagumpay na nakumpleto ang aksyon!')}}</span>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        
        <div class="container-fluid">
            <div class="row" id="home-content">
                <!-- Main Content Column (Emergency/Service Tabs) -->
                <div class="col-lg-8 col-12 main-content-column">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-0">
                                <ul class="nav nav-tabs px-3" id="requestTypeTabs" role="tablist">
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link active" id="emergency-tab" data-bs-toggle="tab" data-bs-target="#emergency" type="button" role="tab">
                                            <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i> {{ T::translate('Emergency', 'Mga Emergency')}}
                                        </button>
                                    </li>
                                    <li class="nav-item" role="presentation">
                                        <button class="nav-link" id="service-tab" data-bs-toggle="tab" data-bs-target="#service" type="button" role="tab">
                                            <i class="bi bi-hand-thumbs-up-fill text-primary me-2"></i> {{ T::translate('Service Request', 'Pakiusap na Serbisyo')}}
                                        </button>
                                    </li>
                                </ul>
                                
                                <div class="tab-content p-3" id="requestTypeTabContent">
                                    <!-- Emergency Tab -->
                                    <div class="tab-pane fade show active" id="emergency" role="tabpanel">
                                        @forelse($emergencyNotices->where('status', 'new') as $notice)
                                            <div class="card notification-card emergency-card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h5 class="card-title fw-bold mb-0 text-dark">{{ $notice->beneficiary->first_name }} {{ $notice->beneficiary->last_name }}</h5>
                                                        <span class="badge bg-danger bg-opacity-10 text-danger">{{ T::translate('New', 'Bago')}}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">{{ T::translate('Address:', 'Tirahan')}}</span>
                                                        <span>{{ $notice->beneficiary->street_address }} ({{ $notice->beneficiary->barangay->barangay_name }}, {{ $notice->beneficiary->municipality->municipality_name }})</span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">Contact:</span>
                                                        <span >{{ $notice->beneficiary->mobile }}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">{{ T::translate('Emergency Contact:', 'Emergency Contact:')}} </span>
                                                        <span class="ms-2">{{ $notice->beneficiary->emergency_contact_name }} ({{ $notice->beneficiary->emergency_contact_relation }}) {{ $notice->beneficiary->emergency_contact_mobile }}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">{{ T::translate('Emergency Type:', 'Uri ng Emergency')}} </span>
                                                        <span class="ms-2"><span class="badge" style="background-color: {{ $notice->emergencyType->color_code }}">{{ $notice->emergencyType->name }}</span></span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-3">
                                                        <span class="info-label">{{ T::translate('Message:', 'Mensahe')}} </span>
                                                        <span>{{ Str::limit($notice->message, 100) }}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($notice->created_at)->diffForHumans() }}</small>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="viewEmergencyDetails({{ $notice->notice_id }})">
                                                                <i class="bi bi-eye me-1"></i> {{ T::translate('View', 'Tingnan')}}
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-danger" onclick="openRespondEmergencyModal({{ $notice->notice_id }})">
                                                                <i class="bi bi-reply me-1"></i> {{ T::translate('Respond', 'Tumugon')}}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="empty-state">
                                                <i class="bi bi-inbox-fill" style="font-size: 3rem;"></i>
                                                <h5 class="mt-3">{{ T::translate('No New Emergency Notices', 'Walang mga bagong emergency notice')}}</h5>
                                                <p>{{ T::translate('There are no new emergency notices at the moment.', 'Walang bagong emergency notice sa ngayon.')}}</p>
                                            </div>
                                        @endforelse
                                    </div>
                                    
                                    <!-- Service Request Tab -->
                                    <div class="tab-pane fade" id="service" role="tabpanel">
                                        @forelse($serviceRequests->where('status', 'new') as $request)
                                            <div class="card notification-card request-card">
                                                <div class="card-body">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h5 class="card-title fw-bold mb-0 text-dark">{{ $request->beneficiary->first_name }} {{ $request->beneficiary->last_name }}</h5>
                                                        <span class="badge bg-primary bg-opacity-10 text-primary">{{ T::translate('New', 'Bago')}}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">{{ T::translate('Service Type:', 'Uri ng Serbisyo:')}}</span>
                                                        <span><span class="badge" style="background-color: {{ $request->serviceType->color_code }}">{{ $request->serviceType->name }}</span></span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">{{ T::translate('Requested Date:', 'Petsa ng Pakiusap:')}}</span>
                                                        <span>{{ \Carbon\Carbon::parse($request->service_date)->format('M d, Y') }}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">{{ T::translate('Requested Time:', 'Oras ng Pakiusap:')}}</span>
                                                        <span>{{ $request->service_time ? \Carbon\Carbon::parse($request->service_time)->format('h:i A') : 'Flexible' }}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-3">
                                                        <span class="info-label">{{ T::translate('Message:', 'Mensahe:')}}</span>
                                                        <span>{{ Str::limit($request->message, 100) }}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <small class="text-muted">{{ \Carbon\Carbon::parse($request->created_at)->diffForHumans() }}</small>
                                                        <div class="btn-group">
                                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="viewServiceRequestDetails({{ $request->service_request_id }})">
                                                                <i class="bi bi-eye me-1"></i> {{ T::translate('View', 'Tingnan')}}
                                                            </button>
                                                            <button type="button" class="btn btn-sm btn-outline-primary" onclick="openHandleServiceRequestModal({{ $request->service_request_id }})">
                                                                <i class="bi bi-reply me-1"></i> {{ T::translate('Handle', 'I-Handle')}}
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @empty
                                            <div class="empty-state">
                                                <i class="bi bi-inbox-fill" style="font-size: 3rem;"></i>
                                                <h5 class="mt-3">{{ T::translate('No New Service Requests', 'Walang mga bagong Pakisap na Serbisyo')}}</h5>
                                                <p>{{ T::translate('There are no new service requests at the moment.', 'Walang mga bagong pakiusap na serbisyo sa ngayon.')}}</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Pending/In Progress Column -->
                    <div class="col-lg-4 col-12 pending-column">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-header-custom">
                                <h5 class="card-title mb-0 section-header text-warning">
                                    <i class="bi bi-hourglass-split me-2"></i>{{ T::translate('Pending/In Progress', 'Nakabinbin/Isinasagawa')}}
                                </h5>
                            </div>
                            <div class="card-body p-3">
                                <!-- Tab-specific content containers -->
                                <div id="emergency-pending-content" class="pending-tab-content active">
                                    <!-- In Progress Emergency Notices -->
                                    @forelse($emergencyNotices->where('status', 'in_progress') as $notice)
                                        <div class="card notification-card pending-card mb-3 clickable-card" onclick="viewEmergencyDetails({{ $notice->notice_id }})">
                                            <div class="card-body">
                                                <div class="card-content">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="fw-bold mb-0 text-dark">{{ $notice->beneficiary->first_name }} {{ $notice->beneficiary->last_name }}</h6>
                                                        <span class="badge bg-info bg-opacity-10 text-info">{{ T::translate('In Progress', 'Isinasagawa')}}</span>
                                                    </div>
                                                    
                                                    @if($notice->action_taken_by)
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">{{ T::translate('Responded:', 'Tinugunan:')}}</span>
                                                        <span>{{ $notice->actionTakenBy ? $notice->actionTakenBy->first_name . ' ' . $notice->actionTakenBy->last_name : 'Unknown' }}</span>
                                                    </div>
                                                    @endif
                                                    
                                                    <div class="d-flex flex-wrap mb-3">
                                                        <span class="info-label">{{ T::translate('Type:', 'Uri:')}}</span>
                                                        <span><span class="badge" style="background-color: {{ $notice->emergencyType->color_code }}">{{ $notice->emergencyType->name }}</span></span>
                                                    </div>
                                                </div>
                                                
                                                <div class="card-footer-actions">
                                                    <small class="text-muted">
                                                        <i class="bi bi-clock me-1"></i> {{ T::translate('Started', 'Sinimulan')}} {{ \Carbon\Carbon::parse($notice->action_taken_at)->diffForHumans() }}
                                                    </small>
                                                    <div class="btn-group" onclick="event.stopPropagation()">
                                                        <button class="btn btn-sm btn-outline-primary" onclick="openRespondEmergencyModal({{ $notice->notice_id }})">
                                                            <i class="bi bi-pencil-square me-1"></i> {{ T::translate('Update', 'I-update')}}
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-success" onclick="openResolveEmergencyModal({{ $notice->notice_id }})">
                                                            <i class="bi bi-check-circle me-1"></i> {{ T::translate('Resolve', 'Lutasin')}}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="empty-state">
                                            <i class="bi bi-hourglass text-muted" style="font-size: 2rem;"></i>
                                            <h6 class="mt-3">{{ T::translate('No Active Emergencies', 'Walang Aktibong Emergencies.')}}</h6>
                                            <p class="small">{{ T::translate('No in-progress emergency notices at the moment.', 'Walang isinasagawa na emergency notice sa ngayon.')}}</p>
                                        </div>
                                    @endforelse
                                </div>
                                
                                <div id="service-pending-content" class="pending-tab-content" style="display: none;">
                                    <!-- Approved Service Requests -->
                                    @forelse($serviceRequests->where('status', 'approved') as $request)
                                        <div class="card notification-card pending-card mb-3 clickable-card" onclick="viewServiceRequestDetails({{ $request->service_request_id }})">
                                            <div class="card-body">
                                                <div class="card-content">
                                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                                        <h6 class="fw-bold mb-0 text-dark">{{ $request->beneficiary->first_name }} {{ $request->beneficiary->last_name }}</h6>
                                                        <span class="badge bg-success bg-opacity-10 text-success">{{ T::translate('Approved', 'Naaprubahan')}}</span>
                                                    </div>

                                                    <div class="d-flex flex-wrap mb-2">
                                                        <h6 class="fw mb-0 text-primary">{{ $request->serviceType->name }}</h6>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-1">
                                                        <span class="info-label">{{ T::translate('Approved:', 'Naaprubahan:')}}</span>
                                                        <span>{{ $request->actionTakenBy ? $request->actionTakenBy->first_name . ' ' . $request->actionTakenBy->last_name : 'Unknown' }}</span>
                                                    </div>
                                                    
                                                    <div class="d-flex flex-wrap mb-3">
                                                        <span class="info-label">{{ T::translate('Assigned To:', 'Itinalaga kay:')}}</span>
                                                        <span>{{ $request->careWorker ? $request->careWorker->first_name . ' ' . $request->careWorker->last_name : 'Not assigned' }}</span>
                                                    </div>
                                                </div>
                                                
                                                <div class="card-footer-actions">
                                                    <small class="text-muted">
                                                        <i class="bi bi-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($request->service_date)->format('M d') }}
                                                    </small>
                                                    <div class="btn-group" onclick="event.stopPropagation()">
                                                        <button class="btn btn-sm btn-outline-primary" onclick="openHandleServiceRequestModal({{ $request->service_request_id }})">
                                                            <i class="bi bi-pencil-square me-1"></i> {{ T::translate('Update', 'I-update')}}
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-success" onclick="openCompleteServiceRequestModal({{ $request->service_request_id }})">
                                                            <i class="bi bi-check-circle me-1"></i> {{ T::translate('Complete', 'Natapos')}}
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @empty
                                        <div class="empty-state">
                                            <i class="bi bi-hourglass text-muted" style="font-size: 2rem;"></i>
                                            <h6 class="mt-3">{{ T::translate('No Active Service Requests', 'Walang mga Aktibong Pakiusap na Serbisyo')}}</h6>
                                            <p class="small">{{ T::translate('No approved service requests at the moment.', 'Walang naaprubahan na mga pakiusap na serbisyo sa ngayon.')}}</p>
                                        </div>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Emergency Response Modal -->
    <div class="modal fade" id="respondEmergencyModal" tabindex="-1" aria-labelledby="respondEmergencyModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-danger text-white">
            <h5 class="modal-title" id="respondEmergencyModalLabel"><i class="bi bi-exclamation-triangle-fill"></i> {{ T::translate('Respond to Emergency', 'Tumugon sa Emergency')}}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="respondEmergencyForm">
            <input type="hidden" id="emergencyNoticeId" name="notice_id">
            
            <div class="emergency-details mb-4">
                <div class="alert alert-light border">
                <div class="d-flex justify-content-between">
                    <h6 class="text-dark" id="emergencyBeneficiaryName">Loading...</h6>
                    <span class="badge bg-danger" id="emergencyTypeBadge">Loading...</span>
                </div>
                <div class="text-muted mb-2" id="emergencyDateTime">Loading...</div>
                <div class="border-top pt-2 mt-2" id="emergencyMessage">Loading...</div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="updateType" class="form-label">{{ T::translate('Response Type', 'Uri ng Tugon')}}</label>
                <select class="form-select" id="updateType" name="update_type" required>
                <option value="response">{{ T::translate('Emergency Response', 'Tugon na Emergency')}}</option>
                <option value="resolution">{{ T::translate('Resolve Emergency', 'Lutasin ang Emergency')}}</option>
                <option value="note">{{ T::translate('Add Note Only', 'Magdagdag lamang ng tala ')}}</option>
                </select>
                 <small id="resolutionWarning" class="text-danger d-none mt-1">
                <i class="bi bi-exclamation-circle"></i> {{ T::translate('Resolving will archive this emergency and move it to history.', 'Ang pagresolba ay mag-aarchive ng emergency na ito at ililipat ito sa kasaysayan.')}}
                </small>
            </div>

            <div class="mb=3">
                <small class="text-primary mt-1">
                    <i class="bi bi-info-circle-fill"></i> {{ T::translate('Adding notes will only be visible to all COSE Staff Members and will not be seen by beneficiaries and their family members.', 'Ang pagdaragdag ng mga tala ay makikita lamang ng lahat ng mga Miyembro ng COSE Staff at hindi makikita ng mga benepisyaryo at kanilang mga miyembro ng pamilya.')}}
                </small>
            </div>
            <br>
            
            <div class="mb-3">
                <label for="responseMessage" class="form-label">{{ T::translate('Response Message', 'Tugon na Mensahe')}}</label>
                <textarea class="form-control" id="responseMessage" name="message" rows="4" placeholder="Enter your response message" required></textarea>
            </div>
            
            <div class="password-confirmation d-none mb-3">
                <div class="alert alert-warning">
                <i class="bi bi-shield-lock"></i> {{ T::translate('This action requires password confirmation', 'Ang aksiyong ito ay nangangailangan ng kumpirmasyon ng password')}}
                </div>
                <label for="confirmPassword" class="form-label">{{ T::translate('Your Password', 'Iyong Password')}}</label>
                <input type="password" class="form-control" id="confirmPassword" name="password" placeholder="{{ T::translate('Enter your password', 'Ilagay ang iyong password')}}">
                <div class="invalid-feedback">{{ T::translate('Incorrect password', 'Mali ang password')}}</div>
            </div>
            
            <div class="previous-updates d-none mb-4">
                <h6 class="border-bottom pb-2">{{ T::translate('Previous Updates', 'Nakaraang mga Update')}}</h6>
                <div id="updateHistoryContainer">
                <!-- Previous updates will be loaded here -->
                </div>
            </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ T::translate('Cancel', 'I-Kansela')}}</button>
            <button type="button" class="btn btn-danger" id="submitEmergencyResponse">{{ T::translate('Submit Response', 'Isumite ang Tugon')}}</button>
        </div>
        </div>
    </div>
    </div>

    <!-- Service Request Response Modal -->
    <div class="modal fade" id="handleServiceRequestModal" tabindex="-1" aria-labelledby="handleServiceRequestModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-primary text-white">
            <h5 class="modal-title" id="handleServiceRequestModalLabel"><i class="bi bi-hand-thumbs-up"></i> {{ T::translate('Handle Service Request', 'I-Handle ang Pakiusap na Serbisyo')}}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <form id="handleServiceRequestForm">
            <input type="hidden" id="serviceRequestId" name="service_request_id">
            
            <div class="service-request-details mb-4">
                <div class="alert alert-light border">
                <div class="d-flex justify-content-between">
                    <h6 class="text-dark" id="requestBeneficiaryName">Loading...</h6>
                    <span class="badge bg-primary" id="requestTypeBadge">Loading...</span>
                </div>
                <div class="text-muted mb-2" id="requestDateTime">Loading...</div>
                <div class="border-top pt-2 mt-2" id="requestMessage">Loading...</div>
                <div class="mt-2">
                    <strong>{{ T::translate('Requested Date:', 'Petsa ng Pakiusap:')}}</strong> <span id="requestedServiceDate">Loading...</span>
                </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="serviceUpdateType" class="form-label">{{ T::translate('Action', 'Aksyon')}}</label>
                <select class="form-select" id="serviceUpdateType" name="update_type" required>
                <option value="approval">{{ T::translate('Approve Request', 'Aprubahan ang Pakiusap')}}</option>
                <option value="rejection">{{ T::translate('Reject Request', 'Tanggihan ang Pakiusap')}}</option>
                <option value="completion">{{ T::translate('Mark as Completed', 'Markahan Bilang Nakumpleto')}}</option>
                <option value="note">{{ T::translate('Add Note Only', 'Magdagdag lamang ng Tala')}}</option>
                </select>
                <small id="completionWarning" class="text-danger d-none mt-1">
                    <i class="bi bi-exclamation-circle"></i> {{ T::translate('Completing will archive this service request and move it to history. Approve and process a request first before marking it complete, or reject it.', 'Ang pagkumpleto ay mag-aarchive ng pakiusap na serbisyo at ililipat ito sa kasaysayan. Aprubahan at iproseso muna ang isang pakiusap bago markahan itong kumpleto, o tanggihan ito.')}}
                </small>
                <small id="rejectionWarning" class="text-danger d-none mt-1">
                    <i class="bi bi-exclamation-circle"></i> {{ T::translate('Rejecting will archive this service request and move it to history.', 'Ang pagtanggi ay mag-aarchive ng pakiusap na serbisyo at ililipat ito sa kasaysayan.')}}
                </small>
                <small id="approvalWarning" class="text-danger d-none mt-1">
                    <i class="bi bi-exclamation-circle"></i> {{ T::translate('Cannot re-approve an already approved request.', 'Hindi maaaring muling aprubahan ang isang pakiusap na naaprubahan na.')}}
                </small>
            </div>

            <div class="mb=3">
                <small class="text-primary mt-1">
                    <i class="bi bi-info-circle-fill"></i> {{ T::translate('Adding notes will only be visible to all COSE Staff Members and will not be seen by beneficiaries and their family members.', 'Ang pagdaragdag ng mga tala ay makikita lamang ng lahat ng mga Miyembro ng COSE Staff at hindi makikita ng mga benepisyaryo at kanilang mga miyembro ng pamilya.')}}
                </small>
            </div>
            <br>
            
            <div class="mb-3 care-worker-options">
                <label for="serviceCareWorkerId" class="form-label">{{ T::translate('Assign Care Worker', 'Magtalaga ng Care Worker')}}</label>
                <select class="form-select" id="serviceCareWorkerId" name="care_worker_id">
                <option value="">-- {{ T::translate('Select Care Worker', 'Pumili ng Care Worker')}} --</option>
                <!-- Will be populated with care workers -->
                </select>
            </div>
            
            <div class="mb-3">
                <label for="serviceResponseMessage" class="form-label">{{ T::translate('Message', 'Mensahe')}}</label>
                <textarea class="form-control" id="serviceResponseMessage" name="message" rows="4" placeholder="Enter your response message" required></textarea>
            </div>
            
            <div class="service-password-confirmation d-none mb-3">
                <div class="alert alert-warning">
                <i class="bi bi-shield-lock"></i> {{ T::translate('This action requires password confirmation', 'Ang aksiyong ito ay nangangailangan ng kumpirmasyon ng password')}}
                </div>
                <label for="serviceConfirmPassword" class="form-label">{{ T::translate('Your Password', 'Iyong Password')}}</label>
                <input type="password" class="form-control" id="serviceConfirmPassword" name="password" placeholder="{{ T::translate('Enter your password', 'Ilagay ang iyong password')}}">
                <div class="invalid-feedback">{{ T::translate('Incorrect password', 'Mali ang password')}}</div>
            </div>
            
            <div class="service-previous-updates d-none mb-4">
                <h6 class="border-bottom pb-2">{{ T::translate('Previous Updates', 'Nakaraang mga Update')}}</h6>
                <div id="serviceUpdateHistoryContainer">
                <!-- Previous updates will be loaded here -->
                </div>
            </div>
            </form>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ T::translate('Cancel', 'I-Kansela')}}</button>
            <button type="button" class="btn btn-primary" id="submitServiceResponse">{{ T::translate('Submit', 'I-Sumite')}}</button>
        </div>
        </div>
    </div>
    </div>

    <!-- Emergency Details Modal (Read-only) -->
    <div class="modal fade" id="emergencyDetailsModal" tabindex="-1" aria-labelledby="emergencyDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-secondary text-white">
            <h5 class="modal-title" id="emergencyDetailsModalLabel"><i class="bi bi-info-circle"></i> {{ T::translate('Emergency Details', 'Mga Detalye ng Emergency')}}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div id="emergencyDetailsContent">
            <!-- Emergency details will be loaded here -->
            </div>
            
            <div class="updates-history mt-4">
            <h6 class="border-bottom pb-2">{{ T::translate('Updates History', 'Kasaysayan ng mga Update')}}</h6>
            <div id="emergencyUpdatesTimeline">
                <!-- Updates will be loaded here -->
            </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ T::translate('Close', 'Isara')}}</button>
            <button type="button" class="btn btn-primary" id="respondToEmergencyBtn">{{ T::translate('Respond', 'Tumugon')}}</button>
        </div>
        </div>
    </div>
    </div>

    <!-- Service Request Details Modal -->
    <div class="modal fade" id="serviceRequestDetailsModal" tabindex="-1" aria-labelledby="serviceRequestDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
        <div class="modal-header bg-secondary text-white">
            <h5 class="modal-title" id="serviceRequestDetailsModalLabel"><i class="bi bi-info-circle"></i> {{ T::translate('Service Request Details', 'Detalye ng mga Pakiusap na Serbisyo')}}</h5>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
            <div id="serviceRequestDetailsContent">
            <!-- Service request details will be loaded here -->
            </div>
            
            <div class="updates-history mt-4">
            <h6 class="border-bottom pb-2">{{ T::translate('Updates History', 'Kasaysayan ng mga Update')}}</h6>
            <div id="serviceUpdatesTimeline">
                <!-- Updates will be loaded here -->
            </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ T::translate('Close', 'Isara')}}</button>
            <button type="button" class="btn btn-primary" id="handleServiceRequestBtn">{{ T::translate('Respond', 'Tumugon')}}</button>
        </div>
        </div>
    </div>
    </div>
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Initialize Bootstrap tabs
        var tabEls = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabEls.forEach(function(tabEl) {
            new bootstrap.Tab(tabEl);
        });
    </script>

    <script>
        // Global variables to track current records
        let currentEmergency = null;
        let currentServiceRequest = null;

        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Fallback for toastr if it's not defined
        if (typeof toastr === 'undefined') {
            window.toastr = {
                success: function(message) { alert('Success: ' + message); },
                error: function(message) { alert('Error: ' + message); },
                warning: function(message) { alert('Warning: ' + message); },
                info: function(message) { alert('Info: ' + message); }
            };
        }

        // ===== EMERGENCY NOTICE HANDLERS =====
        function viewEmergencyDetails(noticeId) {
            // Show loading state
            $('#emergencyDetailsContent').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">{{ T::translate('Loading details...', 'Naglo-load ng mga detalye...')}}</p></div>');
            
            // Open the modal
            $('#emergencyDetailsModal').modal('show');
            
            // Fetch emergency details
            $.ajax({
                url: "/care-manager/emergency-request/emergency/" + noticeId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        // Store current emergency for later use
                        currentEmergency = response.emergency_notice;
                        
                        // Render emergency details
                        renderEmergencyDetails(currentEmergency);
                        
                        // Show or hide respond button based on role
                        const userRole = '{{ Auth::user()->role_id }}';
                        if (userRole <= 2) { // Admin or Care Manager
                            $('#respondToEmergencyBtn').show();
                        } else {
                            // For care workers, show remind button instead
                            $('#respondToEmergencyBtn').text('{{ T::translate('Send Reminder', 'Magpadala ng Paalala')}}').removeClass('btn-primary').addClass('btn-info');
                        }
                    } else {
                        $('#emergencyDetailsContent').html(`<div class="alert alert-danger">{{ T::translate('Error loading details:', 'Error sa pag-load ng mga detalye:')}} ${response.message}</div>`);
                    }
                },
                error: function() {
                    $('#emergencyDetailsContent').html('<div class="alert alert-danger">{{ T::translate('Failed to load emergency details. Please try again.', 'Nabigong i-load ang mga detalye ng emergency. Mangyaring subukan ulit.')}}</div>');
                }
            });
        }

        function openRespondEmergencyModal(noticeId = null) {
            // If noticeId is provided, fetch fresh data, otherwise use current emergency
            if (noticeId) {
                $.ajax({
                    url: "/care-manager/emergency-request/emergency/" + noticeId,
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            currentEmergency = response.emergency_notice;
                            populateEmergencyResponseModal(currentEmergency);
                            $('#respondEmergencyModal').modal('show');
                        }
                    },
                    error: function() {
                        alert('{{ T::translate('Failed to load emergency details. Please try again.', 'Nabigong i-load ang mga detalye ng emergency. Mangyaring subukan ulit.')}}');
                    }
                });
            } else if (currentEmergency) {
                populateEmergencyResponseModal(currentEmergency);
                $('#respondEmergencyModal').modal('show');
            }
        }

        function populateEmergencyResponseModal(emergency) {
            // Set basic details
            $('#emergencyNoticeId').val(emergency.notice_id);
            $('#emergencyBeneficiaryName').text(`${emergency.beneficiary.first_name} ${emergency.beneficiary.last_name}`);
            $('#emergencyTypeBadge').text(emergency.emergency_type.name);
            
            // Format date
            const createdDate = new Date(emergency.created_at);
            $('#emergencyDateTime').text(`{{ T::translate('Reported on:', 'I-ulat noong:')}} ${createdDate.toLocaleDateString()} {{ T::translate('at', 'sa')}} ${createdDate.toLocaleTimeString()}`);
            
            // Message
            $('#emergencyMessage').text(emergency.message);
            
            // Load previous updates if any
            if (emergency.updates && emergency.updates.length > 0) {
                $('.previous-updates').removeClass('d-none');
                let updatesHtml = '';
                
                emergency.updates.forEach(update => {
                    const updateDate = new Date(update.created_at);
                    const formattedDate = `${updateDate.toLocaleDateString()} ${updateDate.toLocaleTimeString()}`;
                    
                    updatesHtml += `
                        <div class="update-item mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="badge ${getUpdateTypeBadgeClass(update.update_type)}">${formatUpdateType(update.update_type)}</span>
                                <small class="text-muted">${formattedDate}</small>
                            </div>
                            <div class="update-message mt-1 border-bottom pb-2">${update.message}</div>
                        </div>
                    `;
                });
                
                $('#updateHistoryContainer').html(updatesHtml);
            } else {
                $('.previous-updates').addClass('d-none');
            }
            
            // Reset form fields
            $('#updateType').val('response');
            $('#responseMessage').val('');
            $('#statusChangeTo').val('in_progress');
            $('#confirmPassword').val('');
            
            // Handle form fields visibility based on selected update type
            handleUpdateTypeChange();
            
            // Load care workers for assignment option
            loadCareWorkers();
        }

        // Event handler for update type change
        $('#updateType').on('change', handleUpdateTypeChange);

        function handleUpdateTypeChange() {
            const updateType = $('#updateType').val();
            
            // Hide all conditional sections and warnings first
            // Remove assignment-options from this line
            $('.password-confirmation').addClass('d-none');
            $('#resolutionWarning').addClass('d-none');
            
            // Show relevant sections based on update type
            switch(updateType) {
                // Remove the assignment case entirely
                case 'resolution':
                    $('.password-confirmation').removeClass('d-none');
                    $('#resolutionWarning').removeClass('d-none'); // Show warning for resolution
                    break;
            }
        }

        // Helper function to get badge class based on update type
        function getUpdateTypeBadgeClass(updateType) {
            switch(updateType) {
                case 'response': return 'bg-primary';
                case 'status_change': return 'bg-warning text-dark';
                case 'assignment': return 'bg-info text-dark';
                case 'resolution': return 'bg-success';
                case 'note': return 'bg-secondary';
                default: return 'bg-secondary';
            }
        }

        // Helper function to format update type
        function formatUpdateType(updateType) {
            switch(updateType) {
                case 'response': return '{{ T::translate('Response', 'Tugon')}}';
                case 'status_change': return '{{ T::translate('Status Change', 'Pagbabago ng Katayuan')}}';
                case 'assignment': return '{{ T::translate('Assignment', 'Pagtalaga')}}';
                case 'resolution': return '{{ T::translate('Resolution', 'Resolusyon')}}';
                case 'note': return '{{ T::translate('Note', 'Tala')}}';
                default: return updateType;
            }
        }

        // Submit emergency response
        $('#submitEmergencyResponse').on('click', function() {
            const form = $('#respondEmergencyForm');
            const formData = new FormData(form[0]);
            
            // Validate form
            if (!form[0].checkValidity()) {
                form[0].reportValidity();
                return;
            }
            
            // Show loading state
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ T::translate('Submitting...', 'Isinumite...')}}');
            
            // Submit form
            $.ajax({
                url: "/care-manager/emergency-request/respond-emergency",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        toastr.success('{{ T::translate('Emergency response submitted successfully', 'Matagumpay na na-isumite ang tugon sa emergency')}}');
                        
                        // Close modal
                        $('#respondEmergencyModal').modal('hide');

                        // Show prominent success message if it was a resolution
                        if ($('#updateType').val() === 'resolution') {
                            const beneficiaryName = $('#emergencyBeneficiaryName').text();
                            const message = `{{ T::translate('Emergency for', 'Ang emergency para kay')}} ${beneficiaryName} {{ T::translate('has been successfully resolved.', 'ay matagumpay na nalutas.')}}`;
                            showSuccessAlert(message);
                        }
                        
                        // Reload page after a brief delay
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        // Show error message
                        toastr.error(response.message || '{{ T::translate('Failed to submit response', 'Nabigong isumite ang tugon')}}');
                        
                        // If password error
                        if (response.errors && response.errors.password) {
                            $('#confirmPassword').addClass('is-invalid');
                            $('.invalid-feedback').text(response.errors.password[0]).show();
                        }
                    }
                },
                error: function(xhr) {
                    // Parse error response
                    let errorMessage = '{{ T::translate('Failed to submit response', 'Nabigong isumite ang tugon')}}';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.errors && response.errors.password) {
                            $('#confirmPassword').addClass('is-invalid');
                            $('.invalid-feedback').text(response.errors.password[0]).show();
                            errorMessage = response.errors.password[0];
                        } else if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {}
                    
                    toastr.error(errorMessage);
                },
                complete: function() {
                    // Reset button state
                    $('#submitEmergencyResponse').prop('disabled', false).html('{{ T::translate('Submit Response', 'Isumite ang Tugon')}}');
                }
            });
        });

        // Add code to check for stored messages on page load
        $(document).ready(function() {
            const storedMessage = sessionStorage.getItem('emergencySuccessMessage');
            if (storedMessage) {
                showSuccessAlert(storedMessage);
                // Clear the message so it doesn't show again on next refresh
                sessionStorage.removeItem('emergencySuccessMessage');
            }

             // Check if we need to switch to the service tab
            const activeTab = sessionStorage.getItem('activeTab');
            if (activeTab === 'service') {
                // Activate the service tab
                $('#service-tab').tab('show');
                
                // Update the pending column view
                $('#emergency-pending-content').hide();
                $('#service-pending-content').show();
                
                // Clear the stored tab
                sessionStorage.removeItem('activeTab');
            }

            // Service request action success message
            const serviceActionType = sessionStorage.getItem('serviceActionType');
            if (serviceActionType) {
                // Show success message if not already shown
                if (!storedMessage) {
                    const message = `{{ T::translate('Service request has been', 'Ang pakiusap na serbisyo ay')}} ${serviceActionType} {{ T::translate('successfully.', 'matagumpay.')}}`;
                    showSuccessAlert(message);
                }
                // Clear the stored action type
                sessionStorage.removeItem('serviceActionType');
            }

            // Handle service request update submission
            $('#submitServiceResponse').on('click', function() {
                // Get form data from the correct service form fields
                const requestId = $('#serviceRequestId').val();
                const message = $('#serviceResponseMessage').val();  // CORRECT ID
                const updateType = $('#serviceUpdateType').val() || 'note'; // CORRECT ID
                const careWorkerId = $('#serviceCareWorkerId').val();
                
                // Basic validation
                if (!message) {
                    toastr.error('{{ T::translate('Please enter a response message', 'Mangyaring maglagay ng mensahe sa pagtugon')}}');
                    return;
                }

                 // Add care worker validation for approval
                if (updateType === 'approval' && !careWorkerId) {
                    toastr.error('{{ T::translate('Please assign a care worker when approving a service request', 'Mangyaring magtalaga ng care worker kapag inaaprubahan ang pakiusap na serbisyo')}}');
                    // Highlight the select box to indicate it needs attention
                    $('#serviceCareWorkerId').addClass('is-invalid');
                    return;
                }
                
                // Clear any previous validation error styling
                $('#serviceCareWorkerId').removeClass('is-invalid');
                
                // Show loading state
                $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ T::translate('Processing...', 'Pinoproseso...')}}');
                $(this).prop('disabled', true);
                
                // Create form data
                const formData = new FormData();
                formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
                formData.append('service_request_id', requestId);
                formData.append('message', message);
                formData.append('update_type', updateType);
                
                // Only add care worker ID if provided and relevant
                if (careWorkerId && (updateType === 'approval' || updateType === 'assignment')) {
                    formData.append('care_worker_id', careWorkerId);
                }
                
                // Add password if completion type
                if (updateType === 'completion') {
                    const password = $('#serviceConfirmPassword').val();
                    if (password) {
                        formData.append('password', password);
                    }
                }
                
                // Submit request
                $.ajax({
                    url: "/care-manager/emergency-request/handle-service",
                    method: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function(response) {
                        if (response.success) {
                            // Toast notification (existing)
                            toastr.success('{{ T::translate('Service request updated successfully', 'Matagumpay na na-update ang pakiusap na serbisyo')}}');
                            
                            // Close modal
                            $('#handleServiceRequestModal').modal('hide');
                            
                            // Save the current tab for reload
                            sessionStorage.setItem('activeTab', 'service');
                            
                            // Show prominent success message for completion or rejection
                            if (updateType === 'completion' || updateType === 'rejection') {
                                const beneficiaryName = $('#requestBeneficiaryName').text();
                                const actionType = updateType === 'completion' ? '{{ T::translate('completed', 'nakumpleto')}}' : '{{ T::translate('rejected', 'tinanggihan')}}';
                                const message = `{{ T::translate('Service request for', 'Ang pakiusap na serbisyo para kay')}} ${beneficiaryName} {{ T::translate('has been', 'ay')}} ${actionType} {{ T::translate('successfully.', 'matagumpay.')}}`;
                                showSuccessAlert(message);
                                
                                // Store the action type for checking after reload
                                sessionStorage.setItem('serviceActionType', actionType);
                            }
                            
                            // Reload page after a brief delay
                            setTimeout(() => {
                                location.reload();
                            }, 1000);
                        } else {
                            toastr.error(response.message || '{{ T::translate('Failed to update service request', 'Nabigong i-update ang pakiusap na serbisyo')}}');
                            $('#submitServiceResponse').html('{{ T::translate('Submit', 'I-Sumite')}}');
                            $('#submitServiceResponse').prop('disabled', false);
                        }
                    },
                    error: function(xhr) {
                        let errorMessage = '{{ T::translate('Failed to update service request', 'Nabigong i-update ang pakiusap na serbisyo')}}';
                        
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const errors = xhr.responseJSON.errors;
                            
                            // Check specifically for password errors and show a clearer message
                            if (errors.password) {
                                $('#serviceConfirmPassword').addClass('is-invalid');
                                $('.service-password-confirmation .invalid-feedback').text(errors.password[0]).show();
                                errorMessage = '{{ T::translate('Password is incorrect. Please try again.', 'Mali ang password. Mangyaring subukan muli.')}}';
                            } else {
                                // Handle other validation errors
                                const firstError = Object.values(errors)[0];
                                if (firstError && firstError[0]) {
                                    errorMessage = firstError[0];
                                }
                            }
                        }
                        
                        toastr.error(errorMessage);
                        $('#submitServiceResponse').html('{{ T::translate('Submit', 'I-Sumite')}}');
                        $('#submitServiceResponse').prop('disabled', false);
                    }
                });
            });
            
            // Also make sure CSRF token is set up
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            
            // For debugging
            console.log('Service request handlers initialized');
        });

        // ===== SERVICE REQUEST HANDLERS =====
        function viewServiceRequestDetails(requestId) {
            // Show loading state
            $('#serviceRequestDetailsContent').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">{{ T::translate('Loading details...', 'Naglo-load ng mga detalye...')}}</p></div>');
            
            // Open the modal
            $('#serviceRequestDetailsModal').modal('show');
            
            // Fetch service request details
            $.ajax({
                url: "/care-manager/emergency-request/service-request/" + requestId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        // Store current service request for later use
                        currentServiceRequest = response.service_request;
                        
                        // Render service request details
                        renderServiceRequestDetails(currentServiceRequest);
                        
                        // Show or hide respond button based on role
                        const userRole = '{{ Auth::user()->role_id }}';
                        if (userRole <= 2) { // Admin or Care Manager
                            $('#handleServiceRequestBtn').show();
                        } else {
                            // For care workers, show remind button instead
                            $('#handleServiceRequestBtn').text('{{ T::translate('Send Reminder', 'Magpadala ng Paalala')}}').removeClass('btn-primary').addClass('btn-info');
                        }
                    } else {
                        $('#serviceRequestDetailsContent').html(`<div class="alert alert-danger">{{ T::translate('Error loading details:', 'Error sa pag-load ng mga detalye:')}} ${response.message}</div>`);
                    }
                },
                error: function() {
                    $('#serviceRequestDetailsContent').html('<div class="alert alert-danger">{{ T::translate('Failed to load service request details. Please try again.', 'Nabigong i-load ang mga detalye ng pakiusap na serbisyo. Mangyaring subukan muli.')}}</div>');
                }
            });
        }

        // Similar functions for service requests...

        // ===== COMMON FUNCTIONS =====
        function loadCareWorkers() {
            $.ajax({
                url: "/care-manager/emergency-request/care-workers",
                method: 'GET',
                success: function(response) {
                    console.log("{{ T::translate('Care workers loaded:', 'Mga tagapag-alaga na-load:')}}", response); // Debug line
                    if (response.success && response.care_workers) {
                        let options = '<option value="">-- {{ T::translate('Select Care Worker', 'Pumili ng Tagapag-alaga')}} --</option>';
                        response.care_workers.forEach(worker => {
                            options += `<option value="${worker.id}">${worker.first_name} ${worker.last_name}</option>`;
                        });
                        $('#serviceCareWorkerId').html(options);
                    } else {
                        console.error("{{ T::translate('Failed to load care workers:', 'Nabigong i-load ang mga tagapag-alaga:')}}", response.message);
                        $('#serviceCareWorkerId').html('<option value="">{{ T::translate('No care workers available', 'Walang mga tagapag-alaga ang available')}}</option>');
                    }
                },
                error: function(xhr, status, error) {
                    console.error("{{ T::translate('Error loading care workers:', 'Error sa pag-load ng mga tagapag-alaga:')}}", error);
                    $('#serviceCareWorkerId').html('<option value="">{{ T::translate('Error loading care workers', 'Error sa pag-loading ng mga tagapag-alaga')}}</option>');
                }
            });
        }

        // Archive record function
        function openArchiveModal(recordId, recordType) {
            $('#archiveRecordId').val(recordId);
            $('#archiveRecordType').val(recordType);
            $('#archiveNote').val('');
            $('#archivePassword').val('');
            $('#archiveRecordModal').modal('show');
        }

        // Submit archive request
        $('#submitArchiveRecord').on('click', function() {
            const form = $('#archiveRecordForm');
            const formData = new FormData(form[0]);
            
            // Validate password
            if (!$('#archivePassword').val()) {
                $('#archivePassword').addClass('is-invalid');
                return;
            }
            
            // Show loading state
            $(this).prop('disabled', true).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ T::translate('Archiving...', 'Nag-a-archive...')}}');
            
            // Submit form
            $.ajax({
                url: "/care-manager/emergency-request/archive",
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    if (response.success) {
                        // Show success message
                        toastr.success('{{ T::translate('Record archived successfully', 'Ang tala ay matagumpay na na-archive')}}');
                        
                        // Close modal
                        $('#archiveRecordModal').modal('hide');
                        
                        // Reload page after a brief delay
                        setTimeout(() => {
                            location.reload();
                        }, 1500);
                    } else {
                        // Show error message
                        toastr.error(response.message || '{{ T::translate('Failed to archive record', 'Nabigong i-archive ang tala')}}');
                        
                        // If password error
                        if (response.errors && response.errors.password) {
                            $('#archivePassword').addClass('is-invalid');
                        }
                    }
                },
                error: function(xhr) {
                    // Parse error response
                    let errorMessage = '{{ T::translate('Failed to archive record', 'Nabigong i-archive ang tala')}}';
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.errors && response.errors.password) {
                            $('#archivePassword').addClass('is-invalid');
                            errorMessage = response.errors.password[0];
                        } else if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {}
                    
                    toastr.error(errorMessage);
                },
                complete: function() {
                    // Reset button state
                    $('#submitArchiveRecord').prop('disabled', false).html('{{ T::translate('Archive', 'I-archive')}}');
                }
            });
        });

        // Initialize tooltips
        $(function () {
        $('[data-bs-toggle="tooltip"]').tooltip();
        });

        // Connect modal opening buttons
        $('#respondToEmergencyBtn').on('click', function() {
            $('#emergencyDetailsModal').modal('hide');
            openRespondEmergencyModal();
        });

        $('#handleServiceRequestBtn').on('click', function() {
            $('#serviceRequestDetailsModal').modal('hide');
            openHandleServiceRequestModal();
        });

        // Service Request modal handler
        function openHandleServiceRequestModal(requestId = null) {
            // If requestId is provided, fetch fresh data, otherwise use current service request
            if (requestId) {
                $.ajax({
                    url: "/care-manager/emergency-request/service-request/" + requestId,
                    method: 'GET',
                    success: function(response) {
                        if (response.success) {
                            currentServiceRequest = response.service_request;
                            populateServiceRequestModal(currentServiceRequest);
                            $('#handleServiceRequestModal').modal('show');
                        }
                    },
                    error: function() {
                        alert('{{ T::translate('Failed to load service request details. Please try again.', 'Nabigong i-load ang mga detalye ng pakiusap na serbisyo. Mangyaring subukan muli.')}}');
                    }
                });
            } else if (currentServiceRequest) {
                populateServiceRequestModal(currentServiceRequest);
                $('#handleServiceRequestModal').modal('show');
            }
        }

        function populateServiceRequestModal(request) {
            // Set basic details
            $('#serviceRequestId').val(request.service_request_id);
            $('#requestBeneficiaryName').text(`${request.beneficiary.first_name} ${request.beneficiary.last_name}`);
            $('#requestTypeBadge').text(request.service_type.name);
            
            // Format date
            const createdDate = new Date(request.created_at);
            $('#requestDateTime').text(`{{ T::translate('Requested on:', 'Hiniling noong:')}} ${createdDate.toLocaleDateString()} {{ T::translate('at', 'sa')}} ${createdDate.toLocaleTimeString()}`);
            
            // Service date
            const serviceDate = new Date(request.service_date);
            $('#requestedServiceDate').text(serviceDate.toLocaleDateString());
            
            // Message
            $('#requestMessage').text(request.message);
            
            // Load previous updates if any
            if (request.updates && request.updates.length > 0) {
                $('.service-previous-updates').removeClass('d-none');
                let updatesHtml = '';
                
                request.updates.forEach(update => {
                    const updateDate = new Date(update.created_at);
                    const formattedDate = `${updateDate.toLocaleDateString()} ${updateDate.toLocaleTimeString()}`;
                    
                    updatesHtml += `
                        <div class="update-item mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="badge ${getUpdateTypeBadgeClass(update.update_type)}">${formatUpdateType(update.update_type)}</span>
                                <small class="text-muted">${formattedDate}</small>
                            </div>
                            <div class="update-message mt-1 border-bottom pb-2">${update.message}</div>
                        </div>
                    `;
                });
                
                $('#serviceUpdateHistoryContainer').html(updatesHtml);
            } else {
                $('.service-previous-updates').addClass('d-none');
            }
            
            // Reset form fields
            $('#serviceUpdateType').val('approval');
            $('#serviceResponseMessage').val('');
            $('#serviceStatusChangeTo').val('approved');
            $('#serviceConfirmPassword').val('');
            
            // Handle form fields visibility based on selected update type
            handleServiceUpdateTypeChange();
            
            // Load care workers for assignment option
            loadCareWorkers();
        }

        // Add function for handling service request form
        function handleServiceUpdateTypeChange() {
            const updateType = $('#serviceUpdateType').val();
            
            // Hide all conditional sections and warnings first
            $('.service-password-confirmation, .care-worker-options').addClass('d-none');
            $('#completionWarning, #rejectionWarning, #statusValidationWarning, #approvalWarning').addClass('d-none');
            
            // Default state - enable button for most types
            $('#submitServiceResponse').prop('disabled', false);
            
            // Show/hide care worker dropdown
            if (updateType === 'approval' || updateType === 'assignment') {
                $('.care-worker-options').removeClass('d-none');
                loadCareWorkers(); // Ensure care workers are loaded
                
                // Check if request is already approved
                if (updateType === 'approval' && currentServiceRequest && currentServiceRequest.status === 'approved') {
                    $('#approvalWarning').removeClass('d-none').text('{{ T::translate('This service request is already approved. Please choose a different action.', 'Ang pakiusap na serbisyo ay naaprubahan na. Mangyaring pumili ng ibang aksyon.')}}');
                    $('#submitServiceResponse').prop('disabled', true);
                }
            }
            
            // Show warning for completion
            else if (updateType === 'completion') {
                $('.service-password-confirmation').removeClass('d-none');
                $('#completionWarning').removeClass('d-none');
                
                // Check if the service request is in an approvable state
                if (currentServiceRequest && currentServiceRequest.status !== 'approved') {
                    $('#statusValidationWarning').removeClass('d-none').text('{{ T::translate('Only approved service requests can be marked as completed.', 'Ang mga naaprubahang pakiusap na serbisyo lamang ang maaaring markahan bilang natapos.')}}');
                    $('#submitServiceResponse').prop('disabled', true);
                }
            }
            
            // Show warning for rejection
            else if (updateType === 'rejection') {
                $('#rejectionWarning').removeClass('d-none');
            }
            
            // Note option - no additional UI changes needed, button remains enabled
        }

        // Tab switcher for main tabs and pending column
        $('button[data-bs-toggle="tab"]').on('shown.bs.tab', function (e) {
            const targetId = $(e.target).attr('data-bs-target');
            
            // Update pending column content based on active tab
            if (targetId === '#emergency') {
                $('#emergency-pending-content').show();
                $('#service-pending-content').hide();
            } else if (targetId === '#service') {
                $('#emergency-pending-content').hide();
                $('#service-pending-content').show();
            }
        });

        // Event handler for service update type change
        $('#serviceUpdateType').on('change', handleServiceUpdateTypeChange);

        // Render emergency details in view modal
        function renderEmergencyDetails(emergency) {
            let content = `
                <div class="mb-4">
                    <h5 class="border-bottom pb-2">{{ T::translate('Emergency Information', 'Impormasyon ng Emergency')}}</h5>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate('Beneficiary:', 'Benepisyaryo:')}}</div>
                        <div class="col-md-8">${emergency.beneficiary.first_name} ${emergency.beneficiary.last_name}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate('Address:', 'Tirahan:')}}</div>
                        <div class="col-md-8">${emergency.beneficiary.street_address} (${emergency.beneficiary.barangay.barangay_name}, ${emergency.beneficiary.municipality.municipality_name})</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Contact Number:</div>
                        <div class="col-md-8">${emergency.beneficiary.mobile || 'Not provided'}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Emergency Contact:</div>
                        <div class="col-md-8">${emergency.beneficiary.emergency_contact_name || 'Not provided'} ${emergency.beneficiary.emergency_contact_name ? `(${emergency.beneficiary.emergency_contact_relation}) - ${emergency.beneficiary.emergency_contact_mobile}` : ''}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate('Type:', 'Uri:')}}</div>
                        <div class="col-md-8"><span class="badge me-2" style="background-color: ${emergency.emergency_type.color_code}">${emergency.emergency_type.name}</span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Status:</div>
                        <div class="col-md-8">${formatStatus(emergency.status)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate('Created:', 'Nalikha:')}}</div>
                        <div class="col-md-8">${formatDateTime(emergency.created_at)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate('Message:', 'Mensahe:')}}</div>
                        <div class="col-md-8">${emergency.message}</div>
                    </div>
                </div>
            `;
            
            $('#emergencyDetailsContent').html(content);
            
            // Load updates timeline if any
            if (emergency.updates && emergency.updates.length > 0) {
                let updatesHtml = '';
                
                emergency.updates.forEach(update => {
                    updatesHtml += `
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-indicator">
                                    <div class="timeline-badge ${getUpdateTypeBadgeClass(update.update_type)}"></div>
                                </div>
                                <div class="timeline-content ms-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="fw-bold">${formatUpdateType(update.update_type)}</span>
                                        <small class="text-muted">${formatDateTime(update.created_at)}</small>
                                    </div>
                                    <p class="mb-1">${update.message}</p>
                                    <small class="text-muted">By: ${update.updated_by_name || 'System'}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                $('#emergencyUpdatesTimeline').html(updatesHtml);
            } else {
                $('#emergencyUpdatesTimeline').html('<p class="text-muted">{{ T::translate('No updates yet', 'Wala pang mga update')}}</p>');
            }
        }

        // Render service request details in view modal
        function renderServiceRequestDetails(request) {
            let content = `
                <div class="mb-4">
                    <h5 class="border-bottom pb-2">{{ T::translate('Service Request Information', 'Impormasyon ng Pakiusap na Serbisyo')}}</h5>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate('Beneficiary:', 'Benepisyaryo:')}}</div>
                        <div class="col-md-8">${request.beneficiary.first_name} ${request.beneficiary.last_name}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate('Service Type:', 'Uri ng Serbisyo:')}}</div>
                        <div class="col-md-8"><span class="badge" style="background-color: ${request.service_type.color_code}">${request.service_type.name}</span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Status:</div>
                        <div class="col-md-8">${formatStatus(request.status)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate('Requested Date:', 'Petsa ng Pakiusap:')}}</div>
                        <div class="col-md-8">${formatDate(request.service_date)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate('Requested Time:', 'Oras ng Pakiusap:')}}</div>
                        <div class="col-md-8">${request.service_time ? formatTime(request.service_time) : 'Not Specified'}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate('Created:', 'Nalikha:')}}</div>
                        <div class="col-md-8">${formatDateTime(request.created_at)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate('Message:', 'Mensahe:')}}</div>
                        <div class="col-md-8">${request.message}</div>
                    </div>
                </div>
            `;
            
            $('#serviceRequestDetailsContent').html(content);
            
            // Load updates timeline if any
            if (request.updates && request.updates.length > 0) {
                let updatesHtml = '';
                
                request.updates.forEach(update => {
                    updatesHtml += `
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-indicator">
                                    <div class="timeline-badge ${getUpdateTypeBadgeClass(update.update_type)}"></div>
                                </div>
                                <div class="timeline-content ms-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="fw-bold">${formatUpdateType(update.update_type)}</span>
                                        <small class="text-muted">${formatDateTime(update.created_at)}</small>
                                    </div>
                                    <p class="mb-1">${update.message}</p>
                                    <small class="text-muted">By: ${update.updated_by_name || 'System'}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                $('#serviceUpdatesTimeline').html(updatesHtml);
            } else {
                $('#serviceUpdatesTimeline').html('<p class="text-muted">{{ T::translate('No updates yet', 'Wala pang mga update')}}</p>');
            }
        }

        // Helper functions for formatting
        function formatDateTime(dateTimeStr) {
            const date = new Date(dateTimeStr);
            return date.toLocaleString();
        }

        function formatDate(dateStr) {
            const date = new Date(dateStr);
            return date.toLocaleDateString();
        }

        function formatTime(timeStr) {
            // Handle cases where timeStr might be just the time portion
            if (timeStr.length <= 8) {
                // Create a dummy date with the time value
                const dummyDate = new Date(`2000-01-01T${timeStr}`);
                return dummyDate.toLocaleTimeString([], {hour: 'numeric', minute: '2-digit'});
            }
            
            // Handle full datetime strings
            const date = new Date(timeStr);
            return date.toLocaleTimeString([], {hour: 'numeric', minute: '2-digit'});
        }

        function formatStatus(status) {
            switch(status) {
                case 'new': return '<span class="badge bg-danger">{{ T::translate('New' ,'Bago')}}</span>';
                case 'in_progress': return '<span class="badge bg-info">{{ T::translate('In Progress' ,'Isinasagawa')}}</span>';
                case 'approved': return '<span class="badge bg-success">{{ T::translate('Approved' ,'Na-aprubahan')}}</span>';
                case 'rejected': return '<span class="badge bg-danger">{{ T::translate('Rejected' ,'Tinaggihan')}}</span>';
                case 'completed': return '<span class="badge bg-primary">{{ T::translate('Completed' ,'Natapos')}}</span>';
                case 'resolved': return '<span class="badge bg-success">{{ T::translate('Resolved' ,'Nalutas')}}</span>';
                case 'archived': return '<span class="badge bg-secondary">Archived</span>';
                default: return `<span class="badge bg-secondary">${status}</span>`;
            }
        }

        function openResolveEmergencyModal(noticeId) {
            // Fetch emergency details and then open modal with resolution pre-selected
            $.ajax({
                url: "/care-manager/emergency-request/emergency/" + noticeId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        currentEmergency = response.emergency_notice;
                        populateEmergencyResponseModal(currentEmergency);
                        
                        // Pre-select resolution
                        $('#updateType').val('resolution').trigger('change');
                        $('#responseMessage').val('{{ T::translate('Emergency has been resolved' ,'Ang emergency ay nalutas na.')}}.');
                        
                        // Show modal
                        $('#respondEmergencyModal').modal('show');
                    }
                },
                error: function() {
                    toastr.error('{{ T::translate('Failed to load emergency details. Please try again.' ,'Nabigong i-load ang mga detalye ng emergency. Pakisubukan muli.')}}');
                }
            });
        }

        function openCompleteServiceRequestModal(requestId) {
            $.ajax({
                url: "/care-manager/emergency-request/service-request/" + requestId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        currentServiceRequest = response.service_request;
                        
                        // Validate if the request can be completed
                        if (currentServiceRequest.status !== 'approved') {
                            toastr.error('{{ T::translate('Only approved service requests can be marked as completed' ,'Tanging ang mga na-aprubang pakiusap na serbisyo lamang ang maaring markahan ng natapos.')}}.');
                            return;
                        }
                        
                        populateServiceRequestModal(currentServiceRequest);
                        
                        // Pre-select completion
                        $('#serviceUpdateType').val('completion').trigger('change');
                        $('#serviceResponseMessage').val('{{ T::translate('Service request has been completed successfully' ,'Ang pakiusap na serbisyo ay matagumpay na natapos.')}}.');
                        
                        // Show modal
                        $('#handleServiceRequestModal').modal('show');
                    }
                },
                error: function() {
                    toastr.error('{{ T::translate('Failed to load service request details. Please try again.' ,'Nabigong i-load ang mga detalye ng pakiusap na serbisyo. Pakisubukan muli.')}}');
                }
            });
        }

        function showSuccessAlert(message) {
            $('#successAlertMessage').text(message);
            $('#successAlert').removeClass('d-none');
            
            // Save to session storage so it persists after page reload
            // Check if this is an emergency message or service message
            if (message.includes('Emergency')) {
                sessionStorage.setItem('emergencySuccessMessage', message);
            } else if (message.includes('Service')) {
                sessionStorage.setItem('serviceSuccessMessage', message);
            } else {
                sessionStorage.setItem('generalSuccessMessage', message);
            }
        }
    </script>
    <script>
        // Variable to track if we're currently interacting with a modal
        let isModalOpen = false;
        
        // Function to check if any Bootstrap modal is currently open
        function checkIfModalOpen() {
            return $('.modal.show').length > 0;
        }
        
        // Set listeners for modal open/close events
        $(document).on('shown.bs.modal', function() {
            isModalOpen = true;
        });
        
        $(document).on('hidden.bs.modal', function() {
            isModalOpen = checkIfModalOpen();
        });
        
        // Function to refresh content 
        function refreshMainContent() {
            // Don't refresh if a modal is open to avoid interrupting user interactions
            if (isModalOpen) {
                return;
            }

            // Remember which tab was active
            let activeTabId = $('#requestTypeTab .nav-link.active').attr('id');
            
            // Add a subtle loading indicator
            $('body').addClass('content-refreshing');
            
            $.get("{{ route('care-manager.emergency.request.partial-content') }}", function(html) {
                $('#main-content-refreshable').html(html);
                
                // After re-activating the tab
                if (activeTabId) {
                    $('#' + activeTabId).tab('show');
                    // Manually trigger the tab shown event handler logic
                    const targetId = $('#' + activeTabId).attr('data-bs-target');
                    if (targetId === '#service') {
                        $('#emergency-pending-content').hide();
                        $('#service-pending-content').show();
                    } else {
                        $('#emergency-pending-content').show();
                        $('#service-pending-content').hide();
                    }
                }
                
                // Re-initialize tooltips
                $('[data-bs-toggle="tooltip"]').tooltip();
                
                // Log refresh for debugging (can be removed in production)
                console.log('Content refreshed at: ' + new Date().toLocaleTimeString());
            }).always(function() {
                // Remove loading state
                $('body').removeClass('content-refreshing');
            });
        }
        
        // Start refreshing content every 10 seconds
        let refreshInterval = setInterval(refreshMainContent, 10000);
        
        // Pause auto-refresh when a modal is open or user is interacting
        $(document).on('show.bs.modal', function() {
            clearInterval(refreshInterval);
        });
        
        // Resume auto-refresh when all modals are closed
        $(document).on('hidden.bs.modal', function() {
            if (!checkIfModalOpen()) {
                refreshInterval = setInterval(refreshMainContent, 10000);
            }
        });
        
        // Also pause refresh during form interactions
        $(document).on('focus', 'input, textarea, select', function() {
            clearInterval(refreshInterval);
        });
        
        $(document).on('blur', 'input, textarea, select', function() {
            if (!checkIfModalOpen()) {
                refreshInterval = setInterval(refreshMainContent, 10000);
            }
        });
    </script>
</body>
</html>