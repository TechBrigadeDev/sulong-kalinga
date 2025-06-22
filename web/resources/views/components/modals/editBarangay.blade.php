@php
use App\Helpers\TranslationHelper as T;
@endphp
<div class="modal fade" id="editBarangayModal" tabindex="-1" aria-labelledby="editBarangayModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBarangayModalLabel">{{ T::translate('Edit Barangay', 'I-Edit ang Barangay') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Error messages container -->
                <div id="editBarangayErrorContainer" class="alert alert-danger d-none"></div>
                
                <!-- Success message container -->
                <div id="editBarangaySuccessContainer" class="d-none">
                    <p class="text-success">
                        <i class="bi bi-check-circle"></i>
                        <strong>{{ T::translate('Success', 'Tagumpay') }}!</strong> <span id="editBarangaySuccessMessage">{{ T::translate('The barangay has been updated successfully.', 'Ang barangay ay matagumpay na na-update.') }}</span>
                    </p>
                    <p>{{ T::translate('The page will reload shortly.', 'Ang page ay magre-reload sa ilang sandali.') }}</p>
                </div>
                
                <form id="editBarangayForm">
                    @csrf
                    <input type="hidden" id="editBarangayId" name="barangay_id">
                    <input type="hidden" id="originalBarangayName" name="original_barangay_name">
                    <input type="hidden" id="originalMunicipalityId" name="original_municipality_id">
                    
                    <div id="editBarangayFormContent">
                        <div class="mb-3">
                            <label for="editMunicipalityId" class="form-label">{{ T::translate('Municipality', 'Munispalidad') }}</label>
                            <select class="form-select" id="editMunicipalityId" name="municipality_id" required>
                                <option value="">{{ T::translate('Select Municipality', 'Pumili ng Munispalidad') }}</option>
                                @foreach($municipalities as $municipality)
                                    <option value="{{ $municipality->municipality_id }}">{{ $municipality->municipality_name }}</option>
                                @endforeach
                            </select>
                            <div class="form-text">{{ T::translate('You can change the municipality this barangay belongs to.', 'Maaari mong baguhin ang munisipalidad na kinabibilangan ng barangay na ito.') }}</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="editBarangayName" class="form-label">{{ T::translate('Barangay Name', 'Pangalan ng Barangay') }}</label>
                            <input type="text" class="form-control" id="editBarangayName" name="barangay_name" required
                                   pattern="^[A-Z][A-Za-z][A-Za-z0-9\s\.\-']*$" 
                                   title="{{ T::translate('Barangay name must start with a capital letter, contain at least 2 letters, and can only include letters, numbers, spaces, periods, hyphens, and apostrophes', 'Ang pangalan ng barangay ay dapat magsimula sa malaking titik, maglaman ng hindi bababa sa 2 titik, at maaari lamang maglaman ng mga titik, numero, espasyo, tuldok, gitling, at apostrope') }}">
                            <div class="form-text">{{ T::translate('Enter the name of the barangay (e.g., San Roque, Zone 4, Barangay 1). Must start with a capital letter and contain at least 2 letters.', 'Ilagay ang pangalan ng barangay (hal., San Roque, Zone 4, Barangay 1). Dapat magsimula sa malaking titik at maglaman ng hindi bababa sa 2 titik.') }}</div>
                        </div>
                        
                        <div class="alert alert-warning">
                            <small><i class="bi bi-exclamation-circle me-1"></i> {{ T::translate('Barangay names must be unique within their municipality.', 'Ang mga pangalan ng barangay ay dapat na natatangi sa loob ng kanilang munisipalidad.') }}</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelEditBarangayButton">{{ T::translate('Cancel', 'I-Kansela') }}</button>
                <button type="button" class="btn btn-primary" id="submitEditBarangay">
                    <i class="bi bi-pencil-square"></i> {{ T::translate('Update Barangay', 'I-update ang Barangay') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const editForm = document.getElementById('editBarangayForm');
    const editFormContent = document.getElementById('editBarangayFormContent');
    const editErrorContainer = document.getElementById('editBarangayErrorContainer');
    const editSuccessContainer = document.getElementById('editBarangaySuccessContainer');
    const editSuccessMessage = document.getElementById('editBarangaySuccessMessage');
    const submitEditButton = document.getElementById('submitEditBarangay');
    const cancelEditButton = document.getElementById('cancelEditBarangayButton');
    
    // Add input event listeners to clear errors when typing or selecting
    document.getElementById('editBarangayName').addEventListener('input', function() {
        editErrorContainer.innerHTML = '';
        editErrorContainer.classList.add('d-none');
    });
    
    document.getElementById('editMunicipalityId').addEventListener('change', function() {
        editErrorContainer.innerHTML = '';
        editErrorContainer.classList.add('d-none');
    });
    
    // Function to prepare edit modal with barangay data
    window.prepareEdit = function(element) {
        // Reset error message
        editErrorContainer.innerHTML = '';
        editErrorContainer.classList.add('d-none');
        
        // Get data from clicked element
        const id = element.dataset.id;
        const name = element.dataset.name;
        const municipalityId = element.dataset.municipality;
        
        // Reset form and show content
        editForm.reset();
        editFormContent.classList.remove('d-none');
        editSuccessContainer.classList.add('d-none');
        submitEditButton.classList.remove('d-none');
        
        // Set form values
        document.getElementById('editBarangayId').value = id;
        document.getElementById('editBarangayName').value = name;
        document.getElementById('editMunicipalityId').value = municipalityId;
        
        // Store original values for comparison
        document.getElementById('originalBarangayName').value = name;
        document.getElementById('originalMunicipalityId').value = municipalityId;
        
        // Open the modal
        const editModal = new bootstrap.Modal(document.getElementById('editBarangayModal'));
        editModal.show();
    };
    
    // Client-side validation
    function validateEditBarangayForm() {
        const municipalityId = document.getElementById('editMunicipalityId').value;
        const barangayName = document.getElementById('editBarangayName').value.trim();
        const originalName = document.getElementById('originalBarangayName').value;
        const originalMunicipalityId = document.getElementById('originalMunicipalityId').value;
        
        // Check if nothing changed
        if (barangayName === originalName && municipalityId === originalMunicipalityId) {
            showEditDetailedError('{{ T::translate('No changes were made. Please modify the barangay name or municipality to update.', 'Walang mga pagbabago na ginawa. Mangyaring baguhin ang pangalan ng barangay o munisipalidad upang ma-update.') }}');
            return false;
        }
        
        if (!municipalityId) {
            showEditDetailedError('{{ T::translate('Please select a municipality.', 'Mangyaring pumili ng isang munisipalidad.') }}');
            return false;
        }
        
        if (!barangayName) {
            showEditDetailedError('{{ T::translate('Barangay name is required.', 'Ang pangalan ng barangay ay kinakailangan.') }}');
            return false;
        }
        
        // Fixed pattern - removed extra escaping
        const namePattern = /^[A-Z][A-Za-z][A-Za-z0-9\s\.\-']*$/;
        if (!namePattern.test(barangayName)) {
            showEditDetailedError('{{ T::translate('Barangay name must start with a capital letter, contain at least 2 letters, and can only include letters, numbers, spaces, periods, hyphens, and apostrophes.', 'Ang pangalan ng barangay ay dapat magsimula sa malaking titik, maglaman ng hindi bababa sa 2 titik, at maaari lamang maglaman ng mga titik, numero, espasyo, tuldok, gitling, at apostrope.') }}');
            return false;
        }
        
        if (barangayName.length > 100) {
            showEditDetailedError('{{ T::translate('Barangay name cannot exceed 100 characters.', 'Ang pangalan ng barangay ay hindi maaaring lumampas sa 100 karakter.') }}');
            return false;
        }
        
        return true;
    }
    
    // Show a more detailed error message with guidance
    function showEditDetailedError(message) {
        editErrorContainer.classList.remove('d-none');
        
        const errorContent = document.createElement('div');
        
        // Main error message
        const errorMessage = document.createElement('p');
        errorMessage.className = 'mb-2';
        errorMessage.innerHTML = `<i class="bi bi-exclamation-circle text-danger me-1"></i> ${message}`;
        errorContent.appendChild(errorMessage);
        
        // Add guidance for name errors
        if (message.includes('Barangay name') || message.includes('pangalan ng barangay')) {
            const guidance = document.createElement('div');
            guidance.className = 'mt-2 pt-2 border-top';
            guidance.innerHTML = `
                <small class="text-muted">
                    <strong>{{ T::translate('Barangay name tips:', 'Mga tip sa pangalan ng barangay:') }}</strong>
                    <ul class="mt-1 mb-0">
                        <li>{{ T::translate('Must start with a capital letter (e.g., "San Roque" not "san roque")', 'Dapat magsimula sa malaking titik (hal., "San Roque" hindi "san roque")') }}</li>
                        <li>{{ T::translate('Must contain at least 2 letters', 'Dapat maglaman ng hindi bababa sa 2 titik') }}</li>
                        <li>{{ T::translate('Can include letters, numbers, spaces, periods, hyphens, and apostrophes', 'Maaaring maglaman ng mga titik, numero, espasyo, tuldok, gitling, at apostrope') }}</li>
                        <li>{{ T::translate('Example: "San Roque", "Barangay 12", "Zone 4"', 'Halimbawa: "San Roque", "Barangay 12", "Zone 4"') }}</li>
                    </ul>
                </small>
            `;
            errorContent.appendChild(guidance);
        }
        
        // Replace error container content
        editErrorContainer.innerHTML = '';
        editErrorContainer.appendChild(errorContent);
    }
    
    // Show success message and hide form
    function showEditBarangaySuccess(message) {
        // Hide form content and error messages
        editFormContent.classList.add('d-none');
        editErrorContainer.classList.add('d-none');
        
        // Show success message
        editSuccessContainer.classList.remove('d-none');
        if (message) {
            editSuccessMessage.textContent = message;
        }
        
        // Update buttons
        submitEditButton.classList.add('d-none');
        cancelEditButton.textContent = '{{ T::translate('Close', 'Isara') }}';
        
        // Set timeout to reload the page
        setTimeout(function() {
            window.location.reload();
        }, 2000);
    }
    
    submitEditButton.addEventListener('click', function() {
        // Reset error container
        editErrorContainer.innerHTML = '';
        editErrorContainer.classList.add('d-none');
        
        // Validate form fields before submission
        if (!validateEditBarangayForm()) {
            return;
        }
        
        // Show loading state
        submitEditButton.disabled = true;
        submitEditButton.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ T::translate('Updating...', 'Nag-u-update...') }}';
        
        // Create form data
        const formData = new FormData(editForm);
        
        // Send AJAX request
        fetch('/admin/locations/barangays/update', {
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
                showEditBarangaySuccess(data.message);
            } else {
                // Show detailed validation errors
                editErrorContainer.classList.remove('d-none');
                
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
                                    enhancedError = '{{ T::translate('The barangay name is required.', 'Ang pangalan ng barangay ay kinakailangan.') }}';
                                } 
                                else if (error.includes('format is invalid') || error.includes('regex')) {
                                    enhancedError = '{{ T::translate('Barangay name must start with a capital letter, contain at least 2 letters, and can only include letters, numbers, spaces, periods, hyphens, and apostrophes.', 'Ang pangalan ng barangay ay dapat magsimula sa malaking titik, maglaman ng hindi bababa sa 2 titik, at maaari lamang maglaman ng mga titik, numero, espasyo, tuldok, gitling, at apostrope.') }}';
                                }
                                else if (error.includes('already exists') || error.includes('has already been taken') || error.includes('unique')) {
                                    enhancedError = '{{ T::translate('This barangay name already exists in the selected municipality.', 'Ang pangalang ito ng barangay ay mayroon na sa napiling munisipalidad.') }}';
                                }
                                else if (error.includes('exceed')) {
                                    enhancedError = '{{ T::translate('Barangay name cannot exceed 100 characters.', 'Ang pangalan ng barangay ay hindi maaaring lumampas sa 100 karakter.') }}';
                                }
                            } else if (key === 'municipality_id') {
                                hasMunicipalityError = true;
                                if (error.includes('required')) {
                                    enhancedError = '{{ T::translate('Please select a municipality.', 'Mangyaring pumili ng isang munisipalidad.') }}';
                                }
                            }
                            
                            const li = document.createElement('li');
                            li.textContent = enhancedError;
                            errorList.appendChild(li);
                        });
                    }
                    
                    editErrorContainer.appendChild(errorList);
                    
                    // Add additional guidance for name errors
                    if (hasNameError) {
                        const guidance = document.createElement('div');
                        guidance.className = 'mt-2 pt-2 border-top';
                        guidance.innerHTML = `
                            <small class="text-muted">
                                <strong>{{ T::translate('Barangay name tips:', 'Mga tip sa pangalan ng barangay:') }}</strong>
                                <ul class="mt-1 mb-0">
                                    <li>{{ T::translate('Must start with a capital letter (e.g., "San Roque" not "san roque")', 'Dapat magsimula sa malaking titik (hal., "San Roque" hindi "san roque")') }}</li>
                                    <li>{{ T::translate('Must contain at least 2 letters', 'Dapat maglaman ng hindi bababa sa 2 titik') }}</li>
                                    <li>{{ T::translate('Can include letters, numbers, spaces, periods, hyphens, and apostrophes', 'Maaaring maglaman ng mga titik, numero, espasyo, tuldok, gitling, at apostrope') }}</li>
                                    <li>{{ T::translate('Example: "San Roque", "Barangay 12", "Zone 4"', 'Halimbawa: "San Roque", "Barangay 12", "Zone 4"') }}</li>
                                </ul>
                            </small>
                        `;
                        editErrorContainer.appendChild(guidance);
                    }
                } else {
                    editErrorContainer.textContent = data.message || '{{ T::translate('An error occurred', 'May naganap na error') }}';
                }
                
                // Reset button
                submitEditButton.disabled = false;
                submitEditButton.innerHTML = '<i class="bi bi-pencil-square"></i> {{ T::translate('Update Barangay', 'I-update ang Barangay') }}';
            }
        })
        .catch(error => {
            // Handle network errors
            editErrorContainer.classList.remove('d-none');
            editErrorContainer.textContent = '{{ T::translate('Network error. Please try again.', 'Network error. Mangyaring subukan muli.') }}';
            
            // Reset button
            submitEditButton.disabled = false;
            submitEditButton.innerHTML = '<i class="bi bi-pencil-square"></i> {{ T::translate('Update Barangay', 'I-update ang Barangay') }}';
        });
    });
    
    // Prevent default form submission
    editForm.addEventListener('submit', function(e) {
        e.preventDefault();
    });
});
</script>