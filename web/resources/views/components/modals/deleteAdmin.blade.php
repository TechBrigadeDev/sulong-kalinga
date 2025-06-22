@php
use App\Helpers\TranslationHelper as T;
@endphp
<div class="modal fade" id="deleteAdminModal" tabindex="-1" aria-labelledby="deleteAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:rgb(251, 68, 68);">
                <h5 class="modal-title text-white" id="deleteAdminModalLabel">{{ T::translate('Confirm Administrator Deletion', 'Kumpirmahin ang Pagtanggal sa Administrator')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="adminDeleteMessage" class="alert d-none" role="alert"></div>
                
                <div id="deleteConfirmation">
                    <p class="text-danger">
                        <i class="bi bi-exclamation-circle"></i> 
                        <strong>{{ T::translate('Warning', 'Babala')}}!</strong> {{ T::translate('You are about to delete this administrator account.', 'Tatanggalin mo na ang administrator account na ito.')}}
                    </p>
                    <p>{{ T::translate('Are you sure you want to permanently delete', 'Sigurado ka bang nais mong permanenteng tanggalin')}} <span id="adminNameToDelete" style="font-weight: bold;"></span>?</p>
                    <div class="mb-3">
                        <label for="adminDeletePasswordInput" class="form-label">{{ T::translate('Enter Your Password to Confirm', 'Ilagay ang Iyong Password upang Kumpirmahin')}}</label>
                        <input type="password" class="form-control" id="adminDeletePasswordInput" placeholder="{{ T::translate('Enter your password', 'Ilagay ang iyong password')}}" required>
                        <input type="hidden" id="adminIdToDelete" value="">
                    </div>
                </div>
                
                <div id="deleteSuccess" class="d-none">
                    <p class="text-success">
                        <i class="bi bi-check-circle"></i>
                        <strong>{{ T::translate('Success', 'Tagumpay')}}!</strong> {{ T::translate('The administrator has been deleted successfully', 'Ang administrator ay matagumpay na natanggal')}}.
                    </p>
                    <p>{{ T::translate('You will be redirected to the administrator list shortly', 'Ikaw ay mare-redirect sa listahan ng administrator sa ilang sandali.')}}.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelDeleteButton">{{ T::translate('Cancel', 'I-kansela')}}</button>
                <button type="button" class="btn btn-danger" id="confirmAdminDeleteButton">
                    <i class="bi bi-trash-fill"></i> {{ T::translate('Delete Administrator', 'Tanggalin ang Administrator')}}
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Function to open the delete modal
window.openDeleteAdminModal = function(id, name) {
    // Reset modal state
    document.getElementById('adminDeleteMessage').classList.add('d-none');
    document.getElementById('deleteConfirmation').classList.remove('d-none');
    document.getElementById('deleteSuccess').classList.add('d-none');
    document.getElementById('adminDeletePasswordInput').value = '';
    document.getElementById('adminIdToDelete').value = id;
    document.getElementById('adminNameToDelete').textContent = name;
    
    const confirmButton = document.getElementById('confirmAdminDeleteButton');
    confirmButton.disabled = false;
    confirmButton.innerHTML = '<i class="bi bi-trash"></i> {{ T::translate('Delete Administrator', 'Tanggalin ang Admininstrator')}}';
    confirmButton.classList.remove('d-none');
    
    document.getElementById('cancelDeleteButton').textContent = '{{ T::translate('Cancel', 'I-Kansela')}}';
    
    // Show the modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteAdminModal'));
    deleteModal.show();
}

// Function to show error message
function showError(message) {
    const messageElement = document.getElementById('adminDeleteMessage');
    messageElement.textContent = message;
    messageElement.classList.remove('d-none', 'alert-success');
    messageElement.classList.add('alert-danger');
}

// Function to show detailed error message with guidance
function showDependencyError(message, errorType) {
    // First hide the confirmation form
    document.getElementById('deleteConfirmation').classList.add('d-none');
    
    // Update the message element
    const messageElement = document.getElementById('adminDeleteMessage');
    messageElement.classList.remove('d-none', 'alert-success', 'alert-warning');
    messageElement.classList.add('alert-danger');
    
    // Create structured error content with icon and guidance
    let errorContent = `
        <div class="d-flex align-items-center mb-2">
            <i class="bi bi-exclamation-circle me-2" style="font-size: 1.5rem;"></i>
            <strong>{{ T::translate('Unable to Delete', 'Hindi Ma-tanggal')}}</strong>
        </div>
        <p>${message}</p>
    `;
    
    // Add specific guidance based on error type
    if (errorType === 'dependency_beneficiaries') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>What you can do instead:</strong>
                <p class="mt-2 mb-2">{{ T::translate('For audit and record-keeping purposes, administrators who have created or updated beneficiary records cannot be deleted from the system.', 'Para sa mga layunin ng pag-audit at pag-iingat ng rekord, ang mga administrator na gumawa o nag-update ng mga talaan ng benepisyaryo ay hindi maaaring tanggalin sa system.')}}</p>
                <ol class="mt-2 mb-0">
                    <li>{{ T::translate('Instead of deleting, you can mark this administrator as', 'Sa halip na tanggalin, markahan ang administrator na ito bilang')}} <strong>{{ T::translate('inactive', 'di-aktibo')}}</strong> {{ T::translate('in their profile', 'sa kanilang profile')}}</li>
                    <li>{{ T::translate('This will prevent them from logging in while preserving the audit trail', 'Pipigilan nito silang mag-log in habang pinapanatili ang audit trail')}}</li>
                    <li>{{ T::translate('Go to', 'Pumunta sa')}} <a href="{{ route('admin.administrators.index') }}">{{ T::translate('Administrator List', 'Listahan ng Administrator')}}</a>, {{ T::translate('find this administrator, and click "Edit"', 'hanapin ang administrator na ito, at pindutin ang \"Edit\"')}}</li>
                    <li>{{ T::translate('Change their status from "Active" to "Inactive" and save the changes', 'Baguhin ang kanilang status mula \"Aktibo\" sa \"Di-Aktibo\" at i-save ang mga pagbabago.')}}</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_audit') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>{{ T::translate('What you can do instead', 'Sa halip ang maari mong gawin')}}:</strong>
                <p class="mt-2 mb-2">{{ T::translate('This administrator has created or updated important records in the system. For audit and compliance reasons, these associations cannot be removed.', 'Ang administrator na ito ay gumawa o nag-update ng mahahalagang tala sa system. Para sa mga dahilan ng pag-audit at pagsunod, hindi maaaring alisin ang mga asosasyong ito.')}}</p>
                <ol class="mt-2 mb-0">
                    <li>{{ T::translate('Instead of deleting, you can mark this administrator as', 'Sa halip na tanggalin, markahan ang administrator bilang')}} <strong>{{ T::translate('inactive', 'di-aktibo')}}</strong> {{ T::translate('in their profile', 'sa kanilang profile')}}</li>
                    <li>{{ T::translate('This will prevent them from logging in while preserving the audit trail', 'Pipigilan nito silang mag-log in habang pinapanatili ang audit trail')}}</li>
                    <li>{{ T::translate('Go to', 'Pumunta sa')}} <a href="{{ route('admin.administrators.index') }}">{{ T::translate('Administrator List', 'Listahan ng Administrator')}}</a>, {{ T::translate('find this administrator, and click "Edit"', 'hanapin ang administrator, at pindutin \"Edit\"')}}</li>
                    <li>{{ T::translate('Change their status from "Active" to "Inactive" and save the changes', 'Baguhin ang kanilang status mula \"Aktibo\" sa \"Di-Aktibo\" at i-save ang mga pagbabago.')}}</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_users') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>What you can do instead:</strong>
                <p class="mt-2 mb-2">{{ T::translate('This administrator is referenced in user accounts for audit purposes. These references must be maintained for record integrity.', 'Ang administrator na ito ay isinangguni sa mga user account para sa mga layunin ng pag-audit. Ang mga sanggunian na ito ay dapat mapanatili para sa integridad ng talaan.')}}</p>
                <ol class="mt-2 mb-0">
                    <li>{{ T::translate('Instead of deleting, you can mark this administrator as', 'Sa halip na tanggalin, markahan ang administrator bilang')}} <strong>{{ T::translate('inactive', 'di-aktibo')}}</strong> {{ T::translate('in their profile', 'sa kanilang profile')}}</li>
                    <li>{{ T::translate('This will prevent them from logging in while preserving the audit trail', 'Pipigilan nito silang mag-log in habang pinapanatili ang audit trail')}}</li>
                    <li>{{ T::translate('Go to', 'Pumunta sa')}} <a href="{{ route('admin.administrators.index') }}">{{ T::translate('Administrator List', 'Listahan ng Administrator')}}</a>, {{ T::translate('find this administrator, and click "Edit"', 'hanapin ang administrator, at pindutin \"Edit\"')}}</li><li>Go to <a href="{{ route('admin.administrators.index') }}">Administrator List</a>, find this administrator, and click "Edit"</li>
                    <li>{{ T::translate('Change their status from "Active" to "Inactive" and save the changes', 'Baguhin ang kanilang status mula \"Aktibo\" sa \"Di-Aktibo\" at i-save ang mga pagbabago.')}}</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_care_plans') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>{{ T::translate('What you can do instead', 'Ang maaari mong gawin')}}:</strong>
                <p class="mt-2 mb-2">{{ T::translate('This administrator has created or updated care plans which must maintain their audit history for compliance purposes.', 'Ang administrator na ito ay gumawa o nag-update ng mga plano sa pangangalaga na dapat panatilihin ang kanilang kasaysayan ng pag-audit para sa mga layunin ng pagsunod.')}}</p>
                <ol class="mt-2 mb-0">
                    <li>{{ T::translate('Instead of deleting, you can mark this administrator as', 'Sa halip na tanggalin, markahan ang administrator bilang')}} <strong>{{ T::translate('inactive', 'di-aktibo')}}</strong> {{ T::translate('in their profile', 'sa kanilang profile')}}</li>
                    <li>{{ T::translate('This will prevent them from logging in while preserving the audit trail', 'Pipigilan nito silang mag-log in habang pinapanatili ang audit trail')}}</li>
                    <li>{{ T::translate('Go to', 'Pumunta sa')}} <a href="{{ route('admin.administrators.index') }}">{{ T::translate('Administrator List', 'Listahan ng Administrator')}}</a>, {{ T::translate('find this administrator, and click "Edit"', 'hanapin ang administrator, at pindutin \"Edit\"')}}</li><li>Go to <a href="{{ route('admin.administrators.index') }}">Administrator List</a>, find this administrator, and click "Edit"</li>
                    <li>{{ T::translate('Change their status from "Active" to "Inactive" and save the changes', 'Baguhin ang kanilang status mula \"Aktibo\" sa \"Di-Aktibo\" at i-save ang mga pagbabago.')}}</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_family') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>{{ T::translate('What you can do instead', 'Ang maaari mong gawin')}}:</strong>
                <p class="mt-2 mb-2">{{ T::translate('This administrator has created or updated family member records which must maintain their history for proper record-keeping.', 'Ang administrator na ito ay gumawa o nag-update ng mga rekord ng miyembro ng pamilya na dapat panatilihin ang kanilang kasaysayan para sa wastong pag-iingat ng talaan.')}}</p>
                <ol class="mt-2 mb-0">
                    <li>{{ T::translate('Instead of deleting, you can mark this administrator as', 'Sa halip na tanggalin, markahan ang administrator bilang')}} <strong>{{ T::translate('inactive', 'di-aktibo')}}</strong> {{ T::translate('in their profile', 'sa kanilang profile')}}</li>
                    <li>{{ T::translate('This will prevent them from logging in while preserving the audit trail', 'Pipigilan nito silang mag-log in habang pinapanatili ang audit trail')}}</li>
                    <li>{{ T::translate('Go to', 'Pumunta sa')}} <a href="{{ route('admin.administrators.index') }}">{{ T::translate('Administrator List', 'Listahan ng Administrator')}}</a>, {{ T::translate('find this administrator, and click "Edit"', 'hanapin ang administrator, at pindutin \"Edit\"')}}</li><li>Go to <a href="{{ route('admin.administrators.index') }}">Administrator List</a>, find this administrator, and click "Edit"</li>
                    <li>{{ T::translate('Change their status from "Active" to "Inactive" and save the changes', 'Baguhin ang kanilang status mula \"Aktibo\" sa \"Di-Aktibo\" at i-save ang mga pagbabago.')}}</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'executive_director') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>{{ T::translate('Executive Director Cannot Be Deleted', 'Hindi maaring Tanggalin ang Executive Director')}}</strong>
                <p class="mt-2 mb-2">{{ T::translate('The Executive Director account is a required system role and cannot be deleted. This ensures continuity of administrative oversight.', 'Ang account ng Executive Director ay isang kinakailangang tungkulin ng system at hindi maaaring tanggalin. Tinitiyak nito ang pagpapatuloy ng pangangasiwa ng administratibo.')}}</p>
                <p>{{ T::translate('The Executive Director should be transferred to another staff member before the current Executive Director can be made inactive.', 'Ang Executive Director ay dapat ilipat sa ibang miyembro ng kawani bago ang kasalukuyang Executive Director ay maaaring gawing hindi aktibo.')}}</p>
            </div>
        `;
    }
    
    messageElement.innerHTML = errorContent;
    
    // Change the cancel button text
    document.getElementById('cancelDeleteButton').textContent = 'Close';
    
    // Hide the delete button
    document.getElementById('confirmAdminDeleteButton').classList.add('d-none');
}

// Function to show success message
function showSuccess() {
    document.getElementById('deleteConfirmation').classList.add('d-none');
    document.getElementById('deleteSuccess').classList.remove('d-none');
    document.getElementById('confirmAdminDeleteButton').classList.add('d-none');
    document.getElementById('cancelDeleteButton').textContent = 'Close';
    
    setTimeout(function() {
        window.location.href = "/admin/administrators";
    }, 2000);
}

// Setup event handlers when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Password input event to clear error messages
    document.getElementById('adminDeletePasswordInput').addEventListener('input', function() {
        document.getElementById('adminDeleteMessage').classList.add('d-none');
    });
    
    // Delete confirmation button click handler
    document.getElementById('confirmAdminDeleteButton').addEventListener('click', function() {
        const password = document.getElementById('adminDeletePasswordInput').value.trim();
        const adminId = document.getElementById('adminIdToDelete').value;
        
        if (!password) {
            showError('Please enter your password to confirm deletion.');
            return;
        }
        
        // Show loading state
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...';
        
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
                        let deleteForm = new FormData();
                        deleteForm.append('admin_id', adminId);
                        deleteForm.append('_token', '{{ csrf_token() }}');
                        
                        const xhr2 = new XMLHttpRequest();
                        xhr2.open('POST', "/admin/administrators/delete", true);
                        xhr2.onload = function() {
                            if (xhr2.status === 200) {
                                try {
                                    const response = JSON.parse(xhr2.responseText);
                                    if (response.success) {
                                        showSuccess();
                                    } else {
                                        // Check for specific error types
                                        if (response.error_type) {
                                            showDependencyError(response.message, response.error_type);
                                        } else {
                                            showError(response.message || 'Failed to delete administrator.');
                                            document.getElementById('confirmAdminDeleteButton').disabled = false;
                                            document.getElementById('confirmAdminDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate('Delete Administrator', 'Tanggalin ang Administrator')}}';
                                        }
                                    }
                                } catch (e) {
                                    console.error('Error parsing JSON response:', e);
                                    showError('An unexpected error occurred. Please try again.');
                                    document.getElementById('confirmAdminDeleteButton').disabled = false;
                                    document.getElementById('confirmAdminDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate('Delete Administrator', 'Tanggalin ang Administrator')}}';
                                }
                            } else {
                                showError('Server error: ' + xhr2.status);
                                document.getElementById('confirmAdminDeleteButton').disabled = false;
                                document.getElementById('confirmAdminDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate('Delete Administrator', 'Tanggalin ang Administrator')}}';
                            }
                        };
                        xhr2.onerror = function() {
                            showError('Network error. Please try again.');
                            document.getElementById('confirmAdminDeleteButton').disabled = false;
                            document.getElementById('confirmAdminDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate('Delete Administrator', 'Tanggalin ang Administrator')}}';
                        };
                        xhr2.send(deleteForm);
                    } else {
                        showError('Incorrect password. Please try again.');
                        document.getElementById('confirmAdminDeleteButton').disabled = false;
                        document.getElementById('confirmAdminDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate('Delete Administrator', 'Tanggalin ang Administrator')}}';
                    }
                } catch (e) {
                    console.error('Error parsing JSON response:', e);
                    showError('An unexpected error occurred during password validation. Please try again.');
                    document.getElementById('confirmAdminDeleteButton').disabled = false;
                    document.getElementById('confirmAdminDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate('Delete Administrator', 'Tanggalin ang Administrator')}}';
                }
            } else {
                showError('Password validation failed. Please try again.');
                document.getElementById('confirmAdminDeleteButton').disabled = false;
                document.getElementById('confirmAdminDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate('Delete Administrator', 'Tanggalin ang Administrator')}}';
            }
        };
        xhr1.onerror = function() {
            showError('Network error during password validation. Please try again.');
            document.getElementById('confirmAdminDeleteButton').disabled = false;
            document.getElementById('confirmAdminDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> {{ T::translate('Delete Administrator', 'Tanggalin ang Administrator')}}';
        };
        xhr1.send(formData);
    });
});
</script>