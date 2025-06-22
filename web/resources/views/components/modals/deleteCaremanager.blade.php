@php
use App\Helpers\TranslationHelper as T;
@endphp

<div class="modal fade" id="deleteCaremanagerModal" tabindex="-1" aria-labelledby="deleteCaremanagerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:rgb(251, 68, 68);">
                <h5 class="modal-title text-white" id="deleteCaremanagerModalLabel">{{ T::translate('Confirm Care Manager Deletion', 'Kumpirmahin ang Pagtanggal ng Care Manager') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="caremanagerDeleteMessage" class="alert d-none" role="alert"></div>
                
                <div id="deleteConfirmation">
                    <p class="text-danger">
                        <i class="bi bi-exclamation-circle"></i> 
                        <strong>{{ T::translate('Warning', 'Babala') }}!</strong> {{ T::translate('You are about to delete this care manager account.', 'Tatanggalin mo na ang account ng care manager na ito.') }}
                    </p>
                    <p>{{ T::translate('Are you sure you want to permanently delete', 'Sigurado ka bang nais mong permanenteng tanggalin') }} <span id="caremanagerNameToDelete" style="font-weight: bold;"></span>?</p>
                    <div class="mb-3">
                        <label for="caremanagerDeletePasswordInput" class="form-label">{{ T::translate('Enter Your Password to Confirm', 'Ilagay ang Iyong Password upang Kumpirmahin') }}</label>
                        <input type="password" class="form-control" id="caremanagerDeletePasswordInput" placeholder="{{ T::translate('Enter your password', 'Ilagay ang iyong password') }}" required>
                        <input type="hidden" id="caremanagerIdToDelete" value="">
                    </div>
                </div>
                
                <div id="deleteSuccess" class="d-none">
                    <p class="text-success">
                        <i class="bi bi-check-circle"></i>
                        <strong>{{ T::translate('Success', 'Tagumpay') }}!</strong> {{ T::translate('The care manager has been deleted successfully.', 'Matagumpay na natanggal ang care manager.') }}
                    </p>
                    <p>{{ T::translate('You will be redirected to the care manager list shortly.', 'Ikaw ay maire-redirect sa listahan ng mga care manager sa ilang sandali.') }}</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelDeleteButton">{{ T::translate('Cancel', 'I-Kansela') }}</button>
                <button type="button" class="btn btn-danger" id="confirmCaremanagerDeleteButton">
                    <i class="bi bi-trash-fill"></i> {{ T::translate('Delete Care Manager', 'Tanggalin ang Care Manager') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Define this function in the global scope so it's accessible from onclick
window.openDeleteCaremanagerModal = function(id, name) {
    // Reset modal state
    document.getElementById('caremanagerDeleteMessage').classList.add('d-none');
    document.getElementById('deleteConfirmation').classList.remove('d-none');
    document.getElementById('deleteSuccess').classList.add('d-none');
    document.getElementById('caremanagerDeletePasswordInput').value = '';
    document.getElementById('caremanagerIdToDelete').value = id;
    document.getElementById('caremanagerNameToDelete').textContent = name;
    
    const confirmButton = document.getElementById('confirmCaremanagerDeleteButton');
    confirmButton.disabled = false;
    confirmButton.innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Care Manager", "Tanggalin ang Care Manager") }}';
    confirmButton.classList.remove('d-none');
    
    document.getElementById('cancelDeleteButton').textContent = '{{ T::translate("Cancel", "I-Kansela") }}';
    
    // Show the modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteCaremanagerModal'));
    deleteModal.show();
}

// Function to show error message
function showError(message) {
    const messageElement = document.getElementById('caremanagerDeleteMessage');
    messageElement.textContent = message;
    messageElement.classList.remove('d-none', 'alert-success');
    messageElement.classList.add('alert-danger');
}

// Function to show detailed error message with guidance
function showDependencyError(message, errorType) {
    // First hide the confirmation form
    document.getElementById('deleteConfirmation').classList.add('d-none');
    
    // Update the message element
    const messageElement = document.getElementById('caremanagerDeleteMessage');
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
    if (errorType === 'dependency_beneficiaries') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>{{ T::translate('What you can do instead', 'Ang maaari mong gawin') }}:</strong>
                <p class="mt-2 mb-2">{{ T::translate('For audit and record-keeping purposes, care managers who have created or updated beneficiary records cannot be deleted from the system.', 'Para sa layunin ng audit at pag-iingat ng rekord, ang mga care manager na gumawa o nag-update ng mga rekord ng benepisyaryo ay hindi maaaring tanggalin mula sa system.') }}</p>
                <ol class="mt-2 mb-0">
                    <li>{{ T::translate('Instead of deleting, you can mark this care manager as inactive in their profile', 'Sa halip na tanggalin, maaari mong markahan ang care manager na ito bilang hindi aktibo sa kanilang profile') }}</li>
                    <li>{{ T::translate('This will prevent them from logging in while preserving the audit trail', 'Ito ay maiiwasan silang mag-log in habang pinapanatili ang audit trail') }}</li>
                    <li>{{ T::translate('Go to Care Manager List, find this care manager, and click the pencil icon in Actions (Edit) or just change their status from the dropdown', 'Pumunta sa Listahan ng Care Manager, hanapin ang care manager na ito, at i-click ang icon ng lapis sa Mga Aksyon (I-edit) o baguhin lamang ang kanilang katayuan mula sa dropdown') }}</li>
                    <li>{{ T::translate('Change their status from "Active" to "Inactive" and save the changes', 'Baguhin ang kanilang katayuan mula sa "Aktibo" patungo sa "Hindi Aktibo" at i-save ang mga pagbabago') }}</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_audit') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>{{ T::translate('What you can do instead', 'Ang maaari mong gawin') }}:</strong>
                <p class="mt-2 mb-2">{{ T::translate('This care manager has created or updated important records in the system. For audit and compliance reasons, these associations cannot be removed.', 'Ang care manager na ito ay gumawa o nag-update ng mahahalagang rekord sa system. Para sa mga kadahilanan ng audit at pagsunod, ang mga asosasyong ito ay hindi maaaring alisin.') }}</p>
                <ol class="mt-2 mb-0">
                    <li>{{ T::translate('Instead of deleting, you can mark this care manager as inactive in their profile', 'Sa halip na tanggalin, maaari mong markahan ang care manager na ito bilang hindi aktibo sa kanilang profile') }}</li>
                    <li>{{ T::translate('This will prevent them from logging in while preserving the audit trail', 'Ito ay maiiwasan silang mag-log in habang pinapanatili ang audit trail') }}</li>
                    <li>{{ T::translate('Go to Care Manager List, find this care manager, and click the pencil icon in Actions (Edit) or just change their status from the dropdown', 'Pumunta sa Listahan ng Care Manager, hanapin ang care manager na ito, at i-click ang icon ng lapis sa Mga Aksyon (I-edit) o baguhin lamang ang kanilang katayuan mula sa dropdown') }}</li>
                    <li>{{ T::translate('Change their status from "Active" to "Inactive" and save the changes', 'Baguhin ang kanilang katayuan mula sa "Aktibo" patungo sa "Hindi Aktibo" at i-save ang mga pagbabago') }}</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_users') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>{{ T::translate('What you can do instead', 'Ang maaari mong gawin') }}:</strong>
                <p class="mt-2 mb-2">{{ T::translate('This care manager is referenced in user accounts for audit purposes. These references must be maintained for record integrity.', 'Ang care manager na ito ay binanggit sa mga account ng user para sa layunin ng audit. Ang mga sangguniang ito ay dapat panatilihin para sa integridad ng rekord.') }}</p>
                <ol class="mt-2 mb-0">
                    <li>{{ T::translate('Instead of deleting, you can mark this care manager as inactive in their profile', 'Sa halip na tanggalin, maaari mong markahan ang care manager na ito bilang hindi aktibo sa kanilang profile') }}</li>
                    <li>{{ T::translate('This will prevent them from logging in while preserving the audit trail', 'Ito ay maiiwasan silang mag-log in habang pinapanatili ang audit trail') }}</li>
                    <li>{{ T::translate('Go to Care Manager List, find this care manager, and click the pencil icon in Actions (Edit) or just change their status from the dropdown', 'Pumunta sa Listahan ng Care Manager, hanapin ang care manager na ito, at i-click ang icon ng lapis sa Mga Aksyon (I-edit) o baguhin lamang ang kanilang katayuan mula sa dropdown') }}</li>
                    <li>{{ T::translate('Change their status from "Active" to "Inactive" and save the changes', 'Baguhin ang kanilang katayuan mula sa "Aktibo" patungo sa "Hindi Aktibo" at i-save ang mga pagbabago') }}</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_care_plans') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>{{ T::translate('What you can do instead', 'Ang maaari mong gawin') }}:</strong>
                <p class="mt-2 mb-2">{{ T::translate('This care manager has created or updated care plans which must maintain their audit history for compliance purposes.', 'Ang care manager na ito ay gumawa o nag-update ng mga care plan na dapat panatilihin ang kanilang kasaysayan ng audit para sa mga layunin ng pagsunod.') }}</p>
                <ol class="mt-2 mb-0">
                    <li>{{ T::translate('Instead of deleting, you can mark this care manager as inactive in their profile', 'Sa halip na tanggalin, maaari mong markahan ang care manager na ito bilang hindi aktibo sa kanilang profile') }}</li>
                    <li>{{ T::translate('This will prevent them from logging in while preserving the audit trail', 'Ito ay maiiwasan silang mag-log in habang pinapanatili ang audit trail') }}</li>
                    <li>{{ T::translate('Go to Care Manager List, find this care manager, and click the pencil icon in Actions (Edit) or just change their status from the dropdown', 'Pumunta sa Listahan ng Care Manager, hanapin ang care manager na ito, at i-click ang icon ng lapis sa Mga Aksyon (I-edit) o baguhin lamang ang kanilang katayuan mula sa dropdown') }}</li>
                    <li>{{ T::translate('Change their status from "Active" to "Inactive" and save the changes', 'Baguhin ang kanilang katayuan mula sa "Aktibo" patungo sa "Hindi Aktibo" at i-save ang mga pagbabago') }}</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_family') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>{{ T::translate('What you can do instead', 'Ang maaari mong gawin') }}:</strong>
                <p class="mt-2 mb-2">{{ T::translate('This care manager has created or updated family member records which must maintain their history for proper record-keeping.', 'Ang care manager na ito ay gumawa o nag-update ng mga rekord ng miyembro ng pamilya na dapat panatilihin ang kanilang kasaysayan para sa tamang pag-iingat ng rekord.') }}</p>
                <ol class="mt-2 mb-0">
                    <li>{{ T::translate('Instead of deleting, you can mark this care manager as inactive in their profile', 'Sa halip na tanggalin, maaari mong markahan ang care manager na ito bilang hindi aktibo sa kanilang profile') }}</li>
                    <li>{{ T::translate('This will prevent them from logging in while preserving the audit trail', 'Ito ay maiiwasan silang mag-log in habang pinapanatili ang audit trail') }}</li>
                    <li>{{ T::translate('Go to Care Manager List, find this care manager, and click the pencil icon in Actions (Edit) or just change their status from the dropdown', 'Pumunta sa Listahan ng Care Manager, hanapin ang care manager na ito, at i-click ang icon ng lapis sa Mga Aksyon (I-edit) o baguhin lamang ang kanilang katayuan mula sa dropdown') }}</li>
                    <li>{{ T::translate('Change their status from "Active" to "Inactive" and save the changes', 'Baguhin ang kanilang katayuan mula sa "Aktibo" patungo sa "Hindi Aktibo" at i-save ang mga pagbabago') }}</li>
                </ol>
            </div>
        `;
    }
    
    messageElement.innerHTML = errorContent;
    
    // Change the cancel button text
    document.getElementById('cancelDeleteButton').textContent = '{{ T::translate("Close", "Isara") }}';
    
    // Hide the delete button
    document.getElementById('confirmCaremanagerDeleteButton').classList.add('d-none');
}

// Function to show success message
function showSuccess() {
    document.getElementById('deleteConfirmation').classList.add('d-none');
    document.getElementById('deleteSuccess').classList.remove('d-none');
    document.getElementById('confirmCaremanagerDeleteButton').classList.add('d-none');
    document.getElementById('cancelDeleteButton').textContent = '{{ T::translate("Close", "Isara") }}';
    
    setTimeout(function() {
        window.location.href = "/admin/care-managers";
    }, 2000);
}

// Setup event handlers when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Password input event to clear error messages
    document.getElementById('caremanagerDeletePasswordInput').addEventListener('input', function() {
        document.getElementById('caremanagerDeleteMessage').classList.add('d-none');
    });
    
    // Delete confirmation button click handler
    document.getElementById('confirmCaremanagerDeleteButton').addEventListener('click', function() {
        const password = document.getElementById('caremanagerDeletePasswordInput').value.trim();
        const caremanagerId = document.getElementById('caremanagerIdToDelete').value;
        
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
        xhr1.open('POST', "/admin/validate-password", true);
        xhr1.onload = function() {
            if (xhr1.status === 200) {
                try {
                    const data = JSON.parse(xhr1.responseText);
                    if (data.valid) {
                        // Password is valid, proceed with deletion
                        const xhr2 = new XMLHttpRequest();
                        xhr2.open('POST', "/admin/care-managers/delete", true);
                        xhr2.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
                        xhr2.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                        xhr2.onload = function() {
                            console.log('Delete response status:', xhr2.status);
                            console.log('Delete response text:', xhr2.responseText);
                            
                            if (xhr2.status === 200) {
                                try {
                                    const response = JSON.parse(xhr2.responseText);
                                    if (response.success) {
                                        showSuccess();
                                    } else {
                                        if (response.error_type && (response.error_type === 'dependency' || response.error_type === 'dependency_beneficiaries')) {
                                            showDependencyError(response.message, response.error_type);
                                        } else {
                                            showError(response.message || '{{ T::translate("Failed to delete care manager.", "Nabigong tanggalin ang care manager.") }}');
                                            document.getElementById('confirmCaremanagerDeleteButton').disabled = false;
                                            document.getElementById('confirmCaremanagerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Care Manager", "Tanggalin ang Care Manager") }}';
                                        }
                                    }
                                } catch (e) {
                                    console.error('Error parsing JSON response:', e);
                                    showError('{{ T::translate("An unexpected error occurred. Please try again.", "Isang hindi inaasahang error ang naganap. Mangyaring subukan muli.") }}');
                                    document.getElementById('confirmCaremanagerDeleteButton').disabled = false;
                                    document.getElementById('confirmCaremanagerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Care Manager", "Tanggalin ang Care Manager") }}';
                                }
                            } else {
                                showError('{{ T::translate("Server error", "Error sa server") }}: ' + xhr2.status);
                                document.getElementById('confirmCaremanagerDeleteButton').disabled = false;
                                document.getElementById('confirmCaremanagerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Care Manager", "Tanggalin ang Care Manager") }}';
                            }
                        };
                        xhr2.onerror = function() {
                            showError('{{ T::translate("Network error. Please try again.", "Error sa network. Mangyaring subukan muli.") }}');
                            document.getElementById('confirmCaremanagerDeleteButton').disabled = false;
                            document.getElementById('confirmCaremanagerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Care Manager", "Tanggalin ang Care Manager") }}';
                        };
                        // Create the request body
                        const params = 'caremanager_id=' + encodeURIComponent(caremanagerId) + '&_token={{ csrf_token() }}';
                        // Send the request
                        xhr2.send(params);
                    } else {
                        showError('{{ T::translate("Incorrect password. Please try again.", "Maling password. Mangyaring subukan muli.") }}');
                        document.getElementById('confirmCaremanagerDeleteButton').disabled = false;
                        document.getElementById('confirmCaremanagerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Care Manager", "Tanggalin ang Care Manager") }}';
                    }
                } catch (e) {
                    console.error('Error parsing JSON response:', e);
                    showError('{{ T::translate("An unexpected error occurred during password validation. Please try again.", "Isang hindi inaasahang error ang naganap sa pag-validate ng password. Mangyaring subukan muli.") }}');
                    document.getElementById('confirmCaremanagerDeleteButton').disabled = false;
                    document.getElementById('confirmCaremanagerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Care Manager", "Tanggalin ang Care Manager") }}';
                }
            } else {
                showError('{{ T::translate("Password validation failed. Please try again.", "Nabigo ang pag-validate ng password. Mangyaring subukan muli.") }}');
                document.getElementById('confirmCaremanagerDeleteButton').disabled = false;
                document.getElementById('confirmCaremanagerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Care Manager", "Tanggalin ang Care Manager") }}';
            }
        };
        xhr1.onerror = function() {
            showError('{{ T::translate("Network error during password validation. Please try again.", "Error sa network habang nagva-validate ng password. Mangyaring subukan muli.") }}');
            document.getElementById('confirmCaremanagerDeleteButton').disabled = false;
            document.getElementById('confirmCaremanagerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Care Manager", "Tanggalin ang Care Manager") }}';
        };
        xhr1.send(formData);
    });
});
</script>