<div class="modal fade" id="deleteCaremanagerModal" tabindex="-1" aria-labelledby="deleteCaremanagerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:rgb(251, 68, 68);">
                <h5 class="modal-title text-white" id="deleteCaremanagerModalLabel">Confirm Care Manager Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="caremanagerDeleteMessage" class="alert d-none" role="alert"></div>
                
                <div id="deleteConfirmation">
                    <p class="text-danger">
                        <i class="bi bi-exclamation-circle"></i> 
                        <strong>Warning!</strong> You are about to delete this care manager account.
                    </p>
                    <p>Are you sure you want to permanently delete <span id="caremanagerNameToDelete" style="font-weight: bold;"></span>?</p>
                    <div class="mb-3">
                        <label for="caremanagerDeletePasswordInput" class="form-label">Enter Your Password to Confirm</label>
                        <input type="password" class="form-control" id="caremanagerDeletePasswordInput" placeholder="Enter your password" required>
                        <input type="hidden" id="caremanagerIdToDelete" value="">
                    </div>
                </div>
                
                <div id="deleteSuccess" class="d-none">
                    <p class="text-success">
                        <i class="bi bi-check-circle"></i>
                        <strong>Success!</strong> The care manager has been deleted successfully.
                    </p>
                    <p>You will be redirected to the care manager list shortly.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelDeleteButton">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmCaremanagerDeleteButton">
                    <i class="bi bi-trash-fill"></i> Delete Care Manager
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
    confirmButton.innerHTML = '<i class="bi bi-trash-fill"></i> Delete Care Manager';
    confirmButton.classList.remove('d-none');
    
    document.getElementById('cancelDeleteButton').textContent = 'Cancel';
    
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
            <strong>Unable to Delete</strong>
        </div>
        <p>${message}</p>
    `;
    
    // Add specific guidance based on error type
    if (errorType === 'dependency_beneficiaries') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>What you can do instead:</strong>
                <p class="mt-2 mb-2">For audit and record-keeping purposes, care managers who have created or updated beneficiary records cannot be deleted from the system.</p>
                <ol class="mt-2 mb-0">
                    <li>Instead of deleting, you can mark this care manager as <strong>inactive</strong> in their profile</li>
                    <li>This will prevent them from logging in while preserving the audit trail</li>
                    <li>Go to <a href="{{ route('admin.caremanagers.index') }}">Care Manager List</a>, find this care manager, and click the pencil icon in Actions (Edit) or just change change their status from the dropdown</li>
                    <li>Change their status from "Active" to "Inactive" and save the changes</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_audit') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>What you can do instead:</strong>
                <p class="mt-2 mb-2">This care manager has created or updated important records in the system. For audit and compliance reasons, these associations cannot be removed.</p>
                <ol class="mt-2 mb-0">
                    <li>Instead of deleting, you can mark this care manager as <strong>inactive</strong> in their profile</li>
                    <li>This will prevent them from logging in while preserving the audit trail</li>
                    <li>Go to <a href="{{ route('admin.caremanagers.index') }}">Care Manager List</a>, find this care manager, and click the pencil icon in Actions (Edit) or just change change their status from the dropdown</li>
                    <li>Change their status from "Active" to "Inactive" and save the changes</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_users') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>What you can do instead:</strong>
                <p class="mt-2 mb-2">This care manager is referenced in user accounts for audit purposes. These references must be maintained for record integrity.</p>
                <ol class="mt-2 mb-0">
                    <li>Instead of deleting, you can mark this care manager as <strong>inactive</strong> in their profile</li>
                    <li>This will prevent them from logging in while preserving the audit trail</li>
                    <li>Go to <a href="{{ route('admin.caremanagers.index') }}">Care Manager List</a>, find this care manager, and click the pencil icon in Actions (Edit) or just change change their status from the dropdown</li>
                    <li>Change their status from "Active" to "Inactive" and save the changes</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_care_plans') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>What you can do instead:</strong>
                <p class="mt-2 mb-2">This care manager has created or updated care plans which must maintain their audit history for compliance purposes.</p>
                <ol class="mt-2 mb-0">
                    <li>Instead of deleting, you can mark this care manager as <strong>inactive</strong> in their profile</li>
                    <li>This will prevent them from logging in while preserving the audit trail</li>
                    <li>Go to <a href="{{ route('admin.caremanagers.index') }}">Care Manager List</a>, find this care manager, and click the pencil icon in Actions (Edit) or just change change their status from the dropdown</li>
                    <li>Change their status from "Active" to "Inactive" and save the changes</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_family') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>What you can do instead:</strong>
                <p class="mt-2 mb-2">This care manager has created or updated family member records which must maintain their history for proper record-keeping.</p>
                <ol class="mt-2 mb-0">
                    <li>Instead of deleting, you can mark this care manager as <strong>inactive</strong> in their profile</li>
                    <li>This will prevent them from logging in while preserving the audit trail</li>
                    <li>Go to <a href="{{ route('admin.caremanagers.index') }}">Care Manager List</a>, find this care manager, and click the pencil icon in Actions (Edit) or just change change their status from the dropdown</li>
                    <li>Change their status from "Active" to "Inactive" and save the changes</li>
                </ol>
            </div>
        `;
    }
    
    messageElement.innerHTML = errorContent;
    
    // Change the cancel button text
    document.getElementById('cancelDeleteButton').textContent = 'Close';
    
    // Hide the delete button
    document.getElementById('confirmCaremanagerDeleteButton').classList.add('d-none');
}

