<!-- filepath: c:\xampp\htdocs\sulong_kalinga\resources\views\components\modals\deleteMunicipality.blade.php -->
<div class="modal fade" id="deleteMunicipalityModal" tabindex="-1" aria-labelledby="deleteMunicipalityModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:rgb(251, 68, 68);">
                <h5 class="modal-title text-white" id="deleteMunicipalityModalLabel">Confirm Municipality Deletion</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="modalBodyContent">
                <div id="municipalityDeleteMessage" class="alert d-none" role="alert"></div>
                
                <div id="deleteConfirmation">
                    <p class="text-danger">
                        <i class="bi bi-exclamation-circle"></i> 
                        <strong>Warning!</strong> You are about to delete this municipality.
                    </p>
                    <p>Are you sure you want to permanently delete <span id="municipalityNameToDelete" style="font-weight: bold;"></span>?</p>
                    <div class="mb-3">
                        <label for="municipalityDeletePasswordInput" class="form-label">Enter Your Password to Confirm</label>
                        <input type="password" class="form-control" id="municipalityDeletePasswordInput" placeholder="Enter your password" required>
                        <input type="hidden" id="municipalityIdToDelete" value="">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="cancelDeleteButton">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmMunicipalityDeleteButton">
                    <i class="bi bi-trash-fill"></i> Delete Municipality
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Function to open the delete modal
window.openDeleteMunicipalityModal = function(id, name) {
    // Reset modal state
    const modalBody = document.getElementById('modalBodyContent');
    
    // Reset to original content
    modalBody.innerHTML = `
        <div id="municipalityDeleteMessage" class="alert d-none" role="alert"></div>
        
        <div id="deleteConfirmation">
            <p class="text-danger">
                <i class="bi bi-exclamation-circle"></i> 
                <strong>Warning!</strong> You are about to delete this municipality.
            </p>
            <p>Are you sure you want to permanently delete <span id="municipalityNameToDelete" style="font-weight: bold;"></span>?</p>
            <div class="mb-3">
                <label for="municipalityDeletePasswordInput" class="form-label">Enter Your Password to Confirm</label>
                <input type="password" class="form-control" id="municipalityDeletePasswordInput" placeholder="Enter your password" required>
                <input type="hidden" id="municipalityIdToDelete" value="">
            </div>
        </div>
    `;
    
    // Set values
    document.getElementById('municipalityIdToDelete').value = id;
    document.getElementById('municipalityNameToDelete').textContent = name;
    
    // Reset buttons
    const confirmButton = document.getElementById('confirmMunicipalityDeleteButton');
    confirmButton.disabled = false;
    confirmButton.innerHTML = '<i class="bi bi-trash-fill"></i> Delete Municipality';
    confirmButton.style.display = 'inline-block';
    
    document.getElementById('cancelDeleteButton').textContent = 'Cancel';
    
    // Show the modal
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteMunicipalityModal'));
    deleteModal.show();
    
    // Re-add event listener for password input
    document.getElementById('municipalityDeletePasswordInput').addEventListener('input', function() {
        const messageElement = document.getElementById('municipalityDeleteMessage');
        if (messageElement) {
            messageElement.classList.add('d-none');
            messageElement.style.display = 'none';
        }
    });
}

// Function to show error message
function showError(message) {
    const messageElement = document.getElementById('municipalityDeleteMessage');
    messageElement.textContent = message;
    messageElement.classList.remove('d-none', 'alert-success');
    messageElement.classList.add('alert-danger');
    messageElement.style.display = 'block';
}

