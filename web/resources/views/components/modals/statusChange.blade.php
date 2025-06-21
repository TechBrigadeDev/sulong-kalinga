@php
use App\Helpers\TranslationHelper as T;
@endphp
<div class="modal fade" id="statusChangeModal" tabindex="-1" aria-labelledby="statusChangeModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusChangeModalLabel">{{ T::translate('Confirm Status Change', 'Kumpirmahin ang Pagbabago sa Status')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>{{ T::translate('Are you sure you want to change the status or access of this', 'Sigurado ka bang nais mong baguhin ang status o access na ito')}} <span id="entityType" style="font-weight: bold;"></span>?</p>
                <form id="statusChangeForm">
                    <div class="mb-3">
                        <label for="passwordInput" class="form-label">{{ T::translate('Enter Password to Confirm', 'Ilagay ang Password upang Kumpirmahin')}}</label>
                        <input type="password" class="form-control" id="passwordInput" placeholder="{{ T::translate('Enter your password', 'Ilagay ang password')}}" required>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ T::translate('Cancel', 'I-Kansela)}}</button>
                <button type="button" class="btn btn-primary" id="confirmStatusChangeButton">{{ T::translate('Confirm', 'Kumpirmahin')}}</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let selectedStatusElement = null;
    let previousStatusValue = null; // Store the previous value of the dropdown
    let entityType = ""; // Store the entity type dynamically

    // Function to open the modal and store the selected status element
    window.openStatusChangeModal = function (selectElement, type) {
        selectedStatusElement = selectElement; // Store the reference to the dropdown
        previousStatusValue = selectElement.value; // Store the previous value
        entityType = type; // Set the entity type dynamically
        document.getElementById("entityType").textContent = entityType; // Update the modal text
        const statusChangeModal = new bootstrap.Modal(document.getElementById("statusChangeModal"));
        statusChangeModal.show();
    };

    // Handle the status change confirmation
    const confirmStatusChangeButton = document.getElementById("confirmStatusChangeButton");
    confirmStatusChangeButton.addEventListener("click", function () {
        const passwordInput = document.getElementById("passwordInput");
        const enteredPassword = passwordInput.value.trim();

        if (!enteredPassword) {
            alert("{{ T::translate('Please enter your password to confirm the status change', 'Mangyaring ilagay ang iyong password upang kumpirmahin ang pagbabago ng status')}}.");
            return;
        }

        // Simulate password validation (replace with actual server-side validation)
        const correctPassword = "userpassword"; // Replace with actual password validation logic
        if (enteredPassword !== correctPassword) {
            alert("{{ T::translate('Incorrect password. Please try again.', 'Mali ang password. Pakisubukan muli.')}}");
            return;
        }

        // Simulate saving the status change (replace with actual logic)
        alert(`Status changed to "${selectedStatusElement.value}" for ${entityType}.`);

        // Reset the modal fields
        passwordInput.value = "";
        const statusChangeModal = bootstrap.Modal.getInstance(document.getElementById("statusChangeModal"));
        statusChangeModal.hide();
    });

    // Handle modal cancellation
    const statusChangeModalElement = document.getElementById("statusChangeModal");
    statusChangeModalElement.addEventListener("hidden.bs.modal", function () {
        if (selectedStatusElement && previousStatusValue !== null) {
            // Revert the dropdown to its previous value if the modal is cancelled
            selectedStatusElement.value = previousStatusValue;
        }
    });
});
</script>