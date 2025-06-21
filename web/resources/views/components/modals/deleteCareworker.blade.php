@php
use App\Helpers\TranslationHelper as T;
@endphp

<div class="modal fade" id="deleteCareworkerModal" tabindex="-1" aria-labelledby="deleteCareworkerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:rgb(251, 68, 68);">
                <h5 class="modal-title text-white" id="deleteCareworkerModalLabel">{{ T::translate('Confirm Care Worker Deletion', 'Kumpirmahin ang Pagtanggal ng Care Worker') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="careworkerDeleteMessage" class="alert d-none" role="alert"></div>
                
                <div id="deleteConfirmation">
                    <p class="text-danger">
                        <i class="bi bi-exclamation-circle"></i> 
                        <strong>{{ T::translate('Warning', 'Babala') }}!</strong> {{ T::translate('You are about to delete this care worker account.', 'Tatanggalin mo na ang account ng care worker na ito.') }}
                    </p>
                    <p>{{ T::translate('Are you sure you want to permanently delete', 'Sigurado ka bang nais mong permanenteng tanggalin') }} <span id="careworkerNameToDelete" style="font-weight: bold;"></span>?</p>
                    <div class="mb-3">
                        <label for="careworkerDeletePasswordInput" class="form-label">{{ T::translate('Enter Your Password to Confirm', 'Ilagay ang Iyong Password upang Kumpirmahin') }}</label>
                        <input type="password" class="form-control" id="careworkerDeletePasswordInput" placeholder="{{ T::translate('Enter your password', 'Ilagay ang iyong password') }}" required>
                        <input type="hidden" id="careworkerIdToDelete" value="">
                    </div>
                </div>
                
                <div id="deleteSuccess" class="d-none">
                    <p class="text-success">
                        <i class="bi bi-check-circle"></i>
                        <strong>{{ T::translate('Success', 'Tagumpay') }}!</strong> {{ T::translate('The care worker has been deleted successfully.', 'Matagumpay na natanggal ang care worker.') }}
                    </p>
                    <p>{{ T::translate('You will be redirected to the care worker list shortly.', 'Ikaw ay maire-redirect sa listahan ng mga care worker sa ilang sandali.') }}</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelDeleteButton">{{ T::translate('Cancel', 'I-Kansela') }}</button>
                <button type="button" class="btn btn-danger" id="confirmCareworkerDeleteButton">
                    <i class="bi bi-trash-fill"></i> {{ T::translate('Delete Care Worker', 'Tanggalin ang Care Worker') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Function to open the delete modal
window.openDeleteCareworkerModal = function(id, name) {
    // Reset modal state
    document.getElementById('careworkerDeleteMessage').classList.add('d-none');
    document.getElementById('deleteConfirmation').classList.remove('d-none');
    document.getElementById('deleteSuccess').classList.add('d-none');
    document.getElementById('careworkerDeletePasswordInput').value = '';
    document.getElementById('careworkerIdToDelete').value = id;
    document.getElementById('careworkerNameToDelete').textContent = name;
    
    const confirmButton = document.getElementById('confirmCareworkerDeleteButton');
    confirmButton.disabled = false;
    confirmButton.innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Care Worker", "Tanggalin ang Care Worker") }}';
    confirmButton.classList.remove('d-none');
    
    document.getElementById('cancelDeleteButton').textContent = '{{ T::translate("Cancel", "I-Kansela") }}';
    
    // Show the modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteCareworkerModal'));
    deleteModal.show();
}

// Function to show error message
function showError(message) {
    const messageElement = document.getElementById('careworkerDeleteMessage');
    messageElement.textContent = message;
    messageElement.classList.remove('d-none', 'alert-success');
    messageElement.classList.add('alert-danger');
}

// Function to show detailed error message with guidance
function showDependencyError(message, errorType) {
    // First hide the confirmation form
    document.getElementById('deleteConfirmation').classList.add('d-none');
    
    // Update the message element
    const messageElement = document.getElementById('careworkerDeleteMessage');
    messageElement.classList.remove('d-none', 'alert-success', 'alert-warning');
    messageElement.classList.add('alert-danger');
    
    // Create structured error content with icon and guidance
    let errorContent = `
        <div class="d-flex align-items-center mb-2">
            <i class="bi bi-exclamation-circle me-2" style="font-size: 1.5rem;"></i>
            <strong>{{ T::translate('Unable to Delete', 'Hindi Matanggal') }}</strong>
        </div>
        <p>${message}</p>
    `;
    
    // Add specific guidance based on error type
    if (errorType === 'dependency_care_plans') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>{{ T::translate('What you can do instead', 'Ang maaari mong gawin') }}:</strong>
                <p class="mt-2 mb-2">{{ T::translate('This care worker has created or updated care plans which must maintain their audit history for compliance purposes.', 'Ang care worker na ito ay gumawa o nag-update ng mga care plan na dapat panatilihin ang kanilang kasaysayan ng audit para sa mga layunin ng pagsunod.') }}</p>
                <ol class="mt-2 mb-0">
                    <li>{{ T::translate('Instead of deleting, you can mark this care worker as inactive in their profile to disable their access to the system', 'Sa halip na tanggalin, maaari mong markahan ang care worker na ito bilang hindi aktibo sa kanilang profile upang hindi na sila makapag-log in') }}</li>
                    <li>{{ T::translate('This will prevent them from logging in while preserving the audit trail', 'Ito ay maiiwasan silang mag-log in habang pinapanatili ang audit trail') }}</li>
                    <li>{{ T::translate('Go to Care Worker List, find this care worker, and change their status', 'Pumunta sa Listahan ng Care Worker, hanapin ang care worker na ito, at baguhin ang kanilang katayuan') }}</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_users') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>{{ T::translate('What you can do instead', 'Ang maaari mong gawin') }}:</strong>
                <p class="mt-2 mb-2">{{ T::translate('This care worker has updated user accounts which require audit history to be maintained.', 'Ang care worker na ito ay nag-update ng mga user account na nangangailangan ng kasaysayan ng audit na mapanatili.') }}</p>
                <ol class="mt-2 mb-0">
                    <li>{{ T::translate('Instead of deleting, you can mark this care worker as inactive in their profile to disable their access to the system', 'Sa halip na tanggalin, maaari mong markahan ang care worker na ito bilang hindi aktibo sa kanilang profile upang hindi na sila makapag-log in') }}</li>
                    <li>{{ T::translate('This will prevent them from logging in while preserving the audit trail', 'Ito ay maiiwasan silang mag-log in habang pinapanatili ang audit trail') }}</li>
                    <li>{{ T::translate('Go to Care Worker List, find this care worker, and change their status', 'Pumunta sa Listahan ng Care Worker, hanapin ang care worker na ito, at baguhin ang kanilang katayuan') }}</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_family') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>{{ T::translate('What you can do instead', 'Ang maaari mong gawin') }}:</strong>
                <p class="mt-2 mb-2">{{ T::translate('This care worker has created or updated family member records which must maintain their history for proper record-keeping.', 'Ang care worker na ito ay gumawa o nag-update ng mga rekord ng miyembro ng pamilya na dapat panatilihin ang kanilang kasaysayan para sa tamang pag-iingat ng rekord.') }}</p>
                <ol class="mt-2 mb-0">
                    <li>{{ T::translate('Instead of deleting, you can mark this care worker as inactive in their profile to disable their access to the system', 'Sa halip na tanggalin, maaari mong markahan ang care worker na ito bilang hindi aktibo sa kanilang profile upang hindi na sila makapag-log in') }}</li>
                    <li>{{ T::translate('This will prevent them from logging in while preserving the audit trail', 'Ito ay maiiwasan silang mag-log in habang pinapanatili ang audit trail') }}</li>
                    <li>{{ T::translate('Go to Care Worker List, find this care worker, and change their status', 'Pumunta sa Listahan ng Care Worker, hanapin ang care worker na ito, at baguhin ang kanilang katayuan') }}</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_audit') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>{{ T::translate('What you can do instead', 'Ang maaari mong gawin') }}:</strong>
                <p class="mt-2 mb-2">{{ T::translate('This care worker has created or updated important records in the system. For audit and compliance reasons, these associations cannot be removed.', 'Ang care worker na ito ay gumawa o nag-update ng mahahalagang rekord sa system. Para sa mga kadahilanan ng audit at pagsunod, ang mga asosasyong ito ay hindi maaaring alisin.') }}</p>
                <ol class="mt-2 mb-0">
                    <li>{{ T::translate('Instead of deleting, you can mark this care worker as inactive in their profile to disable their access to the system', 'Sa halip na tanggalin, maaari mong markahan ang care worker na ito bilang hindi aktibo sa kanilang profile upang hindi na sila makapag-log in') }}</li>
                    <li>{{ T::translate('This will prevent them from logging in while preserving the audit trail', 'Ito ay maiiwasan silang mag-log in habang pinapanatili ang audit trail') }}</li>
                    <li>{{ T::translate('Go to Care Worker List, find this care worker, and change their status', 'Pumunta sa Listahan ng Care Worker, hanapin ang care worker na ito, at baguhin ang kanilang katayuan') }}</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_beneficiaries') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>{{ T::translate('What you can do instead', 'Ang maaari mong gawin') }}:</strong>
                <p class="mt-2 mb-2">{{ T::translate('This care worker is assigned to beneficiaries. You cannot delete a care worker while they\'re assigned to beneficiaries.', 'Ang care worker na ito ay nakatalaga sa mga benepisyaryo. Hindi mo maaaring tanggalin ang isang care worker habang sila ay nakatalaga sa mga benepisyaryo.') }}</p>
                <ol class="mt-2 mb-0">
                    <li>{{ T::translate('First, reassign all beneficiaries to other care workers', 'Una, italaga muli ang lahat ng mga benepisyaryo sa ibang mga care worker') }}</li>
                    <li>{{ T::translate('Return to this page and try deletion again', 'Bumalik sa pahinang ito at subukang tanggalin muli') }}</li>
                    <li>{{ T::translate('Alternatively, you can mark this care worker as inactive in their profile to disable their access to the system and prevent them from logging in', 'Bilang alternatibo, maaari mong markahan ang care worker na ito bilang hindi aktibo sa kanilang profile upang hindi na sila makapag-log in') }}</li>
                </ol>
            </div>
        `;
    }
    
    messageElement.innerHTML = errorContent;
    
    // Change the cancel button text
    document.getElementById('cancelDeleteButton').textContent = '{{ T::translate("Close", "Isara") }}';
    
    // Hide the delete button
    document.getElementById('confirmCareworkerDeleteButton').classList.add('d-none');
}

// Function to show success message
function showSuccess() {
    document.getElementById('deleteConfirmation').classList.add('d-none');
    document.getElementById('deleteSuccess').classList.remove('d-none');
    document.getElementById('confirmCareworkerDeleteButton').classList.add('d-none');
    document.getElementById('cancelDeleteButton').textContent = '{{ T::translate("Close", "Isara") }}';
    
    setTimeout(function() {
        let redirectRoute = "/admin/care-workers";
        @if(Auth::user()->role_id == 2)
            redirectRoute = "/care-manager/care-workers";
        @endif
        window.location.href = redirectRoute;
    }, 2000);
}

// Setup event handlers when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Password input event to clear error messages
    document.getElementById('careworkerDeletePasswordInput').addEventListener('input', function() {
        document.getElementById('careworkerDeleteMessage').classList.add('d-none');
    });
    
    // Delete confirmation button click handler
    document.getElementById('confirmCareworkerDeleteButton').addEventListener('click', function() {
        const password = document.getElementById('careworkerDeletePasswordInput').value.trim();
        const careworkerId = document.getElementById('careworkerIdToDelete').value;
        
        if (!password) {
            showError('{{ T::translate("Please enter your password to confirm deletion.", "Mangyaring ilagay ang iyong password upang kumpirmahin ang pagtanggal.") }}');
            return;
        }
        
        // Show loading state
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ T::translate("Deleting...", "Tinatanggal...") }}';
        
        // First verify password via standard form submission
        let formData = new FormData();
        formData.append('password', password);
        formData.append('_token', '{{ csrf_token() }}');
        
        const xhr1 = new XMLHttpRequest();
        let validatePasswordEndpoint = "/admin/validate-password";
        @if(Auth::user()->role_id == 2)
            validatePasswordEndpoint = "/care-manager/validate-password";
        @endif
        xhr1.open('POST', validatePasswordEndpoint, true);
        xhr1.onload = function() {
            if (xhr1.status === 200) {
                try {
                    const data = JSON.parse(xhr1.responseText);
                    if (data.valid) {
                        // Password is valid, proceed with deletion
                        let deleteForm = new FormData();
                        deleteForm.append('careworker_id', careworkerId);
                        deleteForm.append('_token', '{{ csrf_token() }}');
                        
                        // Determine which endpoint to use based on the user role
                        let endpoint = "/admin/care-workers/delete";
                        @if(Auth::user()->role_id == 2)
                            endpoint = "/care-manager/care-workers/delete";
                        @endif

                        const xhr2 = new XMLHttpRequest();
                        xhr2.open('POST', endpoint, true);
                        xhr2.onload = function() {
                            console.log('Delete response status:', xhr2.status);
                            console.log('Delete response text:', xhr2.responseText);
                            
                            if (xhr2.status === 200) {
                                try {
                                    const response = JSON.parse(xhr2.responseText);
                                    console.log('Parsed response:', response);
                                    
                                    if (response.success) {
                                        showSuccess();
                                    } else {
                                        // Check for specific error types
                                        if (response.error_type) {
                                            showDependencyError(response.message, response.error_type);
                                        } else {
                                            showError(response.message || '{{ T::translate("Failed to delete care worker.", "Nabigong tanggalin ang care worker.") }}');
                                            document.getElementById('confirmCareworkerDeleteButton').disabled = false;
                                            document.getElementById('confirmCareworkerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Care Worker", "Tanggalin ang Care Worker") }}';
                                        }
                                    }
                                } catch (e) {
                                    console.error('Error parsing JSON response:', e);
                                    showError('{{ T::translate("An unexpected error occurred. Please try again.", "Isang hindi inaasahang error ang naganap. Mangyaring subukan muli.") }}');
                                    document.getElementById('confirmCareworkerDeleteButton').disabled = false;
                                    document.getElementById('confirmCareworkerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Care Worker", "Tanggalin ang Care Worker") }}';
                                }
                            } else {
                                showError('{{ T::translate("Server error", "Error sa server") }}: ' + xhr2.status);
                                document.getElementById('confirmCareworkerDeleteButton').disabled = false;
                                document.getElementById('confirmCareworkerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Care Worker", "Tanggalin ang Care Worker") }}';
                            }
                        };
                        xhr2.onerror = function() {
                            showError('{{ T::translate("Network error. Please try again.", "Error sa network. Mangyaring subukan muli.") }}');
                            document.getElementById('confirmCareworkerDeleteButton').disabled = false;
                            document.getElementById('confirmCareworkerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Care Worker", "Tanggalin ang Care Worker") }}';
                        };
                        xhr2.send(deleteForm);
                    } else {
                        showError('{{ T::translate("Incorrect password. Please try again.", "Maling password. Mangyaring subukan muli.") }}');
                        document.getElementById('confirmCareworkerDeleteButton').disabled = false;
                        document.getElementById('confirmCareworkerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Care Worker", "Tanggalin ang Care Worker") }}';
                    }
                } catch (e) {
                    console.error('Error parsing JSON response:', e);
                    showError('{{ T::translate("An unexpected error occurred during password validation. Please try again.", "Isang hindi inaasahang error ang naganap sa pag-validate ng password. Mangyaring subukan muli.") }}');
                    document.getElementById('confirmCareworkerDeleteButton').disabled = false;
                    document.getElementById('confirmCareworkerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Care Worker", "Tanggalin ang Care Worker") }}';
                }
            } else {
                showError('{{ T::translate("Password validation failed. Please try again.", "Nabigo ang pag-validate ng password. Mangyaring subukan muli.") }}');
                document.getElementById('confirmCareworkerDeleteButton').disabled = false;
                document.getElementById('confirmCareworkerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Care Worker", "Tanggalin ang Care Worker") }}';
            }
        };
        xhr1.onerror = function() {
            showError('{{ T::translate("Network error during password validation. Please try again.", "Error sa network habang nagva-validate ng password. Mangyaring subukan muli.") }}');
            document.getElementById('confirmCareworkerDeleteButton').disabled = false;
            document.getElementById('confirmCareworkerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Care Worker", "Tanggalin ang Care Worker") }}';
        };
        xhr1.send(formData);
    });
});
</script>