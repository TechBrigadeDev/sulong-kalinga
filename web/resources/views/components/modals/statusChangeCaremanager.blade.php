@php
use App\Helpers\TranslationHelper as T;
@endphp
<div class="modal fade" id="statusChangeCaremanagerModal" tabindex="-1" aria-labelledby="statusChangeCaremanagerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusChangeCaremanagerModalLabel">{{ T::translate('Confirm Status Change', 'Kumpirmahin ang Pagbabago sa Status')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="caremanagerStatusChangeMessage" class="alert d-none" role="alert"></div>
                <p>{{ T::translate('Are you sure you want to change the status of this', 'Sigurado ka bang nais mong baguhin ang status na ito')}} <span id="caremanagerEntityType" style="font-weight: bold;"></span>?</p>
                <form id="statusChangeCaremanagerForm">
                    <div class="mb-3">
                        <label for="caremanagerPasswordInput" class="form-label">{{ T::translate('Enter Password to Confirm', 'Ilagay ang Password upang Kumpirmaahin')}}</label>
                        <input type="password" class="form-control" id="caremanagerPasswordInput" placeholder="{{ T::translate('Enter your password', 'Ilagay ang iyong password')}}" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ T::translate('Cancel', 'I-Kansela')}}</button>
                <button type="button" class="btn btn-primary" id="confirmCaremanagerStatusChangeButton">{{ T::translate('Confirm', 'Kumpirmahin')}}</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let selectedCaremanagerStatusElement = null;
    let previousCaremanagerStatusValue = null; // Store the previous value of the dropdown
    let caremanagerEntityType = ""; // Store the entity type dynamically
    let caremanagerId = null; // Store the caremanager ID dynamically

    // Function to open the modal
    window.openStatusChangeCaremanagerModal = function (selectElement, type, id, oldStatus) {
        selectedCaremanagerStatusElement = selectElement;
        previousCaremanagerStatusValue = oldStatus;
        caremanagerEntityType = type;
        caremanagerId = id;
        const caremanagerEntityTypeElement = document.getElementById("caremanagerEntityType");
        if (caremanagerEntityTypeElement) {
            caremanagerEntityTypeElement.textContent = caremanagerEntityType;
        }
        const statusChangeCaremanagerModal = new bootstrap.Modal(document.getElementById("statusChangeCaremanagerModal"));
        statusChangeCaremanagerModal.show();
    };

    // Handle confirmation button click
    const confirmCaremanagerStatusChangeButton = document.getElementById("confirmCaremanagerStatusChangeButton");
    if (confirmCaremanagerStatusChangeButton) {
        confirmCaremanagerStatusChangeButton.addEventListener("click", function () {
            const passwordInput = document.getElementById("caremanagerPasswordInput");
            const enteredPassword = passwordInput.value.trim();
            const messageElement = document.getElementById("caremanagerStatusChangeMessage");

            // Remove any existing messages
            messageElement.classList.add("d-none");

            if (!enteredPassword) {
                messageElement.textContent = "{{ T::translate('Please enter your password to confirm the status change.', 'Mangyaring ilagay ang iyong password upang kumpirmahin ang pagbabago sa status.')}}";
                messageElement.classList.remove("d-none", "alert-success");
                messageElement.classList.add("alert-danger");
                return;
            }

            // Validate password
            fetch("/admin/validate-password", {
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
                    // Show success message first
                    messageElement.textContent = `Processing status change...`;
                    messageElement.classList.remove("d-none", "alert-danger");
                    messageElement.classList.add("alert-success");
                    
                    // Make AJAX call to update status - using POST instead of PUT
                    fetch(`/admin/care-managers/${caremanagerId}/update-status-ajax`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ status: selectedCaremanagerStatusElement.value })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('{{ T::translate('Status update failed', 'Nabigo ang pag-update sa status')}}');
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Update success message
                        messageElement.textContent = `Status changed to "${selectedCaremanagerStatusElement.value}" for ${caremanagerEntityType}.`;
                        
                        // Reset password field
                        passwordInput.value = "";

                        // Close modal after delay
                        setTimeout(() => {
                            const modal = bootstrap.Modal.getInstance(document.getElementById("statusChangeCaremanagerModal"));
                            modal.hide();
                            
                            // Update UI without page reload
                            const statusCell = document.querySelector(`#caremanager-${caremanagerId} .status-cell`);
                            if (statusCell) {
                                statusCell.textContent = selectedCaremanagerStatusElement.value.charAt(0).toUpperCase() + selectedCaremanagerStatusElement.value.slice(1);
                                statusCell.className = `status-cell ${selectedCaremanagerStatusElement.value.toLowerCase()}`;
                            } else {
                                // Fallback to page reload if we can't find the cell
                                location.reload();
                            }
                        }, 2000);
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        messageElement.textContent = "{{ T::translate('Failed to update status. Please try again.', 'Nabigong i-update ang status. Pakisubukan muli.')}}";
                        messageElement.classList.remove("alert-success");
                        messageElement.classList.add("alert-danger");
                    });
                } else {
                    messageElement.textContent = "{{ T::translate('Incorrect password. Please try again.', 'Mali ang password. Pakisubukan muli.')}}";
                    messageElement.classList.remove("d-none", "alert-success");
                    messageElement.classList.add("alert-danger");
                }
            })
            .catch(error => {
                console.error('Error:', error);
                messageElement.textContent = "{{ T::translate('An error occurred. Please try again.', 'Isang error ang naganap. Pakisubukan muli.')}}";
                messageElement.classList.remove("d-none", "alert-success");
                messageElement.classList.add("alert-danger");
            });
        });
    }

    // Remove the error message when the user starts to input the password again
    const passwordInput = document.getElementById("caremanagerPasswordInput");
    if (passwordInput) {
        passwordInput.addEventListener("input", function () {
            const messageElement = document.getElementById("caremanagerStatusChangeMessage");
            messageElement.classList.add("d-none");
        });
    }

    // Handle modal cancellation
    const statusChangeCaremanagerModalElement = document.getElementById("statusChangeCaremanagerModal");
    if (statusChangeCaremanagerModalElement) {
        statusChangeCaremanagerModalElement.addEventListener("hidden.bs.modal", function () {
            if (selectedCaremanagerStatusElement && previousCaremanagerStatusValue !== null) {
                // Revert the dropdown to its previous value if the modal is cancelled
                selectedCaremanagerStatusElement.value = previousCaremanagerStatusValue === 'Active' ? '{{ T::translate('Active', 'Aktibo')}}' : '{{ T::translate('Inactive', 'Di-Aktibo')}}';
                location.reload(); // Refresh the page if the modal is cancelled
            }
        });
    }
});
</script>