// Function to show detailed error message with guidance
function showDependencyError(message, errorType) {
    // Get modal body for replacement
    const modalBody = document.getElementById('modalBodyContent');
    
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
    if (errorType === 'dependency_barangays') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>What you can do instead:</strong>
                <p class="mt-2 mb-2">This municipality has barangays assigned to it. For data integrity, municipalities with assigned barangays cannot be deleted.</p>
                <ol class="mt-2 mb-0">
                    <li>First, delete all barangays from this municipality</li>
                    <li>If no other references (such as assignments to beneficiaries) exist, you can delete this municipality</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_beneficiaries') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>System Requirement:</strong>
                <p class="mt-2 mb-2">This municipality has beneficiaries assigned to it. Data integrity requires that all records remain linked.</p>
                <ol class="mt-2 mb-0">
                    <li>First, reassign all beneficiaries to another municipality</li>
                    <li>Then delete all barangays from this municipality</li>
                    <li>Finally, you can delete this municipality</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'dependency_care_users') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>User Assignments:</strong>
                <p class="mt-2 mb-2">This municipality has care workers or care managers assigned to it.</p>
                <ol class="mt-2 mb-0">
                    <li>First, reassign all users to another municipality</li>
                    <li>Go to the Care Workers and Care Managers profiles</li>
                    <li>Assign them to different municipalities</li>
                    <li>If there are no barangays under this municipality, then you can delete this municipality</li>
                </ol>
            </div>
        `;
    } else if (errorType === 'permission_denied') {
        errorContent += `
            <div class="mt-2 border-top pt-2">
                <strong>Permission Denied:</strong>
                <p class="mt-2 mb-2">Only administrators with sufficient permissions can delete municipalities.</p>
                <p>Please contact a system administrator if you need this municipality removed.</p>
            </div>
        `;
    }
    
    errorContent += `</div>`;
    
    // Replace modal body content
    modalBody.innerHTML = errorContent;
    
    // Change the cancel button text
    document.getElementById('cancelDeleteButton').textContent = 'Close';
    
    // Hide the delete button
    document.getElementById('confirmMunicipalityDeleteButton').style.display = 'none';
}

// Function to completely replace modal content with success message
function showSuccess() {
    // Get modal body reference
    const modalBody = document.getElementById('modalBodyContent');
    
    // Replace content with success message
    modalBody.innerHTML = `
        <div class="text-center mb-2">
            <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
        </div>
        <p class="text-success text-center">
            <strong>Success!</strong> The municipality has been deleted successfully.
        </p>
        <p class="text-center">The page will reload shortly.</p>
    `;
    
    // Hide delete button and update cancel button
    document.getElementById('confirmMunicipalityDeleteButton').style.display = 'none';
    document.getElementById('cancelDeleteButton').textContent = 'Close';
    
    // Set timeout to reload the page
    setTimeout(function() {
        window.location.reload();
    }, 2000);
}

// Setup event handlers when page loads
document.addEventListener('DOMContentLoaded', function() {
    // Delete confirmation button click handler
    document.getElementById('confirmMunicipalityDeleteButton').addEventListener('click', function() {
        // Get password and ID from the currently displayed form
        const passwordInput = document.getElementById('municipalityDeletePasswordInput');
        const idInput = document.getElementById('municipalityIdToDelete');
        
        if (!passwordInput || !idInput) {
            console.error('Form elements not found');
            return;
        }
        
        const password = passwordInput.value.trim();
        const municipalityId = idInput.value;
        
        if (!password) {
            showError('Please enter your password to confirm deletion.');
            return;
        }
        
        // Show loading state
        this.disabled = true;
        this.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Deleting...';
        
        // Send delete request with CSRF token and password
        fetch(`{{ route('admin.locations.municipalities.delete', ['id' => ':id']) }}`.replace(':id', municipalityId), {
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
                    showError(data.message || 'Failed to delete municipality.');
                    this.disabled = false;
                    this.innerHTML = '<i class="bi bi-trash-fill"></i> Delete Municipality';
                }
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('An unexpected error occurred. Please try again.');
            this.disabled = false;
            this.innerHTML = '<i class="bi bi-trash-fill"></i> Delete Municipality';
        });
    });
});
</script>