// Function to show success message
function showSuccess() {
    document.getElementById('deleteConfirmation').classList.add('d-none');
    document.getElementById('deleteSuccess').classList.remove('d-none');
    document.getElementById('confirmCaremanagerDeleteButton').classList.add('d-none');
    document.getElementById('cancelDeleteButton').textContent = 'Close';
    
    setTimeout(function() {
        window.location.href = "{{ route('admin.caremanagers.index') }}";
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
        xhr1.open('POST', "{{ route('admin.validate-password') }}", true);
        xhr1.onload = function() {
            if (xhr1.status === 200) {
                try {
                    const data = JSON.parse(xhr1.responseText);
                    if (data.valid) {
                        // Password is valid, proceed with deletion
                        const xhr2 = new XMLHttpRequest();
                        xhr2.open('POST', "{{ route('admin.caremanagers.delete') }}", true);
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
                                            showError(response.message || 'Failed to delete care manager.');
                                            document.getElementById('confirmCaremanagerDeleteButton').disabled = false;
                                            document.getElementById('confirmCaremanagerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> Delete Care Manager';
                                        }
                                    }
                                } catch (e) {
                                    console.error('Error parsing JSON response:', e);
                                    showError('An unexpected error occurred. Please try again.');
                                    document.getElementById('confirmCaremanagerDeleteButton').disabled = false;
                                    document.getElementById('confirmCaremanagerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> Delete Care Manager';
                                }
                            } else {
                                showError('Server error: ' + xhr2.status);
                                document.getElementById('confirmCaremanagerDeleteButton').disabled = false;
                                document.getElementById('confirmCaremanagerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> Delete Care Manager';
                            }
                        };
                        xhr2.onerror = function() {
                            showError('Network error. Please try again.');
                            document.getElementById('confirmCaremanagerDeleteButton').disabled = false;
                            document.getElementById('confirmCaremanagerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> Delete Care Manager';
                        };
                        // Create the request body
                        const params = 'caremanager_id=' + encodeURIComponent(caremanagerId) + '&_token={{ csrf_token() }}';
                        // Send the request
                        xhr2.send(params);
                    } else {
                        showError('Incorrect password. Please try again.');
                        document.getElementById('confirmCaremanagerDeleteButton').disabled = false;
                        document.getElementById('confirmCaremanagerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> Delete Care Manager';
                    }
                } catch (e) {
                    console.error('Error parsing JSON response:', e);
                    showError('An unexpected error occurred during password validation. Please try again.');
                    document.getElementById('confirmCaremanagerDeleteButton').disabled = false;
                    document.getElementById('confirmCaremanagerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> Delete Care Manager';
                }
            } else {
                showError('Password validation failed. Please try again.');
                document.getElementById('confirmCaremanagerDeleteButton').disabled = false;
                document.getElementById('confirmCaremanagerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> Delete Care Manager';
            }
        };
        xhr1.onerror = function() {
            showError('Network error during password validation. Please try again.');
            document.getElementById('confirmCaremanagerDeleteButton').disabled = false;
            document.getElementById('confirmCaremanagerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> Delete Care Manager';
        };
        xhr1.send(formData);
    });
});
</script>