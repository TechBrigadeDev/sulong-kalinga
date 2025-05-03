<div class="modal fade" id="statusChangeModal" tabindex="-1" aria-labelledby="statusChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusChangeModalLabel">Confirm Status Change</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="successMessage" class="alert alert-success" style="display: none;">Status updated successfully!</div>
                <div id="errorMessage" class="alert alert-danger" style="display: none;"></div>
                <p>Are you sure you want to change the status of this <span id="entityType" style="font-weight: bold;"></span>?</p>
                
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle"></i> 
                    <strong>Note:</strong> Changing the status of this beneficiary will affect (allow or prevent) their access to the system, as well as their registered family members.
                </div>

                <form id="statusChangeFormHidden" method="POST" style="display:none;">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" id="statusInput">
                    <input type="hidden" name="reason" id="reasonInput">
                    <input type="hidden" name="password" id="passwordHidden">
                    <input type="hidden" name="return_url" id="returnUrlInput" value="{{ url()->current() }}">
                </form>

                <form id="statusChangeForm">
                    <div class="mb-3" id="reasonDiv">
                        <label for="reasonSelect" class="form-label">Reason for Status Change</label>
                        <select class="form-select" id="reasonSelect">
                            <option value="" selected disabled>Select a reason</option>
                            <option value="Opted Out">Opted Out</option>
                            <option value="Deceased">Deceased</option>
                            <option value="Hospitalized">Hospitalized</option>
                            <option value="Moved Residence">Moved Residence</option>
                            <option value="No Longer Needed Assistance">No Longer Needed Assistance</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="passwordInput" class="form-label">Password</label>
                        <input type="password" class="form-control" id="passwordInput" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmStatusChangeButton">Confirm</button>
            </div>
        </div>
    </div>
</div>

<!-- Update the modal script portion with role detection -->
<script>
document.addEventListener("DOMContentLoaded", function () {
    let selectedStatusElement = null;
    let entityType = ""; 
    let beneficiaryId = null;
    let oldStatus = "";
    
    // Determine user role from current URL path
    const currentPath = window.location.pathname;
    const isAdmin = currentPath.includes('/admin/');
    const isCareManager = currentPath.includes('/care-manager/');
    
    // Set the appropriate validation and update URLs based on role
    const passwordValidationUrl = isAdmin 
        ? "{{ route('admin.validate-password') }}"
        : "{{ route('care-manager.validate-password') }}";
        
    const statusUpdateBaseUrl = isAdmin
        ? "/admin/beneficiaries"
        : "/care-manager/beneficiaries";

    // Function to open the modal and store the selected status element
    window.openStatusChangeModal = function (selectElement, type, id, currentStatus) {
        selectedStatusElement = selectElement;
        entityType = type;
        beneficiaryId = id;
        oldStatus = currentStatus;
        document.getElementById("entityType").textContent = entityType;
        const statusChangeModal = new bootstrap.Modal(document.getElementById("statusChangeModal"));
        statusChangeModal.show();

        // Handle modal close event
        document.getElementById("statusChangeModal").addEventListener('hidden.bs.modal', function () {
            if (!selectedStatusElement.dataset.confirmed) {
                location.reload();
            }
        }, { once: true });

        // Remove the reason input if the new status is active
        const newStatus = selectElement.value;
        const reasonDiv = document.getElementById("reasonDiv");
        if (oldStatus === 'Inactive' && newStatus === 'Active') {
            reasonDiv.style.display = 'none';
        } else {
            reasonDiv.style.display = 'block';
        }
        console.log(`Old Status: ${oldStatus}, New Status: ${newStatus}`);
    };

    // Function to validate the password
    function validatePassword(password) {
        return fetch(passwordValidationUrl, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ password: password })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(data => {
                    throw new Error(data.message || 'Incorrect password. Please try again.');
                });
            }
            return response.json();
        });
    }

    // Handle the status change confirmation
    const confirmStatusChangeButton = document.getElementById("confirmStatusChangeButton");
    confirmStatusChangeButton.addEventListener("click", function () {
        // Hide error message on new attempt
        const errorMessage = document.getElementById("errorMessage");
        errorMessage.style.display = 'none';

        const reasonSelect = document.getElementById("reasonSelect");
        const passwordInput = document.getElementById("passwordInput");
        const selectedReason = reasonSelect.value;
        const password = passwordInput.value;
        const newStatus = selectedStatusElement.value;

        if (newStatus !== 'Active' && !selectedReason) {
            errorMessage.textContent = "Please select a reason for the status change.";
            errorMessage.style.display = 'block';
            return;
        }

        if (!password) {
            errorMessage.textContent = "Please enter your password to confirm the status change.";
            errorMessage.style.display = 'block';
            return;
        }

        validatePassword(password)
        .then(data => {
            if (data.valid) {
                // Show success message first
                const successMessage = document.getElementById("successMessage");
                successMessage.style.display = 'block';
                
                // Make the AJAX call to update status - using role-appropriate URL
                fetch(`${statusUpdateBaseUrl}/${beneficiaryId}/status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        status: newStatus,
                        reason: selectedReason || ''
                    })
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Status update failed');
                    }
                    return response.json();
                })
                .then(data => {
                    // Close modal after showing success message
                    setTimeout(() => {
                        const modal = bootstrap.Modal.getInstance(document.getElementById('statusChangeModal'));
                        modal.hide();
                        
                        // Update UI without page reload
                        const statusCell = document.querySelector(`#beneficiary-${beneficiaryId} .status-cell`);
                        if (statusCell) {
                            statusCell.textContent = newStatus;
                            statusCell.className = `status-cell ${newStatus.toLowerCase()}`;
                        }
                    }, 2000);
                })
                .catch(error => {
                    console.error('Error:', error);
                    errorMessage.textContent = "Failed to update status. Please try again.";
                    errorMessage.style.display = 'block';
                    successMessage.style.display = 'none';
                });
            } else {
                throw new Error('Incorrect password. Please try again.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            errorMessage.textContent = error.message || 'An error occurred. Please try again.';
            errorMessage.style.display = 'block';
        });
    });

    // Hide error message when user starts typing in the password field
    const passwordInput = document.getElementById("passwordInput");
    passwordInput.addEventListener("input", function () {
        const errorMessage = document.getElementById("errorMessage");
        errorMessage.style.display = 'none';
    });
});
</script>