<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Beneficiary Map | Care Worker</title>
    <link rel="icon" type="image/png" sizes="16x16" href="{{asset('images/favicon-white-16x16.png')}}">
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/beneficiaryMap.css') }}">
    <!-- Leaflet CSS for maps -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
</head>
<body>
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp
    @include('components.careWorkerNavbar')
    @include('components.careWorkerSidebar')

    <!-- Modals for Add/Edit Pin are commented out as per requirements -->

    <div class="home-section">
        <div class="text-left">{{ T::translate('BENEFICIARY MAP', 'MAPA NG BENEPISYARYO')}}</div>
        <div class="container-fluid">
            <div class="row" id="home-content">
                <div class="col-lg-8">
                    <div id="map-container">
                        <div id="beneficiary-map"></div>
                        <div id="beneficiary-details" class="beneficiary-details">
                            <h5 id="beneficiary-name"></h5>
                            <p><strong>{{ T::translate('Address:', 'Tirahan')}}</strong> <span id="beneficiary-address"></span></p>
                            <p><strong>Contact:</strong> <span id="beneficiary-contact"></span></p>
                            <p><strong>{{ T::translate('Care Worker:', 'Tagapag-alaga')}}</strong> <span id="beneficiary-caregiver"></span></p>
                            <p><strong>{{ T::translate('Care Worker Contact:', 'Contact ng Tagapag-alaga')}}</strong> <span id="beneficiary-caregiver-contact"></span></p>
                            <button id="close-details" class="btn btn-outline-secondary btn-sm mt-2">{{ T::translate('Close', 'Isara')}}</button>
                        </div>
                        <div id="map-instructions" class="map-instructions" style="display:none;">
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
                        <div class="list-group" id="beneficiary-list-group">
                            <!-- Beneficiary cards will be rendered by JS -->
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
const beneficiariesDatabase = @json($beneficiariesForMap);
const firstBeneficiaryId = @json($firstBeneficiaryId ?? null);

console.log('Loaded beneficiariesDatabase:', beneficiariesDatabase);
console.log('Loaded firstBeneficiaryId:', firstBeneficiaryId);

