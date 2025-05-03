<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Municipality Management</title>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/boxicons.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/municipality.css') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    @include('components.adminNavbar')
    @include('components.adminSidebar')
    @include('components.modals.deleteBarangay')
    @include('components.modals.deleteMunicipality')
    @include('components.modals.selectMunicipality')
    @include('components.modals.addMunicipality')
    @include('components.modals.addBarangay')
    @include('components.modals.editBarangay')

    <div class="home-section">
        <div class="text-left">MUNICIPALITY MANAGEMENT</div>

    <!-- Display success and error messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-circle me-2"></i>
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

        <div class="container-fluid text-center">
        <!-- Search and Filter Row -->
        <div class="row mb-3 align-items-center">
            <!-- Search Bar with Button -->
            <div class="col-12 col-md-6 mb-2">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-search"></i>
                    </span>
                    <input type="text" class="form-control" placeholder="Search barangay or municipality..." id="searchBar">
                    <button class="btn btn-primary" type="button" id="searchButton">
                        Search
                    </button>
                </div>
            </div>

            <!-- Filter Dropdown -->
            <div class="col-12 col-md-6 mb-2">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="bi bi-funnel"></i>
                    </span>
                    <select class="form-select" id="filterDropdown">
                        <option value="">All Municipalities</option>
                        @foreach($municipalities as $municipality)
                            <option value="{{ $municipality->municipality_id }}">{{ $municipality->municipality_name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        <!-- Action Buttons Row -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex gap-2 justify-content-center">
                <button type="button" class="btn btn-primary flex-grow-1" onclick="openMunicipalityModal()">
                    <i class="bi bi-building-fill-add" style="padding-right: 10px;"></i> Add / Edit Municipality
                </button>
                <button class="btn btn-primary flex-grow-1" id="addBarangayButton" data-bs-toggle="modal" data-bs-target="#addBarangayModal">
                    <i class="bi bi-plus" style="padding-right: 10px;"></i> Add Barangay
                </button>
                <button class="btn btn-danger flex-grow-1" id="deleteMunicipalityButton">
                    <i class="bi bi-trash" style="padding-right: 10px;"></i>Delete Municipality
                </button>
                </div>
            </div>
        </div>

            <div class="row" id="municipality">
                <div class="col-12">
                    <div class="table-responsive">
                        <table class="table table-striped w-100 align-middle">
                            <thead>
                                <tr>
                                    <th scope="col">Municipality</th>
                                    <th scope="col">Barangay</th>
                                    <th scope="col">Beneficiaries</th>
                                    <th scope="col">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($barangays as $barangay)
                                    <tr class="municipality-row" data-municipality="{{ $barangay->municipality->municipality_id }}">
                                        <td>{{ $barangay->municipality->municipality_name }}</td>
                                        <td>{{ $barangay->barangay_name }}</td>
                                        <td>{{ $barangay->beneficiaries_count }}</td>
                                        <td>
                                            <div class="action-icons">
                                            <i class="bi bi-trash p-" 
                                                data-id="{{ $barangay->barangay_id }}" 
                                                data-name="{{ $barangay->barangay_name }}"
                                                style="cursor: pointer;"
                                                onclick="openDeleteBarangayModal('{{ $barangay->barangay_id }}', '{{ $barangay->barangay_name }}')"></i>
                                                <i class="bi bi-pencil-square p-0" 
                                                data-id="{{ $barangay->barangay_id }}"
                                                data-name="{{ $barangay->barangay_name }}"
                                                data-municipality="{{ $barangay->municipality_id }}"
                                                onclick="prepareEdit(this)"></i>
                                            </div>          
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center">No barangays found</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Add Municipality Modal --}}
    {{-- @include('components.modals.addMunicipality') --}}
    
    {{-- Edit Barangay Modal --}}
    {{-- @include('components.modals.editBarangay') --}}

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
            document.getElementById('deleteForm').action = "{{ route('admin.locations.barangays.delete', ['id' => ':id']) }}".replace(':id', id);
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
            // Debug console
            console.log('Debug: Fixing barangay deletion messages');
            
            // Get references to modal components
            const deleteModal = document.getElementById('deleteBarangayModal');
            const messageElement = document.getElementById('barangayDeleteMessage');
            
            // Log what we found
            console.log('Modal found:', !!deleteModal);
            console.log('Message element found:', !!messageElement);
            
            // Add direct DOM observer to catch any errors
            if (deleteModal) {
                // Create a new showError function that's guaranteed to work
                window.forceShowBarangayError = function(message) {
                    console.log('Showing error:', message);
                    
                    // Find or create error message element
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
                            forceShowBarangayError('Please enter your password to confirm deletion.');
                            return;
                        }
                        
                        // Show loading state
                        this.disabled = true;
                        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...';
                        
                        // Send delete request
                        fetch(`{{ route('admin.locations.barangays.delete', ['id' => ':id']) }}`.replace(':id', barangayId), {
                            method: 'DELETE',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ password: password })
                        })
                        .then(response => response.json())
                        .then(data => {
                            console.log("Server response:", data);
                            
                            if (data.success) {
                                // Success handling remains the same
                                const modalBody = deleteModal.querySelector('.modal-body');
                                if (modalBody) {
                                    modalBody.innerHTML = `
                                        <div class="text-center mb-4">
                                            <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                                            <h5 class="mt-3 text-success">Success!</h5>
                                            <p>The barangay has been successfully deleted.</p>
                                            <p class="small text-muted">The page will reload shortly...</p>
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
                                this.innerHTML = '<i class="bi bi-trash-fill"></i> Delete Barangay';
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            forceShowBarangayError('An unexpected error occurred.');
                            this.disabled = false;
                            this.innerHTML = '<i class="bi bi-trash-fill"></i> Delete Barangay';
                        });
                    });
                }
            }
        });
    </script>
</body>
</html>