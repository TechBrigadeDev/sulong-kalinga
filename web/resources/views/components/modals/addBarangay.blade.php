<!-- filepath: c:\xampp\htdocs\sulong_kalinga\resources\views\components\modals\addBarangay.blade.php -->
<div class="modal fade" id="addBarangayModal" tabindex="-1" aria-labelledby="addBarangayModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBarangayModalLabel">Add New Barangay</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Error messages container -->
                <div id="barangayErrorContainer" class="alert alert-danger d-none"></div>
                
                <!-- Success message container (new) -->
                <div id="barangaySuccessContainer" class="d-none">
                    <p class="text-success">
                        <i class="bi bi-check-circle"></i>
                        <strong>Success!</strong> <span id="barangaySuccessMessage">The barangay has been added successfully.</span>
                    </p>
                    <p>The page will reload shortly.</p>
                </div>
                
                <form id="addBarangayForm">
                    @csrf
                    <div id="barangayFormContent">
                    <div class="mb-3">
                        <label for="barangayMunicipalitySelect" class="form-label">Municipality</label>
                        <select class="form-select" id="barangayMunicipalitySelect" name="municipality_id" required>
                            <option value="">Select Municipality</option>
                            @foreach($municipalities as $municipality)
                                <option value="{{ $municipality->municipality_id }}">{{ $municipality->municipality_name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">Select the municipality this barangay belongs to.</div>
                    </div>
                        
                        <div class="mb-3">
                            <label for="barangayName" class="form-label">Barangay Name</label>
                            <input type="text" class="form-control" id="barangayName" name="barangay_name" required
                                   pattern="^[A-Z][A-Za-z][A-Za-z0-9\s\.\-']*$" 
                                   title="Barangay name must start with a capital letter, contain at least 2 letters, and can only include letters, numbers, spaces, periods, hyphens, and apostrophes">
                            <div class="form-text">Enter the name of the barangay (e.g., San Roque, Zone 4, Barangay 1). Must start with a capital letter and contain at least 2 letters.</div>
                        </div>
                        
                        <div class="alert alert-warning">
                            <small><i class="bi bi-exclamation-circle"></i> Barangay names must be unique within their municipality.</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelBarangayButton">Cancel</button>
                <button type="button" class="btn btn-primary" id="submitBarangay">
                    <i class="bi bi-plus"></i> Add Barangay
                </button>
            </div>
        </div>
    </div>
</div>

<!-- filepath: c:\xampp\htdocs\sulong_kalinga\resources\views\components\modals\addBarangay.blade.php -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('addBarangayForm');
    const formContent = document.getElementById('barangayFormContent');
    const errorContainer = document.getElementById('barangayErrorContainer');
    const successContainer = document.getElementById('barangaySuccessContainer');
    const successMessage = document.getElementById('barangaySuccessMessage');
    const submitButton = document.getElementById('submitBarangay');
    const cancelButton = document.getElementById('cancelBarangayButton');
    
    // FIXED: Add input event listener for barangayName
    document.getElementById('barangayName').addEventListener('input', function() {
        clearErrors();
    });
    
    // FIXED: Use the correct selector for municipality dropdown
    document.getElementById('barangayMunicipalitySelect').addEventListener('change', function() {
        clearErrors();
    });
    
    // REMOVED: Incorrect event listener for non-existent element
    // document.getElementById('municipalityId').addEventListener('change', function() {
    //     errorContainer.innerHTML = '';
    //     errorContainer.classList.add('d-none');
    // });
    
    // ADDED: Function to clear errors consistently
    function clearErrors() {
        errorContainer.innerHTML = '';
        errorContainer.classList.add('d-none');
    }
    
    // Client-side validation
    function validateBarangayForm() {
        const municipalityId = document.getElementById('barangayMunicipalitySelect').value;
        const barangayName = document.getElementById('barangayName').value.trim();
        
        if (!municipalityId) {
            showDetailedError('Please select a municipality.');
            return false;
        }
        
        if (!barangayName) {
            showDetailedError('Barangay name is required.');
            return false;
        }
        
        // Fixed pattern - removed extra escaping
        const namePattern = /^[A-Z][A-Za-z][A-Za-z0-9\s\.\-']*$/;
        if (!namePattern.test(barangayName)) {
            showDetailedError('Barangay name must start with a capital letter, contain at least 2 letters, and can only include letters, numbers, spaces, periods, hyphens, and apostrophes.');
            return false;
        }
        
        if (barangayName.length > 100) {
            showDetailedError('Barangay name cannot exceed 100 characters.');
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
        if (message.includes('Barangay name')) {
            const guidance = document.createElement('div');
            guidance.className = 'mt-2 pt-2 border-top';
            guidance.innerHTML = `
                <small class="text-muted">
                    <strong>Barangay name tips:</strong>
                    <ul class="mt-1 mb-0">
                        <li>Must start with a capital letter (e.g., "San Roque" not "san roque")</li>
                        <li>Must contain at least 2 letters</li>
                        <li>Can include letters, numbers, spaces, periods, hyphens, and apostrophes</li>
                        <li>Example: "San Roque", "Barangay 12", "Zone 4"</li>
                    </ul>
                </small>
            `;
            errorContent.appendChild(guidance);
        }
        
        // Replace error container content
        errorContainer.innerHTML = '';
        errorContainer.appendChild(errorContent);
    }
    
    // Show success message and hide form
    function showBarangaySuccess(message) {
        // Hide form content and error messages
        formContent.classList.add('d-none');
        errorContainer.classList.add('d-none');
        
        // Show success message
        successContainer.classList.remove('d-none');
        if (message) {
            successMessage.textContent = message;
        }
        
        // Update buttons
        submitButton.classList.add('d-none');
        cancelButton.textContent = 'Close';
        
        // Set timeout to reload the page
        setTimeout(function() {
            window.location.reload();
        }, 2000);
    }
    
    submitButton.addEventListener('click', function() {
        // Reset error container
        clearErrors(); // FIXED: Use the consistent error clearing function
        
        // Validate form fields before submission
        if (!validateBarangayForm()) {
            return;
        }
        
        // Show loading state
        submitButton.disabled = true;
        submitButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Adding...';
        
        // Create form data
        const formData = new FormData(form);
        
        // Send AJAX request
        fetch('{{ route("admin.locations.barangays.store") }}', {
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
                showBarangaySuccess(data.message);
            } else {
                // Show detailed validation errors
                errorContainer.classList.remove('d-none');
                
                if (data.errors) {
                    const errorList = document.createElement('ul');
                    errorList.style.marginBottom = '0';
                    
                    let hasNameError = false;
                    let hasMunicipalityError = false;
                    
                    for (const key in data.errors) {
                        data.errors[key].forEach(error => {
                            // Replace generic error messages with more detailed ones
                            let enhancedError = error;
                            
                            if (key === 'barangay_name') {
                                hasNameError = true;
                                
                                // Enhance specific error messages
                                if (error.includes('required')) {
                                    enhancedError = 'The barangay name is required.';
                                } 
                                else if (error.includes('format is invalid') || error.includes('regex')) {
                                    enhancedError = 'Barangay name must start with a capital letter, contain at least 2 letters, and can only include letters, numbers, spaces, periods, hyphens, and apostrophes.';
                                }
                                else if (error.includes('already exists') || error.includes('has already been taken') || error.includes('unique')) {
                                    enhancedError = 'This barangay name already exists in the selected municipality.';
                                }
                                else if (error.includes('exceed')) {
                                    enhancedError = 'Barangay name cannot exceed 100 characters.';
                                }
                            } else if (key === 'municipality_id') {
                                hasMunicipalityError = true;
                                if (error.includes('required')) {
                                    enhancedError = 'Please select a municipality.';
                                }
                            }
                            
                            const li = document.createElement('li');
                            li.textContent = enhancedError;
                            errorList.appendChild(li);
                        });
                    }
                    
                    errorContainer.appendChild(errorList);
                    
                    // Add additional guidance for name errors
                    if (hasNameError) {
                        const guidance = document.createElement('div');
                        guidance.className = 'mt-2 pt-2 border-top';
                        guidance.innerHTML = `
                            <small class="text-muted">
                                <strong>Barangay name tips:</strong>
                                <ul class="mt-1 mb-0">
                                    <li>Must start with a capital letter (e.g., "San Roque" not "san roque")</li>
                                    <li>Must contain at least 2 letters</li>
                                    <li>Can include letters, numbers, spaces, periods, hyphens, and apostrophes</li>
                                    <li>Example: "San Roque", "Barangay 12", "Zone 4"</li>
                                </ul>
                            </small>
                        `;
                        errorContainer.appendChild(guidance);
                    }
                } else {
                    errorContainer.textContent = data.message || 'An error occurred';
                }
                
                // ADDED: Set up input listeners again for the form fields to clear errors on type
                document.getElementById('barangayName').addEventListener('input', clearErrors);
                document.getElementById('barangayMunicipalitySelect').addEventListener('change', clearErrors);
                
                // Reset button
                submitButton.disabled = false;
                submitButton.innerHTML = '<i class="bi bi-plus"></i> Add Barangay';
            }
        })
        .catch(error => {
            // Handle network errors
            errorContainer.classList.remove('d-none');
            errorContainer.textContent = 'Network error. Please try again.';
            
            // Reset button
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="bi bi-plus"></i> Add Barangay';
        });
    });
    
    // Prevent default form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
    });
});
</script>