<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:rgb(251, 68, 68);">
                <h5 class="modal-title text-white" id="deleteModalLabel">Confirm Delete</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="deleteModalMessage" class="alert d-none" role="alert"></div>
                
                <div id="passwordConfirmationContent">
                    <p id="deleteModalQuestion">Are you sure you want to delete this item? Please enter your password to confirm.</p>
                    <div class="mb-3">
                        <label for="passwordInput" class="form-label">Password</label>
                        <input type="password" class="form-control" id="passwordInput" placeholder="Enter your password" required>
                        <input type="hidden" id="itemToDelete">
                        <input type="hidden" id="deleteEndpoint">
                        <input type="hidden" id="redirectUrl">
                    </div>
                </div>
                
                <div id="deleteSuccessContent" class="d-none text-center">
                    <i class="bi bi-check-circle text-success" style="font-size: 3rem;"></i>
                    <p class="mt-3" id="successMessage">Item deleted successfully!</p>
                    <p>You will be redirected shortly.</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="cancelDeleteButton" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteButton">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const deleteModal = document.getElementById('deleteModal');
    const confirmDeleteButton = document.getElementById('confirmDeleteButton');
    const passwordInput = document.getElementById('passwordInput');
    const messageElement = document.getElementById('deleteModalMessage');
    const passwordContent = document.getElementById('passwordConfirmationContent');
    const successContent = document.getElementById('deleteSuccessContent');
    const cancelButton = document.getElementById('cancelDeleteButton');
    const itemToDelete = document.getElementById('itemToDelete');
    const deleteEndpoint = document.getElementById('deleteEndpoint');
    const redirectUrl = document.getElementById('redirectUrl');
    const deleteQuestion = document.getElementById('deleteModalQuestion');
    const successMessage = document.getElementById('successMessage');
    
    // Function to show the delete confirmation modal with customizations
    window.showDeleteConfirmModal = function(config) {
        // Set the item ID and endpoints
        itemToDelete.value = config.id || '';
        deleteEndpoint.value = config.endpoint || '';
        redirectUrl.value = config.redirectUrl || '';
                
        // Customize text if provided
        if (config.message) {
            deleteQuestion.textContent = config.message;
        }
        if (config.successText) {
            successMessage.textContent = config.successText;
        }
        
        // Reset the state
        passwordInput.value = '';
        messageElement.classList.add('d-none');
        passwordContent.classList.remove('d-none');
        successContent.classList.add('d-none');
        confirmDeleteButton.style.display = '';
        cancelButton.textContent = 'Cancel';
        
        // Show the modal
        const modal = new bootstrap.Modal(deleteModal);
        modal.show();
    };
    
    // Handle delete button click
    confirmDeleteButton.addEventListener('click', function() {
        // Validate password is entered
        if (!passwordInput.value) {
            messageElement.textContent = 'Please enter your password.';
            messageElement.classList.remove('d-none', 'alert-success', 'alert-danger');
            messageElement.classList.add('alert-warning');
            return;
        }
        
        // First validate the password
        const formData = new FormData();
        formData.append('password', passwordInput.value);
        formData.append('_token', '{{ csrf_token() }}');
        
        // Use the appropriate endpoint based on user role
        let validatePasswordEndpoint = '{{ route("admin.validate-password") }}';
        @if(Auth::user()->role_id == 2)
            validatePasswordEndpoint = '{{ route("care-manager.validate-password") }}';
        @elseif(Auth::user()->role_id == 3)
            validatePasswordEndpoint = '{{ route("care-worker.validate-password") }}';
        @endif
        
        fetch(validatePasswordEndpoint, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.valid) {
                // Password is valid, proceed with deletion
                const deleteFormData = new FormData();
                deleteFormData.append('id', itemToDelete.value);
                deleteFormData.append('password', passwordInput.value);
                deleteFormData.append('_token', '{{ csrf_token() }}');
                
                fetch(deleteEndpoint.value, {
                    method: 'POST',
                    body: deleteFormData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        console.log("DELETE SUCCESS DATA:", data);
                        console.log("Has redirectTo?", !!data.redirectTo);

                        passwordContent.classList.add('d-none');
                        successContent.classList.remove('d-none');
                        confirmDeleteButton.style.display = 'none';
                        cancelButton.textContent = 'Close';
                        
                        setTimeout(() => {
                            // Check if the server provided a specific redirect URL
                            if (data.redirectTo) {
                                console.log("Server provided redirect URL:", data.redirectTo);
                                window.location.href = data.redirectTo;
                            } else {
                                // Fallback to reports page
                                console.log("Using fallback reports URL");
                                let reportsUrl = "{{ route('admin.reports') }}";
                                @if(Auth::user()->role_id == 2)
                                    reportsUrl = "{{ route('care-manager.reports') }}";
                                @elseif(Auth::user()->role_id == 3)
                                    reportsUrl = "{{ route('care-worker.reports') }}";
                                @endif
                                
                                window.location.href = reportsUrl;
                            }
                        }, 2000);
                    } else {
                        // Show error message
                        messageElement.textContent = data.message || 'Failed to delete the item.';
                        messageElement.classList.remove('d-none', 'alert-success', 'alert-warning');
                        messageElement.classList.add('alert-danger');
                    }
                })
                .catch(error => {
                    console.error('Delete error:', error);
                    messageElement.textContent = 'An error occurred while processing your request.';
                    messageElement.classList.remove('d-none', 'alert-success', 'alert-warning');
                    messageElement.classList.add('alert-danger');
                });
            } else {
                // Invalid password
                messageElement.textContent = 'The password you entered is incorrect.';
                messageElement.classList.remove('d-none', 'alert-success', 'alert-warning');
                messageElement.classList.add('alert-danger');
            }
        })
        .catch(error => {
            console.error('Password validation error:', error);
            messageElement.textContent = 'An error occurred while validating your password.';
            messageElement.classList.remove('d-none', 'alert-success', 'alert-warning');
            messageElement.classList.add('alert-danger');
        });
    });
});
</script>