<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Care Worker Tracking</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/careWorkerTracking.css') }}">
    <!-- Leaflet CSS for maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')

    <div class="home-section">
        <div class="text-left">CARE WORKER TRACKING</div>
        <div class="container-fluid">
            <div class="row" id="home-content">
                <div class="col-lg-8">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="bi bi-geo-alt-fill me-2"></i>Live Care Worker Locations
                        </div>
                        <div class="card-body p-0">
                            <div id="map"></div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-clock-history me-2"></i>Movement History
                        </div>
                        <div class="card-body py-2 px-3 history-card">
                            <div class="timeline" id="movementHistory">
                                <div class="text-muted text-center py-3">
                                    Select a care worker to view their movement history
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="col-lg-4">
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="bi bi-people-fill me-2"></i>Care Workers On Shift
                        </div>
                        <div class="card-body">
                            <div class="input-group mb-3">
                                <input type="text" class="form-control" placeholder="Search care workers..." id="searchWorkers">
                                <button class="btn btn-outline-secondary" type="button">
                                    <i class="bi bi-search"></i>
                                </button>
                            </div>
                            
                            <div class="list-group list-group-flush" id="workersList">
                                <div class="text-center py-3">
                                    <div class="spinner-border text-primary" role="status">
                                        <span class="visually-hidden">Loading...</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <div class="card-header">
                            <i class="bi bi-info-circle-fill me-2"></i>Worker Details
                        </div>
                        <div class="card-body" id="workerDetails">
                            <div class="text-center py-3 text-muted">
                                <i class="bi bi-person-lines-fill" style="font-size: 1.5rem;"></i>
                                <p class="mt-2 mb-0">Select a care worker to view details</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
   
    <!-- JavaScript Libraries -->
    <script src="{{ asset('js/toggleSideBar.js') }}"></script>
    <script src="{{ asset('js/bootstrap.bundle.min.js') }}"></script>
    <!-- Leaflet JS for maps -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <!-- Moment.js for date formatting -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    
    <script>
        // Northern Samar coordinates (centered on Catarman)
        const northernSamarCenter = [12.4989, 124.6377];
        
        // Initialize the map centered on Northern Samar
        const map = L.map('map').setView(northernSamarCenter, 12);
        
        // Add OpenStreetMap tiles
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);
        
        // Sample data for care workers in Northern Samar
        const careWorkers = [
            {
                id: 1,
                name: "Maria Santos",
                photo: "{{ asset('images/defaultProfile.png') }}",
                status: "on-shift",
                shiftStart: "2023-05-15T08:00:00Z",
                currentLocation: [12.4989, 124.6377], // Catarman
                assignedBeneficiaries: [
                    { name: "Juan Dela Cruz", visitDay: "Monday" },
                    { name: "Lola Remedios", visitDay: "Wednesday" }
                ],
                movementHistory: [
                    { time: new Date(Date.now() - 10*60000).toISOString(), location: [12.4989, 124.6377], address: "Purok 5, Brgy. Dalakit, Catarman" },
                    { time: new Date(Date.now() - 20*60000).toISOString(), location: [12.505, 124.63], address: "Brgy. UEP Village, Catarman" },
                    { time: new Date(Date.now() - 30*60000).toISOString(), location: [12.49, 124.64], address: "Brgy. Macagtas, Catarman" },
                ],
                contact: "maria.santos@eldercare.ph",
                phone: "+63 912 345 6789"
            },
            {
                id: 2,
                name: "Pedro Reyes",
                photo: "{{ asset('images/defaultProfile.png') }}",
                status: "on-shift",
                shiftStart: "2023-05-15T13:00:00Z",
                currentLocation: [12.515, 124.635], // Near Catarman
                assignedBeneficiaries: [
                    { name: "Lolo Andres", visitDay: "Tuesday" },
                    { name: "Nanay Corazon", visitDay: "Friday" }
                ],
                movementHistory: [
                    { time: new Date(Date.now() - 5*60000).toISOString(), location: [12.515, 124.635], address: "Brgy. Erenas, Catarman" },
                    { time: new Date(Date.now() - 15*60000).toISOString(), location: [12.52, 124.625], address: "Brgy. Cal-igang, Catarman" },
                    { time: new Date(Date.now() - 25*60000).toISOString(), location: [12.51, 124.645], address: "Brgy. Ipil-ipil, Catarman" },
                ],
                contact: "pedro.reyes@eldercare.ph",
                phone: "+63 917 890 1234"
            },
            {
                id: 3,
                name: "Luzviminda Gonzales",
                photo: "{{ asset('images/defaultProfile.png') }}",
                status: "off-shift",
                shiftStart: "2023-05-14T08:00:00Z",
                currentLocation: [12.45, 124.65], // Lope de Vega
                assignedBeneficiaries: [
                    { name: "Tatay Carding", visitDay: "Thursday" }
                ],
                movementHistory: [
                    { time: new Date(Date.now() - 120*60000).toISOString(), location: [12.45, 124.65], address: "Brgy. Osang, Lope de Vega" },
                    { time: new Date(Date.now() - 130*60000).toISOString(), location: [12.455, 124.655], address: "Brgy. Hitapi-an, Lope de Vega" },
                ],
                contact: "luz.gonzales@eldercare.ph",
                phone: "+63 918 765 4321"
            }
        ];
        
        // Markers for each care worker
        const markers = {};
        let selectedWorkerId = null;
        
        // Function to render care workers list
        function renderWorkersList() {
            const workersList = document.getElementById('workersList');
            workersList.innerHTML = '';
            
            careWorkers.forEach(worker => {
                const isActive = worker.status === 'on-shift';
                const statusClass = isActive ? 'status-on-shift' : 'status-off-shift';
                const statusText = isActive ? 'ON SHIFT' : 'OFF SHIFT';
                
                const shiftStart = worker.shiftStart ? 
                    `Started: ${moment(worker.shiftStart).format('h:mm A')}` : 'Shift not started';
                
                const workerElement = document.createElement('div');
                workerElement.className = `list-group-item worker-card ${selectedWorkerId === worker.id ? 'active' : ''}`;
                workerElement.innerHTML = `
                    <div class="d-flex w-100 justify-content-between align-items-start">
                        <div>
                            <div class="d-flex align-items-center mb-1">
                                <h6 class="mb-0">${worker.name}</h6>
                            </div>
                            <div class="d-flex align-items-center">
                                <span class="${statusClass} shift-status me-2">${statusText}</span>
                                <small class="text-muted">${shiftStart}</small>
                            </div>
                        </div>
                    </div>
                `;
                
                workerElement.addEventListener('click', () => selectWorker(worker.id));
                workersList.appendChild(workerElement);
            });
        }
        
        // Function to render worker details
        function renderWorkerDetails(workerId) {
            const worker = careWorkers.find(w => w.id === workerId);
            if (!worker) return;
            
            const isActive = worker.status === 'on-shift';
            const statusClass = isActive ? 'bg-success' : 'bg-danger';
            const statusText = isActive ? 'On Shift' : 'Off Shift';
            
            const detailsContainer = document.getElementById('workerDetails');
            detailsContainer.innerHTML = `
                <div class="text-center mb-3">
                    <img src="${worker.photo}" class="rounded-circle" width="100" height="100" alt="${worker.name}" style="object-fit: cover;">
                    <h5 class="mt-2 mb-1">${worker.name}</h5>
                    <span class="badge ${statusClass}">
                        ${statusText}
                    </span>
                </div>
                
                <div class="mb-3">
                    <h6><i class="bi bi-clock-history me-2"></i>Shift Information</h6>
                    <p class="ps-3">
                        ${worker.shiftStart ? `Started: ${moment(worker.shiftStart).format('LLL')}` : 'Not on shift'}<br>
                    </p>
                </div>
                
                <div class="mb-3">
                    <h6><i class="bi bi-people-fill me-2"></i>Assigned Beneficiaries</h6>
                    <div class="ps-2">
                        ${worker.assignedBeneficiaries.map(b => `
                            <div class="beneficiary-item">
                                <div>
                                    <i class="bi bi-person-fill me-2"></i>${b.name}
                                </div>
                                <span class="visit-day">${b.visitDay}</span>
                            </div>
                        `).join('')}
                    </div>
                </div>
                
                <div class="mb-3">
                    <h6><i class="bi bi-telephone-fill me-2"></i>Contact Information</h6>
                    <p class="ps-3"><i class="bi bi-envelope-fill me-2"></i>${worker.contact}</p>
                    <p class="ps-3"><i class="bi bi-phone-fill me-2"></i>${worker.phone}</p>
                </div>
                
                <button class="btn btn-primary w-100 mt-2" onclick="alert('Messaging feature would be implemented here')">
                    <i class="bi bi-chat-left-text-fill me-2"></i>Send Message
                </button>
            `;
        }
        
        // Function to render movement history
        function renderMovementHistory(workerId) {
            const worker = careWorkers.find(w => w.id === workerId);
            if (!worker) return;
            
            const historyContainer = document.getElementById('movementHistory');
            historyContainer.innerHTML = '';
            
            if (worker.movementHistory.length === 0) {
                historyContainer.innerHTML = '<div class="text-muted text-center py-3">No movement history available</div>';
                return;
            }
            
            worker.movementHistory.forEach(entry => {
                const historyItem = document.createElement('div');
                historyItem.className = 'timeline-item';
                historyItem.innerHTML = `
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <strong>${moment(entry.time).format('h:mm A')}</strong>
                        <small class="text-muted">${moment(entry.time).fromNow()}</small>
                    </div>
                    <p class="mb-1">${entry.address}</p>
                    <small class="text-muted d-block">Coordinates: ${entry.location[0].toFixed(4)}, ${entry.location[1].toFixed(4)}</small>
                `;
                historyContainer.appendChild(historyItem);
            });
        }
        
        // Function to select a worker and update all views
        function selectWorker(workerId) {
            selectedWorkerId = workerId;
            const worker = careWorkers.find(w => w.id === workerId);
            
            // Update the workers list to show which one is selected
            renderWorkersList();
            
            // Update the worker details panel
            renderWorkerDetails(workerId);
            
            // Update the movement history
            renderMovementHistory(workerId);
            
            // Center the map on the selected worker
            if (worker.currentLocation) {
                map.setView(worker.currentLocation, 15);
            }
            
            // Highlight the selected worker's marker
            Object.values(markers).forEach(marker => {
                if (marker.workerId === workerId) {
                    marker.setIcon(L.divIcon({
                        className: 'selected-marker',
                        html: `<div style="background-color: #27ae60; width: 28px; height: 28px; border-radius: 50%; border: 3px solid white; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 14px;">${worker.name.charAt(0)}</div>`,
                        iconSize: [28, 28],
                        iconAnchor: [14, 14]
                    }));
                } else {
                    marker.setIcon(L.divIcon({
                        className: 'worker-marker',
                        html: `<div style="background-color: #3498db; width: 24px; height: 24px; border-radius: 50%; border: 2px solid white; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 13px;">${careWorkers.find(w => w.id === marker.workerId).name.charAt(0)}</div>`,
                        iconSize: [24, 24],
                        iconAnchor: [12, 12]
                    }));
                }
            });
        }
        
        // Initialize the app
        document.addEventListener('DOMContentLoaded', function() {
            // Add markers for each care worker with current location
            careWorkers.forEach(worker => {
                if (worker.currentLocation) {
                    const marker = L.marker(worker.currentLocation, {
                        icon: L.divIcon({
                            className: 'worker-marker',
                            html: `<div style="background-color: #3498db; width: 24px; height: 24px; border-radius: 50%; border: 2px solid white; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 13px;">${worker.name.charAt(0)}</div>`,
                            iconSize: [24, 24],
                            iconAnchor: [12, 12]
                        })
                    }).addTo(map);
                    
                    marker.workerId = worker.id;
                    markers[worker.id] = marker;
                    
                    // Add click event to select worker
                    marker.on('click', () => selectWorker(worker.id));
                }
            });
            
            // Render initial views
            renderWorkersList();
            
            // Search functionality
            document.getElementById('searchWorkers').addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                const workerCards = document.querySelectorAll('.worker-card');
                
                workerCards.forEach(card => {
                    const workerName = card.querySelector('h6').textContent.toLowerCase();
                    if (workerName.includes(searchTerm)) {
                        card.style.display = 'flex';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>