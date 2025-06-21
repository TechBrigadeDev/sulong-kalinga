@php
use App\Helpers\TranslationHelper as T;
@endphp
<div class="modal fade" id="deleteBeneficiaryModal" tabindex="-1" aria-labelledby="deleteBeneficiaryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:rgb(251, 68, 68);">
                <h5 class="modal-title text-white" id="deleteBeneficiaryModalLabel">{{ T::translate('Confirm Beneficiary Deletion', 'Kumpirmahin ang Pagtanggal ng Benepisyaryo') }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="beneficiaryDeleteMessage" class="alert d-none" role="alert"></div>
                
                <div id="deleteConfirmation">
                    <p class="text-danger">
                        <i class="bi bi-exclamation-circle"></i> 
                        <strong>{{ T::translate('Warning', 'Babala') }}!</strong> {{ T::translate('You are about to delete this beneficiary profile.', 'Tatanggalin mo na ang profile ng benepisyaryong ito.') }}
                    </p>
                    <p>{{ T::translate('Are you sure you want to permanently delete', 'Sigurado ka bang nais mong permanenteng tanggalin') }} <span id="beneficiaryNameToDelete" style="font-weight: bold;"></span>?</p>
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-info-circle"></i> 
                        <strong>{{ T::translate('Note', 'Paalala') }}:</strong> {{ T::translate('If this beneficiary profile is deleted, the general care plan associated to this beneficiary will also be deleted.', 'Kung tatanggalin ang profile ng benepisyaryong ito, ang general care plan na nakaugnay dito ay tatanggalin din.') }}
                    </div>
                    
                    <div class="mb-3">
                        <label for="beneficiaryDeletePasswordInput" class="form-label">{{ T::translate('Enter Your Password to Confirm', 'Ilagay ang Iyong Password upang Kumpirmahin') }}</label>
                        <input type="password" class="form-control" id="beneficiaryDeletePasswordInput" placeholder="{{ T::translate('Enter your password', 'Ilagay ang iyong password') }}" required>
                        <input type="hidden" id="beneficiaryIdToDelete" value="">
                    </div>
                </div>
                
                <div id="deleteSuccess" class="d-none">
                    <p class="text-success">
                        <i class="bi bi-check-circle"></i>
                        <strong>{{ T::translate('Success', 'Tagumpay') }}!</strong> {{ T::translate('The beneficiary has been deleted successfully.', 'Matagumpay na natanggal ang benepisyaryo.') }}
                    </p>
                    <p>{{ T::translate('You will be redirected to the beneficiary list shortly.', 'Ikaw ay maire-redirect sa listahan ng mga benepisyaryo sa ilang sandali.') }}</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelDeleteButton">{{ T::translate('Cancel', 'I-Kansela') }}</button>
                <button type="button" class="btn btn-danger" id="confirmBeneficiaryDeleteButton">
                    <i class="bi bi-trash-fill"></i> {{ T::translate('Delete Beneficiary', 'Tanggalin ang Benepisyaryo') }}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Function to open the delete modal
window.openDeleteBeneficiaryModal = function(id, name) {
    // Reset modal state
    document.getElementById('beneficiaryDeleteMessage').classList.add('d-none');
    document.getElementById('deleteConfirmation').classList.remove('d-none');
    document.getElementById('deleteSuccess').classList.add('d-none');
    document.getElementById('beneficiaryDeletePasswordInput').value = '';
    document.getElementById('beneficiaryIdToDelete').value = id;
    document.getElementById('beneficiaryNameToDelete').textContent = name;
    
    const confirmButton = document.getElementById('confirmBeneficiaryDeleteButton');
    confirmButton.disabled = false;
    confirmButton.innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate('Delete Beneficiary', 'Tanggalin ang Benepisyaryo') }}';
    confirmButton.classList.remove('d-none');
    
    document.getElementById('cancelDeleteButton').textContent = '{{ T::translate('Cancel', 'I-Kansela') }}';
    
    // Show the modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteBeneficiaryModal'));
    deleteModal.show();
}

// Function to show error message
function showError(message) {
    const messageElement = document.getElementById('beneficiaryDeleteMessage');
    messageElement.textContent = message;
    messageElement.classList.remove('d-none', 'alert-success', 'alert-warning');
    messageElement.classList.add('alert-danger');
}

