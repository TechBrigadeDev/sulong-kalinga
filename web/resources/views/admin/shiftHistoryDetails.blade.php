<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Details | Admin Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shiftHistoryDetails.css') }}">
</head>
<body>
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp
    @include('components.adminNavbar')
    @include('components.adminSidebar')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-2 gap-0">
                <!-- Back button for large screens -->
                <a href="{{ route('admin.shift.histories.index') }}" class="btn btn-secondary btn-sm d-none d-md-inline-flex">
                    <i class="bi bi-arrow-left me-2"></i>{{ T::translate('Back', 'Bumalik')}}
                </a>
                
                <!-- Centered title -->
                <div class="flex-grow-1 text-center">
                    <div class="text-center" style="font-size: 20px; font-weight: bold; padding: 10px;">{{ T::translate('SHIFT DETAILS', 'DETALYE NG SHIFT')}}</div>
                </div>
                
                <!-- Action buttons container -->
                <div class="d-flex action-buttons">
                    <!-- Back button for small screens -->
                    <a href="{{ route('admin.shift.histories.index') }}" class="btn btn-secondary btn-sm d-inline-flex d-md-none">
                        <i class="bi bi-arrow-left me-2"></i>{{ T::translate('Back', 'BUmalik')}}
                    </a>
                    <button class="btn btn-primary btn-sm">
                        <i class="bi bi-download me-2"></i>Export Report
                    </button>
                </div>
            </div>
            <div class="row" id="home-content">
                <div class="col-12">
                    <!-- Shift Information Card -->
                    <div class="card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">{{ T::translate('Shift Information', 'Impormasyon ng Shift')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-grid">
                                        <div class="detail-row">
                                            <div class="detail-label">{{ T::translate('Care Worker:', 'Tagapag-alaga:')}}</div>
                                            <div class="detail-value" id="detail-care-worker">John Smith</div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">{{ T::translate('Date:', 'Petsa:')}}</div>
                                            <div class="detail-value" id="detail-date">May 20, 2025</div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">{{ T::translate('Shift Time:', 'Oras ng Shift')}}</div>
                                            <div class="detail-value" id="detail-shift-time">08:00 AM - 04:00 PM</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-grid">
                                        <div class="detail-row">
                                            <div class="detail-label">Shift Time:</div>
                                            <div class="detail-value" id="detail-shift-time">
                                                {{ \Carbon\Carbon::parse($shift->time_in)->format('h:i A') }} - 
                                                {{ $shift->time_out ? \Carbon\Carbon::parse($shift->time_out)->format('h:i A') : '--:--' }}
                                            </div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">{{ T::translate('Total Hours:', 'Kabuuang Oras:')}}</div>
                                            <div class="detail-value" id="detail-total-hours">8 hours</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location History Card -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">{{ T::translate('Location History', 'Kasaysayan ng Lokasyon')}}</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <!-- <thead>
                                        <tr>
                                            <th style="width: 25%">{{ T::translate('Time', 'Oras')}}</th>
                                            <th style="width: 75%">{{ T::translate('Location', 'Lokasyon')}}</th>
                                            
                                        </tr>
                                    </thead> -->
                                    <thead>
                                        <tr>
                                            <th style="width: 20%">{{ T::translate('Time', 'Oras')}}</th>
                                            <th style="width: 40%">{{ T::translate('Location', 'Lokasyon')}}</th>
                                            <th style="width: 20%">{{ T::translate('Event', 'Kaganapan')}}</th>
                                            <th style="width: 20%">{{ T::translate('Visitation', 'Pagbisita')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="history-table-body">
                                        @forelse($tracks as $track)
                                            <tr>
                                                <td>
                                                    {{ \Carbon\Carbon::parse($track->recorded_at)->format('h:i A') }}
                                                </td>
                                                <td>
                                                    <i class="bi bi-geo-alt-fill text-primary me-2"></i>
                                                    {{ $track->address ?? 'Unknown location' }}
                                                    @if(isset($track->track_coordinates['lat']) && isset($track->track_coordinates['lng']))
                                                        <span class="text-muted" style="font-size: 0.85em;">
                                                            ({{ $track->track_coordinates['lat'] }}, {{ $track->track_coordinates['lng'] }})
                                                        </span>
                                                        <br>
                                                        <span class="badge 
                                                            @if($track->proximity === 'Near Beneficiary (within 100 meters)') bg-success
                                                            @elseif($track->proximity === 'Not Near Beneficiary (within 100 meters)') bg-danger
                                                            @else bg-secondary
                                                            @endif
                                                            " style="font-size: 0.85em;">
                                                            {{ $track->proximity ?? 'N/A' }}
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($track->arrival_status === 'arrived')
                                                        <span class="badge bg-success">Arrived</span>
                                                    @elseif($track->arrival_status === 'departed')
                                                        <span class="badge bg-danger">Departed</span>
                                                    @else
                                                        <span class="badge bg-secondary">Unknown</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    @if($track->visitation)
                                                        {{ $track->visitation->beneficiary->first_name ?? '' }} {{ $track->visitation->beneficiary->last_name ?? '' }}
                                                        <br>
                                                        <span class="text-muted" style="font-size: 0.85em;">
                                                            {{ $track->visitation->visit_type_display ?? '' }}
                                                        </span>
                                                    @else
                                                        <span class="text-muted">N/A</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center text-muted py-3">No location history available for this shift.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <script>
        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Function to load shift details
        // function loadShiftDetails() {
        //     // ... existing loadShiftDetails function ...
        // }

        // document.addEventListener('DOMContentLoaded', loadShiftDetails);
    </script>
</body>
</html>