<!-- Initial Delete Confirmation Modal -->
<div class="modal fade" id="confirmDeleteWeeklyCarePlanModal" tabindex="-1" aria-labelledby="confirmDeleteWeeklyCarePlanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:rgb(251, 68, 68);">
                <h5 class="modal-title text-white" id="confirmDeleteWeeklyCarePlanModalLabel">Delete Weekly Care Plan?</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- For care workers: show no permission message -->
                @if(Auth::user()->role_id == 3)
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle"></i> 
                    <strong>Permission Denied</strong>
                    <p>Care Workers are not allowed to delete weekly care plans. Please contact a Care Manager or Administrator if you believe this plan should be deleted.</p>
                </div>
                @else
                <!-- For admins and care managers: show delete confirmation -->
                <p class="text-danger">
                    <i class="bi bi-exclamation-circle"></i> 
                    <strong>Warning!</strong> You are about to delete this weekly care plan.
                </p>
                <p>Are you sure you want to delete the weekly care plan for <span id="initialBeneficiaryNameToDelete" style="font-weight: bold;"></span>?</p>
                <input type="hidden" id="initialWeeklyCarePlanIdToDelete" value="">

                <p>You are about to permanently delete the record. <strong>This action cannot be undone and all data will be permanently lost.</strong></p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <!-- Hide delete button for care workers -->
                @if(Auth::user()->role_id != 3)
                <button type="button" class="btn btn-danger" id="proceedToPasswordButton">
                    <i class="bi bi-trash"></i> Proceed to Delete
                </button>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// Setup event handlers for first-level modal
document.addEventListener('DOMContentLoaded', function() {
    // Check if the element exists before adding event listener (avoid errors in care worker view)
    const proceedButton = document.getElementById('proceedToPasswordButton');
    if (proceedButton) {
        proceedButton.addEventListener('click', function() {
            // Get values from the initial modal
            const id = document.getElementById('initialWeeklyCarePlanIdToDelete').value;
            const name = document.getElementById('initialBeneficiaryNameToDelete').textContent;
            
            // Close the initial modal
            bootstrap.Modal.getInstance(document.getElementById('confirmDeleteWeeklyCarePlanModal')).hide();
            
            // Determine which endpoint to use based on user role
            let deleteEndpoint = "/admin/weeklycareplans/" + id; 
            let redirectUrl = "{{ route('admin.reports') }}";

            @if(Auth::user()->role_id == 2)
                deleteEndpoint = "/care-manager/weeklycareplans/" + id;
                redirectUrl = "{{ route('care-manager.reports') }}";
            @endif
            
            // Open the second-level modal with role-specific information
            window.openPasswordConfirmationModal(id, name, deleteEndpoint, redirectUrl);
        });
    }
});
</script>