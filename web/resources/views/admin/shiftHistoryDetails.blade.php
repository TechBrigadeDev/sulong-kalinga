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
    @include('components.adminNavbar')
    @include('components.adminSidebar')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-center mb-2 gap-0">
                <!-- Back button for large screens -->
                <a href="{{ route('admin.shift.histories.index') }}" class="btn btn-secondary btn-sm d-none d-md-inline-flex">
                    <i class="bi bi-arrow-left me-2"></i>Back
                </a>
                
                <!-- Centered title -->
                <div class="flex-grow-1 text-center">
                    <div class="text-center" style="font-size: 20px; font-weight: bold; padding: 10px;">SHIFT DETAILS</div>
                </div>
                
                <!-- Action buttons container -->
                <div class="d-flex action-buttons">
                    <!-- Back button for small screens -->
                    <a href="{{ route('admin.shift.histories.index') }}" class="btn btn-secondary btn-sm d-inline-flex d-md-none">
                        <i class="bi bi-arrow-left me-2"></i>Back
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
                            <h5 class="mb-0">Shift Information</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="detail-grid">
                                        <div class="detail-row">
                                            <div class="detail-label">Care Worker:</div>
                                            <div class="detail-value" id="detail-care-worker">
                                                {{ $shift->careWorker->first_name ?? '' }} {{ $shift->careWorker->last_name ?? '' }}
                                            </div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">Date:</div>
                                            <div class="detail-value" id="detail-date">
                                                {{ \Carbon\Carbon::parse($shift->time_in)->format('M d, Y') }}
                                            </div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">Shift Time:</div>
                                            <div class="detail-value" id="detail-shift-time">
                                                {{ \Carbon\Carbon::parse($shift->time_in)->format('h:i A') }} - 
                                                {{ $shift->time_out ? \Carbon\Carbon::parse($shift->time_out)->format('h:i A') : '--:--' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="detail-grid">
                                        <div class="detail-row">
                                            <div class="detail-label">Municipality:</div>
                                            <div class="detail-value" id="detail-municipality">
                                                {{ $shift->careWorker->municipality ?? '-' }}
                                            </div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">Status:</div>
                                            <div class="detail-value">
                                                @if($shift->status === 'completed')
                                                    <span class="badge bg-success" id="detail-status-badge">Completed</span>
                                                @elseif($shift->status === 'in_progress')
                                                    <span class="badge bg-warning text-dark" id="detail-status-badge">In Progress</span>
                                                @else
                                                    <span class="badge bg-secondary" id="detail-status-badge">{{ ucfirst($shift->status) }}</span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">Total Hours:</div>
                                            <div class="detail-value" id="detail-total-hours">
                                                @if($shift->time_out)
                                                    {{ \Carbon\Carbon::parse($shift->time_in)->diffInHours(\Carbon\Carbon::parse($shift->time_out)) }} hours
                                                @else
                                                    In Progress
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Location History Card -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0">Location History</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th style="width: 20%">Time</th>
                                            <th style="width: 40%">Location</th>
                                            <th style="width: 20%">Event</th>
                                            <th style="width: 20%">Visitation</th>
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
                                                            {{ $track->visitation->visit_type ?? '' }}
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
        function loadShiftDetails() {
            // ... existing loadShiftDetails function ...
        }

        document.addEventListener('DOMContentLoaded', loadShiftDetails);
    </script>
</body>
</html>