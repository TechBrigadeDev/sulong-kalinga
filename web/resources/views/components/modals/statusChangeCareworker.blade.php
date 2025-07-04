@php
use App\Helpers\TranslationHelper as T;
@endphp
<div class="modal fade" id="statusChangeCareworkerModal" tabindex="-1" aria-labelledby="statusChangeCareworkerModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusChangeCareworkerModalLabel">{{ T::translate('Confirm Status Change', 'Kumpirmahin ang Pagbabago sa Status')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="careworkerStatusChangeMessage" class="alert d-none" role="alert"></div>
                <p>{{ T::translate('Are you sure you want to change the status of this', 'Sigurado ka bang nais mong baguhin ang status na ito')}} <span id="careworkerEntityType" style="font-weight: bold;"></span>?</p>
                <form id="statusChangeCareworkerForm">
                    <div class="mb-3">
                        <label for="careworkerPasswordInput" class="form-label">{{ T::translate('Enter Password to Confirm', 'Ilagay ang Password upang Kumpirmahin')}}</label>
                        <input type="password" class="form-control" id="careworkerPasswordInput" placeholder="{{ T::translate('Enter your password', 'Ilagay ang password')}}" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="confirmCareworkerStatusChangeButton">{{ T::translate('Confirm', 'Kumpirmahin')}}</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let selectedCareworkerStatusElement = null;
    let previousCareworkerStatusValue = null; // Store the previous value of the dropdown
    let careworkerEntityType = ""; // Store the entity type dynamically
    let careworkerId = null; // Store the careworker ID dynamically

    // Function to open the modal and store the selected status element
    window.openStatusChangeCareworkerModal = function (selectElement, type, id, oldStatus) {
        selectedCareworkerStatusElement = selectElement; // Store the reference to the dropdown
        previousCareworkerStatusValue = oldStatus; // Store the previous value
        careworkerEntityType = type; // Set the entity type dynamically
        careworkerId = id; // Set the careworker ID dynamically
        const careworkerEntityTypeElement = document.getElementById("careworkerEntityType");
        if (careworkerEntityTypeElement) {
            careworkerEntityTypeElement.textContent = careworkerEntityType; // Update the modal text
        }
        const statusChangeCareworkerModal = new bootstrap.Modal(document.getElementById("statusChangeCareworkerModal"));
        statusChangeCareworkerModal.show();
    };

    // Handle the status change confirmation
    const confirmCareworkerStatusChangeButton = document.getElementById("confirmCareworkerStatusChangeButton");
    if (confirmCareworkerStatusChangeButton) {
        confirmCareworkerStatusChangeButton.addEventListener("click", function () {
            const passwordInput = document.getElementById("careworkerPasswordInput");
            const enteredPassword = passwordInput.value.trim();
            const messageElement = document.getElementById("careworkerStatusChangeMessage");

            // Remove any existing messages
            messageElement.classList.add("d-none");

            if (!enteredPassword) {
                messageElement.textContent = "{{ T::translate('Please enter your password to confirm the status change.', 'Mangyaring ilagay ang password upang kumpirmahin ang pagbabago sa status.')}}";
                messageElement.classList.remove("d-none", "alert-success");
                messageElement.classList.add("alert-danger");
                return;
            }

            // Validate the password with the server
            let validatePasswordEndpoint = "/admin/validate-password";
            @if(Auth::user()->role_id == 2)
                validatePasswordEndpoint = "/care-manager/validate-password";
            @endif

            fetch(validatePasswordEndpoint, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ password: enteredPassword })
            })
            .then(response => response.json())
            .then(data => {
                if (data.valid) {
                    // Update the status in the database
                    let updateStatusEndpoint;
                        @if(Auth::user()->role_id == 2)
                            updateStatusEndpoint = "/care-manager/care-workers/" + careworkerId + "/update-status-ajax";
                        @else
                            updateStatusEndpoint = "/admin/care-workers/" + careworkerId + "/update-status-ajax";
                        @endif
                    fetch(updateStatusEndpoint, {
                        method: 'POST',  // Changed from PUT to POST
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ status: selectedCareworkerStatusElement.value })
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('{{ T::translate('Status update failed', 'Nabigo ang pag-update sa Status')}}');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            messageElement.textContent = `Status changed to "${selectedCareworkerStatusElement.value}" for ${careworkerEntityType}.`;
                            messageElement.classList.remove("d-none", "alert-danger");
                            messageElement.classList.add("alert-success");

                            // Reset the modal fields
                            passwordInput.value = "";

                            // Refresh the page after a delay to show the success message
                            setTimeout(() => {
                                const statusChangeCareworkerModal = bootstrap.Modal.getInstance(document.getElementById("statusChangeCareworkerModal"));
                                statusChangeCareworkerModal.hide();
                                
                                // Update UI without refresh to avoid middleware issues
                                const statusCell = document.querySelector(`#careworker-${careworkerId} .status-cell`);
                                if (statusCell) {
                                    statusCell.textContent = selectedCareworkerStatusElement.value.charAt(0).toUpperCase() + selectedCareworkerStatusElement.value.slice(1);
                                    statusCell.className = `status-cell ${selectedCareworkerStatusElement.value.toLowerCase()}`;
                                    
                                    // Update the status badge color if it exists
                                    const statusBadge = document.querySelector(`#careworker-${careworkerId} .status-badge`);
                                    if (statusBadge) {
                                        statusBadge.className = `status-badge ${selectedCareworkerStatusElement.value.toLowerCase()}-badge`;
                                    }
                                } else {
                                    // Fallback to page reload if we can't find the cell to update
                                    location.reload();
                                }
                            }, 2000);
                        } else {
                            let roleText = "{{ Auth::user()->role_id == 2 ? 'care manager' : 'administrator' }}";
                            messageElement.textContent = `Failed to update status. Please try again or contact your system ${roleText === 'care manager' ? 'administrator' : 'technical support'}.`;
                            messageElement.classList.remove("d-none", "alert-success");
                            messageElement.classList.add("alert-danger");
                        }
                    })
                } else {
                    messageElement.textContent = "{{ T::translate('Invalid password. Please try again.', 'Mali ang password. Pakisubukan muli.')}}";
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
    const passwordInput = document.getElementById("careworkerPasswordInput");
    if (passwordInput) {
        passwordInput.addEventListener("input", function () {
            const messageElement = document.getElementById("careworkerStatusChangeMessage");
            messageElement.classList.add("d-none");
        });
    }

    // Handle modal cancellation
    const statusChangeCareworkerModalElement = document.getElementById("statusChangeCareworkerModal");
    if (statusChangeCareworkerModalElement) {
        statusChangeCareworkerModalElement.addEventListener("hidden.bs.modal", function () {
            if (selectedCareworkerStatusElement && previousCareworkerStatusValue !== null) {
                // Revert the dropdown to its previous value if the modal is cancelled
                selectedCareworkerStatusElement.value = previousCareworkerStatusValue === 'Active' ? '{{ T::translate('Active', 'Aktibo')}}' : '{{ T::translate('Inactive', 'Di-Aktibo')}}';
                location.reload(); // Refresh the page if the modal is cancelled
            }
        });
    }
});
</script>