<!-- filepath: c:\xampp\htdocs\sulong_kalinga\resources\views\components\modals\addBarangay.blade.php -->
@php
use App\Helpers\TranslationHelper as T;
@endphp
<div class="modal fade" id="addBarangayModal" tabindex="-1" aria-labelledby="addBarangayModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBarangayModalLabel">{{ T::translate('Add New Barangay', 'Magdagdag ng Bagong Barangay')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Error messages container -->
                <div id="barangayErrorContainer" class="alert alert-danger d-none"></div>
                
                <!-- Success message container (new) -->
                <div id="barangaySuccessContainer" class="d-none">
                    <p class="text-success">
                        <i class="bi bi-check-circle"></i>
                        <strong>Success!</strong> <span id="barangaySuccessMessage">{{ T::translate('The barangay has been added successfully.', 'Ang barangay ay matagumpay na naidagdag')}}</span>
                    </p>
                    <p>{{ T::translate('The page will reload shortly.', 'Magre-reload ang page sa ilang sandali.')}}</p>
                </div>
                
                <form id="addBarangayForm">
                    @csrf
                    <div id="barangayFormContent">
                    <div class="mb-3">
                        <label for="barangayMunicipalitySelect" class="form-label">{{ T::translate('Municipality', 'Munisipalidad')}}</label>
                        <select class="form-select" id="barangayMunicipalitySelect" name="municipality_id" required>
                            <option value="">{{ T::translate('Select Municipality', 'Pumili ng Munisipalidad')}}</option>
                            @foreach($municipalities as $municipality)
                                <option value="{{ $municipality->municipality_id }}">{{ $municipality->municipality_name }}</option>
                            @endforeach
                        </select>
                        <div class="form-text">{{ T::translate('Select the municipality this barangay belongs to.', 'Piliin ang munisipalidad na kinabibilangan ng barangay na ito.')}}</div>
                    </div>
                        
                        <div class="mb-3">
                            <label for="barangayName" class="form-label">{{ T::translate('Barangay Name', 'Pangalan ng Barangay')}}</label>
                            <input type="text" class="form-control" id="barangayName" name="barangay_name" required
                                   pattern="^[A-Z][A-Za-z][A-Za-z0-9\s\.\-']*$" 
                                   title="Barangay name must start with a capital letter, contain at least 2 letters, and can only include letters, numbers, spaces, periods, hyphens, and apostrophes">
                            <div class="form-text">{{ T::translate('Enter the name of the barangay (e.g., San Roque, Zone 4, Barangay 1). Must start with a capital letter and contain at least 2 letters.', 'Ilagay ang pangalan ng barangay (hal., San Roque, Zone 4, Barangay 1). Dapat magsimula sa malaking titik at naglalaman ng hindi bababa sa 2 titik.')}}</div>
                        </div>
                        
                        <div class="alert alert-warning">
                            <small><i class="bi bi-exclamation-circle"></i> {{ T::translate('Barangay names must be unique within their municipality.', 'Ang mga pangalan ng barangay ay dapat na natatangi sa loob ng kanilang munisipalidad.')}}</small>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelBarangayButton">{{ T::translate('Cancel', 'I-Kansela')}}</button>
                <button type="button" class="btn btn-primary" id="submitBarangay">
                    <i class="bi bi-plus"></i> {{ T::translate('Add Barangay', 'Magdagdag ng Barangay')}}
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
            showDetailedError('{{ T::translate('Please select a municipality.', 'Mangyaring pumili ng munisipalidad.') }}');
            return false;
        }
        
        if (!barangayName) {
            showDetailedError('{{ T::translate('Barangay name is required', 'Ang pangalan ng barangay ay kinakailangan')}}.');
            return false;
        }
        
        // Fixed pattern - removed extra escaping
        const namePattern = /^[A-Z][A-Za-z][A-Za-z0-9\s\.\-']*$/;
        if (!namePattern.test(barangayName)) {
            showDetailedError('{{ T::translate('Barangay name must start with a capital letter, contain at least 2 letters, and can only include letters, numbers, spaces, periods, hyphens, and apostrophes.', 'Dapat magsimula ang pangalan ng barangay sa malaking titik, naglalaman ng hindi bababa sa 2 titik, at maaari lamang maglaman ng mga titik, numero, espasyo, tuldok, gitling, at apostrophe.') }}');
            return false;
        }
        
        if (barangayName.length > 100) {
            showDetailedError('{{ T::translate('Barangay name cannot exceed 100 characters.', 'Ang pangalan ng barangay ay hindi dapat lumampas sa 100 na karakter.') }}');
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
                    <strong>{{ T::translate('Barangay name tips:', 'Mga Tip para sa pangalan ng Barangay')}}</strong>
                    <ul class="mt-1 mb-0">
                        <li>{{ T::translate('Must start with a capital letter (e.g., \"San Roque\" not \"san roque\")', 'Dapat magsimula sa malaking titik (hal., \"San Roque\" hindi \"san roque\")')}}</li>
                        <li>{{ T::translate('Must contain at least 2 letters', 'Dapat maglaman ng hindi bababa sa 2 titik')}}</li>
                        <li>{{ T::translate('Can include letters, numbers, spaces, periods, hyphens, and apostrophes', 'Maaaring magsama ng mga titik, numero, puwang, tuldok, gitling, at kudlit')}}</li>
                        <li>{{ T::translate('Example: \"San Roque\", \"Barangay 12\", \"Zone 4\"', 'Halimbawa: \"San Roque\", \"Barangay 12\", \"Zone 4\"')}}</li>
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
        fetch('/admin/locations/barangays/add', {
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
                                    enhancedError = '{{ T::translate('The barangay name is required.', 'Ang Pangalan ng Barangay ay kinakailangan.') }}';
                                } 
                                else if (error.includes('format is invalid') || error.includes('regex')) {
                                    enhancedError = '{{ T::translate('Municipality name must start with a capital letter, contain at least 2 letters, and can only include letters, numbers, spaces, periods, hyphens, and apostrophes.', 'Ang pangalan ng munisipyo ay dapat magsimula sa malaking titik, naglalaman ng hindi bababa sa 2 titik, at maaari lamang magsama ng mga titik, numero, puwang, tuldok, gitling, at kudlit.')}}';
                                }
                                else if (error.includes('already exists') || error.includes('has already been taken') || error.includes('unique')) {
                                    enhancedError = '{{ T::translate('This barangay name already exists in the selected municipality.', 'Ang pangalan ng barangay na ito ay umiiral na sa napiling munisipalidad.') }}';
                                }
                                else if (error.includes('exceed')) {
                                    enhancedError = '{{ T::translate('Barangay name cannot exceed 100 characters.', 'Ang pangalan ng barangay ay hindi dapat lumampas sa 100 na karakter.') }}';
                                }
                            } else if (key === 'municipality_id') {
                                hasMunicipalityError = true;
                                if (error.includes('required')) {
                                    enhancedError = '{{ T::translate('Please select a municipality.', 'Mangyaring pumili ng munisipalidad.') }}';
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
                                <strong>{{ T::translate('Barangay name tips:', 'Mga Tip para sa pangalan ng Barangay')}}</strong>
                                <ul class="mt-1 mb-0">
                                    <li>{{ T::translate('Must start with a capital letter (e.g., \"San Roque\" not \"san roque\")', 'Dapat magsimula sa malaking titik (hal., \"San Roque\" hindi \"san roque\")')}}</li>
                                    <li>{{ T::translate('Must contain at least 2 letters', 'Dapat maglaman ng hindi bababa sa 2 titik')}}</li>
                                    <li>{{ T::translate('Can include letters, numbers, spaces, periods, hyphens, and apostrophes', 'Maaaring magsama ng mga titik, numero, puwang, tuldok, gitling, at kudlit')}}</li>
                                    <li>{{ T::translate('Example: \"San Roque\", \"Barangay 12\", \"Zone 4\"', 'Halimbawa: \"San Roque\", \"Barangay 12\", \"Zone 4\"')}}</li>
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
                submitButton.innerHTML = '<i class="bi bi-plus"></i> {{ T::translate('Add Barangay', 'Magdagdag ng Barangay')}}';
            }
        })
        .catch(error => {
            // Handle network errors
            errorContainer.classList.remove('d-none');
            errorContainer.textContent = '{{ T::translate('Network error. Please try again.', 'Error sa Network. Mangyaring subukang muli.') }}';
            
            // Reset button
            submitButton.disabled = false;
            submitButton.innerHTML = '<i class="bi bi-plus"></i>{{ T::translate('Add Barangay', 'Magdagdag ng Barangay')}}';
        });
    });
    
    // Prevent default form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
    });
});
</script>