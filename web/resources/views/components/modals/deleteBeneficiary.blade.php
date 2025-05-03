<div class="modal fade" id="deleteBeneficiaryModal" tabindex="-1" aria-labelledby="deleteBeneficiaryModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:rgb(251, 68, 68);">
                <h5 class="modal-title text-white" id="deleteBeneficiaryModalLabel">Confirm Beneficiary Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="beneficiaryDeleteMessage" class="alert d-none" role="alert"></div>
                
                <div id="deleteConfirmation">
                    <p class="text-danger">
                        <i class="bi bi-exclamation-circle"></i> 
                        <strong>Warning!</strong> You are about to delete this beneficiary profile.
                    </p>
                    <p>Are you sure you want to permanently delete <span id="beneficiaryNameToDelete" style="font-weight: bold;"></span>?</p>
                    
                    <div class="alert alert-warning">
                        <i class="bi bi-info-circle"></i> 
                        <strong>Note:</strong> If this beneficiary profile is deleted, the general care plan associated to this beneficiary will also be deleted.
                    </div>
                    
                    <div class="mb-3">
                        <label for="beneficiaryDeletePasswordInput" class="form-label">Enter Your Password to Confirm</label>
                        <input type="password" class="form-control" id="beneficiaryDeletePasswordInput" placeholder="Enter your password" required>
                        <input type="hidden" id="beneficiaryIdToDelete" value="">
                    </div>
                </div>
                
                <div id="deleteSuccess" class="d-none">
                    <p class="text-success">
                        <i class="bi bi-check-circle"></i>
                        <strong>Success!</strong> The beneficiary has been deleted successfully.
                    </p>
                    <p>You will be redirected to the beneficiary list shortly.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelDeleteButton">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmBeneficiaryDeleteButton">
                    <i class="bi bi-trash-fill"></i> Delete Beneficiary
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
    confirmButton.innerHTML = '<i class="bi bi-trash-fill"></i> Delete Beneficiary';
    confirmButton.classList.remove('d-none');
    
    document.getElementById('cancelDeleteButton').textContent = 'Cancel';
    
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
            <strong>Unable to Delete</strong>
        </div>
        <p>${message}</p>
    `;
    
    // Add specific guidance based on error type
    if (errorType === 'dependency_weekly_care_plans') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>What you can do instead:</strong>
                <p class="mt-2 mb-2">This beneficiary has weekly care plans which must be maintained for audit and historical purposes.</p>
                <ol class="mt-2 mb-0">
                    <li>Instead of deleting, you can mark this beneficiary as <strong>Inactive</strong> in their profile</li>
                    <li>This will prevent any new care plans from being created while preserving existing records</li>
                    <li>Changing the beneficiary's status will also prevent the beneficiary and their family from logging into the system</li>
                    <li>Use the status dropdown at the Beneficiary Profile page to change their status</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_family') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>What you can do instead:</strong>
                <p class="mt-2 mb-2">This beneficiary is linked to family members in the system.</p>
                <ol class="mt-2 mb-0">
                    <li>First, remove the beneficiary relationship from linked family members</li>
                    <li>Go to each family member's profile and edit their details</li>
                    <li>Remove this beneficiary as their related beneficiary</li>
                    <li>After all relationships are removed, you can try deleting this beneficiary again</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_audit') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>What you can do instead:</strong>
                <p class="mt-2 mb-2">This beneficiary has records in the system that require audit history to be maintained.</p>
                <ol class="mt-2 mb-0">
                    <li>Instead of deleting, you can mark this beneficiary as <strong>Inactive</strong> in their profile</li>
                    <li>This will prevent any new care plans from being created while preserving the audit trail</li>
                    <li>Use the status dropdown at the top of the beneficiary profile to change their status</li>
                </ol>
            </div>
        `;
    }
    
    messageElement.innerHTML = errorContent;
    
    // Change the cancel button text
    document.getElementById('cancelDeleteButton').textContent = 'Close';
    
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
            <h4 class="text-success mb-3">Successfully Deleted</h4>
            <div class="alert alert-light border py-3">
                <p class="mb-1">The beneficiary profile has been permanently removed from the system.</p>
                <div class="mt-2 pt-2 border-top">
                    <p class="mb-0 text-muted">
                        <i class="bi bi-info-circle"></i> The associated general care plan and related records have also been deleted.
                    </p>
                </div>
            </div>
            <p class="text-muted mt-3">
                <i class="bi bi-clock"></i> You will be redirected to the beneficiary list in a few seconds...
            </p>
        </div>
    `;
    
    // Show the success message
    successElement.classList.remove('d-none');
    
    // Hide delete button and change cancel button text
    document.getElementById('confirmBeneficiaryDeleteButton').classList.add('d-none');
    document.getElementById('cancelDeleteButton').textContent = 'Close';
    
    // Redirect after a delay
    setTimeout(function() {
        @if(Auth::user()->role_id == 1)
            window.location.href = "{{ route('admin.beneficiaries.index') }}";
        @else
            window.location.href = "{{ route('care-manager.beneficiaries.index') }}";
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
        @if(Auth::user()->role_id == 1)
            xhr1.open('POST', "{{ route('admin.validate-password') }}", true);
        @else
            xhr1.open('POST', "{{ route('care-manager.validate-password') }}", true);
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
                        let endpoint = "{{ route('admin.beneficiaries.delete') }}"; // Default endpoint for admins
                        
                        // Use care manager endpoint if the current user is a care manager
                        @if(Auth::user()->role_id == 2)
                            endpoint = "{{ route('care-manager.beneficiaries.delete') }}";
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
                                            showError(response.message || 'Failed to delete beneficiary.');
                                            document.getElementById('confirmBeneficiaryDeleteButton').disabled = false;
                                            document.getElementById('confirmBeneficiaryDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> Delete Beneficiary';
                                        }
                                    }
                                } catch (e) {
                                    console.error('Error parsing JSON response:', e);
                                    showError('An unexpected error occurred. Please try again.');
                                    document.getElementById('confirmBeneficiaryDeleteButton').disabled = false;
                                    document.getElementById('confirmBeneficiaryDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> Delete Beneficiary';
                                }
                            } else {
                                showError('Server error: ' + xhr2.status);
                                document.getElementById('confirmBeneficiaryDeleteButton').disabled = false;
                                document.getElementById('confirmBeneficiaryDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> Delete Beneficiary';
                            }
                        };
                        xhr2.onerror = function() {
                            showError('Network error. Please try again.');
                            document.getElementById('confirmBeneficiaryDeleteButton').disabled = false;
                            document.getElementById('confirmBeneficiaryDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> Delete Beneficiary';
                        };
                        xhr2.send(deleteForm);
                    } else {
                        showError('Incorrect password. Please try again.');
                        document.getElementById('confirmBeneficiaryDeleteButton').disabled = false;
                        document.getElementById('confirmBeneficiaryDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> Delete Beneficiary';
                    }
                } catch (e) {
                    console.error('Error parsing JSON response:', e);
                    showError('An unexpected error occurred during password validation. Please try again.');
                    document.getElementById('confirmBeneficiaryDeleteButton').disabled = false;
                    document.getElementById('confirmBeneficiaryDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> Delete Beneficiary';
                }
            } else {
                showError('Password validation failed. Please try again.');
                document.getElementById('confirmBeneficiaryDeleteButton').disabled = false;
                document.getElementById('confirmBeneficiaryDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> Delete Beneficiary';
            }
        };
        xhr1.onerror = function() {
            showError('Network error during password validation. Please try again.');
            document.getElementById('confirmBeneficiaryDeleteButton').disabled = false;
            document.getElementById('confirmBeneficiaryDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> Delete Beneficiary';
        };
        xhr1.send(formData);
    });
});
</script>