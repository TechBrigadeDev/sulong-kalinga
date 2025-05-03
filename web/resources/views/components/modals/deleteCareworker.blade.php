<div class="modal fade" id="deleteCareworkerModal" tabindex="-1" aria-labelledby="deleteCareworkerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:rgb(251, 68, 68);">
                <h5 class="modal-title text-white" id="deleteCareworkerModalLabel">Confirm Care Worker Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="careworkerDeleteMessage" class="alert d-none" role="alert"></div>
                
                <div id="deleteConfirmation">
                    <p class="text-danger">
                        <i class="bi bi-exclamation-circle"></i> 
                        <strong>Warning!</strong> You are about to delete this care worker account.
                    </p>
                    <p>Are you sure you want to permanently delete <span id="careworkerNameToDelete" style="font-weight: bold;"></span>?</p>
                    <div class="mb-3">
                        <label for="careworkerDeletePasswordInput" class="form-label">Enter Your Password to Confirm</label>
                        <input type="password" class="form-control" id="careworkerDeletePasswordInput" placeholder="Enter your password" required>
                        <input type="hidden" id="careworkerIdToDelete" value="">
                    </div>
                </div>
                
                <div id="deleteSuccess" class="d-none">
                    <p class="text-success">
                        <i class="bi bi-check-circle"></i>
                        <strong>Success!</strong> The care worker has been deleted successfully.
                    </p>
                    <p>You will be redirected to the care worker list shortly.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelDeleteButton">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmCareworkerDeleteButton">
                    <i class="bi bi-trash-fill"></i> Delete Care Worker
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
    confirmButton.innerHTML = '<i class="bi bi-trash-fill"></i> Delete Care Worker';
    confirmButton.classList.remove('d-none');
    
    document.getElementById('cancelDeleteButton').textContent = 'Cancel';
    
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
            <strong>Unable to Delete</strong>
        </div>
        <p>${message}</p>
    `;
    
    // Add specific guidance based on error type
    if (errorType === 'dependency_care_plans') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>What you can do instead:</strong>
                <p class="mt-2 mb-2">This care worker has created or updated care plans which must maintain their audit history for compliance purposes.</p>
                <ol class="mt-2 mb-0">
                    <li>Instead of deleting, you can mark this care worker as <strong>inactive</strong> in their profile to disable their access to the system</li>
                    <li>This will prevent them from logging in while preserving the audit trail</li>
                    <li>Go to <a href="@if(Auth::user()->role_id == 2){{ route('care-manager.careworkers.index') }}@else{{ route('admin.careworkers.index') }}@endif">, find this care worker, and change their status</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_users') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>What you can do instead:</strong>
                <p class="mt-2 mb-2">This care worker has updated user accounts which require audit history to be maintained.</p>
                <ol class="mt-2 mb-0">
                    <li>Instead of deleting, you can mark this care worker as <strong>inactive</strong> in their profile to disable their access to the system</li>
                    <li>This will prevent them from logging in while preserving the audit trail</li>
                    <li>Go to <a href="@if(Auth::user()->role_id == 2){{ route('care-manager.careworkers.index') }}@else{{ route('admin.careworkers.index') }}@endif">, find this care worker, and change their status</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_family') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>What you can do instead:</strong>
                <p class="mt-2 mb-2">This care worker has created or updated family member records which must maintain their history for proper record-keeping.</p>
                <ol class="mt-2 mb-0">
                    <li>Instead of deleting, you can mark this care worker as <strong>inactive</strong> in their profile to disable their access to the system</li>
                    <li>This will prevent them from logging in while preserving the audit trail</li>
                    <li><a href="@if(Auth::user()->role_id == 2){{ route('care-manager.careworkers.index') }}@else{{ route('admin.careworkers.index') }}@endif">, find this care worker, and change their status</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_audit') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>What you can do instead:</strong>
                <p class="mt-2 mb-2">This care worker has created or updated important records in the system. For audit and compliance reasons, these associations cannot be removed.</p>
                <ol class="mt-2 mb-0">
                    <li>Instead of deleting, you can mark this care worker as <strong>inactive</strong> in their profile to disable their access to the system</li>
                    <li>This will prevent them from logging in while preserving the audit trail</li>
                    <li>Go to<a href="@if(Auth::user()->role_id == 2){{ route('care-manager.careworkers.index') }}@else{{ route('admin.careworkers.index') }}@endif">, find this care worker, and change their status</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_beneficiaries') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>What you can do instead:</strong>
                <p class="mt-2 mb-2">This care worker is assigned to beneficiaries. You cannot delete a care worker while they're assigned to beneficiaries.</p>
                <ol class="mt-2 mb-0">
                    <li>First, reassign all beneficiaries to other care workers</li>
                    <li>Return to this page and try deletion again</li>
                    <li>Alternatively, you can mark this care worker as <strong>inactive</strong> in their profile to disable their access to the system and prevent them from logging in</li>
                </ol>
            </div>
        `;
    }
    
    messageElement.innerHTML = errorContent;
    
    // Change the cancel button text
    document.getElementById('cancelDeleteButton').textContent = 'Close';
    
    // Hide the delete button
    document.getElementById('confirmCareworkerDeleteButton').classList.add('d-none');
}

    // Function to show success message
    function showSuccess() {
        document.getElementById('deleteConfirmation').classList.add('d-none');
        document.getElementById('deleteSuccess').classList.remove('d-none');
        document.getElementById('confirmCareworkerDeleteButton').classList.add('d-none');
        document.getElementById('cancelDeleteButton').textContent = 'Close';
        
        setTimeout(function() {
        let redirectRoute = "{{ route('admin.careworkers.index') }}";
        @if(Auth::user()->role_id == 2)
            redirectRoute = "{{ route('care-manager.careworkers.index') }}";
        @endif
        window.location.href = redirectRoute;
    }, 2000); // Redirect after 2 seconds
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
        let validatePasswordEndpoint = "{{ route('admin.validate-password') }}";
        @if(Auth::user()->role_id == 2)
            validatePasswordEndpoint = "{{ route('care-manager.validate-password') }}";
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
                        let endpoint = "{{ route('admin.careworkers.delete') }}"; // Default endpoint for admins

                        // Use care manager endpoint if the current user is a care manager
                        @if(Auth::user()->role_id == 2)
                            endpoint = "{{ route('care-manager.careworkers.delete') }}";
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
                                            showError(response.message || 'Failed to delete care worker.');
                                            document.getElementById('confirmCareworkerDeleteButton').disabled = false;
                                            document.getElementById('confirmCareworkerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> Delete Care Worker';
                                        }
                                    }
                                } catch (e) {
                                    console.error('Error parsing JSON response:', e);
                                    showError('An unexpected error occurred. Please try again.');
                                    document.getElementById('confirmCareworkerDeleteButton').disabled = false;
                                    document.getElementById('confirmCareworkerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> Delete Care Worker';
                                }
                            } else {
                                showError('Server error: ' + xhr2.status);
                                document.getElementById('confirmCareworkerDeleteButton').disabled = false;
                                document.getElementById('confirmCareworkerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> Delete Care Worker';
                            }
                        };
                        xhr2.onerror = function() {
                            showError('Network error. Please try again.');
                            document.getElementById('confirmCareworkerDeleteButton').disabled = false;
                            document.getElementById('confirmCareworkerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> Delete Care Worker';
                        };
                        xhr2.send(deleteForm);
                    } else {
                        showError('Incorrect password. Please try again.');
                        document.getElementById('confirmCareworkerDeleteButton').disabled = false;
                        document.getElementById('confirmCareworkerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> Delete Care Worker';
                    }
                } catch (e) {
                    console.error('Error parsing JSON response:', e);
                    showError('An unexpected error occurred during password validation. Please try again.');
                    document.getElementById('confirmCareworkerDeleteButton').disabled = false;
                    document.getElementById('confirmCareworkerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> Delete Care Worker';
                }
            } else {
                showError('Password validation failed. Please try again.');
                document.getElementById('confirmCareworkerDeleteButton').disabled = false;
                document.getElementById('confirmCareworkerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> Delete Care Worker';
            }
        };
        xhr1.onerror = function() {
            showError('Network error during password validation. Please try again.');
            document.getElementById('confirmCareworkerDeleteButton').disabled = false;
            document.getElementById('confirmCareworkerDeleteButton').innerHTML = '<i class="bi bi-trash-fill"></i> Delete Care Worker';
        };
        xhr1.send(formData);
    });
});
</script>