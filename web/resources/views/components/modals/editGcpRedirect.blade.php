<div class="modal fade" id="editGcpRedirectModal" tabindex="-1" aria-labelledby="editGcpRedirectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#0d6efd;">
                <h5 class="modal-title text-white" id="editGcpRedirectModalLabel">
                    @if(Auth::user()->role_id == 3)
                        Redirecting to Update Beneficiary
                    @else
                        Redirecting to Edit Beneficiary
                    @endif
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-4">
                    <i class="bi bi-pencil-square text-primary" style="font-size: 3rem;"></i>
                </div>
                <h5>General Care Plan Editing Information</h5>
                <p>The General Care Plan is incorporated directly into the beneficiary profile.</p>
                
                @if(Auth::user()->role_id == 3)
                <p>As a Care Worker, you can update certain details about your assigned beneficiaries, including some elements of their General Care Plan.</p>
                <p class="text-info"><small>Note: You cannot change beneficiary status or delete beneficiaries.</small></p>
                @elseif(Auth::user()->role_id == 2)
                <p>As a Care Manager, you have full editing capabilities for the General Care Plans of all beneficiaries.</p>
                @else
                <p>As an Administrator, you have full control over all General Care Plan information.</p>
                @endif
                
                <p>You will be redirected to the Edit Beneficiary page where you can modify the General Care Plan information.</p>
                
                <div class="progress mt-4">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                </div>
                
                <p class="mt-3 mb-0">Redirecting in <span id="editGcpCountdown">5</span> seconds...</p>
                <input type="hidden" id="editGcpBeneficiaryId" value="">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="editGcpNowButton">Go Now</button>
            </div>
        </div>
    </div>
</div>

<script>
let editGcpCountdownInterval;

// Helper function to check if care worker can edit this beneficiary
function canEditBeneficiary(beneficiaryId) {
    @if(Auth::user()->role_id == 3)
        // For Care Workers, we'll use a synchronous check for the modal
        let hasPermission = false;
        
        // Make a synchronous check
        const xhr = new XMLHttpRequest();
        xhr.open('GET', `{{ route('care-worker.check-beneficiary-permission', ['id' => ':id']) }}`.replace(':id', beneficiaryId), false);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.send();
        
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            hasPermission = response.hasPermission;
        }
        
        return hasPermission;
    @else
        // Admins and Care Managers can edit all beneficiaries
        return true;
    @endif
}

function openEditGcpRedirectModal(beneficiaryId) {
    // Check permissions for care workers
    @if(Auth::user()->role_id == 3)
    if (!canEditBeneficiary(beneficiaryId)) {
        alert('You do not have permission to edit this beneficiary. You can only edit beneficiaries assigned to you.');
        return;
    }
    @endif
    
    // Set the beneficiary ID for redirect
    document.getElementById('editGcpBeneficiaryId').value = beneficiaryId;
    
    // Reset progress bar
    const progressBar = document.querySelector('#editGcpRedirectModal .progress-bar');
    progressBar.style.width = '0%';
    
    // Reset countdown
    document.getElementById('editGcpCountdown').textContent = '5';
    
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('editGcpRedirectModal'));
    modal.show();
    
    // Start progress bar animation
    let progress = 0;
    const progressInterval = setInterval(() => {
        progress += 1;
        progressBar.style.width = `${progress}%`;
        if (progress >= 100) clearInterval(progressInterval);
    }, 50);
    
    // Start countdown
    let countdown = 5;
    clearInterval(editGcpCountdownInterval);
    editGcpCountdownInterval = setInterval(() => {
        countdown -= 1;
        document.getElementById('editGcpCountdown').textContent = countdown;
        
        if (countdown <= 0) {
            clearInterval(editGcpCountdownInterval);
            redirectToEditBeneficiary();
        }
    }, 1000);
}

function redirectToEditBeneficiary() {
    const beneficiaryId = document.getElementById('editGcpBeneficiaryId').value;
    
    // Use role-specific routes
    @if(Auth::user()->role_id == 2)
        window.location.href = `{{ route('care-manager.beneficiaries.edit', ':id') }}`.replace(':id', beneficiaryId);
    @elseif(Auth::user()->role_id == 3)
        window.location.href = `{{ route('care-worker.beneficiaries.edit', ':id') }}`.replace(':id', beneficiaryId);
    @else
        window.location.href = `{{ route('admin.beneficiaries.edit', ':id') }}`.replace(':id', beneficiaryId);
    @endif
}

// When document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Handle immediate redirect button
    document.getElementById('editGcpNowButton').addEventListener('click', function() {
        clearInterval(editGcpCountdownInterval);
        redirectToEditBeneficiary();
    });
    
    // Stop countdown if modal is closed
    document.getElementById('editGcpRedirectModal').addEventListener('hidden.bs.modal', function() {
        clearInterval(editGcpCountdownInterval);
    });
});
</script>