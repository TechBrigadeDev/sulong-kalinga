@php
    use App\Helpers\TranslationHelper as T;
@endphp

<div class="row" id="home-content">
    <!-- Main Content Column (Emergency/Service Tabs) -->
    <div class="col-lg-8 col-12 main-content-column">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-0">
                    <ul class="nav nav-tabs px-3" id="requestTypeTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="emergency-tab" data-bs-toggle="tab" data-bs-target="#emergency" type="button" role="tab">
                                <i class="bi bi-exclamation-triangle-fill text-danger me-2"></i> {{ T::translate('Emergency', 'Mga Emergency') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="service-tab" data-bs-toggle="tab" data-bs-target="#service" type="button" role="tab">
                                <i class="bi bi-hand-thumbs-up-fill text-primary me-2"></i> {{ T::translate('Service Request', 'Pakiusap na Serbisyo') }}
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
                                            <span class="badge bg-danger bg-opacity-10 text-danger">{{ T::translate('New', 'Bago') }}</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">{{ T::translate('Address:', 'Tirahan:') }}</span>
                                            <span>{{ $notice->beneficiary->street_address }} ({{ $notice->beneficiary->barangay->barangay_name }}, {{ $notice->beneficiary->municipality->municipality_name }})</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">{{ T::translate('Contact:', 'Kontak:') }}</span>
                                            <span >{{ $notice->beneficiary->mobile }}</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">{{ T::translate('Emergency Contact:', 'Emergency Contact:') }}</span>
                                            <span class="ms-2">{{ $notice->beneficiary->emergency_contact_name }} ({{ $notice->beneficiary->emergency_contact_relation }}) {{ $notice->beneficiary->emergency_contact_mobile }}</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">{{ T::translate('Emergency Type:', 'Uri ng Emergency:') }}</span>
                                            <span class="ms-2"><span class="badge" style="background-color: {{ $notice->emergencyType->color_code }}">{{ $notice->emergencyType->name }}</span></span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-3">
                                            <span class="info-label">{{ T::translate('Message:', 'Mensahe:') }}</span>
                                            <span>{{ Str::limit($notice->message, 100) }}</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($notice->created_at)->diffForHumans() }}</small>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="viewEmergencyDetails({{ $notice->notice_id }})">
                                                    <i class="bi bi-eye me-1"></i> {{ T::translate('View', 'Tingnan') }}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="openSendReminderModal({{ $notice->notice_id }}, 'emergency')">
                                                    <i class="bi bi-bell me-1"></i> {{ T::translate('Follow Up', 'Follow Up') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <i class="bi bi-inbox-fill" style="font-size: 3rem;"></i>
                                    <h5 class="mt-3">{{ T::translate('No New Emergency Notices', 'Walang mga bagong emergency notice') }}</h5>
                                    <p>{{ T::translate('There are no new emergency notices at the moment.', 'Walang bagong emergency notice sa ngayon.') }}</p>
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
                                            <span class="badge bg-primary bg-opacity-10 text-primary">{{ T::translate('New', 'Bago') }}</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">{{ T::translate('Service Type:', 'Uri ng Serbisyo:') }}</span>
                                            <span><span class="badge" style="background-color: {{ $request->serviceType->color_code }}">{{ $request->serviceType->name }}</span></span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">{{ T::translate('Requested Date:', 'Petsa ng Pakiusap:') }}</span>
                                            <span>{{ \Carbon\Carbon::parse($request->service_date)->format('M d, Y') }}</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">{{ T::translate('Requested Time:', 'Oras ng Pakiusap:') }}</span>
                                            <span>{{ $request->service_time ? \Carbon\Carbon::parse($request->service_time)->format('h:i A') : T::translate('Flexible', 'Flexible') }}</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-3">
                                            <span class="info-label">{{ T::translate('Message:', 'Mensahe:') }}</span>
                                            <span>{{ Str::limit($request->message, 100) }}</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">{{ \Carbon\Carbon::parse($request->created_at)->diffForHumans() }}</small>
                                            <div class="btn-group">
                                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="viewServiceRequestDetails({{ $request->service_request_id }})">
                                                    <i class="bi bi-eye me-1"></i> {{ T::translate('View', 'Tingnan') }}
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-primary" onclick="openSendReminderModal({{ $request->service_request_id }}, 'service')">
                                                    <i class="bi bi-bell me-1"></i> {{ T::translate('Follow Up', 'Follow Up') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <i class="bi bi-inbox-fill" style="font-size: 3rem;"></i>
                                    <h5 class="mt-3">{{ T::translate('No New Service Requests', 'Walang mga bagong Pakiusap na Serbisyo') }}</h5>
                                    <p>{{ T::translate('There are no new service requests at the moment.', 'Walang mga bagong pakiusap na serbisyo sa ngayon.') }}</p>
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
                        <i class="bi bi-hourglass-split me-2"></i>{{ T::translate('Pending/In Progress', 'Nakabinbin/Isinasagawa') }}
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
                                            <span class="badge bg-info bg-opacity-10 text-info">{{ T::translate('In Progress', 'Isinasagawa') }}</span>
                                        </div>
                                        
                                        @if($notice->action_taken_by)
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">{{ T::translate('Responded:', 'Tinugunan:') }}</span>
                                            <span>{{ $notice->actionTakenBy ? $notice->actionTakenBy->first_name . ' ' . $notice->actionTakenBy->last_name : T::translate('Unknown', 'Hindi kilala') }}</span>
                                        </div>
                                        @endif
                                        
                                        <div class="d-flex flex-wrap mb-3">
                                            <span class="info-label">{{ T::translate('Type:', 'Uri:') }}</span>
                                            <span><span class="badge" style="background-color: {{ $notice->emergencyType->color_code }}">{{ $notice->emergencyType->name }}</span></span>
                                        </div>
                                    </div>
                                    
                                    <div class="card-footer-actions">
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i> {{ T::translate('Started', 'Sinimulan') }} {{ \Carbon\Carbon::parse($notice->action_taken_at)->diffForHumans() }}
                                        </small>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); openSendReminderModal({{ $notice->notice_id }}, 'emergency')">
                                            <i class="bi bi-bell me-1"></i> {{ T::translate('Follow Up', 'Follow Up') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="bi bi-hourglass text-muted" style="font-size: 2rem;"></i>
                                <h6 class="mt-3">{{ T::translate('No Active Emergencies', 'Walang Aktibong Emergencies') }}</h6>
                                <p class="small">{{ T::translate('No in-progress emergency notices at the moment.', 'Walang isinasagawa na emergency notice sa ngayon.') }}</p>
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
                                            <span class="badge bg-success bg-opacity-10 text-success">{{ T::translate('Approved', 'Naaprubahan') }}</span>
                                        </div>

                                        <div class="d-flex flex-wrap mb-2">
                                            <h6 class="fw mb-0 text-primary">{{ $request->serviceType->name }}</h6>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">{{ T::translate('Approved:', 'Naaprubahan:') }}</span>
                                            <span>{{ $request->actionTakenBy ? $request->actionTakenBy->first_name . ' ' . $request->actionTakenBy->last_name : T::translate('Unknown', 'Hindi kilala') }}</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-3">
                                            <span class="info-label">{{ T::translate('Assigned To:', 'Itinalaga kay:') }}</span>
                                            <span>{{ $request->careWorker ? $request->careWorker->first_name . ' ' . $request->careWorker->last_name : T::translate('Not assigned', 'Hindi itinalaga') }}</span>
                                        </div>
                                    </div>
                                    
                                    <div class="card-footer-actions">
                                        <small class="text-muted">
                                            <i class="bi bi-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($request->service_date)->format('M d') }}
                                        </small>
                                        <button type="button" class="btn btn-sm btn-outline-primary" onclick="event.stopPropagation(); openSendReminderModal({{ $request->service_request_id }}, 'service')">
                                            <i class="bi bi-bell me-1"></i> {{ T::translate('Follow Up', 'Follow Up') }}
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="empty-state">
                                <i class="bi bi-hourglass text-muted" style="font-size: 2rem;"></i>
                                <h6 class="mt-3">{{ T::translate('No Active Service Requests', 'Walang mga Aktibong Pakiusap na Serbisyo') }}</h6>
                                <p class="small">{{ T::translate('No approved service requests at the moment.', 'Walang naaprubahan na mga pakiusap na serbisyo sa ngayon.') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>