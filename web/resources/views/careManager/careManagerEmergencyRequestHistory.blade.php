<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Emergency and Service History | Manager</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/emergencyAndServiceHistory.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        /* [Previous CSS content remains exactly the same] */
    </style>
</head>
<body>

    @php
    use App\Helpers\TranslationHelper as T;
    @endphp
    @include('components.careManagerNavbar')
    @include('components.careManagerSidebar')

    <div class="home-section">
        <div class="page-header m-2">
            <div class="text-left">{{ T::translate('EMERGENCY AND SERVICE REQUEST HISTORY', 'KASAYSAYAN NG MGA EMERGENCY AT PAKIUSAP NA SERBISYO') }}</div>
            <button class="history-btn active" onclick="window.location.href='{{ route('care-manager.emergency.request.index') }}'">
                <i class="bi bi-arrow-left me-1"></i> {{ T::translate('Back to Current', 'Bumalik sa Kasalukuyan') }}
            </button>
        </div>
        
        <div class="container-fluid">
            <div class="row" id="home-content">
                <div class="col-12">
                    <!-- Time Range Filter -->
                    <div class="time-range-container">
                        <div class="filter-row">
                            <div class="filter-main-group">
                                <div class="filter-group">
                                    <label for="timeRange">{{ T::translate('Time Range:', 'Hanay ng Oras:') }}</label>
                                    <select class="form-select form-select-sm time-range-selector" id="timeRange" name="time_range">
                                        <option value="30days" selected>{{ T::translate('Last 30 Days', 'Huling 30 Araw') }}</option>
                                        <option value="7days">{{ T::translate('Last 7 Days', 'Huling 7 Araw') }}</option>
                                        <option value="90days">{{ T::translate('Last 90 Days', 'Huling 90 Araw') }}</option>
                                        <option value="custom">{{ T::translate('Custom Range', 'Pasadyang Hanay') }}</option>
                                    </select>
                                </div>
                                
                                <div id="customRange" class="filter-group">
                                    <label for="startDate">{{ T::translate('From:', 'Mula:') }}</label>
                                    <input type="date" class="form-control form-control-sm" id="startDate" name="start_date">
                                    
                                    <label for="endDate">{{ T::translate('To:', 'Hanggang:') }}</label>
                                    <input type="date" class="form-control form-control-sm" id="endDate" name="end_date">
                                </div>
                            </div>
                            
                            <div class="filter-actions">
                                <button class="btn btn-sm btn-primary" id="applyFilter">
                                    <i class="bi bi-funnel-fill me-1"></i>{{ T::translate('Apply Filter', 'Ilapat ang Pagsala') }}
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" id="resetFilter">
                                    <i class="bi bi-arrow-counterclockwise me-1"></i>{{ T::translate('Reset', 'I-reset') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <ul class="nav nav-tabs justify-content-center" id="historyTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="emergency-history-tab" data-bs-toggle="tab" data-bs-target="#emergency-history" type="button" role="tab">
                                <i class="bi bi-exclamation-triangle me-1"></i>{{ T::translate('Emergency History', 'Kasaysayan ng Emergency') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="request-history-tab" data-bs-toggle="tab" data-bs-target="#request-history" type="button" role="tab">
                                <i class="bi bi-hand-thumbs-up me-1"></i>{{ T::translate('Service Request History', 'Kasaysayan ng Pakiusap ng Serbisyo') }}
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="statistics-tab" data-bs-toggle="tab" data-bs-target="#statistics" type="button" role="tab">
                                <i class="bi bi-pie-chart me-1"></i>{{ T::translate('Statistics', 'Istatistika') }}
                            </button>
                        </li>
                    </ul>
                    
                    <div class="tab-content" id="historyTabContent">
                        <!-- Emergency History Tab -->
                        <div class="tab-pane fade show active" id="emergency-history" role="tabpanel">
                            @forelse($resolvedEmergencies ?? [] as $emergency)
                                <div class="card emergency-card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title">{{ T::translate('Resolved:', 'Nalutas:') }} {{ $emergency->emergencyType->name }}</h5>
                                            <span class="badge bg-success bg-opacity-10 text-success">{{ T::translate('Resolved', 'Nalutas') }}</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">{{ T::translate('Beneficiary:', 'Benepisyaryo:') }}</span>
                                            <span class="ms-2">{{ $emergency->beneficiary->first_name }} {{ $emergency->beneficiary->last_name }}</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">{{ T::translate('Address:', 'Tirahan:') }}</span>
                                            <span class="ms-2">{{ $emergency->beneficiary->street_address }} ({{ $emergency->beneficiary->barangay->barangay_name }}, {{ $emergency->beneficiary->municipality->municipality_name }})</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">{{ T::translate('Contact:', 'Kontak:') }}</span>
                                            <span class="ms-2">{{ $emergency->beneficiary->mobile }}</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">{{ T::translate('Message:', 'Mensahe:') }}</span>
                                            <span class="ms-2">{{ \Illuminate\Support\Str::limit($emergency->message, 100) }}</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i> {{ T::translate('Resolved', 'Nalutas') }} {{ \Carbon\Carbon::parse($emergency->updated_at)->diffForHumans() }} 
                                                @if($emergency->actionTakenBy)
                                                    {{ T::translate('by', 'ni') }} {{ $emergency->actionTakenBy->first_name }} {{ $emergency->actionTakenBy->last_name }}
                                                @endif
                                            </small>
                                            <button class="btn btn-sm btn-outline-secondary" onclick="viewEmergencyHistory({{ $emergency->notice_id }})">
                                                <i class="bi bi-eye me-1"></i> {{ T::translate('View Details', 'Tingnan ang Detalye') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <i class="bi bi-archive text-muted" style="font-size: 2rem;"></i>
                                    <h6 class="mt-3">{{ T::translate('No Resolved Emergencies', 'Walang Nalutas na mga Emergency') }}</h6>
                                    <p class="small">{{ T::translate('No emergency notices have been resolved in the selected time period.', 'Walang mga abiso ng emergency ang nalutas sa napiling panahon.') }}</p>
                                </div>
                            @endforelse
                        </div>
                        
                        <!-- Service Request History Tab -->
                        <div class="tab-pane fade" id="request-history" role="tabpanel">
                            @forelse($completedServiceRequests ?? [] as $request)
                                <div class="card request-card mb-3">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h5 class="card-title">{{ ucfirst($request->status) }}: {{ $request->serviceType->name }}</h5>
                                            <span class="badge {{ $request->status == 'completed' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger' }}">
                                                {{ ucfirst($request->status) }}
                                            </span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">{{ T::translate('Beneficiary:', 'Benepisyaryo:') }}</span>
                                            <span class="ms-2">{{ $request->beneficiary->first_name }} {{ $request->beneficiary->last_name }}</span>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">{{ T::translate('Address:', 'Tirahan:') }}</span>
                                            <span class="ms-2">{{ $request->beneficiary->street_address }} ({{ $request->beneficiary->barangay->barangay_name }}, {{ $request->beneficiary->municipality->municipality_name }})</span>
                                        </div>
                                        
                                        @if($request->status == 'completed' && $request->careWorker)
                                            <div class="d-flex flex-wrap mb-1">
                                                <span class="info-label">{{ T::translate('Care Worker:', 'Tagapag-alaga:') }}</span>
                                                <span class="ms-2">{{ $request->careWorker->first_name }} {{ $request->careWorker->last_name }}</span>
                                            </div>
                                        @endif
                                        
                                        <div class="d-flex flex-wrap mb-1">
                                            <span class="info-label">{{ T::translate('Service Date:', 'Petsa ng Serbisyo:') }}</span>
                                            <span class="ms-2">{{ \Carbon\Carbon::parse($request->service_date)->format('M d, Y') }}</span>
                                        </div>
                                        
                                        <div class="d-flex justify-content-between align-items-center mt-3">
                                            <small class="text-muted">
                                                <i class="bi bi-clock me-1"></i> {{ ucfirst($request->status) }} {{ \Carbon\Carbon::parse($request->updated_at)->diffForHumans() }} 
                                                @if($request->actionTakenBy)
                                                    {{ T::translate('by', 'ni') }} {{ $request->actionTakenBy->first_name }} {{ $request->actionTakenBy->last_name }}
                                                @endif
                                            </small>
                                            <button class="btn btn-sm btn-outline-secondary" onclick="viewServiceRequestHistory({{ $request->service_request_id }})">
                                                <i class="bi bi-eye me-1"></i> {{ T::translate('View Details', 'Tingnan ang Detalye') }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="empty-state">
                                    <i class="bi bi-archive text-muted" style="font-size: 2rem;"></i>
                                    <h6 class="mt-3">{{ T::translate('No Completed/Rejected Service Requests', 'Walang Natapos/Tinanggihang mga Pakiusap ng Serbisyo') }}</h6>
                                    <p class="small">{{ T::translate('No service requests have been completed or rejected in the selected time period.', 'Walang mga pakiusap ng serbisyo ang natapos o tinanggihan sa napiling panahon.') }}</p>
                                </div>
                            @endforelse
                        </div>
                        
                        <!-- Statistics Tab -->
                        <div class="tab-pane fade" id="statistics" role="tabpanel">
                            <div class="row">
                                <!-- Emergency Statistics -->
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="card-title mb-0">
                                                    <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                                                    {{ T::translate('Emergency Statistics', 'Istatistika ng Emergency') }}
                                                </h5>
                                                <small class="text-muted" id="emergencyStatsDateRange">{{ T::translate('Last 30 days', 'Huling 30 Araw') }}</small>
                                            </div>
                                            
                                            <div class="row text-center mb-4">
                                                <div class="col-4">
                                                    <div class="h3 text-danger" id="totalEmergencies">{{ ($resolvedEmergencies ?? collect())->count() + ($emergencyNotices->where('status', 'new')->count() ?? 0) + ($emergencyNotices->where('status', 'in_progress')->count() ?? 0) }}</div>
                                                    <small class="text-muted">{{ T::translate('Total', 'Kabuuan') }}</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="h3" id="resolvedEmergencies">{{ ($resolvedEmergencies ?? collect())->count() }}</div>
                                                    <small class="text-muted">{{ T::translate('Resolved', 'Nalutas') }}</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="h3" id="pendingEmergencies">{{ ($emergencyNotices->where('status', 'new')->count() ?? 0) + ($emergencyNotices->where('status', 'in_progress')->count() ?? 0) }}</div>
                                                    <small class="text-muted">{{ T::translate('Pending', 'Nakabinbin') }}</small>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <h6 class="fw-bold">{{ T::translate('Breakdown by Type:', 'Paghahati ayon sa Uri:') }}</h6>
                                                <div class="list-group list-group-flush" id="emergencyTypeBreakdown">
                                                    @foreach($emergencyTypeStats ?? [] as $stat)
                                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                                            {{ $stat['name'] }}
                                                            <span class="badge bg-danger rounded-pill">{{ $stat['count'] }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                                
                                <!-- Service Request Statistics -->
                                <div class="col-md-6">
                                    <div class="card mb-4">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-center mb-3">
                                                <h5 class="card-title mb-0">
                                                    <i class="bi bi-hand-thumbs-up text-primary me-2"></i>
                                                    {{ T::translate('Service Request Statistics', 'Istatistika ng Pakiusap ng Serbisyo') }}
                                                </h5>
                                                <small class="text-muted" id="serviceStatsDateRange">{{ T::translate('Last 30 days', 'Huling 30 Araw') }}</small>
                                            </div>
                                            
                                            <div class="row text-center mb-4">
                                                <div class="col-4">
                                                    <div class="h3 text-primary" id="totalServiceRequests">{{ ($completedServiceRequests ?? collect())->count() + ($serviceRequests->where('status', 'new')->count() ?? 0) + ($serviceRequests->where('status', 'approved')->count() ?? 0) }}</div>
                                                    <small class="text-muted">{{ T::translate('Total', 'Kabuuan') }}</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="h3" id="completedServiceRequests">{{ ($completedServiceRequests ?? collect())->count() }}</div>
                                                    <small class="text-muted">{{ T::translate('Completed / Rejected', 'Natapos / Tinanggihan') }}</small>
                                                </div>
                                                <div class="col-4">
                                                    <div class="h3" id="pendingServiceRequests">{{ ($serviceRequests->where('status', 'new')->count() ?? 0) + ($serviceRequests->where('status', 'approved')->count() ?? 0) }}</div>
                                                    <small class="text-muted">{{ T::translate('Pending', 'Nakabinbin') }}</small>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <h6 class="fw-bold">{{ T::translate('Breakdown by Type:', 'Paghahati ayon sa Uri:') }}</h6>
                                                <div class="list-group list-group-flush" id="serviceTypeBreakdown">
                                                    @foreach($serviceTypeStats ?? [] as $stat)
                                                        <div class="list-group-item d-flex justify-content-between align-items-center">
                                                            {{ $stat['name'] }}
                                                            <span class="badge bg-primary rounded-pill">{{ $stat['count'] }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Emergency History Details Modal -->
    <div class="modal fade" id="emergencyHistoryDetailModal" tabindex="-1" aria-labelledby="emergencyHistoryDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="emergencyHistoryDetailModalLabel"><i class="bi bi-file-earmark-text"></i> {{ T::translate('Emergency History Details', 'Mga Detalye ng Kasaysayan ng Emergency') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="emergencyHistoryContent">
                        <!-- Emergency history details will be loaded here -->
                    </div>
                    
                    <div class="updates-history mt-4">
                        <h6 class="border-bottom pb-2">{{ T::translate('Updates History', 'Kasaysayan ng mga Update') }}</h6>
                        <div id="emergencyHistoryTimeline">
                            <!-- Updates will be loaded here -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ T::translate('Close', 'Isara') }}</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Service Request History Details Modal -->
    <div class="modal fade" id="serviceHistoryDetailModal" tabindex="-1" aria-labelledby="serviceHistoryDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-secondary text-white">
                    <h5 class="modal-title" id="serviceHistoryDetailModalLabel"><i class="bi bi-file-earmark-text"></i> {{ T::translate('Service Request History Details', 'Mga Detalye ng Kasaysayan ng Pakiusap ng Serbisyo') }}</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="serviceHistoryContent">
                        <!-- Service request history details will be loaded here -->
                    </div>
                    
                    <div class="updates-history mt-4">
                        <h6 class="border-bottom pb-2">{{ T::translate('Updates History', 'Kasaysayan ng mga Update') }}</h6>
                        <div id="serviceHistoryTimeline">
                            <!-- Updates will be loaded here -->
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ T::translate('Close', 'Isara') }}</button>
                </div>
            </div>
        </div>
    </div>
   
    <script src="https://code.jquery.com/jquery-3.6.0.min.js" integrity="sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=" crossorigin="anonymous"></script>
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    
    <script>
        // Time range filter functionality
        document.getElementById('timeRange').addEventListener('change', function() {
            const customRange = document.getElementById('customRange');
            if (this.value === 'custom') {
                customRange.style.display = 'flex';
            } else {
                customRange.style.display = 'none';
            }
        });

        // Apply filter button
        $('#applyFilter').on('click', function() {
            const timeRange = $('#timeRange').val();
            const startDate = $('#startDate').val();
            const endDate = $('#endDate').val();
            
            // Validation for custom date range
            if (timeRange === 'custom') {
                if (!startDate || !endDate) {
                    toastr.error('{{ T::translate("Please select both start and end dates", "Mangyaring piliin ang parehong petsa ng simula at pagtatapos") }}');
                    return;
                }
                
                if (new Date(startDate) > new Date(endDate)) {
                    toastr.error('{{ T::translate("Start date cannot be after end date", "Ang petsa ng simula ay hindi maaaring mas mataas kaysa sa petsa ng pagtatapos") }}');
                    return;
                }
            }
            
            // Show loading state
            $(this).html('<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ T::translate("Loading...", "Naglo-load...") }}');
            $(this).prop('disabled', true);
            
            // Send filter request
            $.ajax({
                url: "{{ route('care-manager.emergency.request.filter.history') }}",
                method: 'POST',
                data: {
                    _token: "{{ csrf_token() }}",
                    time_range: timeRange,
                    start_date: startDate,
                    end_date: endDate
                },
                success: function(response) {
                    if (response.success) {
                        // Update date range labels
                        $('#emergencyStatsDateRange').text(response.dateRangeLabel);
                        $('#serviceStatsDateRange').text(response.dateRangeLabel);
                        
                        // Update emergency history
                        if (response.resolvedEmergencies.length === 0) {
                            $('#emergency-history').html(`
                                <div class="empty-state">
                                    <i class="bi bi-archive text-muted" style="font-size: 2rem;"></i>
                                    <h6 class="mt-3">{{ T::translate('No Resolved Emergencies', 'Walang Nalutas na mga Emergency') }}</h6>
                                    <p class="small">{{ T::translate('No emergency notices have been resolved in the selected time period.', 'Walang mga abiso ng emergency ang nalutas sa napiling panahon.') }}</p>
                                </div>
                            `);
                        } else {
                            let emergencyHtml = '';
                            response.resolvedEmergencies.forEach(emergency => {
                                emergencyHtml += renderEmergencyHistoryCard(emergency);
                            });
                            $('#emergency-history').html(emergencyHtml);
                        }
                        
                        // Update service request history
                        if (response.completedServiceRequests.length === 0) {
                            $('#request-history').html(`
                                <div class="empty-state">
                                    <i class="bi bi-archive text-muted" style="font-size: 2rem;"></i>
                                    <h6 class="mt-3">{{ T::translate('No Completed/Rejected Service Requests', 'Walang Natapos/Tinanggihang mga Pakiusap ng Serbisyo') }}</h6>
                                    <p class="small">{{ T::translate('No service requests have been completed or rejected in the selected time period.', 'Walang mga pakiusap ng serbisyo ang natapos o tinanggihan sa napiling panahon.') }}</p>
                                </div>
                            `);
                        } else {
                            let serviceHtml = '';
                            response.completedServiceRequests.forEach(request => {
                                serviceHtml += renderServiceHistoryCard(request);
                            });
                            $('#request-history').html(serviceHtml);
                        }
                        
                        // Update statistics
                        updateEmergencyStatistics(response.emergencyStats);
                        updateServiceStatistics(response.serviceStats);
                        
                        toastr.success('{{ T::translate("Date range filter applied successfully", "Matagumpay na na-apply ang filter ng hanay ng petsa") }}');
                    } else {
                        toastr.error('{{ T::translate("Failed to apply filter", "Nabigong mag-apply ng filter") }}');
                    }
                },
                error: function(xhr) {
                    let errorMessage = '{{ T::translate("Failed to apply filter", "Nabigong mag-apply ng filter") }}';
                    if (xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        const firstError = Object.values(errors)[0];
                        if (firstError && firstError[0]) {
                            errorMessage = firstError[0];
                        }
                    }
                    toastr.error(errorMessage);
                },
                complete: function() {
                    $('#applyFilter').html('<i class="bi bi-funnel-fill me-1"></i>{{ T::translate("Apply Filter", "Ilapat ang Pagsala") }}');
                    $('#applyFilter').prop('disabled', false);
                }
            });
        });
        
        // Reset filter
        $('#resetFilter').on('click', function() {
            // Set default values first
            $('#timeRange').val('30days');
            
            // Set date values with proper formatting
            const today = new Date();
            const thirtyDaysAgo = new Date();
            thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
            
            // Format dates as YYYY-MM-DD
            const formatDate = (date) => {
                const year = date.getFullYear();
                const month = String(date.getMonth() + 1).padStart(2, '0');
                const day = String(date.getDate()).padStart(2, '0');
                return `${year}-${month}-${day}`;
            };
            
            $('#endDate').val(formatDate(today));
            $('#startDate').val(formatDate(thirtyDaysAgo));
            
            // Hide custom range
            $('#customRange').hide();
            
            // Then click apply filter
            $('#applyFilter').click();
        });
        
        // Initialize with current date in end date field for custom range
        document.getElementById('endDate').valueAsDate = new Date();
        
        // Set start date 30 days ago for custom range by default
        const thirtyDaysAgo = new Date();
        thirtyDaysAgo.setDate(thirtyDaysAgo.getDate() - 30);
        document.getElementById('startDate').valueAsDate = thirtyDaysAgo;

        // View emergency history details
        function viewEmergencyHistory(noticeId) {
            $('#emergencyHistoryContent').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">{{ T::translate("Loading details...", "Naglo-load ng mga detalye...") }}</p></div>');
            $('#emergencyHistoryDetailModal').modal('show');
            
            $.ajax({
                url: "{{ route('care-manager.emergency.request.get.emergency', '') }}/" + noticeId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        renderEmergencyHistoryDetails(response.emergency_notice);
                    } else {
                        $('#emergencyHistoryContent').html(`<div class="alert alert-danger">{{ T::translate("Error loading details:", "Error sa pag-load ng mga detalye:") }} ${response.message}</div>`);
                    }
                },
                error: function() {
                    $('#emergencyHistoryContent').html('<div class="alert alert-danger">{{ T::translate("Failed to load emergency details. Please try again.", "Nabigong i-load ang mga detalye ng emergency. Mangyaring subukan muli.") }}</div>');
                }
            });
        }
        
        // View service request history details
        function viewServiceRequestHistory(requestId) {
            $('#serviceHistoryContent').html('<div class="text-center"><div class="spinner-border text-primary" role="status"></div><p class="mt-2">{{ T::translate("Loading details...", "Naglo-load ng mga detalye...") }}</p></div>');
            $('#serviceHistoryDetailModal').modal('show');
            
            $.ajax({
                url: "{{ route('care-manager.emergency.request.get.service', '') }}/" + requestId,
                method: 'GET',
                success: function(response) {
                    if (response.success) {
                        renderServiceHistoryDetails(response.service_request);
                    } else {
                        $('#serviceHistoryContent').html(`<div class="alert alert-danger">{{ T::translate("Error loading details:", "Error sa pag-load ng mga detalye:") }} ${response.message}</div>`);
                    }
                },
                error: function() {
                    $('#serviceHistoryContent').html('<div class="alert alert-danger">{{ T::translate("Failed to load service request details. Please try again.", "Nabigong i-load ang mga detalye ng pakiusap ng serbisyo. Mangyaring subukan muli.") }}</div>');
                }
            });
        }
        
        // Render emergency history details
        function renderEmergencyHistoryDetails(emergency) {
            let content = `
                <div class="mb-4">
                    <h5 class="border-bottom pb-2">{{ T::translate("Emergency Information", "Impormasyon ng Emergency") }}</h5>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Beneficiary:", "Benepisyaryo:") }}</div>
                        <div class="col-md-8">${emergency.beneficiary.first_name} ${emergency.beneficiary.last_name}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Address:", "Tirahan:") }}</div>
                        <div class="col-md-8">${emergency.beneficiary.street_address} (${emergency.beneficiary.barangay.barangay_name}, ${emergency.beneficiary.municipality.municipality_name})</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Contact Number:", "Numero ng Kontak:") }}</div>
                        <div class="col-md-8">${emergency.beneficiary.mobile || '{{ T::translate("Not provided", "Hindi ibinigay") }}'}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Emergency Contact:", "Emergency Contact:") }}</div>
                        <div class="col-md-8">${emergency.beneficiary.emergency_contact_name || '{{ T::translate("Not provided", "Hindi ibinigay") }}'} ${emergency.beneficiary.emergency_contact_name ? `(${emergency.beneficiary.emergency_contact_relation}) - ${emergency.beneficiary.emergency_contact_mobile}` : ''}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Type:", "Uri:") }}</div>
                        <div class="col-md-8"><span class="badge me-2" style="background-color: ${emergency.emergency_type.color_code}">${emergency.emergency_type.name}</span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">Status:</div>
                        <div class="col-md-8">${formatStatus(emergency.status)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Created:", "Nilikha:") }}</div>
                        <div class="col-md-8">${formatDateTime(emergency.created_at)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Resolved:", "Nalutas:") }}</div>
                        <div class="col-md-8">${formatDateTime(emergency.updated_at)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Message:", "Mensahe:") }}</div>
                        <div class="col-md-8">${emergency.message}</div>
                    </div>
                </div>
            `;
            
            $('#emergencyHistoryContent').html(content);
            
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
                                    <small class="text-muted">{{ T::translate("By:", "Ni:") }} ${update.updated_by_name || '{{ T::translate("System", "Sistema") }}'}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                $('#emergencyHistoryTimeline').html(updatesHtml);
            } else {
                $('#emergencyHistoryTimeline').html('<p class="text-muted">{{ T::translate("No updates available", "Walang mga update na available") }}</p>');
            }
        }
        
        // Render service request history details
        function renderServiceHistoryDetails(request) {
            let content = `
                <div class="mb-4">
                    <h5 class="border-bottom pb-2">{{ T::translate("Service Request Information", "Impormasyon ng Pakiusap ng Serbisyo") }}</h5>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Beneficiary:", "Benepisyaryo:") }}</div>
                        <div class="col-md-8">${request.beneficiary ? request.beneficiary.first_name + ' ' + request.beneficiary.last_name : '{{ T::translate("Unknown", "Hindi kilala") }}'}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Address:", "Tirahan:") }}</div>
                        <div class="col-md-8">${(request.beneficiary && request.beneficiary.street_address) || '{{ T::translate("Unknown", "Hindi kilala") }}'} 
                            ${request.beneficiary && request.beneficiary.barangay && request.beneficiary.municipality ? 
                                `(${request.beneficiary.barangay.barangay_name || '{{ T::translate("Unknown", "Hindi kilala") }}'}, ${request.beneficiary.municipality.municipality_name || '{{ T::translate("Unknown", "Hindi kilala") }}'})` : 
                                ''}
                        </div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Service Type:", "Uri ng Serbisyo:") }}</div>
                        <div class="col-md-8"><span class="badge" style="background-color: ${request.service_type ? request.service_type.color_code : '#6c757d'}">${request.service_type ? request.service_type.name : '{{ T::translate("Unknown", "Hindi kilala") }}'}</span></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Status:", "Katayuan:") }}</div>
                        <div class="col-md-8">${formatStatus(request.status)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Requested Date:", "Hiniling na Petsa:") }}</div>
                        <div class="col-md-8">${formatDate(request.service_date)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Created:", "Nilikha:") }}</div>
                        <div class="col-md-8">${formatDateTime(request.created_at)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Completed/Rejected:", "Natapos/Tinanggihan:") }}</div>
                        <div class="col-md-8">${formatDateTime(request.updated_at)}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4 fw-bold">{{ T::translate("Message:", "Mensahe:") }}</div>
                        <div class="col-md-8">${request.message || '{{ T::translate("No message provided", "Walang mensahe na ibinigay") }}'}</div>
                    </div>
                </div>
            `;
            
            $('#serviceHistoryContent').html(content);

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
                                    <small class="text-muted">{{ T::translate("By:", "Ni:") }} ${update.updated_by_name || '{{ T::translate("System", "Sistema") }}'}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });
                
                $('#serviceHistoryTimeline').html(updatesHtml);
            } else {
                $('#serviceHistoryTimeline').html('<p class="text-muted">{{ T::translate("No updates available", "Walang mga update na available") }}</p>');
            }
        }
        
        // Helper function to render emergency history card
        function renderEmergencyHistoryCard(emergency) {
            return `
                <div class="card emergency-card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title">{{ T::translate("Resolved:", "Nalutas:") }} ${emergency.emergency_type ? emergency.emergency_type.name : '{{ T::translate("Unknown", "Hindi kilala") }}'}</h5>
                            <span class="badge bg-success bg-opacity-10 text-success">{{ T::translate("Resolved", "Nalutas") }}</span>
                        </div>
                        
                        <div class="d-flex flex-wrap mb-1">
                            <span class="info-label">{{ T::translate("Beneficiary:", "Benepisyaryo:") }}</span>
                            <span>${emergency.beneficiary ? emergency.beneficiary.first_name + ' ' + emergency.beneficiary.last_name : '{{ T::translate("Unknown", "Hindi kilala") }}'}</span>
                        </div>
                        
                        <div class="d-flex flex-wrap mb-1">
                            <span class="info-label">{{ T::translate("Address:", "Tirahan:") }}</span>
                            <span>
                                ${emergency.beneficiary ? (emergency.beneficiary.street_address || '{{ T::translate("Unknown address", "Hindi kilalang tirahan") }}') : '{{ T::translate("Unknown", "Hindi kilala") }}'}
                                ${emergency.beneficiary && emergency.beneficiary.barangay && emergency.beneficiary.municipality ? 
                                    `(${emergency.beneficiary.barangay.barangay_name || ''}, ${emergency.beneficiary.municipality.municipality_name || ''})` : 
                                    ''}
                            </span>
                        </div>
                        
                        <div class="d-flex flex-wrap mb-1">
                            <span class="info-label">{{ T::translate("Message:", "Mensahe:") }}</span>
                            <span>${emergency.message ? (emergency.message.length > 100 ? emergency.message.substring(0, 100) + '...' : emergency.message) : '{{ T::translate("No message provided", "Walang mensahe na ibinigay") }}'}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i> ${formatTimeAgo(emergency.updated_at)} 
                                ${emergency.action_taken_by ? `{{ T::translate("by", "ni") }} ${emergency.action_taken_by_name || '{{ T::translate("Staff", "Staff") }}'}` : ''}
                            </small>
                            <button class="btn btn-sm btn-outline-secondary" onclick="viewEmergencyHistory(${emergency.notice_id})">
                                <i class="bi bi-eye me-1"></i> {{ T::translate("View Details", "Tingnan ang Detalye") }}
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Helper function to render service history card
       function renderServiceHistoryCard(request) {
            return `
                <div class="card request-card mb-3">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title">${capitalizeFirst(request.status)}: ${request.service_type ? request.service_type.name : '{{ T::translate("Unknown", "Hindi kilala") }}'}</h5>
                            <span class="badge ${request.status == 'completed' ? 'bg-success bg-opacity-10 text-success' : 'bg-danger bg-opacity-10 text-danger'}">
                                ${capitalizeFirst(request.status)}
                            </span>
                        </div>
                        
                        <div class="d-flex flex-wrap mb-1">
                            <span class="info-label">{{ T::translate("Beneficiary:", "Benepisyaryo:") }}</span>
                            <span>${request.beneficiary ? request.beneficiary.first_name + ' ' + request.beneficiary.last_name : '{{ T::translate("Unknown", "Hindi kilala") }}'}</span>
                        </div>
                        
                        <div class="d-flex flex-wrap mb-1">
                            <span class="info-label">{{ T::translate("Address:", "Tirahan:") }}</span>
                            <span>
                                ${request.beneficiary ? (request.beneficiary.street_address || '{{ T::translate("Unknown address", "Hindi kilalang tirahan") }}') : '{{ T::translate("Unknown", "Hindi kilala") }}'}
                                ${request.beneficiary && request.beneficiary.barangay && request.beneficiary.municipality ? 
                                    `(${request.beneficiary.barangay.barangay_name || ''}, ${request.beneficiary.municipality.municipality_name || ''})` : 
                                    ''}
                            </span>
                        </div>
                        
                        ${request.status == 'completed' && request.care_worker ? `
                        <div class="d-flex flex-wrap mb-1">
                            <span class="info-label">{{ T::translate("Care Worker:", "Tagapag-alaga:") }}</span>
                            <span>${request.care_worker.first_name} ${request.care_worker.last_name}</span>
                        </div>
                        ` : ''}
                        
                        <div class="d-flex flex-wrap mb-1">
                            <span class="info-label">{{ T::translate("Service Date:", "Petsa ng Serbisyo:") }}</span>
                            <span>${formatDate(request.service_date)}</span>
                        </div>
                        
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i> ${formatTimeAgo(request.updated_at)} 
                                ${request.action_taken_by ? `{{ T::translate("by", "ni") }} ${request.action_taken_by_name || '{{ T::translate("Staff", "Staff") }}'}` : ''}
                            </small>
                            <button class="btn btn-sm btn-outline-secondary" onclick="viewServiceRequestHistory(${request.service_request_id})">
                                <i class="bi bi-eye me-1"></i> {{ T::translate("View Details", "Tingnan ang Detalye") }}
                            </button>
                        </div>
                    </div>
                </div>
            `;
        }
        
        // Update emergency statistics
        function updateEmergencyStatistics(stats) {
            $('#totalEmergencies').text(stats.total);
            $('#resolvedEmergencies').text(stats.resolved);
            $('#pendingEmergencies').text(stats.pending);
            
            // Add this block to update the emergency type breakdown
            let breakdownHtml = '';
            if (stats.byType && stats.byType.length > 0) {
                stats.byType.forEach(type => {
                    // Handle both color and color_code property names for compatibility
                    const backgroundColor = type.color || type.color_code || '#6c757d';
                    breakdownHtml += `
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <span class="me-2">${type.name}</span>
                            </div>
                            <span class="badge bg-danger rounded-pill">${type.count}</span>
                        </div>
                    `;
                });
            } else {
                breakdownHtml = '<div class="list-group-item text-muted">{{ T::translate("No data available", "Walang available na data") }}</div>';
            }
            $('#emergencyTypeBreakdown').html(breakdownHtml);
            
            // Log information for debugging
            console.log('Emergency stats:', stats);
            if (stats.byType) console.log('Emergency byType count:', stats.byType.length);
        }
        
        // Update service request statistics
        function updateServiceStatistics(stats) {
            $('#totalServiceRequests').text(stats.total);
            // Change this line to use completed + rejected:
            $('#completedServiceRequests').text(stats.completed + stats.rejected);
            $('#pendingServiceRequests').text(stats.pending);
            
            // Rest of the function remains unchanged
            let breakdownHtml = '';
            stats.byType.forEach(type => {
                breakdownHtml += `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        ${type.name}
                        <span class="badge bg-primary rounded-pill">${type.count}</span>
                    </div>
                `;
            });
            $('#serviceTypeBreakdown').html(breakdownHtml);
            
        }
        
        // Helper functions
        function formatDateTime(dateTimeStr) {
            if (!dateTimeStr) return '{{ T::translate("N/A", "N/A") }}';
            const date = new Date(dateTimeStr);
            return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
        }
        
        function formatDate(dateStr) {
            if (!dateStr) return '{{ T::translate("N/A", "N/A") }}';
            const date = new Date(dateStr);
            return date.toLocaleDateString();
        }
        
        function formatTimeAgo(dateTimeStr) {
            if (!dateTimeStr) return '{{ T::translate("N/A", "N/A") }}';
            const date = new Date(dateTimeStr);
            const now = new Date();
            const diffMs = now - date;
            const diffDays = Math.floor(diffMs / (1000 * 60 * 60 * 24));
            const diffHours = Math.floor((diffMs % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            
            if (diffDays > 30) {
                return formatDate(dateTimeStr);
            } else if (diffDays > 0) {
                return `${diffDays} {{ T::translate("day", "araw") }}${diffDays > 1 ? '{{ T::translate("s", "") }}' : ''} {{ T::translate("ago", "ang nakalipas") }}`;
            } else if (diffHours > 0) {
                return `${diffHours} {{ T::translate("hour", "oras") }}${diffHours > 1 ? '{{ T::translate("s", "") }}' : ''} {{ T::translate("ago", "ang nakalipas") }}`;
            } else {
                return '{{ T::translate("Less than an hour ago", "Wala pang isang oras ang nakalipas") }}';
            }
        }
        
        function formatStatus(status) {
            switch(status) {
                case 'new': return '<span class="badge bg-danger">{{ T::translate("New", "Bago") }}</span>';
                case 'in_progress': return '<span class="badge bg-info">{{ T::translate("In Progress", "Kasulukuyang Ginagawa") }}</span>';
                case 'approved': return '<span class="badge bg-success">{{ T::translate("Approved", "Naaprubahan") }}</span>';
                case 'rejected': return '<span class="badge bg-danger">{{ T::translate("Rejected", "Tinanggihan") }}</span>';
                case 'completed': return '<span class="badge bg-primary">{{ T::translate("Completed", "Natapos") }}</span>';
                case 'resolved': return '<span class="badge bg-success">{{ T::translate("Resolved", "Nalutas") }}</span>';
                default: return `<span class="badge bg-secondary">${status}</span>`;
            }
        }
        
        function getUpdateTypeBadgeClass(updateType) {
            switch(updateType) {
                case 'response': return 'bg-info';
                case 'status_change': return 'bg-warning';
                case 'assignment': return 'bg-primary';
                case 'resolution': return 'bg-success';
                case 'completion': return 'bg-success';
                case 'approval': return 'bg-success';
                case 'rejection': return 'bg-danger';
                case 'note': return 'bg-secondary';
                default: return 'bg-secondary';
            }
        }
        
        function formatUpdateType(updateType) {
            switch(updateType) {
                case 'response': return '{{ T::translate("Response", "Tugon") }}';
                case 'status_change': return '{{ T::translate("Status Change", "Pagbabago ng Status") }}';
                case 'assignment': return '{{ T::translate("Assignment", "Pagtalaga") }}';
                case 'resolution': return '{{ T::translate("Resolution", "Resolusyon") }}';
                case 'completion': return '{{ T::translate("Completion", "Pagkumpleto") }}';
                case 'approval': return '{{ T::translate("Approval", "Pag-apruba") }}';
                case 'rejection': return '{{ T::translate("Rejection", "Pagtanggi") }}';
                case 'note': return '{{ T::translate("Note", "Tala") }}';
                default: return updateType;
            }
        }
        
        function capitalizeFirst(string) {
            return string.charAt(0).toUpperCase() + string.slice(1);
        }
        
        // Initialize Bootstrap tabs
        var tabEls = document.querySelectorAll('button[data-bs-toggle="tab"]');
        tabEls.forEach(function(tabEl) {
            new bootstrap.Tab(tabEl);
        });
    </script>
</body>
</html>