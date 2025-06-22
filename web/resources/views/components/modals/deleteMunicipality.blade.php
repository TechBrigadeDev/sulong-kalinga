@php
use App\Helpers\TranslationHelper as T;
@endphp

<div class="modal fade" id="deleteMunicipalityModal" tabindex="-1" aria-labelledby="deleteMunicipalityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:rgb(251, 68, 68);">
                <h5 class="modal-title text-white" id="deleteMunicipalityModalLabel">{{ T::translate('Confirm Municipality Deletion', 'Kumpirmahin ang Pagtanggal ng Munisipalidad') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBodyContent">
                <div id="municipalityDeleteMessage" class="alert d-none" role="alert"></div>
                
                <div id="deleteConfirmation">
                    <p class="text-danger">
                        <i class="bi bi-exclamation-circle"></i> 
                        <strong>{{ T::translate('Warning', 'Babala') }}!</strong> {{ T::translate('You are about to delete this municipality.', 'Tatanggalin mo na ang munisipalidad na ito.') }}
                    </p>
                    <p>{{ T::translate('Are you sure you want to permanently delete', 'Sigurado ka bang nais mong permanenteng tanggalin') }} <span id="municipalityNameToDelete" style="font-weight: bold;"></span>?</p>
                    <div class="mb-3">
                        <label for="municipalityDeletePasswordInput" class="form-label">{{ T::translate('Enter Your Password to Confirm', 'Ilagay ang Iyong Password upang Kumpirmahin') }}</label>
                        <input type="password" class="form-control" id="municipalityDeletePasswordInput" placeholder="{{ T::translate('Enter your password', 'Ilagay ang iyong password') }}" required>
                        <input type="hidden" id="municipalityIdToDelete" value="">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelDeleteButton">{{ T::translate('Cancel', 'I-Kansela') }}</button>
                <button type="button" class="btn btn-danger" id="confirmMunicipalityDeleteButton">
                    <i class="bi bi-trash-fill"></i> {{ T::translate('Delete Municipality', 'Tanggalin ang Munisipalidad') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Function to open the delete modal
window.openDeleteMunicipalityModal = function(id, name) {
    // Reset modal state
    const modalBody = document.getElementById('modalBodyContent');
    
    // Reset to original content
    modalBody.innerHTML = `
        <div id="municipalityDeleteMessage" class="alert d-none" role="alert"></div>
        
        <div id="deleteConfirmation">
            <p class="text-danger">
                <i class="bi bi-exclamation-circle"></i> 
                <strong>{{ T::translate('Warning', 'Babala') }}!</strong> {{ T::translate('You are about to delete this municipality.', 'Tatanggalin mo na ang munisipalidad na ito.') }}
            </p>
            <p>{{ T::translate('Are you sure you want to permanently delete', 'Sigurado ka bang nais mong permanenteng tanggalin') }} <span id="municipalityNameToDelete" style="font-weight: bold;"></span>?</p>
            <div class="mb-3">
                <label for="municipalityDeletePasswordInput" class="form-label">{{ T::translate('Enter Your Password to Confirm', 'Ilagay ang Iyong Password upang Kumpirmahin') }}</label>
                <input type="password" class="form-control" id="municipalityDeletePasswordInput" placeholder="{{ T::translate('Enter your password', 'Ilagay ang iyong password') }}" required>
                <input type="hidden" id="municipalityIdToDelete" value="">
            </div>
        </div>
    `;
    
    // Set values
    document.getElementById('municipalityIdToDelete').value = id;
    document.getElementById('municipalityNameToDelete').textContent = name;
    
    // Reset buttons
    const confirmButton = document.getElementById('confirmMunicipalityDeleteButton');
    confirmButton.disabled = false;
    confirmButton.innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Municipality", "Tanggalin ang Munisipalidad") }}';
    confirmButton.style.display = 'inline-block';
    
    document.getElementById('cancelDeleteButton').textContent = '{{ T::translate("Cancel", "I-Kansela") }}';
    
    // Show the modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteMunicipalityModal'));
    deleteModal.show();
    
    // Re-add event listener for password input
    document.getElementById('municipalityDeletePasswordInput').addEventListener('input', function() {
        const messageElement = document.getElementById('municipalityDeleteMessage');
        if (messageElement) {
            messageElement.classList.add('d-none');
            messageElement.style.display = 'none';
        }
    });
}

// Function to show error message
function showError(message) {
    const messageElement = document.getElementById('municipalityDeleteMessage');
    messageElement.textContent = message;
    messageElement.classList.remove('d-none', 'alert-success');
    messageElement.classList.add('alert-danger');
    messageElement.style.display = 'block';
}

// Function to show detailed error message with guidance
function showDependencyError(message, errorType) {
    // Get modal body for replacement
    const modalBody = document.getElementById('modalBodyContent');
    
    // Create error content
    let errorContent = `
        <div class="alert alert-danger">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-exclamation-circle me-2" style="font-size: 1.5rem;"></i>
                <strong>{{ T::translate('Unable to Delete', 'Hindi Matanggal') }}</strong>
            </div>
            <p>${message}</p>
    `;
    
    // Add specific guidance based on error type
    if (errorType === 'dependency_barangays') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>{{ T::translate('What you can do instead', 'Ang maaari mong gawin') }}:</strong>
                <p class="mt-2 mb-2">{{ T::translate('This municipality has barangays assigned to it. For data integrity, municipalities with assigned barangays cannot be deleted.', 'Ang munisipalidad na ito ay may mga barangay na nakatalaga dito. Para sa integridad ng datos, ang mga munisipalidad na may mga barangay ay hindi maaaring tanggalin.') }}</p>
                <ol class="mt-2 mb-0">
                    <li>{{ T::translate('First, delete all barangays from this municipality', 'Una, tanggalin ang lahat ng mga barangay mula sa munisipalidad na ito') }}</li>
                    <li>{{ T::translate('If no other references (such as assignments to beneficiaries) exist, you can delete this municipality', 'Kung walang ibang mga reference (tulad ng mga assignment sa mga benepisyaryo) ang umiiral, maaari mong tanggalin ang munisipalidad na ito') }}</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_beneficiaries') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>{{ T::translate('System Requirement', 'Kinakailangan ng System') }}:</strong>
                <p class="mt-2 mb-2">{{ T::translate('This municipality has beneficiaries assigned to it. Data integrity requires that all records remain linked.', 'Ang munisipalidad na ito ay may mga benepisyaryong nakatalaga dito. Ang integridad ng datos ay nangangailangan na ang lahat ng mga rekord ay manatiling naka-link.') }}</p>
                <ol class="mt-2 mb-0">
                    <li>{{ T::translate('First, reassign all beneficiaries to another municipality', 'Una, italaga ang lahat ng mga benepisyaryo sa ibang munisipalidad') }}</li>
                    <li>{{ T::translate('Then delete all barangays from this municipality', 'Pagkatapos, tanggalin ang lahat ng mga barangay mula sa munisipalidad na ito') }}</li>
                    <li>{{ T::translate('Finally, you can delete this municipality', 'Sa wakas, maaari mong tanggalin ang munisipalidad na ito') }}</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_care_users') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>{{ T::translate('User Assignments', 'Mga Assignment ng User') }}:</strong>
                <p class="mt-2 mb-2">{{ T::translate('This municipality has care workers or care managers assigned to it.', 'Ang munisipalidad na ito ay may mga care worker o care manager na nakatalaga dito.') }}</p>
                <ol class="mt-2 mb-0">
                    <li>{{ T::translate('First, reassign all users to another municipality', 'Una, italaga ang lahat ng mga user sa ibang munisipalidad') }}</li>
                    <li>{{ T::translate('Go to the Care Workers and Care Managers profiles', 'Pumunta sa mga profile ng Care Workers at Care Managers') }}</li>
                    <li>{{ T::translate('Assign them to different municipalities', 'Italaga sila sa iba\'t ibang munisipalidad') }}</li>
                    <li>{{ T::translate('If there are no barangays under this municipality, then you can delete this municipality', 'Kung walang mga barangay sa ilalim ng munisipalidad na ito, maaari mong tanggalin ang munisipalidad na ito') }}</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'permission_denied') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>{{ T::translate('Permission Denied', 'Hindi Pinahintulutan') }}:</strong>
                <p class="mt-2 mb-2">{{ T::translate('Only administrators with sufficient permissions can delete municipalities.', 'Tanging ang mga administrator na may sapat na pahintulot ang maaaring magtanggal ng mga munisipalidad.') }}</p>
                <p>{{ T::translate('Please contact a system administrator if you need this municipality removed.', 'Mangyaring makipag-ugnayan sa isang system administrator kung kailangan mong alisin ang munisipalidad na ito.') }}</p>
            </div>
        `;
    }
    
    errorContent += `</div>`;
    
    // Replace modal body content
    modalBody.innerHTML = errorContent;
    
    // Change the cancel button text
    document.getElementById('cancelDeleteButton').textContent = '{{ T::translate("Close", "Isara") }}';
    
    // Hide the delete button
    document.getElementById('confirmMunicipalityDeleteButton').style.display = 'none';
}

// Function to completely replace modal content with success message
function showSuccess() {
    // Get modal body reference
    const modalBody = document.getElementById('modalBodyContent');
    
    // Replace content with success message
    modalBody.innerHTML = `
        <div class="text-center mb-2">
            <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
        </div>
        <p class="text-success text-center">
            <strong>{{ T::translate('Success', 'Tagumpay') }}!</strong> {{ T::translate('The municipality has been deleted successfully.', 'Matagumpay na natanggal ang munisipalidad.') }}
        </p>
        <p class="text-center">{{ T::translate('The page will reload shortly.', 'Ang pahina ay magre-reload sa ilang sandali.') }}</p>
    `;
    
    // Hide delete button and update cancel button
    document.getElementById('confirmMunicipalityDeleteButton').style.display = 'none';
    document.getElementById('cancelDeleteButton').textContent = '{{ T::translate("Close", "Isara") }}';
    
    // Set timeout to reload the page
    setTimeout(function() {
        window.location.reload();
    }, 2000);
}

// Setup event handlers when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Delete confirmation button click handler
    document.getElementById('confirmMunicipalityDeleteButton').addEventListener('click', function() {
        // Get password and ID from the currently displayed form
        const passwordInput = document.getElementById('municipalityDeletePasswordInput');
        const idInput = document.getElementById('municipalityIdToDelete');
        
        if (!passwordInput || !idInput) {
            console.error('Form elements not found');
            return;
        }
        
        const password = passwordInput.value.trim();
        const municipalityId = idInput.value;
        
        if (!password) {
            showError('{{ T::translate("Please enter your password to confirm deletion.", "Mangyaring ilagay ang iyong password upang kumpirmahin ang pagtanggal.") }}');
            return;
        }
        
        // Show loading state
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ T::translate("Deleting...", "Tinatanggal...") }}';
        
        // Send delete request with CSRF token and password
        fetch(`/admin/locations/municipalities/${municipalityId}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ password: password })
        })
        .then(response => response.json())
        .then(data => {
            console.log("Server response:", data); // Debug log
            
            if (data.success) {
                showSuccess();
            } else {
                // Check for specific error types
                if (data.error_type) {
                    showDependencyError(data.message, data.error_type);
                } else {
                    showError(data.message || '{{ T::translate("Failed to delete municipality.", "Nabigong tanggalin ang munisipalidad.") }}');
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Municipality", "Tanggalin ang Munisipalidad") }}';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('{{ T::translate("An unexpected error occurred. Please try again.", "Isang hindi inaasahang error ang naganap. Mangyaring subukan muli.") }}');
            this.disabled = false;
            this.innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Municipality", "Tanggalin ang Munisipalidad") }}';
        });
    });
});
</script>