document.addEventListener('DOMContentLoaded', function() {
    // Initialize the map centered on Northern Samar
    const map = L.map('beneficiary-map').setView([12.5657, 124.9905], 13);

    // Add OpenStreetMap tiles
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(map);

    // Create a layer group for beneficiary markers
    const beneficiaryMarkers = L.layerGroup().addTo(map);

    // Store marker references for easy access
    const markerMap = {};

    // Function to add a marker for a beneficiary
    function addBeneficiaryMarker(beneficiary) {
        if (!beneficiary.lat || !beneficiary.lng) {
            console.warn('Skipping marker for beneficiary with missing lat/lng:', beneficiary);
            return;
        }
        const marker = L.marker([beneficiary.lat, beneficiary.lng], {
            beneficiaryId: parseInt(beneficiary.id), // force integer
            draggable: false
        }).addTo(beneficiaryMarkers);

        marker.bindPopup(`
            <b>${beneficiary.name}</b><br>
            ${beneficiary.address}<br>
            <small>Contact: ${beneficiary.contact}</small>
        `);

        marker.on('click', function() {
            console.log('Marker clicked for beneficiary:', beneficiary);
            highlightBeneficiaryInList(parseInt(beneficiary.id));
            showBeneficiaryDetails(beneficiary);
        });

        marker.beneficiaryData = { ...beneficiary, id: parseInt(beneficiary.id) }; // force integer
        markerMap[parseInt(beneficiary.id)] = marker;
        console.log('Added marker for beneficiary:', beneficiary.id, marker);
        return marker;
    }

    // Function to highlight beneficiary in list
    function highlightBeneficiaryInList(beneficiaryId) {
        console.log('Highlighting beneficiary in list:', beneficiaryId);
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
        console.log('Showing details for beneficiary:', beneficiary);
        document.getElementById('beneficiary-name').textContent = beneficiary.name;
        document.getElementById('beneficiary-address').textContent = beneficiary.address;
        document.getElementById('beneficiary-contact').textContent = beneficiary.contact;
        document.getElementById('beneficiary-caregiver').textContent = beneficiary.caregiver;
        document.getElementById('beneficiary-caregiver-contact').textContent = beneficiary.caregiverContact;
        document.getElementById('beneficiary-details').style.display = 'block';
    }

    // Render beneficiary cards in the list and bind click events
    function renderBeneficiaryList(beneficiaries) {
        const listGroup = document.getElementById('beneficiary-list-group');
        listGroup.innerHTML = '';
        beneficiaries.forEach(beneficiary => {
            console.log('Rendering card for beneficiary:', beneficiary);
            const card = document.createElement('div');
            card.className = 'list-group-item beneficiary-card';
            card.dataset.lat = beneficiary.lat;
            card.dataset.lng = beneficiary.lng;
            card.dataset.id = beneficiary.id; 
            card.innerHTML = `
                <div class="beneficiary-card-content">
                    <div class="d-flex w-100 justify-content-between">
                        <h6>${beneficiary.name}</h6>
                        <span class="badge ${beneficiary.category === 'PWD' ? 'bg-info' : 'bg-primary'} badge-category">${beneficiary.category}</span>
                    </div>
                    <p>${beneficiary.address}</p>
                    <div class="contact-info">
                        <div><i class="bi bi-telephone"></i> ${beneficiary.contact}</div>
                        <div><i class="bi bi-person-hearts"></i> ${beneficiary.caregiver} (${beneficiary.caregiverContact})</div>
                    </div>
                </div>
            `;
            // Bind the click event
            card.addEventListener('click', function() {
                const beneficiaryId = parseInt(this.dataset.id);
                const beneficiary = beneficiariesDatabase.find(b => parseInt(b.id) === beneficiaryId);

                console.log('Clicked beneficiaryId:', beneficiaryId, 'Beneficiary:', beneficiary);

                if (beneficiary) {
                    document.querySelectorAll('.beneficiary-card').forEach(c => c.classList.remove('active'));
                    this.classList.add('active');

                    let foundMarker = null;
                    let markerCount = 0;
                    beneficiaryMarkers.eachLayer(function(marker) {
                        markerCount++;
                        console.log('Checking marker:', marker, 'marker.options.beneficiaryId:', marker.options.beneficiaryId, 'target:', beneficiaryId);
                        if (parseInt(marker.options.beneficiaryId) === beneficiaryId) {
                            foundMarker = marker;
                        }
                    });

                    console.log('Total markers checked:', markerCount, 'Found marker:', foundMarker);

                    if (foundMarker) {
                        // Pan to marker, then open popup after a short delay
                        map.setView(foundMarker.getLatLng(), 15, { animate: true });
                        setTimeout(() => {
                            foundMarker.openPopup();
                            console.log('Opened popup for marker:', foundMarker);
                        }, 250);
                    } else {
                        alert('No marker found for beneficiaryId: ' + beneficiaryId);
                        console.error('No marker found for beneficiaryId:', beneficiaryId, 'Available markers:', markerMap);
                    }

                    showBeneficiaryDetails(beneficiary);
                } else {
                    alert('No beneficiary found for id: ' + beneficiaryId);
                    console.error('No beneficiary found for id:', beneficiaryId, 'beneficiariesDatabase:', beneficiariesDatabase);
                }
            });
            listGroup.appendChild(card);
        });
    }

    // Add markers for all beneficiaries FIRST
    beneficiariesDatabase.forEach(beneficiary => {
        addBeneficiaryMarker(beneficiary);
    });

    // Render the list (after markers are created)
    renderBeneficiaryList(beneficiariesDatabase);

    // --- Highlight and show first beneficiary by default ---
    if (firstBeneficiaryId) {
        const firstCard = document.querySelector(`.beneficiary-card[data-id="${firstBeneficiaryId}"]`);
        if (firstCard) {
            firstCard.classList.add('active');
            const beneficiary = beneficiariesDatabase.find(b => parseInt(b.id) === firstBeneficiaryId);
            showBeneficiaryDetails(beneficiary);
            // Center and open marker popup
            if (markerMap[firstBeneficiaryId]) {
                map.setView(markerMap[firstBeneficiaryId].getLatLng(), 15);
                markerMap[firstBeneficiaryId].openPopup();
                console.log('Auto-opened popup for first beneficiary:', markerMap[firstBeneficiaryId]);
            } else {
                console.warn('No marker found for firstBeneficiaryId:', firstBeneficiaryId);
            }
        } else {
            console.warn('No card found for firstBeneficiaryId:', firstBeneficiaryId);
        }
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
        console.log('Closed beneficiary details panel');
    });
});
</script>
</body>
</html>