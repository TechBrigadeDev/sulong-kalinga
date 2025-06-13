<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shift Histories | Admin Dashboard</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/shiftHistory.css') }}">
</head>
<body>
    @include('components.adminNavbar')
    @include('components.adminSidebar')
    
    <div class="home-section">
        <div class="container-fluid">
            <div class="text-left">SHIFT HISTORY</div>            
            <div class="row" id="home-content">
                <div class="col-12">
                    <div class="filter-section">
                        <form action="{{ route('admin.shift.histories.index') }}" method="GET" id="searchFilterForm">
                            <div class="row g-3 align-items-end">
                                <div class="col-md-5 col-12">
                                    <label for="searchBar" class="filter-label">Search Care Worker</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-search"></i>
                                        </span>
                                        <input type="text" class="form-control" placeholder="Search by name..." 
                                            id="searchBar" name="search" value="{{ $search ?? '' }}">
                                    </div>
                                </div>

                                <div class="col-md-4 col-12">
                                    <label for="dateFilter" class="filter-label">Filter by Date</label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="bi bi-calendar"></i>
                                        </span>
                                        <input type="date" class="form-control" 
                                            id="dateFilter" name="date" value="{{ $date ?? '' }}">
                                    </div>
                                </div>

                                <div class="col-md-2 col-6">
                                    <button type="submit" class="btn btn-primary w-100">
                                        <i class="bi bi-funnel me-1"></i> Apply
                                    </button>
                                </div>

                                <div class="col-md-1 col-6">
                                    <button type="button" class="btn btn-outline-secondary w-100" onclick="resetFilters()" title="Reset filters">
                                        <i class="bi bi-arrow-counterclockwise"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <h5 class="mb-0">Care Worker Shift Records</h5>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th scope="col">Care Worker</th>
                                            <th scope="col">Date</th>
                                            <th scope="col">Shift Time</th>
                                            <th scope="col">Municipality</th>
                                            <th scope="col">Status</th>
                                            <th scope="col" class="text-center">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><strong>John Smith</strong></td>
                                            <td>May 20, 2025</td>
                                            <td>08:00 AM - 04:00 PM</td>
                                            <td>Mondragon</td>
                                            <td><span class="badge badge-success">Completed</span></td>
                                            <td class="text-center">
                                                <div class="action-icons">
                                                    <a href="#" title="View Details" data-bs-toggle="modal" data-bs-target="#shiftDetailsModal" 
                                                       onclick="showShiftDetails('John Smith', 'May 20, 2025', '08:00', '16:00', 'Mondragon', 'Completed')">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="#" title="Download Report">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Maria Garcia</strong></td>
                                            <td>May 20, 2025</td>
                                            <td>09:00 AM - 05:00 PM</td>
                                            <td>San Roque</td>
                                            <td><span class="badge badge-success">Completed</span></td>
                                            <td class="text-center">
                                                <div class="action-icons">
                                                    <a href="#" title="View Details" data-bs-toggle="modal" data-bs-target="#shiftDetailsModal" 
                                                       onclick="showShiftDetails('Maria Garcia', 'May 20, 2025', '09:00', '17:00', 'San Roque', 'Completed')">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="#" title="Download Report">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>David Johnson</strong></td>
                                            <td>May 20, 2025</td>
                                            <td>02:00 PM - 10:00 PM</td>
                                            <td>Mondragon</td>
                                            <td><span class="badge badge-warning">In Progress</span></td>
                                            <td class="text-center">
                                                <div class="action-icons">
                                                    <a href="#" title="View Details" data-bs-toggle="modal" data-bs-target="#shiftDetailsModal" 
                                                       onclick="showShiftDetails('David Johnson', 'May 20, 2025', '14:00', '22:00', 'Mondragon', 'In Progress')">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="#" title="Download Report">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td><strong>Sarah Wilson</strong></td>
                                            <td>May 19, 2025</td>
                                            <td>08:00 AM - 04:00 PM</td>
                                            <td>San Roque</td>
                                            <td><span class="badge badge-success">Completed</span></td>
                                            <td class="text-center">
                                                <div class="action-icons">
                                                    <a href="#" title="View Details" data-bs-toggle="modal" data-bs-target="#shiftDetailsModal" 
                                                       onclick="showShiftDetails('Sarah Wilson', 'May 19, 2025', '08:00', '16:00', 'San Roque', 'Completed')">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="#" title="Download Report">
                                                        <i class="bi bi-download"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <nav aria-label="Page navigation" class="mt-4">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item disabled">
                                        <a class="page-link" href="#" tabindex="-1">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>
                                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                                    <li class="page-item">
                                        <a class="page-link" href="#">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shift Details Modal -->
    <div class="modal fade shift-details-modal" id="shiftDetailsModal" tabindex="-1" aria-labelledby="shiftDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Shift Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-4">
                        <div class="detail-row">
                            <div class="detail-label">Care Worker:</div>
                            <div class="detail-value" id="modal-care-worker">John Smith</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Date:</div>
                            <div class="detail-value" id="modal-date">May 20, 2025</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Shift Time:</div>
                            <div class="detail-value" id="modal-shift-time">08:00 AM - 04:00 PM</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Municipality:</div>
                            <div class="detail-value" id="modal-municipality">Mondragon</div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Status:</div>
                            <div class="detail-value">
                                <span class="badge" id="modal-status-badge">Completed</span>
                            </div>
                        </div>
                        <div class="detail-row">
                            <div class="detail-label">Total Hours:</div>
                            <div class="detail-value" id="modal-total-hours">8 hours</div>
                        </div>
                    </div>

                    <h6 class="mb-3">Location History</h6>
                    <div class="history-table">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th style="width: 25%">Time</th>
                                    <th>Location</th>
                                </tr>
                            </thead>
                            <tbody id="history-table-body">
                                <!-- Will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">
                        <i class="bi bi-download me-1"></i> Export Data
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    
    <script>
        // Function to reset filters
        function resetFilters() {
            document.getElementById('searchBar').value = '';
            document.getElementById('dateFilter').value = '';
            document.getElementById('searchFilterForm').submit();
        }

        // Function to generate mock location data
        function generateLocationData(startTime, endTime, municipality) {
            const start = new Date(`2000-01-01T${startTime}:00`);
            const end = new Date(`2000-01-01T${endTime}:00`);
            const locations = [];
            
            // Common locations for the municipality
            const commonLocations = [
                `${municipality} Health Center`,
                `Brgy. Poblacion Home Visit`,
                `Brgy. San Antonio Home Visit`,
                `${municipality} Municipal Hall`,
                `Local Market Area`,
                `Senior Citizen Center`
            ];
            
            // Generate data points every 10 minutes
            let currentTime = new Date(start);
            while (currentTime <= end) {
                const randomLocation = commonLocations[Math.floor(Math.random() * commonLocations.length)];
                const hours = currentTime.getHours().toString().padStart(2, '0');
                const minutes = currentTime.getMinutes().toString().padStart(2, '0');
                const timeString = `${hours}:${minutes}`;
                
                locations.push({
                    time: timeString,
                    location: randomLocation
                });
                
                currentTime = new Date(currentTime.getTime() + 10 * 60000); // Add 10 minutes
            }
            
            return locations;
        }

        // Function to show shift details in modal
        function showShiftDetails(careWorker, date, startTime, endTime, municipality, status) {
            // Format time for display
            const formatTime = (timeStr) => {
                const [hours, minutes] = timeStr.split(':');
                const hourNum = parseInt(hours);
                const period = hourNum >= 12 ? 'PM' : 'AM';
                const displayHour = hourNum % 12 || 12;
                return `${displayHour}:${minutes} ${period}`;
            };

            document.getElementById('modal-care-worker').textContent = careWorker;
            document.getElementById('modal-date').textContent = date;
            document.getElementById('modal-shift-time').textContent = `${formatTime(startTime)} - ${formatTime(endTime)}`;
            document.getElementById('modal-municipality').textContent = municipality;
            
            // Calculate total hours
            const start = new Date(`2000-01-01T${startTime}:00`);
            const end = new Date(`2000-01-01T${endTime}:00`);
            const diff = (end - start) / (1000 * 60 * 60);
            document.getElementById('modal-total-hours').textContent = `${diff.toFixed(1)} hours`;
            
            // Update status badge
            const statusBadge = document.getElementById('modal-status-badge');
            statusBadge.textContent = status;
            statusBadge.className = 'badge ' + (status === 'Completed' ? 'badge-success' : 'badge-warning');
            
            // Generate and display location history
            const locationData = generateLocationData(startTime, endTime, municipality);
            const historyTable = document.getElementById('history-table-body');
            historyTable.innerHTML = '';
            
            locationData.forEach(entry => {
                const row = document.createElement('tr');
                
                const timeCell = document.createElement('td');
                timeCell.innerHTML = `<span class="time-badge">${entry.time}</span>`;
                
                const locationCell = document.createElement('td');
                locationCell.innerHTML = `<i class="bi bi-geo-alt-fill location-marker"></i> ${entry.location}`;
                
                row.appendChild(timeCell);
                row.appendChild(locationCell);
                historyTable.appendChild(row);
            });
        }
    </script>
</body>
</html>