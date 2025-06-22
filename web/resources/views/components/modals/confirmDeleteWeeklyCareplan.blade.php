@php
use App\Helpers\TranslationHelper as T;
@endphp
<!-- Initial Delete Confirmation Modal -->
<div class="modal fade" id="confirmDeleteWeeklyCarePlanModal" tabindex="-1" aria-labelledby="confirmDeleteWeeklyCarePlanModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:rgb(251, 68, 68);">
                <h5 class="modal-title text-white" id="confirmDeleteWeeklyCarePlanModalLabel">{{ T::translate('Delete Weekly Care Plan?', 'Tanggalin ang Weekly Care Plan?')}}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- For care workers: show no permission message -->
                @if(Auth::user()->role_id == 3)
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-circle"></i> 
                    <strong>{{ T::translate('Permission Denied', 'Tinanggihan ang Permiso')}}</strong>
                    <p>{{ T::translate('Care Workers are not allowed to delete weekly care plans. Please contact a Care Manager or Administrator if you believe this plan should be deleted.', 'Hindi pinapayagan ang mga Tagapag-alaga na magtanggal ng mga Weekly Care Plan. Mangyaring makipag-ugnayan sa isang Care Manager o Administrator kung naniniwala kang dapat tanggalin ito.')}}</p>
                </div>
                @else
                <!-- For admins and care managers: show delete confirmation -->
                <p class="text-danger">
                    <i class="bi bi-exclamation-circle"></i> 
                    <strong>{{ T::translate('Warning!', 'Babala!')}}</strong> {{ T::translate('You are about to delete this weekly care plan.', 'Tatanggalingin mo ang Weekly Care Plan na ito.')}}
                </p>
                <p>{{ T::translate('Are you sure you want to delete the weekly care plan for', 'Ikaw ba ay sigurado na tanggalin ang Weekly Care Plan na ito para sa')}} <span id="initialBeneficiaryNameToDelete" style="font-weight: bold;"></span>?</p>
                <input type="hidden" id="initialWeeklyCarePlanIdToDelete" value="">

                <p>{{ T::translate('You are about to permanently delete the record.', 'Permanente mo ng tatanggalin ang tala na ito.')}} <strong>{{ T::translate('This action cannot be undone and all data will be permanently lost.', 'Ang aksyong ito ay hindi na maaaring maibalik at lahat ng datos ay permanenteng mawawala.')}}</strong></p>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">{{ T::translate('Cancel', 'I-Kansela')}}</button>
                <!-- Hide delete button for care workers -->
                @if(Auth::user()->role_id != 3)
                <button type="button" class="btn btn-danger" id="proceedToPasswordButton">
                    <i class="bi bi-trash"></i> {{ T::translate('Proceed to Delete', 'Magpatuloy sa Pagtanggal')}}
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