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
                                            <div class="detail-label">{{ T::translate('Municipality:', 'Munisipalidad:')}}</div>
                                            <div class="detail-value" id="detail-municipality">Mondragon</div>
                                        </div>
                                        <div class="detail-row">
                                            <div class="detail-label">Status:</div>
                                            <div class="detail-value">
                                                <span class="badge bg-success" id="detail-status-badge">Completed</span>
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
                                    <thead>
                                        <tr>
                                            <th style="width: 25%">{{ T::translate('Time', 'Oras')}}</th>
                                            <th style="width: 75%">{{ T::translate('Location', 'Lokasyon')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody id="history-table-body">
                                        @php
                                            $dummyData = [
                                                ['time' => '08:00 AM', 'location' => 'Mondragon Health Center', 'description' => 'Shift Start', 'icon' => 'geo-alt-fill text-primary', 'status' => 'Started', 'status_class' => 'success'],
                                                ['time' => '09:15 AM', 'location' => '123 Oak Street', 'description' => 'Patient Visit', 'icon' => 'geo-alt-fill text-primary', 'status' => 'In Progress', 'status_class' => 'info'],
                                                ['time' => '11:30 AM', 'location' => '456 Pine Avenue', 'description' => 'Patient Visit', 'icon' => 'geo-alt-fill text-primary', 'status' => 'In Progress', 'status_class' => 'info'],
                                                ['time' => '12:45 PM', 'location' => 'Local Cafe', 'description' => 'Lunch Break', 'icon' => 'cup-fill text-warning', 'status' => 'Break', 'status_class' => 'warning'],
                                                ['time' => '02:00 PM', 'location' => '789 Maple Road', 'description' => 'Patient Visit', 'icon' => 'geo-alt-fill text-primary', 'status' => 'In Progress', 'status_class' => 'info'],
                                                ['time' => '04:00 PM', 'location' => 'Mondragon Health Center', 'description' => 'Shift End', 'icon' => 'geo-alt-fill text-danger', 'status' => 'Completed', 'status_class' => 'danger']
                                            ];
                                        @endphp

                                        @foreach($dummyData as $entry)
                                        <tr>
                                            <td><span class="time-badge">{{ $entry['time'] }}</span></td>
                                            <td>
                                                <i class="bi bi-{{ $entry['icon'] }} me-2"></i>
                                                {{ $entry['location'] }} - {{ $entry['description'] }}
                                            </td>
                                        </tr>
                                        @endforeach
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