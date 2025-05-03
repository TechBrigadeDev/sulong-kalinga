<!-- filepath: c:\xampp\htdocs\sulong_kalinga\resources\views\components\modals\addMunicipality.blade.php -->
<div class="modal fade" id="addMunicipalityModal" tabindex="-1" aria-labelledby="addMunicipalityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addMunicipalityModalLabel">Municipality Management</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Error messages container -->
                <div id="municipalityErrorContainer" class="alert alert-danger d-none"></div>
                
                <!-- Success message container -->
                <div id="municipalitySuccessContainer" class="d-none">
                    <p class="text-success">
                        <i class="bi bi-check-circle"></i>
                        <strong>Success!</strong> <span id="municipalitySuccessMessage">The municipality has been processed successfully.</span>
                    </p>
                    <p>The page will reload shortly.</p>
                </div>
                
                <!-- Action selection stage -->
                <div id="actionSelection">
                    <h5 class="mb-3">What would you like to do?</h5>
                    <div class="d-grid gap-3">
                        <button type="button" class="btn btn-primary btn-lg text-start p-3" id="addNewBtn">
                            <i class="bi bi-plus-circle fs-4 me-2"></i>
                            <span class="align-middle">Add New Municipality</span>
                            <p class="text-white-80 mb-0 mt-1 small">Create a new municipality in Northern Samar province</p>
                        </button>
                        
                        <button type="button" class="btn btn-info btn-lg text-start p-3" id="editExistingBtn">
                            <i class="bi bi-pencil-square fs-4 me-2"></i>
                            <span class="align-middle">Edit Existing Municipality</span>
                            <p class="text-white-80 mb-0 mt-1 small">Modify the name of an existing municipality</p>
                        </button>
                    </div>
                </div>
                
                <!-- Municipality selection for edit -->
                <div id="editSelection" class="d-none">
                    
                    <h5 class="mb-3">Select Municipality to Edit</h5>
                    <div class="form-floating mb-3">
                        <select class="form-select" id="municipalitySelect">
                            <option value="" selected disabled>Select a municipality</option>
                            @foreach($municipalities as $municipality)
                                <option value="{{ $municipality->municipality_id }}">{{ $municipality->municipality_name }}</option>
                            @endforeach
                        </select>
                        <label for="municipalitySelect">Municipality</label>
                    </div>
                    
                    <div class="d-grid">
                        <button class="btn btn-primary" id="continueToEditBtn" disabled>
                            <i class="bi bi-pencil-square"></i> Continue to Edit
                        </button>
                    </div>
                </div>
                
                <!-- Actual form for add/edit -->
                <form id="addMunicipalityForm" class="d-none">
                    @csrf
                    <input type="hidden" id="municipalityId" name="municipality_id" value="">
                    <input type="hidden" id="municipalityAction" name="action" value="add">
                    
                    <div id="formContent">
                        
                        <div class="mb-3">
                            <label for="municipalityName" class="form-label">Municipality Name</label>
                            <input type="text" class="form-control" id="municipalityName" name="municipality_name" required
                                pattern="^[A-Z][A-Za-z][A-Za-z0-9\s\.\-']*$" 
                                title="Municipality name must start with a capital letter, contain at least 2 letters, and can only include letters, numbers, spaces, periods, hyphens, and apostrophes">
                                <div class="form-text">Enter the name of the municipality (e.g., Catarman, Las Navas). Must start with a capital letter and contain at least 2 letters.</div>
                        </div>
                        
                        <div class="alert alert-info">
                            <small><i class="bi bi-info-circle me-1"></i> This municipality will be assigned to Northern Samar province.</small>
                        </div>
                        
                        <div class="d-grid mt-3">
                            <button type="button" class="btn btn-primary" id="submitMunicipality">
                                <i class="bi bi-floppy"></i> <span id="submitButtonText">Save Municipality</span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary d-none" id="backButton">
                    <i class="bi bi-arrow-bar-left"></i> Back
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelAddButton">Cancel</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addMunicipalityForm');
    const actionSelection = document.getElementById('actionSelection');
    const editSelection = document.getElementById('editSelection');
    const formContent = document.getElementById('formContent');
    const errorContainer = document.getElementById('municipalityErrorContainer');
    const successContainer = document.getElementById('municipalitySuccessContainer');
    const successMessage = document.getElementById('municipalitySuccessMessage');
    const submitButton = document.getElementById('submitMunicipality');
    const submitButtonText = document.getElementById('submitButtonText');
    const cancelButton = document.getElementById('cancelAddButton');
    const modalTitle = document.getElementById('addMunicipalityModalLabel');
    const addNewBtn = document.getElementById('addNewBtn');
    const editExistingBtn = document.getElementById('editExistingBtn');
    const municipalitySelect = document.getElementById('municipalitySelect');
    const continueToEditBtn = document.getElementById('continueToEditBtn');
    const backButton = document.getElementById('backButton');
    
    // Add input event listener to clear errors when typing
    document.getElementById('municipalityName').addEventListener('input', function() {
        // Clear error container when user types
        errorContainer.innerHTML = '';
        errorContainer.classList.add('d-none');
    });
    
    // Function to open the municipality management modal
    window.openMunicipalityModal = function() {
        resetModal();
        showActionSelection();
        
        // Show modal
        const modal = new bootstrap.Modal(document.getElementById('addMunicipalityModal'));
        modal.show();
    };
    
    // Reset the entire modal state
    function resetModal() {
        // Reset form
        form.reset();
        document.getElementById('municipalityId').value = '';
        document.getElementById('municipalityAction').value = 'add';
        
        // Hide all sections
        actionSelection.classList.add('d-none');
        editSelection.classList.add('d-none');
        form.classList.add('d-none');
        errorContainer.classList.add('d-none');
        successContainer.classList.add('d-none');
        
        // Reset UI elements
        modalTitle.textContent = 'Municipality Management';
        cancelButton.textContent = 'Cancel';
        
        // Reset municipality select
        if (municipalitySelect) {
            municipalitySelect.selectedIndex = 0;
            continueToEditBtn.disabled = true;
        }
    }
    
    // Show the action selection screen
    function showActionSelection() {
        actionSelection.classList.remove('d-none');
        editSelection.classList.add('d-none');
        form.classList.add('d-none');
        errorContainer.classList.add('d-none');
        
        // Hide back button on first screen
        backButton.classList.add('d-none');
    }
    
    // Show the municipality selection screen for editing
    function showEditSelection() {
        actionSelection.classList.add('d-none');
        editSelection.classList.remove('d-none');
        form.classList.add('d-none');
        errorContainer.classList.add('d-none');
        
        // Show back button and set its action
        backButton.classList.remove('d-none');
        backButton.onclick = showActionSelection;
    }
    
    // Show the form for adding or editing
    function showForm(action, municipalityId = '', municipalityName = '') {
        actionSelection.classList.add('d-none');
        editSelection.classList.add('d-none');
        form.classList.remove('d-none');
        errorContainer.classList.add('d-none');
        
        // Update form action and data
        document.getElementById('municipalityAction').value = action;
        document.getElementById('municipalityId').value = municipalityId;
        document.getElementById('municipalityName').value = municipalityName;
        
        // Update UI based on action
        if (action === 'edit') {
            modalTitle.textContent = 'Edit Municipality';
            submitButtonText.textContent = 'Update Municipality';
            submitButton.innerHTML = '<i class="bi bi-pencil-square"></i> Update Municipality';
            
            // Show back button and set its action to go back to municipality selection
            backButton.classList.remove('d-none');
            backButton.onclick = showEditSelection;
        } else {
            modalTitle.textContent = 'Add New Municipality';
            submitButtonText.textContent = 'Add Municipality';
            submitButton.innerHTML = '<i class="bi bi-plus"></i> Add Municipality';
            
            // Show back button and set its action to go back to main selection
            backButton.classList.remove('d-none');
            backButton.onclick = showActionSelection;
        }
    }
    
    // Show success message and hide form
    function showAddSuccess(message) {
        // Hide all sections
        actionSelection.classList.add('d-none');
        editSelection.classList.add('d-none');
        form.classList.add('d-none');
        errorContainer.classList.add('d-none');
        
        // Show success message
        successContainer.classList.remove('d-none');
        if (message) {
            successMessage.textContent = message;
        }
        
        // Update buttons
        backButton.classList.add('d-none');
        cancelButton.textContent = 'Close';
        
        // Set timeout to reload the page
        setTimeout(function() {
            window.location.reload();
        }, 2000);
    }
    
    // Client-side validation
    function validateMunicipalityForm() {
        const municipalityName = document.getElementById('municipalityName').value.trim();
        
        if (!municipalityName) {
            showDetailedError('Municipality name is required.');
            return false;
        }
        
        // Fixed pattern - removed extra escaping
        const namePattern = /^[A-Z][A-Za-z][A-Za-z0-9\s\.\-']*$/;
        if (!namePattern.test(municipalityName)) {
            showDetailedError('Municipality name must start with a capital letter, contain at least 2 letters, and can only include letters, numbers, spaces, periods, hyphens, and apostrophes.');
            return false;
        }
        
        if (municipalityName.length > 100) {
            showDetailedError('Municipality name cannot exceed 100 characters.');
            return false;
        }
        
        return true;
    }
    
    // Show a more detailed error message with guidance
    function showDetailedError(message) {
        errorContainer.classList.remove('d-none');
        
        const errorContent = document.createElement('div');
        
        // Main error message
        const errorMessage = document.createElement('p');
        errorMessage.className = 'mb-2';
        errorMessage.innerHTML = `<i class="bi bi-exclamation-circle text-danger me-1"></i> ${message}`;
        errorContent.appendChild(errorMessage);
        
        // Add guidance for name errors
        if (message.includes('Municipality name')) {
            const guidance = document.createElement('div');
            guidance.className = 'mt-2 pt-2 border-top';
            guidance.innerHTML = `
                <small class="text-muted">
                    <strong>Municipality name tips:</strong>
                    <ul class="mt-1 mb-0">
                        <li>Must start with a capital letter (e.g., "Catarman" not "catarman")</li>
                        <li>Must contain at least 2 letters</li>
                        <li>Can include letters, numbers, spaces, periods, hyphens, and apostrophes</li>
                        <li>Example: "Las Navas", "San Roque", "Bobon"</li>
                    </ul>
                </small>
            `;
            errorContent.appendChild(guidance);
        }
        
        // Replace error container content
        errorContainer.innerHTML = '';
        errorContainer.appendChild(errorContent);
    }
    
    // Enhanced error display with better formatting and more detailed messages
    function showServerErrors(data, isEditing) {
        errorContainer.classList.remove('d-none');
        
        // Create a container for errors
        const errorContent = document.createElement('div');
        
        if (data.errors) {
            const errorList = document.createElement('ul');
            errorList.style.marginBottom = '0';
            
            let hasNameError = false;
            
            for (const key in data.errors) {
                data.errors[key].forEach(error => {
                    // Replace generic error messages with more detailed ones
                    let enhancedError = error;
                    
                    if (key === 'municipality_name') {
                        hasNameError = true;
                        
                        // Enhance specific error messages
                        if (error.includes('required')) {
                            enhancedError = 'The municipality name is required.';
                        } 
                        else if (error.includes('format is invalid') || error.includes('regex')) {
                            enhancedError = 'Municipality name must start with a capital letter, contain at least 2 letters, and can only include letters, numbers, spaces, periods, hyphens, and apostrophes.';
                        }
                        else if (error.includes('already exists') || error.includes('has already been taken') || error.includes('unique')) {
                            enhancedError = isEditing 
                                ? 'This municipality name is already used by another municipality.' 
                                : 'This municipality name already exists in the database.';
                        }
                        else if (error.includes('exceed')) {
                            enhancedError = 'Municipality name cannot exceed 100 characters.';
                        }
                    }
                    
                    const li = document.createElement('li');
                    li.textContent = enhancedError;
                    errorList.appendChild(li);
                });
            }
            
            errorContent.appendChild(errorList);
            
            // Add additional guidance for name errors
            if (hasNameError) {
                const guidance = document.createElement('div');
                guidance.className = 'mt-2 pt-2 border-top';
                guidance.innerHTML = `
                    <small class="text-muted">
                        <strong>Municipality name tips:</strong>
                        <ul class="mt-1 mb-0">
                            <li>Must start with a capital letter (e.g., "Catarman" not "catarman")</li>
                            <li>Must contain at least 2 letters</li>
                            <li>Can include letters, numbers, spaces, periods, hyphens, and apostrophes</li>
                            <li>Example: "Las Navas", "San Roque", "Bobon"</li>
                        </ul>
                    </small>
                `;
                errorContent.appendChild(guidance);
            }
        } else {
            errorContent.textContent = data.message || 'An error occurred';
        }
        
        // Add a "fix and try again" message
        const fixMessage = document.createElement('p');
        fixMessage.className = 'mt-2 mb-0 text-primary small';
        fixMessage.innerHTML = '<i class="bi bi-info-circle"></i> Fix the errors above and try again.';
        errorContent.appendChild(fixMessage);
        
        // Replace error container content
        errorContainer.innerHTML = '';
        errorContainer.appendChild(errorContent);
    }
    
    // Add button click handler
    addNewBtn.addEventListener('click', function() {
        showForm('add');
    });
    
    // Edit button click handler
    editExistingBtn.addEventListener('click', function() {
        showEditSelection();
    });
    
    // Municipality select change handler
    municipalitySelect.addEventListener('change', function() {
        continueToEditBtn.disabled = !this.value;
    });
    
    // Continue to edit button click handler
    continueToEditBtn.addEventListener('click', function() {
        const municipalityId = municipalitySelect.value;
        const municipalityName = municipalitySelect.options[municipalitySelect.selectedIndex].text;
        
        if (municipalityId) {
            showForm('edit', municipalityId, municipalityName);
        }
    });
    
    // Submit button click handler
    submitButton.addEventListener('click', function() {
        // Reset error container
        errorContainer.innerHTML = '';
        errorContainer.classList.add('d-none');
        
        // Validate form fields before submission
        if (!validateMunicipalityForm()) {
            return;
        }
        
        // Show loading state
        submitButton.disabled = true;
        const isEditing = document.getElementById('municipalityAction').value === 'edit';
        submitButton.innerHTML = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> ${isEditing ? 'Updating...' : 'Adding...'}`;
        
        // Create form data
        const formData = new FormData(form);
        
        // Determine the endpoint based on action
        const endpoint = isEditing 
            ? '{{ route("admin.locations.municipalities.update") }}' 
            : '{{ route("admin.locations.municipalities.store") }}';
            
        // Send AJAX request
        fetch(endpoint, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message before redirecting
                showAddSuccess(data.message);
            } else {
                // Show enhanced validation errors
                showServerErrors(data, isEditing);
                
                // Reset button
                submitButton.disabled = false;
                submitButton.innerHTML = isEditing 
                    ? '<i class="bi bi-pencil-square"></i> Update Municipality' 
                    : '<i class="bi bi-plus"></i> Add Municipality';
            }
        })
        .catch(error => {
            // Handle network errors
            errorContainer.classList.remove('d-none');
            errorContainer.textContent = 'Network error. Please try again.';
            console.error('Error:', error);
            
            // Reset button
            submitButton.disabled = false;
            submitButton.innerHTML = isEditing 
                ? '<i class="bi bi-pencil-square"></i> Update Municipality' 
                : '<i class="bi bi-plus"></i> Add Municipality';
        });
    });
    
    // Ensure form submission is handled by our click event
    form.addEventListener('submit', function(e) {
        e.preventDefault();
    });
});
</script>