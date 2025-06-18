<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Municipality Management</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/homeSection.css') }}">
    <link rel="stylesheet" href="{{ asset('css/municipality.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    @php
    use App\Helpers\TranslationHelper as T;
    @endphp
    @include('components.adminNavbar')
    @include('components.adminSidebar')
    @include('components.modals.deleteBarangay')
    @include('components.modals.deleteMunicipality')
    @include('components.modals.selectMunicipality')
    @include('components.modals.addMunicipality')
    @include('components.modals.addBarangay')
    @include('components.modals.editBarangay')

    <div class="home-section">
        <div class="text-left">{{ T::translate('MUNICIPALITY MANAGEMENT', 'PAMAMAHALA SA MUNISIPALIDAD')}}</div>
        <div class="container-fluid">
        <div class="row" id="home-content">

        <!-- Display success and error messages -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>
                <div>{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <div>{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="management-container">
            <!-- Search and Filter Row -->
            <div class="row align-items-center">
                <!-- Search Bar with Button -->
                <div class="col-12 col-md-6 mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control border-start-0" placeholder="{{ T::translate('Search barangay or municipality...', 'Maghanap ng Barangay o Munisipalidad...')}}" id="searchBar">
                        <button class="btn btn-primary" type="button" id="searchButton">
                            <i class="bi bi-search me-1"></i> {{ T::translate('Search', 'Maghanap')}}
                        </button>
                    </div>
                </div>

                <!-- Filter Dropdown -->
                <div class="col-12 col-md-6 mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-funnel"></i>
                        </span>
                        <select class="form-select border-start-0" id="filterDropdown">
                            <option value="">{{ T::translate('All Municipalities', 'Lahat ng Munisipalidad')}}</option>
                            @foreach($municipalities as $municipality)
                                <option value="{{ $municipality->municipality_id }}">{{ $municipality->municipality_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            <!-- Action Buttons Row -->
            <div class="row mb-2">
                <div class="col-12">
                    <div class="action-buttons">
                        <button type="button" class="btn btn-primary" onclick="openMunicipalityModal()">
                            <i class="bi bi-building-fill-add me-1"></i> {{ T::translate('Add/Edit Municipality', 'Magdagdag o I-edit ang Munisipalidad')}}
                        </button>
                        <button class="btn btn-primary" id="addBarangayButton" data-bs-toggle="modal" data-bs-target="#addBarangayModal">
                            <i class="bi bi-plus-circle me-1"></i>{{ T::translate('Add Barangay', 'Magdagdag ng Barangay')}} 
                        </button>
                        <button class="btn btn-danger" id="deleteMunicipalityButton">
                            <i class="bi bi-trash-fill me-1"></i> {{ T::translate('Delete Municipality', 'Tanggalin ang Munisipalidad')}}
                        </button>
                    </div>
                </div>
            </div>

            <div class="row" id="municipality">
                <div class="col-12">
                    <div class="table-container table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th scope="col">{{ T::translate('Municipality', 'Munisipalidad')}}</th>
                                    <th scope="col">Barangay</th>
                                    <th scope="col">{{ T::translate('Beneficiaries', 'Benepisyaryo')}}</th>
                                    <th scope="col">{{ T::translate('Actions', 'Aksyon')}}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($barangays as $barangay)
                                    <tr class="municipality-row" data-municipality="{{ $barangay->municipality->municipality_id }}">
                                        <td data-label="Municipality">{{ $barangay->municipality->municipality_name }}</td>
                                        <td data-label="Barangay">{{ $barangay->barangay_name }}</td>
                                        <td data-label="Beneficiaries">{{ $barangay->beneficiaries_count }}</td>
                                        <td data-label="Actions">
                                            <div class="action-icons">
                                                <i class="bi bi-pencil-square" 
                                                    data-id="{{ $barangay->barangay_id }}"
                                                    data-name="{{ $barangay->barangay_name }}"
                                                    data-municipality="{{ $barangay->municipality_id }}"
                                                    onclick="prepareEdit(this)"
                                                    title="Edit Barangay"></i>
                                                <i class="bi bi-trash" 
                                                    data-id="{{ $barangay->barangay_id }}" 
                                                    data-name="{{ $barangay->barangay_name }}"
                                                    onclick="openDeleteBarangayModal('{{ $barangay->barangay_id }}', '{{ $barangay->barangay_name }}')"
                                                    title="Delete Barangay"></i>
                                            </div>          
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="empty-state">
                                            <i class="bi bi-building-exclamation"></i>
                                            <h5>{{ T::translate('No barangays found', 'Walang barangay na nakita')}}</h5>
                                            <p class="text-muted">{{ T::translate('Add a new barangay to get started', 'Magdagdag ng bagong barangay upang makapag-simula')}}</p>
                                        </td>
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
        // Function to perform the search
        function performSearch() {
            const searchText = document.getElementById('searchBar').value.toLowerCase();
            const rows = document.querySelectorAll('.municipality-row');
            
            rows.forEach(row => {
                const barangayName = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                const municipalityName = row.querySelector('td:first-child').textContent.toLowerCase();
                
                if (barangayName.includes(searchText) || municipalityName.includes(searchText)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Search button click event
        document.getElementById('searchButton').addEventListener('click', function() {
            performSearch();
        });

        // Allow search on Enter key
        document.getElementById('searchBar').addEventListener('keyup', function(event) {
            if (event.key === 'Enter') {
                performSearch();
            }
        });
        
        // Filter functionality
        document.getElementById('filterDropdown').addEventListener('change', function() {
            const municipalityId = this.value;
            const rows = document.querySelectorAll('.municipality-row');
            
            if (!municipalityId) {
                // Show all rows if "All Municipalities" is selected
                rows.forEach(row => {
                    row.style.display = '';
                });
                return;
            }
            
            rows.forEach(row => {
                if (row.dataset.municipality === municipalityId) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });
        
        // Set up delete target
        function setDeleteTarget(element) {
            const id = element.dataset.id;
            const name = element.dataset.name;
            
            // Use string replacement instead of concatenation
            document.getElementById('deleteForm').action = `/admin/locations/barangays/${id}`;
            document.getElementById('deleteItemName').textContent = name;
        }
        
        // Prepare edit modal
        function prepareEdit(element) {
            const id = element.dataset.id;
            const name = element.dataset.name;
            const municipalityId = element.dataset.municipality;
            
            document.getElementById('editBarangayId').value = id;
            document.getElementById('editBarangayName').value = name;
            document.getElementById('editMunicipalityId').value = municipalityId;
            
            // Show the edit modal
            const modal = new bootstrap.Modal(document.getElementById('editBarangayModal'));
            modal.show();
        }
    </script>

    <script>
        // Fix for barangay deletion error messages
        document.addEventListener('DOMContentLoaded', function() {
            // Get references to modal components
            const deleteModal = document.getElementById('deleteBarangayModal');
            const messageElement = document.getElementById('barangayDeleteMessage');
            
            // Add direct DOM observer to catch any errors
            if (deleteModal) {
                // Create a new showError function that's guaranteed to work
                window.forceShowBarangayError = function(message) {
                    let msgElement = document.getElementById('barangayDeleteMessage');
                    
                    // If not found, create it
                    if (!msgElement) {
                        msgElement = document.createElement('div');
                        msgElement.id = 'barangayDeleteMessage';
                        msgElement.className = 'alert';
                        
                        // Add it to the modal body
                        const modalBody = deleteModal.querySelector('.modal-body');
                        if (modalBody) {
                            modalBody.insertBefore(msgElement, modalBody.firstChild);
                        }
                    }
                    
                    // Set message using textContent for consistency with original
                    msgElement.textContent = message;
                    msgElement.classList.remove('d-none', 'alert-success');
                    msgElement.classList.add('alert-danger');
                    msgElement.style.display = 'block';
                };
                
                // Replace the fetch handler for the barangay delete button
                const confirmButton = document.getElementById('confirmBarangayDeleteButton');
                if (confirmButton) {
                    // Clone button to remove all existing event handlers
                    const newButton = confirmButton.cloneNode(true);
                    confirmButton.parentNode.replaceChild(newButton, confirmButton);
                    
                    // Add new event handler
                    newButton.addEventListener('click', function() {
                        const passwordInput = document.getElementById('barangayDeletePasswordInput');
                        const idInput = document.getElementById('barangayIdToDelete');
                        
                        if (!passwordInput || !idInput) {
                            forceShowBarangayError('Form elements not found');
                            return;
                        }
                        
                        const password = passwordInput.value.trim();
                        const barangayId = idInput.value;
                        
                        if (!password) {
                            forceShowBarangayError('{{ T::translate('Please enter your password to confirm deletion.', 'Mangyaring ilagay ang iyonhg password upang kumpirmahin ang pagtanggal.')}}');
                            return;
                        }
                        
                        // Show loading state
                        this.disabled = true;
                        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...';
                        
                        // Send delete request
                        fetch(`/admin/locations/barangays/${barangayId}`, {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ password: password })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Success handling remains the same
                                const modalBody = deleteModal.querySelector('.modal-body');
                                if (modalBody) {
                                    modalBody.innerHTML = `
                                        <div class="text-center mb-2">
                                            <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                                            <h5 class="mt-3 text-success">{{ T::translate('Success', 'Tagumpay')}}!</h5>
                                            <p>{{ T::translate('The barangay has been successfully deleted.', 'Ang barangay ay matagumpay na na-tanggal')}}</p>
                                            <p class="small text-muted">{{ T::translate('The page will reload shortly...', 'Ang page ay magre-reload nang ilang sandali...')}}</p>
                                    </div>
                                    `;
                                }
                                
                                // Hide delete button, update cancel button
                                this.style.display = 'none';
                                const cancelButton = document.getElementById('cancelDeleteButton');
                                if (cancelButton) {
                                    cancelButton.textContent = 'Close';
                                }
                                
                                // Reload page after delay
                                setTimeout(() => {
                                    window.location.reload();
                                }, 2000);
                            } else {
                                // Here's where we ensure the error is shown
                                forceShowBarangayError(data.message || 'Failed to delete barangay.');
                                this.disabled = false;
                                this.innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate('Delete Barangay', 'Tanggalin ang Barangay')}}';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            forceShowBarangayError('{{ T::translate('An unexpected error occurred.', 'Isang hindi inaasahang error ang naganap.')}}');
                            this.disabled = false;
                            this.innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate('Delete Barangay', 'Tanggalin ang Barangay')}}';
                        });
                    });
                }
            }
        });
    </script>
</body>
</html>