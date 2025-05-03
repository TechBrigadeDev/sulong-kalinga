<div class="modal fade" id="statusChangeAdminModal" tabindex="-1" aria-labelledby="statusChangeAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusChangeAdminModalLabel">Confirm Status Change</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="adminStatusChangeMessage" class="alert d-none" role="alert"></div>
                <p>Are you sure you want to change the status of this <span id="adminEntityType" style="font-weight: bold;"></span>?</p>
                <form id="statusChangeAdminForm">
                    <div class="mb-3">
                        <label for="adminPasswordInput" class="form-label">Enter Password to Confirm</label>
                        <input type="password" class="form-control" id="adminPasswordInput" placeholder="Enter your password" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmAdminStatusChangeButton">Confirm</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let selectedAdminStatusElement = null;
    let previousAdminStatusValue = null;
    let adminEntityType = "";
    let administratorId = null;

    // Function to open the modal
    window.openStatusChangeAdminModal = function (selectElement, type, id, oldStatus) {
        selectedAdminStatusElement = selectElement;
        previousAdminStatusValue = oldStatus;
        adminEntityType = type;
        administratorId = id;
        const adminEntityTypeElement = document.getElementById("adminEntityType");
        if (adminEntityTypeElement) {
            adminEntityTypeElement.textContent = adminEntityType;
        }
        const statusChangeAdminModal = new bootstrap.Modal(document.getElementById("statusChangeAdminModal"));
        statusChangeAdminModal.show();
    };

    // Handle confirmation button click
    const confirmAdminStatusChangeButton = document.getElementById("confirmAdminStatusChangeButton");
    if (confirmAdminStatusChangeButton) {
        confirmAdminStatusChangeButton.addEventListener("click", function () {
            const passwordInput = document.getElementById("adminPasswordInput");
            const enteredPassword = passwordInput.value.trim();
            const messageElement = document.getElementById("adminStatusChangeMessage");

            // Remove any existing messages
            messageElement.classList.add("d-none");

            if (!enteredPassword) {
                messageElement.textContent = "Please enter your password to confirm the status change.";
                messageElement.classList.remove("d-none", "alert-success");
                messageElement.classList.add("alert-danger");
                return;
            }

            // Validate password using the correct route
            fetch("{{ route('admin.validate-password') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ password: enteredPassword })
            })
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    // Show processing message
                    messageElement.textContent = `Processing status change...`;
                    messageElement.classList.remove("d-none", "alert-danger");
                    messageElement.classList.add("alert-success");
                    
                    // Make AJAX call to update status - using POST instead of PUT
                    fetch(`/admin/administrators/${administratorId}/update-status-ajax`, {
                        method: 'POST',  // Changed from PUT to POST
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ status: selectedAdminStatusElement.value })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Status update failed');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Update success message
                        messageElement.textContent = `Status changed to "${selectedAdminStatusElement.value}" for ${adminEntityType}.`;
                        
                        // Reset password field
                        passwordInput.value = "";

                        // Close modal after delay
                        setTimeout(() => {
                            const modal = bootstrap.Modal.getInstance(document.getElementById("statusChangeAdminModal"));
                            modal.hide();
                            
                            // Update UI without page reload
                            const statusCell = document.querySelector(`#admin-${administratorId} .status-cell`);
                            if (statusCell) {
                                statusCell.textContent = selectedAdminStatusElement.value.charAt(0).toUpperCase() + selectedAdminStatusElement.value.slice(1);
                                statusCell.className = `status-cell ${selectedAdminStatusElement.value.toLowerCase()}`;
                            } else {
                                // Fallback to page reload if we can't find the cell
                                location.reload();
                            }
                        }, 2000);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        messageElement.textContent = "Failed to update status. Please try again.";
                        messageElement.classList.remove("alert-success");
                        messageElement.classList.add("alert-danger");
                    });
                } else {
                    messageElement.textContent = "Incorrect password. Please try again.";
                    messageElement.classList.remove("d-none", "alert-success");
                    messageElement.classList.add("alert-danger");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageElement.textContent = "An error occurred. Please try again.";
                messageElement.classList.remove("d-none", "alert-success");
                messageElement.classList.add("alert-danger");
            });
        });
    }

    // Remove the error message when the user starts to input the password again
    const passwordInput = document.getElementById("adminPasswordInput");
    if (passwordInput) {
        passwordInput.addEventListener("input", function () {
            const messageElement = document.getElementById("adminStatusChangeMessage");
            messageElement.classList.add("d-none");
        });
    }

    // Handle modal cancellation
    const statusChangeAdminModalElement = document.getElementById("statusChangeAdminModal");
    if (statusChangeAdminModalElement) {
        statusChangeAdminModalElement.addEventListener("hidden.bs.modal", function () {
            if (selectedAdminStatusElement && previousAdminStatusValue !== null) {
                // Revert the dropdown to its previous value if the modal is cancelled
                selectedAdminStatusElement.value = previousAdminStatusValue === 'Active' ? 'Active' : 'Inactive';
                location.reload(); // Refresh the page if the modal is cancelled
            }
        });
    }
});
</script>