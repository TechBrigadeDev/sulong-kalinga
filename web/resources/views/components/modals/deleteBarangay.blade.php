<!-- filepath: c:\xampp\htdocs\sulong_kalinga\resources\views\components\modals\deleteBarangay.blade.php -->
<div class="modal fade" id="deleteBarangayModal" tabindex="-1" aria-labelledby="deleteBarangayModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:rgb(251, 68, 68);">
                <h5 class="modal-title text-white" id="deleteBarangayModalLabel">Confirm Barangay Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="barangayModalBodyContent">
                <div id="barangayDeleteMessage" class="alert d-none" role="alert"></div>
                
                <div id="deleteConfirmation">
                    <p class="text-danger">
                        <i class="bi bi-exclamation-circle"></i> 
                        <strong>Warning!</strong> You are about to delete this barangay.
                    </p>
                    <p>Are you sure you want to permanently delete <span id="barangayNameToDelete" style="font-weight: bold;"></span>?</p>
                    <div class="mb-3">
                        <label for="barangayDeletePasswordInput" class="form-label">Enter Your Password to Confirm</label>
                        <input type="password" class="form-control" id="barangayDeletePasswordInput" placeholder="Enter your password" required>
                        <input type="hidden" id="barangayIdToDelete" value="">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelDeleteButton">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmBarangayDeleteButton">
                    <i class="bi bi-trash-fill"></i> Delete Barangay
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Function to open the delete modal
window.openDeleteBarangayModal = function(id, name) {
    // Reset modal state
    const modalBody = document.getElementById('barangayModalBodyContent');
    
    // Reset to original content
    modalBody.innerHTML = `
        <div id="barangayDeleteMessage" class="alert d-none" role="alert"></div>
        
        <div id="deleteConfirmation">
            <p class="text-danger">
                <i class="bi bi-exclamation-circle"></i> 
                <strong>Warning!</strong> You are about to delete this barangay.
            </p>
            <p>Are you sure you want to permanently delete <span id="barangayNameToDelete" style="font-weight: bold;"></span>?</p>
            <div class="mb-3">
                <label for="barangayDeletePasswordInput" class="form-label">Enter Your Password to Confirm</label>
                <input type="password" class="form-control" id="barangayDeletePasswordInput" placeholder="Enter your password" required>
                <input type="hidden" id="barangayIdToDelete" value="">
            </div>
        </div>
    `;
    
    // Set values
    document.getElementById('barangayIdToDelete').value = id;
    document.getElementById('barangayNameToDelete').textContent = name;
    
    // Reset buttons
    const confirmButton = document.getElementById('confirmBarangayDeleteButton');
    confirmButton.disabled = false;
    confirmButton.innerHTML = '<i class="bi bi-trash-fill"></i> Delete Barangay';
    confirmButton.style.display = 'inline-block';
    
    document.getElementById('cancelDeleteButton').textContent = 'Cancel';
    
    // Show the modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteBarangayModal'));
    deleteModal.show();
    
    // Re-add event listener for password input
    document.getElementById('barangayDeletePasswordInput').addEventListener('input', function() {
        const messageElement = document.getElementById('barangayDeleteMessage');
        if (messageElement) {
            messageElement.classList.add('d-none');
            messageElement.style.display = 'none';
        }
    });
}

// Function to show error message
function showError(message) {
    const messageElement = document.getElementById('barangayDeleteMessage');
    messageElement.textContent = message;
    messageElement.classList.remove('d-none', 'alert-success');
    messageElement.classList.add('alert-danger');
    messageElement.style.display = 'block';
}

// Function to show detailed error message with guidance
function showDependencyError(message, errorType) {
    // Get modal body for replacement
    const modalBody = document.getElementById('barangayModalBodyContent');
    
    // Create error content
    let errorContent = `
        <div class="alert alert-danger">
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
                <strong>Required Action:</strong>
                <p class="mt-2 mb-2">Before deleting this barangay, you need to reassign or remove all beneficiaries assigned to it.</p>
                <ol class="mt-2 mb-0">
                    <li>Go to the <a href="{{ route('admin.beneficiaries.index') }}">Beneficiary List</a>, filter by this barangay</li>
                    <li>Edit each beneficiary to assign them to a different barangay</li>
                    <li>Once all beneficiaries are reassigned, you can delete this barangay</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'last_barangay') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>System Requirement:</strong>
                <p class="mt-2 mb-2">This is the only remaining barangay for its municipality. Each municipality must have at least one barangay.</p>
                <ol class="mt-2 mb-0">
                    <li>Before deleting this barangay, add another barangay to this municipality</li>
                    <li>Click on "Add Barangay" and create at least one more barangay</li>
                    <li>Once another barangay exists, you can delete this one</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'permission_denied') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>Permission Denied:</strong>
                <p class="mt-2 mb-2">Only administrators with sufficient permissions can delete barangays.</p>
                <p>Please contact a system administrator if you need this barangay removed.</p>
            </div>
        `;
    }
    
    errorContent += `</div>`;
    
    // Replace modal body content
    modalBody.innerHTML = errorContent;
    
    // Change the cancel button text
    document.getElementById('cancelDeleteButton').textContent = 'Close';
    
    // Hide the delete button
    document.getElementById('confirmBarangayDeleteButton').style.display = 'none';
}

// Function to completely replace modal content with success message
function showSuccess() {
    // Get modal body reference
    const modalBody = document.getElementById('barangayModalBodyContent');
    
    // Replace content with success message
    modalBody.innerHTML = `
        <div class="text-center mb-2">
            <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
        </div>
        <p class="text-success text-center">
            <strong>Success!</strong> The barangay has been deleted successfully.
        </p>
        <p class="text-center">The page will reload shortly.</p>
    `;
    
    // Hide delete button and update cancel button
    document.getElementById('confirmBarangayDeleteButton').style.display = 'none';
    document.getElementById('cancelDeleteButton').textContent = 'Close';
    
    // Set timeout to reload the page
    setTimeout(function() {
        window.location.reload();
    }, 2000);
}

// Setup event handlers when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Delete confirmation button click handler
    document.getElementById('confirmBarangayDeleteButton').addEventListener('click', function() {
        // Get password and ID from the currently displayed form
        const passwordInput = document.getElementById('barangayDeletePasswordInput');
        const idInput = document.getElementById('barangayIdToDelete');
        
        if (!passwordInput || !idInput) {
            console.error('Form elements not found');
            return;
        }
        
        const password = passwordInput.value.trim();
        const barangayId = idInput.value;
        
        if (!password) {
            showError('Please enter your password to confirm deletion.');
            return;
        }
        
        // Show loading state
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...';
        
        // Send delete request with CSRF token and password
        fetch(`{{ route('admin.locations.barangays.delete', ['id' => ':id']) }}`.replace(':id', barangayId), {
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
                    showError(data.message || 'Failed to delete barangay.');
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-trash-fill"></i> Delete Barangay';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('An unexpected error occurred. Please try again.');
            this.disabled = false;
            this.innerHTML = '<i class="bi bi-trash-fill"></i> Delete Barangay';
        });
    });
});
</script>

