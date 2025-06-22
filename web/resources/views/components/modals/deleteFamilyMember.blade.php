@php
use App\Helpers\TranslationHelper as T;
@endphp

<div class="modal fade" id="deleteFamilyMemberModal" tabindex="-1" aria-labelledby="deleteFamilyMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:rgb(251, 68, 68);">
                <h5 class="modal-title text-white" id="deleteFamilyMemberModalLabel">{{ T::translate('Confirm Family Member Deletion', 'Kumpirmahin ang Pagtanggal ng Miyembro ng Pamilya') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="familyMemberDeleteMessage" class="alert d-none" role="alert"></div>
                
                <div id="deleteConfirmation">
                    <p class="text-danger">
                        <i class="bi bi-exclamation-circle"></i> 
                        <strong>{{ T::translate('Warning', 'Babala') }}!</strong> {{ T::translate('You are about to delete this family member.', 'Tatanggalin mo na ang miyembro ng pamilyang ito.') }}
                    </p>
                    <p>{{ T::translate('Are you sure you want to permanently delete', 'Sigurado ka bang nais mong permanenteng tanggalin') }} <span id="familyMemberNameToDelete" style="font-weight: bold;"></span>?</p>
                    <div class="mb-3">
                        <label for="familyMemberDeletePasswordInput" class="form-label">{{ T::translate('Enter Your Password to Confirm', 'Ilagay ang Iyong Password upang Kumpirmahin') }}</label>
                        <input type="password" class="form-control" id="familyMemberDeletePasswordInput" placeholder="{{ T::translate('Enter your password', 'Ilagay ang iyong password') }}" required>
                        <input type="hidden" id="familyMemberIdToDelete" value="">
                    </div>
                </div>
                
                <div id="deleteSuccess" class="d-none">
                    <p class="text-success">
                        <i class="bi bi-check-circle"></i>
                        <strong>{{ T::translate('Success', 'Tagumpay') }}!</strong> {{ T::translate('The family member has been deleted successfully.', 'Matagumpay na natanggal ang miyembro ng pamilya.') }}
                    </p>
                    <p>{{ T::translate('You will be redirected to the family member list shortly.', 'Ikaw ay maire-redirect sa listahan ng mga miyembro ng pamilya sa ilang sandali.') }}</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelDeleteButton">{{ T::translate('Cancel', 'I-Kansela') }}</button>
                <button type="button" class="btn btn-danger" id="confirmFamilyMemberDeleteButton">
                    <i class="bi bi-trash-fill"></i> {{ T::translate('Delete Family Member', 'Tanggalin ang Miyembro ng Pamilya') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Function to open the delete modal
window.openDeleteFamilyMemberModal = function(id, name) {
    // Reset modal state
    document.getElementById('familyMemberDeleteMessage').classList.add('d-none');
    document.getElementById('deleteConfirmation').classList.remove('d-none');
    document.getElementById('deleteSuccess').classList.add('d-none');
    document.getElementById('familyMemberDeletePasswordInput').value = '';
    document.getElementById('familyMemberIdToDelete').value = id;
    document.getElementById('familyMemberNameToDelete').textContent = name;
    
    const confirmButton = document.getElementById('confirmFamilyMemberDeleteButton');
    confirmButton.disabled = false;
    confirmButton.innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Family Member", "Tanggalin ang Miyembro ng Pamilya") }}';
    confirmButton.classList.remove('d-none');
    
    document.getElementById('cancelDeleteButton').textContent = '{{ T::translate("Cancel", "I-Kansela") }}';
    
    // Show the modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteFamilyMemberModal'));
    deleteModal.show();
}

// Function to show error message
function showError(message) {
    const messageElement = document.getElementById('familyMemberDeleteMessage');
    messageElement.textContent = message;
    messageElement.classList.remove('d-none', 'alert-success');
    messageElement.classList.add('alert-danger');
}

// Function to show detailed error message with guidance
function showDependencyError(message, errorType) {
    // First hide the confirmation form
    document.getElementById('deleteConfirmation').classList.add('d-none');
    
    // Update the message element
    const messageElement = document.getElementById('familyMemberDeleteMessage');
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
                <strong>{{ T::translate("We're sorry", "Paumanhin") }}:</strong>
                <p class="mt-2 mb-2">{{ T::translate('This family member has acknowledged weekly care plans and cannot be deleted for data integrity purposes.', 'Ang miyembro ng pamilyang ito ay may kinikilalang mga weekly care plan at hindi maaaring tanggalin para sa integridad ng datos.') }}</p>
            </div>
        `;
    } else if (errorType === 'dependency_audit') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>{{ T::translate("We're sorry", "Paumanhin") }}:</strong>
                <p class="mt-2 mb-2">{{ T::translate('This family member has records in the system that require audit history to be maintained.', 'Ang miyembro ng pamilyang ito ay may mga rekord sa system na nangangailangan ng kasaysayan ng audit na mapanatili.') }}</p>
            </div>
        `;
    }
    
    messageElement.innerHTML = errorContent;
    
    // Change the cancel button text
    document.getElementById('cancelDeleteButton').textContent = '{{ T::translate("Close", "Isara") }}';
    
    // Hide the delete button
    document.getElementById('confirmFamilyMemberDeleteButton').classList.add('d-none');
}

// Function to show success message
function showSuccess() {
    document.getElementById('deleteConfirmation').classList.add('d-none');
    document.getElementById('deleteSuccess').classList.remove('d-none');
    document.getElementById('confirmFamilyMemberDeleteButton').classList.add('d-none');
    document.getElementById('cancelDeleteButton').textContent = '{{ T::translate("Close", "Isara") }}';
    
    // Use role-specific redirect route
    let redirectRoute = "/admin/families"; // Default for admin
    
    // Use care manager route if current user is a care manager
    @if(Auth::user()->role_id == 2)
        redirectRoute = "/care-manager/families";
    @endif
    
    setTimeout(function() {
        window.location.href = redirectRoute;
    }, 2000);
}

// Setup event handlers when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Password input event to clear error messages
    document.getElementById('familyMemberDeletePasswordInput').addEventListener('input', function() {
        document.getElementById('familyMemberDeleteMessage').classList.add('d-none');
    });
    
    // Delete confirmation button click handler
    document.getElementById('confirmFamilyMemberDeleteButton').addEventListener('click', function() {
        const password = document.getElementById('familyMemberDeletePasswordInput').value.trim();
        const familyMemberId = document.getElementById('familyMemberIdToDelete').value;
        
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
                        deleteForm.append('family_member_id', familyMemberId);
                        deleteForm.append('_token', '{{ csrf_token() }}');
                        
                        // Determine which endpoint to use based on the user role
                        let endpoint = "/admin/families/delete"; // Default endpoint for admins

                        // Use care manager endpoint if the current user is a care manager
                        @if(Auth::user()->role_id == 2)
                            endpoint = "/care-manager/families/delete";
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
                                            showError(response.message || '{{ T::translate("Failed to delete family member.", "Nabigong tanggalin ang miyembro ng pamilya.") }}');
                                            document.getElementById('confirmFamilyMemberDeleteButton').disabled = false;
                                            document.getElementById('confirmFamilyMemberDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Family Member", "Tanggalin ang Miyembro ng Pamilya") }}';
                                        }
                                    }
                                } catch (e) {
                                    console.error('Error parsing JSON response:', e);
                                    showError('{{ T::translate("An unexpected error occurred. Please try again.", "Isang hindi inaasahang error ang naganap. Mangyaring subukan muli.") }}');
                                    document.getElementById('confirmFamilyMemberDeleteButton').disabled = false;
                                    document.getElementById('confirmFamilyMemberDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Family Member", "Tanggalin ang Miyembro ng Pamilya") }}';
                                }
                            } else {
                                showError('{{ T::translate("Server error", "Error sa server") }}: ' + xhr2.status);
                                document.getElementById('confirmFamilyMemberDeleteButton').disabled = false;
                                document.getElementById('confirmFamilyMemberDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Family Member", "Tanggalin ang Miyembro ng Pamilya") }}';
                            }
                        };
                        xhr2.onerror = function() {
                            showError('{{ T::translate("Network error. Please try again.", "Error sa network. Mangyaring subukan muli.") }}');
                            document.getElementById('confirmFamilyMemberDeleteButton').disabled = false;
                            document.getElementById('confirmFamilyMemberDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Family Member", "Tanggalin ang Miyembro ng Pamilya") }}';
                        };
                        xhr2.send(deleteForm);
                    } else {
                        showError('{{ T::translate("Incorrect password. Please try again.", "Maling password. Mangyaring subukan muli.") }}');
                        document.getElementById('confirmFamilyMemberDeleteButton').disabled = false;
                        document.getElementById('confirmFamilyMemberDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Family Member", "Tanggalin ang Miyembro ng Pamilya") }}';
                    }
                } catch (e) {
                    console.error('Error parsing JSON response:', e);
                    showError('{{ T::translate("An unexpected error occurred during password validation. Please try again.", "Isang hindi inaasahang error ang naganap sa pag-validate ng password. Mangyaring subukan muli.") }}');
                    document.getElementById('confirmFamilyMemberDeleteButton').disabled = false;
                    document.getElementById('confirmFamilyMemberDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Family Member", "Tanggalin ang Miyembro ng Pamilya") }}';
                }
            } else {
                showError('{{ T::translate("Password validation failed. Please try again.", "Nabigo ang pag-validate ng password. Mangyaring subukan muli.") }}');
                document.getElementById('confirmFamilyMemberDeleteButton').disabled = false;
                document.getElementById('confirmFamilyMemberDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Family Member", "Tanggalin ang Miyembro ng Pamilya") }}';
            }
        };
        xhr1.onerror = function() {
            showError('{{ T::translate("Network error during password validation. Please try again.", "Error sa network habang nagva-validate ng password. Mangyaring subukan muli.") }}');
            document.getElementById('confirmFamilyMemberDeleteButton').disabled = false;
            document.getElementById('confirmFamilyMemberDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate("Delete Family Member", "Tanggalin ang Miyembro ng Pamilya") }}';
        };
        xhr1.send(formData);
    });
});
</script>