// Function to show detailed error message with guidance
function showDependencyError(message, errorType) {
    // First hide the confirmation form
    document.getElementById('deleteConfirmation').classList.add('d-none');
    
    // Update the message element
    const messageElement = document.getElementById('beneficiaryDeleteMessage');
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
    if (errorType === 'dependency_weekly_care_plans') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>{{ T::translate('What you can do instead', 'Ang maaari mong gawin') }}:</strong>
                <p class="mt-2 mb-2">{{ T::translate('This beneficiary has weekly care plans which must be maintained for audit and historical purposes.', 'Ang benepisyaryong ito ay may mga weekly care plan na dapat panatilihin para sa audit at makasaysayang layunin.') }}</p>
                <ol class="mt-2 mb-0">
                    <li>{{ T::translate('Instead of deleting, you can mark this beneficiary as Inactive in their profile', 'Sa halip na tanggalin, maaari mong markahan ang benepisyaryong ito bilang Hindi Aktibo sa kanilang profile') }}</li>
                    <li>{{ T::translate('This will prevent any new care plans from being created while preserving existing records', 'Ito ay maiiwasan ang anumang mga bagong care plan na malikha habang pinapanatili ang mga umiiral na rekord') }}</li>
                    <li>{{ T::translate('Changing the beneficiary\'s status will also prevent the beneficiary and their family from logging into the system', 'Ang pagbabago ng katayuan ng benepisyaryo ay maiiwasan din ang benepisyaryo at kanilang pamilya na mag-login sa system') }}</li>
                    <li>{{ T::translate('Use the status dropdown at the Beneficiary Profile page to change their status', 'Gamitin ang status dropdown sa pahina ng Profile ng Benepisyaryo upang baguhin ang kanilang katayuan') }}</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_family') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>{{ T::translate('What you can do instead', 'Ang maaari mong gawin') }}:</strong>
                <p class="mt-2 mb-2">{{ T::translate('This beneficiary is linked to family members in the system.', 'Ang benepisyaryong ito ay naka-link sa mga miyembro ng pamilya sa system.') }}</p>
                <ol class="mt-2 mb-0">
                    <li>{{ T::translate('First, remove the beneficiary relationship from linked family members', 'Una, alisin ang relasyon ng benepisyaryo mula sa mga naka-link na miyembro ng pamilya') }}</li>
                    <li>{{ T::translate('Go to each family member\'s profile and edit their details', 'Pumunta sa profile ng bawat miyembro ng pamilya at i-edit ang kanilang mga detalye') }}</li>
                    <li>{{ T::translate('Remove this beneficiary as their related beneficiary', 'Alisin ang benepisyaryong ito bilang kanilang kaugnay na benepisyaryo') }}</li>
                    <li>{{ T::translate('After all relationships are removed, you can try deleting this beneficiary again', 'Matapos maalis ang lahat ng mga relasyon, maaari mong subukang tanggalin muli ang benepisyaryong ito') }}</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_audit') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>{{ T::translate('What you can do instead', 'Ang maaari mong gawin') }}:</strong>
                <p class="mt-2 mb-2">{{ T::translate('This beneficiary has records in the system that require audit history to be maintained.', 'Ang benepisyaryong ito ay may mga rekord sa system na nangangailangan ng kasaysayan ng audit na mapanatili.') }}</p>
                <ol class="mt-2 mb-0">
                    <li>{{ T::translate('Instead of deleting, you can mark this beneficiary as Inactive in their profile', 'Sa halip na tanggalin, maaari mong markahan ang benepisyaryong ito bilang Hindi Aktibo sa kanilang profile') }}</li>
                    <li>{{ T::translate('This will prevent any new care plans from being created while preserving the audit trail', 'Ito ay maiiwasan ang anumang mga bagong care plan na malikha habang pinapanatili ang audit trail') }}</li>
                    <li>{{ T::translate('Use the status dropdown at the top of the beneficiary profile to change their status', 'Gamitin ang status dropdown sa itaas ng profile ng benepisyaryo upang baguhin ang kanilang katayuan') }}</li>
                </ol>
            </div>
        `;
    }
    
    messageElement.innerHTML = errorContent;
    
    // Change the cancel button text
    document.getElementById('cancelDeleteButton').textContent = '{{ T::translate('Close', 'Isara') }}';
    
    // Hide the delete button
    document.getElementById('confirmBeneficiaryDeleteButton').classList.add('d-none');
}

// Function to show success message
function showSuccess(deletedCarePlan) {
    document.getElementById('deleteConfirmation').classList.add('d-none');
    
    // Create enhanced success message without animations
    const successElement = document.getElementById('deleteSuccess');
    successElement.innerHTML = `
        <div class="text-center mb-3">
            <div class="mb-3">
                <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
            </div>
            <h4 class="text-success mb-3">{{ T::translate('Successfully Deleted', 'Matagumpay na Natanggal') }}</h4>
            <div class="alert alert-light border py-3">
                <p class="mb-1">{{ T::translate('The beneficiary profile has been permanently removed from the system.', 'Ang profile ng benepisyaryo ay permanenteng natanggal na mula sa system.') }}</p>
                <div class="mt-2 pt-2 border-top">
                    <p class="mb-0 text-muted">
                        <i class="bi bi-info-circle"></i> {{ T::translate('The associated general care plan and related records have also been deleted.', 'Ang nauugnay na general care plan at mga kaugnay na rekord ay natanggal na rin.') }}
                    </p>
                </div>
            </div>
            <p class="text-muted mt-3">
                <i class="bi bi-clock"></i> {{ T::translate('You will be redirected to the beneficiary list in a few seconds...', 'Ikaw ay maire-redirect sa listahan ng mga benepisyaryo sa ilang segundo...') }}
            </p>
        </div>
    `;
    
    // Show the success message
    successElement.classList.remove('d-none');
    
    // Hide delete button and change cancel button text
    document.getElementById('confirmBeneficiaryDeleteButton').classList.add('d-none');
    document.getElementById('cancelDeleteButton').textContent = '{{ T::translate('Close', 'Isara') }}';
    
    // Redirect after a delay
    setTimeout(function() {
        @if(Auth::user()->role_id == 1)
            window.location.href = "/admin/beneficiaries";
        @else
            window.location.href = "/care-manager/beneficiaries";
        @endif
    }, 3000);
}

// Setup event handlers when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Password input event to clear error messages
    document.getElementById('beneficiaryDeletePasswordInput').addEventListener('input', function() {
        document.getElementById('beneficiaryDeleteMessage').classList.add('d-none');
    });
    
    // Delete confirmation button click handler
    document.getElementById('confirmBeneficiaryDeleteButton').addEventListener('click', function() {
        const password = document.getElementById('beneficiaryDeletePasswordInput').value.trim();
        const beneficiaryId = document.getElementById('beneficiaryIdToDelete').value;
        
        if (!password) {
            showError('{{ T::translate('Please enter your password to confirm deletion.', 'Mangyaring ilagay ang iyong password upang kumpirmahin ang pagtanggal.') }}');
            return;
        }
        
        // Show loading state
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> {{ T::translate('Deleting...', 'Tinatanggal...') }}';
        
        // First verify password via standard form submission
        let formData = new FormData();
        formData.append('password', password);
        formData.append('_token', '{{ csrf_token() }}');
        
        const xhr1 = new XMLHttpRequest();
        @if(Auth::user()->role_id == 1)
            xhr1.open('POST', "/admin/validate-password", true);
        @else
            xhr1.open('POST', "/care-manager/validate-password", true);
        @endif

        // For password validation (xhr1)
        xhr1.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr1.onload = function() {
            if (xhr1.status === 200) {
                try {
                    const data = JSON.parse(xhr1.responseText);
                    if (data.valid) {
                        // Password is valid, proceed with deletion
                        let deleteForm = new FormData();
                        deleteForm.append('beneficiary_id', beneficiaryId);
                        deleteForm.append('_token', '{{ csrf_token() }}');
                        
                        // Determine which endpoint to use based on the user role
                        let endpoint = "/admin/beneficiaries/delete";

                        // Use care manager endpoint if the current user is a care manager
                        @if(Auth::user()->role_id == 2)
                            endpoint = "/care-manager/beneficiaries/delete";
                        @endif
                        
                        const xhr2 = new XMLHttpRequest();
                        xhr2.open('POST', endpoint, true);
                         // For beneficiary deletion (xhr2)
                        xhr2.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
                        xhr2.onload = function() {
                            console.log('Delete response status:', xhr2.status);
                            console.log('Delete response text:', xhr2.responseText);
                            
                            if (xhr2.status === 200) {
                                try {
                                    const response = JSON.parse(xhr2.responseText);
                                    console.log('Parsed response:', response);
                                    
                                    if (response.success) {
                                        showSuccess(response.deleted_care_plan);
                                    } else {
                                        // Check for specific error types
                                        if (response.error_type) {
                                            showDependencyError(response.message, response.error_type);
                                        } else {
                                            showError(response.message || '{{ T::translate('Failed to delete beneficiary.', 'Nabigong tanggalin ang benepisyaryo.') }}');
                                            document.getElementById('confirmBeneficiaryDeleteButton').disabled = false;
                                            document.getElementById('confirmBeneficiaryDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate('Delete Beneficiary', 'Tanggalin ang Benepisyaryo') }}';
                                        }
                                    }
                                } catch (e) {
                                    console.error('Error parsing JSON response:', e);
                                    showError('{{ T::translate('An unexpected error occurred. Please try again.', 'Isang hindi inaasahang error ang naganap. Mangyaring subukan muli.') }}');
                                    document.getElementById('confirmBeneficiaryDeleteButton').disabled = false;
                                    document.getElementById('confirmBeneficiaryDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate('Delete Beneficiary', 'Tanggalin ang Benepisyaryo') }}';
                                }
                            } else {
                                showError('{{ T::translate('Server error', 'Error sa server') }}: ' + xhr2.status);
                                document.getElementById('confirmBeneficiaryDeleteButton').disabled = false;
                                document.getElementById('confirmBeneficiaryDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate('Delete Beneficiary', 'Tanggalin ang Benepisyaryo') }}';
                            }
                        };
                        xhr2.onerror = function() {
                            showError('{{ T::translate('Network error. Please try again.', 'Error sa network. Mangyaring subukan muli.') }}');
                            document.getElementById('confirmBeneficiaryDeleteButton').disabled = false;
                            document.getElementById('confirmBeneficiaryDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate('Delete Beneficiary', 'Tanggalin ang Benepisyaryo') }}';
                        };
                        xhr2.send(deleteForm);
                    } else {
                        showError('{{ T::translate('Incorrect password. Please try again.', 'Maling password. Mangyaring subukan muli.') }}');
                        document.getElementById('confirmBeneficiaryDeleteButton').disabled = false;
                        document.getElementById('confirmBeneficiaryDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate('Delete Beneficiary', 'Tanggalin ang Benepisyaryo') }}';
                    }
                } catch (e) {
                    console.error('Error parsing JSON response:', e);
                    showError('{{ T::translate('An unexpected error occurred during password validation. Please try again.', 'Isang hindi inaasahang error ang naganap sa pag-validate ng password. Mangyaring subukan muli.') }}');
                    document.getElementById('confirmBeneficiaryDeleteButton').disabled = false;
                    document.getElementById('confirmBeneficiaryDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate('Delete Beneficiary', 'Tanggalin ang Benepisyaryo') }}';
                }
            } else {
                showError('{{ T::translate('Password validation failed. Please try again.', 'Nabigo ang pag-validate ng password. Mangyaring subukan muli.') }}');
                document.getElementById('confirmBeneficiaryDeleteButton').disabled = false;
                document.getElementById('confirmBeneficiaryDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate('Delete Beneficiary', 'Tanggalin ang Benepisyaryo') }}';
            }
        };
        xhr1.onerror = function() {
            showError('{{ T::translate('Network error during password validation. Please try again.', 'Error sa network habang nagva-validate ng password. Mangyaring subukan muli.') }}');
            document.getElementById('confirmBeneficiaryDeleteButton').disabled = false;
            document.getElementById('confirmBeneficiaryDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate('Delete Beneficiary', 'Tanggalin ang Benepisyaryo') }}';
        };
        xhr1.send(formData);
    });
});
</script>