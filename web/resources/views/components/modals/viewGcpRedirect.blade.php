<div class="modal fade" id="viewGcpRedirectModal" tabindex="-1" aria-labelledby="viewGcpRedirectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color:#0d6efd;">
                <h5 class="modal-title text-white" id="viewGcpRedirectModalLabel">Redirecting to Beneficiary Profile</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-4">
                    <i class="bi bi-info-circle text-primary" style="font-size: 3rem;"></i>
                </div>
                <h5>General Care Plan Information</h5>
                <p>The General Care Plan is incorporated directly into the beneficiary profile.</p>
                
                @if(Auth::user()->role_id == 3)
                <p>You will be redirected to the beneficiary profile where you can view General Care Plan information for your assigned beneficiary.</p>
                <p class="text-info"><small>Note: You can view and edit beneficiary details, but only administrators and care managers can change a beneficiary's status.</small></p>
                @else
                <p>You will be redirected to the beneficiary profile where you can view all General Care Plan information.</p>
                @endif
                
                <div class="progress mt-4">
                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                </div>
                
                <p class="mt-3 mb-0">Redirecting in <span id="viewGcpCountdown">5</span> seconds...</p>
                
                <!-- Hidden form for POST submission with role-based route -->
                <form id="viewBeneficiaryForm" action="{{ 
                    Auth::user()->role_id == 1 ? route('admin.beneficiaries.view') : 
                    (Auth::user()->role_id == 2 ? route('care-manager.beneficiaries.view-details') : 
                    route('care-worker.beneficiaries.view-details'))
                }}" method="POST" style="display: none;">
                    @csrf
                    <input type="hidden" name="beneficiary_id" id="beneficiaryIdInput">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="viewGcpNowButton">Go Now</button>
            </div>
        </div>
    </div>
</div>

<script>
// The JavaScript code can remain the same as it just handles the countdown and form submission
let viewGcpCountdownInterval;

function openViewGcpRedirectModal(beneficiaryId) {
    // Set the beneficiary ID for the hidden form
    document.getElementById('beneficiaryIdInput').value = beneficiaryId;
    
    // Reset progress bar
    const progressBar = document.querySelector('#viewGcpRedirectModal .progress-bar');
    progressBar.style.width = '0%';
    
    // Reset countdown
    document.getElementById('viewGcpCountdown').textContent = '5';
    
    // Show the modal
    const modal = new bootstrap.Modal(document.getElementById('viewGcpRedirectModal'));
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
    clearInterval(viewGcpCountdownInterval);
    viewGcpCountdownInterval = setInterval(() => {
        countdown -= 1;
        document.getElementById('viewGcpCountdown').textContent = countdown;
        
        if (countdown <= 0) {
            clearInterval(viewGcpCountdownInterval);
            submitViewForm();
        }
    }, 1000);
}

function submitViewForm() {
    // Submit the form to use the POST route
    const form = document.getElementById('viewBeneficiaryForm');
    
    // For care workers, add error handling for access restrictions
    @if(Auth::user()->role_id == 3)
    form.addEventListener('submit', function() {
        // Show loading indicator
        document.getElementById('viewGcpNowButton').innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading...';
    });
    @endif
    
    form.submit();
}

// When document is ready
document.addEventListener('DOMContentLoaded', function() {
    // Handle immediate redirect button
    document.getElementById('viewGcpNowButton').addEventListener('click', function() {
        clearInterval(viewGcpCountdownInterval);
        submitViewForm();
    });
    
    // Stop countdown if modal is closed
    document.getElementById('viewGcpRedirectModal').addEventListener('hidden.bs.modal', function() {
        clearInterval(viewGcpCountdownInterval);
    });
});
</script>