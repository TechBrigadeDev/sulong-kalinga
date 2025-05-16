<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beneficiary Map</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/beneficiaryMap.css') }}">
    <!-- Leaflet CSS for maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body>

    @include('components.adminNavbar')
    @include('components.adminSidebar')

    <!-- Add Beneficiary Modal -->
    <div class="modal fade" id="addBeneficiaryModal" tabindex="-1" aria-labelledby="addBeneficiaryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addBeneficiaryModalLabel">Add Beneficiary Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addBeneficiaryForm">
                        <div class="mb-3">
                            <label for="beneficiarySelect" class="form-label">Select Beneficiary</label>
                            <select class="form-select" id="beneficiarySelect" required>
                                <option value="" selected disabled>Choose beneficiary...</option>
                                <!-- Options will be populated from JavaScript -->
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Location Coordinates</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text">Latitude</span>
                                <input type="text" class="form-control" id="pinLatitude" readonly>
                            </div>
                            <div class="input-group">
                                <span class="input-group-text">Longitude</span>
                                <input type="text" class="form-control" id="pinLongitude" readonly>
                            </div>
                            <div class="form-text">Click and drag the pin on the map to adjust position</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveBeneficiaryPin">Save Location</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit Beneficiary Modal -->
    <div class="modal fade" id="editBeneficiaryModal" tabindex="-1" aria-labelledby="editBeneficiaryModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editBeneficiaryModalLabel">Edit Beneficiary Location</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editBeneficiaryForm">
                        <div class="mb-3">
                            <label class="form-label">Beneficiary</label>
                            <input type="text" class="form-control" id="editBeneficiaryName" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Current Address</label>
                            <input type="text" class="form-control" id="editBeneficiaryAddress" readonly>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Location Coordinates</label>
                            <div class="input-group mb-2">
                                <span class="input-group-text">Latitude</span>
                                <input type="text" class="form-control" id="editPinLatitude">
                            </div>
                            <div class="input-group">
                                <span class="input-group-text">Longitude</span>
                                <input type="text" class="form-control" id="editPinLongitude">
                            </div>
                            <div class="form-text">Click and drag the pin on the map to adjust position</div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="saveEditedBeneficiaryPin">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <div class="home-section">
        <div class="text-left">BENEFICIARY MAP</div>
        <div class="container-fluid">
            <div class="row" id="home-content">
                <div class="col-lg-8">
                    <div id="map-container">
                        <div id="beneficiary-map"></div>
                        
                        <!-- Map Controls Container -->
                        <div class="map-controls-container">
                            <div class="map-control-group">
                                <button id="locate-me" class="btn btn-primary">
                                    <i class="bi bi-geo-alt-fill"></i> My Location
                                </button>
                            </div>
                            <div class="map-control-group">
                                <button id="add-pin-mode" class="btn btn-success">
                                    <i class="bi bi-pin-map-fill"></i> Add Pin
                                </button>
                                <button id="cancel-add-pin" class="btn btn-outline-secondary" style="display:none;">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </button>
                            </div>
                            <div class="map-control-group">
                                <button id="edit-pin-mode" class="btn btn-warning">
                                    <i class="bi bi-pencil-fill"></i> Edit Pin
                                </button>
                                <button id="cancel-edit-pin" class="btn btn-outline-secondary" style="display:none;">
                                    <i class="bi bi-x-circle"></i> Cancel
                                </button>
                            </div>
                        </div>
                        
                        <div id="beneficiary-details" class="beneficiary-details">
                            <h5 id="beneficiary-name"></h5>
                            <p><strong>Address:</strong> <span id="beneficiary-address"></span></p>
                            <p><strong>Contact:</strong> <span id="beneficiary-contact"></span></p>
                            <p><strong>Caregiver:</strong> <span id="beneficiary-caregiver"></span></p>
                            <p><strong>Caregiver Contact:</strong> <span id="beneficiary-caregiver-contact"></span></p>
                            <button id="close-details" class="btn btn-outline-secondary btn-sm mt-2">Close</button>
                        </div>
                        
                        <!-- Instruction box -->
                        <div id="map-instructions" class="map-instructions">
                            <h4 id="instruction-title"></h4>
                            <p id="instruction-text"></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="search-container">
                        <div class="input-group">
                            <input type="text" id="search-beneficiary" class="form-control" placeholder="Search beneficiaries...">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="bi bi-search"></i>
                            </button>
                        </div>
                    </div>
                    <div class="beneficiary-list">
                        <div class="list-group">
                            <!-- Sample beneficiary cards - these would be populated from your database -->
                            <div class="list-group-item beneficiary-card active" data-lat="12.5657" data-lng="124.9905" data-id="1">
                                <div class="beneficiary-card-content">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6>Juan Dela Cruz</h6>
                                        <span class="badge bg-primary badge-category">Senior</span>
                                    </div>
                                    <p>Brgy. Balnasan, Catarman, Northern Samar</p>
                                    <div class="contact-info">
                                        <div><i class="bi bi-telephone"></i> 09123456789</div>
                                        <div><i class="bi bi-person-hearts"></i> Maria Dela Cruz (09187654321)</div>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item beneficiary-card" data-lat="12.5750" data-lng="124.9800" data-id="2">
                                <div class="beneficiary-card-content">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6>Maria Santos</h6>
                                        <span class="badge bg-info badge-category">PWD</span>
                                    </div>
                                    <p>Brgy. Dalakit, Catarman, Northern Samar</p>
                                    <div class="contact-info">
                                        <div><i class="bi bi-telephone"></i> 09234567890</div>
                                        <div><i class="bi bi-person-hearts"></i> Pedro Santos (09211234567)</div>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item beneficiary-card" data-lat="12.5600" data-lng="125.0000" data-id="3">
                                <div class="beneficiary-card-content">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6>Pedro Reyes</h6>
                                        <span class="badge bg-primary badge-category">Senior</span>
                                    </div>
                                    <p>Brgy. UEP, Catarman, Northern Samar</p>
                                    <div class="contact-info">
                                        <div><i class="bi bi-telephone"></i> 09345678901</div>
                                        <div><i class="bi bi-person-hearts"></i> Juan Reyes (09339876543)</div>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item beneficiary-card" data-lat="12.5700" data-lng="124.9950" data-id="4">
                                <div class="beneficiary-card-content">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6>Lourdes Mendoza</h6>
                                        <span class="badge bg-info badge-category">PWD</span>
                                    </div>
                                    <p>Brgy. Ipil-ipil, Catarman, Northern Samar</p>
                                    <div class="contact-info">
                                        <div><i class="bi bi-telephone"></i> 09456789012</div>
                                        <div><i class="bi bi-person-hearts"></i> Carlos Mendoza (09448765432)</div>
                                    </div>
                                </div>
                            </div>
                            <div class="list-group-item beneficiary-card" data-lat="12.5800" data-lng="125.0050" data-id="5">
                                <div class="beneficiary-card-content">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6>Ricardo Gonzales</h6>
                                        <span class="badge bg-primary badge-category">Senior</span>
                                    </div>
                                    <p>Brgy. Macagtas, Catarman, Northern Samar</p>
                                    <div class="contact-info">
                                        <div><i class="bi bi-telephone"></i> 09567890123</div>
                                        <div><i class="bi bi-person-hearts"></i> Sofia Gonzales (09557654321)</div>
                                    </div>
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
    <!-- Leaflet JS for maps -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Initialize the map centered on Northern Samar
            const map = L.map('beneficiary-map').setView([12.5657, 124.9905], 13);
            
            // Add OpenStreetMap tiles
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);
            
            // Create a layer group for beneficiary markers
            const beneficiaryMarkers = L.layerGroup().addTo(map);
            
            // Variables for map interactions
            let manualPinMarker = null;
            let isAddPinMode = false;
            let isEditPinMode = false;
            let currentlyEditingMarker = null;
            const instructionsBox = document.getElementById('map-instructions');
            
            // Dummy database of beneficiaries (in a real app, this would come from your backend)
            const beneficiariesDatabase = [
                { id: 1, name: "Juan Dela Cruz", address: "Brgy. Balnasan, Catarman, Northern Samar", contact: "09123456789", category: "Senior", caregiver: "Maria Dela Cruz", caregiverContact: "09187654321", lat: 12.5657, lng: 124.9905 },
                { id: 2, name: "Maria Santos", address: "Brgy. Dalakit, Catarman, Northern Samar", contact: "09234567890", category: "PWD", caregiver: "Pedro Santos", caregiverContact: "09211234567", lat: 12.5750, lng: 124.9800 },
                { id: 3, name: "Pedro Reyes", address: "Brgy. UEP, Catarman, Northern Samar", contact: "09345678901", category: "Senior", caregiver: "Juan Reyes", caregiverContact: "09339876543", lat: 12.5600, lng: 125.0000 },
                { id: 4, name: "Lourdes Mendoza", address: "Brgy. Ipil-ipil, Catarman, Northern Samar", contact: "09456789012", category: "PWD", caregiver: "Carlos Mendoza", caregiverContact: "09448765432", lat: 12.5700, lng: 124.9950 },
                { id: 5, name: "Ricardo Gonzales", address: "Brgy. Macagtas, Catarman, Northern Samar", contact: "09567890123", category: "Senior", caregiver: "Sofia Gonzales", caregiverContact: "09557654321", lat: 12.5800, lng: 125.0050 }
            ];
            
            // Populate beneficiary select dropdown
            const beneficiarySelect = document.getElementById('beneficiarySelect');
            beneficiariesDatabase.forEach(beneficiary => {
                if (!document.querySelector(`.beneficiary-card[data-id="${beneficiary.id}"]`)) {
                    const option = document.createElement('option');
                    option.value = beneficiary.id;
                    option.textContent = `${beneficiary.name} (${beneficiary.address})`;
                    beneficiarySelect.appendChild(option);
                }
            });
            
            // Function to show instructions
            function showInstructions(title, text) {
                document.getElementById('instruction-title').textContent = title;
                document.getElementById('instruction-text').textContent = text;
                instructionsBox.style.display = 'block';
                
                // Hide after 5 seconds
                setTimeout(() => {
                    instructionsBox.style.display = 'none';
                }, 5000);
            }
            
            // Function to add a marker for a beneficiary
            function addBeneficiaryMarker(beneficiary) {
                const marker = L.marker([beneficiary.lat, beneficiary.lng], {
                    beneficiaryId: beneficiary.id,
                    draggable: false
                }).addTo(beneficiaryMarkers);
                
                marker.bindPopup(`
                    <b>${beneficiary.name}</b><br>
                    ${beneficiary.address}<br>
                    <small>Contact: ${beneficiary.contact}</small>
                `);
                
                marker.on('click', function() {
                    highlightBeneficiaryInList(beneficiary.id);
                    showBeneficiaryDetails(beneficiary);
                });
                
                // Store the beneficiary data in the marker for later reference
                marker.beneficiaryData = beneficiary;
                
                return marker;
            }
            
            // Function to highlight beneficiary in list
            function highlightBeneficiaryInList(beneficiaryId) {
                document.querySelectorAll('.beneficiary-card').forEach(card => {
                    card.classList.remove('active');
                    if (parseInt(card.dataset.id) === beneficiaryId) {
                        card.classList.add('active');
                        card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    }
                });
            }
            
            // Function to show beneficiary details
            function showBeneficiaryDetails(beneficiary) {
                document.getElementById('beneficiary-name').textContent = beneficiary.name;
                document.getElementById('beneficiary-address').textContent = beneficiary.address;
                document.getElementById('beneficiary-contact').textContent = beneficiary.contact;
                document.getElementById('beneficiary-caregiver').textContent = beneficiary.caregiver;
                document.getElementById('beneficiary-caregiver-contact').textContent = beneficiary.caregiverContact;
                document.getElementById('beneficiary-details').style.display = 'block';
            }
            
            // Add markers for all initial beneficiaries
            beneficiariesDatabase.forEach(beneficiary => {
                if (document.querySelector(`.beneficiary-card[data-id="${beneficiary.id}"]`)) {
                    addBeneficiaryMarker(beneficiary);
                }
            });
            
            // Add click event to beneficiary cards
            document.querySelectorAll('.beneficiary-card').forEach(card => {
                card.addEventListener('click', function() {
                    const beneficiaryId = parseInt(this.dataset.id);
                    const beneficiary = beneficiariesDatabase.find(b => b.id === beneficiaryId);
                    
                    if (beneficiary) {
                        // Highlight card
                        document.querySelectorAll('.beneficiary-card').forEach(c => {
                            c.classList.remove('active');
                        });
                        this.classList.add('active');
                        
                        // Find and focus on the marker
                        beneficiaryMarkers.eachLayer(function(marker) {
                            if (marker.options.beneficiaryId === beneficiaryId) {
                                map.setView(marker.getLatLng(), 15);
                                marker.openPopup();
                                showBeneficiaryDetails(beneficiary);
                            }
                        });
                    }
                });
            });
            
            // Locate Me button functionality
            document.getElementById('locate-me').addEventListener('click', function() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function(position) {
                        map.setView([position.coords.latitude, position.coords.longitude], 15);
                        L.marker([position.coords.latitude, position.coords.longitude])
                            .addTo(map)
                            .bindPopup('Your location')
                            .openPopup();
                    }, function(error) {
                        console.error('Geolocation error:', error);
                        alert('Unable to get your location. Please ensure location services are enabled.');
                    });
                } else {
                    alert('Geolocation is not supported by your browser.');
                }
            });
            
            // Add Pin Mode functionality
            document.getElementById('add-pin-mode').addEventListener('click', function() {
                isAddPinMode = true;
                this.style.display = 'none';
                document.getElementById('cancel-add-pin').style.display = 'inline-block';
                
                // Show instructions
                showInstructions('Add Pin Mode', 'Click on the map to place a new pin');
            });
            
            document.getElementById('cancel-add-pin').addEventListener('click', function() {
                isAddPinMode = false;
                this.style.display = 'none';
                document.getElementById('add-pin-mode').style.display = 'inline-block';
                if (manualPinMarker) {
                    map.removeLayer(manualPinMarker);
                    manualPinMarker = null;
                }
                instructionsBox.style.display = 'none';
            });
            
            // Edit Pin Mode functionality
            document.getElementById('edit-pin-mode').addEventListener('click', function() {
                isEditPinMode = true;
                this.style.display = 'none';
                document.getElementById('cancel-edit-pin').style.display = 'inline-block';
                
                // Show instructions
                showInstructions('Edit Pin Mode', 'Click on a pin to edit its position');
            });
            
            document.getElementById('cancel-edit-pin').addEventListener('click', function() {
                isEditPinMode = false;
                this.style.display = 'none';
                document.getElementById('edit-pin-mode').style.display = 'inline-block';
                if (currentlyEditingMarker) {
                    currentlyEditingMarker.setLatLng(currentlyEditingMarker.originalPosition);
                    currentlyEditingMarker.dragging.disable();
                    currentlyEditingMarker = null;
                }
                instructionsBox.style.display = 'none';
            });
            
            // Map click handler for manual pin placement
            map.on('click', function(e) {
                if (isAddPinMode) {
                    // Remove previous manual pin if exists
                    if (manualPinMarker) {
                        map.removeLayer(manualPinMarker);
                    }
                    
                    // Add new manual pin
                    manualPinMarker = L.marker(e.latlng, {
                        draggable: true
                    }).addTo(map);
                    
                    manualPinMarker.bindPopup('New beneficiary location').openPopup();
                    
                    // Update coordinates in form
                    document.getElementById('pinLatitude').value = e.latlng.lat.toFixed(6);
                    document.getElementById('pinLongitude').value = e.latlng.lng.toFixed(6);
                    
                    // Update coordinates when dragging
                    manualPinMarker.on('drag', function() {
                        const latLng = manualPinMarker.getLatLng();
                        document.getElementById('pinLatitude').value = latLng.lat.toFixed(6);
                        document.getElementById('pinLongitude').value = latLng.lng.toFixed(6);
                    });
                    
                    // Show the modal
                    const modal = new bootstrap.Modal(document.getElementById('addBeneficiaryModal'));
                    modal.show();
                }
            });
            
            // Map click handler for editing pins
            map.on('click', function(e) {
                if (isEditPinMode && currentlyEditingMarker) {
                    // If we're in edit mode and clicked elsewhere, save the position
                    const newLatLng = currentlyEditingMarker.getLatLng();
                    updateBeneficiaryCoordinates(currentlyEditingMarker.options.beneficiaryId, newLatLng.lat, newLatLng.lng);
                    
                    currentlyEditingMarker.dragging.disable();
                    currentlyEditingMarker = null;
                    isEditPinMode = false;
                    document.getElementById('cancel-edit-pin').style.display = 'none';
                    document.getElementById('edit-pin-mode').style.display = 'inline-block';
                    instructionsBox.style.display = 'none';
                }
            });
            
            // Marker click handler for editing
            map.on('popupopen', function(e) {
                if (isEditPinMode && e.popup._source.options.beneficiaryId) {
                    currentlyEditingMarker = e.popup._source;
                    currentlyEditingMarker.originalPosition = currentlyEditingMarker.getLatLng();
                    currentlyEditingMarker.dragging.enable();
                    
                    // Show edit modal
                    document.getElementById('editBeneficiaryName').value = currentlyEditingMarker.beneficiaryData.name;
                    document.getElementById('editBeneficiaryAddress').value = currentlyEditingMarker.beneficiaryData.address;
                    document.getElementById('editPinLatitude').value = currentlyEditingMarker.getLatLng().lat.toFixed(6);
                    document.getElementById('editPinLongitude').value = currentlyEditingMarker.getLatLng().lng.toFixed(6);
                    
                    const modal = new bootstrap.Modal(document.getElementById('editBeneficiaryModal'));
                    modal.show();
                }
            });
            
            // Save new beneficiary pin
            document.getElementById('saveBeneficiaryPin').addEventListener('click', function() {
                const beneficiaryId = parseInt(document.getElementById('beneficiarySelect').value);
                const lat = parseFloat(document.getElementById('pinLatitude').value);
                const lng = parseFloat(document.getElementById('pinLongitude').value);
                
                if (beneficiaryId && !isNaN(lat) && !isNaN(lng)) {
                    // In a real app, you would send this to your backend
                    updateBeneficiaryCoordinates(beneficiaryId, lat, lng);
                    
                    // Close the modal
                    bootstrap.Modal.getInstance(document.getElementById('addBeneficiaryModal')).hide();
                    
                    // Exit add pin mode
                    isAddPinMode = false;
                    document.getElementById('cancel-add-pin').style.display = 'none';
                    document.getElementById('add-pin-mode').style.display = 'inline-block';
                    if (manualPinMarker) {
                        map.removeLayer(manualPinMarker);
                        manualPinMarker = null;
                    }
                    instructionsBox.style.display = 'none';
                } else {
                    alert('Please select a beneficiary and ensure valid coordinates');
                }
            });
            
            // Save edited beneficiary pin
            document.getElementById('saveEditedBeneficiaryPin').addEventListener('click', function() {
                if (currentlyEditingMarker) {
                    const lat = parseFloat(document.getElementById('editPinLatitude').value);
                    const lng = parseFloat(document.getElementById('editPinLongitude').value);
                    
                    if (!isNaN(lat) && !isNaN(lng)) {
                        currentlyEditingMarker.setLatLng([lat, lng]);
                        updateBeneficiaryCoordinates(currentlyEditingMarker.options.beneficiaryId, lat, lng);
                        
                        // Close the modal
                        bootstrap.Modal.getInstance(document.getElementById('editBeneficiaryModal')).hide();
                        
                        // Exit edit mode
                        currentlyEditingMarker.dragging.disable();
                        currentlyEditingMarker = null;
                        isEditPinMode = false;
                        document.getElementById('cancel-edit-pin').style.display = 'none';
                        document.getElementById('edit-pin-mode').style.display = 'inline-block';
                        instructionsBox.style.display = 'none';
                    } else {
                        alert('Please enter valid coordinates');
                    }
                }
            });
            
            // Function to update beneficiary coordinates (simulated for this demo)
            function updateBeneficiaryCoordinates(beneficiaryId, lat, lng) {
                // Update in our dummy database
                const beneficiaryIndex = beneficiariesDatabase.findIndex(b => b.id === beneficiaryId);
                if (beneficiaryIndex !== -1) {
                    beneficiariesDatabase[beneficiaryIndex].lat = lat;
                    beneficiariesDatabase[beneficiaryIndex].lng = lng;
                }
                
                // Update in the UI
                const card = document.querySelector(`.beneficiary-card[data-id="${beneficiaryId}"]`);
                if (card) {
                    card.dataset.lat = lat;
                    card.dataset.lng = lng;
                }
                
                // Update or create the marker
                let markerExists = false;
                beneficiaryMarkers.eachLayer(function(marker) {
                    if (marker.options.beneficiaryId === beneficiaryId) {
                        marker.setLatLng([lat, lng]);
                        markerExists = true;
                    }
                });
                
                if (!markerExists) {
                    const beneficiary = beneficiariesDatabase.find(b => b.id === beneficiaryId);
                    if (beneficiary) {
                        addBeneficiaryMarker(beneficiary);
                    }
                }
                
                // In a real app, you would send this update to your backend
                console.log(`Updated beneficiary ${beneficiaryId} to coordinates ${lat}, ${lng}`);
            }
            
            // Search functionality
            document.getElementById('search-beneficiary').addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                document.querySelectorAll('.beneficiary-card').forEach(card => {
                    const text = card.textContent.toLowerCase();
                    card.style.display = text.includes(searchTerm) ? 'flex' : 'none';
                });
            });
            
            // Close details panel
            document.getElementById('close-details').addEventListener('click', function() {
                document.getElementById('beneficiary-details').style.display = 'none';
            });
            
            // Close modals when clicking the X button
            document.querySelectorAll('.btn-close').forEach(btn => {
                btn.addEventListener('click', function() {
                    if (isAddPinMode) {
                        isAddPinMode = false;
                        document.getElementById('cancel-add-pin').style.display = 'none';
                        document.getElementById('add-pin-mode').style.display = 'inline-block';
                        if (manualPinMarker) {
                            map.removeLayer(manualPinMarker);
                            manualPinMarker = null;
                        }
                        instructionsBox.style.display = 'none';
                    }
                    
                    if (isEditPinMode && currentlyEditingMarker) {
                        currentlyEditingMarker.setLatLng(currentlyEditingMarker.originalPosition);
                        currentlyEditingMarker.dragging.disable();
                        currentlyEditingMarker = null;
                        isEditPinMode = false;
                        document.getElementById('cancel-edit-pin').style.display = 'none';
                        document.getElementById('edit-pin-mode').style.display = 'inline-block';
                        instructionsBox.style.display = 'none';
                    }
                });
            });
        });
    </script>
</body>
</html>